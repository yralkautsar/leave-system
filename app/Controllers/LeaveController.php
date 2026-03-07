<?php

require_once __DIR__ . '/../Services/Database.php';

class LeaveController
{
    /* ==========================================================
       AUTH HELPERS
    ========================================================== */

    private static function authorizeLogin()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /leave-system/public/login");
            exit;
        }
    }

    private static function authorizeAdmin()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin_approver') {
            die("Unauthorized");
        }
    }

    /* ==========================================================
       LEAVE – EMPLOYEE
    ========================================================== */

    public static function create()
    {
        self::authorizeLogin();
        $db = Database::connect();

        // Show all periods currently active (date-based, not flag-based)
        // Includes overlapping periods — employee chooses which balance to use
        $periods = $db->query("
            SELECT * FROM leave_periods
            WHERE start_date <= CURDATE() AND end_date >= CURDATE()
            ORDER BY start_date ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $leaveTypes = $db->query("SELECT * FROM leave_types ORDER BY name ASC")
            ->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/leave_create.php';
    }

    public static function store()
    {
        self::authorizeLogin();
        $db = Database::connect();

        $stmt = $db->prepare("
            INSERT INTO leave_requests
            (employee_id, leave_type_id, leave_period_id, start_date, end_date, duration_type, total_days, status, created_at)
            VALUES
            (:emp, :type, :period, :start, :end, :duration, :total, 'pending', NOW())
        ");

        $stmt->execute([
            'emp'      => $_SESSION['user']['id'],
            'type'     => $_POST['leave_type_id'],
            'period'   => $_POST['leave_period_id'],
            'start'    => $_POST['start_date'],
            'end'      => $_POST['end_date'],
            'duration' => $_POST['duration_type'],
            'total'    => $_POST['total_days']
        ]);

        // ── Email notification ──
        try {
            require_once __DIR__ . '/../Services/MailService.php';

            // Fetch employee detail (with hod_id, gm_id, department)
            $empStmt = $db->prepare("
                SELECT u.*, d.name AS department
                FROM users u
                LEFT JOIN departments d ON u.department_id = d.id
                WHERE u.id = :id
            ");
            $empStmt->execute(['id' => $_SESSION['user']['id']]);
            $employee = $empStmt->fetch(PDO::FETCH_ASSOC);

            // Fetch leave type name
            $ltStmt = $db->prepare("SELECT name FROM leave_types WHERE id = :id");
            $ltStmt->execute(['id' => $_POST['leave_type_id']]);
            $leaveTypeName = $ltStmt->fetchColumn();

            $request = [
                'leave_type'  => $leaveTypeName,
                'start_date'  => $_POST['start_date'],
                'end_date'    => $_POST['end_date'],
                'total_days'  => $_POST['total_days'],
            ];

            MailService::notifyLeaveSubmitted($request, $employee);
        } catch (Exception $e) {
            // Silent — email failure must not block leave submission
        }

        header("Location: /leave-system/public/dashboard");
        exit;
    }

    public static function myHistory()
    {
        self::authorizeLogin();
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT lr.*, lt.name AS leave_type
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.employee_id = :id
            ORDER BY lr.created_at DESC
        ");
        $stmt->execute(['id' => $_SESSION['user']['id']]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/leave_history.php';
    }

    public static function cancel(int $id)
    {
        self::authorizeLogin();
        $db = Database::connect();

        $stmt = $db->prepare("
            UPDATE leave_requests
            SET status = 'cancelled'
            WHERE id = :id
            AND employee_id = :emp
            AND status = 'pending'
        ");

        $stmt->execute([
            'id'  => $id,
            'emp' => $_SESSION['user']['id']
        ]);

        header("Location: /leave-system/public/my-history");
        exit;
    }

    /* ==========================================================
       APPROVAL (ADMIN)
    ========================================================== */

    public static function approve(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("
                SELECT * FROM leave_requests
                WHERE id = :id AND status = 'pending'
                FOR UPDATE
            ");
            $stmt->execute(['id' => $id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                throw new Exception("Request not found or already processed.");
            }

            $stmt = $db->prepare("
                SELECT * FROM leave_balances
                WHERE employee_id = :emp
                AND leave_period_id = :period
                FOR UPDATE
            ");
            $stmt->execute([
                'emp'    => $request['employee_id'],
                'period' => $request['leave_period_id']
            ]);
            $balance = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$balance || $balance['remaining_days'] < $request['total_days']) {
                throw new Exception("Insufficient leave balance.");
            }

            $stmt = $db->prepare("
                UPDATE leave_balances
                SET used_days = used_days + :days,
                    remaining_days = remaining_days - :days
                WHERE id = :id
            ");
            $stmt->execute([
                'days' => $request['total_days'],
                'id'   => $balance['id']
            ]);

            $stmt = $db->prepare("
                UPDATE leave_requests
                SET status = 'approved',
                    approved_at = NOW(),
                    approved_by = :admin
                WHERE id = :id
            ");
            $stmt->execute([
                'id'    => $id,
                'admin' => $_SESSION['user']['id']
            ]);

            $db->commit();

            // ── Email notification ──
            try {
                require_once __DIR__ . '/../Services/MailService.php';

                $empStmt = $db->prepare("
                    SELECT u.*, d.name AS department
                    FROM users u
                    LEFT JOIN departments d ON u.department_id = d.id
                    WHERE u.id = :id
                ");
                $empStmt->execute(['id' => $request['employee_id']]);
                $employee = $empStmt->fetch(PDO::FETCH_ASSOC);

                $ltStmt = $db->prepare("SELECT name FROM leave_types WHERE id = :id");
                $ltStmt->execute(['id' => $request['leave_type_id']]);
                $leaveTypeName = $ltStmt->fetchColumn();

                $emailRequest = [
                    'leave_type' => $leaveTypeName,
                    'start_date' => $request['start_date'],
                    'end_date'   => $request['end_date'],
                    'total_days' => $request['total_days'],
                ];

                MailService::notifyLeaveApproved($emailRequest, $employee, $_SESSION['user']['name']);
            } catch (Exception $e) {
                // Silent
            }
        } catch (Exception $e) {
            $db->rollBack();
            die("Error: " . $e->getMessage());
        }

        $redirect = ($_POST['_from'] ?? '') === 'requests'
            ? '/leave-system/public/admin/requests'
            : '/leave-system/public/dashboard';

        header("Location: $redirect");
        exit;
    }

    public static function reject(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        // Fetch before update so we have employee_id and leave_type_id
        $stmt = $db->prepare("SELECT * FROM leave_requests WHERE id = :id AND status = 'pending'");
        $stmt->execute(['id' => $id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("
            UPDATE leave_requests
            SET status      = 'rejected',
                approved_by = :admin,
                approved_at = NOW()
            WHERE id = :id AND status = 'pending'
        ");
        $stmt->execute([
            'id'    => $id,
            'admin' => $_SESSION['user']['id']
        ]);

        // ── Email notification ──
        if ($request) {
            try {
                require_once __DIR__ . '/../Services/MailService.php';

                $empStmt = $db->prepare("
                    SELECT u.*, d.name AS department
                    FROM users u
                    LEFT JOIN departments d ON u.department_id = d.id
                    WHERE u.id = :id
                ");
                $empStmt->execute(['id' => $request['employee_id']]);
                $employee = $empStmt->fetch(PDO::FETCH_ASSOC);

                $ltStmt = $db->prepare("SELECT name FROM leave_types WHERE id = :id");
                $ltStmt->execute(['id' => $request['leave_type_id']]);
                $leaveTypeName = $ltStmt->fetchColumn();

                $emailRequest = [
                    'leave_type' => $leaveTypeName,
                    'start_date' => $request['start_date'],
                    'end_date'   => $request['end_date'],
                    'total_days' => $request['total_days'],
                ];

                MailService::notifyLeaveRejected($emailRequest, $employee, $_SESSION['user']['name']);
            } catch (Exception $e) {
                // Silent
            }
        }

        $redirect = ($_POST['_from'] ?? '') === 'requests'
            ? '/leave-system/public/admin/requests' . (isset($_SERVER['HTTP_REFERER']) ? '?' . parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY) : '')
            : '/leave-system/public/dashboard';

        header("Location: $redirect");
        exit;
    }

    /* ==========================================================
       API – REQUEST DETAIL (JSON)
    ========================================================== */

    public static function requestDetail(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        // Full request detail
        $stmt = $db->prepare("
            SELECT
                lr.id,
                lr.start_date,
                lr.end_date,
                lr.total_days,
                lr.duration_type,
                lr.status,
                lr.created_at,
                lr.approved_at,
                u.name          AS employee_name,
                u.email         AS employee_email,
                u.join_date,
                d.name          AS department,
                j.name          AS job_title,
                lt.name         AS leave_type,
                adm.name        AS processed_by,
                lp.name         AS period_name
            FROM leave_requests lr
            JOIN users u            ON lr.employee_id     = u.id
            JOIN leave_types lt     ON lr.leave_type_id   = lt.id
            LEFT JOIN departments d ON u.department_id    = d.id
            LEFT JOIN job_titles j  ON u.job_title_id     = j.id
            LEFT JOIN users adm     ON lr.approved_by     = adm.id
            LEFT JOIN leave_periods lp ON lr.leave_period_id = lp.id
            WHERE lr.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $detail = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$detail) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            exit;
        }

        // Current balance for this leave type
        $stmt = $db->prepare("
            SELECT
                lb.total_days,
                lb.used_days,
                lb.remaining_days
            FROM leave_balances lb
            JOIN leave_periods lp ON lb.leave_period_id = lp.id
            WHERE lb.employee_id   = (
                SELECT employee_id FROM leave_requests WHERE id = :id
            )
            AND lb.leave_type_id   = (
                SELECT leave_type_id FROM leave_requests WHERE id = :id2
            )
            AND lp.start_date <= CURDATE() AND lp.end_date >= CURDATE()
            LIMIT 1
        ");
        $stmt->execute(['id' => $id, 'id2' => $id]);
        $balance = $stmt->fetch(PDO::FETCH_ASSOC);

        $detail['balance'] = $balance ?: null;

        header('Content-Type: application/json');
        echo json_encode($detail);
        exit;
    }

    /* ==========================================================
       REVOKE — undo an approved leave (admin only)
    ========================================================== */

    public static function revoke(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        try {
            $db->beginTransaction();

            // Lock the request
            $stmt = $db->prepare("
                SELECT * FROM leave_requests
                WHERE id = :id AND status = 'approved'
                FOR UPDATE
            ");
            $stmt->execute(['id' => $id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                throw new Exception("Request not found or not in approved state.");
            }

            // Restore balance
            $stmt = $db->prepare("
                UPDATE leave_balances
                SET used_days      = used_days      - :days,
                    remaining_days = remaining_days + :days
                WHERE employee_id     = :emp
                AND   leave_period_id = :period
            ");
            $stmt->execute([
                'days'   => $request['total_days'],
                'emp'    => $request['employee_id'],
                'period' => $request['leave_period_id'],
            ]);

            // Set back to pending
            $stmt = $db->prepare("
                UPDATE leave_requests
                SET status      = 'pending',
                    approved_at = NULL,
                    approved_by = NULL
                WHERE id = :id
            ");
            $stmt->execute(['id' => $id]);

            $db->commit();

            $_SESSION['success'] = "Leave request has been revoked and returned to pending.";
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "Revoke failed: " . $e->getMessage();
        }

        $qs = $_POST['_qs'] ?? '';
        header("Location: /leave-system/public/admin/requests" . ($qs ? '?' . $qs : ''));
        exit;
    }

    public static function departments()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $departments = $db->query("
            SELECT d.*, COUNT(u.id) AS user_count
            FROM departments d
            LEFT JOIN users u ON u.department_id = d.id AND u.is_active = 1
            GROUP BY d.id
            ORDER BY d.name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_departments.php';
    }

    public static function storeDepartment()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            $_SESSION['error'] = "Department name cannot be empty.";
            header("Location: /leave-system/public/admin/departments");
            exit;
        }

        // Check duplicate
        $check = $db->prepare("SELECT id FROM departments WHERE name = :name LIMIT 1");
        $check->execute(['name' => $name]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Department \"{$name}\" already exists.";
            header("Location: /leave-system/public/admin/departments");
            exit;
        }

        $stmt = $db->prepare("INSERT INTO departments (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
        $_SESSION['success'] = "Department \"{$name}\" added.";

        header("Location: /leave-system/public/admin/departments");
        exit;
    }

    public static function updateDepartment(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            $_SESSION['error'] = "Name cannot be empty.";
            header("Location: /leave-system/public/admin/departments");
            exit;
        }

        $stmt = $db->prepare("UPDATE departments SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);
        $_SESSION['success'] = "Department updated.";

        header("Location: /leave-system/public/admin/departments");
        exit;
    }

    public static function deleteDepartment(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        // Block delete if users are assigned
        $check = $db->prepare("SELECT COUNT(*) FROM users WHERE department_id = :id");
        $check->execute(['id' => $id]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['error'] = "Cannot delete — there are employees assigned to this department.";
            header("Location: /leave-system/public/admin/departments");
            exit;
        }

        $stmt = $db->prepare("DELETE FROM departments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['success'] = "Department deleted.";

        header("Location: /leave-system/public/admin/departments");
        exit;
    }

    /* ==========================================================
       ADMIN – JOB TITLES
    ========================================================== */

    public static function jobTitles()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $jobTitles = $db->query("
            SELECT j.*, COUNT(u.id) AS user_count
            FROM job_titles j
            LEFT JOIN users u ON u.job_title_id = j.id AND u.is_active = 1
            GROUP BY j.id
            ORDER BY j.name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_jobtitles.php';
    }

    public static function storeJobTitle()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            $_SESSION['error'] = "Job title name cannot be empty.";
            header("Location: /leave-system/public/admin/job-titles");
            exit;
        }

        $check = $db->prepare("SELECT id FROM job_titles WHERE name = :name LIMIT 1");
        $check->execute(['name' => $name]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Job title \"{$name}\" already exists.";
            header("Location: /leave-system/public/admin/job-titles");
            exit;
        }

        $stmt = $db->prepare("INSERT INTO job_titles (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
        $_SESSION['success'] = "Job title \"{$name}\" added.";

        header("Location: /leave-system/public/admin/job-titles");
        exit;
    }

    public static function updateJobTitle(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            $_SESSION['error'] = "Name cannot be empty.";
            header("Location: /leave-system/public/admin/job-titles");
            exit;
        }

        $stmt = $db->prepare("UPDATE job_titles SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $name, 'id' => $id]);
        $_SESSION['success'] = "Job title updated.";

        header("Location: /leave-system/public/admin/job-titles");
        exit;
    }

    public static function deleteJobTitle(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $check = $db->prepare("SELECT COUNT(*) FROM users WHERE job_title_id = :id");
        $check->execute(['id' => $id]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['error'] = "Cannot delete — there are employees with this job title.";
            header("Location: /leave-system/public/admin/job-titles");
            exit;
        }

        $stmt = $db->prepare("DELETE FROM job_titles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['success'] = "Job title deleted.";

        header("Location: /leave-system/public/admin/job-titles");
        exit;
    }

    /* ==========================================================
       ADMIN – USERS
    ========================================================== */

    public static function users()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $departments   = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $jobTitles     = $db->query("SELECT * FROM job_titles ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $workSchedules = $db->query("SELECT * FROM work_schedules")->fetchAll(PDO::FETCH_ASSOC);

        // All active users as candidates for HoD / GM selection
        $allUsers = $db->query("
            SELECT id, name, email, role FROM users
            WHERE is_active = 1
            ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $users = $db->query("
            SELECT
                u.*,
                d.name  AS department,
                j.name  AS job_title,
                hod.name  AS hod_name,
                hod.email AS hod_email,
                gm.name   AS gm_name,
                gm.email  AS gm_email
            FROM users u
            LEFT JOIN departments d  ON u.department_id = d.id
            LEFT JOIN job_titles j   ON u.job_title_id  = j.id
            LEFT JOIN users hod      ON u.hod_id        = hod.id
            LEFT JOIN users gm       ON u.gm_id         = gm.id
            ORDER BY u.name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_users.php';
    }

    public static function storeUser()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $email = trim($_POST['email'] ?? '');

        // Duplicate email check
        $check = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Email \"{$email}\" is already registered.";
            header("Location: /leave-system/public/admin/users");
            exit;
        }

        $stmt = $db->prepare("
            INSERT INTO users
            (name, email, password_hash, role,
             department_id, job_title_id,
             hod_id, gm_id,
             join_date, is_active, created_at)
            VALUES
            (:name, :email, :pass, :role,
             :dept, :job,
             :hod, :gm,
             :join, 1, NOW())
        ");

        $stmt->execute([
            'name'  => trim($_POST['name']),
            'email' => $email,
            'pass'  => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'  => $_POST['role'],
            'dept'  => $_POST['department_id'] ?: null,
            'job'   => $_POST['job_title_id']  ?: null,
            'hod'   => $_POST['hod_id']        ?: null,
            'gm'    => $_POST['gm_id']         ?: null,
            'join'  => $_POST['join_date'],
        ]);

        $_SESSION['success'] = "User " . htmlspecialchars(trim($_POST['name'])) . " added.";
        header("Location: /leave-system/public/admin/users");
        exit;
    }

    public static function updateUser(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        // Email uniqueness check (excluding self)
        $email = trim($_POST['email'] ?? '');
        $check = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
        $check->execute(['email' => $email, 'id' => $id]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Email \"{$email}\" is already used by another account.";
            header("Location: /leave-system/public/admin/users");
            exit;
        }

        $sql = "
            UPDATE users SET
                name          = :name,
                email         = :email,
                role          = :role,
                department_id = :dept,
                job_title_id  = :job,
                hod_id        = :hod,
                gm_id         = :gm,
                join_date     = :join
        ";

        $params = [
            'name'  => trim($_POST['name']),
            'email' => $email,
            'role'  => $_POST['role'],
            'dept'  => $_POST['department_id'] ?: null,
            'job'   => $_POST['job_title_id']  ?: null,
            'hod'   => $_POST['hod_id']        ?: null,
            'gm'    => $_POST['gm_id']         ?: null,
            'join'  => $_POST['join_date'],
            'id'    => $id,
        ];

        // Only update password if provided
        if (!empty($_POST['password'])) {
            $sql .= ", password_hash = :pass";
            $params['pass'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $_SESSION['success'] = "User updated.";
        header("Location: /leave-system/public/admin/users");
        exit;
    }

    public static function toggleUserStatus()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $userId = (int)$_POST['user_id'];

        $stmt = $db->prepare("
            UPDATE users
            SET is_active = IF(is_active = 1, 0, 1)
            WHERE id = ?
        ");
        $stmt->execute([$userId]);

        $_SESSION['success'] = "User status updated.";
        header("Location: /leave-system/public/admin/users");
        exit;
    }

    public static function login()
    {
        session_start();

        $db = Database::connect();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $db->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: /leave-system/public/login");
            exit;
        }

        if (!$user['is_active']) {
            $_SESSION['error'] = "Your account has been suspended. Please contact HR.";
            header("Location: /leave-system/public/login");
            exit;
        }

        if (!password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: /leave-system/public/login");
            exit;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'is_active' => $user['is_active']
        ];

        header("Location: /leave-system/public/dashboard");
        exit;
    }


    public static function logout()
    {
        session_start();
        session_destroy();

        header("Location: /leave-system/public/login");
        exit;
    }


    public static function dashboard()
    {
        session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: /leave-system/public/login");
            exit;
        }

        require __DIR__ . '/../../resources/views/dashboard.php';
    }

    /* ==========================================================
   ADMIN – LEAVE PERIODS
    ========================================================== */

    /* ==========================================================
       ADMIN – LEAVE PERIODS
    ========================================================== */

    public static function periods()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $today = date('Y-m-d');

        $periods = $db->query("
            SELECT
                p.*,
                (SELECT COUNT(*) FROM leave_balances lb WHERE lb.leave_period_id = p.id) AS total_generated,
                (SELECT COUNT(*) FROM users WHERE role = 'employee' AND is_active = 1)   AS total_employees,
                CASE
                    WHEN p.start_date > '{$today}'                              THEN 'upcoming'
                    WHEN p.start_date <= '{$today}' AND p.end_date >= '{$today}' THEN 'active'
                    ELSE 'expired'
                END AS status
            FROM leave_periods p
            ORDER BY start_date DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_periods.php';
    }

    public static function storePeriod()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $name  = trim($_POST['name']  ?? '');
        $start = $_POST['start_date'] ?? '';
        $end   = $_POST['end_date']   ?? '';

        if (!$name || !$start || !$end) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: /leave-system/public/admin/periods");
            exit;
        }

        if ($start >= $end) {
            $_SESSION['error'] = "End date must be after start date.";
            header("Location: /leave-system/public/admin/periods");
            exit;
        }

        $dup = $db->prepare("SELECT id FROM leave_periods WHERE name = :name LIMIT 1");
        $dup->execute(['name' => $name]);
        if ($dup->fetch()) {
            $_SESSION['error'] = "A period named \"{$name}\" already exists.";
            header("Location: /leave-system/public/admin/periods");
            exit;
        }

        // Detect overlap — warn but don't block (SOP: 15-month periods overlap by design)
        $overlap = $db->prepare("
            SELECT name FROM leave_periods
            WHERE start_date <= :end AND end_date >= :start
        ");
        $overlap->execute(['start' => $start, 'end' => $end]);
        $overlapping = $overlap->fetchAll(PDO::FETCH_COLUMN);

        try {
            $db->prepare("
                INSERT INTO leave_periods (name, start_date, end_date)
                VALUES (:name, :start, :end)
            ")->execute(['name' => $name, 'start' => $start, 'end' => $end]);

            if (!empty($overlapping)) {
                $_SESSION['warning'] = "Period \"{$name}\" created. Overlaps with: "
                    . implode(', ', array_map('htmlspecialchars', $overlapping))
                    . " — expected for 15-month periods.";
            } else {
                $_SESSION['success'] = "Period \"{$name}\" created.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: /leave-system/public/admin/periods");
        exit;
    }

    public static function deletePeriod(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $check = $db->prepare("SELECT COUNT(*) FROM leave_balances WHERE leave_period_id = :id");
        $check->execute(['id' => $id]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['error'] = "Cannot delete — leave balances have been generated for this period.";
            header("Location: /leave-system/public/admin/periods");
            exit;
        }

        $check2 = $db->prepare("SELECT COUNT(*) FROM leave_requests WHERE leave_period_id = :id");
        $check2->execute(['id' => $id]);
        if ((int)$check2->fetchColumn() > 0) {
            $_SESSION['error'] = "Cannot delete — leave requests exist under this period.";
            header("Location: /leave-system/public/admin/periods");
            exit;
        }

        $db->prepare("DELETE FROM leave_periods WHERE id = :id")->execute(['id' => $id]);
        $_SESSION['success'] = "Period deleted.";
        header("Location: /leave-system/public/admin/periods");
        exit;
    }

    public static function generateBalances()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $periodId = (int)$_POST['period_id'];

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT * FROM leave_periods WHERE id = :id");
            $stmt->execute(['id' => $periodId]);
            $period = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$period) throw new Exception("Leave period not found.");

            $leaveTypes = $db->query("SELECT * FROM leave_types WHERE default_days > 0")
                ->fetchAll(PDO::FETCH_ASSOC);

            if (empty($leaveTypes))
                throw new Exception("No leave types with default_days > 0. Configure leave types first.");

            $employees = $db->query("SELECT * FROM users WHERE role = 'employee' AND is_active = 1")
                ->fetchAll(PDO::FETCH_ASSOC);

            $generatedCount = 0;
            $skippedCount   = 0;
            $probationCount = 0;

            $insert = $db->prepare("
                INSERT INTO leave_balances
                (employee_id, leave_period_id, leave_type_id, total_days, remaining_days, used_days)
                VALUES (:emp, :period, :type, :days, :days, 0)
            ");

            $checkExist = $db->prepare("
                SELECT id FROM leave_balances
                WHERE employee_id = :emp AND leave_period_id = :period AND leave_type_id = :type
            ");

            foreach ($employees as $emp) {
                // Skip employees still in probation at period start date
                if (
                    !empty($emp['probation_end_date'])
                    && $emp['probation_end_date'] > $period['start_date']
                ) {
                    $probationCount++;
                    continue;
                }

                foreach ($leaveTypes as $type) {
                    $checkExist->execute([
                        'emp'    => $emp['id'],
                        'period' => $periodId,
                        'type'   => $type['id'],
                    ]);

                    if ($checkExist->fetch()) {
                        $skippedCount++;
                        continue;
                    }

                    $insert->execute([
                        'emp'    => $emp['id'],
                        'period' => $periodId,
                        'type'   => $type['id'],
                        'days'   => $type['default_days'],
                    ]);
                    $generatedCount++;
                }
            }

            $db->commit();

            $msg = "{$generatedCount} balance(s) generated.";
            if ($skippedCount   > 0) $msg .= " {$skippedCount} already existed — skipped.";
            if ($probationCount > 0) $msg .= " {$probationCount} employee(s) on probation — skipped.";

            $_SESSION['success'] = $generatedCount > 0 ? $msg : "All balances already exist for this period.";
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: /leave-system/public/admin/periods");
        exit;
    }

    /* ==========================================================
       ADMIN – LEAVE TYPES
    ========================================================== */

    public static function leaveTypes()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $leaveTypes = $db->query("
            SELECT lt.*,
                (SELECT COUNT(DISTINCT lb.employee_id)
                 FROM leave_balances lb
                 WHERE lb.leave_type_id = lt.id) AS employee_count
            FROM leave_types lt
            ORDER BY lt.name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_leave_types.php';
    }

    public static function storeLeaveType()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $name = trim($_POST['name']         ?? '');
        $days = (int)($_POST['default_days'] ?? 0);

        if (!$name) {
            $_SESSION['error'] = "Leave type name is required.";
            header("Location: /leave-system/public/admin/leave-types");
            exit;
        }

        $dup = $db->prepare("SELECT id FROM leave_types WHERE name = :name LIMIT 1");
        $dup->execute(['name' => $name]);
        if ($dup->fetch()) {
            $_SESSION['error'] = "A leave type named \"{$name}\" already exists.";
            header("Location: /leave-system/public/admin/leave-types");
            exit;
        }

        $db->prepare("INSERT INTO leave_types (name, default_days) VALUES (:name, :days)")
            ->execute(['name' => $name, 'days' => $days]);

        $_SESSION['success'] = "Leave type \"{$name}\" added.";
        header("Location: /leave-system/public/admin/leave-types");
        exit;
    }

    public static function updateLeaveType(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $name = trim($_POST['name']         ?? '');
        $days = (int)($_POST['default_days'] ?? 0);

        if (!$name) {
            $_SESSION['error'] = "Leave type name is required.";
            header("Location: /leave-system/public/admin/leave-types");
            exit;
        }

        $dup = $db->prepare("SELECT id FROM leave_types WHERE name = :name AND id != :id LIMIT 1");
        $dup->execute(['name' => $name, 'id' => $id]);
        if ($dup->fetch()) {
            $_SESSION['error'] = "Another leave type named \"{$name}\" already exists.";
            header("Location: /leave-system/public/admin/leave-types");
            exit;
        }

        $db->prepare("UPDATE leave_types SET name = :name, default_days = :days WHERE id = :id")
            ->execute(['name' => $name, 'days' => $days, 'id' => $id]);

        $_SESSION['success'] = "Leave type updated.";
        header("Location: /leave-system/public/admin/leave-types");
        exit;
    }

    public static function deleteLeaveType($id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $check = $db->prepare("SELECT COUNT(*) FROM leave_balances WHERE leave_type_id = :id");
        $check->execute(['id' => $id]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['error'] = "Cannot delete — existing leave balances use this type.";
            header("Location: /leave-system/public/admin/leave-types");
            exit;
        }

        $db->prepare("DELETE FROM leave_types WHERE id = :id")->execute(['id' => $id]);
        $_SESSION['success'] = "Leave type deleted.";
        header("Location: /leave-system/public/admin/leave-types");
        exit;
    }

    /* ==========================================================
       ADMIN – BALANCES
    ========================================================== */

    public static function balances()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $search = $_GET['search'] ?? '';
        $type   = $_GET['type']   ?? '';
        $period = $_GET['period'] ?? '';

        $activePeriods = $db->query("
            SELECT id FROM leave_periods
            WHERE start_date <= CURDATE() AND end_date >= CURDATE()
        ")->fetchAll(PDO::FETCH_COLUMN);

        $query = "
            SELECT
                u.name   AS employee_name,
                u.id     AS employee_id,
                lt.name  AS leave_type,
                lt.id    AS leave_type_id,
                lp.name  AS period_name,
                lp.id    AS period_id,
                lp.start_date AS period_start,
                lp.end_date   AS period_end,
                lb.id         AS balance_id,
                lb.total_days,
                lb.used_days,
                lb.remaining_days
            FROM leave_balances lb
            JOIN users        u  ON lb.employee_id     = u.id
            JOIN leave_types  lt ON lb.leave_type_id   = lt.id
            JOIN leave_periods lp ON lb.leave_period_id = lp.id
            WHERE 1=1
        ";
        $params = [];

        if ($search) {
            $query .= " AND u.name LIKE :search";
            $params['search'] = "%{$search}%";
        }
        if ($type) {
            $query .= " AND lt.id = :type";
            $params['type'] = $type;
        }
        if ($period) {
            $query .= " AND lp.id = :period";
            $params['period'] = $period;
        } elseif (!empty($activePeriods) && !$search && !$type) {
            $in = implode(',', array_map('intval', $activePeriods));
            $query .= " AND lp.id IN ({$in})";
        }

        $query .= " ORDER BY u.name ASC, lp.start_date ASC, lt.name ASC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $leaveTypes = $db->query("SELECT id, name FROM leave_types ORDER BY name ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
        $periods    = $db->query("SELECT id, name, start_date, end_date FROM leave_periods ORDER BY start_date DESC")
            ->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_balances.php';
    }

    public static function adjustBalance()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $balanceId = (int)($_POST['balance_id'] ?? 0);
        $days      = (int)($_POST['days']       ?? 0);
        $reason    = trim($_POST['reason']      ?? '');

        if ($days === 0) {
            $_SESSION['error'] = "Adjustment cannot be 0.";
            header("Location: /leave-system/public/admin/balances");
            exit;
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare("SELECT * FROM leave_balances WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $balanceId]);
            $balance = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$balance) throw new Exception("Balance record not found.");

            $mode = $_POST['mode'] ?? 'add';

            if ($mode === 'set') {
                // Set total quota — recalculate remaining as newTotal - used
                $newTotal = max(0, $days); // days field holds the new quota value
                $newRemaining = $newTotal - (int)$balance['used_days'];
                if ($newRemaining < 0) $newRemaining = 0;

                $db->prepare("
                    UPDATE leave_balances
                    SET total_days     = :total,
                        remaining_days = :remaining
                    WHERE id = :id
                ")->execute([
                    'total'     => $newTotal,
                    'remaining' => $newRemaining,
                    'id'        => $balanceId,
                ]);
            } else {
                // Add or deduct — only touch remaining_days
                if ($days < 0 && ($balance['remaining_days'] + $days) < 0) {
                    throw new Exception("Cannot reduce — remaining days would go below 0.");
                }

                $db->prepare("
                    UPDATE leave_balances
                    SET remaining_days = remaining_days + :days
                    WHERE id = :id
                ")->execute(['days' => $days, 'id' => $balanceId]);
            }

            $db->prepare("
                INSERT INTO leave_balance_adjustments
                (employee_id, leave_type_id, days_adjusted, reason, created_by)
                VALUES (:emp, :type, :days, :reason, :admin)
            ")->execute([
                'emp'    => $balance['employee_id'],
                'type'   => $balance['leave_type_id'],
                'days'   => $days,
                'reason' => $reason,
                'admin'  => $_SESSION['user']['id'],
            ]);

            $db->commit();
            if ($mode === 'set') {
                $_SESSION['success'] = "Quota updated to {$days} day(s). Remaining recalculated.";
            } else {
                $_SESSION['success'] = "Balance adjusted by " . ($days > 0 ? "+{$days}" : $days) . " day(s).";
            }
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = $e->getMessage();
        }

        $qs = http_build_query(array_filter([
            'period' => $_POST['period_filter'] ?? '',
            'type'   => $_POST['type_filter']   ?? '',
            'search' => $_POST['search_filter']  ?? '',
        ]));
        header("Location: /leave-system/public/admin/balances" . ($qs ? "?{$qs}" : ""));
        exit;
    }

    public static function balanceHistory()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $balanceId = (int)($_GET['balance_id'] ?? 0);

        // Get employee_id + leave_type_id from balance record
        $lb = $db->prepare("SELECT employee_id, leave_type_id FROM leave_balances WHERE id = :id");
        $lb->execute(['id' => $balanceId]);
        $row = $lb->fetch(PDO::FETCH_ASSOC);

        $history = [];
        if ($row) {
            $stmt = $db->prepare("
                SELECT a.days_adjusted, a.reason, a.created_at, u.name AS admin_name
                FROM leave_balance_adjustments a
                JOIN users u ON a.created_by = u.id
                WHERE a.employee_id  = :emp
                AND   a.leave_type_id = :type
                ORDER BY a.created_at DESC
            ");
            $stmt->execute(['emp' => $row['employee_id'], 'type' => $row['leave_type_id']]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        header('Content-Type: application/json');
        echo json_encode($history);
        exit;
    }

    /** AJAX — available balances for employee when submitting leave */
    public static function availableBalances()
    {
        self::authorizeLogin();
        $db = Database::connect();

        $empId       = (int)$_SESSION['user']['id'];
        $leaveTypeId = (int)($_GET['leave_type_id'] ?? 0);
        $startDate   = $_GET['start_date'] ?? date('Y-m-d');
        $endDate     = $_GET['end_date']   ?? $startDate;

        $stmt = $db->prepare("
            SELECT
                lb.id           AS balance_id,
                lb.remaining_days,
                lb.total_days,
                lb.used_days,
                lp.id           AS period_id,
                lp.name         AS period_name,
                lp.start_date,
                lp.end_date
            FROM leave_balances lb
            JOIN leave_periods lp ON lb.leave_period_id = lp.id
            WHERE lb.employee_id   = :emp
            AND   lb.leave_type_id = :type
            AND   lb.remaining_days > 0
            AND   lp.start_date   <= :end
            AND   lp.end_date     >= :start
            ORDER BY lp.start_date ASC
        ");
        $stmt->execute([
            'emp'   => $empId,
            'type'  => $leaveTypeId,
            'start' => $startDate,
            'end'   => $endDate,
        ]);

        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    public static function holidays()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $year = (int)($_GET['year'] ?? date('Y'));

        $holidays = $db->prepare("
            SELECT * FROM holidays
            WHERE YEAR(holiday_date) = :year
            ORDER BY holiday_date ASC
        ");
        $holidays->execute(['year' => $year]);
        $holidays = $holidays->fetchAll(PDO::FETCH_ASSOC);

        // Available years for filter
        $years = $db->query("
            SELECT DISTINCT YEAR(holiday_date) AS y
            FROM holidays
            ORDER BY y DESC
        ")->fetchAll(PDO::FETCH_COLUMN);

        // Make sure current year is always in list
        if (!in_array(date('Y'), $years)) {
            array_unshift($years, (int)date('Y'));
        }

        require __DIR__ . '/../../resources/views/admin_holidays.php';
    }

    public static function storeHoliday()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $date = $_POST['holiday_date'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'national';

        if (!$date || !$name) {
            $_SESSION['error'] = "Date and name are required.";
            header("Location: /leave-system/public/admin/holidays");
            exit;
        }

        // Duplicate check
        $check = $db->prepare("SELECT id FROM holidays WHERE holiday_date = :date LIMIT 1");
        $check->execute(['date' => $date]);
        if ($check->fetch()) {
            $_SESSION['error'] = "A holiday already exists on " . date('d M Y', strtotime($date)) . ".";
            header("Location: /leave-system/public/admin/holidays?year=" . date('Y', strtotime($date)));
            exit;
        }

        $stmt = $db->prepare("INSERT INTO holidays (holiday_date, name, type) VALUES (:date, :name, :type)");
        $stmt->execute(['date' => $date, 'name' => $name, 'type' => $type]);

        $_SESSION['success'] = "Holiday \"$name\" added.";
        header("Location: /leave-system/public/admin/holidays?year=" . date('Y', strtotime($date)));
        exit;
    }

    public static function updateHoliday(int $id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $date = $_POST['holiday_date'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $type = $_POST['type'] ?? 'national';

        if (!$date || !$name) {
            $_SESSION['error'] = "Date and name are required.";
            header("Location: /leave-system/public/admin/holidays");
            exit;
        }

        // Duplicate check excluding self
        $check = $db->prepare("SELECT id FROM holidays WHERE holiday_date = :date AND id != :id LIMIT 1");
        $check->execute(['date' => $date, 'id' => $id]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Another holiday already exists on " . date('d M Y', strtotime($date)) . ".";
            header("Location: /leave-system/public/admin/holidays?year=" . date('Y', strtotime($date)));
            exit;
        }

        $stmt = $db->prepare("UPDATE holidays SET holiday_date = :date, name = :name, type = :type WHERE id = :id");
        $stmt->execute(['date' => $date, 'name' => $name, 'type' => $type, 'id' => $id]);

        $_SESSION['success'] = "Holiday updated.";
        header("Location: /leave-system/public/admin/holidays?year=" . date('Y', strtotime($date)));
        exit;
    }

    public static function deleteHoliday($id)
    {
        self::authorizeAdmin();
        $db = Database::connect();

        // Get year before delete for redirect
        $stmt = $db->prepare("SELECT holiday_date FROM holidays WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        $year = $row ? date('Y', strtotime($row['holiday_date'])) : date('Y');

        $stmt = $db->prepare("DELETE FROM holidays WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = "Holiday deleted.";
        header("Location: /leave-system/public/admin/holidays?year=$year");
        exit;
    }


    public static function requests()
    {
        self::authorizeAdmin();

        $db = Database::connect();

        // ── Filters from GET ──
        $status     = $_GET['status']     ?? '';
        $search     = $_GET['search']     ?? '';
        $typeFilter = $_GET['leave_type'] ?? '';
        $dateFrom   = $_GET['date_from']  ?? '';
        $dateTo     = $_GET['date_to']    ?? '';

        // ── Status counts for tabs ──
        $countStmt = $db->query("
            SELECT status, COUNT(*) AS total
            FROM leave_requests
            GROUP BY status
        ");
        $countsRaw = $countStmt->fetchAll(PDO::FETCH_ASSOC);
        $counts = ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'cancelled' => 0];
        foreach ($countsRaw as $row) {
            $counts[$row['status']] = (int)$row['total'];
            $counts['all'] += (int)$row['total'];
        }

        // ── Leave types for filter dropdown ──
        $leaveTypes = $db->query("SELECT id, name FROM leave_types ORDER BY name ASC")
            ->fetchAll(PDO::FETCH_ASSOC);

        // ── Main query ──
        $query = "
            SELECT
                lr.id,
                lr.start_date,
                lr.end_date,
                lr.total_days,
                lr.status,
                lr.created_at,
                lr.approved_at,
                u.name       AS employee,
                u.id         AS employee_id,
                d.name       AS department,
                lt.name      AS leave_type,
                lt.id        AS leave_type_id
            FROM leave_requests lr
            JOIN users u        ON lr.employee_id   = u.id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE 1=1
        ";

        $params = [];

        if ($status) {
            $query .= " AND lr.status = :status";
            $params['status'] = $status;
        }

        if ($search) {
            $query .= " AND u.name LIKE :search";
            $params['search'] = "%$search%";
        }

        if ($typeFilter) {
            $query .= " AND lt.id = :type";
            $params['type'] = $typeFilter;
        }

        if ($dateFrom) {
            $query .= " AND lr.start_date >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $query .= " AND lr.end_date <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $query .= " ORDER BY lr.created_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/admin_requests.php';
    }

    /* ==========================================================
       ADMIN – SETTINGS
    ========================================================== */

    public static function settings()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $rows = $db->query("SELECT `key`, `value` FROM settings")
            ->fetchAll(PDO::FETCH_KEY_PAIR);
        $settings = $rows;

        require __DIR__ . '/../../resources/views/admin_settings.php';
    }

    public static function saveSettings()
    {
        self::authorizeAdmin();
        $db = Database::connect();

        $fields = [
            'mail_from_name',
            'mail_from_email',
            'hr_email',
            'smtp_host',
            'smtp_port',
            'smtp_user',
            'smtp_encryption',
        ];

        foreach ($fields as $key) {
            $value = trim($_POST[$key] ?? '');
            $stmt  = $db->prepare("
                INSERT INTO settings (`key`, `value`, `group`)
                VALUES (:k, :v, 'email')
                ON DUPLICATE KEY UPDATE `value` = :v2
            ");
            $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
        }

        // Password — only update if provided
        if (!empty($_POST['smtp_pass'])) {
            $stmt = $db->prepare("
                INSERT INTO settings (`key`, `value`, `group`)
                VALUES ('smtp_pass', :v, 'email')
                ON DUPLICATE KEY UPDATE `value` = :v2
            ");
            $val = $_POST['smtp_pass'];
            $stmt->execute(['v' => $val, 'v2' => $val]);
        }

        $_SESSION['success'] = "Settings saved.";
        header("Location: /leave-system/public/admin/settings");
        exit;
    }

    public static function testEmail()
    {
        self::authorizeAdmin();

        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'message' => 'Invalid email address.']);
            exit;
        }

        require_once __DIR__ . '/../Services/MailService.php';
        $result = MailService::sendTest($email);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}

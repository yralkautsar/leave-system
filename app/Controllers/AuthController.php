<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Services/Database.php';
require_once __DIR__ . '/../Services/MailService.php';

class AuthController
{
    // =========================
    // LOGIN PAGE
    // =========================
    public static function loginPage()
    {
        require __DIR__ . '/../../resources/views/login.php';
    }

    // =========================
    // LOGIN PROCESS
    // =========================
    public static function loginProcess()
    {
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $db   = Database::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = "Incorrect email or password.";
            header("Location: /login");
            exit;
        }

        if (!$user['is_active']) {
            $_SESSION['error'] = "Your account has been suspended. Please contact HR.";
            header("Location: /login");
            exit;
        }

        $_SESSION['user'] = [
            'id'        => $user['id'],
            'name'      => $user['name'],
            'nickname'  => $user['nickname']  ?? null,
            'religion'  => $user['religion']  ?? null,
            'role'      => $user['role'],
            'is_active' => $user['is_active'],
        ];

        header("Location: /dashboard");
        exit;
    }

    // =========================
    // FORGOT PASSWORD PAGE
    // =========================
    public static function forgotPasswordPage()
    {
        require __DIR__ . '/../../resources/views/forgot_password.php';
    }

    // =========================
    // FORGOT PASSWORD PROCESS
    // =========================
    public static function forgotPasswordProcess()
    {
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Please enter a valid email address.";
            header("Location: /forgot-password");
            exit;
        }

        $db   = Database::connect();
        $stmt = $db->prepare("SELECT id, name, email FROM users WHERE email = :email AND is_active = 1 LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token     = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $db->prepare("DELETE FROM password_resets WHERE email = :email")
                ->execute(['email' => $email]);

            $db->prepare("
                INSERT INTO password_resets (email, token, expires_at)
                VALUES (:email, :token, :expires)
            ")->execute(['email' => $email, 'token' => $token, 'expires' => $expiresAt]);

            $resetUrl = self::baseUrl() . "/reset-password?token={$token}";

            try {
                MailService::send(
                    $user['email'],
                    $user['name'],
                    '[ICS Leave] Password Reset Request',
                    self::resetEmailTemplate($user['name'], $resetUrl)
                );
            } catch (\Throwable $e) {
                error_log("[AuthController] Reset email failed: " . $e->getMessage());
            }
        }

        $_SESSION['success'] = "If that email is registered, you'll receive a reset link shortly. Check your inbox (and spam folder).";
        header("Location: /forgot-password");
        exit;
    }

    // =========================
    // RESET PASSWORD PAGE
    // =========================
    public static function resetPasswordPage()
    {
        $token      = $_GET['token'] ?? '';
        $validToken = false;

        if ($token) {
            $db   = Database::connect();
            $stmt = $db->prepare("
                SELECT id FROM password_resets
                WHERE token = :token
                AND expires_at > NOW()
                AND used_at IS NULL
                LIMIT 1
            ");
            $stmt->execute(['token' => $token]);
            $validToken = (bool)$stmt->fetch();
        }

        require __DIR__ . '/../../resources/views/reset_password.php';
    }

    // =========================
    // RESET PASSWORD PROCESS
    // =========================
    public static function resetPasswordProcess()
    {
        $token    = $_POST['token']            ?? '';
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!$token) {
            header("Location: /login");
            exit;
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = "Password must be at least 8 characters.";
            header("Location: /reset-password?token={$token}");
            exit;
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: /reset-password?token={$token}");
            exit;
        }

        $db   = Database::connect();
        $stmt = $db->prepare("
            SELECT * FROM password_resets
            WHERE token = :token
            AND expires_at > NOW()
            AND used_at IS NULL
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            $_SESSION['error'] = "This reset link is invalid or has expired.";
            header("Location: /forgot-password");
            exit;
        }

        $db->beginTransaction();
        try {
            $db->prepare("UPDATE users SET password_hash = :hash WHERE email = :email")
                ->execute([
                    'hash'  => password_hash($password, PASSWORD_DEFAULT),
                    'email' => $reset['email'],
                ]);

            $db->prepare("UPDATE password_resets SET used_at = NOW() WHERE token = :token")
                ->execute(['token' => $token]);

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            $_SESSION['error'] = "Something went wrong. Please try again.";
            header("Location: /reset-password?token={$token}");
            exit;
        }

        $_SESSION['success'] = "Password updated successfully. Please sign in.";
        header("Location: /login");
        exit;
    }

    // =========================
    // DASHBOARD ROUTER
    // =========================
    public static function dashboard()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }

        $db   = Database::connect();
        $user = $_SESSION['user'];

        if ($user['role'] !== 'admin_approver') {
            $stmt = $db->prepare("
                SELECT lb.remaining_days, lb.total_days, lb.used_days,
                       lt.name AS leave_type,
                       lp.name AS period_name, lp.end_date
                FROM leave_balances lb
                JOIN leave_types   lt ON lb.leave_type_id   = lt.id
                JOIN leave_periods lp ON lb.leave_period_id = lp.id
                WHERE lb.employee_id = :uid
                AND lp.start_date <= CURDATE()
                AND lp.end_date   >= CURDATE()
                ORDER BY lp.start_date ASC, lt.name ASC
            ");
            $stmt->execute(['uid' => $user['id']]);
            $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Comp leave available balance
            $compStmt = $db->prepare("
                SELECT COALESCE(SUM(days_remaining), 0)
                FROM comp_claims
                WHERE employee_id = :uid
                  AND status      = 'approved'
                  AND expires_at  > CURDATE()
            ");
            $compStmt->execute(['uid' => $user['id']]);
            $compBalance = (float)$compStmt->fetchColumn();

            // Admin-granted balances (event-based, floating)
            $grantStmt = $db->prepare("
                SELECT lt.name AS leave_type,
                       lb.total_days, lb.used_days,
                       (lb.total_days - lb.used_days) AS remaining_days
                FROM leave_balances lb
                JOIN leave_types lt ON lb.leave_type_id = lt.id
                WHERE lb.employee_id      = :uid
                  AND lt.balance_source   = 'admin_grant'
                  AND lb.leave_period_id  IS NULL
                ORDER BY lt.name ASC
            ");
            $grantStmt->execute(['uid' => $user['id']]);
            $grantBalances = $grantStmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $db->prepare("SELECT status, COUNT(*) total FROM leave_requests WHERE employee_id = :uid GROUP BY status");
            $stmt->execute(['uid' => $user['id']]);
            $statsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stats = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
            foreach ($statsRaw as $row) $stats[$row['status']] = $row['total'];

            $stmt = $db->prepare("
                SELECT lr.*, lt.name AS leave_type,
                       COALESCE(lp.name, 'Compensate Leave') AS period_name
                FROM leave_requests lr
                JOIN leave_types   lt ON lr.leave_type_id   = lt.id
                LEFT JOIN leave_periods lp ON lr.leave_period_id = lp.id
                WHERE lr.employee_id = :uid
                ORDER BY lr.created_at DESC LIMIT 5
            ");
            $stmt->execute(['uid' => $user['id']]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Notifications: approved/rejected in last 7 days
            $stmt = $db->prepare("
                SELECT lr.id, lr.status, lr.start_date, lr.end_date,
                       lr.total_days, lr.rejection_reason,
                       lt.name AS leave_type,
                       lr.approved_at
                FROM leave_requests lr
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                WHERE lr.employee_id = :uid
                AND lr.status IN ('approved','rejected')
                AND lr.approved_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY lr.approved_at DESC
            ");
            $stmt->execute(['uid' => $user['id']]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            require __DIR__ . '/../../resources/views/dashboard_employee.php';
            return;
        }

        // ── Stat card counts ─────────────────────────────────────
        $statsRaw = $db->query("
            SELECT status, COUNT(*) total FROM leave_requests GROUP BY status
        ")->fetchAll(PDO::FETCH_ASSOC);
        $stats = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($statsRaw as $row) $stats[$row['status']] = $row['total'];

        $onLeaveToday = $db->query("
            SELECT COUNT(DISTINCT employee_id) FROM leave_requests
            WHERE status = 'approved' AND CURDATE() BETWEEN start_date AND end_date
        ")->fetchColumn();

        $totalEmployees = $db->query("
            SELECT COUNT(*) FROM users WHERE role = 'employee' AND is_active = 1
        ")->fetchColumn();

        $pendingCompClaims = (int)$db->query("
            SELECT COUNT(*) FROM comp_claims WHERE status = 'pending'
        ")->fetchColumn();

        // ── Departments list for filter dropdown ─────────────────
        $departments = $db->query("
            SELECT id, name FROM departments ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $deptFilter = isset($_GET['dept']) && (int)$_GET['dept'] > 0
            ? (int)$_GET['dept'] : null;

        // ── Pending list for dashboard (oldest first, max 15) ─────
        $pendingSql = "
            SELECT lr.id, lr.start_date, lr.end_date, lr.total_days, lr.duration_type, lr.created_at,
                   u.name  AS employee_name,
                   lt.name AS leave_type,
                   d.name  AS department
            FROM leave_requests lr
            JOIN users       u  ON lr.employee_id   = u.id
            JOIN leave_types lt ON lr.leave_type_id  = lt.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE lr.status = 'pending'
        ";
        $pendingParams = [];
        if ($deptFilter) {
            $pendingSql .= " AND u.department_id = :dept";
            $pendingParams['dept'] = $deptFilter;
        }
        $pendingSql .= " ORDER BY lr.created_at ASC LIMIT 15";
        $stmt = $db->prepare($pendingSql);
        $stmt->execute($pendingParams);
        $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ── On leave today — detail list for right panel ──────────
        $onLeaveSql = "
            SELECT
                u.name  AS employee_name,
                lt.name AS leave_type,
                lr.start_date,
                lr.end_date,
                d.name  AS department,
                DATEDIFF(lr.end_date, CURDATE()) AS days_left
            FROM leave_requests lr
            JOIN users       u  ON lr.employee_id   = u.id
            JOIN leave_types lt ON lr.leave_type_id  = lt.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE lr.status = 'approved'
            AND CURDATE() BETWEEN lr.start_date AND lr.end_date
        ";
        $onLeaveParams = [];
        if ($deptFilter) {
            $onLeaveSql .= " AND u.department_id = :dept";
            $onLeaveParams['dept'] = $deptFilter;
        }
        $onLeaveSql .= " ORDER BY lr.end_date ASC";
        $stmt = $db->prepare($onLeaveSql);
        $stmt->execute($onLeaveParams);
        $onLeaveTodayList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__ . '/../../resources/views/dashboard_admin.php';
    }

    // =========================
    // LOGOUT
    // =========================
    public static function logout()
    {
        session_destroy();
        header("Location: /login");
        exit;
    }

    private static function baseUrl(): string
    {
        $s = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return "{$s}://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "";
    }

    private static function resetEmailTemplate(string $name, string $url): string
    {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
<tr><td align="center">
<table width="520" cellpadding="0" cellspacing="0" style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.07);">
  <tr><td style="background:#0f172a;padding:24px 32px;">
    <span style="color:white;font-size:20px;font-weight:800;">ICS</span>
    <span style="color:#f97316;font-size:20px;font-weight:800;"> ·</span>
    <span style="color:#94a3b8;font-size:13px;margin-left:8px;">Leave Management</span>
  </td></tr>
  <tr><td style="padding:32px;">
    <h2 style="margin:0 0 12px;font-size:18px;font-weight:700;color:#0f172a;">Password Reset Request</h2>
    <p style="color:#374151;font-size:14px;line-height:1.6;margin:0 0 20px;">
      Hi {$name},<br><br>
      We received a request to reset your password. Click the button below to set a new one.
      This link expires in <strong>1 hour</strong>.
    </p>
    <p style="margin:0 0 24px;">
      <a href="{$url}" style="display:inline-block;background:#f97316;color:white;padding:13px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;">Reset My Password</a>
    </p>
    <p style="color:#94a3b8;font-size:12px;margin:0;line-height:1.6;">
      If you did not request a password reset, ignore this email — your password will not change.
    </p>
  </td></tr>
  <tr><td style="background:#f8fafc;padding:16px 32px;border-top:1px solid #e5e7eb;">
    <p style="margin:0;font-size:12px;color:#94a3b8;">Automated message from ICS Leave Management. © {$year} ICS Travel Group</p>
  </td></tr>
</table>
</td></tr></table>
</body></html>
HTML;
    }
}

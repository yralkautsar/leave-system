<?php
session_start();

require_once __DIR__ . '/../app/Services/Database.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/LeaveController.php';
require_once __DIR__ . '/../app/Controllers/CalendarController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/* ==========================================================
   HOME
========================================================== */
if ($uri === '/leave-system/public/' || $uri === '/leave-system/public') {
    header("Location: /leave-system/public/login");
    exit;
}

/* ==========================================================
   AUTH
========================================================== */

if ($uri === '/leave-system/public/login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    AuthController::loginPage();
    exit;
}

if ($uri === '/leave-system/public/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::loginProcess();
    exit;
}

if ($uri === '/leave-system/public/forgot-password' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    AuthController::forgotPasswordPage();
    exit;
}

if ($uri === '/leave-system/public/forgot-password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::forgotPasswordProcess();
    exit;
}

if ($uri === '/leave-system/public/reset-password' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    AuthController::resetPasswordPage();
    exit;
}

if ($uri === '/leave-system/public/reset-password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::resetPasswordProcess();
    exit;
}

if ($uri === '/leave-system/public/logout') {
    AuthController::logout();
    exit;
}

/* ==========================================================
   DASHBOARD
========================================================== */

if ($uri === '/leave-system/public/dashboard') {
    AuthController::dashboard();
    exit;
}

/* ==========================================================
   CALENDAR
========================================================== */

if ($uri === '/leave-system/public/calendar') {
    CalendarController::calendar();
    exit;
}

/* ==========================================================
   LEAVE (EMPLOYEE)
========================================================== */

if ($uri === '/leave-system/public/leave') {
    LeaveController::create();
    exit;
}

if ($uri === '/leave-system/public/leave-store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::store();
    exit;
}

if ($uri === '/leave-system/public/my-history') {
    LeaveController::myHistory();
    exit;
}

if ($uri === '/leave-system/public/cancel' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::cancel((int)$_POST['id']);
    exit;
}

/* ==========================================================
   APPROVAL (ADMIN)
========================================================== */

if ($uri === '/leave-system/public/approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::approve((int)$_POST['id']);
    exit;
}

if ($uri === '/leave-system/public/reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::reject((int)$_POST['id']);
    exit;
}

/* ==========================================================
   ADMIN – DEPARTMENTS
========================================================== */

if ($uri === '/leave-system/public/admin/departments') {
    LeaveController::departments();
    exit;
}

if ($uri === '/leave-system/public/admin/departments/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::storeDepartment();
    exit;
}

if (
    preg_match('#^/leave-system/public/admin/departments/update/(\d+)$#', $uri, $m)
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    LeaveController::updateDepartment((int)$m[1]);
    exit;
}

if ($uri === '/leave-system/public/admin/departments/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::deleteDepartment((int)$_POST['id']);
    exit;
}

/* ==========================================================
   ADMIN – JOB TITLES
========================================================== */

if ($uri === '/leave-system/public/admin/job-titles') {
    LeaveController::jobTitles();
    exit;
}

if ($uri === '/leave-system/public/admin/job-titles/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::storeJobTitle();
    exit;
}

if (
    preg_match('#^/leave-system/public/admin/job-titles/update/(\d+)$#', $uri, $m)
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    LeaveController::updateJobTitle((int)$m[1]);
    exit;
}

if ($uri === '/leave-system/public/admin/job-titles/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::deleteJobTitle((int)$_POST['id']);
    exit;
}

/* ==========================================================
   ADMIN – USERS
========================================================== */

if ($uri === '/leave-system/public/admin/users') {
    LeaveController::users();
    exit;
}

if ($uri === '/leave-system/public/admin/users/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::storeUser();
    exit;
}

if (
    preg_match('#^/leave-system/public/admin/users/update/(\d+)$#', $uri, $m)
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    LeaveController::updateUser((int)$m[1]);
    exit;
}

if ($uri === '/leave-system/public/admin/users/toggle-status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::toggleUserStatus();
    exit;
}

/* ==========================================================
   ADMIN – LEAVE PERIODS
========================================================== */

if ($uri === '/leave-system/public/admin/periods') {
    LeaveController::periods();
    exit;
}

if ($uri === '/leave-system/public/admin/periods/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::storePeriod();
    exit;
}

if ($uri === '/leave-system/public/admin/periods/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::deletePeriod((int)$_POST['id']);
    exit;
}

if ($uri === '/leave-system/public/admin/periods/generate' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::generateBalances();
    exit;
}

/* ==========================================================
   ADMIN – BALANCES
========================================================== */

if ($uri === '/leave-system/public/admin/balances') {
    LeaveController::balances();
    exit;
}

if ($uri === '/leave-system/public/admin/balances/export' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    LeaveController::exportBalances();
    exit;
}

if ($uri === '/leave-system/public/admin/balance-adjust' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::adjustBalance();
    exit;
}

if ($uri === '/leave-system/public/admin/balance-history' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    LeaveController::balanceHistory();
    exit;
}

if ($uri === '/leave-system/public/api/available-balances' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    LeaveController::availableBalances();
    exit;
}

/* ==========================================================
   ADMIN – LEAVE TYPES
========================================================== */

if ($uri === '/leave-system/public/admin/leave-types') {
    LeaveController::leaveTypes();
    exit;
}

if ($uri === '/leave-system/public/admin/leave-types/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::storeLeaveType();
    exit;
}

if (
    preg_match('#^/leave-system/public/admin/leave-types/(\d+)/update$#', $uri, $m)
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    LeaveController::updateLeaveType((int)$m[1]);
    exit;
}

if ($uri === '/leave-system/public/admin/leave-types/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::deleteLeaveType((int)$_POST['id']);
    exit;
}

/* ==========================================================
   ADMIN – HOLIDAYS
========================================================== */

if ($uri === '/leave-system/public/admin/holidays') {
    LeaveController::holidays();
    exit;
}

if ($uri === '/leave-system/public/admin/holidays/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::storeHoliday();
    exit;
}

if (
    preg_match('#^/leave-system/public/admin/holidays/update/(\d+)$#', $uri, $m)
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    LeaveController::updateHoliday((int)$m[1]);
    exit;
}

if ($uri === '/leave-system/public/admin/holidays/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::deleteHoliday((int)$_POST['id']);
    exit;
}

/* ==========================================================
   LEAVE REQUESTS
========================================================== */

if ($uri === '/leave-system/public/admin/requests') {
    LeaveController::requests();
    exit;
}

if ($uri === '/leave-system/public/admin/requests/export' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    LeaveController::exportRequests();
    exit;
}

if (preg_match('#^/leave-system/public/admin/requests/(\d+)/detail$#', $uri, $m)) {
    LeaveController::requestDetail((int)$m[1]);
    exit;
}

if (
    preg_match('#^/leave-system/public/admin/requests/(\d+)/revoke$#', $uri, $m)
    && $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    LeaveController::revoke((int)$m[1]);
    exit;
}

/* ==========================================================
   ADMIN – SETTINGS
========================================================== */

if ($uri === '/leave-system/public/admin/settings') {
    LeaveController::settings();
    exit;
}

if ($uri === '/leave-system/public/admin/settings/save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::saveSettings();
    exit;
}

if ($uri === '/leave-system/public/admin/settings/test-email' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    LeaveController::testEmail();
    exit;
}

/* ==========================================================
   API – PENDING COUNT
========================================================== */

if ($uri === '/leave-system/public/pending-count') {

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin_approver') {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $db = Database::connect();
    $stmt = $db->query("SELECT COUNT(*) FROM leave_requests WHERE status='pending'");
    $count = (int)$stmt->fetchColumn();

    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
    exit;
}

/* ==========================================================
   404
========================================================== */

http_response_code(404);
echo "404 Not Found";
exit;

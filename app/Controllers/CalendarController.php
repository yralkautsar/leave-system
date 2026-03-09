<?php

require_once __DIR__ . '/../Services/Database.php';

class CalendarController
{
    public static function calendar()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /login");
            exit;
        }

        $db   = Database::connect();
        $user = $_SESSION['user'];

        // ── Month ─────────────────────────────────────────────────
        $month      = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : date('Y-m');
        $monthStart = $month . '-01';
        $monthEnd   = date('Y-m-t', strtotime($monthStart));

        // ── Filters (admin only) ──────────────────────────────────
        $deptFilter = ($user['role'] === 'admin_approver') ? ($_GET['dept'] ?? '') : '';

        // ── Approved leaves ───────────────────────────────────────
        $sql = "
            SELECT
                lr.start_date,
                lr.end_date,
                lr.total_days,
                u.name,
                u.id        AS employee_id,
                lt.name     AS leave_type,
                d.name      AS department
            FROM leave_requests lr
            JOIN users       u  ON lr.employee_id   = u.id
            JOIN leave_types lt ON lr.leave_type_id  = lt.id
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE lr.status = 'approved'
            AND lr.start_date <= :end
            AND lr.end_date   >= :start
        ";
        $params = ['start' => $monthStart, 'end' => $monthEnd];

        // Employee only sees their own leaves on calendar
        if ($user['role'] !== 'admin_approver') {
            $sql .= " AND lr.employee_id = :uid";
            $params['uid'] = $user['id'];
        } elseif ($deptFilter) {
            $sql .= " AND d.id = :dept";
            $params['dept'] = $deptFilter;
        }

        $sql .= " ORDER BY lr.start_date ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ── Holidays ──────────────────────────────────────────────
        $stmt = $db->prepare("
            SELECT holiday_date AS date, name, type
            FROM holidays
            WHERE holiday_date BETWEEN :start AND :end
            ORDER BY holiday_date ASC
        ");
        $stmt->execute(['start' => $monthStart, 'end' => $monthEnd]);
        $holidays = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ── All leave types (for legend) ──────────────────────────
        $leaveTypes = $db->query("SELECT id, name FROM leave_types ORDER BY name ASC")
            ->fetchAll(PDO::FETCH_ASSOC);

        // ── Departments (for filter dropdown — admin only) ────────
        $departments = [];
        if ($user['role'] === 'admin_approver') {
            $departments = $db->query("SELECT id, name FROM departments ORDER BY name ASC")
                ->fetchAll(PDO::FETCH_ASSOC);
        }

        require __DIR__ . '/../../resources/views/calendar.php';
    }
}

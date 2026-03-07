<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/LeaveCalculator.php';

class LeaveBalanceGenerator
{
    public static function generateForPeriod(int $periodId)
    {
        $db = Database::connect();

        // Ambil periode
        $stmt = $db->prepare("SELECT * FROM leave_periods WHERE id = :id");
        $stmt->execute(['id' => $periodId]);
        $period = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$period) {
            throw new Exception("Periode tidak ditemukan.");
        }

        $periodStart = $period['start_date'];
        $periodEnd   = $period['end_date'];

        // Ambil ID Annual Leave
        $stmt = $db->prepare("SELECT id FROM leave_types WHERE name = 'Annual Leave' LIMIT 1");
        $stmt->execute();
        $annualLeaveId = $stmt->fetchColumn();

        if (!$annualLeaveId) {
            throw new Exception("Annual Leave belum dibuat.");
        }

        // Ambil semua user aktif
        $stmt = $db->query("SELECT * FROM users WHERE is_active = 1");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {

            // Hitung prorated leave
            $proratedDays = LeaveBalanceGenerator::calculateProratedLeave(
                $user['join_date'],
                $periodStart,
                $periodEnd
            );

            // Cek apakah sudah pernah dibuat
            $check = $db->prepare("
                SELECT COUNT(*) 
                FROM leave_balances
                WHERE employee_id = :uid
                AND leave_type_id = :ltid
                AND leave_period_id = :pid
            ");

            $check->execute([
                'uid' => $user['id'],
                'ltid' => $annualLeaveId,
                'pid' => $periodId
            ]);

            if ($check->fetchColumn() > 0) {
                continue;
            }

            // Insert balance
            $insert = $db->prepare("
                INSERT INTO leave_balances
                (employee_id, leave_type_id, leave_period_id, total_quota, used_days, remaining_days)
                VALUES
                (:uid, :ltid, :pid, :quota, 0, :quota)
            ");

            $insert->execute([
                'uid' => $user['id'],
                'ltid' => $annualLeaveId,
                'pid' => $periodId,
                'quota' => $proratedDays
            ]);
        }
    }

    public static function calculateProratedLeave($joinDate, $periodStart, $periodEnd)
    {
        $join = new DateTime($joinDate);
        $start = new DateTime($periodStart);
        $end = new DateTime($periodEnd);

        if ($join > $end) {
            return 0;
        }

        $effectiveStart = $join > $start ? $join : $start;

        $months = 0;
        $current = clone $effectiveStart;
        $current->modify('first day of this month');

        while ($current <= $end) {

            if (
                $current->format('Y-m') == $effectiveStart->format('Y-m') &&
                (int)$effectiveStart->format('d') >= 15
            ) {
                $current->modify('+1 month');
                continue;
            }

            $months++;
            $current->modify('+1 month');
        }

        if ($months > 15) {
            $months = 15;
        }

        return (float)$months;
    }
}
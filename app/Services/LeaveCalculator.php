<?php

require_once __DIR__ . '/Database.php';

class LeaveCalculator
{
    public static function calculateWorkingDays(int $userId, string $startDate, string $endDate): int
    {
        $db = Database::connect();

        // ==============================
        // 1️ Ambil work schedule user
        // ==============================
        $stmt = $db->prepare("
            SELECT work_schedule_id 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $userId]);
        $scheduleId = $stmt->fetchColumn();

        if (!$scheduleId) {
            throw new Exception("User tidak memiliki work schedule.");
        }

        // ==============================
        // 2️ Ambil daftar weekday aktif
        // ==============================
        $stmt = $db->prepare("
            SELECT weekday 
            FROM work_schedule_days 
            WHERE schedule_id = :sid
        ");
        $stmt->execute(['sid' => $scheduleId]);
        $workingDaysList = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($workingDaysList)) {
            throw new Exception("Work schedule tidak memiliki hari kerja.");
        }

        // ==============================
        // 3️ Ambil semua holiday dalam range
        // ==============================
        $stmt = $db->prepare("
            SELECT holiday_date 
            FROM holidays 
            WHERE holiday_date BETWEEN :start AND :end
        ");
        $stmt->execute([
            'start' => $startDate,
            'end'   => $endDate
        ]);
        $holidayList = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // ==============================
        // 4️ Iterasi tanggal
        // ==============================
        $start = new DateTime($startDate);
        $end   = new DateTime($endDate);
        $end->modify('+1 day'); // agar endDate ikut dihitung

        $interval = new DateInterval('P1D');
        $period   = new DatePeriod($start, $interval, $end);

        $workingDays = 0;

        foreach ($period as $date) {

            $weekday = (int)$date->format('N'); // 1 (Mon) - 7 (Sun)
            $formattedDate = $date->format('Y-m-d');

            // Jika bukan hari kerja user → skip
            if (!in_array($weekday, $workingDaysList)) {
                continue;
            }

            // Jika hari libur nasional → skip
            if (in_array($formattedDate, $holidayList)) {
                continue;
            }

            $workingDays++;
        }

        return $workingDays;
    }
}
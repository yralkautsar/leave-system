<?php

require_once __DIR__ . '/Database.php';

class LeaveCalculator
{
    public static function calculateWorkingDays(int $userId, string $startDate, string $endDate): int
    {
        $db = Database::connect();

        // ==============================
        // 1. Work schedule
        // ==============================
        $stmt = $db->prepare("
            SELECT u.work_schedule_id, u.religion
            FROM users u
            WHERE u.id = :id
        ");
        $stmt->execute(['id' => $userId]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userRow || !$userRow['work_schedule_id']) {
            throw new Exception("User does not have a work schedule assigned.");
        }

        $scheduleId   = $userRow['work_schedule_id'];
        $userReligion = $userRow['religion'] ?? null;

        // ==============================
        // 2. Working weekdays
        // ==============================
        $stmt = $db->prepare("
            SELECT weekday
            FROM work_schedule_days
            WHERE schedule_id = :sid
        ");
        $stmt->execute(['sid' => $scheduleId]);
        $workingDaysList = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($workingDaysList)) {
            throw new Exception("Work schedule has no working days configured.");
        }

        // ==============================
        // 3. Holidays applicable to this user
        //
        //    A holiday applies to this user if:
        //    a) It has NO rows in holiday_religions (applies to all), OR
        //    b) It has a row matching the user's religion
        // ==============================
        $stmt = $db->prepare("
            SELECT h.holiday_date
            FROM holidays h
            WHERE h.holiday_date BETWEEN :start AND :end
            AND (
                -- applies to all: no religion rows exist for this holiday
                NOT EXISTS (
                    SELECT 1 FROM holiday_religions hr
                    WHERE hr.holiday_id = h.id
                )
                OR
                -- applies to this user's religion
                EXISTS (
                    SELECT 1 FROM holiday_religions hr
                    WHERE hr.holiday_id = h.id
                    AND hr.religion     = :religion
                )
            )
        ");
        $stmt->execute([
            'start'    => $startDate,
            'end'      => $endDate,
            'religion' => $userReligion ?? '',
        ]);
        $holidayList = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // ==============================
        // 4. Iterate dates
        // ==============================
        $start = new DateTime($startDate);
        $end   = new DateTime($endDate);
        $end->modify('+1 day');

        $interval    = new DateInterval('P1D');
        $period      = new DatePeriod($start, $interval, $end);
        $workingDays = 0;

        foreach ($period as $date) {
            $weekday       = (int)$date->format('N'); // 1=Mon..7=Sun
            $formattedDate = $date->format('Y-m-d');

            if (!in_array($weekday, $workingDaysList)) continue;
            if (in_array($formattedDate, $holidayList))  continue;

            $workingDays++;
        }

        return $workingDays;
    }
}

#!/usr/bin/env php
<?php

// =============================================================
//  deploy-replace.php
//  Run ONCE before going live to replace all hardcoded
//  /leave-system/public URLs with your production domain.
//
//  Usage:
//    php deploy-replace.php
//
//  Run from the ROOT of the project (one level above /public).
// =============================================================

$OLD = '/leave-system/public';   // ← current hardcoded prefix
$NEW = '';                        // ← production prefix (empty = root domain)

// Files to process
$targets = [
    // Controllers
    'app/Controllers/AuthController.php',
    'app/Controllers/LeaveController.php',
    'app/Controllers/CalendarController.php',
    // Views
    'resources/views/layout.php',
    'resources/views/login.php',
    'resources/views/forgot_password.php',
    'resources/views/reset_password.php',
    'resources/views/dashboard_admin.php',
    'resources/views/dashboard_employee.php',
    'resources/views/admin_users.php',
    'resources/views/admin_departments.php',
    'resources/views/admin_jobtitles.php',
    'resources/views/admin_periods.php',
    'resources/views/admin_balances.php',
    'resources/views/admin_leave_types.php',
    'resources/views/admin_holidays.php',
    'resources/views/admin_requests.php',
    'resources/views/admin_leave_grants.php',
    'resources/views/admin_comp_claims.php',
    'resources/views/admin_settings.php',
    'resources/views/leave_create.php',
    'resources/views/leave_history.php',
    'resources/views/my_data.php',
    'resources/views/comp_claim.php',
    'resources/views/calendar.php',
];

$replaced = 0;
$skipped  = 0;

foreach ($targets as $rel) {
    if (!file_exists($rel)) {
        echo "  SKIP (not found): $rel\n";
        $skipped++;
        continue;
    }

    $original = file_get_contents($rel);
    $updated  = str_replace($OLD, $NEW, $original);

    if ($original === $updated) {
        echo "  OK (no change): $rel\n";
        continue;
    }

    file_put_contents($rel, $updated);
    $count = substr_count($original, $OLD);
    echo "  REPLACED $count occurrence(s): $rel\n";
    $replaced++;
}

echo "\n✅ Done. $replaced file(s) updated, $skipped skipped.\n";
echo "   Old prefix : \"$OLD\"\n";
echo "   New prefix : \"" . ($NEW ?: '(empty — root)') . "\"\n";

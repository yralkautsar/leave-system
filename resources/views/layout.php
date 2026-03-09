<?php
if (!isset($_SESSION['user'])) {
    header("Location: /leave-system/public/login");
    exit;
}

if (!$_SESSION['user']['is_active']) {
    session_destroy();
    header("Location: /leave-system/public/login");
    exit;
}

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$isAdmin     = ($_SESSION['user']['role'] === 'admin_approver');

/* ── SVG icon helper ── */
function icon(string $name): string
{
    $icons = [

        'dashboard' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>',

        'requests'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>',

        'users'     => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',

        'dept'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>',

        'jobtitle'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>',

        'periods'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',

        'balances'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6"/></svg>',

        'holidays'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>',

        'calendar'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>',

        'submit'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',

        'history'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>',

        'logout'    => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',

        'user'      => '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',

        'settings'  => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',

        'comp'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',

        'grant'     => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>',
    ];

    return $icons[$name] ?? '';
}

/* ── Active helper ── */
function isActive(string $path, string $match, bool $exact = false): string
{
    if ($exact) return $path === $match ? 'active' : '';
    return str_contains($path, $match) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management — ICS Travel Group</title>
    <link rel="stylesheet" href="/leave-system/public/assets/css/admin.css">
</head>

<body>

    <div class="app">

        <!-- ════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->
        <aside class="sidebar" id="sidebar">

            <!-- Logo -->
            <div class="sidebar-logo">
                <img src="/leave-system/public/assets/white.png" alt="ICS Travel Group">
            </div>

            <!-- Navigation -->
            <nav class="nav">

                <!-- Dashboard — both roles -->
                <a href="/leave-system/public/dashboard"
                    class="nav-link <?= isActive($currentPath, '/leave-system/public/dashboard', true) ?>">
                    <?= icon('dashboard') ?>
                    <span>Dashboard</span>
                </a>

                <?php if ($isAdmin): ?>

                    <!-- ── Section: Requests ── -->
                    <div class="nav-section">Requests</div>

                    <a href="/leave-system/public/admin/requests"
                        class="nav-link <?= isActive($currentPath, '/admin/requests') ?>"
                        id="nav-requests">
                        <?= icon('requests') ?>
                        <span>Leave Requests</span>
                        <span class="nav-badge" id="pendingBadge" style="display:none;"></span>
                    </a>

                    <a href="/leave-system/public/admin/comp-claims"
                        class="nav-link <?= isActive($currentPath, '/admin/comp-claims') ?>">
                        <?= icon('comp') ?>
                        <span>Comp Claims</span>
                    </a>

                    <!-- ── Section: People ── -->
                    <div class="nav-section">People</div>

                    <a href="/leave-system/public/admin/users"
                        class="nav-link <?= isActive($currentPath, '/admin/users') ?>">
                        <?= icon('users') ?>
                        <span>Users</span>
                    </a>

                    <a href="/leave-system/public/admin/departments"
                        class="nav-link <?= isActive($currentPath, '/admin/departments') ?>">
                        <?= icon('dept') ?>
                        <span>Departments</span>
                    </a>

                    <a href="/leave-system/public/admin/job-titles"
                        class="nav-link <?= isActive($currentPath, '/admin/job-titles') ?>">
                        <?= icon('jobtitle') ?>
                        <span>Job Titles</span>
                    </a>

                    <!-- ── Section: Leave Config ── -->
                    <div class="nav-section">Leave Config</div>

                    <a href="/leave-system/public/admin/periods"
                        class="nav-link <?= isActive($currentPath, '/admin/periods') || isActive($currentPath, '/admin/leave-types') ? 'active' : '' ?>">
                        <?= icon('periods') ?>
                        <span>Leave Periods</span>
                    </a>

                    <a href="/leave-system/public/admin/balances"
                        class="nav-link <?= isActive($currentPath, '/admin/balances') ?>">
                        <?= icon('balances') ?>
                        <span>Leave Balances</span>
                    </a>

                    <a href="/leave-system/public/admin/holidays"
                        class="nav-link <?= isActive($currentPath, '/admin/holidays') ?>">
                        <?= icon('holidays') ?>
                        <span>Public Holidays</span>
                    </a>

                    <a href="/leave-system/public/admin/leave-grants"
                        class="nav-link <?= isActive($currentPath, '/admin/leave-grants') ?>">
                        <?= icon('grant') ?>
                        <span>Leave Grants</span>
                    </a>

                <?php else: ?>

                    <!-- ── Section: My Leave ── -->
                    <div class="nav-section">My Leave</div>

                    <a href="/leave-system/public/leave"
                        class="nav-link <?= isActive($currentPath, '/leave-system/public/leave', true) ?>"
                        data-tooltip="Submit Leave">
                        <?= icon('submit') ?>
                        <span>Submit Leave</span>
                    </a>

                    <a href="/leave-system/public/my-data"
                        class="nav-link <?= isActive($currentPath, '/my-data') ?>"
                        data-tooltip="My Data">
                        <?= icon('user') ?>
                        <span>My Data</span>
                    </a>

                    <a href="/leave-system/public/comp-claim"
                        class="nav-link <?= isActive($currentPath, '/comp-claim') ?>"
                        data-tooltip="Compensate Leave">
                        <?= icon('comp') ?>
                        <span>Comp Leave</span>
                    </a>

                <?php endif; ?>

                <!-- Calendar — both roles -->
                <div class="nav-section">Overview</div>

                <a href="/leave-system/public/calendar"
                    class="nav-link <?= isActive($currentPath, 'calendar') ?>">
                    <?= icon('calendar') ?>
                    <span>Calendar</span>
                </a>

                <?php if ($isAdmin): ?>
                    <a href="/leave-system/public/admin/settings"
                        class="nav-link <?= isActive($currentPath, 'settings') ?>">
                        <?= icon('settings') ?>
                        <span>Settings</span>
                    </a>
                <?php endif; ?>

            </nav>

            <!-- User info + logout -->
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-user-avatar">
                        <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['user']['name']) ?></div>
                        <div class="sidebar-user-role"><?= $isAdmin ? 'Admin' : 'Employee' ?></div>
                    </div>
                </div>

                <a href="/leave-system/public/logout" class="sidebar-logout" title="Logout">
                    <?= icon('logout') ?>
                    <span>Logout</span>
                </a>
            </div>

        </aside>

        <!-- Mobile overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
        ════════════════════════════════════════ -->
        <main class="main">

            <!-- Topbar -->
            <?php
            $pageTitles = [
                '/leave-system/public/dashboard'         => 'Dashboard',
                '/leave-system/public/admin/requests'    => 'Leave Requests',
                '/leave-system/public/admin/users'       => 'Users',
                '/leave-system/public/admin/departments' => 'Departments',
                '/leave-system/public/admin/job-titles'  => 'Job Titles',
                '/leave-system/public/admin/periods'     => 'Leave Periods',
                '/leave-system/public/admin/leave-types' => 'Leave Types',
                '/leave-system/public/admin/balances'    => 'Leave Balances',
                '/leave-system/public/admin/holidays'    => 'Public Holidays',
                '/leave-system/public/admin/settings'    => 'Settings',
                '/leave-system/public/calendar'          => 'Calendar',
                '/leave-system/public/leave'             => 'Submit Leave',
                '/leave-system/public/my-data'           => 'My Data',
            ];
            $pageTitle = $pageTitles[$currentPath] ?? 'Leave Management';
            ?>
            <div class="topbar">
                <?php if (!$isAdmin): ?>
                    <button class="topbar-hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Menu">
                        <span></span><span></span><span></span>
                    </button>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>
                <span class="topbar-page"><?= htmlspecialchars($pageTitle) ?></span>
                <div></div>
            </div>

            <!-- Content -->
            <div class="content">

                <?php /* ── Global flash messages ── */
                foreach (['success' => 'flash-success', 'error' => 'flash-error', 'warning' => 'flash-warning'] as $key => $cls):
                    if (!empty($_SESSION[$key])): ?>
                        <div class="<?= $cls ?> flash-msg" id="flashMsg">
                            <span><?= htmlspecialchars($_SESSION[$key]) ?></span>
                            <button onclick="this.parentElement.remove()" class="flash-close">×</button>
                        </div>
                <?php unset($_SESSION[$key]);
                    endif;
                endforeach; ?>

                <?= $content ?>
            </div>

            <div id="globalModalRoot"></div>

        </main>

    </div><!-- .app -->

    <!-- ════════════════════════════════════════
     SIDEBAR STYLES
════════════════════════════════════════ -->
    <style>
        /* Override sidebar from admin.css with richer version */
        .sidebar {
            width: var(--sidebar-width);
            flex-shrink: 0;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none;
        }

        .sidebar::-webkit-scrollbar {
            display: none;
        }

        /* Logo */
        .sidebar-logo {
            padding: 22px 18px 18px;
            border-bottom: 1px solid #1e293b;
            flex-shrink: 0;
        }

        .sidebar-logo img {
            max-width: 110px;
            height: auto;
        }

        /* Nav */
        .nav {
            flex: 1;
            padding: 12px 10px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .nav::-webkit-scrollbar {
            display: none;
        }

        /* Section label */
        .nav-section {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: #334155;
            padding: 14px 10px 4px;
            pointer-events: none;
            flex-shrink: 0;
        }

        /* Nav link */
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 500;
            color: #94a3b8;
            text-decoration: none;
            transition: background var(--ease), color var(--ease), transform var(--ease);
            position: relative;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .nav-link svg {
            flex-shrink: 0;
            opacity: .7;
            transition: opacity var(--ease);
        }

        .nav-link:hover {
            background: #1e293b;
            color: #e2e8f0;
            transform: translateX(2px);
        }

        .nav-link:hover svg {
            opacity: 1;
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        .nav-link.active svg {
            opacity: 1;
        }

        .nav-link.active:hover {
            background: var(--primary-dark);
            transform: none;
        }

        /* Pending badge */
        .nav-badge {
            margin-left: auto;
            background: white;
            color: var(--primary);
            font-size: 10px;
            font-weight: 800;
            padding: 1px 7px;
            border-radius: var(--radius-full);
            min-width: 20px;
            text-align: center;
            line-height: 1.6;
        }

        .nav-link:not(.active) .nav-badge {
            background: var(--primary);
            color: white;
        }

        /* Sidebar footer */
        .sidebar-footer {
            flex-shrink: 0;
            padding: 12px 10px;
            border-top: 1px solid #1e293b;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        /* User block */
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-md);
        }

        .sidebar-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #e2e8f0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: #475569;
            margin-top: 1px;
        }

        /* Logout */
        .sidebar-logout {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: var(--ease);
        }

        .sidebar-logout:hover {
            background: #1e293b;
            color: #94a3b8;
            transform: none;
        }

        /* Topbar */
        .topbar {
            display: flex;
            align-items: center;
            height: var(--topbar-height);
            padding: 0 28px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
        }

        .topbar-page {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: capitalize;
        }

        /* Tablet — icon-only sidebar */
        @media (max-width: 900px) {
            :root {
                --sidebar-width: 62px;
            }

            .sidebar-logo img {
                max-width: 36px;
            }

            .sidebar-logo {
                padding: 16px 13px;
            }

            .nav-link span {
                display: none;
            }

            .nav-section {
                display: none;
            }

            .nav-badge {
                position: absolute;
                top: 4px;
                right: 4px;
                min-width: 16px;
                padding: 1px 4px;
                font-size: 9px;
            }

            .sidebar-user-info {
                display: none;
            }

            .sidebar-logout span {
                display: none;
            }

            .sidebar-user {
                padding: 8px 6px;
                justify-content: center;
            }

            .nav-link {
                justify-content: center;
                padding: 9px 6px;
            }

            /* Tooltip on hover */
            .nav-link:hover::after {
                content: attr(data-tooltip);
                position: absolute;
                left: calc(100% + 10px);
                top: 50%;
                transform: translateY(-50%);
                background: #1e293b;
                color: #e2e8f0;
                font-size: 12px;
                padding: 5px 10px;
                border-radius: var(--radius-md);
                white-space: nowrap;
                z-index: 100;
                pointer-events: none;
            }
        }

        /* Mobile — hide sidebar */
        @media (max-width: 480px) {
            :root {
                --sidebar-width: 0px;
            }

            .sidebar {
                display: none;
            }
        }

        /* ── Flash messages ── */
        .flash-msg {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 500;
            animation: flashIn .2s ease;
        }

        @keyframes flashIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash-success {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            color: #166534;
        }

        .flash-error {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            color: #991b1b;
        }

        .flash-warning {
            background: #fffbeb;
            border: 1.5px solid #fcd34d;
            color: #92400e;
        }

        .flash-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            padding: 0;
            color: inherit;
            opacity: .6;
            flex-shrink: 0;
            transition: opacity .12s;
        }

        .flash-close:hover {
            opacity: 1;
        }

        /* ════════════════════════════════════════
   MOBILE — Employee only (≤768px)
════════════════════════════════════════ */
        @media (max-width: 768px) {

            /* Sidebar becomes off-canvas drawer */
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform .25s ease;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.25);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            /* Dark overlay behind sidebar */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }

            /* Main takes full width */
            .main {
                width: 100%;
                min-width: 0;
            }

            /* Topbar — 3-column: hamburger | title | empty */
            .topbar {
                display: grid;
                grid-template-columns: 44px 1fr 44px;
                align-items: center;
                padding: 0 12px;
                height: 56px;
            }

            .topbar-page {
                text-align: center;
                font-size: 14px;
            }

            /* Hamburger button */
            .topbar-hamburger {
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 5px;
                width: 36px;
                height: 36px;
                background: none;
                border: none;
                cursor: pointer;
                padding: 4px;
                border-radius: 8px;
                transition: background .15s;
            }

            .topbar-hamburger:hover {
                background: #f1f5f9;
            }

            .topbar-hamburger span {
                display: block;
                height: 2px;
                background: #374151;
                border-radius: 2px;
                transition: all .2s ease;
            }

            /* Content padding adjustment */
            .content {
                padding: 16px;
            }

            /* Tables — horizontal scroll on mobile */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                border-radius: 8px;
            }

            /* Cards stack nicely */
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            /* Modal full width on mobile */
            .modal-content {
                width: calc(100vw - 32px);
                max-width: 100%;
                max-height: 90vh;
                overflow-y: auto;
            }
        }

        /* Hide hamburger on desktop */
        @media (min-width: 769px) {
            .topbar-hamburger {
                display: none;
            }

            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>

    <!-- ════════════════════════════════════════
     PENDING BADGE SCRIPT
════════════════════════════════════════ -->
    <?php if ($isAdmin): ?>
        <script>
            (function fetchPending() {
                fetch('/leave-system/public/pending-count')
                    .then(r => r.json())
                    .then(data => {
                        const badge = document.getElementById('pendingBadge');
                        if (badge && data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = 'inline-block';
                        }
                    })
                    .catch(() => {}); // silent fail
            })();
        </script>
    <?php endif; ?>

    <script>
        /* ── Global Modal Helper ─────────────────────────────────
   Usage:  openGM({ title, html, size, onOpen })
   size: 'sm' | '' | 'lg'
   Close:  closeGM()
─────────────────────────────────────────────────────── */
        window.openGM = function(opts) {
            const sizeClass = opts.size === 'sm' ? 'gm-box-sm' : opts.size === 'lg' ? 'gm-box-lg' : '';
            document.getElementById('globalModalRoot').innerHTML = `
    <div class="gm-bd" onclick="if(event.target===this)closeGM()">
        <div class="gm-box ${sizeClass}">
            <div class="gm-hd">
                <h3>${opts.title || ''}</h3>
                <button type="button" class="gm-x" onclick="closeGM()">✕</button>
            </div>
            ${opts.html || ''}
        </div>
    </div>`;
            if (typeof opts.onOpen === 'function') opts.onOpen();
        };

        window.closeGM = function() {
            document.getElementById('globalModalRoot').innerHTML = '';
        };

        /* ── Escape HTML helper (available globally) ── */
        window.escH = function(s) {
            return String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        };
    </script>

    <?php if (!$isAdmin): ?>
        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const isOpen = sidebar.classList.contains('open');

                if (isOpen) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                } else {
                    sidebar.classList.add('open');
                    overlay.classList.add('show');
                    document.body.style.overflow = 'hidden'; // prevent background scroll
                }
            }

            // Close sidebar when a nav link is tapped on mobile
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        const sidebar = document.getElementById('sidebar');
                        const overlay = document.getElementById('sidebarOverlay');
                        sidebar.classList.remove('open');
                        overlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });

            // Close on swipe left
            (function() {
                let startX = 0;
                const sidebar = document.getElementById('sidebar');

                sidebar.addEventListener('touchstart', e => {
                    startX = e.touches[0].clientX;
                }, {
                    passive: true
                });

                sidebar.addEventListener('touchend', e => {
                    const diff = startX - e.changedTouches[0].clientX;
                    if (diff > 60) toggleSidebar(); // swipe left to close
                }, {
                    passive: true
                });
            })();
        </script>
    <?php endif; ?>
</body>

</html>
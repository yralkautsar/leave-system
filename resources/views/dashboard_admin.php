<?php ob_start(); ?>

<?php
$adminName = explode(' ', $_SESSION['user']['name'])[0];
$hour      = (int)date('H');
$greeting  = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$today     = date('l, d F Y');
?>

<!-- ══════════════════════════════════════════
     GREETING
══════════════════════════════════════════ -->
<div class="dash-greeting">
    <div>
        <h2 class="dash-greeting-title"><?= $greeting ?>, <?= htmlspecialchars($adminName) ?> 👋</h2>
        <p class="subtext"><?= $today ?></p>
    </div>
    <a href="/leave-system/public/admin/requests" class="btn-outline" style="flex-shrink:0;font-size:13px;">
        View All Requests
    </a>
</div>

<!-- ══════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════ -->
<div class="dash-stats">

    <a href="/leave-system/public/admin/requests?status=pending" class="dstat dstat-orange" style="text-decoration:none;">
        <div class="dstat-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
        </div>
        <div>
            <div class="dstat-value"><?= (int)$stats['pending'] ?></div>
            <div class="dstat-label">Pending Approval</div>
        </div>
    </a>

    <a href="/leave-system/public/admin/requests?status=approved" class="dstat dstat-green" style="text-decoration:none;">
        <div class="dstat-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>
        <div>
            <div class="dstat-value"><?= (int)$stats['approved'] ?></div>
            <div class="dstat-label">Approved</div>
        </div>
    </a>

    <a href="/leave-system/public/admin/requests?status=rejected" class="dstat dstat-red" style="text-decoration:none;">
        <div class="dstat-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </div>
        <div>
            <div class="dstat-value"><?= (int)$stats['rejected'] ?></div>
            <div class="dstat-label">Rejected</div>
        </div>
    </a>

    <div class="dstat dstat-blue">
        <div class="dstat-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
        </div>
        <div>
            <div class="dstat-value"><?= (int)$onLeaveToday ?></div>
            <div class="dstat-label">On Leave Today</div>
        </div>
    </div>

    <a href="/leave-system/public/admin/users" class="dstat dstat-slate" style="text-decoration:none;">
        <div class="dstat-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="5" />
                <path d="M20 21a8 8 0 1 0-16 0" />
            </svg>
        </div>
        <div>
            <div class="dstat-value"><?= (int)$totalEmployees ?></div>
            <div class="dstat-label">Active Employees</div>
        </div>
    </a>

</div>


<!-- ══════════════════════════════════════════
     DEPARTMENT FILTER
══════════════════════════════════════════ -->
<?php
$deptFilter = isset($_GET['dept']) && (int)$_GET['dept'] > 0 ? (int)$_GET['dept'] : null;
$activeDeptName = '';
if ($deptFilter) {
    foreach ($departments as $d) {
        if ($d['id'] == $deptFilter) {
            $activeDeptName = $d['name'];
            break;
        }
    }
}
?>
<div class="dash-dept-bar">
    <div class="dash-dept-label">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;">
            <rect x="2" y="7" width="20" height="14" rx="2" />
            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
        </svg>
        Showing:
    </div>
    <div class="dash-dept-pills">
        <a href="/leave-system/public/dashboard"
            class="dash-dept-pill <?= !$deptFilter ? 'active' : '' ?>">
            All Departments
        </a>
        <?php foreach ($departments as $d): ?>
            <a href="/leave-system/public/dashboard?dept=<?= $d['id'] ?>"
                class="dash-dept-pill <?= $deptFilter == $d['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($d['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>


<!-- ══════════════════════════════════════════
     TWO-COLUMN BODY
══════════════════════════════════════════ -->
<div class="dash-body">

    <!-- ── LEFT: Pending requests ── -->
    <div class="dash-col-main">
        <div class="card" style="margin-bottom:0;">

            <div class="dash-card-head">
                <div>
                    <h3 style="margin:0 0 3px;">Pending Requests</h3>
                    <p class="subtext" style="margin:0;">
                        Oldest first — needs your action
                        <?php if ($deptFilter): ?>
                            <span class="dash-dept-chip"><?= htmlspecialchars($activeDeptName) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ((int)$stats['pending'] > 15): ?>
                    <a href="/leave-system/public/admin/requests?status=pending" class="dash-see-all">
                        +<?= (int)$stats['pending'] - 15 ?> more →
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($pendingRequests)): ?>
                <div class="dash-empty">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    <p>All caught up! No pending requests.</p>
                </div>
            <?php else: ?>
                <div class="dash-req-list">
                    <?php foreach ($pendingRequests as $r): ?>
                        <div class="dash-req-row">

                            <div class="dash-req-avatar">
                                <?= strtoupper(substr($r['employee_name'], 0, 1)) ?>
                            </div>

                            <div class="dash-req-info">
                                <div class="dash-req-name"><?= htmlspecialchars($r['employee_name']) ?></div>
                                <div class="dash-req-meta">
                                    <span class="badge badge-days" style="font-size:11px;"><?= htmlspecialchars($r['leave_type']) ?></span>
                                    <?php if ($r['department']): ?>
                                        <span class="dash-req-dept"><?= htmlspecialchars($r['department']) ?></span>
                                    <?php endif; ?>
                                    <span class="dash-req-date">
                                        <?= date('d M', strtotime($r['start_date'])) ?>
                                        <?= ($r['start_date'] !== $r['end_date']) ? '–' . date('d M', strtotime($r['end_date'])) : '' ?>
                                    </span>
                                    <span class="dash-req-days">
                                        <?= $r['total_days'] ?> day<?= $r['total_days'] > 1 ? 's' : '' ?>
                                        <?php if (($r['duration_type'] ?? '') === 'half_am'): ?>
                                            <span class="dur-tag">AM</span>
                                        <?php elseif (($r['duration_type'] ?? '') === 'half_pm'): ?>
                                            <span class="dur-tag">PM</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="dash-req-age"><?= _timeDiff($r['created_at']) ?></div>

                            <div class="dash-req-actions">
                                <form method="POST" action="/leave-system/public/approve" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button class="btn-outline-success" style="font-size:12px;padding:5px 11px;">Approve</button>
                                </form>
                                <button
                                    class="btn-outline-danger"
                                    style="font-size:12px;padding:5px 11px;"
                                    onclick="openRejectModal(<?= $r['id'] ?>)">
                                    Reject
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>


    <!-- ── RIGHT: On Leave Today ── -->
    <div class="dash-col-side">
        <div class="card" style="margin-bottom:0;">

            <div class="dash-card-head">
                <div>
                    <h3 style="margin:0 0 3px;">On Leave Today</h3>
                    <p class="subtext" style="margin:0;">
                        <?= date('d F Y') ?>
                        <?php if ($deptFilter): ?>
                            <span class="dash-dept-chip"><?= htmlspecialchars($activeDeptName) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <a href="/leave-system/public/calendar" class="dash-see-all">Calendar →</a>
            </div>

            <?php if (empty($onLeaveTodayList)): ?>
                <div class="dash-empty">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    <p>Everyone is in today.</p>
                </div>
            <?php else: ?>
                <div class="dash-ol-list">
                    <?php foreach ($onLeaveTodayList as $ol): ?>
                        <div class="dash-ol-row">

                            <div class="dash-ol-avatar">
                                <?= strtoupper(substr($ol['employee_name'], 0, 1)) ?>
                            </div>

                            <div class="dash-ol-info">
                                <div class="dash-ol-name"><?= htmlspecialchars($ol['employee_name']) ?></div>
                                <div class="dash-ol-meta">
                                    <span class="dash-ol-type"><?= htmlspecialchars($ol['leave_type']) ?></span>
                                    <?php if ($ol['department']): ?>
                                        <span class="dash-ol-dept">· <?= htmlspecialchars($ol['department']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="dash-ol-until">
                                <?php if ((int)$ol['days_left'] === 0): ?>
                                    <span class="dash-ol-last">last day</span>
                                <?php else: ?>
                                    <span class="dash-ol-return">back in <?= (int)$ol['days_left'] + 1 ?> day<?= $ol['days_left'] > 0 ? 's' : '' ?></span>
                                <?php endif; ?>
                                <div class="dash-ol-date">until <?= date('d M', strtotime($ol['end_date'])) ?></div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>


<?php
function _timeDiff(string $dt): string
{
    $d = time() - strtotime($dt);
    if ($d < 60)    return 'just now';
    if ($d < 3600)  return floor($d / 60) . 'm ago';
    if ($d < 86400) return floor($d / 3600) . 'h ago';
    if ($d < 604800) return floor($d / 86400) . 'd ago';
    return date('d M', strtotime($dt));
}
?>

<style>
    /* ── Greeting ─────────────────────────────────── */
    .dash-greeting {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        gap: 16px;
    }

    .dash-greeting-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 3px;
    }

    /* ── Stat grid ────────────────────────────────── */
    /* ── Department filter bar ── */
    .dash-dept-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .dash-dept-label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .dash-dept-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .dash-dept-pill {
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        color: #374151;
        text-decoration: none;
        transition: all .15s ease;
        white-space: nowrap;
    }

    .dash-dept-pill:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
    }

    .dash-dept-pill.active {
        background: #f97316;
        border-color: #f97316;
        color: #fff;
    }

    .dash-dept-chip {
        display: inline-block;
        padding: 1px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        background: #fff7ed;
        color: #f97316;
        border: 1px solid #fed7aa;
        margin-left: 6px;
        vertical-align: middle;
    }

    .dash-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }

    .dstat {
        background: #fff;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform .15s ease, box-shadow .15s ease;
        color: inherit;
    }

    .dstat:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.09);
    }

    .dstat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .dstat-orange .dstat-icon {
        background: #fff7ed;
        color: #f97316;
    }

    .dstat-green .dstat-icon {
        background: #f0fdf4;
        color: #16a34a;
    }

    .dstat-red .dstat-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .dstat-blue .dstat-icon {
        background: #eff6ff;
        color: #2563eb;
    }

    .dstat-slate .dstat-icon {
        background: #f8fafc;
        color: #475569;
    }

    .dstat-value {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .dstat-label {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 3px;
        font-weight: 500;
    }

    /* ── Body grid ────────────────────────────────── */
    .dash-body {
        display: grid;
        grid-template-columns: 1fr 330px;
        gap: 20px;
        align-items: start;
    }

    .dash-col-main,
    .dash-col-side {
        min-width: 0;
    }

    /* ── Card head ────────────────────────────────── */
    .dash-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .dash-see-all {
        font-size: 12px;
        font-weight: 600;
        color: #f97316;
        text-decoration: none;
        white-space: nowrap;
    }

    .dash-see-all:hover {
        color: #ea580c;
    }

    /* ── Empty ────────────────────────────────────── */
    .dash-empty {
        text-align: center;
        padding: 36px 20px;
        color: #94a3b8;
        font-size: 13.5px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    /* ── Pending rows ─────────────────────────────── */
    .dash-req-list {
        display: flex;
        flex-direction: column;
    }

    .dash-req-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .dash-req-row:last-child {
        border-bottom: none;
    }

    .dash-req-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #fff7ed;
        color: #f97316;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #fed7aa;
    }

    .dash-req-info {
        flex: 1;
        min-width: 0;
    }

    .dash-req-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dash-req-meta {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 3px;
        flex-wrap: wrap;
    }

    .dash-req-dept {
        font-size: 11px;
        color: #94a3b8;
    }

    .dash-req-date {
        font-size: 12px;
        color: #64748b;
    }

    .dash-req-days {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* .dur-tag defined globally in admin.css */
    .dash-req-age {
        font-size: 11px;
        color: #94a3b8;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .dash-req-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }

    /* ── On leave today rows ──────────────────────── */
    .dash-ol-list {
        display: flex;
        flex-direction: column;
    }

    .dash-ol-row {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .dash-ol-row:last-child {
        border-bottom: none;
    }

    .dash-ol-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #bfdbfe;
    }

    .dash-ol-info {
        flex: 1;
        min-width: 0;
    }

    .dash-ol-name {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dash-ol-meta {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    .dash-ol-type {
        font-size: 11.5px;
        color: #64748b;
    }

    .dash-ol-dept {
        font-size: 11px;
        color: #94a3b8;
    }

    .dash-ol-until {
        text-align: right;
        flex-shrink: 0;
    }

    .dash-ol-last {
        font-size: 11.5px;
        font-weight: 600;
        color: #dc2626;
        background: #fee2e2;
        padding: 2px 7px;
        border-radius: 999px;
    }

    .dash-ol-return {
        font-size: 11.5px;
        font-weight: 600;
        color: #16a34a;
    }

    .dash-ol-date {
        font-size: 10.5px;
        color: #94a3b8;
        margin-top: 2px;
    }

    /* ── Responsive ───────────────────────────────── */
    @media (max-width:1100px) {
        .dash-stats {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width:900px) {
        .dash-body {
            grid-template-columns: 1fr;
        }

        .dash-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width:480px) {
        .dash-stats {
            grid-template-columns: 1fr 1fr;
        }

        .dash-req-actions {
            flex-direction: column;
        }
    }
</style>

<script>
    function openRejectModal(id) {
        document.getElementById('globalModalRoot').innerHTML = `
        <div class="gm-bd" onclick="if(event.target===this)closeGM()">
            <div class="gm-box gm-box-sm">
                <div class="gm-hd">
                    <h3>Reject Leave Request</h3>
                    <button type="button" class="gm-x" onclick="closeGM()">✕</button>
                </div>
                <form method="POST" action="/leave-system/public/reject">
                    <input type="hidden" name="id" value="${id}">
                    <div class="gm-body">
                        <p style="margin:0 0 14px;font-size:13.5px;color:#374151;">
                            Optionally provide a reason — this will be included in the notification email.
                        </p>
                        <div class="gm-fg">
                            <label for="rejectReasonDash">Reason <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                            <textarea
                                id="rejectReasonDash"
                                name="rejection_reason"
                                rows="3"
                                placeholder="e.g. Insufficient team coverage..."
                                style="padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13.5px;resize:vertical;outline:none;width:100%;box-sizing:border-box;font-family:inherit;transition:.15s;"
                                onfocus="this.style.borderColor='#f97316';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.12)'"
                                onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                            ></textarea>
                        </div>
                    </div>
                    <div class="gm-ft">
                        <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
                        <button type="submit" class="gm-btn-danger">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>`;
        setTimeout(() => document.getElementById('rejectReasonDash')?.focus(), 50);
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
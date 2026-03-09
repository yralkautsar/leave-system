<?php ob_start(); ?>

<style>
    /* ══════════════════════════════════════
   EMPLOYEE DASHBOARD
══════════════════════════════════════ */

    /* Hero */
    .ed-hero {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border-radius: 16px;
        padding: 28px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
        box-shadow: 0 8px 24px rgba(249, 115, 22, 0.25);
    }

    .ed-hero-text h2 {
        margin: 0 0 4px;
        font-size: 22px;
        font-weight: 700;
        color: white;
    }

    .ed-hero-text p {
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
        font-size: 13.5px;
    }

    .ed-hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 22px;
        background: white;
        color: #ea580c;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        flex-shrink: 0;
        transition: all .15s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .ed-hero-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
    }

    /* Balances */
    .ed-section-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #94a3b8;
        margin: 0 0 12px;
    }

    .ed-balances {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .ed-bal-card {
        background: white;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .ed-bal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.09);
    }

    .ed-bal-type {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 10px;
    }

    .ed-bal-remaining {
        font-size: 36px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 4px;
    }

    .ed-bal-remaining span {
        font-size: 14px;
        font-weight: 500;
        color: #94a3b8;
        margin-left: 3px;
    }

    .ed-bal-bar-wrap {
        height: 5px;
        background: #f1f5f9;
        border-radius: 99px;
        margin: 10px 0 8px;
        overflow: hidden;
    }

    .ed-bal-bar-fill {
        height: 100%;
        background: #f97316;
        border-radius: 99px;
        transition: width .4s ease;
    }

    .ed-bal-bar-fill.low {
        background: #ef4444;
    }

    .ed-bal-meta {
        font-size: 11.5px;
        color: #94a3b8;
    }

    .ed-bal-empty {
        background: #f8fafc;
        border: 1.5px dashed #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 13px;
    }

    .ed-bal-comp {
        border: 1.5px solid #ddd6fe;
        background: linear-gradient(135deg, #faf5ff 0%, #ede9fe 100%);
    }

    .ed-bal-comp:hover {
        border-color: #7c3aed;
        box-shadow: 0 8px 20px rgba(124, 58, 237, 0.15);
    }

    .ed-bal-grant {
        border: 1.5px solid #fde68a;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .ed-bal-grant:hover {
        border-color: #f59e0b;
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.15);
    }

    /* Stats */
    .ed-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }

    .ed-stat {
        background: white;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ed-stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .ed-stat-icon.pending {
        background: #fff7ed;
        color: #f97316;
    }

    .ed-stat-icon.approved {
        background: #f0fdf4;
        color: #16a34a;
    }

    .ed-stat-icon.rejected {
        background: #fef2f2;
        color: #dc2626;
    }

    .ed-stat-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 2px;
    }

    .ed-stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    /* Recent */
    .ed-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .ed-card-hd {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ed-card-hd h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .ed-card-hd a {
        font-size: 12.5px;
        color: #f97316;
        text-decoration: none;
        font-weight: 500;
    }

    .ed-card-hd a:hover {
        text-decoration: underline;
    }

    .ed-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ed-table th {
        padding: 10px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #94a3b8;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .ed-table td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .ed-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ed-table tbody tr {
        transition: background .12s ease;
    }

    .ed-table tbody tr:hover {
        background: #fff7ed;
    }

    /* Status badges */
    .bd {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .bd-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .bd-approved {
        background: #f0fdf4;
        color: #15803d;
    }

    .bd-rejected {
        background: #fef2f2;
        color: #b91c1c;
    }

    .bd-cancelled {
        background: #f1f5f9;
        color: #64748b;
    }

    .ed-empty {
        padding: 32px 20px;
        text-align: center;
        color: #94a3b8;
        font-size: 13.5px;
    }

    /* Notification banners */
    .ed-notif-wrap {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }

    .ed-notif {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1.5px solid;
        position: relative;
        animation: notifIn .25s ease;
    }

    @keyframes notifIn {
        from {
            opacity: 0;
            transform: translateY(-6px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .ed-notif.approved {
        background: #f0fdf4;
        border-color: #86efac;
    }

    .ed-notif.rejected {
        background: #fef2f2;
        border-color: #fca5a5;
    }

    .ed-notif-icon {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .ed-notif-body {
        flex: 1;
    }

    .ed-notif-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 2px;
    }

    .ed-notif-sub {
        font-size: 12.5px;
        color: #64748b;
        margin: 0;
    }

    .ed-notif-close {
        position: absolute;
        top: 10px;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #94a3b8;
        line-height: 1;
        padding: 0;
        transition: color .12s;
    }

    .ed-notif-close:hover {
        color: #374151;
    }

    /* Period badge on balance card */
    .ed-bal-period {
        font-size: 11px;
        color: #94a3b8;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ed-bal-period span {
        background: #f1f5f9;
        color: #64748b;
        padding: 2px 7px;
        border-radius: 99px;
        font-size: 10.5px;
        font-weight: 600;
    }

    /* Cancel button in recent table */
    /* ed-cancel-btn removed — uses global .btn-outline-danger */

    @media (max-width: 768px) {
        .ed-balances {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .ed-summary-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .ed-hero {
            padding: 18px 16px;
            border-radius: 12px;
        }

        .ed-hero-title {
            font-size: 20px;
        }

        .ed-recent-table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>

<?php
$balances      = $balances      ?? [];
$stats         = $stats         ?? ['pending' => 0, 'approved' => 0, 'rejected' => 0];
$history       = $history       ?? [];
$notifications = $notifications ?? [];
$userName = !empty($_SESSION['user']['nickname'])
    ? $_SESSION['user']['nickname']
    : explode(' ', $_SESSION['user']['name'])[0];

// Filter out dismissed notifications (via sessionStorage — handled in JS)
?>

<!-- HERO -->
<div class="ed-hero">
    <div class="ed-hero-text">
        <h2>Hello, <?= htmlspecialchars($userName) ?> </h2>
        <p>Here's your leave overview for today.</p>
    </div>
    <a href="/leave" class="ed-hero-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Submit Leave
    </a>
</div>

<!-- NOTIFICATION BANNERS -->
<?php if (!empty($notifications)): ?>
    <div class="ed-notif-wrap" id="notifWrap">
        <?php foreach ($notifications as $n):
            $isApproved = $n['status'] === 'approved';
            $icon       = $isApproved ? '✅' : '❌';
            $date       = date('d M Y', strtotime($n['start_date']));
            $endDate    = date('d M Y', strtotime($n['end_date']));
            $label      = $isApproved ? 'approved' : 'rejected';
            $notifId    = 'notif-' . $n['id'];
        ?>
            <div class="ed-notif <?= $label ?>" id="<?= $notifId ?>" data-id="<?= $n['id'] ?>">
                <div class="ed-notif-icon"><?= $icon ?></div>
                <div class="ed-notif-body">
                    <p class="ed-notif-title">
                        Leave request <?= $isApproved ? 'approved' : 'rejected' ?> —
                        <?= htmlspecialchars($n['leave_type']) ?>
                    </p>
                    <p class="ed-notif-sub">
                        <?= $date ?> – <?= $endDate ?> &middot; <?= (float)$n['total_days'] ?> day(s)
                        <?php if (!$isApproved && !empty($n['rejection_reason'])): ?>
                            &middot; Reason: "<?= htmlspecialchars($n['rejection_reason']) ?>"
                        <?php endif; ?>
                    </p>
                </div>
                <button class="ed-notif-close" onclick="dismissNotif('<?= $notifId ?>')" title="Dismiss">×</button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- LEAVE BALANCES -->
<p class="ed-section-title">Leave Balance — Current Period</p>
<div class="ed-balances">
    <?php if (!empty($balances)): ?>
        <?php foreach ($balances as $b):
            $pct   = $b['total_days'] > 0 ? round(($b['remaining_days'] / $b['total_days']) * 100) : 0;
            $low   = $pct <= 25 ? 'low' : '';
            $href  = '/my-data?tab=history&type=' . urlencode($b['leave_type']);
        ?>
            <a href="<?= $href ?>" class="ed-bal-card" style="text-decoration:none;color:inherit;">
                <div class="ed-bal-period">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    <span><?= htmlspecialchars($b['period_name']) ?></span>
                    · expires <?= date('d M Y', strtotime($b['end_date'])) ?>
                </div>
                <div class="ed-bal-type"><?= htmlspecialchars($b['leave_type']) ?></div>
                <div class="ed-bal-remaining">
                    <?= (float)$b['remaining_days'] ?><span>days left</span>
                </div>
                <div class="ed-bal-bar-wrap">
                    <div class="ed-bal-bar-fill <?= $low ?>" style="width:<?= $pct ?>%;"></div>
                </div>
                <div class="ed-bal-meta"><?= (float)$b['used_days'] ?> used &middot; <?= (float)$b['total_days'] ?> total</div>
                <div style="font-size:11px;color:#f97316;margin-top:8px;font-weight:600;">View history →</div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="ed-bal-empty">No leave balance allocated for the current period.</div>
    <?php endif; ?>

    <?php if ($compBalance > 0): ?>
        <!-- Comp Leave balance card -->
        <a href="/comp-claim" class="ed-bal-card ed-bal-comp" style="text-decoration:none;color:inherit;">
            <div class="ed-bal-period" style="color:#7c3aed;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
                    <polyline points="17 6 23 6 23 12" />
                </svg>
                <span>Floating · 6-month expiry</span>
            </div>
            <div class="ed-bal-type">Compensate Leave</div>
            <div class="ed-bal-remaining" style="color:#7c3aed;">
                <?= number_format($compBalance, 1) ?><span>days available</span>
            </div>
            <div style="font-size:11px;color:#7c3aed;margin-top:8px;font-weight:600;">View claims →</div>
        </a>
    <?php endif; ?>

    <?php foreach ($grantBalances ?? [] as $g): ?>
        <?php if ((float)$g['remaining_days'] > 0): ?>
            <div class="ed-bal-card ed-bal-grant">
                <div class="ed-bal-period" style="color:#92400e;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <span>Event-based grant</span>
                </div>
                <div class="ed-bal-type"><?= htmlspecialchars($g['leave_type']) ?></div>
                <div class="ed-bal-remaining" style="color:#92400e;">
                    <?= (float)$g['remaining_days'] ?><span>days left</span>
                </div>
                <div class="ed-bal-meta"><?= (float)$g['used_days'] ?> used &middot; <?= (float)$g['total_days'] ?> total</div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>

<!-- STATS -->
<p class="ed-section-title">Request Summary</p>
<div class="ed-stats">
    <div class="ed-stat">
        <div class="ed-stat-icon pending">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
            </svg>
        </div>
        <div>
            <div class="ed-stat-label">Pending</div>
            <div class="ed-stat-value"><?= $stats['pending'] ?></div>
        </div>
    </div>
    <div class="ed-stat">
        <div class="ed-stat-icon approved">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12" />
            </svg>
        </div>
        <div>
            <div class="ed-stat-label">Approved</div>
            <div class="ed-stat-value"><?= $stats['approved'] ?></div>
        </div>
    </div>
    <div class="ed-stat">
        <div class="ed-stat-icon rejected">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
        </div>
        <div>
            <div class="ed-stat-label">Rejected</div>
            <div class="ed-stat-value"><?= $stats['rejected'] ?></div>
        </div>
    </div>
</div>

<!-- RECENT ACTIVITY -->
<div class="ed-card">
    <div class="ed-card-hd">
        <h3>Recent Requests</h3>
        <a href="/my-data">View all →</a>
    </div>
    <?php if (!empty($history)): ?>
        <table class="ed-table">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['leave_type']) ?></td>
                        <td><?= $h['start_date'] ?></td>
                        <td><?= $h['end_date'] ?></td>
                        <td>
                            <?= (float)$h['total_days'] ?>
                            <?php if (($h['duration_type'] ?? '') === 'half_am'): ?>
                                <span class="dur-tag">AM</span>
                            <?php elseif (($h['duration_type'] ?? '') === 'half_pm'): ?>
                                <span class="dur-tag">PM</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="bd bd-<?= $h['status'] ?>"><?= ucfirst($h['status']) ?></span></td>
                        <td>
                            <?php if ($h['status'] === 'pending'): ?>
                                <form method="POST" action="/cancel"
                                    onsubmit="return confirm('Cancel this leave request?')">
                                    <input type="hidden" name="id" value="<?= $h['id'] ?>">
                                    <button type="submit" class="btn-outline-danger" style="padding:4px 10px;font-size:11.5px;">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="ed-empty">No leave requests yet. <a href="/leave" style="color:#f97316;">Submit your first one →</a></div>
    <?php endif; ?>
</div>

<script>
    // Dismiss notifications — stored in sessionStorage per notif ID
    function dismissNotif(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.transition = 'opacity .2s ease, transform .2s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-4px)';
        setTimeout(() => {
            el.remove();
            // Hide wrapper if empty
            const wrap = document.getElementById('notifWrap');
            if (wrap && !wrap.querySelector('.ed-notif')) wrap.remove();
        }, 200);
        sessionStorage.setItem('dismissed_' + id, '1');
    }

    // On load — hide already-dismissed notifications
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.ed-notif[data-id]').forEach(el => {
            const key = 'dismissed_notif-' + el.dataset.id;
            if (sessionStorage.getItem(key)) el.remove();
        });
        const wrap = document.getElementById('notifWrap');
        if (wrap && !wrap.querySelector('.ed-notif')) wrap.remove();
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
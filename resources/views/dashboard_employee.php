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
</style>

<?php
$balances = $balances ?? [];
$stats    = $stats    ?? ['pending' => 0, 'approved' => 0, 'rejected' => 0];
$history  = $history  ?? [];
$userName = explode(' ', $_SESSION['user']['name'])[0]; // first name
?>

<!-- HERO -->
<div class="ed-hero">
    <div class="ed-hero-text">
        <h2>Hello, <?= htmlspecialchars($userName) ?> 👋</h2>
        <p>Here's your leave overview for today.</p>
    </div>
    <a href="/leave-system/public/leave" class="ed-hero-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Submit Leave
    </a>
</div>

<!-- LEAVE BALANCES -->
<p class="ed-section-title">Leave Balance — Current Period</p>
<div class="ed-balances">
    <?php if (!empty($balances)): ?>
        <?php foreach ($balances as $b):
            $pct   = $b['total_days'] > 0 ? round(($b['remaining_days'] / $b['total_days']) * 100) : 0;
            $low   = $pct <= 25 ? 'low' : '';
        ?>
            <div class="ed-bal-card">
                <div class="ed-bal-type"><?= htmlspecialchars($b['leave_type']) ?></div>
                <div class="ed-bal-remaining">
                    <?= (float)$b['remaining_days'] ?><span>days left</span>
                </div>
                <div class="ed-bal-bar-wrap">
                    <div class="ed-bal-bar-fill <?= $low ?>" style="width:<?= $pct ?>%;"></div>
                </div>
                <div class="ed-bal-meta"><?= (float)$b['used_days'] ?> used &middot; <?= (float)$b['total_days'] ?> total</div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="ed-bal-empty">No leave balance allocated for the current period.</div>
    <?php endif; ?>
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
        <a href="/leave-system/public/my-data">View all →</a>
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $h): ?>
                    <tr>
                        <td><?= htmlspecialchars($h['leave_type']) ?></td>
                        <td><?= $h['start_date'] ?></td>
                        <td><?= $h['end_date'] ?></td>
                        <td><?= (float)$h['total_days'] ?></td>
                        <td><span class="bd bd-<?= $h['status'] ?>"><?= ucfirst($h['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="ed-empty">No leave requests yet. <a href="/leave-system/public/leave" style="color:#f97316;">Submit your first one →</a></div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
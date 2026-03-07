<?php ob_start(); ?>

<div class="card">
    <h2>Dashboard</h2>
</div>

<div class="summary-grid">

    <div class="summary-box">
        <h3>Remaining Annual Leave</h3>
        <p><?= $remaining ?></p>
    </div>

    <div class="summary-box">
        <h3>Pending</h3>
        <p><?= $stats['pending'] ?></p>
    </div>

    <div class="summary-box">
        <h3>Approved</h3>
        <p><?= $stats['approved'] ?></p>
    </div>

    <div class="summary-box">
        <h3>Rejected</h3>
        <p><?= $stats['rejected'] ?></p>
    </div>

</div>

<a href="/leave-system/public/leave" class="btn btn-primary">
    + Submit Leave
</a>

<br><br>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0;">Recent Leave History</h3>
        <span class="caption-text">Showing latest 5 records</span>
    </div>

    <br>

    <?php if (empty($history)): ?>
        <p style="color:#64748b;">No leave history yet.</p>
    <?php else: ?>

        <table class="history-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['leave_type']) ?></td>
                    <td><?= $h['start_date'] ?></td>
                    <td><?= $h['end_date'] ?></td>
                    <td><?= $h['total_days'] ?></td>
                    <td>
                        <?php if ($h['status'] === 'pending'): ?>
                            <span class="badge badge-pending">Pending</span>
                        <?php elseif ($h['status'] === 'approved'): ?>
                            <span class="badge badge-approved">Approved</span>
                        <?php elseif ($h['status'] === 'rejected'): ?>
                            <span class="badge badge-rejected">Rejected</span>
                        <?php else: ?>
                            <span class="badge badge-cancelled">Cancelled</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <br>

        <div style="text-align:right;">
            <a href="/leave-system/public/my-history" class="btn btn-outline">
                View All History →
            </a>
        </div>

    <?php endif; ?>

</div>

<style>

/* Caption */
.caption-text {
    font-size: 12px;
    color: #94a3b8;
}

/* Table animation */
.history-table tbody tr {
    transition: all 0.2s ease;
}

.history-table tbody tr:hover {
    background: #fff7ed;
    transform: scale(1.01);
}

/* Outline button */
.btn-outline {
    padding: 9px 16px;
    border-radius: 8px;
    font-size: 14px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #475569;
    text-decoration: none;
    transition: all 0.25s ease;
}

.btn-outline:hover {
    border: 1px solid #f97316;
    color: #f97316;
}

.badge {
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-pending { background:#fff7ed; color:#f97316; }
.badge-approved { background:#ecfdf5; color:#16a34a; }
.badge-rejected { background:#fef2f2; color:#dc2626; }
.badge-cancelled { background:#e2e8f0; color:#475569; }

</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
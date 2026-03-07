<?php ob_start(); ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2 style="margin:0;">All Leave Requests</h2>
        <span class="caption-text">
            Total Requests: <?= count($requests) ?>
        </span>
    </div>
</div>

<div class="card">

    <?php if (empty($requests)): ?>
        <p style="color:#64748b;">No leave requests found.</p>
    <?php else: ?>

        <table class="request-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['leave_type']) ?></td>
                    <td><?= $r['start_date'] ?></td>
                    <td><?= $r['end_date'] ?></td>
                    <td><?= $r['total_days'] ?></td>
                    <td>
                        <?php if ($r['status'] === 'pending'): ?>
                            <span class="badge badge-pending">Pending</span>
                        <?php elseif ($r['status'] === 'approved'): ?>
                            <span class="badge badge-approved">Approved</span>
                        <?php elseif ($r['status'] === 'rejected'): ?>
                            <span class="badge badge-rejected">Rejected</span>
                        <?php else: ?>
                            <span class="badge badge-cancelled">Cancelled</span>
                        <?php endif; ?>
                    </td>

                    <td style="text-align:right;">
                        <?php if ($r['status'] === 'pending'): ?>

                            <form method="POST" action="/leave-system/public/approve" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-outline-success">
                                    Approve
                                </button>
                            </form>

                            <form method="POST" action="/leave-system/public/reject" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn btn-outline-danger">
                                    Reject
                                </button>
                            </form>

                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<style>

/* Caption */
.caption-text {
    font-size: 12px;
    color: #94a3b8;
}

/* Table hover animation */
.request-table tbody tr {
    transition: all 0.2s ease;
}

.request-table tbody tr:hover {
    background: #fff7ed;
    transform: scale(1.01);
}

/* Outline buttons */
.btn-outline-success {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #16a34a;
    font-size: 13px;
    transition: all 0.25s ease;
    cursor: pointer;
}

.btn-outline-success:hover {
    border: 1px solid #16a34a;
    background: #ecfdf5;
}

.btn-outline-danger {
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #dc2626;
    font-size: 13px;
    transition: all 0.25s ease;
    cursor: pointer;
}

.btn-outline-danger:hover {
    border: 1px solid #dc2626;
    background: #fef2f2;
}

/* Badges */
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
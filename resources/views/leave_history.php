<?php ob_start(); ?>

<div class="card">
    <h2>My Leave History</h2>
</div>

<div class="card">

    <?php if (empty($history)): ?>
        <p style="color:#64748b;">No leave history found.</p>
    <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
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
                        <span class="badge badge-<?= $h['status'] ?>">
                            <?= ucfirst($h['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($h['status'] === 'pending'): ?>
                            <form method="POST" action="/cancel">
                                <input type="hidden" name="id" value="<?= $h['id'] ?>">
                                <button class="btn btn-danger">Cancel</button>
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

.badge {
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
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
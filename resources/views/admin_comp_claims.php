<?php ob_start(); ?>

<?php
// ── Stats ─────────────────────────────────────────────────
$db = Database::connect();

$statsStmt = $db->query("
    SELECT status, COUNT(*) AS total
    FROM comp_claims
    GROUP BY status
");
$statsRaw = $statsStmt->fetchAll(PDO::FETCH_ASSOC);
$ccStats = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
foreach ($statsRaw as $r) $ccStats[$r['status']] = (int)$r['total'];
?>

<!-- STAT PILLS -->
<div class="cc-stat-row">
    <a href="?status=pending"
        class="cc-stat-pill cc-stat-pending <?= ($statusFilter ?? 'pending') === 'pending' ? 'cc-stat-active' : '' ?>">
        Pending <span><?= $ccStats['pending'] ?></span>
    </a>
    <a href="?status=approved"
        class="cc-stat-pill cc-stat-approved <?= ($statusFilter ?? '') === 'approved' ? 'cc-stat-active' : '' ?>">
        Approved <span><?= $ccStats['approved'] ?></span>
    </a>
    <a href="?status=rejected"
        class="cc-stat-pill cc-stat-rejected <?= ($statusFilter ?? '') === 'rejected' ? 'cc-stat-active' : '' ?>">
        Rejected <span><?= $ccStats['rejected'] ?></span>
    </a>
</div>


<div class="card">

    <div class="table-header">
        <div>
            <h2>Compensate Leave Claims</h2>
            <p class="subtext">Review and approve employee comp leave claims</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-success"><?= $_SESSION['success'];
                                    unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-error"><?= $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
    <?php endif; ?>


    <!-- FILTER -->
    <div class="form-wrapper">
        <form method="GET" action="/leave-system/public/admin/comp-claims">
            <div class="form-grid">

                <div class="form-group">
                    <label>Search Employee</label>
                    <input type="text" name="search"
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        placeholder="Employee name...">
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="pending" <?= ($statusFilter ?? 'pending') === 'pending'  ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="" <?= ($statusFilter ?? 'pending') === ''  ? 'selected' : '' ?>>All</option>
                    </select>
                </div>

                <div class="form-group" style="align-self:end;">
                    <button class="btn-primary">Search</button>
                </div>

            </div>
        </form>
    </div>


    <!-- TABLE -->
    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date Worked</th>
                    <th>Reason</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Expires</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($claims)): ?>
                    <tr>
                        <td colspan="7" class="empty-row">No claims found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($claims as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['employee_name']) ?></td>

                            <td>
                                <?= htmlspecialchars($c['worked_date']) ?>
                                <?php
                                $d   = new DateTime($c['worked_date']);
                                $dow = (int)$d->format('N'); // 6=Sat, 7=Sun
                                if ($dow >= 6): ?>
                                    <span class="cc-weekend-tag">Weekend</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($c['reason']) ?></td>

                            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>

                            <td>
                                <?php if ($c['status'] === 'pending'): ?>
                                    <span class="badge cc-badge-pending">Pending</span>
                                <?php elseif ($c['status'] === 'approved'): ?>
                                    <span class="badge cc-badge-approved">Approved</span>
                                    <?php if ($c['approved_by_name']): ?>
                                        <div class="subtext" style="font-size:11px;">
                                            by <?= htmlspecialchars($c['approved_by_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge cc-badge-rejected">Rejected</span>
                                    <?php if ($c['rejection_reason']): ?>
                                        <div class="subtext" style="font-size:11px;">
                                            <?= htmlspecialchars($c['rejection_reason']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($c['expires_at']): ?>
                                    <?= date('d M Y', strtotime($c['expires_at'])) ?>
                                <?php else: ?>
                                    <span class="subtext">—</span>
                                <?php endif; ?>
                            </td>

                            <td class="action-cell">
                                <?php if ($c['status'] === 'pending'): ?>

                                    <form method="POST"
                                        action="/leave-system/public/admin/comp-claims/approve"
                                        style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <button class="btn-outline-success">Approve</button>
                                    </form>

                                    <button
                                        type="button"
                                        class="btn-outline-danger"
                                        onclick="openRejectModal(<?= $c['id'] ?>, '<?= htmlspecialchars($c['employee_name'], ENT_QUOTES) ?>')">
                                        Reject
                                    </button>

                                <?php else: ?>
                                    <span class="subtext">—</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>


<script>
    function openRejectModal(claimId, empName) {
        openGM(`
        <h3 style="margin:0 0 4px;">Reject Claim</h3>
        <p style="margin:0 0 20px; color:#64748b; font-size:13px;">${empName}</p>
        <form method="POST" action="/leave-system/public/admin/comp-claims/reject">
            <input type="hidden" name="id" value="${claimId}">
            <div class="form-group">
                <label>Rejection Reason (optional)</label>
                <input type="text" name="rejection_reason" placeholder="e.g. Date not a holiday or weekend">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-outline" onclick="closeGM()">Cancel</button>
                <button type="submit" class="btn-outline-danger">Confirm Reject</button>
            </div>
        </form>
    `);
    }
</script>


<style>
    /* ── Stat pills ──────────────────────────────────────────── */
    .cc-stat-row {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .cc-stat-pill {
        padding: 8px 18px;
        border-radius: 99px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid transparent;
        transition: all .2s ease;
    }

    .cc-stat-pill span {
        background: rgba(0, 0, 0, 0.1);
        padding: 1px 8px;
        border-radius: 99px;
        font-size: 12px;
    }

    .cc-stat-pending {
        background: #fef3c7;
        color: #92400e;
        border-color: #fde68a;
    }

    .cc-stat-approved {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .cc-stat-rejected {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    .cc-stat-active {
        box-shadow: 0 0 0 2px currentColor;
        font-weight: 700;
    }

    /* ── Badges (shared with comp_claim.php) ─────────────────── */
    .cc-badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .cc-badge-approved {
        background: #d1fae5;
        color: #065f46;
    }

    .cc-badge-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .cc-badge-days {
        background: #ede9fe;
        color: #5b21b6;
    }

    /* ── Weekend tag ─────────────────────────────────────────── */
    .cc-weekend-tag {
        font-size: 11px;
        background: #ede9fe;
        color: #6d28d9;
        padding: 2px 6px;
        border-radius: 99px;
        margin-left: 4px;
    }
</style>


<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
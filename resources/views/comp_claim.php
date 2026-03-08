<?php ob_start(); ?>

<?php
// ── Comp balance (approved, not expired) ──────────────────
$db     = Database::connect();
$userId = (int)$_SESSION['user']['id'];

$balStmt = $db->prepare("
    SELECT COALESCE(SUM(days_remaining), 0)
    FROM comp_claims
    WHERE employee_id = :id
      AND status      = 'approved'
      AND expires_at  > CURDATE()
");
$balStmt->execute(['id' => $userId]);
$compBalance = (float)$balStmt->fetchColumn();

// ── Claims history ────────────────────────────────────────
$histStmt = $db->prepare("
    SELECT cc.*,
           u.name AS approved_by_name
    FROM comp_claims cc
    LEFT JOIN users u ON u.id = cc.approved_by
    WHERE cc.employee_id = :id
    ORDER BY cc.created_at DESC
");
$histStmt->execute(['id' => $userId]);
$claims = $histStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- BALANCE BANNER -->
<div class="cc-banner">
    <div class="cc-banner-inner">
        <div class="cc-banner-label">Available Compensate Leave</div>
        <div class="cc-banner-val"><?= number_format($compBalance, 1) ?> <span>days</span></div>
    </div>
    <div class="cc-banner-note">
        Balance from approved claims that have not expired (valid 6 months from approval).
    </div>
</div>


<!-- SUBMIT FORM -->
<div class="card">

    <h2>Claim Compensate Leave</h2>
    <p class="subtext">Submit a claim for a day you worked during a public holiday or weekend.</p>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-success"><?= $_SESSION['success'];
                                    unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-error"><?= $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-wrapper">
        <form method="POST" action="/leave-system/public/comp-claim/store">

            <div class="form-grid">

                <div class="form-group">
                    <label>Date Worked <span class="cc-req">*</span></label>
                    <input type="date"
                        name="worked_date"
                        id="workedDate"
                        max="<?= date('Y-m-d') ?>"
                        required>
                    <span class="cc-hint" id="dateHint" style="display:none;"></span>
                </div>

                <div class="form-group">
                    <label>Reason <span class="cc-req">*</span></label>
                    <input type="text"
                        name="reason"
                        placeholder="e.g. Worked on Independence Day"
                        required
                        maxlength="255">
                </div>

            </div>

            <button type="submit" class="btn-primary">Submit Claim</button>

        </form>
    </div>

</div>


<!-- HISTORY TABLE -->
<div class="card">

    <div class="table-header">
        <div>
            <h2>Claim History</h2>
            <p class="subtext">All your compensate leave claims</p>
        </div>
    </div>

    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th>Date Worked</th>
                    <th>Reason</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Expires</th>
                    <th>Remaining</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($claims)): ?>
                    <tr>
                        <td colspan="6" class="empty-row">No claims submitted yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($claims as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['worked_date']) ?></td>
                            <td><?= htmlspecialchars($c['reason']) ?></td>
                            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>

                            <td>
                                <?php if ($c['status'] === 'pending'): ?>
                                    <span class="badge cc-badge-pending">Pending</span>
                                <?php elseif ($c['status'] === 'approved'): ?>
                                    <span class="badge cc-badge-approved">Approved</span>
                                <?php else: ?>
                                    <span class="badge cc-badge-rejected"
                                        title="<?= htmlspecialchars($c['rejection_reason'] ?? '') ?>">
                                        Rejected
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($c['status'] === 'approved' && $c['expires_at']): ?>
                                    <?php
                                    $daysLeft = (int)ceil((strtotime($c['expires_at']) - time()) / 86400);
                                    $expClass = $daysLeft <= 30 ? 'cc-exp-warn' : '';
                                    ?>
                                    <span class="<?= $expClass ?>">
                                        <?= date('d M Y', strtotime($c['expires_at'])) ?>
                                        <?php if ($daysLeft <= 30 && $daysLeft > 0): ?>
                                            <span class="cc-exp-tag"><?= $daysLeft ?>d left</span>
                                        <?php elseif ($daysLeft <= 0): ?>
                                            <span class="cc-exp-tag cc-expired">Expired</span>
                                        <?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="subtext">—</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($c['status'] === 'approved'): ?>
                                    <?php
                                    $expired = $c['expires_at'] && $c['expires_at'] < date('Y-m-d');
                                    ?>
                                    <?php if ($expired): ?>
                                        <span class="subtext">0 days</span>
                                    <?php else: ?>
                                        <span class="badge cc-badge-days">
                                            <?= number_format((float)$c['days_remaining'], 1) ?> days
                                        </span>
                                    <?php endif; ?>
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


<style>
    /* ── Banner ─────────────────────────────────────────────── */
    .cc-banner {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: white;
        border-radius: 14px;
        padding: 24px 30px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        box-shadow: 0 8px 24px rgba(124, 58, 237, 0.25);
    }

    .cc-banner-label {
        font-size: 13px;
        opacity: 0.85;
        margin-bottom: 4px;
    }

    .cc-banner-val {
        font-size: 36px;
        font-weight: 700;
        line-height: 1;
    }

    .cc-banner-val span {
        font-size: 16px;
        font-weight: 400;
        opacity: 0.8;
    }

    .cc-banner-note {
        font-size: 12px;
        opacity: 0.75;
        max-width: 320px;
        line-height: 1.5;
    }

    /* ── Form helpers ────────────────────────────────────────── */
    .cc-req {
        color: #ef4444;
        margin-left: 2px;
    }

    .cc-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        display: block;
    }

    /* ── Badges ──────────────────────────────────────────────── */
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
        cursor: help;
    }

    .cc-badge-days {
        background: #ede9fe;
        color: #5b21b6;
    }

    /* ── Expiry warning ──────────────────────────────────────── */
    .cc-exp-warn {
        color: #b45309;
    }

    .cc-exp-tag {
        font-size: 11px;
        background: #fef3c7;
        color: #92400e;
        padding: 2px 6px;
        border-radius: 99px;
        margin-left: 4px;
    }

    .cc-exp-tag.cc-expired {
        background: #fee2e2;
        color: #991b1b;
    }
</style>


<script>
    // Show hint when a weekend date is chosen
    document.getElementById('workedDate').addEventListener('change', function() {
        const hint = document.getElementById('dateHint');
        const d = new Date(this.value + 'T00:00:00');
        const day = d.getDay(); // 0=Sun, 6=Sat
        if (day === 0 || day === 6) {
            hint.textContent = '✓ Weekend — eligible for compensate leave.';
            hint.style.display = 'block';
            hint.style.color = '#16a34a';
        } else {
            hint.textContent = 'Note: If this was a public holiday, your claim is still valid.';
            hint.style.display = 'block';
            hint.style.color = '#64748b';
        }
    });
</script>


<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
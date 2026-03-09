<?php ob_start(); ?>

<div class="card">

    <div class="header-row">
        <div>
            <h2>Leave Grants</h2>
            <p class="subtext">Manually grant event-based leave (Marriage, Maternity, Paternity, Bereavement)</p>
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

    <!-- GRANT FORM -->
    <div class="form-wrapper">
        <form method="POST" action="/admin/leave-grants/store">

            <div class="form-grid">

                <div class="form-group">
                    <label>Employee <span style="color:#ef4444;">*</span></label>
                    <select name="employee_id" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Leave Type <span style="color:#ef4444;">*</span></label>
                    <select name="leave_type_id" required>
                        <option value="">Select Type</option>
                        <?php foreach ($grantTypes as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Days to Grant <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="days" min="0.5" step="0.5" placeholder="e.g. 3" required>
                </div>

                <div class="form-group">
                    <label>Reason / Notes</label>
                    <input type="text" name="reason"
                        placeholder="e.g. Marriage leave per company policy">
                </div>

            </div>

            <button type="submit" class="btn-primary">Grant Leave</button>

        </form>
    </div>


    <!-- GRANTS TABLE -->
    <div class="table-section">

        <div class="table-header">
            <h3>Recent Grants</h3>
            <span class="count-text"><?= count($grants) ?> records</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Total</th>
                    <th>Used</th>
                    <th>Remaining</th>
                    <th>Reason</th>
                    <th>Granted By</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($grants)): ?>
                    <tr>
                        <td colspan="9" class="empty-row">No grants found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($grants as $g): ?>
                        <tr>
                            <td><?= htmlspecialchars($g['employee_name']) ?></td>
                            <td><?= htmlspecialchars($g['leave_type']) ?></td>

                            <td>
                                <span class="badge-days"><?= (float)$g['total_days'] ?> days</span>
                            </td>

                            <td><?= (float)$g['used_days'] ?> days</td>

                            <td>
                                <?php if ((float)$g['remaining_days'] <= 0): ?>
                                    <span class="badge badge-danger">0 days</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= (float)$g['remaining_days'] ?> days</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($g['grant_reason'] ?? '—') ?></td>

                            <td><?= htmlspecialchars($g['granted_by_name'] ?? '—') ?></td>

                            <td><?= date('d M Y', strtotime($g['created_at'])) ?></td>

                            <td>
                                <?php if ((float)$g['used_days'] == 0): ?>
                                    <form method="POST"
                                        action="/admin/leave-grants/revoke"
                                        onsubmit="return confirm('Remove this grant?')">
                                        <input type="hidden" name="id" value="<?= $g['id'] ?>">
                                        <button class="btn-outline-danger">Revoke</button>
                                    </form>
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

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
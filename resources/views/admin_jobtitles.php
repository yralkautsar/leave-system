<?php ob_start(); ?>

<?php $jobTitles = $jobTitles ?? []; ?>

<!-- ── Header ── -->
<div class="u-header">
    <div>
        <h2 class="u-title">Job Titles</h2>
        <p class="subtext">Manage position titles used across the organisation</p>
    </div>
    <button class="btn-primary" onclick="openAddJt()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Job Title
    </button>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert-success" style="margin-bottom:16px;"><?= htmlspecialchars($_SESSION['success']);
                                                            unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert-error" style="margin-bottom:16px;"><?= htmlspecialchars($_SESSION['error']);
                                                            unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- ── Table card ── -->
<div class="u-card">
    <div class="u-card-top">
        <span class="caption-text"><?= count($jobTitles) ?> job title<?= count($jobTitles) !== 1 ? 's' : '' ?></span>
    </div>

    <table class="u-table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Active Employees</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($jobTitles)): ?>
                <tr>
                    <td colspan="3" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="12" cy="8" r="5" />
                                    <path d="M20 21a8 8 0 1 0-16 0" />
                                </svg>
                            </div>
                            <div class="empty-state-title">No job titles yet</div>
                            <div class="empty-state-desc">Add position titles like "Senior Developer" or "HR Manager" to assign to employees.</div>
                            <button class="btn-primary" onclick="openAddJt()">+ Add First Job Title</button>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($jobTitles as $j): ?>
                    <tr>
                        <td style="font-weight:600; color:#0f172a;"><?= htmlspecialchars($j['name']) ?></td>
                        <td>
                            <?php if ($j['user_count'] > 0): ?>
                                <span class="badge badge-days"><?= $j['user_count'] ?> employee<?= $j['user_count'] > 1 ? 's' : '' ?></span>
                            <?php else: ?>
                                <span class="subtext">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="crud-actions">
                                <button class="btn-outline" style="font-size:12.5px;padding:5px 12px;"
                                    onclick="openEditJt(<?= $j['id'] ?>, '<?= htmlspecialchars(addslashes($j['name'])) ?>')">
                                    Edit
                                </button>
                                <?php if ($j['user_count'] > 0): ?>
                                    <button class="btn-disabled" disabled
                                        title="Cannot delete — <?= $j['user_count'] ?> employee(s) assigned">
                                        Delete
                                    </button>
                                <?php else: ?>
                                    <form method="POST" action="/leave-system/public/admin/job-titles/delete"
                                        style="display:inline"
                                        onsubmit="return confirm('Delete job title «<?= htmlspecialchars(addslashes($j['name'])) ?>»?')">
                                        <input type="hidden" name="id" value="<?= $j['id'] ?>">
                                        <button class="btn-outline-danger" style="font-size:12.5px;padding:5px 12px;">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function openAddJt() {
        openGM({
            title: 'Add Job Title',
            size: 'sm',
            html: `
        <form method="POST" action="/leave-system/public/admin/job-titles/store">
            <div class="gm-body">
                <div class="gm-fg">
                    <label>Job Title <span style="color:#dc2626">*</span></label>
                    <input type="text" name="name" required autofocus placeholder="e.g. Senior Developer, HR Manager">
                </div>
            </div>
            <div class="gm-ft">
                <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
                <button type="submit" class="gm-btn-save">Add Job Title</button>
            </div>
        </form>`
        });
    }

    function openEditJt(id, currentName) {
        openGM({
            title: 'Edit Job Title',
            size: 'sm',
            html: `
        <form method="POST" action="/leave-system/public/admin/job-titles/update/${id}">
            <div class="gm-body">
                <div class="gm-fg">
                    <label>Job Title <span style="color:#dc2626">*</span></label>
                    <input type="text" name="name" value="${escH(currentName)}" required autofocus>
                </div>
            </div>
            <div class="gm-ft">
                <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
                <button type="submit" class="gm-btn-save">Save Changes</button>
            </div>
        </form>`
        });
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
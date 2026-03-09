<?php ob_start(); ?>

<?php $departments = $departments ?? []; ?>

<!-- ── Header ── -->
<div class="u-header">
    <div>
        <h2 class="u-title">Departments</h2>
        <p class="subtext">Manage organisational departments</p>
    </div>
    <button class="btn-primary" onclick="openAddDept()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Department
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
        <span class="caption-text"><?= count($departments) ?> department<?= count($departments) !== 1 ? 's' : '' ?></span>
    </div>

    <table class="u-table">
        <thead>
            <tr>
                <th>Department</th>
                <th>Active Employees</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($departments)): ?>
                <tr>
                    <td colspan="3" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="2" y="7" width="20" height="14" rx="2" />
                                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                </svg>
                            </div>
                            <div class="empty-state-title">No departments yet</div>
                            <div class="empty-state-desc">Create your first department to start organising employees by team or division.</div>
                            <button class="btn-primary" onclick="openAddDept()">+ Add First Department</button>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($departments as $d): ?>
                    <tr>
                        <td style="font-weight:600; color:#0f172a;"><?= htmlspecialchars($d['name']) ?></td>
                        <td>
                            <?php if ($d['user_count'] > 0): ?>
                                <span class="badge badge-days"><?= $d['user_count'] ?> employee<?= $d['user_count'] > 1 ? 's' : '' ?></span>
                            <?php else: ?>
                                <span class="subtext">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="crud-actions">
                                <button class="btn-outline" style="font-size:12.5px;padding:5px 12px;"
                                    onclick="openEditDept(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['name'])) ?>')">
                                    Edit
                                </button>
                                <?php if ($d['user_count'] > 0): ?>
                                    <button class="btn-disabled" disabled
                                        title="Cannot delete — <?= $d['user_count'] ?> employee(s) assigned">
                                        Delete
                                    </button>
                                <?php else: ?>
                                    <form method="POST" action="/admin/departments/delete"
                                        style="display:inline"
                                        onsubmit="return confirm('Delete department «<?= htmlspecialchars(addslashes($d['name'])) ?>»?')">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
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
    function openAddDept() {
        openGM({
            title: 'Add Department',
            size: 'sm',
            html: `
        <form method="POST" action="/admin/departments/store">
            <div class="gm-body">
                <div class="gm-fg">
                    <label>Department Name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="name" required autofocus placeholder="e.g. Engineering, HR, Finance">
                </div>
            </div>
            <div class="gm-ft">
                <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
                <button type="submit" class="gm-btn-save">Add Department</button>
            </div>
        </form>`
        });
    }

    function openEditDept(id, currentName) {
        openGM({
            title: 'Edit Department',
            size: 'sm',
            html: `
        <form method="POST" action="/admin/departments/update/${id}">
            <div class="gm-body">
                <div class="gm-fg">
                    <label>Department Name <span style="color:#dc2626">*</span></label>
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
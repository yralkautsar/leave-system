<?php ob_start(); ?>

<?php $departments = $departments ?? []; ?>

<!-- ── Header (outside card, on grey bg) ── -->
<div class="u-header">
    <div>
        <h2 class="u-title">Departments</h2>
        <p class="subtext">Manage organisational departments</p>
    </div>
    <button class="btn-primary" onclick="openAddModal()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                    <td colspan="3" class="empty-row">No departments yet.</td>
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
                                <button class="btn-outline" style="font-size:12.5px; padding:5px 12px;"
                                    onclick="openEditModal(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['name'])) ?>')">
                                    Edit
                                </button>
                                <?php if ($d['user_count'] > 0): ?>
                                    <button class="btn-disabled" disabled title="Cannot delete — employees assigned">Delete</button>
                                <?php else: ?>
                                    <form method="POST" action="/leave-system/public/admin/departments/delete"
                                        style="display:inline"
                                        onsubmit="return confirm('Delete &quot;<?= htmlspecialchars(addslashes($d['name'])) ?>&quot;?')">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <button class="btn-outline-danger" style="font-size:12.5px; padding:5px 12px;">Delete</button>
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

<div id="deptModalRoot"></div>

<script>
    function openAddModal() {
        document.getElementById('deptModalRoot').innerHTML = `
        <div class="modal" onclick="if(event.target===this)closeDeptModal()">
            <div class="modal-content" style="width:420px;max-width:95vw;">
                <h3 style="margin:0 0 18px;font-size:16px;">Add Department</h3>
                <form method="POST" action="/leave-system/public/admin/departments/store">
                    <div class="form-group">
                        <label>Department Name</label>
                        <input type="text" name="name" required autofocus placeholder="e.g. Engineering">
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-outline" onclick="closeDeptModal()">Cancel</button>
                        <button type="submit" class="btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>`;
    }

    function openEditModal(id, currentName) {
        document.getElementById('deptModalRoot').innerHTML = `
        <div class="modal" onclick="if(event.target===this)closeDeptModal()">
            <div class="modal-content" style="width:420px;max-width:95vw;">
                <h3 style="margin:0 0 18px;font-size:16px;">Edit Department</h3>
                <form method="POST" action="/leave-system/public/admin/departments/update/${id}">
                    <div class="form-group">
                        <label>Department Name</label>
                        <input type="text" name="name" value="${currentName}" required autofocus>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-outline" onclick="closeDeptModal()">Cancel</button>
                        <button type="submit" class="btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>`;
    }

    function closeDeptModal() {
        document.getElementById('deptModalRoot').innerHTML = '';
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
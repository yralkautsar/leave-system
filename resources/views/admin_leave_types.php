<?php ob_start(); ?>

<?php $leaveTypes = $leaveTypes ?? []; ?>

<style>
    .lt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .lt-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .lt-top {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .lt-table {
        width: 100%;
        border-collapse: collapse;
    }

    .lt-table thead th {
        padding: 10px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .lt-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f8fafc;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }

    .lt-table tbody tr:last-child td {
        border-bottom: none;
    }

    .lt-table tbody tr {
        transition: background .12s;
    }

    .lt-table tbody tr:hover {
        background: #fff7ed;
    }

    .lt-days-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .lt-count {
        font-size: 13px;
        color: #64748b;
    }

    .lt-btn-edit {
        padding: 5px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: .15s;
    }

    .lt-btn-edit:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
    }

    .lt-btn-del {
        padding: 5px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #fca5a5;
        background: #fff;
        color: #dc2626;
        cursor: pointer;
        transition: .15s;
    }

    .lt-btn-del:hover {
        background: #fee2e2;
    }

    .lt-btn-dis {
        padding: 5px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .lt-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: ltFade .15s ease;
    }

    @keyframes ltFade {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    .lt-modal {
        background: #fff;
        border-radius: 16px;
        width: 420px;
        max-width: 95vw;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        animation: ltSlide .18s ease;
        overflow: hidden;
    }

    @keyframes ltSlide {
        from {
            opacity: 0;
            transform: translateY(10px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .lt-modal-hd {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .lt-modal-hd h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .lt-modal-x {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        border: none;
        background: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        transition: .15s;
    }

    .lt-modal-x:hover {
        background: #f1f5f9;
        color: #374151;
    }

    .lt-modal-body {
        padding: 20px 24px;
    }

    .lt-modal-ft {
        padding: 14px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .lt-fg {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 14px;
    }

    .lt-fg label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .lt-fg input {
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: .15s;
    }

    .lt-fg input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .lt-hint {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 3px;
    }

    .lt-btn-cancel {
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: .15s;
    }

    .lt-btn-cancel:hover {
        background: #f8fafc;
    }

    .lt-btn-save {
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        background: #f97316;
        color: #fff;
        cursor: pointer;
        transition: .15s;
    }

    .lt-btn-save:hover {
        background: #ea580c;
    }
</style>

<div class="lt-header">
    <div>
        <h2 style="margin:0 0 4px;">Leave Types</h2>
        <p class="subtext" style="margin:0;">Configure leave categories and their default annual allocation</p>
    </div>
    <button class="btn-primary" onclick="openLtModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Leave Type
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

<div class="lt-card">
    <div class="lt-top">
        <span style="font-size:13px;color:#94a3b8;"><?= count($leaveTypes) ?> type<?= count($leaveTypes) !== 1 ? 's' : '' ?></span>
        <span style="font-size:12px;color:#94a3b8;">Default days are used when generating balances for a new period.</span>
    </div>

    <table class="lt-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Default Days / Period</th>
                <th>Employees with Balance</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leaveTypes)): ?>
                <tr>
                    <td colspan="4" class="empty-row">No leave types yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($leaveTypes as $t): ?>
                    <tr>
                        <td style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($t['name']) ?></td>
                        <td><span class="lt-days-badge"><?= (int)$t['default_days'] ?> days</span></td>
                        <td class="lt-count"><?= (int)$t['employee_count'] ?> employee<?= $t['employee_count'] != 1 ? 's' : '' ?></td>
                        <td>
                            <div style="display:flex;gap:8px;justify-content:flex-end;">
                                <button class="lt-btn-edit"
                                    onclick='openLtModal(<?= htmlspecialchars(json_encode($t), ENT_QUOTES) ?>)'>
                                    Edit
                                </button>
                                <?php if ($t['employee_count'] > 0): ?>
                                    <button class="lt-btn-dis" title="Cannot delete — employees have balances for this type">
                                        Delete
                                    </button>
                                <?php else: ?>
                                    <form method="POST" action="/leave-system/public/admin/leave-types/delete" style="margin:0;"
                                        onsubmit="return confirm('Delete leave type &quot;<?= htmlspecialchars(addslashes($t['name'])) ?>&quot;?')">
                                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                        <button class="lt-btn-del">Delete</button>
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

<div id="ltModalRoot"></div>

<script>
    function openLtModal(t) {
        const isEdit = !!t;
        const action = isEdit ?
            `/leave-system/public/admin/leave-types/${t.id}/update` :
            '/leave-system/public/admin/leave-types/store';
        const v = k => t ? (t[k] ?? '') : '';

        document.getElementById('ltModalRoot').innerHTML = `
    <div class="lt-modal-backdrop" onclick="if(event.target===this)closeLtModal()">
    <div class="lt-modal">
        <div class="lt-modal-hd">
            <h3>${isEdit ? 'Edit Leave Type' : 'Add Leave Type'}</h3>
            <button type="button" class="lt-modal-x" onclick="closeLtModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form method="POST" action="${action}">
        <div class="lt-modal-body">
            <div class="lt-fg">
                <label>Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="${escH(v('name'))}" required autofocus
                    placeholder="e.g. Annual Leave, Sick Leave">
            </div>
            <div class="lt-fg">
                <label>Default Days per Period <span style="color:#dc2626;">*</span></label>
                <input type="number" name="default_days" value="${v('default_days') || 0}" min="0" required>
                <span class="lt-hint">Used when generating balances. Can be adjusted per employee after generation.</span>
            </div>
        </div>
        <div class="lt-modal-ft">
            <button type="button" class="lt-btn-cancel" onclick="closeLtModal()">Cancel</button>
            <button type="submit" class="lt-btn-save">${isEdit ? 'Save Changes' : 'Add'}</button>
        </div>
        </form>
    </div>
    </div>`;
    }

    function closeLtModal() {
        document.getElementById('ltModalRoot').innerHTML = '';
    }

    function escH(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
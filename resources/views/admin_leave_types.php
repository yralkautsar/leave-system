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

    /* lt-modal-* shell CSS removed — uses global openGM() + .gm-* from admin.css */
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
                <th>Balance Source</th>
                <th>Default Days</th>
                <th>Employees with Balance</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leaveTypes)): ?>
                <tr>
                    <td colspan="5" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <line x1="12" y1="1" x2="12" y2="23" />
                                    <path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6" />
                                </svg>
                            </div>
                            <div class="empty-state-title">No leave types yet</div>
                            <div class="empty-state-desc">Leave types define what employees can apply for.</div>
                            <button class="btn-primary" onclick="openLtModal()">+ Add First Leave Type</button>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php
                $sourceLabels = [
                    'period'      => ['Period (Auto-generate)', '#dbeafe', '#1d4ed8'],
                    'comp'        => ['Compensate Claim',       '#ede9fe', '#6d28d9'],
                    'unlimited'   => ['Unlimited (No quota)',   '#dcfce7', '#15803d'],
                    'admin_grant' => ['Admin Grant (Event)',    '#fef3c7', '#92400e'],
                ];
                ?>
                <?php foreach ($leaveTypes as $t):
                    $src   = $t['balance_source'] ?? 'period';
                    [$srcLabel, $srcBg, $srcColor] = $sourceLabels[$src] ?? $sourceLabels['period'];
                ?>
                    <tr>
                        <td style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($t['name']) ?></td>
                        <td>
                            <span style="background:<?= $srcBg ?>;color:<?= $srcColor ?>;
                                 padding:3px 10px;border-radius:99px;font-size:12px;font-weight:600;">
                                <?= $srcLabel ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($src === 'period'): ?>
                                <span class="lt-days-badge"><?= (int)$t['default_days'] ?> days</span>
                            <?php else: ?>
                                <span class="subtext">—</span>
                            <?php endif; ?>
                        </td>
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
                                    <form method="POST" action="/admin/leave-types/delete" style="margin:0;"
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

<script>
    function openLtModal(t) {
        const isEdit = !!t;
        const action = isEdit ?
            `/admin/leave-types/${t.id}/update` :
            '/admin/leave-types/store';
        const v = k => t ? (t[k] ?? '') : '';

        const sourceOptions = [{
                value: 'period',
                label: 'Period (Auto-generate per period)'
            },
            {
                value: 'unlimited',
                label: 'Unlimited (No quota — Sick Leave)'
            },
            {
                value: 'admin_grant',
                label: 'Admin Grant (Event-based — Marriage, Maternity, etc.)'
            },
            {
                value: 'comp',
                label: 'Compensate Claim (Employee submits claim)'
            },
        ];

        const srcSel = sourceOptions.map(o =>
            `<option value="${o.value}" ${v('balance_source') === o.value ? 'selected' : ''}>${o.label}</option>`
        ).join('');

        const currentSrc = v('balance_source') || 'period';
        const showDays = currentSrc === 'period';

        openGM({
            title: isEdit ? 'Edit Leave Type' : 'Add Leave Type',
            size: 'sm',
            html: `
        <form method="POST" action="${action}" style="display:contents;">
        <div class="gm-body">
            <div class="gm-fg">
                <label>Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="${escH(v('name'))}" required
                    placeholder="e.g. Annual Leave, Sick Leave">
            </div>
            <div class="gm-fg">
                <label>Balance Source <span style="color:#dc2626;">*</span></label>
                <select name="balance_source" id="ltSrcSel" onchange="toggleDaysField(this.value)">
                    ${srcSel}
                </select>
                <span class="gm-hint">Determines how balance is tracked for this leave type.</span>
            </div>
            <div class="gm-fg" id="ltDaysField" style="display:${showDays ? 'flex' : 'none'};flex-direction:column;">
                <label>Default Days per Period <span style="color:#dc2626;">*</span></label>
                <input type="number" name="default_days" value="${v('default_days') || 0}" min="0">
                <span class="gm-hint">Used when generating balances for a period.</span>
            </div>
        </div>
        <div class="gm-ft">
            <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
            <button type="submit" class="gm-btn-save">${isEdit ? 'Save Changes' : 'Add'}</button>
        </div>
        </form>`,
            onOpen: () => {
                document.querySelector('.gm-body input[name="name"]')?.focus();
            }
        });
    }

    function toggleDaysField(src) {
        const field = document.getElementById('ltDaysField');
        if (field) field.style.display = src === 'period' ? 'flex' : 'none';
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
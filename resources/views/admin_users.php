<?php ob_start(); ?>

<?php
$users       = $users       ?? [];
$departments = $departments ?? [];
$jobTitles   = $jobTitles   ?? [];
$allUsers    = $allUsers    ?? [];
$roleLabel   = ['employee' => 'Employee', 'admin_approver' => 'Admin'];
?>

<style>
    /* ── Page layout ── */
    .u-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 16px;
    }

    /* ── Table ── */
    .u-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .u-card-top {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .u-table {
        width: 100%;
        border-collapse: collapse;
    }

    .u-table thead th {
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

    .u-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 13.5px;
        color: #374151;
    }

    .u-table tbody tr:last-child td {
        border-bottom: none;
    }

    .u-table tbody tr {
        transition: background .15s ease;
    }

    .u-table tbody tr:hover {
        background: #fff7ed;
    }

    /* ── Avatar ── */
    .u-emp {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .u-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #fff7ed;
        color: #f97316;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #fed7aa;
    }

    .u-name {
        font-weight: 600;
        font-size: 13.5px;
        color: #0f172a;
    }

    .u-email {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 1px;
    }

    /* ── Badges ── */
    .u-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .u-badge-admin {
        background: #dcfce7;
        color: #166534;
    }

    .u-badge-employee {
        background: #fff7ed;
        color: #c2410c;
    }

    .u-badge-active {
        background: #dcfce7;
        color: #166534;
    }

    .u-badge-suspended {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ── Actions ── */
    .u-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .u-btn-edit {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: .15s ease;
    }

    .u-btn-edit:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
    }

    .u-btn-suspend {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid #fca5a5;
        background: #fff;
        color: #dc2626;
        cursor: pointer;
        transition: .15s ease;
    }

    .u-btn-suspend:hover {
        background: #fee2e2;
    }

    .u-btn-activate {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid #86efac;
        background: #fff;
        color: #16a34a;
        cursor: pointer;
        transition: .15s ease;
    }

    .u-btn-activate:hover {
        background: #dcfce7;
    }

    /* ── HoD cell ── */
    .u-hod-name {
        font-size: 13px;
        font-weight: 500;
        color: #0f172a;
    }

    .u-hod-email {
        font-size: 11px;
        color: #94a3b8;
    }

    /* ── Modal ── */
    .u-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: uFadeIn .15s ease;
    }

    @keyframes uFadeIn {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    .u-modal {
        background: #fff;
        border-radius: 16px;
        width: 640px;
        max-width: 95vw;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        animation: uSlideUp .18s ease;
        overflow: hidden;
    }

    @keyframes uSlideUp {
        from {
            opacity: 0;
            transform: translateY(12px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .u-modal-hd {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 22px 28px 18px;
        border-bottom: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .u-modal-hd h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .u-modal-x {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: none;
        background: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        transition: .15s ease;
    }

    .u-modal-x:hover {
        background: #f1f5f9;
        color: #374151;
    }

    .u-modal-body {
        padding: 20px 28px;
        overflow-y: auto;
        flex: 1;
    }

    .u-modal-ft {
        padding: 16px 28px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-shrink: 0;
    }

    /* ── Form inside modal ── */
    .u-section {
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin: 20px 0 10px;
    }

    .u-section:first-child {
        margin-top: 0;
    }

    .u-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .u-full {
        grid-column: 1/-1;
    }

    .u-fg {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .u-fg label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }

    .u-fg input,
    .u-fg select {
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        background: #fff;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: .15s ease;
    }

    .u-fg input:focus,
    .u-fg select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .u-req {
        color: #dc2626;
        font-size: 11px;
    }

    .u-opt {
        color: #94a3b8;
        font-size: 11px;
        font-weight: 400;
    }

    .u-hint {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 3px;
    }

    /* ── Modal buttons ── */
    .u-btn-cancel {
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: .15s ease;
    }

    .u-btn-cancel:hover {
        background: #f8fafc;
    }

    .u-btn-save {
        padding: 9px 22px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        border: none;
        background: #f97316;
        color: #fff;
        cursor: pointer;
        transition: .15s ease;
    }

    .u-btn-save:hover {
        background: #ea580c;
    }

    @media (max-width:560px) {
        .u-grid {
            grid-template-columns: 1fr;
        }

        .u-full {
            grid-column: 1;
        }
    }
</style>

<!-- HEADER -->
<div class="u-header">
    <div>
        <h2 style="margin:0 0 4px;">Users</h2>
        <p class="subtext" style="margin:0;">Manage employee accounts, roles, and reporting lines</p>
    </div>
    <button class="btn-primary" onclick="openUserModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add User
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

<!-- TABLE -->
<div class="u-card">

    <div class="u-card-top">
        <span style="font-size:13px;color:#94a3b8;"><?= count($users) ?> user<?= count($users) !== 1 ? 's' : '' ?></span>
    </div>

    <table class="u-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Department</th>
                <th>Job Title</th>
                <th>Role</th>
                <th>Head of Dept</th>
                <th>Join Date</th>
                <th>Status</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>

            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <div class="empty-state-title">No users yet</div>
                            <div class="empty-state-desc">Add your first employee or admin using the form above. Set up Departments and Job Titles first for a complete profile.</div>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <tr>

                        <td>
                            <div class="u-emp">
                                <div class="u-avatar" style="<?= !$u['is_active'] ? 'opacity:.4;' : '' ?>">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="u-name"><?= htmlspecialchars($u['name']) ?></div>
                                    <div class="u-email"><?= htmlspecialchars($u['email']) ?></div>
                                </div>
                            </div>
                        </td>

                        <td><?= htmlspecialchars($u['department'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($u['job_title']  ?? '—') ?></td>

                        <td>
                            <span class="u-badge <?= $u['role'] === 'admin_approver' ? 'u-badge-admin' : 'u-badge-employee' ?>">
                                <?= $roleLabel[$u['role']] ?? $u['role'] ?>
                            </span>
                        </td>

                        <td>
                            <?php if (!empty($u['hod_name'])): ?>
                                <div class="u-hod-name"><?= htmlspecialchars($u['hod_name']) ?></div>
                                <div class="u-hod-email"><?= htmlspecialchars($u['hod_email']) ?></div>
                            <?php else: ?><span style="color:#cbd5e1;">—</span><?php endif; ?>
                        </td>

                        <td style="color:#64748b;">
                            <?= $u['join_date'] ? date('d M Y', strtotime($u['join_date'])) : '—' ?>
                        </td>

                        <td>
                            <span class="u-badge <?= $u['is_active'] ? 'u-badge-active' : 'u-badge-suspended' ?>">
                                <?= $u['is_active'] ? 'Active' : 'Suspended' ?>
                            </span>
                        </td>

                        <td>
                            <div class="u-actions">
                                <button class="u-btn-edit"
                                    onclick='openUserModal(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)'>
                                    Edit
                                </button>
                                <form method="POST" action="/leave-system/public/admin/users/toggle-status" style="margin:0;">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <?php if ($u['is_active']): ?>
                                        <button class="u-btn-suspend"
                                            onclick="return confirm('Suspend <?= htmlspecialchars(addslashes($u['name'])) ?>?')">
                                            Suspend
                                        </button>
                                    <?php else: ?>
                                        <button class="u-btn-activate">Activate</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

        </tbody>
    </table>
</div>

<!-- MODAL ROOT -->
<div id="userModalRoot"></div>

<script>
    const DEPTS = <?= json_encode(array_values($departments)) ?>;
    const JOB_TITLES = <?= json_encode(array_values($jobTitles)) ?>;
    const ALL_USERS = <?= json_encode(array_values($allUsers)) ?>;

    function selOpts(data, vKey, lKey, selId, placeholder) {
        let h = `<option value="">${placeholder}</option>`;
        data.forEach(i => {
            h += `<option value="${i[vKey]}" ${i[vKey] == selId ? 'selected' : ''}>${escH(i[lKey])}</option>`;
        });
        return h;
    }

    function userOpts(selId, ph) {
        let h = `<option value="">${ph}</option>`;
        ALL_USERS.forEach(u => {
            h += `<option value="${u.id}" ${u.id == selId ? 'selected' : ''}>${escH(u.name)} — ${escH(u.email)}</option>`;
        });
        return h;
    }

    function escH(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function openUserModal(user) {
        const isEdit = !!user;
        const u = user || {};
        const action = isEdit ?
            `/leave-system/public/admin/users/update/${u.id}` :
            '/leave-system/public/admin/users/store';

        const v = k => u[k] ?? '';

        document.getElementById('userModalRoot').innerHTML = `
    <div class="u-modal-backdrop" onclick="if(event.target===this)closeUserModal()">
    <div class="u-modal">

        <div class="u-modal-hd">
            <h3>${isEdit ? 'Edit User' : 'Add User'}</h3>
            <button class="u-modal-x" onclick="closeUserModal()" type="button">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="${action}" style="display:contents;">
        <div class="u-modal-body">

            <div class="u-section">Account</div>
            <div class="u-grid">
                <div class="u-fg">
                    <label>Full Name <span class="u-req">*</span></label>
                    <input type="text" name="name" value="${escH(v('name'))}" required autofocus>
                </div>
                <div class="u-fg">
                    <label>Email <span class="u-req">*</span></label>
                    <input type="email" name="email" value="${escH(v('email'))}" required>
                </div>
                <div class="u-fg">
                    <label>Password ${isEdit
                        ? '<span class="u-opt">(leave blank to keep)</span>'
                        : '<span class="u-req">*</span>'}
                    </label>
                    <input type="password" name="password" ${isEdit ? '' : 'required'}
                        autocomplete="new-password" placeholder="${isEdit ? '••••••••' : ''}">
                </div>
                <div class="u-fg">
                    <label>Role <span class="u-req">*</span></label>
                    <select name="role" required>
                        <option value="employee"       ${v('role')==='employee'       ?'selected':''}>Employee</option>
                        <option value="admin_approver" ${v('role')==='admin_approver' ?'selected':''}>Admin</option>
                    </select>
                </div>
            </div>

            <div class="u-section">Organisation</div>
            <div class="u-grid">
                <div class="u-fg">
                    <label>Department</label>
                    <select name="department_id">${selOpts(DEPTS, 'id', 'name', v('department_id'), 'Select Department')}</select>
                </div>
                <div class="u-fg">
                    <label>Job Title</label>
                    <select name="job_title_id">${selOpts(JOB_TITLES, 'id', 'name', v('job_title_id'), 'Select Job Title')}</select>
                </div>
                <div class="u-fg">
                    <label>Join Date <span class="u-req">*</span></label>
                    <input type="date" name="join_date" value="${escH(v('join_date'))}" required>
                </div>
            </div>

            <div class="u-section">Reporting Line</div>
            <div class="u-grid">
                <div class="u-fg u-full">
                    <label>Head of Department</label>
                    <select name="hod_id">${userOpts(v('hod_id'), 'None — not assigned')}</select>
                    <span class="u-hint">Receives email notification when this user submits or gets a leave decision.</span>
                </div>
                <div class="u-fg u-full">
                    <label>General Manager</label>
                    <select name="gm_id">${userOpts(v('gm_id'), 'None — not assigned')}</select>
                    <span class="u-hint">CC'd when leave is approved.</span>
                </div>
            </div>

        </div><!-- body -->

        <div class="u-modal-ft">
            <button type="button" class="u-btn-cancel" onclick="closeUserModal()">Cancel</button>
            <button type="submit" class="u-btn-save">${isEdit ? 'Save Changes' : 'Add User'}</button>
        </div>

        </form>
    </div>
    </div>`;
    }

    function closeUserModal() {
        document.getElementById('userModalRoot').innerHTML = '';
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
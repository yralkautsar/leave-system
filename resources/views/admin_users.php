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

    /* ── Filter bar ── */
    .u-filter-bar {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .u-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .u-fg input,
    .u-fg select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        color: #374151;
        background: #fff;
        box-sizing: border-box;
    }

    .u-fg input:focus,
    .u-fg select:focus {
        border-color: #f97316;
        outline: none;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }

    .u-btn-search {
        padding: 9px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        background: #f97316;
        color: #fff;
        cursor: pointer;
        white-space: nowrap;
    }

    .u-btn-search:hover {
        background: #ea580c;
    }

    .u-btn-clear {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #64748b;
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
    }

    .u-btn-clear:hover {
        background: #f8fafc;
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

    .u-btn-reset {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid #94a3b8;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: .15s ease;
    }

    .u-btn-reset:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
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
    /* u-modal shell CSS removed — uses global openGM() + .gm-* */

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
    /* u-btn-cancel/save removed — uses gm-btn-cancel/save inside openGM() */

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
<?php if (isset($_SESSION['warning'])): ?>
    <div class="alert-warning" style="margin-bottom:16px;"><?= htmlspecialchars($_SESSION['warning']);
                                                            unset($_SESSION['warning']); ?></div>
<?php endif; ?>

<!-- SEARCH / FILTER -->
<div class="u-filter-bar">
    <form method="GET" action="/leave-system/public/admin/users">
        <div class="u-filter-grid">
            <div class="u-fg">
                <input type="text" name="search"
                    placeholder="Search name or email…"
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="u-fg">
                <select name="role">
                    <option value="">All Roles</option>
                    <option value="employee" <?= (($_GET['role'] ?? '') === 'employee')       ? 'selected' : '' ?>>Employee</option>
                    <option value="admin_approver" <?= (($_GET['role'] ?? '') === 'admin_approver') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="u-fg">
                <select name="dept">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= (($_GET['dept'] ?? '') == $d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="u-fg">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="active" <?= (($_GET['status'] ?? '') === 'active')    ? 'selected' : '' ?>>Active</option>
                    <option value="suspended" <?= (($_GET['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <button type="submit" class="u-btn-search">Search</button>
                <?php if (!empty($_GET['search']) || !empty($_GET['role']) || !empty($_GET['dept']) || !empty($_GET['status'])): ?>
                    <a href="/leave-system/public/admin/users" class="u-btn-clear">Clear</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

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
                                <button class="u-btn-reset"
                                    onclick='openResetPwModal(<?= (int)$u['id'] ?>, <?= htmlspecialchars(json_encode($u['name']), ENT_QUOTES) ?>)'>
                                    Reset PW
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

<script>
    const DEPTS = <?= json_encode(array_values($departments)) ?>;
    const JOB_TITLES = <?= json_encode(array_values($jobTitles)) ?>;
    const ALL_USERS = <?= json_encode(array_values($allUsers)) ?>;
    const WORK_SCHEDS = <?= json_encode(array_values($workSchedules)) ?>;

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
    /* escH() defined globally in layout.php */

    function openUserModal(user) {
        const isEdit = !!user;
        const u = user || {};
        const action = isEdit ?
            `/leave-system/public/admin/users/update/${u.id}` :
            '/leave-system/public/admin/users/store';

        const v = k => u[k] ?? '';

        openGM({
            title: isEdit ? 'Edit User' : 'Add User',
            size: 'lg',
            html: `
        <form method="POST" action="${action}" style="display:contents;">
        <div class="gm-body" style="padding:20px 28px;">

            <div class="u-section">Account</div>
            <div class="u-grid">
                <div class="u-fg">
                    <label>Full Name <span class="u-req">*</span></label>
                    <input type="text" name="name" value="${escH(v('name'))}" required>
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
                    <label>Work Schedule <span class="u-req">*</span></label>
                    <select name="work_schedule_id" required>
                        <option value="">Select Schedule</option>
                        ${WORK_SCHEDS.map(w => `<option value="${w.id}" ${w.id == v('work_schedule_id') ? 'selected' : ''}>${escH(w.name)}</option>`).join('')}
                    </select>
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

        </div>

        <div class="gm-ft">
            <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
            <button type="submit" class="gm-btn-save">${isEdit ? 'Save Changes' : 'Add User'}</button>
        </div>
        </form>`,
            onOpen: () => {
                document.querySelector('.gm-body input[name="name"]')?.focus();
            }
        });
    }
    /* closeUserModal removed — use closeGM() from layout.php */

    function openResetPwModal(userId, userName) {
        openGM({
            title: 'Reset Password',
            size: 'sm',
            html: `
        <form method="POST" action="/leave-system/public/admin/users/reset-password"
              style="display:contents;"
              onsubmit="return validateResetPw(this)">
        <input type="hidden" name="user_id" value="${userId}">
        <div class="gm-body" style="padding:20px 28px;">
            <p style="margin:0 0 18px;font-size:13.5px;color:#64748b;">
                Resetting password for <strong style="color:#0f172a;">${escH(userName)}</strong>.
                Employee must be notified manually.
            </p>
            <div class="u-fg" style="margin-bottom:14px;">
                <label>New Password <span class="u-req">*</span></label>
                <input type="password" name="password" id="rpPw" required
                    placeholder="Min. 8 characters" autocomplete="new-password">
            </div>
            <div class="u-fg">
                <label>Confirm Password <span class="u-req">*</span></label>
                <input type="password" name="password_confirm" id="rpPwC" required
                    placeholder="Repeat password" autocomplete="new-password">
            </div>
            <div id="rpErr" style="display:none;margin-top:10px;font-size:12.5px;
                color:#dc2626;background:#fee2e2;padding:8px 12px;border-radius:8px;">
            </div>
        </div>
        <div class="gm-ft">
            <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
            <button type="submit" class="gm-btn-save">Reset Password</button>
        </div>
        </form>`,
            onOpen: () => {
                document.getElementById('rpPw')?.focus();
            }
        });
    }

    function validateResetPw(form) {
        const pw = document.getElementById('rpPw')?.value || '';
        const pwc = document.getElementById('rpPwC')?.value || '';
        const err = document.getElementById('rpErr');
        if (pw.length < 8) {
            err.textContent = 'Password must be at least 8 characters.';
            err.style.display = 'block';
            return false;
        }
        if (pw !== pwc) {
            err.textContent = 'Passwords do not match.';
            err.style.display = 'block';
            return false;
        }
        return true;
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
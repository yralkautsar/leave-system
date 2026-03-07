<?php ob_start(); ?>

<?php
/* ── Defensive fallbacks — prevent fatal error if controller is out of sync ── */
$requests   = $requests   ?? [];
$counts     = $counts     ?? ['all' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'cancelled' => 0];
$leaveTypes = $leaveTypes ?? [];

$currentStatus = $_GET['status'] ?? '';
$currentSearch = $_GET['search'] ?? '';
$currentType   = $_GET['leave_type'] ?? '';
$currentFrom   = $_GET['date_from'] ?? '';
$currentTo     = $_GET['date_to'] ?? '';

/* ── Build base URL preserving non-status filters ── */
function reqUrl(string $status = ''): string
{
    $params = array_filter([
        'status'     => $status,
        'search'     => $_GET['search']     ?? '',
        'leave_type' => $_GET['leave_type'] ?? '',
        'date_from'  => $_GET['date_from']  ?? '',
        'date_to'    => $_GET['date_to']    ?? '',
    ]);
    $qs = $params ? '?' . http_build_query($params) : '';
    return '/leave-system/public/admin/requests' . $qs;
}

$statusLabels = [
    ''          => 'All',
    'pending'   => 'Pending',
    'approved'  => 'Approved',
    'rejected'  => 'Rejected',
    'cancelled' => 'Cancelled',
];

$badgeClass = [
    'pending'   => 'badge badge-pending',
    'approved'  => 'badge badge-success',
    'rejected'  => 'badge badge-danger',
    'cancelled' => 'badge badge-cancelled',
];
?>

<!-- ══ PAGE HEADER ══ -->
<div class="req-header">
    <div>
        <h2>Leave Requests</h2>
        <p class="subtext">Review, approve, or reject employee leave requests</p>
    </div>
</div>

<!-- ══ STATUS TABS ══ -->
<div class="req-tabs">
    <?php foreach ($statusLabels as $val => $label): ?>
        <a href="<?= reqUrl($val) ?>"
            class="req-tab <?= $currentStatus === $val ? 'active' : '' ?>">
            <?= $label ?>
            <span class="req-tab-count">
                <?= number_format($counts[$val === '' ? 'all' : $val] ?? 0) ?>
            </span>
        </a>
    <?php endforeach; ?>
</div>

<!-- ══ FILTER BAR ══ -->
<div class="req-filters card">
    <form method="GET" action="/leave-system/public/admin/requests" id="filterForm">

        <!-- preserve active status tab -->
        <?php if ($currentStatus): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
        <?php endif; ?>

        <div class="req-filter-row">

            <!-- Search -->
            <div class="req-filter-search">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input
                    type="text"
                    name="search"
                    placeholder="Search employee name..."
                    value="<?= htmlspecialchars($currentSearch) ?>"
                    autocomplete="off">
            </div>

            <!-- Leave type -->
            <select name="leave_type" class="req-filter-select" onchange="this.form.submit()">
                <option value="">All Leave Types</option>
                <?php foreach ($leaveTypes as $lt): ?>
                    <option value="<?= $lt['id'] ?>"
                        <?= $currentType == $lt['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lt['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Date range -->
            <div class="req-filter-dates">
                <input type="date" name="date_from"
                    value="<?= htmlspecialchars($currentFrom) ?>"
                    placeholder="From"
                    title="Start date from">
                <span class="req-filter-sep">–</span>
                <input type="date" name="date_to"
                    value="<?= htmlspecialchars($currentTo) ?>"
                    placeholder="To"
                    title="Start date to">
            </div>

            <button type="submit" class="btn-primary">Filter</button>

            <?php if ($currentSearch || $currentType || $currentFrom || $currentTo): ?>
                <a href="<?= reqUrl($currentStatus) ?>" class="btn-outline">Clear</a>
            <?php endif; ?>

        </div>

    </form>
</div>

<!-- ══ TABLE ══ -->
<div class="card" style="padding:0; overflow:hidden;">

    <div class="req-table-head">
        <span class="caption-text"><?= count($requests) ?> result<?= count($requests) !== 1 ? 's' : '' ?></span>
    </div>

    <table class="req-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Days</th>
                <th>Submitted</th>
                <th>Status</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>

            <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="8" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                </svg>
                            </div>
                            <div class="empty-state-title">No requests found</div>
                            <div class="empty-state-desc">
                                <?php if ($currentStatus === 'pending'): ?>
                                    Great — no pending requests right now. All caught up!
                                <?php elseif ($currentSearch || $currentType || $currentFrom || $currentTo): ?>
                                    No requests match the current filters. Try adjusting your search.
                                <?php else: ?>
                                    No leave requests have been submitted yet.
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($requests as $r): ?>
                    <tr>

                        <!-- Employee -->
                        <td>
                            <div class="req-emp">
                                <div class="req-emp-avatar">
                                    <?= strtoupper(substr($r['employee'], 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($r['employee']) ?></span>
                            </div>
                        </td>

                        <!-- Department -->
                        <td>
                            <span class="req-dept"><?= htmlspecialchars($r['department'] ?? '—') ?></span>
                        </td>

                        <!-- Leave type -->
                        <td><?= htmlspecialchars($r['leave_type']) ?></td>

                        <!-- Duration -->
                        <td class="req-dates">
                            <?= date('d M Y', strtotime($r['start_date'])) ?>
                            <?php if ($r['start_date'] !== $r['end_date']): ?>
                                <span class="req-date-sep">→</span>
                                <?= date('d M Y', strtotime($r['end_date'])) ?>
                            <?php endif; ?>
                        </td>

                        <!-- Days -->
                        <td>
                            <span class="badge badge-days"><?= $r['total_days'] ?> day<?= $r['total_days'] > 1 ? 's' : '' ?></span>
                        </td>

                        <!-- Submitted -->
                        <td class="req-submitted">
                            <?= date('d M Y', strtotime($r['created_at'])) ?>
                            <span class="req-time"><?= date('H:i', strtotime($r['created_at'])) ?></span>
                        </td>

                        <!-- Status -->
                        <td>
                            <span class="<?= $badgeClass[$r['status']] ?? 'badge' ?>">
                                <?= ucfirst($r['status']) ?>
                            </span>
                        </td>

                        <!-- Action -->
                        <td>
                            <div class="req-actions">

                                <!-- View detail — always shown -->
                                <button
                                    class="btn-outline req-view-btn"
                                    onclick="openDetailModal(<?= $r['id'] ?>)"
                                    title="View detail">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    View
                                </button>

                                <?php if ($r['status'] === 'pending'): ?>

                                    <form method="POST" action="/leave-system/public/approve" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="_from" value="requests">
                                        <button class="btn-outline-success">Approve</button>
                                    </form>

                                    <form method="POST" action="/leave-system/public/reject" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <input type="hidden" name="_from" value="requests">
                                        <button class="btn-outline-danger">Reject</button>
                                    </form>

                                <?php elseif ($r['status'] === 'approved'): ?>

                                    <form method="POST"
                                        action="/leave-system/public/admin/requests/<?= $r['id'] ?>/revoke"
                                        style="display:inline;"
                                        onsubmit="return confirm('Revoke this approved leave? Balance will be restored and request returned to pending.')">
                                        <input type="hidden" name="_qs" value="<?= htmlspecialchars(http_build_query(array_filter(['status' => $currentStatus, 'search' => $currentSearch, 'leave_type' => $currentType]))) ?>">
                                        <button class="btn-outline-danger">Revoke</button>
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

<style>
    /* ── Page header ── */
    .req-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 16px;
    }

    /* ── Status tabs ── */
    .req-tabs {
        display: flex;
        gap: 4px;
        border-bottom: 2px solid var(--border);
        margin-bottom: 20px;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .req-tabs::-webkit-scrollbar {
        display: none;
    }

    .req-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 16px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--text-muted);
        text-decoration: none;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -2px;
        white-space: nowrap;
        transition: color var(--ease), border-color var(--ease);
    }

    .req-tab:hover {
        color: var(--primary);
    }

    .req-tab.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        font-weight: 700;
    }

    .req-tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--border);
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: var(--radius-full);
        min-width: 22px;
    }

    .req-tab.active .req-tab-count {
        background: var(--primary);
        color: white;
    }

    /* ── Filter bar ── */
    .req-filters {
        padding: 16px 20px;
        margin-bottom: 20px;
    }

    .req-filter-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .req-filter-search {
        flex: 1;
        min-width: 200px;
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        padding: 8px 12px;
        transition: border-color var(--ease);
    }

    .req-filter-search:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .req-filter-search svg {
        color: var(--text-light);
        flex-shrink: 0;
    }

    .req-filter-search input {
        border: none;
        background: none;
        outline: none;
        font-size: 13.5px;
        color: var(--text);
        width: 100%;
    }

    .req-filter-select {
        padding: 9px 12px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 13px;
        color: var(--text);
        background: var(--bg);
        outline: none;
        cursor: pointer;
        transition: border-color var(--ease);
    }

    .req-filter-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .req-filter-dates {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .req-filter-dates input {
        padding: 8px 10px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 13px;
        color: var(--text);
        background: var(--bg);
        outline: none;
        transition: border-color var(--ease);
    }

    .req-filter-dates input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .req-filter-sep {
        font-size: 13px;
        color: var(--text-light);
    }

    /* ── Table ── */
    .req-table-head {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        background: var(--bg);
    }

    .req-table {
        width: 100%;
        border-collapse: collapse;
    }

    .req-table thead th {
        padding: 11px 14px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .05em;
        background: var(--bg);
        border-bottom: 1px solid var(--border);
    }

    .req-table tbody td {
        padding: 13px 14px;
        font-size: 13.5px;
        border-bottom: 1px solid var(--border-light);
        vertical-align: middle;
    }

    .req-table tbody tr:last-child td {
        border-bottom: none;
    }

    .req-table tbody tr {
        transition: background var(--ease);
    }

    .req-table tbody tr:hover {
        background: var(--primary-soft);
    }

    /* Employee cell */
    .req-emp {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .req-emp-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .req-dept {
        font-size: 12px;
        color: var(--text-muted);
    }

    .req-dates {
        font-size: 13px;
        color: var(--text);
        white-space: nowrap;
    }

    .req-date-sep {
        color: var(--text-light);
        margin: 0 4px;
        font-size: 11px;
    }

    .req-submitted {
        font-size: 12.5px;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .req-time {
        display: block;
        font-size: 11px;
        color: var(--text-light);
        margin-top: 1px;
    }

    .req-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .req-view-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        padding: 6px 10px;
    }
</style>

<!-- ══ DETAIL MODAL ══ -->
<!-- detail modal uses globalModalRoot -->

<script>
    async function openDetailModal(id) {
        document.getElementById('globalModalRoot').innerHTML = `
        <div class="gm-bd">
            <div class="gm-box gm-box-lg">
                <div class="gm-hd">
                    <h3>Leave Request</h3>
                    <button type="button" class="gm-x" onclick="closeDetailModal()">✕</button>
                </div>
                <div class="gm-body" style="text-align:center;padding:48px 0;color:#94a3b8;">
                    <div class="req-spinner"></div>
                    <p style="margin-top:14px;font-size:13px;">Loading…</p>
                </div>
            </div>
        </div>`;

        try {
            const res = await fetch(`/leave-system/public/admin/requests/${id}/detail`);
            const data = await res.json();
            if (data.error) throw new Error(data.error);

            const fmt = s => s ? new Date(s + 'T00:00:00')
                .toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) : '—';
            const fmtDt = s => s ? new Date(s)
                .toLocaleString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }) : '—';

            const sc = {
                pending: '#f97316',
                approved: '#16a34a',
                rejected: '#dc2626',
                cancelled: '#64748b'
            } [data.status] || '#64748b';

            let balanceRow = '';
            if (data.balance) {
                const pct = data.balance.total_days > 0 ?
                    Math.round((data.balance.remaining_days / data.balance.total_days) * 100) : 0;
                const barColor = pct > 50 ? '#16a34a' : pct > 20 ? '#f59e0b' : '#dc2626';
                balanceRow = `
                <div class="det-section">
                    <div class="det-section-title">Leave Balance (Active Period)</div>
                    <div class="det-balance-nums">
                        <span><strong>${data.balance.remaining_days}</strong> days remaining</span>
                        <span style="color:var(--text-light)">${data.balance.used_days} used · ${data.balance.total_days} total</span>
                    </div>
                    <div class="det-balance-bar">
                        <div class="det-balance-fill" style="width:${pct}%;background:${barColor};"></div>
                    </div>
                </div>`;
            }

            document.getElementById('globalModalRoot').innerHTML = `
            <div class="gm-bd" onclick="if(event.target===this)closeDetailModal()">
                <div class="gm-box gm-box-lg" style="max-height:88vh;">

                    <!-- Header -->
                    <div class="gm-hd">
                        <div class="det-emp-block">
                            <div class="det-avatar">${data.employee_name.charAt(0).toUpperCase()}</div>
                            <div>
                                <div class="det-emp-name">${data.employee_name}</div>
                                <div class="det-emp-meta">${data.department||'—'} · ${data.job_title||'—'}</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                            <span class="badge" style="background:${sc}18;color:${sc};border:1px solid ${sc}40;font-size:12px;padding:4px 10px;border-radius:999px;">
                                ${data.status.charAt(0).toUpperCase()+data.status.slice(1)}
                            </span>
                            <button type="button" class="gm-x" onclick="closeDetailModal()">✕</button>
                        </div>
                    </div>

                    <!-- Body (scrollable) -->
                    <div class="gm-body">

                        <div class="det-section">
                            <div class="det-section-title">Leave Details</div>
                            <div class="det-grid">
                                <div class="det-item">
                                    <div class="det-label">Leave Type</div>
                                    <div class="det-value">${data.leave_type}</div>
                                </div>
                                <div class="det-item">
                                    <div class="det-label">Duration</div>
                                    <div class="det-value">${data.total_days} day${data.total_days>1?'s':''} · ${data.duration_type==='half'?'Half Day':'Full Day'}</div>
                                </div>
                                <div class="det-item">
                                    <div class="det-label">Start Date</div>
                                    <div class="det-value">${fmt(data.start_date)}</div>
                                </div>
                                <div class="det-item">
                                    <div class="det-label">End Date</div>
                                    <div class="det-value">${fmt(data.end_date)}</div>
                                </div>
                                <div class="det-item">
                                    <div class="det-label">Period</div>
                                    <div class="det-value">${data.period_name||'—'}</div>
                                </div>
                                <div class="det-item">
                                    <div class="det-label">Submitted</div>
                                    <div class="det-value">${fmtDt(data.created_at)}</div>
                                </div>
                                ${data.approved_at ? `
                                <div class="det-item">
                                    <div class="det-label">Processed At</div>
                                    <div class="det-value">${fmtDt(data.approved_at)}</div>
                                </div>` : ''}
                                ${data.processed_by ? `
                                <div class="det-item">
                                    <div class="det-label">Processed By</div>
                                    <div class="det-value">${data.processed_by}</div>
                                </div>` : ''}
                                ${data.employee_email ? `
                                <div class="det-item">
                                    <div class="det-label">Email</div>
                                    <div class="det-value">${data.employee_email}</div>
                                </div>` : ''}
                            </div>
                        </div>

                        ${balanceRow}

                    </div><!-- /.gm-body -->

                    <!-- Footer -->
                    <div class="gm-ft">
                        ${data.status === 'pending' ? `
                        <form method="POST" action="/leave-system/public/reject" style="margin:0;">
                            <input type="hidden" name="id" value="${data.id}">
                            <button class="gm-btn-danger" onclick="closeDetailModal()">Reject</button>
                        </form>
                        <form method="POST" action="/leave-system/public/approve" style="margin:0;">
                            <input type="hidden" name="id" value="${data.id}">
                            <button class="gm-btn-save" style="background:#16a34a;" onclick="closeDetailModal()">Approve</button>
                        </form>
                        ` : `<button class="gm-btn-cancel" onclick="closeDetailModal()">Close</button>`}
                    </div>

                </div>
            </div>`;

        } catch (e) {
            document.getElementById('globalModalRoot').innerHTML = `
            <div class="gm-bd" onclick="if(event.target===this)closeDetailModal()">
                <div class="gm-box gm-box-sm">
                    <div class="gm-hd"><h3>Error</h3><button class="gm-x" onclick="closeDetailModal()">✕</button></div>
                    <div class="gm-body"><p style="color:#dc2626;font-size:13px;margin:0;">Failed to load: ${e.message}</p></div>
                    <div class="gm-ft">
                        <button class="gm-btn-cancel" onclick="closeDetailModal()">Close</button>
                    </div>
                </div>
            </div>`;
        }
    }

    function closeDetailModal() {
        document.getElementById('globalModalRoot').innerHTML = '';
    }
</script>

<style>
    .det-modal {
        width: 540px;
        max-width: 95vw;
        padding: 0;
        overflow: hidden;
    }

    .det-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 24px 28px 20px;
        border-bottom: 1px solid var(--border);
        gap: 12px;
    }

    .det-emp-block {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .det-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .det-emp-name {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .det-emp-meta {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .det-section {
        padding: 18px 28px;
        border-bottom: 1px solid var(--border-light);
    }

    .det-section:last-of-type {
        border-bottom: none;
    }

    .det-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .det-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .det-label {
        font-size: 11px;
        color: var(--text-light);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 3px;
    }

    .det-value {
        font-size: 13.5px;
        font-weight: 500;
        color: #0f172a;
    }

    .det-balance-nums {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .det-balance-bar {
        height: 6px;
        background: var(--border);
        border-radius: 99px;
        overflow: hidden;
    }

    .det-balance-fill {
        height: 100%;
        border-radius: 99px;
        transition: width .4s ease;
    }

    .det-modal .modal-actions {
        padding: 16px 28px;
        border-top: 1px solid var(--border);
        margin-top: 0;
    }

    .req-spinner {
        width: 28px;
        height: 28px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin .6s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
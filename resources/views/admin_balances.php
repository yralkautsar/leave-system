<?php ob_start(); ?>

<?php
$balances   = $balances   ?? [];
$leaveTypes = $leaveTypes ?? [];
$periods    = $periods    ?? [];

$sourceInfo = [
    'period'      => ['Annual (Period)',    '#dcfce7', '#166534'],
    'comp'        => ['Compensate',         '#ede9fe', '#6d28d9'],
    'admin_grant' => ['Event Grant',        '#fef3c7', '#92400e'],
    'unlimited'   => ['Unlimited',          '#e0f2fe', '#0369a1'],
];
?>

<style>
    .bal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .bal-filter {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 18px 20px;
        margin-bottom: 20px;
    }

    .bal-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 12px;
        align-items: end;
    }

    .bal-fg {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .bal-fg label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
    }

    .bal-fg input,
    .bal-fg select {
        padding: 8px 11px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: .15s;
    }

    .bal-fg input:focus,
    .bal-fg select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }

    .bal-btn-search {
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

    .bal-btn-search:hover {
        background: #ea580c;
    }

    .bal-btn-clear {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #64748b;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .bal-btn-clear:hover {
        background: #f8fafc;
    }

    .bal-btn-export {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid #16a34a;
        background: #fff;
        color: #16a34a;
        text-decoration: none;
        display: inline-block;
    }

    .bal-btn-export:hover {
        background: #dcfce7;
    }

    .bal-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .bal-card-top {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bal-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bal-table thead th {
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .bal-table tbody td {
        padding: 13px 14px;
        border-bottom: 1px solid #f8fafc;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }

    .bal-table tbody tr:last-child td {
        border-bottom: none;
    }

    .bal-table tbody tr {
        transition: background .12s;
    }

    .bal-table tbody tr:hover {
        background: #fff7ed;
    }

    .bal-src-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .bal-bar-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .bal-bar-outer {
        flex: 1;
        background: #f1f5f9;
        border-radius: 999px;
        height: 6px;
        min-width: 60px;
    }

    .bal-bar-inner {
        height: 6px;
        border-radius: 999px;
    }

    .bal-bar-inner.low {
        background: #dc2626;
    }

    .bal-bar-inner.mid {
        background: #f97316;
    }

    .bal-bar-inner.full {
        background: #16a34a;
    }

    .bal-days-main {
        font-weight: 700;
        font-size: 14px;
        color: #0f172a;
    }

    .bal-days-sub {
        font-size: 11.5px;
        color: #94a3b8;
    }

    .bal-badge-zero {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11.5px;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        font-weight: 600;
    }

    .bal-badge-warn {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11.5px;
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        font-weight: 600;
    }

    .bal-period-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 500;
    }

    .bal-period-active {
        background: #dcfce7;
        color: #166534;
    }

    .bal-period-upcoming {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .bal-period-expired {
        background: #f1f5f9;
        color: #64748b;
    }

    .bal-btn-adj {
        padding: 5px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        cursor: pointer;
    }

    .bal-btn-adj:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
    }

    .bal-btn-hist {
        padding: 5px 12px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        cursor: pointer;
    }

    .bal-btn-hist:hover {
        border-color: #2563eb;
        color: #2563eb;
        background: #eff6ff;
    }

    .bal-ctx {
        background: #f8fafc;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
    }

    .bal-ctx-name {
        font-weight: 700;
        font-size: 14px;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .bal-ctx-meta {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 10px;
    }

    .bal-ctx-stats {
        display: flex;
        gap: 0;
        border-top: 1px solid #e5e7eb;
        padding-top: 10px;
    }

    .bal-ctx-stat {
        flex: 1;
        text-align: center;
    }

    .bal-ctx-stat:not(:last-child) {
        border-right: 1px solid #e5e7eb;
    }

    .bal-ctx-stat .sv {
        font-weight: 800;
        font-size: 22px;
        color: #0f172a;
    }

    .bal-ctx-stat .sk {
        font-size: 10px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-top: 2px;
    }

    .bal-mode-tabs {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        margin-bottom: 18px;
    }

    .bal-mode-tab {
        padding: 9px 6px;
        border-radius: 9px;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        color: #64748b;
        text-align: center;
    }

    .bal-mode-tab.active-add {
        border-color: #16a34a;
        background: #dcfce7;
        color: #166534;
    }

    .bal-mode-tab.active-deduct {
        border-color: #dc2626;
        background: #fee2e2;
        color: #991b1b;
    }

    .bal-mode-tab.active-set {
        border-color: #f97316;
        background: #fff7ed;
        color: #c2410c;
    }

    .bal-fg-m {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 14px;
    }

    .bal-fg-m label {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .bal-fg-m input {
        padding: 10px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 9px;
        font-size: 14px;
        color: #0f172a;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }

    .bal-fg-m input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
    }

    .bal-hint {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 4px;
        line-height: 1.5;
    }

    .bal-preview {
        margin-top: 8px;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 500;
        display: none;
    }

    .bal-preview.add {
        background: #dcfce7;
        color: #166534;
    }

    .bal-preview.deduct {
        background: #fee2e2;
        color: #991b1b;
    }

    .bal-preview.set {
        background: #fff7ed;
        color: #c2410c;
    }

    .bal-hist-wrap {
        max-height: 300px;
        overflow-y: auto;
    }

    .bal-hist-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .bal-hist-table th {
        padding: 7px 10px;
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #94a3b8;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .bal-hist-table td {
        padding: 9px 10px;
        border-bottom: 1px solid #f8fafc;
        color: #374151;
    }

    .bal-hist-table tr:last-child td {
        border-bottom: none;
    }

    .bal-hist-pos {
        color: #16a34a;
        font-weight: 700;
    }

    .bal-hist-neg {
        color: #dc2626;
        font-weight: 700;
    }

    .bal-alert {
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 18px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .bal-alert-ok {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .bal-alert-err {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .bal-summary {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .bal-sum-pill {
        padding: 7px 16px;
        border-radius: 99px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1.5px solid;
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>

<!-- Header -->
<div class="bal-header">
    <div>
        <h2 style="margin:0 0 4px;">Leave Balances</h2>
        <p class="subtext" style="margin:0;">All leave allocations — annual, compensate, event grants &amp; unlimited</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <form method="POST" action="/admin/balance-sync"
            onsubmit="return confirm('Recalculate remaining_days for ALL period balances?')">
            <button type="submit" class="btn-outline" style="font-size:13px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;">
                    <polyline points="23 4 23 10 17 10" />
                    <polyline points="1 20 1 14 7 14" />
                    <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" />
                </svg>
                Sync Balances
            </button>
        </form>
        <a href="/admin/periods" class="btn-outline" style="font-size:13px;">&#8592; Back to Periods</a>
    </div>
</div>

<!-- Flash -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bal-alert bal-alert-ok"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <polyline points="20 6 9 17 4 12" />
        </svg><?= htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="bal-alert bal-alert-err"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg><?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Filter -->
<div class="bal-filter">
    <form method="GET" action="/admin/balances">
        <div class="bal-filter-grid">
            <div class="bal-fg">
                <label>Employee</label>
                <input type="text" name="search" placeholder="Search name&#8230;" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="bal-fg">
                <label>Leave Type</label>
                <select name="type">
                    <option value="">All Types</option>
                    <?php foreach ($leaveTypes as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (($_GET['type'] ?? '') == $t['id']) ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="bal-fg">
                <label>Period <span style="font-weight:400;color:#b0bec5;font-size:10px;">(annual only)</span></label>
                <select name="period">
                    <option value="">Active periods</option>
                    <?php foreach ($periods as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= (($_GET['period'] ?? '') == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <button type="submit" class="bal-btn-search">Search</button>
                <?php if (!empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['period'])): ?>
                    <a href="/admin/balances" class="bal-btn-clear">Clear</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
    <?php $exportQuery = http_build_query(['search' => $_GET['search'] ?? '', 'type' => $_GET['type'] ?? '', 'period' => $_GET['period'] ?? '']); ?>
    <div style="margin-top:12px;text-align:right;">
        <a href="/admin/balances/export?<?= $exportQuery ?>" class="bal-btn-export">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:5px;">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="7 10 12 15 17 10" />
                <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            Export CSV
        </a>
    </div>
</div>

<?php
$countBySource = [];
foreach ($balances as $b) {
    $src = $b['balance_source'] ?? 'period';
    $countBySource[$src] = ($countBySource[$src] ?? 0) + 1;
}
?>

<!-- Summary pills -->
<?php if (!empty($balances)): ?>
    <div class="bal-summary">
        <?php foreach ($countBySource as $src => $cnt):
            [$label, $bg, $color] = $sourceInfo[$src] ?? ['Other', '#f1f5f9', '#64748b'];
        ?>
            <div class="bal-sum-pill" style="background:<?= $bg ?>;color:<?= $color ?>;border-color:<?= $color ?>30;">
                <?= $label ?> <strong><?= $cnt ?></strong>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Table -->
<div class="bal-card">
    <div class="bal-card-top">
        <span style="font-size:13px;color:#64748b;font-weight:500;"><?= count($balances) ?> record<?= count($balances) !== 1 ? 's' : '' ?></span>
        <span style="font-size:12px;color:#94a3b8;"><?= (empty($_GET['period']) && empty($_GET['search']) && empty($_GET['type'])) ? 'Active periods &middot; non-zero balances only' : 'Filtered results' ?></span>
    </div>

    <table class="bal-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Leave Type</th>
                <th>Source</th>
                <th>Period / Scope</th>
                <th>Remaining</th>
                <th>Quota</th>
                <th>Used</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($balances)): ?>
                <tr>
                    <td colspan="8" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <line x1="12" y1="1" x2="12" y2="23" />
                                    <path d="M17 5H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6" />
                                </svg></div>
                            <div class="empty-state-title">No leave balances found</div>
                            <?php if (!empty($_GET['search']) || !empty($_GET['type']) || !empty($_GET['period'])): ?>
                                <div class="empty-state-desc">No balances match filters. <a href="/admin/balances">Clear search</a>.</div>
                            <?php else: ?>
                                <div class="empty-state-desc">Generate from <a href="/admin/periods">Leave Periods</a> or grant via <a href="/admin/leave-grants">Leave Grants</a>.</div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($balances as $b):
                    $src   = $b['balance_source'] ?? 'period';
                    $rem   = ($b['remaining_days'] !== null) ? (float)$b['remaining_days'] : null;
                    $total = ($b['total_days']     !== null) ? (float)$b['total_days']     : null;
                    $used  = ($b['used_days']      !== null) ? (float)$b['used_days']      : 0;

                    $pct      = ($total > 0 && $rem !== null) ? min(100, round($rem / $total * 100)) : 0;
                    $barClass = $pct < 30 ? 'low' : ($pct < 70 ? 'mid' : 'full');

                    [$srcLabel, $srcBg, $srcColor] = $sourceInfo[$src] ?? ['Other', '#f1f5f9', '#64748b'];

                    $today = date('Y-m-d');
                    $ps = 'active';
                    if ($b['period_start'] && $b['period_end']) {
                        if ($b['period_start'] > $today)                                   $ps = 'upcoming';
                        elseif ($b['period_end'] < $today)                                 $ps = 'expired';
                    }

                    $bJson = null;
                    if (in_array($src, ['period', 'admin_grant']) && !empty($b['balance_id'])) {
                        $bJson = htmlspecialchars(json_encode([
                            'balance_id'    => (int)$b['balance_id'],
                            'employee_name' => $b['employee_name'],
                            'leave_type'    => $b['leave_type'],
                            'period_name'   => $b['period_name'] ?? ucfirst(str_replace('_', ' ', $src)),
                            'remaining'     => (float)($rem ?? 0),
                            'total'         => (float)($total ?? 0),
                            'used'          => (float)$used,
                        ]), ENT_QUOTES);
                    }
                ?>
                    <tr>
                        <td style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($b['employee_name']) ?></td>
                        <td><?= htmlspecialchars($b['leave_type']) ?></td>

                        <td><span class="bal-src-badge" style="background:<?= $srcBg ?>;color:<?= $srcColor ?>;"><?= $srcLabel ?></span></td>

                        <td>
                            <?php if ($src === 'period' && $b['period_name']): ?>
                                <span class="bal-period-pill bal-period-<?= $ps ?>"><?= htmlspecialchars($b['period_name']) ?></span>
                            <?php elseif ($src === 'comp'): ?>
                                <span style="font-size:12px;color:#7c3aed;">Floating &middot; expires per claim</span>
                            <?php elseif ($src === 'admin_grant'): ?>
                                <span style="font-size:12px;color:#92400e;">Event-based &middot; no expiry</span>
                            <?php else: ?>
                                <span style="font-size:12px;color:#0369a1;">No quota &middot; YTD usage</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($src === 'unlimited'): ?>
                                <span style="font-size:13px;color:#0369a1;font-weight:600;">&#8734; Unlimited</span>
                            <?php elseif ($rem === null): ?>
                                <span class="subtext">&#8212;</span>
                            <?php elseif ($rem < 0): ?>
                                <span class="bal-badge-warn"><?= $rem ?> days &#9888;</span>
                            <?php elseif ($rem == 0): ?>
                                <span class="bal-badge-zero">0 days left</span>
                            <?php else: ?>
                                <div class="bal-bar-wrap">
                                    <div style="min-width:52px;">
                                        <span class="bal-days-main"><?= $rem ?></span>
                                        <?php if ($total !== null): ?><span class="bal-days-sub"> / <?= $total ?></span><?php endif; ?>
                                    </div>
                                    <?php if ($total !== null): ?>
                                        <div class="bal-bar-outer">
                                            <div class="bal-bar-inner <?= $barClass ?>" style="width:<?= $pct ?>%;"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ($src === 'unlimited'): ?>
                                <span style="color:#0369a1;font-weight:600;">Unlimited</span>
                            <?php elseif ($total !== null): ?>
                                <span style="font-weight:600;color:#374151;"><?= $total ?> days</span>
                            <?php else: ?>
                                <span class="subtext">&#8212;</span>
                            <?php endif; ?>
                        </td>

                        <td style="color:#64748b;"><?= $used ?> days</td>

                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <?php if ($bJson): ?>
                                    <button class="bal-btn-adj" onclick='openAdjModal(<?= $bJson ?>)'>&#9998; Adjust</button>
                                    <button class="bal-btn-hist" onclick='openHistModal(<?= (int)$b['balance_id'] ?>,<?= htmlspecialchars(json_encode($b['employee_name']), ENT_QUOTES) ?>,<?= htmlspecialchars(json_encode($b['leave_type']), ENT_QUOTES) ?>)'>History</button>
                                <?php elseif ($src === 'comp'): ?>
                                    <a href="/admin/comp-claims?search=<?= urlencode($b['employee_name']) ?>" class="bal-btn-hist" style="text-decoration:none;">View Claims</a>
                                <?php else: ?>
                                    <span class="subtext" style="font-size:12px;">&#8212;</span>
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
    const currentFilters = {
        period: '<?= htmlspecialchars($_GET['period'] ?? '') ?>',
        type: '<?= htmlspecialchars($_GET['type'] ?? '') ?>',
        search: '<?= htmlspecialchars($_GET['search'] ?? '') ?>'
    };
    let _adjData = null;

    function openAdjModal(b) {
        _adjData = b;
        renderAdjModal('add');
    }

    function renderAdjModal(mode) {
        const b = _adjData;
        openGM({
            title: 'Adjust Leave Balance',
            html: `
        <form method="POST" action="/admin/balance-adjust" id="adjForm" onsubmit="return submitAdj(event)" style="display:contents;">
        <input type="hidden" name="balance_id" value="${b.balance_id}">
        <input type="hidden" name="mode" id="adjMode" value="${mode}">
        <input type="hidden" name="days" id="adjDays" value="">
        <input type="hidden" name="period_filter" value="${currentFilters.period}">
        <input type="hidden" name="type_filter" value="${currentFilters.type}">
        <input type="hidden" name="search_filter" value="${currentFilters.search}">
        <div class="gm-body">
            <div class="bal-ctx">
                <div class="bal-ctx-name">${escH(b.employee_name)}</div>
                <div class="bal-ctx-meta">${escH(b.leave_type)} &middot; ${escH(b.period_name)}</div>
                <div class="bal-ctx-stats">
                    <div class="bal-ctx-stat"><div class="sv" style="color:${b.remaining<0?'#dc2626':'#0f172a'}">${b.remaining}</div><div class="sk">Remaining</div></div>
                    <div class="bal-ctx-stat"><div class="sv">${b.total}</div><div class="sk">Quota</div></div>
                    <div class="bal-ctx-stat"><div class="sv">${b.used}</div><div class="sk">Used</div></div>
                </div>
            </div>
            <div class="bal-mode-tabs">
                <button type="button" id="tabAdd" class="bal-mode-tab ${mode==='add'?'active-add':''}" onclick="switchMode('add')">+ Add Days</button>
                <button type="button" id="tabDeduct" class="bal-mode-tab ${mode==='deduct'?'active-deduct':''}" onclick="switchMode('deduct')">- Deduct Days</button>
                <button type="button" id="tabSet" class="bal-mode-tab ${mode==='set'?'active-set':''}" onclick="switchMode('set')">Set Quota</button>
            </div>
            <div id="adjInputArea">${buildInput(mode,b)}</div>
            <div class="bal-fg-m"><label>Reason (optional)</label><input type="text" name="reason" placeholder="e.g. carry-over, correction"></div>
        </div>
        <div class="gm-ft">
            <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
            <button type="submit" class="gm-btn-save" id="adjSaveBtn" style="background:${modeBtnColor(mode)};">${modeBtnLabel(mode)}</button>
        </div>
        </form>`,
            onOpen: () => attachInput(mode)
        });
    }

    function buildInput(mode, b) {
        if (mode === 'add') return `<div class="bal-fg-m"><label>Days to Add</label><input type="number" id="adjInput" min="1" placeholder="e.g. 5" autofocus><div class="bal-hint">Current remaining: <strong>${b.remaining}</strong></div><div class="bal-preview add" id="adjPreview"></div></div>`;
        if (mode === 'deduct') return `<div class="bal-fg-m"><label>Days to Deduct</label><input type="number" id="adjInput" min="1" max="${Math.max(b.remaining,0)}" placeholder="e.g. 3" autofocus><div class="bal-hint">Max: <strong>${b.remaining}</strong> days</div><div class="bal-preview deduct" id="adjPreview"></div></div>`;
        return `<div class="bal-fg-m"><label>New Total Quota</label><input type="number" id="adjInput" min="0" value="${Math.max(b.total,0)}" autofocus><div class="bal-hint">Remaining = new quota - ${b.used} used</div><div class="bal-preview set" id="adjPreview"></div></div>`;
    }

    function attachInput(mode) {
        setTimeout(() => {
            const inp = document.getElementById('adjInput'),
                pre = document.getElementById('adjPreview');
            if (!inp) return;
            inp.addEventListener('input', () => {
                const v = parseInt(inp.value) || 0,
                    b = _adjData;
                if (!v && mode !== 'set') {
                    pre.style.display = 'none';
                    return;
                }
                pre.style.display = 'block';
                if (mode === 'add') pre.textContent = `After: ${b.remaining}+${v}=${b.remaining+v} days`;
                else if (mode === 'deduct') {
                    const a = b.remaining - v;
                    pre.textContent = a < 0 ? `Cannot deduct ${v} — only ${b.remaining} available` : `After: ${b.remaining}-${v}=${a} days`;
                    pre.style.background = a < 0 ? '#fee2e2' : '';
                    pre.style.color = a < 0 ? '#991b1b' : '';
                } else {
                    const r = v - b.used;
                    pre.textContent = `New quota: ${v} → Remaining: ${r} days${r<0?' ⚠ Used exceeds quota':''}`;
                }
            });
        }, 30);
    }

    function switchMode(mode) {
        document.getElementById('adjMode').value = mode;
        const b = _adjData;
        document.getElementById('tabAdd').className = 'bal-mode-tab' + (mode === 'add' ? ' active-add' : '');
        document.getElementById('tabDeduct').className = 'bal-mode-tab' + (mode === 'deduct' ? ' active-deduct' : '');
        document.getElementById('tabSet').className = 'bal-mode-tab' + (mode === 'set' ? ' active-set' : '');
        document.getElementById('adjInputArea').innerHTML = buildInput(mode, b);
        const btn = document.getElementById('adjSaveBtn');
        btn.style.background = modeBtnColor(mode);
        btn.textContent = modeBtnLabel(mode);
        attachInput(mode);
    }

    function modeBtnColor(m) {
        return m === 'add' ? '#16a34a' : m === 'deduct' ? '#dc2626' : '#f97316';
    }

    function modeBtnLabel(m) {
        return m === 'add' ? 'Add Days' : m === 'deduct' ? 'Deduct Days' : 'Update Quota';
    }

    function submitAdj(e) {
        e.preventDefault();
        const mode = document.getElementById('adjMode').value,
            inp = document.getElementById('adjInput'),
            v = parseInt(inp.value),
            b = _adjData;
        if (isNaN(v)) {
            alert('Enter a valid number.');
            return false;
        }
        if (mode === 'add' && v <= 0) {
            alert('Days must be > 0.');
            return false;
        }
        if (mode === 'deduct' && v <= 0) {
            alert('Days must be > 0.');
            return false;
        }
        if (mode === 'deduct' && v > b.remaining) {
            alert(`Cannot deduct ${v} — only ${b.remaining} remaining.`);
            return false;
        }
        if (mode === 'set' && v < 0) {
            alert('Quota cannot be negative.');
            return false;
        }
        if (mode === 'add') document.getElementById('adjDays').value = v;
        if (mode === 'deduct') document.getElementById('adjDays').value = -v;
        if (mode === 'set') document.getElementById('adjDays').value = v;
        e.target.submit();
    }

    async function openHistModal(balanceId, empName, leaveType) {
        openGM({
            title: 'Adjustment History',
            size: 'lg',
            html: `
        <div class="gm-body">
            <div class="bal-ctx"><div class="bal-ctx-name">${escH(empName)}</div><div class="bal-ctx-meta">${escH(leaveType)}</div></div>
            <div id="histContent" style="text-align:center;padding:24px;color:#94a3b8;font-size:13px;">Loading&#8230;</div>
        </div>
        <div class="gm-ft"><button type="button" class="gm-btn-cancel" onclick="closeGM()">Close</button></div>`
        });
        try {
            const res = await fetch(`/admin/balance-history?balance_id=${balanceId}`);
            const data = await res.json();
            let html = data.length ?
                `<div class="bal-hist-wrap"><table class="bal-hist-table"><thead><tr><th>Change</th><th>Reason</th><th>By</th><th>Date</th></tr></thead><tbody>` +
                data.map(r => `<tr><td class="${r.days_adjusted>0?'bal-hist-pos':'bal-hist-neg'}">${r.days_adjusted>0?'+':''}${r.days_adjusted} days</td><td>${escH(r.reason||'—')}</td><td style="font-weight:500;">${escH(r.admin_name)}</td><td style="white-space:nowrap;font-size:12px;color:#94a3b8;">${r.created_at.slice(0,10)}</td></tr>`).join('') +
                `</tbody></table></div>` :
                '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:16px;">No adjustments yet.</p>';
            const el = document.getElementById('histContent');
            if (el) el.innerHTML = html;
        } catch (e) {
            const el = document.getElementById('histContent');
            if (el) el.innerHTML = '<p style="color:#dc2626;font-size:13px;text-align:center;">Failed to load.</p>';
        }
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
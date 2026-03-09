<?php ob_start(); ?>

<?php
$holidays = $holidays ?? [];
$years    = $years    ?? [date('Y')];
$year     = (int)($_GET['year'] ?? date('Y'));

$monthNames = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

// Group by month
$grouped = [];
foreach ($holidays as $h) {
    $m = (int)date('n', strtotime($h['holiday_date']));
    $grouped[$m][] = $h;
}
?>

<style>
    .hol-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 16px;
    }

    .hol-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Year tabs */
    .hol-years {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .hol-year-tab {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #64748b;
        text-decoration: none;
        transition: .15s ease;
    }

    .hol-year-tab:hover {
        border-color: #f97316;
        color: #f97316;
    }

    .hol-year-tab.active {
        background: #f97316;
        border-color: #f97316;
        color: #fff;
    }

    /* Month group */
    .hol-month {
        margin-bottom: 28px;
    }

    .hol-month-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #94a3b8;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 0;
    }

    /* Table */
    .hol-table {
        width: 100%;
        border-collapse: collapse;
    }

    .hol-table td {
        padding: 12px 14px;
        font-size: 13.5px;
        color: #374151;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    .hol-table tr:last-child td {
        border-bottom: none;
    }

    .hol-table tr {
        transition: background .12s ease;
    }

    .hol-table tr:hover {
        background: #fff7ed;
    }

    /* Date cell */
    .hol-date-block {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .hol-day-num {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #fff7ed;
        color: #f97316;
        font-size: 15px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #fed7aa;
    }

    .hol-day-name {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 1px;
    }

    /* Type badge */
    .hol-badge-national {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .hol-badge-company {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
        background: #f3e8ff;
        color: #7c3aed;
    }


    /* Religion column */
    .hol-rel-all {
        display: inline-block;
        font-size: 11.5px;
        padding: 3px 10px;
        border-radius: 999px;
        background: #f0fdf4;
        color: #15803d;
        font-weight: 600;
        border: 1px solid #bbf7d0;
    }

    .hol-rel-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    .hol-rel-pill {
        display: inline-block;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 999px;
        font-weight: 500;
    }

    .hol-rel-islam {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .hol-rel-kristen {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .hol-rel-katolik {
        background: #faf5ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
    }

    .hol-rel-hindu {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde68a;
    }

    .hol-rel-buddha {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .hol-rel-konghucu {
        background: #fdf2f8;
        color: #9d174d;
        border: 1px solid #fbcfe8;
    }

    /* Actions */
    .hol-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .hol-btn-edit {
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

    .hol-btn-edit:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
    }

    .hol-btn-del {
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

    .hol-btn-del:hover {
        background: #fee2e2;
    }

    /* Empty */
    .hol-empty {
        text-align: center;
        padding: 48px 20px;
        color: #94a3b8;
        font-size: 14px;
    }

    /* Modal */
    /* hol-modal shell CSS removed — uses global openGM() + .gm-* */
</style>

<!-- HEADER -->
<div class="hol-header">
    <div>
        <h2 style="margin:0 0 4px;">Public Holidays</h2>
        <p class="subtext" style="margin:0;">National and company holidays — used to exclude non-working days from leave calculations</p>
    </div>
    <button class="btn-primary" onclick="openHolModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Holiday
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

<!-- YEAR FILTER -->
<div style="display:flex; align-items:center; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
    <span style="font-size:13px; color:#94a3b8; font-weight:600;">Year:</span>
    <div class="hol-years">
        <?php foreach ($years as $y): ?>
            <a href="?year=<?= $y ?>"
                class="hol-year-tab <?= $y == $year ? 'active' : '' ?>">
                <?= $y ?>
            </a>
        <?php endforeach; ?>
    </div>
    <span style="font-size:13px; color:#94a3b8; margin-left:auto;">
        <?= count($holidays) ?> holiday<?= count($holidays) !== 1 ? 's' : '' ?> in <?= $year ?>
    </span>
</div>

<!-- CONTENT -->
<div class="card">
    <?php if (empty($holidays)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
            </div>
            <div class="empty-state-title">No holidays in <?= $year ?></div>
            <div class="empty-state-desc">Add national or company holidays so they're excluded from leave day calculations and shown on the calendar.</div>
            <button class="btn-primary" onclick="openHolModal()">+ Add Holiday</button>
        </div>
    <?php else: ?>
        <?php foreach ($monthNames as $mNum => $mName): ?>
            <?php if (empty($grouped[$mNum])) continue; ?>

            <div class="hol-month">
                <div class="hol-month-label"><?= $mName ?></div>
                <table class="hol-table">
                    <tbody>
                        <?php foreach ($grouped[$mNum] as $h): ?>
                            <?php
                            $ts      = strtotime($h['holiday_date']);
                            $dayNum  = date('d', $ts);
                            $dayName = date('l', $ts);
                            ?>
                            <tr>
                                <td width="200">
                                    <div class="hol-date-block">
                                        <div class="hol-day-num"><?= $dayNum ?></div>
                                        <div>
                                            <div style="font-weight:600;color:#0f172a;"><?= date('d M Y', $ts) ?></div>
                                            <div class="hol-day-name"><?= $dayName ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight:500;color:#0f172a;"><?= htmlspecialchars($h['name']) ?></td>
                                <td width="120">
                                    <?php if ($h['type'] === 'national'): ?>
                                        <span class="hol-badge-national">National</span>
                                    <?php else: ?>
                                        <span class="hol-badge-company">Company</span>
                                    <?php endif; ?>
                                </td>
                                <td width="260">
                                    <?php
                                    $isAll = empty($h['religions']) || count($h['religions']) === 6;
                                    ?>
                                    <?php if ($isAll): ?>
                                        <span class="hol-rel-all">✦ Semua Agama</span>
                                    <?php else: ?>
                                        <div class="hol-rel-pills">
                                            <?php foreach ($h['religions'] as $rel): ?>
                                                <span class="hol-rel-pill hol-rel-<?= strtolower($rel) ?>">
                                                    <?= htmlspecialchars($rel) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td width="160">
                                    <div class="hol-actions">
                                        <button class="hol-btn-edit"
                                            onclick="openHolModal(<?= htmlspecialchars(json_encode($h), ENT_QUOTES) ?>)">
                                            Edit
                                        </button>
                                        <form method="POST" action="/admin/holidays/delete"
                                            style="margin:0;"
                                            onsubmit="return confirm('Delete &quot;<?= htmlspecialchars(addslashes($h['name'])) ?>&quot;?')">
                                            <input type="hidden" name="id" value="<?= $h['id'] ?>">
                                            <button class="hol-btn-del">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- MODAL ROOT -->
<script>
    function openHolModal(h) {
        const isEdit = !!h;
        const action = isEdit ?
            `/admin/holidays/update/${h.id}` :
            '/admin/holidays/store';

        const v = k => h ? (h[k] ?? '') : '';
        const sel = (val, opt) => val === opt ? 'selected' : '';

        openGM({
            title: isEdit ? 'Edit Holiday' : 'Add Holiday',
            size: 'sm',
            html: `
        <form method="POST" action="${action}" style="display:contents;">
        <div class="gm-body">
            <div class="gm-fg">
                <label>Date <span style="color:#dc2626;">*</span></label>
                <input type="date" name="holiday_date" value="${v('holiday_date')}" required>
            </div>
            <div class="gm-fg">
                <label>Holiday Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="${escH(v('name'))}" required
                    placeholder="e.g. Hari Kemerdekaan">
            </div>
            <div class="gm-fg">
                <label>Type</label>
                <select name="type">
                    <option value="national" ${sel(v('type'), 'national')}>National</option>
                    <option value="company"  ${sel(v('type'), 'company')}>Company</option>
                </select>
            </div>
            <div class="gm-fg">
                <label>Applies To
                    <span style="font-size:11px;color:#94a3b8;font-weight:400;"> — leave blank for all religions</span>
                </label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px 12px;margin-top:4px;">
                    ${['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'].map(r => `
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:400;cursor:pointer;">
                        <input type="checkbox" name="religions[]" value="${r}"
                            ${(v('religions') || []).includes(r) ? 'checked' : ''}
                            style="width:14px;height:14px;accent-color:#f97316;">
                        ${r}
                    </label>`).join('')}
                </div>
            </div>
        </div>
        <div class="gm-ft">
            <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
            <button type="submit" class="gm-btn-save">${isEdit ? 'Save Changes' : 'Add Holiday'}</button>
        </div>
        </form>`,
            onOpen: () => {
                document.querySelector('.gm-body input[name="holiday_date"]')?.focus();
            }
        });
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
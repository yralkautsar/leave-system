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
    .hol-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: hFadeIn .15s ease;
    }

    @keyframes hFadeIn {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    .hol-modal {
        background: #fff;
        border-radius: 16px;
        width: 460px;
        max-width: 95vw;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        animation: hSlide .18s ease;
        overflow: hidden;
    }

    @keyframes hSlide {
        from {
            opacity: 0;
            transform: translateY(10px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .hol-modal-hd {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .hol-modal-hd h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .hol-modal-x {
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

    .hol-modal-x:hover {
        background: #f1f5f9;
        color: #374151;
    }

    .hol-modal-body {
        padding: 20px 24px;
    }

    .hol-modal-ft {
        padding: 14px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .hol-fg {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 14px;
    }

    .hol-fg label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .hol-fg input,
    .hol-fg select {
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        background: #fff;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: .15s;
    }

    .hol-fg input:focus,
    .hol-fg select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .hol-btn-cancel {
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

    .hol-btn-cancel:hover {
        background: #f8fafc;
    }

    .hol-btn-save {
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

    .hol-btn-save:hover {
        background: #ea580c;
    }
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
                                <td width="160">
                                    <div class="hol-actions">
                                        <button class="hol-btn-edit"
                                            onclick="openHolModal(<?= htmlspecialchars(json_encode($h), ENT_QUOTES) ?>)">
                                            Edit
                                        </button>
                                        <form method="POST" action="/leave-system/public/admin/holidays/delete"
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
<div id="holModalRoot"></div>

<script>
    function openHolModal(h) {
        const isEdit = !!h;
        const action = isEdit ?
            `/leave-system/public/admin/holidays/update/${h.id}` :
            '/leave-system/public/admin/holidays/store';

        const v = k => h ? (h[k] ?? '') : '';
        const sel = (val, opt) => val === opt ? 'selected' : '';

        document.getElementById('holModalRoot').innerHTML = `
    <div class="hol-modal-backdrop" onclick="if(event.target===this)closeHolModal()">
    <div class="hol-modal">
        <div class="hol-modal-hd">
            <h3>${isEdit ? 'Edit Holiday' : 'Add Holiday'}</h3>
            <button type="button" class="hol-modal-x" onclick="closeHolModal()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="${action}">
        <div class="hol-modal-body">
            <div class="hol-fg">
                <label>Date <span style="color:#dc2626;">*</span></label>
                <input type="date" name="holiday_date" value="${v('holiday_date')}" required autofocus>
            </div>
            <div class="hol-fg">
                <label>Holiday Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="${escH(v('name'))}" required placeholder="e.g. Hari Kemerdekaan">
            </div>
            <div class="hol-fg">
                <label>Type</label>
                <select name="type">
                    <option value="national" ${sel(v('type'), 'national')}>National</option>
                    <option value="company"  ${sel(v('type'), 'company')}>Company</option>
                </select>
            </div>
        </div>
        <div class="hol-modal-ft">
            <button type="button" class="hol-btn-cancel" onclick="closeHolModal()">Cancel</button>
            <button type="submit" class="hol-btn-save">${isEdit ? 'Save Changes' : 'Add Holiday'}</button>
        </div>
        </form>
    </div>
    </div>`;
    }

    function closeHolModal() {
        document.getElementById('holModalRoot').innerHTML = '';
    }

    function escH(s) {
        return String(s ?? '')
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
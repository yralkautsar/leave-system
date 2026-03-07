<?php ob_start(); ?>

<?php
$isAdmin = ($_SESSION['user']['role'] === 'admin_approver');

/* ── Month math ─────────────────────────────────────────── */
$daysInMonth = (int) date('t', strtotime($monthStart));
$firstDay    = (int) date('N', strtotime($monthStart)); // 1=Mon…7=Sun
$totalCells  = $daysInMonth + $firstDay - 1;
$totalWeeks  = (int) ceil($totalCells / 7);

/* ── Nav ────────────────────────────────────────────────── */
$ts   = strtotime($monthStart);
$prev = date('Y-m', strtotime('-1 month', $ts));
$next = date('Y-m', strtotime('+1 month', $ts));

/* ── Color palette ──────────────────────────────────────── */
$palette = [
    '#3b82f6',
    '#ef4444',
    '#10b981',
    '#ec4899',
    '#8b5cf6',
    '#f59e0b',
    '#0ea5e9',
    '#f97316',
    '#06b6d4',
    '#6366f1',
    '#84cc16',
    '#14b8a6',
];
$holidayColor = '#475569';

$colorMap = [];
foreach ($leaveTypes as $i => $lt) {
    $colorMap[$lt['name']] = $palette[$i % count($palette)];
}
foreach ($leaves as $l) {
    if (!isset($colorMap[$l['leave_type']])) {
        $colorMap[$l['leave_type']] = $palette[count($colorMap) % count($palette)];
    }
}

/* ── Pre-process ────────────────────────────────────────── */
$leavesP = [];
foreach ($leaves as $l) {
    $leavesP[] = $l + [
        '_sTs'   => strtotime($l['start_date']),
        '_eTs'   => strtotime($l['end_date']),
        '_color' => $colorMap[$l['leave_type']] ?? '#3b82f6',
    ];
}
$holidaysP = [];
foreach ($holidays as $h) {
    $holidaysP[] = $h + ['_ts' => strtotime($h['date'])];
}

$fmtD = static fn(string $d): string => date('d M', strtotime($d));

/* ── Lane assignment per week ───────────────────────────── */
$weekData = [];
for ($week = 0; $week < $totalWeeks; $week++) {

    $wFirst = $week * 7 - $firstDay + 2;
    $wLast  = $wFirst + 6;
    $wSTs   = strtotime($month . '-' . str_pad(max($wFirst, 1), 2, '0', STR_PAD_LEFT));
    $wETs   = strtotime($month . '-' . str_pad(min($wLast, $daysInMonth), 2, '0', STR_PAD_LEFT));

    $evs = [];

    foreach ($leavesP as $l) {
        if ($l['_sTs'] > $wETs || $l['_eTs'] < $wSTs) continue;
        $segSTs = max($l['_sTs'], $wSTs);
        $segETs = min($l['_eTs'], $wETs);
        $colS   = (int) date('N', $segSTs);
        $colE   = (int) date('N', $segETs);
        $evs[] = [
            'col'    => $colS,
            'span'   => $colE - $colS + 1,
            'color'  => $l['_color'],
            'label'  => htmlspecialchars($l['name'] . ' · ' . $l['leave_type']),
            'cont_l' => ($segSTs > $l['_sTs']),
            'cont_r' => ($segETs < $l['_eTs']),
            'date'   => date('Y-m-d', $segSTs),
            'type'   => 'leave',
        ];
    }

    foreach ($holidaysP as $h) {
        if ($h['_ts'] < $wSTs || $h['_ts'] > $wETs) continue;
        $col = (int) date('N', $h['_ts']);
        $evs[] = [
            'col'    => $col,
            'span'   => 1,
            'color'  => $holidayColor,
            'label'  => htmlspecialchars($h['name']),
            'cont_l' => false,
            'cont_r' => false,
            'date'   => $h['date'],
            'type'   => 'holiday',
        ];
    }

    usort($evs, fn($a, $b) => $a['col'] <=> $b['col']);
    $lanes = [];
    foreach ($evs as &$ev) {
        $end = $ev['col'] + $ev['span'] - 1;
        $placed = false;
        foreach ($lanes as $li => $lEnd) {
            if ($ev['col'] > $lEnd) {
                $ev['lane'] = $li;
                $lanes[$li] = $end;
                $placed = true;
                break;
            }
        }
        if (!$placed) {
            $ev['lane'] = count($lanes);
            $lanes[] = $end;
        }
    }
    unset($ev);

    $weekData[$week] = ['evs' => $evs, 'laneCount' => count($lanes)];
}
?>

<!-- ══════════════════════════════════════════
     PAGE-LEVEL LAYOUT OVERRIDES
     Strip the max-width + padding constraints
     so the calendar fills the full content area
══════════════════════════════════════════ -->
<style>
    /* Make content area full width, no max-width */
    .main {
        padding: 0 !important;
    }

    .content {
        max-width: none !important;
        padding: 0 !important;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
    }
</style>


<!-- ══════════════════════════════════════════
     HEADER BAR  (nav + filter + legend)
══════════════════════════════════════════ -->
<div class="cal-bar">

    <!-- Left: month title + nav -->
    <div class="cal-bar-left">
        <h2 class="cal-month-title"><?= date('F Y', $ts) ?></h2>
        <div class="cal-nav-btns">
            <a class="cal-nav-btn" href="?month=<?= $prev ?>">&#8249;</a>
            <a class="cal-nav-btn cal-nav-today" href="?month=<?= date('Y-m') ?>">Today</a>
            <a class="cal-nav-btn" href="?month=<?= $next ?>">&#8250;</a>
        </div>
    </div>

    <!-- Right: dept filter (admin) + legend -->
    <div class="cal-bar-right">

        <?php if ($isAdmin && !empty($departments)): ?>
            <form method="GET" class="cal-filter-form">
                <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">
                <select name="dept" class="cal-filter-select" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"
                            <?= (($_GET['dept'] ?? '') == $d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>

        <div class="cal-legend">
            <?php foreach ($colorMap as $typeName => $color): ?>
                <div class="cal-leg-pill" style="background:<?= $color ?>18; border-color:<?= $color ?>44;">
                    <span class="cal-leg-dot" style="background:<?= $color ?>;"></span>
                    <?= htmlspecialchars($typeName) ?>
                </div>
            <?php endforeach; ?>
            <div class="cal-leg-pill" style="background:<?= $holidayColor ?>18; border-color:<?= $holidayColor ?>44;">
                <span class="cal-leg-dot" style="background:<?= $holidayColor ?>;"></span>
                Public Holiday
            </div>
        </div>

    </div>
</div>


<!-- ══════════════════════════════════════════
     CALENDAR GRID — fills remaining height
══════════════════════════════════════════ -->
<div class="gcal">

    <!-- Weekday header -->
    <div class="gcal-head">
        <?php foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $wd): ?>
            <div><?= $wd ?></div>
        <?php endforeach; ?>
    </div>

    <!-- Week rows — flex:1 each so they fill equally -->
    <?php for ($week = 0; $week < $totalWeeks; $week++):
        $wd = $weekData[$week];
    ?>
        <div class="gcal-week">

            <!-- Day cells -->
            <?php for ($col = 1; $col <= 7; $col++):
                $ci    = $week * 7 + $col;
                $dn    = $ci - $firstDay + 1;
                $empty = ($dn < 1 || $dn > $daysInMonth);
                $date  = $empty ? '' : ($month . '-' . str_pad($dn, 2, '0', STR_PAD_LEFT));
                $today = ($date === date('Y-m-d'));
                $wknd  = ($col >= 6);
            ?>
                <div class="gcal-cell<?= $empty ? ' gcal-empty' : '' ?><?= $wknd ? ' gcal-wknd' : '' ?>"
                    <?= $date ? "onclick=\"openDayModal('$date')\"" : '' ?>>
                    <?php if (!$empty): ?>
                        <span class="gcal-dn<?= $today ? ' gcal-today' : '' ?>"><?= $dn ?></span>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>

            <!-- Event bars overlay -->
            <div class="gcal-elay">
                <?php foreach ($wd['evs'] as $ev):
                    $top = 30 + $ev['lane'] * 24;
                    $rl  = $ev['cont_l'] ? '0' : '4px';
                    $rr  = $ev['cont_r'] ? '0' : '4px';
                    $ml  = $ev['cont_l'] ? '0' : '3px';
                    $mr  = $ev['cont_r'] ? '0' : '3px';
                ?>
                    <div class="gcal-ev"
                        title="<?= $ev['label'] ?>"
                        onclick="event.stopPropagation(); openDayModal('<?= $ev['date'] ?>')"
                        style="
                         background:<?= $ev['color'] ?>;
                         grid-column:<?= $ev['col'] ?> / span <?= $ev['span'] ?>;
                         top:<?= $top ?>px;
                         border-radius:<?= $rl ?> <?= $rr ?> <?= $rr ?> <?= $rl ?>;
                         margin-left:<?= $ml ?>;
                         margin-right:<?= $mr ?>;
                     ">
                        <?= $ev['label'] ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    <?php endfor; ?>

</div>


<!-- Modal root -->
<div id="calModalRoot"></div>


<!-- ══════════════════════════════════════════
     STYLES
══════════════════════════════════════════ -->
<style>
    /* ── Header bar ──────────────────────────────── */
    .cal-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 24px 12px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .cal-bar-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .cal-bar-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cal-month-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .cal-nav-btns {
        display: flex;
        gap: 5px;
    }

    .cal-nav-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        min-width: 28px;
        padding: 0 9px;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        background: #fff;
        color: #374151;
        font-size: 14px;
        text-decoration: none;
        transition: background .12s, border-color .12s;
    }

    .cal-nav-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: none;
    }

    .cal-nav-today {
        font-size: 12px;
        font-weight: 600;
    }

    /* ── Filter ──────────────────────────────────── */
    .cal-filter-form {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .cal-filter-select {
        height: 28px;
        padding: 0 9px;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        font-size: 12px;
        color: #374151;
        background: #fff;
        cursor: pointer;
    }

    .cal-filter-select:focus {
        outline: none;
        border-color: #f97316;
    }

    /* ── Legend ──────────────────────────────────── */
    .cal-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
    }

    .cal-leg-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 11px;
        font-weight: 500;
        color: #374151;
    }

    .cal-leg-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* ══════════════════════════════════════════════
   CALENDAR GRID — fills all remaining height
══════════════════════════════════════════════ */
    .gcal {
        flex: 1;
        /* fills remaining height inside .content */
        display: flex;
        flex-direction: column;
        min-height: 0;
        /* critical: allows shrink below natural height */
        background: #fff;
        border-top: none;
        overflow: hidden;
    }

    /* Weekday header */
    .gcal-head {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .gcal-head>div {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: #94a3b8;
        padding: 8px 0;
        border-right: 1px solid #e2e8f0;
    }

    .gcal-head>div:last-child {
        border-right: none;
    }

    /* Week rows — each gets equal share of remaining height */
    .gcal-week {
        flex: 1;
        /* ← key: equal distribution */
        min-height: 90px;
        /* floor so events aren't crushed */
        position: relative;
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border-bottom: 1px solid #e2e8f0;
    }

    .gcal-week:last-child {
        border-bottom: none;
    }

    /* Day cells */
    .gcal-cell {
        border-right: 1px solid #e2e8f0;
        padding: 6px 7px;
        cursor: pointer;
        background: #fff;
        transition: background .1s;
        overflow: hidden;
    }

    .gcal-cell:last-child {
        border-right: none;
    }

    .gcal-cell:hover {
        background: #f8fafc;
    }

    .gcal-cell.gcal-empty {
        background: #fafafa;
        cursor: default;
    }

    .gcal-cell.gcal-empty:hover {
        background: #fafafa;
    }

    .gcal-cell.gcal-wknd {
        background: #fcfcfd;
    }

    .gcal-cell.gcal-wknd:hover {
        background: #f4f4f8;
    }

    .gcal-dn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    .gcal-dn.gcal-today {
        background: #f97316;
        color: #fff;
    }

    /* Event overlay */
    .gcal-elay {
        position: absolute;
        inset: 0;
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        pointer-events: none;
    }

    .gcal-ev {
        position: absolute;
        height: 20px;
        left: 0;
        right: 0;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        padding: 0 6px;
        display: flex;
        align-items: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        pointer-events: auto;
        cursor: pointer;
        transition: filter .12s, transform .1s;
        z-index: 3;
    }

    .gcal-ev:hover {
        filter: brightness(1.1);
        transform: scaleY(1.06);
        z-index: 10;
    }

    /* Modal rows */
    .cal-modal-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 9px 12px;
        border-radius: 8px;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 500;
        border-left: 3px solid transparent;
    }

    .cal-modal-empty {
        font-size: 13px;
        color: #94a3b8;
        padding: 10px 0;
        text-align: center;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .cal-bar {
            flex-direction: column;
            align-items: flex-start;
        }

        .cal-bar-right {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>


<!-- ══════════════════════════════════════════
     SCRIPT
══════════════════════════════════════════ -->
<script>
    const _leaves = <?= json_encode($leaves) ?>;
    const _holidays = <?= json_encode($holidays) ?>;
    const _colors = <?= json_encode($colorMap) ?>;
    const _hColor = '<?= $holidayColor ?>';

    function _fmt(s) {
        return new Date(s + 'T00:00:00').toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short'
        });
    }

    function openDayModal(date) {
        let rows = '';

        _holidays.forEach(h => {
            if (h.date !== date) return;
            rows += `
            <div class="cal-modal-row" style="background:${_hColor}12; border-left-color:${_hColor};">
                <div>
                    <span style="font-weight:700;">${h.name}</span>
                    <span style="font-size:11px; opacity:.55; margin-left:8px; text-transform:capitalize;">${h.type} Holiday</span>
                </div>
            </div>`;
        });

        _leaves.forEach(l => {
            if (date < l.start_date || date > l.end_date) return;
            const c = _colors[l.leave_type] || '#3b82f6';
            const dept = l.department ?
                `<span style="font-size:11px;color:#94a3b8;margin-left:6px;">· ${l.department}</span>` : '';
            rows += `
            <div class="cal-modal-row" style="background:${c}14; border-left-color:${c};">
                <div>
                    <span style="font-weight:700;">${l.name}</span>${dept}
                    <span style="display:inline-block;margin-left:8px;font-size:11px;
                        background:${c}22;color:${c};padding:1px 7px;border-radius:999px;font-weight:600;">
                        ${l.leave_type}
                    </span>
                    <div style="font-size:11px;color:#64748b;margin-top:4px;">
                        ${_fmt(l.start_date)} – ${_fmt(l.end_date)}
                        &nbsp;·&nbsp; ${l.total_days} day${l.total_days > 1 ? 's' : ''}
                    </div>
                </div>
            </div>`;
        });

        if (!rows) rows = `<div class="cal-modal-empty">No events on this day.</div>`;

        const label = new Date(date + 'T00:00:00').toLocaleDateString('en-GB', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        document.getElementById('calModalRoot').innerHTML = `
        <div class="modal" onclick="if(event.target===this)closeCalModal()">
            <div class="modal-content" style="max-width:420px;">
                <p style="margin:0 0 14px;font-weight:700;font-size:15px;color:#0f172a;">${label}</p>
                ${rows}
                <div class="modal-actions">
                    <button class="btn-outline" onclick="closeCalModal()">Close</button>
                </div>
            </div>
        </div>`;
    }

    function closeCalModal() {
        document.getElementById('calModalRoot').innerHTML = '';
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
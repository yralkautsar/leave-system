<?php ob_start(); ?>

<style>
    /* ══════════════════════════════════════
   SUBMIT LEAVE — CALENDAR + PANEL
══════════════════════════════════════ */

    .lc-wrap {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        align-items: start;
    }

    /* ── Calendar card ── */
    .lc-cal-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
        overflow: hidden;
    }

    .lc-cal-hd {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .lc-cal-hd h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
    }

    .lc-nav-btns {
        display: flex;
        gap: 8px;
    }

    .lc-nav-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        text-decoration: none;
        transition: all .15s ease;
        font-size: 14px;
    }

    .lc-nav-btn:hover {
        background: #f8fafc;
        border-color: #f97316;
        color: #f97316;
    }

    /* Grid */
    .lc-cal-body {
        padding: 16px 16px 20px;
    }

    .lc-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 8px;
    }

    .lc-wd {
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #94a3b8;
        padding: 4px 0;
    }

    .lc-wd.weekend {
        color: #cbd5e1;
    }

    .lc-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }

    .lc-day {
        aspect-ratio: 1;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 6px 4px 4px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all .12s ease;
        position: relative;
        min-height: 52px;
    }

    .lc-day:hover {
        background: #fff7ed;
        border-color: #fed7aa;
    }

    .lc-day.empty {
        cursor: default;
        pointer-events: none;
        background: transparent;
    }

    .lc-day.today {
        border-color: #f97316;
    }

    .lc-day.weekend {
        background: #f8fafc;
    }

    .lc-day.weekend:hover {
        background: #fff7ed;
    }

    .lc-day.selected {
        background: #fff7ed;
        border-color: #f97316;
    }

    .lc-day.in-range {
        background: #fff7ed;
        border-color: transparent;
        border-radius: 0;
    }

    .lc-day.range-start {
        border-radius: 10px 0 0 10px;
        border-color: #f97316;
        background: #fff7ed;
    }

    .lc-day.range-end {
        border-radius: 0 10px 10px 0;
        border-color: #f97316;
        background: #fff7ed;
    }

    .lc-day.range-start.range-end {
        border-radius: 10px;
    }

    .lc-day.has-leave {
        cursor: default;
        opacity: .5;
        pointer-events: none;
    }

    .lc-day-num {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        line-height: 1;
        margin-bottom: 4px;
    }

    .lc-day.today .lc-day-num {
        color: #f97316;
    }

    .lc-day.weekend .lc-day-num {
        color: #94a3b8;
    }

    .lc-day.has-leave .lc-day-num {
        color: #94a3b8;
    }

    /* Dots */
    .lc-dots {
        display: flex;
        gap: 2px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .lc-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
    }

    .lc-dot.approved {
        background: #16a34a;
    }

    .lc-dot.pending {
        background: #f97316;
    }

    .lc-day.is-holiday {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .lc-day.is-holiday .lc-day-num {
        color: #dc2626;
        font-weight: 700;
    }

    .lc-day.is-holiday:hover {
        background: #fee2e2;
        border-color: #f87171;
    }

    /* Holiday label - bigger and bolder now */
    .lc-hol {
        position: absolute;
        bottom: 3px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 10px;
        font-weight: 600;
        background: #dc2626;
        color: white;
        border-radius: 4px;
        padding: 2px 5px;
        white-space: nowrap;
        max-width: 92%;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Legend */
    .lc-legend {
        display: flex;
        gap: 18px;
        padding: 12px 24px;
        border-top: 1px solid #f1f5f9;
        font-size: 11.5px;
        color: #64748b;
    }

    .lc-legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .lc-legend-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    /* ── Side panel ── */
    .lc-panel {
        position: sticky;
        top: 80px;
    }

    .lc-panel-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
        overflow: hidden;
    }

    .lc-panel-hd {
        padding: 18px 22px;
        border-bottom: 1px solid #f1f5f9;
    }

    .lc-panel-hd h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    .lc-panel-hd p {
        margin: 4px 0 0;
        font-size: 12.5px;
        color: #94a3b8;
    }

    .lc-panel-body {
        padding: 20px 22px;
    }

    /* Empty state */
    .lc-panel-empty {
        padding: 40px 24px;
        text-align: center;
    }

    .lc-panel-empty-icon {
        width: 52px;
        height: 52px;
        background: #f8fafc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        color: #cbd5e1;
    }

    .lc-panel-empty p {
        margin: 0;
        font-size: 13px;
        color: #94a3b8;
        line-height: 1.5;
    }

    /* Warning */
    .lc-warn {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-left: 3px solid #f59e0b;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12.5px;
        color: #92400e;
        margin-bottom: 16px;
        display: flex;
        gap: 8px;
        align-items: flex-start;
    }

    /* Form */
    .lc-fg {
        margin-bottom: 14px;
    }

    .lc-fg label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .lc-fg input,
    .lc-fg select {
        width: 100%;
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #374151;
        box-sizing: border-box;
        background: white;
        transition: border-color .15s ease;
    }

    .lc-fg input:focus,
    .lc-fg select:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }

    .lc-fg-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* Working days preview */
    .lc-days-preview {
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .lc-days-preview-label {
        font-size: 12.5px;
        color: #64748b;
    }

    .lc-days-preview-value {
        font-size: 20px;
        font-weight: 800;
        color: #f97316;
    }

    .lc-days-preview-value.zero {
        color: #dc2626;
    }

    /* Balance info */
    .lc-balance-info {
        background: #f0fdf4;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12.5px;
        color: #166534;
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .lc-balance-info.warn {
        background: #fef2f2;
        color: #991b1b;
    }

    .lc-balance-val {
        font-weight: 700;
        font-size: 15px;
    }

    /* Submit */
    .lc-submit {
        width: 100%;
        padding: 12px;
        background: #f97316;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
        margin-top: 4px;
    }

    .lc-submit:hover {
        background: #ea580c;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(249, 115, 22, 0.3);
    }

    .lc-submit:disabled {
        background: #e5e7eb;
        color: #94a3b8;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Alerts */
    .lc-alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 3px solid #16a34a;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 13px;
        color: #166534;
        margin-bottom: 20px;
    }

    .lc-alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-left: 3px solid #dc2626;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 13px;
        color: #991b1b;
        margin-bottom: 20px;
    }

    /* Mobile */
    @media (max-width: 820px) {
        .lc-wrap {
            grid-template-columns: 1fr;
        }

        .lc-panel {
            position: static;
        }
    }
</style>

<?php
$month      = $month      ?? date('Y-m');
$monthStart = $monthStart ?? ($month . '-01');
$holidays   = $holidays   ?? [];
$myLeaves   = $myLeaves   ?? [];
$workDays   = $workDays   ?? [1, 2, 3, 4, 5];
$leaveTypes = $leaveTypes ?? [];

$daysInMonth = (int)date('t', strtotime($monthStart));
$firstDow    = (int)date('N', strtotime($monthStart)); // 1=Mon..7=Sun
$today       = date('Y-m-d');

// Build holiday map: date => name
$holidayMap = [];
foreach ($holidays as $h) $holidayMap[$h['date']] = $h['name'];

// Build leave coverage map: date => [status, ...]
$leaveDates = [];
foreach ($myLeaves as $l) {
    $cur = new DateTime($l['start_date']);
    $end = new DateTime($l['end_date']);
    while ($cur <= $end) {
        $leaveDates[$cur->format('Y-m-d')][] = $l['status'];
        $cur->modify('+1 day');
    }
}

$prev = date('Y-m', strtotime('-1 month', strtotime($monthStart)));
$next = date('Y-m', strtotime('+1 month', strtotime($monthStart)));
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="lc-alert-success"><?= htmlspecialchars($_SESSION['success']);
                                    unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="lc-alert-error"><?= htmlspecialchars($_SESSION['error']);
                                unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="lc-wrap">

    <!-- ── LEFT: CALENDAR ── -->
    <div class="lc-cal-card">

        <!-- Header -->
        <div class="lc-cal-hd">
            <h2><?= date('F Y', strtotime($monthStart)) ?></h2>
            <div class="lc-nav-btns">
                <a href="?month=<?= $prev ?>" class="lc-nav-btn" title="Previous month">&#8249;</a>
                <a href="?month=<?= $next ?>" class="lc-nav-btn" title="Next month">&#8250;</a>
            </div>
        </div>

        <div class="lc-cal-body">

            <!-- Weekday headers -->
            <div class="lc-weekdays">
                <div class="lc-wd">Mon</div>
                <div class="lc-wd">Tue</div>
                <div class="lc-wd">Wed</div>
                <div class="lc-wd">Thu</div>
                <div class="lc-wd">Fri</div>
                <div class="lc-wd weekend">Sat</div>
                <div class="lc-wd weekend">Sun</div>
            </div>

            <!-- Day cells -->
            <div class="lc-days" id="calGrid">

                <?php
                // Empty cells before first day
                for ($i = 1; $i < $firstDow; $i++) {
                    echo '<div class="lc-day empty"></div>';
                }

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $date    = $month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $dow     = (int)date('N', strtotime($date)); // 1=Mon..7=Sun
                    $isToday = ($date === $today);
                    $isWknd  = !in_array($dow, $workDays);
                    $isHol   = isset($holidayMap[$date]);
                    $hasLeave = isset($leaveDates[$date]);

                    $classes = ['lc-day'];
                    if ($isToday)  $classes[] = 'today';
                    if ($isWknd)   $classes[] = 'weekend';
                    if ($isHol)    $classes[] = 'is-holiday';
                    if ($hasLeave) $classes[] = 'has-leave';

                    $dataAttrs  = "data-date=\"{$date}\"";
                    $dataAttrs .= " data-weekend=\"" . ($isWknd ? '1' : '0') . "\"";
                    $dataAttrs .= " data-holiday=\"" . ($isHol ? htmlspecialchars($holidayMap[$date]) : '') . "\"";

                    echo "<div class=\"" . implode(' ', $classes) . "\" {$dataAttrs} onclick=\"selectDay(this)\">";
                    echo "<div class=\"lc-day-num\">{$day}</div>";

                    // Leave dots
                    if ($hasLeave) {
                        echo '<div class="lc-dots">';
                        foreach (array_unique($leaveDates[$date]) as $status) {
                            if ($status === 'approved' || $status === 'pending') {
                                echo "<div class=\"lc-dot {$status}\"></div>";
                            }
                        }
                        echo '</div>';
                    }

                    // Holiday badge
                    if ($isHol) {
                        $holName = htmlspecialchars($holidayMap[$date]);
                        echo "<div class=\"lc-hol\" title=\"{$holName}\">{$holName}</div>";
                    }

                    echo '</div>';
                }
                ?>

            </div>
        </div>

        <!-- Legend -->
        <div class="lc-legend">
            <div class="lc-legend-item">
                <div class="lc-legend-dot" style="background:#16a34a;"></div> Approved leave
            </div>
            <div class="lc-legend-item">
                <div class="lc-legend-dot" style="background:#f97316;"></div> Pending leave
            </div>
            <div class="lc-legend-item">
                <div class="lc-legend-dot" style="background:#fef2f2; border:1px solid #fecaca;"></div> Holiday
            </div>
        </div>

    </div><!-- .lc-cal-card -->


    <!-- ── RIGHT: PANEL ── -->
    <div class="lc-panel">
        <div class="lc-panel-card" id="lcPanel">

            <!-- Empty state (default) -->
            <div class="lc-panel-empty" id="panelEmpty">
                <div class="lc-panel-empty-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <p>Click any date on the<br>calendar to start your<br>leave request.</p>
            </div>

            <!-- Form (hidden until date selected) -->
            <div id="panelForm" style="display:none;">

                <div class="lc-panel-hd">
                    <h3 id="panelDateLabel">—</h3>
                    <p id="panelDateSub">—</p>
                </div>

                <div class="lc-panel-body">

                    <!-- Warning (weekend / holiday) -->
                    <div id="panelWarn" class="lc-warn" style="display:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            <line x1="12" y1="9" x2="12" y2="13" />
                            <line x1="12" y1="17" x2="12.01" y2="17" />
                        </svg>
                        <span id="panelWarnText"></span>
                    </div>

                    <form method="POST" action="/leave-system/public/leave-store" id="leaveForm">

                        <!-- Leave type -->
                        <div class="lc-fg">
                            <label>Leave Type</label>
                            <select name="leave_type_id" id="fLeaveType" onchange="updatePreview()">
                                <option value="">Select type…</option>
                                <?php foreach ($leaveTypes as $lt): ?>
                                    <option value="<?= $lt['id'] ?>"
                                        data-balance="<?= (float)$lt['remaining_days'] ?>">
                                        <?= htmlspecialchars($lt['name']) ?>
                                        (<?= (float)$lt['remaining_days'] ?> days left)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date range -->
                        <div class="lc-fg-row">
                            <div class="lc-fg">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="fStartDate"
                                    onchange="onDateChange()">
                            </div>
                            <div class="lc-fg">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="fEndDate"
                                    onchange="onDateChange()">
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="lc-fg">
                            <label>Duration</label>
                            <select name="duration_type" id="fDuration" onchange="updatePreview()">
                                <option value="full">Full Day</option>
                                <option value="half">Half Day</option>
                            </select>
                        </div>

                        <!-- Working days preview -->
                        <div class="lc-days-preview">
                            <div class="lc-days-preview-label">Estimated working days</div>
                            <div class="lc-days-preview-value" id="previewDays">—</div>
                        </div>

                        <!-- Balance info -->
                        <div class="lc-balance-info" id="balanceInfo" style="display:none;">
                            <span id="balanceLabel">Balance remaining</span>
                            <span class="lc-balance-val" id="balanceVal">—</span>
                        </div>

                        <button type="submit" class="lc-submit" id="submitBtn">
                            Submit Leave Request
                        </button>

                    </form>
                </div>
            </div>

        </div><!-- .lc-panel-card -->
    </div><!-- .lc-panel -->

</div><!-- .lc-wrap -->


<script>
    /* ──────────────────────────────────────────
   DATA FROM PHP
────────────────────────────────────────── */
    const HOLIDAYS = <?= json_encode(array_column($holidays, 'name', 'date')) ?>;
    const WORK_DAYS = <?= json_encode($workDays) ?>; // ISO: 1=Mon..7=Sun
    const LEAVE_TYPES = <?= json_encode(array_values(array_map(fn($lt) => [
                            'id'            => $lt['id'],
                            'name'          => $lt['name'],
                            'remaining'     => (float)$lt['remaining_days'],
                        ], $leaveTypes))) ?>;

    /* ──────────────────────────────────────────
       DATE HELPERS
    ────────────────────────────────────────── */
    function isoToDate(str) {
        // str = 'YYYY-MM-DD', returns Date at noon local time
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d, 12, 0, 0);
    }

    function dateToIso(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function getDow(dateStr) {
        // Returns ISO weekday: 1=Mon..7=Sun
        const d = isoToDate(dateStr).getDay(); // 0=Sun
        return d === 0 ? 7 : d;
    }

    function isWorkingDay(dateStr) {
        return WORK_DAYS.includes(getDow(dateStr));
    }

    function isHoliday(dateStr) {
        return dateStr in HOLIDAYS;
    }

    function formatDate(dateStr) {
        const d = isoToDate(dateStr);
        return d.toLocaleDateString('en-GB', {
            weekday: 'short',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    /* ──────────────────────────────────────────
       WORKING DAYS CALCULATION
    ────────────────────────────────────────── */
    function calcWorkingDays(startStr, endStr, duration) {
        if (!startStr || !endStr) return null;
        if (endStr < startStr) return null;

        const start = isoToDate(startStr);
        const end = isoToDate(endStr);
        let count = 0;
        const cur = new Date(start);

        while (cur <= end) {
            const iso = dateToIso(cur);
            const dow = cur.getDay() === 0 ? 7 : cur.getDay();
            if (WORK_DAYS.includes(dow) && !isHoliday(iso)) count++;
            cur.setDate(cur.getDate() + 1);
        }

        if (duration === 'half') return Math.max(0.5, +(count / 2).toFixed(1));
        return count;
    }

    /* ──────────────────────────────────────────
       SELECT A DAY
    ────────────────────────────────────────── */
    function selectDay(el) {
        if (el.classList.contains('empty') || el.classList.contains('has-leave')) return;

        const date = el.dataset.date;
        const isWknd = el.dataset.weekend === '1';
        const holName = el.dataset.holiday;

        // Highlight selected cell
        document.querySelectorAll('.lc-day').forEach(d => d.classList.remove('selected'));
        el.classList.add('selected');

        // Fill form dates
        document.getElementById('fStartDate').value = date;
        document.getElementById('fEndDate').value = date;

        // Show panel form
        document.getElementById('panelEmpty').style.display = 'none';
        document.getElementById('panelForm').style.display = 'block';

        // Header
        document.getElementById('panelDateLabel').textContent = formatDate(date);
        document.getElementById('panelDateSub').textContent = 'Set your date range below';

        // Warning
        const warnEl = document.getElementById('panelWarn');
        const warnText = document.getElementById('panelWarnText');
        let warnings = [];
        if (isWknd) warnings.push('This day is outside your work schedule.');
        if (holName) warnings.push(`Public holiday: ${holName}.`);
        if (warnings.length) {
            warnText.textContent = warnings.join(' ') + ' This day will not count as a working day.';
            warnEl.style.display = 'flex';
        } else {
            warnEl.style.display = 'none';
        }

        updatePreview();
    }

    /* ──────────────────────────────────────────
       DATE RANGE CHANGED
    ────────────────────────────────────────── */
    function onDateChange() {
        const start = document.getElementById('fStartDate').value;
        const end = document.getElementById('fEndDate').value;

        // If end < start, reset end
        if (start && end && end < start) {
            document.getElementById('fEndDate').value = start;
        }

        updateRangeHighlight();
        updatePreview();
    }

    function updateRangeHighlight() {
        const start = document.getElementById('fStartDate').value;
        const end = document.getElementById('fEndDate').value;

        document.querySelectorAll('.lc-day').forEach(el => {
            el.classList.remove('in-range', 'range-start', 'range-end');
            if (!el.dataset.date) return;

            const d = el.dataset.date;
            if (start && end) {
                if (d === start && d === end) {
                    el.classList.add('range-start', 'range-end');
                } else if (d === start) {
                    el.classList.add('range-start');
                } else if (d === end) {
                    el.classList.add('range-end');
                } else if (d > start && d < end) {
                    el.classList.add('in-range');
                }
            } else if (d === start) {
                el.classList.add('range-start', 'range-end');
            }
        });
    }

    /* ──────────────────────────────────────────
       UPDATE PREVIEW
    ────────────────────────────────────────── */
    function updatePreview() {
        const start = document.getElementById('fStartDate').value;
        const end = document.getElementById('fEndDate').value;
        const duration = document.getElementById('fDuration').value;
        const typeEl = document.getElementById('fLeaveType');
        const typeOpt = typeEl.options[typeEl.selectedIndex];

        const days = calcWorkingDays(start, end, duration);

        const previewEl = document.getElementById('previewDays');
        if (days === null) {
            previewEl.textContent = '—';
            previewEl.className = 'lc-days-preview-value';
        } else {
            previewEl.textContent = days === 0 ? '0 ⚠' : days;
            previewEl.className = 'lc-days-preview-value' + (days === 0 ? ' zero' : '');
        }

        // Balance info
        const balInfo = document.getElementById('balanceInfo');
        const balLabel = document.getElementById('balanceLabel');
        const balVal = document.getElementById('balanceVal');

        if (typeOpt && typeOpt.value) {
            const remaining = parseFloat(typeOpt.dataset.balance);
            const enough = days === null || days === 0 || remaining >= days;
            balInfo.style.display = 'flex';
            balInfo.className = 'lc-balance-info' + (enough ? '' : ' warn');
            balLabel.textContent = enough ? 'Balance remaining' : '⚠ Insufficient balance';
            balVal.textContent = remaining + ' days';
        } else {
            balInfo.style.display = 'none';
        }

        // Submit button state
        const submitBtn = document.getElementById('submitBtn');
        const canSubmit = start && end && typeOpt && typeOpt.value && days !== null && days > 0;
        submitBtn.disabled = !canSubmit;
    }

    // Init
    updatePreview();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
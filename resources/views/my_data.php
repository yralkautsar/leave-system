<?php ob_start(); ?>

<style>
    /* ══════════════════════════════════════
   MY DATA
══════════════════════════════════════ */

    /* Tab nav */
    .md-tabs {
        display: flex;
        gap: 4px;
        background: white;
        border-radius: 12px;
        padding: 5px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        margin-bottom: 22px;
        width: fit-content;
    }

    .md-tab {
        padding: 9px 22px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border: none;
        background: transparent;
        transition: all .15s ease;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .md-tab:hover {
        color: #f97316;
        background: #fff7ed;
    }

    .md-tab.active {
        background: #f97316;
        color: white;
    }

    /* Section */
    .md-section {
        display: none;
    }

    .md-section.active {
        display: block;
    }

    /* ── PROFILE ── */
    .md-profile-grid {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 24px;
        align-items: start;
    }

    .md-avatar-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        padding: 28px 20px;
        text-align: center;
    }

    .md-avatar {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: white;
        margin: 0 auto 12px;
    }

    .md-avatar-name {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .md-avatar-role {
        display: inline-block;
        background: #fff7ed;
        color: #c2410c;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 99px;
    }

    .md-info-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .md-info-hd {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .md-info-body {
        padding: 8px 0;
    }

    .md-info-row {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 12px;
        padding: 12px 24px;
        border-bottom: 1px solid #f8fafc;
        align-items: center;
    }

    .md-info-row:last-child {
        border-bottom: none;
    }

    .md-info-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #94a3b8;
    }

    .md-info-value {
        font-size: 13.5px;
        color: #374151;
        font-weight: 500;
    }

    .md-info-empty {
        color: #cbd5e1;
        font-style: italic;
    }

    /* Balance summary in profile */
    .md-bal-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
        margin-top: 20px;
    }

    .md-bal-mini {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }

    .md-bal-mini-type {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 6px;
    }

    .md-bal-mini-val {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
    }

    .md-bal-mini-val span {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 400;
    }

    .md-bal-mini-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 3px;
    }

    .md-bal-comp {
        border: 1.5px solid #ddd6fe;
        background: linear-gradient(135deg, #faf5ff 0%, #ede9fe 100%);
    }

    .md-bal-grant {
        border: 1.5px solid #fde68a;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }

    .md-comp-batches {
        margin-top: 8px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .md-comp-batch {
        font-size: 11px;
        background: white;
        border: 1px solid #ddd6fe;
        color: #6d28d9;
        border-radius: 6px;
        padding: 2px 7px;
        display: inline-block;
    }

    /* ── HISTORY ── */
    .md-hist-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .md-hist-hd {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .md-hist-hd h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .md-hist-count {
        font-size: 12.5px;
        color: #94a3b8;
    }

    /* Filter */
    .md-filter {
        padding: 12px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .md-filter select {
        padding: 7px 10px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 12.5px;
        color: #374151;
        background: white;
        cursor: pointer;
    }

    .md-filter select:focus {
        outline: none;
        border-color: #f97316;
    }

    .md-hist-table {
        width: 100%;
        border-collapse: collapse;
    }

    .md-hist-table th {
        padding: 10px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #94a3b8;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .md-hist-table td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .md-hist-table tbody tr:last-child td {
        border-bottom: none;
    }

    .md-hist-table tbody tr {
        transition: background .12s ease;
    }

    .md-hist-table tbody tr:hover {
        background: #fafafa;
    }

    /* Status badges */
    .bd {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .bd-pending {
        background: #fff7ed;
        color: #c2410c;
    }

    .bd-approved {
        background: #f0fdf4;
        color: #15803d;
    }

    .bd-rejected {
        background: #fef2f2;
        color: #b91c1c;
    }

    .bd-cancelled {
        background: #f1f5f9;
        color: #64748b;
    }

    /* Cancel btn */
    /* md-cancel-btn removed — uses global .btn-outline-danger */

    .md-empty {
        padding: 48px 24px;
        text-align: center;
        color: #94a3b8;
        font-size: 13.5px;
    }

    /* Rejection reason */
    .md-rejection-note {
        font-size: 11.5px;
        color: #b91c1c;
        background: #fef2f2;
        border-radius: 5px;
        padding: 3px 8px;
        margin-top: 3px;
        display: inline-block;
    }

    /* Alerts */
    .md-alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 3px solid #16a34a;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 13px;
        color: #166534;
        margin-bottom: 20px;
    }

    @media (max-width: 700px) {
        .md-profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
$profile        = $profile        ?? [];
$history        = $history        ?? [];
$balanceSummary = $balanceSummary ?? [];

$activeTab = $_GET['tab'] ?? 'profile';
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="md-alert-success"><?= htmlspecialchars($_SESSION['success']);
                                    unset($_SESSION['success']); ?></div>
<?php endif; ?>

<!-- TABS -->
<div class="md-tabs">
    <button class="md-tab <?= $activeTab === 'profile' ? 'active' : '' ?>"
        onclick="switchTab('profile')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
        </svg>
        Profile
    </button>
    <button class="md-tab <?= $activeTab === 'history' ? 'active' : '' ?>"
        onclick="switchTab('history')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="1 4 1 10 7 10" />
            <path d="M3.51 15a9 9 0 1 0 .49-4.95" />
        </svg>
        Leave History
        <?php if (!empty($history)): ?>
            <span style="background:#f1f5f9;color:#64748b;padding:1px 7px;border-radius:99px;font-size:11px;margin-left:2px;">
                <?= count($history) ?>
            </span>
        <?php endif; ?>
    </button>
</div>


<!-- ═══════════════════ PROFILE TAB ═══════════════════ -->
<div id="tab-profile" class="md-section <?= $activeTab === 'profile' ? 'active' : '' ?>">

    <div class="md-profile-grid">

        <!-- Avatar card -->
        <div class="md-avatar-card">
            <div class="md-avatar">
                <?= strtoupper(substr($profile['name'] ?? '?', 0, 1)) ?>
            </div>
            <div class="md-avatar-name"><?= htmlspecialchars($profile['name'] ?? '—') ?></div>
            <div style="margin-top:6px;">
                <span class="md-avatar-role">
                    <?= $profile['role'] === 'admin_approver' ? 'Admin' : 'Employee' ?>
                </span>
            </div>
        </div>

        <!-- Info card -->
        <div class="md-info-card">
            <div class="md-info-hd">Account Information</div>
            <div class="md-info-body">
                <div class="md-info-row">
                    <div class="md-info-label">Full Name</div>
                    <div class="md-info-value"><?= htmlspecialchars($profile['name'] ?? '—') ?></div>
                </div>
                <div class="md-info-row">
                    <div class="md-info-label">Email</div>
                    <div class="md-info-value"><?= htmlspecialchars($profile['email'] ?? '—') ?></div>
                </div>
                <div class="md-info-row">
                    <div class="md-info-label">Department</div>
                    <div class="md-info-value <?= empty($profile['dept_name']) ? 'md-info-empty' : '' ?>">
                        <?= htmlspecialchars($profile['dept_name'] ?? 'Not assigned') ?>
                    </div>
                </div>
                <div class="md-info-row">
                    <div class="md-info-label">Job Title</div>
                    <div class="md-info-value <?= empty($profile['job_title_name']) ? 'md-info-empty' : '' ?>">
                        <?= htmlspecialchars($profile['job_title_name'] ?? 'Not assigned') ?>
                    </div>
                </div>
                <div class="md-info-row">
                    <div class="md-info-label">Join Date</div>
                    <div class="md-info-value">
                        <?= !empty($profile['join_date'])
                            ? date('d F Y', strtotime($profile['join_date']))
                            : '<span class="md-info-empty">—</span>' ?>
                    </div>
                </div>
                <?php if (!empty($profile['probation_end_date'])): ?>
                    <div class="md-info-row">
                        <div class="md-info-label">Probation Until</div>
                        <div class="md-info-value">
                            <?php
                            $probEnd = $profile['probation_end_date'];
                            $isProbation = $probEnd >= date('Y-m-d');
                            echo date('d F Y', strtotime($probEnd));
                            if ($isProbation) echo ' <span style="color:#f59e0b;font-size:11.5px;font-weight:600;">(active)</span>';
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- .md-profile-grid -->

    <!-- Balance summary -->
    <?php
    $showBalance = !empty($balanceSummary) || !empty($grantBalances) || (isset($compBalance) && $compBalance > 0) || !empty($compClaims);
    ?>
    <?php if ($showBalance): ?>
        <p style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin:24px 0 10px;">
            Current Leave Balance
        </p>
        <div class="md-bal-grid">

            <?php foreach ($balanceSummary as $b): ?>
                <div class="md-bal-mini">
                    <div class="md-bal-mini-type"><?= htmlspecialchars($b['leave_type']) ?></div>
                    <div class="md-bal-mini-val">
                        <?= (float)$b['remaining_days'] ?><span> days</span>
                    </div>
                    <div class="md-bal-mini-sub">
                        <?= (float)$b['used_days'] ?> used &middot; <?= (float)$b['total_days'] ?> total
                    </div>
                </div>
            <?php endforeach; ?>

            <?php foreach ($grantBalances ?? [] as $g): ?>
                <div class="md-bal-mini md-bal-grant">
                    <div class="md-bal-mini-type" style="color:#92400e;"><?= htmlspecialchars($g['leave_type']) ?></div>
                    <div class="md-bal-mini-val" style="color:#92400e;">
                        <?= (float)$g['remaining_days'] ?><span> days</span>
                    </div>
                    <div class="md-bal-mini-sub">
                        <?= (float)$g['used_days'] ?> used &middot; <?= (float)$g['total_days'] ?> total
                    </div>
                    <div style="font-size:11px;color:#b45309;margin-top:4px;">Event-based grant</div>
                </div>
            <?php endforeach; ?>

            <?php
            $compTotal   = isset($compBalance)  ? $compBalance  : 0;
            $compUsedVal = isset($compUsed)      ? $compUsed     : 0;
            if ($compTotal > 0 || $compUsedVal > 0):
            ?>
                <div class="md-bal-mini md-bal-comp">
                    <div class="md-bal-mini-type" style="color:#7c3aed;">Compensate Leave</div>
                    <div class="md-bal-mini-val" style="color:#7c3aed;">
                        <?= number_format($compTotal, 1) ?><span> days</span>
                    </div>
                    <div class="md-bal-mini-sub">
                        <?= number_format($compUsedVal, 1) ?> used &middot; floating balance
                    </div>
                    <?php if (!empty($compClaims)): ?>
                        <div class="md-comp-batches">
                            <?php foreach ($compClaims as $cc): ?>
                                <?php if ((float)$cc['days_remaining'] > 0): ?>
                                    <div class="md-comp-batch">
                                        <?= number_format((float)$cc['days_remaining'], 1) ?>d
                                        · expires <?= date('d M Y', strtotime($cc['expires_at'])) ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <a href="/leave-system/public/comp-claim"
                        style="font-size:11px;color:#7c3aed;margin-top:8px;display:block;font-weight:600;">
                        View claims →
                    </a>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>

</div><!-- #tab-profile -->


<!-- ═══════════════════ HISTORY TAB ═══════════════════ -->
<div id="tab-history" class="md-section <?= $activeTab === 'history' ? 'active' : '' ?>">

    <div class="md-hist-card">

        <div class="md-hist-hd">
            <h3>All Leave Requests</h3>
            <span class="md-hist-count"><?= count($history) ?> total</span>
        </div>

        <!-- Filters -->
        <div class="md-filter">
            <label style="font-size:12px;color:#94a3b8;font-weight:600;">Filter:</label>
            <select id="histStatusFilter" onchange="filterHistory()">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select id="histTypeFilter" onchange="filterHistory()">
                <option value="">All Types</option>
                <?php
                $leaveTypeNames = array_unique(array_column($history, 'leave_type'));
                sort($leaveTypeNames);
                $urlType = $_GET['type'] ?? '';
                foreach ($leaveTypeNames as $tn): ?>
                    <option value="<?= htmlspecialchars($tn) ?>"
                        <?= $tn === $urlType ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tn) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select id="histPeriodFilter" onchange="filterHistory()">
                <option value="">All Periods</option>
                <?php foreach (($periods ?? []) as $p): ?>
                    <option value="<?= htmlspecialchars($p['name']) ?>">
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (!empty($history)): ?>
            <div class="table-responsive">
                <table class="md-hist-table" id="histTable">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Days</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr data-status="<?= $h['status'] ?>"
                                data-type="<?= htmlspecialchars($h['leave_type']) ?>"
                                data-period="<?= htmlspecialchars($h['period_name'] ?? '') ?>">
                                <td><?= htmlspecialchars($h['leave_type']) ?></td>
                                <td><?= date('d M Y', strtotime($h['start_date'])) ?></td>
                                <td><?= date('d M Y', strtotime($h['end_date'])) ?></td>
                                <td>
                                    <?= (float)$h['total_days'] ?>
                                    <?php if (($h['duration_type'] ?? '') === 'half_am'): ?>
                                        <span class="dur-tag">AM</span>
                                    <?php elseif (($h['duration_type'] ?? '') === 'half_pm'): ?>
                                        <span class="dur-tag">PM</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:12.5px;color:#94a3b8;"><?= htmlspecialchars($h['period_name'] ?? '—') ?></td>
                                <td>
                                    <span class="bd bd-<?= $h['status'] ?>"><?= ucfirst($h['status']) ?></span>
                                    <?php if (!empty($h['rejection_reason'])): ?>
                                        <br>
                                        <span class="md-rejection-note">
                                            "<?= htmlspecialchars($h['rejection_reason']) ?>"
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($h['status'] === 'pending'): ?>
                                        <form method="POST" action="/leave-system/public/cancel"
                                            onsubmit="return confirm('Cancel this leave request?')">
                                            <input type="hidden" name="id" value="<?= $h['id'] ?>">
                                            <button type="submit" class="btn-outline-danger" style="padding:5px 12px;font-size:12px;">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div><!-- .table-responsive -->
        <?php else: ?>
            <div class="md-empty">
                No leave requests yet.
                <a href="/leave-system/public/leave" style="color:#f97316;">Submit your first leave →</a>
            </div>
        <?php endif; ?>

    </div><!-- .md-hist-card -->

</div><!-- #tab-history -->


<script>
    function switchTab(tab) {
        document.querySelectorAll('.md-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.md-section').forEach(s => s.classList.remove('active'));
        document.getElementById('tab-' + tab).classList.add('active');
        event.target.closest('.md-tab').classList.add('active');

        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        history.replaceState(null, '', url);
    }

    function filterHistory() {
        const status = document.getElementById('histStatusFilter').value;
        const type = document.getElementById('histTypeFilter').value;
        const period = document.getElementById('histPeriodFilter').value;
        const rows = document.querySelectorAll('#histTable tbody tr');
        let visible = 0;
        rows.forEach(row => {
            const matchStatus = !status || row.dataset.status === status;
            const matchType = !type || row.dataset.type === type;
            const matchPeriod = !period || row.dataset.period === period;
            const show = matchStatus && matchType && matchPeriod;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        const countEl = document.querySelector('.md-hist-count');
        if (countEl) countEl.textContent = visible + ' total';
    }

    // Auto-apply filters on page load (handles ?type= from dashboard link)
    document.addEventListener('DOMContentLoaded', () => {
        const hasFilter = document.getElementById('histTypeFilter')?.value ||
            document.getElementById('histStatusFilter')?.value;
        if (hasFilter) filterHistory();
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
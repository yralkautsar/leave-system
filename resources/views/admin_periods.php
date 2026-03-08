<?php ob_start(); ?>

<?php $periods = $periods ?? []; ?>

<style>
    /* ── Layout ─────────────────────────────────────────── */
    .per-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 16px;
    }

    /* ── Card ───────────────────────────────────────────── */
    .per-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .per-card-top {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* ── Table ──────────────────────────────────────────── */
    .per-table {
        width: 100%;
        border-collapse: collapse;
    }

    .per-table thead th {
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

    .per-table tbody td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }

    .per-table tbody tr:last-child td {
        border-bottom: none;
    }

    .per-table tbody tr {
        transition: background .12s;
    }

    .per-table tbody tr:hover {
        background: #fafafa;
    }

    /* ── Status badges ──────────────────────────────────── */
    .per-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .per-badge-active {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .per-badge-upcoming {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .per-badge-expired {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .per-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }

    .per-dot-active {
        background: #16a34a;
    }

    .per-dot-upcoming {
        background: #2563eb;
    }

    .per-dot-expired {
        background: #94a3b8;
    }

    /* ── Progress bar ───────────────────────────────────── */
    .per-progress-wrap {
        width: 100%;
        background: #f1f5f9;
        border-radius: 999px;
        height: 5px;
        margin-top: 5px;
    }

    .per-progress-bar {
        height: 5px;
        border-radius: 999px;
        background: #f97316;
    }

    /* ── Overlap warning ────────────────────────────────── */
    .per-overlap-tag {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
        margin-left: 6px;
        cursor: help;
    }

    /* ── Action buttons ─────────────────────────────────── */
    .per-btn-gen {
        padding: 6px 14px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid #f97316;
        background: #fff;
        color: #f97316;
        cursor: pointer;
        transition: .15s;
    }

    .per-btn-gen:hover {
        background: #f97316;
        color: #fff;
    }

    .per-btn-done {
        padding: 6px 14px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #94a3b8;
        cursor: default;
    }

    .per-btn-del {
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1px solid #fca5a5;
        background: #fff;
        color: #dc2626;
        cursor: pointer;
        transition: .15s;
    }

    .per-btn-del:hover {
        background: #fee2e2;
    }

    .per-btn-dis {
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 12.5px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #cbd5e1;
        cursor: not-allowed;
    }

    /* ── Footer links ───────────────────────────────────── */
    .per-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
    }

    /* ── Add modal ──────────────────────────────────────── */
    /* per-modal-* shell CSS removed — uses global openGM() + .gm-* from admin.css */
    .per-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .per-fg {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 14px;
    }

    .per-fg.full {
        grid-column: 1 / -1;
    }

    .per-fg label {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .per-fg input,
    .per-fg select {
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: .15s;
    }

    .per-fg input:focus,
    .per-fg select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .per-hint {
        font-size: 11.5px;
        color: #94a3b8;
        margin-top: 3px;
        line-height: 1.4;
    }

    .per-info-box {
        padding: 10px 14px;
        border-radius: 8px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        font-size: 12.5px;
        color: #1e40af;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .per-info-box b {
        display: block;
        margin-bottom: 2px;
    }

    /* per-btn-cancel/save removed — uses gm-btn-cancel/save inside openGM() */

    /* ── Alert ──────────────────────────────────────────── */
    .per-alert {
        padding: 11px 16px;
        border-radius: 10px;
        font-size: 13px;
        margin-bottom: 18px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .per-alert-ok {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .per-alert-err {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .per-alert-warn {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }
</style>

<!-- ── Page header ──────────────────────────────────── -->
<div class="per-header">
    <div>
        <h2 style="margin:0 0 4px;">Leave Periods</h2>
        <p class="subtext" style="margin:0;">15-month periods. Overlaps in Jan–Mar are expected and allowed.</p>
    </div>
    <button class="btn-primary" onclick="openPerModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:4px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        New Period
    </button>
</div>

<!-- ── Flash messages ───────────────────────────────── -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="per-alert per-alert-ok">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
            <polyline points="20 6 9 17 4 12" />
        </svg>
        <?= htmlspecialchars($_SESSION['success']);
        unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['warning'])): ?>
    <div class="per-alert per-alert-warn">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            <line x1="12" y1="9" x2="12" y2="13" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
        </svg>
        <?= htmlspecialchars($_SESSION['warning']);
        unset($_SESSION['warning']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="per-alert per-alert-err">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        <?= htmlspecialchars($_SESSION['error']);
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- ── Periods table ────────────────────────────────── -->
<div class="per-card">
    <div class="per-card-top">
        <span style="font-size:13px;color:#94a3b8;"><?= count($periods) ?> period<?= count($periods) !== 1 ? 's' : '' ?></span>
        <span style="font-size:12px;color:#94a3b8;">Status is auto-detected from dates — no manual toggle needed.</span>
    </div>

    <table class="per-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date Range</th>
                <th>Progress</th>
                <th>Balances</th>
                <th>Status</th>
                <th style="text-align:right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($periods)): ?>
                <tr>
                    <td colspan="6" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>
                            <div class="empty-state-title">No leave periods yet</div>
                            <div class="empty-state-desc">Create a leave period first, then use <strong>Generate Balance</strong> to allocate leave days to all active employees.</div>
                            <button class="btn-primary" onclick="openPerModal()">+ Create First Period</button>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($periods as $p):
                    $status = $p['status'];

                    // Progress calc
                    $pStart   = strtotime($p['start_date']);
                    $pEnd     = strtotime($p['end_date']);
                    $now      = time();
                    $totalLen = $pEnd - $pStart;
                    $elapsed  = min(max($now - $pStart, 0), $totalLen);
                    $pct      = $totalLen > 0 ? round($elapsed / $totalLen * 100) : 0;

                    // Overlap detection (other periods that share dates)
                    $overlapInfo = '';
                ?>
                    <tr>
                        <!-- Name -->
                        <td>
                            <span style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($p['name']) ?></span>
                        </td>

                        <!-- Date range -->
                        <td style="color:#64748b;font-size:13px;white-space:nowrap;">
                            <?= date('d M Y', strtotime($p['start_date'])) ?>
                            <span style="color:#cbd5e1;margin:0 4px;">→</span>
                            <?= date('d M Y', strtotime($p['end_date'])) ?>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                                <?php
                                $months = round((strtotime($p['end_date']) - strtotime($p['start_date'])) / (30.44 * 86400));
                                echo $months . ' months';
                                ?>
                            </div>
                        </td>

                        <!-- Progress -->
                        <td style="min-width:120px;">
                            <?php if ($status === 'active'): ?>
                                <div style="font-size:12px;color:#64748b;margin-bottom:4px;"><?= $pct ?>% elapsed</div>
                                <div class="per-progress-wrap">
                                    <div class="per-progress-bar" style="width:<?= $pct ?>%;"></div>
                                </div>
                            <?php elseif ($status === 'upcoming'): ?>
                                <span style="font-size:12px;color:#2563eb;">Starts <?= date('d M Y', strtotime($p['start_date'])) ?></span>
                            <?php else: ?>
                                <span style="font-size:12px;color:#94a3b8;">Ended <?= date('d M Y', strtotime($p['end_date'])) ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- Balances generated -->
                        <td>
                            <?php if ($p['total_generated'] > 0): ?>
                                <span style="font-weight:600;color:#16a34a;"><?= $p['total_generated'] ?></span>
                                <span style="font-size:12px;color:#94a3b8;">/ <?= $p['total_employees'] ?> emp</span>
                            <?php else: ?>
                                <span style="font-size:12.5px;color:#94a3b8;">Not generated</span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td>
                            <?php if ($status === 'active'): ?>
                                <span class="per-badge per-badge-active"><span class="per-dot per-dot-active"></span>Active</span>
                            <?php elseif ($status === 'upcoming'): ?>
                                <span class="per-badge per-badge-upcoming"><span class="per-dot per-dot-upcoming"></span>Upcoming</span>
                            <?php else: ?>
                                <span class="per-badge per-badge-expired"><span class="per-dot per-dot-expired"></span>Expired</span>
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td>
                            <div style="display:flex;gap:8px;justify-content:flex-end;align-items:center;">

                                <!-- Generate / Already generated -->
                                <?php if ($p['total_generated'] > 0): ?>
                                    <button class="per-btn-done" disabled>Generated</button>
                                <?php else: ?>
                                    <form method="POST" action="/leave-system/public/admin/periods/generate" style="margin:0;">
                                        <input type="hidden" name="period_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="per-btn-gen">Generate Balances</button>
                                    </form>
                                <?php endif; ?>

                                <!-- Delete (only if no balances/requests) -->
                                <?php if ($p['total_generated'] > 0): ?>
                                    <button class="per-btn-dis" title="Cannot delete — balances exist">Delete</button>
                                <?php else: ?>
                                    <form method="POST" action="/leave-system/public/admin/periods/delete" style="margin:0;"
                                        onsubmit="return confirm('Delete period &quot;<?= htmlspecialchars(addslashes($p['name'])) ?>&quot;?')">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button class="per-btn-del">Delete</button>
                                    </form>
                                <?php endif; ?>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="per-footer">
        <a href="/leave-system/public/admin/balances" class="btn-outline" style="font-size:13px;">View All Balances →</a>
        <a href="/leave-system/public/admin/leave-types" class="btn-outline" style="font-size:13px;">Manage Leave Types →</a>
    </div>
</div>

<!-- ── Add period modal ──────────────────────────────── -->
<script>
    function openPerModal() {
        openGM({
            title: 'New Leave Period',
            html: `
        <form method="POST" action="/leave-system/public/admin/periods/store" style="display:contents;">
        <div class="gm-body">
            <div class="per-info-box">
                <b>📅 15-Month Period SOP</b>
                Periods at ICS run for 15 months (e.g. Jan 2025 → Mar 2026).
                Overlap during Jan–Mar is expected — employees can choose which period's balance to use.
            </div>
            <div class="per-grid">
                <div class="per-fg full">
                    <label>Period Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Leave Period 2025">
                </div>
                <div class="per-fg">
                    <label>Start Date <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="start_date" required id="perStart">
                </div>
                <div class="per-fg">
                    <label>End Date <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="end_date" required id="perEnd">
                    <span class="per-hint" id="perDuration"></span>
                </div>
            </div>
        </div>
        <div class="gm-ft">
            <button type="button" class="gm-btn-cancel" onclick="closeGM()">Cancel</button>
            <button type="submit" class="gm-btn-save">Create Period</button>
        </div>
        </form>`,
            onOpen: () => {
                const s = document.getElementById('perStart');
                const e = document.getElementById('perEnd');
                const dur = document.getElementById('perDuration');

                function updateDuration() {
                    if (!s.value || !e.value) {
                        dur.textContent = '';
                        return;
                    }
                    const ms = new Date(e.value) - new Date(s.value);
                    if (ms <= 0) {
                        dur.textContent = '⚠ End must be after start';
                        dur.style.color = '#dc2626';
                        return;
                    }
                    const months = Math.round(ms / (30.44 * 86400000));
                    dur.textContent = months + ' month' + (months !== 1 ? 's' : '');
                    dur.style.color = months === 15 ? '#16a34a' : '#f97316';
                }

                s.addEventListener('change', function() {
                    if (s.value && !e.value) {
                        const d = new Date(s.value);
                        d.setMonth(d.getMonth() + 15);
                        d.setDate(d.getDate() - 1);
                        e.value = d.toISOString().slice(0, 10);
                    }
                    updateDuration();
                });
                e.addEventListener('change', updateDuration);
                document.querySelector('.gm-body input[name="name"]')?.focus();
            }
        });
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
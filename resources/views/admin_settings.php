<?php ob_start(); ?>

<?php
$settings = $settings ?? [];
$s = fn($key, $default = '') => htmlspecialchars($settings[$key] ?? $default);
?>

<style>
    .st-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .st-full {
        grid-column: 1/-1;
    }

    .st-fg {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .st-fg label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .st-fg input,
    .st-fg select {
        padding: 10px 14px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        color: #0f172a;
        background: #fff;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: .15s ease;
    }

    .st-fg input:focus,
    .st-fg select:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .st-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .st-section {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #94a3b8;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        margin: 28px 0 18px;
    }

    .st-section:first-of-type {
        margin-top: 0;
    }

    .st-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 28px;
    }

    .st-test-wrap {
        margin-top: 28px;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .st-test-wrap h4 {
        margin: 0 0 12px;
        font-size: 14px;
        color: #0f172a;
    }

    .st-test-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .st-test-row input {
        padding: 9px 14px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13.5px;
        color: #0f172a;
        flex: 1;
        outline: none;
        transition: .15s;
    }

    .st-test-row input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
    }

    .st-test-btn {
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        border: none;
        background: #0f172a;
        color: #fff;
        cursor: pointer;
        transition: .15s;
        white-space: nowrap;
    }

    .st-test-btn:hover {
        background: #1e293b;
    }

    .st-test-result {
        margin-top: 12px;
        font-size: 13px;
        display: none;
        padding: 10px 14px;
        border-radius: 8px;
    }

    .st-test-ok {
        background: #dcfce7;
        color: #166534;
    }

    .st-test-err {
        background: #fee2e2;
        color: #991b1b;
    }

    .st-info-box {
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 24px;
        font-size: 13px;
        color: #c2410c;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .st-info-box svg {
        flex-shrink: 0;
        margin-top: 1px;
    }
</style>

<div style="max-width:700px;">

    <div style="margin-bottom:24px;">
        <h2 style="margin:0 0 4px;">Settings</h2>
        <p class="subtext" style="margin:0;">Configure SMTP email and system preferences</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-success" style="margin-bottom:20px;"><?= htmlspecialchars($_SESSION['success']);
                                                                unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-error" style="margin-bottom:20px;"><?= htmlspecialchars($_SESSION['error']);
                                                                unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="st-info-box">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        <div>
            SMTP via <strong>PHPMailer</strong>. Install with:
            <code style="background:#fef3c7;padding:1px 5px;border-radius:4px;font-size:12px;">composer require phpmailer/phpmailer</code>
            in project root. If not installed, system falls back to PHP <code style="background:#fef3c7;padding:1px 5px;border-radius:4px;font-size:12px;">mail()</code>.
        </div>
    </div>

    <div class="card">

        <form method="POST" action="/leave-system/public/admin/settings/save">

            <!-- ── Email Identity ── -->
            <div class="st-section">Email Identity</div>
            <div class="st-grid">
                <div class="st-fg">
                    <label>From Name</label>
                    <input type="text" name="mail_from_name"
                        value="<?= $s('mail_from_name', 'ICS Leave System') ?>"
                        placeholder="ICS Leave System">
                </div>
                <div class="st-fg">
                    <label>From Email</label>
                    <input type="email" name="mail_from_email"
                        value="<?= $s('mail_from_email', 'noreply@icstravelgroup.com') ?>"
                        placeholder="noreply@icstravelgroup.com">
                </div>
                <div class="st-fg st-full">
                    <label>HR Email <span style="color:#f97316;">*</span></label>
                    <input type="email" name="hr_email"
                        value="<?= $s('hr_email', 'hr-id@icstravelgroup.com') ?>"
                        placeholder="hr-id@icstravelgroup.com">
                    <span class="st-hint">Leave submissions are sent to this address. Can be a shared inbox.</span>
                </div>
            </div>

            <!-- ── SMTP ── -->
            <div class="st-section">SMTP Server</div>
            <div class="st-grid">
                <div class="st-fg">
                    <label>SMTP Host</label>
                    <input type="text" name="smtp_host"
                        value="<?= $s('smtp_host') ?>"
                        placeholder="smtp.gmail.com">
                </div>
                <div class="st-fg">
                    <label>SMTP Port</label>
                    <input type="number" name="smtp_port"
                        value="<?= $s('smtp_port', '587') ?>"
                        placeholder="587">
                </div>
                <div class="st-fg">
                    <label>Username</label>
                    <input type="text" name="smtp_user"
                        value="<?= $s('smtp_user') ?>"
                        placeholder="your@email.com"
                        autocomplete="off">
                </div>
                <div class="st-fg">
                    <label>Password</label>
                    <input type="password" name="smtp_pass"
                        value="<?= $s('smtp_pass') ?>"
                        placeholder="<?= empty($settings['smtp_pass']) ? 'Enter password' : '••••••••' ?>"
                        autocomplete="new-password">
                    <span class="st-hint">Leave blank to keep current password.</span>
                </div>
                <div class="st-fg">
                    <label>Encryption</label>
                    <select name="smtp_encryption">
                        <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
                        <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                        <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                    </select>
                </div>
            </div>

            <div class="st-actions">
                <button type="submit" class="btn-primary">Save Settings</button>
                <span style="font-size:13px;color:#94a3b8;">Changes apply immediately.</span>
            </div>

        </form>

        <!-- ── Test Email ── -->
        <div class="st-test-wrap">
            <h4>Test Email Connection</h4>
            <p style="margin:0 0 12px;font-size:13px;color:#64748b;">
                Send a test email to verify your SMTP settings are working.
            </p>
            <div class="st-test-row">
                <input type="email" id="testEmail"
                    placeholder="recipient@example.com"
                    value="<?= $s('hr_email') ?>">
                <button class="st-test-btn" onclick="sendTest()">Send Test</button>
            </div>
            <div id="testResult" class="st-test-result"></div>
        </div>

    </div>

</div>

<script>
    async function sendTest() {
        const email = document.getElementById('testEmail').value.trim();
        const result = document.getElementById('testResult');

        if (!email) {
            result.className = 'st-test-result st-test-err';
            result.style.display = 'block';
            result.textContent = 'Please enter a recipient email address.';
            return;
        }

        result.className = 'st-test-result';
        result.style.display = 'block';
        result.textContent = 'Sending…';

        try {
            const fd = new FormData();
            fd.append('email', email);

            const res = await fetch('/leave-system/public/admin/settings/test-email', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();

            result.className = 'st-test-result ' + (data.ok ? 'st-test-ok' : 'st-test-err');
            result.textContent = data.message;
        } catch (e) {
            result.className = 'st-test-result st-test-err';
            result.textContent = 'Request failed: ' + e.message;
        }
    }
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>
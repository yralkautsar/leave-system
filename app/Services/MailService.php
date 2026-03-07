<?php

require_once __DIR__ . '/Database.php';

/**
 * MailService
 *
 * Reads SMTP config from `settings` table.
 * Uses PHPMailer if available (vendor/autoload.php),
 * falls back to PHP mail() automatically.
 *
 * Install PHPMailer: composer require phpmailer/phpmailer
 */
class MailService
{
    private static ?array $cfg = null;

    /* ── Config loader (cached per request) ── */
    private static function cfg(): array
    {
        if (self::$cfg !== null) return self::$cfg;
        try {
            $db = Database::connect();
            self::$cfg = $db->query("SELECT `key`, `value` FROM settings WHERE `group` = 'email'")
                ->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (\Throwable $e) {
            self::$cfg = [];
        }
        return self::$cfg;
    }

    private static function get(string $key, string $default = ''): string
    {
        return self::cfg()[$key] ?? $default;
    }

    /* ════════════════════════════════════════
       PUBLIC API
    ════════════════════════════════════════ */

    /** Leave submitted → HR email + all admins + HoD */
    public static function notifyLeaveSubmitted(array $request, array $employee): void
    {
        $db      = Database::connect();
        $subject = "[Leave Request] {$employee['name']} — {$request['leave_type']}";
        $body    = self::template(
            "New Leave Request Submitted",
            "<p>A new leave request requires your attention.</p>"
                . self::requestTable($request, $employee)
                . "<p style='margin-top:20px;'><a href='" . self::baseUrl()
                . "/admin/requests?status=pending' style='" . self::btnStyle() . "'>Review Request</a></p>"
        );

        $sent = [];

        // Dedicated HR email
        $hrEmail = self::get('hr_email');
        if ($hrEmail) {
            self::send($hrEmail, 'HR Team', $subject, $body);
            $sent[] = $hrEmail;
        }

        // All admin_approver users
        $admins = $db->query("SELECT name, email FROM users WHERE role='admin_approver' AND is_active=1")
            ->fetchAll(PDO::FETCH_ASSOC);
        foreach ($admins as $a) {
            if (!in_array($a['email'], $sent)) {
                self::send($a['email'], $a['name'], $subject, $body);
                $sent[] = $a['email'];
            }
        }

        // HoD
        if (!empty($employee['hod_id'])) {
            $stmt = $db->prepare("SELECT name, email FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $employee['hod_id']]);
            $hod = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($hod && !in_array($hod['email'], $sent)) {
                self::send($hod['email'], $hod['name'], $subject, $body);
            }
        }
    }

    /** Leave approved → employee + HoD + GM */
    public static function notifyLeaveApproved(array $request, array $employee, string $adminName): void
    {
        $db      = Database::connect();
        $subject = "[Leave Approved] {$employee['name']} — {$request['leave_type']}";
        $body    = self::template(
            "Leave Request Approved ✓",
            "<p>Your leave request has been <strong style='color:#16a34a;'>approved</strong>.</p>"
                . self::requestTable($request, $employee)
                . "<p style='color:#64748b;font-size:13px;margin-top:16px;'>Approved by: {$adminName}</p>"
        );

        self::send($employee['email'], $employee['name'], $subject, $body);

        foreach (['hod_id' => 'Head of Department', 'gm_id' => 'General Manager'] as $field => $role) {
            if (empty($employee[$field])) continue;
            $stmt = $db->prepare("SELECT name, email FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $employee[$field]]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($p) self::send(
                $p['email'],
                $p['name'],
                $subject,
                self::withNote($body, "FYI ({$role}): {$employee['name']}'s leave has been approved.")
            );
        }
    }

    /** Leave rejected → employee + HoD */
    public static function notifyLeaveRejected(array $request, array $employee, string $adminName): void
    {
        $db      = Database::connect();
        $subject = "[Leave Rejected] {$employee['name']} — {$request['leave_type']}";
        $reasonBlock = !empty($request['rejection_reason'])
            ? "<div style='margin-top:16px;padding:14px 18px;background:#fef2f2;border-radius:10px;border:1px solid #fca5a5;'>"
            . "<p style='margin:0 0 4px;font-size:12px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.05em;'>Reason</p>"
            . "<p style='margin:0;font-size:14px;color:#374151;'>" . htmlspecialchars($request['rejection_reason']) . "</p>"
            . "</div>"
            : '';

        $body    = self::template(
            "Leave Request Rejected",
            "<p>Your leave request has been <strong style='color:#dc2626;'>rejected</strong>.</p>"
                . self::requestTable($request, $employee)
                . $reasonBlock
                . "<p style='color:#64748b;font-size:13px;margin-top:16px;'>Rejected by: {$adminName}</p>"
                . "<p style='color:#64748b;font-size:13px;'>Please contact HR if you have any questions.</p>"
        );

        self::send($employee['email'], $employee['name'], $subject, $body);

        if (!empty($employee['hod_id'])) {
            $stmt = $db->prepare("SELECT name, email FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $employee['hod_id']]);
            $hod = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($hod) self::send(
                $hod['email'],
                $hod['name'],
                $subject,
                self::withNote($body, "FYI (Head of Department): {$employee['name']}'s leave was rejected.")
            );
        }
    }

    /** Test connection — called from Settings page */
    public static function sendTest(string $toEmail): array
    {
        try {
            self::send(
                $toEmail,
                'Test Recipient',
                '[ICS Leave] SMTP Test Email',
                self::template(
                    'SMTP Test Successful ✓',
                    "<p>This is a test email from ICS Leave Management System.</p>
                     <p style='color:#64748b;font-size:13px;'>Your SMTP configuration is working correctly.</p>"
                )
            );
            return ['ok' => true, 'message' => "Test email sent to {$toEmail}."];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /* ════════════════════════════════════════
       CORE SEND
    ════════════════════════════════════════ */

    public static function send(string $toEmail, string $toName, string $subject, string $html): void
    {
        $phpmailerPath = __DIR__ . '/PHPMailer/PHPMailer.php';

        if (file_exists($phpmailerPath)) {
            self::sendSmtp($toEmail, $toName, $subject, $html);
        } else {
            self::sendNative($toEmail, $toName, $subject, $html);
        }
    }

    private static function sendSmtp(string $toEmail, string $toName, string $subject, string $html): void
    {
        require_once __DIR__ . '/PHPMailer/PHPMailer.php';
        require_once __DIR__ . '/PHPMailer/SMTP.php';
        require_once __DIR__ . '/PHPMailer/Exception.php';

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = self::get('smtp_host', 'smtp.gmail.com');
        $mail->SMTPAuth   = true;
        $mail->Username   = self::get('smtp_user');
        $mail->Password   = self::get('smtp_pass');
        $mail->Port       = (int) self::get('smtp_port', '587');
        $mail->SMTPSecure = strtolower(self::get('smtp_encryption', 'tls')) === 'ssl'
            ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom(
            self::get('mail_from_email', 'noreply@icstravelgroup.com'),
            self::get('mail_from_name',  'ICS Leave System')
        );
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->send();
    }

    private static function sendNative(string $toEmail, string $toName, string $subject, string $html): void
    {
        $from     = self::get('mail_from_email', 'noreply@icstravelgroup.com');
        $fromName = self::get('mail_from_name',  'ICS Leave System');
        $headers  = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n"
            . "From: {$fromName} <{$from}>\r\nReply-To: {$from}\r\n";
        @mail("{$toName} <{$toEmail}>", $subject, $html, $headers);
    }

    /* ════════════════════════════════════════
       TEMPLATE HELPERS
    ════════════════════════════════════════ */

    private static function template(string $title, string $body): string
    {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>{$title}</title></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
<tr><td align="center">
<table width="580" cellpadding="0" cellspacing="0" style="background:white;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.07);">
  <tr><td style="background:#0f172a;padding:28px 36px;">
    <span style="color:white;font-size:22px;font-weight:800;">ICS</span>
    <span style="color:#f97316;font-size:22px;font-weight:800;"> ·</span>
    <span style="color:#94a3b8;font-size:13px;margin-left:8px;">Leave Management</span>
  </td></tr>
  <tr><td style="padding:32px 36px;">
    <h2 style="margin:0 0 20px;font-size:20px;font-weight:700;color:#0f172a;">{$title}</h2>
    {$body}
  </td></tr>
  <tr><td style="background:#f8fafc;padding:20px 36px;border-top:1px solid #e5e7eb;">
    <p style="margin:0;font-size:12px;color:#94a3b8;">Automated message from ICS Leave Management. Do not reply. © {$year} ICS Travel Group</p>
  </td></tr>
</table>
</td></tr></table></body></html>
HTML;
    }

    private static function requestTable(array $r, array $emp): string
    {
        $start = date('d M Y', strtotime($r['start_date']));
        $end   = date('d M Y', strtotime($r['end_date']));
        $dates = $r['start_date'] === $r['end_date'] ? $start : "{$start} – {$end}";
        $name  = htmlspecialchars($emp['name']      ?? '');
        $dept  = htmlspecialchars($emp['department'] ?? '—');
        $type  = htmlspecialchars($r['leave_type']  ?? '');
        $days  = (int)($r['total_days'] ?? 0);

        return <<<HTML
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb;margin-top:16px;font-size:14px;">
  <tr><td style="padding:11px 16px;color:#64748b;border-bottom:1px solid #e5e7eb;width:38%;">Employee</td>
      <td style="padding:11px 16px;font-weight:600;color:#0f172a;border-bottom:1px solid #e5e7eb;">{$name}</td></tr>
  <tr><td style="padding:11px 16px;color:#64748b;border-bottom:1px solid #e5e7eb;">Department</td>
      <td style="padding:11px 16px;color:#0f172a;border-bottom:1px solid #e5e7eb;">{$dept}</td></tr>
  <tr><td style="padding:11px 16px;color:#64748b;border-bottom:1px solid #e5e7eb;">Leave Type</td>
      <td style="padding:11px 16px;color:#0f172a;border-bottom:1px solid #e5e7eb;">{$type}</td></tr>
  <tr><td style="padding:11px 16px;color:#64748b;border-bottom:1px solid #e5e7eb;">Date</td>
      <td style="padding:11px 16px;color:#0f172a;border-bottom:1px solid #e5e7eb;">{$dates}</td></tr>
  <tr><td style="padding:11px 16px;color:#64748b;">Duration</td>
      <td style="padding:11px 16px;font-weight:600;color:#f97316;">{$days} day(s)</td></tr>
</table>
HTML;
    }

    private static function withNote(string $body, string $note): string
    {
        $note   = htmlspecialchars($note);
        $insert = "<p style='background:#fff7ed;border-left:3px solid #f97316;padding:10px 14px;"
            . "font-size:13px;color:#c2410c;border-radius:4px;margin-bottom:16px;'>{$note}</p>";
        return preg_replace('/(<h2[^>]*>.*?<\/h2>)/s', '$1' . $insert, $body, 1);
    }

    private static function btnStyle(): string
    {
        return "display:inline-block;background:#f97316;color:white;padding:12px 24px;"
            . "border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;";
    }

    private static function baseUrl(): string
    {
        $s = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return "{$s}://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/leave-system/public";
    }
}

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — ICS Leave Management</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f1f5f9;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-logo img {
            height: 52px;
            width: auto;
        }

        .login-card {
            background: #fff;
            border-radius: 18px;
            padding: 36px 32px 28px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.09);
        }

        .login-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px;
            text-align: center;
        }

        .login-sub {
            font-size: 13px;
            color: #94a3b8;
            text-align: center;
            margin: 0 0 28px;
        }

        .l-alert {
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .l-alert-err {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .l-alert-ok {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .l-fg {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 16px;
        }

        .l-fg label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .l-fg input {
            padding: 10px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            transition: .15s ease;
            width: 100%;
        }

        .l-fg input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
        }

        .l-forgot-row {
            display: flex;
            justify-content: flex-end;
            margin-top: -10px;
            margin-bottom: 22px;
        }

        .l-forgot-link {
            font-size: 12.5px;
            color: #f97316;
            text-decoration: none;
            font-weight: 500;
        }

        .l-forgot-link:hover {
            text-decoration: underline;
        }

        .l-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #f97316;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: .15s ease;
            margin-top: 4px;
        }

        .l-btn:hover {
            background: #ea580c;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #cbd5e1;
        }
    </style>
</head>

<body>
    <div class="login-wrap">

        <div class="login-logo">
            <img src="/leave-system/public/assets/black.png" alt="ICS">
        </div>

        <div class="login-card">

            <p class="login-title">Welcome back</p>
            <p class="login-sub">Sign in to ICS Leave Management</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="l-alert l-alert-err">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <?= htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="l-alert l-alert-ok">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    <?= htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/leave-system/public/login">
                <div class="l-fg">
                    <label>Email</label>
                    <input type="email" name="email" required autofocus
                        placeholder="your@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="l-fg">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <div class="l-forgot-row">
                    <a href="/leave-system/public/forgot-password" class="l-forgot-link">Forgot password?</a>
                </div>
                <button type="submit" class="l-btn">Sign In</button>
            </form>

        </div>

        <p class="login-footer">© <?= date('Y') ?> ICS Travel Group</p>
    </div>
</body>

</html>
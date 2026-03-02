<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
</head>
<body style="margin:0;padding:0;background:#0b1220;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#e6edf3;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#0b1220;padding:32px 12px;">
    <tr>
        <td align="center">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);border-radius:22px;overflow:hidden;">
                <tr>
                    <td style="padding:26px 26px 10px 26px;text-align:center;">
                        <img src="cid:logo" alt="{{ $appName }} logo" width="90" height="90" style="display:block;margin:0 auto 10px auto;object-fit:contain;">
                        <div style="font-size:22px;font-weight:900;letter-spacing:.2px;">
                            {{ $appName }}
                        </div>
                        <div style="font-size:13px;color:#9fb0c0;margin-top:6px;line-height:1.5;">
                            You requested a password reset for your account.
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:14px 26px 26px 26px;">
                        <div style="background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.10);border-radius:16px;padding:14px 14px;color:#9fb0c0;font-size:13px;line-height:1.6;">
                            <strong style="color:#e6edf3;">Hi{{ isset($user->name) ? ' '.$user->name : '' }}!</strong><br>
                            Click the button below to reset your password. This link expires in <strong style="color:#e6edf3;">60 minutes</strong>.
                        </div>

                        <div style="text-align:center;margin:22px 0;">
                            <a href="{{ $resetUrl }}"
                               style="display:inline-block;background:#e6edf3;color:#0b1220;text-decoration:none;font-weight:900;padding:12px 18px;border-radius:14px;">
                                Reset Password
                            </a>
                        </div>

                        <div style="color:#9fb0c0;font-size:12px;line-height:1.6;">
                            If the button doesn’t work, copy and paste this link:
                        </div>
                        <div style="word-break:break-all;margin-top:8px;font-size:12px;line-height:1.6;">
                            <a href="{{ $resetUrl }}" style="color:#9fb0c0;text-decoration:underline;">
                                {{ $resetUrl }}
                            </a>
                        </div>

                        <div style="margin-top:18px;color:#9fb0c0;font-size:12px;line-height:1.6;">
                            If you didn’t request a password reset, you can safely ignore this email.
                        </div>

                        <div style="margin-top:18px;color:#9fb0c0;font-size:12px;">
                            Regards,<br>
                            <span style="color:#e6edf3;font-weight:800;">{{ $appName }}</span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:14px 26px;background:rgba(0,0,0,0.18);border-top:1px solid rgba(255,255,255,0.08);color:#9fb0c0;font-size:11px;text-align:center;line-height:1.5;">
                        © {{ date('Y') }} {{ $appName }} — Password reset email
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>
<?php
/**
 * Modabber Application Email (HTML Template)
 *
 * Variables passed:
 * @var array $project
 * @var string $full_name
 * @var string $phone
 * @var string $message
 * @var string $resume_filename
 * @var string $sent_at
 * @var string $ip_address
 * @var string $project_url
 */
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>درخواست همکاری جدید</title>
</head>
<body style="margin:0;padding:24px;background-color:#EEF9EE;font-family:Tahoma,Arial,sans-serif;direction:rtl;color:#0B1711;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background-color:#FAFEFB;border-radius:24px;border:1px solid #CBE5D4;overflow:hidden;box-shadow:0 10px 30px rgba(11,23,17,0.08);">
        <!-- Header Banner -->
        <tr>
            <td style="background-color:#B5EFCC;padding:28px 32px;text-align:center;border-bottom:1px solid #CBE5D4;">
                <h1 style="margin:0;font-size:22px;font-weight:bold;color:#0B1711;">درخواست همکاری جدید</h1>
                <p style="margin:8px 0 0;font-size:14px;color:#4B5D53;">وب‌سایت رسمی مدبرنت</p>
            </td>
        </tr>
        <!-- Content Body -->
        <tr>
            <td style="padding:32px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:15px;line-height:1.8;">
                    <tr>
                        <td style="padding:10px 0;color:#4B5D53;width:140px;font-weight:bold;vertical-align:top;">پروژه:</td>
                        <td style="padding:10px 0;color:#0B1711;vertical-align:top;">
                            <a href="<?= e($project_url) ?>" style="color:#1F8B59;text-decoration:none;font-weight:bold;"><?= e($project['name']) ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#4B5D53;font-weight:bold;vertical-align:top;border-top:1px solid #CBE5D4;">نام و نام خانوادگی:</td>
                        <td style="padding:10px 0;color:#0B1711;vertical-align:top;border-top:1px solid #CBE5D4;font-weight:bold;">
                            <?= e($full_name) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#4B5D53;font-weight:bold;vertical-align:top;border-top:1px solid #CBE5D4;">شماره تماس:</td>
                        <td style="padding:10px 0;vertical-align:top;border-top:1px solid #CBE5D4;">
                            <span dir="ltr" style="unicode-bidi:isolate;display:inline-block;">
                                <a href="tel:<?= e($phone) ?>" style="color:#1F8B59;text-decoration:none;font-weight:bold;"><?= e($phone) ?></a>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#4B5D53;font-weight:bold;vertical-align:top;border-top:1px solid #CBE5D4;">فایل رزومه:</td>
                        <td style="padding:10px 0;color:#0B1711;vertical-align:top;border-top:1px solid #CBE5D4;">
                            <span style="display:inline-block;background-color:#CFE9D8;color:#0B1711;padding:4px 12px;border-radius:999px;font-size:13px;font-weight:bold;">
                                پیوست شد: <?= e($resume_filename) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#4B5D53;font-weight:bold;vertical-align:top;border-top:1px solid #CBE5D4;">زمان ارسال:</td>
                        <td style="padding:10px 0;color:#0B1711;vertical-align:top;border-top:1px solid #CBE5D4;">
                            <?= e($sent_at) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#4B5D53;font-weight:bold;vertical-align:top;border-top:1px solid #CBE5D4;">آی‌پی فرستنده:</td>
                        <td style="padding:10px 0;color:#4B5D53;vertical-align:top;border-top:1px solid #CBE5D4;direction:ltr;text-align:right;">
                            <code style="font-family:monospace;background-color:#EEF9EE;padding:2px 6px;border-radius:4px;"><?= e($ip_address) ?></code>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 0;color:#4B5D53;font-weight:bold;vertical-align:top;border-top:1px solid #CBE5D4;">توضیحات:</td>
                        <td style="padding:10px 0;color:#0B1711;vertical-align:top;border-top:1px solid #CBE5D4;white-space:pre-wrap;background-color:#EEF9EE;padding:12px;border-radius:12px;">
                            <?= !empty($message) ? nl2br(e($message)) : '—' ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- Footer Notice -->
        <tr>
            <td style="padding:20px 32px;background-color:#EEF9EE;text-align:center;border-top:1px solid #CBE5D4;font-size:13px;color:#4B5D53;">
                این پیام از فرم پروژه‌ها در وب‌سایت مدبرنت ارسال شده است.
            </td>
        </tr>
    </table>
</body>
</html>

<?php
// Simple mail helper using PHPMailer + .env values.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/Exception.php';

if (!function_exists('loadEnv')) {
    /**
     * Small .env parser shared with db.php and setup.
     */
    function loadEnv($path)
    {
        if (!file_exists($path)) {
            return [];
        }

        $env = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $trimmed = ltrim($line);
            if ($trimmed === '' || $trimmed[0] === '#' || $trimmed[0] === ';') {
                continue;
            }
            $delimiterPos = strpos($trimmed, '=');
            if ($delimiterPos === false) {
                continue;
            }
            $key = trim(substr($trimmed, 0, $delimiterPos));
            $value = trim(substr($trimmed, $delimiterPos + 1));
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            $env[$key] = $value;
        }
        return $env;
    }
}

/**
 * Send an email using SMTP settings from .env.
 * Returns true on success, false on failure.
 */
function sendAppMail($to, $subject, $bodyHtml, $bodyText = '')
{
    $env = loadEnv(__DIR__ . '/../.env');

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $env['MAIL_HOST'] ?? 'smtp.gmail.com';
        $mail->Port       = (int)($env['MAIL_PORT'] ?? 587);
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = $env['MAIL_ENCRYPTION'] ?? 'tls';
        $mail->Username   = $env['MAIL_USERNAME'] ?? '';
        $mail->Password   = $env['MAIL_PASSWORD'] ?? '';
        $mail->CharSet    = 'UTF-8';

        $fromAddress = $env['MAIL_FROM_ADDRESS'] ?? ($env['MAIL_USERNAME'] ?? 'noreply@example.com');
        $fromName    = $env['MAIL_FROM_NAME'] ?? ($env['APP_NAME'] ?? 'Cloud 9 Cafe');

        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($to);
        $mail->addReplyTo($fromAddress, $fromName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;
        $mail->AltBody = $bodyText ?: strip_tags($bodyHtml);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error to PHP error log for debugging.
        error_log('Email send failed: ' . $mail->ErrorInfo);
        return false;
    }
}
?>

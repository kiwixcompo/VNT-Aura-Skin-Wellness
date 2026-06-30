<?php
namespace App\Helpers;

class EmailHelper
{
    private $mailer = null;
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/mail.php';
        $this->setupMailer();
    }

    private function setupMailer()
    {
        // Attempt to load PHPMailer if composer is installed
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
                $this->mailer->isSMTP();
                $this->mailer->Host       = $this->config['mailers']['smtp']['host'];
                $this->mailer->SMTPAuth   = true;
                $this->mailer->Username   = $this->config['mailers']['smtp']['username'];
                $this->mailer->Password   = $this->config['mailers']['smtp']['password'];
                $this->mailer->SMTPSecure = $this->config['mailers']['smtp']['encryption'];
                $this->mailer->Port       = $this->config['mailers']['smtp']['port'];
                
                $this->mailer->setFrom($this->config['from']['address'], $this->config['from']['name']);
                $this->mailer->isHTML(true);
            } catch (\Exception $e) {
                error_log("PHPMailer initialization failed: " . $e->getMessage());
                $this->mailer = null;
            }
        }
    }

    public function sendVerificationEmail($toEmail, $code)
    {
        $subject = "Your VNT Aura Verification Code";
        $body = $this->getVerificationTemplate(['code' => $code]);
        
        $this->saveEmailForDevelopment($toEmail, $subject, $body);
        $this->saveVerificationCode($code);

        if ($this->mailer === null || empty($this->config['mailers']['smtp']['username'])) {
            return $this->sendSimpleEmail($toEmail, $subject, $body);
        }

        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body);
            return $this->mailer->send();
        } catch (\Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    private function sendSimpleEmail($toEmail, $subject, $body)
    {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $this->config['from']['name'] . " <" . $this->config['from']['address'] . ">\r\n";
        
        return @mail($toEmail, $subject, $body, $headers);
    }

    private function saveEmailForDevelopment($to, $subject, $body)
    {
        $dir = __DIR__ . '/../../storage/emails/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $filename = $dir . date('Y-m-d_H-i-s') . '_' . md5($to) . '.html';
        $content = "<!-- TO: $to -->\n<!-- SUBJECT: $subject -->\n<hr>\n" . $body;
        file_put_contents($filename, $content);
    }

    private function saveVerificationCode($code)
    {
        $dir = __DIR__ . '/../../public/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $filename = $dir . 'verification_codes.txt';
        $log = date('Y-m-d H:i:s') . " - CODE: $code\n";
        file_put_contents($filename, $log, FILE_APPEND);
    }

    private function getVerificationTemplate($data)
    {
        $code = htmlspecialchars($data['code']);
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; background-color: #fcfcfc;'>
            <h2 style='color: #1c1c1c; text-align: center; border-bottom: 1px solid #B4975A; padding-bottom: 10px;'>VNT Aura Skin & Wellness</h2>
            <p style='color: #5e5e5e; font-size: 16px;'>Hello,</p>
            <p style='color: #5e5e5e; font-size: 16px;'>Thank you for registering. Please use the verification code below to confirm your email address:</p>
            <div style='text-align: center; margin: 30px 0;'>
                <span style='font-size: 32px; font-weight: bold; color: #B4975A; letter-spacing: 5px; background: #fff; padding: 10px 20px; border: 1px dashed #B4975A;'>
                    {$code}
                </span>
            </div>
            <p style='color: #5e5e5e; font-size: 14px; text-align: center;'>This code will expire in 24 hours.</p>
        </div>";
    }
}

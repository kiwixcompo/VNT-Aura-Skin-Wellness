<?php

class EmailHelper
{
    private $config;
    private $mailer;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/mail.php';
        $this->setupMailer();
    }

    private function setupMailer(): void
    {
        // Check if PHPMailer is available
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            // Attempt to load from vendor if not already loaded
            $autoloadPath = __DIR__ . '/../vendor/autoload.php';
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
            }
        }

        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $this->mailer = null;
            error_log("PHPMailer not found - using PHP mail() function as fallback");
            return;
        }
        
        try {
            $smtpConfig = $this->config['mailers']['smtp'];
            
            // Check if SMTP is properly configured
            if (empty($smtpConfig['host']) || empty($smtpConfig['username']) || empty($smtpConfig['password'])) {
                error_log("SMTP not configured - using PHP mail() function as fallback");
                $this->mailer = null;
                return;
            }
            
            $this->mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Enable verbose debug output (only in debug mode)
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true' || ($_ENV['APP_DEBUG'] ?? false) === true) {
                // Keep verbose output off by default for production, but can be enabled if needed
                // $this->mailer->SMTPDebug = 2;
                $this->mailer->Debugoutput = function($str, $level) {
                    error_log("SMTP Debug [$level]: $str");
                };
            }
            
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $smtpConfig['host'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $smtpConfig['username'];
            $this->mailer->Password = $smtpConfig['password'];
            
            // Use STARTTLS encryption for port 587
            if ($smtpConfig['port'] == 587) {
                $this->mailer->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $this->mailer->SMTPSecure = $smtpConfig['encryption'];
            }
            
            $this->mailer->Port = $smtpConfig['port'];
            
            $this->mailer->Timeout = 30;
            $this->mailer->SMTPKeepAlive = false;
            
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->Encoding = 'base64';
            
            $this->mailer->setFrom(
                $this->config['from']['address'],
                $this->config['from']['name']
            );
            
            $this->mailer->isHTML(true);
            
            error_log("PHPMailer configured successfully with Google Workspace SMTP: " . $smtpConfig['host'] . ":" . $smtpConfig['port']);
            
        } catch (\Exception $e) {
            error_log("PHPMailer setup failed: " . $e->getMessage());
            $this->mailer = null;
        }
    }

    /**
     * Send Admin Notification for New Booking
     */
    public function sendAdminBookingNotification(string $adminEmail, array $bookingData): bool
    {
        try {
            $subject = "New Booking Request: " . $bookingData['service'];
            $body = $this->getEmailTemplate('booking_admin_notification', $bookingData);
            
            if ($this->mailer === null) {
                return $this->sendSimpleEmail($adminEmail, $subject, $body);
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($adminEmail);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            return $this->mailer->send();
            
        } catch (\Exception $e) {
            error_log("Admin notification email failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Client Confirmation for New Booking
     */
    public function sendClientBookingConfirmation(string $clientEmail, array $bookingData): bool
    {
        try {
            $subject = "Booking Request Received - VNT Aura";
            $body = $this->getEmailTemplate('booking_client_confirmation', $bookingData);
            
            if ($this->mailer === null) {
                return $this->sendSimpleEmail($clientEmail, $subject, $body);
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($clientEmail);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            return $this->mailer->send();
            
        } catch (\Exception $e) {
            error_log("Client confirmation email failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Status Update Notification
     */
    public function sendStatusUpdateNotification(string $clientEmail, array $bookingData, string $newStatus): bool
    {
        try {
            $bookingData['newStatus'] = $newStatus;
            $subject = "Your Booking Status: " . ucfirst($newStatus);
            $body = $this->getEmailTemplate('booking_status_update', $bookingData);
            
            if ($this->mailer === null) {
                return $this->sendSimpleEmail($clientEmail, $subject, $body);
            }
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($clientEmail);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            
            return $this->mailer->send();
            
        } catch (\Exception $e) {
            error_log("Status update email failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generic test email
     */
    public function sendTestEmail(string $to): bool
    {
        try {
            $subject = "VNT Aura - SMTP Test Successful!";
            $body = "If you are reading this, your SMTP configuration is working perfectly via EmailHelper.";
            
            if ($this->mailer === null) {
                return $this->sendSimpleEmail($to, $subject, $body);
            }
            
            // Force debug output for test email
            $this->mailer->SMTPDebug = 2;
            $this->mailer->Debugoutput = function($str, $level) {
                echo htmlspecialchars($str) . "<br>";
                error_log("SMTP Debug [$level]: $str");
            };
            
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            return $this->mailer->send();
        } catch (\Exception $e) {
            echo "<strong>Mailer Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
            return false;
        }
    }

    /**
     * Simple email system - uses PHP mail() function
     */
    private function sendSimpleEmail(string $to, string $subject, string $body): bool
    {
        try {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . $this->config['from']['name'] . " <" . $this->config['from']['address'] . ">" . "\r\n";
            $headers .= "Reply-To: " . $this->config['from']['address'] . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            $result = @mail($to, $subject, $body, $headers);
            
            if ($result) {
                error_log("✓ mail() function: Email sent successfully to: $to");
            } else {
                error_log("✗ mail() function: Email sending failed to: $to");
            }
            
            return $result;
            
        } catch (\Exception $e) {
            error_log("Email exception: " . $e->getMessage());
            return false;
        }
    }

    private function getEmailTemplate(string $template, array $data): string
    {
        switch ($template) {
            case 'booking_admin_notification':
                return $this->getBookingAdminNotificationTemplate($data);
            case 'booking_client_confirmation':
                return $this->getBookingClientConfirmationTemplate($data);
            case 'booking_status_update':
                return $this->getBookingStatusUpdateTemplate($data);
            default:
                throw new \Exception("Unknown email template: {$template}");
        }
    }

    private function getBookingAdminNotificationTemplate(array $data): string
    {
        $name = htmlspecialchars($data['name'] ?? $data['client_name'] ?? '');
        $email = htmlspecialchars($data['email'] ?? $data['client_email'] ?? '');
        $phone = htmlspecialchars($data['phone'] ?? $data['client_phone'] ?? '');
        $service = htmlspecialchars($data['service'] ?? '');
        $date = htmlspecialchars($data['date'] ?? $data['preferred_date'] ?? '');
        $time = htmlspecialchars($data['time'] ?? $data['preferred_time'] ?? '');
        $notes = nl2br(htmlspecialchars($data['notes'] ?? ''));

        return "
        <h2>New Booking Request</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Service:</strong> $service</p>
        <p><strong>Date:</strong> $date</p>
        <p><strong>Time:</strong> $time</p>
        <p><strong>Notes:</strong><br>$notes</p>
        ";
    }

    private function getBookingClientConfirmationTemplate(array $data): string
    {
        $name = htmlspecialchars($data['name'] ?? $data['client_name'] ?? '');
        $service = htmlspecialchars($data['service'] ?? '');
        $date = htmlspecialchars($data['date'] ?? $data['preferred_date'] ?? '');
        $time = htmlspecialchars($data['time'] ?? $data['preferred_time'] ?? '');

        return "
        <h2>Hello $name,</h2>
        <p>Thank you for booking with VNT Aura Skin & Wellness.</p>
        <p>We have received your request for <strong>$service</strong> on <strong>$date</strong> at <strong>$time</strong>.</p>
        <p>Our team will contact you shortly to confirm your appointment.</p>
        <br>
        <p>Warm regards,<br>VNT Aura Team</p>
        ";
    }

    private function getBookingStatusUpdateTemplate(array $data): string
    {
        $name = htmlspecialchars($data['client_name'] ?? '');
        $service = htmlspecialchars($data['service'] ?? '');
        $date = htmlspecialchars($data['preferred_date'] ?? '');
        $time = htmlspecialchars($data['preferred_time'] ?? '');
        $newStatus = htmlspecialchars($data['newStatus'] ?? '');

        return "
        <h3>Hello $name,</h3>
        <p>The status of your booking for <strong>$service</strong> on $date at $time has been updated to: <strong style='text-transform: uppercase;'>$newStatus</strong>.</p>
        <p>If you have any questions, please contact us.</p>
        <br>
        <p>Best regards,<br>VNT Aura Skin & Wellness</p>
        ";
    }
}

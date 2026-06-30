<?php
namespace App\Controllers;

require_once __DIR__ . '/../Helpers/EmailHelper.php';

use App\Helpers\EmailHelper;
use PDO;
use Exception;

class AuthController
{
    private $db;
    private $emailHelper;

    public function __construct($db)
    {
        $this->db = $db;
        $this->emailHelper = new EmailHelper();
    }

    // Step A: Dispatching on Register
    public function register($email, $password)
    {
        // Check if email exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception("Email is already registered.");
        }

        // 1. Generate a cryptographically secure 6-digit string
        $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
        $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 2. Save user to DB as 'pending'
        $stmt = $this->db->prepare("INSERT INTO users (email, password, verification_code, verification_expires) VALUES (?, ?, ?, ?)");
        $stmt->execute([$email, $hashedPassword, $verificationCode, $verificationExpires]);
        $userId = $this->db->lastInsertId();

        // 3. Dispatch Email inside a resilient try-catch
        try {
            $this->emailHelper->sendVerificationEmail($email, $verificationCode);
        } catch (\Exception $e) {
            error_log("Email dispatch failed: " . $e->getMessage());
        }
        
        return $userId;
    }

    // Step B: Verifying the OTP
    public function verifyEmail($email, $submittedCode)
    {
        $stmt = $this->db->prepare("SELECT id, verification_code, verification_expires FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("User not found.");
        }

        if ($user['verification_code'] !== $submittedCode) {
            throw new Exception("Invalid verification code.");
        }

        if (strtotime($user['verification_expires']) < time()) {
            throw new Exception("Verification code has expired.");
        }

        // Valid, update database
        $stmt = $this->db->prepare("UPDATE users SET email_verified = 1, verification_code = NULL, verification_expires = NULL, account_status = 'active' WHERE id = ?");
        $stmt->execute([$user['id']]);

        return true;
    }

    // Step C: The Login Guard
    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT id, password, email_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password.");
        }

        if (!$user['email_verified']) {
            // Generate a fresh code, save to DB, and resend email
            $newCode = sprintf('%06d', mt_rand(100000, 999999));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            $stmt = $this->db->prepare("UPDATE users SET verification_code = ?, verification_expires = ? WHERE id = ?");
            $stmt->execute([$newCode, $expires, $user['id']]);
            
            try {
                $this->emailHelper->sendVerificationEmail($email, $newCode);
            } catch (\Exception $e) {
                // Ignore exception
            }
            
            // Deny login and throw specific exception for redirect
            throw new Exception("UNVERIFIED");
        }

        return $user['id'];
    }
}

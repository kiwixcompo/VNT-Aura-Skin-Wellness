<?php
/**
 * Mail Configuration
 */

return [
    'default' => 'smtp',
    
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
            'port' => $_ENV['MAIL_PORT'] ?? 587,
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
            'username' => $_ENV['MAIL_USERNAME'] ?? null,
            'password' => $_ENV['MAIL_PASSWORD'] ?? null,
            'timeout' => 60,
        ],
    ],
    
    'from' => [
        'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@vntaura.com',
        'name' => $_ENV['MAIL_FROM_NAME'] ?? 'VNT Aura Skin & Wellness',
    ],
    
    'templates' => [
        'booking_admin_notification' => 'emails/booking_admin_notification',
        'booking_client_confirmation' => 'emails/booking_client_confirmation',
        'booking_status_update' => 'emails/booking_status_update',
    ],
];

<?php


use PHPMailer\PHPMailer\PHPMailer;

return [
    'host' => '', //your host to send through
    'smtp_auth' => true, // enable SMTP authentication
    'username' => '',// SMTP username
    'password' => '', //SMTP username
    'smtp_secure' => PHPMailer::ENCRYPTION_STARTTLS,// default is tls
    'port' => '',
    'default_sender' => '',
    'default_message' => 'Please do not reply to this email', // adds a small footer text to your email
];

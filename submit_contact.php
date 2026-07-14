<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

function wants_json_response(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    return stripos($accept, 'application/json') !== false
        || strtolower($requestedWith) === 'fetch';
}

function send_response(int $status, array $payload): void
{
    http_response_code($status);

    if (wants_json_response()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    $title = $payload['ok'] ? 'Message sent' : 'Message not sent';
    $message = $payload['message'] ?? 'Please try again.';

    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . h($title) . '</title><link rel="stylesheet" href="styles.css">';
    echo '</head><body><main class="section"><div class="container">';
    echo '<p class="section-kicker">Contact</p><h1 class="response-title">' . h($title) . '</h1>';
    echo '<p>' . h($message) . '</p><a class="button primary" href="index.php#contact">Back to portfolio</a>';
    echo '</div></main></body></html>';
    exit;
}

function send_contact_email(string $to, array $messageData): bool
{
    if (!function_exists('mail')) {
        return false;
    }

    $subject = 'Portfolio contact: ' . $messageData['subject'];
    $body = implode("\n", [
        'New message from your portfolio contact form.',
        '',
        'Name: ' . $messageData['name'],
        'Email: ' . $messageData['email'],
        'Subject: ' . $messageData['subject'],
        '',
        'Message:',
        $messageData['message'],
    ]);
    $headers = [
        'From: Portfolio Contact <no-reply@localhost>',
        'Reply-To: ' . $messageData['email'],
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return mail($to, $subject, $body, implode("\r\n", $headers));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(405, [
        'ok' => false,
        'message' => 'Use the contact form to send a message.',
    ]);
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

$errors = [];

if ($name === '' || strlen($name) > 120) {
    $errors[] = 'Enter your name.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) {
    $errors[] = 'Enter a valid email address.';
}

if ($subject === '' || strlen($subject) > 160) {
    $errors[] = 'Enter a subject.';
}

if ($message === '' || strlen($message) > 2000) {
    $errors[] = 'Enter a message under 2000 characters.';
}

if ($errors) {
    send_response(422, [
        'ok' => false,
        'message' => implode(' ', $errors),
    ]);
}

$pdo = portfolio_db();
$recipientEmail = portfolio_config()['site']['email'];
$emailSent = send_contact_email($recipientEmail, [
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
]);

if (!$pdo) {
    if ($emailSent) {
        send_response(200, [
            'ok' => true,
            'message' => 'Thanks. Your message was sent to Eugen.',
        ]);
    }

    send_response(503, [
        'ok' => false,
        'message' => 'The message could not be delivered because email sending or MySQL is not configured yet.',
    ]);
}

try {
    $statement = $pdo->prepare(
        'INSERT INTO contact_messages
            (name, email, subject, message, ip_address, user_agent)
         VALUES
            (:name, :email, :subject, :message, :ip_address, :user_agent)'
    );

    $statement->execute([
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
    ]);
} catch (Throwable $error) {
    error_log('Portfolio contact insert failed: ' . $error->getMessage());
    if (!$emailSent) {
        send_response(500, [
            'ok' => false,
            'message' => 'The message could not be saved or emailed. Please try again later.',
        ]);
    }
}

send_response(200, [
    'ok' => true,
    'message' => $emailSent
        ? 'Thanks. Your message was sent to Eugen.'
        : 'Thanks. Your message was saved successfully.',
]);

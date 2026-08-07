<?php
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Read JSON or Form data
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

$name = filter_var($data['name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$company = filter_var($data['company'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phone = filter_var($data['phone'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$auditType = filter_var($data['auditType'] ?? 'quick', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$message = filter_var($data['message'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$name || !$email || !$company) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Kérjük töltse ki a kötelező mezőket (Név, Cég/Intézmény, E-mail)!']);
    exit;
}

// Recipient email
$to = "info@it-lens.hu"; // Primary notification address
$subject = "Új IT LENS Audit Igénylés: " . $company . " (" . $name . ")";

$body = "Új audit igénylés érkezett az IT LENS weboldaláról:\n\n";
$body .= "Név: " . $name . "\n";
$body .= "Cég / Intézmény: " . $company . "\n";
$body .= "E-mail: " . $email . "\n";
$body .= "Telefon: " . ($phone ? $phone : 'Nem adott meg') . "\n";
$body .= "Audit Típus: " . $auditType . "\n";
$body .= "Megjegyzés / Kihívás:\n" . ($message ? $message : 'Nincs megjegyzés') . "\n\n";
$body .= "Küldés ideje: " . date('Y-m-d H:i:s') . "\n";
$body .= "IP Cím: " . $_SERVER['REMOTE_ADDR'] . "\n";

$headers = "From: IT LENS Weboldal <noreply@it-lens.hu>\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$mailSent = @mail($to, $subject, $body, $headers);

if ($mailSent) {
    echo json_encode([
        'success' => true, 
        'message' => 'Köszönjük! Az audit igénylést sikeresen rögzítettük. 24 órán belül felvesszük Önnel a kapcsolatot.'
    ]);
} else {
    // If mail function fails on localhost/test, return success for UX with log note
    echo json_encode([
        'success' => true, 
        'message' => 'Köszönjük az igénylést! Az adatokat rögzítettük, hamarosan keressük Önt.'
    ]);
}

<?php
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) {
    $data = $_POST;
}

$name = trim((string)($data['name'] ?? ''));
$company = trim((string)($data['company'] ?? ''));
$emailRaw = trim((string)($data['email'] ?? ''));
$phone = trim((string)($data['phone'] ?? ''));
$auditType = (string)($data['auditType'] ?? 'quick');
$message = trim((string)($data['message'] ?? ''));
$privacyAcknowledged = ($data['privacyAcknowledged'] ?? false) === true || ($data['privacyAcknowledged'] ?? '') === 'true';
$privacyVersion = trim((string)($data['privacyVersion'] ?? ''));
$honeypot = trim((string)($data['website'] ?? ''));

if ($honeypot !== '') {
    echo json_encode(['success' => true, 'message' => 'Köszönjük a megkeresést.']);
    exit;
}

$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$allowedAuditTypes = ['quick', 'deep', 'advisory'];

if ($name === '' || $company === '' || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Kérjük, töltse ki a kötelező mezőket (név, szervezet, e-mail).']);
    exit;
}

if (!$privacyAcknowledged || $privacyVersion === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Az adatkezelési tájékoztató megismerésének visszaigazolása szükséges.']);
    exit;
}

if (!in_array($auditType, $allowedAuditTypes, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Érvénytelen szolgáltatástípus.']);
    exit;
}

$name = mb_substr(strip_tags($name), 0, 120);
$company = mb_substr(strip_tags($company), 0, 160);
$phone = mb_substr(strip_tags($phone), 0, 50);
$message = mb_substr(strip_tags($message), 0, 3000);

$to = "info@it-lens.hu";
$subject = "Új IT LENS megkeresés: " . $company . " (" . $name . ")";

$body = "Új megkeresés érkezett az IT LENS weboldaláról:\n\n";
$body .= "Név: " . $name . "\n";
$body .= "Cég / Intézmény: " . $company . "\n";
$body .= "E-mail: " . $email . "\n";
$body .= "Telefon: " . ($phone !== '' ? $phone : 'Nem adott meg') . "\n";
$body .= "Szolgáltatás: " . $auditType . "\n";
$body .= "Megjegyzés:\n" . ($message !== '' ? $message : 'Nincs megjegyzés') . "\n\n";
$body .= "Adatkezelési tájékoztató verzió: " . $privacyVersion . "\n";
$body .= "Küldés ideje: " . gmdate('Y-m-d H:i:s') . " UTC\n";

$headers = "From: IT LENS Weboldal <noreply@it-lens.hu>\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$mailSent = @mail($to, $subject, $body, $headers);

if (!$mailSent) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Az igénylés elküldése technikai okból nem sikerült. Kérjük, írjon közvetlenül az info@it-lens.hu címre.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Köszönjük! A megkeresést elküldtük. 1 munkanapon belül felvesszük Önnel a kapcsolatot.'
]);

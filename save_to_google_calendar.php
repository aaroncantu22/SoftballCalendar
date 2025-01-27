<?php
session_start();
require_once 'vendor/autoload.php';

// Check if the user is authenticated and the token is available
if (!isset($_SESSION['access_token'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

$client = new Google\Client();
$client->setAccessToken($_SESSION['access_token']);

// Check if the access token is expired and refresh it if needed
if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    $_SESSION['access_token'] = $client->getAccessToken();
}

// Get appointments from the request
$input = json_decode(file_get_contents('php://input'), true);
$appointments = $input['appointments'] ?? [];

if (empty($appointments)) {
    echo json_encode(['success' => false, 'message' => 'No appointments provided']);
    exit();
}

$service = new Google_Service_Calendar($client);
$calendarId = 'primary'; // Use the primary Google Calendar of the authenticated user

$timezone = 'America/Los_Angeles'; // Set this to your local timezone
$errors = [];

foreach ($appointments as $appointment) {
    // Prepare event data for each appointment
    $startTime = date('Y-m-d\TH:i:s', strtotime($appointment['appointment_date']));
    $endTime = date('Y-m-d\TH:i:s', strtotime($appointment['appointment_date'] . ' + ' . $appointment['duration'] . ' minutes'));
    
    $event = new Google_Service_Calendar_Event([
        'summary' => $appointment['name'],
        'description' => $appointment['notes'],
        'start' => [
            'dateTime' => $startTime,
            'timeZone' => $timezone,
        ],
        'end' => [
            'dateTime' => $endTime,
            'timeZone' => $timezone,
        ],
    ]);

    try {
        $service->events->insert($calendarId, $event);
    } catch (Exception $e) {
        $errors[] = 'Failed to save appointment: ' . $appointment['name'] . ' - Error: ' . $e->getMessage();
    }
}

if (empty($errors)) {
    echo json_encode(['success' => true, 'message' => 'All appointments saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
}
?>

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

// Check if the access token is expired, refresh it if needed
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

$errors = [];

foreach ($appointments as $appointment) {
    // Prepare event data for each appointment
    $event = new Google_Service_Calendar_Event([
        'summary' => $appointment['name'],
        'description' => $appointment['notes'],
        'start' => [
            'dateTime' => date('c', strtotime($appointment['appointment_date'])),
            'timeZone' => 'America/Los_Angeles', // Change this to the appropriate timezone
        ],
        'end' => [
            'dateTime' => date('c', strtotime($appointment['appointment_date'] . ' + ' . $appointment['duration'] . ' minutes')),
            'timeZone' => 'America/Los_Angeles',
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

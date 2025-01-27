<?php
// Tables.php
// Start the session
session_start();

// Include the Google API client library
require_once 'vendor/autoload.php'; // Adjust the path as needed

// Configure the Google Client
$client = new Google_Client();
$client->setAuthConfig('client_secret_658903576011-887mvp8v7ro1lac6i42n5t3nqo23hijj.apps.googleusercontent.com.json');
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
$client->setRedirectUri('http://localhost/SoftballCalendar/Calendar/oauth2callback.php');

// Check if the user is authenticated with Google
if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] === null) {
    // User is not authenticated, redirect to OAuth2 callback page
    header('Location: http://localhost/SoftballCalendar/Calendar/oauth2callback.php');
    exit();
}

// Set the access token for the client
$client->setAccessToken($_SESSION['access_token']);

// Check if the access token is expired
if ($client->isAccessTokenExpired()) {
    // Check if a refresh token is stored in the session
    if (isset($_SESSION['refresh_token']) && $_SESSION['refresh_token'] !== null) {
        // Refresh the token using the refresh token
        $client->fetchAccessTokenWithRefreshToken($_SESSION['refresh_token']);
        
        // Store the new access token in the session
        $_SESSION['access_token'] = $client->getAccessToken();
    } else {
        // If no refresh token is available, reauthenticate the user
        header('Location: http://localhost/SoftballCalendar/Calendar/oauth2callback.php');
        exit();
    }
}
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";
echo "<img class = 'image2' src='SoftballCalendar.jpeg' alt='jpeg'>";
echo "<img class = 'hide-bg' src='SoftballCalendar2.jpeg' alt='jpeg'>";
try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*****************************************************************TABLE LOGIC****************************************************************/
    // Fetch all appointments for the current month and year, ordered by date and time
    $query = "SELECT * FROM appointments ORDER BY appointment_date ASC";
    $stmt = $conn->query($query);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<div class = 'nav-buttons'>";
    echo "<button onclick='redirect2Calendar()'>View Calendar</button>";
    echo "<button onclick='printTable()'>Print Appointments</button>";
    echo "<button onclick='redirect2ScheduleAppointment()'class='add-button' id='openAppointmentForm'>Schedule an Appointment</button>";
    echo "</div>";
    echo "<table id='appointmentsTable' border='1'>";
    echo "<tr>
            <th>Actions</th>
            <th>Date</th>
            <th>Name</th>
            <th>Lesson Type</th>
            <th>Payment</th>
            <th>Cost</th>
            <th>Credit</th>
            <th>Notes</th>
            <th>Duration</th>
        </tr>";
    // Display all the appointment details on a table
    foreach ($appointments as $appointment) {
        $formattedDate = date('m-d-Y g:iA', strtotime($appointment["appointment_date"]));
        $endDate = new DateTime($appointment["appointment_date"]);
        $endDate->modify("+{$appointment['duration']} minutes");
        $formattedEndDate = $endDate->format('g:iA');
        $formattedDateRange = $formattedDate . ' - ' . $formattedEndDate;
        $credit = $appointment["payment"] - $appointment["cost"];
        echo "<tr>";
        echo "<td>";
        echo "<a href='edit_appointment.php?appointment_id=" . htmlspecialchars($appointment["id"]) . "' title='Edit'><i class='fas fa-pencil-alt'></i></a> ";
        echo "<a href='delete_appointment.php?appointment_id=" . htmlspecialchars($appointment["id"]) . "' onclick='return confirmDelete()' title='Delete'><i class='fas fa-trash-alt'></i></a>";
        echo "</td>";
        echo "<td>" . htmlspecialchars($formattedDateRange) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["lesson_type"]) . "</td>";
        echo "<td>$" . number_format(htmlspecialchars($appointment["payment"]), 2) . "</td>";
        echo "<td>$" . number_format(htmlspecialchars($appointment["cost"]), 2) . "</td>";
        echo "<td>$" . number_format($credit, 2) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["notes"]) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["duration"]) . " minutes</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Get the current month and year
    $month = isset($_GET['month']) ? $_GET['month'] : date('m');
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

    // Get the appointments for the selected month and year
    $query = "SELECT * FROM appointments WHERE MONTH(appointment_date) = :month AND YEAR(appointment_date) = :year";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Calendar</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Calendar.css">
    <script src="Calendar.js" defer></script>
</head>
<script>
    function redirect2ScheduleAppointment(){
    location.href = 'schedule_appointment.php'
}
</script>
</html>
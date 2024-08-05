<?php
// daily_calendar.php

// Database connection parameters
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login_Page.html");
    exit();
}
$username = $_SESSION['user'];

try {
    // Connect to the database
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the date from the query parameter
    if (!isset($_GET['date'])) {
        throw new Exception("Date not specified");
    }
    $date = $_GET['date'];

    // Fetch appointments for the given date
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE DATE(appointment_date) = :date ORDER BY TIME(appointment_date)");
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the images, navigation buttons, and time gap form
    echo "<img class='image2' src='SoftballCalendar.jpeg' alt='jpeg'>";
    echo "<img class='hide-bg' src='SoftballCalendar2.jpeg' alt='jpeg'>";
    echo "<div class='nav-buttons'>";
    echo "<button onclick='redirect2Calendar()'>Go Back to Calendar</button>";
    echo "<button onclick='print_DailyCalendar()'>Print Daily Calendar</button>";
    echo "</div>";

    // Set default time gap (in minutes)
    $timeGap = isset($_GET['gap']) ? (int)$_GET['gap'] : 60; // Default to 60 minutes
    $timeGap = max(1, min($timeGap, 1440)); // Ensure the gap is between 1 and 1440 minutes

    // Display the appointments in a daily calendar format
    echo "<div class='daily-calendar'>";
    echo "<h2> Appointments for " . date('F j, Y', strtotime($date)) . "</h2>";

    // Time gap input form
    echo "<div class='jump-to'>";
    echo "<form method='get' action=''>";
    echo "<input type='hidden' name='date' value='" . htmlspecialchars($date) . "'>";
    echo "<label for='gap'>Time Gap (minutes): </label>";
    echo "<input type='number' id='gap' name='gap' min='1' max='1440' value='" . htmlspecialchars($timeGap) . "'>";
    echo "<button type='submit'>Update</button>";
    echo "</div>";
    echo "</form>";

    // Display time slots in a table
    echo "<table class='calendar-table'>";
    echo "<tr><th>Time</th><th>Appointment</th></tr>";

    // Generate time slots based on time gap
    $startOfDay = strtotime($date . ' 07:00');
    $endOfDay = strtotime($date . ' 20:59');
    for ($time = $startOfDay; $time <= $endOfDay; $time += $timeGap * 60) {
        $timeFormatted = date('g:i A', $time);
        echo "<tr>";
        echo "<td class='time-slot'>" . $timeFormatted . "</td>";
        echo "<td class='appointments'>";

        // Display appointments in the respective time slot
        $slotHasAppointment = false;
        foreach ($appointments as $appointment) {
            $startTime = strtotime($appointment['appointment_date']);
            $endTime = strtotime($appointment['appointment_date'] . ' + ' . $appointment['duration'] . ' minutes');

            // Check if the appointment falls within this time slot
            if ($startTime >= $time && $startTime < $time + $timeGap * 60) {
                echo "<div class='appointment'>";
                echo "<a href='#' class='rappointment-link' data-appointment='" . htmlspecialchars(json_encode($appointment)) . "'>" . htmlspecialchars($appointment['name']) . "</a>";
                echo "<p>" . date('g:i A', $startTime) . " - " . date('g:i A', $endTime) . "</p>";
                echo "</div>";
                $slotHasAppointment = true;
            }
        }

        if (!$slotHasAppointment) {
            echo "No appointments";
        }

        echo "</td>";
        echo "</tr>";
    }

    echo "</table>"; // Close calendar table
    echo "</div>"; // Close daily-calendar

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Daily Calendar</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Calendar.css">
    <link rel="stylesheet" href="Modal.css">
    <script src="Calendar.js" defer></script>
</head>
<body>
    <div class="header">
        <div class="welcome-message">
            Welcome <?php echo htmlspecialchars($username); ?>!
        </div>
    </div>
    <div id="modal-details"></div>
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-appointment-details"></div>
        </div>
    </div>
</body>
</html>

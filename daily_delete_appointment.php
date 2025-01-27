<?php
// Set the default timezone
date_default_timezone_set('UTC');

// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id']) && isset($_GET['date'])) {
        $appointmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);

        if ($appointmentId && $date) {
            // Delete the appointment
            $query = "DELETE FROM appointments WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->execute([':id' => $appointmentId]);

            // Redirect back to the daily calendar for the specific date
            header("Location: daily_calendar.php?date=$date");
            exit;
        } else {
            echo "Invalid appointment ID or date.";
            echo "<br><a href='Calendar.php'>Back to Calendar</a>";
        }
    } else {
        echo "No appointment ID or date specified.";
        echo "<br><a href='Calendar.php'>Back to Calendar</a>";
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("There was an error processing your request. Please try again later.");
}
?>

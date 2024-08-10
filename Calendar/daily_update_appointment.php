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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate input
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $lesson_type = filter_input(INPUT_POST, 'lesson_type', FILTER_SANITIZE_STRING);
        $payment = filter_input(INPUT_POST, 'payment', FILTER_VALIDATE_FLOAT);
        $cost = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_FLOAT);
        $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
        $appointment_date = filter_input(INPUT_POST, 'appointment_date', FILTER_SANITIZE_STRING);
        $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);

        if (!$id || !$name || !$lesson_type || !$payment || !$cost || !$appointment_date || !$duration) {
            echo "<h2>Invalid input. Please ensure all fields are filled correctly.</h2>";
            echo "<div class='nav-buttons'>";
            echo "<a href='Calendar.php'>Back to Calendar</a>";
            echo "<a href='Tables.php'>Back to Appointment Tables</a>";
            echo "</div>";
            exit;
        }

        $startDate = new DateTime($appointment_date);
        $endDate = clone $startDate;
        $endDate->modify("+{$duration} minutes");

        // Calculate the start and end time with 10-minute gap
        $gap = new DateInterval('PT10M'); // 10 minutes interval

        // Check if the updated appointment conflicts with existing ones
        $query = "SELECT id, appointment_date, duration FROM appointments WHERE id != ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conflict = false;
        foreach ($appointments as $appointment) {
            $existingStart = new DateTime($appointment["appointment_date"]);
            $existingEnd = clone $existingStart;
            $existingEnd->modify("+{$appointment['duration']} minutes");

            // Ensure a 10-minute gap between appointments
            $requiredStartAfter = clone $existingEnd;
            $requiredStartAfter->add($gap);

            $requiredEndBefore = clone $existingStart;
            $requiredEndBefore->sub($gap);

            if (($startDate < $requiredStartAfter && $endDate > $existingStart) ||
                ($endDate > $requiredEndBefore && $startDate < $existingEnd)) {
                $conflict = true;
                break;
            }
        }

        if ($conflict) {
            echo "<h2>Appointment conflict: Please choose a different time.</h2>";
            echo "<div class='nav-buttons'>";
            echo "<a href='Calendar.php'>Back to Calendar</a>";
            echo "<a href='Tables.php'>Back to Appointment Tables</a>";
            echo "</div>";
        } else {
            // Update the appointment
            $query = "UPDATE appointments SET name = :name, lesson_type = :lesson_type, payment = :payment, cost = :cost, credit = :credit, notes = :notes, appointment_date = :appointment_date, duration = :duration WHERE id = :id";
            $stmt = $conn->prepare($query);
            $credit = $payment - $cost;
            $stmt->execute([
                ':name' => $name,
                ':lesson_type' => $lesson_type,
                ':payment' => $payment,
                ':cost' => $cost,
                ':credit' => $credit,
                ':notes' => $notes,
                ':appointment_date' => $appointment_date,
                ':duration' => $duration,
                ':id' => $id
            ]);

            // Extract the date from the appointment_date
            $date = (new DateTime($appointment_date))->format('Y-m-d');
            $redirectUrl = "daily_calendar.php?date=$date";

            // Display the success message and the button to redirect
            echo "<h2>Appointment updated successfully!</h2>";
            echo "<div class='nav-buttons'>";
            echo "<a href='$redirectUrl' class='button'>Back to Daily Calendar</a>";
            echo "</div>";
        }
    } else {
        header("Location: Tables.php");
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("There was an error processing your request. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Calendar.css">
</head>
<body>
</body>
</html>

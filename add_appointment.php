<?php
// Set the default timezone
date_default_timezone_set('UTC');

// Connect to the database
$dsn = "mysql:host=127.0.0.1:3306;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate input
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $lesson_type = filter_input(INPUT_POST, 'lesson_type', FILTER_SANITIZE_STRING);
        $payment = filter_input(INPUT_POST, 'payment', FILTER_VALIDATE_FLOAT);
        $cost = filter_input(INPUT_POST, 'cost', FILTER_VALIDATE_FLOAT);
        $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
        $appointment_date = filter_input(INPUT_POST, 'appointment_date', FILTER_SANITIZE_STRING);
        $duration = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);

        if (!$name || !$lesson_type || $payment === false || $cost === false || !$appointment_date || !$duration) {
            echo "Invalid input. Please ensure all fields are filled correctly.";
            echo "<br><a href='Tables.php'>Back to Appointment Tables</a>";
            echo "<br><a href='Calendar.php'>Back to Calendar</a>";
            exit;
        }

        $startDate = new DateTime($appointment_date);
        $endDate = clone $startDate;
        $endDate->modify("+{$duration} minutes");

        // Calculate the start and end time with 10-minute gap
        $gap = new DateInterval('PT10M'); // 10 minutes interval

        // Check if the new appointment overlaps with existing ones
        $query = "SELECT appointment_date, duration FROM appointments";
        $stmt = $conn->query($query);
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
            echo "<h2> Appointment conflict: Please choose a different time.</h2>";
            echo "<div class='nav-buttons'>";
            echo "<a href='Tables.php'>Back to Appointment Tables</a>";
            echo "<br>";
            echo "<a href='Calendar.php'>Back to Calendar</a>";
            echo "</div>";
        } else {
            // Insert the new appointment
            $query = "INSERT INTO appointments (name, lesson_type, payment, cost, credit, notes, appointment_date, duration) VALUES (:name, :lesson_type, :payment, :cost, :credit, :notes, :appointment_date, :duration)";
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
                ':duration' => $duration
            ]);
            echo "<h2>Appointment added successfully. </h2>";
            echo "<div class='nav-buttons'>";
            echo "<a href='Tables.php'>Back to Appointment Tables</a>";
            echo "<br>";
            echo "<a href='Calendar.php'>Back to Calendar</a>";
            echo "</div>";
        }
    } else {
        header("Location: ../Tables.php");
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

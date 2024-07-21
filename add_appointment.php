<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $lesson_type = $_POST["lesson_type"];
        $payment = $_POST["payment"];
        $cost = $_POST["cost"];
        $notes = $_POST["notes"];
        $appointment_date = $_POST["appointment_date"];
        $duration = $_POST["duration"];

        $startDate = new DateTime($appointment_date);
        $endDate = clone $startDate;
        $endDate->modify("+{$duration} minutes");
        $formattedEndDate = $endDate->format('Y-m-d H:i:s');

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
            $requiredStart = clone $existingEnd;
            $requiredStart->add($gap);

            if ($startDate < $requiredStart && $endDate > $existingStart) {
                $conflict = true;
                break;
            }
        }

        if ($conflict) {
            echo "Appointment conflict: Please choose a different time. <a href='Calendar.php'>Back to Calendar</a>";
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

            echo "Appointment added successfully. <a href='Tables.php'>Back to Appointment Tables</a>";
        }
    } else {
        header("Location: ../Tables.php");
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST["id"];
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

        // Calculate the start and end time with 15-minute gap
        $gap = new DateInterval('PT10M'); // 15 minutes interval

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

            // Ensure a 15-minute gap between appointments
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

            echo "Appointment updated successfully. <a href='Tables.php'>Back to Appointment Tables</a>";
        }
    } else {
        header("Location: Tables.php");
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

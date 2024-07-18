<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

if (isset($_GET["appointment_id"])) {
    $appointment_id = $_GET["appointment_id"];

    try {
        $conn = new PDO($dsn, $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the query
        $query = "SELECT * FROM appointments WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($appointment) {
            echo "<h2>Appointment Details</h2>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($appointment["name"]) . "</p>";
            echo "<p><strong>Lesson Type:</strong> " . htmlspecialchars($appointment["lesson_type"]) . "</p>";
            echo "<p><strong>Payment:</strong> " . htmlspecialchars($appointment["payment"]) . "</p>";
            echo "<p><strong>Cost:</strong> $" . htmlspecialchars($appointment["cost"]) . "</p>";
            echo "<p><strong>Notes:</strong> " . htmlspecialchars($appointment["notes"]) . "</p>";
            echo "<p><strong>Appointment Date:</strong> " . htmlspecialchars($appointment["appointment_date"]) . "</p>";
            echo "<p><strong>Duration:</strong> " . htmlspecialchars($appointment["duration"]) . " minutes</p>";
        } else {
            echo "No appointment found with the provided ID.";
        }
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    echo "No appointment ID provided.";
}
?>

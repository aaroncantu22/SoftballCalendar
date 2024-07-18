<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $lesson_type = $_POST["lesson_type"];
    $payment = $_POST["payment"];
    $cost = $_POST["cost"];
    $notes = $_POST["notes"];
    $appointment_date = $_POST["appointment_date"];
    $duration = $_POST["duration"];

    try {
        $conn = new PDO($dsn, $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the query
        $query = "INSERT INTO appointments (name, lesson_type, payment, cost, notes, appointment_date, duration) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$name, $lesson_type, $payment, $cost, $notes, $appointment_date, $duration]);

        // Redirect to success page
        header("Location: success.php");
        exit;
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../Calendar.php");
    exit;
}
?>

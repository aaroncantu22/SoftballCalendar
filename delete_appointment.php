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

        // Delete the appointment
        $query = "DELETE FROM appointments WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$appointment_id]);

        header("Location: Tables.php");
        exit;
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
    
} else {
    echo "No appointment ID provided.";
}
?>

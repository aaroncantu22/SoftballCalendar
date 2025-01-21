<?php
// Tables.php

// Connect to the database
$dsn = "mysql:host=127.0.0.1:3306;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";
try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*****************************************************************TABLE LOGIC****************************************************************/
    // Fetch all appointments for the current month and year, ordered by date and time
    $query = "SELECT * FROM appointments ORDER BY appointment_date ASC";
    $stmt = $conn->query($query);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display all the appointment details on a table
    foreach ($appointments as $appointment) {
        $formattedDate = date('m-d-Y g:iA', strtotime($appointment["appointment_date"]));
        $endDate = new DateTime($appointment["appointment_date"]);
        $endDate->modify("+{$appointment['duration']} minutes");
        $formattedEndDate = $endDate->format('g:iA');
        $formattedDateRange = $formattedDate . ' - ' . $formattedEndDate;
        $credit = $appointment["payment"] - $appointment["cost"];
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
    <title>Calendar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Calendar.css">
    <script src="Calendar.js" defer></script>
</head>

</div>
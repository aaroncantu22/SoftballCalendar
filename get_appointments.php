<?php
// Fetch appointments for a given month and year
function getAppointments($month, $year) {
    $dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
    $dbusername = "root";
    $dbpassword = "manicquail735";
    
    try {
        $conn = new PDO($dsn, $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = "SELECT name, appointment_date FROM appointments WHERE MONTH(appointment_date) = ? AND YEAR(appointment_date) = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$month, $year]);
        
        $appointments = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date = date('j', strtotime($row['appointment_date'])); // Get day of the month
            if (!isset($appointments[$date])) {
                $appointments[$date] = [];
            }
            $appointments[$date][] = $row['name'];
        }
        
        return $appointments;
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}
?>

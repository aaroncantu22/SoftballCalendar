<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all appointments for the current month and year, ordered by date and time
    $query = "SELECT * FROM appointments ORDER BY appointment_date ASC";
    $stmt = $conn->query($query);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Calendar</h2>";
    echo "<button onclick='printTable()'>Print Appointments</button>";
    echo "<table id='appointmentsTable' border='1'>";
    echo "<tr>
            <th>Actions</th>
            <th>Date</th>
            <th>Name</th>
            <th>Lesson Type</th>
            <th>Payment</th>
            <th>Cost</th>
            <th>Credit</th>
            <th>Notes</th>
            <th>Duration</th>
          </tr>";

    foreach ($appointments as $appointment) {
        $formattedDate = date('m-d-Y g:iA', strtotime($appointment["appointment_date"]));
        $endDate = new DateTime($appointment["appointment_date"]);
        $endDate->modify("+{$appointment['duration']} minutes");
        $formattedEndDate = $endDate->format('g:iA');
        $formattedDateRange = $formattedDate . ' - ' . $formattedEndDate;
        $credit = $appointment["payment"] - $appointment["cost"];

        echo "<tr>";
        echo "<td>";
        echo "<a href='edit_appointment.php?appointment_id=" . htmlspecialchars($appointment["id"]) . "' title='Edit'><i class='fas fa-pencil-alt'></i></a> ";
        echo "<a href='delete_appointment.php?appointment_id=" . htmlspecialchars($appointment["id"]) . "' onclick='return confirmDelete()' title='Delete'><i class='fas fa-trash-alt'></i></a>";
        echo "</td>";
        echo "<td>" . htmlspecialchars($formattedDateRange) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["lesson_type"]) . "</td>";
        echo "<td>$" . number_format(htmlspecialchars($appointment["payment"]), 2) . "</td>";
        echo "<td>$" . number_format(htmlspecialchars($appointment["cost"]), 2) . "</td>";
        echo "<td>$" . number_format($credit, 2) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["notes"]) . "</td>";
        echo "<td>" . htmlspecialchars($appointment["duration"]) . " minutes</td>";
        echo "</tr>";
    }

    echo "</table>";

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
    <link rel="stylesheet" href="Calendar.css"> <!-- Include your CSS file -->
</head>


<body>

    <div id="calendar" class="calendar-container"></div>

    <div class="jump-to">
        <input type="number" id="jump-month" placeholder="Month (1-12)" min="1" max="12">
        <input type="number" id="jump-year" placeholder="Year (2024-2026)" min="2024" max="2026">
        <button onclick="jumpToMonth()">Jump to Month</button>
    </div>

    <div class="appointment-form">
        <h2>Schedule an Appointment</h2>
        
        <form action="add_appointment.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="lesson_type">Lesson Type:</label>
    <select id="lesson_type" name="lesson_type" required>
        <option value="Pitching for pitchers">Pitching for pitchers</option>
        <option value="Hitting">Hitting</option>
        <option value="Fielding">Fielding</option>
        <option value="Catching for catchers">Catching for catchers</option>
        <option value="Basic throwing">Basic throwing</option>
        <option value="Basic catching">Basic catching</option>
        <option value="Baserunning">Baserunning</option>
    </select><br>

        <label for="payment">Payment:</label>
        <input type="text" id="payment" name="payment" required><br>

        <label for="cost">Cost:</label>
        <input type="number" id="cost" name="cost" required><br>

        <label for="notes">Notes:</label>
        <input type="text" id="notes" name="notes"><br>

        <label for="appointment_date">Appointment Date:</label>
        <input type="datetime-local" id="appointment_date" name="appointment_date" required><br>

        <label for="duration">Duration (minutes):</label>
    <select id="duration" name="duration" required>
        <option value="45">45 minutes</option>
        <option value="60">60 minutes</option>
    </select><br>
        
        <input type="submit" value="Schedule Appointment">
    </form>

<script src="Calendar.js"></script>

</body>
</html>
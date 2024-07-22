<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";
echo "<button onclick='redirect2Tables()'>View Appointment Tables</button>";
/****************************************************************CALENDAR LOGIC****************************************************************/
include 'get_appointments.php';
function generateCalendar($month, $year, $appointments) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    
    echo "<table border='1'>";
    echo "<tr>";
    
    foreach ($daysOfWeek as $day) {
        echo "<th>$day</th>";
    }
    
    echo "</tr><tr>";
    
    if ($dayOfWeek > 0) { 
        for ($k = 0; $k < $dayOfWeek; $k++) {
            echo "<td></td>";
        }
    }
    
    $currentDay = 1;
    
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            echo "</tr><tr>";
        }
        
        echo "<td>$currentDay";
        
        if (isset($appointments[$currentDay])) {
            echo "<div class='names-container'>";
            foreach ($appointments[$currentDay] as $name) {
                echo "<div class='appointment-link'>$name</div>";
            }
            echo "</div>";
        }
        
        echo "</td>";
        
        $currentDay++;
        $dayOfWeek++;
    }
    
    if ($dayOfWeek != 7) { 
        $remainingDays = 7 - $dayOfWeek;
        for ($k = 0; $k < $remainingDays; $k++) {
            echo "<td></td>";
        }
    }
    
    echo "</tr>";
    echo "</table>";
}

// Example usage
$month = isset($_GET['month']) ? intval($_GET['month']) : date('n'); // Default to current month
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y'); // Default to current year
$appointments = getAppointments($month, $year);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="Calendar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="Calendar.js" defer></script>
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <button onclick="showPreviousMonth()"><i class="fas fa-arrow-left"></i></button>
            <div class="month-name"><?php echo date('F Y', strtotime("$year-$month-01")); ?></div>
            <button onclick="showNextMonth()"><i class="fas fa-arrow-right"></i></button>
            <div class="jump-to">
                <form id="jump-to-form" onsubmit="return jumpToMonth();">
                    <label for="jump-month">Month:</label>
                    <select id="jump-month" name="month">
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $selected = ($i == $month) ? 'selected' : '';
                            echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 10)) . "</option>";
                        }
                        ?>
                    </select>
                    <label for="jump-year">Year:</label>
                    <input type="number" id="jump-year" name="year" value="<?php echo $year; ?>" required>
                    <button type="submit">Jump</button>
                </form>
            </div>
        </div>
        <div class="calendar-body">
            <?php generateCalendar($month, $year, $appointments); ?>
        </div>
    </div>
    <!--Appointments Construction-->
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
    </div>
    <script>
        function jumpToMonth() {
            const month = document.getElementById('jump-month').value;
            const year = document.getElementById('jump-year').value;
            window.location.href = `Calendar.php?month=${month}&year=${year}`;
            return false;
        }
    </script>
</body>
</html>

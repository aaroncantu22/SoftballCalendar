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
            foreach ($appointments[$currentDay] as $name) {
                echo "<br>$name";
            }
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
                <form method="post">
                    <input type="number" name="month" min="1" max="12" value="<?php echo $month; ?>">
                    <input type="number" name="year" min="1900" value="<?php echo $year; ?>">
                    <button type="submit" name="jump_to"><i class="fas fa-calendar-day"></i> Jump to Month</button>
                </form>
            </div>
        </div>
        <div class="calendar-body">
            <?php generateCalendar($month, $year, $appointments); ?>
        </div>
    </div>

<?php
// daily_calendar.php

// Database connection parameters
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login_Page.html");
    exit();
}
$username = $_SESSION['user'];

try {
    // Connect to the database
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the date from the query parameter
    if (!isset($_GET['date'])) {
        throw new Exception("Date not specified");
    }
    $date = $_GET['date'];

    // Fetch appointments for the given date
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE DATE(appointment_date) = :date ORDER BY TIME(appointment_date)");
    $stmt->bindParam(':date', $date);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the images, navigation buttons, and time gap form
    echo "<img class='image2' src='SoftballCalendar.jpeg' alt='jpeg'>";
    echo "<img class='hide-bg' src='SoftballCalendar2.jpeg' alt='jpeg'>";
    echo "<div class='nav-buttons'>";
    echo "<button onclick='redirect2Calendar()'>Go Back to Calendar</button>";
    echo "<button onclick='print_DailyCalendar()'>Print Daily Calendar</button>";
    echo "</div>";

    // Set default time gap (in minutes)
    $timeGap = isset($_GET['gap']) ? (int)$_GET['gap'] : 45; // Default to 45 minutes
    $timeGap = max(1, min($timeGap, 1440)); // Ensure the gap is between 1 and 1440 minutes
    

    // Display the appointments in a daily calendar format
    echo "<div class='daily-calendar'>";
    echo "<h2>Appointments for " . date('F j, Y', strtotime($date)) . "</h2>";

    // Time gap input form
    echo "<div class='jump-to'>";
    echo "<form method='get' action=''>";
    echo "<input type='hidden' name='date' value='" . htmlspecialchars($date) . "'>";
    echo "<label for='gap'>Time Gap (minutes): </label>";
    echo "<input type='number' id='gap' name='gap' min='1' max='1440' value='" . htmlspecialchars($timeGap) . "'>";
    echo "<button type='submit'>Update</button>";
    echo "</form>";
    echo "</div>";

    // Display time slots in a table
    echo "<table class='calendar-table'>";
    echo "<tr><th>Actions</th><th>Time</th><th>Appointment</th></tr>";

    // Generate time slots based on time gap
    $startOfDay = strtotime($date . ' 07:00');
    $endOfDay = strtotime($date . ' 20:59');
    $timeSlots = [];

    // Generate time slots for the day
    for ($time = $startOfDay; $time <= $endOfDay; $time += $timeGap * 60) {
        $timeSlots[] = $time;
    }

    // Add slots for exact appointment start/end times
    foreach ($appointments as $appointment) {
        $startTime = strtotime($appointment['appointment_date']);
        $endTime = strtotime($appointment['appointment_date'] . ' + ' . $appointment['duration'] . ' minutes');

        // Add start and end time slots if not already present
        if (!in_array($startTime, $timeSlots)) {
            $timeSlots[] = $startTime;
        }
        if (!in_array($endTime, $timeSlots)) {
            $timeSlots[] = $endTime;
        }
    }

    // Sort time slots in ascending order
    sort($timeSlots);

    // Merging Logic with 23, 24, 30, and 60-Minute Gap Condition
    $prevAppointment = null;
    $mergedTimes = [];
    $mergedSlots = [];

    foreach ($timeSlots as $time) {
        $timeFormatted = date('g:i A', $time);
        $currentAppointment = null;

        // Check if there is an appointment in the current time slot
        foreach ($appointments as $appointment) {
            $startTime = strtotime($appointment['appointment_date']);
            $endTime = strtotime($appointment['appointment_date'] . ' + ' . $appointment['duration'] . ' minutes');
            if ($time >= $startTime && $time <= $endTime) {
                $currentAppointment = $appointment;
                break;
            }
        }

        // Specific logic for the 15, 23, 24, 30, 45, 60, 70, 75 and 90-minute gaps
        if (in_array($timeGap,[15, 23, 24, 30, 45, 60, 70, 75, 90]) && $prevAppointment && $currentAppointment) {
            $prevEndTime = strtotime($prevAppointment['appointment_date'] . ' + ' . $prevAppointment['duration'] . ' minutes');
            $currentEndTime = strtotime($currentAppointment['appointment_date'] . ' + ' . $currentAppointment['duration'] . ' minutes');

            if ($prevEndTime == $currentEndTime && $prevAppointment['name'] === $currentAppointment['name']) {
                // Merge only if the end times are the same
                $mergedTimes[] = $timeFormatted;
            } else {
                // Display the previous merged timeslot
                $mergedSlots[] = [
                    'times' => $mergedTimes,
                    'appointment' => $prevAppointment
                ];

                // Start a new group of merged times
                $mergedTimes = [$timeFormatted];
            }
        } else if ($prevAppointment && $currentAppointment && $prevAppointment['name'] === $currentAppointment['name']) {
            // General merging logic (non-23/24/30/60-minute gaps)
            $mergedTimes[] = $timeFormatted;
        } else {
            // If there was a previous appointment, display the merged row
            if ($prevAppointment) {
                $mergedSlots[] = [
                    'times' => $mergedTimes,
                    'appointment' => $prevAppointment
                ];
            }

            // Start a new group of merged times
            $mergedTimes = [$timeFormatted];

            // If there's no current appointment, display "No appointments"
            if (!$currentAppointment) {
                $mergedSlots[] = [
                    'times' => $mergedTimes,
                    'appointment' => null
                ];
            }
        }

        $prevAppointment = $currentAppointment;
    }

    // Handle the last group of merged times
    if ($prevAppointment) {
        $mergedSlots[] = [
            'times' => $mergedTimes,
            'appointment' => $prevAppointment
        ];
    }

    // Display the merged slots
    foreach ($mergedSlots as $slot) {
        $slotTimes = $slot['times'];
        echo "<tr>";
        echo "<td class='actions'>";
        if ($slot['appointment']) {
            echo "<button class='action-button edit-button' title='Edit Appointment' data-id='" . htmlspecialchars($slot['appointment']['id']) . "' data-date='" . htmlspecialchars($slot['appointment']['appointment_date']) . "'><i class='fas fa-pencil-alt'></i></button>";
            echo "<button class='action-button delete-button' title='Delete Appointment' data-id='" . htmlspecialchars($slot['appointment']['id']) . "' data-date='" . htmlspecialchars($date) . "'><i class='fas fa-trash-alt'>";
        } else {
            // Pass the slot time to the data-time attribute
            echo "<button class='action-button add-button' title='Add Appointment' data-time='" . date('H:i', strtotime($slot['times'][0])) . "'><i class='fas fa-plus'></i></button>";
        }
        echo "</td>";
        echo "<td class='time-slot'>" . implode("<br><br><br><br><br><br>", $slot['times']) . "</td>";
        if ($slot['appointment']) {
            echo "<td class='appointments'>";
            echo "<div class='appointment'>";
            echo "<a href='#' class='rappointment-link' data-appointment='" . htmlspecialchars(json_encode($slot['appointment'])) . "'>" . htmlspecialchars($slot['appointment']['name']) . "</a>";
            echo "<p>" . date('g:i A', strtotime($slot['appointment']['appointment_date'])) . " - " . date('g:i A', strtotime($slot['appointment']['appointment_date'] . ' + ' . $slot['appointment']['duration'] . ' minutes')) . "</p>";
            echo "</div>";
            echo "</td>";
        } else {
            echo "<td class='appointments'>No appointments</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Daily Calendar</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Daily_Calendar.css">
    <link rel="stylesheet" href="Modal.css">
    <script src="Daily_Calendar.js"></script>
</head>
<body>
    <div class="header">
        <div class="welcome-message">
            Welcome <?php echo htmlspecialchars($username); ?>!
        </div>
    </div>
 <!-- Add Appointment Modal -->
 <div id="addAppointmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Schedule an Appointment</h2>
            <div class="appointment-form">
                <form action="daily_add_appointment.php" method="post">
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
                    <input type="number" id="payment" name="payment" step="0.01" min="0" required><br>

                    <label for="cost">Cost:</label>
                    <input type="number" id="cost" name="cost" step="0.01" min="0" required><br>

                    <label for="notes">Notes:</label>
                    <input type="text" id="notes" name="notes"><br>

                    <label for="appointment_date">Appointment Date:</label>
                    <input type="datetime-local" id="appointment_date" name="appointment_date" required><br>


                    <label for="duration">Duration (minutes):</label>
                    <select id="duration" name="duration" required>
                        <option value="45">45 minutes</option>
                        <option value="60">60 minutes</option>
                    </select><br>
                    <div class="nav-buttons">
                        <input type="submit" value="Schedule Appointment">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    // Open the modal for adding/editing an appointment
    document.querySelectorAll('.add-button').forEach(function(button) {
        button.addEventListener('click', function() {
            const time = this.getAttribute('data-time');
            const date = '<?php echo $date; ?>';
            const appointmentDateTime = date + 'T' + time;
            document.getElementById('appointment_date').value = appointmentDateTime;
            document.getElementById('appointment_id').value = ''; // Clear any existing appointment ID
            openModal();
        });
    });

    document.querySelectorAll('.edit-button').forEach(function(button) {
        button.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-id');
            const appointmentDate = this.getAttribute('data-date');
            document.getElementById('appointment_date').value = appointmentDate;
            document.getElementById('appointment_id').value = appointmentId;
            openModal();
        });
    });

    // Open the modal
    function openModal() {
        document.getElementById('appointmentModal').style.display = 'block';
    }

    // Close the modal
    function closeModal() {
        document.getElementById('appointmentModal').style.display = 'none';
    }
</script>
    <!-- Modal structure -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
        <span class="close">&times;</span>
            <div id="modal-appointment-details"></div>
        </div>
    </div>
</body>
</html>

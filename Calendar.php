<?php
// Start the session
session_start();

// Include the Google API client library
require_once 'vendor/autoload.php'; // Adjust the path as needed

// Configure the Google Client
$client = new Google_Client();
$client->setAuthConfig('client_secret_658903576011-887mvp8v7ro1lac6i42n5t3nqo23hijj.apps.googleusercontent.com.json');
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
$client->setRedirectUri('http://localhost/SoftballCalendar/Calendar/oauth2callback.php');

// Check if the user is authenticated with Google
if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] === null) {
    // User is not authenticated, redirect to OAuth2 callback page
    header('Location: http://localhost/SoftballCalendar/Calendar/oauth2callback.php');
    exit();
}

// Set the access token for the client
$client->setAccessToken($_SESSION['access_token']);

// If the token is expired, refresh it
if ($client->isAccessTokenExpired()) {
    // Refresh the token
    $_SESSION['access_token'] = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
}
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3306;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";
echo "<img class = 'image2' src='SoftballCalendar.jpeg' alt='jpeg'>";
echo "<img class = 'hide-bg' src='SoftballCalendar2.jpeg' alt='jpeg'>";
echo "<div class='nav-buttons'>";
echo "<button onclick='redirect2Tables()'>View Appointment Tables</button>";
echo "<button onclick='printCalendar()'>Print Calendar</button>";
echo "<button class='action-button add-button' id='openAppointmentForm'>Schedule an Appointment</button>";
echo "</div>";

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login_Page.html");
    exit();
}
$username = $_SESSION['user'];


// Fetch default settings from the database
$conn = new PDO($dsn, $dbusername, $dbpassword);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $conn->prepare("SELECT default_month, default_year FROM default_settings LIMIT 1");
$stmt->execute();
$defaultSettings = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect to URL with default month and year if not present
if (!isset($_GET['month']) || !isset($_GET['year'])) {
    $defaultMonth = $defaultSettings['default_month'] ?? 7; // Fallback to 7 if default_month is not set
    $defaultYear = $defaultSettings['default_year'] ?? 2024; // Fallback to 2024 if default_year is not set
    header("Location: Calendar.php?month=$defaultMonth&year=$defaultYear");
    exit();
}
/****************************************************************CALENDAR LOGIC****************************************************************/
include 'get_appointments.php';

// Function to get US observed holidays for a given year
function getUSHolidays($year) {
    $holidays = [
        'New Year\'s Day' => date('Y-m-d', strtotime("$year-01-01")),
        'Martin Luther King Jr. Day' => date('Y-m-d', strtotime("third monday of january $year")),
        'Presidents\' Day' => date('Y-m-d', strtotime("third monday of february $year")),
        'Memorial Day' => date('Y-m-d', strtotime("last monday of may $year")),
        'Juneteenth' => date('Y-m-d', strtotime("$year-06-19")),
        'Independence Day' => date('Y-m-d', strtotime("$year-07-04")),
        'Labor Day' => date('Y-m-d', strtotime("first monday of september $year")),
        'Columbus Day' => date('Y-m-d', strtotime("second monday of october $year")),
        'Veterans Day' => date('Y-m-d', strtotime("$year-11-11")),
        'Thanksgiving Day' => date('Y-m-d', strtotime("fourth thursday of november $year")),
        'Christmas Day' => date('Y-m-d', strtotime("$year-12-25"))
    ];

    // Adjust holidays that fall on weekends
    foreach ($holidays as $name => $date) {
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 6) { // Saturday
            $holidays[$name] = date('Y-m-d', strtotime("$date -1 day"));
        } elseif ($dayOfWeek == 0) { // Sunday
            $holidays[$name] = date('Y-m-d', strtotime("$date +1 day"));
        }
    }

    return $holidays;
}
function generateCalendar($month, $year, $appointments) {
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $dayOfWeek = $dateComponents['wday'];

    $holidays = getUSHolidays($year);

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

        $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($currentDay, 2, '0', STR_PAD_LEFT);
        echo "<td>";
        echo "<button class='day-button' data-date='$date' onclick='window.location.href=\"daily_calendar.php?date=$date\"'>$currentDay</button>";

        if (isset($appointments[$currentDay])) {
            echo "<div class='names-wrapper'>";
            echo "<div class='names-container'>";
            foreach ($appointments[$currentDay] as $appointment) {
                $timeRange = date('g:iA', strtotime($appointment['start_time'])) . '-' . date('g:iA', strtotime($appointment['end_time']));
                echo "<div class='appointment-link' data-appointment='" . htmlspecialchars(json_encode($appointment), ENT_QUOTES, 'UTF-8') . "'>-$timeRange<br>" . $appointment['name'] . "</div>";
            }
            echo "</div>";
            echo "</div>";
        }

        $holidayList = '';
        foreach ($holidays as $holidayName => $holidayDate) {
            if ($date == $holidayDate) {
                $holidayList .= "<div class='holiday-link'>$holidayName</div>";
            }
        }
        echo "<div class='holiday-wrapper'>";
        if ($holidayList) {
            echo "<div class='holiday-container'>$holidayList</div>";
        }
        echo "</div>";
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_defaults'])) {
    $defaultMonth = intval($_POST['default_month']);
    $defaultYear = intval($_POST['default_year']);

    
    // Update default settings
    $stmt = $conn->prepare("REPLACE INTO default_settings (id, default_month, default_year) VALUES (1, :default_month, :default_year)");
    $stmt->bindParam(':default_month', $defaultMonth);
    $stmt->bindParam(':default_year', $defaultYear);
    $stmt->execute();


    // Update the default values for the current page load
    $month = $defaultMonth;
    $year = $defaultYear;
    header("Location: Calendar.php?month=" . urlencode($month) . "&year=" . urlencode($year));
} else {
    // Default to the fetched settings if they exist
    $month = isset($_GET['month']) ? intval($_GET['month']) : ($defaultSettings['default_month'] ?? 10);
    $year = isset($_GET['year']) ? intval($_GET['year']) : ($defaultSettings['default_year'] ?? 2024);
}
$appointments = getAppointments($month, $year);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Calendar</title>
    <link rel="stylesheet" href="Calendar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Modal.css">
    <script src="Calendar.js" defer></script>
    <script src="Modal.js" defer></script>
</head>
<body>
<div class="header">
        <div class="welcome-message">
            Welcome <?php echo htmlspecialchars($username);?>!
        </div>
        <a class="logout-button" href="logout.php">Logout</a>
    </div>
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="nav-buttons">
                <button onclick="showPreviousMonth()"><i class="fas fa-arrow-left"></i></button>
            </div>
            <div class="month-name"><?php echo date('F Y', strtotime("$year-$month-01")); ?></div>
            <div class="nav-buttons">
                <button onclick="showNextMonth()"><i class="fas fa-arrow-right"></i></button>
            </div>
            <div class="jump-to">
                <form id="jump-to-form" onsubmit="return jumpToMonth();">
                <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jump to a specific month</h2>
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
            <!-- Form to Set Default Month and Year -->
            <div class="set-default-settings">
                <div class = "jump-to">
                <form action="" method="post">
                <h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Set Default Month and Year</h2>
                    <label for="default_month">Month:</label>
                    <select id="default_month" name="default_month" required>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $selected = ($i == $month) ? 'selected' : '';
                            echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 10)) . "</option>";
                        }
                        ?>
                    </select>
                    <label for="default_year">Year:</label>
                    <input type="number" id="default_year" name="default_year" value="<?php echo $year; ?>" required>
                    <button type="submit" name="update_defaults">Set Default</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="calendar-body">
            <?php generateCalendar($month, $year, $appointments); ?>
        </div>
    </div>
    
    <!-- Modal Template -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-details"></div>
        </div>
    </div>


    <div id="addAppointmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="center-text">Schedule an Appointment</h2>
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
                    <label for="override_gap" class="small-text">Override 10-minute gap:</label>
                    <input type="checkbox" id="override_gap" name="override_gap" class="small-text-checkbox"><br>

                    <div class="nav-buttons">
                        <input type="submit" value="Schedule Appointment">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
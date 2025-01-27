<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Calendar</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="Calendar.css">
    <script src="Calendar.js" defer></script>
</head>
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
    <button type="button" onclick="window.history.back()">Cancel</button>
    </div>

</html>
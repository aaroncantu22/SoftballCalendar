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

        // Fetch the current details of the appointment
        $query = "SELECT * FROM appointments WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($appointment) {
            ?>

            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Edit Appointment</title>
            </head>
            <body>
                <h2>Edit Appointment</h2>
                <form action="update_appointment.php" method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($appointment['id']); ?>">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($appointment['name']); ?>" required><br>

                    <label for="lesson_type">Lesson Type:</label>
                    <select id="lesson_type" name="lesson_type" required>
                        <option value="Pitching for pitchers" <?php echo ($appointment['lesson_type'] == 'Pitching for pitchers') ? 'selected' : ''; ?>>Pitching for pitchers</option>
                        <option value="Hitting" <?php echo ($appointment['lesson_type'] == 'Hitting') ? 'selected' : ''; ?>>Hitting</option>
                        <option value="Fielding" <?php echo ($appointment['lesson_type'] == 'Fielding') ? 'selected' : ''; ?>>Fielding</option>
                        <option value="Catching for catchers" <?php echo ($appointment['lesson_type'] == 'Catching for catchers') ? 'selected' : ''; ?>>Catching for catchers</option>
                        <option value="Basic throwing" <?php echo ($appointment['lesson_type'] == 'Basic throwing') ? 'selected' : ''; ?>>Basic throwing</option>
                        <option value="Basic catching" <?php echo ($appointment['lesson_type'] == 'Basic catching') ? 'selected' : ''; ?>>Basic catching</option>
                        <option value="Baserunning" <?php echo ($appointment['lesson_type'] == 'Baserunning') ? 'selected' : ''; ?>>Baserunning</option>
                    </select><br>

                    <label for="payment">Payment:</label>
                    <input type="text" id="payment" name="payment" value="<?php echo htmlspecialchars($appointment['payment']); ?>" required><br>

                    <label for="cost">Cost:</label>
                    <input type="number" id="cost" name="cost" value="<?php echo htmlspecialchars($appointment['cost']); ?>" required><br>

                    <label for="notes">Notes:</label>
                    <input type="text" id="notes" name="notes" value="<?php echo htmlspecialchars($appointment['notes']); ?>"><br>

                    <label for="appointment_date">Appointment Date:</label>
                    <input type="datetime-local" id="appointment_date" name="appointment_date" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($appointment['appointment_date']))); ?>" required><br>

                    <label for="duration">Duration (minutes):</label>
                    <select id="duration" name="duration" required>
                        <option value="45" <?php echo ($appointment['duration'] == 45) ? 'selected' : ''; ?>>45 minutes</option>
                        <option value="60" <?php echo ($appointment['duration'] == 60) ? 'selected' : ''; ?>>60 minutes</option>
                    </select><br>
                    
                    <input type="submit" value="Update Appointment">
                </form>
                <button onclick="window.location.href='Calendar.php'">Back to Calendar</button>
            </body>
            </html>

            <?php
        } else {
            echo "No appointment found with the provided ID.";
        }
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    echo "No appointment ID provided.";
}
?>
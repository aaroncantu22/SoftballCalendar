<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "manicquail735";

try {
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all appointments
    $query = "SELECT id, name, lesson_type, payment, cost, (payment - cost) AS credit, notes, appointment_date, duration FROM appointments";
    $stmt = $conn->query($query);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Calendar</h2>";
    echo "<table border='1'>";
    echo "<tr>
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
        echo "<tr>";
        echo "<td>" . htmlspecialchars($appointment["appointment_date"]) . "</td>";
        echo "<td><a href='fetch_appointment.php?appointment_id=" . htmlspecialchars($appointment["id"]) . "'>" . htmlspecialchars($appointment["name"]) . "</a></td>";
        echo "<td>" . htmlspecialchars($appointment["lesson_type"]) . "</td>";
        echo "<td>$" . htmlspecialchars($appointment["payment"]) . "</td>";
        echo "<td>$" . htmlspecialchars($appointment["cost"]) . "</td>";
        echo "<td>$" . htmlspecialchars($appointment["credit"]) . "</td>";
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
    <title>Multi-Year Calendar with Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .calendar-container {
            margin: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 900px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #007BFF;
            color: white;
        }

        .month-name {
            font-size: 2em;
        }

        .calendar-body {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1.2em;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        th {
            background-color: #f4f4f4;
        }

        .nav-buttons {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .nav-buttons button {
            margin: 0 10px;
            padding: 15px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }

        .nav-buttons button:hover {
            background-color: #0056b3;
        }

        .jump-to {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .jump-to input {
            margin: 0 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            width: 150px;
            text-align: center;
        }

        .jump-to button {
            padding: 15px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }

        .jump-to button:hover {
            background-color: #0056b3;
        }

        .appointment-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
        }

        .appointment-form input, .appointment-form select, .appointment-form textarea {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            width: 300px;
        }

        .appointment-form button {
            padding: 15px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }

        .appointment-form button:hover {
            background-color: #0056b3;
        }

        .appointments {
            margin: 20px 0;
            width: 80%;
            max-width: 900px;
        }

        .appointment {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            background-color: white;
        }
    </style>
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
        <input type="text" id="lesson_type" name="lesson_type" required><br>

        <label for="payment">Payment:</label>
        <input type="text" id="payment" name="payment" required><br>

        <label for="cost">Cost:</label>
        <input type="number" id="cost" name="cost" required><br>

        <label for="notes">Notes:</label>
        <input type="text" id="notes" name="notes"><br>

        <label for="appointment_date">Appointment Date:</label>
        <input type="datetime-local" id="appointment_date" name="appointment_date" required><br>

        <label for="duration">Duration (minutes):</label>
        <input type="number" id="duration" name="duration" placeholder="45min or 1hr?" required><br>
        
        <input type="submit" value="Schedule Appointment">
    </form>

<script>
    const startYear = 2024;
    const startMonth = 7; // August
    const endYear = 2026;
    const endMonth = 11; // December

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    let currentYear = startYear;
    let currentMonth = startMonth;
    let appointments = [];

    async function fetchAppointments() {
        const response = await fetch('get_appointments.php');
        const data = await response.json();
        appointments = data.reduce((acc, appt) => {
            const dateKey = new Date(appt.appointment_date).toISOString().split('T')[0];
            if (!acc[dateKey]) {
                acc[dateKey] = [];
            }
            acc[dateKey].push(appt);
            return acc;
        }, {});
        generateCalendar(currentYear, currentMonth);
    }

    function generateCalendar(year, month) {
        const calendarContainer = document.getElementById('calendar');
        calendarContainer.innerHTML = '';

        const calendarHeader = document.createElement('div');
        calendarHeader.className = 'calendar-header';
        const monthName = document.createElement('div');
        monthName.className = 'month-name';
        monthName.innerText = `${monthNames[month]} ${year}`;
        calendarHeader.appendChild(monthName);

        const navButtons = document.createElement('div');
        navButtons.className = 'nav-buttons';
        const prevButton = document.createElement('button');
        prevButton.innerText = '<';
        prevButton.onclick = showPreviousMonth;
        const nextButton = document.createElement('button');
        nextButton.innerText = '>';
        nextButton.onclick = showNextMonth;
        navButtons.appendChild(prevButton);
        navButtons.appendChild(nextButton);

        calendarHeader.appendChild(navButtons);
        calendarContainer.appendChild(calendarHeader);

        const calendarBody = document.createElement('div');
        calendarBody.className = 'calendar-body';

        const table = document.createElement('table');

        const thead = document.createElement('thead');
        const theadRow = document.createElement('tr');
        daysOfWeek.forEach(day => {
            const th = document.createElement('th');
            th.innerText = day;
            theadRow.appendChild(th);
        });
        thead.appendChild(theadRow);

        const tbody = document.createElement('tbody');
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let date = 1;
        let row = document.createElement('tr');
        for (let i = 0; i < 7; i++) {
            if (i < firstDay) {
                const cell = document.createElement('td');
                cell.innerText = '';
                row.appendChild(cell);
            } else if (date <= daysInMonth) {
                const cell = document.createElement('td');
                cell.innerText = date;
                const dateKey = `${year}-${month + 1}-${date}`;
                if (appointments[dateKey] && appointments[dateKey].length > 0) {
                    cell.className = 'has-appointments';
                    cell.onclick = () => showAppointmentDetails(dateKey, date);
                }
                row.appendChild(cell);
                date++;
            }
        }
        tbody.appendChild(row);

        while (date <= daysInMonth) {
            row = document.createElement('tr');
            for (let i = 0; i < 7; i++) {
                if (date <= daysInMonth) {
                    const cell = document.createElement('td');
                    cell.innerText = date;
                    const dateKey = `${year}-${month + 1}-${date}`;
                    if (appointments[dateKey] && appointments[dateKey].length > 0) {
                        cell.className = 'has-appointments';
                        cell.onclick = () => showAppointmentDetails(dateKey, date);
                    }
                    row.appendChild(cell);
                    date++;
                } else {
                    const cell = document.createElement('td');
                    cell.innerText = '';
                    row.appendChild(cell);
                }
            }
            tbody.appendChild(row);
        }

        table.appendChild(thead);
        table.appendChild(tbody);
        calendarBody.appendChild(table);
        calendarContainer.appendChild(calendarBody);
    }

    function showPreviousMonth() {
        if (currentMonth === 0) {
            currentMonth = 11;
            currentYear--;
        } else {
            currentMonth--;
        }
        generateCalendar(currentYear, currentMonth);
    }

    function showNextMonth() {
        if (currentMonth === 11) {
            currentMonth = 0;
            currentYear++;
        } else {
            currentMonth++;
        }
        generateCalendar(currentYear, currentMonth);
    }

    function jumpToMonth() {
        const month = parseInt(document.getElementById('jump-month').value, 10) - 1;
        const year = parseInt(document.getElementById('jump-year').value, 10);

        if (month >= 0 && month <= 11 && year >= startYear && year <= endYear) {
            currentMonth = month;
            currentYear = year;
            generateCalendar(currentYear, currentMonth);
        } else {
            alert('Please enter a valid month and year.');
        }
    }

    function showAppointments(year, month, day) {
        const appointmentContainer = document.getElementById('appointments');
        appointmentContainer.innerHTML = '';

        const dateKey = `${year}-${month + 1}-${day}`;
        if (appointments[dateKey]) {
            appointments[dateKey].forEach(appt => {
                const apptDiv = document.createElement('div');
                apptDiv.innerHTML = `
                    <p>Name: ${appt.name}</p>
                    <p>Lesson Type: ${appt.lesson_type}</p>
                    <p>Payment: $${appt.payment.toFixed(2)}</p>
                    <p>Cost: $${appt.cost.toFixed(2)}</p>
                    <p>Credit: $${appt.credit.toFixed(2)}</p>
                    <p>Notes: ${appt.notes}</p>
                    <p>Date: ${appt.appointment_date.split(' ')[0]}</p>
                    <p>Time: ${appt.start_time} for ${appt.duration} minutes</p>
                `;
                appointmentContainer.appendChild(apptDiv);
            });
        } else {
            appointmentContainer.innerHTML = '<p>No appointments for this day.</p>';
        }
    }

    function showAppointmentDetails(dateKey, name) {
        const appointmentContainer = document.getElementById('appointments');
        appointmentContainer.innerHTML = '';

        if (appointments[dateKey]) {
            const appointment = appointments[dateKey].find(appt => appt.name === name);
            if (appointment) {
                const apptDiv = document.createElement('div');
                apptDiv.innerHTML = `
                    <p>Name: ${appointment.name}</p>
                    <p>Lesson Type: ${appointment.lesson_type}</p>
                    <p>Payment: $${appointment.payment.toFixed(2)}</p>
                    <p>Cost: $${appointment.cost.toFixed(2)}</p>
                    <p>Credit: $${appointment.credit.toFixed(2)}</p>
                    <p>Notes: ${appointment.notes}</p>
                    <p>Date: ${appointment.appointment_date.split(' ')[0]}</p>
                    <p>Time: ${appointment.start_time} for ${appointment.duration} minutes</p>
                `;
                appointmentContainer.appendChild(apptDiv);
            }
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await fetchAppointments();
        generateCalendar(currentYear, currentMonth);
    });
</script>
</body>
</html>
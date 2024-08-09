document.addEventListener('DOMContentLoaded', function() {
    // Event listener for day buttons
    document.querySelectorAll('.day-button').forEach(button => {
        button.addEventListener('click', function() {
            const date = this.getAttribute('data-date');
            window.location.href = `daily_calendar.php?date=${date}`;
        });
    });

    // Event listener for appointment links
    document.querySelectorAll(".rappointment-link").forEach(link => {
        link.addEventListener("click", (event) => {
            event.preventDefault();
            showModal(event.target.getAttribute("data-appointment"));
        });
    });

    // Modal handling
    const modal = document.getElementById("appointmentModal");
    const modalDetails = document.getElementById("modal-appointment-details");
    const closeModal = document.getElementsByClassName("close")[0];

    function showModal(details) {
        const appointment = JSON.parse(details);
        modalDetails.innerHTML = `
            <strong>Name:</strong> ${appointment.name}<br>
            <strong>Lesson Type:</strong> ${appointment.lesson_type}<br>
            <strong>Payment:</strong> $${appointment.payment}<br>
            <strong>Cost:</strong> $${appointment.cost}<br>
            <strong>Credit:</strong> $${appointment.credit}<br>
            <strong>Notes:</strong> ${appointment.notes}<br>
            <strong>Duration:</strong> ${appointment.duration} minutes
        `;
        modal.style.display = "block";
    }

    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
});

function print_DailyCalendar() {
    // Extract the daily calendar content
    var dailyCalendar = document.querySelector('.daily-calendar');
    var dateHeader = dailyCalendar.querySelector('h2').textContent;
    var calendarTable = dailyCalendar.querySelector('table.calendar-table').outerHTML;

    // Open a new window for printing
    var newWin = window.open('', '', 'height=600,width=1000');
    newWin.document.write('<html><head><title>Print Daily Calendar</title>');
    newWin.document.write('<style>');
    newWin.document.write('body { font-family: Arial, sans-serif; }');
    newWin.document.write('table { width: 100%; border-collapse: collapse; }');
    newWin.document.write('h1 { font-size: 15px; text-align: center; }'); // Adjust the header size
    newWin.document.write('th, td { padding: 8px; text-align: center; border: 1px solid #ddd; }');
    newWin.document.write('.appointment { background-color: none; font-size: small; color: black; padding: 0; margin: 0; border-radius: 0; width: auto; display: block; }'); // Minimize appointment box styling
    newWin.document.write('.rappointment-link { color: black; text-decoration: none; }'); // Update link style for printing
    newWin.document.write('.rappointment-link:hover { text-decoration: none; }'); // Remove hover effect on print
    newWin.document.write('button, .nav-buttons, form { display: none; }'); // Hide buttons, navigation, and form when printing
    newWin.document.write('</style>');
    newWin.document.write('</head><body>');
    newWin.document.write('<h1>' + dateHeader + '</h1>');
    newWin.document.write(calendarTable);
    newWin.document.write('</body></html>');
    newWin.document.close();
    newWin.print();
}

function redirect2Calendar() {
    location.href = 'Calendar.php';
}

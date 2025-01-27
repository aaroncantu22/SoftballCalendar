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

    // Event listener for add appointment button
    document.querySelectorAll('.add-button').forEach(button => {
        button.addEventListener('click', () => {
            showAddAppointmentModal();
        });
    });

    // Modal handling for appointment details
    const modal = document.getElementById("appointmentModal");
    const closeModal = modal.querySelector(".close");

    function showModal(details) {
        const appointment = JSON.parse(details);
        const modalDetails = document.getElementById("modal-appointment-details");
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

    // Close the modal when the "X" button is clicked
    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    // Close the modal when clicking outside of it
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
});

// Show the "Add Appointment" modal
function showAddAppointmentModal() {
    const addAppointmentModal = document.getElementById("addAppointmentModal");
    addAppointmentModal.style.display = "block";

    // Close modal functionality
    const closeModal = addAppointmentModal.querySelector(".close");
    closeModal.addEventListener("click", () => {
        addAppointmentModal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target == addAppointmentModal) {
            addAppointmentModal.style.display = "none";
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    // Event listener for all delete buttons
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-id');
            const date = this.getAttribute('data-date');

            if (confirm("Are you sure you want to delete this appointment?")) {
                // Redirect to delete_appointment.php with appointment ID and date
                window.location.href = `daily_delete_appointment.php?id=${appointmentId}&date=${date}`;
            }
        });
    });
});
// Edit button event listener
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-button').forEach(function(button) {
        button.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-id');
            const appointmentDate = this.getAttribute('data-date');
            window.location.href = `daily_edit_appointment.php?appointment_id=${appointmentId}&date=${appointmentDate}`;
        });
    });
});

function print_DailyCalendar() {
    // Extract the daily calendar content
    var dailyCalendar = document.querySelector('.daily-calendar');
    var dateHeader = dailyCalendar.querySelector('h2').textContent;
    var calendarTable = dailyCalendar.querySelector('table.calendar-table').outerHTML;

    // Remove <br> tags from the calendarTable to avoid extra whitespace
    calendarTable = calendarTable.replace(/<br\s*\/?>/gi, '');

    // Open a new window for printing
    var newWin = window.open('', '', 'height=600,width=1000');
    newWin.document.write('<html><head><title>Print Daily Calendar</title>');
    newWin.document.write('<style>');
    newWin.document.write('body { font-family: Arial, sans-serif; margin: 0; padding: 0; }');
    newWin.document.write('table { width: 100%; border-collapse: collapse; margin: 0; padding: 0; }');
    newWin.document.write('h1 { font-size: 18px; text-align: center; margin: 0; padding: 0; }'); // Adjust header size
    newWin.document.write('th, td { padding: 4px; text-align: center; border: 1px solid #ddd; margin: 0; }'); // Reduce padding
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


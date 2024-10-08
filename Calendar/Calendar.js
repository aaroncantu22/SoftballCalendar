// Save scroll position before navigation
function saveScrollPosition() {
    localStorage.setItem('scrollPosition', window.scrollY);
}

// Restore scroll position after page loads
function restoreScrollPosition() {
    const savedPosition = localStorage.getItem('scrollPosition');
    if (savedPosition !== null) {
        window.scrollTo(0, parseInt(savedPosition, 10));
        localStorage.removeItem('scrollPosition');
    }
}

function confirmDelete() {
    return confirm("Do you really want to delete this appointment?");
}

function printTable() {
    var tableContent = document.getElementById('appointmentsTable').outerHTML;
    var newWin = window.open('', '', 'height=500,width=700');
    newWin.document.write('<html><head><title>Print Appointments</title>');
    newWin.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }</style>');
    newWin.document.write('</head><body>');
    newWin.document.write(tableContent);
    newWin.document.write('</body></html>');
    newWin.document.close();
    newWin.print();
}
function printCalendar() {
    var calendarBody = document.querySelector('.calendar-body').innerHTML;
    var monthYear = document.querySelector('.month-name').textContent;
    var newWin = window.open('', '', 'height=800,width=1000');
    newWin.document.write('<html><head><title>Print Calendar</title>');
    newWin.document.write('<style>');
    newWin.document.write('body { font-family: Arial, sans-serif; }');
    newWin.document.write('table { width: 100%; border-collapse: collapse; }');
    newWin.document.write('th, td { padding: 8px; text-align: center; border: 1px solid #ddd; }');
    newWin.document.write('.holiday-link { color: red; }');
    newWin.document.write('.appointment-link { color: blue; }');
    newWin.document.write('.day-button { background: none; border: none; color: inherit; font: inherit; cursor: pointer; text-decoration: none; padding: 0;}');
    newWin.document.write('</style>');
    newWin.document.write('</head><body>');
    newWin.document.write('<h1>Calendar for ' + monthYear + '</h1>');
    newWin.document.write(calendarBody);
    newWin.document.write('</body></html>');
    newWin.document.close();
    newWin.print();
    
}

function showPreviousMonth() {
    navigateMonth(-1);
}

function showNextMonth() {
    navigateMonth(1);
}

function navigateMonth(direction) {
    saveScrollPosition(); // Save scroll position before navigation

    const params = new URLSearchParams(window.location.search);
    const currentMonth = parseInt(params.get('month') || new Date().getMonth() + 1);
    const currentYear = parseInt(params.get('year') || new Date().getFullYear());

    let newMonth = currentMonth + direction;
    let newYear = currentYear;

    if (newMonth < 1) {
        newMonth = 12;
        newYear--;
    } else if (newMonth > 12) {
        newMonth = 1;
        newYear++;
    }

    // Update the URL with the new month and year
    location.href = `Calendar.php?month=${newMonth}&year=${newYear}`;
}

function jumpToMonth() {
    saveScrollPosition(); // Save scroll position before navigation
    const month = document.getElementById('jump-month').value;
    const year = document.getElementById('jump-year').value;
    window.location.href = `Calendar.php?month=${month}&year=${year}`;
    return false;
}

// Check URL parameters and trigger jump-to button click if needed
document.addEventListener('DOMContentLoaded', () => {
    // Restore scroll position when the page loads
    restoreScrollPosition();

    // Event listeners for buttons
    document.querySelector('.nav-buttons button[onclick="showPreviousMonth()"]').addEventListener('click', () => {
        navigateMonth(-1);
    });

    document.querySelector('.nav-buttons button[onclick="showNextMonth()"]').addEventListener('click', () => {
        navigateMonth(1);
    });

    document.querySelector('.jump-to button').addEventListener('click', () => {
        jumpToMonth();
    });

    const modal = document.getElementById("appointmentModal");
    const span = document.getElementsByClassName("close")[0];

    document.querySelectorAll('.appointment-link').forEach(item => {
        item.addEventListener('click', event => {
            const appointment = JSON.parse(event.target.getAttribute('data-appointment'));
            const details = `
                <strong>Date:</strong> ${appointment.date}<br>
                <strong>Time:</strong> ${appointment.start_time} - ${appointment.end_time}<br>
                <strong>Name:</strong> ${appointment.name}<br>
                <strong>Lesson Type:</strong> ${appointment.lesson_type}<br>
                <strong>Payment:</strong> $${appointment.payment}<br>
                <strong>Cost:</strong> $${appointment.cost}<br>
                <strong>Credit:</strong> $${appointment.credit}<br>
                <strong>Notes:</strong> ${appointment.notes}<br>
                <strong>Duration:</strong> ${appointment.duration} minutes
            `;
            document.getElementById('modal-details').innerHTML = details;
            modal.style.display = "block";
        });
    });

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

function redirect2Calendar(){
    location.href = 'Calendar.php'
}
function redirect2Tables(){
    location.href = 'Tables.php'
}
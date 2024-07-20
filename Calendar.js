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
function saveAsPDF() {
    window.location.href = 'save_as_pdf.php';
}

function saveAsExcel() {
    window.location.href = 'save_as_excel.php';
}

function saveAsWord() {
    window.location.href = 'save_as_word.php';
}
  // Modal functionality
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById("appointmentModal");
    const span = document.getElementsByClassName("close")[0];
    document.querySelectorAll('.appointment-link').forEach(item => {
        item.addEventListener('click', event => {
            const appointment = JSON.parse(event.target.getAttribute('data-appointment'));
             console.log(appointment);
             const details = `
                <strong>Name:</strong> ${appointment.name}<br>
                <strong>Lesson Type:</strong> ${appointment.lesson_type}<br>
                <strong>Payment:</strong> $${appointment.payment}<br>
                <strong>Cost:</strong> $${appointment.cost}<br>
                <strong>Credit:</strong> $${appointment.credit}<br>
                <strong>Notes:</strong> ${appointment.notes}<br>
                <strong>Duration:</strong> ${appointment.duration} minutes
            `;
            const Id = `edit_calendar_appointment.php?appointment_id=${appointment.Id}`;
            const ID = `delete_calendar_appointment.php?appointment_id=${appointment.ID}`;
            document.getElementById('modal-details').innerHTML = details;
            const link = document.getElementById('edit-Button');
            const Dlink = document.getElementById('delete-Button');
            link.href = Id;
            Dlink.href = ID;
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

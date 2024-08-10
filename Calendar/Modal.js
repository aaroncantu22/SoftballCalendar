  // Modal functionality
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById("appointmentModal");
    const span = document.getElementsByClassName("close")[0];
    document.querySelectorAll('.appointment-link').forEach(item => {
        item.addEventListener('click', event => {
            const appointment = JSON.parse(event.target.getAttribute('data-appointment'));
            const details = `
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

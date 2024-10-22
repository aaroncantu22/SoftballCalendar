Softball Calendar Appointment System

Overview:

The Softball Calendar Appointment System is a PHP-based application designed to manage and display appointments. It features a calendar interface for visualizing appointments, a table view for detailed appointment information, and functionality for adding, editing, and deleting appointments. The system connects to a MySQL database to store and retrieve appointment data and default settings. This version, 1.8, includes bug fixes, Google Calendar integration, and new features.

Features:

Calendar View: Displays appointments in a calendar format with navigation controls to view different months and years. Holidays are also marked.
Appointment Tables: Provides a tabular view of all appointments, allowing users to view, edit, or delete them.
Default Settings: Allows users to set default month and year for the calendar view.
Appointment Management: Users can schedule new appointments with details such as name, lesson type, payment, cost, and duration.
Daily Calendar View: Displays appointments for a specific day on a daily calendar overview that could also be saved onto google calendar. 

New Features in Version 1.8:


Google Calendar Integration: Users can now save all appointments from the daily calendar view to their Google Calendar.

A "Save to Google Calendar" button is available, allowing users to export their appointments with a single click.
The account used for saving is the one signed in through the Google OAuth2 flow.
Users will receive confirmation messages for successful saves or errors if the save fails.
Other Enhancements: Includes buttons for adding, editing, and deleting appointments directly from the daily calendar.

Files: Coming soon!

php
Copy code
$dsn = "mysql:host=your_dsn&portnumber; dbname=calendar_db";
$dbusername = "root";
$dbpassword = "your_password"; //using password (yes)

Installation
Setup Environment:
Install XAMPP and ensure Apache and MySQL services are running.
File Placement:
Place daily_calendar.php, Calendar.php, Tables.php, and associated files in the htdocs directory of your XAMPP installation.
Dependencies:
Google Cloud Console: You will need to obtain a client_secret.json file from your Google Cloud Console project with the appropriate credentials for OAuth2.
Composer: Install Composer and use the vendor folder from Composer along with composer.json and composer.lock files.
Run the following command in your project directory:

bash
Copy code
composer install
Usage
Accessing the Application:
Daily Calendar View: Navigate to http://localhost/daily_calendar.php to access the daily calendar view.
Monthly Calendar View: Navigate to http://localhost/Calendar/Calendar.php for the monthly calendar view.
Appointment Table View: Navigate to http://localhost/Calendar/Tables.php to view appointments in a tabular format.
Managing Appointments:
Add Appointment: Use the "Add Appointment" button next to a time slot to open the modal for scheduling a new appointment.
Edit Appointment: Use the "Edit" button next to an existing appointment to modify its details.
Delete Appointment: Use the "Delete" button next to an appointment to remove it.
Save Appointments to Google Calendar:
To save your daily appointments, click the "Save to Google Calendar" button available on the daily calendar view.
Ensure you're logged in with your Google account via the OAuth2 authorization (handled in oauth2callback.php).
Upon clicking the button, appointments will be transferred to your Google Calendar, with a message confirming success or showing any errors.
Time Gap Settings:
Adjust the time gap between slots using the input form and click "Update" to apply changes.
Printing:
Click the "Print Daily Calendar" button to print the current view of the daily calendar.
Contact
For any questions or issues, please contact aaroncantu227316@mail.fresnostate.edu.

License
This project is licensed under the MIT License. See the LICENSE file for details.

Let me know if you need further modifications!

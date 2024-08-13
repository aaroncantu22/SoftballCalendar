Softball Calendar Appointment System
Overview
The Softball Calendar Appointment System is a PHP-based application designed to manage and display appointments. It features a calendar interface for visualizing appointments, a table view for detailed appointment information, and functionality for adding, editing, and deleting appointments. The system connects to a MySQL database to store and retrieve appointment data and default settings. This version, 1.7, includes bug fixes and new features.

Features
Calendar View: Displays appointments in a calendar format with navigation controls to view different months and years. Holidays are also marked.
Appointment Tables: Provides a tabular view of all appointments, allowing users to view, edit, or delete them.
Default Settings: Allows users to set default month and year for the calendar view.
Appointment Management: Users can schedule new appointments with details such as name, lesson type, payment, cost, and duration.
New Features in Version 1.7: Includes buttons for adding, editing, and deleting appointments directly from the daily calendar.
Files
daily_calendar.php: Main file for displaying the daily calendar view. Includes functionality for viewing, managing, and displaying appointments. Features time gap customization and appointment merging logic.
Calendar.php: Main file for displaying the calendar view with navigation and default month/year settings. Displays holidays and appointments for the selected month and year.
Tables.php: Provides a tabular view of all appointments with functionality to view, edit, and delete appointments.
Daily_Calendar.css: CSS file for styling the daily calendar interface.
Daily_Calendar.js: JavaScript file for handling dynamic interactions and calendar functionalities.
Modal.css: CSS file for modal styling used in the appointment management interface.
Modal.js: JavaScript file for modal functionalities, including opening and closing modals.
login_Page.html: Login page for user authentication.
daily_add_appointment.php: Script for adding new appointments.
SoftballCalendar.jpeg and SoftballCalendar2.jpeg: Images used in the calendar interface.
favicon.ico: Icon for the application.
Database Setup
Create Database:

Create a MySQL database named calendar_db.
Import Schema:

Import the required schema and data into the database (details not provided in this project). The database should include tables named appointments and default_settings.
Configuration:

Update the database connection details in daily_calendar.php, Calendar.php, and Tables.php with your MySQL server credentials:
php
Copy code
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "your_password";
Installation
Setup Environment:

Install XAMPP and ensure Apache and MySQL services are running.
File Placement:

Place daily_calendar.php, Calendar.php, Tables.php, and associated files in the htdocs directory of your XAMPP installation.
Usage
Accessing the Application:

Open your browser and navigate to http://localhost/daily_calendar.php to access the daily calendar view.
Navigate to http://localhost/Calendar.php for the monthly calendar view.
Navigate to http://localhost/Tables.php to view appointments in a tabular format.
Login:

Users must log in to access the calendar. If not logged in, they will be redirected to login_Page.html.
Managing Appointments:

Add Appointment: Use the "Add Appointment" button next to a time slot to open the modal for scheduling a new appointment.
Edit Appointment: Use the "Edit" button next to an existing appointment to modify its details.
Delete Appointment: Use the "Delete" button next to an appointment to remove it.
Time Gap Settings:

Adjust the time gap between slots using the input form and click "Update" to apply changes.
Printing:

Click the "Print Daily Calendar" button to print the current view of the daily calendar.
Dependencies
FontAwesome: Used for icons. Ensure you have an internet connection or host the FontAwesome files locally.
Notes
Ensure your web server supports PHP and is configured to work with MySQL.
Set correct permissions for the PHP files and database access.
License
This project is licensed under the MIT License. See the LICENSE file for details.

Contact
For any questions or issues, please contact [aaroncantu227316@mail.fresnostate.edu].

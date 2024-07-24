This project was made with XAMMP and MyPHPAdmin.

Softball Calendar Appointment System

Overview
The Calendar Appointment System is a PHP-based application designed to manage and display appointments. It features a calendar interface for visualizing appointments, a table view for detailed appointment information, and functionality for adding, editing, and deleting appointments. The system connects to a MySQL database to store and retrieve appointment data and default settings.

Features
Calendar View: Displays appointments in a calendar format with navigation controls to view different months and years. Holidays are also marked.
Appointment Tables: Provides a tabular view of all appointments, allowing users to view, edit, or delete them.
Default Settings: Allows users to set default month and year for the calendar view.
Appointment Management: Users can schedule new appointments with details such as name, lesson type, payment, cost, and duration.
Files
Calendar.php

Main file for displaying the calendar view.
Includes functionality for viewing, navigating, and setting default month and year.
Displays holidays and appointments for the selected month and year.

Tables.php

Provides a tabular view of all appointments.
Includes functionality to view, edit, and delete appointments.
Allows for appointment scheduling similar to the calendar view.
Database Connection

Both Calendar.php and Tables.php connect to a MySQL database using PDO.
CSS and JavaScript Files

Calendar.css: Styles for the calendar and appointment form.
Calendar.js: JavaScript functions for calendar navigation and default settings management.
Modal.css and Modal.js: Styles and functionality for modals used in the calendar view.
Setup
Database Setup

Create a MySQL database named calendar_db. With tables: "appointments" & "default_settings".
Import the required schema and data into the database (not provided in this project).
Configuration

Update the database connection details in Calendar.php and Tables.php with your MySQL server credentials:
php
Copy code
$dsn = "mysql:host=127.0.0.1:3307;dbname=calendar_db";
$dbusername = "root";
$dbpassword = "your_password";
Running the Project

Place Calendar.php and Tables.php in your web server's document root.
Access the application through your web browser by navigating to the corresponding URL.
Dependencies

The project includes FontAwesome for icons. Ensure you have an internet connection or host the FontAwesome files locally.
Usage
View Calendar: Navigate to Calendar.php to view and interact with the calendar.
View Appointments: Navigate to Tables.php to see all appointments in a tabular format.
Add/Edit Appointments: Use the forms provided in both Calendar.php and Tables.php to schedule or modify appointments.
Notes
Ensure your web server supports PHP and is configured to work with MySQL.
Make sure to set correct permissions for the PHP files and database access.
License
This project is licensed under the MIT License. See the LICENSE file for details.


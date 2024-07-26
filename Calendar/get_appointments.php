<?php
function getAppointments($month, $year) {
    global $dsn, $dbusername, $dbpassword;
    $pdo = new PDO($dsn, $dbusername, $dbpassword);

    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE MONTH(appointment_date) = :month AND YEAR(appointment_date) = :year");
    $stmt->execute(['month' => $month, 'year' => $year]);

    $appointments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $date = (int)date('j', strtotime($row['appointment_date']));
        $startTime = date('H:i', strtotime($row['appointment_date']));
        $endTime = date('H:i', strtotime($row['appointment_date'] . ' + ' . $row['duration'] . ' minutes'));
        $appointments[$date][] = [
            'date' => date('m-d-Y', strtotime($row['appointment_date'])),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'name' => $row['name'],
            'lesson_type' => $row['lesson_type'],
            'payment' => $row['payment'],
            'cost' => $row['cost'],
            'credit' => $row['credit'],
            'notes' => $row['notes'],
            'duration' => $row['duration']
        ];
    }

    foreach ($appointments as $date => $apptArray) {
        usort($appointments[$date], function($a, $b) {
            return strcmp($a['start_time'], $b['start_time']);
        });
    }

    return $appointments;
}
?>

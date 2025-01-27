<?php
session_start();

// Connect to the database
$dsn = "mysql:host=127.0.0.1;port=3307;dbname=loginaccounts";
$dbusername = "root";
$dbpassword = "manicquail735";

try {
    // Database connection
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Corrected superglobal names
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE userName = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Username found, check the password
        $hashedPasswordFromDB = $result['userPassword'];

        if (password_verify($password, $hashedPasswordFromDB)) {
            // Set the session variable
            $_SESSION['user'] = $username;
            header("Location: Calendar.php");
            exit();
        }
    }

    // Username or password not found
    // Display error message
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Result</title>
    <link rel="stylesheet" href="loginResult.css" />
    <style>
        body {
            background-color: #f4f4f4; /* Background color to match Calendar.css */
            color: #000000; /* Black text color */
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .result-container {
            text-align: center;
            padding: 20px;
            background-color: #007BFF; /* Blue background to match Calendar.css */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .error-message {
            color: #ffffff; /* White text color */
            font-size: 18px;
            margin-bottom: 20px;
        }

        .back-to-login {
            text-decoration: none;
            color: #ffffff; /* White text color */
            font-size: 16px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #0056b3; /* Darker blue background color */
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-to-login:hover {
            background-color: #003f7f; /* Darker blue on hover */
        }
    </style>
</head>
<body>

<div class="result-container">
    <p class="error-message">Username or Password does not exist. Try Again</p>
    <a class="back-to-login" href="login_Page.html">Back to Login</a>
</div>

</body>
</html>

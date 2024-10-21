<?php
// Connect to the database
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=loginaccounts";
$dbusername = "root";
$dbpassword = "manicquail735";
// Corrected superglobal names
$username = $_POST['username'];
$password = $_POST['password'];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Database connection
    $conn = new PDO($dsn, $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if the username already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE userName = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Username already exists
        $existingUserError = "Username is already in use!";
    } else {
        // Insert new user into the database with the hashed password
        $stmt = $conn->prepare("INSERT INTO users (userName, userPassword) VALUES (:username, :hashedPassword)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':hashedPassword', $hashedPassword);
        $stmt->execute();
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup Result</title>
    <link rel="stylesheet" href="signSuccess.css" />
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

        .login_form_container {
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

        #back_to_signup_button,
        #create_account_button {
            text-decoration: none;
            color: #ffffff; /* White text color */
            font-size: 16px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #0056b3; /* Darker blue background color */
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        #back_to_signup_button:hover,
        #create_account_button:hover {
            background-color: #003f7f; /* Darker blue on hover */
        }
    </style>
</head>
<body>

<div class="login_form_container">
    <div class="login_form">
    <?php
    if (isset($existingUserError)) {
        // Display error message
        echo "<h2 class='error'>Signup Error</h2>";
        echo "<p class='error'>$existingUserError</p>";

        // Add a button to go back to the sign-up page
        echo "<form action='signup_page.html' method='get'>";
        echo "<button id='back_to_signup_button' type='submit'>Back to Sign-up</button>";
        echo "</form>";
    } else {
        echo "<h2>Signup Successful</h2>";
        echo "<p>User added successfully!</p>";

        // Add the "Go to Login" button inside the text box
        echo "<form action='login_Page.html' method='get'>";
        echo "<button id='create_account_button' type='submit'>Go to Login</button>";
        echo "</form>";
    }
    ?>
    </div>
</div>

</body>
</html>

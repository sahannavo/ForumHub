<?php
include "php/../db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Prepare the SQL query
    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "<p style='color: red; text-align: center;'>Error: Statement preparation failed.</p>";
    } else {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "s", $email);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Bind result variables
        mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);

        // Fetch the result
        if (mysqli_stmt_fetch($stmt)) {
            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Set session variables for the logged-in user
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["email"] = $email;
                header("Location:../index.php");
                exit();
            } else {
                echo "<p style='color: red; text-align: center;'>Invalid email or password.</p>";
            }
        } else {
            echo "<p style='color: red; text-align: center;'>Invalid email or password.</p>";
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<style>
    /* Reset and center the form */
body {
    margin: 0;
    padding: 0;
    background: #e0e5ec !important;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: 'Segoe UI', sans-serif;
}

/* Login form container */
form {
    background: #e0e5ec !important;
    border-radius: 20px;
    padding: 40px 30px;
    width: 320px;
     box-shadow: 10px 10px 30px #c0c3c9,
                -10px -10px 30px #ffffff !important;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Heading */
form h2 {
    font-size: 24px;
    margin-bottom: 30px;
    color: #444;
}

/* Neumorphic input fields */
form input {
    width: 100%;
    padding: 14px;
    margin: 10px 0;
    border: none;
    border-radius: 12px;
    background: #e0e5ec;
    box-shadow: inset 5px 5px 10px #bec4d0,
                inset -5px -5px 10px #ffffff;
    font-size: 14px;
    color: #333;
}

form input::placeholder {
    color: #aaa;
}

/* Neumorphic login button */
form button {
    margin-top: 20px;
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 12px;
    background: #e0e5ec;
    box-shadow: 5px 5px 10px #bec4d0,
                -5px -5px 10px #ffffff;
    color: #333;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
}

form button:hover {
    box-shadow: inset 3px 3px 6px #bec4d0,
                inset -3px -3px 6px #ffffff;
}

/* Additional text below the form */
form p {
    font-size: 14px;
    color: #555;
    margin-top: 20px;
}

form a {
    color: #0077cc;
    text-decoration: none;
}

</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login_signup.css">

</head>

<body>
    <form action="login.php" method="POST">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="signup.php">Signup</a></p>
    </form>
</body>

</html>
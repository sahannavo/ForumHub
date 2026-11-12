<?php
include "db.php";

// Initialize $x to avoid undefined variable error
$x = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //role
    $role = "user";
    // username
    $username = trim($_POST["username"]);
    // first name
    $fname = trim($_POST["fname"]);
    // last name
    $lname = trim($_POST["lname"]);
    // user's age
    $age = intval($_POST["age"]); // Convert age to an integer
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check for duplicate username or email
    $check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $check_stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($check_stmt, $check_sql)) {
        mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($result) > 0) {
            $x = true; // Duplicate found
        }

        mysqli_stmt_close($check_stmt);
    }

    // Insert new user if no duplicate found
    if ($x == false) {
        $sql = "INSERT INTO users (role, username, fname, lname, age, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmt, $sql)) {
            // Bind the $role variable along with other values
            mysqli_stmt_bind_param($stmt, "ssssiss", $role, $username, $fname, $lname, $age, $email, $password);

            if (mysqli_stmt_execute($stmt)) {
                echo "<a href='login.php'>Login here</a>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error: Statement preparation failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="login_signup.css">
    <style>
        /* General styles for both forms */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e0e5ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container for the form */
        form {
            background: #e0e5ec;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 10px 10px 30px #c0c3c9,
                -10px -10px 30px #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px;
        }

        form h2 {
            margin-bottom: 20px;
            color: #555;
        }

        /* Input fields */
        form input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 15px;
            background: #e0e5ec;
            box-shadow: inset 5px 5px 10px #c0c3c9,
                inset -5px -5px 10px #ffffff;
            font-size: 14px;
            color: #555;
        }

        form input:focus {
            outline: none;
        }

        /* Submit button */
        form button {
            margin-top: 15px;
            padding: 12px 25px;
            border: none;
            border-radius: 15px;
            background: #e0e5ec;
            box-shadow: 5px 5px 10px #c0c3c9,
                -5px -5px 10px #ffffff;
            font-size: 16px;
            color: #333;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        form button:hover {
            box-shadow: inset 2px 2px 5px #c0c3c9,
                inset -2px -2px 5px #ffffff;
        }

        /* Link text */
        form p {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        form a {
            color: #0077cc;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <form action="signup.php" method="POST">
        <h2>Signup</h2>
        <?php
        if ($x == true) {
            echo "<p style='color: red;'>Username or Email already exists. Please try again.</p>";
        }
        ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="fname" placeholder="First name" required>
        <input type="text" name="lname" placeholder="Last name" required>
        <input type="text" name="age" placeholder="Age" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Signup</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </form>
</body>

</html>
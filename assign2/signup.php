<?php
session_start();
require_once("settings.php");

// Initialize variables
$email = $profilename = $password = $confirm_password = "";
$email_error = $profile_error = $pass_error = $repass_error = $db_error = "";

// Regular expressions for validation
$profile_regex = "/^[a-zA-Z]+$/";
$pass_regex = "/^[a-zA-Z0-9]+$/";

// Function to sanitize input
function sanitise($x) {
    $data = trim($x);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate user input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = 0;

    // Email validation
    if (empty($_POST["email"])) {
        $email_error = "* Email address is required";
        $errors += 1;
    } else {
        $email = sanitise($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "* Please enter a valid email";
            $errors += 1;
        }
    }

    // Profile Name validation
    if (empty($_POST["profilename"])) {
        $profile_error = "* Profile Name is required";
        $errors += 1;
    } else {
        $profilename = sanitise($_POST["profilename"]);
        if (!preg_match($profile_regex, $profilename)) {
            $profile_error = "* Profile Name can only contain letters";
            $errors += 1;
        }
    }

    // Password validation
    if (empty($_POST["password"])) {
        $pass_error = "* Password is required";
        $errors += 1;
    } else {
        $password = sanitise($_POST["password"]);
        if (!preg_match($pass_regex, $password)) {
            $pass_error = "* Password can only contain letters and numbers";
            $errors += 1;
        }
    }

    // Confirm Password validation
    if (empty($_POST["confirm_password"])) {
        $repass_error = "* Password confirmation is required";
        $errors += 1;
    } else {
        $confirm_password = sanitise($_POST["confirm_password"]);
        if (strcmp($password, $confirm_password) != 0) {
            $repass_error = "* Password confirmation does not match";
            $errors += 1;
        }
    }

    // Connect to the database
    $conn = @mysqli_connect($host, $user, $pwd, $dbnm);
    if (!$conn) {
        $db_error = "<p>Unable to connect to the database. Please try again later!</p>";
    } else {
        // Check if email already exists
        $query_mail = "SELECT friend_email FROM friends WHERE friend_email = ?";
        $stmt = $conn->prepare($query_mail);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $email_error = "* This email is already registered";
            $errors += 1;
        }
        $stmt->close();

        // If no errors, insert new account
        if ($errors == 0) {
            $insert_query = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES (?, ?, ?, NOW(), 0)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sss", $email, $password, $profilename);
            if ($stmt->execute()) {
                // Set up session variables
                $_SESSION["profile_name"] = $profilename;
                $_SESSION["friend_id"] = mysqli_insert_id($conn);
                $_SESSION["num_of_friends"] = 0;
                $_SESSION["login_status"] = TRUE;
                header("Location: friendadd.php");
                exit();
            } else {
                $db_error = "<p>We are presently experiencing database difficulties. Please try again later!</p>";
            }
            $stmt->close();
        }
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Friends System - Sign Up</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="content">
        <h1 class="header">My Friends System</h1>
        <nav>
            <ul class="navigator">
                <li class="navlink"><a href="index.php">Home</a></li>
                <li class="navlink"><a href="signup.php">Sign-up</a></li>
                <li class="navlink"><a href="login.php">Log-In</a></li>
                <li class="navlink"><a href="about.php">About</a></li>
            </ul>
        </nav>
        <div class="signin-text">
            <h2>Registration Page</h2>
            <form action="signup.php" method="post">
                <?php
                // Display errors if any
                if ($email_error || $profile_error || $pass_error || $repass_error || $db_error) {
                    echo "<div class='content'><font color='red'>" . implode("<br>", array_filter([$email_error, $profile_error, $pass_error, $repass_error, $db_error])) . "</font></div>";
                }
                ?>
                <label for="email">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>

                <label for="profilename">Profile Name</label>
                <input name="profilename" value="<?php echo htmlspecialchars($profilename); ?>" required><br>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value=""><br>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" value=""><br>

                <div class="links">
                    <button type="submit" name="register">Register</button>
                    <button type="reset">Clear</button>
                </div>
            </form>
            <footer>
                <p><a href="index.php">Home Page</a></p>
                <div class="lastnote">
                    <p class="note"><span class="asterisk">*</span> All fields are required.</p>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>

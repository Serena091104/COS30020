<?php
session_start();

function sanitise($x)
{
    $data = trim($x);
    $data = stripslashes($x);
    $data = htmlspecialchars($x);
    return $data; // Return sanitized data
}

$_SESSION["login_status"] = FALSE;
$_SESSION["profile_name"] = $_SESSION["friend_id"] = "";
$email = $pass = $email_error = $pass_error = $db_error = "";
$pass_regex = "/^[a-zA-Z0-9]+$/";
$errors = []; // Initialize as an array

// Validate user account
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $email_error = "* Email address is required";
        $errors[] = $email_error; // Add to errors array
    } else {
        $email = sanitise($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "* Please enter a valid email";
            $errors[] = $email_error; // Add to errors array
        }
    }

    if (empty($_POST["password"])) {
        $pass_error = "* Password is required";
        $errors[] = $pass_error; // Add to errors array
    } else {
        $pass = sanitise($_POST["password"]);
        if (!preg_match($pass_regex, $pass)) {
            $pass_error = "* Password can only contain letters and numbers";
            $errors[] = $pass_error; // Add to errors array
        }
    }

    // Proceed only if there are no validation errors
    if (empty($errors)) {
        // Connect to the database
        require_once("settings.php");
        $conn = @mysqli_connect($host, $user, $pwd, $dbnm);
        if (!$conn) {
            $db_error = "<p>Unable to connect to the database. Please try again later!</p>";
            $errors[] = $db_error; // Add to errors array
        } else {
            // Use prepared statements to avoid SQL injection
            $stmt = mysqli_prepare($conn, "SELECT * FROM friends WHERE friend_email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
                // No email match, account does not exist
                $db_error = "<p>This account does not exist!</p>";
                $errors[] = $db_error; // Add to errors array
            } else {
                // Account exists, check the password
                $row = mysqli_fetch_assoc($result);
                if ($row['password'] !== $pass) {
                    // Password does not match
                    $pass_error = "<p>Passwords do not match!</p>";
                    $errors[] = $pass_error; // Add to errors array
                } else {
                    // Set up the session variables
                    $_SESSION["profile_name"] = $row["profile_name"];
                    $_SESSION["friend_id"] = $row["friend_id"];
                    $_SESSION["num_of_friends"] = $row["num_of_friends"];
                    $_SESSION["login_status"] = TRUE;
                    mysqli_free_result($result);
                    header("Location: friendlist.php");
                    exit();
                }
            }
            mysqli_close($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web application development" />
    <meta name="author" content="Serena"/>
    <title>My Friends System - Log In</title>
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
        <div class="login-text">
            <h2>Log in Page</h2>
            <form action="login.php" method="post">
                <?php
                if (!empty($errors)) {
                    echo "<div class='content'><font color='red'>" . implode("<br>", $errors) . "</font></div>";
                }
                ?>
                <div id="text">
                    <!-- Email -->
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"><br>

                    <!-- Password -->
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required><br>
                </div>

                <div class="links">
                    <button type="submit" name="login">Log in</button>
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

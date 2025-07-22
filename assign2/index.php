<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Web application development" />
  <meta name="keywords" content="PHP" />
  <meta name="author" content="Serena" />
  <title>My Friends System</title>
  <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
  <h1 class="header">My Friends System</h1>
  <nav>
    <ul class="navigator">
      <li class="navlink"> <a href="index.php">Home</a></li>
      <li class="navlink"> <a href="signup.php">Sign-up</a></li>
      <li class="navlink"> <a href="login.php">Log-In</a></li>
      <li class="navlink"> <a href="about.php">About</a></li>
    </ul>
  </nav>
  <div class="text">
            <h2>Assignment Home Page</h2>
  <fieldset>
  <div class="container">
        <p>Name: Serena Truong</p>
        <p>Student ID: 104480538</p>
        <p>Email address: <a href="mailto:104480538@student.swin.edu.au" class="link">104480538@student.swin.edu.au</a></p>
        <p class="declare">I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other student’s work or from any other source.</p>
  </div>
</fieldset>
</div>
  <div class="container">
    <?php
    // Connect to the database 
    require_once("settings.php");
    $db_message = "";  // Initialize the message variable

    $sql = "CREATE TABLE IF NOT EXISTS friends (
        friend_id INT NOT NULL AUTO_INCREMENT,
        friend_email VARCHAR(50) NOT NULL,
        password VARCHAR(20) NOT NULL,
        profile_name VARCHAR(30) NOT NULL,
        date_started DATE NOT NULL,
        num_of_friends INT UNSIGNED,
        PRIMARY KEY (friend_id)
    )";

    $sql2 = "CREATE TABLE IF NOT EXISTS myfriends (
        friend_id1 INT NOT NULL,
        friend_id2 INT NOT NULL,
        PRIMARY KEY (friend_id1, friend_id2),
        FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
        FOREIGN KEY (friend_id2) REFERENCES friends(friend_id),
        UNIQUE KEY (friend_id1, friend_id2)
    )";

    // Connect to the database
    $conn = @mysqli_connect($host, $user, $pwd, $dbnm);
    if (!$conn) {
        $db_message = "<p>Database connection error. Please try again later!</p>";
    } else {
        $result1 = mysqli_query($conn, $sql);
        $result2 = mysqli_query($conn, $sql2);
        if ($result1 && $result2) {
            // Insert sample data into 'friends' table
            $sql1 = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES
            ('serena09@gmail.com', 'sere123', 'Serena Truong', '2023-10-11', 4),
            ('tracy23@gmail.com', 'tracy67', 'Tracy Dinh', '2023-06-05', 2),
            ('nani18@gmail.com', 'nani78', 'Nani Tran', '2023-08-06', 8),
            ('lisa62@gmail.com', 'slisa', 'Lisa Kim', '2023-03-07', 9),
            ('alex84@gmail.com', 'sere123', 'Alex Dan', '2023-04-09', 6),
            ('tien22@gmail.com', 'sere1236', 'Tien Tran', '2023-04-09', 7),
            ('daniel36@gmail.com', 'sere1273', 'Daniel McCall', '2023-02-13', 5),
            ('annie2@gmail.com', 'sere1923', 'Annie Nguyen', '2023-05-16', 1),
            ('kimties78@gmail.com', 'sere1723', 'Kim Ties', '2023-08-29', 3),
            ('joong56@gmail.com', 'sere1223', 'Joong Ayden', '2023-01-10', 8)";

            $result3 = mysqli_query($conn, $sql);

            // Check if myfriends table is empty before inserting
            $myfriends_query = mysqli_query($conn, "SELECT * FROM myfriends");
            $rows = mysqli_num_rows($myfriends_query);
            if ($rows == 0) {
                $sql2 = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES
                (1, 2), (2, 3), (3, 4), (4, 5), (5, 6),
                (6, 7), (7, 8), (8, 9), (9, 10), (10, 1),
                (1, 3), (2, 4), (3, 5), (4, 6), (5, 7),
                (6, 8), (7, 9), (8, 10), (9, 1), (10, 2)";
                
                $result4 = mysqli_query($conn, $sql2);
                $rows += 20;  // Update row count
            }

            if ($result3 && $rows > 0) {
                $db_message = "<p style=color:green>Tables successfully created and populated.</p>";
            } else {
                $db_message = "<p style=color:red>Data inserted unsuccessfully!</p>";
            }
        } else {
            $db_message = "<p style=color:green>Table cannot be created!</p>";
        }
        mysqli_close($conn);
    }
    echo $db_message; // Display database message
    ?>
    <div class="links" >
                <button>
                    <a href="signup.php">Sign-Up</a>
                </button>
                <button>
                    <a href="login.php">Log-In</a>
                </button>
                <button>
                <a href="about.php">About</a>
            </button>
</div>
</body>
</html>
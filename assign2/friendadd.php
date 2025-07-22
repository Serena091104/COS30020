<?php
session_start();
require_once("settings.php");

global $conn, $numOfFriends, $table1, $table2;
$table1 = 'friends';
$table2 = 'myfriends';

// Check if the user is logged in
if (!isset($_SESSION["friend_id"]) || !isset($_SESSION["profile_name"])) {
    header("Location: login.php");
    exit();
}

$friend_id = $_SESSION['friend_id'];
$profile_name = $_SESSION['profile_name'];
$limitrecord = 10; // Records per page

// Initialize numOfFriends if not set
if (!isset($_SESSION['numOfFriends'])) {
    $_SESSION['numOfFriends'] = 0; // Default to 0 for newly signed-up accounts
}

// Query to count the number of friends the user already has
$queryFriendCount = "SELECT COUNT(*) FROM myfriends WHERE friend_id1 = ?";
if ($stmtCount = $conn->prepare($queryFriendCount)) {
    $stmtCount->bind_param("i", $friend_id);
    $stmtCount->execute();
    $stmtCount->bind_result($_SESSION['numOfFriends']);
    $stmtCount->fetch();
    $stmtCount->close();
} else {
    die("Database query failed: " . $conn->error);
}

// Handle add friend action
if (isset($_POST["addfriend"])) {
    $targetID = $_POST['id'];
    $queryAddFriend = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES (?, ?)"; 
    if ($stmt = $conn->prepare($queryAddFriend)) {
        $stmt->bind_param("ii", $friend_id, $targetID);
        $stmt->execute();
        $stmt->close();

        // Update friend count for the logged-in user
        $queryUpdateCount = "UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id = ?";
        if ($stmtCount = $conn->prepare($queryUpdateCount)) {
            $stmtCount->bind_param("i", $friend_id);
            $stmtCount->execute();
            $stmtCount->close();
        } else {
            die("Database query failed: " . $conn->error);
        }

        // Refresh friend count in session
        $_SESSION['numOfFriends']++;
        header("Location: friendadd.php?page=1");
        exit();
    } else {
        die("Database query failed: " . $conn->error);
    }
}

// Get the current page and calculate the offset
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page number is at least 1
$startFrom = ($page - 1) * $limitrecord;

// Fetch friends with mutual friend count
$queryFriends = "SELECT f.friend_id, f.profile_name,
    (SELECT COUNT(*) FROM myfriends mf1 
     JOIN myfriends mf2 ON mf1.friend_id2 = mf2.friend_id2 
     WHERE mf1.friend_id1 = ? AND mf2.friend_id1 = f.friend_id) as mutualCount 
    FROM friends f 
    WHERE f.friend_id NOT IN (SELECT myfriends.friend_id2 FROM myfriends WHERE myfriends.friend_id1 = ?) 
    AND f.friend_id != ? 
    LIMIT ?, ?";

if ($stmt = $conn->prepare($queryFriends)) {
    $stmt->bind_param("iiiii", $friend_id, $friend_id, $friend_id, $startFrom, $limitrecord);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Database query failed: " . $conn->error);
}

// Query to count the total number of potential friends (users who are not already friends)
$queryTotalCount = "SELECT COUNT(*) as total 
                     FROM friends 
                     WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) 
                     AND friend_id != ?";
if ($stmt = $conn->prepare($queryTotalCount)) {
    $stmt->bind_param("ii", $friend_id, $friend_id);
    $stmt->execute();
    $stmt->bind_result($totalRecords);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Database query failed: " . $conn->error);
}

// Calculate total pages
$totalPages = ceil($totalRecords / $limitrecord);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web application development" />
    <meta name="author" content="Your Name"/>
    <title>Friend Add Page</title>
    <link href="style2.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="content">
        <img src="style/addgroup.png" alt="Addgroup Logo" style="height: 65px; vertical-align: middle; margin-right: 20px;">
        <h1 class="header">My Friends System</h1>
        <nav>
            <ul class="navigator">
                <li class="navlink"><a href="index.php">Home</a></li>
                <li class="navlink"><a href="signup.php">Sign-up</a></li>
                <li class="navlink"><a href="login.php">Log-In</a></li>
                <li class="navlink"><a href="about.php">About</a></li>
            </ul>
        </nav>

        <div class="container-fluid">
            <h2><?php echo htmlspecialchars($profile_name); ?>'s Add Friend Page</h2>
            <h3>Total number of friends: <?php echo htmlspecialchars($_SESSION['numOfFriends']); ?></h3>
            
            <article width='70%' style="padding: 0">
                <table class='styled-table'>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['profile_name']) . "</td>";
                                echo "<td>Mutual Friends: " . htmlspecialchars($row['mutualCount']) . "</td>";
                                echo "<td width='35%'>
                                        <form action='friendadd.php' method='POST' style='margin: 0; padding: .40em .80em'>
                                            <input name='id' value='" . htmlspecialchars($row['friend_id']) . "' hidden>
                                            <button type='submit' name='addfriend' style='margin: 0'>Add as friend</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No users available to add as friends.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </article>

            <!-- Display pagination links -->
            <div class='pagination'>
                <?php if ($page > 1): ?>
                    <p><a href='friendadd.php?page=<?php echo $page - 1; ?>'>&laquo; Previous</a></p>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <p><a href='friendadd.php?page=<?php echo $page + 1; ?>'>Next &raquo;</a></p>
                <?php endif; ?>
            </div>

            <div class='friendslinks'>
                <p><a class='link' href='friendlist.php'><span>Friend Lists</span></a></p>
                <p><a class='link' href='logout.php'><span>Log out</span></a></p>
            </div>
        </div>
    </div>
</body>
</html>

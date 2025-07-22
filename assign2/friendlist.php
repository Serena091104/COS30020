<?php
session_start();
include_once("settings.php");

// Check if the user is logged in
if (!isset($_SESSION["friend_id"]) || !isset($_SESSION["profile_name"])) {
    header("Location: login.php");
    exit();
}

$friend_id = $_SESSION["friend_id"];
$profile_name = $_SESSION["profile_name"];

// Handle unfriend action if 'unfriend_id' is present
if (isset($_POST["unfriend_id"])) {
    $unfriend_id = $_POST["unfriend_id"];

    // Use prepared statements for security
    $delete_friend_query = "DELETE FROM myfriends 
                             WHERE (friend_id1 = ? AND friend_id2 = ?)
                             OR (friend_id2 = ? AND friend_id1 = ?)";
    $stmt = $conn->prepare($delete_friend_query);
    $stmt->bind_param("iiii", $friend_id, $unfriend_id, $friend_id, $unfriend_id);
    $stmt->execute();
    $stmt->close();

    // Reduce number of friends for both users
    $reduce_num_of_friends = "UPDATE friends 
                               SET num_of_friends = GREATEST(num_of_friends - 1, 0)
                               WHERE friend_id = ?";
    
    // Update the logged-in user's friend count
    $stmt = $conn->prepare($reduce_num_of_friends);
    $stmt->bind_param("i", $friend_id);
    $stmt->execute();
    $stmt->close();

    // Update the unfriended user's friend count
    $stmt = $conn->prepare($reduce_num_of_friends);
    $stmt->bind_param("i", $unfriend_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to the friendlist page after unfriending
    header("Location: friendlist.php");
    exit();
}

// SQL query for friends
$friends_query = "SELECT friends.friend_id, friends.profile_name
                  FROM friends
                  INNER JOIN myfriends
                  ON (myfriends.friend_id1 = ? AND myfriends.friend_id2 = friends.friend_id)
                  OR (myfriends.friend_id2 = ? AND myfriends.friend_id1 = friends.friend_id)
                  ORDER BY friends.profile_name";

$stmt = $conn->prepare($friends_query);
$stmt->bind_param("ii", $friend_id, $friend_id);
$stmt->execute();
$result = $stmt->get_result();
$num_of_friends = $result->num_rows; // Get the number of friends

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web application development" />
    <meta name="author" content="Serena"/>
    <title>List Friend Page</title>
    <link href="style3.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="content">
    <img src="style/biodegradable.png" alt="Biodegradable Logo" style="height: 50px; vertical-align: middle; margin-right: 10px;">
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
            <h2><?php echo htmlspecialchars($profile_name); ?>'s Friend List Page</h2>
            <h2>Total number of friends: <?php echo htmlspecialchars($num_of_friends); ?></h2>
            <article width='70%' style="padding: 0">
                <table role="grid" width='70%'>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['profile_name']) . "</td>";
                                echo '<td width="50px">
                                        <form action="" method="POST" style="margin: 0; padding: .25em .75em">
                                            <input type="hidden" name="unfriend_id" value="' . htmlspecialchars($row['friend_id']) . '">
                                            <button type="submit" style="margin: 0">Unfriend</button>
                                        </form>
                                      </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No friends to display.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </article>
            <div class="friendslinks">
                <p><a class="link" href="friendadd.php"><span>Add Friends</span></a></p>
                <p><a class="link" href="logout.php"><span>Log out</span></a></p>
            </div>
        </div>
    </div>
</body>
</html>

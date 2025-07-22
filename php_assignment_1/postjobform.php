<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Job Posting Form">
    <meta name="author" content="Serena">
    <title>Job Posting Form</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <header>
        <h1 class="header">Job Posting Form</h1>
    </header>
    <nav>
    <ul class="navigator">
      <li class="navlist"> <a href="index.php">Home</a></li>
      <li class="navlist current"> <a href="postjobform.php">Post job</a></li>
      <li class="navlist"> <a href="searchjobform.php"> Search job</a></li>
      <li class="navlist"> <a href="about.php">About assignment</a></li>
    </ul>
</nav>
    <div class="container">
    <form action="postjobprocess.php" method="POST">
        <!-- position_id  -->
        <label for="position_id">Position ID:</label>
        <input type="text" id="position_id" name="position_id" pattern="ID\d{3}" required title="Position ID must be 5 characters long and start with 'ID' followed by 3 digits.">        <br>

            <div class="row">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" maxlength="10" pattern="[A-Za-z0-9 ,.!]{1,10}" required title="Title must be up to 10 alphanumeric characters and may include spaces, commas, periods, and exclamation points.">
            </div>

            <div class="row">
                <label for="description">Description:</label>
                <textarea id="description" name="description" maxlength="250" required></textarea>
            </div>

            <div class="row">
                <label for="closingDate">Closing Date:</label>
                <input type="text" id="closing_date" name="closing_date" value="<?= date('d/m/y'); ?>" required>
            </div>

            <div class="row">
                <label>Position:</label>
                <input type="radio" id="fullTime" name="position" value="Full Time" required>
                <label for="fullTime">Full Time</label>
                <input type="radio" id="partTime" name="position" value="Part Time" required>
                <label for="partTime">Part Time</label>
            </div>

            <div class="row">
                <label>Contract:</label>
                <input type="radio" id="ongoing" name="contract" value="On-going" required>
                <label for="ongoing">On-going</label>
                <input type="radio" id="fixedTerm" name="contract" value="Fixed term" required>
                <label for="fixedTerm">Fixed term</label>
            </div>

            <div class="row">
                <label>Location:</label>
                <input type="radio" id="onSite" name="location" value="On site" required>
                <label for="onSite">On site</label>
                <input type="radio" id="remote" name="location" value="Remote" required>
                <label for="remote">Remote</label>
            </div>

            <div class="row">
                <label>Accept Application by:</label>
                <input type="checkbox" id="post" name="accept[]" value="Post">
                <label for="post">Post</label>
                <input type="checkbox" id="email" name="accept[]" value="Email">
                <label for="email">Email</label>
            </div>
            <div class="submit">
        <input class="button" type="submit" value="Submit">
        <input class="button" type="reset" value="Reset">
      </div>
      <div class="lastnote">
      <p class="note"><span class="asterisk">*</span> All fields are required.</p>
      </div>
</form>
</div>
<footer>
        <br> <a href="index.php"> Return to Home Page</a>
</footer>
</body>
</html>

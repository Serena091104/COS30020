<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Search Job Vacancy">
    <meta name="author" content="Serena">
    <title>Job Posting Form</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <header><h1 class="header">Search Job Vacancy Posting System</h1>
    </header>
    <nav>
        <ul class="navigator">
            <li class="navlist"><a href="index.php">Home</a></li>
            <li class="navlist current"><a href="postjobform.php">Post job</a></li>
            <li class="navlist"><a href="searchjobform.php">Search job</a></li>
            <li class="navlist"><a href="about.php">About assignment</a></li>
        </ul>
    </nav>
    <!-- search job form  -->
    <form action="searchjobprocess.php" method="GET">
            <!-- title  -->
            <label for="search_title">Search by Job Title:</label>
            <input type="text" id="search_title" name="search_title">
            <br>   
           <!-- position  -->
           <label for="search_position">Search by Position:</label>
            <select id="search_position" name="search_position">
                <option value="">Choose</option>
                <option value="Full Time">Full Time</option>
                <option value="Part Time">Part Time</option>
            </select>            
            <br>
            <!-- contract  -->
            <label for="search_contract">Search by Contract:</label>
            <select id="search_contract" name="search_contract">
                <option value="">Choose</option>
                <option value="On-going">On-going</option>
                <option value="Fixed Term">Fixed Term</option>
            </select>
            <br>
            <!-- Application Type -->
            <label for="search_application">Search by Application Type:</label>
            <input type="checkbox" id="post" name="app[]" value="Post">
            <label for="post">Post</label>
            <input type="checkbox" id="mail" name="app[]" value="Mail">
            <label for="mail">Mail</label>
            <br>
            <!-- Location -->
            <label>Location:</label>
            <input type="radio" id="onsite" name="location" value="On-site">
            <label for="onsite">On-site</label>
            <input type="radio" id="remote" name="location" value="Remote">
            <label for="remote">Remote</label>
            <br>
            <!-- buttons -->
            <button type="submit">Search</button>
</form>
</div>
<footer>
    <br><a href="index.php"> Return to Home Page</a>
</footer>
</body>
</html>
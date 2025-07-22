<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Search Job Vacancy">
    <meta name="author" content="Serena">
    <title>Search Job Vacancy</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <header>
        <h1 class="header">Search Job Vacancy Posting System</h1>
    </header>
    <nav>
        <ul class="navigator">
            <li class="navlist"><a href="index.php">Home</a></li>
            <li class="navlist"><a href="postjobform.php">Post job</a></li>
            <li class="navlist current"><a href="searchjobform.php">Search job</a></li>
            <li class="navlist"><a href="about.php">About assignment</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="process">
            <?php
            // To get search values:
            $search_title = isset($_GET["search_title"]) ? trim($_GET["search_title"]) : "";
            $search_position = isset($_GET["search_position"]) ? $_GET["search_position"] : "";
            $search_contract = isset($_GET["search_contract"]) ? $_GET["search_contract"] : "";
            $search_location = isset($_GET["search_location"]) ? $_GET["search_location"] : "";
            $search_application = isset($_GET["search_application"]) ? $_GET["search_application"] : "";

            $file_path = "../../data/jobs/positions.txt";

            if (empty($search_title)) {  // Check if search title is empty
                echo "<p>Please provide a job title to search.</p>";
                echo '<p><a href="index.php">Return to Home Page</a> | <a href="searchjobform.php">Return to Search Job Page</a></p>';
                exit;
            } else if (!file_exists($file_path)) { // Check if file exists
                echo "<p>No job vacancy records found.</p>";
                echo '<p><a href="index.php">Return to Home Page</a> | <a href="searchjobform.php">Return to Search Job Page</a></p>';
                exit;
            } else { // If file exists, search for jobs
                $file_contents = file_get_contents($file_path);
                if ($file_contents === false) {
                    echo "<p>Unable to read the file.</p>";
                    echo '<p><a href="index.php">Return to Home Page</a> | <a href="searchjobform.php">Return to Search Job Page</a></p>';
                    exit;
                }
                $lines = explode("\n", $file_contents);
                // Array to store jobs found
                $found_jobs = [];

                // Check search values against each job row/line
                foreach ($lines as $line) {
                    $fields = explode("\t", $line);
                    if (
                        count($fields) >= 8 &&
                        (empty($search_title) || stripos($fields[1], $search_title) !== false) &&
                        (empty($search_position) || $fields[4] === $search_position) &&
                        (empty($search_contract) || $fields[5] === $search_contract) &&
                        (empty($search_application) || stripos($fields[6], $search_application) !== false) &&
                        (empty($search_location) || $fields[7] === $search_location)
                    ) {
                        $found_jobs[] = $fields;
                    }
                }
                
                // Filter jobs by closing date (from future dates to today)
                $current_date = date("d/m/y");
                $filtered_jobs = array_filter($found_jobs, function ($job) use ($current_date) {
                    return strtotime($job[3]) >= strtotime($current_date);
                });
                
                // Sort jobs by closing date from future dates to today
                function compareClosingDates($a, $b) {
                    $dateA = strtotime($a[3]);
                    $dateB = strtotime($b[3]);
                    
                    if ($dateA == $dateB) {
                        return 0;
                    }
                    return ($dateA > $dateB) ? -1 : 1;
                }
                usort($filtered_jobs, 'compareClosingDates');

                // Display jobs
                if (empty($filtered_jobs)) { // If no jobs found
                    echo "<p>No job vacancies found for your search.</p>";
                } else { // If jobs found
                    foreach ($filtered_jobs as $job) {
                        echo "<div class='job'>";
                        echo "<p><b>Job Title:</b> " . htmlspecialchars($job[1]) . "</p>";
                        echo "<p><b>Description:</b> " . htmlspecialchars($job[2]) . "</p>";
                        echo "<p><b>Closing Date:</b> " . htmlspecialchars($job[3]) . "</p>";
                        echo "<p><b>Position:</b> " . htmlspecialchars($job[4]) . "</p>";
                        echo "<p><b>Contract:</b> " . htmlspecialchars($job[5]) . "</p>";
                        echo "<p><b>Location:</b> " . htmlspecialchars($job[7]) . "</p>";
                        echo "<p><b>Application by:</b> " . htmlspecialchars($job[6]) . "</p>";
                        echo "</div>";
                    }
                }
            }
            ?>
        </div>
        <footer>
            <br> <a href="searchjobform.php">Search Another Job</a>
            <br> <a href="index.php">Return to Home Page</a>
        </footer>
    </div>
</body>
</html>
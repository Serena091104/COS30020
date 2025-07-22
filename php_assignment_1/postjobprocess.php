<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Job Posting Form">
    <meta name="author" content="Serena">
    <title>Post Job Process</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <header>
        <h1 class="header">Job Vacancy Posting System</h1>
    </header>
    <nav>
        <ul class="navigator">
            <li class="navlist"><a href="index.php">Home</a></li>
            <li class="navlist current"><a href="postjobform.php">Post job</a></li>
            <li class="navlist"><a href="searchjobform.php">Search job</a></li>
            <li class="navlist"><a href="about.php">About assignment</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="process">
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // function to validate the date in the correct format, d/m/y used as default
                function validateDate($date, $format = 'd/m/y') {
                    $d = DateTime::createFromFormat($format, $date);
                    return $d && $d->format($format) == $date;
                }
            
                // array to store errors
                $errors = [];
            
                // get form data, using ternary operators to check if data exists first before assigning it
                $position_id = isset($_POST["position_id"]) ? $_POST["position_id"] : "";
                $title = isset($_POST["title"]) ? $_POST["title"] : "";
                $description = isset($_POST["description"]) ? $_POST["description"] : "";
                $closing_date = isset($_POST["closing_date"]) ? $_POST["closing_date"] : "";
                $position = isset($_POST["position"]) ? $_POST["position"] : "";
                $contract = isset($_POST["contract"]) ? $_POST["contract"] : "";
                $location = isset($_POST["location"]) ? $_POST["location"] : "";
                $accept = isset($_POST["accept"]) ? $_POST["accept"] : [];
            
                // validation checks 
                if (empty($position_id) || !preg_match('/^ID\d{3}$/', $position_id)) {
                    $errors[] = "Invalid Position ID. It must start with ID followed by 3 digits.";
                }
                if (empty($title) || strlen($title) > 10 || !preg_match('/^[A-Za-z0-9 ,.!]{1,10}+$/', $title)) {
                    $errors[] = "<p>Invalid Title. It must be up to 10 alphanumeric characters and may include spaces, commas, periods, and exclamation points.</p>";
                }
                if (empty($description) || strlen($description) > 250) {
                    $errors[] = "<p>Invalid Description. It must be up to 250 characters.</p>";
                }
                if (empty($closing_date) || !validateDate($closing_date, 'd/m/y')) {
                    $errors[] = "<p>Invalid Closing Date. It must be in 'dd/mm/yy' format.</p>";
                }
                if (empty($position)) {
                    $errors[] = "<p>Position is required.</p>";
                }
                if (empty($contract)) {
                    $errors[] = "<p>Contract type is required.</p>";
                }
                if (empty($location)) {
                    $errors[] = "<p>Location is required.</p>";
                }
                if (empty($accept)) {
                    $errors[] = "<p>You must select at least one method to accept applications.</p>";
                }
                // create directory if it does not exist
                umask(0007); 
                $dir = "../../data/jobs"; // double check if this is the correct path
                if (!file_exists($dir)) {
                    mkdir($dir, 02770, true);
                }
                
                // if no errors, start logic to write to file
                if (empty($errors)) {
                    $data = "$position_id\t$title\t$description\t$closing_date\t$position\t$contract\t" . implode(", ", $accept) . "\t$location\n";
                    $file_path = "../../data/jobs/positions.txt";
            
                    $existing_position_ids = [];
            
                    // check if file exists to avoid overwriting existing data
                    if (file_exists($file_path)) {
                        $handle = fopen($file_path, "r");
                        if ($handle) {
                            while (($line = fgets($handle)) !== false) {
                                $fields = explode("\t", $line);
                                if (!empty($fields[0])) {
                                    $existing_position_ids[] = trim($fields[0]);
                                }
                            }
                            fclose($handle);
                        } else {
                            $errors[] = "Unable to open $file_path.";
                        }
                    }
            
                        // check if position id already exists
                        if (in_array($position_id, $existing_position_ids)) {
                            $errors[] = "The position ID already exists. Please enter a unique ID.";
                        } else { // if position id does not exist, write to file
                            $handle = fopen($file_path, "a");
                            if ($handle) {
                                fputs($handle, $data);
                                fclose($handle);
                                $confirmation_message = "Job vacancy has been successfully posted.";
                        } else {
                            $errors[] = "Unable to open $file_path for writing.";
                        } 
                    }
                }
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        echo "<p>$error</p>";
                    }
                } else {
                    if (isset($confirmation_message)) {
                        echo "<p>$confirmation_message</p>";
                    }
                }
            }
            ?>
            <div class="lastnote">
                <p class="return"><a href="postjobform.php"><span>Back to Job Posting Page</span></a></p>
                <p class="return"><a href="index.php"><span>Back to Home</span></a></p>
            </div>
        </div>
        <div class="push"></div>
    </div>
    <footer>
        <br> <a href="index.php"> Return to Home Page</a>
    </footer>
</body>
</html>
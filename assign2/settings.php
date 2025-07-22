<?php
mysqli_report(MYSQLI_REPORT_OFF);

$host = "feenix-mariadb.swin.edu.au";
$user = "s104480538";
$pwd = "091104";
$dbnm = "s104480538_db";

// Connect to database
$conn = @mysqli_connect($host, $user, $pwd, $dbnm);
if (!$conn) {
  // Get error message
  $errMsg = mysqli_connect_error();
  $errNo = mysqli_connect_errno();
  session_start();
  // Store error message in session variable and redirect to error page
  $_SESSION["errMsg"] = $errMsg;
  $_SESSION["errNo"] = $errNo;
  header("Location: error.php");
  exit();
}
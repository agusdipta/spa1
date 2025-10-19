<?php
// ----------- CONFIG -----------
$DB_HOST = "sql109.infinityfree.com";   // change to your host
$DB_USER = "if0_40150645";              // change to your user
$DB_PASS = "WAxPLp8M1uz";          // change to your password
$DB_NAME = "if0_40150645_spadb";          // change to your DB name

$WA_NUMBER = "62895357154445"; // WhatsApp target number (owner/admin)
$BASE_URL  = ""; // optional, e.g. "https://yourdomain.com"

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
    die("DB connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
?>

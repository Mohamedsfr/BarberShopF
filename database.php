<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "salon";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

?>
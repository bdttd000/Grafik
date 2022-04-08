<?php
session_start();

$servername = "";
$database_name = "";
$user = "";
$pass = "";

$schedule = mysqli_connect($servername, $user, $pass, $database_name);
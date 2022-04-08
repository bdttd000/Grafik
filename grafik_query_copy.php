<?php

include('./functions.php');

$first_day = $_GET['first_day'];
$second_day = $_GET['second_day'];

$first_day = json_decode($first_day);
$second_day = json_decode($second_day);

$change = 0;
$response = 0;

$sql = "SELECT info FROM `".$first_day[0]."` WHERE day='".$first_day[1]."'";
$result = $schedule->query($sql)->fetch_assoc();
$result = $result['info'];

$sql = "SELECT info FROM `".$second_day[0]."` WHERE day='".$second_day[1]."'";
$check_if_exist = $schedule->query($sql)->num_rows;

if ($check_if_exist>0) {
    if ($result == '') {
        $sql = "DELETE FROM `".$second_day[0]."` WHERE day='".$second_day[1]."'";
        $schedule->query($sql);
        $change = 1;
        $response = 0;
    } else {
        $sql = "UPDATE `".$second_day[0]."` SET info='".$result."' WHERE day='".$second_day[1]."'";
        $schedule->query($sql);
        $change = 0;
    }
} else {
    if ($result == '') {
        $change = 0;
    } else {
        $sql = "INSERT INTO `".$second_day[0]."` (day,info) VALUES ('".$second_day[1]."','".$result."')";
        $schedule->query($sql);
        $change = 1;
        $response = 1;
    }
}

echo json_encode(array($change,$response));

?>
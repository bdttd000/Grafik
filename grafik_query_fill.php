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

$days_array = [];
$change_array = [];

$first_day[1] = str_replace("_","-",$first_day[1]);
$second_day[1] = str_replace("_","-",$second_day[1]);

if ($first_day[1] > $second_day[1]) {
    $first_day[1] = date("y-m-d",strtotime($first_day[1].' - 1 days'));
    $temp = $first_day[1];
    $first_day[1] = $second_day[1];
    $second_day[1] = $temp;
} else {
    $first_day[1] = date("y-m-d",strtotime($first_day[1].' + 1 days'));
}

do {
    if(date("N",strtotime($first_day[1])) < 6) array_push($days_array,str_replace("-","_",$first_day[1]));
    $first_day[1] = date("y-m-d",strtotime($first_day[1].' + 1 days'));
} while ($first_day[1] <= $second_day[1]);

foreach ($days_array as $day) {
    $sql = "SELECT * FROM `".$first_day[0]."` WHERE day='".$day."'";
    $check_if_exist = $schedule->query($sql)->num_rows;

    if ($check_if_exist>0) {
        if ($result == '') {
            $sql = "DELETE FROM `".$first_day[0]."` WHERE day='".$day."'";
            $schedule->query($sql);
            array_push($change_array,$day);
            $change = 1;
            $response = 0;
        } else {
            $sql = "UPDATE `".$first_day[0]."` SET info='".$result."' WHERE day='".$day."'";
            $schedule->query($sql);
        }
    } else {
        if ($result == '') {
        } else {
            $sql = "INSERT INTO `".$first_day[0]."` (day,info) VALUES ('".$day."','".$result."')";
            $schedule->query($sql);
            array_push($change_array,$day);
            $change = 1;
            $response = 1;
        }
    }
}

echo json_encode(array($change,$response,$change_array));

?>
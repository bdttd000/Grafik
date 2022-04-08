<?php

include('./functions.php');

$array = $_GET['array'];
$table = $_GET['table_info'];

$array = json_decode($array);
$table = json_decode($table);

$desired_array = [];
$response = 0;

foreach ($array as $row) {
    $row = (array)$row;
    $desired_array[$row['id']] = array('id'=>$row['id'], 'importance'=>$row['importance'], 'note'=>$row['note']);
}

if (reset($desired_array)) {
    ksort($desired_array);
    $desired_array = serialize($desired_array);

    $sql = "SELECT * FROM `".$table[0]."` WHERE day='".$table[1]."'";
    $check_if_exist = $schedule->query($sql)->num_rows;

    if ($check_if_exist) {
        $sql = "UPDATE `".$table[0]."` SET info='".$desired_array."' WHERE day='".$table[1]."'";
        $schedule->query($sql);
    } else {
        $sql = "INSERT INTO `".$table[0]."` (day,info) VALUES ('".$table[1]."','".$desired_array."')";
        $schedule->query($sql);
    }

    $response = 1;

} else {
    $sql = "DELETE FROM `".$table[0]."` WHERE day='".$table[1]."'";
    $schedule->query($sql);
}

echo $response;

?>
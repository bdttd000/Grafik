<?php

include('./functions.php');

$id = $_GET['id'];
$day = $_GET['day'];

$sql = "SELECT info FROM `$id` WHERE day='$day'";
$project = $schedule->query($sql)->fetch_row();

$sql = "SELECT id, name, surname, username FROM users";
$result = $schedule->query($sql);

$users_array = [];

while ($row = $result->fetch_assoc()) {
    $users_array[$row['id']] = array(
        'id'=>$row['id'],
        'name'=>$row['name'],
        'surname'=>$row['surname']
    );
}

if ($project[0] && $new_array = unserialize($project[0])) {
    if (reset($new_array)) {
        foreach ($new_array as $temp_array) {
            $users_array[$temp_array['id']]['importance'] = $temp_array['importance'];
            $users_array[$temp_array['id']]['note'] = $temp_array['note'];
        }
    }
}

echo '<table id="'.$id.'" class="table-container">';
echo '<tr>';
echo '<th>Pracownik</th>';
echo '<th>Priorytet</th>';
echo '<th>Notatka</th>';
echo '</tr>';

foreach ($users_array as $user) {
    echo '<tr id="'.$user['id'].'">';

    echo '<td>'.$user['name'].' '.$user['surname'].'</td>';

    echo '<td>';
    echo '<select class="align-project-week-select">';
    echo '<option></option>';
    $node = '';
    for ($x=1; $x<11; $x++) {
        $node .= ($x==$user['importance']) ? '<option selected>'.$x.'</option>' : '<option>'.$x.'</option>';
    }
    echo $node;
    echo '</select>';
    echo '</td>';

    echo '<td>';
    echo '<input class="align-project-week-input" type="text" placeholder="notatka"'; 
    echo ($user['importance'] && $user['note']) ? 'value="'.$user['note'].'"' : '';
    echo 'value=""';
    echo '>';
    echo '</td>';

    echo '</tr>';
}

echo '</table>';

?>
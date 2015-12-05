<?php

require_once 'connection.php';
$investor_id = $_GET['inv_id'];
$count_of_recos = $_GET['n'];
$return_recos = array();

//Get the investor State
$sql = "SELECT state_or_province FROM users u WHERE u.id='$investor_id' limit 1";
$result = mysql_query($sql);
$value = mysql_fetch_object($result);
$state = $value->state_or_province;

//Get % of state in state of states
$sql = "SELECT percentage_achieved FROM state_of_states sos WHERE sos.state='$state' limit 1";
$result = mysql_query($sql);
$value = mysql_fetch_object($result);
$percent = $value->percentage_achieved;


//Get position of state
$sql = "SELECT 
       (SELECT COUNT(*) FROM `state_of_states` WHERE `percentage_achieved` >= '$percent') AS `position`

FROM `state_of_states`
WHERE `percentage_achieved` >= '$percent' LIMIT 1";
$result = mysql_query($sql);
$value = mysql_fetch_object($result);
$position = $value->position;

//Get total of states
$sql = "SELECT COUNT(*) as count FROM `state_of_states`";
$result = mysql_query($sql);
$value = mysql_fetch_object($result);
$count = $value->count;

$percent_position = $position / $count;


//If the state is in bottom 50%, pick the 5 least funded loan profiles from the state
if ($percent_position <= 0.5) {
  //return last $count_of_recos from the state
  $sql = "select loan_profile_id from loan_profile_percent lpp where lpp.state = '$state' ORDER BY rank DESC LIMIT $count_of_recos;";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $id = $row['loan_profile_id'];
    $return_recos[] = $id;
  }
}
else {
  //Else, pick the least funded state and pick the 5 least funded loan profiles from the state
  $sql = "select state from state_of_states  s  ORDER BY s.`percentage_achieved` ASC LIMIT $count_of_recos;";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $state = $row['state'];
    $sql2 = "select loan_profile_id from loan_profile_percent lpp where lpp.`state`='$state' order by lpp.rank ASC limit 1";
    $result2 = mysql_query($sql2);
    $value2 = mysql_fetch_object($result2);
    $id = $value2->loan_profile_id;
    $return_recos[] = $id;
  }
}

//Return the recos
$json = json_encode($return_recos);
print_r($json);
?>
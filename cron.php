<?php

require_once 'connection.php';


$sql = "CREATE TABLE IF NOT EXISTS `state_of_states` (
`state` VARCHAR(127),
`loan_amount_in_need` INT(4),
`loan_amount_in_investment` INT(4),
`percentage_achieved` DECIMAL(4,2)
)";


//Create the table if not exists
$result = mysql_query($sql);

//Get list of unique states
$sql = "SELECT DISTINCT(`borrower_state`) AS `state` FROM `loan_profiles`;";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
  $state = $row['state'];
  //Get total amount needed for the state
  //@TODO: Later you might want to add a filter here where loan_profile state = "IN_NEED"
  $sql4 = "SELECT SUM(lp.`loan_amount_in_paisa`) as tn from `loan_profiles` as lp where lp.`borrower_state` = '$state'";
  $result4 = mysql_query($sql4);
  $value4 = mysql_fetch_object($result4);

  //Get total amount funded for the state
  //@TODO: Later you might want to add a filter here where loan_profile state = "IN_NEED"
  $sql4c = "SELECT lp.`id` as tn from `loan_profiles` as lp where lp.`borrower_state` = '$state'";
  $sql5 = "SELECT SUM(i.`contribution_in_paisa`) as tc from `investments` as i where i.`loan_profile_id` IN ($sql4c) ";

  $result5 = mysql_query($sql5);
  $value5 = mysql_fetch_object($result5);

  $percent_funded = $value5->tc / $value4->tn * 100;


  //  Check if there is a record for $row['state']
  $sql5 = "SELECT * from `state_of_states` as s where s.state = '$state'";
  $result5 = mysql_query($sql5);
  $rowcount5 = mysql_num_rows($result5);
  if ($rowcount5) {
    //State row exists. So update
    $sql6 = "UPDATE state_of_states SET loan_amount_in_need='$value4->tn', loan_amount_in_investment='$value5->tc', percentage_achieved='$percent_funded' where state_of_states.state='$state'; ";
    mysql_query($sql6);
  }
  else {
    //Insert state row
    $sql6 = "INSERT INTO state_of_states VALUES('$state','$value4->tn','$value5->tc','$percent_funded' ) ";
    mysql_query($sql6);
  }
}

//Loan load profile % rank table
$sql7 = "CREATE TABLE IF NOT EXISTS `loan_profile_percent` (
`loan_profile_id` INT(4),
`state` VARCHAR(127),
`rank` DECIMAL(4,2)
)";

//Create the table if not exists
$result = mysql_query($sql7);
//Get list of unique states
$sql = "SELECT *  FROM `loan_profiles`;";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
  $id = $row['id'];
  $state = $row['borrower_state'];
  $total_amount = $row['loan_amount_in_paisa'];

  $sql8 = "SELECT SUM(i.`contribution_in_paisa`) as tc from `investments` as i where i.`loan_profile_id` = $id ";
  $result8 = mysql_query($sql8);
  $value8 = mysql_fetch_object($result8);

  $invested_amount = $value8->tc;

  $completed_pc = $invested_amount / $total_amount * 100;

  //Check if the record exists for this loan, else create it.

  $sql9 = "SELECT * from `loan_profile_percent` as lpp where lpp.id = '$id'";
  $result9 = mysql_query($sql9);
  $rowcount9 = mysql_num_rows($result9);
  if ($rowcount9) {
    //State row exists. So update
    $sql10 = "UPDATE loan_profile_percent SET loan_profile_id='$id', state='$state', rank='$completed_pc' where loan_profile_percent.id='$id'; ";
    mysql_query($sql10);
  }
  else {
    //Insert state row
    $sql10 = "INSERT INTO loan_profile_percent VALUES('$id','$state','$completed_pc' ) ";
    mysql_query($sql10);
  }
}

echo "CRON successfully completed";
?>

<?php
include('config.php');
session_start();
$user_id=$_POST['user_id'];
$password=$_POST['password'];
$result = mysqli_query($conn,"SELECT * FROM `user_profile` where `user_id`='".mysqli_real_escape_string($conn,$user_id)."' and `password`='".mysqli_real_escape_string($conn,$password)."'");
$email_check = mysqli_query($conn,"SELECT * FROM `user_profile` where `user_id`='".mysqli_real_escape_string($conn,$user_id)."'");
echo "\n\n";
echo "SELECT * FROM `user_profile` where `user_id`='".mysqli_real_escape_string($conn,$user_id)."' and `password`='".mysqli_real_escape_string($conn,$password)."'";
if(mysqli_num_rows($email_check)==1){
if (mysqli_num_rows($result) == 1) {
$_SESSION['user_id'] = $_POST['user_id'];
while($row = mysqli_fetch_array($result)) {
  $_SESSION['mail_id']=$row['mail_id'];
  $_SESSION['name']=$row['name'];
  $_SESSION['Birthday']=$row['Birthday'];
  $_SESSION['user_id']=$row['user_id'];
}
header('Location: problems.php');
}
else {
// Jump to login page
header('Location: fail.php');
}}
else {
header('Location: not_exist.php');
}
?>

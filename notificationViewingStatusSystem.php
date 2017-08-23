<?php

session_start();

include 'dbh.php';


$notificationId=mysqli_real_escape_string($conn,$_POST['notificationId']);
$sql="UPDATE notifications_table SET viewingStatus='1' WHERE id='$notificationId'";
$result=mysqli_query($conn,$sql);
echo "success";

<?php

session_start();

include 'dbh.php';

$email=mysqli_real_escape_string($conn,$_POST['email']);
$inviterUserId=mysqli_real_escape_string($conn,$_SESSION['id']);
$inviterUsername=mysqli_real_escape_string($conn,$_SESSION['username']);
if($email!="")
{
//$sql="SELECT email FROM memberstable WHERE email='$email'";
//$result=mysqli_query($conn,$sql);
$stmt= $conn->prepare("SELECT email FROM memberstable WHERE email=?");
$stmt-> bind_param("s",$EMAIL);
$EMAIL=$email;
$stmt->execute();
$result=$stmt->get_result();

if($row=mysqli_fetch_assoc($result)){

	$response="already a member";
	//$response=$email;
}
else{
	//generating invitation code
	$c = array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
	
	do{
		$invitationCode="";
		for($i=0;$i<10;$i++)
		{
			$invitationCode.=$c[mt_rand(0,count($c)-1)];
		}

		//$sql="SELECT inviteCode FROM invite_codes_table WHERE inviteCode='$invitationCode'";
		//$result=mysqli_query($conn,$sql);
		$stmt= $conn->prepare("SELECT inviteCode FROM invite_codes_table WHERE inviteCode=?");
		$stmt-> bind_param("s",$invitationCode);
		$IC=$invitationCode;		
		$stmt->execute();
		$result=$stmt->get_result();

		if($row=mysqli_fetch_assoc($result)){
			$invitationCodeUniqueness=false;
		}
		else{
			$invitationCodeUniqueness=true;
		}

	}while($invitationCodeUniqueness==false);
	
	//$sql="INSERT INTO invite_codes_table (inviterId,inviterUsername,emailToBeInvited,inviteCode,codeUsedStatus) VALUES ('$inviterUserId','$inviterUsername','$email','$invitationCode',0)";
	//$result=mysqli_query($conn,$sql);
	$sql="INSERT INTO invite_codes_table (inviterId,inviterUsername,emailToBeInvited,inviteCode,codeUsedStatus) VALUES (?,?,?,?,?)";
	$stmt= $conn->prepare($sql);
	$stmt-> bind_param("dsssd",$UID,$UNM,$EMAIL,$IC,$CUS);
	$UID=$inviterUserId;
	$EMAIL=$email;
	$UNM=$inviterUsername;
	$IC=$invitationCode;
	$CUS=0;
	$stmt->execute();
	$result=$stmt->get_result();
	
	$to      = $email;
	$subject = 'Invited to Meagl.com by '.$inviterUsername;
	$message = 'Hi there! '.$inviterUsername.' has invited you to join him at Meagl.com! Your unique id is:'.$invitationCode.'. Click on this link: http://www.meagl.com/signup.php and enter the unique code while signing up so that '.$inviterUsername.' can be notified that you have joined in!';
	$headers = 'From: support@meagl.com' . "\r\n" .
	    'Reply-To: support@meagl.com' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
	
	$response="success";
}

echo $response;
}
<?php

session_start();

include 'dbh.php';

$response="";
$obtainID=0;//user id initialization
//if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['pwd'])){
if($_POST['name']!="" && $_POST['username']!="" && $_POST['email']!="" && $_POST['pwd']!=""){
	$name=mysqli_real_escape_string($conn,$_POST['name']);
	$username=mysqli_real_escape_string($conn,$_POST['username']);
	$email=mysqli_real_escape_string($conn,$_POST['email']);
	$pwd=mysqli_real_escape_string($conn,$_POST['pwd']);
	if(strlen($username)<=15)
	{
	
	$profilePictureLocation="defaults/defaultProfilePicture.png";
	$defaultStatus="I love Meagl!";

	if(isset($_POST['lastpage'])){
		$lastpage=mysqli_real_escape_string($conn,$_POST['lastpage']);
		//echo '<script language="javascript">alert("last page ala!!!!!!!!!!!!!="'.$lastpage.')</script>';
	}
	//seeing if the same username is already present
	//$sql1="SELECT username FROM memberstable WHERE username='$username'";
	//$result1=mysqli_query($conn,$sql1);

	$stmt= $conn->prepare("SELECT username FROM memberstable WHERE username=?");
	$stmt-> bind_param("s",$USERNAME);
	$USERNAME=$username;
	$stmt->execute();
	$result1=$stmt->get_result();

	if($row=mysqli_fetch_assoc($result1)){
		//same username already present of another user
		$_SESSION['signinError']=true;
		//echo '<script language="javascript">alert("signin error")</script>';
		//header("LOCATION: signup.php");
		$response="signup username error";
	}
	else{
		//no other user has the same username
		//seeing if the same username is already present
		//$sql1="SELECT email FROM memberstable WHERE email='$email'";
		//$result1=mysqli_query($conn,$sql1);

		$stmt= $conn->prepare("SELECT email FROM memberstable WHERE email=?");
		$stmt-> bind_param("s",$EMAIL);
		$EMAIL=$email;
		$stmt->execute();
		$result1=$stmt->get_result();

		if($row=mysqli_fetch_assoc($result1)){
			//same username already present of another user
			$_SESSION['signinError']=true;
			//echo '<script language="javascript">alert("signin error")</script>';
			//header("LOCATION: signup.php");
			$response="signup email error";
		}
		else{
			//echo '<script language="javascript">alert("else madhe ala")</script>';
			//initialsing session variable 'username'
			$_SESSION['username']=$username;
			//$_SESSION['numberofSubscribers']=0;

			//getting the ip address
			/*$ip = getenv('HTTP_CLIENT_IP')?:
				  getenv('HTTP_X_FORWARDED_FOR')?:
				  getenv('HTTP_X_FORWARDED')?:
				  getenv('HTTP_FORWARDED_FOR')?:
				  getenv('HTTP_FORWARDED')?:
				  getenv('REMOTE_ADDR');*/

			//get ip address of the user here....uncomment this when website is released for this doesn't work when running the website on localhost
			/*$ipaddress = '';
		    if ($_SERVER['HTTP_CLIENT_IP'] != '127.0.0.1')
		        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		    else if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1')
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    else if ($_SERVER['HTTP_X_FORWARDED'] != '127.0.0.1')
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		    else if ($_SERVER['HTTP_FORWARDED_FOR'] != '127.0.0.1')
		        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		    else if ($_SERVER['HTTP_FORWARDED'] != '127.0.0.1')
		        $ipaddress = $_SERVER['HTTP_FORWARDED'];
		    else if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1')
		        $ipaddress = $_SERVER['REMOTE_ADDR'];
		    else
		        $ipaddress = 'UNKNOWN';*/			


			$redirect=true;//stores true if user is to be redirected after signup

			if($_POST['inviteCode']!=""){
				//means this user has been sent an invitation code by someone
				$invitationCode=mysqli_real_escape_string($conn,$_POST['inviteCode']);

				//$sql="SELECT codeUsedStatus,inviterId FROM invite_codes_table WHERE inviteCode='$invitationCode' AND emailToBeInvited='$email'";
				//$result=mysqli_query($conn,$sql);
				$stmt= $conn->prepare("SELECT codeUsedStatus,inviterId FROM invite_codes_table WHERE inviteCode=? AND emailToBeInvited=?");
				$stmt-> bind_param("ss",$INVITATONCODE,$EMAIL1);
				$EMAIL1=$email;
				$INVITATONCODE=$invitationCode;
				$stmt->execute();
				$result=$stmt->get_result();

				if($row=mysqli_fetch_assoc($result)){
					$codeUsedStatus=$row['codeUsedStatus'];
					$inviterId=$row['inviterId'];

					if($codeUsedStatus==0){
						//$sql1="UPDATE invite_codes_table SET codeUsedStatus=1 WHERE inviteCode='$invitationCode'";
						//$result1=mysqli_query($conn,$sql1);
						$stmt= $conn->prepare("UPDATE invite_codes_table SET codeUsedStatus=? WHERE inviteCode=?");
						$stmt-> bind_param("ds",$CUS,$IC);
						$CUS=1;
						$IC=$invitationCode;
						$stmt->execute();

						$sql2="UPDATE memberstable SET points=points+40 WHERE id='$inviterId'";
						$result2=mysqli_query($conn,$sql2);

						//inserting data into memberstable
						$sql="INSERT INTO memberstable (name, username, email, pwd, profilePictureLocation, userStatus, memesUploaded, numberofSubscribers, numberOfQuestionsAsked, ipAddress) VALUES (?,?,?,?, ?,?, ?, ?, ?,?)";
						$stmt= $conn->prepare($sql);
						$stmt-> bind_param("ssssssdddd",$NAME,$USERNAME,$EMAIL,$PWD,$PP,$US,$MU,$NOS,$NOQA,$IP);
						$NAME=$name;
						$USERNAME=$username;
						$EMAIL=$email;
						$PWD=$pwd;
						$PP=$profilePictureLocation;
						$US=$defaultStatus;
						$MU=0;
						$NOS=0;
						$NOQA=0;
						$IP=0;
						$stmt->execute();
						$result=$stmt->get_result();
						//$result=mysqli_query($conn,$sql);
						//declaring session variable
						$_SESSION['memesUploaded']=0;
						$_SESSION['numberofSubscribers']=0;
						$_SESSION['numberOfQuestionsAsked']=0;
						//getting the id from the last query
						$obtainID=mysqli_insert_id($conn);
						//initialsing session variable 'id'
						$_SESSION['id']=$obtainID;

						$sql123="INSERT INTO english_meme_viewers (viewerId) VALUES ('$obtainID')";
						$result123=mysqli_query($conn,$sql123);
						$sql123="INSERT INTO hinglish_meme_viewers (viewerId) VALUES ('$obtainID')";
						$result123=mysqli_query($conn,$sql123);

						//making a directory to store all the user's uploaded memes
						mkdir("users/".$username,0777,true);
						mkdir("users/".$username."/profilepicture",0777,true);
						mkdir("users/".$username."/memes",0777,true);
						mkdir("users/".$username."/questionMemes",0777,true);
						mkdir("users/".$username."/answerMemes",0777,true);
						mkdir("users/".$username."/answerReplyMemes",0777,true);

					}else{
						$response='code already used';
						$redirect=false;
					}
				}else{
					$response='invalid invitation code';
					$redirect=false;
				}

			}else{
				//inserting data into memberstable
				$sql="INSERT INTO memberstable (name, username, email, pwd, profilePictureLocation, userStatus, memesUploaded, numberofSubscribers, numberOfQuestionsAsked, ipAddress) VALUES (?,?,?,?, ?,?, ?, ?, ?,?)";
				$stmt= $conn->prepare($sql);
				$stmt-> bind_param("ssssssdddd",$NAME,$USERNAME,$EMAIL,$PWD,$PP,$US,$MU,$NOS,$NOQA,$IP);
				$NAME=$name;
				$USERNAME=$username;
				$EMAIL=$email;
				$PWD=$pwd;
				$PP=$profilePictureLocation;
				$US=$defaultStatus;
				$MU=0;
				$NOS=0;
				$NOQA=0;
				$IP=0;
				$stmt->execute();
				$result=$stmt->get_result();
				//$result=mysqli_query($conn,$sql);
				//declaring session variable
				$_SESSION['profilePictureLocation']=$profilePictureLocation;
				$_SESSION['memesUploaded']=0;
				$_SESSION['numberofSubscribers']=0;
				$_SESSION['numberOfQuestionsAsked']=0;
				//getting the id from the last query
				$obtainID=mysqli_insert_id($conn);
				//initialsing session variable 'id'
				$_SESSION['id']=$obtainID;

				$sql123="INSERT INTO english_meme_viewers (viewerId) VALUES ('$obtainID')";
				$result123=mysqli_query($conn,$sql123);
				$sql123="INSERT INTO hinglish_meme_viewers (viewerId) VALUES ('$obtainID')";
				$result123=mysqli_query($conn,$sql123);

				//making a directory to store all the user's uploaded memes
				mkdir("users/".$username,0777,true);
				mkdir("users/".$username."/profilepicture",0777,true);
				mkdir("users/".$username."/memes",0777,true);
				mkdir("users/".$username."/questionMemes",0777,true);
				mkdir("users/".$username."/answerMemes",0777,true);
				mkdir("users/".$username."/answerReplyMemes",0777,true);
			}

			if($_POST['institute']!=''){
				$institute=mysqli_real_escape_string($conn,$_POST['institute']);

				$sql="UPDATE memberstable SET institute=? WHERE id=?";
				$stmt= $conn->prepare($sql);
				$stmt-> bind_param("sd",$INST,$ID);
				$INST=$institute;		
				$ID=$obtainID;		
				$stmt->execute();
				$result=$stmt->get_result();
			}
			

			//redirecting to the appropriate page
			if($redirect==true){
				if(isset($lastpage)){
					$response=htmlentities($lastpage);
				}
				else{
					//$response='userprofile.php?id='.$obtainID;
					$response="editPersonalInfo.php";
				}
			}
		}
		
	}
	}
	else{
		echo "username too long";
	}
}
else{
	$response="not all filled";
}
echo $response;
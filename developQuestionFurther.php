<?php 

/*if(isset($_SESSION['id'])){
	echo '<a href="userprofile.php">Back to profile page</a>';
}
else{
	echo '<a href="index.php">Back to home page</a>';
}*/
//session_start();
ob_start();
include 'top.php';
$b=ob_get_contents();
ob_end_clean();

$title = "Develop Question Further";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $b);

echo $buffer;
?>
<img src="logo/m_gold.png" class="develop-logo centering">
<p class="develop-text">We are working on a memeMaker with amazing features. If you have got ideas that you would like to be included in this memeMaker, you can mail us at support@meagl.com.</p>
<?php
if(!isset($_SESSION['id'])){
?>
<p class="develop-sign-in">Not signed in yet? <a href="signup.php">Sign in</a> now!</p>
<?php
}
?>

</div>
</body>
</html>

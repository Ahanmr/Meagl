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

$title = "Contact Us";
$buffer = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1' . $title . '$3', $b);

echo $buffer;
?>
<img src="logo/m_gold.png" class="develop-logo centering">
<p class="develop-text">Have any suggestions, feedback, business queries or have found some bugs? Feel free to mail us at support@meagl.com</p>
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

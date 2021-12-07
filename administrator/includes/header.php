<?php
	
//put inside body tag of page
//Auth Logout Warning Popup. Referenced from auth.php file
?>
<div id="authLogoutWarning" style="display:none;position:absolute;top:50px;left:10%;">
You are about to be logged out! Click Ok to stay signed in.
</div>


<?php
//
//logo goes here
//menu will go here
//
echo "<div class='nav_menu'>";
echo "Home | Grooming | Kennels | Welcome {$_SESSION['user_name']} |";
if($_SESSION['valid_login']==1){
	echo "<a href='includes/logout.php'>Logout</a>";
}
echo "</div>";

?>

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
echo "<a href='home.php'>Home</a> | Grooming | <a href='reservation.php'>New Reservation/Appointment</a> | Welcome {$_SESSION['user_name']} | <a href='includes/logout.php'>Logout</a>";
echo "</div>";

?>

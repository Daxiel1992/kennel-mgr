<?php
//
//logo goes here
//menu will go here
//
echo "<div class='nav_menu'>";
echo "<a href='home.php'>Home</a> | Grooming | <a href='reservation.php'>New Reservation/Appointment</a> | Welcome {$_SESSION['user_name']} | <a href='includes/logout.php'>Logout</a>";
echo "</div>";

?>

<?php

/*
 * A template allowing a conistant navigation across the admin section of the site
 */
?>
<nav class="cloud"><ul><?php
// tee hee hee
$mensecs = new Section();
$result = $mensecs->listSections();
while($row = mysql_fetch_assoc($result)){
    echo "<li><a href='section.php?id=" . $row["id"] . "'>" . $row["name"] . "</a></li>";
    ?><?php
}
?><li id="gallery"><a href="gallery.php">Gallery</a></li>
<li id="contact"><a href="contact.php">Contact Us</a></li></ul></nav>

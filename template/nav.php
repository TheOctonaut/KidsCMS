<?php

/*
 * A template allowing a conistant navigation across the admin section of the site
 */
?>
<nav class="body"><ul><li><a href="index.php"><?php echo $lang->home; ?></a></li><?php
// tee hee hee
$mensecs = new Section();
$result = $mensecs->listSections();
while($row = mysql_fetch_assoc($result)){
    echo "<li><a href='section.php?id=" . $row["id"] . "'>" . $row["name"] . "</a></li>";
    ?><?php
}
?></ul></nav>

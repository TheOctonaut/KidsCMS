<?php

/**
 * A template file to enable a consistant header across all pages.
 */
?>
<header class="body cloud"><h1><img src="img/cloud.png" alt="Kids' Academy International Pre-school" title="Kids' Academy Logo" /></h1>
    <ul id="langselect"><li id="langselecttext"><?php echo $lang->lang;?>:</li>
        <li class="flag"><a href="<?php echo $_SERVER['SELF'];?>?lang=en-GB"><img src="img/flags/png/gb.png" /></a></li>
        <li class="flag"><a href="<?php echo $_SERVER['SELF'];?>?lang=ja"><img src="img/flags/png/jp.png" /></a></li><!--
        <li class="flag"><a href="<?php echo $_SERVER['SELF'];?>?lang=th"><img src="img/flags/png/th.png" /></a></li>
        <li class="flag"><a href="<?php echo $_SERVER['SELF'];?>?lang=ga"><img src="img/flags/png/ie.png" /></a></li>-->
  </ul><?php include_once("nav.php");?></header>
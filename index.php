<?php
session_start();
$loggedin = false;
$subd = "/kidsacademy";
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");
$langcode = "";
if(isset($_GET["lang"])){
    $langcode = mEx($_GET["lang"]);
} elseif(isset($_COOKIE["lang"])){
    $langcode = mEx($_COOKIE["lang"]);
} else {
    $langcode = "en-GB";
}
$lang = "";
$langfilename = $_SERVER['DOCUMENT_ROOT'].$subd."/lang/".$langcode.".xml";
if (file_exists($langfilename)) {
    $lang = simplexml_load_file($langfilename);
    setcookie('lang', $langcode);
} else {
    exit('Language File Not Found');
}
if(isset($_SESSION["id"])){
	$User = new User();
	if($User->getUserById($_SESSION["id"])){
		$loggedin = true;
	} else {
		//echo "didnt get User";
	}
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <title>Kids' Academy Testsite</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/default.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/html5.css" type="text/css" media="screen">
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/scriptaculous.js"></script>
    <!--[if IE]>
    	<script src="js/html5.js"></script>
    <![endif]-->
  </head>
  <body id="index" class="home">
      <?php
      //include the header template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/header.php");
      //include the navigation template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/nav.php"); ?>
      <section class="body">
    <?
        if (isset($_REQUEST["warn"])){
                if($_REQUEST["warn"] == "loginfail"){
                ?>
                        <p><?php echo $lang->loginfail; ?></p>
                <?
                }
        }
        include_once("template/feedback.php");
        if ($loggedin){ ?>
      <p>
          <?php echo $lang->greeting . " " . $User->getName() . " (" . $User->getEmail() . ") "; ?>
          <a href="auth.php?act=logout"><?php echo $lang->logout; ?></a>
      </p>
      <p>
  <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FBangkok-Thailand%2FKids-Academy-International-Pre-school%2F113396902069677&amp;layout=box_count&amp;show_faces=true&amp;width=450&amp;action=like&amp;font&amp;colorscheme=light&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:65px;" allowTransparency="true"></iframe></p><?php
      } else { ?>
            <h3><?php echo $lang->login; ?></h3>
            <section class="login">
		<form action="auth.php" method="post">
                    <input type="hidden" value="login" id="act" name="act" />
                    <label for="email"><?php echo $lang->email;?>: </label>
                    <input id="email" name="email" type="email"/>
                    <label for="password"><?php echo $lang->password;?>: </label>
                    <input id="password" name="password" type="password" />
                    <label for="submit"> </label>
                    <input class="submit" id="submit" name="submit" type="submit" value="<?php echo $lang->login; ?>" />
                </form>
            </section>
            <p><?php
            echo $lang->or; ?> <a href="register.php"><?php
            echo $lang->register;
            ?></a></p>
<? } //end of logged out user ?>

      </section>
      <?php
      //include the footer template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/footer.php"); ?>
  </body>
</html>
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
    <title>Kids' Academy Testsite Admin</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/default.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/html5.css" type="text/css" media="screen">
    <!--[if IE]>
    	<script src="js/html5.js"></script>
    <![endif]-->
  </head>
  <body id="index" class="home">
      <?php
      //include the header template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/header.php");

      if ($loggedin){
          if($User->getPower() >= 3){
              //include the admin navigation template
              include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/nav_admin.php");
          } ?>
      <section class="body">
    <?
        if (isset($_REQUEST["msg"])){
            $strings = $lang->xpath($_REQUEST["msg"]);
            while(list( , $node) = each($strings)) {
                ?>
                        <p><?php echo $node; ?></p>
                <?
            }
        }?>
          <?php echo $lang->greeting . " " . $User->getName() . " (" . $User->getEmail() . ") "; ?>
          <a href="auth.php?act=logout"><?php echo $lang->logout; ?></a>
      </p>
      <?php
        if($User->getPower() >= 3){
            //
            ?><p>Welcome, administrator. This is the admin section of the website.</p><?php
        } else {
            echo "<p>" . $lang->no_permission . "</p>";
        }
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
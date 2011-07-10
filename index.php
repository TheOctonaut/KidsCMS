<?php
session_start();
$loggedin = false;
$subd = "/kidsacademy";
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/includes/lang.php");
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/includes/sessioncontrol.php");
?><!DOCTYPE html>
<html lang="en">
  <head>
    <title>Kids' Academy Testsite</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/css.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/js.php"); ?>
  </head>
  <body id="index" class="home">
      <?php
      //include the header template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/header.php");
      //include the navigation template
      //include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/nav.php"); ?>
      
    <?php include_once("template/splash.php");?>
      <section class="body">
    <?php include_once("template/feedback.php");?>
          <iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FKids-Academy-International-Pre-school%2F113396902069677&amp;width=292&amp;colorscheme=light&amp;show_faces=false&amp;border_color=%23ffffff&amp;stream=false&amp;header=false&amp;height=62" 
            scrolling="no" frameborder="0" style="border:none; overflow:hidden; 
            width:292px; height:100px;" allowTransparency="true"></iframe><?php
        if (1==2){ ?>
      <p>
          <?php echo $lang->greeting . " " . $User->getName() . " (" . $User->getEmail() . ") "; ?>
          <a href="auth.php?act=logout"><?php echo $lang->logout; ?></a>
      </p>
  <?php
      } elseif(2==3) { ?>
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
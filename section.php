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
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/nav.php"); ?>
      <section class="body">
    <?php include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/feedback.php"); ?>
      <?php if ($loggedin){ ?>
          <p>
              <?php echo $lang->greeting . " " . $User->getName() . " (" . $User->getEmail() . ") "; ?>
              <a href="auth.php?act=logout"><?php echo $lang->logout; ?></a>
          </p>
          <?php $Section = new Section();
          $Section->getSectionById(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT));
          ?>
          <h2><?php echo $Section->getName(); ?></h2><details><summary><?php echo $Section->getSummary(); ?></summary></details>
          <h3>Articles in this Section</h3>
          <?php 
          $articles = $Section->getArticles();
          if($articles){
          foreach ($articles as $a){
          ?>
          <ol>
              <li>
                  <h4><?php echo $a->getTitle();?></h4>
                  <p><?php echo $a->getSummary();?></p>
              </li>
          </ol>
          <?php
          }
          } else {
              ?><p>No articles found.</p><?
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
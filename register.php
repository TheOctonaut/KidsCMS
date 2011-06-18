<?php
session_start();
$loggedin = false;
$subd = "/kidsacademy";
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");
$lang = "";
if(isset($_REQUEST["lang"])){
    $lang = mEx($_REQUEST["lang"]);
} else {
    $lang = "en-GB";
}
$langfilename = $_SERVER['DOCUMENT_ROOT'].$subd."/lang/".$lang.".xml";
if (file_exists($langfilename)) {
    $lang = simplexml_load_file($langfilename);
} else {
    exit('Language File Not Found');
}
if(isset($_SESSION["user_id"])){
	//echo $_SESSION["user_id"];
	$User = new User();
	if($User->getUserById($_SESSION["user_id"])){
		$loggedin = true;
	} else {
		//echo "didnt get User";
	}
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <title>Kid's Academy Testsite</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/default.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/html5.css" type="text/css" media="screen">
    <!--[if IE]>
    	<script src="js/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/scriptaculous.js"></script>
    <script type="text/javascript" src="js/validation.js"></script>
    <script type="text/javascript">
        var RecaptchaOptions = {
            theme : 'clean'
        };
    </script>
  </head>
  <body id="index" class="home">
      <?php
      //include the header template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/header.php");
      //include the navigation template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/nav.php"); ?>
      <section class="body">
      <?php if ($loggedin){ ?>
      <p>
          <?php echo $lang->greeting . " " . $User->getName() . " (" . $User->getEmail() . ") "; ?>
          <a href="auth.php?act=logout"><?php echo $lang->logout; ?></a>
      </p>
      <?php } else { ?>
      <?
        if (isset($_REQUEST["warn"])){
                if($_REQUEST["warn"] == "loginfail"){
                ?>
                        <p><?php echo $lang->loginfail; ?></p>
                <?
                } elseif($_REQUEST["warn"] == "resultError"){
                        ?><p>The Result Wasn't Right!</p><?
                } elseif($_REQUEST["warn"] == "dbError"){
                        ?><p>There was a database error.</p><?
                }
        } ?>
            <h3><?php echo $lang->register;?></h3>
            <section class="form">
                <form action="auth.php" method="post">
		<input type="hidden" value="register" id="act" name="act" />
                <div class="form_half">
                    <label for="name"><?php echo $lang->name;?>: </label>
                    <input id="name" name="name" onkeyup="javascript:check(this.id);" /><span class="validator" id="name_v"></span>
                    <label for="email"><?php echo $lang->email;?>: </label>
                    <input id="email" name="email" type="email" onkeyup="javascript:check(this.id);" /><span class="validator" id="email_v"></span>
                    <label for="password"><?php echo $lang->password;?> <abbr class="formhelp" title="<?php echo $lang->password_help;?>">(?)</abbr>: </label>
		<input autocomplete='off' id="password" name="password" type="password" onkeyup="javascript:check(this.id);" /><span class="validator" id="password_v"></span>
                </div>
                <div class="form_half">
                    <label for="display_name"><?php echo $lang->display_name;?>: </label>
                    <input id="display_name" name="display_name" onkeyup="javascript:check(this.id);" /><span class="validator" id="display_name_v"></span>
                    <label for="contact_number"><?php echo $lang->contact_number;?>: </label>
                    <input id="contact_number" name="contact_number" onkeyup="javascript:check(this.id);" /><span class="validator" id="contact_number_v"></span>
                    <label for="retype_password"><?php echo $lang->retype_password;?>: </label>
                    <input autocomplete='off' id="retype_password" name="retype_password" type="password" onkeyup="javascript:passcheck('retype_password', 'password');" /><span class="validator" id="retype_password_v"></span>
                </div>
                <label for="recaptcha_challenge_field"><?php echo $lang->captcha;?> <abbr class="formhelp" title="<?php echo $lang->captcha_help;?>">(?)</abbr>: </label>
                <?php require_once('includes/recaptchalib.php');
                  echo recaptcha_get_html("6LcXe8MSAAAAAIPob6_Oa_3Kal8IiPZaYLQH-STl"); ?>
		<label for="submit"> </label>
		<input class="submit" id="user_submit" name="user_submit" type="submit" value="<?php echo $lang->register;?>" />
		</form>
	</section>
<? } //end of logged out user ?>
      </section>
      <?php
      //include the footer template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/footer.php"); ?>
  </body>
</html>
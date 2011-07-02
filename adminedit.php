<?php
session_start();
$loggedin = false;
$subd = "/kidsacademy";
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");

if(isset($_SESSION["id"])){
    $User = new User();
    if($User->getUserById($_SESSION["id"])){
            $loggedin = true;
    } else {
            //echo "didnt get User";
    }
}
require_once("includes/lang.php");
if($loggedin){
    // Check that the user has at least a power of 3.
    // In future this value might not be hard coded.
    if($User->getPower() >= 3){
        // what type of objects are we working with here?
        switch ($_REQUEST["type"]){
            case "users":
                // make us a user who's going to have the act done to them
                $EditUser = new User();
                switch($_REQUEST["act"]){
                    case "save":
                        // check that all our values are set and sanitised.
                        $name = isset($_POST["name"]) ? filter_var($_POST["name"], FILTER_SANITIZE_STRING) : false;
                        $dname = isset($_POST["display_name"]) ? filter_var($_POST["display_name"], FILTER_SANITIZE_STRING) : false;
                        // we have to do some special stuff for id,
                        // as it isn't always an integer. This is simpler to
                        // maintain than having separate functions 
                        // for new/updated users!
                        $uid = "";
                        if(isset($_POST["id"])){
                            if($_POST["id"] == "new"){
                                $uid = "new";
                            } elseif(is_numeric($_POST["id"])){
                                $uid = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
                            } else {
                                $uid = -1;
                            }
                        } else {
                            $uid = -1;
                        }
                        $cnum = isset($_POST["contact_number"]) ? filter_var($_POST["contact_number"], FILTER_SANITIZE_NUMBER_INT) : false;
                        $ugp = isset($_POST["user_group"]) ? filter_var($_POST["user_group"], FILTER_SANITIZE_NUMBER_INT) : false;
                        $eml = isset($_POST["email"]) ? filter_var($_REQUEST["email"], FILTER_SANITIZE_EMAIL) : false;
                        // if all of the above passed...
                        if($name && $dname && $cnum && $ugp >= 0 && $eml){
                            $invalid = array();
                            $status = true;
                            if(($uid >= 0 && $uid <= 99999) || $uid == "new") { $EditUser->setId($uid); } else { array_push($invalid, "invalid_user_id"); $status = false; }
                            if(strlen(filter_var($name, FILTER_SANITIZE_STRING)) > 2){ $EditUser->setName($name); } else { array_push($invalid, "invalid_user_name"); $status = false; }
                            if(strlen(filter_var($dname, FILTER_SANITIZE_STRING)) > 2){ $EditUser->setDisplayName($dname); } else { array_push($invalid, "invalid_user_display_name"); $status = false; }
                            if(filter_var($eml, FILTER_VALIDATE_EMAIL)){ $EditUser->setEmail($eml); } else { array_push($invalid, "invalid_user_email"); $status = false; }
                            if($cnum >= 9999 && $cnum < 999999999999) { $EditUser->setContactNumber($cnum); } else { array_push($invalid, "invalid_user_contact_number"); $status = false; }
                            if($ugp >= 0 && $ugp <= 9) { $EditUser->setUserGroup($ugp); } else { array_push($invalid, "invalid_user_group"); $status = false; }
                            $sendemail = false;
                            if($uid == "new"){
                                $sendemail = true;
                                // we'll use the salt generator to
                                //  create a random password.
                                $temppass = generateSalt(7) . rand(0, 9);
                                if($EditUser->is_valid_password($temppass)){
                                    //if they've matched, we generate some salt
                                    $EditUser->setSalt(generateSalt(5));
                                    //sprinkle the salt on the end of the password
                                    //cook the whole thing with MD5
                                    //and set it as our password to be stored
                                    $EditUser->setPassword(md5($temppass . $EditUser->getSalt()));
                                } else {
                                    //not a valid password
                                    $status = false;
                                    // TODO: lang.xml generated_invalid_password
                                    array_push($msgs, "generated_invalid_password");
                                }
                            }
                            // if none of the above checks have set our status to false
                            if($status){
                                // try to save the user
                                if($EditUser->save()){
                                    if($sendemail){
                                        // email the user their new password
                                        $URL = "http://www.mononyk.us/kidsacademy/";
                                        $subject = "Account Created at Kids\' Academy";
                                        $message = '<h1>Hello ' . $EditUser->getName() . "!</h1><p>You have had an account created for you at <strong><a href='" . $URL . "' />Kids' Academy</a></strong>.";
                                        $message .= "Your temporary password is: <strong>" . $temppass. "</strong>. You can use it to log into the site with your new account, but please don't forget to change it to something you will remember.</p>";
                                        $message .= "<p>See you soon!</p><h3>The Kids' Academy family</h3>";
                                        $headers  = 'MIME-Version: 1.0' . "\r\n";
                                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                                        $headers .= 'From: newaccounts@kidsacademy.ac.th' . "\r\n" .
                                                        'Reply-To: info@campusbike.ucc.ie' . "\r\n" .
                                                        'X-Mailer: PHP/' . phpversion();

                                        if(mail($EditUser->getEmail(), $subject, $message, $headers)){

                                        } else {
                                                error_log("TEMPPASS MAIL NOT SENT");
                                        }
                                    }
                                    moveOn("adminview.php", "?type=users&msg=saved_user");
                                } else {
                                    moveOn("adminedit.php", "?type=users&id=" . $_POST["id"] . "&msg=saved_user_error");
                                }
                            } else {
                                //otherwise, return error messages to the edit page.
                                $extra = "?type=users&id=" . $_POST["id"];
                                foreach ($invalid as $inv){
                                    $extra .= "&msg[]=" . $inv;
                                }
                                moveOn("adminedit.php", $extra);
                            }
                        } else {
                            //otherwise, return error messages to the edit page.
                                $extra = "?type=users&id=" . $_POST["id"];
                                $extra .= "&msg=must_complete_form";
                                moveOn("adminedit.php", $extra);
                        }
                        break;
                    case "delete":
                        $uid = "";
                        $status = false;
                        if(isset($_REQUEST["id"])){
                            if(is_numeric($_REQUEST["id"])){
                                $uid = filter_var($_REQUEST["id"], FILTER_SANITIZE_NUMBER_INT);
                            } else {
                                $uid = -1;
                            }
                        } else {
                            $uid = -1;
                        }
                        if($uid >= 0 && $uid <= 99999) { 
                            $EditUser->setId($uid);
                            if($EditUser->delete()){
                                $status = true;
                            }
                        }
                        if($status){
                            $extra = "?type=users&msg=deleted_user";
                            moveOn("adminview.php", $extra);
                        } else {
                            $extra = "?type=users&msg=deleted_user_error";
                            moveOn("adminview.php", $extra);
                        }
                        break;
                    default:
                        // No action needed. Allow the the edit input screen.
                        break;
                } //end act switch
                break;
            case "groups":
                $EditUserGroup = new UserGroup();
                switch ($_REQUEST["act"]){
                    case "save":
                        $gid = "";
                        if(isset($_POST["id"])){
                            if($_POST["id"] == "new"){
                                $gid="new";
                            } elseif(is_numeric($_POST["id"])){
                                $gid = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
                            } else {
                                $gid = -1;
                            }
                        } else {
                            $gid = -1;
                        }
                        $gname = isset($_POST["user_group_name"]) ? filter_var($_POST["user_group_name"], FILTER_SANITIZE_STRING) : false;
                        $gpower = isset($_POST["user_group_power"]) ? filter_var($_POST["user_group_power"], FILTER_SANITIZE_NUMBER_INT) : false;
                        if((is_numeric($gid) || $gid=="new") && $gname && $gpower >= 0 && $gpower <= 9){
                            $EditUserGroup->setName($gname);
                            $EditUserGroup->setPower($gpower);
                            $EditUserGroup->setId($gid);
                            if($EditUserGroup->save()){
                                $extra = "?type=groups&message=saved_group";
                                moveOn("adminview.php", $extra);
                            } else {
                                $extra = "?type=groups&id=" . $_POST["id"] . "&message=saved_group_error";
                                moveOn("adminedit.php", $extra);
                            }
                        } else {
                            $extra = "?type=groups&id=" . $_POST["id"] . "&message=must_complete_form";
                            moveOn("adminedit.php", $extra);
                        }
                        break;
                    case "delete":
                        $gid = "";
                        $status = false;
                        if(isset($_POST["id"])){
                            if(is_numeric($_POST["id"])){
                                $gid = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
                            } else {
                                $gid = -1;
                            }
                        } else {
                            $gid = -1;
                        }
                        if($gid >= 0 && $uid <= 99999) { 
                            $EditUserGroup->setId($uid);
                            if($EditUserGroup->delete()){
                                $status = true;
                            }
                        }
                        if($status){
                            $extra = "?type=groups&msg=deleted_user_group";
                            moveOn("adminview.php", $extra);
                        } else {
                            $extra = "?type=groups&msg=deleted_user_group_error";
                            moveOn("adminview.php", $extra);
                        }
                        break;
                    default;
                        // No action needed. Allow the the edit input screen.
                        break;
                }
                break;
            case "articles": 
                $Article = new Article();
                switch ($_REQUEST['act']){
                    case "save":
                        if(isset($_REQUEST["section_id"])){
                            if($Article->setSection(intval())){
                                //success
                            } else {
                                // add error to list of messages
                            }
                        } else {
                            // feedback
                        }
                        if(isset($_REQUEST["title"])){
                            if($Article->setTitle($_REQUEST["title"])){
                                //success
                            } else {
                                // error
                            }
                        } else {
                            // error
                        }
                        if(isset($_REQUEST["summary"])){
                            if($Article->setSummary($_REQUEST["summary"])){
                                // success
                            } else {
                                // error
                            }
                        } else {
                            // error
                        }
                        if(isset($_REQUEST["content"])){
                            if($Article->setContent($_REQUEST["content"])){
                                // success
                            } else {
                                // error
                            }
                        } else {
                            // error
                        }
                        if(isset($_REQUEST["author"])){
                            if($Article->setUser(intval($_REQUEST["author"]))){
                                // success
                            } else {
                                // error
                            }
                        } else {
                            // error
                        }
                        if(isset($_REQUEST["published"])){
                            if($Article->setPublished($_REQUEST["published"])){
                                // success
                            } else {
                                // error
                            }
                        } else {
                            // error
                        }
                        break;
                    case "delete";
                        break;
                    default:
                        break;
                }
                //edit/save article
                break;
            case "sections":
                $Section= new Section();
                switch ($_REQUEST['act']){
                    case "save":
                        break;
                    case "delete";
                        break;
                    default:
                        break;
                }
                //edit/save section
                break;
            default: 
                // do something sane
                break;
        } //end type switch
    } // end if user has power
} // end if logged in
?><!DOCTYPE html>
<html lang="<?php echo $langcode; ?>">
  <head>
    <title>Kids' Academy - <?php echo $lang->title_admin_edit; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/reset.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/default.css" type="text/css" media="screen">
    <link rel="stylesheet" href="css/html5.css" type="text/css" media="screen">
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/scriptaculous.js"></script>
    <script type="text/javascript" src="js/validation.js"></script>
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
      <section class="body"><?php echo $lang->greeting . " " . $User->getName() . " (" . $User->getEmail() . ") "; ?>
          <a href="auth.php?act=logout"><?php echo $lang->logout; ?></a>
      </p>
      <section class="form">
      <form action="adminedit.php" method="post">
          <input id="id" name="id" type="hidden" value="<?php echo filter_var($_GET["id"], FILTER_SANITIZE_STRING);?>" />
          <input id="type" name="type" type="hidden" value="<?php echo filter_var($_GET["type"], FILTER_SANITIZE_STRING);?>" />
          <input id="act" name="act" type="hidden" value="save" />
          
      <?php
        if($User->getPower() >= 3){
            switch($_GET["type"]){
                case "users":
                    if($_GET["id"] == "new"){
                        ?>
                            <h3><?php echo $lang->creating_new_user;?></h3>
                            <?php //include the feedback template
                            include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/feedback.php"); ?>
                            <p><?php echo $lang->temp_pass;?></p>
                            <div class="form_half">
                                <label for="name"><?php echo $lang->name;?>: </label>
                                <input id="name" name="name" onkeyup="javascript:check(this.id);" /><span class="validator" id="name_v"></span>
                                <label for="email"><?php echo $lang->email;?>: </label>
                                <input id="email" name="email" type="email" onkeyup="javascript:check(this.id);" /><span class="validator" id="email_v"></span>
                                <label for="user_group">Group: </label><select id="user_group" name="user_group">
                                <?php
                                $ng = new UserGroup();
                                $result = $ng->listUserGroups();
                                while ($row = mysql_fetch_assoc($result)){
                                    ?><option value="<?php echo $row["id"];?>"><?php echo $row["name"];?></option><?php
                                } ?></select>
                            </div>
                            <div class="form_half">
                                <label for="display_name"><?php echo $lang->display_name;?>: </label>
                                <input id="display_name" name="display_name" onkeyup="javascript:check(this.id);" /><span class="validator" id="display_name_v"></span>
                                <label for="contact_number"><?php echo $lang->contact_number;?>: </label>
                                <input id="contact_number" name="contact_number" onkeyup="javascript:check(this.id);" /><span class="validator" id="contact_number_v"></span>
                            </div>
                        <?php
                        } else {
                            $nu = new User();
                            if($nu->getUserById($_GET["id"])){
                                    ?>
                                <h3><?php echo $lang->editing_user . " #" . $nu->getID() ?></h3>
                                <?php //include the feedback template
                                include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/feedback.php"); ?>
                                <div class="form_half">
                                    <label for="name"><?php echo $lang->name;?>: </label>
                                    <input id="name" name="name" onkeyup="javascript:check(this.id);" value="<? echo $nu->getName();?>" /><span class="validator" id="name_v"></span>
                                    <label for="email"><?php echo $lang->email;?>: </label>
                                    <input id="email" name="email" type="email" onkeyup="javascript:check(this.id);" value="<? echo $nu->getEmail();?>"/><span class="validator" id="email_v"></span>
                                    <label for="user_group">Group: </label><select id="user_group" name="user_group">
                                    <?php
                                    $gid =  $nu->getUserGroup();
                                    $sel = "";
                                    $ng = new UserGroup();
                                    $result = $ng->listUserGroups();
                                    while ($row = mysql_fetch_assoc($result)){
                                        if($gid == $row["id"]){
                                            $sel = " selected='selected'";
                                        }
                                        ?><option value="<?php echo $row["id"];?>"<?php echo $sel?>><?php echo $row["name"];?></option><?php
                                        $sel = "";
                                    } ?></select>
                                </div>
                                <div class="form_half">
                                    <label for="display_name"><?php echo $lang->display_name;?>: </label>
                                    <input id="display_name" name="display_name" onkeyup="javascript:check(this.id);" value="<? echo $nu->getDisplayName();?>"/><span class="validator" id="display_name_v"></span>
                                    <label for="contact_number"><?php echo $lang->contact_number;?>: </label>
                                    <input id="contact_number" name="contact_number" onkeyup="javascript:check(this.id);" value="<? echo $nu->getContactNumber();?>"v/><span class="validator" id="contact_number_v"></span>
                                </div>
                            <?php
                        } //end if user exists check
                    } //end id set check
                    break;
                case "articles":
                    $Article =& new Article();
                    echo $Article->createHTMLEditForm();
                    break;
                case "sections":
                    $Section =& new Section();
                    echo $Section->createHTMLEditForm();
                    break;
                } //end case switch
            ?><input type="submit" value="Save" /></form><?php
        } else {
            echo "<p>" . $lang->no_permission . "</p>";
        } ?></section><? //end of permission check& form
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
<?php
session_start();
$loggedin = false;
$subd = "/kidsacademy";
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/includes/lang.php");
require_once($_SERVER['DOCUMENT_ROOT'].$subd."/includes/sessioncontrol.php");

if($loggedin){
    // Check that the user has at least a power of 3.
    // In future this value might not be hard coded.
    if($power >= 2){
        switch($_REQUEST["act"]){
            case "save":
                $msgarray = array();
                $valid = true;
                // what type of objects are we working with here?
                switch ($_REQUEST["type"]){
                    case "users":
                        // make us a user who's going to have the act done to them
                        $EditUser = new User();
                        // check that all our values are set and sanitised.
                        if(isset($_POST["name"])){
                            if(!$EditUser->setName($_POST["name"])){
                                array_push($msgarray, "invalid_name");
                                $valid = false;
                            }
                        } else {
                            array_push($msgarray, "invalid_name_not_set");
                            $valid = false;
                        }
                        if(isset($_POST["display_name"])){
                            if(!$EditUser->setDisplayName($_POST["display_name"])){
                                array_push($msgarray, "invalid_display_name");
                                $valid = false;
                            }
                        } else {
                            array_push($msgarray, "invalid_display_name_not_set");
                            $valid = false;
                        }
                        if(isset($_POST["id"])){
                            if($_POST["id"] == "new"){
                                if(!$EditUser->setId("new")){
                                    array_push($msgarray, "invalid_user_id");
                                    $valid = false;
                                }
                            } elseif(is_numeric($_POST["id"])){
                                if(!$EditUser->setId(intval($_POST["id"]))){
                                    array_push($msgarray, "invalid_user_id");
                                    $valid = false;
                                }
                            } else {
                                array_push($msgarray, "invalid_user_id");
                                $valid = false;
                            }
                        } else {
                            array_push($msgarray, "invalid_user_id_not_set");
                            $valid = false;
                        }
                        if(isset($_POST["contact_number"])){
                            if(is_numeric($_POST["contact_number"])){
                                if(!$EditUser->setContactNumber(intval($_POST["contact_number"]))){
                                    array_push($msgarray, "invalid_contact_number");
                                    $valid = false;
                                }
                            } else {
                                array_push($msgarray, "invalid_contact_number");
                                $valid = false;
                            }
                        } else {
                           array_push($msgarray, "invalid_contact_number_not_set");
                           $valid = false;
                        }
                        if(isset($_POST["user_group"])){
                            if(is_numeric($_POST["user_group"])){
                                if(!$EditUser->setUserGroup(intval($_POST["user_group"]))){
                                    array_push($msgarray, "invalid_user_group");
                                    $valid = false;
                                }
                            } else {
                                array_push($msgarray, "invalid_user_group");
                                $valid = false;
                            }
                        } else {
                           array_push($msgarray, "invalid_user_group_not_set");
                           $valid = false;
                        }
                        if(isset($_POST["email"])){
                            if(!$EditUser->setEmail(filter_var($_POST["email"], FILTER_SANITIZE_EMAIL))){
                                array_push($msgarray, "invalid_email");
                                $valid = false;
                            }
                        } else {
                            array_push($msgarray, "invalid_email_not_set");
                            $valid = false;
                        }
                        if($valid){
                            if(!$EditUser->save()){
                                array_push($msgarray, "saved_user");
                            } else {
                                array_push($msgarray, "save_user_failed");
                                $valid = false;
                            }
                        }
                        break;
                    case "groups":
                        $EditUserGroup = new UserGroup();
                        // make us a user who's going to have the act done to them
                        if(isset($_POST["user_group_name"])){
                            if(!$EditUser->setName($_POST["name"])){
                                array_push($msgarray, "invalid_name");
                                $valid = false;
                            }
                        } else {
                            array_push($msgarray, "invalid_name_not_set");
                            $valid = false;
                        }
                        if(isset($_POST["id"])){
                            if($_POST["id"] == "new"){
                                if(!$EditGroup->setId("new")){
                                    array_push($msgarray, "invalid_user_group_id");
                                    $valid = false;
                                }
                            } elseif(is_numeric($_POST["id"])){
                                if(!$EditGroup->setId(intval($_POST["id"]))){
                                    array_push($msgarray, "invalid_user_group_id");
                                    $valid = false;
                                }
                            } else {
                                array_push($msgarray, "invalid_user_group_id");
                                $valid = false;
                            }
                        } else {
                            array_push($msgarray, "invalid_user_group_id_not_set");
                            $valid = false;
                        }
                        if(isset($_POST["user_group_power"])){
                            if(is_numeric($_POST["user_group_power"])){
                                if(!$EditGroup->setPower(intval($_POST["contact_number"]))){
                                    array_push($msgarray, "invalid_group_power");
                                    $valid = false;
                                }
                            } else {
                                array_push($msgarray, "invalid_group_power");
                                $valid = false;
                            }
                        } else {
                           array_push($msgarray, "invalid_contact_number_not_set");
                           $valid = false;
                        }
                        if($valid){
                            if(!$EditUser->save()){
                                array_push($msgarray, "saved_user");
                            } else {
                                array_push($msgarray, "save_user_failed");
                                $valid = false;
                            }
                        }
                        break;
                    case "articles": 
                        $Article = new Article();
                        if(isset($_REQUEST["section_id"])){
                            if(is_numeric($_REQUEST["section_id"])){
                                if(!$Article->setSection(intval())){
                                    $valid=false;
                                    array_push($msgarray, "invalid_article_section");
                                }
                            } else {
                                $valid=false;
                                array_push($msgarray, "invalid_article_section");    
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_article_section_not_set");
                        }
                        if(isset($_REQUEST["title"])){
                            if(!$Article->setTitle($_REQUEST["title"])){
                                $valid = false;
                                array_push($msgarray, "invalid_article_title");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_article_title_not_set");
                        }
                        if(isset($_REQUEST["summary"])){
                            if(!$Article->setSummary($_REQUEST["summary"])){
                                $valid = false;
                                array_push($msgarray, "invalid_article_summary");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_article_summary_not_set");
                        }
                        if(isset($_REQUEST["content"])){
                            if(!$Article->setContent($_REQUEST["content"])){
                                $valid = false;
                                array_push($msgarray, "invalid_article_content");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_article_content_not_set");
                        }
                        if(isset($_REQUEST["author"])){
                            if(is_numeric($_REQUEST["author"])){
                                if(!$Article->setUser(intval($_REQUEST["author"]))){
                                    $valid = false;
                                    array_push($msgarray, "invalid_article_author");
                                }
                            } else {
                                $valid = false;
                                array_push($msgarray, "invalid_article_author");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_article_author_not_set");
                        }
                        if(isset($_REQUEST["published"])){
                            if(is_numeric($_REQUEST["published"])){
                                if(!$Article->setPublished(intval($_REQUEST["published"]))){
                                    $valid = false;
                                    array_push($msgarray, "invalid_article_published");
                                }
                            } else {
                                $valid = false;
                                array_push($msgarray, "invalid_article_published");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_article_published_not_set");
                        }
                        if($valid){
                            if($Article->save()){
                                array_push($msgarray, "article_saved");
                            } else {
                                $valid = false;
                                array_push($msgarray, "article_save_error");
                            }
                        }
                        break;
                    case "sections":
                        $Section= new Section();
                        if(isset($_REQUEST["name"])){
                            if(!$Section->setName($v)){
                                $valid = false;
                                array_push($msgarray, "invalid_section_name");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_section_name_not_set");
                        }
                        if(isset($_REQUEST["summary"])){
                            if(!$Section->setSummary($_REQUEST["summary"])){
                                $valid = false;
                                array_push($msgarray, "invalid_section_summary");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_section_summary_not_set");
                        }
                        if(isset($_REQUEST["media"])){
                            if(is_numeric($_REQUEST["media"])){
                                if(!$Section->setMedia(intval($_REQUEST["media"]))){
                                    $valid = false;
                                    array_push($msgarray, "invalid_section_media");
                                }
                            } else {
                                $valid = false;
                                array_push($msgarray, "invalid_section_media");
                            }
                        } else {
                            $valid = false;
                            array_push($msgarray, "invalid_section_media_not_set");
                        }
                        if($valid){
                            if($Section->save()){
                                array_push($msgarray, "section_saved");
                            } else {
                                $valid = false;
                            }
                        }
                        break;
                    default:
                        // TODO: something sane if no real type set
                }
                $destination = ($valid) ? "adminview" : "adminedit";
                moveOn($destination, array("type"=>$_REQUEST["type"],"msg"=>$msgarray,"id" => $id));
                break;
            case "delete":
                $msgarray = array();
                $valid = true;
                $DeleteObject = "";
                switch($_REQUEST["type"]){
                    case "users":
                        $DeleteObject = new User();
                        break;
                    case "groups":
                        $DeleteObject = new UserGroup();
                        break;
                    case "articles":
                        $DeleteObject = new Article();
                        break;
                    case "sections":
                        $DeleteObject = new Section();
                        break;
                    default:
                        // TODO: something sane if no real type set
                        break;
                }
                if(isset($_REQUEST["id"])){
                    if(is_numeric($_REQUEST["id"])){
                        if($DeleteObject->setId(intval($_REQUEST["id"]))){
                            if($DeleteObject->delete()){
                                array_push($msgarray, "delete_confirm");
                            } else {
                                array_push($msgarray, "delete_failed");
                            }
                        } else {
                            array_push($msgarray, "delete_failed_invalid_id");
                        }
                    } else {
                        array_push($msgarray, "delete_failed_invalid_id");
                    }
                } else {
                    array_push($msgarray, "delete_failed_no_id");
                }
                moveOn("adminview", array("type"=>$_REQUEST["type"],"msg"=>$msgarray,"id" => $_REQUEST["id"]));
                break;
            default:
                // TODO: something sane if no real act set
        } //end act switch
    } // end if user has power
} // end if logged in
?><!DOCTYPE html>
<html lang="<?php echo $langcode; ?>">
  <head>
    <title>Kids' Academy - <?php echo $lang->title_admin_edit; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/css.php"); ?>
    <?php include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/js.php"); ?>
  </head>
  <body id="index" class="home">
      <?php
      //include the header template
      include_once($_SERVER['DOCUMENT_ROOT'].$subd."/template/header.php");
      if ($loggedin){
          if($power >= 2){
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
        if($power >= 2){
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
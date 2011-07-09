<?php
session_start();
$subd = "/kidsacademy";
include($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");

$loggedin = false;
if(isset($_SESSION["id"])){
	$User = new User();
	if($User->getUserById($_SESSION["id"])){
		$loggedin = true;
	} else {
		//echo "didnt get User";
	}
}
if(strlen($_REQUEST['act']) > 0){
    if ($_POST['act'] == "register"){
        //get the length of all the form's post values together.
        $formlen = strlen($_POST["recaptcha_challenge_field"]
            .$_POST["recaptcha_response_field"]
            .$_POST["name"].$_POST["display_name"]
            .$_POST["email"].$_POST["contact_number"]
            .$_POST["password"].$_POST["retype_password"]);
        // we have 8 form elements. There should be at least 8 characters in
        //all of the post values together, or at least one is missing!
        if($formlen >= 8){
            /**
             * @var boolean We're initially assuming everything is ok, and then check for errors
             * because there are multiple ways the form can be filled incorrectly
             * and we want to inform the user of each way if it occurs.
             */
            $status = true;
            $msgs = array();
            $new_user = new User();
            $new_user->setId("new");
            if($new_user->is_valid_email($_POST['email'])){
                $new_user->setEmail($_POST["email"]);
            } else {
                //not a valid email address
                $status = false;
                array_push($msgs, "registration_invalid_email");
            }
            if(strlen(mEx($_POST["name"])) > 3){
                $new_user->setName(mEx($_POST["name"]));
            } else {
                //not a valid email address
                $status = false;
                array_push($msgs, "registration_invalid_name");
            }
            if(strlen(mEx($_POST["display_name"])) > 3){
                $new_user->setDisplayName(mEx($_POST["display_name"]));
            } else {
                //not a valid display name
                $status = false;
                array_push($msgs, "registration_invalid_display_name");
            }
            if(is_numeric(mEx($_POST["contact_number"]))){
                $new_user->setContactNumber(mEx($_POST["contact_number"]));
            } else {
                //not a valid contact number
                $status = false;
                array_push($msgs, "registration_invalid_contact_number");
            }
            $new_user->setUserGroup(0);

            //generate password hash
            $ep = $_POST["password"];
            $lp = strlen($ep);
            $rp = $_POST["retype_password"];
            if($new_user->is_valid_password($ep)){
                // password rules. Must match retyped.
                if($ep == $rp){
                    //if they've matched, we generate some salt
                    $new_user->setSalt(generateSalt(5));
                    //sprinkle the salt on the end of the password
                    //cook the whole thing with MD5
                    //and set it as our password to be stored
                    $new_user->setPassword(md5($ep . $new_user->getSalt()));
                } else {
                    //not a valid password
                    $status = false;
                    array_push($msgs, "registration_password_mismatch");
                }
            } else {
                    //not a valid password
                    $status = false;
                    array_push($msgs, "registration_invalid_password");
            }
            if(!check_captcha($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"])){
                $status = false;
            }
            if($status){
                //If no problems have been identified, attempt to
                //save the user.
                if($new_user->save()){
                    $_SESSION["id"] = $new_user->getId();
                    //we're also going to set the logged in time
                    $updatequery = "UPDATE users SET last_ip = '" . $_SERVER["REMOTE_ADDR"] . "', last_logged_in = NOW() WHERE id = " . $new_user->getId();
                    mysql_query($updatequery);
                    if(mysql_affected_rows() <= 0){
                        //not a critical error
                        error_log("New user created successfully but couldnt save log in time");
                    }
                    moveOn("index.php", "?msg=registration_complete");
                    exit;
                } else {
                    $status = false;
                    moveOn("register.php", "?msg=saved_user_error");
                }
            } else {
                //uh-oh, looks like we ran into problems!
                //feed all of these back to the user.

                //we're sending them as an array
                //but we need to print them into a request string
                //this requires a little bit of meddling because
                //the first string must have ? instead of &
                $firstime = true;
                /**
                 *
                 *  @desc This string will be passed to the registration
                 * page to inform the user of all the problems.
                 */
                $msgstring="";
                foreach ($msgs as $m){
                    if($firstime){
                        $amp = "?";
                        //ok, next time wont be the first time!
                        $firstime = false;
                    } else {
                        $amp = "&";
                    } //end firsttime check
                    $msgstring .= $amp . "msg[]=" . $m;
                } //end foreach loop
                moveOn("register.php", $msgstring);
            } //end status check
        } else {
            $status = false;
            moveOn("register.php", "?msg=must_complete_form");
        } //end form complete check
    } elseif($_REQUEST["act"] == "login"){
        $user_email = $_REQUEST["email"];
        $user_password = $_REQUEST["password"];
        $User = new User();
        $extra = $User->login($email, $password, $_SERVER["REMOTE_ADDR"]);
        moveOn("index", $extra);
    } elseif($_REQUEST["act"] == "logout"){
        session_destroy();
        moveOn("index.php");
    } elseif($_REQUEST["act"] == "activate"){
        $key = mEx($_GET["key"]);
        $nu = new User();
        if($nu->activate($key)){
            moveOn("index.php", "?msg=activationsuccess");
        }
    } else {
        // no action specified
    }
}?>
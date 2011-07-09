<?php $browsertest = ($_REQUEST["omgbrowsertest"] == "omg!") ? true : false;
$power = 0;
$loggedin = false;
if(isset($_SESSION["id"])){
    $User = new User();
    if(is_numeric($_SESSION["id"])){
        if($User->getUserById($_SESSION["id"])){
            $loggedin = true;
            $power = $User->getPower();
        } else {
            error_log("Problem logging in user_id: " . $_SESSION["id"]);
        }
    }
} ?>
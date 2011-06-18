<?php
	//utility functions
        $subd ="/kidsacademy";
	include_once($_SERVER['DOCUMENT_ROOT'].$subd."/settings.php");
	include_once($_SERVER['DOCUMENT_ROOT'].$subd."/Classes/User.php");
        include_once($_SERVER['DOCUMENT_ROOT'].$subd."/Classes/UserGroup.php");
        include_once($_SERVER['DOCUMENT_ROOT'].$subd."/Classes/Section.php");

        function check_captcha($challenge, $response){
            $status = false;
            require_once('includes/recaptchalib.php');
            $resp = recaptcha_check_answer("6LcXe8MSAAAAAFtVJbX6YpSmkINcmHWzNQyBt_Fh", $_SERVER["REMOTE_ADDR"], $challenge, $response);
            if ($resp->is_valid) {
                $status=true;
            }
            return $status;
        }

	function cb_connect(){
	//creates a connection to the database using the settings hardcoded into settings.php
		$status = false;
		if(mysql_connect(CB_HOST, CB_USERNAME, CB_PASSWORD)){
                    if (mysql_select_db(CB_NAME)) {
                        $status = true;
                    }
		}
		return $status;
	}

	function parseToXML($htmlStr){
		$xmlStr=str_replace('<','&lt;',$htmlStr);
		$xmlStr=str_replace('>','&gt;',$xmlStr);
		$xmlStr=str_replace('"','&quot;',$xmlStr);
		$xmlStr=str_replace("'",'&#39;',$xmlStr);
		$xmlStr=str_replace("&",'&amp;',$xmlStr);
		return $xmlStr;
	}

	function mEx($var){
		//Shorthand for mysql_real_escape_string() so the code looks a little tidier...
		if(cb_connect()){
			return mysql_real_escape_string($var);
		}
	}

	function generateSalt($length=5){
		//generates a random string for encryption purposes.
		$string = "";
		$possible = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		for($i=0;$i < $length;$i++) {
			$char = $possible[mt_rand(0, strlen($possible)-1)];
			$string .= $char;
		}
		return $string;
	}

	function moveOn($page, $extra = ""){
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/$page$extra");
		exit;
	}


	function manage_options($m, $r, $lang){
		//prints a table cell with buttons for managing items.
		$e = "<td><menu class='manage_options'><li><a href='adminedit.php?type=" . $m . "&id=" . $r . "'>";
		$e .= "<img src='img/icons/page_white_wrench.png' alt='" . $lang->edit . "' title='" . $lang->edit . "' /></a></li>";
		$e .= "<li><a onclick=\"javascript:return confirm('" . $lang->confirm_delete ."');\" ' href='adminedit.php?act=delete&type=" . $m . "&id=" . $r . "'>";
		$e .= "<img src='img/icons/delete.png' alt='" . $lang->delete . "' title='" . $lang->delete ."' /></a></li></menu></td>";
		return $e;
	}

	function mo($m, $r, $lang){
		//shorthand for above function!
		return manage_options($m, $rm, $lang);
	}

	function present_hour($hour){
		//turns a single/dobule figure hour into a presentable 24 hour time
		$return = "";
		if($hour >= 0 && $hour < 10){
			$return = "0" . $hour . ":00";
		} elseif ($hour >=10 && $hour <= 23){
			$return = $hour . ":00";
		}
		return $return;
	}

	function ago($datefrom,$dateto=-1){
		//returns a nice length of time since an event occurred
     if($datefrom==0) { return "A long time ago"; }
     if($dateto==-1) { $dateto = time(); }
     $datefrom = strtotime($datefrom);
     $difference = $dateto - $datefrom;
     switch(true)
     {
         case(strtotime('-1 min', $dateto) < $datefrom):
             $datediff = $difference;
             $res = ($datediff==1) ? $datediff.' sec' : $datediff.' secs';
             break;
         case(strtotime('-1 hour', $dateto) < $datefrom):
             $datediff = floor($difference / 60);
             $res = ($datediff==1) ? $datediff.' min' : $datediff.' mins';
             break;
         case(strtotime('-1 day', $dateto) < $datefrom):
             $datediff = floor($difference / 60 / 60);
             $res = ($datediff==1) ? $datediff.' hr' : $datediff.' hrs';
             break;
			case(strtotime('-1 week', $dateto) < $datefrom):
             $day_difference = 1;
             while (strtotime('-'.$day_difference.' day', $dateto) >= $datefrom)
             { $day_difference++; }
             $datediff = $day_difference;
             $res = ($datediff==1) ? 'yday' : $datediff.' days';
             break;
         case(strtotime('-1 month', $dateto) < $datefrom):
             $week_difference = 1;
             while (strtotime('-'.$week_difference.' week', $dateto) >= $datefrom)
             { $week_difference++; }
             $datediff = $week_difference;
             $res = ($datediff==1) ? '1 wk' : $datediff.' wks';
             break;
         case(strtotime('-1 year', $dateto) < $datefrom):
             $months_difference = 1;
             while (strtotime('-'.$months_difference.' month', $dateto) >= $datefrom)
             { $months_difference++; }
             $datediff = $months_difference;
             $res = ($datediff==1) ? $datediff.' month' : $datediff.' months';
             break;
         case(strtotime('-1 year', $dateto) >= $datefrom):
             $year_difference = 1;
             while (strtotime('-'.$year_difference.' year', $dateto) >= $datefrom)
             { $year_difference++; }
             $datediff = $year_difference;
             $res = ($datediff==1) ? $datediff.' yr' : $datediff.' yrs';
             break;
     }
     return $res;
	}
?>
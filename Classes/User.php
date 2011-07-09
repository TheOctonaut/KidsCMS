<?php

/**
 * User class with methods and properties.
 *
 * @author Gavin Golden
 * @package KidsCMS
 * @version 1.0
 */
$subd = "/kidsacademy";
include_once($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");

class User {
	private $id;
	private $name;
        private $displayname;
        private $email;
	private $password;
	private $salt;
	private $usergroup;
        private $contactnumber;
	private $power;
        private $lastip;

        function setLastIP( $val ) {
		$this->lastip = $val;
		return;
	}
	function getLastIP() {
		return $this->lastip;
	}
        
	function setName( $val ) {
            $val = filter_var($val, FILTER_SANITIZE_STRING);
            if(strlen($val) > 2){
		$this->name = $val;
		return true;
            } else {
                return false;
            }
	}
	function getName() {
		return $this->name;
	}
        function setDisplayName( $val ) {
            $val = filter_var($val, FILTER_SANITIZE_STRING);
            if(strlen($val) > 2){
		$this->displayname = $val;
		return true;
            } else {
                return false;
            }
	}
	function getDisplayName() {
		return $this->displayname;
	}
	function setId( $val ) {
            if(is_int($val)){
                if($val >= 0 && $val <= 99999){
                    $this->id = $val;
                    return true;
                } else {
                    return false;
                }
            } elseif ($val=="new"){
                $this->id = "new";
                return true;
            } else {
                return false;
            }
        }
	function getId() {
		return $this->id;
	}
	function setEmail( $val ) {
            if(filter_var($val, FILTER_VALIDATE_EMAIL)){
		$this->email = $val;
		return true;
            } else {
                return false;
            }
	}
	function getEmail() {
		return $this->email;
	}
	function setPassword( $val ) {
		$this->password = $val;
		return;
	}
	function getPassword() {
		return $this->password;
	}
	function setSalt( $val ) {
		$this->salt = $val;
		return;
	}
	function getSalt() {
		return $this->salt;
	}
	function setUserGroup( $val ) {
            if(is_int($val)){
                if($val >= 0 && $val <= 9){
                    $this->usergroup = $val;
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
	}
	function getUserGroup() {
		return $this->usergroup;
	}
        function setContactNumber( $val ) {
            if(is_int($val)){
                if($val >= 9999 && $val < 999999999999){
                    $this->contactnumber = $val;
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
	}
	function getContactNumber() {
		return $this->contactnumber;
	}

	function save($temppass = false){
            try {
                cb_connect();
                if($this->getId() == "new"){
                    if($temppass){
                        $temppass= generateSalt(7) . rand(0, 9);
                        if($this->is_valid_password($temppass)){
                            //if they've matched, we generate some salt
                            $this>setSalt(generateSalt(5));
                            //sprinkle the salt on the end of the password
                            //cook the whole thing with MD5
                            //and set it as our password to be stored
                            $this->setPassword(md5($temppass . $this->getSalt()));
                        }
                    }
                    $query = "INSERT INTO users (name, displayname, email, contact_number, pass, salt, user_group) ";
                    $query .= "VALUES ('". $this->getName() . "', '". $this->getDisplayName() . "', '" . $this->getEmail() . "', ";
                    $query .= $this->getContactNumber() . ", '" . $this->getPassword() . "', '" . $this->getSalt() . "', 0);";
                    $result = mysql_query($query);
                    $this->setId(mysql_insert_id());
                    if($temppass){
                        if(!$this->send_temppass_email($temppass)){
                            // TODO: handle failure to send temppass email
                        }
                    }                            
                    $key = generateSalt(8);
                    $actquery = "INSERT INTO activations (user, akey, expiry) VALUES (" . $this->getId() . ", '" . $key . "', DATE_ADD(NOW(), INTERVAL 3 DAY))";
                    $actresult = mysql_query($actquery);
                    if($this->send_activation_email($key)){
                        return true;
                    } else {
                        // TODO: handle failure to send activation email
                    }
                } elseif (is_int($this->getId())){
                    $query = "UPDATE users SET user_group = " . $this->getUserGroup() . ", displayname = '" . $this->getDisplayName() . "', email = '";
                    $query .= $this->getEmail() . "', contact_number = " . $this->getContactNumber() . " WHERE id = " . $this->getId();
                    $result = mysql_query($query);
                    if(mysql_affected_rows() == 1){
                        return true;
                    } else {
                        return false;
                    }
                }
            } catch (Exception $e){
                error_log($e);
                return false;
            }
	}

	function getUserById($uid){
		if(cb_connect()){
                    $query = "SELECT * FROM users WHERE id = " . $uid;
                    $result = mysql_query($query) or die(mysql_error());
                    if(mysql_num_rows($result) > 0){
                        $row = mysql_fetch_array($result);
                        $this->setId(intval($row[0]));
                        $this->setName($row[1]);
                        $this->setDisplayName($row[2]);
                        $this->setEmail($row[3]);
                        $this->setPassword($row[4]);
                        $this->setSalt($row[5]);
                        $this->setUserGroup(intval($row[6]));
                        $this->setContactNumber($row[9]);
                        return true;
                    } else {
                        return false;
                    }
		} else {
                    return false;
		}
	}
	function delete(){
		$status = false;
		if(cb_connect()){
			$query = "DELETE FROM users WHERE id = " . $this->getId();
			mysql_query($query) or die("Delete query was broken. " . mysql_error());
			if (mysql_affected_rows() > 0){
				$status = true;
			} else {
				error_log("Delete didn't affect any rows");
			}
		}
		return $status;
	}

	function getPower(){
		$p = 0;
		if(!isset($this->power)){
			$ug = new UserGroup();
			$ug->getUserGroupById($this->getUserGroup());
			$p = $ug->getPower();
			$this->power = $p;
		}
		return $this->power;
	}

	function login($email, $password, $ip){
		if(cb_connect()){
			$query = "SELECT id FROM users WHERE pass = MD5(CONCAT('". $password . "', salt)) AND email = '" . $email . "'";
			$result = mysql_query($query);
                        if (!$result) {
                            $message  = 'Invalid query: ' . mysql_error() . "\n";
                            $message .= 'Whole query: ' . $query;
                            die($message);
                        } else {
			if (mysql_num_rows($result) > 0){
				$row = mysql_fetch_array($result);
				$_SESSION["id"] = $row[0];
				$updatequery = "UPDATE users SET last_ip = '" . $ip.  "', last_logged_in = NOW() WHERE id = " . $row[0];
				mysql_query($updatequery);
				if(mysql_affected_rows() > 0){
					//successfully update last login time

				}
				$extra = "loggedin=1";
			} else {
				$extra = "warn=loginfail";
			}
                        }
		} else {
			$extra = "?warn=dbError";
		}
		return $extra;
	}

	function listUsers(){
		$result= false;
		if(cb_connect()){
			$query = "SELECT * FROM users";
			$result = mysql_query($query) or die("Error: " . mysql_error());
		}
		return $result;
	}

    function listEmails(){
        $result= false;
        if(cb_connect()){
            $query = "SELECT email FROM users";
            $result = mysql_query($query) or die("Error: " . mysql_error());
        }
        return $result;
    }

    function listEmailsByUserGroupId($gid){
        $result= false;
        $query = "SELECT email FROM users";
        if(cb_connect()){
                if(is_array($gid)){
                        $query .= " WHERE user_group IN (";
                        for ($i = 0; $i < count($gid); $i++){
                                if($i == 0){
                                        $query .= $gid[$i];
                                } else {
                                        $query .= ", " . $gid[$i];
                                }
                        }
                        $query .= ")";
                } else {
                        $query = " WHERE user_group = " . $gid;
                }

                $result = mysql_query($query) or die($query . "<br />Error: " . mysql_error());
        }
        return $result;
    }

	function listUsersNicely(){
            $result = false;
            if(cb_connect()){
                $query = "SELECT users.id, users.name AS user_name, users.displayname AS user_displayname, users.email, user_groups.name AS group_name, users.contact_number, users.last_logged_in ";
                $query .= "FROM users INNER JOIN user_groups ON users.user_group=user_groups.id";
                $result = mysql_query($query);
            }
            return $result;
	}

	function is_valid_name($val){
		$return = 0;
		$vallen = strlen($val);
		// Username has to be 3 to 50 characters - to allow 'Rob'! :)
		if($vallen < 51 && $vallen > 2){
			// username should be alphabetic, start with a capital, with spaces allowed in between names
			$rex = '/^[A-Za-z][A-Za-z]*(?:\x20[A-Za-z]+)*$/';
			//More simple REx:
			//$rex = "^[a-z -']+$";
			if(preg_match($rex, $val)){
				$return = 1;
			}
		}
		return $return;
	}

	function is_valid_email($email) {
            return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email);
	}

    function is_valid_contact_number($num) {
        if(is_numeric($num)){ 
            return true;
        } else { 
            return false;
        }
    }

    function is_unique_email($email){
        //checks if a supplied email address is not already in the database;
        // if it is, returns a list of users with that address, in the hope that
        // just one comes back, and it's the requesting user.
        if(cb_connect()){
            $query = "SELECT id FROM users WHERE email = '" . $email . "'";
            $result = mysql_query($query);
            $num = mysql_num_rows($result);
            if($num == 0){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function is_valid_password($ep){
        try {
            $passrules = "/(?!^[0-9]*$)(?!^[a-zA-Z]*$)^([a-zA-Z0-9]{5,12})$/";
            if(preg_match($passrules, $ep) > 0){
                return true;
            } else {
                return false;
            }
        } catch (Exception $e){
            // TODO: handle exception
            return false;
        }
    }
    
    function activate($key){
        if(strlen($key) > 5){
            if(cb_connect()){
                $query = "UPDATE users SET user_group = 1 WHERE id = (SELECT user FROM activations WHERE akey = '" . $key . "')";
                $result = mysql_query($query);
                if(mysql_affected_rows() > 0){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
        
    function getUsers(){
        try {
            cb_connect();
            $query = "SELECT * FROM users";
            $result = mysql_query($query) or die("Error: " . mysql_error());
            $users = array();
            while ($row = mysql_fetch_array($result)){
                $loopyUser = new User();
                $loopyUser->setId(intval($row["id"]));
                $loopyUser->setName($row["name"]);
                $loopyUser->setEmail($row["email"]);
                $loopyUser->setDisplayName($row["displayname"]);
                $loopyUser->setLastIP($row["lastip"]);
                $loopyUser->setUserGroup($row["user_group"]);
                $loopyUser->setContactNumber($row["contactnumber"]);
                $users[]=$loopyUser;
            }
            return $users;
        } catch (Exception $e){
            // TODO: handle exception
            return false;
        }
    }
    
    function send_activation_email($key){
        $URL = "http://www.mononyk.us/kidsacademy/";
        $subject = "Account Created at Kids\' Academy";
        $message = '<h1>Hello ' . $this->getName() . "!</h1><p>You have had an account created for you at <strong><a href='" . $URL . "' />Kids' Academy</a></strong>.";
        $message .= "You <em>need</em> to activate your account. Click the following link to activcate:</p>";
        $message .= "<ul><li><a href='" . $URL . "auth.php?act=activate&key=". $key ."'>Key: " . $key . "</a></li></ul>";
        $message .= "<p>See you soon!</p><h3>The Kids' Academy family</h3>";
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: newaccounts@kidsacademy.ac.th' . "\r\n" .
                        'Reply-To: info@campusbike.ucc.ie' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

        if(mail($this->getEmail(), $subject, $message, $headers)){
            return true;
        } else {
            return false;
        }
    }
    
    function send_temppass_email($temppass){
        $URL = "http://www.mononyk.us/kidsacademy/";
        $subject = "Account Created at Kids\' Academy";
        $message = '<h1>Hello ' . $this->getName() . "!</h1><p>You have had an account created for you at <strong><a href='" . $URL . "' />Kids' Academy</a></strong>.";
        $message .= "Your temporary password is: <strong>" . $temppass. "</strong>. You can use it to log into the site with your new account, but please don't forget to change it to something you will remember.</p>";
        $message .= "<p>See you soon!</p><h3>The Kids' Academy family</h3>";
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: newaccounts@kidsacademy.ac.th' . "\r\n" .
                        'Reply-To: info@campusbike.ucc.ie' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

        if(mail($this->getEmail(), $subject, $message, $headers)){
            return true;
        } else {
            return false;
        }
    }
}
?>

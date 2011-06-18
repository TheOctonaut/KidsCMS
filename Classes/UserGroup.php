<?php

$subd = "/kidsacademy";
include_once($_SERVER['DOCUMENT_ROOT'].$subd."/utilities.php");
class UserGroup {
        var $id;
        var $name;
        var $power;

        function getId(){
                return $this->id;
        }
        function setId($v){
                $this->id = $v;
                return;
        }
        function getName(){
                return $this->name;
        }
        function setName($v){
                $this->name = $v;
                return;
        }
        function getPower(){
                return $this->power;
        }
        function setPower($v){
                $this->power = $v;
                return;
        }

        function getUserGroupById($id){
                $status = false;
                if(cb_connect()){
                    $query = "SELECT * FROM user_groups WHERE id = " . $id;
                    $result = mysql_query($query);
                    if(mysql_num_rows($result) > 0){
                            $row = mysql_fetch_row($result);
                            $status = true;
                            $this->setId($row[0]);
                            $this->setName($row[1]);
                            $this->setPower($row[2]);
                    }
                } else {
                        error_log("DB Not Connected in UserGroup.php SELECT");
                }
                return $status;
        }

        function save(){
                $status = false;
                if(cb_connect()){
                        if($this->getId() == "new"){
                                $query = "INSERT INTO user_groups (name, power) VALUES ('" . $this->getName() . "', " . $this->getPower() . ")";
                                $result = mysql_query($query);
                                $new_id = mysql_insert_id();
                                if($new_id != null){
                                        $this->setId($new_id);
                                        $status = true;
                                }
                        } else {
                                $query = "UPDATE user_groups SET name = '" . $this->getName() . "', power = " . $this->getPower() . "  WHERE id = " . $this->getId();
                                $result = mysql_query($query);
                                $status = true;
                        }
                } else {
                        error_log("DB Not Connected in UserGroup.php");
                }
                return $status;
        }

        // Deletes the object in memory from the database.

        function delete(){
                $status = false;
                if(cb_connect()){
                    $query = "UPDATE users SET user_group = (SELECT id FROM user_group ORDER BY id ASC LIMIT 0,1) WHERE user_group = " . $this->getId();
                    $result = mysql_query("DELETE FROM user_groups WHERE id = " . $this->getId());
                    $status = true;
                }
                return $status;
        }

        function listUserGroups(){
                $result = false;
                if(cb_connect()){
                        $result = mysql_query("SELECT * FROM user_groups");
                }
                return $result;
        }
}
?>
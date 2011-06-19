<?php

$subd = "/kidsacademy";
include_once($_SERVER['DOCUMENT_ROOT'] . $subd . "/utilities.php");

class Section {

    private $id;
    private $title;
    private $summary;
    private $user;
    private $section;
    private $date_created;
    private $content;
    private $published;

    function getId() {
        return $this->id;
    }

    function setId($v) {
        $this->id = $v;
        return;
    }

    function getTitle() {
        return $this->title;
    }

    function setTitle($v) {
        $this->title = $v;
        return;
    }

    function getSummary() {
        return $this->summary;
    }

    function setSummary($v) {
        $this->summary = $v;
        return;
    }

    function getUser() {
        return $this->user;
    }

    function setUser($v) {
        $this->user = $v;
        return;
    }

    function getSection() {
        return $this->section;
    }

    function setSection($v) {
        $this->section = $v;
        return;
    }

    function getDateCreated() {
        return $this->datecreated;
    }

    function setDateCreated($v) {
        $this->date_created = $v;
        return;
    }
    function getContent() {
        return $this->content;
    }

    function setContent($v) {
        $this->content = $v;
        return;
    }
    function getPublished() {
        return $this->published;
    }

    function setPublished($v) {
        $this->published = $v;
        return;
    }

    function getArticleById($id) {
        $status = false;
        if (cb_connect()) {
            $query = "SELECT * FROM articles WHERE id = " . $id . " LIMIT 0,1";
            $result = mysql_query($query);
            if (mysql_num_rows($result) > 0) {
                $row = mysql_fetch_assoc($result);
                $this->setId($row["id"]);
                $this->setTitle($row["title"]);
                $this->setSummary($row["summary"]);
                $this->setUser($row["user"]);
                $this->setContent($row["content"]);
                $this->setDateCreated($row["date_created"]);
                $this->setSection($row["section"]);
                $this->setPublished($row["published"]);
                $status = true;
            }
        } else {
            error_log("DB Not Connected in UserGroup.php SELECT");
        }
        return $status;
    }

    function save() {
        $status = false;
        if (cb_connect()) {
            if ($this->getId() == "new") {
                $query = "INSERT INTO articles (title, summary, user, section, content, date_created, published)";
                $query.= " VALUES ('" . $this->getTitle() . "', '" . $this->getSummary() . "'";
                $query.= ", " . $this->getUser() . ", " . $this->getSection();
                $query.= ", '" . $this->getContent() . "', '" . $this->getDateCreated() ."', " . $this->getPublished() . ")";
                $result = mysql_query($query);
                $new_id = mysql_insert_id();
                if ($new_id != null) {
                    $this->setId($new_id);
                    $status = true;
                }
            } else {
                $query = "UPDATE articles SET title = '" . $this->getTitle() . "'";
                $query.= ", summary = '" . $this->getSummary() . "'";
                $query.= ", user = " . $this->getUser();
                $query.= ", section = " . $this->getSection();
                $query.= ", content = '" . $this->getContent() . "'";
                $query.= ", date_created = '" . $this->getDateCreated() . "'";
                $query.= ", published = " . $this->getPublished();
                $query.= "  WHERE id = " . $this->getId();
                $result = mysql_query($query);
                $status = true;
            }
        } else {
            error_log("DB Not Connected in UserGroup.php");
        }
        return $status;
    }

    // Deletes the object in memory from the database.

    function delete() {
        $status = false;
        if (cb_connect()) {
            $result = mysql_query("DELETE FROM articles WHERE id = " . $this->getId());
            $status = true;
        }
        return $status;
    }
}
?>
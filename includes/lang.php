<?php
$langcode = "";
if(isset($_GET["lang"])){
    // move this section to new include file?
    $langcode = mEx($_GET["lang"]);
} elseif(isset($_COOKIE["lang"])){
    $langcode = mEx($_COOKIE["lang"]);
} else {
    $langcode = "en-GB";
}
$lang = "";
$langfilename = $_SERVER['DOCUMENT_ROOT'].$subd."/lang/".$langcode.".xml";
if (file_exists($langfilename)) {
    $lang = simplexml_load_file($langfilename);
    setcookie('lang', $langcode);
} else {
    exit('Language File Not Found');
}
?>

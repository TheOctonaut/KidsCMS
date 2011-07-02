<? if (isset($_REQUEST["msg"]) && isset($lang)){
    ?><details id="feedback" class="feedback"><?php
    $msgs = $_REQUEST["msg"];
    if(is_array($msgs)){
        foreach ($msgs as $m){
            foreach ($lang->xpath($m) as $string){
                echo "<p>" . $string . "</p>";
            }
        }
    } else {
        echo "<p>" . $msgs . "</p>";
    }?></details><script>new Effect.Highlight('feedback', { startcolor: '#ff6565', endcolor: '#fff565' });</script>
<?php }?>
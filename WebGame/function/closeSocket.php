<?php
 $out = shell_exec('kill $(lsof -t -i:9002)');
 var_dump($out);
 header('Content-typ: application/json');
?>
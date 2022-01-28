<?php
$game_dir = "game";
if(!file_exists($game_dir)) mkdir($game_dir);

$default = "default-game.log";
//if(!file_exists("$game_dir/$default")) file_put_contents("$game_dir/$default", "");

switch($_POST["action"]) {
    case "send":
        $gamefile = "$game_dir/" . $_POST["room"] . ".log";
        writegame($gamefile);
        echo gameLog($gamefile);
        break;
    
    case "poll":
        $gamefile = "$game_dir/" . $_POST["room"] . ".log";
        echo gameLog($gamefile);
        break;
        
    case "room":
        echo getRoom($game_dir);
        break;     
}

function writegame($f) {
    $format = "<p>%s&nbsp;<span class='namelite'>%s</span>&nbsp;%s</p>";
    $str = sprintf($format, date("H:i"), $_POST["name"], $_POST["msg"]);
    file_put_contents($f, "$str\n", FILE_APPEND | LOCK_EX);
}

function gameLog($f) {
    if(file_exists($f)) $log = file_get_contents($f); else $log = "";
    return $log;
}

function getRoom($dir) {    
    $ffs = preg_grep('/^([^.])/', scandir($dir));
    $ffs = array_values($ffs);
    foreach($ffs as $key=>$ff) {
        $ffs[$key] = explode(".", $ff)[0];
    }
    //$new = $_POST['new'];
    if(isset($_POST['new'])) {
        $new = $_POST['new'];
        file_put_contents("$dir/$new" . ".log", "");
        $ffs = array_merge([ $new ], $ffs);
    }
    return json_encode($ffs);    
}
?>
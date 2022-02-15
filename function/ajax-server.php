<?php
include_once "database.php";
header("Content-Type:text/html; charset=utf-8");

$game_dir = "game";
if(!file_exists($game_dir)) mkdir($game_dir);

switch($_POST["action"]) {
    case "send":
        $gamefile = "$game_dir/" . $_POST["Sid"] . ".log";
        writegame($gamefile);
        echo gameLog($gamefile);
        break;
    case "startGame":
        $gamefile = "$game_dir/" . $_POST["Sid"] . ".log";
        startgame($gamefile);
        echo gameLog($gamefile);
        break;
    case "endGame":
        $gamefile = "$game_dir/" . $_POST["Sid"] . ".log";
        echo endgame($gamefile);
        gameLog($gamefile);
        break;
    case "poll":
        $gamefile = "$game_dir/" . $_POST["room"] . ".log";
        echo gameLog($gamefile);
        break;
    case "room":
        echo getRoom($game_dir);
        break;
    case "getCloest":
        $gamefile = "$game_dir/" . $_POST["Sid"] . ".log";
        echo getCloest($gamefile);
        break;
}

function percentToDecimal($percent): float{
    $percent = str_replace('%', '', $percent);
    return $percent / 100.00;
}

function startgame($f){
    $format = "<p>%s Start Game: %s, Round: %s</p>";
    $str = sprintf($format, date("H:i"),$_POST["Sid"],$_POST["round"]);
    file_put_contents($f, "$str\n", FILE_APPEND | LOCK_EX);
}

function endgame($f){
    $format = "<p>%s End Game: %s, Round: %s</p>";
    $str = sprintf($format, date("H:i"),$_POST["Sid"],$_POST["round"]);
    file_put_contents($f, "$str\n", FILE_APPEND | LOCK_EX);

    $round = $_POST["round"];
    $array=array();

    $file_array = file( $f ); //取到檔案陣列
    foreach ( $file_array as $line_num => $line ) { //輸出陣列元素
        if((strpos($line,"Start")!==false) & (strpos($line,"Round: ".$round)!==false)){
            array_push($array,$line_num);
        }
        if((strpos($line,"End")!==false) & (strpos($line,"Round: ".$round)!==false)){
            array_push($array,$line_num);
        }
    }
    
    $sum=0;
    $num=0;
    for ($i = $array[0]+1 ; $i<$array[1] ; $i++){
        $num += 1;
        $txt = file($f);
        $sum += substr($txt[$i],-7);
    }
    return ($sum/$num);
}

function getClosestNum($search, $arr) {
    $closest = null;
    foreach ($arr as $item) {
       if ($closest === null || abs($search - $closest) > abs($item - $search)) {
          $closest = $item;
       }
    }
    return $closest;
}

function getCloest($f){
    $Sid = $_POST["Sid"];
    $round = $_POST["round"];
    $result = $_POST["result"];

    $array=array();
    $file_array = file( $f ); //取到檔案陣列
    foreach ( $file_array as $line_num => $line ) { //輸出陣列元素
        if((strpos($line,"Start")!==false) & (strpos($line,"Round: ".$round)!==false)){
            array_push($array,$line_num);
        }
        if((strpos($line,"End")!==false) & (strpos($line,"Round: ".$round)!==false)){
            array_push($array,$line_num);
        }
    }
    
    $pick = array();
    for ($i = $array[0]+1 ; $i<$array[1] ; $i++){
        $txt = file($f);
        $pickNum = substr($txt[$i],-7);
        array_push($pick,$pickNum);
    }
    $cloestNum = getClosestNum($resultNum,$pick);
    $winner = "";
    $winnerId = "";
    foreach ( $file_array as $line_num => $line ) { //輸出陣列元素
        if((strpos($line,$cloestNum)!==false)){
            for( $i = 17 ; $i < strlen($line) ; $i++){
                $winner = $winner.$line[$i];
            }
            for( $i = 17 ; $i < 24 ; $i++){
                $winnerId = $winnerId.$line[$i];
            }
        }
    }

    $sql = "UPDATE `sessionData` SET `winner`=$winnerId WHERE `Sid`=$Sid AND `Round`=$round";
    global $con;
    try{
        mysqli_query($con,$sql);
    }catch(Exception $e){
        $error = $e->getMessage();
        echo $error;
    }
    return $winner;
}

function writegame($f) {
    $studentId = $_POST["studentId"];
    $studentName = $_POST["studentName"];
    $pickNumber = $_POST["pickNumber"];

    $format = "<p>%s Student %s %s PickNumber: %s</p>";
    $str = sprintf($format, date("H:i"), $studentId, $studentName, $pickNumber);
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
 
    if(isset($_POST["Sid"])){
        $Sid = $_POST["Sid"];
    }
    if(isset($_POST["Rid"])){
        $Rid=$_POST["Rid"];
    }
    if(isset($_POST["pValue"])){
        $pValue=percentToDecimal($_POST["pValue"]);
    }
    if(isset($_POST["payoff"])){
        $payoff=$_POST["payoff"];
    }
    if(isset($_POST["numOfStudent"])){
        $numOfStudent=$_POST["numOfStudent"];
    }

    global $con;
    for( $i=1 ; $i <= $Rid ; $i++ ){
        $sql = "INSERT INTO `sessionData`(`Sid`, `Round`, `pValue`, `payoff`, `numOfStudent`) VALUES ('$Sid','$i','$pValue','$payoff','$numOfStudent')";
        
        try{
            mysqli_query($con,$sql);
        }catch(Exception $e){
            $error = $e->getMessage();
            echo $error;
        }
    }
    
    if(isset($_POST["new"])) {
        $new = $_POST["new"];
        file_put_contents("$dir/$new" . ".log", "");
        $ffs = array_merge([ $new ], $ffs);
    }
    return json_encode($ffs);    
}
?>
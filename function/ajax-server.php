<?php
include_once "database.php";
header("Content-Type:text/html; charset=utf-8");

$game_dir = "game";
if(!file_exists($game_dir)) mkdir($game_dir);
$game2_dir = "game2";
if(!file_exists($game2_dir)) mkdir($game2_dir);

switch($_POST["action"]) {
    case "send":
        $gamefile = "$game_dir/" . $_POST["Sid"] . ".log";
        writegame($gamefile);
        echo gameLog($gamefile);
        break;
    case "joinPGame":
        $gamefile = "$game2_dir/" . $_POST["Pid"] . ".log";
        joinPGame($gamefile);
        break;
    case "sendPGame":
        $gamefile = "$game2_dir/" . $_POST["Pid"] . ".log";
        writeGame2($gamefile);
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
    case "poll2":
        $gamefile = "$game2_dir/" . $_POST["room"] . ".log";
        echo gameLog($gamefile);
        break;
    case "room":
        echo getRoom($game_dir);
        break;
    case "room2":
        echo getRoom2($game2_dir);
        break;
    case "getCloest":
        $gamefile = "$game_dir/" . $_POST["Sid"] . ".log";
        echo getCloest($gamefile);
        break;
    case "login":
        echo login();
        break;
}

function percentToDecimal($percent){
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
function joinPGame($f){
    $studentId = $_POST["studentId"];
    $studentName = $_POST["studentName"];

    $format = "<p>%s Student %s %s:";
    $str = sprintf($format, date("H:i"), $studentId, $studentName);
    file_put_contents($f, "$str\n", FILE_APPEND | LOCK_EX);

    $file_array = file( $f );
    foreach ( $file_array as $line_num => $line ) { //輸出陣列元素
        if( boolval($line_num & 1) & (strpos($line," 2 ")==false)){
            $lines = file($f, FILE_IGNORE_NEW_LINES );
            $lines[$line_num] = $lines[$line_num] . " 2 ";
            file_put_contents($f, implode("\n", $lines) );
            file_put_contents($f, "\n", FILE_APPEND | LOCK_EX);
        }else if( !boolval($line_num & 1) & (strpos($line," 1 ")==false)){
            $lines = file($f, FILE_IGNORE_NEW_LINES );
            $lines[$line_num] = $lines[$line_num] . " 1 ";
            file_put_contents($f, implode("\n", $lines) );
            file_put_contents($f, "\n", FILE_APPEND | LOCK_EX);
        }
    }
}
function writeGame2($f){
    $studentId = $_POST["studentId"];
    $studentName = $_POST["studentName"];
    $chosen = $_POST["chosen"];

    $file_array = file( $f );
    foreach ( $file_array as $line_num => $line ) { //輸出陣列元素
        if( (strpos($line,$studentId)!==false) & (strpos($line,$studentName)!==false) ){
            $lines = file($f, FILE_IGNORE_NEW_LINES );
            $lines[$line_num] = $lines[$line_num] . " chosen: " . $chosen . "</p>";
            file_put_contents($f, implode("\n", $lines) );
            file_put_contents($f, "\n", FILE_APPEND | LOCK_EX);
        }
    }
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

function getRoom2($dir){
    $ffs = preg_grep('/^([^.])/', scandir($dir));
    $ffs = array_values($ffs);
    foreach($ffs as $key=>$ff) {
        $ffs[$key] = explode(".", $ff)[0];
    }

    if(isset($_POST["Pid"])){
        $Pid = $_POST["Pid"];
    }
    if(isset($_POST["numOfStudent"])){
        $numOfStudent=$_POST["numOfStudent"];
    }

    global $con;
    $sql = "INSERT INTO `pGameInfo`(`Pid`,`numOfStudent`) VALUES('$Pid','$numOfStudent')";
    try{
        mysqli_query($con,$sql);
    }catch(Exception $e){
        $error = $e->getMessage();
        echo $error;
    }

    if(isset($_POST["new"])) {
        $new = $_POST["new"];
        file_put_contents("$dir/$new" . ".log", "");
        $ffs = array_merge([ $new ], $ffs);
    }
    return json_encode($ffs);
}

function login(){
    $IAccount = $_POST["IAccount"];
    $IPassword = $_POST["IPassword"];
    
    global $con;
    $sql = "SELECT * FROM `instructorAcc` WHERE `IAccount`='$IAccount' AND `IPassword`='$IPassword'";
    try{
        $result = mysqli_query($con,$sql);
        while($x=mysqli_fetch_array($result)){
            $passChanged;
            if($x[0]!==""){
                if($IPassword=="11111111" || $IPassword=="22222222" || $IPassword=="33333333"){
                    $passChanged="0";
                }else{
                    $passChanged="1";
                }
                return $IAccount.$passChanged;
            }
        }
    }catch(Exception $e){
        $error = $e -> getMessage();
        return $error;
    }
}
?>
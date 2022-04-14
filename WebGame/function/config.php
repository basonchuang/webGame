<?php
include_once "database.php";
header("Content-Type:text/html; charset=utf-8");

switch($_POST["action"]) {
    case "insLogin":
        insLogin();
        break;
    case "pollPlayList":
        pollPlayList();
        break;
    case "pollGameInfo":
        pollGameInfo();
        break;
    case "insCreateGGame":
        #insCreateGGame();
        break;
    case "insStartGGame":
        #insStartGGame();
        break;
    case "showGGameResult":
        #showGGameResult();
        break;
    case "studentJoinGGame":
        #studentJoinGGame();
        break;
    case "studentPickNumber":
        #studentPickNumber();
        break;
}

function insLogin(){
    $IAccount = $_POST["username"];
    $IPassword = $_POST["password"];
    
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
                echo $IAccount.$passChanged;
            }
        }
    }catch(Exception $e){
        $error = $e -> getMessage();
        echo $error;
    }
}

function pollPlayList(){
    if(isset($_POST["Iid"])){
        $Iid = $_POST["Iid"];
    }

    global $con;
    $sql = "SELECT * FROM `instructorPlayList` WHERE `Iid`= '$Iid'";

    $result = mysqli_query($con,$sql);
    $gameCode = array();
    $gameType = array();
    while($x=mysqli_fetch_array($result)){
        array_push($gameCode,$x['gameCode']);
        array_push($gameType,$x['gameType']);
    }
    $return = array_merge($gameCode, $gameType);

    echo json_encode($return);
}

function pollGameInfo(){
    if(isset($_POST["gameCode"])){
        $gameCode = $_POST["gameCode"];
    }

    global $con;
    $sql = "SELECT `Round`,`pValue`,`payOff`,`numOfStudent` FROM `gGameInfo` WHERE `Sid` = '$gameCode'";
    $result = mysqli_query($con,$sql);
    $return = array();

    while($x = mysqli_fetch_array($result)){
        echo json_encode($x['Round']);
        /*
        array_push($return,$x['Round']);
        array_push($return,$x['pValue']);
        array_push($return,$x['payOff']);
        array_push($return,$x['numOfStudent']);
        */
    }
    
    echo json_encode($return);      //return include round, pvalue, payoff, numofstudent 
}
?>
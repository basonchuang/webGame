<?php
include_once "database.php";
header("Content-Type:text/html; charset=utf-8");
switch($_POST["action"]) {
    case "insLogin":
        insLogin();
        break;
    case "insCreateGGame":
        insCreateGGame();
        break;
    case "insStartGGame":
        insStartGGame();
        break;
    case "showGGameResult":
        showGGameResult();
        break;
    case "studentJoinGGame":
        studentJoinGGame();
        break;
    case "studentPickNumber":
        studentPickNumber();
}

function insLogin(){
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
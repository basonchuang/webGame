<?php
    include_once "database.php";
    header("Content-Type:text/html; charset=utf-8");
    if(isset($_GET["method"])){
        switch($_GET["method"]){
            case "createGame":
                createGame();
                break;
            case "showGames":
                showGames();
                break;
            case "startGame":
                startGame();
                break;
            case "startPGame":
                startPGame();
                break;
            case "studentPickNum":
                studentPickNum();
                break;
            case "showGameInfo":
                showGameInfo();
                break;
            case "showPGameInfo":
                showPGameInfo();
                break;
        }
    }
    function createGame(){
        if(isset($_POST["Sid"])){
            $Sid = $_POST["Sid"];
        }
        $arr = array();
        $arr = explode("_",$Sid,2);
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

        $sql = "INSERT INTO `gGameInfo`(`createGGameBy`,`Sid`, `Rid`, `pValue`, `payoff`, `numOfStudent`) VALUES ('$arr[0]','$arr[1]','$Rid','$pValue','$payoff','$numOfStudent')";
        global $con;
        try{
            mysqli_query($con,$sql);
            header('Location:http://localhost/instructor.html');
        }catch(Exception $e){
            $error = $e->getMessage();
            echo $error;
        }
    }
    function percentToDecimal($percent){
        $percent = str_replace('%', '', $percent);
        return $percent / 100.00;
    }
    function showGames(){
        $sql = "SELECT * FROM `gGameInfo` WHERE 1";
        global $con;
        $result = mysqli_query($con,$sql);
        $Sid = array();
        while($x=mysqli_fetch_array($result)){
            array_push($Sid,$x['Sid']);
        }
        $return = array_merge($Sid);
        echo json_encode($return);
    }
    function startGame(){
        if(isset($_POST["gameList"])){
            $Sid = $_POST["gameList"];
            $arr = array();
            $arr = explode("_",$Sid,2);
            $sql = "SELECT * FROM `gGameInfo` WHERE `createGGameBy`='$arr[0]' AND `Sid`='$arr[1]'";
            global $con;

            startSession();

            try{
                mysqli_query($con,$sql);
                //header('Location:http://localhost/gameResult.html');
            }catch(Exception $e){
                $error = $e->getMessage();
                echo $error;
            }
        }
    }
    function startPGame(){
        if(isset($_POST["pGameList"])){
            $Pid = $_POST["pGameList"];
            $sql = "SELECT * FROM `pGameInfo` WHERE Pid=`$Pid`";
            global $con;

            try{
                mysqli_query($con,$sql);
                //header('Location:http://localhost/pGameResult.html');
            }catch(Exception $e){
                $error = $e->getMessage();
                echo $error;
            }
        }
    }
    function studentPickNum(){
        $studentId = $_POST['studentId'];
        $studentName = $_POST['studentName'];
        $Sid = $_POST['Sid'];
        $arr = array();
        $arr = explode("_",$Sid,2);
        $Rid = $_POST['Rid'];
        $pickNumber = $_POST['pickNumber'];

        $sql = "INSERT INTO `gGameRecord`(`studentId`, `studentName`, `Iid`, `Sid`, `Rid`, `pickNumber`, `studentPayOff`) VALUES ('$studentId','$studentName','$arr[0]','$arr[1]','$Rid','$pickNumber','0')";
        global $con;
        try{
            mysqli_query($con,$sql);
        }catch(Exception $e){
            $error = $e->getMessage();
            echo $error;
        }
    }
    function showGameInfo(){
        if(isset($_GET["Sid"])){
            $Sid = $_GET["Sid"];
            $arr = array();
            $arr = explode("_",$Sid,2);
            $sql = "SELECT * FROM `gGameInfo` WHERE `createGGameBy`= '$arr[0]' AND `Sid` = '$arr[1]'";
            global $con;
            $result = mysqli_query($con,$sql);
            $Rid = array();
            $pValue = array();
            $payoff = array();
            $numOfStudent = array();

            while($x=mysqli_fetch_array($result)){
                array_push($Rid,$x['Round']);
                array_push($pValue,$x['pValue']);
                array_push($payoff,$x['payoff']);
                array_push($numOfStudent,$x['numOfStudent']);
            }
            $return = array_merge($Rid,$pValue,$payoff,$numOfStudent);
            echo json_encode($return);
        }
    }
    function showPGameInfo(){
        if(isset($_GET["Pid"])){
            $Pid = $_GET["Pid"];
            $arr = array();
            $arr = explode("_",$Pid,2);
            $sql = "SELECT * FROM `pGameInfo` WHERE `createPGameBy` = '$arr[0]' AND `Pid` = '$arr[1]'";
            global $con;
            $result = mysqli_query($con,$sql);
            $numOfStudent = array();
            while($x=mysqli_fetch_array($result)){
                array_push($numOfStudent,$x['numOfStudent']);
            }
            echo json_encode($numOfStudent);
        }
    }
?>
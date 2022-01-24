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
            case "studentPickNum":
                studentPickNum();
                break;
            case "showGameInfo":
                showGameInfo();
                break;
        }
    }
    function createGame(){
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
        $Sid = rand();

        $sql = "INSERT INTO `sessionData`(`Sid`, `Rid`, `pValue`, `payoff`, `numOfStudent`) VALUES ('$Sid','$Rid','$pValue','$payoff','$numOfStudent')";
        global $con;
        try{
            mysqli_query($con,$sql);
            header('Location:http://localhost/instructor.html');
        }catch(Exception $e){
            $error = $e->getMessage();
            echo $error;
        }
    }
    function percentToDecimal($percent): float{
        $percent = str_replace('%', '', $percent);
        return $percent / 100.00;
    }
    function showGames(){
        $sql = "SELECT * FROM `sessionData` WHERE 1";
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
            $sql = "SELECT * FROM `sessionData` WHERE Sid=`$Sid`";
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
    function studentPickNum(){
        $studentId = $_POST['studentId'];
        $studentName = $_POST['studentName'];
        $Sid = $_POST['Sid'];
        $Rid = $_POST['Rid'];
        $pickNumber = $_POST['pickNumber'];
        session_start();
        $_SESSION['Sid'] = $Sid;
        $_SESSION['pickNumber'] = $pickNumber;
        $_SESSION['studentId'] = $studentId;
        /*
        $sql = "INSERT INTO `studentPlayRecord`(`studentId`, `studentName`, `Sid`, `Rid`, `pickNumber`, `studentPayOff`) VALUES ('$studentId','$studentName','$Sid','$Rid','$pickNumber','0')";
        global $con;
        try{
            mysqli_query($con,$sql);
            header('Location:http://localhost/game.php');
        }catch(Exception $e){
            $error = $e->getMessage();
            echo $error;
        }
        */
    }
    function showGameInfo(){
        if(isset($_GET["Sid"])){
            $Sid = $_GET["Sid"];
            $sql = "SELECT * FROM `sessionData` WHERE `Sid` = $Sid";
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
    function startSession(){
        $host = "127.0.0.1";
        $port = 3000;
        set_time_limit(0);
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        $result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
        $result = socket_listen($socket, 3) or die("Could not set up socket listener\n");
        
        $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
        $input = socket_read($spawn, 1024) or die("Could not read input\n");
        $input = trim($input);
        echo "Client Message : ".$input;
        $output = strrev($input) . "\n";
        socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
        //socket_close($spawn);
        //socket_close($socket);
    }
?>
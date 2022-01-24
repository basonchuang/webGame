<?php
    $server='localhost';
    $id='root';
    $pwd='1074535';
    $dbname='p1';
    $con = mysqli_connect($server , $id , $pwd);
    if (!$con){
        die("Could not connect: " . mysql_error());
    }
    mysqli_select_db($con , $dbname);
    mysqli_query($con ,"SET NAMES utf8");
?>
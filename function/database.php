<?php
    $server='127.0.0.1';
    $id='root';
    $pwd='1074535';
    $dbname='p1';
    $con = mysqli_connect($server , $id , $pwd);

    mysqli_select_db($con , $dbname);
    mysqli_query($con ,"SET NAMES utf8");
?>
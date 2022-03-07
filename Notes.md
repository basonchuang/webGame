    //Using php file to execute php, note this will not gonna close the socket
    <?php
    $out = shell_exec('php server.php');var_dump($out);
    header('Content-typ: application/json');
    ?>
    //To search the port is running
    lsof -i:portnum
    //To terminate the certain port running program
    //execute in terminal
    kill $(lsof -t -i:portnum)

    //running with sh command but not what I need
    #!/bin/bash
    PID=`ps -aef | grep "testwebsock.php" | grep -v grep | awk '{print $2}'`
    if [ -z $PID ]
    then
        #echo "Launching now"
        nohup php testwebsock.php > error_log &
    fi
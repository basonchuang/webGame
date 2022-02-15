<?php
    include_once "database.php";
    $f = "game/6231.log";
    $round = "1";
    $resultNum = 31;

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
    
    function getClosest($search, $arr) {
        $closest = null;
        foreach ($arr as $item) {
           if ($closest === null || abs($search - $closest) > abs($item - $search)) {
              $closest = $item;
           }
        }
        return $closest;
     }
    $pick = array();

    for ($i = $array[0]+1 ; $i<$array[1] ; $i++){
        $txt = file($f);
        $pickNum = substr($txt[$i],-7);
        array_push($pick,$pickNum);
    }
    $cloestNum = getClosest($resultNum,$pick);
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
    echo $winnerId;
    echo $winner;
?>
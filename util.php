<?php
  //checking for a '--prod' flag on cli
  function isDebug($argv){
    //var_dump($argv);
    foreach ($argv as $arg) {
      if($arg == "--prod"){
        return false;
      }
    }
    return true;
  }

  function getArgs($argv){
    $input = array();
    for($i =0; $i < count($argv); $i++) {
      if($argv[$i] == "--prod"){
        array_push($input,"--prod");
      }elseif ($argv[$i] == "--t") {
        $temp = $argv[++$i];
        $input['to'] = explode("," , $temp);
      }
    }
    return $input;
  }

 ?>

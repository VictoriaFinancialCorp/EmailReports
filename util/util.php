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
    for($i =1; $i < count($argv); $i++) {
      if($argv[$i] == "--prod"){
        array_push($input,"--prod");
      }elseif ($argv[$i] == "--t") {
        $temp = $argv[++$i];
        $input['to'] = explode("," , $temp);
      }elseif ($argv[$i] == "--cc") {
        $temp = $argv[++$i];
        $input['cc'] = explode("," , $temp);
      }elseif ($argv[$i] == "--bcc") {
        $temp = $argv[++$i];
        $input['bcc'] = explode("," , $temp);
      }else{
        print("invalid parameter: $argv[$i] \n" );
      }
    }
    return $input;
  }

 ?>

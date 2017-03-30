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

 ?>

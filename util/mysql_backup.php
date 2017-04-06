<?php

include_once "../config/config.php";
include_once "../util/util.php";
include_once "logger.php";

$log = Logger::getLogger(basename(__FILE__));

$input = getArgs($argv);

$dbname = null;
if( isset($input['backup']) && !empty($input['backup'])){
  $dbname = $input['backup'];
}else{
  die("use '--backup' and indicate a db name to backup");
}

$dbhost = DB::host;
$dbuser = DB::user;
$dbpass = DB::pass;

try { //check database exists with correct config
    $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $dbuser, $dbpass);
} catch (PDOException $e) {
    $log->warn("Error!: " . $e->getMessage() . "<br/>");
    die();
}

$backup_file = getcwd() . "\\..\\backup\\" . $dbname . date("Y-m-d-His") . '.sql';
$command = DB::bin_path . "mysqldump -h $dbhost -u $dbuser -p$dbpass $dbname > $backup_file"  ;
//print $command . "\n\n";
$output = system($command);

//$log->debug($output);
$log->info("$dbname DB backup done");

?>

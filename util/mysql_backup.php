<?php

include_once "../config/config.php";
include_once "../util/util.php";

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

$backup_file = getcwd() . "\\..\\backup\\" . $dbname . date("Y-m-d-His") . '.sql';
$command = DB::bin_path . "mysqldump -h $dbhost -u $dbuser -p$dbpass $dbname > $backup_file"  ;
//print $command . "\n\n";
$output = system($command);

print $output;
print "$dbname db backup done";

?>

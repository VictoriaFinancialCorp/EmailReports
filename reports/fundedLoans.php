<?php

include_once "../config/config.php";
include_once "../util/mailgun.php";
include_once "../util/util.php";
include_once "../util/logger.php";


function prepareMessage(){
  include_once "head.template.php";

  $count = 0;
  $loanSum = 0.00;

  $today = new DateTime();
  $header = "<h3>Files Funded {$today->format('m/d/y')}</h3>" ;


  $message = "<table border=1><tr><th>Investor</th>" .
    "<th>Investor#</th>" .
    "<th>Loan#</th>" .
    "<th>Borrower Name</th>" .
    "<th>Loan Amount</th>" .
    "<th>Processor</th>" .
    "<th>Loan Officer</th>" .
    "</tr>";


  try {
      $dbh = new PDO('mysql:host=' . DB::host . ';dbname=' . DB::db_name, DB::user, DB::pass);
      $query = "SELECT investor, investorNum, loanNum, b1_lname, b1_fname, " .
        "loanAmt, processor, loanOfficer, fundedDate " .
        "FROM loans " .
        "WHERE fundedDate = '" . $today->format('Y-m-d') . "'";

        //print($query);

      foreach($dbh->query($query) as $row) {
          $message .= "<tr>" .
          "<td>{$row['investor']}</td>" .
          "<td>{$row['investorNum']}</td>" .
          "<td>{$row['loanNum']}</td>" .
          "<td>{$row['b1_lname']}, {$row['b1_fname']}</td>" .
          "<td>{$row['loanAmt']}</td>" .
          "<td>{$row['processor']}</td>" .
          "<td>{$row['loanOfficer']}</td>" .
          "</tr>";
          $count++;
          $loanSum +=  (float)str_replace(array("$", ","), '', $row['loanAmt']);
          //$loanSum += money_format('%i', $row['loanAmt']);
      }

      $dbh = null;
      $message .= "<tr><td colspan=7>TOTAL: $count file(s)  $" . number_format($loanSum) . "</td></tr>";
      $message .= "</table>";

      if($count==0){
        //return nothing if message is empty
        return;
      }


  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }

  return $head . $header . $message;


}

$log = Logger::getLogger(basename(__FILE__));

//message will only be emailed with a '--prod' flag on cli
$htmlMessage = prepareMessage();
$debug = (isset($argv)) ? isDebug($argv) : true;
if($debug){
  print($htmlMessage);
}elseif(empty($htmlMessage)){
  $log->info("Report is empty. Nothing to mail out.");
}else{
  $input = getArgs($argv);
  sendMail($input, '[Server Report] Files Funded ' . date_create()->format('m/d/y'), $htmlMessage);
}

?>

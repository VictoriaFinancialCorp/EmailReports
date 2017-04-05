<?php

include_once "../config/config.php";
include_once "../util/mailgun.php";
include_once "../util/util.php";

function prepareMessage(){
  include_once "head.template.php";

  $count = 0;

  $today = new DateTime();
  $header = "<h3>Files Funded w/o Investor Lock as of {$today->format('m/d/y')}</h3>" ;
  $header .= "<p style='color:red;'>These files have been funded but do not have an investor lock recorded. Please address immediately.</p>" ;


  $message = "<table border=1><tr><th>Investor</th>" .
    "<th>Investor#</th>" .
    "<th>Loan#</th>" .
    "<th>Borrower Name</th>" .
    "<th>Address</th>" .
    "<th>Rate</th>" .
    "<th>Loan Amount</th>" .
    "<th>Funded Date</th>" .
    "<th>Total Rebate</th>" .
    "<th>Purpose</th>" .
    "<th>Processor</th>" .
    "<th>Loan Officer</th>" .
    "</tr>";


  try {
      $dbh = new PDO('mysql:host=' . DB::host . ';dbname=' . DB::db_name, DB::user, DB::pass);
      $query = "SELECT investor, investorNum, loanNum, b1_lname, b1_fname, " .
        "loanAmt, currentStatus, loanFolder, address, loan_purpose, ".
        "processor, loanOfficer, fundedDate, purchasedDate " .
        "totalAdj, netSRP, netYSP, int_rate " .
        "FROM loans " .
        "WHERE fundedDate IS NOT NULL " .
          "AND purchasedDate IS NULL " .
          "AND investorLockDate IS NULL " .
          "AND ( currentStatus = ' Active Loan' OR currentStatus = ' Loan Originated') " .
          "AND loanFolder = 'My Pipeline' " .
        "ORDER BY fundedDate ASC ";

      //  print($query);

      foreach($dbh->query($query) as $row) {
          $message .= "<tr>" .
          "<td>{$row['investor']}</td>" .
          "<td>{$row['investorNum']}</td>" .
          "<td>{$row['loanNum']}</td>" .
          "<td>{$row['b1_lname']}, {$row['b1_fname']}</td>" .
          "<td>{$row['address']}</td>" .
          "<td>{$row['int_rate']}</td>" .
          "<td>{$row['loanAmt']}</td>" .
          "<td>" . date_create($row['fundedDate'])->format('m/d/y') . "</td>" .
          "<td>" . ($row['totalAdj'] + $row['netSRP'] + $row['netYSP']) ."</td>" .
          "<td>{$row['loan_purpose']}</td>" .
          "<td>{$row['processor']}</td>" .
          "<td>{$row['loanOfficer']}</td>" .
          "</tr>";
          $count++;
      }

      $dbh = null;
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


//message will only be emailed with a '--prod' flag on cli
$htmlMessage = prepareMessage();
$debug = (isset($argv)) ? isDebug($argv) : true;
if($debug){
  print_r($htmlMessage);
}elseif(empty($htmlMessage)){
  print_r("Report is empty. Nothing to mail out.");
}else{
  $input = getArgs($argv);
  sendMail($input, '[Server Report] Funded Files w/o Lock', $htmlMessage);
}

?>

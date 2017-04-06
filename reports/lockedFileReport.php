<?php

include_once "../config/config.php";
include_once "../util/mailgun.php";
include_once "../util/util.php";

function prepareMessage(){
  include_once "head.template.php";

  $dateTo = new DateTime();
  $dateFrom = date_sub(new DateTime(), new DateInterval('P15D'));

  $message = "<h3>Locked Files from {$dateFrom->format('m/d/y')} to {$dateTo->format('m/d/y')}</h3>" ;
  $message .= "<table border=1><tr><th>Investor</th>" .
    "<th>Investor#</th>" .
    "<th>Loan#</th>" .
    "<th>Borrower Name</th>" .
    "<th>Loan Amount</th>" .
    "<th>Inv. Lock Date</th>" .
    "<th>Lock Exp</th>" .
    "<th>Lock Type</th>" .
    "<th>Rebate</th>" .
    "<th>Processor</th>" .
    "<th>Loan Officer</th>" .
    "<th>Funded Date</th>" .
    "</tr>";


  try {
      $dbh = new PDO('mysql:host=' . DB::host . ';dbname=' . DB::db_name, DB::user, DB::pass);
      $query = "SELECT investor, investorNum, loanNum, b1_lname, b1_fname, " .
        "loanAmt, investorLockDate, investorLockExpDate, investorLockType, ".
        "baseYSP, netSRP, netYSP, processor, loanOfficer, fundedDate FROM loans " .
        "WHERE investorLockDate >= '{$dateFrom->format('Y-m-d')}' " .
          "AND investorLockDate <= '{$dateTo->format('y-m-d')}' " .
        "ORDER BY investorLockDate DESC LIMIT 50 ";

      foreach($dbh->query($query) as $row) {
          $rebate = 100 + $row['netSRP'] + $row['netYSP'];
          $message .= "<tr>" .
            "<td>{$row['investor']}</td>" .
            "<td>{$row['investorNum']}</td>" .
            "<td>{$row['loanNum']}</td>" .
            "<td>{$row['b1_lname']}, {$row['b1_fname']}</td>" .
            "<td>{$row['loanAmt']}</td>" .
            "<td>" . date_create($row['investorLockDate'])->format('m/d/y') . "</td>" .
            "<td>" . date_create($row['investorLockExpDate'])->format('m/d/y') . "</td>" .
            "<td>{$row['investorLockType']}</td>" ;
          if($row['baseYSP'] < 50){
            $message .= "<td class='warning' style='text-decoration:line-through;'>$rebate</td>" ;
          }elseif( $rebate < 100){
            $message .= "<td class='danger'>$rebate</td>" ;
          }else{
            $message .= "<td>$rebate</td>";
          }

          $message .= "<td>{$row['processor']}</td>" .
            "<td>{$row['loanOfficer']}</td>" .
            "<td>" . date_create($row['fundedDate'])->format('m/d/y') . "</td>" .
            "</tr>";
      }

      $dbh = null;
      $message .= "<tr><td class='warning' colspan='6'>Invalid Format: Needs Revision</td><td class='danger' colspan='6'>Negative Rebate</td></tr>";

      $message .= "</table>";



  } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
  }
  return $head . $message;
}

//message will only be emailed with a '--prod' flag on cli
$htmlMessage = prepareMessage();
$debug = (isset($argv)) ? isDebug($argv) : true;
if($debug){
  print($htmlMessage);
}else{
  $input = getArgs($argv);
  $result = sendMail($input, '[Server Report] Locked Files', $htmlMessage);
}

?>

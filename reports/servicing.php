<?php

include_once "../config/config.php";
include_once "../util/mailgun.php";
include_once "../util/util.php";

function prepareMessage(){
  include_once "head.template.php";
  $count = 0;

  $today = new DateTime();
  $header = "<h3>Payments to collect as of {$today->format('m/d/y')}</h3>" ;

  $message = "<table><tr>" .
    "<th>Servicing Status</th>" .
    "<th>1st Payment Date</th>" .
    "<th>Inv. 1st Payment Date</th>" .
    "<th>Next Payment Date</th>" .
    "<th>Last Printed Statement</th>" .
    "<th>Payments Collected</th>" .
    "<th>Expected pyaments</th>" .
    "<th>Investor</th>" .
    "<th>Inv.#</th>" .
    "<th>Loan#</th>" .
    "<th>Borrower Name</th>" .
    "<th>Processor</th>" .
    "<th>Loan Officer</th>" .
    "</tr>";


  try {
      $dbh = new PDO('mysql:host=localhost;dbname=' . DB::name, DB::user, DB::pass);
      $query = "SELECT investor, investorNum, loanNum, b1_lname, b1_fname, " .
        "processor, loanOfficer, firstPaymentDate, firstPaymentDateInvestor,  " .
        "mortgageStatementLastPrinted, servicingStatus, paymentsCollected  " .
        "FROM loans " .
        "WHERE fundedDate IS NOT NULL " .
          "AND mortgageStatementLastPrinted IS NOT NULL " .
          "AND ( servicingStatus = ' Current' OR servicingStatus = ' Past Due') " .
        "ORDER BY mortgageStatementLastPrinted DESC ";

        //print($query);
      foreach($dbh->query($query) as $row) {
        //logic handling
        $firstPaymentDateInvestor = (is_null($row['firstPaymentDateInvestor'])) ? 'N/A' : date_create($row['firstPaymentDateInvestor']) -> format('m/d/y');
        $firstPaymentDate = date_create($row['firstPaymentDate']);
        $nextPayment = ($firstPaymentDateInvestor == 'N/A') ?
            date_create()->
              setDate(date_create()->format('Y'), date_create()->format('m'), 1)->
              add(new DateInterval('P1M')) :
            date_create($row['firstPaymentDateInvestor']) ;
        $expectedPayments = date_diff($firstPaymentDate, $nextPayment )->format('%m');
        $paymentsCollected = $row['paymentsCollected'];

        $row_style = 'none';
        if($row['servicingStatus'] == " Past Due"){
          $row_style = 'danger';
        }elseif( $expectedPayments > $paymentsCollected ){
          $row_style = 'warning';
        }
        //end of logic handling

        $message .= "<tr class='{$row_style}'>" .
        "<td>{$row['servicingStatus']}</td>" .
        "<td>" . $firstPaymentDate = date_create($row['firstPaymentDate']) -> format('m/d/y') . " </td>" .
        "<td>{$firstPaymentDateInvestor}</td>" .
        "<td>" . $nextPayment-> format('m/d/y')  . "</td>" .
        "<td>" . date_create($row['mortgageStatementLastPrinted']) -> format('m/d/y') . "</td>" .
        "<td>{$paymentsCollected}</td>" .
        "<td>{$expectedPayments}</td>" .
        "<td>{$row['investor']}</td>" .
        "<td>{$row['investorNum']}</td>" .
        "<td>{$row['loanNum']}</td>" .
        "<td>{$row['b1_lname']}, {$row['b1_fname']}</td>" .
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
  sendMail($input, '[Server Report] Servicing Loans', $htmlMessage);
}

?>

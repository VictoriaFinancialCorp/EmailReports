<?php

require_once '../vendor/autoload.php';
include_once '../config/config.php';
include_once "logger.php";
use Mailgun\Mailgun;

function sendMail($input, $subject, $html, $class=null){
  $log = Logger::getLogger( (isset($class)) ? $class: "Mailgun");

  # Instantiate the client.
  $mgClient = new MailGun(MailGunConfig::api);
  $domain = MailGunConfig::domain;
  $unsubscribe = "";

  if(isset($input['to']) ){
    $list = explode('@', $input['to'][0]);
    if(in_array($list[1], MailGunConfig::getMailingLists())){
      $unsubscribe .= "<div class='unsubscribe'><a href='%mailing_list_unsubscribe_url%'>Unsubscribe from this Report</a>  |  ";
      $unsubscribe .= "<a href='%unsubscribe_url%'>Unsubscribe from all email from ALL Reports</a></div>";
    }
  }

  $options = [];
  $options['subject'] = (isset($subject)) ? $subject : MailGunConfig::subject;
  $options['from'] = (isset($from)) ? $from : MailGunConfig::from;
  (isset($input['to'])) ? $options['to'] = $input['to'] : "";
  (isset($input['cc'])) ? $options['cc'] = $input['cc'] : "";
  (isset($input['bcc'])) ? $options['bcc'] = $input['bcc'] : "";
  (isset($html)) ? $options['html'] = $html . $unsubscribe : "";
  $options['o:require-tls'] = true;
  //var_dump($options);

  # Make the call to the client.
  try{
    $result = $mgClient->sendMessage($domain, $options);
    $log->info($subject . " Email " . $result->{"http_response_body"}->{"message"});
  }catch(Exception $e){
    $log->warn($e);
  }

}

?>

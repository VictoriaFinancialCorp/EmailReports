<?php

require_once '../vendor/autoload.php';
include_once '../config/config.php';
include_once "logger.php";
use Mailgun\Mailgun;

function sendMail($input, $subject, $html){
  $log = Logger::getLogger("Mailgun");

  # Instantiate the client.
  $mgClient = new MailGun(MailGunConfig::api);
  $domain = MailGunConfig::domain;

  $options = [];
  $options['subject'] = (isset($subject)) ? $subject : MailGunConfig::subject;
  $options['from'] = (isset($from)) ? $from : MailGunConfig::from;
  (isset($input['to'])) ? $options['to'] = $input['to'] : "";
  (isset($input['cc'])) ? $options['cc'] = $input['cc'] : "";
  (isset($input['bcc'])) ? $options['bcc'] = $input['bcc'] : "";
  (isset($html)) ? $options['html'] = $html : "";
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

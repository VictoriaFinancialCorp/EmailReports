<?php

require_once 'vendor/autoload.php';
include_once 'config.php';
use Mailgun\Mailgun;

function sendMail($from, $to, $subject, $html){
  # Instantiate the client.
  $mgClient = new MailGun(MailGunConfig::api);
  $domain = MailGunConfig::domain;

  $options = [];
  $options['subject'] = (isset($subject)) ? $subject : MailGunConfig::subject;
  $options['from'] = (isset($from)) ? $from : MailGunConfig::from;
  (isset($to)) ? $options['to'] = $to : "";
  (isset($cc)) ? $options['cc'] = $cc : "";
  (isset($bcc)) ? $options['bcc'] = $bcc : "";
  (isset($html)) ? $options['html'] = $html : "";
  //var_dump($options);

  # Make the call to the client.
  $result = $mgClient->sendMessage($domain, $options);
  print $result->{"http_response_body"}->{"message"} ;
}

?>
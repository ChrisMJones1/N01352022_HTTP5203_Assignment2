<?php
session_start();
$ticket_xml = simplexml_load_file("./xml/support_tickets.xml");

if(!isset($_SESSION['username']) ){
    header('Location: login.php');
}
if($_SESSION['priv'] !== '494ceea5e99b9ddbd222afab8d72ce8f') {
    header('Location: main.php');
}

$cached_ticket_count = $_SESSION['ticket_count'];

$_SESSION['ticket_count'] = count($ticket_xml->ticket);

$new_tickets = false;

if($cached_ticket_count !== $_SESSION['ticket_count'] && $_SESSION['priv'] === '494ceea5e99b9ddbd222afab8d72ce8f')
{
    $new_tickets = true;
}

$jsonstu = json_encode($new_tickets);

header('Content-Type: Application/json');
echo $jsonstu;

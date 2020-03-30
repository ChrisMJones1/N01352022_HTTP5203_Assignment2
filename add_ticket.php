<?php
session_start();
if(!isset($_SESSION['username']) ){
    header('Location: login.php');
}
if(!isset($_POST['ticket_submit']) ){
    header('Location: login.php');
}

if($_SESSION['priv'] === '494ceea5e99b9ddbd222afab8d72ce8f') {
    header('Location: main.php');
}

$ticket_xml = simplexml_load_file("./xml/support_tickets.xml");
$userid = $_SESSION['userid'];

$ticket_id = (int)$ticket_xml->xpath("//ticket[last()]/ticket_id")[0] + 1;

$time = new DateTime;
$current_datetime = $time->format(DateTime::ATOM);

$subject = $_POST['ticket_subject'];

$ticket = $ticket_xml->addChild('ticket');
$status_attr = $ticket->addAttribute('status', 'ongoing');
$user_id_attr = $ticket->addAttribute('user_id', $userid);

$ticket_id_child = $ticket->addChild('ticket_id', $ticket_id);
$date_of_issue_child = $ticket->addChild('date_of_issue', $current_datetime);
$subject_child = $ticket->addChild('subject', $subject);
$support_messages_child = $ticket->addChild('support_messages');

$ticket_xml->asXML('./xml/support_tickets.xml');
header('Location: main.php');



<?php
session_start();
$ticket_xml = simplexml_load_file("./xml/support_tickets.xml");

if(!isset($_SESSION['username']) ){
    header('Location: login.php');
}

$userid = $_SESSION['userid'];
$priv = $_SESSION['priv'];
$author_type = ($priv === '494ceea5e99b9ddbd222afab8d72ce8f' ? "support" : "client");
$title = $_POST['title'];
$content = $_POST['message'];
$ticketid = $_POST['ticket_id'];

$time = new DateTime;
$current_datetime = $time->format(DateTime::ATOM);

$message_node = $ticket_xml->xpath("//ticket[ticket_id=$ticketid]/support_messages")[0];

$message = $message_node->addChild('message');
$author_id = $message->addAttribute('author_id', $userid);
$author_type_attr = $message->addAttribute('author_type', $author_type);

$title_child = $message->addChild("title", $title);
$timestamp_child = $message->addChild("message_timestamp", $current_datetime);
$content_child = $message->addChild("content", $content);

$ticket_xml->asXML('./xml/support_tickets.xml');

$message_bg = ($priv === '494ceea5e99b9ddbd222afab8d72ce8f' ? "bg-primary text-white" : "border border-primary");
$message_orientation = ($priv === '494ceea5e99b9ddbd222afab8d72ce8f' ? "justify-content-start" : "justify-content-end");

$ticket_name = $_SESSION['username'];

$new_message = "
<div class= 'row mb-2 $message_orientation '><div class='col-8 rounded-lg $message_bg '><div class='container h5'>$title</div><div class='container'>$ticket_name</div><div class='container'>$content</div><div class='container h6'>$current_datetime</div></div></div>";
$json_return = array($ticketid ,$new_message);

$jsonstu = json_encode($json_return);

header('Content-Type: Application/json');
echo $jsonstu;



?>
<?php
session_start();
$ticket_body = $tickets = $ticket_logs = '';
$ticket_xml = simplexml_load_file("./xml/support_tickets.xml");
$users_xml = simplexml_load_file("./xml/users.xml");
$time = new DateTime;
$current_datetime = $time->format(DateTime::ATOM);



if(!isset($_SESSION['username']) ){
    header('Location: login.php');
}
$username = $_SESSION['username'];
$userid = $_SESSION['userid'];

$_SESSION['ticket_count'] = count($ticket_xml->ticket);

if($_SESSION['priv'] !== '494ceea5e99b9ddbd222afab8d72ce8f') {
    $tickets = $ticket_xml->xpath("//ticket[@user_id=$userid]");
    $author_type = "client";
} else {
    $tickets = $ticket_xml->ticket;
    $author_type = "support";

}



if(isset($_POST['status_submit'])) {
    $status_id = $_POST['ticket_id'];
    $resolved_value = ($_POST['status_submit'] === 'mark resolved' ? "resolved" : "ongoing");

    $resolved_node = $ticket_xml->xpath("//ticket[ticket_id=$status_id]")[0];
    $resolved_node->attributes()->status = $resolved_value;

    $ticket_xml->asXML('./xml/support_tickets.xml');
    $ticket_xml = simplexml_load_file("./xml/support_tickets.xml");


}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ticket Support</title>


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous">

    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</head>

<body>
<div class="jumbotron bg-primary">
    <h1 class="text-center">Ticket Support</h1>
    <div class="container text-right">
        <a class="btn btn-warning" href="logout.php">Log out</a>
    </div>
    <div class="container text-left text-white">User: <?= $username ?></div>
</div>



<div class="container">
    <div class="row">
        <div class="col-4">
            <div class="h2">Your Tickets
                <?php if($_SESSION['priv'] !== '494ceea5e99b9ddbd222afab8d72ce8f') { ?>
                    <div class="btn btn-success" data-toggle="modal" data-target="#add_ticket">Make New Ticket</div>
                <?php } else { ?>
                    <a href="main.php" id="ticket_refresh_button" class="btn btn-primary d-none">New Tickets. Click to refresh.</a>
                <?php } ?>
            </div>
            <?php foreach($tickets as $ticket) {
                $resolved_class = ((string)$ticket->attributes()->status === 'resolved' ? "border-success" : "border-warning");
                ?>

                <div id='ticket_stub_<?= $ticket->ticket_id ?>' class="container-fluid border rounded-sm mb-2 p-0 <?= $resolved_class ?>">
                    <a class="btn container-fluid" data-toggle="collapse" href="#ticket_<?= $ticket->ticket_id ?>">
                        <div class="container"><?= $ticket->subject ?></div>
                        <div class="container text-info">
                            <?php
                            $date = date_create($ticket->date_of_issue);
                            echo date_format($date,"Y/m/d H:i:s");
                            ?>
                        </div>
                        <div class="container">
                            <?= $ticket->attributes()->status ?>
                        </div>
                    </a>
                </div>

            <?php } ?>
        </div>
        <div id="tickets_box" class="col-8">
            <?php foreach($tickets as $ticket) {
                $resolved_button = ((string)$ticket->attributes()->status === 'resolved' ? "btn-warning" : "btn-success");
                $resolved_value = ((string)$ticket->attributes()->status === 'resolved' ? "reopen" : "mark resolved");
                ?>
                <div id="ticket_<?= $ticket->ticket_id ?>" data-parent="#tickets_box" class="container-fluid collapse border border-info mb-2">
                    <div class="container display-4"><?= $ticket->subject ?></div>
                    <div class="row">
                        <div class="col-4">

                            <div class="container">Submitted by:
                                <?php
                                $ticketuser = (string)$ticket->attributes()->user_id;
                                echo $users_xml->xpath("//user[user_id='$ticketuser']/username")[0]
                                ?>
                            </div>
                            <div class="container">On:
                                <?php
                                $date = date_create($ticket->date_of_issue);
                                echo date_format($date,"Y/m/d H:i:s");
                                ?>
                            </div>
                            <div class="container">Status: <?= $ticket->attributes()->status ?></div>

                            <?php if($_SESSION['priv'] === '494ceea5e99b9ddbd222afab8d72ce8f') { ?>
                                <form name="status_toggle" class="status_toggle" method="post" action="">
                                    <input class="d-none" name="ticket_id" value="<?= $ticket->ticket_id ?>" />
                                    <input type="submit" name="status_submit" class="form-control btn <?= $resolved_button ?>" value="<?= $resolved_value ?>" />

                                </form>
                            <?php } ?>

                        </div>
                        <div class="col-8">
                            <div id="ticket_messages_<?= $ticket->ticket_id ?>"  class="container-fluid messages border border-dark" style="overflow-y: scroll; max-height: 250px;">
                                <?php foreach ($ticket->support_messages->message as $message) {
                                    $message_bg = ((string)$message->attributes()->author_type === 'support' ? "bg-primary text-white" : "border border-primary");
                                    $message_orientation = ((string)$message->attributes()->author_type === 'support' ? "justify-content-start" : "justify-content-end");

                                    $ticket_user = (string)$message->attributes()->author_id;
                                    $ticket_name = $users_xml->xpath("//user[user_id='$ticket_user']/username")[0]
                                    ?>
                                    <div class="row mb-2 <?= $message_orientation ?>">
                                        <div class="col-8 rounded-lg <?= $message_bg ?>">
                                            <div class="container h5"><?= $message->title ?> <small><?= $ticket_name ?></small></div>
                                            <div class="container"><?= $message->content ?></div>
                                            <div class="container h6">
                                                <?php
                                                $date = date_create($message->message_timestamp);
                                                echo date_format($date,"Y/m/d H:i:s");
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="container m-1">
                                <section>
                                    <form id="message_form_<?= $ticket->ticket_id ?>" name="message_form" class="message_form" action="" method="post">
                                        <fieldset>
                                            <legend>New Message</legend>
                                            <div class="form-group">
                                                <label for="title_input_<?= $ticket->ticket_id ?>">Title:</label>
                                                <input type="text" class="form-control title_input"  id="title_input_<?= $ticket->ticket_id ?>" required />
                                            </div>
                                            <div class="form-group">
                                                <label for="message_input_<?= $ticket->ticket_id ?>">Message:</label>
                                                <textarea  rows="5" class="form-control message_input" name="message_input" id="message_input_<?= $ticket->ticket_id ?>" required></textarea>
                                            </div>
                                            <input id="ticketid_input_<?= $ticket->ticket_id ?>"  class="d-none ticketid_input" value="<?= $ticket->ticket_id ?>" />
                                            <input type="submit" name="message_submit" class="form-control" value="Submit" />
                                        </fieldset>
                                    </form>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<div id="add_ticket" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add_ticket_title" aria-hidden="true">
    <form method="post" action="add_ticket.php">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title h5" id="add_ticket_title">Create New Ticket</div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="ticket_subject">Ticket Subject:</label>
                        <input type="text" id="ticket_subject" name="ticket_subject" class="form-control" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <input type="submit" value="Create New Ticket" name="ticket_submit" class="button btn btn-success" />
                </div>
            </div>
        </div>
    </form>
</div>

<script src="scripts/script.js"></script>
</body>
</html>


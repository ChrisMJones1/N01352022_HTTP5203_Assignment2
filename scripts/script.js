$( document ).ready(function() {
    ticket_update();
    $(".message_form").on('submit', function (event) {
        event.preventDefault();
        let title = $(this).find(".title_input").val();
        let message = $(this).find(".message_input").val();
        let ticket_id = $(this).find(".ticketid_input").val();
        $.post('./add_message.php', { "title": title, "message": message, "ticket_id": ticket_id}, function(new_message){
            $("#ticket_messages_" + new_message[0]).append($(new_message[1]));
            $("#ticket_messages_" + new_message[0]).scrollTop($("#ticket_messages_" + new_message[0])[0].scrollHeight);
        })
    });
    function ticket_update() {
        $.post('./ticket_refresh.php', { }, function(new_tickets){
            if(new_tickets === true)
            {
                $("#ticket_refresh_button").removeClass('d-none');
            }
            setTimeout(ticket_update, 5000);
        })
    }
});

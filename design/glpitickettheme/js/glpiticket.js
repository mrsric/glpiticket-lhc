
var spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
var glpiTicket = {
	createTicket : function(chat_id) {

		var buttonTicket = $('#glpi-tickter-'+chat_id);
		var textButton = buttonTicket.text();
		buttonTicket.attr('disabled','disabled');	
		buttonTicket.addClass('disabled');	
		buttonTicket.html(spinner + ' ' + textButton);

		$.postJSON(WWW_DIR_JAVASCRIPT  + 'glpiticket/createanissue/' + chat_id, function(data){
			if (data.error == false) {
				buttonTicket.replaceWith(data.msg);
			} else {
				alert(data.msg);
				buttonTicket.removeAttr('disabled');
				buttonTicket.removeClass('disabled');
				buttonTicket.html(textButton);	
			}			
        });	
		return false;
	}	
};
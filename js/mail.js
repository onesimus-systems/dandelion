var mail = {
		areUnread: function() {
			$.getJSON("scripts/mail/getMailCount.php", function(data) {
				if (data['count'] > 0) {
					$("#mailicon").attr("src", "images/mail.png");
					$("#mailicon").attr("alt", "You have mail");
					$("#mailicon").attr("title", data['count']);
				}
			});
			
			setTimeout(function(){ mail.areUnread(); }, 10000);
		},
		
		getAllMail: function() {
			$.getJSON("scripts/mail/getAllMail.php", function(data) {
				// TODO: Create formatted table for mail items
			});
		}
};
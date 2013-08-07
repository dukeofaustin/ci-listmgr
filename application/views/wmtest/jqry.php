<!DOCTYPE html>
<html>
<head>
   <link rel="stylesheet" href="<?php echo base_url();?>style/nettut.css" type="text/css" media="screen" charset="utf-8">
   <script src="<?php echo base_url();?>assets/js/jquery-1.5.2.min.js" type="text/javascript"></script>

   <title>Demo Page</title>
</head>
<body>
<h3>This is the demo page for the simple AJAX post in CodeIgniter Using JQuery</h3>
	<h2>Username: myusername | Password: mypassword</h2>
	<h2>TRY TO ENTER nothing in the fields and click the Submit button.<br/>
	Try to mismatch the username and/or password and click the Submit button. </h2>
<div style="text-align:right; width:500px;" >
<div id="form_message"></div>
<label name="mess"></label>
<div id="login_form">
	<h1>Login to Enter</h1>
    <?php 
	echo form_open('wmtest/post_action');
	echo form_input('username', 'Username');
	echo form_password('password', 'Password');
	echo form_submit('submit', 'Login');
	echo anchor('login/signup_form', 'Create Account');
	echo form_close();
     ?>
</div><!-- end login_form-->    </div>
<script type="text/javascript">
$('#submit').click(function() {
	var username = $('#username').val();
	alert(username);
	if (!username || username == 'Username') {
		alert('Please enter your user name');
		return false;
	}
	var form_data = {
		username: $('#username').val(),
		password: $('#password').val(),
		ajax: '1'		
	};
	$.ajax({
		url: "<?php echo site_url('wmtest/post_action'); ?>",
		type: 'POST',
		contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		cache:false,
		async: false,
		data: form_data,
		success: function(data) {
 		       alert(data);
	               $('#form_message').html(data.message).css({'background-color' : data.bg_color}).fadeIn('slow');
 		}
	});
	return false;
});
	
	
</script>
<!-- end contact_form-->
</body>
</html>

<?php phpinfo();
exit(); ?>

<head>
	<script src='https://www.google.com/recaptcha/api.js?render=6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb'></script>
</head>

<body>
	<form name="contactform" action="check.php" method="post">
		<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
		<input type="hidden" name="action" value="validate_captcha">
		<input type="Submit" class="button" value="SEND"><input type="Reset" class="button" value="RESET">
	</form>
	<script>
		grecaptcha.ready(function() {
			grecaptcha.execute('6Lc5RnwbAAAAAF1iOYAEgWqmIesmvK7S-nES_7Pb', {
				action: 'homepage'
			}).then(function(token) {
				// Verify the token on the server.
				document.getElementById('g-recaptcha-response').value = token;
			});
		});
	</script>
</body>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	// Build POST request:
	$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
	$recaptcha_secret = '4trt54t544rt';
	$recaptcha_response = $_POST['g-recaptcha-response'];
	// Make and decode POST request:
	$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
	$recaptcha = json_decode($recaptcha);
	print_r($recaptcha);
	echo $recaptcha->score;
}
?>
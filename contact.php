<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A layout example with a side menu that hides on mobile, just like the Pure website.">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">

</head>


<body id="nrat-contact">
<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>
	<?php require_once("php/raceresultsutilities.php");
		LogActivity();
	?>

	<div id="main">
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>Get In Touch</h2>
		</div>

		<div class="content">
			<h2 class="content-subhead">Problems?</h2>
			<p>
				Found an issue?  Have questions or suggestions?  Drop me a line.
			</p>
			<h2 class="content-subhead">Results Missing?</h2>
			<p>
				Getting results from various races can be a bit of a chore depending
				on where and how they get posted.  It can be a little time consuming
				to copy/translate/format them to get them in the database.
			</p>
			<p>
				If the results are missing for some race that you would like maybe
				you can help me out with the front end processing.  Reach out if
				there a race you'd like to see that isn't there and I'll let you
				know what I need.
			</p>
			<h2 class="content-subhead">Send Me Email</h2>
			<p>
				The best way to get in touch is send me an email 
				<a href="mailto:info@nordicraceanalysis.com?
				 subject=NRAT%20Contact">HERE</a>
			</p>
			<h2 class="content-subhead">Super Bored?</h2>
			<p>
				You can always head over to my personal blog and read the multi-thousand
				word race reports I write.
			</p>
			<a href="http://www.btmski.com">www.btmski.com</a>
		</div>
	</div>
</div>

<script src="ui.js"></script>

</body>
</html>

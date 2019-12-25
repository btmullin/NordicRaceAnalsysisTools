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

<?php
// Setting the navigation flag for the year passed as an argument

// include the utilities functions
require_once 'php/raceresultsutilities.php';

{
	if (isset($_REQUEST["year"]))
	{
		$year = $_REQUEST["year"];
	}
	else
	{
		die("Must specify year");
	}
	echo "<body id=\"nrat-elmcreekseries-$year\">";
}
?>

<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<?php
			// include the utilities functions
			require_once 'php/raceresultsutilities.php';

			{
				if (isset($_REQUEST["year"]))
				{
					$year = $_REQUEST["year"];
				}
				else
				{
					die("Must specify year");
				}
				echo "<h2>Elm Creek Race Series $year</h2>";
			}
			?>
		</div>

		<div class="container">
			<div class="content">
			Follow one of these links to see the current series standings.<br>
			<?php
			// include the utilities functions
			require_once 'php/raceresultsutilities.php';

			{
				if (isset($_REQUEST["year"]))
				{
					$year = $_REQUEST["year"];
				}
				else
				{
					die("Must specify year");
				}
			}
			echo "<a href=\"elmcreekxc.php?style=Freestyle&gender=M&year=$year\">Freestyle Male</a><br>";
			echo "<a href=\"elmcreekxc.php?style=Freestyle&gender=F&year=$year\">Freestyle Female</a><br>";
			echo "<a href=\"elmcreekxc.php?style=Classic&gender=M&year=$year\">Classic Male</a><br>";
			echo "<a href=\"elmcreekxc.php?style=Classic&gender=F&year=$year\">Classic Female</a><br>";
			?>
			</div>
			
			<!-- /* Sidebar here */ -->
			<?php include "nratsidebar.php"; ?>
		</div>
	</div>
</div>

<script src="ui.js"></script>

</body>
</html>

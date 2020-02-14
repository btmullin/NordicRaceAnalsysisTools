<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detailed view of the results of an individual skier.">
	<meta name="author" content="Ben Mullin">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="../css/layouts/side-menu.css">
	<link rel="stylesheet" href="../css/nratstyle.css">

</head>
<?php
include '../php/raceresultsutilities.php';
// If this isn't a post, then we should display the form first
if (!isset($_POST['submit']) AND !isset($_REQUEST['team_id']))
{
	// This isn't a the initial submit or confirm so we should display the form
	// select the team
?>
	<form action="viewteamresults.php" method="post">
	Team: <select name='team_id'>
		<?php
			$result = RaceResultsQuery('SELECT * FROM Team ORDER BY Name');
			while ($row = $result->fetch_array())
			{
				$id = $row["TeamID"];
				$name = $row["Name"];
				echo "<option value=".$id.">".$name."</option>";
			}
		?>
	</select><br>
	<input type="submit" value="View Team Results" name="submit">
	</form><br>
	
	<?php
	// Make a table of teams and number of members
	echo "<table style=\"width:300px\">";
	//header
	echo "<tr>";
	$data = RaceResultsQuery("select Team.TeamID, Name, count(Affiliation.TeamID) as \"Members\" FROM Team, Affiliation WHERE Team.TeamID=Affiliation.TeamID GROUP By Affiliation.TeamID ORDER BY Name ASC");
	$header = $data->fetch_fields();
	foreach ($header as $col)
	{
		if ($col->name != "TeamID")
		{
			echo "<th><b>$col->name</b></th>";
		}
	}
	echo "</tr>";
	// body
	$lastdate = null;
	while ($row = $data->fetch_assoc())
	{
		echo "<tr>";
		// if it exists, grab the event and racer ids
		$team_id = $row["TeamID"];
		$team_name = $row["Name"];
		$count = $row["Members"];
		echo "<td><a href=\"viewteamresults.php?team_id=".$team_id."\">$team_name</a></td>";
		echo "<td>$count</td>";
		echo "</tr>";
	}
	echo "</table>";
	?>
<?php
}
else
{
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// The user has assigned a team
		$team_id = $_POST["team_id"];
	}
	else
	{
		$team_id = $_REQUEST["team_id"];
	}
	


    // get the list of events in the season for that team
    $event_list = RaceResultsQuery("select Result.EventID FROM Result, Event WHERE RacerID in (select RacerID FROM Affiliation WHERE TeamID=$team_id) AND Event.EventDate >= '2019-06-01' AND Event.EventID=Result.EventID group by Result.EventID");
    
    // for each event
    foreach ($event_list as $event)
    {
        // TODO - get the event information
        $event_id = $event["EventID"];
        $event_info = RaceResultsQuery("select * From EventView WHERE EventID=$event_id");
        
        echo "<b>".$event_info["FullName"]."</b><br>";
        
        // TODO - get the results
    }








	// Show Team
	//$result = RaceResultsQuery("SELECT Name FROM Team WHERE TeamID=$team_id");
	//$row = $result->fetch_array();
	//$team_name = $row["Name"];
	//echo "<b>Team: $team_name</b><br>";
	
	//$result = RaceResultsQuery("SELECT FirstName, LastName FROM Racer, Affiliation WHERE Racer.RacerID=Affiliation.RacerID AND Affiliation.TeamID=$team_id ORDER BY LastName ASC, FirstName ASC");
	//ResultToTable($result,"300px");
}
?>
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
        $result = RaceResultsQuery("select * From EventView WHERE EventID=$event_id");
        $event_info = $result->fetch_array();
        $team_name = $event_info["Name"];
        
        echo "<b>".$event_info["FullName"]."</b><br>";
        
        // get the list of team members in the racer
        $racers = RaceResultsQuery("SELECT Result.RacerID, Racer.FirstName, Racer.LastName, Result.TimeInSec, Racer.Gender FROM Result, Affiliation, Racer WHERE EventID=$event_id AND TeamID=$team_id AND Result.RacerID=Affiliation.RacerID AND Racer.RacerID=Result.RacerID ORDER BY Result.TimeInSec");
        // for each racer, list their overall and gender place
        echo "<table style=\"width:400px\"><tr><th>First Name</th><th>Last Name</th><th>Overall</th><th>Gender</th></tr>";
        while ($racer = $racers->fetch_array())
        {
            $fn = $racer["FirstName"];
            $ln = $racer["LastName"];

            $time = $racer["TimeInSec"];
            $gender = $racer["Gender"];
            
            // calculate overall place
            $oapq = "SELECT * FROM Result, Racer WHERE EventID=$event_id AND Racer.RacerID=Result.RacerID AND TimeInSec<$time";
            $oapr = RaceResultsQuery($oapq);
            $oap = $oapr->num_rows+1;
            
            // calculate gender place
            $gpq = "SELECT * FROM Result, Racer WHERE EventID=$event_id AND Racer.RacerID=Result.RacerID AND Gender=\"$gender\" AND TimeInSec<$time";
            $gpr = RaceResultsQuery($gpq);
            $gp = $gpr->num_rows+1;

            echo "<tr><td>$fn</td><td>$ln</td><td>$oap</td><td>$gp</td></tr>";
        }
        echo"</table>";
        echo "<br>";
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
<?php
// Put all events into a table for viewing on the web

// include the utilities functions
require_once 'php/raceresultsutilities.php';

LogActivity();

// Open the database
$mysqli = OpenRaceResultsDatabase();

// get the event data
$query = 'SELECT 
			EventID AS "EventID",
			EventDate AS "Date",
			FullName AS "Event Name",
			DistanceInKM AS "Distance (km)",
			Technique AS "Technique",
			RaceType AS "Type",
			(SELECT COUNT(*) FROM Result WHERE Result.EventID=EventView.EventID) as Participants
			FROM EventView
			ORDER BY EventDate DESC, FullName DESC';

$result = $mysqli->query($query);

if ($result)
{
	ResultToTable($result);
}
else
{
	echo "Query did not work<br/>";
}

?>
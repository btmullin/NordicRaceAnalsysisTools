<?php
// Put all events into a table for viewing on the web

// include the utilities functions
require_once 'raceresultsutilities.php';

// get the event data
$query = "SELECT *, (SELECT COUNT(*) FROM Result WHERE Result.EventID=EventView.EventID) as Participants FROM EventView";

$result = RaceResultsQuery($query);

if ($result)
{
	ResultToTable($result);
}
else
{
	echo "Query did not work<br/>";
}

?>
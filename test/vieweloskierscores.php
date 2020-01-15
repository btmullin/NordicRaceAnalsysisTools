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

if (isset($_REQUEST["rid"]))
{
	$RacerID = $_REQUEST["rid"];
}
else
{
	die("No Racer Selected");
}

$query = 'SELECT 
			FirstName AS "First Name",
			LastName AS "Last Name",
			BirthYear AS "Birth Year",
			HomeTown AS "Home Town",
			HomeState AS "Home State",
			Gender AS "Gender",
			(SELECT COUNT(*) FROM Result WHERE Result.RacerID='.$RacerID.') as Results
		FROM Racer WHERE RacerID = '.$RacerID;

$result = RaceResultsQuery($query);

if ($result)
{
	ResultToTable($result);
}
else
{
	echo "Query did not work<br/>";
}

echo "<br>";
// Show Scores
$result = RaceResultsQuery("SELECT Score, EventView.FullName from EloScore, EventView WHERE EventView.EventID=EloScore.EventID AND RacerID=$RacerID ORDER BY EventView.EventDate ASC");
ResultToTable($result,"80%");

?>
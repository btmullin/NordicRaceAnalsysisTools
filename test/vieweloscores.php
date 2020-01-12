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

// Show Team
$result = RaceResultsQuery("SELECT OuterRacer.RacerID, FirstName, LastName, (SELECT Score FROM EloScore, Event WHERE EloScore.RacerID=OuterRacer.RacerID AND Event.EventID=EloScore.EventID ORDER BY Event.EventDate DESC LIMIT 1) as LastScore FROM EloScore, Racer as OuterRacer WHERE EloScore.RacerID=OuterRacer.RacerID GROUP BY OuterRacer.RacerID ORDER BY LastScore DESC");
ResultToTable($result,"80%");

?>
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
$data = RaceResultsQuery('SELECT OuterRacer.RacerID, FirstName, LastName,
								(SELECT Score
									FROM EloScore, Event 
									WHERE EloScore.RacerID=OuterRacer.RacerID AND 
										Event.EventID=EloScore.EventID
									ORDER BY Event.EventDate DESC LIMIT 1) as "Elo Score"
							FROM EloScore, Racer as OuterRacer
							WHERE EloScore.RacerID=OuterRacer.RacerID
							GROUP BY OuterRacer.RacerID ORDER BY "Elo Score" DESC');


//	$query = 'SELECT @r := @r+1 AS Place,
//					 z.*
//			  FROM(SELECT Result.Bib,
//						  Racer.RacerID,
//						  CONCAT(Racer.FirstName,\' \',Racer.LastName) AS "Skier Name",
//						  Racer.Gender as "Gender",
//						  TIME_FORMAT(SEC_TO_TIME(Result.TimeInSec),\'%H:%i:%s\') AS Time,
//						  FORMAT(((Result.TimeInSec-'.$min_time.')/'.$min_time.'*100),2) AS "% Back"
//				   FROM Racer INNER JOIN Result ON Racer.RacerID = Result.RacerId WHERE Result.EventID='.$event_id.' ORDER BY Result.TimeInSec)z,(select @r:=0)y';



// for some reason the query is not sorting, so grab all results and sort ourselves
$scores = array();
while ($row = $data->fetch_assoc()) {
  $scores[] = $row;
}
usort($scores, function($a, $b) {
    return $b['Elo Score'] - $a['Elo Score'];
});


$width = "50%";

echo "<table";
if (!is_null($width))
{
	echo " style=\"width:".$width."\"";
}
echo ">";
//header
echo "<tr>";
$header = $data->fetch_fields();
foreach ($header as $col)
{
	if (($col->name != "EventID") && ($col->name != "RacerID"))
	{
		echo "<th><b>$col->name</b></th>";
	}
}
echo "</tr>";

// body

$lastdate = null;
foreach ($scores as $row)
{
	echo "<tr>";
	// if it exists, grab racer ids
	$racer_id = null;
	if (array_key_exists("RacerID", $row))
	{
		$racer_id = $row["RacerID"];
	}

	foreach ($row as $key => $field)
	{
		if ($key == "RacerID")
		{
			// we skip outputting the racer id now
			// we will use it along with the name to make a link
		}
		else if ($key == "Skier Name")
		{
			echo '<td class="racerid">';
			if ($racer_id != null)
			{
				echo '<a href="viewskier.php?rid='.$racer_id.'">'.$field.'</a>';
			}
			else
			{
				echo $field."What";
			}
			echo "</td>";
		}
		else if ($key == "Elo Score")
		{
			echo "<td>";
			if ($racer_id != null)
			{
				echo '<a href="vieweloskierscores.php?rid='.$racer_id.'">'.$field.'</a>';
			}
			else
			{
				echo $field."What";
			}
			echo "</td>";
		}
		else
		{
			echo "<td>";
			echo "$field";
			echo "</td>";
		}
	}
	echo "</tr>";
}
echo "</table>";

?>
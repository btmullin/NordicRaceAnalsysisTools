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
								(SELECT Points
									FROM NRATPoints, Event 
									WHERE NRATPoints.RacerID=OuterRacer.RacerID AND 
										Event.EventID=NRATPoints.EventID
									ORDER BY Event.EventDate DESC LIMIT 1) as "NRAT Points"
							FROM NRATPoints, Racer as OuterRacer
							WHERE NRATPoints.RacerID=OuterRacer.RacerID
							GROUP BY OuterRacer.RacerID ORDER BY "NRAT Points" DESC');


// for some reason the query is not sorting, so grab all results and sort ourselves
$scores = array();
while ($row = $data->fetch_assoc()) {
  $scores[] = $row;
}
usort($scores, function($a, $b) {
    return $a['NRAT Points'] - $b['NRAT Points'];
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
echo "<th><b>Rank</b></th>";
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
$rank = 0;
foreach ($scores as $row)
{
	$rank += 1;
	echo "<tr>";
	echo "<td>$rank</td>";
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
				echo '<a href="viewskiernratpoints.php?rid='.$racer_id.'">'.$field.'</a>';
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
<?php
// Scatter plot of percent back versus event date for a given racer
function DateLabelCallback($val)
{
	$dt = new DateTime();
	$dt->setTimestamp($val);
	return $dt->format('Y');
}

	include 'raceresultsutilities.php';
	
	// Get racers results
	$RacerID = $_REQUEST["rid"];
	
	$mysqli = OpenRaceResultsDatabase();

	// get a count of the events for that racer with each technique type
	$q = "SELECT TechniqueOut.Name, (SELECT COUNT(*) FROM Result,Event WHERE Result.EventID=Event.EventID AND Event.Technique=TechniqueOut.TechniqueID AND Result.RacerID=$RacerID) as RaceCount FROM Technique as TechniqueOut";
	$result = $mysqli->query($q);
	$data = array();
	$labels = array();
	$total = 0;
	while ($row = $result->fetch_array())
	{
		$total += $row["RaceCount"];
		$data[] = $row["RaceCount"];
		$labels[] = $row["Name"];
	}
	for ($i = 0; $i < count($data); $i++)
	{
		$data[i]/$total;
	}
	
	
	// Build the graph
	require_once('jpgraph/src/jpgraph.php');
	require_once('jpgraph/src/jpgraph_pie.php');
	$graph = new PieGraph(500, 500);
	$graph->SetScale('intint');
	$graph->title->Set('Racer Technique Distribution');

	$p1 = new PiePlot($data);
	$p1->SetLegends($labels);
	$p1->SetLabelType(PIE_VALUE_ABS);
	$p1->value->SetFormat("%d races");
	$p1->SetLabelPos(.75);
	$graph->Add($p1);
	
	// Build the graph
	$graph->Stroke();
?>
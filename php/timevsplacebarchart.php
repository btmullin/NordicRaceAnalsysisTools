<?php
// Build a graph for the results of a race plotting a bar chart of racer time
// over position
function TimeLabelCallback($val)
{
	return gmdate('H:i:s',$val);
}

	include 'raceresultsutilities.php';
	
	// Get the common results
	$EventID = $_REQUEST["eid"];
	
	$mysqli = OpenRaceResultsDatabase();

	$q = "SELECT * FROM Result WHERE Result.EventID=$EventID ORDER BY Result.TimeInSec";
	
	// Build a pair of arrays
	$result = $mysqli->query($q);
	$x = array();
	$y = array();
	$i = 1;
	while ($row = $result->fetch_array())
	{
		$x[] = $i;
		$y[] = $row["TimeInSec"];
		$i++;
	}
	
	// Get event titles
	$q = "SELECT FullName FROM EventView WHERE EventID=$EventID";
	$result = $mysqli->query($q);
	$row = $result->fetch_array();
	$EventName = $row["FullName"];
	
	// Build the graph
	require_once('jpgraph/src/jpgraph.php');
	require_once('jpgraph/src/jpgraph_bar.php');
	$graph = new Graph(800, 600);
	$graph->SetScale('intint');
	$graph->title->Set("$EventName - Time vs Place");
	$graph->xaxis->SetTitle("Place","center");
	$graph->yaxis->SetTitle("Time","middle");
	$graph->SetMargin(100, 50, 0, 75);
	$graph->yaxis->SetTitleMargin(60);
	$graph->xaxis->SetTitleMargin(50);
	$graph->yaxis->SetLabelFormatCallback('TimeLabelCallback');
	$graph->xaxis->SetLabelAngle(45);
	$graph->xgrid->Show();
	
	
	// Plot the included results
	$bar = new BarPlot($y);
	$bar->SetFillColor("blue");
	$bar->SetWidth(.8);
	$graph->Add($bar);
	$graph->yaxis->scale->SetAutoMin(min($y));

	// Build the graph
	$graph->Stroke();
?>
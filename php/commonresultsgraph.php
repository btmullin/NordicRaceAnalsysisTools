<?php
function LabelCallback($val)
{
	return gmdate('H:i:s',$val);
}

	include 'raceresultsutilities.php';
	
	// Get the common results
	$EventID1 = $_REQUEST["e1"];
	$EventID2 = $_REQUEST["e2"];
	$PBLimit = $_REQUEST["pb"];
	$RacerID = $_REQUEST["rid"];
	
	$result = RaceResultsQuery("SELECT MIN(TimeInSec) AS WinningTime From Result WHERE Result.EventId=$EventID1");
	$min_time1 = $result->fetch_assoc()["WinningTime"];
	$result = RaceResultsQuery("SELECT MIN(TimeInSec) AS WinningTime From Result WHERE Result.EventId=$EventID2");
	$min_time2 = $result->fetch_assoc()["WinningTime"];
	$limit1 = $min_time1*(1+$PBLimit/100);
	$limit2 = $min_time2*(1+$PBLimit/100);

	$q = "SELECT Racer.RacerID, Racer.FirstName, Racer.LastName, r1.TimeInSec as \"Race 1 Time\", r2.TimeInSec as \"Race 2 Time\"
			FROM Racer, Result r1, Result r2
			WHERE r1.EventID=$EventID1 AND r2.EventID=$EventID2 AND r1.RacerID=r2.RacerID AND Racer.RacerID=r1.RacerID";
	
	// Build a pair of arrays
	$result = RaceResultsQuery($q);
	$x_inc = array();
	$y_inc = array();
	$x_ex = array();
	$y_ex = array();
	$x_racer = array();
	$y_racer = array();
	while ($row = $result->fetch_array())
	{
		if (($row["Race 1 Time"] <= $limit1) AND ($row["Race 2 Time"] <= $limit2))
		{
			$x_inc[] = $row["Race 1 Time"];
			$y_inc[] = $row["Race 2 Time"];
		}
		else
		{
			$x_ex[] = $row["Race 1 Time"];
			$y_ex[] = $row["Race 2 Time"];
		}
		
		// If this is the racer being compared get the data point
		if ($row["RacerID"] == $RacerID)
		{
			$x_racer[] = $row["Race 1 Time"];
			$y_racer[] = $row["Race 2 Time"];
		}
	}
	
	// Get event titles
	$q = "SELECT FullName FROM EventView WHERE EventID=$EventID1";
	$result = RaceResultsQuery($q);
	$row = $result->fetch_array();
	$Event1Name = $row["FullName"];
	$q = "SELECT FullName FROM EventView WHERE EventID=$EventID2";
	$result = RaceResultsQuery($q);
	$row = $result->fetch_array();
	$Event2Name = $row["FullName"];
	
	// Build the graph
	require_once('jpgraph/src/jpgraph.php');
	require_once('jpgraph/src/jpgraph_scatter.php');
	$graph = new Graph(800, 600);
	$graph->SetScale('intint');
	$graph->title->Set('Common Racer Results');
	$graph->xaxis->SetTitle("$Event1Name","center");
	$graph->yaxis->SetTitle("$Event2Name","middle");
	$graph->SetMargin(100, 50, 0, 75);
	$graph->yaxis->SetTitleMargin(60);
	$graph->xaxis->SetTitleMargin(50);
	$graph->xaxis->SetLabelFormatCallback('LabelCallback');
	$graph->yaxis->SetLabelFormatCallback('LabelCallback');
	$graph->xaxis->SetLabelAngle(45);
	$graph->xgrid->Show();
	
	
	// Plot the included results
	$scatter = new ScatterPlot($y_inc,$x_inc);
	$scatter->mark->SetFillColor("blue");
	$graph->Add($scatter);
	
	// Do the trendline?
	require_once('jpgraph/src/jpgraph_line.php');
	
	/* Library from Richard At Home seems off low (about half)
	$trendeq = linear_regression($x,$y);
	$m = $trendeq["m"];
	$b = $trendeq["b"];*/
	
	require_once( 'jpgraph/src/jpgraph_utils.inc.php');
	$linreg = new LinearRegression($x_inc, $y_inc);
	$ab = $linreg->GetAB();
	$m = $ab[1];
	$b = $ab[0];
	
	$tx = array();
	$ty = array();
	$tx[] = min($x_inc);
	$ty[] = $m*$tx[0]+$b;
	$tx[] = max($x_inc);
	$ty[] = $m*$tx[1]+$b;

	// Set the properties
	$trendline = new ScatterPlot($ty, $tx);
	$trendline->mark->SetColor("red");
	$trendline->mark->SetFillColor("red");
	$trendline->link->Show();
	$trendline->link->SetWeight(1);
	$trendline->link->SetColor('red');
	$trendline->link->SetStyle('solid');
	$graph->Add($trendline);

	// Plot the excluded results
	if (count($y_ex) > 0)
	{
		$excluded = new ScatterPlot($y_ex,$x_ex);
		$excluded->mark->SetColor("pink");
		$excluded->mark->SetFillColor("pink");
		$graph->Add($excluded);
	}
	
	// Plot the racer being compared
	if (count($y_racer) > 0)
	{
		$racer = new ScatterPlot($y_racer,$x_racer);
		$racer->mark->SetColor("green");
		$racer->mark->SetFillColor("green");
		$graph->Add($racer);
	}

	// Build the graph
	$graph->Stroke();
?>
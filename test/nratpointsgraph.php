<?php
// Scatter plot of percent back versus event date for a given racer
function DateLabelCallback($val)
{
	$dt = new DateTime();
	$dt->setTimestamp($val);
	return $dt->format('Y-m-d');
}

	include '../php/raceresultsutilities.php';
	
	// Get racers results
	$RacerID = $_REQUEST["rid"];
	
	$q = "SELECT RacePoints, EventView.FullName, EventView.EventDate from NRATPoints, EventView WHERE EventView.EventID=NRATPoints.EventID AND RacerID=$RacerID ORDER BY EventView.EventDate ASC";
	// Build a pair of arrays
	$result = RaceResultsQuery($q);
	$x = array();
	$y = array();
	$e = 1;
	while ($row = $result->fetch_array())
	{
		$x[] = (new DateTime($row["EventDate"]))->getTimestamp();
		$y[] = $row["RacePoints"];
	}
	
	// Build the graph
	require_once('jpgraph/src/jpgraph.php');
	require_once('jpgraph/src/jpgraph_date.php');
	require_once('jpgraph/src/jpgraph_scatter.php');
	$graph = new Graph(800, 600);
	$graph->SetScale('intint');
	$graph->title->Set('Elo Score Over Time');
	$graph->xaxis->SetTitle("Event Date","center");
	$graph->yaxis->SetTitle("NRAT Points","middle");
	$graph->SetMargin(100, 50, 0, 75);
	$graph->yaxis->SetTitleMargin(60);
	$graph->xaxis->SetTitleMargin(50);
	$graph->xaxis->SetLabelFormatCallback('DateLabelCallback');
	$graph->xaxis->SetLabelAngle(45);
	$graph->xgrid->Show(false);
	$graph->ygrid->Show(false);

	// Add some bands to identify years
	require_once('jpgraph/src/jpgraph_plotband.php');
	require_once( 'jpgraph/src/jpgraph_utils.inc.php');
	$q = "SELECT MIN(YEAR(Event.EventDate)) as Start, MAX(YEAR(Event.EventDate)) as End FROM Event";
	$result = RaceResultsQuery($q);
	$row = $result->fetch_assoc();
	$minyear = $row["Start"];
	$maxyear = $row["End"];
	$year = $minyear;
	while ($year <= $maxyear)
	{
		$d1 = new DateTime("$year-1-1");
		$year++;
		$d2 = new DateTime("$year-1-1");
		if ($year%2 == 1)
		{
			$color = 175;
		}
		else
		{
			$color = 225;
		}
		$band = new PlotBand(VERTICAL, BAND_SOLID, $d1->getTimestamp(), $d2->getTimestamp(), array($color,255,255));
		$band->ShowFrame(true);
		$graph->AddBand($band);
	}
	$dateUtils = new DateScaleUtils();
	$min = min($x);
	$max = max($x);
    $startyear = date('Y',$min);
	$endyear = date('Y',$max);
	for($year=$startyear; $year <= $endyear; ++$year ) {
		$tickPositions[$i++] = mktime(0,0,0,1,1,$year);
	}
	//list($tickPositions,$minTickPositions) = $dateUtils->GetTicksFromMinMax($min, $max, DS_UTILS_YEAR1);
	if (($endyear - $startyear) >= 2)
	{
		$graph->xaxis->SetTickPositions($tickPositions);
	}
	

	// Plot the included results
	$scatter = new ScatterPlot($y,$x);
	$scatter->mark->SetFillColor("blue");
	$scatter->link->Show();
	$scatter->link->SetWeight(2);
	$scatter->link->SetColor("blue");
	$graph->Add($scatter);
		
	// Build the graph
	$graph->Stroke();
?>
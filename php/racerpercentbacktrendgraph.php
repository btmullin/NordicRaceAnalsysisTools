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
	
	$q = "SELECT Event.EventDate,
			Result.TimeInSec,
			((TimeInSec - (SELECT MIN(TimeInSec) FROM Result as R1 WHERE R1.EventID=Event.EventID))/
						  (SELECT MIN(TimeInSec) FROM Result as R2 WHERE R2.EventID=Event.EventID)*100) as \"PB\"
			FROM Event, Racer, Result
			WHERE Racer.RacerID=Result.RacerID AND
				Result.EventID=Event.EventID AND
				Racer.RacerID=$RacerID
			ORDER BY Event.EventDate";
	
	// Build a pair of arrays
	$result = RaceResultsQuery($q);
	$x = array();
	$y = array();
	$e = 1;
	while ($row = $result->fetch_array())
	{
		$x[] = (new DateTime($row["EventDate"]))->getTimestamp();
		$y[] = $row["PB"];
	}
	
	// Build the graph
	require_once('jpgraph/src/jpgraph.php');
	require_once('jpgraph/src/jpgraph_date.php');
	require_once('jpgraph/src/jpgraph_scatter.php');
	$graph = new Graph(800, 600);
	$graph->SetScale('intint');
	$graph->title->Set('Racer Percent Back Trend Over Time');
	$graph->xaxis->SetTitle("Event Date","center");
	$graph->yaxis->SetTitle("Percent Back","middle");
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
	$graph->Add($scatter);
	
	// Do the trendline?
	require_once('jpgraph/src/jpgraph_line.php');
	require_once( 'jpgraph/src/jpgraph_utils.inc.php');
	$linreg = new LinearRegression($x, $y);
	$ab = $linreg->GetAB();
	$m = $ab[1];
	$b = $ab[0];
	
	$tx = array();
	$ty = array();
	$tx[] = min($x);
	$ty[] = $m*$tx[0]+$b;
	$tx[] = max($x);
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
	
	// Build the graph
	$graph->Stroke();
?>
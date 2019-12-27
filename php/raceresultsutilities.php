<?php
// This file is a collection of utility functions for use in PHP/Racer DB
// usage.


// This function will output the racer data contained in the argument into a
// table.  It assumes the data is the query result for all columns of the Racer
// table.
function RacersToTable($data) {
	if ($data)
	{
		echo '<table border="1">';
		echo '<tr><td><b>ID</b></td><td><b>First Name</b></td><td><b>Last Name</b></td><td><b>Home Town</b></td><td><b>State</b></td><td><b>Gender</b></td><td><b>Birth Year</b></td><td><b>Primary ID</b></td></tr>';
		while($row = $data->fetch_array())
		{
			echo '<tr>';
			$id = $row["RacerID"];
			$fn = $row["FirstName"];
			$ln = $row["LastName"];
			$ht = $row["HomeTown"];
			$hs = $row["HomeState"];
			$gen = $row["Gender"];
			$by = $row["BirthYear"];
			$prim = $row["PrimaryRacerID"];
			echo '<td>'.$id.'</td><td>'.$fn.'</td><td>'.$ln.'</td><td>'.$ht.'</td><td>'.$hs.'</td><td>'.$gen.'</td><td>'.$by.'</td><td>'.$prim.'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
}

// This function opens the race results database and returns the mysqli object
function OpenRaceResultsDatabase()
{
	include 'nratdbsettings.php';
	$mysqli = new mysqli($hostname,$username,$password,$racedb);
	unset($hostname, $username, $password, $database);
	if ($mysqli->errno)
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	return $mysqli;
}


// This function outputs a mysql result to a table including header
function ResultToTable($data)
{
	if ($data)
	{
		echo "<table>";
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
		while ($row = $data->fetch_assoc())
		{
			echo "<tr";
			// if it exists, grab the event and racer ids
			$racer_id = null;
			if (array_key_exists("RacerID", $row))
			{
				$racer_id = $row["RacerID"];
			}
			$event_id = null;
			if (array_key_exists("EventID", $row))
			{
				$event_id = $row["EventID"];
			}
			
			if (array_key_exists("Date", $row) or array_key_exists("Event Date", $row))
			{
				if (array_key_exists("Date", $row))
				{
					$d = $row["Date"];
				}
				else
				{
					$d = $row["Event Date"];
				}
				if (($lastdate !== null) and (!SameSeason($d, $lastdate)))
				{
					// need a separator indicator above
					echo ' class="divisor"';
				}
				else
				{
					// normal row
				}
				$lastdate = $d;
			}
			
			echo ">";

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
				else if ($key == "EventID")
				{
					// we skip outputting the event id now
					// we will use it along with the event name to make a link
				}
				else if ($key == "Event Name")
				{
					echo '<td class="eventid">';
					if ($event_id != null)
					{
						echo '<a href="viewresults.php?eid='.$event_id.'">'.$field.'</a>';
					}
					else
					{
						echo $field;
					}
					echo "</td>";
				}
				else if ($key == "Source")
				{
					// This is the source of the event data, make it a link
					echo '<td><a href="'.$field.'">'.$field.'</a></td>';
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
	}
}

// This function outputs a table
function ArrayToTable($header, $data)
{
	if ($data)
	{
		echo "<table>";
		//header
		echo "<tr>";
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
		foreach ($data as $row)
		{
			echo "<tr";
			// if it exists, grab the event and racer ids
			$racer_id = null;
			if (array_key_exists("RacerID", $row))
			{
				$racer_id = $row["RacerID"];
			}
			$event_id = null;
			if (array_key_exists("EventID", $row))
			{
				$event_id = $row["EventID"];
			}
			
			if (array_key_exists("Date", $row) or array_key_exists("Event Date", $row))
			{
				if (array_key_exists("Date", $row))
				{
					$d = $row["Date"];
				}
				else
				{
					$d = $row["Event Date"];
				}
				if (($lastdate !== null) and (!SameSeason($d, $lastdate)))
				{
					// need a separator indicator above
					echo ' class="divisor"';
				}
				else
				{
					// normal row
				}
				$lastdate = $d;
			}
			
			echo ">";

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
				else if ($key == "EventID")
				{
					// we skip outputting the event id now
					// we will use it along with the event name to make a link
				}
				else if ($key == "Event Name")
				{
					echo '<td class="eventid">';
					if ($event_id != null)
					{
						echo '<a href="viewresults.php?eid='.$event_id.'">'.$field.'</a>';
					}
					else
					{
						echo $field;
					}
					echo "</td>";
				}
				else if ($key == "Source")
				{
					// This is the source of the event data, make it a link
					echo '<td><a href="'.$field.'">'.$field.'</a></td>';
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
	}
}

/**
 * linear regression function
 * @param $x array x-coords
 * @param $y array y-coords
 * @returns array() m=>slope, b=>intercept
 */
function linear_regression($x, $y) {

  // calculate number points
  $n = count($x);
  
  // ensure both arrays of points are the same size
  if ($n != count($y)) {

    trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);
  
  }

  // calculate sums
  $x_sum = array_sum($x);
  $y_sum = array_sum($y);

  $xx_sum = 0;
  $xy_sum = 0;
  
  for($i = 0; $i < $n; $i++) {
  
    $xy_sum+=($x[$i]*$y[$i]);
    $xx_sum+=($x[$i]*$x[$i]);
    
  }
  
  // calculate slope
  $m = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));
  
  // calculate intercept
  $b = ($y_sum - ($m * $x_sum)) / $n;
    
  // return result
  return array("m"=>$m, "b"=>$b);

}

/**
 * Creates a HTML table containing the results from the specified event.
 * @param $event_id the event id index
 * @returns nothing
 */
function raceresultstable($event_id) {
	// Open the database
	$mysqli = OpenRaceResultsDatabase();

	$result = $mysqli->query("SELECT MIN(TimeInSec) AS WinningTime From Result WHERE Result.EventID=$event_id");
	$min_time = $result->fetch_assoc()["WinningTime"];
	// Build the race results query
	$query = 'SELECT @r := @r+1 AS Place,
					 z.*
			  FROM(SELECT Result.Bib,
						  Racer.RacerID,
						  CONCAT(Racer.FirstName,\' \',Racer.LastName) AS "Skier Name",
						  Racer.Gender as "Gender",
						  TIME_FORMAT(SEC_TO_TIME(Result.TimeInSec),\'%H:%i:%s\') AS Time,
						  FORMAT(((Result.TimeInSec-'.$min_time.')/'.$min_time.'*100),2) AS "% Back"
				   FROM Racer INNER JOIN Result ON Racer.RacerID = Result.RacerId WHERE Result.EventID='.$event_id.' ORDER BY Result.TimeInSec)z,(select @r:=0)y';

   $result = $mysqli->query($query);
   
	if ($result)
	{
		$header = $result->fetch_fields();
		// hacking a new column into the result so copy one header to modify it
		$header[] = clone $header[0];
		$header[count($header)-1]->name = "Gender Place";
		for ($set = array (); $row = $result->fetch_assoc(); $set[] = $row);
		
		// Add in gender place
		$mplace = 0;$fplace = 0;
		foreach ($set as &$s)
		{
		   if ($s["Gender"] == "M")
		   {
			   $mplace++;
			   $s["Gender Place"] = $mplace;
		   }
		   else if ($s["Gender"] == "F")
		   {
			   $fplace++;
			   $s["Gender Place"] = $fplace;
		   }
	   }

		ArrayToTable($header,$set);
	}
	else
	{
		echo $result."<br>";
		echo "DB error";
	}
}

/**
 * Determines if two dates are in the same "season"
 * @param $date_1 the date of one item
 * @param $date_2 the date of the second item
 * @returns true if the same season
 */
function SameSeason($date_1, $date_2) {
	$d1 = date_parse($date_1);
	$d2 = date_parse($date_2);
	
	if ((($d1["year"] == $d2["year"]) and
			((($d1["month"] >= 6) and ($d2["month"] >= 6)) or
			 (($d1["month"] < 6) and ($d2["month"] < 6))))  or
		(($d1["year"] == ($d2["year"]+1)) and ($d2["month"] >= 6) and ($d1["month"] < 6)) or
		(($d2["year"] == ($d1["year"]+1)) and ($d1["month"] >= 6) and ($d2["month"] < 6)))
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Logs the activity for statistical analysis purposes.
 * @param $parms is an array of data relative to the calling function
 * @returns none
 */
function LogActivity($parms = null)
{
	// Open the file
	$handle = fopen("/home/btmullin/www/nordicraceanalysis/logs/log.txt", "a");

	if ($handle)
	{
		fwrite($handle, strftime("%Y-%m-%d %H:%M:%S, "));
		$backtrace = debug_backtrace();
		fwrite($handle, $backtrace[0]["file"]);
		if ($parms != null)
		{
			foreach ($parms as $p)
			{
				fwrite($handle, ", ".$p);
			}
		}
		fwrite($handle, "\n");
		fclose($handle);
	}
}

/**
 * @param $e1 - event id for teh first race
 * @param $e2 - event id for the second race
 * @param $limit - % back limit for the comparison
 * @param $rid - racer id to predict
 * @returns array containing m and b to convert e1 to e2 and the predicted time
 */
function LinearRegressionRaceCompare($e1, $e2, $limit, $rid = null)
{
	// Open the database
	$mysqli = OpenRaceResultsDatabase();
	// Get the winning times to use in calculating the percent back
	$result = $mysqli->query("SELECT MIN(TimeInSec) AS WinningTime From Result WHERE Result.EventID=$e1");
	$min_time1 = $result->fetch_assoc()["WinningTime"];
	$result = $mysqli->query("SELECT MIN(TimeInSec) AS WinningTime From Result WHERE Result.EventID=$e2");
	$min_time2 = $result->fetch_assoc()["WinningTime"];
	$min_time1_string = gmdate("H:i:s",$min_time1);
	$min_time2_string = gmdate("H:i:s",$min_time2);
	
	// Calculate the time limit based on percent back
	$limit1 = $min_time1*(1+$limit/100);
	$limit2 = $min_time2*(1+$limit/100);
	$limit1_string = gmdate("H:i:s",$limit1);
	$limit2_string = gmdate("H:i:s",$limit2);
					
					// btm - having some trouble with the following query losing connection to the database
					// going to try closing the connection and reopening it here
					$mysqli->close();
					$mysqli = OpenRaceResultsDatabase();

	// Do the linear regression on the common racers that are within the limit
	$q = "SELECT Racer.FirstName, Racer.LastName, r1.TimeInSec as \"Race 1 Time\", r2.TimeInSec as \"Race 2 Time\"
			FROM Racer, Result r1, Result r2
			WHERE r1.EventID=$e1 AND r2.EventID=$e2 AND r1.RacerID=r2.RacerID AND Racer.RacerID=r1.RacerID
			LIMIT 1000";
	
	// Find the results within the limit
	$result = $mysqli->query($q);
	if (!$result)
	{
		error_log("No common results in limit for $e1 and $e2");
	}
	$x_inc = array();
	$y_inc = array();
	while ($row = $result->fetch_array())
	{
		if (($row["Race 1 Time"] <= $limit1) AND ($row["Race 2 Time"] <= $limit2))
		{
			$x_inc[] = $row["Race 1 Time"];
			$y_inc[] = $row["Race 2 Time"];
		}
	}	
	require_once( 'php/jpgraph/src/jpgraph_utils.inc.php');
	$linreg = new LinearRegression($x_inc, $y_inc);
	$ab = $linreg->GetAB();
	$m = $ab[1];
	$b = $ab[0];
	
	// If a racer to predict is specified, find their time and do the prediction
	$pred = null;
	if ($rid != null)
	{
		// Find the racers time in the events
		$q = "SELECT TimeInSec FROM Result WHERE EventID=$e1 AND RacerID=$rid";
		$result = $mysqli->query($q);
		if ($result->num_rows == 1)
		{
			$row = $result->fetch_assoc();
			$FirstRaceTime = $row["TimeInSec"];
			$pred = $m*$FirstRaceTime + $b;
		}
	}
	
	//echo "M = $m<br>B = $b<br>prediction = $pred<br>";
	
	return array("m"=>$m, "b"=>$b, "prediction"=>$pred, "compared"=>count($x_inc));
}
?>
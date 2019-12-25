<?php
// The first page of uploading results
//
// Page 1 the user selects the event and the file containing the results

include '../php/raceresultsutilities.php';

$RacerFields = array("FirstName" => ["First Name", null],
				"LastName" => ["Last Name", null],
				"BirthYear" => ["Birth Year", null],
				"HomeTown" => ["Home Town", null],
				"Gender" => ["Gender", null],
				"HomeState" => ["Home State/Country", null]);
				
$ResultFields = array("TimeInSec" => ["Time", null],
					  "Bib" => ["Bib Number", null]);
					  
// how do we make a drop down that is a list of the fields we are able to select
// for the various columns of data?


// foreach ($array as $key => $value)
if (isset($_REQUEST["eid"]))
{
	$EventID = $_REQUEST["eid"];
}
?>

<html>
<body>

<form action="uploadgender2.php" method="post" enctype="multipart/form-data">
Event: <select name='id'>
	<?php
		$mysqli = OpenRaceResultsDatabase();

		$result = $mysqli->query('SELECT * FROM EventView');
		while ($row = $result->fetch_array())
		{
			$id = $row["EventID"];
			$name = $row["FullName"];
			echo "<option value=".$id." ";
			if (isset($EventID) AND $EventID==$id)
			{
				echo "selected=\"selected\"";
			}
			echo ">".$name."</option>";
		}
	?>
</select><br><br>
<input type="file" name="fileToUpload" id="fileToUpload"></br></br>
<input type="submit">
</form>

</body>
</html>

<?php

function buildfieldoptions($racerfields, $resultfields)
{
	$options = "";
	foreach ($racerfields as $racerkey => $racerfield)
	{
		$options .= "<option value=$racerkey>$racerfield[0]</option>";
	}
	foreach ($resultfields as $resultkey => $resultfield)
	{
		$options .= "<option values=$resultkey>$resultfield[0]</option>";
	}
	return $options;
}

?>
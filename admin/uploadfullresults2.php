<?php
// The first page of uploading results
//
// Page 2 the user identifies what columns contain what data

include '../php/raceresultsutilities.php';
include_once 'uploadresultsfields.php';

session_start();

$_SESSION["EventID"] = $_POST["id"];
					  
?>
					  
<html>
<body>
<form action="uploadfullresults3.php" method="post">

<?php
					  
// Be sure a file was uploaded
if (is_uploaded_file($_FILES['fileToUpload']['tmp_name']))
{
	//Import uploaded file to Database
	
	// First copy the file to a temp location since we will want it on the next
	// form
	$uniqname = uniqid('result_', TRUE);
	$uniqfile = "/home/btmullin/tmp/$uniqname.csv";
	move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uniqfile);
	$_SESSION["ResultFile"] = $uniqfile;

	// Open the file
	$handle = fopen($uniqfile, "r");

	if ($handle)
	{
		// Get the first few rows of the file into a table so we can pick
		// the fields appropriately
		$row = 0;
		echo "<table border=\"1\">";
		$field_count = 0;
		while (($row < 5) && (($data = fgetcsv($handle, 1000, ",")) !== FALSE))
		{
			echo "<tr>";
			$field_count = count($data);
			foreach ($data as $field)
			{
				echo "<td>$field</td>";
			}
			echo "</tr>";
			$row++;
		}
		echo "<tr>";
		for ($i = 0; $i < $field_count; $i++)
		{
			echo "<td>";
			echo "<select name='field$i'>";
			echo buildfieldoptions($RacerFields, $ResultFields, $CalculatedFields);
			echo "</td>";
		}
		echo "</tr>";
		echo "</table><br>";
	}
}
else
{
	echo "You must select a file<br>";
	var_dump($_FILES);
}

// foreach ($array as $key => $value)
?>
<input type="checkbox" name="headers" value"headers">Data Has Header Row<br><br>
<input type="submit">
</form>

</body>
</html>

<?php

function buildfieldoptions($racerfields, $resultfields, $calculatedfields)
{
	// First is the "no data" option
	$options = "<option value=\"Nothing\"></option>";
	foreach ($racerfields as $racerkey => $racerfield)
	{
		$options .= "<option value=$racerkey>$racerfield[0]</option>";
	}
	foreach ($resultfields as $resultkey => $resultfield)
	{
		$options .= "<option value=$resultkey>$resultfield[0]</option>";
	}
	foreach ($calculatedfields as $calculatedkey => $calculatedfield)
	{
		$options .= "<option value=$calculatedkey>$calculatedfield[0]</option>";
	}
	return $options;
}

?>
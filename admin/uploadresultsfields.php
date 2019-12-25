<?php
// The first page of uploading results
//
// Page 2 the user identifies what columns contain what data

$RacerFields = array("FirstName" => ["First Name", null],
				"LastName" => ["Last Name", null],
				"BirthYear" => ["Birth Year", null],
				"HomeTown" => ["Home Town", null],
				"Gender" => ["Gender", null],
				"HomeState" => ["Home State/Country", null]);
				
$ResultFields = array("TimeInSec" => ["Time In Seconds", null],
					  "Bib" => ["Bib Number", null],
					  "Overall" => ["Overall", null]);
					  
$CalculatedFields = array("Age" => ["Age", null],
						  "Time" => ["Time (h:m:s)", null]);
?>
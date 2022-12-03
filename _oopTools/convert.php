<?php
/*
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
*/
?>
<!DOCTYPE html>
<html class="no-js" lang="en" dir="ltr" prefix="og: http://ogp.me/ns#">
	<head>
		<title>CSV to SQL convertor</title>
        <link rel="stylesheet" type="text/css" href="/_oopTools/tools.css" />
	</head>
	<body>
        <nav>
            <ul>
                <li><a href="/_oopTools/classGenerator.php">Class Generator</a></li>
                <li><a href="/_oopTools/createBEFiles.php">Create Back End Files</a></li>
                <li><a href="/_oopTools/generateErrorVars.php">Generate Error Variables</a></li>
                <li><a href="/_oopTools/convert.php">Convert CSV to SQL Statements</a></li>
            </ul>
        </nav>
		<h1>CSV to SQL convertor</h1>

		<!-- Input form begin -->

		<form name="csv2sql" method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
			<fieldset>
			    <input type="hidden" name="ref" value="csv2sql" />
                <label for="delimiter">Delimiting character:</label><br />
				<input type="text" id="delimiter" name="delimiter" value="," size="1" /><br />
				<br />
                <label for="headings">First line is column headings:</label><br />
				<input type="checkbox" id="headings" name="headings" value="checked" /><br />
				<br />
                <label for="lower">Convert column names to lower case:</label><br />
				<input type="checkbox" id="lower" name="lower" value="checked" /><br />
				<br />
                <label for="convertspaces">Convert column name spaces to underscores:</label><br />
				<input type="checkbox" id="convertspaces" name="convertspaces" value="checked" /><br />
				<br />
                <label for="tofile">Write to file:</label><br />
				<input type="text" id="tofile" name="tofile" size="11" /><br />
				<br />
                <label for="table_name">Insertion table:</label><br />
				<input type="text" id="table_name" name="table_name" size="50" /><br />
				<br />
                <label for="fromfile">CSV file:</label><br />
				<input type="file" id="fromfile" name="fromfile" size="11" /><br />
				<br />
                <label for="csv_data">CSV data:</label><br />
				<textarea id="csv_data" name="csv_data" rows="20" cols="100"></textarea><br />
				<br />
				<input type="submit" value="     Convert to SQL query     " />
			</fieldset>
		</form>

		<!-- Input form end -->

<?php

// Set default delimiter
$delimiter = ",";

// Parse incoming information if above form was posted
$filtered_ref = filter_input(INPUT_POST, 'csv2sql', FILTER_UNSAFE_RAW);
if ($filtered_ref == "csv2sql") {

	echo "<h2>SQL Query Output:</h2>";

	// Get information from form & prepare it for parsing
	$delimiter = filter_input(INPUT_POST, 'delimiter', FILTER_UNSAFE_RAW);
	$table_name = filter_input(INPUT_POST, 'table_name', FILTER_UNSAFE_RAW);
	$filtered_fromfile = filter_input(INPUT_POST, 'fromfile', FILTER_UNSAFE_RAW);
	if (($filtered_fromfile === null) || ($filtered_fromfile === '')) :
		$csv_data = filter_input(INPUT_POST, 'csv_data', FILTER_UNSAFE_RAW);
	else :
		$csv_data = file_get_contents($filtered_fromfile);
	endif;
	$csv_array    = explode("\n", $csv_data);
	$column_names = explode($delimiter, $csv_array[0]);

	// Generate base query
	$base_query = "INSERT INTO $table_name";

	// Include column headings if required
	$filtered_headings = filter_input(INPUT_POST, 'headings', FILTER_UNSAFE_RAW);
	if ($filtered_headings === 'checked') :
		$base_query .= " (";
		$first      = true;
		foreach ($column_names as $column_name) :
			if ($first === false) :
				$base_query .= ", ";
			endif;
			$column_name = trim($column_name);
			$filtered_lower = filter_input(INPUT_POST, 'lower', FILTER_UNSAFE_RAW);
			if ($filtered_lower === 'checked') :
				$column_name = strtolower($column_name);
			endif;
			$filtered_convertspaces = filter_input(INPUT_POST, 'convertspaces', FILTER_UNSAFE_RAW);
			if ($filtered_convertspaces === 'checked') :
				$column_name = str_replace(' ', '_', $column_name);
			endif;
			$base_query .= "$column_name";
			$first = false;
		endforeach;
		$base_query .= ")";
	endif;
	$base_query .= " ";
	// Loop through all CSV data rows and generate separate
	// INSERT queries based on base_query + the row information
	$last_data_row = count($csv_array) - 1;
	$sql = '';
	for ($counter = 1; $counter < $last_data_row; $counter++) :
		$value_query = "VALUES ('";
		$first = true;
		$data_row = explode($delimiter, $csv_array[$counter]);
		$value_counter = 0;
		foreach ($data_row as $data_value) :
			if ($first === false) :
				$value_query .= "', '";
			endif;
			$data_value = trim($data_value);
			$value_query .= "$data_value";
			$first = false;
		endforeach;

		$value_query .= "')";

		// Combine generated queries to generate final query
		$query = $base_query .$value_query . ";";

		echo "$query <br />";

		// Append newest query to the string to be written to file
		$filtered_tofile = filter_input(INPUT_POST, 'tofile', FILTER_UNSAFE_RAW);
        if (($filtered_tofile !== null) && ($filtered_tofile !== '')) :
            $sql .= $query . "\n";
        endif;
	endfor;

	// Write to file if required
    if (!empty($filtered_tofile)) :
        $tofile = $filtered_tofile;
        if (($tofile !== null) && ($tofile !== '')) :
            $fh = fopen($tofile, 'w')
                or die("Unable to write to file " . $tofile . ".");
            fwrite($fh, $sql);
            fclose($fh);
            echo "<h2>SQL Query Output written to file.</h2>";
        endif;
    endif;
}

?>

		<p>
			<a href="http://validator.w3.org/check?uri=referer">
				<img src="http://www.w3.org/Icons/valid-xhtml11" alt="Valid XHTML 1.1" height="31" width="88" />
			</a>
		</p
	</body>
</html>

<?php
require_once '../config.php';
?>
<html lang="en">
<head>
	<title>Error Variables Generator</title>
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
	<h1>PHP Error Variable Generator</h1>
	<p>Enter the path for the formComponents Form Fields file.</p>

	<?php
	$errVariables = "<?php\r\n";
	$errVariables .= '$intErrs = \'\';'."\r\n";

	$filtered_submit = filter_input(INPUT_POST, 'submit', FILTER_UNSAFE_RAW);
	if (!empty($filtered_submit) && $filtered_submit == 'Generate Variables') {
		$dir = filter_input(INPUT_POST, 'theDir', FILTER_UNSAFE_RAW);
		$fileName = filter_input(INPUT_POST, 'theFile', FILTER_UNSAFE_RAW);
		$theFile = $dir.'/_formComponents/'.$fileName.'.php';
		if (file_exists($theFile)) {
			$fileArray = file($theFile);
				// Check content of file:
			$nbrRows = count($fileArray);
			for($i=0; $i<$nbrRows; $i++) {
				if (strpos($fileArray[$i], '$strErr')) {
					$thisErrVar = trim($fileArray[$i]);
					$thisErrVar = str_replace("'errorString'=>", "", $thisErrVar);
					$thisErrVar = str_replace(',', '', $thisErrVar);
					$thisErrVar = str_replace("\t", '', $thisErrVar);
					$errVariables .= $thisErrVar." = '';\r\n";
				}
			}
		} else {
			$errVariables = 'File does not exist.';
		}
	}
	$directories = [_e($_ENV['ROOTDIR']), $_ENV['ADMINDIR']];
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<label for="theDir">Directory:</label>
		<select name="theDir" id="theDir">
			<?php
			$dirCount = count($directories);
			for ($i=0; $i<$dirCount; $i++) {
				echo '<option value="'.$directories[$i].'">'.$directories[$i].'</option>';
			}
			?>
		</select>
		<label for="theFile">File Name:</label>
		<input type="text" name="theFile" id="theFile" value="" />
		<input type="submit" name="submit" value="Generate Variables" class="btn btn-primary btn-md" />
	</form>
	<?php
	if ($errVariables != '') {
		echo '<p>Here are the variables to create</p>'.PHP_EOL;
		echo '<textarea>';
			echo $errVariables;
		echo '</textarea>';
	}
	?>
	<script type="text/javascript">
		window.onload = function() {
			document.getElementById("theDir").focus();
		};
	</script>
</body>
</html>

<?php
require_once '../config.php';
function fauxTabs($tabstops) {
    $spaces = $tabstops * 4;
    $stop = str_repeat(' ', $spaces);
    return $stop;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$filtered_submit = filter_input(INPUT_POST, 'submit', FILTER_UNSAFE_RAW);
if ($filtered_submit != '' && $filtered_submit != Null && $filtered_submit == 'Generate Class') :
        // fill parameters from form
    $table = filter_input(INPUT_POST, 'tablename', FILTER_UNSAFE_RAW);
    $class = filter_input(INPUT_POST, 'classname', FILTER_UNSAFE_RAW);
    $key = filter_input(INPUT_POST, 'keyname', FILTER_UNSAFE_RAW);
    $varType = filter_input(INPUT_POST, 'varType', FILTER_UNSAFE_RAW);
    $xTerm = filter_input(INPUT_POST, 'xTerm', FILTER_UNSAFE_RAW);
    $hasGUID = filter_input(INPUT_POST, 'hasGUID', FILTER_SANITIZE_NUMBER_INT);
    $guidVar = filter_input(INPUT_POST, 'guidVar', FILTER_UNSAFE_RAW);

    $columns = DB::columnList($table);
    $table_structure = DB::query("DESC " . $table);

    $dir = dirname(__FILE__);
    $dir = $_SERVER['DOCUMENT_ROOT'];
    $filename = strtolower($class) . '.php';

    // if file exists, then delete it
    if (file_exists($dir . '/lib/' . $filename)) :
        unlink($dir . '/lib/' . $filename);
    endif;
    // open file in insert mode
    $file = fopen($dir . '/lib/' . $filename, 'w+');
    $file_date = date("m/d/Y");

    $c = '';

    $c = '<?php' . PHP_EOL;
    $c .= '/* *****************************************************';
    $c .= '**' . PHP_EOL;
    $c .= '* CLASSNAME:        ' . $class . PHP_EOL;
    $c .= '* GENERATION DATE:  ' . $file_date . PHP_EOL;
    $c .= '* CLASS FILE:       ' . $filename . PHP_EOL;
    $c .= '* FOR MYSQL TABLE:  ' . $table . PHP_EOL;
    $c .= '******************************************************* ';
    $c .= '*/' . PHP_EOL . PHP_EOL;
    $c .= 'class ' . $class . PHP_EOL;
    $c .= '{' . PHP_EOL;
        $c .= fauxTabs(1) . '// Properties' . PHP_EOL;
        $c .= fauxTabs(1) . '// Define the variables' . PHP_EOL;
        $c .= fauxTabs(1) . '// Primary key with auto increment' . PHP_EOL;
        $c .= fauxTabs(1) . $varType . ' $' . $key . ' = \'\';' . PHP_EOL;
        foreach ($table_structure AS $ts) {
            if ($ts['Field'] != $key) :
                $c .= fauxTabs(1) . $varType . ' $' . $ts['Field'] . ' = ';
                switch ($ts['Type']) :
                    case 'datetime' :
                        $c .= '\'\';' . PHP_EOL;
                        break;
                    default :
                        switch($ts['Null']) :
                            case 'YES' :
                                if ($ts['Default'] == Null) :
                                    $c .= 'Null;'  . PHP_EOL;
                                else :
                                    $c .= $ts['Default'] . ';' . PHP_EOL;
                                endif;
                                break;
                            case 'NO' :
                                if ($ts['Default'] != Null) :
                                    $c .= $ts['Default'] . ';' . PHP_EOL;
                                else :
                                    $c .= '\'\';' . PHP_EOL;
                                endif;
                                break;
                        endswitch;
                endswitch;
            endif;
        }
        $c .= fauxTabs(1) . 'private $data = [];' . PHP_EOL;

        $c .= PHP_EOL . fauxTabs(1) . '// Methods' .PHP_EOL;
            // CONSTRUCTOR
        $c .= fauxTabs(2) . '// Define the constructor' . PHP_EOL;
        $c .= fauxTabs(1) . 'public function __construct($id=Null) ' . PHP_EOL;
        $c .= fauxTabs(1) . '{' . PHP_EOL;
            $c .= fauxTabs(2) . 'if (isset($id) && $id !== 0) :' . PHP_EOL;
                $c .= fauxTabs(3) . '$this->read($id);' . PHP_EOL;
            $c .= fauxTabs(2) . 'endif;' . PHP_EOL;
        $c .= fauxTabs(1).'}' . PHP_EOL;

        if ($varType != 'public') {
                // GETTER
            $c .= PHP_EOL;
            $c .= fauxTabs(2) . '// Create the getter methods' . PHP_EOL;
            foreach ($columns AS $name => $details) {
                $c .= fauxTabs(1) . 'function get' . $name . '() { ' . PHP_EOL;
                    $c .= 'return $this->' . $name.';' . PHP_EOL;
                $c .= '}' . PHP_EOL;
            }

                // SETTER
            $c .= '// Create the setter methods' . PHP_EOL;
            $c .= PHP_EOL.fauxTabs(2) . PHP_EOL;
            foreach ($columns AS $name => $details) :
                $c .= fauxTabs(1) . 'function set' . $name . '($val) {';
                $c .= PHP_EOL;
                    $c .= '$this->' . $name . ' = $val' . PHP_EOL;
                $c .= ' }' . PHP_EOL;
            endforeach;
        }

            // Return one row
        $c .= PHP_EOL;
        $c .= fauxTabs(2) . '// SELECT the record from the table and load ';
        $c .= 'it to the variables' . PHP_EOL;
        $c .= fauxTabs(1) . 'public function read($id) {' . PHP_EOL;
            if ($hasGUID == 1) :
                $c .= fauxTabs(2) . 'if ($this->isValidGUID($id)) :' . PHP_EOL;
                    $c .= fauxTabs(3) . '$rawData = DB::queryFirstRow("SELECT * FROM ' . $table . ' WHERE ' . $guidVar . ' = %s", $id);' . PHP_EOL;
                $c .= fauxTabs(2) . 'else :' . PHP_EOL;
                    $c .= fauxTabs(3) . '$rawData = DB::queryFirstRow("SELECT * FROM ' . $table . ' WHERE ' . $key . ' = %i", $id);' . PHP_EOL;
                $c .= fauxTabs(2) . 'endif;' . PHP_EOL;
            else :
                $c .= fauxTabs(2) . '$rawData = DB::queryFirstRow("SELECT * FROM ' . $table . ' WHERE ' . $key . ' = %i", $id);' . PHP_EOL;
            endif;
            $c .= fauxTabs(2) . 'if (DB::count() == 1) :' . PHP_EOL;
                $c .= fauxTabs(3) . '$result = stripSlashesDeep($rawData);' . PHP_EOL;
                $c .= fauxTabs(3) . 'foreach($result AS $key=>$value) :' . PHP_EOL;
                    $c .= fauxTabs(4) . 'if (!is_null($value)) :' . PHP_EOL;
                        $c .= fauxTabs(5) . '$this->$key = $value;' . PHP_EOL;
                    $c .= fauxTabs(4) . 'elseif (is_null($value)) :' . PHP_EOL;
                        $c .= fauxTabs(5) . '$this->$key = Null;' . PHP_EOL;
                    $c .= fauxTabs(4) . 'else :' . PHP_EOL;
                        $c .= fauxTabs(5) . '$this->$key = \'\';' . PHP_EOL;
                    $c .= fauxTabs(4) . 'endif;' . PHP_EOL;
                $c .= fauxTabs(3) . 'endforeach;' . PHP_EOL;
                $c .= fauxTabs(3) . 'unset($result);' . PHP_EOL;
            $c .= fauxTabs(2) . 'endif;' . PHP_EOL;
            $c .= fauxTabs(2) . 'unset($rawData);' . PHP_EOL;
        $c .= fauxTabs(1).'}' . PHP_EOL;

            // delete record from the table
        $c .= PHP_EOL;
        $c .= fauxTabs(2) . '// DELETE the record from the table' . PHP_EOL;
        $c .= fauxTabs(1) . 'public function delete() ' . PHP_EOL;
        $c .= fauxTabs(1) . '{' . PHP_EOL;
            $c .= fauxTabs(2) . 'DB::delete(\'' . $table . '\',';
                $c .= '"' . $key . ' = %i", $this->' . $key;
            $c .= ');' . PHP_EOL;
        $c .= fauxTabs(1).'}' . PHP_EOL;

            // insert record to the table
        $c .= PHP_EOL;
        $c .= fauxTabs(2) . '// INSERT a record into the table' . PHP_EOL;
        $c .= fauxTabs(1) . 'public function create()' . PHP_EOL;
        $c .= fauxTabs(1) . '{' . PHP_EOL;
            $c .= fauxTabs(2) . '$this->buildData();' . PHP_EOL;
            $c .= fauxTabs(2) . 'DB::insert(\'' . $table . '\', $this->data);' . PHP_EOL;
            $c .= fauxTabs(2) . '$this->'.$key.' = DB::insertID();' . PHP_EOL;
            $c .= fauxTabs(2) . '$this->clearData();' . PHP_EOL;
        $c .= fauxTabs(1) . '}' . PHP_EOL;

            // update a record in the table
        $c .= PHP_EOL;
        $c .= fauxTabs(2) . '// UPDATE the record in the table' . PHP_EOL;
        $c .= fauxTabs(1) . 'public function update()' . PHP_EOL;
        $c .= fauxTabs(1) . '{' . PHP_EOL;
            $c .= fauxTabs(2) . '$this->buildData();' . PHP_EOL;
            $c .= fauxTabs(2);
            $c .= 'DB::update(\'' . $table . '\', $this->data,"' . $key . ' = %i", $this->' . $key . ');';
            $c .= PHP_EOL;
            $c .= fauxTabs(2) . '$this->clearData();' . PHP_EOL;
        $c .= fauxTabs(1) . '}' . PHP_EOL;

            // Build the data array for the update and insert functions
        $c .= PHP_EOL;
        $c .= fauxTabs(2) . '// Build the data array for the update and insert functions' . PHP_EOL;
        $c .= fauxTabs(1) . 'private function buildData()' . PHP_EOL;
        $c .= fauxTabs(1) . '{' . PHP_EOL;
            foreach ($columns AS $name => $details) {
                if ($name != $key) :
                    $c .= fauxTabs(2) . '$this->data[\'' . $name . '\'] = $this->' . $name . ';';
                    $c .= PHP_EOL;
                endif;
            }
        $c .= fauxTabs(1) . '}' . PHP_EOL;

            // Build the function to clear the data array
        $c .= PHP_EOL;
        $c .= fauxTabs(1) . 'private function clearData()' . PHP_EOL;
        $c .= fauxTabs(1) . '{' . PHP_EOL;
            $c .= fauxTabs(2) . '$this->data = array_diff($this->data, $this->data);' . PHP_EOL;
        $c .= fauxTabs(1) . '}' . PHP_EOL;

            // Output the status of the Insert, Update or Delete functions
        $c .= PHP_EOL;
        $c .= fauxTabs(2) . '// Output the status of the Insert, Update or Delete functions';
        $c .= PHP_EOL;
        $c .= fauxTabs(1) . 'public function crudStatus($status)' . PHP_EOL;
        $c .= fauxTabs(1) . '{' . PHP_EOL;
            $c .= fauxTabs(2) . 'if ($status == \'n\') :' . PHP_EOL;
                $c .= fauxTabs(3) . '?>' . PHP_EOL;
                $c .= fauxTabs(3) . '<div class="alert alert-success">' . PHP_EOL;
                    $c .= fauxTabs(4) . '<p>';
                        $c .= 'This ' . $xTerm . ' has been created. You can edit it below.</p>' . PHP_EOL;
                $c .= fauxTabs(3) . '</div>' . PHP_EOL;
                $c .= fauxTabs(3) . '<?php' . PHP_EOL;
            $c .= fauxTabs(2) . 'elseif ($status == \'u\') :' . PHP_EOL;
                $c .= fauxTabs(3) . '?>' . PHP_EOL;
                $c .= fauxTabs(3) . '<div class="alert alert-success">';
                $c .= PHP_EOL;
                    $c .= fauxTabs(4) . '<p>This ' . $xTerm . ' has been ';
                    $c .= 'updated.</p>' . PHP_EOL;
                $c .= fauxTabs(3) . '</div>' . PHP_EOL;
                $c .= fauxTabs(3) . '<?php' . PHP_EOL;
            $c .= fauxTabs(2) . 'else :' . PHP_EOL;
                $c .= fauxTabs(3) . '?>' . PHP_EOL;
                $c .= fauxTabs(3) . '<div class="alert alert-info">' . PHP_EOL;
                    $c .= fauxTabs(4) . '<p>This '.$xTerm.' has been reset.';
                    $c .= '</p>' . PHP_EOL;
                $c .= fauxTabs(3) . '</div>' . PHP_EOL;
                $c .= fauxTabs(3) . '<?php' . PHP_EOL;
            $c .= fauxTabs(2) . 'endif;' . PHP_EOL;
        $c .= fauxTabs(1) . '}' . PHP_EOL;

        $c .= PHP_EOL . fauxTabs(2) . '// Check if the string passed in is a valid GUID' . PHP_EOL;
        $c .= fauxTabs(1) . 'private function isValidGuid($testGUID) {' . PHP_EOL;
            $c .= fauxTabs(2) . 'return !empty($testGUID) && preg_match(\'/^\{?[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}\}?$/\', $testGUID);' . PHP_EOL;
        $c .= fauxTabs(1) . '}' . PHP_EOL;
    $c.= '}' . PHP_EOL;
    $c .= PHP_EOL;
    fwrite($file, $c);
    fclose($file);
        ?>
        <p><?php echo $class; ?> was successfully created.</p>
        <p><a href="classGenerator.php">Create a new class.</a></p>
        <?php
endif;
?>
<html lang="en">
<head>
	<title>Class Generator</title>
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
	<div id="wrapper">
		<h1>PHP MYSQL Class Generator</h1>
		<h2>This will create a class in the /lib folder.</h2>
		<h3>This folder must have permissions set to 777.</h3>
        <?php
		$tablelist = DB::tableList();
		$varTypes = ['public', 'protected', 'private'];
		?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<label for="tablename">Select Table</label>
			<select name="tablename" id="tablename" required>
				<?php
				$tableCount = count($tablelist);
				for ($i=0; $i<$tableCount; $i++) :
					?>
		  			<option value="<?php echo $tablelist[$i]; ?>">
						<?php echo $tablelist[$i]; ?>
					</option>
					<?php
				endfor;
				?>
			</select>

			<label for="classname">Class Name</label>
			<input type="text" name="classname" id="classname" value="" required />

			<label for="keyname">Primary Field Name</label>
			<input type="text" name="keyname" id="keyname" value="" required />

			<label for="varType">Declare Variables as:</label>
			<select name="varType" id="varType" required>
				<?php
				$varTypeCount = count($varTypes);
				for ($i=0; $i<$varTypeCount; $i++) :
					?>
		  			<option value="<?php echo $varTypes[$i]; ?>">
						<?php echo $varTypes[$i] . PHP_EOL; ?>
					</option>
					<?php
				endfor;
				?>
			</select>

			<label for="xTerm">CRUD Term</label>
			<input type="text" name="xTerm" id="xTerm" value="" required />

            <input type="checkbox" name="hasGUID" id="hasGUID" value="1" />
            <label class="ckbox" for="hasGUID">Has GUID?</label>

            <label for="guidVar">GUID Variable Name</label>
            <input type="text" name="guidVar" id="guidVar" value="" />

            <input type="submit" name="submit" value="Generate Class" class="btn btn-primary btn-md" />
        </form>
	</div>
	<script type="text/javascript">
	window.onload = function() {
		document.getElementById("tablename").focus();
	};
	</script>
</body>
</html>

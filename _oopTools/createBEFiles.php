<?php
include $_SERVER['DOCUMENT_ROOT'].'/config.php';

/**
 * Change
 * xID to the appropriate variable name
 * objVar to the variable used to represent the object
 * xClass with the class name
 * xTerm with the correct Delete from text
 * xTable with the correct table name
 * xOptionData & primaryKeyField with the primary key for the table
 * xDisplayData with the item to be deleted
 * xSortField field name in DB that controls sorting
 * xParentID with Parent Menu ID
 * xMenuText with the display text for the menu
 * xCategoryDir directory name for parent menu
 * $xSubdirectory - sub directory under xCategoryDir
 * $xSidebar - Name of the xCategoryDir menu
 * $xSortID - Position of new menu item on the sidebar menu
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ini_set('max_execution_time', 3600);

function doReplacements($line) {
	$line = str_replace('xID', $_POST['xID'], $line);
	$line = str_replace('objVar', $_POST['objVar'], $line);
	$line = str_replace('xClass', $_POST['xClass'], $line);
	$line = str_replace('xTerm', $_POST['xTerm'], $line);
	$line = str_replace('xTable', $_POST['whichTable'], $line);
	$line = str_replace('tablename', $_POST['whichTable'], $line);
	$line = str_replace('xOptionData', $_POST['xOptionData'], $line);
	$line = str_replace('primaryKeyField', $_POST['xOptionData'], $line);
	$line = str_replace('xDisplayData', $_POST['xDisplayData'], $line);
	$line = str_replace('xSortField', $_POST['xSortField'], $line);
	$line = str_replace('xDir', $_POST['filenamePrefix'], $line);
	$line = str_replace('xSidebar', $_POST['xSidebar'], $line);
	$line = str_replace('xSubdirectory', $_POST['xSubdirectory'], $line);
	$line = str_replace('xCategoryDir', $_POST['xCategoryDir'], $line);
	return $line;
}

$filenamePrefix = '';
$whichTable = '';
$xID = '';
$objVar = '';
$xClass = '';
$xTerm = '';
$xOptionData = '';
$primaryKeyField = '';
$xDisplayData = '';
$xSortField = '';
$xMenuText = '';
$xParentID = '';
$xCategoryDir = '';
$xSubdirectory = '';
$xSidebar = '';
$xSortID = '';

$formComponentsPath = $_SERVER['DOCUMENT_ROOT'] . '/Admin/_formComponents/';
?>
<html lang="en">
<head>
	<title>File Builder</title>
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
    <h1>PHP Admin File Generator</h1>

    <?php

    $rs = DB::queryFirstRow("SELECT menuID FROM menu_structure ORDER BY menuID DESC");
    $nextID = intval($rs['menuID']) + 1;
    DB::query("ALTER TABLE menu_structure AUTO_INCREMENT = $nextID");
    unset($rs);

    $fSubmit = filter_input(INPUT_POST, 'genFiles', FILTER_UNSAFE_RAW);
    if ($fSubmit != '' && $fSubmit != Null && $fSubmit == 'Generate Files') :
        $filenamePrefix = $xDir = filter_input(INPUT_POST, 'filenamePrefix', FILTER_UNSAFE_RAW);
        $whichTable = filter_input(INPUT_POST, 'whichTable', FILTER_UNSAFE_RAW);
        $xID = filter_input(INPUT_POST, 'xID', FILTER_UNSAFE_RAW);
        $objVar = filter_input(INPUT_POST, 'objVar', FILTER_UNSAFE_RAW);
        $xClass = filter_input(INPUT_POST, 'xClass', FILTER_UNSAFE_RAW);
        $xTerm = filter_input(INPUT_POST, 'xTerm', FILTER_UNSAFE_RAW);
        $xSortField = filter_input(INPUT_POST, 'xSortField', FILTER_UNSAFE_RAW);
        $xOptionData = filter_input(INPUT_POST, 'xOptionData', FILTER_UNSAFE_RAW);
        $xDisplayData = filter_input(INPUT_POST, 'xDisplayData', FILTER_UNSAFE_RAW);
        $xMenuText = filter_input(INPUT_POST, 'xMenuText', FILTER_UNSAFE_RAW);
        $xParentID = filter_input(INPUT_POST, 'xParentID', FILTER_SANITIZE_NUMBER_INT);
        $xCategoryDir = filter_input(INPUT_POST, 'xCategoryDir', FILTER_UNSAFE_RAW);
        $xSubdirectory = filter_input(INPUT_POST, 'xSubdirectory', FILTER_UNSAFE_RAW);
        $xSidebar = filter_input(INPUT_POST, 'xSidebar', FILTER_UNSAFE_RAW);
        $xSortID  = filter_input(INPUT_POST, 'xSortID', FILTER_UNSAFE_RAW);

        $adminPath = $_SERVER['DOCUMENT_ROOT'] . '/Admin/';
        $categoryPath = $adminPath . $xCategoryDir.'/';
        $subdirectoryPath = $categoryPath . $xSubdirectory.'/';
        $editPath = $subdirectoryPath . 'edit/';
        $newPath = $subdirectoryPath . 'new/';
        $deletePath = $subdirectoryPath . 'delete/';
        $formComponentsPath = $adminPath . '_formComponents/';
        $templateLevel1 = $_SERVER['DOCUMENT_ROOT'] . '/_oopTools/crud-templates/';
        $templateLevel2 = $templateLevel1 . 'subdirectory/';

        if (!file_exists($categoryPath)) :
            mkdir($categoryPath, 0777, true);
        endif;
        if (!file_exists($subdirectoryPath)) :
            mkdir($subdirectoryPath, 0777, true);
        endif;
        if (!file_exists($editPath)) :
            mkdir($editPath, 0777, true);
        endif;
        if (!file_exists($newPath)) :
            mkdir($newPath, 0777, true);
        endif;
        if (!file_exists($deletePath)) :
            mkdir($deletePath, 0777, true);
        endif;

        $categoryURL = 'Admin/'.$xCategoryDir.'/';
        $subdirectoryURL = $categoryURL.$xSubdirectory;
        $editURL = $subdirectoryURL.'/edit'.'/';
        $newURL = $subdirectoryURL.'/new'.'/';
        $deleteURL = $subdirectoryURL.'/delete'.'/';

        $fn = filter_input(INPUT_POST, 'filenamePrefix', FILTER_UNSAFE_RAW);

            //['ft'=>'root', 'fn'=>'index.php', 'path'=>$categoryPath],
        $filesToCreate = [
            ['ft'=>'index', 'fn'=>'index.php', 'path'=>$subdirectoryPath],
            ['ft'=>'new', 'fn'=>'index.php', 'path'=>$newPath],
            ['ft'=>'edit', 'fn'=>'index.php', 'path'=>$editPath],
            ['ft'=>'delete', 'fn'=>'index.php', 'path'=>$deletePath],
            ['ft'=>'variables', 'fn'=>$fn.'-variables.php', 'path'=>$formComponentsPath],
            ['ft'=>'form', 'fn'=>$fn.'-form.php', 'path'=>$formComponentsPath],
            ['ft'=>'validation', 'fn'=>$fn.'-tgr_rubric_k_2_image1_additional_questions_validation.php', 'path'=>$formComponentsPath]
        ];

        foreach ($filesToCreate AS $thisFile) :
            switch ($thisFile['ft']) :
                case 'variables':
                    $c = '';
                    $fullPath = $thisFile['path'].$thisFile['fn'];
                    echo $fullPath.'<br />';
                    $file = fopen($fullPath, 'w+');
                    $fWhichTable = filter_input(
                                        INPUT_POST,
                                        'whichTable',
                                        FILTER_UNSAFE_RAW
                                    );
                    $columns = DB::columnList($fWhichTable);
                    $c = '<?php'.PHP_EOL;
                    $c .= '$intErrs = 0;'.PHP_EOL;
                    $c .= '$strErr = \'\';'.PHP_EOL;
                    foreach ($columns AS $column) :
                        $c .= '$'.$column.' = $strErr_'.$column.' = \'\';'.PHP_EOL;
                    endforeach;
                    fwrite($file, $c);
                    fclose($file);
                    chmod($fullPath, 0777);
                break;
                case 'validation':
                case 'form':
                    $c = '';
                    $fullPath = $thisFile['path'].$thisFile['fn'];
                    echo $fullPath.'<br />';
                    if (file_exists($fullPath)) :
                        unlink($fullPath);
                    endif;
                    $file = fopen($fullPath, 'w+');
                    $c = '<?php'.PHP_EOL;
                    fwrite($file, $c);
                    fclose($file);
                    chmod($fullPath, 0777);
                break;
                case 'new':
                    $c = '';
                    $tname = $templateLevel2.$thisFile['ft'].'/'.$thisFile['fn'];
                    $thisTemplate = fopen($tname, 'r');
                    $fullPath = $thisFile['path'].$thisFile['fn'];
                    if (file_exists($fullPath)) :
                        unlink($fullPath);
                    endif;
                    $file = fopen($fullPath, 'w+');
                    while(!feof($thisTemplate)) :
                        $thisLine = fgets($thisTemplate);
                        $c .= doReplacements($thisLine);
                        unset($thisLine);
                    endwhile;
                    fclose($thisTemplate);
                    fwrite($file, $c);
                    fclose($file);
                    chmod($fullPath, 0777);
                    unset($c);
                    if (file_exists($fullPath)) :
                        echo $fullPath.'<br />';
                    endif;
                    $data['menu_text'] = 'New';
                    $data['menuName'] = '';
                    $data['menulevel'] = 3;
                    $data['url'] = $newURL;
                    $data['parent_menu_id'] = $xParentID;
                    $data['audience'] = 'private';
                    $data['hiddenPage'] = 0;
                    $data['adminPage'] = 1;
                    $data['active'] = 1;
                    $data['sort_order'] = 1;
                    DB::insert('menu_structure', $data);
                    unset($data);
                break;
                case 'edit':
                    $c = '';
                    $tname = $templateLevel2.$thisFile['ft'].'/'.$thisFile['fn'];
                    $thisTemplate = fopen($tname, 'r');
                    $fullPath = $thisFile['path'].$thisFile['fn'];
                    if (file_exists($fullPath)) :
                        unlink($fullPath);
                    endif;
                    $file = fopen($fullPath, 'w+');
                    while(!feof($thisTemplate)) :
                        $thisLine = fgets($thisTemplate);
                        $c .= doReplacements($thisLine);
                        unset($thisLine);
                    endwhile;
                    fclose($thisTemplate);
                    fwrite($file, $c);
                    fclose($file);
                    chmod($fullPath, 0777);
                    unset($c);
                    if (file_exists($fullPath)) :
                        echo $fullPath.'<br />';
                    endif;
                    $data['menu_text'] = 'Edit';
                    $data['menuName'] = '';
                    $data['menulevel'] = 3;
                    $data['url'] = $editURL;
                    $data['parent_menu_id'] = $xParentID;
                    $data['audience'] = 'private';
                    $data['hiddenPage'] = 0;
                    $data['adminPage'] = 1;
                    $data['active'] = 1;
                    $data['sort_order'] = 2;
                    DB::insert('menu_structure', $data);
                    $xParentID = DB::insertId();
                    unset($data);
                break;
                case 'delete':
                    $c = '';
                    $tname = $templateLevel2.$thisFile['ft'].'/'.$thisFile['fn'];
                    $thisTemplate = fopen($tname, 'r');
                    $fullPath = $thisFile['path'].$thisFile['fn'];
                    if (file_exists($fullPath)) :
                        unlink($fullPath);
                    endif;
                    $file = fopen($fullPath, 'w+');
                    while(!feof($thisTemplate)) :
                        $thisLine = fgets($thisTemplate);
                        $c .= doReplacements($thisLine);
                        unset($thisLine);
                    endwhile;
                    fclose($thisTemplate);
                    fwrite($file, $c);
                    fclose($file);
                    chmod($fullPath, 0777);
                    unset($c);
                    if (file_exists($fullPath)) :
                        echo $fullPath.'<br />';
                    endif;
                    $data['menu_text'] = 'Delete';
                    $data['menuName'] = '';
                    $data['menulevel'] = 3;
                    $data['url'] = $deleteURL;
                    $data['parent_menu_id'] = $xParentID;
                    $data['audience'] = 'private';
                    $data['hiddenPage'] = 0;
                    $data['adminPage'] = 1;
                    $data['active'] = 1;
                    $data['sort_order'] = 3;
                    DB::insert('menu_structure', $data);
                    unset($data);
                break;
                case 'index':
                    $c = '';
                    $thisTemplate = fopen($templateLevel2.$thisFile['fn'], 'r');
                    $fullPath = $thisFile['path'].$thisFile['fn'];
                    if (file_exists($fullPath)) :
                        unlink($fullPath);
                    endif;
                    $file = fopen($fullPath, 'w+');
                    while(!feof($thisTemplate)) :
                        $thisLine = fgets($thisTemplate);
                        $c .= doReplacements($thisLine);
                        unset($thisLine);
                    endwhile;
                    fclose($thisTemplate);
                    fwrite($file, $c);
                    fclose($file);
                    chmod($fullPath, 0777);
                    unset($c);
                    if (file_exists($fullPath)) :
                        echo $fullPath.'<br />';
                    endif;
                    $data['menu_text'] = $xMenuText;
                    $data['menuName'] = '';
                    $data['menulevel'] = 2;
                    $data['url'] = $subdirectoryURL;
                    $data['parent_menu_id'] = $xParentID;
                    $data['audience'] = 'private';
                    $data['hiddenPage'] = 0;
                    $data['adminPage'] = 1;
                    $data['active'] = 1;
                    $data['sort_order'] = $xSortID;
                    DB::insert('menu_structure', $data);
                    $xParentID = DB::insertId();
                    unset($rs, $data);
                break;
                case 'root':
                    $c = '';
                    $thisTemplate = fopen($templateLevel1.$thisFile['fn'], 'r');
                    $fullPath = $thisFile['path'].$thisFile['fn'];
                    if (file_exists($fullPath)) :
                        unlink($fullPath);
                    endif;
                    $file = fopen($fullPath, 'w+');
                    while(!feof($thisTemplate)) :
                        $thisLine = fgets($thisTemplate);
                        $c .= doReplacements($thisLine);
                        unset($thisLine);
                    endwhile;
                    fclose($thisTemplate);
                    fwrite($file, $c);
                    fclose($file);
                    chmod($fullPath, 0777);
                    unset($c);
                    if (file_exists($fullPath)) :
                        echo $fullPath.'<br />';
                    endif;
                break;
            endswitch;
        endforeach;
        ?>
        <p>The files listed above were successfully created.</p>
        <?php
    endif;
    $directories = [$_SERVER['DOCUMENT_ROOT'], $_SERVER['DOCUMENT_ROOT'] . '/Admin/'];
    $tableList = DB::tableList();
    ?>
    <form action="<?php self(1); ?>" method="POST" id="classGenerator" class="classGenerator">

        <label for="whichTable">Table: <span class="required">*</span></label>
        <select name="whichTable" id="whichTable" required class="selectbox">
            <?php
            foreach ($tableList as $table) :
                if ($whichTable == $table) :
                    $ckVal = ' checked';
                else :
                    $ckVal = '';
                endif;
                ?>
                <option value="<?php echo $table; ?>"<?php echo $ckVal; ?>><?php echo $table; ?></option>
                <?php
            endforeach;
            ?>
        </select>

        <label for="filenamePrefix">
            File Name Prefix (for component files): <span class="required">*</span>
        </label>
        <input type="text" name="filenamePrefix" id="filenamePrefix" value="<?php echo $filenamePrefix; ?>" required />

        <label for="xID">
            Query String ID (eg: aid, eid): <span class="required">*</span>
        </label>
        <input type="text" name="xID" id="xID" value="<?php echo $xID; ?>" required />

        <label for="xID">
            Sort order: <span class="required">*</span>
        </label>
        <input type="text" name="xSortID" id="xSortID" value="<?php echo $xSortID; ?>" required />


        <label for="xClass">Class Name: <span class="required">*</span></label>
        <input type="text" name="xClass" id="xClass" value="<?php echo $xClass; ?>" required />

        <label for="objVar">
            Object Variable Name (eg: similar to class name):
            <span class="required">*</span>
        </label>
        <input type="text" name="objVar" id="objVar" value="<?php echo $objVar; ?>" required />

        <label for="xTerm">Term verbiage: <span class="required">*</span></label>
        <input type="text" name="xTerm" id="xTerm" value="<?php echo $xTerm; ?>" required />

        <label for="xOptionData">
            Primary key field name: <span class="required">*</span>
        </label>
        <input type="text" name="xOptionData" id="xOptionData" value="<?php echo $xOptionData; ?>" required />

        <label for="xDisplayData">
            Display data field name from DB: <span class="required">*</span>
        </label>
        <input type="text" name="xDisplayData" id="xDisplayData" value="<?php echo $xDisplayData; ?>" required />

        <label for="xSortField">
            Sort Order field name from DB: <span class="required">*</span>
        </label>
        <input type="text" name="xSortField" id="xSortField" value="<?php echo $xSortField; ?>" required />

        <label for="xParentID">
            Parent Page ID: <span class="required">*</span>
        </label>
        <input type="text" name="xParentID" id="xParentID" value="<?php echo $xParentID; ?>" required />

        <label for="xMenuText">
            Sidebar Menu text: <span class="required">*</span>
        </label>
        <input type="text" name="xMenuText" id="xMenuText" value="<?php echo $xMenuText; ?>" required />

        <label for="xCategoryDir">
            Admin Sub-Directory: <span class="required">*</span>
        </label>
        <input type="text" name="xCategoryDir" id="xCategoryDir" value="<?php echo $xCategoryDir; ?>" required />

        <label for="xSidebar">
            Sidebar name to load: <span class="required">*</span
        ></label>
        <input type="text" name="xSidebar" id="xSidebar" value="<?php echo $xSidebar; ?>" required />

        <label for="xSubdirectory">
            Secondary Sub-Directory: <span class="required">*</span>
        </label>
        <input type="text" name="xSubdirectory" id="xSubdirectory" value="<?php echo $xSubdirectory; ?>" required />

        <input type="submit" id="genFiles" name="genFiles" value="Generate Files" />
    </form>
</div>
</body>
</html>

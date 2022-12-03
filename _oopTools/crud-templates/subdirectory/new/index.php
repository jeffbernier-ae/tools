<?php
/** @noinspection ALL */
/* Include the configuration file from the root directory */
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
$thisFile = setThisFile($_SERVER['REQUEST_URI']);
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/_formComponents/xDir-variables.php';
$sidebarInclude = 'xSidebar';

$objVar = new xClass();
$filtered_xID = filter_input(INPUT_GET, 'xID', FILTER_UNSAFE_RAW);
$fformKey = filter_input(INPUT_POST, 'formKey', FILTER_UNSAFE_RAW);
if (!empty($fformKey) && !empty($_SESSION['formKey']) && $fformKey == $_SESSION['formKey']) :
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/_formComponents/xDir-tgr_rubric_k_2_image1_additional_questions_validation.php';

	if ($intErrs == 0) :
		shuffleSortOrder(
			'tablename',
			'primaryKeyField',
			'xSortField',
			$objVar->xSortField,
			1
		);
		$objVar->create();
		shuffleSortOrder(
			'tablename',
			'primaryKeyField',
			'xSortField',
			$objVar->xSortField,
			2
		);
		$_SESSION['formKey'] = '';
		$strLoc = base(0).'Admin/xCategoryDir/xSubdirectory/edit/?s=n&xID=';
		$strLoc .= $objVar->xOptionData;
		header('Location: '.$strLoc);
		exit(0);
	endif;
endif;
$formKey = new formKey();

$pageTitle = 'xTerm Add';
// $jsFileName = 'glossary';

	/* Include the common layout header file */
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutHeader.php';
?>
<section id="contentStart" class="pageContent">
	<h1>xTerm Add</h1>

	<?php
	$fStatus = filter_input(INPUT_GET, 's', FILTER_UNSAFE_RAW);
	if (!empty($fStatus)) :
		$objVar->crudStatus($fStatus);
	endif;
	include $_SERVER['DOCUMENT_ROOT'] . '/lib/errorWarnings.php';
	?>

	<form action="<?php self(1); ?>" method="post" id="itemNew" name="itemNewNew" class="systemForm" novalidate>
		<?php
		$formKey->outputKey();
		include $_SERVER['DOCUMENT_ROOT'] . '/Admin/_formComponents/xDir-form.php';
		?>
		<p>
			<input type="submit" name="submit" value="Add" class="button" />
			<input type="reset" name="reset" value="Reset" class="button" onclick="openDialog('reset-overlay', this)" />
		</p>
	</form>
</section>
<?php
	/* Include the common layout footer file */
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutFooter.php';

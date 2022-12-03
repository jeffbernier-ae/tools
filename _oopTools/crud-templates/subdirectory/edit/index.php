<?php
/** @noinspection PhpUndefinedClassInspection */
/* Include the configuration file from the root directory */
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/_formComponents/xDir-variables.php';
$thisFile = setThisFile($_SERVER['REQUEST_URI']);
$sidebarInclude = 'xSidebar';

$xID = $strErr_xID = '';
$pageTitle = 'xTerm Edit';

$xID = filter_input(INPUT_POST, 'xID', FILTER_SANITIZE_NUMBER_INT);
if (empty($xID)) :
	$xID = filter_input(INPUT_GET, 'xID', FILTER_SANITIZE_NUMBER_INT);
endif;

if (empty($xID)) :
	/**
	 * No ID was present in the $_POST variable so display a select box with
	 * a list of item that the user can choose from. This form will submit back
	 * to this page with an ID set and then display the edit screen.
	 */
	$items = DB::query("SELECT xOptionData AS optionData,
								xDisplayData as displayData
							FROM xTable
							ORDER BY xDisplayData"
						);
	/* Include the common layout header file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutHeader.php';
	?>
	<section id="contentStart" class="pageContent">
		<h1>xTerm Edit</h1>
		<form action="<?php self(1); ?>" method="POST" class="systemForm">
			<?php
			gfSelectBox(
				$items,
				'Select the xTerm to edit',
				'xID',
				1,
				1,
				'',
				'itemSelection',
				'itemSelection',
				$strErr_xID,
				'',
				0,
				0,
				0,
				0
			);
			?>
			<p>
				<input type="submit"
					name="submit"
					value="Edit"
					class="button" />
			</p>
		</form>
	</section>
	<?php
	/* Include the common layout footer file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutFooter.php';
else :
	/**
	 * ID was present in the $_POST variable so display the edit form. This form
	 * will submit back to this page with the ID and formKey variables set and
	 * will attempt to process the edits.
	 */
	$objVar = new xClass($xID);

	$fformKey = filter_input(INPUT_POST, 'formKey', FILTER_UNSAFE_RAW);
	if (!empty($fformKey) && !empty($_SESSION['formKey']) && $fformKey == $_SESSION['formKey']) :
		/**
		 * Test if the form submission is valid, if so, write changes to the
		 * database
		 */
		include $_SERVER['DOCUMENT_ROOT'] . '/Admin/_formComponents/xDir-tgr_rubric_k_2_image1_additional_questions_validation.php';
		if ($intErrs == 0) :
			if ($objVar->xSortField > $_POST['originalSortOrder']) :
				$objVar->xSortField = $objVar->xSortField + 1;
			endif;
			shuffleSortOrder(
				'tablename',
				'primaryKeyField',
				'xSortField',
				$objVar->xSortField,
				1
			);
			$objVar->update();
			shuffleSortOrder(
				'tablename',
				'primaryKeyField',
				'xSortField',
				$objVar->xSortField,
				2
			);
			$_SESSION['formKey'] = '';
			$strLoc = base(0).'Admin/xCategoryDir/xSubdirectory/edit/';
			$strLoc .= '?s=u&xID='.$objVar->xOptionData;
			header('Location: '.$strLoc);
			exit(0);
		endif;
	endif;
	/**
	 * The form was not submitted so generate a formKey to prevent misuse of the
	 * form
	 */
	$formKey = new formKey();

	/* Include the common layout header file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutHeader.php';
	?>
	<section id="contentStart" class="pageContent">
		<h1>xTerm Edit</h1>
		<?php
		$fStatus = filter_input(INPUT_GET, 's', FILTER_UNSAFE_RAW);
		if (!empty($fStatus) && 0 == $intErrs) :
			$objVar->crudStatus($fStatus);
		endif;
		include $_SERVER['DOCUMENT_ROOT'] . '/lib/errorWarnings.php';
		?>
		<form action="<?php self(1); ?>"
				method="post"
				id="itemEdit"
				name="itemEdit"
				class="systemForm">
			<?php
			$formKey->outputKey();
			?>
			<input type="hidden"
				name="xID"
				value="<?php echo $objVar->xOptionData; ?>"
				id="xID" />
			<input type="hidden"
				name="originalSortOrder"
				id="originalSortOrder"
				value="<?php echo $objVar->xSortField; ?>" />
			<?php
			include $_SERVER['DOCUMENT_ROOT'] . '/Admin/_formComponents/xDir-form.php';
			?>
			<p class="button-row">
				<input type="submit"
					name="submit"
					value="Save"
					class="button" />
				<input type="reset"
					name="reset"
					value="Reset"
					class="button" />
			</p>
		</form>
	</section>
	<?php
	/* Include the common layout footer file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutFooter.php';
endif;

<?php
/* Include the configuration file from the root directory */
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
$thisFile = setThisFile($_SERVER['REQUEST_URI']);
$sidebarInclude = 'xSidebar';

$xID = '';
$strErr_xID = '';
$pageTitle = 'Delete xTerm';

$xID = filter_input(INPUT_POST, 'xID', FILTER_SANITIZE_NUMBER_INT);

if (empty($xID)) :
	/* Include the common layout header file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutHeader.php';
	?>
	<section id="contentStart" class="pageContent">
		<h1>Delete xTerm</h1>
		<?php
		$items = DB::query("SELECT xOptionData AS optionData,
									xDisplayData as displayData
								FROM xTable
								ORDER BY xDisplayData"
							);
		?>
		<form action="<?php self(1); ?>" method="POST" class="systemForm">
			<?php
			gfSelectBox(
				$items,
				'Select the xTerm to delete',
				'xID',
				1,
				1,
				'',
				'itemSelection',
				'itemSelection',
				$strErr_xID,
				'',
				1,
				0,
				0,
				0
			);
			?>
			<p>
				<input type="submit"
					name="submit"
					value="Delete"
					class="button"
					id="deleteYes" />
			</p>
		</form>
	</section>
	<?php
	/* Include the common layout footer file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutFooter.php';
else :
	$objVar = new xClass($xID);

	$fformKey = filter_input(INPUT_POST, 'formKey', FILTER_UNSAFE_RAW);
	if (!empty($fformKey) && !empty($_SESSION['formKey']) && $fformKey == $_SESSION['formKey']) :
		$objVar->delete();
		shuffleSortOrder(
			'tablename',
			'primaryKeyField',
			'xSortField',
			$objVar->xSortField,
			2
		);
		unset($objVar, $_SESSION['formKey']);
		$strLoc = base(0).'Admin/xCategoryDir/xSubdirectory/list/';
		header('Location: '.$strLoc);
	endif;
	$formKey = new formKey();

	/* Include the common layout header file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutHeader.php';
	?>
	<section id="contentStart" class="pageContent">
		<h1>Delete xTerm</h1>
		<p class="noBottomMargin">
			You are about to delete
			<strong><?php echo $objVar->xDisplayData; ?></strong>
			from the site.
		</p>
		<p>
			Are you sure you wish to continue?
		</p>
		<form method="post" action="<?php self(1); ?>" class="systemForm">
			<?php
			$formKey->outputKey();
			$strLink = base(0).'Admin/xCategoryDir/';
			$strLink .= 'xSubdirectory/list/';
			?>
			<input type="hidden"
				name="xID"
				value="<?php echo $objVar->xOptionData; ?>"
				id="xID" />
			<p class="button-row">
				<input type="submit"
					value="Yes"
					class="button"
					name="deleteYes" />
				<a href="<?php echo $strLink; ?>"
					class="button"
					name="deleteNo">
					No
				</a>
			</p>
		</form>
	</section>
	<?php
	/* Include the common layout footer file */
	include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutFooter.php';
endif;

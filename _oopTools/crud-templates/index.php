<?php
	/* Include the configuration file from the root directory */
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
$thisFile = base(0).substr($_SERVER['REQUEST_URI'], 1);

$sidebarInclude = 'xSidebar';
$pageTitle = 'Accessibility Evaluations';

	/* Include the common layout header file */
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutHeader.php';
?>
<section id="contentStart" class="pageContent">
	<!-- ADD YOUR CONTENT BELOW HERE -->
	<h1>Accessibility Evaluations Dashboard</h1>



	<!-- DO NOT EDIT BELOW HERE -->
</section>
<?php
	/* Include the common layout footer file */
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutFooter.php';
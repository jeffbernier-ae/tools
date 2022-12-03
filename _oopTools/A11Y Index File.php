<?php
/**
 * @var string $currentDT - The current date declared in the config.php file and included in calling file
 * @var int $intErrs - error counter defined in standard_variables.php and included in the index_variables.php file
 */

/* Include the configuration file from the root directory */
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$thisFile = setThisFile($_SERVER['REQUEST_URI']);


// TODO Add any tests or queries to get / validate the information needed to define the object and do so
// TODO update obj with the correct variable name and Class with the correct class name
$obj = new Class;

$intErrs = 0;
$formKey = '';
    /* Define Error variables */
// TODO define the error variables
$errBullets = '';

/**
 * Define breadcrumbs for this page
 */
// TODO define the breadcrumbs, sidebar Include file, page title and javascript file name
$breadcrumbs = [
    ['label'=>'', 'last'=>false],
    ['label'=>'', 'last'=>'true']
];
$isContentWide = 0;
$sidebarInclude = '';
$pageTitle = '';
$includeTimezoneJS = 0;
$jsFileName = '';
$formSubmitted = false;

$fformKey = filter_input(INPUT_POST, 'formKey', FILTER_UNSAFE_RAW);
if (!empty($fformKey) && !empty($_SESSION['formKey']) && $fformKey == $_SESSION['formKey']) :
    $formSubmitted = true;
    /**
     * Perform server side validation and assignments to the object
     */

    /* Validate required fields and conditionally required fields */

    /* Assign optional fields and conditionally required fields */

    $intErrs++;
    if ($intErrs == 0) :
        // TODO replace obj with correct variable
        $obj->create();
        $_SESSION['formKey'] = '';
        $recordCreated = true;
    endif;
endif;
$formKey = new formKey();

/* Include the common layout header file */
include $_SERVER['DOCUMENT_ROOT'] . '/includes/__layoutHeader.php';
// TODO input the page title
?>
    <section id="contentStart" class="pageContent">
        <h1></h1>

        <?php // TODO Input the id and name for the form ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id=""
              name="" class="standardForm">
            <?php
            $formKey->outputKey();
            ?>
            <section id="errorWrapper" aria-labelledby="err" aria-live="polite">
                <?php
                if ($intErrs > 0) :
                    ?>
                    <p id="err" class="errMsg heavy" aria-hidden="true" tabindex="-1">
                        An error occurred with your form submission. Please try again.
                    </p>
                    <ul>
                        <?php echo $errBullets; ?>
                    </ul>
                    <?php
                elseif ($formSubmitted) :
                    // TODO update obj with the correct variable name
                    $obj->crudStatus('n');
                endif;
                ?>
            </section>
            </div>
        </form>
    </section>
<?php
/* Include the common layout footer file */
include $_SERVER['DOCUMENT_ROOT'] . '/includes/__layoutFooter.php';

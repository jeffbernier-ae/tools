<?php
/* Include the configuration file from the root directory */
include $_SERVER['DOCUMENT_ROOT'].'/config.php';
$thisFile = setThisFile($_SERVER['REQUEST_URI']);
$sidebarInclude = 'xSidebar';

$pageTitle = 'xTerm List';

/* Added table prefix from environment variables */
$rs = DB::query("SELECT * FROM xTable ORDER BY xOptionData");
$items = stripSlashesDeep($rs);
unset($rs);

/* Include the common layout header file */
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutHeader.php';
?>
    <section id="contentStart" class="pageContent">
        <h1>xTerm List</h1>

        <p>Select the xTerm you would like to edit.</p>

        <div class="list">
            <div class="term">xTerm</div>

            <?php
            foreach($items AS $item) :
                $strLink = base(0).'Admin/xCategoryDir/xSubdirectory/edit/';
                $strLink .= '?xID='.$item['xOptionData'];
                ?>
                <div class="term">
                    <a href="<?php echo $strLink; ?>">
                        <?php
                        echo _e($item['xDisplayData']);
                        ?>
                    </a>
                </div>
            <?php
            endforeach;
            ?>
        </div>
    </section>
<?php
/* Include the common layout footer file */
include $_SERVER['DOCUMENT_ROOT'] . '/Admin/__layoutFooter.php';

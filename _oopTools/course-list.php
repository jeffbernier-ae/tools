<?php
/* Include the configuration file from the root directory */
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$thisFile = setThisFile($_SERVER['REQUEST_URI']);

$courses = DB::query("SELECT course_id, course_name, GUID, quiz_name 
                        FROM courses 
                        WHERE quiz_name IS NOT NULL
                        ORDER BY course_name");

/* Include the common layout header file */
include $_SERVER['DOCUMENT_ROOT'] . '/includes/__layoutHeader.php';
?>
<section id="contentStart" class="pageContent">
    <h1>Completed quizzes</h1>
    <ul>
        <?php
        foreach($courses as $c) :
            console_log($c);
            $strOutput = '<li>' . PHP_EOL;
            $strOutput .= '<a href="/training/champion_program/view-recording/quiz.php?cid=' . $c['GUID'] . '" target="_blank">';
            $strOutput .= $c['course_name'];
            $strOutput .= '</a>' . PHP_EOL;
            $strOutput .= '</li>';
            echo $strOutput;
        endforeach;
        ?>
    </ul>
</section>

<?php
/* Include the common layout footer file */
include $_SERVER['DOCUMENT_ROOT'] . '/includes/__layoutFooter.php';

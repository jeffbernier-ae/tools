<?php
/**
 * @var string $currentDT
 */
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$rs = DB::query("SELECT course_id FROM courses");
foreach ($rs AS $r) :
    $thisGUID = create_guid();
    DB::query("UPDATE courses SET GUID = %s WHERE course_id = %i", $thisGUID, $r['course_id']);
endforeach;
echo 'complete';

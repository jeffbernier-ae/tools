<?php
/**
 * @var string $currentDT
 */
include '../config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/lib/emailWrapper.php';
$host = intEmailHost();
$type = 'internal';


$htmlBody = '<p>pass 1: Sr&period; Client Program Manager</p>' . PHP_EOL;
$htmlBody = '<p>pass 2: ' . html_entity_decode('Sr&period; Client Program Manager') . '</p>' . PHP_EOL;
$htmlBody = '<p>pass 3: ' . htmlspecialchars_decode('Sr&period; Client Program Manager') . '</p>' . PHP_EOL;

$result = emailProcessor([
    'toName'=>'Jeff Bernier',
    'toEmail'=>'jeff.bernier@pearson.com',
    'fromName'=>'Accessibility Team for Assessments',
    'fromEmail'=>'accessibility@pearson.com',
    'fromPassword'=>emailPass(),
    'htmlBody'=>$htmlBody,
    'subject'=>'test decoding',
    'smtpPort'=>smtpPort(),
    'host'=>$host,
    'siteStatus'=>site(),
    'emailType'=>$type
]);
echo 'results';
echo $htmlBody;

<?php
/**
 * @var string $currentDT
 */
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/SSOSecurePaths.php';
$securedpaths = !empty($frontpaths) && is_array($frontpaths) ? $frontpaths : [];


function trim_slashes($sercuredpath)
{
    return trim(trim($sercuredpath, "/"));
}

// Trim the slashes from the values.
$securedpaths = array_map('trim_slashes', $securedpaths);

echo $_ENV['CURRENTURI'] . '<br />';
echo trim($_ENV['CURRENTURI'], "/") . '<br />';
if (in_array(trim($_ENV['CURRENTURI'], "/"), $securedpaths)) :
    echo 'true'. '<br />';
else :
    echo 'false'. '<br />';
endif;

exit(0);
if (!empty(trim($_ENV['CURRENTURI'], "/")) && (in_array(trim($_ENV['CURRENTURI'], "/"), $securedpaths) ||
        substr(trim(strtolower($_ENV['CURRENTURI']), "/"), 0, 5) === "admin")) {
    return true;
} else {
    return false;
}

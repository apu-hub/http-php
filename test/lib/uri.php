<?php
require(__DIR__."/../../vendor/autoload.php");
use App\Uri;

$start_time = microtime(true);
// getSegment();
matchUriAndPath();
// parseParamData();

$end_time = microtime(true);
$execution_time = ($end_time - $start_time);
echo "Execution in " . $execution_time . " sec";

// Get Segment
function getSegment()
{
    $urlArray = [
        "/a/b/c/",
        "/a/b/c",
        "a/b/c",
        "a/b/c/",
        "a/b//c/",
    ];

    foreach ($urlArray as $uri) {
        print_r(Uri::getSegment($uri));
    }
}


// Match Uri And path
function matchUriAndPath()
{
    $uriArray = [
        "/a/b",
        "/a/b/c",
        "/a/b/c/d",
    ];
    $pathArray = [
        "/a/b/c/",
        "/a/bf/c",
        "/a/b/c/d",
        "a/:asgfdhgh/c",
        "a/*",
    ];

    foreach ($uriArray as $uri) {
        echo "\n\n====== For URI \"$uri\" ======\n";
        foreach ($pathArray as $path) {
            echo "\nURI  => \"$uri\"";
            echo "\nPATH => \"$path\"";
            echo (Uri::matchUriAndPath($uri, $path)) ? "\nmatch" : "\nnot";
            echo "\n";
        }
    }
}

function parseParamData()
{
    $uriArray = [
        "/a/b/c/",
        "/a/b/c",
        "a/234/c",
        "a/b/c/",
    ];
    $pathArray = [
        "/a/b/c/",
        "/a/*/bf/c",
        "a/:v:1/c",
        "a/*",
    ];

    foreach ($pathArray as $index => $path) {
        $isMatch = Uri::matchUriAndPath($uriArray[$index], $path);
        var_dump($isMatch);
        if ($isMatch) {
            var_dump(Uri::parseParamData($uriArray[$index], $path));
        }
    }
}

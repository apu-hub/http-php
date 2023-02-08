<?php
namespace App;
// define("wildcardParam",   '*'); // indicates a optional greedy parameter
// define("paramStarterChar", ':'); // start character for a parameter with name
class Uri
{
    public static function cleaner(string $url): string
    {
        // echo "\nUri cleaner =======\n";
        // echo $url, " => ";
        // remove uri querys
        $url = explode("?", trim($url));
        $url = current($url);

        // trim extra slash
        $url = ltrim($url, "/");
        $url = rtrim($url, "/");
        // echo $url;
        return $url;
    }
    public static function getSegment(string $url): array
    {
        // remove uri querys
        $url = explode("?", trim($url));
        $url = current($url);

        // trim extra slash
        $url = ltrim($url, "/");
        $url = rtrim($url, "/");

        // separate all params
        $url_array = explode("/", $url);

        // return 
        return $url_array;
    }

    public static function matchUriAndPath(string $uri, string $path): bool
    {
        $uriArray = Uri::getSegment($uri);
        $pathArray = Uri::getSegment($path);

        // uri < path
        if (count($uriArray) < count($pathArray)) {
            // path segment is a wildcard
            if (end($pathArray) == "*") {
                return true;
            }
            return false;
        }
        // uri >= path
        for ($i = 0; $i < count($pathArray); $i++) {
            // path & uri segment not match 
            if ($pathArray[$i] != $uriArray[$i]) {
                // path segment is a wildcard
                if ($pathArray[$i] == "*") {
                    return true;
                }
                // path segment is not a parameter
                if (!preg_match_all("/^:/", $pathArray[$i])) {
                    return false;
                }
            }
        }
        // uri != path
        if (count($uriArray) != count($pathArray)) {
            return false;
        }
        // all match
        return true;
    }
    // PATH must smaller or equal to segment URI
    public static function parseParamData(string $uri, string $path): array
    {
        // echo "\nUri parse param =======\n";
        // echo "uri => ",$uri,"\n";
        // echo "uri => ",$path,"\n";

        $data = [];
        $uriArray = Uri::getSegment($uri);
        $pathArray = Uri::getSegment($path);

        // uri >= path
        for ($i = 0; $i < count($pathArray); $i++) {
            // path & uri segment not match 
            if ($pathArray[$i] != $uriArray[$i]) {
                // path segment is a wildcard
                if ($pathArray[$i] == "*") {
                    return $data;
                }
                // if path segment is parameter
                if (preg_match_all("/^:/", $pathArray[$i])) {
                    // remove ':'
                    $param_key = ltrim($pathArray[$i], ":");
                    $data[$param_key] = $uriArray[$i];
                }
            }
        }
        // echo "data => ";
        // var_dump($data);

        return $data;
    }

    // ################
    public static function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }
}

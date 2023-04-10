<?php

namespace App;

class Utils
{
   public static function cleaner($input)
   {
      $input = trim($input);
      $input = stripslashes($input);
      $input = htmlspecialchars($input);
      return $input;
   }
   public static function htmlRedirect($url, $message = "")
   {
      if ($message != "")
         echo '<script type="text/javascript"> alert("' . $message . '") </script>';

      echo '<script type="text/javascript"> location.href="' . $url . '"; </script>';
      exit;
   }
   public static function phpRedirect($url)
   {
      header('Location: ' . $url);
      exit();
   }
   public static function randomNumber($length)
   {
      $result = '';

      for ($i = 0; $i < $length; $i++) {
         $result .= mt_rand(0, 9);
      }

      return $result;
   }
   /**
    * Display Data
    */
   public static function dd($data)
   {
      echo "<br>";
      echo "<pre>";
      print_r($data);
      echo "</pre>";
      echo "<br>";
   }
   public static function url(string $uri)
   {
      $Host_Name = $_ENV['APP_URL'] ?? "localhost";
      $Host_Protocal = $_ENV['APP_PROTOCAL'] ?? "http";

      $host_url = $Host_Protocal . "://" . $Host_Name;

      if (trim($host_url) == "")
         return $uri;

      return rtrim($host_url, "/") . "/" . rtrim(ltrim($uri, "/"), "/");
   }

   public static function filter_array(array $data, array $schema, bool $strict = false)
   {
      $out_array = [];
      foreach ($schema as $value) {
         // if unset & strict
         if (!isset($data[$value]) || $strict) {
            throw new \Exception("\"$value\" Key not found in data array");
         }

         // if unset
         if (!isset($data[$value]))
            continue;

         // add value to array
         $out_array = array_merge($out_array, [$value => $data[$value]]);
      }
      return $out_array;
   }
   public static function public_path(string $path = "")
   {
      $app_path = $_SERVER['DOCUMENT_ROOT'];

      if ($path == "")
         return $app_path;

      return join(DIRECTORY_SEPARATOR, [$app_path, $path]);
   }
}

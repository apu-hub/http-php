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
}
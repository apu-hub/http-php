<?php

namespace App;

use Rakit\Validation\Validator;

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
   public static function dd(...$data)
   {

      foreach ($data as $key => $value) {
         # code...
         echo "<br>";
         echo "<pre>";
         echo "<br>";
         print_r($value);
         echo "</br>";
         echo "<br>";
      }
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

   /**
    * Filter Array
    * @param array $data [ 'key' => 'value' ]
    *
    * @param array $schema [ 'key' => 'rules' ]
    * 
    * '' for optional, 'required'
    * @return array
    */
   public static function filter_array_v2(array $data, array $schema)
   {
      $out_array = [];
      foreach ($schema as $key => $value) {
         // echo "<br>", $key;
         $rules = explode('|', $value);
         // if unset & strict
         foreach ($rules as $rule) {
            if ($rule == 'required') {
               if (!isset($data[$key])) {
                  $key_name = str_replace('_', ' ', $key);
                  throw new \Exception("The $key_name field is required.");
               }
            }
         }

         // if unset
         if (!isset($data[$key]))
            continue;

         // add value to array
         $out_array = array_merge($out_array, [$key => $data[$key]]);
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
   public static function validation(array $values, array $rules, $messages = [])
   {
      $validator = new Validator;
      $response_session = new Session('request');

      // make it
      $validation = $validator->make($values, $rules, $messages);

      // then validate
      $validation->validate();

      // handling errors
      if ($validation->fails()) {
         $errors = $validation->errors();

         $response_session->set("errors", $errors->firstOfAll() ?? []);

         //  set old values
         foreach ($rules as $key => $value) {
            if (isset($values[$key]))
               $response_session->set($key, $values[$key]);
         }

         $err = $errors->all();
         return ['errors' => $errors->firstOfAll() ?? [], 'error' => $err[0] ?? "Invalid Input"];
      }

      return ['data' => $validation->getValidatedData()];
      // return $validation->getValidData();
      // return $validation->getInvalidData();
   }

   public static function slugify($text)
   {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicated - symbols
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
         return 'n-a';
      }

      return $text;
   }
}

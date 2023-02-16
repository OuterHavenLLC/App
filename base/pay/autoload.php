<?php
 spl_autoload_register(function($className) {
  if(strpos($className, "Braintree") !== 0) {
   return;
  }
  $fn = dirname(__DIR__)."/pay/";
  if($lastNsPos = strripos($className, "\\")) {
   $namespace = substr($className, 0, $lastNsPos);
   $className = substr($className, $lastNsPos + 1);
   $fn .= str_replace("\\", DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
  }
  $fn .= str_replace("_", DIRECTORY_SEPARATOR, $className).".php";
  if(is_file($fn)) {
   require_once($fn);
  }
 });
?>
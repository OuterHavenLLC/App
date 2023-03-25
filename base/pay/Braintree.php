<?php
 spl_autoload_register(function($className) {
  if(strpos($className, "Braintree") !== 0) {
   return;
  }
  $fileName = "/";
  if($lastNsPos = strripos($className, "\\")) {
   $namespace = substr($className, 0, $lastNsPos);
   $className = substr($className, $lastNsPos + 1);
   $fileName .= str_replace("\\", "/", $namespace)."/";
  }
  $fileName .= str_replace("_", "/", $className).".php";
  $fileName = __DIR__.$fileName;
  if(is_file($fileName)) {
   require_once($fileName);
  }
 });
 if(version_compare(PHP_VERSION, "7.3.0", "<")) {
  throw new Braintree\Exception("PHP version >= 7.3.0 required");
 }
 /**
  * Braintree PHP Library
  * Creates class_aliases for old class names replaced by PSR-4 Namespaces
 */
 class Braintree {
  /**
   * Checks for required dependencies
   *
   * @throws Braintree/Exception With the missing extension
   *
   * @return void
  */
  public static function requireDependencies() {
   $extensions = ['xmlwriter', 'openssl', 'dom', 'hash', 'curl'];
   foreach($extensions as $ext) {
    if(!extension_loaded($ext)) {
     throw new Braintree\Exception("The Braintree library requires the ".$ext." extension.");
    }
   }
  }
 }
 Braintree::requireDependencies();
?>
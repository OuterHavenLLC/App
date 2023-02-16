<?php
 require_once("autoload.php");
 if(version_compare(PHP_VERSION, "7.2.0", "<")) {
  throw new Braintree_Exception("PHP version >= 7.2.0 required");
 }
 class Braintree {
  public static function requireDependencies() {
   $ext = ["curl", "dom", "hash", "openssl", "xmlwriter"];
    foreach($ext as $e) {
     if(!extension_loaded($e)) {
      throw new Braintree_Exception("The Braintree library requires the $e extension.");
     }
    }
   }
  }
 Braintree::requireDependencies();
?>
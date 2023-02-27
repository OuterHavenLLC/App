<?php
 Class 2FA extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home() {
   $r = $this->system->Page("XXXX");
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
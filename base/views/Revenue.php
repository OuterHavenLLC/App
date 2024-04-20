<?php
 Class Revenue extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
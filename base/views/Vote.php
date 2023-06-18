<?php
 Class Vote extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Containers(array $a) {
  }
  function Save(array $a) {
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
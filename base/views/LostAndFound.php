<?php
 Class LostAndFound extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home() {
   $r = $this->system->Page("XXXX");
   return $this->system->Card([
    "Front" => $r
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
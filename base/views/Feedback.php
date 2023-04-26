<?php
 Class Feedback extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home(array $a) {
   $r = $this->system->Element([
    "h1", "Home"
   ]).$this->system->Element([
    "p", "View and respond to feedback."
   ]);
   return $r;
  }
  function Save(array $a) {
   $r = $this->system->Element([
    "p", "Saves the response to the Feedback thread, among other admin-level preferences."
   ]);
   return $r;
  }
  function Stream(array $a) {
   $r = $this->system->Element(["div", "Test", [
    "class" => "MSGt"
   ]]).$this->system->Element(["div", "Response", [
    "class" => "MSGy"
   ]]);
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
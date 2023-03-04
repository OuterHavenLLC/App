<?php
 Class LostAndFound extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home() {
   $r = $this->system->Change([[
    "[LostAndFound.Options.Password]" => base64_encode("v=".base64_encode("LostAndFound:Password")),
    "[LostAndFound.Options.PIN]" => base64_encode("v=".base64_encode("LostAndFound:PIN")),
    "[LostAndFound.Options.Username]" => base64_encode("v=".base64_encode("LostAndFound:Username"))
   ], $this->system->Page("65c5bed973a21411e6167bbdc721bbe4")]);
   return $this->system->Card([
    "Front" => $r
   ]);
  }
  function Password() {
   return $this->system->Element(["button", "Back", [
    "class" => "GoToParent LI header",
    "data-type" => "LostAndFound"
   ]]).$this->system->Element([
    "h2", "Password"
   ]);
  }
  function PIN() {
   return $this->system->Element(["button", "Back", [
    "class" => "GoToParent LI header",
    "data-type" => "LostAndFound"
   ]]).$this->system->Element([
    "h2", "PIN"
   ]);
  }
  function Username() {
   return $this->system->Element(["button", "Back", [
    "class" => "GoToParent LI header",
    "data-type" => "LostAndFound"
   ]]).$this->system->Element([
    "h2", "Username"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
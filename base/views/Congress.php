<?php
 Class Congress extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   // DEMOCRATIZED CONTENT MODERATION
   // HOUSE = 2X POPULATION OF SENATE, EX: 200:100 OR 800:400 RATIOS
   $r = $this->system->Page("Congress");
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
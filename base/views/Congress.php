<?php
 Class Congress extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   // DEMOCRATIZED CONTENT MODERATION
   // HOUSE = 2X POPULATION OF SENATE, EX: 200:100 OR 800:400 RATIOS
   $r = $this->core->Page("Congress");
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "Title" => "Congress of ".$this->core->config["App"]["Name"]
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
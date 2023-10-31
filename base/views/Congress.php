<?php
 Class Congress extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $chamber = $data["Chanber"] ?? "";
   $chambers = $data["Chanbers"] ?? 0;
   $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
   $pub = $data["pub"] ?? 0;
   // DEMOCRATIZED CONTENT MODERATION
   // HOUSE = 2X POPULATION OF SENATE, EX: 200:100 OR 800:400 RATIOS
   if($chambers == 1) {
    if($chamber == "House") {
     $r = $this->core->Element([
      "h1", $chamber
     ]).$this->core->Element([
      "p", "Welcome to the Chamber of the $chamber of Congress."
     ]);
    } elseif($chamber == "Senate") {
     $r = $this->core->Element([
      "h1", $chamber
     ]).$this->core->Element([
      "p", "Welcome to the Chamber of the $chamber of Congress."
     ]);
    }
   } else {
    $r = $this->core->Change([[
     "[Congress.Chambers.House]" => base64_encode("v=".base64_encode("Congress:Home")."&Chamber=House&Chambers=1"),
     "[Congress.Chambers.Senate]" => base64_encode("v=".base64_encode("Congress:Home")."&Chamber=Senate&Chambers=1"),
     "[Congress.CoverPhoto]" => $this->core->PlainText([
      "Data" => "[Media:Congress]",
      "Display" => 1
     ])
    ], $this->core->Page("Congress")]);
   }
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
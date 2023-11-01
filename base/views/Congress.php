<?php
 Class Congress extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $chamber = $data["Chamber"] ?? "";
   $chambers = $data["Chambers"] ?? 0;
   $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
   $pub = $data["pub"] ?? 0;
   // DEMOCRATIZED CONTENT MODERATION
   // HOUSE = 2X POPULATION OF SENATE, EX: 200:100 OR 800:400 RATIOS
   if($chambers == 1) {
    if($chamber == "House") {
     $r = $this->core->Element(["button", "Back", [
      "class" => "LI GoToParent",
      "data-type" => "Congress"
     ]]).$this->core->Element([
      "h1", $chamber
     ]).$this->core->Element([
      "p", "Welcome to the Chamber of the $chamber of Congress.. A list of House members, the ability to vote in new members, and more will be present here in the future."
     ]);
    } elseif($chamber == "Senate") {
     $r = $this->core->Element(["button", "Back", [
      "class" => "LI GoToParent",
      "data-type" => "Congress"
     ]]).$this->core->Element([
      "h1", $chamber
     ]).$this->core->Element([
      "p", "Welcome to the Chamber of the $chamber of Congress. A list of Senators, the ability to vote in new Senators if you are a House member, and more will be present here in the future."
     ]);
    }
   } else {
    $r = $this->core->Change([[
     "[Congress.Chambers.House]" => base64_encode("v=".base64_encode("Congress:Home")."&Chamber=House&Chambers=1"),
     "[Congress.Chambers.House.Join]" => "",
     "[Congress.Chambers.Senate]" => base64_encode("v=".base64_encode("Congress:Home")."&Chamber=Senate&Chambers=1"),
     "[Congress.Chambers.Senate.Join]" => "",
     "[Congress.CoverPhoto]" => $this->core->PlainText([
      "Data" => "[Media:Congress]",
      "Display" => 1
     ])
    ], $this->core->Page("8a38a3053ce5449ca2d321719f5aea0f")]);
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
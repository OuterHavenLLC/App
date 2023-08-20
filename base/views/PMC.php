<?php
 Class PMC extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Home(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $r = $this->core->Page("5f3a58adef65d3fbd25f0c3ec26d0aa6");
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
    "Title" => "Outer Haven P.M.C. Branch"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
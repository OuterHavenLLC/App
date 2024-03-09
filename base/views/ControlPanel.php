<?php
 Class ControlPanel extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $responseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif($y["Rank"] == md5("High Command")) {
    $accessCode = "Accepted";
    $config = $this->core->Configuration() ?? [];
    $search = base64_encode("Search:Containers");
    $r = $this->core->Change([[
     "[Admin.Domain]" => "W('https://www.godaddy.com/', '_blank');",
     "[Admin.Feedback]" => base64_encode("v=$search&st=Feedback"),
     "[Admin.Files]" => base64_encode("v=".base64_encode("Album:List")."&AID=".md5("unsorted")."&UN=".base64_encode($this->core->ID)),
     "[Admin.MassMail]" => base64_encode("v=$search&st=ADM-MassMail"),
     "[Admin.Mail]" => "https://mail.outerhaven.nyc/iredadmin/",
     "[Admin.Pages]" => base64_encode("v=$search&CARD=1&st=ADM-LLP"),
     "[Admin.RenewSubscriptions]" => base64_encode("v=".base64_encode("Subscription:RenewAll")),
     "[Admin.Server]" => "https://www.digitalocean.com/",
     "[App.Configuration.Model]" => json_encode($config, true)
    ], $this->core->Extension("5c1ce5c08e2add4d1487bcd2193315a7")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
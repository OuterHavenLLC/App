<?php
 Class ControlPanel extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function __SampleProcessorModel(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif($y["Rank"] == md5("High Command")) {
    $config = $this->core->config ?? [];
    // LOGIC
    #$this->core->Data("Save", ["app", md5("config"), $config]);
    $r = [
     "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
     "Header" => "Done",
     "Scrollable" => json_encode($config, true)
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
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
    $_Search = base64_encode("Search:Containers");
    $accessCode = "Accepted";
    $config = $this->core->config ?? [];
    $events = "";
    $eventsList = $config["PublicEvents"] ?? [];
    $responseType = "View";
    $search = "";
    $searchLists = $config["App"]["Search"] ?? [];
    foreach($eventsList as $event => $info) {
     $events .= $this->core->Change([[
      "[Clone.ID]" => $event,
      "[Event.BannerText]" => $info["BannerText"],
      "[Event.CoverPhoto]" => "",
      "[Event.Description]" => $info["Description"],
      "[Event.ID]" => $event,
      "[Event.Link]" => $info["Link"],
      "[Event.Title]" => $info["Title"]
     ], $this->core->Extension("889a3f39fa958bcc2a57b2f1882198ff")]);
    } foreach($searchLists as $list => $info) {
     $search .= $this->core->Change([[
      "[Clone.ID]" => $list,
      "[List.Description]" => $info["Description"],
      "[List.ID]" => $list,
      "[List.Title]" => $info["Title"]
     ], $this->core->Extension("3777f71aa914041840ead48e3a259866")]);
    }
    $config["App"]["Maintenance"] = 0;//TEMP
    $r = $this->core->Change([[
     "[Admin.Domain]" => "W('https://www.godaddy.com/', '_blank');",
     "[Admin.Feedback]" => base64_encode("v=$_Search&st=Feedback"),
     "[Admin.Files]" => base64_encode("v=".base64_encode("Album:List")."&AID=".md5("unsorted")."&UN=".base64_encode($this->core->ID)),
     "[Admin.MassMail]" => base64_encode("v=$_Search&st=ADM-MassMail"),
     "[Admin.Mail]" => "https://mail.outerhaven.nyc/iredadmin/",
     "[Admin.Pages]" => base64_encode("v=$_Search&CARD=1&st=ADM-LLP"),
     "[Admin.RenewSubscriptions]" => base64_encode("v=".base64_encode("Subscription:RenewAll")),
     "[Admin.Server]" => "https://www.digitalocean.com/",
     "[App.Configuration.Model.Media]" => json_encode($config["Media"], true),
     "[Configuration.App.Description]" => $config["App"]["Description"],
     "[Configuration.App.Maintenance]" => $config["App"]["Maintenance"],
     "[Configuration.App.Name]" => $config["App"]["Name"],
     "[Configuration.Events]" => $events,
     "[Configuration.Events.Clone]" => base64_encode($this->core->Change([[
      "[Event.BannerText]" => "",
      "[Event.CoverPhoto]" => "",
      "[Event.Description]" => "",
      "[Event.ID]" => "",
      "[Event.Link]" => "",
      "[Event.Title]" => ""
     ], $this->core->Extension("889a3f39fa958bcc2a57b2f1882198ff")])),
     "[Configuration.Search]" => $search,
     "[Configuration.Search.Clone]" => base64_encode($this->core->Change([[
      "[Event.Description]" => "",
      "[Event.ID]" => "",
      "[Event.Title]" => ""
     ], $this->core->Extension("3777f71aa914041840ead48e3a259866")]))
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
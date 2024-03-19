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
    $_Search = base64_encode("Search:Containers");
    $accessCode = "Accepted";
    $config = $this->core->config ?? [];
    $events = "";
    $eventsList = $config["PublicEvents"] ?? [];
    $media = "";
    $mediaList = $config["Media"] ?? [];
    $responseType = "View";
    $search = "";
    $searchLists = $config["App"]["Search"] ?? [];
    $statistics = "";
    $statisticsList = $config["Statistics"] ?? [];
    foreach($mediaList as $key => $info) {
     $media .= $this->core->Change([[
      "[Clone.ID]" => $key,
      "[Media.File]" => $info["File"],
      "[Media.ID]" => $key,
      "[Media.Name]" => $info["Name"]
     ], $this->core->Extension("f1a8c31050b241ebcea22f33cf6171f4")]);
    } foreach($eventsList as $event => $info) {
     $coverPhoto = $this->core->Element(["div", $this->core->Element([
      "h4", "Cover Photo", ["class" => "UpperCase"]
     ]).$this->core->Element([
      "p", "UI and inputs coming soon..."
     ]), ["class" => "Medium"]]);
     $events .= $this->core->Change([[
      "[Clone.ID]" => $event,
      "[Event.BannerText]" => $info["BannerText"],
      "[Event.CoverPhoto]" => $coverPhoto,
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
    } foreach($statisticsList as $stat => $name) {
     $search .= $this->core->Change([[
      "[Clone.ID]" => $stat,
      "[Statistic.ID]" => $stat,
      "[Statistic.Name]" => $name
     ], $this->core->Extension("21af4585b38e4b15a37fce7dfbb95161")]);
    }
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
     "[Configuration.App.Description]" => base64_encode($config["App"]["Description"]),
     "[Configuration.App.Keywords]" => base64_encode($config["App"]["Keywords"]),
     "[Configuration.App.Maintenance]" => $config["App"]["Maintenance"],
     "[Configuration.App.Name]" => base64_encode($config["App"]["Name"]),
     "[Configuration.Events]" => $events,
     "[Configuration.Events.Clone]" => base64_encode($this->core->Change([[
      "[Event.BannerText]" => "",
      "[Event.CoverPhoto]" => "",
      "[Event.Description]" => "",
      "[Event.ID]" => "",
      "[Event.Link]" => "",
      "[Event.Title]" => ""
     ], $this->core->Extension("889a3f39fa958bcc2a57b2f1882198ff")])),
     "[Configuration.Media]" => $media,
     "[Configuration.Media.Clone]" => base64_encode($this->core->Change([[
      "[Media.File]" => "",
      "[Media.ID]" => "",
      "[Media.Name]" => ""
     ], $this->core->Extension("f1a8c31050b241ebcea22f33cf6171f4")])),
     "[Configuration.Save.App]" => base64_encode("v=".base64_encode("ControlPanel:SaveApp")),
     "[Configuration.Save.Events]" => base64_encode("v=".base64_encode("ControlPanel:SaveEvents")),
     "[Configuration.Save.Media]" => base64_encode("v=".base64_encode("ControlPanel:SaveCoreMedia")),
     "[Configuration.Save.Search]" => base64_encode("v=".base64_encode("ControlPanel:SaveSearchLists")),
     "[Configuration.Save.Statistics]" => base64_encode("v=".base64_encode("ControlPanel:SaveStatistics")),
     "[Configuration.Search]" => $search,
     "[Configuration.Search.Clone]" => base64_encode($this->core->Change([[
      "[List.Description]" => "",
      "[List.ID]" => "",
      "[List.Title]" => ""
     ], $this->core->Extension("3777f71aa914041840ead48e3a259866")])),
     "[Configuration.Statistics]" => "",
     "[Configuration.Statistics.Clone]" => base64_encode($this->core->Change([[
      "[Statistic.ID]" => "",
      "[Statistic.Name]" => ""
     ], $this->core->Extension("21af4585b38e4b15a37fce7dfbb95161")]))
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
  function SaveApp(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
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
    $app = $config["App"] ?? [];
    $search = $app["Search"] ?? [];
    $description = $data["Description"] ?? $app["Description"];
    $keywords = $data["Keywords"] ?? $app["Keywords"];
    $maintenance = $data["Maintenance"] ?? 0;
    $name = $data["Name"] ?? $app["Name"];
    $app = [
     "Description" => $description,
     "Keywords" => $keywords,
     "Maintenance" => $maintenance,
     "Name" => $name,
     "Search" => $search
    ];
    $config["App"] = $app;
    $this->core->Data("Save", ["app", md5("config"), $config]);
    $r = [
     "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
     "Header" => "Done"
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
  function SaveEvents(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
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
  function SaveMedia(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
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
  function SaveSearch(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
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
  function SaveStatistics(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
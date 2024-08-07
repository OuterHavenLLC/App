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
    $_LiveView = base64_encode("v=".base64_encode("LiveView:CoreMedia")."&DLC=");
    $_Search = base64_encode("Search:Containers");
    $accessCode = "Accepted";
    $config = $this->core->config ?? [];
    $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
    $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
     "Header" => base64_encode($this->core->Element([
      "h1", "Control Panel", ["class" => "CenterText"]
     ])),
     "Text" => base64_encode("Please enter your PIN to access the <em>".$config["App"]["Name"]."</em> configuration."),
     "ViewData" => base64_encode(json_encode([
      "SecureKey" => base64_encode($y["Login"]["PIN"]),
      "VerifyPassPhrase" => 1,
      "v" => base64_encode("ControlPanel:Home")
     ], true))
    ]]);
    $r = $this->core->RenderView($r);
    if($verifyPassPhrase == 1) {
     $accessCode = "Denied";
     $key = $data["Key"] ?? base64_encode("");
     $key = base64_decode($key);
     $r = $this->core->Element(["p", "The Key is missing."]);
     $secureKey = $data["SecureKey"] ?? base64_encode("");
     $secureKey = base64_decode($secureKey);
     if(md5($key) != $secureKey) {
      $r = $this->core->Element(["p", "The Keys do not match."]);
     } else {
      $accessCode = "Accepted";
      $events = "";
      $eventsList = $config["PublicEvents"] ?? [];
      $media = "";
      $mediaList = $config["Media"] ?? [];
      $previewQuantity = base64_encode("Single");
      $responseType = "View";
      $saveFirst = base64_encode("v=".base64_encode("ControlPanel:SaveFirst"));
      $search = "";
      $searchLists = $config["App"]["Search"] ?? [];
      $shopID = $config["App"]["ShopID"] ?? "Mike";
      $statistics = "";
      $statisticsList = $config["Statistics"] ?? [];
      foreach($mediaList as $key => $info) {
       $addTo = base64_encode("Link to ".str_replace(":", "&colon;", $info["Name"]).":.AddTo$key");
       $added = base64_encode("Added! Feel free to close this card.");
       $file = (!empty($info["File"])) ? base64_encode($info["File"]) : "";
       $media .= $this->core->Change([[
        "[Clone.ID]" => $key,
        "[Media.Add]" => base64_encode("v=".base64_encode("Search:Containers")."&lPG=Files&st=XFS&AddTo=$addTo&Added=$added&UN=".base64_encode($this->core->ID)),
        "[Media.File]" => $file,
        "[Media.File.Quantity]" => $previewQuantity,
        "[Media.ID]" => $key,
        "[Media.Input]" => "MediaFile[]",
        "[Media.Input.LiveView]" => $_LiveView,
        "[Media.Name]" => $info["Name"]
       ], $this->core->Extension("f1a8c31050b241ebcea22f33cf6171f4")]);
      } foreach($eventsList as $event => $info) {
       $addTo = base64_encode("Set as ".$info["Title"]."'s Cover Photo:.AddTo$event");
       $added = base64_encode("Added! Feel free to close this card.");
       $coverPhoto = (!empty($info["CoverPhoto"])) ? base64_encode($info["CoverPhoto"]) : "";
       $domains_base = $config["App"]["Domains_Base"] ?? "outerhaven.nyc";
       $domains_fileSystem = $config["App"]["Domains_FileSystem"] ?? "efs.outerhaven.nyc";
       $events .= $this->core->Change([[
        "[Clone.ID]" => $event,
        "[Event.BannerText]" => $info["BannerText"],
        "[Event.Description]" => $info["Description"],
        "[Event.ID]" => $event,
        "[Event.Link]" => $info["Link"],
        "[Event.Title]" => $info["Title"],
        "[Media.Add]" => base64_encode("v=".base64_encode("Search:Containers")."&CARD=1&st=XFS&AddTo=$addTo&Added=$added&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".base64_encode($this->core->ID)),
        "[Media.File]" => $coverPhoto,
        "[Media.File.Quantity]" => $previewQuantity,
        "[Media.Input]" => "EventCoverPhoto[]",
        "[Media.Input.LiveView]" => $_LiveView
       ], $this->core->Extension("889a3f39fa958bcc2a57b2f1882198ff")]);
      } foreach($searchLists as $list => $info) {
       $search .= $this->core->Change([[
        "[Clone.ID]" => $list,
        "[List.Description]" => $info["Description"],
        "[List.ID]" => $list,
        "[List.Title]" => $info["Title"]
       ], $this->core->Extension("3777f71aa914041840ead48e3a259866")]);
      } foreach($statisticsList as $stat => $name) {
       $statistics .= $this->core->Change([[
        "[Clone.ID]" => $stat,
        "[Statistic.ID]" => $stat,
        "[Statistic.Name]" => $name
       ], $this->core->Extension("21af4585b38e4b15a37fce7dfbb95161")]);
      }
      $r = $this->core->Change([[
       "[Admin.Databases]" => "W('https://outerhaven.nyc/phpmyadmin', '_blank');",
       "[Admin.Domain]" => "W('https://www.godaddy.com/', '_blank');",
       "[Admin.Feedback]" => base64_encode("v=$_Search&st=Feedback"),
       "[Admin.Files]" => base64_encode("v=".base64_encode("Album:List")."&AID=".md5("unsorted")."&UN=".base64_encode($this->core->ID)),
       "[Admin.Mail]" => "https://mail.outerhaven.nyc/iredadmin/",
       "[Admin.Pages]" => base64_encode("v=$_Search&CARD=1&st=ADM-LLP"),
       "[Admin.RenewSubscriptions]" => base64_encode("v=".base64_encode("Subscription:RenewAll")),
       "[Admin.Server]" => "https://www.digitalocean.com/",
       "[Configuration.App.Description]" => base64_encode($config["App"]["Description"]),
       "[Configuration.App.Domains.Base]" => base64_encode($domains_base),
       "[Configuration.App.Domains.FileSystem]" => base64_encode($domains_fileSystem),
       "[Configuration.App.Keywords]" => base64_encode($config["App"]["Keywords"]),
       "[Configuration.App.Maintenance]" => $config["Maintenance"],
       "[Configuration.App.Name]" => base64_encode($config["App"]["Name"]),
       "[Configuration.App.ShopID]" => base64_encode($shopID),
       "[Configuration.Events]" => $events,
       "[Configuration.Events.Clone]" => base64_encode($this->core->Change([[
        "[Event.BannerText]" => "",
        "[Event.Description]" => "",
        "[Event.ID]" => "",
        "[Event.Link]" => "",
        "[Event.Title]" => "",
        "[Media.Add]" => $saveFirst,
        "[Media.File]" => "",
        "[Media.File.Quantity]" => $previewQuantity,
        "[Media.Input]" => "EventCoverPhoto[]",
        "[Media.Input.LiveView]" => $_LiveView
       ], $this->core->Extension("889a3f39fa958bcc2a57b2f1882198ff")])),
       "[Configuration.Media]" => $media,
       "[Configuration.Media.Clone]" => base64_encode($this->core->Change([[
        "[Media.Add]" => $saveFirst,
        "[Media.File]" => "",
        "[Media.File.Quantity]" => $previewQuantity,
        "[Media.Input]" => "MediaFile[]",
        "[Media.Input.LiveView]" => $_LiveView,
        "[Media.ID]" => "",
        "[Media.Name]" => ""
       ], $this->core->Extension("f1a8c31050b241ebcea22f33cf6171f4")])),
       "[Configuration.Save.App]" => base64_encode("v=".base64_encode("ControlPanel:SaveApp")),
       "[Configuration.Save.Events]" => base64_encode("v=".base64_encode("ControlPanel:SaveEvents")),
       "[Configuration.Save.Media]" => base64_encode("v=".base64_encode("ControlPanel:SaveMedia")),
       "[Configuration.Save.Search]" => base64_encode("v=".base64_encode("ControlPanel:SaveSearch")),
       "[Configuration.Save.Statistics]" => base64_encode("v=".base64_encode("ControlPanel:SaveStatistics")),
       "[Configuration.Search]" => $search,
       "[Configuration.Search.Clone]" => base64_encode($this->core->Change([[
        "[List.Description]" => "",
        "[List.ID]" => "",
        "[List.Title]" => ""
       ], $this->core->Extension("3777f71aa914041840ead48e3a259866")])),
       "[Configuration.Statistics]" => $statistics,
       "[Configuration.Statistics.Clone]" => base64_encode($this->core->Change([[
        "[Statistic.ID]" => "",
        "[Statistic.Name]" => ""
       ], $this->core->Extension("21af4585b38e4b15a37fce7dfbb95161")]))
      ], $this->core->Extension("5c1ce5c08e2add4d1487bcd2193315a7")]);
     }
    }
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
    $domains_base = $data["Domains_Base"] ?? "outerhaven.nyc";
    $domains_fileSystem = $data["Domains_FileSystem"] ?? "efs.outerhaven.nyc";
    $keywords = $data["Keywords"] ?? $app["Keywords"];
    $name = $data["Name"] ?? $app["Name"];
    $shopID = $data["ShopID"] ?? "Mike";
    $app = [
     "Description" => $description,
     "Domains_Base" => $domains_base,
     "Domains_FileSystem" => $domains_fileSystem,
     "Keywords" => $keywords,
     "Name" => $name,
     "Search" => $search,
     "ShopID" => $shopID
    ];
    $config["App"] = $app;
    $config["Maintenance"] = $data["Maintenance"] ?? 0;
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
    $activeEventInfo = [];
    $activeEvents = 0;
    $config = $this->core->config ?? [];
    $events = $config["PublicEvents"] ?? [];
    $missingBannerInfo = 0;
    $newEvents = [];
    for($i = 0; $i < count($data["EventActive"]); $i++) {
     $check = $data["EventActive"][$i] ?? 0;
     $check2 = (!empty($data["EventBannerText"][$i]) && !empty($data["EventLink"][$i])) ? 1 : 0;
     if($activeEvents > 1 && $check == 1) {
      break;
     } elseif($check == 1 && $check2 == 0) {
      $missingBannerInfo = 1;
      break;
     } else {
      $coverPhoto = $data["EventCoverPhoto"][$i] ?? base64_encode("");
      $coverPhoto = base64_decode($coverPhoto);
      $coverPhoto = (str_ends_with($coverPhoto, ";")) ? rtrim($coverPhoto, ";") : $coverPhoto;
      $coverPhoto = explode(";", $coverPhoto);
      $newEvents[$data["EventID"][$i]] = [
       "Active" => $data["EventActive"][$i],
       "BannerText" => $data["EventBannerText"][$i],
       "CoverPhoto" => end($coverPhoto),
       "Description" => $data["EventDescription"][$i],
       "EnablePublicBroadcast" => $data["EventEnablePublicBroadcast"][$i],
       "Link" => $data["EventLink"][$i],
       "Title" => $data["EventTitle"][$i]
      ];
      if($check == 1) {
       $activeEvent = $newEvents[$data["EventID"][$i]];
       $activeEvents++;
      }
     }
    }
    $config["PublicEvents"] = $newEvents;
    if($activeEvents > 1) {
     $r = [
      "Body" => "There are currently $activeEvents active events. Please make sure only one is active, and try again."
     ];
    } elseif($missingBannerInfo == 1) {
     $r = [
      "Body" => "Active events require both Banner Text and Link to be populated."
     ];
    } else {
     if($activeEvents == 1) {
      $chat = $this->core->Data("Get", ["chat", "7216072bbd437563e692cc7ff69cdb69"]) ?? [];
      $now = $this->core->timestamp;
      $chat["Description"] = $activeEvent["Description"];
      $chat["Messages"] = [];
      $chat["Modified"] = $now;
      $chat["ModifiedBy"][$now] = $you;
      $chat["Title"] = $activeEvent["Title"];
      $this->core->Data("Save", ["chat", "7216072bbd437563e692cc7ff69cdb69", $chat]);
     }
     $this->core->Data("Save", ["app", md5("config"), $config]);
     $r = [
      "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
      "Header" => "Done"
     ];
    }
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
  function SaveFirst() {
   return $this->core->JSONResponse([
    "AccessCode" => "Denied",
    "Response" => [
     "JSON" => "",
     "Web" => [
      "Body" => "Please save the configuration and re-load the Control Panel to continue."
     ]
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
    $media = $config["Media"] ?? [];
    $newMedia = [];
    for($i = 0; $i < count($data["MediaID"]); $i++) {
     $file = $data["MediaFile"][$i] ?? base64_encode("");
     $file = base64_decode($file);
     $file = (str_ends_with($file, ";")) ? rtrim($file, ";") : $file;
     $file = explode(";", $file);
     $newMedia[$data["MediaID"][$i]] = [
      "File" => end($file),
      "Name" => $data["MediaName"][$i]
     ];
    }
    $config["Media"] = $newMedia;
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
    $app = $config["App"] ?? [];
    $newSearch = [];
    $search = $app["Search"] ?? [];
    for($i = 0; $i < count($data["ListID"]); $i++) {
     $newSearch[$data["ListID"][$i]] = [
      "Description" => $data["ListDescription"][$i],
      "Title" => $data["ListTitle"][$i]
     ];
    }
    $app["Search"] = $newSearch;
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
    $newStatistics = [];
    $statistics = $config["Statistics"] ?? [];
    for($i = 0; $i < count($data["StatisticID"]); $i++) {
     $newStatistics[$data["StatisticID"][$i]] = $data["StatisticName"][$i];
    }
    $config["Statistics"] = $newStatistics;
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
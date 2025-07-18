<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol.$_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
 Class ControlPanel extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $data): string {
   $_AddTopMargin = 1;
   $_Commands = "";
   $_Dialog = [
    "Body" => "You do not have permission to access this experience.",
    "Header" => "Unauthorized"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif($y["Rank"] == md5("High Command")) {
    $_Dialog = "";
    $_LiveView = $this->core->AESencrypt("v=".base64_encode("LiveView:CoreMedia")."&DLC=");
    $_Search = base64_encode("Search:Containers");
    $config = $this->core->config ?? [];
    $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
    $_View = $this->core->RenderView($_View);
    $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
    if($verifyPassPhrase == 1) {
     $_Dialog = "";
     $_View = "";
     $key = $data["Key"] ?? base64_encode("");
     $key = base64_decode($key);
     $secureKey = $data["SecureKey"] ?? base64_encode("");
     $secureKey = base64_decode($secureKey);
     if(md5($key) == $secureKey) {
      $_AddTopMargin = "0";
      $allowedAudio = $config["XFS"]["FT"]["A"] ?? [];
      $allowedAudioList = "";
      $allowedDocuments = $config["XFS"]["FT"]["D"] ?? [];
      $allowedDocumentsList = "";
      $allowedPhotos = $config["XFS"]["FT"]["P"] ?? [];
      $allowedPhotosList = "";
      $allowedVideos = $config["XFS"]["FT"]["V"] ?? [];
      $allowedVideosList = "";
      $base = $this->core->base;
      $defaultUI = $config["App"]["UIVariant"] ?? 0;
      $events = "";
      $eventsList = $config["PublicEvents"] ?? [];
      $mainUI = $this->core->Data("Get", ["app", md5("MainUI")]);
      $mainUIvariants = "";
      $media = "";
      $mediaList = $config["Media"] ?? [];
      $saveFirst = base64_encode("v=".base64_encode("ControlPanel:SaveFirst"));
      $search = "";
      $searchLists = $config["App"]["Search"] ?? [];
      $searchUI = $this->core->Data("Get", ["app", md5("SearchUI")]);
      $searchUIvariants = "";
      $shopID = $config["App"]["ShopID"] ?? "Mike";
      $statistics = "";
      $statisticsList = $config["Statistics"] ?? [];
      foreach($allowedAudio as $allowedAudio) {
       $allowedAudioList .= $this->core->Change([[
        "[Clone.ID]" => md5($allowedAudio),
        "[Input.Name]" => "AllowedAudio",
        "[Input.Placeholder]" => "New Extension",
        "[Input.Value]" => $allowedAudio
       ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")]);
      } foreach($allowedDocuments as $allowedDocuments) {
       $allowedDocumentsList .= $this->core->Change([[
        "[Clone.ID]" => md5($allowedDocuments),
        "[Input.Name]" => "AllowedDocuments",
        "[Input.Placeholder]" => "New Extension",
        "[Input.Value]" => $allowedDocuments
       ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")]);
      } foreach($allowedPhotos as $allowedPhotos) {
       $allowedPhotosList .= $this->core->Change([[
        "[Clone.ID]" => md5($allowedPhotos),
        "[Input.Name]" => "AllowedPhotos",
        "[Input.Placeholder]" => "New Extension",
        "[Input.Value]" => $allowedPhotos
       ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")]);
      } foreach($allowedVideos as $allowedVideos) {
       $allowedVideosList .= $this->core->Change([[
        "[Clone.ID]" => md5($allowedVideos),
        "[Input.Name]" => "AllowedVideos",
        "[Input.Placeholder]" => "New Extension",
        "[Input.Value]" => $allowedVideos
       ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")]);
      } foreach($eventsList as $event => $info) {
       $addTo = base64_encode("Set as ".$info["Title"]."'s Cover Photo:.AddTo$event");
       $coverPhoto = (!empty($info["CoverPhoto"])) ? base64_encode($info["CoverPhoto"]) : "";
       $domains_base = $config["App"]["Domains_Base"] ?? "outerhavenusa.com";
       $domains_fileSystem = $config["App"]["Domains_FileSystem"] ?? "efs.outerhavenusa.com";
       $events .= $this->core->Change([[
        "[Clone.ID]" => $event,
        "[Event.BannerText]" => $info["BannerText"],
        "[Event.Description]" => $info["Description"],
        "[Event.ID]" => $event,
        "[Event.Link]" => $info["Link"],
        "[Event.Title]" => $info["Title"],
        "[Media.Add]" => $this->core->AESencrypt("v=".base64_encode("Search:Containers")."&CARD=1&st=XFS&AddTo=$addTo&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".base64_encode($this->core->ID)),
        "[Media.File]" => $coverPhoto,
        "[Media.Input]" => "EventCoverPhoto[]",
        "[Media.Input.LiveView]" => $_LiveView
       ], $this->core->Extension("889a3f39fa958bcc2a57b2f1882198ff")]);
      } foreach($mainUI as $key => $info) {
       $mainUIvariants .= $this->core->Change([[
        "[Clone.ID]" => md5(uniqid($key)),
        "[UI.Body]" => base64_decode($info["UI"]),
        "[UI.Description]" => $info["Description"],
        "[UI.ID]" => $info["ID"],
        "[UI.Name]" => "MainUI"
       ], $this->core->Extension("b20f28260e3e37e0092a019849960f13")]);
      } foreach($mediaList as $key => $info) {
       $addTo = base64_encode("Attach to ".str_replace(":", "&colon;", $info["Name"]).":.AddTo$key");
       $file = (!empty($info["File"])) ? base64_encode($info["File"]) : "";
       $media .= $this->core->Change([[
        "[Clone.ID]" => $key,
        "[Media.Add]" => $this->core->AESencrypt("v=".base64_encode("Search:Containers")."&lPG=Files&st=XFS&AddTo=$addTo&UN=".base64_encode($this->core->ID)),
        "[Media.File]" => $file,
        "[Media.ID]" => $key,
        "[Media.Input]" => "MediaFile[]",
        "[Media.Input.LiveView]" => $_LiveView,
        "[Media.Name]" => $info["Name"]
       ], $this->core->Extension("f1a8c31050b241ebcea22f33cf6171f4")]);
      } foreach($searchLists as $list => $info) {
       $search .= $this->core->Change([[
        "[Clone.ID]" => $list,
        "[List.Description]" => $info["Description"],
        "[List.ID]" => $list,
        "[List.Title]" => $info["Title"]
       ], $this->core->Extension("3777f71aa914041840ead48e3a259866")]);
      } foreach($searchUI as $key => $info) {
       $searchUIvariants .= $this->core->Change([[
        "[Clone.ID]" => md5(uniqid($key)),
        "[UI.Body]" => base64_decode($info["UI"]),
        "[UI.Description]" => $info["Description"],
        "[UI.ID]" => $info["ID"],
        "[UI.Name]" => "SearchUI"
       ], $this->core->Extension("b20f28260e3e37e0092a019849960f13")]);
      } foreach($statisticsList as $stat => $name) {
       $statistics .= $this->core->Change([[
        "[Clone.ID]" => $stat,
        "[Statistic.ID]" => $stat,
        "[Statistic.Name]" => $name
       ], $this->core->Extension("21af4585b38e4b15a37fce7dfbb95161")]);
      }
      $_Commands = [
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         ".AppConfiguration",
         [
          [
           "Attributes" => [
            "name" => "Name",
            "placeholder" => "The Everything App",
            "type" => "text"
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "Name"
           ],
           "Type" => "Text",
           "Value" => $this->core->AESencrypt($config["App"]["Name"])
          ],
          [
           "Attributes" => [
            "name" => "Description",
            "placeholder" => "The go to platform for everything."
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "Description"
           ],
           "Type" => "TextBox",
           "Value" => $this->core->AESencrypt($config["App"]["Description"])
          ],
          [
           "Attributes" => [
            "name" => "Keywords",
            "placeholder" => "Constitutional, Social, Media, Free, Speech"
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "Keywords"
           ],
           "Type" => "TextBox",
           "Value" => $this->core->AESencrypt($config["App"]["Keywords"])
          ],
          [
           "Attributes" => [],
           "OptionGroup" => [
            "0" => "Off",
            "1" => "On"
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "Maintenance"
           ],
           "Name" => "Maintenance",
           "Type" => "Select",
           "Value" => $config["Maintenance"]
          ],
          [
           "Attributes" => [
            "name" => "ShopID",
            "placeholder" => "Mike"
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "Shop Identifier"
           ],
           "Type" => "Text",
           "Value" => $this->core->AESencrypt($shopID)
          ],
          [
           "Attributes" => [
            "class" => "PersonalUIVariant",
            "name" => "UIVariant",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $defaultUI
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         ".AppDomains",
         [
          [
           "Attributes" => [
            "name" => "Domains_Base",
            "placeholder" => "outerhavenusa.com"
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "Main"
           ],
           "Type" => "Text",
           "Value" => $this->core->AESencrypt($domains_base)
          ],
          [
           "Attributes" => [
            "name" => "Domains_FileSystem",
            "placeholder" => "media.outerhavenusa.com"
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "File System"
           ],
           "Type" => "Text",
           "Value" => $this->core->AESencrypt($domains_fileSystem)
          ]
         ]
        ]
       ]
      ];
      $_View = [
       "ChangeData" => [
        "[Admin.Databases]" => "W('$base/phpmyadmin', '_blank');",
        "[Admin.Domain]" => "W('https://www.godaddy.com/', '_blank');",
        "[Admin.Feedback]" => base64_encode("v=$_Search&st=Feedback"),
        "[Admin.Files]" => base64_encode("v=".base64_encode("Album:List")."&AID=".md5("unsorted")."&UN=".base64_encode($this->core->ID)),
        "[Admin.Mail]" => "https://box.outerhavenusa.com/iredadmin/",
        "[Admin.Pages]" => base64_encode("v=$_Search&CARD=1&st=ADM-LLP"),
        "[Admin.RenewSubscriptions]" => base64_encode("v=".base64_encode("Subscription:RenewAll")),
        "[Admin.Server]" => "https://aws.amazon.com/",
        "[Configuration.App.UploadLimits]" => json_encode($config["XFS"], true),
        "[Configuration.App.UploadLimits.Audio]" => $config["XFS"]["limits"]["Audio"],
        "[Configuration.App.UploadLimits.Documents]" => $config["XFS"]["limits"]["Documents"],
        "[Configuration.App.UploadLimits.Photos]" => $config["XFS"]["limits"]["Images"],
        "[Configuration.App.UploadLimits.Total]" => $config["XFS"]["limits"]["Total"],
        "[Configuration.App.UploadLimits.Videos]" => $config["XFS"]["limits"]["Videos"],
        "[Configuration.App.UIVariants]" => $this->core->Extension("4d3675248e05b4672863c6a7fd1df770"),
        "[Configuration.App.SearchUI]" => $searchUIvariants,
        "[Configuration.App.SearchUI.Clone]" => base64_encode($this->core->Change([[
         "[UI.Body]" => "",
         "[UI.Description]" => "",
         "[UI.ID]" => "",
         "[UI.Name]" => "SearchUI"
        ], $this->core->Extension("b20f28260e3e37e0092a019849960f13")])),
        "[Configuration.App.UI]" => $mainUIvariants,
        "[Configuration.App.UI.Clone]" => base64_encode($this->core->Change([[
         "[UI.Body]" => "",
         "[UI.Description]" => "",
         "[UI.ID]" => "",
         "[UI.Name]" => "MainUI"
        ], $this->core->Extension("b20f28260e3e37e0092a019849960f13")])),
        "[Configuration.Events]" => $events,
        "[Configuration.Events.Clone]" => base64_encode($this->core->Change([[
         "[Event.BannerText]" => "",
         "[Event.Description]" => "",
         "[Event.ID]" => "",
         "[Event.Link]" => "",
         "[Event.Title]" => "",
         "[Media.Add]" => $saveFirst,
         "[Media.File]" => "",
         "[Media.Input]" => "EventCoverPhoto[]",
         "[Media.Input.LiveView]" => $_LiveView
        ], $this->core->Extension("889a3f39fa958bcc2a57b2f1882198ff")])),
        "[Configuration.FileSystem.AllowedAudio]" => $allowedAudioList,
        "[Configuration.FileSystem.AllowedAudio.Clone]" => base64_encode($this->core->Change([[
         "[Input.Name]" => "AllowedAudio",
         "[Input.Placeholder]" => "New Extension",
         "[Input.Value]" => ""
        ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")])),
        "[Configuration.FileSystem.AllowedDocuments]" => $allowedDocumentsList,
        "[Configuration.FileSystem.AllowedDocuments.Clone]" => base64_encode($this->core->Change([[
         "[Input.Name]" => "AllowedDocuments",
         "[Input.Placeholder]" => "New Extension",
         "[Input.Value]" => ""
        ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")])),
        "[Configuration.FileSystem.AllowedPhotos]" => $allowedPhotosList,
        "[Configuration.FileSystem.AllowedPhotos.Clone]" => base64_encode($this->core->Change([[
         "[Input.Name]" => "AllowedPhotos",
         "[Input.Placeholder]" => "New Extension",
         "[Input.Value]" => ""
        ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")])),
        "[Configuration.FileSystem.AllowedVideos]" => $allowedVideosList,
        "[Configuration.FileSystem.AllowedVideos.Clone]" => base64_encode($this->core->Change([[
         "[Input.Name]" => "AllowedVideos",
         "[Input.Placeholder]" => "New Extension",
         "[Input.Value]" => ""
        ], $this->core->Extension("da548e440d656beaafeba4b155bf058a")])),
        "[Configuration.Media]" => $media,
        "[Configuration.Media.Clone]" => base64_encode($this->core->Change([[
         "[Media.Add]" => $saveFirst,
         "[Media.File]" => "",
         "[Media.Input]" => "MediaFile[]",
         "[Media.Input.LiveView]" => $_LiveView,
         "[Media.ID]" => "",
         "[Media.Name]" => ""
        ], $this->core->Extension("f1a8c31050b241ebcea22f33cf6171f4")])),
        "[Configuration.Save.App]" => $this->core->AESencrypt("v=".base64_encode("ControlPanel:SaveApp")),
        "[Configuration.Save.Events]" => $this->core->AESencrypt("v=".base64_encode("ControlPanel:SaveEvents")),
        "[Configuration.Save.Media]" => $this->core->AESencrypt("v=".base64_encode("ControlPanel:SaveMedia")),
        "[Configuration.Save.Search]" => $this->core->AESencrypt("v=".base64_encode("ControlPanel:SaveSearch")),
        "[Configuration.Save.Statistics]" => $this->core->AESencrypt("v=".base64_encode("ControlPanel:SaveStatistics")),
        "[Configuration.Save.UI]" => $this->core->AESencrypt("v=".base64_encode("ControlPanel:SaveUI")),
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
        ], $this->core->Extension("21af4585b38e4b15a37fce7dfbb95161")])),
       ],
       "ExtensionID" => "5c1ce5c08e2add4d1487bcd2193315a7"
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function SaveApp(array $data): string {
   $_Dialog = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif($y["Rank"] == md5("High Command")) {
    $config = $this->core->config ?? [];
    $app = $config["App"] ?? [];
    $search = $app["Search"] ?? [];
    $description = $data["Description"] ?? $app["Description"];
    $domains_base = $data["Domains_Base"] ?? "outerhavenusa.com";
    $domains_fileSystem = $data["Domains_FileSystem"] ?? "media.outerhavenusa.com";
    $domains_mailService = $data["Domains_MailService"] ?? "box.outerhavenusa.com:1776";
    $keywords = $data["Keywords"] ?? $app["Keywords"];
    $name = $data["Name"] ?? $app["Name"];
    $newAudio = [];
    $newDocuments = [];
    $newPhotos = [];
    $newVideos = [];
    $setUIVariant = $data["UIVariant"] ?? 0;
    $shopID = $data["ShopID"] ?? "Mike";
    for($i = 0; $i < count($data["AllowedAudio"]); $i++) {
     array_push($newAudio, $data["AllowedAudio"][$i]);
    } for($i = 0; $i < count($data["AllowedDocuments"]); $i++) {
     array_push($newDocuments, $data["AllowedDocuments"][$i]);
    } for($i = 0; $i < count($data["AllowedPhotos"]); $i++) {
     array_push($newPhotos, $data["AllowedPhotos"][$i]);
    } for($i = 0; $i < count($data["AllowedVideos"]); $i++) {
     array_push($newVideos, $data["AllowedVideos"][$i]);
    }
    $app = [
     "Description" => $description,
     "Domains_Base" => $domains_base,
     "Domains_FileSystem" => $domains_fileSystem,
     "Domains_MailService" => $domains_mailService,
     "Keywords" => $keywords,
     "Name" => $name,
     "Search" => $search,
     "ShopID" => $shopID,
     "UIVariant" => $setUIVariant
    ];
    $config["App"] = $app;
    $config["Maintenance"] = $data["Maintenance"] ?? 0;
    $config["XFS"]["FT"]["A"] = $newAudio;
    $config["XFS"]["FT"]["D"] = $newDocuments;
    $config["XFS"]["FT"]["P"] = $newPhotos;
    $config["XFS"]["FT"]["V"] = $newVideos;
    $config["XFS"]["limits"]["Audio"] = $data["UploadLimits_Audio"];
    $config["XFS"]["limits"]["Documents"] = $data["UploadLimits_Documents"];
    $config["XFS"]["limits"]["Images"] = $data["UploadLimits_Images"];
    $config["XFS"]["limits"]["Total"] = $data["UploadLimits_Total"];
    $config["XFS"]["limits"]["Videos"] = $data["UploadLimits_Videos"];
    $this->core->Data("Save", ["app", md5("config"), $config]);
    $_Dialog = [
     "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveEvents(array $data): string {
   $_Dialog = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
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
     $_Dialog = [
      "Body" => "There are currently $activeEvents active events. Please make sure only one is active, and try again."
     ];
    } elseif($missingBannerInfo == 1) {
     $_Dialog = [
      "Body" => "Active events require both Banner Text and Link to be populated."
     ];
    } elseif($activeEvents == 1) {
     $chat = $this->core->Data("Get", ["chat", "7216072bbd437563e692cc7ff69cdb69"]);
     $now = $this->core->timestamp;
     $chat["Description"] = $activeEvent["Description"];
     $chat["Messages"] = [];
     $chat["Modified"] = $now;
     $chat["ModifiedBy"][$now] = $you;
     $chat["Title"] = $activeEvent["Title"];
     $this->core->Data("Save", ["chat", "7216072bbd437563e692cc7ff69cdb69", $chat]);
    }
    $this->core->Data("Save", ["app", md5("config"), $config]);
    $_Dialog = [
     "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveFirst(): string {
   return $this->core->JSONResponse([
    "Dialog" => [
     "Body" => "Please save the configuration and reload the Control Panel first.",
     "Header" => "Action Required"
    ]
   ]);
  }
  function SaveMedia(array $data): string {
   $_Dialog = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif($y["Rank"] == md5("High Command")) {
    $config = $this->core->config ?? [];
    $media = $config["Media"] ?? [];
    $newMedia = [];
    for($i = 0; $i < count($data["MediaID"]); $i++) {
     $file = $data["MediaFile"][$i] ?? base64_encode("");
     $file = base64_decode($file);
     $newMedia[$data["MediaID"][$i]] = [
      "File" => $file,
      "Name" => $data["MediaName"][$i]
     ];
    }
    $config["Media"] = $newMedia;
    $this->core->Data("Save", ["app", md5("config"), $config]);
    $_Dialog = [
     "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveSearch(array $data): string {
   $_Dialog = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
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
    $_Dialog = [
     "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveStatistics(array $data): string {
   $_Dialog = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
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
    $_Dialog = [
     "Body" => "The <em>".$config["App"]["Name"]."</em> configuration was updated!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveUI(array $data): string {
   $_Dialog = [
    "Body" => "You do not have permission to access this resource.",
    "Header" => "Unauthorized"
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif($y["Rank"] == md5("High Command")) {
    $newUI = [];
    $newSearchUI = [];
    for($i = 0; $i < count($data["MainUIID"]); $i++) {
     array_push($newUI, [
      "Description" => $data["MainUIDescription"][$i],
      "ID" => $data["MainUIID"][$i],
      "UI" => base64_encode($data["MainUIBody"][$i])
     ]);
    } for($i = 0; $i < count($data["SearchUIID"]); $i++) {
     array_push($newSearchUI, [
      "Description" => $data["SearchUIDescription"][$i],
      "ID" => $data["SearchUIID"][$i],
      "UI" => base64_encode($data["SearchUIBody"][$i])
     ]);
    }
    $this->core->Data("Save", ["app", md5("MainUI"), $newUI]);
    $this->core->Data("Save", ["app", md5("SearchUI"), $newSearchUI]);
    $_Dialog = [
     "Body" => "The UI Variants were updated!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
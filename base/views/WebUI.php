<?php
 Class WebUI extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Attachments(array $data): string {
   $_View = $this->core->Element(["p", "The content identifier is missing."]);
   $header = $data["Header"] ?? "";
   $id = $data["ID"] ?? "";
   $media = $data["Media"] ?? [];
   $parentContentID = $data["ParentContentID"] ?? "";
   $uiid = $this->core->UUID($id);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(empty($media)) {
    $_View = $this->core->Element(["p", "The media identifiers are missing."]);
   } else {
    $_SymbolicLink = "v=".base64_encode("Search:Containers")."&AddTo=[Link.AddTo]&CARD=1&st=";
    $_Translate = "";
    $_ViewDesign = "";
    $clone = $this->core->Element([
     "div", $this->core->Element([
      "button", "X", [
       "class" => "Delete v1",
       "data-target" => ".[Clone.ID]"
      ]
     ]).$this->core->Element([
      "div", "[Clone.Content]", [
       "class" => "NONAME"
      ]
     ]), [
      "class" => "[Clone.ID]"
     ]
    ]);
    $mediaUI = $this->core->Extension("02ec63fe4f0fffe5e6f17621eb3b50ad");
    $_View = (!empty($header)) ? $this->core->Element(["h3", $header]) : "";
    $section = $this->core->Element([
     "div", NULL, ["class" => "NONAME"]
    ]).$this->core->Element(["button", "<h4>[Section.Name]</h4>", [
     "class" => "PSAccordion",
     "data-type" => ".ContentAttachments$uiid;.AttachmentType;.AttachmentGroup[Section.ID]"
    ]]).$this->core->Element(["div", "[Section.Content]", [
     "class" => "AttachmentGroup[Section.ID] AttachmentType NONAME h"
    ]]);
    $symbolicLinks = [
     "Album" => "MBR-ALB&UN=".base64_encode($you),
     "Article" => "CA",
     "Blog" => "BLG",
     "BlogPost" => "BGP",
     "BundledProduct" => "SHOP-Products&UN=".base64_encode($parentContentID),
     "Chat" => "Chat",
     "Default" => "XFS&lPG=Files&UN=".base64_encode($you),
     "Forum" => "Forums",
     "ForumPost" => "Forums-Posts",
     "Member" => "MBR",
     "Poll" => "Polls",
     "Product" => "Products",
     "Shop" => "SHOP",
     "Update" => "StatusUpdates"
    ];
    foreach($media as $key => $attachments) {
     if($key == "Translate") {
      $_Translate = $this->core->Element([
       "div", NULL, ["class" => "Translate$id"]
      ]).$this->core->Element([
        "script", "UpdateContent(\".Translate$id\", \"".base64_encode("v=".base64_encode("Translate:Edit")."&ID=".base64_encode($id))."\");"
      ]);
     } if($key == "ViewDesign") {
      $_ViewDesign = $this->core->Change([[
       "[DesignView.ID]" => $id,
       "[DesignView.Processor]" => base64_encode("v=".base64_encode("WebUI:DesignView")."&DV=")
      ], $this->core->Extension("14a059ccc9de46edbfca01da0e06b12f")]);
     } else {
      $mediaList = "";
      $mediaInput = ($key == "CoverPhoto") ? "CoverPhoto" : $key."[]";
      $mediaType = $key;
      $sectionName = $key;
      $sectionName = ($key == "Album") ? "Albums" : $sectionName;
      $sectionName = ($key == "Article") ? "Articles" : $sectionName;
      $sectionName = ($key == "Attachment") ? "Attachments" : $sectionName;
      $sectionName = ($key == "Blog") ? "Blogs" : $sectionName;
      $sectionName = ($key == "BlogPost") ? "Blog Posts" : $sectionName;
      $sectionName = ($key == "BundledProduct") ? "Bundled Products" : $sectionName;
      $sectionName = ($key == "Chat") ? "Chats" : $sectionName;
      $sectionName = ($key == "CoverPhoto") ? "Cover Photo" : $sectionName;
      $sectionName = ($key == "DemoFile") ? "Demo Files" : $sectionName;
      $sectionName = ($key == "Forum") ? "Forums" : $sectionName;
      $sectionName = ($key == "ForumPost") ? "Forum Posts" : $sectionName;
      $sectionName = ($key == "Member") ? "Members" : $sectionName;
      $sectionName = ($key == "Poll") ? "Polls" : $sectionName;
      $sectionName = ($key == "Product") ? "Products" : $sectionName;
      $sectionName = ($key == "Shop") ? "Shops" : $sectionName;
      $sectionName = ($key == "Update") ? "Status Updates" : $sectionName;
      if($key == "CoverPhoto") {
       $addMedia = $symbolicLinks[$mediaType] ?? $symbolicLinks["Default"];
       $cloneID = $this->core->UUID("AttachmentMedia".rand(100, 999));
       $addTo = base64_encode("Attach:.AddTo$cloneID");
       $addMedia = str_replace("[Link.AddTo]", $addTo, $_SymbolicLink.$addMedia);
       $liveView = base64_encode("v=".base64_encode("LiveView:Editor")."&MediaType=".base64_encode($key)."&Media=");
       $mediaList .= $this->core->Change([[
        "[Clone.ID]" => $cloneID,
        "[Media.Add]" => base64_encode($addMedia),
        "[Media.File]" => $attachments,
        "[Media.ID]" => $cloneID,
        "[Media.Input]" => $mediaInput,
        "[Media.Input.LiveView]" => $liveView,
        "[Media.Name]" => $mediaType
       ], $mediaUI]);
      } else {
       $mediaCount = count($attachments);
       for($i = 0; $i < $mediaCount; $i++) {
        $addMedia = $symbolicLinks[$mediaType] ?? $symbolicLinks["Default"];
        $cloneID = $this->core->UUID("AttachmentMedia".rand(100, 999));
        $addTo = base64_encode("Attach:.AddTo$cloneID");
        $addMedia = str_replace("[Link.AddTo]", $addTo, $_SymbolicLink.$addMedia);
        $liveView = base64_encode("v=".base64_encode("LiveView:Editor")."&MediaType=".base64_encode($key)."&Media=");
        $mediaList .= $this->core->Change([[
         "[Clone.Content]" => $this->core->Change([[
          "[Clone.ID]" => $cloneID,
          "[Media.Add]" => base64_encode($addMedia),
          "[Media.File]" => $attachments[$i],
          "[Media.ID]" => $cloneID,
          "[Media.Input]" => $mediaInput,
          "[Media.Input.LiveView]" => $liveView,
          "[Media.Name]" => $mediaType
         ], $mediaUI]),
         "[Clone.ID]" => $cloneID
        ], $clone]);
       }
      }
      $addTo = base64_encode("Attach:.AddTo[Clone.ID]");
      $addMedia = $symbolicLinks[$mediaType] ?? $symbolicLinks["Default"];
      $addMedia = str_replace("[Link.AddTo]", $addTo, $_SymbolicLink.$addMedia);
      $cloneSourceID = $this->core->UUID("CloneSource".md5($key));
      $liveView = base64_encode("v=".base64_encode("LiveView:Editor")."&MediaType=".base64_encode($key)."&Media=");
      $mediaListID = $this->core->UUID("MediaList".md5($key));
      $mediaListIDSS = ($key != "CoverPhoto") ? "$mediaListID SideScroll" : $mediaListID;
      $mediaClone = $this->core->Change([[
       "[Media.Add]" => base64_encode($addMedia),
       "[Media.File]" => "",
       "[Media.ID]" => "[Clone.ID]",
       "[Media.Input]" => $mediaInput,
       "[Media.Input.LiveView]" => $liveView,
       "[Media.Name]" => $mediaType
      ], $mediaUI]);
      $mediaClone = ($mediaType != "CoverPhoto") ? $this->core->Change([[
       "[Clone.Content]" => $mediaClone
      ], $clone]) : $mediaClone;
      $removeAfterUse = ($mediaType == "CoverPhoto") ? "on" : "off";
      $mediaList = $this->core->Element(["div", $mediaList, [
       "class" => $mediaListIDSS
      ]]).$this->core->Element(["div", base64_encode($mediaClone), [
       "class" => "$cloneSourceID h"
      ]]);
      $mediaList .= ($key != "CoverPhoto") ? $this->core->Element([
       "button", "Add Media", [
        "class" => "CloneAttachments v2 v2w",
        "data-destination" => ".$mediaListID",
        "data-remove" => $removeAfterUse,
        "data-source" => ".$cloneSourceID"
       ]
      ]) : "";
      if(!in_array($key, ["Translate", "ViewDesign"])) {
       $_View .= $this->core->Change([[
        "[Section.Content]" => $mediaList,
        "[Section.ID]" => $uiid.$mediaType,
        "[Section.Name]" => $sectionName
       ], $section]);
      }
     }
    }
    $_View .= (!empty($_Translate)) ? $this->core->Change([[
     "[Section.Content]" => $_Translate,
     "[Section.ID]" => $uiid."Translate",
     "[Section.Name]" => "Translate"
    ], $section]) : "";
    $_View .= (!empty($_ViewDesign)) ? $this->core->Change([[
     "[Section.Content]" => $_ViewDesign,
     "[Section.ID]" => $uiid."ViewDesign",
     "[Section.Name]" => "View Design"
    ], $section]) : "";
    $_View = $this->core->Element([
     "div", $_View, [
      "class" => "ContentAttachments$uiid NONAME"
     ]
    ]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function DesignView(array $data): string {
   $data = $data["Data"] ?? [];
   $designView = $data["DV"] ?? "";
   $_View = (!empty($designView)) ? $this->core->PlainText([
    "BBCodes" => 1,
    "Data" => $designView,
    "Decode" => 1,
    "Display" => 1,
    "HTMLDecode" => 1
   ]) : $this->core->Element([
    "p", "Add content to reveal its design...", ["class" => "CenterText"]
   ]);
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "ChangeData" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function Error(array $data): string {
   $data = $data["Data"] ?? [];
   $error = $data["Error"] ?? "";
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "h1", "Something went wrong...", ["class" => "UpperCase"]
     ]).$this->core->Element([
      "p", $error
     ]))
    ]
   ]);
  }
  function Extensions(array $data): string {
   $clientExtensions = [];
   $data = $data["Data"] ?? [];
   $doNotIndex = [
    "45787465-6e73-496f-ae42-794d696b65-67abee895c024",
    "45787465-6e73-496f-ae42-794d696b65-67fa6b4a2b998",
    "45787465-6e73-496f-ae42-794d696b65-67fa6b71bda8b",
    "97291f4b155f663aa79cc8b624323c5b",
    "d4efcd44be4b2ef2a395f0934a9e446a",
    "5b22de694d66b763c791395da1de58e1"
   ];
   $id = $data["ID"] ?? base64_encode("");
   $id = base64_decode($id);
   if(!empty($id)) {
    if(!in_array($id, $doNotIndex)) {
     $extension = $this->core->Data("Get", ["extension", $id]);
     $data = $extension["Body"] ?? "";
     $data = (!empty($data)) ? $this->core->PlainText([
      "Data" => $data,
      "Display" => 1,
      "HTMLDecode" => 1
     ]) : $this->core->Change([
      "p", "The Extension <em>$id</em> could not be found."
     ]);
     $clientExtensions = [
      "Data" => $this->core->AESencrypt($data),
      "ID" => $id
     ];
    } else {
     $clientExtensions = [
      "Data" => $this->core->AESencrypt("Invalid Extension <em>$id</em>."),
      "ID" => $id
     ];
    }
   } else {
    $extensions = $this->core->DatabaseSet("Extensions");
    foreach($extensions as $key => $id) {
     $id = str_replace("nyc.outerhaven.extension.", "", $id);
     if(!in_array($id, $doNotIndex)) {
      $extension = $this->core->Data("Get", ["extension", $id]);
      $data = $extension["Body"] ?? "";
      $data = (!empty($data)) ? $this->core->PlainText([
       "Data" => $data,
       "Display" => 1,
       "HTMLDecode" => 1
      ]) : $this->core->Change([
       "p", "The Extension <em>$id</em> could not be found."
      ]);
      array_push($clientExtensions, [
       "Data" => $this->core->AESencrypt($data),
       "ID" => $id
      ]);
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "JSON" => $clientExtensions
   ]);
  }
  function Gateway(): string {
   $eventMedia = $this->core->RenderEventMedia();
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [
      "[Gateway.Company]" => $this->core->AESencrypt("v=".base64_encode("Company:Home")."&Card=1"),
      "[Gateway.Architecture]" => $this->core->AESencrypt("v=".base64_encode("Company:VVA")."&CARD=1"),
      "[Gateway.Banner]" => $eventMedia["Banner"],
      "[Gateway.CoverPhoto]" => $eventMedia["CoverPhoto"],
      "[Gateway.IT]" => $this->core->AESencrypt("v=".base64_encode("Shop:Home")."&CARD=1&UN=".base64_encode($this->core->ShopID)),
      "[Gateway.SignIn]" => $this->core->AESencrypt("v=".base64_encode("Profile:SignIn")),
      "[Gateway.SignUp]" => $this->core->AESencrypt("v=".base64_encode("Profile:SignUp"))
     ],
     "ExtensionID" => "db69f503c7c6c1470bd9620b79ab00d7"
    ]
   ]);
  }
  function Landing(): string {
   $content = "v=".base64_encode("WebUI:Gateway");
   $headers = apache_request_headers();
   $language = $headers["Language"] ?? $this->core->language;
   $setUIvariant = 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID != $you) {
    $content = "v=".base64_encode("Search:Containers")."&st=Mainstream";
    $shop = $this->core->Data("Get", ["shop", md5($you)]);
    foreach($y["Subscriptions"] as $subscription => $info) {
     if(strtotime($info["B"]) > $info["E"]) {
      $info["A"] = 0;
     } if($subscription == "Artist") {
      $shop["Open"] = $info["A"] ?? 0;
     } elseif($subscription == "VIP") {
      $highCommand = ($y["Rank"] == md5("High Command")) ? 1 : 0;
      $sonsOfLiberty = "cb3e432f76b38eaa66c7269d658bd7ea";
      $manifest = $this->core->Data("Get", [
       "pfmanifest",
       $sonsOfLiberty
      ]) ?? [];
      if($info["A"] == 1) {
       $role = ($highCommand == 1) ? "Admin" : "Member";
       $manifest[$you] = $role;
      } elseif($info["A"] == 0 && $highCommand == 0) {
       $newManifest = [];
       foreach($manifest as $member => $role) {
        if($member != $you) {
         $newManifest[$member] = $role;
        }
       }
       $manifest = $newManifest;
      }
      $this->core->Data("Save", ["pfmanifest", $sonsOfLiberty, $manifest]);
     }
    }
    $setUIvariant = $y["Personal"]["UIVariant"] ?? 0;
    $y["Inactive"] = 0;
    $y["Personal"]["Language"] = $language;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $this->core->Data("Save", ["shop", md5($you), $shop]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => [
     [
      "Name" => "AddContent"
     ],
     [
      "Name" => "Bulletins"
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".Content",
       $this->core->AESencrypt($content)
      ]
     ],
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".Menu",
       $this->core->AESencrypt("v=".base64_encode("WebUI:Menu")),
       6000
      ]
     ]
    ],
    "SetUIVariant" => $setUIvariant,
    "View" => [
     "ChangeData" => [
      "[App.Search]" => $this->core->AESencrypt("v=".base64_encode("Search:ReSearch")."&query=")
     ],
     "Extension" => $this->core->AESencrypt($this->core->RenderUI("Main"))
    ]
   ]);
  }
  function Menu(): string {
   $search = base64_encode("Search:Containers");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $changeData = [
     "[Menu.Company.Feedback]" => $this->core->AESencrypt("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.FreeAmericaRadio]" => $this->core->AESencrypt("v=".base64_encode("Company:FreeAmericaRadio")),
     "[Menu.Company.Home]" => $this->core->AESencrypt("v=".base64_encode("Company:Home")),
     "[Menu.Company.IncomeDisclosure]" => $this->core->AESencrypt("v=".base64_encode("Revenue:Home")."&Shop=".base64_encode($this->core->ShopID)),
     "[Menu.Company.Portfolio]" => $this->core->AESencrypt("v=".base64_encode("Company:Portfolio")),
     "[Menu.Company.PressReleases]" => $this->core->AESencrypt("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => $this->core->AESencrypt("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => $this->core->AESencrypt("v=".base64_encode("Company:VVA")),
     "[Menu.Gateway]" => $this->core->AESencrypt("v=".base64_encode("WebUI:Gateway")),
     "[Menu.LostAndFound]" => $this->core->AESencrypt("v=".base64_encode("LostAndFound:Home")),
     "[Menu.Mainstream]" => $this->core->AESencrypt("v=$search&st=Mainstream"),
     "[Menu.MiNY]" => $this->core->AESencrypt("v=".base64_encode("Shop:MadeInNewYork")),
     "[Menu.OptIn]" => $this->core->AESencrypt("v=".base64_encode("WebUI:Gateway")),
     "[Menu.SignIn]" => $this->core->AESencrypt("v=".base64_encode("Profile:SignIn")),
     "[Menu.SignUp]" => $this->core->AESencrypt("v=".base64_encode("Profile:SignUp")),
     "[Menu.SwitchLanguages]" => $this->core->AESencrypt("v=".base64_encode("WebUI:SwitchLanguages"))
    ];
    $extensionID = "73859ffa637c369b9fa88399a27b5598";
   } else {
    $admin = ($y["Rank"] == md5("High Command")) ? $this->core->Element([
     "button", "Control Panel", [
      "class" => "CloseNetMap LI UpdateContent",
      "data-encryption" => "AES",
      "data-view" => $this->core->AESencrypt("v=".base64_encode("ControlPanel:Home"))
     ]
    ]) : "";
    $bulletinBadge = $this->view(base64_encode("Profile:Bulletins"), []);
    $bulletinBadge = $this->core->RenderView($bulletinBadge);
    $bulletinBadge = ($bulletinBadge > 0) ? $this->core->Element([
     "span", "&nbsp; $bulletinBadge &nbsp;", [
      "class" => "Red Right v2",
      "style" => "margin:0em;padding:0em"
     ]
    ]) : "";
    $i = 0;
    $subscriptionsList = "";
    $verified = $y["Verified"] ?? 0;
    $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
    foreach($y["Subscriptions"] as $key => $value) {
     $subscription = $this->core->config["Subscriptions"][$key] ?? [];
     if(!empty($subscription)) {
      $i++;
      $subscriptionsList .= $this->core->Element(["button", $subscription["Title"], [
       "class" => "LI OpenCard",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Subscription:Home")."&sub=".base64_encode($key))
      ]]);
     }
    } if($i > 0) {
     $subscriptions = $this->core->Element([
      "h3", "Subscriptions"
     ]);
     $subscriptions .= $subscriptionsList;
     $subscriptions = $this->core->Element([
      "div", $subscriptions, ["class" => "Desktop33 Medium SideBarFull scr"]
     ]);
    }
    $changeData = [
     "[Menu.Administration]" => $admin,
     "[Menu.BulletinBadge]" => $bulletinBadge,
     "[Menu.Company.Feedback]" => $this->core->AESencrypt("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.FreeAmericaRadio]" => $this->core->AESencrypt("v=".base64_encode("Company:FreeAmericaRadio")),
     "[Menu.Company.Home]" => $this->core->AESencrypt("v=".base64_encode("Company:Home")),
     "[Menu.Company.IncomeDisclosure]" => $this->core->AESencrypt("v=".base64_encode("Revenue:Home")."&Shop=".base64_encode($this->core->ShopID)),
     "[Menu.Company.Portfolio]" => $this->core->AESencrypt("v=".base64_encode("Company:Portfolio")),
     "[Menu.Company.PressReleases]" => $this->core->AESencrypt("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => $this->core->AESencrypt("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => $this->core->AESencrypt("v=".base64_encode("Company:VVA")),
     "[Menu.Congress]" => $this->core->AESencrypt("v=".base64_encode("Congress:Home")),
     "[Menu.Mainstream]" => $this->core->AESencrypt("v=$search&st=Mainstream"),
     "[Menu.Member.Articles]" => $this->core->AESencrypt("v=$search&st=MBR-LLP"),
     "[Menu.Member.Blacklist]" => $this->core->AESencrypt("v=".base64_encode("Profile:Blacklists")),
     "[Menu.Member.Blogs]" => $this->core->AESencrypt("v=$search&st=MBR-BLG"),
     "[Menu.Member.BulletinCenter]" => $this->core->AESencrypt("v=".base64_encode("Profile:BulletinCenter")),
     "[Menu.Member.Chat]" => $this->core->AESencrypt("v=".base64_encode("Chat:Menu")."&Integrated=1"),
     "[Menu.Member.Contacts]" => $this->core->AESencrypt("v=$search&st=Contacts"),
     "[Menu.Member.DisplayName]" => $y["Personal"]["DisplayName"].$verified,
     "[Menu.Member.Files]" => $this->core->AESencrypt("v=$search&st=XFS"),
     "[Menu.Member.Forums]" => $this->core->AESencrypt("v=$search&lPG=MBR-Forums&st=MBR-Forums"),
     "[Menu.Member.Polls]" => $this->core->AESencrypt("v=$search&st=MBR-Polls"),
     "[Menu.Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:10%;width:80%"),
     "[Menu.Member.Library]" => $this->core->AESencrypt("v=$search&UN=".base64_encode($you)."&lPG=MediaLib&st=MBR-ALB"),
     "[Menu.Member.Preferences]" => $this->core->AESencrypt("v=".base64_encode("Profile:Preferences")),
     "[Menu.Member.Profile]" => $this->core->AESencrypt("v=".base64_encode("Profile:Home")."&UN=".base64_encode($you)),
     "[Menu.Member.Username]" => $you,
     "[Menu.MiNY]" => $this->core->AESencrypt("v=".base64_encode("Shop:MadeInNewYork")),
     "[Menu.MiNY.History]" => $this->core->AESencrypt("v=".base64_encode("Shop:History")."&ID=".md5($this->core->ShopID)),
     "[Menu.Search.Chat]" => $this->core->AESencrypt("v=$search&Integrated=1&lPG=Chat&st=Chat"),
     "[Menu.SwitchLanguages]" => $this->core->AESencrypt("v=".base64_encode("WebUI:SwitchLanguages")),
     "[Menu.Subscriptions]" => $subscriptions
    ];
    $extensionID = "d14e3045df35f4d9784d45ac2c0fe73b";
   }
   $coverPhoto = $y["Personal"]["CoverPhoto"] ?? "";
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => [
     [
      "Name" => "UpdateCoverPhoto",
      "Parameters" => [
       ".AppContainer",
       $this->core->CoverPhoto($coverPhoto)
      ]
     ]
    ],
    "View" => [
     "ChangeData" => $changeData,
     "ExtensionID" => $extensionID
    ]
   ]);
  }
  function Public(array $data): string {
   $_Commands = "";
   $_View = "";
   $data = $data["Data"] ?? [];
   $type = $data["Type"] ?? $this->core->AESencrypt("");
   $type = $this->core->AESdecrypt($type);
   $view = $data["View"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($type == "Chat") {
    $_Commands = [
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".NetMap",
       $this->core->AESencrypt("v=".base64_encode("Chat:Menu"))
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->RenderUI("Chat"))
    ];
   } elseif($type == "Public") {
    $_Commands = [
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".Content",
       $view
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->RenderUI("Public"))
    ];
   } elseif($type == "ReSearch") {
    $_Commands = [
     [
      "Name" => "UpdateContent",
      "Parameters" => [
       ".Content",
       base64_encode($view)
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[App.Search]" => base64_encode("v=".base64_encode("Search:ReSearch")."&query=")
     ],
     "Extension" => $this->core->AESencrypt($this->core->RenderUI("Search"))
    ];
   }
   $setUIvariant = $y["Personal"]["UIVariant"] ?? 0;
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "SetUIVariant" => $setUIvariant,
    "View" => $_View
   ]);
  }
  function SubscribeSection(array $data): string {
   $_Dialog = [
    "Body" => "The Content Identifier or Type are missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($type)) {
    $_Dialog = [
     "Body" => "An invalid Content Type was supplied."
    ];
    $_View = "";
    $check = 0;
    $processor = "";
    $subscribers = [];
    if($type == "Article") {
     $article = $this->core->Data("Get", ["pg", $id]);
     $check = ($article["UN"] != $you) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("Page:Subscribe"));
     $subscribers = $article["Subscribers"] ?? [];
     $title = $article["Title"];
    } elseif($type == "Blog") {
     $blog = $this->core->Data("Get", ["blg", $id]);
     $check = ($blog["UN"] != $you) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("Blog:Subscribe"));
     $subscribers = $blog["Subscribers"] ?? [];
     $title = $blog["Title"];
    } elseif($type == "BlogPost") {
     $post = $this->core->Data("Get", ["bp", $id]) ?? [];
     $check = ($post["UN"] != $you) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("BlogPost:Subscribe"));
     $subscribers = $post["Subscribers"] ?? [];
     $title = $post["Title"];
    } elseif($type == "Shop") {
     $check = (md5($you) != $id) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("Shop:Subscribe"));
     $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
     $subscribers = $shop["Subscribers"] ?? [];
     $title = $shop["Title"];
    } if($check == 1 && $this->core->ID != $you) {
     $_Dialog = "";
     $text = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
     $_View = [
      "ChangeData" => [
       "[Subscribe.ContentID]" => $id,
       "[Subscribe.ID]" => $id,
       "[Subscribe.Processor]" => $processor,
       "[Subscribe.Text]" => $text,
       "[Subscribe.Title]" => $title
      ],
      "ExtensionID" => "489a64595f3ec2ec39d1c568cd8a8597"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function SwitchLanguages(): string {
   $options = "";
   foreach($this->core->Languages() as $region => $language) {
    if($region == "en_US") {//TEMP
     $options .= $this->core->Element(["button", $language, [
      "class" => "LI Reg",
      "data-type" => $region
     ]]);
    }//TEMP
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [
      "[App.Languages]" => $options
     ],
     "ExtensionID" => "96021f84defa49827569f2aa1070755b"
    ]
   ]);
  }
  function TwoFactorAuthentication(array $data): string {
   $_AccessCode = "Denied";
   $_Commands = "";
   $_Dialog = [
    "Body" => "An email address is required for us to continue the verification process."
   ];
   $_ResponseType = "View";
   $_View = "";
   $_AddTopMargin = "1";
   $data = $data["Data"] ?? [];
   $_2FA = $data["2FA"] ?? "";
   $_2FAconfirm = $data["2FAconfirm"] ?? "";
   $email = $data["Email"] ?? "";
   $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
   $viewData = json_decode(base64_decode($viewData), true);
   $parentView = $viewData["ParentView"] ?? "SignIn";
   if(!empty($_2FA) && !empty($_2FAconfirm)) {
    $_Dialog = [
     "Body" => "The code you entered does not match the one we sent you."
    ];
    $_View = "";
    $_AddTopMargin = "1";
    $data = $this->core->DecodeBridgeData($data);
    $_2FA = $data["2FA"] ?? "";
    $_2FA = md5($_2FA);
    $_2FAconfirm = $data["2FAconfirm"] ?? "";
    $returnView = $data["ReturnView"] ?? "";
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
    $viewData = json_decode(base64_decode($viewData), true);
    if($_2FA == $_2FAconfirm) {
     $_Dialog = [
      "Body" => "The Return View Identifier is missing."
     ];
     $_View = "";
     if(!empty($returnView)) {
      $_AccessCode = "Accepted";
      $_Dialog = "";
      $_ResponseType = "GoToView";
      $viewData["ViewData"] = $viewData;
      $_View = $this->view($returnView, ["Data" => $viewData]);
      $_View = $this->core->RenderView($_View);
     }
    }
   } elseif(!empty($email)) {
    $_2FA = rand(000000, 999999);
    $_2FAconfirm = md5($_2FA);
    $email = base64_decode($email);
    $_Dialog = [
     "Body" => "A valid email address is required."
    ];
    $_View = "";
    if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $_AccessCode = "Accepted";
     $_Dialog = "";
     $_ResponseType = "GoToView";
     $returnView = $data["ReturnView"] ?? base64_encode("");
     $this->core->SendEmail([
      "Message" => $this->core->Element([
       "p", "Use this code to verify your email address: "
      ]).$this->core->Element([
       "h3", $_2FA
      ]),
      "Title" => "Your Verification Code",
      "To" => $email
     ]);
     $_View = [
      "ChangeData" => [
       "[2FA.Confirm]" => $_2FAconfirm,
       "[2FA.Email]" => $this->core->ObfuscateEmail($email),
       "[2FA.Step2]" => base64_encode("v=".base64_encode("WebUI:TwoFactorAuthentication")),
       "[2FA.ReturnView]" => base64_decode($returnView),
       "[2FA.ViewData]" => $data["ViewData"],
       "[2FA.ViewPairID]" => $parentView
      ],
      "ExtensionID" => "ab9d092807adfadc3184c8ab844a1406"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => $_AddTopMargin,
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function WYSIWYG(): string {
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "ExtensionID" => "8980452420b45c1e6e526a7134d6d411"
    ]
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
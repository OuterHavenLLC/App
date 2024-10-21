<?php
 Class WebUI extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Attachments(array $data) {
   $header = $data["Header"] ?? "";
   $id = $data["ID"] ?? "";
   $media = $data["Media"] ?? [];
   $parentContentID = $data["ParentContentID"] ?? "";
   $uiid = $this->core->UUID($id);
   $r = $this->core->Element(["p", "The content identifier is missing."]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(empty($media)) {
    $r = $this->core->Element(["p", "The media identifiers are missing."]);
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
    $r = (!empty($header)) ? $this->core->Element(["h3", $header]) : "";
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
        "[Media.File]" => base64_decode($attachments),
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
      $cloneSourceID = uniqid("CloneSource".md5($key));
      $liveView = base64_encode("v=".base64_encode("LiveView:Editor")."&MediaType=".base64_encode($key)."&Media=");
      $mediaListID = uniqid("MediaList".md5($key));
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
       $r .= $this->core->Change([[
        "[Section.Content]" => $mediaList,
        "[Section.ID]" => $uiid.$mediaType,
        "[Section.Name]" => $sectionName
       ], $section]);
      }
     }
    }
    $r .= (!empty($_Translate)) ? $this->core->Change([[
     "[Section.Content]" => $_Translate,
     "[Section.ID]" => $uiid."Translate",
     "[Section.Name]" => "Translate"
    ], $section]) : "";
    $r .= (!empty($_ViewDesign)) ? $this->core->Change([[
     "[Section.Content]" => $_ViewDesign,
     "[Section.ID]" => $uiid."ViewDesign",
     "[Section.Name]" => "View Design"
    ], $section]) : "";
    $r = $this->core->Element(["div", $r, ["class" => "ContentAttachments$uiid NONAME"]]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Containers(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $content = $this->view(base64_encode("WebUI:OptIn"), []);
   $content = $this->core->RenderView($content);
   $content = $data["Content"] ?? $content;
   $r = $this->core->Change([[
    "[App.Content]" => $content
   ], $this->core->RenderUI("Public")]);
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($type == "Chat") {
    $r = $this->core->Change([[
     "[App.Menu]" => base64_encode("v=".base64_encode("Chat:Menu"))
    ], $this->core->RenderUI("Chat")]);
   } elseif($type == "ReSearch") {
    $r = $this->core->Change([[
     "[App.Content]" => $content,
     "[App.Search]" => base64_encode("v=".base64_encode("Search:ReSearch")."&query=")
    ], $this->core->RenderUI("Search")]);
   }
   $setUIvariant = $y["Personal"]["UIVariant"] ?? 0;
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "SetUIVariant" => $setUIvariant
   ]);
  }
  function DesignView(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $dv = $data["DV"] ?? "";
   $r = (!empty($dv)) ? $this->core->PlainText([
    "BBCodes" => 1,
    "Data" => $dv,
    "Decode" => 1,
    "Display" => 1,
    "HTMLDecode" => 1
   ]) : $this->core->Element([
    "p", "Add content to reveal its design...", ["class" => "CenterText"]
   ]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Error(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $r = $this->core->Element([
    "h1", "Something went wrong...", ["class" => "UpperCase"]
   ]).$this->core->Element([
    "p", $data["Error"]
   ]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function LockScreen(array $a) {
   $accessCode = "Denied";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "If you are signed in, you can lock your session.",
     "Header" => "Lock"
    ];
   } else {
    $accessCode = "Accepted";
    $r = [
     "Header" => "Resume Session",
     "NoClose" => 1,
     "Scrollable" => $this->core->Change([[
      "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
      "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
      "[Member.PIN]" => $y["Login"]["PIN"]
     ], $this->core->Extension("723a9e510879c2c16bf9690ffe7273b5")])
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Menu(array $a) {
   $accessCode = "Denied";
   $r = [
    "Body" => "Could not load the Network Map..."
   ];
   $search = base64_encode("Search:Containers");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $accessCode = "Accepted";
    $changeData = [
     "[Menu.Company.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.Home]" => base64_encode("v=".base64_encode("Company:Home")),
     "[Menu.Company.IncomeDisclosure]" => base64_encode("v=".base64_encode("Revenue:Home")."&Shop=".base64_encode($this->core->ShopID)),
     "[Menu.Company.PressReleases]" => base64_encode("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => base64_encode("v=".base64_encode("Company:VVA")),
     "[Menu.Gateway]" => base64_encode("v=".base64_encode("WebUI:OptIn")),
     "[Menu.LostAndFound]" => base64_encode("v=".base64_encode("LostAndFound:Home")),
     "[Menu.Mainstream]" => base64_encode("v=$search&st=Mainstream"),
     "[Menu.MiNY]" => base64_encode("v=".base64_encode("Shop:MadeInNewYork")),
     "[Menu.OptIn]" => base64_encode("v=".base64_encode("WebUI:OptIn")),
     "[Menu.SignIn]" => base64_encode("v=".base64_encode("Profile:SignIn")),
     "[Menu.SignUp]" => base64_encode("v=".base64_encode("Profile:SignUp")),
     "[Menu.SwitchLanguages]" => base64_encode("v=".base64_encode("WebUI:SwitchLanguages"))
    ];
    $extension = "73859ffa637c369b9fa88399a27b5598";
   } else {
    $accessCode = "Accepted";
    $admin = ($y["Rank"] == md5("High Command")) ? $this->core->Element([
     "button", "Control Panel", [
      "class" => "CloseNetMap LI UpdateContent",
      "data-view" => base64_encode("v=".base64_encode("ControlPanel:Home"))
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
       "data-view" => base64_encode("v=".base64_encode("Subscription:Home")."&sub=".base64_encode($key))
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
     "[Menu.Company.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.Home]" => base64_encode("v=".base64_encode("Company:Home")),
     "[Menu.Company.IncomeDisclosure]" => base64_encode("v=".base64_encode("Revenue:Home")."&Shop=".base64_encode($this->core->ShopID)),
     "[Menu.Company.PressReleases]" => base64_encode("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => base64_encode("v=".base64_encode("Company:VVA")),
     "[Menu.Congress]" => base64_encode("v=".base64_encode("Congress:Home")),
     "[Menu.LockScreen]" => base64_encode("v=".base64_encode("WebUI:LockScreen")),
     "[Menu.Mainstream]" => base64_encode("v=$search&st=Mainstream"),
     "[Menu.Member.Articles]" => base64_encode("v=$search&st=MBR-LLP"),
     "[Menu.Member.Blacklist]" => base64_encode("v=".base64_encode("Profile:Blacklists")),
     "[Menu.Member.Blogs]" => base64_encode("v=$search&st=MBR-BLG"),
     "[Menu.Member.BulletinCenter]" => base64_encode("v=".base64_encode("Profile:BulletinCenter")),
     "[Menu.Member.Chat]" => base64_encode("v=".base64_encode("Chat:Menu")."&Integrated=1"),
     "[Menu.Member.Contacts]" => base64_encode("v=$search&st=Contacts"),
     "[Menu.Member.CoverPhoto]" => $this->core->CoverPhoto($y["Personal"]["CoverPhoto"]),
     "[Menu.Member.DisplayName]" => $y["Personal"]["DisplayName"].$verified,
     "[Menu.Member.Files]" => base64_encode("v=$search&UN=".base64_encode($you)."&st=XFS"),
     "[Menu.Member.Forums]" => base64_encode("v=$search&lPG=MBR-Forums&st=MBR-Forums"),
     "[Menu.Member.Polls]" => base64_encode("v=$search&st=MBR-Polls"),
     "[Menu.Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:2em 30% 0em 30%;width:40%"),
     "[Menu.Member.Library]" => base64_encode("v=$search&UN=".base64_encode($you)."&lPG=MediaLib&st=MBR-ALB"),
     "[Menu.Member.Preferences]" => base64_encode("v=".base64_encode("Profile:Preferences")),
     "[Menu.Member.Profile]" => base64_encode("v=".base64_encode("Profile:Home")."&UN=".base64_encode($you)),
     "[Menu.Member.Username]" => $you,
     "[Menu.MiNY]" => base64_encode("v=".base64_encode("Shop:MadeInNewYork")),
     "[Menu.MiNY.History]" => base64_encode("v=".base64_encode("Shop:History")."&ID=".md5($this->core->ShopID)),
     "[Menu.Search.Chat]" => base64_encode("v=$search&Integrated=1&lPG=Chat&st=Chat"),
     "[Menu.SwitchLanguages]" => base64_encode("v=".base64_encode("WebUI:SwitchLanguages")),
     "[Menu.Subscriptions]" => $subscriptions
    ];
    $extension = "d14e3045df35f4d9784d45ac2c0fe73b";
   }
   $r = $this->core->Change([
    $changeData,
    $this->core->Extension($extension)
   ]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function OptIn(array $a) {
   $accessCode = "Accepted";
   $eventMedia = $this->core->RenderEventMedia();
   $r = $this->core->Change([[
    "[Gateway.Company]" => base64_encode("v=".base64_encode("Company:Home")."&Card=1"),
    "[Gateway.Architecture]" => base64_encode("v=".base64_encode("Company:VVA")."&CARD=1"),
    "[Gateway.Banner]" => $eventMedia["Banner"],
    "[Gateway.CoverPhoto]" => $eventMedia["CoverPhoto"],
    "[Gateway.IT]" => base64_encode("v=".base64_encode("Shop:Home")."&CARD=1&UN=".base64_encode($this->core->ShopID)),
    "[Gateway.SignIn]" => base64_encode("v=".base64_encode("Profile:SignIn")),
    "[Gateway.SignUp]" => base64_encode("v=".base64_encode("Profile:SignUp"))
   ], $this->core->Extension("db69f503c7c6c1470bd9620b79ab00d7")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SubscribeSection(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Content Identifier or Type are missing."
   ];
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($type)) {
    $accessCode = "Accepted";
    $check = 0;
    $processor = "";
    $r = "";
    $subscribers = [];
    if($type == "Article") {
     $article = $this->core->Data("Get", ["pg", $id]) ?? [];
     $check = ($article["UN"] != $you) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("Page:Subscribe"));
     $subscribers = $article["Subscribers"] ?? [];
     $title = $article["Title"];
    } elseif($type == "Blog") {
     $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
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
     $text = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
     $r = $this->core->Change([[
      "[Subscribe.ContentID]" => $id,
      "[Subscribe.ID]" => $id,
      "[Subscribe.Processor]" => $processor,
      "[Subscribe.Text]" => $text,
      "[Subscribe.Title]" => $title
     ], $this->core->Extension("489a64595f3ec2ec39d1c568cd8a8597")]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SwitchLanguages() {
   $options = "";
   foreach($this->core->Languages() as $region => $language) {
    if($region == "en_US") {//TEMP
     $options .= $this->core->Element(["button", $language, [
      "class" => "LI Reg",
      "data-type" => $region
     ]]);
    }//TEMP
   }
   $r = $this->core->Change([[
    "[App.Languages]" => $options
   ], $this->core->Extension("96021f84defa49827569f2aa1070755b")]);
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function UIContainers(array $a) {
   $accessCode = "Accepted";
   $content = base64_encode("v=".base64_encode("WebUI:OptIn"));
   $addTopMargin = "0";
   $headers = apache_request_headers();
   $language = $headers["Language"] ?? $this->core->language;
   $setUIvariant = 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID != $you) {
    $addTopMargin = 1;
    $content = base64_encode("v=".base64_encode("Search:Containers")."&st=Mainstream");
    $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
    foreach($y["Subscriptions"] as $subscription => $data) {
     if(strtotime($data["B"]) > $data["E"]) {
      $data["A"] = 0;
     } if($subscription == "Artist") {
      $shop["Open"] = $data["A"] ?? 0;
     } elseif($subscription == "VIP") {
      $highCommand = ($y["Rank"] == md5("High Command")) ? 1 : 0;
      $sonsOfLiberty = "cb3e432f76b38eaa66c7269d658bd7ea";
      $manifest = $this->core->Data("Get", [
       "pfmanifest",
       $sonsOfLiberty
      ]) ?? [];
      if($data["A"] == 1) {
       $role = ($highCommand == 1) ? "Admin" : "Member";
       $manifest[$you] = $role;
      } elseif($data["A"] == 0 && $highCommand == 0) {
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
   $r = $this->core->Change([[
    "[App.Content]" => $content,
    "[App.Menu]" => base64_encode("v=".base64_encode("WebUI:Menu")),
    "[App.Search]" => base64_encode("v=".base64_encode("Search:ReSearch")."&query=")
   ], $this->core->RenderUI("Main")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => $addTopMargin,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "SetUIVariant" => $setUIvariant,
    "UIContainers" => 1
   ]);
  }
  function WYSIWYG(array $a) {
   $data = $a["Data"] ?? [];
   $r = $this->core->Extension("8980452420b45c1e6e526a7134d6d411");
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
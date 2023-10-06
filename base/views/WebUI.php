<?php
 Class WebUI extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Containers(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $content = $this->view(base64_encode("WebUI:OptIn"), []);
   $content = $this->core->RenderView($content);
   $content = $data["Content"] ?? $content;
   $r = $this->core->Change([[
    "[App.Content]" => $content
   ], $this->core->Page("606c44e9e7eac67c34c5ad8d1062b003")]);
   $type = $data["Type"] ?? "";
   if($type == "Chat") {
    $r = $this->core->Change([[
     "[App.Menu]" => base64_encode("v=".base64_encode("Chat:Menu"))
    ], $this->core->Page("988e96fd9025b718f43ad357dc25247d")]);
   } elseif($type == "ReSearch") {
    $r = $this->core->Change([[
     "[App.Content]" => $content,
     "[App.Search]" => base64_encode("v=".base64_encode("Search:ReSearch")."&query=")
    ], $this->core->Page("937560239a386533aecf5017371f4d34")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
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
     "NoClose" => 1,
     "Scrollable" => $this->core->Change([[
      "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
      "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
      "[Member.PIN]" => $y["Login"]["PIN"]
     ], $this->core->Page("723a9e510879c2c16bf9690ffe7273b5")])
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
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
   $admin = ($y["Rank"] == md5("High Command")) ? $this->core->Change([[
    "[Admin.Domain]" => "W('https://www.godaddy.com/', '_blank');",
    "[Admin.Feedback]" => base64_encode("v=$search&st=Feedback"),
    "[Admin.Files]" => base64_encode("v=".base64_encode("Album:List")."&AID=".md5("unsorted")."&UN=".base64_encode($this->core->ID)),
    "[Admin.MassMail]" => base64_encode("v=$search&st=ADM-MassMail"),
    "[Admin.Pages]" => base64_encode("v=$search&st=ADM-LLP"),
    "[Admin.RenewSubscriptions]" => base64_encode("v=".base64_encode("Subscription:RenewAll")),
    "[Admin.Server]" => "https://www.digitalocean.com/",
    "[Admin.WHM]" => "https://admin.outerhaven.nyc:2087/"
   ], $this->core->Page("5c1ce5c08e2add4d1487bcd2193315a7")]) : "";
   $shop = ($y["Subscriptions"]["Artist"]["A"] == 1) ? $this->core->Element([
    "button", "Shop", [
     "class" => "CloseNetMap LI UpdateContent",
     "data-view" => base64_encode("v=".base64_encode("Shop:Home")."&UN=".base64_encode($you))
    ]
   ]) : "";
   if($this->core->ID == $you) {
    $accessCode = "Accepted";
    $r = $this->core->Change([[
     "[Menu.Company.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.Defense]" => base64_encode("v=".base64_encode("PMC:Home")),
     "[Menu.Company.Home]" => base64_encode("v=".base64_encode("Company:Home")),
     "[Menu.Company.IncomeDisclosure]" => base64_encode("v=".base64_encode("Common:Income")."&UN=".base64_encode($this->core->ShopID)),
     "[Menu.Company.PressReleases]" => base64_encode("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => base64_encode("v=".base64_encode("Company:VVA")),
     "[Menu.LostAndFound]" => base64_encode("v=".base64_encode("LostAndFound:Home")),
     "[Menu.Mainstream]" => base64_encode("v=$search&st=Mainstream"),
     "[Menu.MiNY]" => base64_encode("v=".base64_encode("Shop:MadeInNewYork")),
     "[Menu.OptIn]" => base64_encode("v=".base64_encode("WebUI:OptIn"))
    ], $this->core->Page("73859ffa637c369b9fa88399a27b5598")]);
   } else {
    $accessCode = "Accepted";
    $r = $this->core->Change([[
     "[Menu.Administration]" => $admin,
     "[Menu.Chat]" => base64_encode("v=".base64_encode("Chat:Menu")."&Integrated=1"),
     "[Menu.Company.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.Home]" => base64_encode("v=".base64_encode("Company:Home")),
     "[Menu.Company.Defense]" => base64_encode("v=".base64_encode("PMC:Home")),
     "[Menu.Company.IncomeDisclosure]" => base64_encode("v=".base64_encode("Common:Income")."&UN=".base64_encode($this->core->ShopID)),
     "[Menu.Company.PressReleases]" => base64_encode("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => base64_encode("v=".base64_encode("Company:VVA")),
     "[Menu.LockScreen]" => base64_encode("v=".base64_encode("WebUI:LockScreen")),
     "[Menu.Mainstream]" => base64_encode("v=$search&st=Mainstream"),
     "[Menu.Member.Articles]" => base64_encode("v=$search&st=MBR-LLP"),
     "[Menu.Member.Blacklist]" => base64_encode("v=".base64_encode("Common:Blacklist")),
     "[Menu.Member.Blogs]" => base64_encode("v=$search&st=MBR-BLG"),
     "[Menu.Member.BulletinCenter]" => base64_encode("v=".base64_encode("Profile:BulletinCenter")),
     "[Menu.Member.Contacts]" => base64_encode("v=$search&st=Contacts"),
     "[Menu.Member.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Menu.Member.Files]" => base64_encode("v=$search&UN=".base64_encode($you)."&lPG=Files&st=XFS"),
     "[Menu.Member.Forums]" => base64_encode("v=$search&lPG=MBR-Forums&st=MBR-Forums"),
     "[Menu.Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:2em 30% 0em 30%;width:40%"),
     "[Menu.Member.Shop]" => $shop,
     "[Menu.Member.Library]" => base64_encode("v=$search&UN=".base64_encode($you)."&lPG=MediaLib&st=MBR-ALB"),
     "[Menu.Member.NewArticle]" => base64_encode("v=".base64_encode("Page:Edit")."&new=1"),
     "[Menu.Member.Preferences]" => base64_encode("v=".base64_encode("Profile:Preferences")),
     "[Menu.Member.Profile]" => base64_encode("v=".base64_encode("Profile:Home")."&UN=".base64_encode($you)),
     "[Menu.Member.Subscriptions]" => base64_encode("v=".base64_encode("Subscription:Index")),
     "[Menu.Member.UpdateStatus]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($you)),
     "[Menu.Member.Username]" => $you,
     "[Menu.MiNY]" => base64_encode("v=".base64_encode("Shop:MadeInNewYork")),
     "[Menu.MiNY.History]" => base64_encode("v=".base64_encode("Shop:History")."&ID=".md5($this->core->ShopID)),
     "[Menu.Search.Archive]" => base64_encode("v=$search&lPG=Archive&st=CA"),
     "[Menu.Search.Artists]" => base64_encode("v=$search&lPG=Shops&st=SHOP"),
     "[Menu.Search.Blogs]" => base64_encode("v=$search&lPG=Blogs&st=BLG"),
     "[Menu.Search.Members]" => base64_encode("v=$search&lPG=Members&st=MBR"),
     "[Menu.Search.PublicForums]" => base64_encode("v=$search&lPG=Forums&st=Forums"),
     "[Menu.SwitchLanguages]" => base64_encode("v=".base64_encode("WebUI:SwitchLanguages"))
    ], $this->core->Page("d14e3045df35f4d9784d45ac2c0fe73b")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function OptIn(array $a) {
   $accessCode = "Accepted";
   $r = $this->core->Change([[
    "[Gateway.About]" => base64_encode("v=".base64_encode("Page:Card")."&ID=".base64_encode("a7b00d61b747827ec4ae74c358da6a01")),
    "[Gateway.Architecture]" => base64_encode("v=".base64_encode("Company:VVA")."&CARD=1"),
    "[Gateway.CoverPhoto]" => $this->core->PlainText([
     "BBCodes" => 1,
     "Data" => "[sIMG:CPW]"
    ]),
    "[Gateway.IT]" => base64_encode("v=".base64_encode("Shop:Home")."&CARD=1&ID=".md5($this->core->ShopID)),
    "[Gateway.SignIn]" => base64_encode("v=".base64_encode("Profile:SignIn")),
    "[Gateway.SignUp]" => base64_encode("v=".base64_encode("Profile:SignUp"))
   ], $this->core->Page("db69f503c7c6c1470bd9620b79ab00d7")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SwitchLanguages() {
   $accessCode = "Accepted";
   $languages = $this->core->Languages() ?? [];
   $options = "";
   foreach($languages as $key => $value) {
    if($key == "en_US") {//TEMP
     $options .= $this->core->Element(["button", $value, [
      "class" => "LI Reg v2 v2w",
      "data-type" => $key,
      "onclick" => "CloseFirSTEPTool();"
     ]]);
    }//TEMP
   }
   $r = $this->core->Change([[
    "[LanguageSwitch.Options]" => $options
   ], $this->core->Page("350d1d8dfa7ce14e12bd62f5f5f27d30")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function UIContainers(array $a) {
   $accessCode = "Accepted";
   $main = base64_encode("Search:Containers");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = $this->view(base64_encode("WebUI:OptIn"), []);
    $r = $this->core->RenderView($r);
   } else {
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
      $this->core->Data("Save", [
       "pfmanifest",
       $sonsOfLiberty,
       $manifest
      ]);
     }
    }
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $this->core->Data("Save", ["shop", md5($you), $shop]);
    $r = $this->view($main, ["Data" => [
     "st" => "Mainstream"
    ]]);
    $r = $this->core->RenderView($r);
   }
   $r = $this->core->Change([[
    "[App.Content]" => $r,
    "[App.Menu]" => base64_encode("v=".base64_encode("WebUI:Menu")),
    "[App.Search]" => base64_encode("v=".base64_encode("Search:ReSearch")."&query=")
   ], $this->core->Page("dd5e4f7f995d5d69ab7f696af4786c49")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function WYSIWYG(array $a) {
   $data = $a["Data"] ?? [];
   $r = $this->core->Page("8980452420b45c1e6e526a7134d6d411");
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
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
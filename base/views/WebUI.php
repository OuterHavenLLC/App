<?php
 Class WebUI extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Containers(array $a) {
   $data = $a["Data"] ?? [];
   $content = $data["Content"] ?? $this->view(base64_encode("WebUI:OptIn"), []);
   $type = $data["Type"] ?? "";
   if($type == "Chat") {
    $r = $this->system->Change([[
     "[TopBar.Ground]" => base64_encode("N/A"),
     "[TopBar.FST]" => base64_encode("v=".base64_encode("Search:Containers")."&Chat=1&st=ContactsChatList"),
     "[TopBar.ID]" => base64_encode(md5("ChatContacts"))
    ], $this->system->Page("988e96fd9025b718f43ad357dc25247d")]);
   } else {
    $r = $this->system->Change([[
     "[OH.MainContent]" => $content
    ], $this->system->Page("606c44e9e7eac67c34c5ad8d1062b003")]);
   }
   return $r;
  }
  function Error(array $a) {
   $data = $a["Data"] ?? [];
   return $this->system->Element([
    "h1", "Something went wrong...", ["class" => "UpperCase"]
   ]).$this->system->Element([
    "p", $data["Error"]
   ]);
  }
  function LockScreen(array $a) {
   $y = $this->you;
   if($this->system->ID == $y["Login"]["Username"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "If you are signed in, you can lock your session."
     ]),
     "Header" => "Lock"
    ]);
   } else {
    $r = $this->system->Change([[
     "[Member.ProfilePicture]" => $this->system->ProfilePicture($y, "margin:5%;width:90%"),
     "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Member.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "class" => "PIN req",
        "maxlen" => 8,
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "PIN"
       ],
       "Type" => "Text"
      ],
      [
       "Attributes" => [
        "class" => "req sPIN",
        "name" => "sPIN",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME"
       ],
       "Type" => "Text",
       "Value" => $y["Login"]["PIN"]
      ]
     ])
    ], $this->system->Page("723a9e510879c2c16bf9690ffe7273b5")]);
   }
   return $r;
  }
  function Menu(array $a) {
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $admin = ($y["Rank"] == md5("High Command")) ? $this->system->AdminMenu() : "";
   $search = base64_encode("Search:Containers");
   $support = [md5("High Command"), md5("Support")];
   $support = (in_array($y["Rank"], $support)) ? "<!--SUPPORT OPTIONS-->" : "";
   $shop = ($y["Subscriptions"]["Artist"]["A"] == 1) ? $this->system->Element([
    "button", "Shop", [
     "class" => "CloseNetMap Small UpdateContent v2",
     "data-view" => base64_encode("v=".base64_encode("Shop:Home")."&UN=".base64_encode($you))
    ]
   ]) : "";
   if($this->system->ID == $you) {
    $r = $this->system->Change([[
     "[Menu.Company.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.Defense]" => base64_encode("v=".base64_encode("PMC:Home")),
     "[Menu.Company.Home]" => "v=".base64_encode("Company:Home"),
     "[Menu.Company.IncomeDisclosure]" => "v=".base64_encode("Common:Income")."&UN=".base64_encode($this->system->ShopID),
     "[Menu.Company.PressReleases]" => base64_encode("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => base64_encode("v=".base64_encode("Company:VVA")),
     "[Menu.LostAndFound]" => base64_encode("v=".base64_encode("LostAndFound:Home")),
     "[Menu.Mainstream]" => base64_encode("v=$search&st=Mainstream"),
     "[Menu.MiNY]" => base64_encode("v=".base64_encode("Shop:MadeInNewYork")),
     "[Menu.OptIn]" => base64_encode("v=".base64_encode("WebUI:OptIn"))
    ], $this->system->Page("73859ffa637c369b9fa88399a27b5598")]);
   } else {
    $r = $this->system->Change([[
     "[Menu.Administration]" => $admin,
     "[Menu.Company.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
     "[Menu.Company.Home]" => base64_encode("v=".base64_encode("Company:Home")),
     "[Menu.Company.Defense]" => base64_encode("v=".base64_encode("PMC:Home")),
     "[Menu.Company.IncomeDisclosure]" => base64_encode("v=".base64_encode("Common:Income")."&UN=".base64_encode($this->system->ShopID)),
     "[Menu.Company.PressReleases]" => base64_encode("v=$search&lPG=PG&st=PR"),
     "[Menu.Company.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
     "[Menu.Company.VVA]" => base64_encode("v=".base64_encode("Company:VVA")),
     "[Menu.LockScreen]" => base64_encode("v=".base64_encode("WebUI:LockScreen")),
     "[Menu.Mainstream]" => base64_encode("v=$search&st=Mainstream"),
     "[Menu.Member.Articles]" => base64_encode("v=$search&st=MBR-LLP"),
     "[Menu.Member.Articles.FSTID]" => md5("MemberArticles"),
     "[Menu.Member.Blacklist]" => "v=".base64_encode("Common:Blacklist"),
     "[Menu.Member.Blacklist.FSTID]" => md5("Blacklist"),
     "[Menu.Member.Blogs]" => "v=$search&st=MBR-BLG",
     "[Menu.Member.Blogs.FSTID]" => md5("MemberBlogs"),
     "[Menu.Member.BulletinCenter]" => "v=".base64_encode("Profile:BulletinCenter"),
     "[Menu.Member.Contacts]" => "v=$search&st=Contacts",
     "[Menu.Member.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Menu.Member.Files]" => base64_encode("v=$search&UN=".base64_encode($you)."&lPG=Files&st=XFS"),
     "[Menu.Member.Forums]" => base64_encode("v=$search&lPG=MBR-Forums&st=MBR-Forums"),
     "[Menu.Member.Shop]" => $shop,
     "[Menu.Member.Library]" => base64_encode("v=$search&UN=".base64_encode($you)."&lPG=MediaLib&st=MBR-ALB"),
     "[Menu.Member.NewArticle]" => base64_encode("v=".base64_encode("Page:Edit")."&new=1"),
     "[Menu.Member.Preferences]" => base64_encode("v=".base64_encode("Profile:Preferences")),
     "[Menu.Member.Profile]" => base64_encode("v=".base64_encode("Profile:Home")."&UN=".base64_encode($you)),
     "[Menu.Member.Subscriptions]" => base64_encode("v=".base64_encode("Subscription:Index")),
     "[Menu.Member.Switch]" => base64_encode("v=".base64_encode("Common:SwitchMember")),
     "[Menu.Member.UpdateStatus]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($you)),
     "[Menu.Member.Username]" => $you,
     "[Menu.MiNY]" => "v=".base64_encode("Shop:MadeInNewYork"),
     "[Menu.MiNY.History]" => "'N/A', 'v=".base64_encode("Shop:History")."&ID=".md5($this->system->ShopID)."', '".md5("ShoppingHistory")."'",
     "[Menu.Search.Archive]" => base64_encode("v=$search&lPG=Archive&st=CA"),
     "[Menu.Search.Artists]" => base64_encode("v=$search&lPG=Shops&st=SHOP"),
     "[Menu.Search.Blogs]" => base64_encode("v=$search&lPG=Blogs&st=BLG"),
     "[Menu.Search.Members]" => base64_encode("v=$search&lPG=Members&st=MBR"),
     "[Menu.Search.PublicForums]" => base64_encode("v=$search&lPG=Forums&st=Forums"),
     "[Menu.Support.Manage]" => $support,
     "[Menu.SwitchLanguages]" => base64_encode("v=".base64_encode("WebUI:SwitchLanguages"))
    ], $this->system->Page("d14e3045df35f4d9784d45ac2c0fe73b")]);
   }
   return $r;
  }
  function OptIn(array $a) {
   return $this->system->Change([[
    "[Gateway.About]" => base64_encode("v=".base64_encode("Page:Card")."&ID=".base64_encode("a7b00d61b747827ec4ae74c358da6a01")),
    "[Gateway.Architecture]" => base64_encode("v=".base64_encode("Company:VVA")."&CARD=1"),
    "[Gateway.CoverPhoto]" => $this->system->PlainText([
     "BBCodes" => 1,
     "Data" => "[sIMG:CPW]"
    ]),
    "[Gateway.IT]" => base64_encode("v=".base64_encode("Shop:Home")."&CARD=1&ID=".md5($this->system->ShopID)),
    "[Gateway.SignIn]" => base64_encode("v=".base64_encode("Profile:SignIn")),
    "[Gateway.SignUp]" => base64_encode("v=".base64_encode("Profile:SignUp"))
   ], $this->system->Page("db69f503c7c6c1470bd9620b79ab00d7")]);
  }
  function SwitchLanguages() {
   $languages = $this->system->Languages() ?? [];
   $options = "";
   foreach($languages as $key => $value) {
    if($key == "en_US") {//TEMP
     $options .= $this->system->Element(["button", $value, [
      "class" => "LI Reg v2 v2w",
      "data-type" => $key,
      "onclick" => "FSTX();"
     ]]);
    }//TEMP
   }
   return $this->system->Change([[
    "[LanguageSwitch.Options]" => $options
   ], $this->system->Page("350d1d8dfa7ce14e12bd62f5f5f27d30")]);
  }
  function UIContainers(array $a) {
   $main = base64_encode("Search:Containers");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->view(base64_encode("WebUI:OptIn"), []);
   } else {
    $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
    foreach($y["Subscriptions"] as $subscription => $data) {
     if(strtotime($data["B"]) > $data["E"]) {
      $data["A"] = 0;
     } if($subscription == "Artist") {
      $shop["Open"] = $data["A"] ?? 0;
     } elseif($subscription == "VIP") {
      $highCommand = ($y["Rank"] == md5("High Command")) ? 1 : 0;
      $sonsOfLiberty = "cb3e432f76b38eaa66c7269d658bd7ea";
      $manifest = $this->system->Data("Get", [
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
      $this->system->Data("Save", ["pfmanifest", $sonsOfLiberty, $manifest]);
     }
    }
    $this->system->Data("Save", ["mbr", md5($you), $y]);
    $this->system->Data("Save", ["shop", md5($you), $shop]);
    $r = $this->view($main, ["Data" => ["st" => "Mainstream"]]);
   }
   return $this->system->Change([[
    "[OH.MainContent]" => $r,
    "[OH.TopBar.NetMap]" => base64_encode("v=".base64_encode("WebUI:Menu")),
    "[OH.TopBar.Search]" => base64_encode("v=".base64_encode("Search:ReSearch")."&query=")
   ], $this->system->Page("dd5e4f7f995d5d69ab7f696af4786c49")]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
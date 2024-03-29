<?php
 Class Company extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Donate(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $donate = "v=".base64_encode("Shop:Pay")."&Shop=".md5($this->core->ShopID)."&Type=Donation&Amount=";
   $pub = $data["pub"] ?? 0;
   $r = $this->core->Change([[
    "[Donate.5]" => base64_encode($donate.base64_encode(5)),
    "[Donate.10]" => base64_encode($donate.base64_encode(10)),
    "[Donate.15]" => base64_encode($donate.base64_encode(15)),
    "[Donate.20]" => base64_encode($donate.base64_encode(20)),
    "[Donate.25]" => base64_encode($donate.base64_encode(25)),
    "[Donate.30]" => base64_encode($donate.base64_encode(30)),
    "[Donate.35]" => base64_encode($donate.base64_encode(35)),
    "[Donate.40]" => base64_encode($donate.base64_encode(40)),
    "[Donate.45]" => base64_encode($donate.base64_encode(45)),
    "[Donate.1000]" => base64_encode($donate.base64_encode(1000)),
    "[Donate.2000]" => base64_encode($donate.base64_encode(2000))
   ], $this->core->Extension("39e1ff34ec859482b7e38e012f81a03f")]);
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
    "ResponseType" => "View"
   ]);
  }
  function Home(array $a) {
   $_ViewTitle = "About ".$this->core->config["App"]["Name"];
   $accessCode = "Accepted";
   $b2 = urlencode($this->core->config["App"]["Name"]);
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $shopID = base64_encode($this->core->ShopID);
   $r = $this->core->Change([[
    "[App.Earnings]" => base64_encode("v=".base64_encode("Common:Income")."&UN=$shopID"),
    "[App.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
    "[App.Hire]" => base64_encode("v=".base64_encode("Shop:HireSection")."&Shop=".md5($this->core->ShopID)),
    "[App.News]" => base64_encode("v=".base64_encode("Search:Containers")."&b2=$b2&lPG=OHC&st=PR"),
    "[App.Partners]" => base64_encode("v=".base64_encode("Company:Partners")),
    "[App.Shop]" => "OHC;".base64_encode("v=".base64_encode("Shop:Home")."&b2=$b2&back=1&lPG=OHC&UN=$shopID"),
    "[App.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
    "[App.VVA]" => "OHC;".base64_encode("v=".base64_encode("Company:VVA")."&b2=$b2&back=1&lPG=OHC")
   ], $this->core->Extension("0a24912129c7df643f36cb26038300d6")]);
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
    "ResponseType" => "View"
   ]);
  }
  function MassMail(array $a) {
   $accessCode = "Denied";
   $button = "";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Pre-Set Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($y["Rank"] != md5("High Command")) {
    $r = [
     "Body" => "This is an administrative function.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($you.uniqid("MassMail-")) : $id;
    $button = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".NewMail$id",
     "data-processor" => base64_encode("v=".base64_encode("Company:SendMassMail"))
    ]]);
    $preSets = $this->core->Data("Get", ["app", md5("MassMail")]) ?? [];
    $preSet = $preSets[$id] ?? [];
    $body = $preSet["Body"] ?? base64_encode("");
    $description = $preSet["Description"] ?? "";
    $title = $preSet["Title"] ?? "New Mail";
    $r = $this->core->Change([[
     "[Email.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body,
      "Decode" => 1
     ])),
     "[Email.Description]" => base64_encode($description),
     "[Email.ID]" => $id,
     "[Email.SecureTitle]" => base64_encode($title),
     "[Email.Title]" => $title
    ], $this->core->Extension("81ccdda23bf18e557bc0ba3071c1c2d4")]);
    $r = [
     "Action" => $button,
     "Front" => $r
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
  function Partners(array $a) {
   $accessCode = "Accepted";
   $partners = $this->core->Member($this->core->ShopID);
   $shop = $this->core->Data("Get", [
    "shop",
    md5($partners["Login"]["Username"])
   ]) ?? [];
   $partners = $shop["Contributors"] ?? [];
   $partnersList = "";
   $template = $this->core->Extension("a10a03f2d169f34450792c146c40d96d");
   foreach($partners as $key => $value) {
    $partnersList .= $this->core->Change([[
     "[IncomeDisclosure.Partner.Company]" => $value["Company"],
     "[IncomeDisclosure.Partner.Description]" => $value["Description"],
     "[IncomeDisclosure.Partner.DisplayName]" => $key,
     "[IncomeDisclosure.Partner.Title]" => $value["Title"]
    ], $template]);
   }
   $r = $this->core->Change([[
    "[Partners.Table]" => $partnersList
   ], $this->core->Extension("2c726e65e5342489621df8fea850dc47")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SendMassMail(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "Body",
    "Description",
    "ID",
    "Save",
    "Title"
   ]);
   $id = $data["ID"];
   $now = $this->core->timestamp;
   $new = $data["new"] ?? 0;
   $preSets = $this->core->Data("Get", ["app", md5("MassMail")]) ?? [];
   $nextSend = $preSets["NextSend"] ?? strtotime($now);
   $r = [
    "Body" => "The Pre-Set Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif($y["Rank"] != md5("High Command")) {
    $r = [
     "Body" => "This is an administrative function.",
     "Header" => "Forbidden"
    ];
   } elseif(strtotime($now) < $nextSend) {
    $r = [
     "Body" => "You may not send an email yet, please try again later.",
     "Header" => "Forbidden"
    ];
   } else {
    $accessCode = "Accepted";
    $contactList = $this->core->Data("Get", [
     "app",
     md5("ContactList")
    ]) ?? [];
    $preSets["NextSend"] = $this->core->TimePlus($now, 1, "month");
    foreach($contactList as $email => $info) {
     if($info["SendOccasionalEmails"] == 1) {
      $this->core->SendEmail([
       "Message" => $data["Body"],
       "Title" => $data["Title"],
       "To" => $email
      ]);
     }
    } if($data["Save"] == 1) {
     $preSets[$id] = [
      "Body" => base64_encode($data["Body"]),
      "Description" => $data["Description"],
      "Title" => $data["Title"]
     ];
    }
    $this->core->Data("Save", ["app", md5("MassMail"), $preSets]);
    $r = [
     "Body" => "Your email has been sent to every Member who elected to receive occasional emails.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function Statistics(array $a) {
   $accessCode = "Accepted";
   $st = "";
   $statistics = $this->core->Data("Get", ["app", "stats"]) ?? [];
   $tpl = $this->core->Extension("676193c49001e041751a458c0392191f");
   $tpl2 = $this->core->Extension("a936651004efc98932b63c2d684715f8");
   $tpl3 = $this->core->Extension("d019a2b62accac6e883e04b358953f3f");
   foreach($statistics as $key => $value) {
    $stk = "";
    foreach($value as $key2 => $value2) {
     $ks = "";
     foreach($value2 as $key3 => $value3) {
      $stat = $this->core->config["Statistics"][$key3] ?? $key3;
      $ks .= $this->core->Change([[
       "[Statistics.Statistic]" => $this->core->Change([[
        $key3 => $stat
       ], $key3]),
       "[Statistics.Statistic.Value]" => $value3
      ], $tpl3]);
     }
     $stk .= $this->core->Change([[
      "[Statistics.Table.Month]" => $this->ConvertCalendarMonths($key2),
      "[Statistics.Table.Month.Statistics]" => $ks
     ], $tpl2]);
    }
    $st .= $this->core->Change([[
     "[IncomeDisclosure.Table.Year]" => $key,
     "[IncomeDisclosure.Table.Year.Lists]" => $stk
    ], $tpl]);
   }
   $r = $this->core->Change([[
    "[Statistics.Table]" => $st
   ], $this->core->Extension("0ba6b9256b4c686505aa66d23bec6b5c")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function VVA(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "CARD",
    "b2",
    "back",
    "lPG",
    "pub"
   ]);
   $back = ($data["back"] == 1) ? $this->core->Element([
    "button", "Back to <em>".$data["b2"]."</em>", [
     "class" => "GoToParent LI head",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $r = $this->core->Change([[
    "[VVA.Back]" => $back,
    "[VVA.Hire]" => base64_encode("v=".base64_encode("Shop:HireSection")."&Shop=".md5($this->core->ShopID))
   ], $this->core->Extension("a7977ac51e7f8420f437c70d801fc72b")]);
   $r = ($data["CARD"] == 1) ? [
    "Front" => $r
   ] : $r;
   if($data["pub"] == 1) {
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
    "Title" => "Visual Vanguard Architecture"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
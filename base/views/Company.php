<?php
 Class Company extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Donate(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $donate = "v=".base64_encode("Pay:Donation")."&amount=";
   $pub = $data["pub"] ?? 0;
   $r = $this->system->Change([[
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
   ], $this->system->Page("39e1ff34ec859482b7e38e012f81a03f")]);
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->system->RenderView($r);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Home(array $a) {
   $accessCode = "Accepted";
   $b2 = urlencode($this->system->core["SYS"]["Title"]);
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $sid = base64_encode($this->system->ShopID);
   $r = $this->system->Change([[
    "[OHC.Earnings]" => base64_encode("v=".base64_encode("Company:Income")."&UN=".base64_encode($this->system->ShopID)),
    "[OHC.News]" => base64_encode("v=".base64_encode("Search:Containers")."&b2=$b2&lPG=OHC&st=PR"),
    "[OHC.Partners]" => base64_encode("v=".base64_encode("Company:Partners")),
    "[OHC.Shop]" => base64_encode("OHC;".base64_encode("v=".base64_encode("Shop:Home")."&b2=$b2&back=1&lPG=OHC&UN=$sid")),
    "[OHC.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
    "[OHC.VVA]" => base64_encode("OHC;".base64_encode("v=".base64_encode("Company:VVA")."&b2=$b2&back=1&lPG=OHC"))
   ], $this->system->Page("0a24912129c7df643f36cb26038300d6")]);
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->system->RenderView($r);
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, ["AID", "new"]);
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Pre-Set Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
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
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".NewMail$id",
     "data-processor" => base64_encode("v=".base64_encode("Company:SendMassMail"))
    ]]);
    $preSets = $this->system->Data("Get", ["x", md5("MassMail")]) ?? [];
    $preSet = $preSets[$id] ?? [];
    $body = $preSet["Body"] ?? base64_encode("");
    $description = $preSet["Description"] ?? "";
    $title = $preSet["Title"] ?? "New Mail";
    $r = $this->system->Change([[
     "[Email.ID]" => $id,
     "[Email.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $id
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "Title",
        "placeholder" => "Title",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Title"
       ],
       "Type" => "Text",
       "Value" => $title
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "Description",
        "placeholder" => "Description"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Description"
       ],
       "Type" => "TextBox",
       "Value" => $description
      ],
      [
       "Attributes" => [
        "class" => "Body Xdecode req",
        "id" => "EditMailBody$id",
        "name" => "Body",
        "placeholder" => "Body"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Body",
        "WYSIWYG" => 1
       ],
       "Type" => "TextBox",
       "Value" => $this->system->PlainText([
        "Data" => $body,
        "Decode" => 1
       ])
      ]
     ]),
     "[Email.Save]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "Save"
       ],
       "Options" => [],
       "Text" => "Save this template as a pre-set for future use.",
       "Type" => "Check",
       "Value" => 1
      ]
     ]),
     "[Email.Title]" => $title
    ], $this->system->Page("81ccdda23bf18e557bc0ba3071c1c2d4")]);
    $r = [
     "Action" => $button,
     "Front" => $r
    ];
   }
   return $this->system->JSONResponse([
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
   $partners = $this->system->Member($this->system->ShopID);
   $shop = $this->system->Data("Get", [
    "shop",
    md5($partners["Login"]["Username"])
   ]) ?? [];
   $partners = $shop["Contributors"] ?? [];
   $partnersList = "";
   $template = $this->system->Page("a10a03f2d169f34450792c146c40d96d");
   foreach($partners as $key => $value) {
    $partnersList .= $this->system->Change([[
     "[IncomeDisclosure.Partner.Company]" => $value["Company"],
     "[IncomeDisclosure.Partner.Description]" => $value["Description"],
     "[IncomeDisclosure.Partner.DisplayName]" => $key,
     "[IncomeDisclosure.Partner.Title]" => $value["Title"]
    ], $template]);
   }
   $r = $this->system->Change([[
    "[Partners.Table]" => $partnersList
   ], $this->system->Page("2c726e65e5342489621df8fea850dc47")]);
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "Body",
    "Description",
    "ID",
    "Save",
    "Title"
   ]);
   $id = $data["ID"];
   $now = $this->system->timestamp;
   $new = $data["new"] ?? 0;
   $preSets = $this->system->Data("Get", ["x", md5("MassMail")]) ?? [];
   $nextSend = $preSets["NextSend"] ?? strtotime($now);
   $r = [
    "Body" => "The Pre-Set Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
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
    $contactList = $this->system->Data("Get", [
     "x",
     md5("ContactList")
    ]) ?? [];
    $preSets["NextSend"] = $this->system->TimePlus($now, 1, "month");
    $preSets["NextSend"] = strtotime($preSets["NextSend"]);
    foreach($contactList as $email => $info) {
     if($info["SendOccasionalEmails"] == 1) {
      $this->system->SendEmail([
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
    $this->system->Data("Save", ["x", md5("MassMail"), $preSets]);
    $r = [
     "Body" => "Your email has been sent to every Member who elected to receive occasional emails.",
     "Header" => "Done"
    ];
   }
   return $this->system->JSONResponse([
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
   $statistics = $this->system->Data("Get", ["x", "stats"]) ?? [];
   $tpl = $this->system->Page("676193c49001e041751a458c0392191f");
   $tpl2 = $this->system->Page("a936651004efc98932b63c2d684715f8");
   $tpl3 = $this->system->Page("d019a2b62accac6e883e04b358953f3f");
   foreach($statistics as $key => $value) {
    $stk = "";
    foreach($value as $key2 => $value2) {
     $ks = "";
     foreach($value2 as $key3 => $value3) {
      $stat = $this->system->core["STAT"][$key3] ?? $key3;
      $ks .= $this->system->Change([[
       "[Statistics.Statistic]" => $this->system->Change([[
        $key3 => $stat
       ], $key3]),
       "[Statistics.Statistic.Value]" => $value3
      ], $tpl3]);
     }
     $stk .= $this->system->Change([[
      "[Statistics.Table.Month]" => $this->ConvertCalendarMonths($key2),
      "[Statistics.Table.Month.Statistics]" => $ks
     ], $tpl2]);
    }
    $st .= $this->system->Change([[
     "[IncomeDisclosure.Table.Year]" => $key,
     "[IncomeDisclosure.Table.Year.Lists]" => $stk
    ], $tpl]);
   }
   $r = $this->system->Change([[
    "[Statistics.Table]" => $st
   ], $this->system->Page("0ba6b9256b4c686505aa66d23bec6b5c")]);
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, [
    "CARD",
    "b2",
    "back",
    "lPG",
    "pub"
   ]);
   $bck = ($data["back"] == 1) ? $this->system->Element([
    "button", "Back to <em>".$data["b2"]."</em>", [
     "class" => "GoToParent LI head",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $r = $this->system->Change([[
    "[VVA.Back]" => $bck
   ], $this->system->Page("a7977ac51e7f8420f437c70d801fc72b")]);
   $r = ($data["CARD"] == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->system->RenderView($r);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
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
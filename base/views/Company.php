<?php
 Class Company extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Donate(array $a) {
   $data = $a["Data"] ?? [];
   $donate = "v=".base64_encode("Pay:Donation")."&amount=";
   $pub = $data["pub"] ?? 0;
   $r = $this->system->Change([[
    "[Donate.5]" => $donate.base64_encode(5),
    "[Donate.10]" => $donate.base64_encode(10),
    "[Donate.15]" => $donate.base64_encode(15),
    "[Donate.20]" => $donate.base64_encode(20),
    "[Donate.25]" => $donate.base64_encode(25),
    "[Donate.30]" => $donate.base64_encode(30),
    "[Donate.35]" => $donate.base64_encode(35),
    "[Donate.40]" => $donate.base64_encode(40),
    "[Donate.45]" => $donate.base64_encode(45),
    "[Donate.1000]" => $donate.base64_encode(1000),
    "[Donate.2000]" => $donate.base64_encode(2000),
    "[Donate.FSTID]" => md5("Donation_Pay")
   ], $this->system->Page("39e1ff34ec859482b7e38e012f81a03f")]);
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function Home(array $a) {
   $b2 = urlencode($this->system->core["SYS"]["Title"]);
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $sid = base64_encode($this->system->ShopID);
   $r = $this->system->Change([[
    "[OHC.Earnings]" => $this->view(base64_encode("Common:Income"), [
     "Data" => [
      "UN" => base64_encode($this->system->ShopID)
     ]
    ]),
    "[OHC.News]" => $this->view(base64_encode("Search:Containers"), [
     "Data" => [
      "b2" => $b2,
      "lPG" => "OHC",
      "st" => "PR"
     ]
    ]),
    "[OHC.Partners]" => $this->view(base64_encode("Company:Partners"), []),
    "[OHC.Shop]" => "OHC;".base64_encode("v=".base64_encode("Shop:Home")."&b2=$b2&back=1&lPG=OHC&UN=$sid"),
    "[OHC.Statistics]" => $this->view(base64_encode("Company:Statistics"), []),
    "[OHC.VVA]" => "OHC;".base64_encode("v=".base64_encode("Company:VVA")."&b2=$b2&back=1&lPG=OHC")
   ], $this->system->Page("0a24912129c7df643f36cb26038300d6")]);
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function MassMail(array $a) {
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["AID", "new"]);
   $id = $data["AID"];
   $new = $data["new"] ?? 0;
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Pre-Set Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif($y["Rank"] != md5("High Command")) {
    $r = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "This is an administrative function."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id) || $new == 1) {
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
    $body = base64_encode($body);
    $description = $preSet["Description"] ?? "";
    $title = $preSet["Title"] ?? "New Email";
    $r = $this->system->Change([[
     "[Email.Body]" => $body,
     "[Email.ID]" => $id,
     "[Email.Description]" => $description,
     "[Email.Title]" => $title
    ], $this->system->Page("81ccdda23bf18e557bc0ba3071c1c2d4")]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function Partners(array $a) {
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
   return $this->system->Change([[
    "[Partners.Table]" => $partnersList
   ], $this->system->Page("2c726e65e5342489621df8fea850dc47")]);
  }
  function SendMassMail(array $a) {
   // MASS MAIL SENDER, SAVES TEMPLATE AS PRE-SET IF ELECTED
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
   $new = $data["new"] ?? 0;
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Pre-Set Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Error"
    ]);
   } elseif($y["Rank"] != md5("High Command")) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "This is an administrative function."
     ]),
     "Header" => "Forbidden"
    ]);
   } else {
    $accessCode = "Accepted";
    $contactList = $this->system->Data("Get", [
     "x",
     md5("ContactList")
    ]) ?? [];
    foreach($contactList as $email => $info) {
     if($info["SendOccasionalEmails"] == 1) {
      $this->system->SendEmail([
       "Message" => $data["Body"],
       "Title" => $data["Title"],
       "To" => $email
      ]);
     }
    } if($data["Save"] == 1) {
     // SAVE TPL DATA AS PRE-SET
    }
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "Your email has been sent to every Member who elected to receive occasional emails.".json_encode($data, true)
     ]),
     "Header" => "Done"
    ]);
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
      $stat = $this->system->core["STAT"][$key3] ?? "";
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
   return $this->system->Change([[
    "[Statistics.Table]" => $st
   ], $this->system->Page("0ba6b9256b4c686505aa66d23bec6b5c")]);
  }
  function VVA(array $a) {
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
   $r = ($data["CARD"] == 1) ? $this->system->Card(["Front" => $r]) : $r;
   $r = ($data["pub"] == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
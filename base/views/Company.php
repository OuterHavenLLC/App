<?php
 Class Company extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Donate(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $donate = "v=".base64_encode("Shop:Pay")."&Shop=".md5($this->core->ShopID)."&Type=Donation&ViewPairID=".base64_encode("CompanyDonations")."&Amount=";
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
    "ResponseType" => "View",
    "Title" => "Donate to ".$this->core->config["App"]["Name"]
   ]);
  }
  function FreeAmericaRadio() {
   $_View = "";
   $_ViewTitle = "Free America Radio";
   $activeEvent = 0;
   $broadcastViewEnabled = 0;
   $description = $this->core->config["App"]["Description"] ?? "";
   $events = $this->core->config["PublicEvents"] ?? [];
   $selectedEvent = [];
   foreach($events as $event => $info) {
    if($info["Active"] == 1) {
     $activeEvent = 1;
     $broadcastViewEnabled = $info["EnablePublicBroadcast"] ?? 0;
     $selectedEvent = $info;
     break;
    }
   } if($broadcastViewEnabled == 1) {
    $_ViewTitle = $selectedEvent["Title"] ?? $_ViewTitle;
    $description = $selectedEvent["Description"] ?? $description;
   }
   $_ExtensionID = ($activeEvent == 1) ? "1870885288027c3d4bc0a29bdf5f7579" : "c0f79632dc2313352f92b41819fe4739";
   $_View = [
    "ChangeData" => [
     "[FAR.Chat]" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=7216072bbd437563e692cc7ff69cdb69"),
     "[FAR.Listen]" => base64_encode("v=".base64_encode("Subscription:FARPlayer")),
     "[FAR.Title]" => $_ViewTitle
    ],
    "ExtensionID" => $_ExtensionID
   ];
   return $this->core->JSONResponse([
    "Title" => $_ViewTitle,
    "View" => $_View
   ]);
  }
  function Home(array $a) {
   $_ViewTitle = "About ".$this->core->config["App"]["Name"];
   $accessCode = "Accepted";
   $b2 = urlencode($this->core->config["App"]["Name"]);
   $eventMedia = $this->core->RenderEventMedia() ?? [];
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $pub = $data["pub"] ?? 0;
   $shopID = base64_encode($this->core->ShopID);
   $r = $this->core->Change([[
    "[App.Banner]" => $eventMedia["Banner"],
    "[App.CoverPhoto]" => $eventMedia["CoverPhoto"],
    "[App.Earnings]" => base64_encode("v=".base64_encode("Revenue:Home")."&Shop=$shopID"),
    "[App.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread")),
    "[App.Hire]" => base64_encode("v=".base64_encode("Shop:HireSection")."&Shop=".md5($this->core->ShopID)),
    "[App.News]" => base64_encode("v=".base64_encode("Search:Containers")."&b2=$b2&lPG=OHC&st=PR"),
    "[App.Partners]" => base64_encode("v=".base64_encode("Company:Partners")),
    "[App.Shop]" => base64_encode("v=".base64_encode("Shop:Home")."&b2=$b2&back=1&lPG=OHC&UN=$shopID"),
    "[App.Statistics]" => base64_encode("v=".base64_encode("Company:Statistics")),
    "[App.VVA]" => base64_encode("v=".base64_encode("Company:VVA")."&b2=$b2&back=1&lPG=OHC")
   ], $this->core->Extension("0a24912129c7df643f36cb26038300d6")]);
   $r = ($card == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "Title" => $_ViewTitle
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
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Statistics(array $a) {
   $data = $a["Data"] ?? [];
   $month = $data["Month"] ?? base64_encode("");
   $month = base64_decode($month);
   $pub = $data["pub"] ?? 0;
   $r = $this->core->Change([[
    "[Statistics.Years]" => base64_encode("v=".base64_encode("Company:Statistics")."&View=".base64_encode("Years"))
   ], $this->core->Extension("0ba6b9256b4c686505aa66d23bec6b5c")]);
   $references = $this->core->config["Statistics"] ?? [];
   $statistics = $this->core->Data("Get", ["app", md5("stats")]) ?? [];
   $view = $data["View"] ?? base64_encode("");
   $view = base64_decode($view);
   $year = $data["Year"] ?? base64_encode("");
   $year = base64_decode($year);
   if(!empty($view)) {
    $i = 0;
    $lineItem = $this->core->Extension("d019a2b62accac6e883e04b358953f3f");
    $r = "";
    $tile = $this->core->Extension("633ddf914ed8a2e2aa7e023471ec83b2");
    if($view == "Month") {
     $days = "";
     $monthData = $statistics[$year][$month] ?? [];
     $monthLineItems = "";
     $monthTotals = [];
     foreach($monthData as $day => $statistic) {
      $dayLineItems = "";
      $dayTotals = [];
      $i++;
      foreach($statistic as $name => $value) {
       $dayStat = $dayTotals[$name] ?? 0;
       $dayTotals[$name] = $dayStat + $value;
       $monthStat = $monthTotals[$name] ?? 0;
       $monthTotals[$name] = $monthStat + $value;
      } foreach($dayTotals as $key => $value) {
       $key = $references[$key] ?? $key;
       $dayLineItems .= $this->core->Change([[
        "[Statistic.Name]" => $key,
        "[Statistic.Value]" => number_format($value)
       ], $lineItem]);
      }
      $days .= $this->core->Change([[
       "[Tile.Action]" => "",
       "[Tile.Data]" => $dayLineItems,
       "[Tile.Header]" => $day
      ], $tile]);
     } foreach($monthTotals as $name => $value) {
      $name = $references[$name] ?? $name;
      $monthLineItems .= $this->core->Change([[
       "[Statistic.Name]" => $name,
       "[Statistic.Value]" => number_format($value)
      ], $lineItem]);
     }
     $monthName = $this->core->GetMonthConversion($month);
     $r = ($i > 0) ? $this->core->Change([
      [
       "[Month.Days]" => $days,
       "[Month.Name]" => "$monthName, $year",
       "[Month.Totals]" => $monthLineItems
      ], $this->core->Extension("a936651004efc98932b63c2d684715f8")
     ]) : $this->core->Element(["h4", "No Statistics Recorded for Statistics Month $month", [
      "class" => "CenterText UpperCase"
     ]]);
    } elseif($view == "Year") {
     $months = "";
     $yearData = $statistics[$year] ?? [];
     $yearLineItems = "";
     $yearTotals = [];
     foreach($yearData as $month => $data) {
      $monthLineItems = "";
      $monthTotals = [];
      foreach($data as $day => $statistic) {
       foreach($statistic as $name => $value) {
        $monthStat = $monthTotals[$name] ?? 0;
        $monthTotals[$name] = $monthStat + $value;
        $yearStat = $yearTotals[$name] ?? 0;
        $yearTotals[$name] = $yearStat + $value;
       }
      } foreach($monthTotals as $name => $value) {
       $name = $references[$name] ?? $name;
       $monthLineItems .= $this->core->Change([[
        "[Statistic.Name]" => $name,
        "[Statistic.Value]" => number_format($value)
       ], $lineItem]);
      }
      $i++;
      $monthName = $this->core->GetMonthConversion($month);
      $months .= $this->core->Element(["div", $this->core->Change([[
       "[Tile.Action]" => $this->core->Element(["button", "View", [
        "class" => "GoToView v2 v2w",
        "data-type" => "Statistics;".base64_encode("v=".base64_encode("Company:Statistics")."&Month=".base64_encode($month)."&View=".base64_encode("Month")."&Year=".base64_encode($year)),
       ]]),
       "[Tile.Data]" => $monthLineItems,
       "[Tile.Header]" => $monthName
      ], $tile]), [
       "class" => "Medium"
      ]]);
     } foreach($yearTotals as $name => $value) {
      $name = $references[$name] ?? $name;
      $yearLineItems .= $this->core->Change([[
       "[Statistic.Name]" => $name,
       "[Statistic.Value]" => number_format($value)
      ], $lineItem]);
     }
     $r = ($i > 0) ? $this->core->Change([
      [
       "[Year.Months]" => $months,
       "[Year.Totals]" => $yearLineItems
      ], $this->core->Extension("64ae7d51379d924fc223df7aa6364f4c")
     ]) : $this->core->Element(["h4", "No Statistics Recorded for Statistics Year $year", [
      "class" => "CenterText UpperCase"
     ]]);
    } elseif($view == "Years") {
     foreach($statistics as $year => $data) {
      $r .= $this->core->Change([[
       "[Year]" => $year,
       "[Year.View]" => base64_encode("v=".base64_encode("Company:Statistics")."&View=".base64_encode("Year")."&Year=".base64_encode($year))
      ], $this->core->Extension("823daad2deeb06a561481fae9b88b1f3")]);
     }
    }
   } if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "Title" => "Statistics @ ".$this->core->config["App"]["Name"]
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
   $portfolio = $this->view(base64_encode("Search:Containers"), ["Data" => [
    "st" => "VVA"
   ]]);
   $r = $this->core->Change([[
    "[VVA.Back]" => $back,
    "[VVA.Hire]" => base64_encode("v=".base64_encode("Shop:HireSection")."&Shop=".md5($this->core->ShopID)),
    "[VVA.Portfolio]" => $this->core->RenderView($portfolio)
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
    "AddTopMargin" => "0",
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
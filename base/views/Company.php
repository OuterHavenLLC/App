<?php
 Class Company extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Donate(): string {
   $donate = "v=".base64_encode("Shop:Pay")."&Shop=".md5($this->core->ShopID)."&Type=Donation&ViewPairID=".base64_encode("CompanyDonations")."&Amount=";
   $_View = [
    "ChangeData" => [
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
    ],
    "ExtensionID" => "39e1ff34ec859482b7e38e012f81a03f"
   ];
   return $this->core->JSONResponse([
    "Title" => "Donate to ".$this->core->config["App"]["Name"],
    "View" => $_View
   ]);
  }
  function FreeAmericaRadio(): string {
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
  function Home(array $data): string {
   $data = $data["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $eventMedia = $this->core->RenderEventMedia() ?? [];
   $shopID = base64_encode($this->core->ShopID);
   $_View = [
    "ChangeData" => [
     "[App.Banner]" => $eventMedia["Banner"],
     "[App.CoverPhoto]" => $eventMedia["CoverPhoto"],
     "[App.Feedback]" => $this->core->AESencrypt("v=".base64_encode("Feedback:NewThread")),
     "[App.Shop]" => $this->core->AESencrypt("v=".base64_encode("Shop:Home")."&b2=".urlencode("Company Home")."&back=1&lPG=OHC&UN=$shopID"),
     "[App.VVA]" => $this->core->AESencrypt("v=".base64_encode("Company:VVA")."&TopMargin=".base64_encode("0")."&back=1")
    ],
    "ExtensionID" => "0a24912129c7df643f36cb26038300d6"
   ];
   $_Card = ($card == 1) ? [
    "Front" => $_View
   ] : "";
   $_View = ($card == 0) ? $_View : "";
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => [
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".AppEarnings",
       $this->core->AESencrypt("v=".base64_encode("Revenue:Home")."&Shop=$shopID")
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".AppNews",
       $this->core->AESencrypt("v=".base64_encode("Search:Containers")."&&st=PR")
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".AppPartners",
       $this->core->AESencrypt("v=".base64_encode("Company:Partners"))
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".AppStatistics",
       $this->core->AESencrypt("v=".base64_encode("Company:Statistics")."&TopMargin=".base64_encode("0"))
      ]
     ],
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".AppHire",
       $this->core->AESencrypt("v=".base64_encode("Shop:HireSection")."&Shop=".md5($this->core->ShopID)),
       30000
      ]
     ]
    ],
    "Title" => "About ".$this->core->config["App"]["Name"],
    "View" => $_View
   ]);
  }
  function Partners(): string {
   $partners = $this->core->Member($this->core->ShopID);
   $shop = $this->core->Data("Get", [
    "shop",
    md5($partners["Login"]["Username"])
   ]) ?? [];
   $partners = $shop["Contributors"] ?? [];
   $partnersList = "";
   $template = $this->core->Extension("a10a03f2d169f34450792c146c40d96d");
   foreach($partners as $partner => $info) {
    $partnersList .= $this->core->Change([[
     "[Partner.Company]" => $info["Company"],
     "[Partner.Description]" => $info["Description"],
     "[Partner.DisplayName]" => $partner,
     "[Partner.Pay]" => "",
     "[Partner.Title]" => $info["Title"]
    ], $template]);
   }
   return $this->core->JSONResponse([
    "View" => [
     "ChangeData" => [
      "[Partners.Table]" => $partnersList
     ],
     "ExtensionID" => "2c726e65e5342489621df8fea850dc47"]
   ]);
  }
  function Portfolio(): string {
   return $this->core->JSONResponse([
    "Title" => $this->core->config["App"]["Name"]." Portfolio",
    "View" => [
     "ChangeData" => [],
     "ExtensionID" => "45787465-6e73-496f-ae42-794d696b65-67fe54e4cbfc4"
    ]
   ]);
  }
  function Statistics(array $data): string {
   $_AddTopMargin = 1;
   $_Commands = [
    [
     "Name" => "UpdateContentAES",
     "Parameters" => [
      ".StatisticsYears",
      $this->core->AESencrypt("v=".base64_encode("Company:Statistics")."&View=".base64_encode("Years"))
     ]
    ]
   ];
   $_View = [
    "ChangeData" => [],
    "ExtensionID" => "0ba6b9256b4c686505aa66d23bec6b5c"
   ];
   $data = $data["Data"] ?? [];
   $_AddTopMargin = $data["AddTopMargin"] ?? base64_encode($_AddTopMargin);
   $_AddTopMargin = base64_decode($_AddTopMargin);
   $month = $data["Month"] ?? base64_encode("");
   $month = base64_decode($month);
   $references = $this->core->config["Statistics"] ?? [];
   $statistics = $this->core->Data("Get", ["app", md5("stats")]);
   $view = $data["View"] ?? base64_encode("");
   $view = base64_decode($view);
   $year = $data["Year"] ?? base64_encode("");
   $year = base64_decode($year);
   if(!empty($view)) {
    $_Commands = [];
    $_View = "";
    $i = 0;
    $lineItem = $this->core->Extension("d019a2b62accac6e883e04b358953f3f");
    $statisticsData = [
     "Data" => [],
     "Labels" => []
    ];
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
       "[Statistic.Value]" => $value
      ], $lineItem]);
      array_push($statisticsData["Data"], $value);
      array_push($statisticsData["Labels"], $name);
     }
     $monthName = $this->core->GetMonthConversion($month);
     array_push($_Commands, [
      "Name" => "RenderChart",
      "Parameters" => [
       [
        "BackgroundColor" => [
         0,
         0,
         255
        ],
        "Chart" => "StatisticsMonth$month",
        "DataSets" => [
         [
          "borderColor" => "white",
          "borderWidth" => 0.5,
          "data" => $statisticsData["Data"],
          "label" => "Statistics",
          "tension" => 0.4
         ]
        ],
        "Labels" => $statisticsData["Labels"],
        "Title" => "Statistics for $monthName",
        "Type" => "bar"
       ]
      ]
     ]);
     $_View = ($i > 0) ? $this->core->Change([
      [
       "[Month.Days]" => $days,
       "[Month.Name]" => "$monthName, $year",
       "[Month.Number]" => $month,
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
        "data-encryption" => "AES",
        "data-type" => "Statistics;".$this->core->AESencrypt("v=".base64_encode("Company:Statistics")."&Month=".base64_encode($month)."&View=".base64_encode("Month")."&Year=".base64_encode($year)),
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
       "[Statistic.Value]" => $value
      ], $lineItem]);
      array_push($statisticsData["Data"], $value);
      array_push($statisticsData["Labels"], $name);
     }
     array_push($_Commands, [
      "Name" => "RenderChart",
      "Parameters" => [
       [
        "BackgroundColor" => [
         0,
         0,
         255
        ],
        "Chart" => "StatisticsYear$year",
        "DataSets" => [
         [
          "borderColor" => "white",
          "borderWidth" => 0.5,
          "data" => $statisticsData["Data"],
          "label" => "Statistics",
          "tension" => 0.4
         ]
        ],
        "Labels" => $statisticsData["Labels"],
        "Title" => "Statistics for $year",
        "Type" => "bar"
       ]
      ]
     ]);
     $_View = ($i > 0) ? $this->core->Change([
      [
       "[Year]" => $year,
       "[Year.Months]" => $months,
       "[Year.Totals]" => $yearLineItems
      ], $this->core->Extension("64ae7d51379d924fc223df7aa6364f4c")
     ]) : $this->core->Element(["h4", "No Statistics Recorded for Statistics Year $year", [
      "class" => "CenterText UpperCase"
     ]]);
    } elseif($view == "Years") {
     foreach($statistics as $year => $data) {
      array_push($_Commands, [
       "Name" => "UpdateContentAES",
       "Parameters" => [
        ".StatisticsYear$year",
        $this->core->AESencrypt("v=".base64_encode("Company:Statistics")."&View=".base64_encode("Year")."&Year=".base64_encode($year))
       ]
      ]);
      $_View .= $this->core->Change([[
       "[Year]" => $year
      ], $this->core->Extension("823daad2deeb06a561481fae9b88b1f3")]);
     }
    }
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Commands" => $_Commands,
    "Title" => "Statistics @ ".$this->core->config["App"]["Name"],
    "View" => $_View
   ]);
  }
  function VVA(array $data): string {
   $_AddTopMargin = 1;
   $_Card = "";
   $_View = "";
   $_AddTopMargin = $data["TopMargin"] ?? base64_encode($_AddTopMargin);
   $_AddTopMargin = base64_decode($_AddTopMargin);
   $data = $data["Data"] ?? [];
   $back = $data["back"] ?? 0;
   $back = ($back == 1) ? $this->core->Element([
    "button", "Back to <em>Company Home</em>", [
     "class" => "GoToParent LI",
     "data-type" => "OHC"
    ]
   ]) : "";
   $card = $data["Card"] ?? 0;
   $_View = [
    "ChangeData" => [
     "[VVA.Back]" => $back
    ],
    "ExtensionID" => "a7977ac51e7f8420f437c70d801fc72b"
   ];
   $_Card = ($card == 1) ? [
    "Front" => $_View
   ] : "";
   $_View = ($card == 0) ? $_View : "";
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Card" => $_Card,
    "Commands" => [
     [
      "Name" => "RefreshCoverPhoto",
      "Parameters" => [
       ".VVACP",
       [
        "0" => $this->core->PlainText([
         "Data" => "[Media:CP]"
        ]),
        "1" => $this->core->PlainText([
         "Data" => "[Media:VVA-CP]"
        ])
       ]
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".VVAPortfolio",
       $this->core->AESencrypt("v=".base64_encode("Search:Containers")."&st=VVA")
      ]
     ],
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".VVAHire",
       $this->core->AESencrypt("v=".base64_encode("Shop:HireSection")."&Shop=".$this->core->ShopID),
       30000
      ]
     ]
    ],
    "Title" => "Visual Vanguard Architecture",
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
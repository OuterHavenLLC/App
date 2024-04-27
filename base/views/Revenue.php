<?php
 Class Revenue extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $_ViewTitle = "Revenue @ ".$this->core->config["App"]["Name"];
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $pub = $data["pub"] ?? 0;
   $shop = $data["Shop"] ?? "";
   $r = $this->core->Extension("d98a89321f5067f73c63a4702dad32d4");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shop)) {
    $shop = base64_decode($shop);
    $bl = $this->core->CheckBlocked([$y, "Members", $shop]);
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    if($_Shop["Empty"] == 0) {
     $_Owner = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Member;".md5($shop))
     ]);
     $_Owner = ($_Owner["Empty"] == 0) ? $_Owner : $this->core->RenderGhostMember();
     $_ViewTitle = "Revenue for ".$_Shop["ListItem"]["Title"];
     $r = $this->core->Change([[
      "[Revenue.Shop.Owner.DisplayName]" => $_Owner["ListItem"]["Title"],
      "[Revenue.Shop.Title]" => $_Shop["ListItem"]["Title"],
      "[Revenue.Shop]" => $shop,
      "[Revenue.Years]" => base64_encode("v=".base64_encode("Revenue:Years")."&Shop=".$data["Shop"])
     ], $this->core->Extension("4ab1c6f35d284a6eae66ebd46bb88d5d")]);
    }
   }
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
    "AccessCode" => "Accepted",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "Title" => $_ViewTitle
   ]);
  }
  function PayPeriod(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $shop = $data["Shop"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shop)) {
    $accessCode = "Accepted";
    $shop = base64_decode($shop);
    $bl = $this->core->CheckBlocked([$y, "Members", $shop]);
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    #$partner = $this->core->Extension("a10a03f2d169f34450792c146c40d96d");
    #$transaction = $this->core->Extension("a2adc6269f67244fc703a6f3269c9dfe");
    $r = [
     "Front" => $this->core->Change([[
      "[PayPeriod.Gross]" => "",
      "[PayPeriod.Expenses]" => "",
      "[PayPeriod.Net]" => "",
      "[PayPeriod.Number]" => "",
      "[PayPeriod.Partners]" => "",
      "[PayPeriod.Range.End]" => "",
      "[PayPeriod.Range.Start]" => "",
      "[PayPeriod.Taxes]" => "",
      "[PayPeriod.Transactions]" => ""
     ], $this->core->Extension("ca72b0ed3686a52f7db1ae3b2f2a7c84")])
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
  function SaveTransaction(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $cost = $data["Cost"] ?? base64_encode(0);
   $cost = str_replace(",", "", base64_decode($cost));
   $orderID = $data["OrderID"] ?? base64_encode("");
   $profit = $data["Profit"] ?? base64_encode(0);
   $profit = str_replace(",", "", base64_decode($profit));
   $shop = $data["Shop"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $responseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shop)) {
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    $r = [
     "Body" => "The Shop does not exist."
    ];
    if($_Shop["Empty"] == 0) {
     $accessCode = "Accepted";
     $now = $this->core->timestamp,;
     $responseType = "View";
     $yearData = $this->core->Data("Get", ["revenue", date("Y")."-".md5($shop)]) ?? [];
     $owner = $yearData["Owner"] ?? $shop;
     $payroll = $yearData["Payroll"] ?? [];
     if(empty($payroll)) {
      for($payPeriod = 1; $payPeriod <= 24; $payPeriod++) {
       $month = 1;
       $month = (in_array($payPeriod, [3, 4])) ? 2 : $month;
       $month = (in_array($payPeriod, [5, 6])) ? 3 : $month;
       $month = (in_array($payPeriod, [7, 8])) ? 4 : $month;
       $month = (in_array($payPeriod, [9, 10])) ? 5 : $month;
       $month = (in_array($payPeriod, [11, 12])) ? 6 : $month;
       $month = (in_array($payPeriod, [13, 14])) ? 7 : $month;
       $month = (in_array($payPeriod, [15, 16])) ? 8 : $month;
       $month = (in_array($payPeriod, [17, 18])) ? 9 : $month;
       $month = (in_array($payPeriod, [19, 20])) ? 10 : $month;
       $month = (in_array($payPeriod, [21, 22])) ? 11 : $month;
       $month = (in_array($payPeriod, [23, 24])) ? 12 : $month;
       $begins = $this->core->timestamp;
       $ends = $this->core->timestamp;
       $payroll[$payPeriod] = [
        "Begins" => $begins,
        "Begins_UNIX" => time($begins),
        "Ends" => $ends,
        "Ends_UNIX" => time($ends),
        "Partners" => []
       ];
      }
     }
     $transactions = $yearData["Transactions"] ?? [];
     // UPDATE CURRENT PAY PERIOD'S PARTNERS IF EMPTY
     array_push($transactions, [
      "Cost" => $cost,
      "OrderID" => base64_decode($orderID),
      "Profit" => $profit,
      "Purchaser" => $you,
      "Timestamp" => $now,
      "Timestamp_UNIX" => strtotime($now),
      "Type" => "{Transaction_Type:Donation|Disbursement|Refund|Sale}"
     ]);
     #$this->core->Data("Get", ["revenue", date("Y")."-".md5($shop), $yearData]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function Year(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $shop = $data["Shop"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $y = $this->you;
   $year = $data["Year"] ?? "";
   $you = $y["Login"]["Username"];
   if(empty($year)) {
    $r = [
     "Body" => "The Revenue Year is missing."
    ];
   } elseif(!empty($shop)) {
    $accessCode = "Accepted";
    $shop = base64_decode($shop);
    $bl = $this->core->CheckBlocked([$y, "Members", $shop]);
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    $yearData = $this->core->Data("Get", ["revenue", "$year-".md5($shop)]) ?? [];
    $transactions = $yearData["Transactions"] ?? [];
    if(!empty($transactions)) {
     $payPeriodData = $yearData["Payroll"] ?? [];
     $payPeriodExtension = $this->core->Extension("2044776cf5f8b7307b3c4f4771589111");
     $payPeriodTotals_Gross = 0;
     $payPeriodTotals_Expenses = 0;
     $payPeriodTotals_Net = 0;
     $payPeriodTotals_Taxes = 0;
     $payPeriods = "";
     // LOOP THROUGH PAY PERIODS
     // -> LOOP THROIUGH TRANSACTIONS TO GET PAY PERIOD TOTALS
     // -> ADD PAY PERIOD TOTALS TO YEAR TOTALS
     $view = ($payPeriodTotals_Gross > 0) ? $this->core->Element(["button", "View", [
      "class" => "OpenCard v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("Revenue:PayPeriod")."&PayPeriod=1&Shop=".$data["Shop"]."&Year=$yesr")//TEMP
      #"data-view" => base64_encode("v=".base64_encode("Revenue:PayPeriod")."&PayPeriod=$payPeriodNumber&Shop=".$data["Shop"]."&Year=$yesr")
     ]]) : "";
     $payPeriods .= $this->core->Change([[
      "[PayPeriod.Gross]" => "",
      "[PayPeriod.Expenses]" => "",
      "[PayPeriod.Net]" => "",
      "[PayPeriod.Number]" => "",
      "[PayPeriod.Taxes]" => "",
      "[PayPeriod.View]" => $view
     ], $this->core->Extension("2044776cf5f8b7307b3c4f4771589111")]);
     $payPeriods = $payPeriods ?? $this->core->Element(["h4", "No Transactions", [
      "class" => "CenterText"
     ]]);
     $r = $this->core->Change([[
      "[Year.Gross]" => "",
      "[Year.Expenses]" => "",
      "[Year.Net]" => "",
      "[Year.PayPeriods]" => $payPeriods,
      "[Year.Taxes]" => ""
     ], $this->core->Extension("676193c49001e041751a458c0392191f")]);
    }
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
  function Years(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $shop = $data["Shop"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shop)) {
    $accessCode = "Accepted";
    $i = 0;
    $shop = base64_decode($shop);
    $bl = $this->core->CheckBlocked([$y, "Members", $shop]);
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    $r = "";
    for($year = date("Y"); $year >= 2017; $year--) {
     $yearData = $this->core->Data("Get", ["revenue", "$year-".md5($shop)]) ?? [];
     $transactions = $yearData["Transactions"] ?? [];
     if(!empty($transactions)) {
      $i++;
      $r .= $this->core->Change([[
       "[Shop.ID]" => $shop,
       "[Year]" => $year,
       "[Year.View]" => base64_encode("v=".base64_encode("Revenue:Year")."&Shop=".$data["Shop"]."&Year=$year")
      ], $this->core->Extension("4c7848ac49eafc9fbd14c20213398e14")]);
     }
    }
    $r = ($i > 0) ? $r : $this->core->Element([
     "h4", "No Revenue Recorded for ".$_Shop["ListItem"]["Title"], [
      "class" => "CenterText InnerMargin UpperCase"
     ]
    ]);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
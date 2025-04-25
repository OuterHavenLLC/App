<?php
 Class Revenue extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function AddTransaction(array $data): string {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $shop = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shop)) {
    $shop = base64_decode($shop);
    $shopID = md5($shop);
    $_Card = [
     "Action" => $this->core->Element(["button", "Save", [
      "class" => "CardButton SendData",
      "data-form" => ".ManualTransaction$shopID",
      "data-processor" => base64_encode("v=".base64_encode("Revenue:SaveManualTransaction"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Shop.ID]" => $shopID,
       "[Shop.Owner]" => $shop
      ],
      "ExtensionID" => "1de8727b9004824edc701907efc32f8c"
     ]
    ];
    $_Dialog = "";
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_View = [
    "ChangeData" => [],
    "ExtensionID" => "d98a89321f5067f73c63a4702dad32d4"
   ];
   $_ViewTitle = "Revenue @ ".$this->core->config["App"]["Name"];
   $data = $data["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $shop = $data["Shop"] ?? "";
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
     $addTransaction = ($shop == $you) ? $this->core->Element(["button", "Add Transaction", [
      "class" => "OpenCard v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("Revenue:AddTransaction")."&Shop=".$data["Shop"])
     ]]) : "";
     $_View = [
      "ChangeData" => [
       "[Revenue.AddTransaction]" => $addTransaction,
       "[Revenue.Shop.Owner.DisplayName]" => $_Owner["ListItem"]["Title"],
       "[Revenue.Shop.Title]" => $_Shop["ListItem"]["Title"],
       "[Revenue.Shop]" => md5($shop),
       "[Revenue.Years]" => base64_encode("v=".base64_encode("Revenue:Years")."&Shop=".$data["Shop"])
      ],
      "ExtensionID" => "4ab1c6f35d284a6eae66ebd46bb88d5d"
     ];
     $_ViewTitle = "Revenue for ".$_Shop["ListItem"]["Title"];
    }
   }
   $_Card = ($card == 1) ? [
    "Front" => $_View
   ] : "";
   $_View = ($card === 0) ? $_View : "";
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Title" => $_ViewTitle,
    "View" => $_View
   ]);
  }
  function PayPeriod(array $data): string {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Pay Period, Revenue Year, or Shop Identifiers are missing."
   ];
   $data = $data["Data"] ?? [];
   $payPeriodID = $data["PayPeriod"] ?? "";
   $shop = $data["Shop"] ?? "";
   $y = $this->you;
   $year = $data["Year"] ?? "";
   $you = $y["Login"]["Username"];
   if(!empty($payPeriodID) && !empty($shop)) {
    $_Dialog = [
     "Body" => "Failed to load the Revenue data for @$shop."
    ];
    $payPeriodID = base64_decode($payPeriodID);
    $payPeriodTotals_Gross = 0;
    $payPeriodTotals_Expenses = 0;
    $payPeriodTotals_Net = 0;
    $payPeriodTotals_Taxes = 0;
    $shop = base64_decode($shop);
    $bl = $this->core->CheckBlocked([$y, "Members", $shop]);
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    $year = base64_decode($year);
    if($_Shop["Empty"] == 0) {
     $revenue =$this->core->Data("Get", ["revenue", "$year-".md5($shop)]) ?? [];
     $payroll = $revenue["Payroll"] ?? [];
     $payPeriodData = $payroll[$payPeriodID] ?? [];
     $_Dialog = [
      "Body" => "Failed to load the Revenue data for Pay Period $year-$payPeriodID."
     ];
     if(!empty($payPeriodData)) {
      $_Dialog = "";
      $tax = $_Shop["DataModel"]["Tax"] ?? 10.00;
      $partners = $payPeriodData["Partners"] ?? [];
      $partnersList = "";
      $partnerCount = count($partners) - 1;
      $partnerCount = ($partnerCount == 0) ? 1 : $partnerCount;
      $transactions = $revenue["Transactions"] ?? [];
      $transactionsList = "";
      foreach($transactions as $transaction => $info) {
       $check = ($info["Timestamp_UNIX"] >= $payPeriodData["Begins_UNIX"]) ? 1 : 0;
       $check2 = ($info["Timestamp_UNIX"] <= $payPeriodData["Ends_UNIX"]) ? 1 : 0;
       if($check == 1 && $check2 == 1) {
        $payPeriodTotals_Gross = $payPeriodTotals_Gross + $info["Profit"];
        if(!in_array($info["Type"], ["Disbursement", "Refund"])) {
         $payPeriodTotals_Expenses = $payPeriodTotals_Expenses + $info["Cost"];
        }
        $transactionsList .= $this->core->Change([[
         "[Transaction.Price]" => number_format($info["Cost"] + $info["Profit"], 2),
         "[Transaction.Title]" => $info["Title"],
         "[Transaction.Type]" => $info["Type"]
        ], $this->core->Extension("a2adc6269f67244fc703a6f3269c9dfe")]);
       }
      }
      $payPeriodTotals_Gross = $payPeriodTotals_Gross + $payPeriodTotals_Expenses;
      $payPeriodTotals_Taxes = $payPeriodTotals_Gross * ($tax / 100);
      $payPeriodTotals_Net = $payPeriodTotals_Gross - $payPeriodTotals_Expenses - $payPeriodTotals_Taxes;
      foreach($partners as $partner => $info) {
       $isPayable = (strtotime($this->core->timestamp) > $payPeriodData["Ends"]) ? 1 : 0;
       $partner = $this->core->Data("Get", ["mbr", md5($partner)]) ?? $this->core->RenderGhostMember();
       $displayName = $partner["Personal"]["DisplayName"];
       $payPeriodSplit = $payPeriodTotals_Net / 2;
       $payPeriodSplit = $payPeriodSplit / $partnerCount;
       $paid = $info["Paid"] ?? 0;
       $isPayable = ($isPayable == 1 && $paid == 0 && $partner["Login"]["Username"] != $you) ? 1 : 0;
       $pay = ($isPayable == 1) ? $this->core->Element([
        "button", "$".number_format($payPeriodSplit, 2), [
         "class" => "BBB GoToView v2",
         "data-type" => "PartnerPayment;".base64_encode("v=".base64_encode("Shop:Pay")."&Amount=".base64_encode($payPeriodSplit)."&Partner=".base64_encode($partner["Login"]["Username"])."&PayPeriod=".base64_encode($payPeriodID)."&Shop=".md5($partner["Login"]["Username"])."&Type=Disbursement&Year=".base64_encode($year))
        ]
       ]) : $this->core->Element(["p", "No Action Needed"]);
       $pay = ($shop == $you) ? $pay : "";
       $partnersList .= $this->core->Change([[
        "[Partner.Company]" => $info["Company"],
        "[Partner.Description]" => $info["Description"],
        "[Partner.DisplayName]" => $displayName,
        "[Partner.Pay]" => $pay,
        "[Partner.Username]" => $partner["Login"]["Username"],
        "[Partner.Title]" => $info["Title"]
       ], $this->core->Extension("a10a03f2d169f34450792c146c40d96d")]);
      }
      $_Card = [
       "Front" => [
        "ChangeData" => [
         "[PayPeriod.Gross]" => number_format($payPeriodTotals_Gross, 2),
         "[PayPeriod.Expenses]" => number_format($payPeriodTotals_Expenses, 2),
         "[PayPeriod.Net]" => number_format($payPeriodTotals_Net, 2),
         "[PayPeriod.Number]" => $payPeriodID,
         "[PayPeriod.Partners]" => $partnersList,
         "[PayPeriod.Range.End]" => $payPeriodData["Ends"],
         "[PayPeriod.Range.Start]" => $payPeriodData["Begins"],
         "[PayPeriod.Shop]" => md5($shop),
         "[PayPeriod.Taxes]" => number_format($payPeriodTotals_Taxes, 2),
         "[PayPeriod.Transactions]" => $transactionsList,
         "[PayPeriod.Year]" => $year
        ],
        "ExtensionID" => "ca72b0ed3686a52f7db1ae3b2f2a7c84"
       ]
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function SaveManualTransaction(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $cost = $data["Cost"] ?? 0;
   $cost = ($cost == "") ? 0 : $cost;
   $orderID = $data["OrderID"] ?? "";
   $profit = $data["Profit"] ?? 0;
   $profit = ($profit == "") ? 0 : $profit;
   $shop = $data["Shop"] ?? "";
   $title = $data["Title"] ?? "";
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(empty($orderID)) {
    $_Dialog = [
     "Body" => "The Order Identifier is missing."
    ];
   } elseif(empty($title)) {
    $_Dialog = [
     "Body" => "The Title is missing."
    ];
   } elseif(empty($type)) {
    $_Dialog = [
     "Body" => "The Type is missing."
    ];
   } elseif(!empty($shop)) {
    $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
     "Cost" => $cost,
     "OrderID" => $orderID,
     "Profit" => $profit,
     "Quantity" => 1,
     "Shop" => $shop,
     "Title" => $title,
     "Type" => $type
    ]]);
    $_AccessCode = "Accepted";
    $_Dialog = [
     "Body" => "The transaction was recorded in the current pay period.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function SaveTransaction(array $data): string {
   $_View = $this->core->Element(["p", "The Shop Identifier is missing."]);
   $data = $data["Data"] ?? [];
   $cost = $data["Cost"] ?? 0;
   $cost = str_replace(",", "", $cost);
   $orderID = $data["OrderID"] ?? "N/A";
   $profit = $data["Profit"] ?? 0;
   $profit = str_replace(",", "", $profit);
   $quantity = $data["Quantity"] ?? 1;
   $responseType = "Dialog";
   $shop = $data["Shop"] ?? "";
   $title = $data["Title"] ?? "Unknown";
   $type = $data["Type"] ?? "Sale";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shop)) {
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => 0,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    $_View = $this->core->Element(["p", "Error loading the Revenue data for @$shop."]);
    if($_Shop["Empty"] == 0) {
     $now = $this->core->timestamp;
     $responseType = "View";
     $yearData = $this->core->Data("Get", ["revenue", date("Y")."-".md5($shop)]) ?? [];
     $owner = $yearData["Owner"] ?? $shop;
     $payroll = $yearData["Payroll"] ?? [];
     if(empty($payroll)) {
      for($payPeriod = 1; $payPeriod <= 24; $payPeriod++) {
       $month = "01";
       $month = (in_array($payPeriod, [3, 4])) ? "02" : $month;
       $month = (in_array($payPeriod, [5, 6])) ? "03" : $month;
       $month = (in_array($payPeriod, [7, 8])) ? "04" : $month;
       $month = (in_array($payPeriod, [9, 10])) ? "05" : $month;
       $month = (in_array($payPeriod, [11, 12])) ? "06" : $month;
       $month = (in_array($payPeriod, [13, 14])) ? "07" : $month;
       $month = (in_array($payPeriod, [15, 16])) ? "08" : $month;
       $month = (in_array($payPeriod, [17, 18])) ? "09" : $month;
       $month = (in_array($payPeriod, [19, 20])) ? "10" : $month;
       $month = (in_array($payPeriod, [21, 22])) ? "11" : $month;
       $month = (in_array($payPeriod, [23, 24])) ? "12" : $month;
       $monthYear = "-$month-".date("Y");
       $getLastDayFromMonthYear = (new DateTime("01$monthYear"))->modify("last day of")->format("d");
       $endDay = ($payPeriod % 2 == 0) ? $getLastDayFromMonthYear : 14;
       $startDay = ($payPeriod % 2 == 0) ? 15 : 1;
       $begins = date($startDay.$monthYear." 00:00:00");
       $ends = date($endDay.$monthYear." 23:59:59");
       $payroll[$payPeriod] = [
        "Begins" => $begins,
        "Begins_UNIX" => strtotime($begins),
        "Ends" => $ends,
        "Ends_UNIX" => strtotime($ends),
        "Partners" => []
       ];
      }
     }
     $transactions = $yearData["Transactions"] ?? [];
     $payPeriodID = "";
     foreach($payroll as $id => $payPeriod) {
      $check = (strtotime($now) >= $payPeriod["Begins_UNIX"]) ? 1 : 0;
      $check2 = (strtotime($now) <= $payPeriod["Ends_UNIX"]) ? 1 : 0;
      if($check == 1 && $check2 == 1) {
       $payPeriodID = $id;
       break;
      }
     } if(empty($payroll[$payPeriodID]["Partners"])) {
      $payroll[$payPeriodID]["Partners"] = $_Shop["DataModel"]["Contributors"] ?? [];
     }
     array_push($transactions, [
      "Client" => $you,
      "Cost" => $cost,
      "OrderID" => $orderID,
      "Profit" => $profit,
      "Quantity" => $quantity,
      "Timestamp" => $now,
      "Timestamp_UNIX" => strtotime($now),
      "Title" => $title,
      "Type" => $type
     ]);
     $yearData = [
      "Owner" => $owner,
      "Payroll" => $payroll,
      "Transactions" => $transactions
     ];
     $this->core->Data("Save", ["revenue", date("Y")."-".md5($shop), $yearData]);
     $_View = $this->core->Element(["p", "OK"]);
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function Year(array $data): string {
   $_Dialog = [
    "Body" => "The Shop Identifier or Year are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $shop = $data["Shop"] ?? "";
   $y = $this->you;
   $year = $data["Year"] ?? "";
   $you = $y["Login"]["Username"];
   if(!empty($shop) && !empty($year)) {
    $shop = base64_decode($shop);
    $bl = $this->core->CheckBlocked([$y, "Members", $shop]);
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    $tax = $_Shop["DataModel"]["Tax"] ?? 10.00;
    $year = base64_decode($year);
    $yearData = $this->core->Data("Get", ["revenue", "$year-".md5($shop)]) ?? [];
    $yearTotals_Gross = 0;
    $yearTotals_Expenses = 0;
    $yearTotals_Net = 0;
    $yearTotals_Taxes = 0;
    $transactions = $yearData["Transactions"] ?? [];
    if(!empty($transactions)) {
     $_Dialog = "";
     $payPeriodData = $yearData["Payroll"] ?? [];
     $payPeriods = "";
     foreach($payPeriodData as $id => $payPeriod) {
      $partnerPaymentsOwed = 0;
      $partners = $payPeriod["Partners"] ?? [];
      $payPeriodTotals_Gross = 0;
      $payPeriodTotals_Expenses = 0;
      $payPeriodTotals_Net = 0;
      $payPeriodTotals_Taxes = 0;
      foreach($transactions as $transaction => $info) {
       $check = ($info["Timestamp_UNIX"] >= $payPeriod["Begins_UNIX"]) ? 1 : 0;
       $check2 = ($info["Timestamp_UNIX"] <= $payPeriod["Ends_UNIX"]) ? 1 : 0;
       if($check == 1 && $check2 == 1) {
        $payPeriodTotals_Gross = $payPeriodTotals_Gross + $info["Profit"];
        if(!in_array($info["Type"], ["Disbursement", "Refund"])) {
         $payPeriodTotals_Expenses = $payPeriodTotals_Expenses + $info["Cost"];
        }
       }
      } foreach($partners as $partner => $info) {
       $paid = $info["Paid"] ?? 0;
       $partnerPaymentsOwed = $partnerPaymentsOwed + $paid;
      }
      $view = ($payPeriodTotals_Gross > 0) ? $this->core->Element(["button", "View", [
       "class" => "OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Revenue:PayPeriod")."&PayPeriod=".base64_encode($id)."&Shop=".$data["Shop"]."&Year=".$data["Year"])
      ]]) : "";
      $yearTotals_Gross = $yearTotals_Gross + $payPeriodTotals_Gross;
      $yearTotals_Expenses = $yearTotals_Expenses + $payPeriodTotals_Expenses;
      $partnerPaymentsOwed = (strtotime($this->core->timestamp) > $payPeriod["Ends_UNIX"] && $partnerPaymentsOwed > 0) ? $this->core->Element([
       "p", "Partner Payments Due"
      ]) : "";
      $payPeriodTotals_Gross = $payPeriodTotals_Gross + $payPeriodTotals_Expenses;
      $payPeriodTotals_Taxes = $payPeriodTotals_Gross * ($tax / 100);
      $payPeriodTotals_Net = $payPeriodTotals_Gross - $payPeriodTotals_Expenses - $payPeriodTotals_Taxes;
      $payPeriods .= $this->core->Change([[
       "[PayPeriod.Gross]" => number_format($payPeriodTotals_Gross, 2),
       "[PayPeriod.Expenses]" => number_format($payPeriodTotals_Expenses, 2),
       "[PayPeriod.Net]" => number_format($payPeriodTotals_Net, 2),
       "[PayPeriod.Number]" => $id,
       "[PayPeriod.PartnerPaymentsOwed]" => $partnerPaymentsOwed,
       "[PayPeriod.Taxes]" => number_format($payPeriodTotals_Taxes, 2),
       "[PayPeriod.View]" => $view
      ], $this->core->Extension("2044776cf5f8b7307b3c4f4771589111")]);
     }
     $payPeriods = $payPeriods ?? $this->core->Element(["h4", "No Transactions", [
      "class" => "CenterText"
     ]]);
     $yearTotals_Gross = $yearTotals_Gross + $yearTotals_Expenses;
     $yearTotals_Taxes = $yearTotals_Gross * ($tax / 100);
     $yearTotals_Net = $yearTotals_Gross - $yearTotals_Expenses - $yearTotals_Taxes;
     $_View = [
      "ChangeData" => [
       "[Year.Gross]" => number_format($yearTotals_Gross, 2),
       "[Year.Expenses]" => number_format($yearTotals_Expenses, 2),
       "[Year.Net]" => number_format($yearTotals_Net, 2),
       "[Year.PayPeriods]" => $payPeriods,
       "[Year.Taxes]" => number_format($yearTotals_Taxes, 2)
      ],
      "ExtensionID" => "676193c49001e041751a458c0392191f"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Years(array $data): string {
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $shop = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shop)) {
    $_Dialog = "";
    $_View = "";
    $i = 0;
    $shop = base64_decode($shop);
    $bl = $this->core->CheckBlocked([$y, "Members", $shop]);
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    for($year = date("Y"); $year >= 2017; $year--) {
     $yearData = $this->core->Data("Get", ["revenue", "$year-".md5($shop)]) ?? [];
     $transactions = $yearData["Transactions"] ?? [];
     if(!empty($transactions)) {
      $i++;
      $_View .= $this->core->Change([[
       "[Shop.ID]" => $shop,
       "[Year]" => $year,
       "[Year.View]" => base64_encode("v=".base64_encode("Revenue:Year")."&Shop=".$data["Shop"]."&Year=".base64_encode($year))
      ], $this->core->Extension("4c7848ac49eafc9fbd14c20213398e14")]);
     }
    }
    $_View = ($i > 0) ? $_View : $this->core->Element([
     "h4", "No Revenue Recorded for ".$_Shop["ListItem"]["Title"], [
      "class" => "CenterText InnerMargin UpperCase"
     ]
    ]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
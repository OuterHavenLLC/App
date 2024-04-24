<?php
 Class Revenue extends OH {
  // ALL INCOME AND PAYROLL FUNCTIONS WILL BE CONSOLIDATED HERE
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $pub = $data["pub"] ?? 0;
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
    $_Owner = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Member;".md5($shop))
    ]);
    $_Owner = ($_Owner["Empty"] == 0) ? $_Owner : $this->core->RenderGhostMember();
    $_Shop = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Shop;".md5($shop)),
     "Owner" => $shop
    ]);
    $r = $this->core->Change([[
     "[Revenue.Shop.Owner.DisplayName]" => $_Owner["DataModel"]["Personal"]["DisplayName"],
     "[Revenue.Shop.Title]" => $_Shop["DataModel"]["Title"],
     "[Revenue.Shop]" => $shop,
     "[Revenue.Years]" => base64_encode("v=".base64_encode("Revenue:Years")."&Shop=".$data["Shop"])
    ], $this->core->Extension("4ab1c6f35d284a6eae66ebd46bb88d5d")]);
   }
   $r = ($card == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    if($this->core->ID == $you) {
     $r = $this->view(base64_encode("WebUI:OptIn"), []);
     $r = $this->core->RenderView($r);
    }
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
    $years = "";
    for($year = 2017; $year < date("Y"); $year++) {
     $yearData = $this->core->Data("Get", ["revenue", "$year-$shop"]) ?? [];
     #if(!empty($yearData)) {
      $i++;
      #if(!empty($yearData["Transactions"])) {
       $payPeriodExtension = $this->core->Extension("2044776cf5f8b7307b3c4f4771589111");
       $payPeriods = "";
       // LOOP THROUGH PAY PERIODS
       // -> LOOP THROIUGH TRANSACTIONS TO GET PAY PERIOD TOTALS
       // -> ADD PAY PERIOD TOTALS TO YEAR TOTALS
       /*--
       $view = $this->core->Element(["button", "View", [
        "class" => "OpenCard v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Revenue:PayPeriod")."&PayPeriod=$payPeriodNumber&Shop=".$data["Shop"]."&Year=$yesr")
       ]]);
       --*/
       $payPeriods .= $this->core->Change([[
        "[PayPeriod.Gross]" => "",
        "[PayPeriod.Expenses]" => "",
        "[PayPeriod.Net]" => "",
        "[PayPeriod.Number]" => "",
        "[PayPeriod.Taxes]" => "",
        "[PayPeriod.View]" => ""//TEMP
        #"[PayPeriod.View]" => $view
       ], $this->core->Extension("2044776cf5f8b7307b3c4f4771589111")]);//TEMP
      #}
      $payPeriods = $payPeriods ?? $this->core->Element(["h4", "No Transactions", [
       "class" => "CenterText"
      ]]);
      $years .= $this->core->Change([[
       "[Year]" => $year,
       "[Year.Gross]" => "",
       "[Year.Expenses]" => "",
       "[Year.Net]" => "",
       "[Year.PayPeriods]" => $payPeriods,
       "[Year.Taxes]" => ""
      ], $this->core->Extension("676193c49001e041751a458c0392191f")]);
     #}
    }
    $r = ($i > 0) ? $years : $this->core->Element(["h4", "No Revenue Recorded", [
     "class" => "CenterText InnerMargin UpperCase"
    ]]);
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
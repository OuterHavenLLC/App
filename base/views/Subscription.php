<?php
 Class Subscription extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function FARPlayer(): string {
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [
      "[Player.Title]" => "Free America Radio"
     ],
     "ExtensionID" => "d17b1f6a69e6c27b7e0760099d99e2ca"
    ]
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Subscription Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $subscription = $data["sub"] ?? base64_encode("");
   $subscription = base64_decode($subscription);
   $search = base64_encode("Search:Containers");
   $sub = $this->core->config["Subscriptions"][$subscription] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $ysub = $y["Subscriptions"][$subscription] ?? [];
   if(!empty($subscription)) {
    $changeData = [];
    if($ysub["A"] == 0) {
     $extension = "ffdcc2a6f8e1265543c190fef8e7982f";
    } else {
     if($subscription == "Artist") {
      $_LastMonth = $this->core->LastMonth()["LastMonth"];
      $commissions = $y["ArtistCommissionsPaid"] ?? [];
      $commissions = $commissions[$_LastMonth] ?? [];
      $_LastMonth = explode("-", $_LastMonth);
      $commission = 0;
      $commissionIsDue = 0;
      $revenueMonth = $_LastMonth[1];
      $revenueYear = $_LastMonth[0];
      $monthYear = "-$revenueMonth-$revenueYear";
      $revenueMonthBegins = strtotime("01$monthYear 00:00:00");
      $revenueMonthEnds = strtotime((new DateTime("01$monthYear"))->modify("last day of")->format("d")."$monthYear 23:59:59");
      $revenue = $this->core->Data("Get", ["revenue", "$revenueYear-".md5($you)]) ?? [];
      $transactions = $revenue["Transactions"] ?? [];
      foreach($transactions as $transaction => $info) {
       $check = ($info["Timestamp_UNIX"] >= $revenueMonthBegins) ? 1 : 0;
       $check2 = ($info["Timestamp_UNIX"] <= $revenueMonthEnds) ? 1 : 0;
       if($check == 1 && $check2 == 1) {
        $commission = $commission + $info["Profit"];
       }
      }
      $commission = $commission * (5.00 / 100);
      $commissionIsDue = (empty($commissions) && $commission > 0) ? 1 : 0;
      if($commissionIsDue == 1 && $this->core->ShopID != $you) {
       $commission = number_format($commission * (5.00 / 100), 2);
       $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
       $shop["Open"] = 0;
       $this->core->Data("Save", ["shop", md5($you), $shop]);
       $_Card = [
        "ChangeData" => [
         "[Commission.Pay]" => base64_encode("v=".base64_encode("Shop:Pay")."&Amount=".base64_encode($commission)."&Month=".base64_encode($revenueMonth)."&Shop=".md5($this->core->ShopID)."&Type=Commission&Year=".base64_encode($revenueYear)),
         "[Commission.Total]" => $commission
        ],
        "ExtensionID" => "f844c17ae6ce15c373c2bd2a691d0a9a"
       ];
      } else {
       $_Card = $this->view(base64_encode("Shop:Home"), ["Data" => [
        "UN" => base64_encode($you)
       ]]);
       $_Card = $this->core->RenderView($_Card);
      }
     } elseif($subscription == "Developer") {
      $heathKits = [
       base64_encode("App/1c48161334e41522f112494baf2c8a60.jpg"),
       base64_encode("App/a1b9837369061a7b3db741e532b40b9d.jpg"),
       base64_encode("App/78569ee93f82cf2cd9415e7c4ca5e65b.png")
      ];
      $_Card = [
       "ChangeData" => [
        "[Developer.CoverPhoto]" => $this->core->PlainText([
         "Data" => "[Media:CP]",
         "Display" => 1
        ]),
        "[Developer.Download]" => base64_encode("v=".base64_encode("File:Download")),
        "[Developer.Download.All]" => base64_encode(implode(";", $heathKits)),
        "[Developer.Feedback]" => base64_encode("v=".base64_encode("Feedback:NewThread"))
       ],
       "ExtensionID" => "9070936bf7decfbd767391176bc0acdb"
      ];
     } elseif($subscription == "VIP") {
      $_Card = [
       "ChangeData" => [
        "[VIP.CoverPhoto]" => $this->core->PlainText([
         "Data" => "[Media:CP]",
         "Display" => 1
        ]),
        "[VIP.Chat]" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=5ec1e051bf732d19e09ea9673cd7986b"),
        "[VIP.Email]" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=f7f6947173514c96a5b32b4931c92df1&UN=".base64_encode($this->core->ShopID)),
        "[VIP.FAB]" => base64_encode("v=".base64_encode("Subscription:FARPlayer")),
        "[VIP.Forum]" => base64_encode("v=".base64_encode("Forum:Home")."&CARD=1&ID=cb3e432f76b38eaa66c7269d658bd7ea")
       ],
       "ExtensionID" => "89d36f051962ca4bbfbcb1dc2bd41f60"
      ];
     } if(strtotime($this->core->timestamp) > $y["Subscriptions"][$subscription]["E"]) {
      $y["Subscriptions"][$subscription]["A"] = 0;
      $this->core->Data("Save", ["mbr", md5($you), $y]);
      $_Card = [
       "ChangeData" => [],
       "ExtensionID" => "a0891fc91ad185b6a99f1ba501b3c9be"
      ];
     }
    }
    $_Card = [
     "Front" => $_Card
    ];
    $_Dialog = "";
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function RenewAll(): string {
   $_Dialog = [
    "Body" => "You do not have permission to access this view."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($y["Rank"] == md5("High Command")) {
    $now = $this->core->timestamp;
    foreach($y["Subscriptions"] as $subscription => $info) {
     $y["Subscriptions"][$subscription] = [
      "A" => 1,
      "B" => $now,
      "E" => $this->TimePlus($now, 1, "year")
     ];
    }
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $_Dialog = [
     "Body" => "Your subscriptions have been renewed!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
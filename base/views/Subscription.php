<?php
 Class Subscription extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function FABPlayer() {
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "Response" => [
     "JSON" => "",
     "Web" => $this->core->Change([[
      "[Player.Title]" => "Free America Broadcasting"
     ], $this->core->Extension("d17b1f6a69e6c27b7e0760099d99e2ca")])
    ],
    "ResponseType" => "View"
   ]);
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $s = $data["sub"] ?? base64_encode("");
   $s = base64_decode($s);
   $search = base64_encode("Search:Containers");
   $sub = $this->core->config["Subscriptions"][$s] ?? [];
   $r = [
    "Body" => "The Subscription Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $ysub = $y["Subscriptions"][$s] ?? [];
   if(!empty($s)) {
    $accessCode = "Accepted";
    $changeData = [];
    if($ysub["A"] == 0) {
     $extension = "ffdcc2a6f8e1265543c190fef8e7982f";
    } else {
     if($s == "Artist") {
      $_LastMonth = $this->core->LastMonth()["LastMonth"];
      $_LastMonth = explode("-", $_LastMonth);
      $commission = 0;
      $income = $this->core->Data("Get", ["id", md5($you)]) ?? [];
      $income = $income[$_LastMonth[0]] ?? [];
      $income = $income[$_LastMonth[1]] ?? [];
      $paidCommission = $income["PaidCommission"] ?? 0;
      $sales = $income["Sales"] ?? [];
      foreach($sales as $day => $salesGroup) {
       foreach($salesGroup as $daySales => $daySale) {
        foreach($daySale as $id => $product) {
         $price = $product["Cost"] + $product["Profit"];
         $price = $price * $product["Quantity"];
         $price = number_format($price, 2);
         $commission = $commission + $price;
        }
       }
      } if($commission > 0 && $paidCommission == 0) {
       $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
       $commission = number_format($commission * (5.00 / 100), 2);
       $shop["Open"] = 0;
       $this->core->Data("Save", ["shop", md5($you), $shop]);
       $changeData = [
        "[Commission.Pay]" => base64_encode("v=".base64_encode("Shop:Pay")."&Type=Commission&Amount=".base64_encode($commission)."&Shop=".md5($this->core->ShopID)),
        "[Commission.Total]" => $commission
       ];
       $extension = "f844c17ae6ce15c373c2bd2a691d0a9a";
      } else {
       $changeData = [
        "[Artist.Charts]" => "",
        "[Artist.Contributors]" => base64_encode("v=$search&ID=".base64_encode(md5($you))."&Type=".base64_encode("Shop")."&st=Contributors"),
        "[Artist.CoverPhoto]" => $this->core->PlainText([
         "Data" => "[Media:CP]",
         "Display" => 1
        ]),
        "[Artist.Hire]" => base64_encode("v=".base64_encode("Shop:EditPartner")."&new=1"),
        "[Artist.Orders]" => base64_encode("v=$search&st=SHOP-Orders"),
        "[Artist.ID]" => md5($you),
        "[Artist.Payroll]" => base64_encode("v=".base64_encode("Shop:Payroll")),
        "[Artist.Revenue]" => base64_encode("v=".base64_encode("Common:Income")."&UN=".base64_encode($you))
       ];
       $extension = "20820f4afd96c9e32440beabed381d36";
      }
     } elseif($s == "Blogger") {
      $changeData = [
       "[Blogger.CoverPhoto]" => $this->core->PlainText([
        "Data" => "[Media:CP]",
        "Display" => 1
       ]),
       "[Blogger.Stream]" => base64_encode("v=$search&UN=".base64_encode($you)."&st=MBR-BLG"),
       "[Blogger.Title]" => $sub["Title"]
      ];
      $extension = "566f9967f00f97350e54b0ee14faef36";
     } elseif($s == "Developer") {
      $changeData = [
       "[Developer.CoverPhoto]" => $this->core->PlainText([
        "Data" => "[Media:CP]",
        "Display" => 1
       ])
      ];
      $extension = "9070936bf7decfbd767391176bc0acdb";
     } elseif($s == "VIP") {
      $changeData = [
       "[VIP.CoverPhoto]" => $this->core->PlainText([
        "Data" => "[Media:CP]",
        "Display" => 1
       ]),
       "[VIP.Chat]" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=5ec1e051bf732d19e09ea9673cd7986b"),
       "[VIP.Email]" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=f7f6947173514c96a5b32b4931c92df1&UN=".base64_encode($this->core->ShopID)),
       "[VIP.FAB]" => base64_encode("v=".base64_encode("Subscription:FABPlayer")),
       "[VIP.Forum]" => base64_encode("v=".base64_encode("Forum:Home")."&CARD=1&ID=cb3e432f76b38eaa66c7269d658bd7ea"),
       "[VIP.Mail]" => "W('https://mail.outerhaven.nyc/mail/', '_blank');"
      ];
      $extension = "89d36f051962ca4bbfbcb1dc2bd41f60";
     } elseif($s == "XFS") {
      $changeData = [
       "[XFS.CoverPhoto]" => $this->core->PlainText([
        "Data" => "[Media:CP]",
        "Display" => 1
       ])
      ];
      $extension = "dad7bf9214d25c12fa8a4543bbdb9d23";
     } if(strtotime($this->core->timestamp) > $y["Subscriptions"][$s]["E"]) {
      $y["Subscriptions"][$s]["A"] = 0;
      $this->core->Data("Save", ["mbr", md5($you), $y]);
      $extension = "a0891fc91ad185b6a99f1ba501b3c9be";
     }
    }
    $r = [
     "Front" => $this->core->Change([
      $changeData,
      $this->core->Extension($extension)
     ])
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
  function RenewAll(array $a) {
   $accessCode = "Denied";
   $r = [
    "Body" => "You do not have permission to access this view."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($y["Rank"] == md5("High Command")) {
    $accessCode = "Accepted";
    foreach($y["Subscriptions"] as $key => $value) {
     $y["Subscriptions"][$key] = [
      "A" => 1,
      "B" => $this->core->timestamp,
      "E" => $this->TimePlus($this->core->timestamp, 1, "year")
     ];
    }
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "Your subscriptions have been renewed!",
     "Header" => "Done"
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
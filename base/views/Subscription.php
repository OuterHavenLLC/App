<?php
 Class Subscription extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Index(array $a) {
   $accessCode = "Accepted";
   $active = "";
   $ai = 0;
   $base = $this->system->base;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   foreach($y["Subscriptions"] as $key => $value) {
    $subscription = $this->system->config["SUB"][$key] ?? [];
    if(!empty($subscription)) {
     $subscription = $this->system->Element([
      "button", $this->system->Element([
       "h4", $subscription["Title"]
      ]).$this->system->Element([
       "p", $subscription["Description"]
      ]), [
       "class" => "K4i OpenCard",
       "data-view" => base64_encode("v=".base64_encode("Subscription:Home")."&sub=".base64_encode($key))
      ]
     ]);
     if($value["A"] == 1) {
      $active .= $subscription;
      $ai++;
     }
    }
   } if($ai == 0 || $this->system->ID == $you) {
    $active = $this->system->Element([
     "h4", "No Active Subscriptions",
     ["class" => "CenterText InnerMargin UpperCase"]
    ]);
   }
   $r = $this->system->Change([[
    "[Subscriptions.Active]" => $active
   ], $this->system->Page("81c6e3ce434e1b052240cf71ec7b1bc3")]);
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
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $s = $data["sub"] ?? base64_encode("");
   $s = base64_decode($s);
   $search = base64_encode("Search:Containers");
   $sub = $this->system->config["SUB"][$s] ?? [];
   $r = [
    "Body" => "The Subscription Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $ysub = $y["Subscriptions"][$s] ?? [];
   if(!empty($s)) {
    $accessCode = "Accepted";
    if($ysub["A"] == 0) {
     $r = $this->system->Page("ffdcc2a6f8e1265543c190fef8e7982f");
    } else {
     if($s == "Artist") {
      $_LastMonth = $this->system->LastMonth()["LastMonth"];
      $_LastMonth = explode("-", $_LastMonth);
      $commission = 0;
      $income = $this->system->Data("Get", ["id", md5($you)]) ?? [];
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
       $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
       $commission = number_format($commission, 2);
       $commission = number_format($commission * (5.00 / 100), 2);
       $shop["Open"] = 0;
       $this->system->Data("Save", ["shop", md5($you), $shop]);
       $r = $this->system->Change([[
        "[Commission.Pay]" => base64_encode("v=".base64_encode("Pay:Commission")."&amount=".base64_encode($commission)),
        "[Commission.Total]" => $commission
       ], $this->system->Page("f844c17ae6ce15c373c2bd2a691d0a9a")]);
      } else {
       $r = $this->system->Change([[
        "[Artist.Charts]" => "",
        "[Artist.Contributors]" => base64_encode("v=$search&ID=".base64_encode(md5($you))."&Type=".base64_encode("Shop")."&st=Contributors"),
        "[Artist.CoverPhoto]" => $this->system->PlainText([
         "Data" => "[sIMG:CP]",
         "Display" => 1
        ]),
        "[Artist.Hire]" => base64_encode("v=".base64_encode("Shop:EditPartner")."&new=1"),
        "[Artist.Orders]" => base64_encode("v=$search&st=SHOP-Orders"),
        "[Artist.ID]" => md5($you),
        "[Artist.Payroll]" => base64_encode("v=".base64_encode("Shop:Payroll")),
        "[Artist.Revenue]" => base64_encode("v=".base64_encode("Common:Income")."&UN=".base64_encode($you))
       ], $this->system->Page("20820f4afd96c9e32440beabed381d36")]);
      }
     } elseif($s == "Blogger") {
      $r = $this->system->Change([[
       "[Blogger.CoverPhoto]" => $this->system->PlainText([
        "Data" => "[sIMG:CP]",
        "Display" => 1
       ]),
       "[Blogger.List]" => base64_encode("v=$search&st=S-Blogger"),
       "[Blogger.Title]" => $sub["Title"]
      ], $this->system->Page("566f9967f00f97350e54b0ee14faef36")]);
     } elseif($s == "VIP") {
      $forum = base64_encode("Forum:Home");
      $id = base64_encode("cb3e432f76b38eaa66c7269d658bd7ea");
      $r = $this->system->Change([[
       "[VIP.CoverPhoto]" => $this->system->PlainText([
        "Data" => "[sIMG:CP]",
        "Display" => 1
       ]),
       "[VIP.FAB]" => base64_encode("v=$search&st=FAB"),
       "[VIP.Forum]" => base64_encode("v=$forum&CARD=1&ID=$id")
      ], $this->system->Page("89d36f051962ca4bbfbcb1dc2bd41f60")]);
     } elseif($s == "XFS") {
      $r = $this->system->Change([[
       "[XFS.CoverPhoto]" => $this->system->PlainText([
        "Data" => "[sIMG:CP]",
        "Display" => 1
       ])
      ], $this->system->Page("dad7bf9214d25c12fa8a4543bbdb9d23")]);
     } if(strtotime($this->system->timestamp) > $y["Subscriptions"][$s]["E"]) {
      $y["Subscriptions"][$s]["A"] = 0;
      $this->system->Data("Save", ["mbr", md5($you), $y]);
      $r = $this->system->Page("a0891fc91ad185b6a99f1ba501b3c9be");
     }
    }
    $r = [
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
  function RenewAll(array $a) {
   $accessCode = "Denied";
   $r = [
    "Body" => "You do not have permission to access this view."
   ];
   $y = $this->you;
   if($y["Rank"] == md5("High Command")) {
    $accessCode = "Accepted";
    foreach($y["Subscriptions"] as $key => $value) {
     $y["Subscriptions"][$key] = [
      "A" => 1,
      "B" => $this->system->timestamp,
      "E" => $this->TimePlus($this->system->timestamp, 1, "year")
     ];
    }
    $this->system->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
    $r = [
     "Body" => "Your subscriptions have been renewed!",
     "Header" => "Done"
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
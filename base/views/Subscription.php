<?php
 Class Subscription extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Index(array $a) {
   $active = "";
   $ai = 0;
   $base = $this->system->base;
   $dt = "v=".base64_encode("Subscription:Home")."&sub=";
   $y = $this->you;
   foreach($y["Subscriptions"] as $k => $v) {
    $s = $this->system->core["SUB"][$k];
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:MiNY]",
     "Display" => 1
    ]);
    $s = $this->system->Change([[
     "[X.LI.D]" => $s["Description"],
     "[X.LI.DT]" => ".OHCC;Subscriptions;".base64_encode($dt.base64_encode($k)),
     "[X.LI.I]" => $this->system->CoverPhoto($coverPhoto),
     "[X.LI.T]" => $s["Title"]
    ], $this->system->Page("e7829132e382ee4ab843f23685a123cf")]);
    if($v["A"] == 1) {
     $active .= $s;
     $ai++;
    }
   } if($ai == 0 || $this->system->ID == $y["Login"]["Username"]) {
    $active = $this->system->Element([
     "h4", "No Active Subscriptions",
     ["class" => "CenterText InnerMargin UpperCase"]
    ]);
   }
   return $this->system->Change([[
    "[Container]" => "Subscriptions",
    "[Container.List]" => $this->system->Change([[
     "[Subscriptions.Active]" => $active
    ], $this->system->Page("81c6e3ce434e1b052240cf71ec7b1bc3")])
   ], $this->system->Page("46fc25c871bbcd0203216e329db12162")]);
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $s = $data["sub"] ?? base64_encode("");
   $s = base64_decode($s);
   $search = base64_encode("Search:Containers");
   $sub = $this->system->core["SUB"][$s] ?? [];
   $r = $this->system->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Subscription Identifier is missing."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $ysub = $y["Subscriptions"][$s] ?? [];
   if(!empty($s)) {
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
      if($commission > 0 && $paidCommission == 0) {
       $sales = $income["Sales"] ?? [];
       $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
       foreach($sales as $day => $salesGroup) {
        foreach($salesGroup as $daySales => $daySale) {
         foreach($daySale as $id => $product) {
          $price = $product["Cost"] + $product["Profit"];
          $price = $price * $product["Quantity"];
          $price = number_format($price, 2);
          $commission = $commission + $price;
         }
        }
       }
       $commission = number_format($commission, 2);
       $commission = number_format($commission * (5.00 / 100), 2);
       $shop["Open"] = 0;
       $r = $this->system->Change([[
        "[Container]" => "SUB_$s",
        "[Container.List]" => $this->system->Change([[
         "[Commission.FSTID]" => md5("Commission_Pay"),
         "[Commission.Pay]" => "v=".base64_encode("Pay:Commission")."&amount=".base64_encode($commission),
         "[Commission.Total]" => $commission
        ], $this->system->Page("f844c17ae6ce15c373c2bd2a691d0a9a")])
       ], $this->system->Page("46fc25c871bbcd0203216e329db12162")]);
       $this->system->Data("Save", ["shop", md5($you), $shop]);
       $this->system->Data("Save", ["mbr", md5($you), $y]);
      } else {
       $r = $this->system->Change([[
        "[Artist.Charts]" => "",
        "[Artist.Contributors]" => $this->view($search, ["Data" => [
         "ID" => base64_encode(md5($you)),
         "Type" => base64_encode("Shop"),
         "st" => "Contributors"
        ]]),
        "[Artist.CoverPhoto]" => $this->system->PlainText([
         "Data" => "[sIMG:CP]",
         "Display" => 1
        ]),
        "[Artist.Hire]" => base64_encode("v=".base64_encode("Shop:EditPartner")."&new=1"),
        "[Artist.Orders]" => $this->view($search, ["Data" => [
         "st" => "SHOP-Orders"
        ]]),
        "[Artist.ID]" => md5($you),
        "[Artist.Payroll]" => "v=".base64_encode("Shop:Payroll"),
        "[Artist.Revenue]" => "v=".base64_encode("Common:Income")."&UN=".base64_encode($you)
       ], $this->system->Page("20820f4afd96c9e32440beabed381d36")]);
      }
     } elseif($s == "Blogger") {
      $r = $this->system->Change([[
       "[Blogger.CoverPhoto]" => $this->system->PlainText([
        "Data" => "[sIMG:CP]",
        "Display" => 1
       ]),
       "[Blogger.List]" => $this->view($search, [
        "Data" => ["st" => "S-Blogger"]
       ]),
       "[Blogger.Title]" => $sub["Title"]
      ], $this->system->Page("566f9967f00f97350e54b0ee14faef36")]);
     } elseif($s == "Developer") {
      $r = $this->system->Change([[
       "[Developer.CoverPhoto]" => $this->system->PlainText([
        "Data" => "[sIMG:CP]",
        "Display" => 1
       ])
      ], $this->system->Page("c936edd5c57aca06897b44fed29d0843")]);
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
     $r = $this->system->Change([[
      "[Container]" => "SUB_$s",
      "[Container.List]" => $r
     ], $this->system->Page("46fc25c871bbcd0203216e329db12162")]);
    }
   }
   return $r;
  }
  function RenewAll(array $a) {
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "You do not have permission to access this view."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if($y["Rank"] == md5("High Command")) {
    foreach($y["Subscriptions"] as $key => $value) {
     $y["Subscriptions"][$key] = [
      "A" => 1,
      "B" => $this->system->timestamp,
      "E" => $this->TimePlus($this->system->timestamp, 1, "year")
     ];
    }
    $this->system->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "Your subscriptions have been renewed!"
     ]),
     "Header" => "Done"
    ]);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
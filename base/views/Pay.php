<?php
 Class Pay extends GW {
  function __construct() {
   parent::__construct();
   $this->root = $_SERVER["DOCUMENT_ROOT"]."/base/pay/Braintree.php";
   $this->you = $this->system->Member($this->system->Username());
  }
  function Checkout(array $a) {
   $data = $a["Data"] ?? [];
   $r = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = $data["UN"] ?? base64_encode($you);
   if(!empty($username)) {
    require_once($this->root);
    $username = base64_decode($username);
    $shop = $this->system->Data("Get", ["shop", md5($username)]) ?? [];
    $t = ($username == $you) ? $y : $this->system->Member($username);
    $braintree = $shop["Processing"] ?? [];
    $envrionment = ($shop["Live"] == 1) ? "production" : "sandbox";
    $token = base64_decode($braintree["BraintreeToken"]);
    $btmid = base64_decode($braintree["BraintreeMerchantID"]);
    $braintree = new Braintree_Gateway([
     "environment" => $envrionment,
     "merchantId" => $btmid,
     "privateKey" => base64_decode($braintree["BraintreePrivateKey"]),
     "publicKey" => base64_decode($braintree["BraintreePublicKey"])
    ]);
    $token = $braintree->clientToken()->generate([
     "merchantAccountId" => $btmid
    ]) ?? $token;
    $cart = $y["Shopping"]["Cart"][md5($username)]["Products"] ?? [];
    $cartCount = count($cart);
    $credits = $y["Shopping"]["Cart"][md5($username)]["Credits"] ?? 0;
    $credits = number_format($credits, 2);
    $discountCode = $y["Shopping"]["Cart"][md5($username)]["DiscountCode"] ?? 0;
    $now = $this->system->timestamp;
    $subtotal = 0;
    $total = 0;
    foreach($cart as $key => $value) {
     $product = $this->system->Data("Get", ["miny", $key]) ?? [];
     $ck = (strtotime($now) < $product["Expires"]) ? 1 : 0;
     if($ck == 1) {
      $price = str_replace(",", "", $product["Cost"]);
      $price = $price + str_replace(",", "", $product["Profit"]);
      $subtotal = $subtotal + $price;
     }
    } if($discountCode != 0) {
     $discountCode = $discountCode ?? [];
     $dollarAmount = $discountCode["DollarAmount"] ?? 0.00;
     $dollarAmount = number_format($dollarAmount, 2);
     $percentile = $discountCode["Percentile"] ?? 0;
     $percentile = $subtotal * ($percentile / 100);
     $check = ($dollarAmount > $percentile) ? "Dollars" : "Percentile";
     $discountCode = [
      "Amount" => $check,
      "Dollars" => $dollarAmount,
      "Percentile" => $percentile
     ];
     if($discountCode["Amount"] == "Dollars") {
      $discountCode = $discountCode["Dollars"];
     } else {
      $discountCode = number_format($discountCode["Percentile"], 2);
     }
    }
    $total = $subtotal - $credits - $discountCode;
    $tax = $shop["Tax"] ?? 10.00;
    $tax = number_format($total * ($tax / 100), 2);
    $r = $this->system->Change([[
     "[Checkout.FSTID]" => md5("Checkout_$btmid"),
     "[Checkout.ID]" => md5($btmid),
     "[Checkout.Processor]" => "v=".base64_encode("Pay:SaveCheckout")."&ID=".md5($username)."&UN=".$data["UN"]."&payment_method_nonce=",
     "[Checkout.Region]" => $this->system->region,
     "[Checkout.Title]" => $shop["Title"],
     "[Checkout.Token]" => $token,
     "[Checkout.Total]" => number_format($tax + $total, 2)
    ], $this->system->Page("a32d886447733485978116cc52d4f7aa")]);
   }
   return $r;
  }
  function Commission(array $a) {
   require_once($this->root);
   $data = $a["Data"] ?? [];
   $amount = $data["amount"] ?? base64_encode(0);
   $amount = number_format(base64_decode($amount), 2);
   $username = $this->system->ShopID;
   $shop = $this->system->Data("Get", ["shop", md5($username)]) ?? [];
   $braintree = $shop["Processing"] ?? [];
   $environment = ($shop["Live"] == 1) ? "production" : "sandbox";
   $token = base64_decode($braintree["BraintreeToken"]);
   $btmid = base64_decode($braintree["BraintreeMerchantID"]);
   $braintree = new Braintree_Gateway([
    "environment" => $environment,
    "merchantId" => $btmid,
    "privateKey" => base64_decode($braintree["BraintreePrivateKey"]),
    "publicKey" => base64_decode($braintree["BraintreePublicKey"])
   ]);
   $token = $braintree->clientToken()->generate([
    "merchantAccountId" => $btmid
   ]) ?? $token;
   return $this->system->Change([[
    "[Commission.Action]" => "pay your $$amount commission",
    "[Commission.FSTID]" => md5("Commission_$btmid"),
    "[Commission.ID]" => md5($btmid),
    "[Commission.Processor]" => "v=".base64_encode("Pay:SaveCommissionOrDonation")."&amount=".base64_encode($amount)."&ID=".md5($username)."&st=".base64_encode("Commission")."&payment_method_nonce=",
    "[Commission.Title]" => $shop["Title"],
    "[Commission.Region]" => $this->system->region,
    "[Commission.Token]" => $token,
    "[Commission.Total]" => $amount
   ], $this->system->Page("d84203cf19a999c65a50ee01bbd984dc")]);
  }
  function Donation(array $a) {
   require_once($this->root);
   $data = $a["Data"] ?? [];
   $amount = $data["amount"] ?? base64_encode(0);
   $amount = base64_decode($amount);
   $username = $this->system->ShopID;
   $shop = $this->system->Data("Get", ["shop", md5($username)]) ?? [];
   $braintree = $shop["Processing"] ?? [];
   $environment = ($shop["Live"] == 1) ? "production" : "sandbox";
   $token = base64_decode($braintree["BraintreeToken"]);
   $btmid = base64_decode($braintree["BraintreeMerchantID"]);
   $braintree = new Braintree_Gateway([
    "environment" => $environment,
    "merchantId" => $btmid,
    "privateKey" => base64_decode($braintree["BraintreePrivateKey"]),
    "publicKey" => base64_decode($braintree["BraintreePublicKey"])
   ]);
   $token = $braintree->clientToken()->generate([
    "merchantAccountId" => $btmid
   ]) ?? $token;
   return $this->system->Change([[
    "[Commission.Action]" => "donate $$amount",
    "[Commission.FSTID]" => md5("Donation_$btmid"),
    "[Commission.ID]" => md5($btmid),
    "[Commission.Processor]" => "v=".base64_encode("Pay:SaveCommissionOrDonation")."&amount=".$data["amount"]."&ID=".md5($username)."&st=".base64_encode("Donation")."&payment_method_nonce=",
    "[Commission.Title]" => $shop["Title"],
    "[Commission.Region]" => $this->system->region,
    "[Commission.Token]" => $token,
    "[Commission.Total]" => number_format($amount, 2)
   ], $this->system->Page("d84203cf19a999c65a50ee01bbd984dc")]);
  }
  function Partner(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["Month", "UN", "Year"]);
   $sp = base64_encode("Pay:PartnerComplete");
   $username = base64_decode($data["UN"]);
   $shop = $this->system->Data("Get", ["shop", md5($username)]) ?? [];
   $t = $this->system->Member($username);
   $braintree = $shop["Processing"] ?? [];
   $ck = $this->system->CheckBraintreeKeys($bt);
   $r = $this->system->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Member, Month, or Year Identifiers are missing, or the keys are missing."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Month"]) && !empty($data["UN"]) && !empty($data["Year"]) && $ck == 4) {
    $id = $this->system->Data("Get", ["id", md5($you)]) ?? [];
    $id = $id[$data["Year"]][$data["Month"]] ?? [];
    $partners = count($id["Partners"]);
    $sales = 0;
    foreach($id as $k => $v) {
     if($k == "Sales") {
      for($i = 0; $i < count($k); $i++) {
       foreach($v[$i] as $k2 => $v2) {
        $prc = $v2["Cost"] + $v2["Profit"];
        $prc = $prc * $v2["Quantity"];
        $sales = $sales + $prc;
       }
      }
     }
    }
    $r = $this->system->Change([
     [
      "[Error.Back]" => "",
      "[Error.Header]" => "No Payment Necessary",
      "[Error.Message]" => "You cannot pay ".$t["Personal"]["DisplayName"].", as there are no funds to disburse."
     ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")
    ]);
    if($s == 0) {
     $id[$data["Year"]][$data["Month"]][$username]["Paid"] = 1;
     $this->system->Data("Save", ["id", md5($y["Login"]["Username"]), $id]);
    } else {
     require_once($this->root);
     $env = ($shop["Live"] == 1) ? "production" : "sandbox";
     $btmid = base64_decode($braintree["BraintreeMerchantID"]);
     $btpubk = base64_decode($braintree["BraintreePublicKey"]);
     $braintree = new Braintree_Gateway([
      "environment" => $env,
      "merchantId" => $btmid,
      "privateKey" => base64_decode($braintree["BraintreePrivateKey"]),
      "publicKey" => $btpubk
     ]);
     $token = $braintree->clientToken()->generate([
      "merchantAccountId" => $btmid
     ]) ?? $btpubk;
     $total = $sales / $partners;
     $r = $this->system->Change([[
      "[Partner.Checkout]" => "v=$sp&Month=".$data["Month"]."&UN=".$data["UN"]."&Year=".$data["Year"]."&amount=".base64_encode($total)."&payment_method_nonce=",
      "[Partner.LastMonth]" => $this->system->ConvertCalendarMonths($data["Month"]),
      "[Partner.Pay.Amount]" => number_format($total, 2),
      "[Partner.Pay.FSTID]" => md5("PaymentComplete$username"),
      "[Partner.Pay.ID]" => md5($btmid),
      "[Partner.Pay.Region]" => $this->system->region,
      "[Partner.Pay.Token]" => $token,
      "[Partner.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:12.5% 25%;width:50%"),
      "[Partner.Username]" => $username
     ], $this->system->Page("6ed9bbbc61563b846b512acf94550806")]);
    }
   }
   return $r;
  }
  function PartnerComplete(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "UN",
    "amount",
    "payment_method_nonce"
   ]);
   $paymentNonce = $data["payment_method_nonce"];
   $r = $this->system->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Member Identifier or Payment Type are missing."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Month"]) && !empty($data["Year"]) && !empty($paymentNonce) && !empty($username)) {
    $username = base64_decode($username);
    $t = $this->system->Member($username);
    $shop = $this->system->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $braintree = $shop["Processing"] ?? [];
    $ck = $this->system->CheckBraintreeKeys($bt);
    if($ck == 4) {
     $env = ($shop["Live"] == 1) ? "production" : "sandbox";
     require_once($this->root);
     $braintree = new Braintree_Gateway([
      "environment" => $env,
      "merchantId" => base64_decode($braintree["BraintreeMerchantID"]),
      "privateKey" => base64_decode($braintree["BraintreePrivateKey"]),
      "publicKey" => base64_decode($braintree["BraintreePublicKey"])
     ]);
     $amount = $data["amount"] ?? base64_encode(0);
     $amount = number_format(base64_decode($amount), 2);
     $order = $braintree->transaction()->sale([
      "amount" => str_replace(",", "", $amount),
       "customer" => [
       "firstName" => $y["Personal"]["FirstName"]
      ],
      "options" => [
       "submitForSettlement" => true
      ],
      "paymentMethodNonce" => $paymentNonce
     ]);
     if($order->success) {
      $id = $this->system->Data("Get", ["id", md5($you)]) ?? [];
      $pc = number_format(0, 2);
      $pid = "DISBURSEMENTS*$username";
      /*$this->system->Revenue([$username, [
       "Cost" => $amount,
       "ID" => $pid,
       "Partners" => $shop["Contributors"],
       "Profit" => $pc,
       "Quantity" => 1,
       "Title" => $pid
      ]]);
      $this->system->Revenue([$you, [
       "Cost" => $amount,
       "ID" => $pid,
       "Partners" => $shop["Contributors"],
       "Profit" => $pc,
       "Quantity" => 1,
       "Title" => $pid
      ]]);*/
      $id[$data["Year"]][$data["Month"]]["Partners"][$username]["Paid"] = 1;
      $this->system->Data("Save", ["id", md5($you), $id]);
      $r = $this->system->Change([[
       "[Partner.Amount]" => $amount,
       "[Partner.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:12.5% 25%;width:50%"),
       "[Partner.Username]" => $username
      ], $this->system->Page("70881ae11e9353107ded2bed93620ef4")]);
     } else {
      $r = $this->system->Change([[
       "[Checkout.Order.Message]" => $order->message,
       "[Checkout.Order.Products]" => 1,
       "[Checkout.Order.Success]" => json_encode($order->success)
      ], $this->system->Page("229e494ec0f0f43824913a622a46dfca")]);
     }
    }
   }
   return $r;
  }
  function ProcessCartOrder(array $a) {
   $bundle = $a["Bundled"] ?? 0;
   $physicalOrders = $a["PhysicalOrders"] ?? [];
   $purchaseQuantity = $a["Product"]["Quantity"] ?? 1;
   $r = "";
   $shopOwner = $a["UN"] ?? "";
   $shopID = md5($shopOwner);
   $y = $a["You"] ?? $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shopOwner) && is_array($a["Product"])) {
    $history = $y["Shopping"]["History"][$shopID] ?? [];
    $id = $a["Product"]["ID"] ?? "";
    $product = $this->system->Data("Get", ["miny", $id]) ?? [];
    $shop = $this->system->Data("Get", ["shop", $shopID]) ?? [];
    $t = ($shopOwner == $you) ? $y : $this->system->Member($shopOwner);
    if(!empty($product)) {
     $bundledProducts = $product["Bundled"] ?? [];
     $contributors = $shop["Contributors"] ?? [];
     $now = $this->system->timestamp;
     $opt = "";
     $productExpires = $product["Expires"] ?? $now;
     if(strtotime($now) < $productExpires) {
      $category = $product["Category"];
      $coverPhoto = $product["ICO"] ?? $this->system->PlainText([
       "Data" => "[sIMG:MiNY]",
       "Display" => 1
      ]);
      $coverPhoto = base64_encode($coverPhoto);
      $points = $this->system->core["PTS"]["Products"];
      $quantity = $product["Quantity"] ?? 1;
      $subscriptionTerm = $product["SubscriptionTerm"] ?? "month";
      if($category == "ARCH") {
       # Architecture
      } elseif($category == "DLC") {
       # Downloadable Content
      } elseif($category == "DONATE") {
       # Donations
       $opt = $this->system->Element(["p", "Thank You for donating!"]);
      } elseif($category == "PHYS") {
       # Physical Products
       $opt = $this->system->Element(["button", "Contact the Seller", [
        "class" => "BB BBB v2 v2w"
       ]]);
       $physicalOrders[md5($this->system->timestamp.rand(0, 9999))] = [
        "Complete" => 0,
        "Instructions" => base64_encode($a["Product"]["Instructions"]),
        "ProductID" => $id,
        "Quantity" => $purchaseQuantity,
        "UN" => $you
       ];
      } elseif($category == "SUB") {
       $opt = $this->system->Element(["button", "Go to Subscription", [
        "class" => "BB BBB v2 v2w"
       ]]);
       if($id == "355fd2f096bdb49883590b8eeef72b9c") {
        # V.I.P. Subscription
        foreach($y["Subscriptions"] as $sk => $sv) {
         if(!in_array($sk, ["Artist", "Developer"])) {
          $y["Subscriptions"][$sk] = [
           "A" => 1,
           "B" => $now,
           "E" => $this->system->TimePlus($now, 1, $subscriptionTerm)
          ];
         }
        }
       } elseif($id == "39d05985f0667a69f3a725d5afd1265c") {
        $y["Subscriptions"]["Developer"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->system->TimePlus($now, 1, $subscriptionTerm)
        ];
       } elseif($id == "5bfb3f44cdb9d3f2cd969a23f0e37093") {
        $y["Subscriptions"]["XFS"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->system->TimePlus($now, 1, $subscriptionTerm)
        ];
       } elseif($id == "c7054e9c7955203b721d142dedc9e540") {
        $y["Subscriptions"]["Artist"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->system->TimePlus($now, 1, $subscriptionTerm)
        ];
       } elseif($id == "cc84143175d6ae2051058ee0079bd6b8") {
        $y["Subscriptions"]["Blogger"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->system->TimePlus($now, 1, $subscriptionTerm)
        ];
       }
      }
      $history[md5($id.$now.rand(0, 1776))] = [
       "ID" => $id,
       "Instructions" => $a["Product"]["Instructions"],
       "Quantity" => $purchaseQuantity,
       "Timestamp" => $now
      ];
      $product["Quantity"] = ($quantity > 0) ? $quantity - $purchaseQuantity : $quantity;
      $r .= $this->system->Change([[
       "[Product.Added]" => $this->system->TimeAgo($now),
       "[Product.ICO]" => $coverPhoto,
       "[Product.Description]" => $this->system->PlainText([
        "Data" => $product["Description"],
        "Display" => 1
       ]),
       "[Product.Options]" => $opt,
       "[Product.Quantity]" => $purchaseQuantity,
       "[Product.Title]" => $product["Title"]
      ], $this->system->Page("4c304af9fcf2153e354e147e4744eab6")]);
      $y["Shopping"]["History"][$shopID] = $history;
      $y["Points"] = $y["Points"] + $points[$category];
      if($bundle == 0) {
       $this->system->Revenue([$shopOwner, [
        "Cost" => $product["Cost"],
        "ID" => $id,
        "Partners" => $contributors,
        "Profit" => $product["Profit"],
        "Quantity" => $purchaseQuantity,
        "Title" => $product["Title"]
       ]]);
      } if($product["Quantity"] > 0) {
       $this->system->Data("Save", ["miny", $id, $product]);
      }
     } foreach($bundledProducts as $bundled) {
      $bundled = explode("-", base64_decode($bundled));
      $bundledProduct = $bundled[1] ?? "";
      $bundledProductShopOwner = $bundled[0] ?? "";
      if(!empty($bundledProduct) && !empty($bundledProductShopOwner)) {
       $cartOrder = $this->ProcessCartOrder([
        "PhysicalOrders" => $physicalOrders,
        "Product" => [
         "DiscountCode" => 0,
         "DiscountCredit" => 0,
         "ID" => $bundledProduct,
         "Instructions" => "",
         "Quantity" => 1
        ],
        "UN" => $bundledProductShopOwner,
        "You" => $y
       ]);
       $physicalOrders = ($cartOrder["ERR"] == 0) ? $cartOrder["PhysicalOrders"] : $physicalOrders;
       $r .= $cartOrder["Response"];
       $y = $cartOrder["Member"];
      }
     }
    }
    $r = [
     "ERR" => 0,
     "Member" => $y,
     "PhysicalOrders" => $physicalOrders,
     "Response" => $r
    ];
   } else {
    $r = [
     "ERR" => 1,
     "Parameters" => [],
     "Response" => $r
    ];
   }
   return $r;
  }
  function SaveCheckout(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["UN", "payment_method_nonce"]);
   $username = $data["UN"];
   $paymentNonce = $data["payment_method_nonce"];
   $r = $this->system->Change([[
    "[Checkout.Data]" => json_encode($data)
   ], $this->system->Page("f9ee8c43d9a4710ca1cfc435037e9abd")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($paymentNonce)) {
    require_once($this->root);
    $username = (!empty($username)) ? base64_decode($username) : $you;
    $t = ($username == $you) ? $y : $this->system->Member($username);
    $shopID = md5($username);
    $shop = $this->system->Data("Get", ["shop", $shopID]) ?? [];
    $braintree = $shop["Processing"] ?? [];
    $live = $shop["Live"] ?? 0;
    $environment = ($live == 1) ? "production" : "sandbox";
    $braintree = new Braintree_Gateway([
     "environment" => $environment,
     "merchantId" => base64_decode($braintree["BraintreeMerchantID"]),
     "privateKey" => base64_decode($braintree["BraintreePrivateKey"]),
     "publicKey" => base64_decode($braintree["BraintreePublicKey"])
    ]);
    $cart = $y["Shopping"]["Cart"][$shopID]["Products"] ?? [];
    $cartCount = count($cart);
    $credits = $y["Shopping"]["Cart"][$shopID]["Credits"] ?? 0;
    $credits = number_format($credits, 2);
    $discountCode = $y["Shopping"]["Cart"][$shopID]["DiscountCode"] ?? 0;
    $now = $this->system->timestamp;
    $subtotal = 0;
    $total = 0;
    foreach($cart as $key => $value) {
     $product = $this->system->Data("Get", ["miny", $key]) ?? [];
     $ck = (strtotime($now) < $product["Expires"]) ? 1 : 0;
     if($ck == 1) {
      $price = str_replace(",", "", $product["Cost"]);
      $price = $price + str_replace(",", "", $product["Profit"]);
      $subtotal = $subtotal + $price;
     }
    } if($discountCode != 0) {
     $discountCode = $discountCode ?? [];
     $dollarAmount = $discountCode["DollarAmount"] ?? 0.00;
     $dollarAmount = number_format($dollarAmount, 2);
     $percentile = $discountCode["Percentile"] ?? 0;
     $percentile = $subtotal * ($percentile / 100);
     $check = ($dollarAmount > $percentile) ? "Dollars" : "Percentile";
     $discountCode = [
      "Amount" => $check,
      "Dollars" => $dollarAmount,
      "Percentile" => $percentile
     ];
     if($discountCode["Amount"] == "Dollars") {
      $discountCode = $discountCode["Dollars"];
     } else {
      $discountCode = number_format($discountCode["Percentile"], 2);
     }
    }
    $total = $subtotal - $credits - $discountCode;
    $tax = $shop["Tax"] ?? 10.00;
    $tax = number_format($total * ($tax / 100), 2);
    $total = number_format($tax + $total, 2);
    $order = $braintree->transaction()->sale([
     "amount" => str_replace(",", "", $total),
     "customer" => [
      "firstName" => $y["Personal"]["FirstName"]
     ],
     "options" => [
      "submitForSettlement" => true
     ],
     "paymentMethodNonce" => $paymentNonce
    ]);
    if($order->success) {
     $physicalOrders = $this->system->Data("Get", ["po", $shopID]) ?? [];
     $r = "";
     foreach($cart as $key => $value) {
      $bundle = $value["Bundled"] ?? [];
      $bundle = (!empty($bundle)) ? 1 : 0;
      $value["ID"] = $value["ID"] ?? $key;
      $value["Quantity"] = $value["Quantity"] ?? 1;
      $cartOrder = $this->ProcessCartOrder([
       "Bundled" => $bundle,
       "PhysicalOrders" => $physicalOrders,
       "Product" => $value,
       "UN" => $username,
       "You" => $y
      ]);
      $physicalOrders = ($cartOrder["ERR"] == 0) ? $cartOrder["PhysicalOrders"] : $physicalOrders;
      $r .= $cartOrder["Response"];
      $y = $cartOrder["Member"];
     }
     $y["Shopping"]["Cart"][$shopID]["Credits"] = 0;
     $y["Shopping"]["Cart"][$shopID]["DiscountCode"] = 0;
     $y["Shopping"]["Cart"][$shopID]["Products"] = [];
     $r = $this->system->Change([[
      "[Checkout.Order]" => $r,
      "[Checkout.Title]" => $shop["Title"],
      "[Checkout.Total]" => $t
     ], $this->system->Page("83d6fedaa3fa042d53722ec0a757e910")]);
     $this->system->Data("Save", ["mbr", md5($you), $y]);
     $this->system->Data("Save", ["po", $shopID, $physicalOrders]);
    } else {
     $r = $this->system->Change([[
      "[Checkout.Order.Message]" => $order->message,
      "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
      "[Checkout.Order.Success]" => json_encode($order->success)
     ], $this->system->Page("229e494ec0f0f43824913a622a46dfca")]);
    }
   }
   return $r;
  }
  function SaveCommissionOrDonation(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "amount",
    "payment_method_nonce",
    "st"
   ]);
   $r = $this->system->Change([[
    "[Checkout.Data]" => json_encode($data)
   ], $this->system->Page("f9ee8c43d9a4710ca1cfc435037e9abd")]);
   $username = $this->system->ShopID;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["payment_method_nonce"])) {
    require_once($this->root);
    $amount = $data["amount"] ?? base64_encode(0);
    $amount = number_format(base64_decode($amount), 2);
    $points = $this->system->core["PTS"]["Donations"] ?? 100;
    $saleType = (!empty($data["st"])) ? base64_decode($data["st"]) : "";
    $shop = $this->system->Data("Get", ["shop", md5($username)]) ?? [];
    $braintree = $shop["Processing"] ?? [];
    $live = $shop["Live"] ?? 0;
    $environment = ($live == 1) ? "production" : "sandbox";
    $braintree = new Braintree_Gateway([
     "environment" => $environment,
     "merchantId" => base64_decode($braintree["BraintreeMerchantID"]),
     "privateKey" => base64_decode($braintree["BraintreePrivateKey"]),
     "publicKey" => base64_decode($braintree["BraintreePublicKey"])
    ]);
    $order = $braintree->transaction()->sale([
     "amount" => str_replace(",", "", $amount),
      "customer" => [
      "firstName" => $y["Personal"]["FirstName"]
     ],
     "options" => [
      "submitForSettlement" => true
     ],
     "paymentMethodNonce" => $data["payment_method_nonce"]
    ]);
    if($order->success) {
     $_MiNYContributors = $shop["Contributors"] ?? [];
     $now = $this->system->timestamp;
     if($saleType == "Commission") {
      $_LastMonth = $this->system->LastMonth()["LastMonth"];
      $_LastMonth = explode("-", $_LastMonth);
      $income = $this->system->Data("Get", ["id", md5($you)]) ?? [];
      $income[$_LastMonth[0]][$_LastMonth[1]]["PaidCommission"] = 1;
      $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
      $shop["Open"] = 1;
      $shopSaleID = "COMMISSION*".$shop["Title"];
      $this->system->Data("Save", ["id", md5($username), $income]);
      $this->system->Data("Save", ["shop", md5($you), $shop]);
      $this->system->Revenue([$username, [
       "Cost" => 0,
       "ID" => $shopSaleID,
       "Partners" => $_MiNYContributors,
       "Profit" => $amount,
       "Quantity" => 1,
       "Title" => $shopSaleID
      ]]);
      $y["Subscriptions"]["Artist"] = [
       "A" => 1,
       "B" => $now,
       "E" => $this->TimePlus($now, 1, "month")
      ];
     } elseif($saleType == "Donation") {
      $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
      $shopSaleID = "DONATION*".$shop["Title"];
      $this->system->Revenue([$username, [
       "Cost" => 0,
       "ID" => $shopSaleID,
       "Partners" => $_MiNYContributors,
       "Profit" => $amount,
       "Quantity" => 1,
       "Title" => $shopSaleID
      ]]);
     }
     $amount = "$$amount";
     $amount .= ($saleType == "Commission") ? " commission" : " donation";
     $message = ($saleType == "Commission") ? "You may now access your Artist dashboard." : "$points points have been added";
     $y["Points"] = $y["Points"] + $points;
     $this->system->Data("Save", ["mbr", md5($you), $y]);
     $r = $this->system->Change([[
      "[Commission.Message]" => $message,
      "[Commission.Type]" => $amount
     ], $this->system->Page("f2bea3c1ebf2913437fcfdc0c1601ce0")]);
    } else {
     $r = $this->system->Change([[
      "[Checkout.Order.Message]" => $order->message,
      "[Checkout.Order.Products]" => 1,
      "[Checkout.Order.Success]" => json_encode($order->success)
     ], $this->system->Page("229e494ec0f0f43824913a622a46dfca")]);
    }
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
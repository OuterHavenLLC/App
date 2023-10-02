<?php
 Class Pay extends GW {
  function __construct() {
   parent::__construct();
   $this->bt = $this->core->DocumentRoot."/base/pay/Braintree.php";
   $this->you = $this->core->Member($this->core->Username());
  }
  function Checkout(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Shop Identifier is missing"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = $data["UN"] ?? base64_encode($you);
   if(!empty($username)) {
    $accessCode = "Accepted";
    $username = base64_decode($username);
    $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $payments = $shop["Processing"] ?? [];
    $payments = $this->core->FixMissing($payments, [
     "BraintreeMerchantIDLive",
     "BraintreePrivateKeyLive",
     "BraintreePublicKeyLive",
     "BraintreeTokenLive",
     "PayPalClientID",
     "PayPalClientIDLive",
     "PayPalEmailLive"
    ]);
    $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
    $paymentProcessors = $this->core->config["Shop"]["PaymentProcessors"] ?? [];
    if($paymentProcessor == "Braintree") {
     require_once($this->bt);
     $envrionment = ($shop["Live"] == 1) ? "production" : "sandbox";
     $braintree = ($shop["Live"] == 1) ? [
      "MerchantID" => $payments["BraintreeMerchantIDLive"],
      "Token" => $payments["BraintreeTokenLive"],
      "PrivateKey" => $payments["BraintreePrivateKeyLive"],
      "PublicKey" => $payments["BraintreePublicKeyLive"]
     ] : [
      "MerchantID" => $payments["BraintreeMerchantID"],
      "Token" => $payments["BraintreeToken"],
      "PrivateKey" => $payments["BraintreePrivateKey"],
      "PublicKey" => $payments["BraintreePublicKey"]
     ];
     $token = base64_decode($braintree["Token"]);
     $merchantID = base64_decode($braintree["MerchantID"]);
     $braintree = new Braintree\Gateway([
      "environment" => $envrionment,
      "merchantId" => $merchantID,
      "privateKey" => base64_decode($braintree["PrivateKey"]),
      "publicKey" => base64_decode($braintree["PublicKey"])
     ]);
     $token = $braintree->clientToken()->generate([
      "merchantAccountId" => $merchantID
     ]) ?? $token;
    } elseif($paymentProcessor == "PayPal") {
     $paypal = ($shop["Live"] == 1) ? [
      "ClientID" => $payments["PayPalClientIDLive"]
     ] : [
      "ClientID" => $payments["PayPalClientID"]
     ];
    } if(in_array($paymentProcessor, $paymentProcessors)) {
     $cart = $y["Shopping"]["Cart"][md5($username)]["Products"] ?? [];
     $cartCount = count($cart);
     $credits = $y["Shopping"]["Cart"][md5($username)]["Credits"] ?? 0;
     $credits = number_format($credits, 2);
     $discountCode = $y["Shopping"]["Cart"][md5($username)]["DiscountCode"] ?? 0;
     $now = $this->core->timestamp;
     $subtotal = 0;
     $total = 0;
     foreach($cart as $key => $value) {
      $product = $this->core->Data("Get", ["product", $key]) ?? [];
      $quantity = $product["Quantity"] ?? 0;
      if(!empty($product) && $quantity != 0) {
       $productIsActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
       if($productIsActive == 1) {
        $price = str_replace(",", "", $product["Cost"]);
        $price = $price + str_replace(",", "", $product["Profit"]);
        $subtotal = $subtotal + $price;
       }
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
     if($paymentProcessor == "Braintree") {
      $r = $this->core->Change([[
       "[Checkout.FSTID]" => md5("Checkout_$merchantID"),
       "[Checkout.ID]" => md5($merchantID),
       "[Checkout.Processor]" => "v=".base64_encode("Pay:SaveCheckout")."&ID=".md5($username)."&UN=".$data["UN"]."&payment_method_nonce=",
       "[Checkout.Region]" => $this->core->region,
       "[Checkout.Title]" => $shop["Title"],
       "[Checkout.Token]" => $token,
       "[Checkout.Total]" => number_format($tax + $total, 2)
      ], $this->core->Page("a32d886447733485978116cc52d4f7aa")]);
     } elseif($paymentProcessor == "PayPal") {
      $clientID = base64_decode($paypal["ClientID"]);
      $r = $this->core->Change([[
       "[Checkout.ClientID]" => $clientID,
       "[Checkout.FSTID]" => md5("Checkout_$clientID"),
       "[Checkout.ID]" => md5($clientID),
       "[Checkout.Processor]" => "v=".base64_encode("Pay:SaveCheckout")."&ID=".md5($username)."&UN=".$data["UN"],
       "[Checkout.Title]" => $shop["Title"],
       "[Checkout.Total]" => str_replace(",", "", number_format($tax + $total, 2))
      ], $this->core->Page("b2144e711b28ac34d30725b7d91ac33b")]);
     }
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
  function Commission(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $amount = $data["amount"] ?? base64_encode(0);
   $amount = number_format(base64_decode($amount), 2);
   $username = $this->core->ShopID;
   $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
   $payments = $shop["Processing"] ?? [];
   $payments = $this->core->FixMissing($payments, [
    "BraintreeMerchantIDLive",
    "BraintreePrivateKeyLive",
    "BraintreePublicKeyLive",
    "BraintreeTokenLive",
    "PayPalClientID",
    "PayPalClientIDLive",
    "PayPalEmailLive"
   ]);
   $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
   $r = [
    "Body" => "Something went wrong..."
   ];
   if($paymentProcessor == "Braintree") {
    $accessCode = "Accepted";
    require_once($this->bt);
    $environment = ($shop["Live"] == 1) ? "production" : "sandbox";
    $token = base64_decode($payments["BraintreeToken"]);
    $merchantID = base64_decode($payments["BraintreeMerchantID"]);
    $braintree = new Braintree\Gateway([
     "environment" => $environment,
     "merchantId" => $merchantID,
     "privateKey" => base64_decode($payments["BraintreePrivateKey"]),
     "publicKey" => base64_decode($payments["BraintreePublicKey"])
    ]);
    $token = $braintree->clientToken()->generate([
     "merchantAccountId" => $merchantID
    ]) ?? $token;
    $r = $this->core->Change([[
     "[Commission.Action]" => "pay your $$amount commission",
     "[Commission.FSTID]" => md5("Commission_$merchantID"),
     "[Commission.ID]" => md5($merchantID),
     "[Commission.Processor]" => "v=".base64_encode("Pay:SaveCommissionOrDonation")."&amount=".base64_encode($amount)."&ID=".md5($username)."&st=".base64_encode("Commission")."&payment_method_nonce=",
     "[Commission.Title]" => $shop["Title"],
     "[Commission.Region]" => $this->core->region,
     "[Commission.Token]" => $token,
     "[Commission.Total]" => number_format($amount, 2),
     "[Commission.Total.String]" => str_replace(",", "", number_format($amount, 2))
    ], $this->core->Page("d84203cf19a999c65a50ee01bbd984dc")]);
   } elseif($paymentProcessor == "PayPal") {
    $accessCode = "Accepted";
    $paypal = ($shop["Live"] == 1) ? [
     "ClientID" => $payments["PayPalClientIDLive"]
    ] : [
     "ClientID" => $payments["PayPalClientID"]
    ];
    $clientID = base64_decode($paypal["ClientID"]);
    $r = $this->core->Change([[
     "[Commission.Action]" => "pay your $$amount commission",
     "[Commission.ClientID]" => $clientID,
     "[Commission.FSTID]" => md5("Commission_$clientID"),
     "[Commission.ID]" => md5($clientID),
     "[Commission.Processor]" => "v=".base64_encode("Pay:SaveCommissionOrDonation")."&amount=".base64_encode($amount)."&ID=".md5($username)."&st=".base64_encode("Commission"),
     "[Commission.Title]" => $shop["Title"],
     "[Commission.Total]" => number_format($amount, 2),
     "[Commission.Total.String]" => str_replace(",", "", number_format($amount, 2))
    ], $this->core->Page("55cdc1a2ae60bf6bc766f59905358152")]);
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
  function Donation(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $amount = $data["amount"] ?? base64_encode(0);
   $amount = base64_decode($amount);
   $username = $this->core->ShopID;
   $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
   $payments = $shop["Processing"] ?? [];
   $payments = $this->core->FixMissing($payments, [
    "BraintreeMerchantIDLive",
    "BraintreePrivateKeyLive",
    "BraintreePublicKeyLive",
    "BraintreeTokenLive",
    "PayPalClientID",
    "PayPalClientIDLive",
    "PayPalEmailLive"
   ]);
   $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
   $r = [
    "Body" => "Something went wrong..."
   ];
   if($paymentProcessor == "Braintree") {
    $accessCode = "Accepted";
    require_once($this->bt);
    $environment = ($shop["Live"] == 1) ? "production" : "sandbox";
    $braintree = ($shop["Live"] == 1) ? [
     "MerchantID" => $payments["BraintreeMerchantIDLive"],
     "PrivateKey" => $payments["BraintreePrivateKeyLive"],
     "PublicKey" => $payments["BraintreePublicKeyLive"],
     "Token" => $payments["BraintreeTokenLive"]
    ] : [
     "MerchantID" => $payments["BraintreeMerchantID"],
     "PrivateKey" => $payments["BraintreePrivateKey"],
     "PublicKey" => $payments["BraintreePublicKey"],
     "Token" => $payments["BraintreeToken"]
    ];
    $merchantID = base64_decode($braintree["MerchantID"]);
    $token = base64_decode($braintree["Token"]);
    $braintree = new Braintree\Gateway([
     "environment" => $environment,
     "merchantId" => $merchantID,
     "privateKey" => base64_decode($braintree["PrivateKey"]),
     "publicKey" => base64_decode($braintree["PublicKey"])
    ]);
    $token = $braintree->clientToken()->generate([
     "merchantAccountId" => $merchantID
    ]) ?? $token;
    $r = $this->core->Change([[
     "[Commission.Action]" => "donate $$amount",
     "[Commission.FSTID]" => md5("Donation_$merchantID"),
     "[Commission.ID]" => md5($merchantID),
     "[Commission.Processor]" => "v=".base64_encode("Pay:SaveCommissionOrDonation")."&amount=".$data["amount"]."&ID=".md5($username)."&st=".base64_encode("Donation")."&payment_method_nonce=",
     "[Commission.Title]" => $shop["Title"],
     "[Commission.Region]" => $this->core->region,
     "[Commission.Token]" => $token,
     "[Commission.Total]" => number_format($amount, 2),
     "[Commission.Total.String]" => str_replace(",", "", number_format($amount, 2))
    ], $this->core->Page("d84203cf19a999c65a50ee01bbd984dc")]);
   } elseif($paymentProcessor == "PayPal") {
    $accessCode = "Accepted";
    $paypal = ($shop["Live"] == 1) ? [
     "ClientID" => $payments["PayPalClientIDLive"]
    ] : [
     "ClientID" => $payments["PayPalClientID"]
    ];
    $clientID = base64_decode($paypal["ClientID"]);
    $r = $this->core->Change([[
     "[Commission.Action]" => "donate $$amount",
     "[Commission.ClientID]" => $clientID,
     "[Commission.FSTID]" => md5("Donation_$clientID"),
     "[Commission.ID]" => md5($clientID),
     "[Commission.Processor]" => "v=".base64_encode("Pay:SaveCommissionOrDonation")."&amount=".$data["amount"]."&ID=".md5($username)."&st=".base64_encode("Donation"),
     "[Commission.Title]" => $shop["Title"],
     "[Commission.Total]" => number_format($amount, 2),
     "[Commission.Total.String]" => str_replace(",", "", number_format($amount, 2))
    ], $this->core->Page("55cdc1a2ae60bf6bc766f59905358152")]);
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
  function Disbursement(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "Amount",
    "Month",
    "UN",
    "Year"
   ]);
   $amount = $data["Amount"];
   $username = $data["UN"];
   $r = [
    "Body" => "The Member, Month, or Year Identifiers are missing, or the keys are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Amount"]) && !empty($data["Month"]) && !empty($data["UN"]) && !empty($data["Year"])) {
    $amount = base64_decode($amount);
    $username = base64_decode($data["UN"]);
    $t = $this->core->Member($username);
    $r = [
     "Body" => "You cannot pay ".$t["Personal"]["DisplayName"]." as there are no funds to disburse. Funds: $amount."
    ];
    if($amount == 0) {
     $income = $this->core->Data("Get", ["id", md5($you)]) ?? [];
     $income[$data["Year"]][$data["Month"]][$username]["Paid"] = 1;
     #$this->core->Data("Save", ["id", md5($you), $income]);
    } else {
     $accessCode = "Accepted";
     $amount = number_format($amount, 2);
     $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
     $payments = $shop["Processing"] ?? [];
     $payments = $this->core->FixMissing($payments, [
      "BraintreeMerchantIDLive",
      "BraintreePrivateKeyLive",
      "BraintreePublicKeyLive",
      "BraintreeTokenLive",
      "PayPalClientID",
      "PayPalClientIDLive",
      "PayPalEmailLive"
     ]);
     $paymentProcessor = $payments["PaymentProcessor"] ?? "PayPal";
     if($paymentProcessor == "Braintree") {
      require_once($this->bt);
      $environment = ($shop["Live"] == 1) ? "production" : "sandbox";
      $braintree = ($shop["Live"] == 1) ? [
       "MerchantID" => $payments["BraintreeMerchantIDLive"],
       "PrivateKey" => $payments["BraintreePrivateKeyLive"],
       "PublicKey" => $payments["BraintreePublicKeyLive"],
       "Token" => $payments["BraintreeTokenLive"]
      ] : [
       "MerchantID" => $payments["BraintreeMerchantID"],
       "PrivateKey" => $payments["BraintreePrivateKey"],
       "PublicKey" => $payments["BraintreePublicKey"],
       "Token" => $payments["BraintreeToken"]
      ];
      $merchantID = base64_decode($braintree["MerchantID"]);
      $token = base64_decode($braintree["Token"]);
      $braintree = new Braintree\Gateway([
       "environment" => $environment,
       "merchantId" => $merchantID,
       "privateKey" => base64_decode($braintree["PrivateKey"]),
       "publicKey" => base64_decode($braintree["PublicKey"])
      ]);
      $token = $braintree->clientToken()->generate([
       "merchantAccountId" => $merchantID
      ]) ?? $token;
     } elseif($paymentProcessor == "PayPal") {
      $paypal = ($shop["Live"] == 1) ? [
       "ClientID" => $payments["PayPalClientIDLive"]
      ] : [
       "ClientID" => $payments["PayPalClientID"]
      ];
      $clientID = base64_decode($paypal["ClientID"]);
     } if($paymentProcessor == "Braintree") {
      $r = $this->core->Change([[
       "[Partner.Checkout]" => "v=".base64_encode("Pay:DisbursementComplete")."&Month=".$data["Month"]."&UN=".$data["UN"]."&Year=".$data["Year"]."&amount=".base64_encode($amount)."&payment_method_nonce=",
       "[Partner.LastMonth]" => $this->core->ConvertCalendarMonths($data["Month"]),
       "[Partner.Pay.Amount]" => $amount,
       "[Partner.Pay.FSTID]" => md5("PaymentComplete$merchantID"),
       "[Partner.Pay.ID]" => md5($merchantID),
       "[Partner.Pay.Region]" => $this->core->region,
       "[Partner.Pay.Token]" => $token,
       "[Partner.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:12.5% 25%;width:50%"),
       "[Partner.Username]" => $username
      ], $this->core->Page("6ed9bbbc61563b846b512acf94550806")]);
     } elseif($paymentProcessor == "PayPal") {
      $r = $this->core->Change([[
       "[Partner.Checkout]" => "v=".base64_encode("Pay:DisbursementComplete")."&Month=".$data["Month"]."&UN=".$data["UN"]."&Year=".$data["Year"]."&amount=".base64_encode($amount),
       "[Partner.ClientID]" => $clientID,
       "[Partner.LastMonth]" => $this->core->ConvertCalendarMonths($data["Month"]),
       "[Partner.Pay.Amount]" => $amount,
       "[Partner.Pay.ID]" => md5("PaymentComplete$clientID"),
       "[Partner.Pay.Total]" => str_replace(",", "", $amount),
       "[Partner.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:12.5% 25%;width:50%"),
       "[Partner.Username]" => $username
      ], $this->core->Page("0c7719c7da9bbead3fea3bffe65294f4")]);
     }
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
  function DisbursementComplete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "Month",
    "UN",
    "Year",
    "amount",
    "payment_method_nonce",
    "order_ID"
   ]);
   $r = [
    "Body" => "The Member Identifier or Payment Type are missing."
   ];
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Month"]) && !empty($data["Year"]) && !empty($username)) {
    $accessCode = "Accepted";
    $amount = $data["amount"] ?? base64_encode(0);
    $amount = str_replace(",", "", base64_decode($amount));
    $amount = number_format($amount, 2);
    $orderID = $data["order_ID"] ?? "N/A";
    $username = base64_decode($username);
    $t = $this->core->Member($username);
    $shop = $this->core->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $live = $shop["Live"] ?? 0;
    $payments = $shop["Processing"] ?? [];
    $payments = $this->core->FixMissing($payments, [
     "BraintreeMerchantIDLive",
     "BraintreePrivateKeyLive",
     "BraintreePublicKeyLive",
     "BraintreeTokenLive",
     "PayPalClientID",
     "PayPalClientIDLive",
     "PayPalEmailLive"
    ]);
    $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
    if($paymentProcessor == "Braintree") {
     $paymentNonce = $data["payment_method_nonce"];
     if(!empty($paymentNonce)) {
      require_once($this->bt);
      $envrionment = ($shop["Live"] == 1) ? "production" : "sandbox";
      $braintree = ($shop["Live"] == 1) ? [
       "MerchantID" => $payments["BraintreeMerchantIDLive"],
       "Token" => $payments["BraintreeTokenLive"],
       "PrivateKey" => $payments["BraintreePrivateKeyLive"],
       "PublicKey" => $payments["BraintreePublicKeyLive"]
      ] : [
       "MerchantID" => $payments["BraintreeMerchantID"],
       "Token" => $payments["BraintreeToken"],
       "PrivateKey" => $payments["BraintreePrivateKey"],
       "PublicKey" => $payments["BraintreePublicKey"]
      ];
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
      $ck = ($order->success) ? 1 : 0;
      $order->message = $order->message ?? "N/A";
      $r = $this->core->Change([[
       "[Checkout.Order.Message]" => $order->message,
       "[Checkout.Order.Products]" => 1,
       "[Checkout.Order.Success]" => $order->success
      ], $this->core->Page("229e494ec0f0f43824913a622a46dfca")]);
     }
    } elseif($paymentProcessor == "PayPal") {
     $ck = (!empty($orderID)) ? 1 : 0;
     $orderID = base64_decode($orderID);
    } if($ck == 1) {
     $id = "DISBURSEMENTS*$username";
     $income = $this->core->Data("Get", ["id", md5($you)]) ?? [];
     $profit = number_format(0, 2);
     $this->core->Revenue([$username, [
      "Cost" => $amount,
      "ID" => $id,
      "Partners" => $shop["Contributors"],
      "Profit" => $profit,
      "Quantity" => 1,
      "Title" => $id
     ]]);
     $this->core->Revenue([$you, [
      "Cost" => $amount,
      "ID" => $id,
      "Partners" => $shop["Contributors"],
      "Profit" => $profit,
      "Quantity" => 1,
      "Title" => $id
     ]]);
     $income[$data["Year"]][$data["Month"]]["Partners"][$username]["Paid"] = 1;
     $this->core->Data("Save", ["id", md5($you), $income]);
     $r = $this->core->Change([[
      "[Partner.Amount]" => $amount,
      "[Partner.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:12.5% 25%;width:50%"),
      "[Partner.Username]" => $username
     ], $this->core->Page("70881ae11e9353107ded2bed93620ef4")]);
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
  function ProcessCartOrder(array $a) {
   $accessCode = "Accepted";
   $bundle = $a["Bundled"] ?? 0;
   $orderID = $a["PayPalOrderID"] ?? "N/A";
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
    $product = $this->core->Data("Get", ["product", $id]) ?? [];
    $quantity = $product["Quantity"] ?? 0;
    $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
    $t = ($shopOwner == $you) ? $y : $this->core->Member($shopOwner);
    if(!empty($product) && $quantity != 0) {
     $bundledProducts = $product["Bundled"] ?? [];
     $contributors = $shop["Contributors"] ?? [];
     $now = $this->core->timestamp;
     $opt = "";
     $productExpires = $product["Expires"] ?? $now;
     if(strtotime($now) < $productExpires) {
      $category = $product["Category"];
      $coverPhoto = $product["ICO"] ?? $this->core->PlainText([
       "Data" => "[sIMG:MiNY]",
       "Display" => 1
      ]);
      $coverPhoto = base64_encode($coverPhoto);
      $points = $this->core->config["PTS"]["Products"];
      $quantity = $product["Quantity"] ?? 1;
      $subscriptionTerm = $product["SubscriptionTerm"] ?? "month";
      if($category == "ARCH") {
       # Architecture
      } elseif($category == "DLC") {
       # Downloadable Content
      } elseif($category == "DONATE") {
       # Donations
       $opt = $this->core->Element(["p", "Thank You for donating!"]);
      } elseif($category == "PHYS") {
       # Physical Products
       $opt = $this->core->Element(["button", "Contact the Seller", [
        "class" => "BB BBB v2 v2w"
       ]]);
       $physicalOrders[md5($this->core->timestamp.rand(0, 9999))] = [
        "Complete" => 0,
        "Instructions" => base64_encode($a["Product"]["Instructions"]),
        "ProductID" => $id,
        "Quantity" => $purchaseQuantity,
        "UN" => $you
       ];
      } elseif($category == "SUB") {
       $opt = $this->core->Element(["button", "Go to Subscription", [
        "class" => "BB BBB v2 v2w"
       ]]);
       if($id == "355fd2f096bdb49883590b8eeef72b9c") {
        # V.I.P. Subscription
        foreach($y["Subscriptions"] as $sk => $sv) {
         if($sk == "Artist") {
          $y["Subscriptions"][$sk] = [
           "A" => 1,
           "B" => $now,
           "E" => $this->core->TimePlus($now, 1, $subscriptionTerm)
          ];
         }
        }
       } elseif($id == "5bfb3f44cdb9d3f2cd969a23f0e37093") {
        $y["Subscriptions"]["XFS"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->core->TimePlus($now, 1, $subscriptionTerm)
        ];
       } elseif($id == "c7054e9c7955203b721d142dedc9e540") {
        $y["Subscriptions"]["Artist"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->core->TimePlus($now, 1, $subscriptionTerm)
        ];
       } elseif($id == "cc84143175d6ae2051058ee0079bd6b8") {
        $y["Subscriptions"]["Blogger"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->core->TimePlus($now, 1, $subscriptionTerm)
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
      $r .= $this->core->Change([[
       "[Product.Added]" => $this->core->TimeAgo($now),
       "[Product.ICO]" => $coverPhoto,
       "[Product.Description]" => $this->core->PlainText([
        "Data" => $product["Description"],
        "Display" => 1
       ]),
       "[Product.Options]" => $opt,
       "[Product.OrderID]" => $orderID,
       "[Product.Quantity]" => $purchaseQuantity,
       "[Product.Title]" => $product["Title"]
      ], $this->core->Page("4c304af9fcf2153e354e147e4744eab6")]);
      $y["Shopping"]["History"][$shopID] = $history;
      $y["Points"] = $y["Points"] + $points[$category];
      if($bundle == 0) {
       /*$this->core->Revenue([$shopOwner, [
        "Cost" => $product["Cost"],
        "ID" => $id,
        "Partners" => $contributors,
        "Profit" => $product["Profit"],
        "Quantity" => $purchaseQuantity,
        "Title" => $product["Title"]
       ]]);*/
      } if($product["Quantity"] > 0) {
       #$this->core->Data("Save", ["product", $id, $product]);
      }
     } foreach($bundledProducts as $bundled) {
      $bundled = explode("-", base64_decode($bundled));
      $bundledProduct = $bundled[1] ?? "";
      $bundledProductShopOwner = $bundled[0] ?? "";
      if(!empty($bundledProduct) && !empty($bundledProductShopOwner)) {
       $cartOrder = $this->ProcessCartOrder([
        "PayPalOrderID" => $orderID,
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
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SaveCheckout(array $a) {
   $accessCode = "Accepted";
   $ck = 0;
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "UN",
    "order_ID",
    "payment_method_nonce"
   ]);
   $orderID = $data["order_ID"] ?? "";
   $r = $this->core->Change([[
    "[Checkout.Data]" => json_encode($data)
   ], $this->core->Page("f9ee8c43d9a4710ca1cfc435037e9abd")]);
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($username)) {
    $username = (!empty($username)) ? base64_decode($username) : $you;
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $shopID = md5($username);
    $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
    $live = $shop["Live"] ?? 0;
    $payments = $shop["Processing"] ?? [];
    $payments = $this->core->FixMissing($payments, [
     "BraintreeMerchantIDLive",
     "BraintreePrivateKeyLive",
     "BraintreePublicKeyLive",
     "BraintreeTokenLive",
     "PayPalClientID",
     "PayPalClientIDLive",
     "PayPalEmailLive"
    ]);
    $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
    $cart = $y["Shopping"]["Cart"][$shopID]["Products"] ?? [];
    $cartCount = count($cart);
    $credits = $y["Shopping"]["Cart"][$shopID]["Credits"] ?? 0;
    $credits = number_format($credits, 2);
    $discountCode = $y["Shopping"]["Cart"][$shopID]["DiscountCode"] ?? 0;
    $now = $this->core->timestamp;
    $subtotal = 0;
    $total = 0;
    foreach($cart as $key => $value) {
     $product = $this->core->Data("Get", ["product", $key]) ?? [];
     $quantity = $product["Quantity"] ?? 0;
     if(!empty($product) && $quantity != 0) {
      $productIsActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
      if($productIsActive == 1) {
       $price = str_replace(",", "", $product["Cost"]);
       $price = $price + str_replace(",", "", $product["Profit"]);
       $subtotal = $subtotal + $price;
      }
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
    if($paymentProcessor == "Braintree") {
     $orderID = "N/A";
     $paymentNonce = $data["payment_method_nonce"];
     if(!empty($paymentNonce)) {
      require_once($this->bt);
      $envrionment = ($shop["Live"] == 1) ? "production" : "sandbox";
      $braintree = ($shop["Live"] == 1) ? [
       "MerchantID" => $payments["BraintreeMerchantIDLive"],
       "Token" => $payments["BraintreeTokenLive"],
       "PrivateKey" => $payments["BraintreePrivateKeyLive"],
       "PublicKey" => $payments["BraintreePublicKeyLive"]
      ] : [
       "MerchantID" => $payments["BraintreeMerchantID"],
       "Token" => $payments["BraintreeToken"],
       "PrivateKey" => $payments["BraintreePrivateKey"],
       "PublicKey" => $payments["BraintreePublicKey"]
      ];
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
      $ck = ($order->success) ? 1 : 0;
      $order->message = $order->message ?? "N/A";
      $r = $this->core->Change([[
       "[Checkout.Order.Message]" => $order->message,
       "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
       "[Checkout.Order.Success]" => $order->success
      ], $this->core->Page("229e494ec0f0f43824913a622a46dfca")]);
     }
    } elseif($paymentProcessor == "PayPal") {
     $ck = (!empty($orderID)) ? 1 : 0;
     $orderID = base64_decode($orderID);
    } if($ck == 1) {
     $points = $y["Points"] ?? 0;
     $physicalOrders = $this->core->Data("Get", ["po", $shopID]) ?? [];
     $r = "";
     foreach($cart as $key => $value) {
      $product = $this->core->Data("Get", ["product", $key]) ?? [];
      if(!empty($product)) {
       $bundle = $value["Bundled"] ?? [];
       $bundle = (!empty($bundle)) ? 1 : 0;
       $isActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
       $isInStock = $product["Quantity"] ?? 0;
       $isInStock = ($isInStock != 0) ? 1 : 0;
       $value["ID"] = $value["ID"] ?? $key;
       $value["Quantity"] = $value["Quantity"] ?? 1;
       if($isActive == 0 || $isInStock == 0) {
        $price = str_replace(",", "", $product["Cost"]);
        $price = $price + str_replace(",", "", $product["Profit"]);
        $points = $points + ($price * 10000);
       } else {
        $cartOrder = $this->ProcessCartOrder([
         "Bundled" => $bundle,
         "PayPalOrderID" => $orderID,
         "PhysicalOrders" => $physicalOrders,
         "Product" => $value,
         "UN" => $username,
         "You" => $y
        ]);
        $physicalOrders = ($cartOrder["ERR"] == 0) ? $cartOrder["PhysicalOrders"] : $physicalOrders;
        $r .= $cartOrder["Response"];
        $y = $cartOrder["Member"];
       }
      }
     }
     $y["Points"] = $points;
     $y["Shopping"]["Cart"][$shopID]["Credits"] = 0;
     $y["Shopping"]["Cart"][$shopID]["DiscountCode"] = 0;
     $y["Shopping"]["Cart"][$shopID]["Products"] = [];
     $y["Verified"] = 1;
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     $this->core->Data("Save", ["po", $shopID, $physicalOrders]);
     $r = $this->core->Change([[
      "[Checkout.Order]" => $r,
      "[Checkout.Title]" => $shop["Title"],
      "[Checkout.Total]" => $total
     ], $this->core->Page("83d6fedaa3fa042d53722ec0a757e910")]);
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
  function SaveCommissionOrDonation(array $a) {
   $accessCode = "Accepted";
   $ck = 0;
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "amount",
    "order_ID",
    "payment_method_nonce",
    "st"
   ]);
   $amount = $data["amount"] ?? base64_encode(0);
   $amount = number_format(base64_decode($amount), 2);
   $r = $this->core->Change([[
    "[Checkout.Data]" => json_encode($data)
   ], $this->core->Page("f9ee8c43d9a4710ca1cfc435037e9abd")]);
   $username = $this->core->ShopID;
   $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
   $payments = $shop["Processing"] ?? [];
   $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($paymentProcessor == "Braintree") {
    $paymentNonce = $data["payment_method_nonce"];
    if(!empty($paymentNonce)) {
     require_once($this->bt);
     $live = $shop["Live"] ?? 0;
     $environment = ($live == 1) ? "production" : "sandbox";
     $braintree = new Braintree\Gateway([
      "environment" => $environment,
      "merchantId" => base64_decode($payments["BraintreeMerchantID"]),
      "privateKey" => base64_decode($payments["BraintreePrivateKey"]),
      "publicKey" => base64_decode($payments["BraintreePublicKey"])
     ]);
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
     $ck = ($order->success) ? 1 : 0;
     $order->message = $order->message ?? "N/A";
     $r = $this->core->Change([[
      "[Checkout.Order.Message]" => $order->message,
      "[Checkout.Order.Products]" => 1,
      "[Checkout.Order.Success]" => $order->success
     ], $this->core->Page("229e494ec0f0f43824913a622a46dfca")]);
    }
   } elseif($paymentProcessor == "PayPal") {
    $ck = (!empty($data["order_ID"])) ? 1 : 0;
   } if($ck == 1) {
    $_MiNYContributors = $shop["Contributors"] ?? [];
    $points = $this->core->config["PTS"]["Donations"] ?? 100;
    $saleType = (!empty($data["st"])) ? base64_decode($data["st"]) : "";
    $now = $this->core->timestamp;
    if($saleType == "Commission") {
     $_LastMonth = $this->core->LastMonth()["LastMonth"];
     $_LastMonth = explode("-", $_LastMonth);
     $income = $this->core->Data("Get", ["id", md5($you)]) ?? [];
     $income[$_LastMonth[0]][$_LastMonth[1]]["PaidCommission"] = 1;
     $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
     $shop["Open"] = 1;
     $shopSaleID = "COMMISSION*".$shop["Title"];
     $this->core->Data("Save", ["id", md5($username), $income]);
     $this->core->Data("Save", ["shop", md5($you), $shop]);
     $this->core->Revenue([$username, [
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
     $from = ($this->core->ID == $you) ? "Anonymous" : $you;
     $shopSaleID = "DONATION*$from";
     $this->core->Revenue([$username, [
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
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->core->Change([[
     "[Commission.Message]" => $message,
     "[Commission.Type]" => $amount
    ], $this->core->Page("f2bea3c1ebf2913437fcfdc0c1601ce0")]);
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
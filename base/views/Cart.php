<?php
 Class Cart extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Add(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "T"]);
   $id = $data["ID"];
   $r = [
    "Body" => "You must be signed in to make purchases.",
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["T"]) && $this->core->ID != $you) {
    $sub = $y["Subscriptions"]["Artist"]["A"] ?? 0;
    $t = ($data["T"] == $you) ? $y : $this->core->Member($data["T"]);
    $shop = $this->core->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    if($sub == 0 && $id == "c7054e9c7955203b721d142dedc9e540") {
     $r = [
      "Body" => "Pay your commisiion via the Subscriptions page, and you will automatically be subscribed.",
      "Header" => "Commission Due"
     ];
    } else {
     $accessCode = "Accepted";
     $product = $this->core->Data("Get", ["product", $id]) ?? [];
     $category = $product["Category"] ?? "";
     $hasInstructions = $product["Instructions"] ?? "";
     $productQuantity = $product["Quantity"] ?? 0;
     $id = $product["ID"] ?? "";
     $quantities = [];
     $quantity = $product["Quantity"] ?? 0;
     $ck = (!empty($id)) ? 1 : 0;
     $ck2 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $ck3 = $shop["Open"] ?? 0;
     $ck4 = ($quantity != 0) ? 1 : 0;
     $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) ? 1 : 0;
     for($i = 0; $i <= $productQuantity; $i++) {
      $quantities[$i] = $i;
     } if($ck == 1 || ($t["Login"]["Username"] == $this->core->ShopID && $quantity != 0)) {
      $instructions = ($category == "Product" && $hasInstructions == 1) ? $this->core->Element([
       "p", "Please add your shipping address.",
       ["class" => "CenterText"]
      ]) : "";
      $instructions .= ($hasInstructions == 1) ? $this->core->Element([
       "textarea", NULL, [
        "name" => "Instructions",
        "placeholder" => "Write your instructions here..."
       ]
      ]) : "";
      $lowStock = ($quantity > 0 && $quantity < 20) ? $this->core->Element([
       "p", "This is selling fast, act soon before it's sold out!",
       ["class" => "CenterText"]
      ]) : "";
      $price = $product["Cost"] + $product["Profit"];
      $quantity = ($category == "Product" && $quantity > 0) ? [
       "Attributes" => [],
       "OptionGroup" => $quantities,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull"
       ],
       "Name" => "Quantity",
       "Title" => "Quantity",
       "Type" => "Select",
       "Value" => $quantity
      ] : [
       "Attributes" => [
        "name" => "Quantity",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => 1
      ];
      $quantity = json_encode([
       $quantity
      ], true);
      $r = $this->core->Change([[
       "[AddToCart.Data]" => base64_encode("v=".base64_encode("Cart:SaveAdd")),
       "[AddToCart.Product.ID]" => $id,
       "[AddToCart.Product.Instructions]" => $instructions,
       "[AddToCart.Product.LowStock]" => $lowStock,
       "[AddToCart.Product.Price]" => number_format($price, 2),
       "[AddToCart.Product.Quantity]" => $quantity,
       "[AddToCart.Shop.ID]" => md5($t["Login"]["Username"]),
       "[AddToCart.Shop.Owner]" => $t["Login"]["Username"]
      ], $this->core->Page("624bcc664e9bff0002e01583e7706d83")]);
      if(($category == "Product") && $t["Login"]["Username"] == $you) {
       $r = $this->core->Element([
        "p", "Physical orders are disabled as you own this shop.",
        ["class" => "CenterText"]
       ]);
      } elseif($category == "Subscription") {
       $sub = $this->core->Element([
        "h4", "Already Subscribed",
        ["class" => "UpperCase CenterText"]
       ]);
       if($id == "355fd2f096bdb49883590b8eeef72b9c") {
        $r = ($y["Subscriptions"]["VIP"]["A"] == 1) ? $sub : $r;
       } elseif($id == "39d05985f0667a69f3a725d5afd1265c") {
        $r = ($y["Subscriptions"]["Developer"]["A"] == 1) ? $sub : $r;
       } elseif($id == "5bfb3f44cdb9d3f2cd969a23f0e37093") {
        $r = ($y["Subscriptions"]["XFS"]["A"] == 1) ? $sub : $r;
       } elseif($id == "c7054e9c7955203b721d142dedc9e540") {
        $r = ($y["Subscriptions"]["Artist"]["A"] == 1) ? $sub : $r;
        } elseif($id == "cc84143175d6ae2051058ee0079bd6b8") {
        $r = ($y["Subscriptions"]["Blogger"]["A"] == 1) ? $sub : $r;
       }
      }
     } else {
      $r = $this->core->Element([
       "p", "Out of Stock", ["class" => "CenterText"]
      ]);
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
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Scrollable" => $this->core->Page("8b3e21c565a8220fb6eb0a4433fe0739")
   ];
   $username = base64_decode($data["UN"]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = (!empty($username)) ? $username : $you;
   if($this->core->ID != $username) {
    $accessCode = "Accepted";
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $id = md5($t["Login"]["Username"]);
    $points = 1000;
    $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
    $shop = $this->core->FixMissing($shop, ["Title"]);
    $creditExchange = $this->Element([
     "p", "Credit Exchange requires a minimum of 1,000 points to be converted.",
     ["class" => "CenterText"]
    ]);
    if($points <= $y["Points"]) {
     $creditExchange = $this->core->Change([[
      "[CreditExchange.ID]" => md5(uniqid().rand(0, 9999)),
      "[CreditExchange.Points]" => $points,
      "[CreditExchange.Processor]" => base64_encode("v=".base64_encode("Shop:SaveCreditExchange")."&ID=$id&P="),
      "[CreditExchange.YourPoints]" => $y["Points"]
     ], $this->core->Page("b9c61e4806cf07c0068f1721678bef1e")]);
    }
    $discountCodes = $y["Shopping"]["Cart"][$id]["DiscountCode"] ?? 0;
    $discountCodes = ($discountCodes == 0) ? $this->core->Change([
     [
      "[DiscountCodes.ID]" => $id,
      "[DiscountCodes.Points]" => $points,
      "[DiscountCodes.Processor]" => base64_encode("v=".base64_encode("Shop:SaveDiscountCodes")."&DC=[DC]&ID=[ID]"),
      "[DiscountCodes.Shop.Title]" => $shop["Title"]
     ], $this->core->Page("0511fae6fcc6f9c583dfe7669b0217cc")
    ]) : $this->core->Element([
     "p", "<em>".base64_decode($discountCodes["Code"])."</em> was applied to your order!",
     ["class" => "CenterText"]
    ]);
    $r = $this->core->Change([[
     "[Cart.CreditExchange]" => $creditExchange,
     "[Cart.DiscountCodes]" => $discountCodes,
     "[Cart.List]" => base64_encode("v=".base64_encode("Search:Containers")."&Username=".$t["Login"]["Username"]."&st=CART"),
     "[Cart.Shop.ID]" => $id,
     "[Cart.Shop.Title]" => $shop["Title"],
     "[Cart.Summary]" => base64_encode("v=".base64_encode("Cart:Summary")."&UN=".$data["UN"])
    ], $this->core->Page("ac678179fb0fb0c66cd45d738991abb9")]);
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
  function Remove(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "ProductID",
    "ShopID"
   ]);
   $r = [
    "Body" => "The Product or Shop Identifiers are missing."
   ];
   if(!empty($data["ProductID"]) && !empty($data["ShopID"])) {
    $accessCode = "Accepted";
    $r = $this->core->Change([[
     "[RemoveFromCart.ProductID]" => $data["ProductID"],
     "[RemoveFromCart.ShopID]" => $data["ShopID"],
     "[RemoveFromCart.Remove]" => base64_encode("Cart:SaveRemove")
    ], $this->core->Page("554566eff3c7949301784c2be0a6be07")]);
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
  function SaveAdd(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["Product"] ?? "";
   $r = [
    "Body" => "The Member or Product Identifier is missing."
   ];
   $username = $data["Username"] ?? "";
   $shopID = $data["Shop"] ?? md5($username);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($username)) {
    $accessCode = "Accepted";
    $instructions = $data["Instructions"] ?? "";
    $username = $username ?? $you;
    $t = ($username == $you) ? $y : $this->core->Member($t);
    $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
    $title = $shop["Title"] ?? "Made in New York";
    $product = $this->core->Data("Get", ["product", $id]) ?? [];
    $productTitle = $product["Title"];
    $quantity = $data["Quantity"] ?? 1;
    $view = "v=".base64_encode("Cart:Home")."&UN=".base64_encode($t["Login"]["Username"]);
    $cart = $y["Shopping"]["Cart"][$shopID] ?? [];
    $cart["UN"] = $t["Login"]["Username"];
    $cart["Credits"] = $cart["Credits"] ?? 0;
    $cart["DiscountCode"] = $cart["DiscountCode"] ?? 0;
    $cart["Products"] = $cart["Products"] ?? [];
    $cart["Products"][$id] = $cart["Products"][$id] ?? [];
    $cart["Products"][$id]["Instructions"] = $instructions;
    $cart["Products"][$id]["QTY"] = $cart["Products"][$id]["QTY"] ?? 0;
    $cart["Products"][$id]["QTY"] = $cart["Products"][$id]["QTY"] + $quantity;
    $y["Shopping"]["Cart"][$shopID] = $cart;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "<em>$productTitle</em> was added to your cart for <em>$title</em>!",
     "Header" => "Added to Cart",
     "Options" => [
      $this->core->Element(["button", "View My Cart", [
       "class" => "CloseAllCards CloseDialog OpenFirSTEPTool v2 v2w",
       "data-fst" => base64_encode($view)
      ]])
     ]
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function SaveRemove(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "ProductID",
    "ShopID"
   ]);
   $productID = $data["ProductID"];
   $shopID = $data["ShopID"];
   $r = [
    "Body" => "The Shop or Product Identifier are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($productID) && !empty($shopID)) {
    $accessCode = "Accepted";
    $newProducts = [];
    $productID = base64_decode($productID);
    $shopID = base64_decode($shopID);
    $products = $y["Shopping"]["Cart"][$shopID]["Products"] ?? [];
    foreach($products as $key => $value) {
     if($key != $productID) {
      $newProducts[$key] = $value;
     }
    }
    $y["Shopping"]["Cart"][$shopID]["Products"] = $newProducts;
    $r = [
     "Body" => "The Product was removed from your cart.",
     "Header" => "Done"
    ];
    $this->core->Data("Save", ["mbr", md5($you), $y]);
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
  function Summary(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["UN"]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = $data["UN"];
   $username = (!empty($username)) ? base64_decode($username) : $you;
   $cart = $y["Shopping"]["Cart"][md5($username)]["Products"] ?? [];
   $cartCount = count($cart);
   $credits = $y["Shopping"]["Cart"][md5($username)]["Credits"] ?? 0;
   $credits = number_format($credits, 2);
   $discountCode = $y["Shopping"]["Cart"][md5($username)]["DiscountCode"] ?? 0;
   $now = $this->core->timestamp;
   $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
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
      $subtotal = $subtotal + ($price * $value["QTY"]);
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
   $mayContinue = ($cartCount > 0 && $subtotal > 0) ? 1 : 0;
   $continue = ($mayContinue == 1) ? $this->core->Element([
    "button", "Continue", [
     "class" => "BBB OpenFirSTEPTool v2 v2w",
     "data-fst" => base64_encode("v=".base64_encode("Shop:Pay")."&Shop=".md5($username)."&Type=Checkout")
    ]
   ]) : "";
   $r = $this->core->Change([[
    "[Cart.Continue]" => $continue,
    "[Cart.Summary.Discount]" => number_format($credits + $discountCode, 2),
    "[Cart.Summary.Subtotal]" => number_format($subtotal, 2),
    "[Cart.Summary.Tax]" => number_format($tax, 2),
    "[Cart.Summary.Total]" => number_format($tax + $total, 2)
   ], $this->core->Page("94eb319f456356da1d6e102670686a29")]);
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
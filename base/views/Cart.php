<?php
 Class Cart extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Add(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "T"]);
   $id = $data["ID"];
   $r = $this->system->Element([
    "p", "You must be signed in to make purchases.",
    ["class" => "CenterText"]
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["T"]) && $this->system->ID != $you) {
    $sub = $y["Subscriptions"]["Artist"]["A"] ?? 0;
    $t = ($data["T"] == $you) ? $y : $this->system->Member($data["T"]);
    $shop = $this->system->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    if($sub == 0 && $id == "c7054e9c7955203b721d142dedc9e540") {
     $r = $this->system->Element([
      "p", "Pay your commisiion via the Subscriptions page, and you will automatically be subscribed.",
      ["class" => "CenterText"]
     ]);
    } else {
     $product = $this->system->Data("Get", ["miny", $id]) ?? [];
     $cat = $product["Category"] ?? "";
     $i = $product["Instructions"] ?? "";
     $id = $product["ID"] ?? "";
     $quantity = $product["Quantity"] ?? 0;
     $ck = (!empty($id)) ? 1 : 0;
     $ck2 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $ck3 = $shop["Open"] ?? 0;
     $ck4 = ($quantity != 0) ? 1 : 0;
     $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) ? 1 : 0;
     if($ck == 1 || ($t["Login"]["Username"] == $this->system->ShopID && $quantity != 0)) {
      $inst = ($cat == "PHYS" && $i == 1) ? $this->system->Element([
       "p", "Please add your shipping address.",
       ["class" => "CenterText"]
      ]) : "";
      $inst .= ($i == 1) ? $this->system->Element([
       "textarea", NULL, [
        "name" => "Instructions",
        "placeholder" => "Write your instructions here..."
       ]
      ]) : "";
      $ls = ($quantity > 0 && $quantity < 20) ? $this->system->Element([
       "p", "This is selling fast, act soon before it's sold out!",
       ["class" => "CenterText"]
      ]) : "";
      $price = $product["Cost"] + $product["Profit"];
      $r = $this->system->Change([[
       "[AddToCart.Data]" => base64_encode("v=".base64_encode("Cart:SaveAdd")),
       "[AddToCart.Shop.ID]" => md5($t["Login"]["Username"]),
       "[AddToCart.Member.Username]" => base64_encode($t["Login"]["Username"]),
       "[AddToCart.Product.ID]" => $id,
       "[AddToCart.Product.Instructions]" => $inst,
       "[AddToCart.Product.LowStock]" => $ls,
       "[AddToCart.Product.Price]" => number_format($price, 2),
       "[AddToCart.Product.Quantity]" => $this->system->Select("prodQTY", "req v2w", $quantity)
      ], $this->system->Page("624bcc664e9bff0002e01583e7706d83")]);
      if($cat == "PHYS" && $t["Login"]["Username"] == $y["Login"]["Username"]) {
       $r = $this->system->Element([
        "p", "Physical orders are disabled as you own this shop.",
        ["class" => "CenterText"]
       ]);
      } elseif($cat == "SUB") {
       $sub = $this->system->Element([
        "h4", "Already Subscribed", ["class" => "UpperCase CenterText"]
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
      $r = $this->system->Element([
       "p", "Out of Stock", ["class" => "CenterText"]
      ]);
     }
    }
   }
   return $r;
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $r = $this->system->Page("8b3e21c565a8220fb6eb0a4433fe0739");
   $username = base64_decode($data["UN"]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = (!empty($username)) ? $username : $you;
   if($this->system->ID != $username) {
    // MOVE NEXT TWO (2) LINES TO ORDER PROCESSING
    #$y["Shopping"]["Cart"][md5($username)]["DiscountCode"] = 0;
    #$this->system->Data("Save", ["mbr", md5($you), $y]);
    $t = ($username == $you) ? $y : $this->system->Member($username);
    $id = md5($t["Login"]["Username"]);
    $shop = $this->system->Data("Get", ["shop", $id]) ?? [];
    $shop = $this->system->FixMissing($shop, ["Title"]);
    $discountCodes = $y["Shopping"]["Cart"][$id]["DiscountCode"] ?? 0;
    $discountCodes = ($discountCodes == 0) ? $this->system->Change([
     [
      "[DiscountCodes.Save]" => base64_encode("v=".base64_encode("Shop:SaveDiscountCodes")."&DC=[DC]&ID=[ID]"),
      "[DiscountCodes.Shop.ID]" => $id,
      "[DiscountCodes.Shop.Title]" => $shop["Title"]
     ], $this->system->Page("0511fae6fcc6f9c583dfe7669b0217cc")
    ]) : $this->system->Element([
     "p", "<em>".base64_decode($discountCodes["Code"])."</em> was applied to your order!",
     ["class" => "CenterText"]
    ]);
    $r = $this->system->Change([[
     "[Cart.CreditExchange]" => $this->system->Select("CE", NULL, $id),
     "[Cart.DiscountCodes]" => $discountCodes,
     "[Cart.List]" => $this->view(base64_encode("Search:Containers"), [
      "Data" => [
       "CRID" => md5("Shop$id"),
       "UN" => $t["Login"]["Username"],
       "st" => "CART"
      ]
     ]),
     "[Cart.Shop.ID]" => $id,
     "[Cart.Shop.Title]" => $shop["Title"],
     "[Cart.Summary]" => "v=".base64_encode("Cart:Summary")."&UN=".$data["UN"]
    ], $this->system->Page("ac678179fb0fb0c66cd45d738991abb9")]);
   }
   return $r;
  }
  function Remove(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "ProductID",
    "ShopID"
   ]);
   $r = "&nbsp;";
   if(!empty($data["ProductID"]) && !empty($data["ShopID"])) {
    $r = $this->system->Change([[
     "[RemoveFromCart.ProductID]" => $data["ProductID"],
     "[RemoveFromCart.ShopID]" => $data["ShopID"],
     "[RemoveFromCart.Remove]" => base64_encode("Cart:SaveRemove")
    ], $this->system->Page("554566eff3c7949301784c2be0a6be07")]);
   }
   return $r;
  }
  function SaveAdd(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "Instructions",
    "Product",
    "Shop",
    "UN"
   ]);
   $id = $data["Product"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Member or Product Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["UN"]) && !empty($id)) {
    $accessCode = "Accepted";
    $un = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($un);
    $t = ($t == $you) ? $y : $this->system->Member($t);
    $shop = $this->system->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $shop = $data["Shop"] ?? md5($t["Login"]["Username"]);
    $title = $shop["Title"] ?? "Made in New York";
    $product = $this->system->Data("Get", ["miny", $id]) ?? [];
    $productTitle = $product["Title"];
    $quantity = $data["prodQTY"] ?? 1;
    $view = "v=".base64_encode("Cart:Home")."&UN=".base64_encode($t["Login"]["Username"]);
    $cart = $y["Shopping"]["Cart"][$shop] ?? [];
    $cart["UN"] = $t["Login"]["Username"];
    $cart["Credits"] = $cart["Credits"] ?? 0;
    $cart["DiscountCode"] = $cart["DiscountCode"] ?? 0;
    $cart["Products"] = $cart["Products"] ?? [];
    $cart["Products"][$id] = $cart["Products"][$id] ?? [];
    $cart["Products"][$id]["Instructions"] = $data["Instructions"];
    $cart["Products"][$id]["QTY"] = $cart["Products"][$id]["QTY"] ?? 0;
    $cart["Products"][$id]["QTY"] = $cart["Products"][$id]["QTY"] + $quantity;
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "<em>$productTitle</em> was added to your cart for <em>$title</em>!"
     ]),
     "Header" => "Added to Cart",
     "Option" => $this->system->Element(["button", "View My Cart", [
      "class" => "CloseAllCards dBC v2 v2w",
      "onclick" => "FST('N/A', '$view', '".md5("Cart")."');"
     ]])
    ]);
    $y["Shopping"]["Cart"][$shop] = $cart;
    $this->system->Data("Save", ["mbr", md5($you), $y]);
   }
   return $this->system->JSONResponse([
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
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "ProductID",
    "ShopID"
   ]);
   $productID = $data["ProductID"];
   $shopID = $data["ShopID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Shop or Product Identifier are missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($productID) && !empty($shopID)) {
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
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Product was removed from your cart."
     ]),
     "Header" => "Done"
    ]);
    $this->system->Data("Save", ["mbr", md5($you), $y]);
   }
   return $r;
  }
  function Summary(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["UN"]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = $data["UN"];
   $username = (!empty($username)) ? base64_decode($username) : $you;
   $cart = $y["Shopping"]["Cart"][md5($username)]["Products"] ?? [];
   $cartCount = count($cart);
   $continue = ($cartCount > 0) ? $this->system->Element([
    "button", "Continue", [
     "class" => "BB BBB v2 v2w",
     "onclick" => "FST('N/A', 'v=".base64_encode("Pay:Checkout")."&UN=".$data["UN"]."', '".md5("ShoppingCart$username-Checkout")."');"
    ]
   ]) : "";
   $credits = $y["Shopping"]["Cart"][md5($username)]["Credits"] ?? 0;
   $credits = number_format($credits, 2);
   $discountCode = $y["Shopping"]["Cart"][md5($username)]["DiscountCode"] ?? 0;
   $now = $this->system->timestamp;
   $shop = $this->system->Data("Get", ["shop", md5($username)]) ?? [];
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
   return $this->system->Change([[
    "[Cart.Continue]" => $continue,
    "[Cart.Summary.Discount]" => number_format($credits + $discountCode, 2),
    "[Cart.Summary.Subtotal]" => number_format($subtotal, 2),
    "[Cart.Summary.Tax]" => number_format($tax, 2),
    "[Cart.Summary.Total]" => number_format($tax + $total, 2)
   ], $this->system->Page("94eb319f456356da1d6e102670686a29")]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
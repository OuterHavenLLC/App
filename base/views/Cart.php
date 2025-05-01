<?php
 Class Cart extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Add(array $data): string {
   $_Commands = "";
   $_Dialog = $this->core->Element([
    "p", "You must be signed in to make purchases.", ["class" => "CenterText"]
   ]);
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["T"]) && $this->core->ID != $you) {
    $sub = $y["Subscriptions"]["Artist"]["A"] ?? 0;
    $t = ($data["T"] == $you) ? $y : $this->core->Member($data["T"]);
    $shop = $this->core->Data("Get", ["shop", md5($t["Login"]["Username"])]);
    if($sub == 0 && $id == "c7054e9c7955203b721d142dedc9e540") {
     $_Dialog = [
      "Body" => "Pay your commisiion via the Subscriptions page, and you will automatically be subscribed.",
      "Header" => "Commission Due"
     ];
    } else {
     $_Dialog = "";
     $product = $this->core->Data("Get", ["product", $id]);
     $category = $product["Category"] ?? "";
     $cost = $product["Cost"] ?? 0;
     $hasInstructions = $product["Instructions"] ?? "";
     $productQuantity = $product["Quantity"] ?? 0;
     $id = $product["ID"] ?? "";
     $profit = $product["Profit"] ?? 0;
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
       "textarea", NULL, [
        "name" => "Instructions",
        "placeholder" => "Write your instructions here..."
       ]
      ]) : "";
      $lowStock = ($quantity > 0 && $quantity < 20) ? $this->core->Element([
       "p", "This is selling fast, act soon before it's sold out!",
       ["class" => "CenterText"]
      ]) : "";
      $price = str_replace(",", "", $cost) + str_replace(",", "", $profit);
      $quantity = ($category == "Product" && $quantity > 0) ? [
       "Attributes" => [
        "data-price" => base64_encode($price)
       ],
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
      $_Commands = [
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         "ATC$id",
         [
          [
           "Attributes" => [
            "name" => "Product",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $id
          ],
          [
           "Attributes" => [
            "name" => "Shop",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => md5($t["Login"]["Username"])
          ],
          [
           "Attributes" => [
            "name" => "Username",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $t["Login"]["Username"]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         "ATCQuantity$id",
         $quantity
        ]
       ]
      ];
      $_View = [
       "ChangeData" => [
        "[AddToCart.Data]" => base64_encode("v=".base64_encode("Cart:SaveAdd")),
        "[AddToCart.Product.ID]" => $id,
        "[AddToCart.Product.Instructions]" => $instructions,
        "[AddToCart.Product.LowStock]" => $lowStock,
        "[AddToCart.Product.Price]" => number_format($price, 2)
       ],
       "ExtensionID" => "624bcc664e9bff0002e01583e7706d83"
      ];
      if(($category == "Product") && $t["Login"]["Username"] == $you) {
       $_View = [
        "ChangeData" => [],
        "Extension" => $this->core->AESencrypt($this->core->Element([
         "p", "Deliverable Products are disabled as you own this shop.",
         ["class" => "CenterText"]
        ]))
       ];
      } elseif($category == "Subscription") {
       $_Subscribed = [
        "ChangeData" => [],
        "Extension" => $this->core->AESencrypt($this->core->Element([
         "h4", "Already Subscribed",
         ["class" => "UpperCase CenterText"]
        ]))
       ];
       if($id == "e4302295d2812e4f374ef1035891c4d1") {
        $_View = ($y["Subscriptions"]["Developer"]["A"] == 1) ? $_Subscribed : $_View;
       } elseif($id == "c7054e9c7955203b721d142dedc9e540") {
        $_View = ($y["Subscriptions"]["Artist"]["A"] == 1) ? $_Subscribed : $_View;
       } elseif($id == "cc84143175d6ae2051058ee0079bd6b8") {
        $_View = ($y["Subscriptions"]["Blogger"]["A"] == 1) ? $_Subscribed : $_View;
       } elseif($id == "355fd2f096bdb49883590b8eeef72b9c") {
        $_View = ($y["Subscriptions"]["VIP"]["A"] == 1) ? $_Subscribed : $_View;
       } elseif($id == "5bfb3f44cdb9d3f2cd969a23f0e37093") {
        $_View = ($y["Subscriptions"]["XFS"]["A"] == 1) ? $_Subscribed : $_View;
       }
      }
     } else {
      $_View = [
       "ChangeData" => [],
       "Extension" => $this->core->AESencrypt($this->core->Element([
        "p", "Out of Stock", ["class" => "CenterText"]
       ]))
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Home(array $data): string {
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $username = $data["UN"] $data["UN"] ?? base64_encode($you);
   $username = base64_decode($username);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } else {
    $_Dialog = "";
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $id = md5($t["Login"]["Username"]);
    $points = 100000;
    $shop = $this->core->Data("Get", ["shop", $id]);
    $shop = $this->core->FixMissing($shop, ["Title"]);
    $creditExchange = $this->Element([
     "p", "Credit Exchange requires a minimum of 1,000 points to be converted.",
     ["class" => "CenterText"]
    ]);
    if($points <= $y["Points"]) {
     $creditExchange = str_replace("[CreditExchange.ID]", $id, $this->core->Extension("b9c61e4806cf07c0068f1721678bef1e"));
    }
    $discountCode = $y["Shopping"]["Cart"][$id]["DiscountCode"] ?? "";
    $discountCode = (empty($discountCode) && $discountCode != 0) ? $this->core->Change([
     [
      "[DiscountCodes.ID]" => $id,
      "[DiscountCodes.Shop.Title]" => $shop["Title"]
     ], $this->core->Extension("0511fae6fcc6f9c583dfe7669b0217cc")
    ]) : $this->core->Element([
     "p", "<em>".base64_decode($discountCode)."</em> was applied to your order!",
     ["class" => "CenterText"]
    ]);
    $_Commands = [
     [
      "Name" => "GetCreditExchange",
      "Parameters" => [
       $id
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".CreditExchangeQuantity$id",
       [
        [
         "Attributes" => [
          "class" => "RangeInput$id UpdateRangeValue",
          "data-u" => base64_encode("v=".base64_encode("Shop:SaveCreditExchange")."&ID=$id&P="),
          "max" => $y["Points"],
          "min" => $points,
          "name" => "CE",
          "type" => "range"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME"
         ],
         "Type" => "Text",
         "Value" => $points
        ]
       ]
      ]
     ],
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".DiscountCode$id",
       [
        [
         "Attributes" => [
          "class" => "DiscountCodes",
          "data-id" => $id,
          "data-u" => base64_encode("v=".base64_encode("Shop:SaveDiscountCodes")."&DC=[DC]&ID=[ID]"),
          "type" => "text"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => base64_encode($discountCode)
        ]
       ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".Cart$id",
       $this->core->AESencrypt("v=".base64_encode("Search:Containers")."&Username=".$t["Login"]["Username"]."&st=CART")
      ]
     ],
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".CartSummary$id",
       $this->core->AESencrypt("v=".base64_encode("Cart:Summary")."&UN=".$data["UN"]),
       6000
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[Cart.CreditExchange]" => $creditExchange,
      "[Cart.DiscountCodes]" => $discountCode,
      "[Cart.Shop.ID]" => $id,
      "[Cart.Shop.Title]" => $shop["Title"]
     ],
     "ExtensionID" => "ac678179fb0fb0c66cd45d738991abb9"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function SaveAdd(array $data): string {
   $_AccessCode = "Denied";
   $_DIalog = [
    "Body" => "The Member or Product Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["Product"] ?? "";
   $username = $data["Username"] ?? "";
   $shopID = $data["Shop"] ?? md5($username);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($username)) {
    $_AccessCode = "Accepted";
    $instructions = $data["Instructions"] ?? "";
    $username = $username ?? $you;
    $t = ($username == $you) ? $y : $this->core->Member($t);
    $shop = $this->core->Data("Get", ["shop", $shopID]);
    $title = $shop["Title"] ?? "Made in New York";
    $product = $this->core->Data("Get", ["product", $id]);
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
    $_Dialog = [
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
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function SaveRemove(array $data): string {
   $_Dialog = [
    "Body" => "The Shop or Product Identifier are missing."
   ];
   $data = $data["Data"] ?? [];
   $product = $data["Product"] ?? "";
   $shop = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($product) && !empty($shop)) {
    $newProducts = [];
    $cart = $y["Shopping"]["Cart"][$shop] ?? [];
    $products = $cart["Products"] ?? [];
    foreach($products as $key => $value) {
     if($key != $product) {
      $newProducts[$key] = $value;
     }
    }
    $cart["Products"] = $newProducts;
    $y["Shopping"]["Cart"][$shop] = $cart;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $_Dialog = [
     "Body" => "The Product was removed from your cart.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function Summary(array $data): string {
   $data = $data["Data"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = $data["UN"] ?? base64_encode($you);;
   $username = base64_decode($username);
   $shopID = md5($username);
   $cart = $y["Shopping"]["Cart"][$shopID]["Products"] ?? [];
   $cartCount = count($cart);
   $credits = $y["Shopping"]["Cart"][$shopID]["Credits"] ?? 0;
   $credits = number_format($credits, 2);
   $discountCode = $y["Shopping"]["Cart"][$shopID]["DiscountCode"] ?? 0;
   $now = $this->core->timestamp;
   $shop = $this->core->Data("Get", ["shop", $shopID]);
   $subtotal = 0;
   $total = 0;
   foreach($cart as $key => $value) {
    $product = $this->core->Data("Get", ["product", $key]);
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
     "class" => "BBB GoToView v2 v2w",
     "data-type" => "Checkout;".base64_encode("v=".base64_encode("Shop:Pay")."&Shop=$shopID&Type=Checkout&ViewPairID=".base64_encode("Checkout"))
    ]
   ]) : "";
   return $this->core->JSONResponse([
    "View" => [
     "ChangeData" => [
      "[Cart.Continue]" => $continue,
      "[Cart.Summary.Discount]" => number_format($credits + $discountCode, 2),
      "[Cart.Summary.Subtotal]" => number_format($subtotal, 2),
      "[Cart.Summary.Tax]" => number_format($tax, 2),
      "[Cart.Summary.Total]" => number_format($tax + $total, 2)
     ],
     "ExtensionID" => "94eb319f456356da1d6e102670686a29"
    ]
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
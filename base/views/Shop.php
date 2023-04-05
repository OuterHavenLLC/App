<?php
 Class Shop extends GW {
  function __construct() {
   parent::__construct();
   $this->root = $_SERVER["DOCUMENT_ROOT"]."/base/pay/Braintree.php";
   $this->you = $this->system->Member($this->system->Username());
  }
  function Banish(array $a) {
   $data = $a["Data"] ?? [];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Username is missing."]),
    "Header" => "Error"
   ]);
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You must sign in to continue."]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($username)) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You cannot fire yourself."]),
     "Header" => "Error"
    ]);
    $username = base64_decode($username);
    if($username != $you) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "You are about to fire $username. Are you sure?"
      ]),
       "Header" => "Fire $username?",
       "Option" => $this->system->Element([
       "button", "Cancel", ["class" => "dBC v2 v2w"]
      ]),
      "Option2" => $this->system->Element([
       "button", "Fire $username", [
        "class" => "BBB dBC dBO v2 v2w",
        "data-type" => "v=".base64_encode("Shop:SaveBanish")."&UN=".$data["UN"]
       ]
      ])
     ]);
    }
   }
   return $r;
  }
  function CompleteOrder(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $ec = "Denied";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Order Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($data["ID"])) {
    $ec = "Accepted";
    $id = base64_decode($data["ID"]);
    $po = $this->system->Data("Get", [
     "po",
     md5($y["Login"]["Username"])
    ]) ?? [];
    $po[$id]["Complete"] = 1;
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The order has been marked as complete!"
     ]),
     "Header" => "Done!"
    ]);
    $this->system->Data("Save", ["po", md5($y["Login"]["Username"]), $po]);
   }
  return $this->system->JSONResponse([$ec, $r]);
  }
  function Edit(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $back = "";
   $button = "";
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Shop Identifier is missing."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $id = base64_decode($id);
    $shop = $this->system->Data("Get", ["shop", $id]) ?? [];
    $atinput = ".Shop$id-CoverPhoto";
    $at = base64_encode("Set as the Shop's Cover Photo:$atinput");
    $at2 = base64_encode("All done! Feel free to close this card.");
    $atinput = "$atinput .rATT";
    $search = base64_encode("Search:Containers");
    $back = $this->system->Change([
     [
      "[CP.ContentType]" => "Shop",
      "[CP.Files]" => base64_encode("v=$search&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
      "[CP.ID]" => $id
     ], $this->system->Page("dc027b0a1f21d65d64d539e764f4340a")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $button = $this->system->Element(["button", "Update", [
     "class" => "CardButton SendData",
     "data-form" => ".Shop$id",
     "data-processor" => base64_encode("v=".base64_encode("Shop:Save"))
    ]]);
    $coverPhoto = $shop["CoverPhotoSource"] ?? "";
    $shop = $this->system->FixMissing($shop, [
     "Description",
     "Live",
     "Open",
     "Title",
     "Welcome"
    ]);
    $nsfw = $shop["NSFW"] ?? $y["Privacy"]["NSFW"];
    $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
    $privacy = $shop["Privacy"] ?? $y["Privacy"]["Shop"];
    $processing = $shop["Processing"] ?? [];
    $processing = $this->system->FixMissing($processing, [
     "BraintreeMerchantIDLive",
     "BraintreePrivateKeyLive",
     "BraintreePublicKeyLive",
     "BraintreeTokenLive",
     "PayPalClientID",
     "PayPalClientIDLive",
     "PayPalEmailLive"
    ]);
    $tax = $shop["Tax"] ?? 10.00;
    $r = $this->system->Change([[
     "[Shop.CoverPhoto]" => $coverPhoto,
     "[Shop.CoverPhoto.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
     "[Shop.Description]" => $shop["Description"],
     "[Shop.ID]" => $id,
     "[Shop.Live]" => $this->system->Select("Live", "LI v2w", $shop["Live"]),
     "[Shop.NSFW]" => $this->system->Select("nsfw", "LI v2w", $nsfw),
     "[Shop.Open]" => $this->system->Select("Open", "LI v2w", $shop["Open"]),
     "[Shop.PaymentProcessor]" => $this->system->Select("PaymentProcessor", "LI v2w", $paymentProcessor),
     "[Shop.Pay.MerchantID]" => base64_decode($processing["BraintreeMerchantID"]),
     "[Shop.Pay.MerchantID.Live]" => base64_decode($processing["BraintreeMerchantIDLive"]),
     "[Shop.Pay.PayPalClientID]" => base64_decode($processing["PayPalClientID"]),
     "[Shop.Pay.PayPalClientID.Live]" => base64_decode($processing["PayPalClientIDLive"]),
     "[Shop.Pay.PayPalEmail]" => base64_decode($processing["PayPalEmail"]),
     "[Shop.Pay.PayPalEmail.Live]" => base64_decode($processing["PayPalEmailLive"]),
     "[Shop.Pay.PrivateKey]" => base64_decode($processing["BraintreePrivateKey"]),
     "[Shop.Pay.PrivateKey.Live]" => base64_decode($processing["BraintreePrivateKeyLive"]),
     "[Shop.Pay.PublicKey]" => base64_decode($processing["BraintreePublicKey"]),
     "[Shop.Pay.PublicKey.Live]" => base64_decode($processing["BraintreePublicKeyLive"]),
     "[Shop.Pay.Token]" => base64_decode($processing["BraintreeToken"]),
     "[Shop.Pay.Token.Live]" => base64_decode($processing["BraintreeTokenLive"]),
     "[Shop.Privacy]" => $this->system->Select("Privacy", "LI v2w", $privacy),
     "[Shop.Tax]" => $this->system->Select("Percentile", "req v2w", $tax),
     "[Shop.Title]" => $shop["Title"],
     "[Shop.Welcome]" => $this->system->WYSIWYG([
      "Body" => $this->system->PlainText([
       "Data" => base64_encode($shop["Welcome"]),
       "Decode" => 1
      ]),
      "adm" => 1,
      "opt" => [
       "id" => "WelcomeMessage",
       "class" => "UIE$id".md5($this->system->timestamp)." Welcome Xdecode req",
       "name" => "Welcome",
       "placeholder" => "Welcome Message",
       "rows" => 20
      ]
     ])
    ], $this->system->Page("201c1fca2d1214dddcbabdc438747c9f")]);
   }
   return $this->system->Card([
    "Back" => $back,
    "Front" => $r,
    "FrontButton" => $button,
   ]);
  }
  function EditPartner(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["UN", "new"]);
   $fr = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Partner Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $frbtn = "";
   $new = $data["new"] ?? 0;
   $username = (!empty($data["UN"])) ? base64_decode($data["UN"]) : "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $fr = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($username) || $new == 1) {
    if($new == 1) {
     $action = "Hire";
     $company = "Company";
     $description = "Description";
     $header = "New Partner";
     $inputType = "text";
     $title = "Title";
    } else {
     $action = "Update";
     $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
     $partner = $shop["Contributors"][$username] ?? [];
     $company = $partner["Company"];
     $description = $partner["Description"];
     $header = "Edit $username";
     $inputType = "hidden";
     $title = $partner["Title"];
    }
    $fr = $this->system->Change([[
     "[Partner.Company]" => $company,
     "[Partner.Description]" => $description,
     "[Partner.Header]" => $header,
     "[Partner.ID]" => md5($username),
     "[Partner.New]" => $new,
     "[Partner.Title]" => $title,
     "[Partner.Username]" => $username,
     "[Partner.Username.InputType]" => $inputType
    ], $this->system->Page("a361fab3e32893af6c81a15a81372bb7")]);
    $frbtn = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".Partner".md5($username),
     "data-processor" => base64_encode("v=".base64_encode("Shop:SavePartner"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $fr,
    "FrontButton" => $frbtn
   ]);
  }
  function History(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $i = 0;
   $y = $this->you;
   $si = base64_encode("Common:SignIn");
   $su = base64_encode("Common:SignUp");
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Change([[
     "[ShoppingHistory.SignIn]" => base64_encode("v=$si"),
     "[ShoppingHistory.SignUp]" => base64_encode("v=$su")
    ], $this->system->Page("530578e8f5a619e234704ea1f6cd3d64")]);
   } else {
    $r = $this->system->Change([[
     "[Error.Header]" => "Error",
     "[Error.Message]" => "The Shop Identifier is missing."
    ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
    if(!empty($data["ID"])) {
     $h = $y["Shopping"]["History"] ?? [];
     $h = $h[$data["ID"]] ?? [];
     $h2 = [];
     $r = "";
     foreach(array_reverse($h) as $k => $v) {
      $opt = "";
      $product = $this->system->Data("Get", ["miny", $v["ID"]]) ?? [];
      $exp = $product["Expires"] ?? [
       "Created" => $product["Created"],
       "Quantity" => 1,
       "TimeSpan" => "year"
      ];
      $ck = ($this->system->timestamp < $this->system->TimePlus($product["Created"], $exp["Quantity"], $exp["TimeSpan"])) ? 1 : 0;
      if(!empty($p) && $ck == 1) {
       $cat = $product["Category"];
       $h2[$k] = $v;
       $i++;
       $coverPhoto = $product["ICO"] ?? $this->system->PlainText([
        "Data" => "[sIMG:MiNY]",
        "Display" => 1
       ]);
       $id = $product["ID"];
       $pts = $this->system->core["PTS"]["Products"];
       $qty = $product["Quantity"] ?? 0;
       $qty2 = $product["QTY"] ?? 0;
       if($cat == "ARCH") {
        # Architecture
       } elseif($cat == "DLC") {
        # Downloadable Content
       } elseif($cat == "DONATE") {
        # Donations
        $opt = $this->system->Element(["p", "Thank you for donating!"]);
       } elseif($cat == "PHYS") {
        # Physical Products (require delivery info)
        $opt = $this->system->Element([
         "button", "Contact the Seller", ["class" => "BB BBB v2 v2w"]
        ]);
       } elseif($cat == "SUB") {
        $opt = $this->system->Element(["button", "Go to Subscription", [
         "class" => "BB BBB v2 v2w"
        ]]);
       }
       $r .= $this->system->Change([[
        "[Product.Added]" => $this->system->TimeAgo($v["Timestamp"]),
        "[Product.ICO]" => $this->system->CoverPhoto(base64_encode($coverPhoto)),
        "[Product.Description]" => $this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $product["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Product.Options]" => $opt,
        "[Product.Quantity]" => $qty2,
        "[Product.Title]" => $product["Title"]
       ], $this->system->Page("4c304af9fcf2153e354e147e4744eab6")]);
      }
     } if($i == 0) {
      $r = $this->system->Element(["h3", "No Results", [
       "class" => "CenterText UpperCase",
       "style" => "margin:1em"
      ]]);
     }
     $y["Shopping"]["History"][$data["ID"]] = $h2;
     $this->system->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
     $r = $this->system->Change([[
      "[ShoppingHistory.List]" => $r
     ], $this->system->Page("20664fb1019341a3ea2e539360108ac3")]);
    }
   }
   return $r;
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "CARD",
    "UN",
    "b2",
    "back",
    "lPG",
    "pub"
   ]);
   $bck = ($data["back"] == 1) ? $this->system->Element([
    "button", "Back", [
     "class" => "GoToParent LI head",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $i = 0;
   $pub = $data["pub"] ?? 0;
   $r = $this->MadeInNewYork(["back" => $bck]);
   $username = $data["UN"] ?? base64_encode("");
   $username = base64_decode($username);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
    $shops = $this->system->DatabaseSet("SHOP") ?? [];
    foreach($shops as $key => $value) {
     $shop = str_replace("c.oh.shop.", "", $value);
     $shop = $this->system->Data("Get", ["shop", $shop]) ?? [];
     $t = $this->system->Data("Get", ["mbr", $shop]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->system->CallSign($shop["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
     }
    }
   } if(!empty($username) || $i > 0) {
    $t = ($username == $you) ? $y : $this->system->Member($username);
    $id = md5($t["Login"]["Username"]);
    $shop = $this->system->Data("Get", ["shop", $id]) ?? [];
    $commission = $shop["Commission"] ?? 0;
    $subscribers = $shop["Subscribers"] ?? [];
    $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
    $ck2 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
    $ck3 = ($ck2 == 0 && $commission > 0) ? 1 : 0;
    if($ck == 1 && $ck3 == 1) {
     $r = $this->system->Change([[
      "[Commission.AddToCart]" => $this->view(base64_encode("Pay:Commission"), ["Data" => [
       "ID" => $id,
       "T" => $t["Login"]["Username"]
      ]])
     ], $this->system->Page("f844c17ae6ce15c373c2bd2a691d0a9a")]);
    } elseif($ck == 1 || $ck2 == 1) {
     $bl = $this->system->CheckBlocked([$t, "Members", $you]);
     $cms = $this->system->Data("Get", ["cms", $id]) ?? [];
     $ck2 = $this->system->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $t["Privacy"]["Shop"],
      "UN" => $t["Login"]["Username"],
      "Y" => $you
     ]);
     $ck2 = ($t["Login"]["Username"] == $this->system->ShopID) ? 1 : $ck2;
     $ck3 = $this->system->CheckBraintreeKeys($shop["Processing"]);
     $contributors = $shop["Contributors"] ?? [];
     if($ck == 1 || ($bl == 0 && $ck2 == 1 && $ck3 > 0)) {
      $active = 0;
      foreach($contributors as $member => $role) {
       if($active == 0 && $member == $you) {
        $active++;
       }
      }
      $ck = ($active == 1 || $id == md5($you)) ? 1 : 0;
      $coverPhoto = $shop["CoverPhoto"] ?? $this->system->PlainText([
       "Data" => "[sIMG:MiNY]",
       "Display" => 1
      ]);
      $coverPhoto = base64_encode($coverPhoto);
      $disclaimer = "Products and Services sold on the <em>Made in New York</em> Shop Network by third parties do not represent the views of <em>Outer Haven</em>, unless sold under the signature Shop.";
      $edit = ($ck == 1) ? $this->system->Element([
       "button", "Edit", [
        "class" => "Small dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Shop:Edit")."&ID=".base64_encode($id))
       ]
      ]) : "";
      $payroll = ($id == md5($you)) ? $this->system->Element([
       "button", "Payroll", [
        "class" => "Small dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Shop:Payroll"))
       ]
      ]) : "";
      $reactions = ($id != md5($you)) ? $this->view(base64_encode("Common:Reactions"), ["Data" => [
       "CRID" => $id,
       "T" => $t["Login"]["Username"],
       "Type" => 4
      ]]) : "";
      $search = base64_encode("Search:Containers");
      $subscribe = (md5($you) != $id && $this->system->ID != $you) ? 1 : 0;
      $subscribeText = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
      $subscribe = ($subscribe == 1) ? $this->system->Change([[
       "[Subscribe.ContentID]" => $id,
       "[Subscribe.ID]" => md5($you),
       "[Subscribe.Processor]" => base64_encode("v=".base64_encode("Shop:Subscribe")),
       "[Subscribe.Text]" => $subscribeText,
       "[Subscribe.Title]" => $shop["Title"]
      ], $this->system->Page("489a64595f3ec2ec39d1c568cd8a8597")]) : "";
      $r = $this->system->Change([[
       "[Shop.Back]" => $bck,
       "[Shop.CoverPhoto]" => $this->system->CoverPhoto($coverPhoto),
       "[Shop.Cart]" => "v=".base64_encode("Cart:Home")."&UN=".$data["UN"]."&PFST=$pub",
       "[Shop.Conversation]" => $this->system->Change([[
        "[Conversation.CRID]" => $id,
        "[Conversation.CRIDE]" => base64_encode($id),
        "[Conversation.Level]" => base64_encode(1),
        "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
       ], $this->system->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
       "[Shop.Disclaimer]" => $disclaimer,
       "[Shop.Edit]" => $edit,
       "[Shop.History]" => "v=".base64_encode("Shop:History")."&ID=$id&PFST=$pub",
       "[Shop.ID]" => $id,
       "[Shop.Partners]" => $this->view($search, ["Data" => [
        "ID" => base64_encode($id),
        "Type" => base64_encode("Shop"),
        "st" => "Contributors"
       ]]),
       "[Shop.Payroll]" => $payroll,
       "[Shop.ProductList]" => $this->view($search, ["Data" => [
         "UN" => base64_encode($t["Login"]["Username"]),
         "b2" => $shop["Title"],
         "lPG" => "MiNY$id",
         "pubP" => $pub,
         "st" => "MiNY"
        ]]),
       "[Shop.Reactions]" => $reactions,
       "[Shop.Subscribe]" => $subscribe,
       "[Shop.Title]" => $shop["Title"],
       "[Shop.Welcome]" => $this->system->PlainText([
        "Data" => $shop["Welcome"],
        "HTMLDecode" => 1
       ])
      ], $this->system->Page("f009776d658c21277f8cfa611b843c24")]);
     }
    }
   }
   $r = ($data["CARD"] == 1) ? $this->system->Card(["Front" => $r]) : $r;
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function MadeInNewYork(array $a) {
   $data = $a["Data"] ?? [];
   $bck = $data["back"] ?? "";
   $callsign = $this->system->Data("Get", [
    "miny",
    "355fd2f096bdb49883590b8eeef72b9c"
   ]) ?? [];
   $callsign = $this->system->CallSign($callsign["Title"]);
   $lpg = "MadeInNY";
   $pub = $data["pub"] ?? 0;
   $sc = base64_encode("Search:Containers");
   $username = base64_encode($this->system->ShopID);
   $r = $this->system->Change([[
    "[MadeInNY.Artists]" => $this->view($sc, ["Data" => [
     "b2" => "Made in New York",
     "lPG" => $lpg,
     "st" => "SHOP"
    ]]),
    "[MadeInNY.Back]" => $bck,
    "[MadeInNY.Products]" => $this->view($sc, ["Data" => [
     "b2" => "Made in New York",
     "lPG" => $lpg,
     "st" => "SHOP-Products"
    ]]),
    "[MadeInNY.VIP]" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&CS=$callsign&UN=$username&pub=$pub")
   ], $this->system->Page("62ee437edb4ce6d30afa8b3ea4ec2b6e")]);
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function Payroll(array $a) {
   $_Day = $this->system->Page("ca72b0ed3686a52f7db1ae3b2f2a7c84");
   $_Month = $this->system->Page("2044776cf5f8b7307b3c4f4771589111");
   $_Partner = $this->system->Page("210642ff063d1b3cbe0b2468aba070f2");
   $_Sale = $this->system->Page("a2adc6269f67244fc703a6f3269c9dfe");
   $_Year = $this->system->Page("676193c49001e041751a458c0392191f");
   $data = $a["Data"] ?? [];
   $r = $this->system->Change([[
    "[Error.Header]" => "No Data",
    "[Error.Message]" => "You have not earned any income yet..."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   $yearTable = "";
   $you = $y["Login"]["Username"];
   $payroll = $this->system->Data("Get", ["id", md5($you)]) ?? [];
   foreach($payroll as $year => $yearData) {
    if(is_array($yearData)) {
     $monthTable = "";
     if($year != "UN") {
      foreach($yearData as $month => $monthData) {
       $dayTable = "";
       $partnerTable = "";
       $partners = $monthData["Partners"] ?? [];
       $partnersCount = count($partners);
       $sales = $monthData["Sales"] ?? [];
       $subtotal = 0;
       $tax = 0;
       $total = 0;
       foreach($sales as $day => $salesGroup) {
        $saleTable = "";
        foreach($salesGroup as $daySales => $daySale) {
         foreach($daySale as $id => $product) {
          $price = str_replace(",", "", $product["Cost"]);
          $price = $price + str_replace(",", "", $product["Profit"]);
          $price = $price * $product["Quantity"];
          $subtotal = $subtotal + $price;
          $saleTable .= $this->system->Change([[
           "[IncomeDisclosure.Sale.Price]" => number_format($price, 2),
           "[IncomeDisclosure.Sale.Title]" => $product["Title"]
          ], $_Sale]);
         }
        }
        $dayTable .= $this->system->Change([[
         "[IncomeDisclosure.Day]" => $day,
         "[IncomeDisclosure.Day.Sales]" => $saleTable
        ], $_Day]);
       }
       $subtotal = str_replace(",", "", $subtotal);
       $commission = number_format($subtotal * (5.00 / 100), 2);
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format($subtotal - $commission - $tax, 2);
       $revenueOverheadCosts = $total * (5.00 / 100);
       $revenueSplit = ($revenue - $revenueOverheadCosts) / $partnersCount;
       foreach($partners as $partner => $info) {
        $paid = $vinfo["Paid"] ?? 0;
        $pck = ($paid == 0 && $partner != $you) ? 1 : 0;
        $pck = ($pck == 1 && $month != date("m")) ? 1 : 0;
        $pay = ($pck == 1) ? $this->system->Element([
         "button", "$".number_format($revenueSplit, 2), [
          "class" => "BB BBB v2",
          "data-lm" => base64_encode($month),
          "onclick" => "FST('N/A', 'v=".base64_encode("Pay:Disbursement")."&Month=$month&UN=".base64_encode($username)."&Year=$year', '".md5("Pay".md5($username))."');"
         ]
        ]) : $this->system->Element(["p", "No Action Needed"]);
        $partner .= $this->system->Change([[
         "[Partner.Description]" => $data["Description"],
         "[Partner.DisplayName]" => $username,
         "[Partner.Pay]" => $pay
        ], $_Partner]);
       }
       $monthTable .= $this->system->Change([[
        "[IncomeDisclosure.Table.Month]" => $this->ConvertCalendarMonths($month),
        "[IncomeDisclosure.Table.Month.Commission]" => $commission,
        "[IncomeDisclosure.Table.Month.Partners]" => $partnerTable,
        "[IncomeDisclosure.Table.Month.Sales]" => $dayTable,
        "[IncomeDisclosure.Table.Month.Subtotal]" => number_format($subtotal, 2),
        "[IncomeDisclosure.Table.Month.Tax]" => $tax,
        "[IncomeDisclosure.Table.Month.Total]" => $total
       ], $_Month]);
      }
      $yearTable .= $this->system->Change([[
       "[IncomeDisclosure.Table.Year]" => $year,
       "[IncomeDisclosure.Table.Year.Lists]" => $monthTable
      ], $_Year]);
     }
    }
    $yearTable = (empty($id)) ? $this->system->Element([
     "h3", "No earnings to report...", [
      "class" => "CenterText",
      "style" => "margin:0.5em"
     ]
    ]) : $yearTable;
    $r = $this->system->Change([[
     "[IncomeDisclosure.DisplayName]" => $t["Personal"]["DisplayName"],
     "[IncomeDisclosure.Gallery.Title]" => $shop["Title"],
     "[IncomeDisclosure.Table]" => $yearTable
    ], $this->system->Page("4ab1c6f35d284a6eae66ebd46bb88d5d")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "Unknown error."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $shops = $this->system->DatabaseSet("MBR");
    $title = $data["Title"] ?? "";
    $i = 0;
    foreach($shops as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $shop = $this->system->Data("Get", ["shop", $value]) ?? [];
     $ttl = $shop["Title"] ?? "";
     if($id != $value && $title == $ttl) {
      $i++;
     }
    } if($i > 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Shop <em>$title</em> is taken."
      ]),
      "Header" => "Error"
     ]);
    } else {
     $accessCode = "Accepted";
     $shop = $this->system->Data("Get", ["shop", $id]) ?? [];
     $coverPhoto = "";
     $coverPhotoSource = "";
     foreach($data as $key => $value) {
      if(strpos($key, "Processing_") !== false) {
       $key = explode("_", $key);
       $shop["Processing"][$key[1]] = base64_encode($value);
      }
     } if(!empty($data["CoverPhoto"])) {
      $dlc = array_filter(explode(";", base64_decode($data["CoverPhoto"])));
      $dlc = array_reverse($dlc);
      foreach($dlc as $dlc) {
       if(!empty($dlc) && $i == 0) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         $t = $this->system->Member($f[0]);
         $efs = $this->system->Data("Get", [
          "fs",
          md5($t["Login"]["Username"])
         ]) ?? [];
         $coverPhoto = $f[0]."/".$efs["Files"][$f[1]]["Name"];
         $coverPhotoSource = base64_encode($f[0]."-".$f[1]);
         $i++;
        }
       }
      }
     }
    }
    $contributors = $shop["Contributors"] ?? [];
    $description = $data["Description"] ?? $shop["Description"];
    $live = $data["Live"] ?? 0;
    $nsfw = $data["nsfw"] ?? 0;
    $open = $data["Open"] ?? 0;
    $paymentProcessor = $data["PaymentProcessor"] ?? "PayPal";
    $privacy = $data["pri"] ?? $y["Privacy"]["Shop"];
    $products = $shop["Products"] ?? [];
    $tax = $data["Percentile"] ?? 10.00;
    $title = $title ?? $shop["Title"];
    $welcome = $data["Welcome"] ?? "";
    $shop = [
     "Contributors" => $contributors,
     "CoverPhoto" => $coverPhoto,
     "CoverPhotoSource" => base64_encode($coverPhotoSource),
     "Description" => $description,
     "Live" => $live,
     "Modified" => $this->system->timestamp,
     "NSFW" => $nsfw,
     "Open" => $open,
     "PaymentProcessor" => $paymentProcessor,
     "Privacy" => $privacy,
     "Processing" => $shop["Processing"],
     "Products" => $products,
     "Tax" => $tax,
     "Title" => $title,
     "Welcome" => $this->system->PlainText([
      "Data" => $welcome,
      "HTMLEncode" => 1
     ])
    ];
    $this->system->Data("Save", ["shop", $id, $shop]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "$title has been updated."]),
     "Header" => "Done"
    ]);
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
  function SaveBanish(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["UN"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Username is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($data["UN"])) {
    $username = base64_decode($data["UN"]);
    if($username == $you) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "You cannot fire yourself."]),
      "Header" => "Error"
     ]);
    } else {
     $newContributors = [];
     $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
     $contributors = $shop["Contributors"] ?? [];
     foreach($contributors as $key => $value) {
      if($key != $username) {
       $newContributors[$key] = $value;
      }
     }
     $shop["Contributors"] = $newContributors;
     $this->system->Data("Save", ["shop", md5($you), $shop]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "You fired $username."]),
      "Header" => "Done"
     ]);
    }
   }
   return $r;
  }
  function SaveCreditExChange(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "P", "UN"]);
   $points = base64_decode($data["P"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "Unknown error."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(is_numeric($points)) {
    $points = ($points < $y["Points"]) ? $points : $y["Points"];
    $credits = $points * 0.00001;
    $creditsDecimal = number_format($credits, 2);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You requested more credits than you can afford."
     ]),
     "Header" => "Error"
    ]);
    if($points < $y["Points"]) {
     $accessCode = "Accepted";
     $yourCredits = $y["Shopping"]["Cart"][$data["ID"]]["Credits"] ?? 0;
     $y["Shopping"]["Cart"][$data["ID"]]["Credits"] = $creditsDecimal + $yourCredits;
     $y["Points"] = $y["Points"] - $points;
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "<em>$points</em> points were converted to $<em>$creditsDecimal</em> credits, and have <em>".$y["Points"]."</em> remaining."
      ]),
      "Header" => "Done"
     ]);
     $this->system->Data("Save", ["mbr", md5($you), $y]);
    }
   }
   return $this->system->JSONResponse([$accessCode, $r]);
  }
  function SaveDiscountCodes(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["DC", "ID"]);
   $i = 0;
   $r = "The Code Identifier is missing.";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = "You must be signed in to continue.";
   } elseif(!empty($data["DC"]) && !empty($data["ID"])) {
    $id = base64_decode($data["ID"]);
    $discount = $this->system->Data("Get", ["dc", $id]) ?? [];
    $code = base64_decode($data["DC"]);
    $encryptedCode = $data["DC"] ?? base64_encode("OuterHaven.DC.Invalid");
    $r = "<em>$code</em> is an Invalid code.";
    foreach($discount as $key => $value) {
     if($i == 0 && $encryptedCode == $value["Code"]) {
      $accessCode = "Accepted";
      $dollarAmount = $value["DollarAmount"] ?? 0;
      $percentile = $value["Percentile"] ?? 0;
      $quantity = $value["Quantity"] - 1;
      $quantity = ($quantity < 0) ? 0 : $quantity;
      $discount[$key]["Quantity"] = $quantity;
      $y["Shopping"]["Cart"][$id]["DiscountCode"] = [
       "Code" => $value["Code"],
       "DollarAmount" => $dollarAmount,
       "Percentile" => $percentile
      ];
      $r = "<em>$code</em> was applied to your order!";
      $i++;
     }
    }
    $this->system->Data("Save", ["dc", $id, $discount]);
    $this->system->Data("Save", ["mbr", md5($you), $y]);
   }
   return $this->system->JSONResponse([
    $accessCode,
    $this->system->Element(["p", $r, ["class" => "CenterText"]])
   ]);
  }
  function SavePartner(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "Company",
    "Description",
    "Title",
    "UN",
    "new"
   ]);
   $new = $data["new"] ?? 0;
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Username is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $username = $data["UN"];
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($username)) {
    $i = 0;
    $members = $this->system->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     if(md5($username) == $value) {
      $i++;
     }
    } if($i == 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Member <em>$username</em> does not exist."
      ]),
      "Header" => "Done"
     ]);
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "hired" : "updated";
     $now = $this->system->timestamp;
     $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
     $hired = $shop["Contributors"][$username]["Hired"] ?? $now;
     $contributors = $shop["Contributors"] ?? [];
     $contributors[$username] = [
      "Company" => $data["Company"],
      "Description" => $data["Description"],
      "Hired" => $hired,
      "Paid" => 0,
      "Title" => $data["Title"]
     ];
     $shop["Contributors"] = $contributors;
     if($new == 1) {
      $this->system->SendBulletin([
       "Data" => [
        "ShopID" => md5($you),
        "Member" => $username,
        "Role" => "Partner"
       ],
       "To" => $username,
       "Type" => "InviteToShop"
      ]);
     }
     $this->system->Data("Save", ["shop", md5($you), $shop]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "Your Partner $username was $actionTaken."
      ]),
      "Header" => "Done"
     ]);
    }
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
  function Share(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["UN"]);
   $ec = "Denied";
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $username = $data["UN"];
   $y = $this->you;
   if(!empty($username)) {
    $username = base64_decode($username);
    $t = ($username == $y["Login"]["Username"]) ? $y : $this->system->Member($username);
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out <em>".$shop["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!"
     ]).$this->system->Element([
      "div", "[Shop:$username]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$username&Type=Shop",
     "[Share.ContentID]" => "Shop",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $username,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $shop["Title"]
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function Subscribe(array $a) {
   $accessCode = "Denied";
   $responseType = "Dialog";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Shop Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to subscribe."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $responseType = "UpdateText";
    $shop = $this->system->Data("Get", ["shop", $id]) ?? [];
    $subscribers = $shop["Subscribers"] ?? [];
    $subscribed = (in_array($you, $subscribers)) ? 1 : 0;
    if($subscribed == 1) {
     $newSubscribers = [];
     $r = "Subscribe";
     foreach($subscribers as $key => $value) {
      if($value != $you) {
       $newSubscribers[$key] = $value;
      }
     }
     $subscribers = $newSubscribers;
    } else {
     array_push($subscribers, $you);
     $r = "Unsubscribe";
    }
    $shop["Subscribers"] = $subscribers;
    $this->system->Data("Save", ["shop", $id, $shop]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
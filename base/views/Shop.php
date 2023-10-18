<?php
 Class Shop extends GW {
  function __construct() {
   parent::__construct();
   $this->root = $_SERVER["DOCUMENT_ROOT"]."/base/pay/Braintree.php";
   $this->you = $this->core->Member($this->core->Username());
  }
  function Banish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Username is missing."
   ];
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($username)) {
    $r = [
     "Body" => "You cannot fire yourself."
    ];
    $username = base64_decode($username);
    if($username != $you) {
     $accessCode = "Accepted";
     $r = [
      "Actions" => [
       $this->core->Element(["button", "Fire $username", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Shop:SaveBanish")."&UN=".$data["UN"])
       ]])
      ],
      "Body" => "You are about to fire $username. Are you sure?",
      "Header" => "Fire $username?"
     ];
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
  function CompleteOrder(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
   $r = [
    "Body" => "The Order Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $id = base64_decode($data["ID"]);
    $po = $this->core->Data("Get", ["po", md5($you)]) ?? [];
    $po[$id]["Complete"] = 1;
    $this->core->Data("Save", ["po", md5($you), $po]);
    $r = [
     "Body" => "The order has been marked as complete!",
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
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
    $shop = $this->core->FixMissing($shop, [
     "Description",
     "Live",
     "Open",
     "Title",
     "Welcome"
    ]);
    $atinput = ".Shop$id-CoverPhoto";
    $at = base64_encode("Set as the Shop's Cover Photo:$atinput");
    $at2 = base64_encode("All done! Feel free to close this card.");
    $atinput = "$atinput .rATT";
    $action = $this->core->Element(["button", "Update", [
     "class" => "CardButton SendData",
     "data-form" => ".Shop$id",
     "data-processor" => base64_encode("v=".base64_encode("Shop:Save"))
    ]]);
    $coverPhoto = $shop["CoverPhotoSource"] ?? "";
    $designViewEditor = "UIE$id";
    $enableHireSection = $shop["EnableHireSection"] ?? 0;
    $header = "Edit ".$shop["Title"];
    $hireLimit = $shop["HireLimit"] ?? 5;
    $hireTerms = $shop["HireTerms"] ?? $this->core->Page("285adc3ef002c11dfe1af302f8812c3a");
    $nsfw = $shop["NSFW"] ?? $y["Privacy"]["NSFW"];
    $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
    $percentages = [];
    for($i = 1; $i < 100; $i++) {
     $percentages[$i] = "$i%";
    }
    $privacy = $shop["Privacy"] ?? $y["Privacy"]["Shop"];
    $processing = $shop["Processing"] ?? [];
    $processing = $this->core->FixMissing($processing, [
     "BraintreeMerchantIDLive",
     "BraintreePrivateKeyLive",
     "BraintreePublicKeyLive",
     "BraintreeTokenLive",
     "PayPalClientID",
     "PayPalClientIDLive",
     "PayPalEmailLive"
    ]);
    $search = base64_encode("Search:Containers");
    $tax = $shop["Tax"] ?? 10.00;
    $r = $this->core->Change([[
     "[Shop.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => "Shop",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => $designViewEditor,
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=N/A&Added=N/A&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Shop.Braintree.Live.MerchantID]" => $processing["BraintreeMerchantIDLive"],
     "[Shop.Braintree.Live.PrivateKey]" => $processing["BraintreePrivateKeyLive"],
     "[Shop.Braintree.Live.PublicKey]" => $processing["BraintreePublicKeyLive"],
     "[Shop.Braintree.Live.Token]" => $processing["BraintreeTokenLive"],
     "[Shop.Braintree.Sandbox.MerchantID]" => $processing["BraintreeMerchantID"],
     "[Shop.Braintree.Sandbox.PrivateKey]" => $processing["BraintreePrivateKey"],
     "[Shop.Braintree.Sandbox.PublicKey]" => $processing["BraintreePublicKey"],
     "[Shop.Braintree.Sandbox.Token]" => $processing["BraintreeToken"],
     "[Shop.CoverPhoto]" => $coverPhoto,
     "[Shop.CoverPhoto.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
     "[Shop.Description]" => base64_encode($shop["Description"]),
     "[Shop.DesignView]" => $designViewEditor,
     "[Shop.EnableHireSection]" => $enableHireSection,
     "[Shop.HireTerms]" => base64_encode($this->core->PlainText([
      "Data" => $hireTerms
     ])),
     "[Shop.HireLimit]" => $hireLimit,
     "[Shop.HireLimits]" => json_encode([
      5 => 5,
      10 => 10,
      15 => 15,
      20 => 20,
      25 => 25,
      30 => 30
     ], true),
     "[Shop.ID]" => $id,
     "[Shop.Header]" => $header,
     "[Shop.PaymentProcessor]" => $paymentProcessor,
     "[Shop.PayPal.Live.ClientID]" => $processing["PayPalClientIDLive"],
     "[Shop.PayPal.Live.Email]" => $processing["PayPalEmailLive"],
     "[Shop.PayPal.Sandbox.ClientID]" => $processing["PayPalClientID"],
     "[Shop.PayPal.Sandbox.Email]" => $processing["PayPalEmail"],
     "[Shop.Tax]" => $tax,
     "[Shop.Tax.Percentages]" => json_encode($percentages, true),
     "[Shop.Title]" => base64_encode($shop["Title"]),
     "[Shop.Visibility.Live]" => $shop["Live"],
     "[Shop.Visibility.NSFW]" => $nsfw,
     "[Shop.Visibility.Open]" => $shop["Open"],
     "[Shop.Visibility.Privacy]" => $privacy,
     "[Shop.Welcome]" => base64_encode($this->core->PlainText([
      "Data" => $shop["Welcome"]
     ]))
    ], $this->core->Page("201c1fca2d1214dddcbabdc438747c9f")]);
    $r = [
     "Action" => $action,
     "Front" => $r,
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
  function EditPartner(array $a) {
   $accessCode = "Denied";
   $action = "";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["UN", "new"]);
   $r = [
    "Body" => "The Partner Identifier is missing."
   ];
   $new = $data["new"] ?? 0;
   $username = (!empty($data["UN"])) ? base64_decode($data["UN"]) : "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($username) || $new == 1) {
    $accessCode = "Accepted";
    if($new == 1) {
     $action = "Hire";
     $company = "Company";
     $description = "Description";
     $header = "New Partner";
     $inputType = "text";
     $title = "Title";
    } else {
     $action = "Update";
     $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
     $partner = $shop["Contributors"][$username] ?? [];
     $company = $partner["Company"];
     $description = $partner["Description"];
     $header = "Edit $username";
     $inputType = "hidden";
     $title = $partner["Title"];
    }
    $r = $this->core->Change([[
     "[Partner.Company]" => $company,
     "[Partner.Description]" => $description,
     "[Partner.Header]" => $header,
     "[Partner.ID]" => md5($username),
     "[Partner.New]" => $new,
     "[Partner.Title]" => $title,
     "[Partner.Username]" => $username,
     "[Partner.Username.InputType]" => $inputType
    ], $this->core->Page("a361fab3e32893af6c81a15a81372bb7")]);
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".Partner".md5($username),
     "data-processor" => base64_encode("v=".base64_encode("Shop:SavePartner"))
    ]]);
    $r = [
     "Action" => $action,
     "Front" => $r
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
  function HireSection(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["Shop"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
    $enableHireSection = $shop["EnableHireSection"] ?? 0;
    $services = $shop["InvoicePresets"] ?? [];
    $hire = (md5($you) != $id) ? 1 : 0;
    $hire = (count($services) > 0 && $hire == 1) ? 1 : 0;
    $hire = (!empty($shop["InvoicePresets"]) && $hire == 1) ? 1 : 0;
    $limit = $shop["HireLimit"] ?? 5;
    $openInvoices = 0;
    $partners = $shop["Contributors"] ?? [];
    $hireText = (count($partners) == 1) ? "Me" : "Us";
    foreach($shop["Invoices"] as $key => $invoice) {
     $invoice = $this->core->Data("Get", ["invoice", $invoice]) ?? [];
     if($invoice["Status"] == "Open") {
      $openInvoices++;
     }
    } if($openInvoices < $limit) {
     $r = ($hire == 1 && $shop["Open"] == 1) ? $this->core->Change([[
      "[Hire.Text]" => $hireText,
      "[Hire.View]" => base64_encode("v=".base64_encode("Invoice:Hire")."&Card=1&CreateJob=1&ID=$id")
     ], $this->core->Page("357a87447429bc7b6007242dbe4af715")]) : "";
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
  function History(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
   $i = 0;
   $si = base64_encode("Profile:SignIn");
   $su = base64_encode("Profile:SignUp");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Header" => "Sign In",
     "Scrollable" => $this->core->Change([[
      "[ShoppingHistory.SignIn]" => base64_encode("v=$si"),
      "[ShoppingHistory.SignUp]" => base64_encode("v=$su")
     ], $this->core->Page("530578e8f5a619e234704ea1f6cd3d64")])
    ];
   } else {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($data["ID"])) {
     $accessCode = "Accepted";
     $h = $y["Shopping"]["History"] ?? [];
     $h = $h[$data["ID"]] ?? [];
     $h2 = [];
     $r = "";
     foreach(array_reverse($h) as $k => $v) {
      $opt = "";
      $product = $this->core->Data("Get", ["product", $v["ID"]]) ?? [];
      $exp = $product["Expires"] ?? [
       "Created" => $product["Created"],
       "Quantity" => 1,
       "TimeSpan" => "year"
      ];
      $ck = ($this->core->timestamp < $this->core->TimePlus($product["Created"], $exp["Quantity"], $exp["TimeSpan"])) ? 1 : 0;
      if(!empty($p) && $ck == 1) {
       $cat = $product["Category"];
       $h2[$k] = $v;
       $i++;
       $coverPhoto = $product["ICO"] ?? $this->core->PlainText([
        "Data" => "[sIMG:MiNY]",
        "Display" => 1
       ]);
       $id = $product["ID"];
       $pts = $this->core->config["PTS"]["Products"];
       $qty = $product["Quantity"] ?? 0;
       $qty2 = $product["QTY"] ?? 0;
       if($cat == "Architecture") {
        # Architecture
       } elseif($cat == "Download") {
        # Downloadable Content
       } elseif($cat == "Donation") {
        # Donations
        $opt = $this->core->Element(["p", "Thank you for donating!"]);
       } elseif($cat == "Product") {
        # Physical Products (require delivery info)
        $opt = $this->core->Element([
         "button", "Contact the Seller", ["class" => "BB BBB v2 v2w"]
        ]);
       } elseif($cat == "Subscription") {
        $opt = $this->core->Element(["button", "Go to Subscription", [
         "class" => "BBB v2 v2w"
        ]]);
       }
       $r .= $this->core->Change([[
        "[Product.Added]" => $this->core->TimeAgo($v["Timestamp"]),
        "[Product.ICO]" => $this->core->CoverPhoto(base64_encode($coverPhoto)),
        "[Product.Description]" => $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $product["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Product.Options]" => $opt,
        "[Product.Quantity]" => $qty2,
        "[Product.Title]" => $product["Title"]
       ], $this->core->Page("4c304af9fcf2153e354e147e4744eab6")]);
      }
     } if($i == 0) {
      $r = $this->core->Element(["h3", "No Results", [
       "class" => "CenterText UpperCase",
       "style" => "margin:1em"
      ]]);
     }
     $y["Shopping"]["History"][$data["ID"]] = $h2;
     $this->core->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
     $r = $this->core->Change([[
      "[ShoppingHistory.List]" => $r
     ], $this->core->Page("20664fb1019341a3ea2e539360108ac3")]);
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
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "CARD",
    "UN",
    "b2",
    "back",
    "lPG",
    "pub"
   ]);
   $bck = ($data["back"] == 1) ? $this->core->Element([
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
    $shops = $this->core->DatabaseSet("SHOP") ?? [];
    foreach($shops as $key => $value) {
     $shop = str_replace("c.oh.shop.", "", $value);
     $shop = $this->core->Data("Get", ["shop", $shop]) ?? [];
     $t = $this->core->Data("Get", ["mbr", $shop]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->core->CallSign($shop["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
     }
    }
   } if(!empty($username) || $i > 0) {
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $id = md5($t["Login"]["Username"]);
    $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
    $enableHireSection = $shop["EnableHireSection"] ?? 0;
    $partners = $shop["Contributors"] ?? [];
    $commission = $shop["Commission"] ?? 0;
    $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
    $ck2 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
    $ck3 = ($ck2 == 0 && $commission > 0) ? 1 : 0;
    if($ck == 1 && $ck3 == 1) {
     $r = $this->core->Change([[
      "[Commission.AddToCart]" => $this->view(base64_encode("Pay:Commission"), ["Data" => [
       "ID" => $id,
       "T" => $t["Login"]["Username"]
      ]])
     ], $this->core->Page("f844c17ae6ce15c373c2bd2a691d0a9a")]);
    } elseif($ck == 1 || $ck2 == 1) {
     $_Search = base64_encode("Search:Containers");
     $bl = $this->core->CheckBlocked([$t, "Members", $you]);
     $cms = $this->core->Data("Get", ["cms", $id]) ?? [];
     $ck2 = $this->core->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $t["Privacy"]["Shop"],
      "UN" => $t["Login"]["Username"],
      "Y" => $you
     ]);
     $ck2 = ($t["Login"]["Username"] == $this->core->ShopID) ? 1 : $ck2;
     $partners = $shop["Contributors"] ?? [];
     $services = $shop["InvoicePresets"] ?? [];
     if($ck == 1 || ($bl == 0 && $ck2 == 1)) {
      $active = 0;
      foreach($partners as $member => $role) {
       if($active == 0 && $member == $you) {
        $active++;
       }
      }
      $ck = ($active == 1 || $id == md5($you)) ? 1 : 0;
      $coverPhoto = $shop["CoverPhoto"] ?? $this->core->PlainText([
       "Data" => "[sIMG:MiNY]",
       "Display" => 1
      ]);
      $coverPhoto = base64_encode($coverPhoto);
      $disclaimer = "Products and Services sold on the <em>Made in New York</em> Shop Network by third parties do not represent the views of <em>Outer Haven</em>, unless sold under the signature Shop.";
      $edit = ($ck == 1) ? $this->core->Element([
       "button", "Edit", [
        "class" => "OpenCard Small v2",
        "data-view" => base64_encode("v=".base64_encode("Shop:Edit")."&ID=".base64_encode($id))
       ]
      ]) : "";
      $payroll = ($id == md5($you)) ? $this->core->Element([
       "button", "Payroll", [
        "class" => "OpenCard Small v2",
        "data-view" => base64_encode("v=".base64_encode("Shop:Payroll"))
       ]
      ]) : "";
      $share = (md5($you) == $id || $shop["Privacy"] == md5("Public")) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element([
       "button", "Share", [
        "class" => "OpenCard Small v2",
        "data-view" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($id)."&Type=".base64_encode("Shop")."&Username=".base64_encode($username))
       ]
      ]) : "";
      $votes = ($id != md5($you)) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $r = $this->core->Change([[
       "[Shop.Back]" => $bck,
       "[Shop.CoverPhoto]" => $this->core->CoverPhoto($coverPhoto),
       "[Shop.Cart]" => base64_encode("v=".base64_encode("Cart:Home")."&UN=".$data["UN"]."&PFST=$pub"),
       "[Shop.Conversation]" => $this->core->Change([[
        "[Conversation.CRID]" => $id,
        "[Conversation.CRIDE]" => base64_encode($id),
        "[Conversation.Level]" => base64_encode(1),
        "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
       ], $this->core->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
       "[Shop.Disclaimer]" => $disclaimer,
       "[Shop.Edit]" => $edit,
       "[Shop.Hire]" => base64_encode("v=".base64_encode("Shop:HireSection")."&Shop=$id"),
       "[Shop.History]" => base64_encode("v=".base64_encode("Shop:History")."&ID=$id&PFST=$pub"),
       "[Shop.ID]" => $id,
       "[Shop.Partners]" => base64_encode("v=$_Search&ID=".base64_encode($id)."&Type=".base64_encode("Shop")."&st=Contributors"),
       "[Shop.Payroll]" => $payroll,
       "[Shop.Share]" => $share,
       "[Shop.Stream]" => base64_encode("v=$_Search&UN=".base64_encode($t["Login"]["Username"])."&b2=".$shop["Title"]."&lPG=SHOP-Products$id&pubP=$pub&st=SHOP-Products"),
       "[Shop.Subscribe]" => base64_encode("v=".base64_encode("Common:SubscribeSection")."&ID=$id&Type=Shop"),
       "[Shop.Title]" => $shop["Title"],
       "[Shop.Welcome]" => $this->core->PlainText([
        "Data" => $shop["Welcome"],
        "HTMLDecode" => 1
       ]),
       "[Shop.Votes]" => base64_encode("v=$votes&ID=$id&Type=4")
      ], $this->core->Page("f009776d658c21277f8cfa611b843c24")]);
     }
    }
   }
   $r = ($data["CARD"] == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
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
  function MadeInNewYork(array $a) {
   $accessCode = "Accepted";
   $_Search = base64_encode("Search:Containers");
   $data = $a["Data"] ?? [];
   $back = $data["back"] ?? "";
   $id = md5($this->core->ShopID);
   $pub = $data["pub"] ?? 0;
   $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
   $partners = $shop["Contributors"] ?? [];
   $username = base64_encode($this->core->ShopID);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $payroll = ($id == md5($you)) ? $this->core->Element([
    "button", "Payroll", [
     "class" => "OpenCard Small v2",
     "data-view" => base64_encode("v=".base64_encode("Shop:Payroll"))
    ]
   ]) : "";
   $r = $this->core->Change([[
    "[MadeInNY.Artists]" => base64_encode("v=".$_Search."&b2=Made in New York&lPG=MadeInNY&st=SHOP"),
    "[MadeInNY.Back]" => $back,
    "[MadeInNY.Hire]" => base64_encode("v=".base64_encode("Shop:HireSection")."&Shop=$id"),
    "[MadeInNY.Products]" => base64_encode("v=".$_Search."&b2=Made in New York&lPG=MadeInNY&st=Products"),
    "[MadeInNY.Subscribe]" => base64_encode("v=".base64_encode("Common:SubscribeSection")."&ID=$id&Type=Shop"),
    "[MadeInNY.VIP]" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=355fd2f096bdb49883590b8eeef72b9c&UN=$username&pub=$pub")
   ], $this->core->Page("62ee437edb4ce6d30afa8b3ea4ec2b6e")]);
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
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
  function Pay(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $now = $this->core->timestamp;
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $shopID = $data["Shop"] ?? "";
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($shopID)) {
    $r = [
     "Body" => "The Payment Type is missing."
    ];
    if(!empty($type)) {
     $_Braintree = $this->core->DocumentRoot."/base/pay/Braintree.php";
     $accessCode = "Accepted";
     $changeData = [];
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     $shopOwner = $this->core->Data("Get", ["mbr", $shopID]) ?? [];
     $step = $data["Step"] ?? 0;
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
     $paymentProcessors = $this->core->config["Shop"]["PaymentProcessors"] ?? [];
     if($paymentProcessor == "Braintree") {
      require_once($_Braintree);
      $envrionment = ($live == 1) ? "production" : "sandbox";
      $braintree = ($live == 1) ? [
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
      $paypal = ($live == 1) ? [
       "ClientID" => $payments["PayPalClientIDLive"]
      ] : [
       "ClientID" => $payments["PayPalClientID"]
      ];
      $token = "";
     } if(in_array($paymentProcessor, $paymentProcessors)) {
      $check = 0;
      $message = "";
      $orderID = $data["OrderID"] ?? "";
      $paymentNonce = $data["payment_method_nonce"] ?? "";
      $processor = "v=".base64_encode("Shop:Pay")."&Shop=$shopID&Step=2&Type=$type";
      $subtotal = 0;
      $tax = 0;
      $total = 0;
      $r = [
       "Body" => "The Payment Processor is missing or unsupportes is missing."
      ];
      if($type == "Checkout") {
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $extension = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $cart = $y["Shopping"]["Cart"][$shopID]["Products"] ?? [];
       $cartCount = count($cart);
       $credits = $y["Shopping"]["Cart"][$shopID]["Credits"] ?? 0;
       $credits = number_format($credits, 2);
       $discountCode = $y["Shopping"]["Cart"][$shopID]["DiscountCode"] ?? 0;
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
        $discountCodeAmount = ($dollarAmount > $percentile) ? "Dollars" : "Percentile";
        $discountCode = [
         "Amount" => $discountCodeAmount,
         "Dollars" => $dollarAmount,
         "Percentile" => $percentile
        ];
        if($discountCode["Amount"] == "Dollars") {
         $discountCode = $discountCode["Dollars"];
        } else {
         $discountCode = number_format($discountCode["Percentile"], 2);
        }
       }
       $subtotal = $subtotal - $credits - $discountCode;
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
          $extension = "229e494ec0f0f43824913a622a46dfca";
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $message = $this->core->Element([
           "p", "Thank you for your purchase!"
          ]);
          $points = $y["Points"] ?? 0;
          $physicalOrders = $this->core->Data("Get", [
           "po",
           $shopID
          ]) ?? [];
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
              "UN" => $shopOwner["Login"]["Username"],
              "You" => $y
             ]);
             $physicalOrders = ($cartOrder["ERR"] == 0) ? $cartOrder["PhysicalOrders"] : $physicalOrders;
             $message .= $cartOrder["Response"];
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
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "You are about to complete your purchase with <em>".$shop["Title"]."</em>. Please verify that the total listed below is accurate."
        ]);
       }
      } elseif($type == "Commission") {
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $extension = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $subtotal = $data["Amount"] ?? base64_encode(0);
       $subtotal = base64_decode($subtotal);
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
          $extension = "229e494ec0f0f43824913a622a46dfca";
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $_LastMonth = $this->core->LastMonth()["LastMonth"];
          $_LastMonth = explode("-", $_LastMonth);
          $income = $this->core->Data("Get", ["id", md5($you)]) ?? [];
          $income[$_LastMonth[0]][$_LastMonth[1]]["PaidCommission"] = 1;
          $points = $strippedTotal * 1000;
          $y["Points"] = $y["Points"] + $points;
          $y["Subscriptions"]["Artist"] = [
            "A" => 1,
            "B" => $now,
            "E" => $this->TimePlus($now, 1, "month")
          ];
          $y["Verified"] = 1;
          $yourShop = $this->core->Data("Get", [
           "shop",
           md5($you)
          ]) ?? [];
          $yourShop["Open"] = 1;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->core->Data("Save", ["shop", md5($you), $yourShop]);
          $this->core->Revenue([$shopOwner["Login"]["Username"], [
           "Cost" => 0,
           "ID" => "COMMISSION*".$shop["Title"],
           "Partners" => $shop["Contributors"],
           "Profit" => $total,
           "Quantity" => 1,
           "Title" => "COMMISSION*".$shop["Title"]
          ]]);
          $message = $this->core->Element([
           "p", "We appreciate your commission payment of $$total to <em>".$shop["Title"]."</em>, as well as your continued business with us! As a token of gratitude, we are also giving you $points which you may redeem for Credits at any shop within our network.<br/>"
          ]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "Thank you very much for your commission payment of $$total (includes tax) to <em>".$shop["Title"]."</em>. We hope to continue providing great ways to maximize your business with us."
        ]);
        $subtotal = str_replace(",", "", $subtotal);
        $processor .= "&Amount=".$data["Amount"];
       }
      } elseif($type == "Disbursement") {
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $extension = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $partner = base64_decode($data["PayTo"]);
       $subtotal = $data["Amount"] ?? base64_encode(0);
       $subtotal = base64_decode($subtotal);
       $total = number_format($subtotal, 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
          $extension = "229e494ec0f0f43824913a622a46dfca";
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $disbursementID = "DISBURSEMENTS*$partner";
          $income = $this->core->Data("Get", ["id", md5($you)]) ?? [];
          $partnerShop = $this->core->Data("Get", [
           "shop",
           md5($partner)
          ]) ?? [];
          $this->core->Revenue([$partner, [
           "Cost" => 0,
           "ID" => $disbursementID,
           "Partners" => $partnerShop["Contributors"],
           "Profit" => $total,
           "Quantity" => 1,
           "Title" => $disbursementID
          ]]);
          $this->core->Revenue([$you, [
           "Cost" => $total,
           "ID" => $disbursementID,
           "Partners" => $shop["Contributors"],
           "Profit" => 0,
           "Quantity" => 1,
           "Title" => $disbursementID
          ]]);
          $income[$data["Year"]][$data["Month"]]["Partners"][$partner]["Paid"] = 1;
          $this->core->Data("Save", ["id", md5($you), $income]);
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $y["Points"] = $y["Points"] + ($strippedTotal * 1000);
          $message = $this->core->Element([
           "p", "We appreciate you for recognizing $partner's work with your $$total payment."
          ]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "You are about to pay $partner $$total for their previous work."
        ]);
        $subtotal = str_replace(",", "", $subtotal);
        $processor .= "&Amount=".$data["Amount"]."&Month=".$data["Month"]."&PayTo=".$data["PayTo"]."&Year=".$data["Year"];
       }
      } elseif($type == "Donation") {
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $extension = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $subtotal = $data["Amount"] ?? base64_encode(0);
       $subtotal = base64_decode($subtotal);
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
          $extension = "229e494ec0f0f43824913a622a46dfca";
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $points = $strippedTotal * 1000;
          $y["Points"] = $y["Points"] + $points;
          $y["Verified"] = 1;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $message = $this->core->Element([
           "p", "We appreciate your donation of $$total to <em>".$shop["Title"]."</em>! This will help fund our continuing effort to preserve free speech on the internet. We are also giving you $points towards Credits which you may use for future purchases if you are currently signed in."
          ]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "Thank you very much for considering a donation of $$total (includes tax) to <em>".$shop["Title"]."</em>."
        ]);
        $subtotal = str_replace(",", "", $subtotal);
        $processor .= "&Amount=".$data["Amount"];
       }
      } elseif($type == "Invoice") {
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $charge = $data["Charge"] ?? "";
       $extension = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $invoiceID = $data["Invoice"] ?? "";
       $invoice = $this->core->Data("Get", [
        "invoice",
        $invoiceID
       ]) ?? [];
       $charges = $invoice["Charges"] ?? [];
       $unpaid = 0;
       foreach($charges as $key => $info) {
        $value = $info["Value"] ?? 0.00;
        $unpaid = $unpaid + $value;
        if($charge == $key || $payInFull == 1) {
         if($info["Paid"] == 0) {
          $subtotal = $subtotal + $value;
         }
        }
       }
       $payInFull = $data["PayInFull"] ?? 0;
       if($subtotal > 0) {
        $tax = $shop["Tax"] ?? 10.00;
        $tax = number_format($subtotal * ($tax / 100), 2);
       }
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        $changeData = [
         "[Checkout.Data]" => json_encode($data, true)
        ];
        $extension = "f9ee8c43d9a4710ca1cfc435037e9abd";
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $name = $invoice["ChargeTo"] ?? $invoice["Email"];
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
            "customer" => [
            "firstName" => $name
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => 1,
           "[Checkout.Order.Success]" => $order->success
          ];
          $extension = "229e494ec0f0f43824913a622a46dfca";
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          if(!empty($charge)) {
           $invoice["Charges"][$charge]["Paid"] = 1;
           if($invoice["Charges"][$charge]["Value"] == $unpaid) {
            $invoice["Status"] = "Closed";
           }
          } elseif($payInFull == 1) {
           $invoice["PaidInFull"] = 1;
           $invoice["Status"] = "Closed";
           $charges = $invoice["Charges"] ?? [];
           foreach($charges as $key => $charge) {
            $invoice["Charges"][$key]["Paid"] = 1;
           }
          }
          $points = $subtotal + ($subtotal * 10000);
          $y["Points"] = $points;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->core->Data("Save", [
           "invoice",
           $invoiceID,
           $invoice
          ]);
          $message = $this->core->Element([
           "p", "Thank you for your payment towards Invoice $invoiceID!"
          ]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "You are about to make a $$total payment towards Invoice $invoiceID."
        ]);
        $processor .= "&Charge=$charge&Invoice=$invoiceID";
       }
      } if($step == 2) {
       $changeData = [
        "[Payment.Message]" => $message,
        "[Payment.Shop]" => $shop["Title"],
        "[Payment.Total]" => number_format($tax + $subtotal, 2)
       ];
       $extension = "83d6fedaa3fa042d53722ec0a757e910";
      } else {
       $changeData = [
        "[Payment.Message]" => $message,
        "[Payment.PayPal.ClientID]" => base64_decode($paypal["ClientID"]),
        "[Payment.Processor]" => base64_encode($processor),
        "[Payment.Region]" => $this->core->region,
        "[Payment.Shop]" => $shopID,
        "[Payment.Title]" => $shop["Title"],
        "[Payment.Token]" => $token,
        "[Payment.Total]" => $total,
        "[Payment.Total.Stripped]" => str_replace(",", "", $total)
       ];
       if($paymentProcessor == "Braintree") {
        $extension = "a1a7a61b89ce8e2715efc0157aa92383";
       } elseif($paymentProcessor == "PayPal") {
        $extension = "7c0f626e2bbb9bd8c04291565f84414a";
       }
      }
      $r = $this->core->Change([
       $changeData,
       $this->core->Page($extension)
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
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function Payroll(array $a) {
   $accessCode = "Denied";
   $_Day = $this->core->Page("ca72b0ed3686a52f7db1ae3b2f2a7c84");
   $_Month = $this->core->Page("2044776cf5f8b7307b3c4f4771589111");
   $_Partner = $this->core->Page("210642ff063d1b3cbe0b2468aba070f2");
   $_Sale = $this->core->Page("a2adc6269f67244fc703a6f3269c9dfe");
   $_Year = $this->core->Page("676193c49001e041751a458c0392191f");
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "You have not earned any income yet...",
    "Header" => "No Data"
   ];
   $y = $this->you;
   $yearTable = "";
   $you = $y["Login"]["Username"];
   $payroll = $this->core->Data("Get", ["id", md5($you)]) ?? [];
   $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
   foreach($payroll as $year => $yearData) {
    if(is_array($yearData)) {
     $accessCode = "Accepted";
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
          $saleTable .= $this->core->Change([[
           "[IncomeDisclosure.Sale.Price]" => number_format($price, 2),
           "[IncomeDisclosure.Sale.Title]" => $product["Title"]
          ], $_Sale]);
         }
        }
        $dayTable .= $this->core->Change([[
         "[IncomeDisclosure.Day]" => $day,
         "[IncomeDisclosure.Day.Sales]" => $saleTable
        ], $_Day]);
       }
       $subtotal = str_replace(",", "", $subtotal);
       $commission = number_format($subtotal * (5.00 / 100), 2);
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format(($subtotal - $commission - $tax), 2);
       $revenue = str_replace(",", "", $total);
       $revenueOverheadCosts = $revenue * (50.00 / 100);
       $revenueSplit = ($revenue - $revenueOverheadCosts) / $partnersCount;
       foreach($partners as $partner => $info) {
        $paid = $info["Paid"] ?? 0;
        $pck = ($paid == 0 && $partner != $you) ? 1 : 0;
        $pck = ($pck == 1 && $month != date("m")) ? 1 : 0;
        $pay = ($pck == 1) ? $this->core->Element([
         "button", "$".number_format($revenueSplit, 2), [
          "class" => "BBB CloseCard OpenFirSTEPTool v2",
          "data-fst" => base64_encode("v=".base64_encode("Shop:Pay")."&Amount=".base64_encode($revenueSplit)."&Month=$month&PayTo=".base64_encode($partner)."&Shop=".md5($you)."&Type=Disbursement&Year=$year")
         ]
        ]) : $this->core->Element(["p", "No Action Needed"]);
        $partnerTable .= $this->core->Change([[
         "[Partner.Description]" => $info["Description"],
         "[Partner.DisplayName]" => $partner,
         "[Partner.Pay]" => $pay
        ], $_Partner]);
       }
       $monthTable .= $this->core->Change([[
        "[IncomeDisclosure.Table.Month]" => $this->ConvertCalendarMonths($month),
        "[IncomeDisclosure.Table.Month.Commission]" => $commission,
        "[IncomeDisclosure.Table.Month.Partners]" => $partnerTable,
        "[IncomeDisclosure.Table.Month.Sales]" => $dayTable,
        "[IncomeDisclosure.Table.Month.Subtotal]" => number_format($subtotal, 2),
        "[IncomeDisclosure.Table.Month.Tax]" => $tax,
        "[IncomeDisclosure.Table.Month.Total]" => $total
       ], $_Month]);
      }
      $yearTable .= $this->core->Change([[
       "[IncomeDisclosure.Table.Year]" => $year,
       "[IncomeDisclosure.Table.Year.Lists]" => $monthTable
      ], $_Year]);
     }
    }
    $yearTable = $yearTable ?? $this->core->Element([
     "h3", "No earnings to report...", [
      "class" => "CenterText",
      "style" => "margin:0.5em"
     ]
    ]);
    $r = $this->core->Change([[
     "[IncomeDisclosure.DisplayName]" => $y["Personal"]["DisplayName"],
     "[IncomeDisclosure.Gallery.Title]" => $shop["Title"],
     "[IncomeDisclosure.Table]" => $yearTable
    ], $this->core->Page("4ab1c6f35d284a6eae66ebd46bb88d5d")]);
    $r = [
     "Front" => $r
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
      if($category == "Architecture") {
       # Architecture
      } elseif($category == "Donation") {
       # Donations
      } elseif($category == "Download") {
       # Downloadable Content
       $opt = $this->core->Element(["p", "Thank You for donating!"]);
      } elseif($category == "Product") {
       # Physical Products
       $opt = $this->core->Element(["button", "Contact the Seller", [
        "class" => "BB v2 v2w"
       ]]);
       $physicalOrders[md5($this->core->timestamp.rand(0, 9999))] = [
        "Complete" => 0,
        "Instructions" => base64_encode($a["Product"]["Instructions"]),
        "ProductID" => $id,
        "Quantity" => $purchaseQuantity,
        "UN" => $you
       ];
      } elseif($category == "Subscription") {
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
       $this->core->Revenue([$shopOwner, [
        "Cost" => $product["Cost"],
        "ID" => $id,
        "Partners" => $contributors,
        "Profit" => $product["Profit"],
        "Quantity" => $purchaseQuantity,
        "Title" => $product["Title"]
       ]]);
      } if($product["Quantity"] > 0) {
       $this->core->Data("Save", ["product", $id, $product]);
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
   return $r;
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing.."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $shops = $this->core->DatabaseSet("MBR");
    $title = $data["Title"] ?? "";
    $i = 0;
    foreach($shops as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $shop = $this->core->Data("Get", ["shop", $value]) ?? [];
     $ttl = $shop["Title"] ?? "";
     if($id != $value && $title == $ttl) {
      $i++;
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Shop <em>$title</em> is taken.",
      "Header" => "Error"
     ];
    } else {
     $accessCode = "Accepted";
     $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
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
         $t = $this->core->Member($f[0]);
         $efs = $this->core->Data("Get", [
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
    $enableHireSection = $data["EnableHireSection"] ?? 0;
    $hireLimit = $data["HireLimit"] ?? 5;
    $hireTerms = $data["HireTerms"] ?? "";
    $invoicePresets = $shop["InvoicePresets"] ?? [];
    $invoices = $shop["Invoices"] ?? [];
    $live = $data["Live"] ?? 0;
    $now = $this->core->timestamp;
    $modifiedBy = $shop["ModifiedBy"] ?? [];
    $modifiedBy[$now] = $you;
    $nsfw = $data["nsfw"] ?? 0;
    $open = $data["Open"] ?? 0;
    $paymentProcessor = $data["PaymentProcessor"] ?? "PayPal";
    $privacy = $data["pri"] ?? $y["Privacy"]["Shop"];
    $products = $shop["Products"] ?? [];
    $tax = $data["Tax"] ?? 10.00;
    $title = $title ?? $shop["Title"];
    $welcome = $data["Welcome"] ?? "";
    $shop = [
     "Contributors" => $contributors,
     "CoverPhoto" => $coverPhoto,
     "CoverPhotoSource" => base64_encode($coverPhotoSource),
     "Description" => $description,
     "EnableHireSection" => $enableHireSection,
     "HireLimit" => $hireLimit,
     "HireTerms" => $this->core->PlainText([
      "Data" => $hireTerms,
      "HTMLEncode" => 1
     ]),
     "InvoicePresets" => $invoicePresets,
     "Invoices" => $invoices,
     "Live" => $live,
     "Modified" => $now,
     "ModifiedBy" => $modifiedBy,
     "NSFW" => $nsfw,
     "Open" => $open,
     "PaymentProcessor" => $paymentProcessor,
     "Privacy" => $privacy,
     "Processing" => $shop["Processing"],
     "Products" => $products,
     "Tax" => $tax,
     "Title" => $title,
     "Welcome" => $this->core->PlainText([
      "Data" => $welcome,
      "HTMLEncode" => 1
     ])
    ];
    $this->core->Data("Save", ["shop", $id, $shop]);
    $r = [
     "Body" => "$title has been updated.",
     "Header" => "Done"
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
  function SaveBanish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["UN"]);
   $r = [
    "Body" => "The Username is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["UN"])) {
    $username = base64_decode($data["UN"]);
    if($username == $you) {
     $r = [
      "Body" => "You cannot fire yourself.",
      "Header" => "Error"
     ];
    } else {
     $accessCode = "Accepted";
     $newContributors = [];
     $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
     $contributors = $shop["Contributors"] ?? [];
     foreach($contributors as $key => $value) {
      if($key != $username) {
       $newContributors[$key] = $value;
      }
     }
     $shop["Contributors"] = $newContributors;
     $this->core->Data("Save", ["shop", md5($you), $shop]);
     $r = [
      "Body" => "You fired $username.",
      "Header" => "Done"
     ];
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
  function SaveCreditExChange(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "ID",
    "P",
    "UN"
   ]);
   $points = base64_decode($data["P"]);
   $r = [
    "Body" => "Unknown error.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(is_numeric($points)) {
    $points = ($points < $y["Points"]) ? $points : $y["Points"];
    $credits = $points * 0.00001;
    $creditsDecimal = number_format($credits, 2);
    $r = [
     "Body" => "You requested more credits than you can afford.",
     "Header" => "Error"
    ];
    if($points < $y["Points"]) {
     $accessCode = "Accepted";
     $yourCredits = $y["Shopping"]["Cart"][$data["ID"]]["Credits"] ?? 0;
     $y["Shopping"]["Cart"][$data["ID"]]["Credits"] = $creditsDecimal + $yourCredits;
     $y["Points"] = $y["Points"] - $points;
     $r = [
      "Body" => "<em>$points</em> points were converted to $<em>$creditsDecimal</em> credits, and have <em>".$y["Points"]."</em> remaining.",
      "Header" => "Done"
     ];
     $this->core->Data("Save", ["mbr", md5($you), $y]);
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
  function SaveDiscountCodes(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["DC", "ID"]);
   $i = 0;
   $r = "The Code Identifier is missing.";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = "You must be signed in to continue.";
   } elseif(!empty($data["DC"]) && !empty($data["ID"])) {
    $id = base64_decode($data["ID"]);
    $discount = $this->core->Data("Get", ["dc", $id]) ?? [];
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
    $this->core->Data("Save", ["dc", $id, $discount]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
   }
   return $this->core->JSONResponse([
    $accessCode,
    $this->core->Element(["p", $r, ["class" => "CenterText"]])
   ]);
  }
  function SavePartner(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "Company",
    "Description",
    "Title",
    "UN",
    "new"
   ]);
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Username is missing."
   ];
   $y = $this->you;
   $username = $data["UN"];
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($username)) {
    $i = 0;
    $members = $this->core->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     if(md5($username) == $value) {
      $i++;
     }
    } if($i == 0) {
     $r = [
      "Body" => "The Member <em>$username</em> does not exist.",
      "Header" => "Done"
     ];
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "hired" : "updated";
     $now = $this->core->timestamp;
     $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
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
      $this->core->SendBulletin([
       "Data" => [
        "ShopID" => md5($you),
        "Member" => $username,
        "Role" => "Partner"
       ],
       "To" => $username,
       "Type" => "InviteToShop"
      ]);
     }
     $this->core->Data("Save", ["shop", md5($you), $shop]);
     $r = [
      "Body" => "Your Partner $username was $actionTaken.",
      "Header" => "Done"
     ];
    }
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
  function Subscribe(array $a) {
   $accessCode = "Denied";
   $responseType = "Dialog";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $responseType = "UpdateText";
    $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
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
    $this->core->Data("Save", ["shop", $id, $shop]);
   }
   return $this->core->JSONResponse([
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
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
    $r = [
     "Body" => "The order has been marked as complete!",
     "Header" => "Done"
    ];
    $this->core->Data("Save", ["po", md5($you), $po]);
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
     "[Shop.General]" => $this->core->RenderInputs([
      [
       "Attributes" => [
        "class" => "rATT rATT$id-CoverPhoto",
        "data-a" => "#ATTL$id-CoverPhoto",
        "data-u" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
        "name" => "CoverPhoto",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditShop$id-CoverPhoto"
       ],
       "Type" => "Text",
       "Value" => $coverPhoto
      ],
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $id
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "Title",
        "placeholder" => "Title",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Title"
       ],
       "Type" => "Text",
       "Value" => $shop["Title"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "Description",
        "placeholder" => "Description"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Description"
       ],
       "Type" => "TextBox",
       "Value" => $shop["Description"]
      ],
      [
       "Attributes" => [
        "class" => "$designViewEditor Welcome Xdecode req",
        "id" => "EditWelcomeMessage$id",
        "name" => "Welcome",
        "placeholder" => "Welcome"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Welcome Message",
        "WYSIWYG" => 1
       ],
       "Type" => "TextBox",
       "Value" => $this->core->PlainText([
        "Data" => $shop["Welcome"]
       ])
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        "Braintree" => "Braintree",
        "PayPal" => "PayPal"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Payment Processor"
       ],
       "Name" => "PaymentProcessor",
       "Title" => "Payment Processor",
       "Type" => "Select",
       "Value" => $paymentProcessor
      ],
      [
       "Attributes" => [],
       "OptionGroup" => $percentages,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Tax"
       ],
       "Name" => "Tax",
       "Title" => "Tax",
       "Type" => "Select",
       "Value" => $tax
      ]
     ]),
     "[Shop.ID]" => $id,
     "[Shop.Payments.Braintree.Live]" => $this->core->RenderInputs([
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreeMerchantIDLive",
        "placeholder" => "Merchant ID",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Merchant ID"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreeMerchantIDLive"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreePrivateKeyLive",
        "placeholder" => "Private Key",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Private Key"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreePrivateKeyLive"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreePublicKeyLive",
        "placeholder" => "Public Key",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Public Key"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreePublicKeyLive"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreeTokenLive",
        "placeholder" => "Token",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Token"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreeTokenLive"])
      ]
     ]),
     "[Shop.Payments.Braintree.Sandbox]" => $this->core->RenderInputs([
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreeMerchantID",
        "placeholder" => "Merchant ID",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Merchant ID"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreeMerchantID"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreePrivateKey",
        "placeholder" => "Private Key",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Private Key"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreePrivateKey"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreePublicKey",
        "placeholder" => "Public Key",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Public Key"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreePublicKey"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_BraintreeToken",
        "placeholder" => "Token",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Token"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["BraintreeToken"])
      ]
     ]),
     "[Shop.Payments.PayPal.Live]" => $this->core->RenderInputs([
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_PayPalClientIDLive",
        "placeholder" => "Client ID",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Client ID"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["PayPalClientIDLive"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_PayPalEmailLive",
        "placeholder" => "Email",
        "type" => "email"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Email"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["PayPalEmailLive"])
      ]
     ]),
     "[Shop.Payments.PayPal.Sandbox]" => $this->core->RenderInputs([
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_PayPalClientID",
        "placeholder" => "Client ID",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Client ID"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["PayPalClientID"])
      ],
      [
       "Attributes" => [
        "class" => "Xdecode",
        "name" => "Processing_PayPalEmail",
        "placeholder" => "Email",
        "type" => "email"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Email"
       ],
       "Type" => "Text",
       "Value" => base64_decode($processing["PayPalEmail"])
      ]
     ]),
     "[Shop.Title]" => $shop["Title"],
     "[Shop.Visibility]" => $this->core->RenderInputs([
      [
       "Attributes" => [],
       "OptionGroup" => [
        0 => "No",
        1 => "Yes"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Live"
       ],
       "Name" => "Live",
       "Title" => "Live",
       "Type" => "Select",
       "Value" => $shop["Live"]
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        0 => "No",
        1 => "Yes"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Open"
       ],
       "Name" => "Open",
       "Title" => "Open",
       "Type" => "Select",
       "Value" => $shop["Open"]
      ]
     ]).$this->core->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->core->RenderVisibilityFilter([
      "Value" => $privacy
     ])
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
    $subscribers = $shop["Subscribers"] ?? [];
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
      $hire = (md5($you) != $id) ? 1 : 0;
      $hire = (count($services) > 0 && $hire == 1) ? 1 : 0;
      $hire = (!empty($shop["InvoicePresets"]) && $hire == 1) ? 1 : 0;
      $hireText = (count($partners) == 1) ? "Me" : "Us";
      $hire = ($hire == 1) ? $this->core->Change([[
       "[Hire.Text]" => $hireText,
       "[Hire.View]" => base64_encode("v=".base64_encode("Invoice:Hire")."&CreateJob=1&ID=$id")
      ], $this->core->Page("357a87447429bc7b6007242dbe4af715")]) : "";
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
      $subscribe = (md5($you) != $id && $this->core->ID != $you) ? 1 : 0;
      $subscribeText = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
      $subscribe = ($subscribe == 1) ? $this->core->Change([[
       "[Subscribe.ContentID]" => $id,
       "[Subscribe.ID]" => md5($you),
       "[Subscribe.Processor]" => base64_encode("v=".base64_encode("Shop:Subscribe")),
       "[Subscribe.Text]" => $subscribeText,
       "[Subscribe.Title]" => $shop["Title"]
      ], $this->core->Page("489a64595f3ec2ec39d1c568cd8a8597")]) : "";
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
       "[Shop.Hire]" => $hire,
       "[Shop.History]" => base64_encode("v=".base64_encode("Shop:History")."&ID=$id&PFST=$pub"),
       "[Shop.ID]" => $id,
       "[Shop.Partners]" => base64_encode("v=$_Search&ID=".base64_encode($id)."&Type=".base64_encode("Shop")."&st=Contributors"),
       "[Shop.Payroll]" => $payroll,
       "[Shop.Share]" => $share,
       "[Shop.Stream]" => base64_encode("v=$_Search&UN=".base64_encode($t["Login"]["Username"])."&b2=".$shop["Title"]."&lPG=SHOP-Products$id&pubP=$pub&st=SHOP-Products"),
       "[Shop.Subscribe]" => $subscribe,
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
   $subscribers = $shop["Subscribers"] ?? [];
   $username = base64_encode($id);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $enableHireSection = $shop["EnableHireSection"] ?? 0;
   $services = $shop["InvoicePresets"] ?? [];
   $hire = (md5($you) != $id) ? 1 : 0;
   $hire = (count($services) > 0 && $hire == 1) ? 1 : 0;
   $hire = (!empty($shop["InvoicePresets"]) && $hire == 1) ? 1 : 0;
   $hireText = (count($partners) == 1) ? "Me" : "Us";
   $hire = ($hire == 1) ? $this->core->Change([[
    "[Hire.Text]" => $hireText,
    "[Hire.View]" => base64_encode("v=".base64_encode("Invoice:Hire")."&CreateJob=1&ID=$id")
   ], $this->core->Page("357a87447429bc7b6007242dbe4af715")]) : "";
   $payroll = ($id == md5($you)) ? $this->core->Element([
    "button", "Payroll", [
     "class" => "OpenCard Small v2",
     "data-view" => base64_encode("v=".base64_encode("Shop:Payroll"))
    ]
   ]) : "";
   $subscribe = (md5($you) != $id && $this->core->ID != $you) ? 1 : 0;
   $subscribeText = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
   $subscribe = ($subscribe == 1) ? $this->core->Change([[
    "[Subscribe.ContentID]" => $id,
    "[Subscribe.ID]" => md5($you),
    "[Subscribe.Processor]" => base64_encode("v=".base64_encode("Shop:Subscribe")),
    "[Subscribe.Text]" => $subscribeText,
    "[Subscribe.Title]" => $shop["Title"]
   ], $this->core->Page("489a64595f3ec2ec39d1c568cd8a8597")]) : "";
   $r = $this->core->Change([[
    "[MadeInNY.Artists]" => base64_encode("v=".$_Search."&b2=Made in New York&lPG=MadeInNY&st=SHOP"),
    "[MadeInNY.Back]" => $back,
    "[MadeInNY.Hire]" => $hire,
    "[MadeInNY.Products]" => base64_encode("v=".$_Search."&b2=Made in New York&lPG=MadeInNY&st=Products"),
    "[MadeInNY.Subscribe]" => $subscribe,
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
     $accessCode = "Accepted";
     $r = $this->core->Element([
      "h1", "Pay"
     ]).$this->core->Element([
      "p", "A new, consolidated payment workflow will be built here to accomodate all payment types."
     ]);
     if($type == "Invoice") {
      $r.=$this->core->Element(["p", "Invoice"]);//TEMP
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
       $total = number_format($subtotal - $commission - $tax, 2);
       $intTotal = str_replace(",", "", $total);
       $revenueOverheadCosts = $intTotal * (5.00 / 100);
       $revenueSplit = ($intTotal - $revenueOverheadCosts) / $partnersCount;
       foreach($partners as $partner => $info) {
        $paid = $info["Paid"] ?? 0;
        $pck = ($paid == 0 && $partner != $you) ? 1 : 0;
        $pck = ($pck == 1 && $month != date("m")) ? 1 : 0;
        $pay = ($pck == 1) ? $this->core->Element([
         "button", "$".number_format($revenueSplit, 2), [
          "class" => "BB BBB v2",
          "data-lm" => base64_encode($month),
          "onclick" => "dB2C();FST('N/A', 'v=".base64_encode("Pay:Disbursement")."&Amount=".base64_encode($revenueSplit)."&&Month=$month&UN=".base64_encode($partner)."&Year=$year', '".md5("Pay".md5($partner))."');"
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
    $invoicePresets = $shop["InvoicePresets"] ?? [];
    $invoices = $shop["Invoices"] ?? [];
    $live = $data["Live"] ?? 0;
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
     "InvoicePresets" => $invoicePresets,
     "Invoices" => $invoices,
     "Live" => $live,
     "Modified" => $this->core->timestamp,
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
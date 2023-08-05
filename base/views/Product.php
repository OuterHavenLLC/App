<?php
 Class Product extends GW {
  function __construct() {
   parent::__construct();
   $this->illegal = $this->system->core["SYS"]["Illegal"];
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $r = [
    "Body" => "The Product Identifier is missing."
   ];
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $_YourPrivacy = $y["Privacy"] ?? [];
    $accessCode = "Accepted";
    $id = ($new == 1) ? md5("MiNY_PROD_$you".$this->system->timestamp) : $id;
    /*$action = ($new == 1) ? "Post" : "Update";
    $action = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditProduct$id",
     "data-processor" => base64_encode("v=".base64_encode("Product:Save"))
    ]]);*/
    $attachments = "";
    $bundledProducts = "";
    $dlc = "";
    $product = $this->system->Data("Get", ["miny", $id]) ?? [];
    $product = $this->system->FixMissing($product, [
     "Description",
     "Disclaimer",
     "Body",
     "Category",
     "Instructions",
     "Role",
     "Price",
     "Title"
    ]);
    $title = $product["Title"] ?? "Product";
    $at = base64_encode("Added to $title!");
    $at2input = ".ATTI$id";
    $at2 = base64_encode("Set as Product Cover Photo:$at2input");
    $at2input = "$at2input .rATT";
    $at3input = ".ATTDLC$id";
    $at3 = base64_encode("Add Downloadable Content to $title:$at3input");
    $at3input = "$at3input .rATT";
    $at4input = ".ATTF$id";
    $at4 = base64_encode("Add to $title Demo Files:$at4input");
    $at4input = "$at4input .rATT";
    $at5input = ".ATTP$id";
    $at5 = base64_encode("Add to $title Bundle:.ATTP$id");
    $at5input = "$at4input .rATT";
    $categories = ($y["Rank"] == md5("High Command")) ? [
     "ARCH" => "Architecture",
     "DLC" => "Downloadable",
     "DONATE" => "Donation",
     "PHYS" => "Physical Product",
     "SUB" => "Subscription"
    ] : [
     "DLC" => "Downloadable",
     "DONATE" => "Donation",
     "PHYS" => "Physical Product"
    ];
    $category = $product["Category"] ?? "";
    $cost = $product["Cost"] ?? 0.00;
    $coverPhoto = $product["ICO-SRC"] ?? "";
    $created = $product["Created"] ?? $this->system->timestamp;
    $expirationQuantities = [];
    $designViewEditor = "UIE$id";
    $editorLiveView = base64_encode("LiveView:EditorMossaic");
    $header = ($new == 1) ? "New Product" : "Edit ".$product["Title"];
    $nsfw = $product["NSFW"] ?? $_YourPrivacy["NSFW"];
    $privacy = $product["Privacy"] ?? $_YourPrivacy["Products"];
    $profit = $product["Profit"] ?? 0.00;
    $quantities = [];
    $quantity = $product["Quantity"] ?? "-1";
    $search = base64_encode("Search:Containers");
    $subscriptionTerm = $product["SubscriptionTerm"] ?? "";
    for($i = 1; $i <= 100; $i++) {
     $expirationQuantities[$i] = $i;
     $quantities[$i] = $i;
    } if(!empty($product["Attachments"])) {
     $attachments = base64_encode(implode(";", $product["Attachments"]));
    } if(!empty($product["Bundled"])) {
     $bundledProducts = base64_encode(implode(";", $product["Bundled"]));
    } if(!empty($product["DLC"])) {
     $dlc = base64_encode(implode(";", $product["DLC"]));
    }
    $r = $this->system->Change([[
     "[Product.AdditionalContent]" => $this->system->Change([
      [
       "[Extras.ContentType]" => "Product",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => $designViewEditor,
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=$id")
      ], $this->system->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Product.Header]" => $header,
     "[Product.ID]" => $id,
     "[Product.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "Created",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $created
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
        "name" => "new",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $new
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTF",
        "data-a" => "#ATTL$id-ATTF",
        "data-u" => base64_encode("v=$editorLiveView&AddTo=$at4input&ID="),
        "name" => "rATTF",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditProduct$id-ATTF"
       ],
       "Type" => "Text",
       "Value" => $attachments
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTDLC",
        "data-a" => "#ATTL$id-ATTDLC",
        "data-u" => base64_encode("v=$editorLiveView&AddTo=$at3input&ID="),
        "name" => "rATTDLC",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditProduct$id-ATTDLC"
       ],
       "Type" => "Text",
       "Value" => $dlc
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTI",
        "data-a" => "#ATTL$id-ATTI",
        "data-u" => base64_encode("v=$editorLiveView&AddTo=$at2input&ID="),
        "name" => "rATTI",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditProduct$id-ATTI"
       ],
       "Type" => "Text",
       "Value" => $coverPhoto
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTP",
        "data-a" => "#ATTL$id-ATTP",
        "data-u" => base64_encode("v=$editorLiveView&AddTo=$at5input&ID="),
        "name" => "rATTP",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditProduct$id-ATTP"
       ],
       "Type" => "Text",
       "Value" => $bundledProducts
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
       "Value" => $product["Title"]
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
       "Value" => $product["Description"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "Disclaimer",
        "placeholder" => "Disclaimer"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Disclaimer"
       ],
       "Type" => "TextBox",
       "Value" => $product["Disclaimer"]
      ],
      [
       "Attributes" => [
        "class" => "$designViewEditor Body Xdecode req",
        "id" => "EditProductBody$id",
        "name" => "Body",
        "placeholder" => "Body"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Body",
        "WYSIWYG" => 1
       ],
       "Type" => "TextBox",
       "Value" => $this->system->PlainText([
        "Data" => $product["Body"]
       ])
      ],
      [
       "Attributes" => [],
       "OptionGroup" => $categories,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Category"
       ],
       "Name" => "ProductCategory",
       "Title" => "Category",
       "Type" => "Select",
       "Value" => $category
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        0 => "No",
        0 => "Yes"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Instructions"
       ],
       "Name" => "ProductInstructions",
       "Title" => "Instructions",
       "Type" => "Select",
       "Value" => $product["Instructions"]
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        0 => "Administrator",
        1 => "Contributor"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Role"
       ],
       "Name" => "Role",
       "Type" => "Select",
       "Value" => $product["Role"]
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        "Month" => "Month",
        "Year" => "Year"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Subscription Term"
       ],
       "Name" => "ProductSubscriptionTerm",
       "Title" => "Subscription Term",
       "Type" => "Select",
       "Value" => $subscriptionTerm
      ]
     ]),
     "[Product.Expiration]" => $this->system->RenderInputs([
      [
       "Attributes" => [],
       "OptionGroup" => $expirationQuantities,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Quantity"
       ],
       "Name" => "ProductExpiresQuantity",
       "Title" => "Quantity",
       "Type" => "Select",
       "Value" => ""
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        "Month" => "Month",
        "Year" => "Year"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Time Frame"
       ],
       "Name" => "ProductExpiresTimeSpan",
       "Title" => "Time Frame",
       "Type" => "Select",
       "Value" => ""
      ]
     ]),
     "[Product.Inventory]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "class" => "CheckIfNumeric",
        "data-symbols" => "Y",
        "maxlen" => "7",
        "name" => "Cost",
        "placeholder" => "5.00",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Cost"
       ],
       "Type" => "Text",
       "Value" => $cost
      ],
      [
       "Attributes" => [
        "class" => "CheckIfNumeric",
        "data-symbols" => "Y",
        "maxlen" => "7",
        "name" => "Profit",
        "placeholder" => "5.00",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Profit"
       ],
       "Type" => "Text",
       "Value" => $profit
      ],
      [
       "Attributes" => [],
       "OptionGroup" => $quantities,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Quantity"
       ],
       "Name" => "Quantity",
       "Title" => "Quantity",
       "Type" => "Select",
       "Value" => $quantity
      ]
     ]),
     "[Product.Visibility]" => $this->system->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->system->RenderVisibilityFilter([
      "Value" => $privacy
     ])
    ], $this->system->Page("3e5dc31db9719800f28abbaa15ce1a37")]);
    $r = [
     #"Action" => $action,
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
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "AddTo",
    "Added",
    "CARD",
    "CallSign",
    "ID",
    "UN",
    "b2",
    "back",
    "lPG",
    "pub"
   ]);
   $i = 0;
   $id = $data["ID"];
   $lpg = $data["lPG"];
   $bck = ($data["back"] == 1) ? $this->system->Element(["button", "Back to ".$data["b2"], [
    "class" => "GoToParent LI head",
    "data-type" => ".OHCC;$lpg"
   ]]) : "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Product could not be found."
   ];
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
    $accessCode = "Accepted";
    $products = $this->system->DatabaseSet("PROD") ?? [];
    foreach($products as $key => $value) {
     $product = str_replace("c.oh.miny.", "", $value);
     $product = $this->system->Data("Get", ["shop", $product]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->system->CallSign($product["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
     }
    }
   } if((!empty($id) || $i > 0) && !empty($data["UN"])) {
    $accessCode = "Accepted";
    $base = $this->system->base;
    $username = base64_decode($data["UN"]);
    $t = ($username == $you) ? $y : $this->system->Member($username);
    $product = $this->system->Data("Get", ["miny", $id]) ?? [];
    $shop = $this->system->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $bl = $this->system->CheckBlocked([$y, "Products", $id]);
    $ck = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
    $ck2 = (strtotime($this->system->timestamp) < $product["Expires"]) ? 1 : 0;
    $ck3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
    $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
    $illegal = $product["Illegal"] ?? 0;
    $illegal = ($illegal < $this->illegal) ? 1 : 0;
    $illegal = ($illegal == 1 && $t["Login"]["Username"] != $this->system->ShopID) ? 1 : 0;
    if($bl == 0 && $ck == 1 && $illegal == 0) {
     $actions = "";
     $active = 0;
     foreach($shop["Contributors"] as $member => $role) {
      if($active == 0 && $member == $you) {
       $active++;
      }
     }
     $addTo = $data["AddTo"] ?? base64_encode("");
     $addTo = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
     if(!empty($data["AddTo"]) && $t["Login"]["Username"] == $you) {
      $actions .= $this->system->Element(["button", $addTo[0], [
       "class" => "AddTo Small v2",
       "data-a" => base64_encode($t["Login"]["Username"]."-$value"),
       "data-c" => $data["Added"],
       "data-f" => base64_encode($addTo[1]),
       "data-m" => base64_encode(json_encode([
        "t" => $t["Login"]["Username"],
        "y" => $you
       ]))
      ]]);
     }
     $actions .= ($t["Login"]["Username"] == $you) ? $this->system->Element([
      "button", "Delete", [
       "class" => "CloseCard OpenDialog Small v2",
       "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteProduct")."&ID=".$product["ID"])
      ]
     ]) : "";
     $actions .= ($active == 1) ? $this->system->Element([
      "button", "Edit", [
       "class" => "OpenCard Small v2",
       "data-view" => base64_encode("v=".base64_encode("Product:Edit")."&ID=".$product["ID"])
      ]
     ]) : "";
     $bck = ($data["CARD"] != 1 && $pub == 1) ? $this->system->Element([
      "button", "See more at <em>".$shop["Title"]."</em>", [
       "class" => "CloseCard LI header",
       "onclick" => "W('$base/MadeInNewYork/".$t["Login"]["Username"]."/', '_top');"
      ]
     ]) : $bck;
     $bundle = "";
     $coverPhoto = $product["ICO"] ?? $this->system->PlainText([
      "Data" => "[sIMG:MiNY]",
      "Display" => 1
     ]);
     $coverPhoto = base64_encode($coverPhoto);
     $modified = $product["ModifiedBy"] ?? [];
     if(empty($modified)) {
      $modified = "";
     } else {
      $_Member = end($modified);
      $_Time = $this->system->TimeAgo(array_key_last($modified));
      $modified = " &bull; Modified ".$_Time." by ".$_Member;
      $modified = $this->system->Element(["em", $modified]);
     }
     $votes = ($t["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $r = $this->system->Change([[
      "[Product.Actions]" => $actions,
      "[Product.Back]" => $bck,
      "[Product.Body]" => $this->system->PlainText([
       "Data" => $product["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[Product.Brief.AddToCart]" => base64_encode("v=".base64_encode("Cart:Add")."&ID=".$product["ID"]."&T=".$t["Login"]["Username"]),
      "[Product.Brief.Category]" => $this->system->Element([
       "p", $this->system->ProductCategory($product["Category"]),
       ["class" => "CenterText"]
      ]),
      "[Product.Brief.Description]" => $product["Description"],
      "[Product.Brief.Icon]" => "{product_category}",
      "[Product.Bundled]" => $bundle,
      "[Product.Conversation]" => $this->system->Change([[
       "[Conversation.CRID]" => $product["ID"],
       "[Conversation.CRIDE]" => base64_encode($product["ID"]),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->system->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[Product.Created]" => $this->system->TimeAgo($product["Created"]),
      "[Product.CoverPhoto]" => $this->system->CoverPhoto($coverPhoto),
      "[Product.Disclaimer]" => htmlentities($product["Disclaimer"]),
      "[Product.ID]" => $product["ID"],
      "[Product.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Product;".$product["ID"])),
      "[Product.Modified]" => $modified,
      "[Product.Title]" => $product["Title"],
      "[Product.Share]" => base64_encode("v=".base64_encode("Product:Share")."&ID=".base64_encode($product["ID"])."&UN=".$data["UN"]),
      "[Product.Votes]" => base64_encode("v=$votes&ID=".$product["ID"]."&Type=4")
     ], $this->system->Page("96a6768e7f03ab4c68c7532be93dee40")]);
     $r = ($data["CARD"] == 1) ? [
      "Front" => $r
     ] : $r;
    }
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "Title", "new"]);
   $r = [
    "Body" => "The Product Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $i = 0;
    $new = $data["new"] ?? 0;
    $now = $this->system->timestamp;
    $products = $this->system->DatabaseSet("PROD") ?? [];
    $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
    $title = $data["Title"] ?? "New Product";
    foreach($products as $key => $value) {
     $product = str_replace("c.oh.miny.", "", $value);
     $product = $this->system->Data("Get", ["shop", $product]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->system->CallSign($product["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Product <em>$title</em> has already been taken. Please choose a different one."
     ];
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $id = $data["ID"];
     $product = $this->system->Data("Get", ["miny", $id]) ?? [];
     $attachments = [];
     $bundle = [];
     $category = base64_decode($data["ProductCategory"]);
     $cats = ["ARCH", "DLC", "DONATE", "PHYS", "SUB"];
     $cost = $data["Cost"] ?? 5.00;
     $created = $product["Created"] ?? $now;
     $dlc = [];
     $coverPhoto = "";
     $coverPhotoSource = "";
     $expirationQuantity = $data["ProductExpiresQuantity"] ?? 1;
     $expirationTimeSpan = $data["ProductExpiresTimeSpan"] ?? "year";
     $illegal = $product["Illegal"] ?? 0;
     $modified = $product["Modified"] ?? $now;
     $modifiedBy = $product["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $newProducts = $shop["Products"] ?? [];
     $points = $this->system->core["PTS"];
     $profit = $data["Profit"] ?? 0.00;
     $quantity = $data["Quantity"] ?? "-1";
     $quantity = ($quantity == "-1") ? $quantity : number_format($quantity);
     $username = $product["UN"] ?? $you;
     if(!empty($data["rATT$id"])) {
      $db = explode(";", base64_decode($data["rATT$id"]));
      $dbc = count($db);
      for($i = 0; $i < $dbc; $i++) {
       if(!empty($db[$i])) {
        $dbi = explode("-", base64_decode($db[$i]));
        if(!empty($dbi[0]) && !empty($dbi[1])) {
         array_push($attachments, base64_encode($dbi[0]."-".$dbi[1]));
        }
       }
      }
     } if(!empty($data["rATTDLC$id"])) {
      $db = explode(";", base64_decode($data["rATTDLC$id"]));
      $dbc = count($db);
      for($i = 0; $i < $dbc; $i++) {
       if(!empty($db[$i])) {
        $dbi = explode("-", base64_decode($db[$i]));
        if(!empty($dbi[0]) && !empty($dbi[1])) {
         array_push($dlc, base64_encode($dbi[0]."-".$dbi[1]));
        }
       }
      }
     } if(!empty($data["rATTI$id"])) {
      $db = explode(";", base64_decode($data["rATTI$id"]));
      $dbc = count($db);
      $i2 = 0;
      for($i = 0; $i < $dbc; $i++) {
       if(!empty($db[$i]) && $i2 == 0) {
        $dbi = explode("-", base64_decode($db[$i]));
        if(!empty($dbi[0]) && !empty($dbi[1])) {
         $t = $this->system->Member($dbi[0]);
         $efs = $this->system->Data("Get", [
          "fs",
          md5($t["Login"]["Username"])
         ]) ?? [];
         $coverPhoto = $dbi[0]."/".$efs["Files"][$dbi[1]]["Name"];
         $coverPhotoSource = base64_encode($dbi[0]."-".$dbi[1]);
         $i2++;
        }
       }
      }
     } if(!empty($data["rATTP$id"])) {
      $db = explode(";", base64_decode($data["rATTP$id"]));
      $dbc = count($db);
      for($i = 0; $i < $dbc; $i++) {
       if(!empty($db[$i])) {
        array_push($bundle, $db[$i]);
       }
      }
     } if(in_array($category, $cats)) {
      $points = $points["Products"][$category];
     } else {
      $points = $points["Default"];
     } if(!in_array($id, $newProducts)) {
      array_push($newProducts, $id);
      $shop["Products"] = array_unique($newProducts);
     }
     $y["Points"] = $y["Points"] + $points;
     $product = [
      "Attachments" => $attachments,
      "Body" => $data["Body"],
      "Bundled" => $bundle,
      "Category" => $category,
      "Cost" => number_format($cost, 2),
      "Created" => $created,
      "Description" => htmlentities($data["Description"]),
      "Disclaimer" => $data["Disclaimer"],
      "Expires" => $this->system->TimePlus($now, $expirationQuantity, $expirationTimeSpan),
      "DLC" => $dlc,
      "ICO" => $coverPhoto,
      "ICO-SRC" => base64_encode($coverPhotoSource),
      "ID" => $id,
      "Illegal" => $illegal,
      "Instructions" => $data["ProductInstructions"],
      "Modified" => $modified,
      "ModifiedBy" => $modifiedBy,
      "NSFW" => $data["nsfw"],
      "Points" => $points,
      "Profit" => number_format($profit, 2),
      "Quantity" => $quantity,
      "Role" => $data["Role"],
      "SubscriptionTerm" => $data["ProductSubscriptionTerm"],
      "Title" => $title,
      "UN" => $username
     ];
     $this->system->Data("Save", ["miny", $id, $product]);
     $this->system->Data("Save", ["mbr", md5($you), $y]);
     $this->system->Data("Save", ["shop", md5($you), $shop]);
     $r = [
      "Body" => "The Product <em>$title</em> has been successfully $actionTaken!",
      "Header" => "Done"
     ];
     if($new == 1) {
      $subscribers = $shop["Subscribers"] ?? [];
      foreach($subscribers as $key => $value) {
       $this->system->SendBulletin([
        "Data" => [
         "ProductID" => $id,
         "ShopID" => base64_encode(md5($you))
        ],
        "To" => $value,
        "Type" => "NewProduct"
       ]);
      }
      $this->system->Statistic("PROD");
     } else {
      $this->system->Statistic("PRODu");
     }
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Product Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
    $newProducts = [];
    $products = $shop["Products"] ?? [];
    foreach($products as $key => $value) {
     if($id != $value) {
      $newProducts[$key] = $value;
     }
    }
    $shop["Products"] = $newProducts;
    $this->view(base64_encode("Conversation:SaveDelete"), [
     "Data" => ["ID" => $id]
    ]);
    $this->system->Data("Purge", ["miny", $id]);
    $this->system->Data("Purge", ["local", $id]);
    $this->system->Data("Purge", ["votes", $id]);
    $this->system->Data("Save", ["shop", md5($you), $shop]);
    $r = [
     "Body" => "The Product was deleted.",
     "Header" => "Done"
    ];
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function Share(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Share Sheet Identifier is missing."
   ];
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($username)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $product = $this->system->Data("Get", ["miny", $id]) ?? [];
    $username = base64_decode($username);
    $t = ($username == $you) ? $y : $this->system->Member($username);
    $shop = $this->system->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $shop = $shop["Title"];
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out <em>".$product["Title"]."</em> by $shop!"
     ]).$this->system->Element([
      "div", "[Product:$id]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=Product",
     "[Share.ContentID]" => "Product",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $product["Title"]." by $shop"
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
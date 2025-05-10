<?php
 Class Product extends OH {
  function __construct() {
   parent::__construct();
   $this->illegal = $this->core->config["App"]["Illegal"] ?? 777;
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data): string {// DEBUG EDITOR COMMANDS EXECUTION AND PROCESOR
   $_Card = "";
   $_Commands = "";
   $_Dialog = "";
   $_View = "";
   $data = $data["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $edit = base64_encode("Product:Edit");
   $editor = $data["Editor"] ?? "";
   $id = $data["ID"] ?? md5("ShopProduct-".$this->core->timestamp);
   $new = $data["new"] ?? 0;
   $parentView = $data["ParentView"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $shopID = $data["Shop"] ?? md5($you);
   $shopOwner = $this->core->Data("Get", ["mbr", $shopID]);
   $shopOwner = $shopOwner["Login"]["Username"] ?? "";
   $template = "00f3b49a6e3b39944e3efbcc98b4948d";
   $template = ($y["Rank"] == md5("High Command")) ? "5f00a072066b37c0b784aed2276138a6" : $template;
   $_Card = [
    "Front" => $this->core->Change([[
     "[Product.Architecture]" => base64_encode("v=$edit&Editor=Architecture&Shop=$shopID&new=1"),
     "[Product.Donation]" => base64_encode("v=$edit&Editor=Donation&Shop=$shopID&new=1"),
     "[Product.Download]" => base64_encode("v=$edit&Editor=Download&Shop=$shopID&new=1"),
     "[Product.Product]" => base64_encode("v=$edit&Editor=Product&Shop=$shopID&new=1"),
     "[Product.Service]" => base64_encode("v=".base64_encode("Invoice:Edit")."&Shop=$shopID"),
     "[Product.Subscription]" => base64_encode("v=$edit&Editor=Subscription&new=1")
    ], $this->core->Extension($template)])
   ];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($editor)) {
    $_ExtensionID = "3e5dc31db9719800f28abbaa15ce1a37";
    $_ExtensionID = ($editor == "Architecture") ? "c6d935b62b8dcb47785ccd6fa99fc468" : $_ExtensionID;
    $action = ($new == 1) ? "Post" : "Update";
    $back = (!empty($parentView)) ? $this->core->Element(["button", "Back", [
     "class" => "BB GoToParent v2 v2w",
     "data-type" => $parentView
    ]]) : $back;
    $back = ($new == 1) ? $this->core->Element(["button", "Back", [
     "class" => "GoToParent v2 v2w",
     "data-type" => "ProductEditors"
    ]]) : $back;
    $editorLiveView = base64_encode("LiveView:EditorMossaic");
    $product = $this->core->Data("Get", ["product", $id]);
    $product = $this->core->FixMissing($product, [
     "Description",
     "Disclaimer",
     "Body",
     "Instructions",
     "Role",
     "Price",
     "Title"
    ]);
    $albums = $product["Albums"] ?? [];
    $articles = $product["Articles"] ?? [];
    $attachments = $product["Attachments"] ?? [];
    $blogs = $product["Blogs"] ?? [];
    $blogPosts = $product["BlogPosts"] ?? [];
    $category = $product["Category"] ?? $editor;
    $chats = $product["Chat"] ?? [];
    $cost = $product["Cost"] ?? 0.00;
    $coverPhoto = $product["CoverPhoto"] ?? "";
    $created = $product["Created"] ?? $this->core->timestamp;
    $demoFiles = $product["DemoFiles"] ?? [];
    $description = $product["Description"] ?? "";
    $disclaimer = $product["Disclaimer"] ?? "";
    $expirationQuantity = $product["ExpirationQuantity"] ?? 1;
    $expirationQuantities = [];
    $expirationTerm = $product["ExpirationTerm"] ?? "Year";
    $forums = $product["Forums"] ?? [];
    $forumPosts = $product["ForumPosts"] ?? [];
    $header = ($new == 1) ? "New Product" : "Edit ".$product["Title"];
    $instructions = ($editor == "Product") ? 1 : 0;
    $members = $product["Members"] ?? [];
    $nsfw = $product["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $product["PassPhrase"] ?? "";
    $privacy = $product["Privacy"] ?? $y["Privacy"]["Products"];
    $polls = $product["Polls"] ?? [];
    $products = $product["Bundled"] ?? [];
    $profit = $product["Profit"] ?? 0.00;
    $quantities = [];
    $quantities["-1"] = "Unlimited";
    $quantity = $product["Quantity"] ?? "-1";
    $role = $product["Role"] ?? 0;
    $search = base64_encode("Search:Containers");
    $shops = $product["Shops"] ?? [];
    $subscriptionTerm = $product["SubscriptionTerm"] ?? "";
    $title = $product["Title"] ?? "";
    $updates = $product["Updates"] ?? [];
    for($i = 1; $i <= 100; $i++) {
     $expirationQuantities[$i] = $i;
    } for($i = 0; $i <= 100; $i++) {
     $quantities[$i] = $i;
    }
    $attachments = $this->view(base64_encode("WebUI:Attachments"), [
     "Header" => "Attachments",
     "ID" => $id,
     "Media" => [
      "Album" => $albums,
      "Article" => $articles,
      "Attachment" => $attachments,
      "Blog" => $blogs,
      "BlogPost" => $blogPosts,
      "BundledProduct" => $products,
      "Chat" => $chats,
      "CoverPhoto" => $coverPhoto,
      "DemoFile" => $demoFiles,
      "Forum" => $forums,
      "ForumPost" => $forumPosts,
      "Member" => $members,
      "Poll" => $polls,
      "Shop" => $shops,
      "Update" => $updates
     ],
     "ParentContentID" => $shopOwner
    ]);
    $translateAndViewDeign = $this->view(base64_encode("WebUI:Attachments"), [
     "ID" => $id,
     "Media" => [
      "Translate" => [],
      "ViewDesign" => []
     ]
    ]);
    $generalInformation = [
     [
      "Attributes" => [
       "name" => "Category",
       "type" => "hidden"
      ],
      "Options" => [],
      "Type" => "Text",
      "Value" => $category
     ],
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
       "name" => "Instructions",
       "type" => "hidden"
      ],
      "Options" => [],
      "Type" => "Text",
      "Value" => $instructions
     ],
     [
      "Attributes" => [
       "name" => "Quantity",
       "type" => "hidden"
      ],
      "Options" => [],
      "Type" => "Text",
      "Value" => $quantity
     ],
     [
      "Attributes" => [
       "name" => "ShopID",
       "type" => "hidden"
      ],
      "Options" => [],
      "Type" => "Text",
      "Value" => $shopID
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
      "Value" => $this->core->AESencrypt($title)
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
      "Value" => $this->core->AESencrypt($description)
     ],
     [
      "Attributes" => [
       "class" => "Edit$id req",
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
      "Value" => $this->core->AESencrypt($this->core->PlainText([
       "Data" => $product["Body"],
       "Decode" => 1
      ]))
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
      "Value" => $this->core->AESencrypt($disclaimer)
     ],
     [
      "Attributes" => [
       "name" => "PassPhrase",
       "placeholder" => "Pass Phrase",
       "type" => "text"
      ],
      "Options" => [
       "Container" => 1,
       "ContainerClass" => "Desktop50 MobileFull",
       "Header" => 1,
       "HeaderText" => "Pass Phrase"
      ],
      "Type" => "Text",
      "Value" => $this->core->AESencrypt($passPhrase)
     ],
     [
      "Attributes" => [],
      "OptionGroup" => [
       "0" => "Administrator",
       "1" => "Contributor"
      ],
      "Options" => [
       "Container" => 1,
       "ContainerClass" => "Desktop50 MobileFull",
       "Header" => 1,
       "HeaderText" => "Role"
      ],
      "Name" => "Role",
      "Type" => "Select",
      "Value" => $role
     ]
    ];
    $inventory = [
     [
      "Attributes" => [
       "class" => "CheckIfNumeric",
       "data-symbols" => "Y",
       "maxlen" => 7,
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
      "Value" => $this->core->AESencrypt($cost)
     ],
     [
      "Attributes" => [
       "class" => "CheckIfNumeric",
       "data-symbols" => "Y",
       "maxlen" => 7,
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
      "Value" => $this->core->AESencrypt($profit)
     ]
    ];
    if($editor == "Product") {
     array_push($generalInformation, [
      "Attributes" => [],
      "OptionGroup" => $expirationQuantities,
      "Options" => [
       "Container" => 1,
       "ContainerClass" => "Desktop50 MobileFull",
       "Header" => 1,
       "HeaderText" => "Expiration Quantity"
      ],
      "Name" => "ExpirationQuantity",
      "Type" => "Select",
      "Value" => $expirationQuantity
     ]);
     array_push($generalInformation, [
      "Attributes" => [],
      "OptionGroup" => [
       "month" => "Month",
       "year" => "Year"
      ],
      "Options" => [
       "Container" => 1,
       "ContainerClass" => "Desktop50 MobileFull",
       "Header" => 1,
       "HeaderText" => "Expiration Time Span"
      ],
      "Name" => "ExpirationTimeSpan",
      "Type" => "Select",
      "Value" => $expirationTerm
     ]);
     array_push($inventory, [
      "Attributes" => [],
      "OptionGroup" => $quantities,
      "Options" => [
       "Container" => 1,
       "ContainerClass" => "Desktop50 MobileFull",
       "Header" => 1,
       "HeaderText" => "Quantity"
      ],
      "Name" => "Quantity",
      "Type" => "Select",
      "Value" => 1
     ]);
    } else {
     array_push($generalInformation, [
      "Attributes" => [
       "name" => "ExpirationQuantity",
       "type" => "hidden"
      ],
      "Options" => [],
      "Type" => "Text",
      "Value" => 1000
     ]);
     array_push($generalInformation, [
      "Attributes" => [
       "name" => "ExpirationTimeSpan",
       "type" => "hidden"
      ],
      "Options" => [],
      "Type" => "Text",
      "Value" => "year"
     ]);
    } if($editor == "Subscription") {
     array_push($generalInformation, [
      "Attributes" => [],
      "OptionGroup" => [
       "month" => "Month",
       "year" => "Year"
      ],
      "Options" => [
       "Container" => 1,
       "ContainerClass" => "Desktop50 MobileFull",
       "Header" => 1,
       "HeaderText" => "Subscription Term"
      ],
      "Name" => "SubscriptionTerm",
      "Title" => "Subscription Term",
      "Type" => "Select",
      "Value" => $subscriptionTerm
     ]);
    }
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".ProductInformation$id",
       $generalInformation
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".Inventory$id",
       $inventory
      ]
     ]
    ];
    $_Commands = ($editor == "Subscription") ? [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".ProductInformation$id",
       $generalInformation
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".Inventory$id",
       [
        [
         "Attributes" => [
          "name" => "Cost",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => "0"
        ],
        [
         "Attributes" => [
          "class" => "CheckIfNumeric",
          "data-symbols" => "Y",
          "maxlen" => 7,
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
         "Value" => $this->core->AESencrypt($profit)
        ]
       ]
      ]
     ]
    ] : $_Commands;
    array_push($_Commands, [
     "Name" => "RenderVisibilityFilter",
     "Parameters" => [
      ".NSFW$id",
      [
       "Filter" => "NSFW",
       "Name" => "NSFW",
       "Title" => "Content Status",
       "Value" => $nsfw
      ]
     ]
    ]);
    array_push($_Commands, [
     "Name" => "RenderVisibilityFilter",
     "Parameters" => [
      ".Privacy$id",
      [
       "Value" => $privacy
      ]
     ]
    ]);
    $_View = [
     "ChangeData" => [
      "[Product.Action]" => $action,
      "[Product.Attachments]" => $this->core->RenderView($attachments),
      "[Product.Back]" => $back,
      "[Product.Created]" => $created,
      "[Product.Header]" => $header,
      "[Product.ID]" => $id,
      "[Product.Save]" => $this->core->AESencrypt("v=".base64_encode("Product:Save")),
      "[Product.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign)
     ],
     "ExtensionID" => $_ExtensionID
    ];
    $_Card = ($card == 1) ? [
     "Front" => $_View
    ] : "";
    $_Dialog = "";
    // BEGIN TEMP
    $_Dialog = [
     "Body" => "Debug data:",
     "Header" => "Debug",
     "Scrollable" => json_encode($_Commands, true)
    ];
    // END TEMP
    $_View = ($card == 0) ? $_View : "";
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The requested Product could not be found."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "AddTo",
    "Added",
    "CallSign",
    "b2",
    "back",
   ]);
   $addTo = $data["AddTo"] ?? "";
   $card = $data["CARD"] ?? 0;
   $i = 0;
   $id = $data["ID"] ?? "";
   $lpg = $data["lPG"] ?? "";
   $back = ($data["back"] == 1) ? $this->core->Element(["button", "Back to ".$data["b2"], [
    "class" => "GoToParent LI head",
    "data-type" => $lpg
   ]]) : "";
   $public = $data["pub"] ?? 0;
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($public == 1) {
    $products = $this->core->DatabaseSet("PROD");
    foreach($products as $key => $value) {
     $product = str_replace("nyc.outerhaven.product.", "", $value);
     $product = $this->core->Data("Get", ["shop", $product]);
     $callSignsMatch = ($data["CallSign"] == $this->core->CallSign($product["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
      break;
     }
    }
   } if((!empty($id) || $i > 0) && !empty($username)) {
    $base = $this->core->base;
    $bl = $this->core->CheckBlocked([$y, "Products", $id]);
    $username = base64_decode($username);
    $_Product = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Product;$id"),
     "Owner" => base64_encode($username)
    ]);
    if($_Product["Empty"] == 0) {
     $_AccessCode = "Accepted";
     $product = $_Product["DataModel"];
     $passPhrase = $product["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_Dialog = "";
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Product["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "SecureKey" => base64_encode($passPhrase),
        "ID" => $data["ID"],
        "UN" => $data["UN"],
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Product:Home")
       ], true))
      ]]);
      $_View = $this->core->RenderView($_View);
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = [
       "Body" => "The Key is missing."
      ];
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $_Dialog = "";
      } else {
       $_View = $this->view(base64_encode("Product:Home"), ["Data" => [
        "ID" => $data["ID"],
        "UN" => $data["UN"],
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $options = $_Product["ListItem"]["Options"];
      $shop = $this->core->Data("Get", ["shop", md5($username)]);
      $ck = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
      $t = ($username == $you) ? $y : $this->core->Member($username);
      $ck3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
      $ck = (!empty($shop) && $ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal < $this->illegal) ? 1 : 0;
      $illegal = ($illegal == 1 && $t["Login"]["Username"] != $this->core->ShopID) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $illegal == 0) {
       $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
       $actions = (!empty($addToData)) ? $this->core->Element([
        "button", "Attach", [
         "class" => "Attach Small v2",
         "data-input" => base64_encode($addToData[1]),
         "data-media" => base64_encode("Product;$id")
        ]
       ]) : "";
       $active = 0;
       $partners = $shop["Contributors"] ?? [];
       foreach($partners as $member => $role) {
        if($active == 0 && $member == $you) {
         $active++;
        }
       }
       $blockCommand = ($bl == 0) ? "Block" : "Unblock";
       $actions .= ($username == $you) ? $this->core->Element([
        "button", "Delete", [
         "class" => "CloseCard OpenDialog Small v2",
         "data-encryption" => "AES",
         "data-view" => $options["Delete"]
        ]
       ]) : $this->core->Element([
        "button", $blockCommand, [
         "class" => "Small UpdateButton v2",
         "data-processor" => $options["Block"]
        ]
       ]);
       $actions .= ($active == 1) ? $this->core->Element([
        "button", "Edit", [
         "class" => "GoToView Small v2",
         "data-encryption" => "AES",
         "data-type" => "Product$id;".$options["Edit"]
        ]
       ]) : "";
       $back = ($card != 1 && $public == 1) ? $this->core->Element([
        "button", "See more at <em>".$shop["Title"]."</em>", [
         "class" => "CloseCard LI header",
         "onclick" => "W('$base/MadeInNewYork/".$t["Login"]["Username"]."/', '_top');"
        ]
       ]) : $back;
       $product["Attachments"] = [];
       $share = ($product["UN"] == $you || $product["Privacy"] == md5("Public")) ? 1 : 0;
       $share = ($share == 1) ? $this->core->Element([
        "button", "Share", [
         "class" => "OpenCard Small v2",
         "data-view" => $options["Share"]
        ]
       ]) : "";
       $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($product, "LiveView", [
        "ProductType" => "Product"
       ]);
      $_Commands = [
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".AddToCart$id",
         $this->core->AESencrypt("v=".base64_encode("Cart:Add")."&ID=$id&T=$username")
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Conversation$id",
         $this->core->AESencrypt("v=".base64_encode("Conversation:Home")."&CRID=".base64_encode($id)."&LVL=".base64_encode(1))
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Vote$id",
         $options["Vote"]
        ]
       ]
      ];
       $_Dialog = "";
       $_View = [
        "ChangeData" => [
         "[Attached.Albums]" => $liveViewSymbolicLinks["Albums"],
         "[Attached.Articles]" => $liveViewSymbolicLinks["Articles"],
         "[Attached.Attachments]" => $liveViewSymbolicLinks["Attachments"],
         "[Attached.Blogs]" => $liveViewSymbolicLinks["Blogs"],
         "[Attached.BlogPosts]" => $liveViewSymbolicLinks["BlogPosts"],
         "[Attached.Chats]" => $liveViewSymbolicLinks["Chats"],
         "[Attached.DemoFiles]" => $liveViewSymbolicLinks["DemoFiles"],
         "[Attached.Forums]" => $liveViewSymbolicLinks["Forums"],
         "[Attached.ForumPosts]" => $liveViewSymbolicLinks["ForumPosts"],
         "[Attached.ID]" => $this->core->UUID("UpdateAttachments"),
         "[Attached.Members]" => $liveViewSymbolicLinks["Members"],
         "[Attached.Polls]" => $liveViewSymbolicLinks["Polls"],
         "[Attached.Products]" => $liveViewSymbolicLinks["Products"],
         "[Attached.Shops]" => $liveViewSymbolicLinks["Shops"],
         "[Attached.Updates]" => $liveViewSymbolicLinks["Updates"],
         "[Product.Actions]" => $actions,
         "[Product.Back]" => $back,
         "[Product.Body]" => $this->core->PlainText([
          "Data" => $product["Body"],
          "Decode" => 1,
          "Display" => 1,
          "HTMLDecode" => 1
         ]),
         "[Product.Brief.Category]" => $this->core->Element([
          "p", $product["Category"],
          ["class" => "CenterText"]
         ]),
         "[Product.Brief.Description]" => $_Product["ListItem"]["Description"],
         "[Product.Brief.Icon]" => "{product_category}",
         "[Product.Created]" => $this->core->TimeAgo($product["Created"]),
         "[Product.CoverPhoto]"  => $_Product["ListItem"]["CoverPhoto"],
         "[Product.Disclaimer]" => htmlentities($product["Disclaimer"]),
         "[Product.ID]" => $id,
         "[Product.Modified]" => $_Product["ListItem"]["Modified"],
         "[Product.Report]" => $this->core->AESencrypt("v=".base64_encode("Congress:Report")."&ID=".base64_encode("Product;$id")),
         "[Product.Title]" => $_Product["ListItem"]["Title"],
         "[Product.Share]" => $share
        ],
        "ExtensionID" => "96a6768e7f03ab4c68c7532be93dee40"
       ];
      }
     }
     $_Card = ($card == 1) ? [
      "Front" => $_View
     ] : "";
     $_View = ($card == 0) ? $_View : "";
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Purge(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Product Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $id = base64_decode($id);
    $shop = $this->core->Data("Get", ["shop", md5($you)]);
    $newProducts = [];
    $products = $shop["Products"] ?? [];
    foreach($products as $key => $value) {
     if($id != $value) {
      $newProducts[$key] = $value;
     }
    }
    $shop["Products"] = $newProducts;
    $conversation = $this->core->Data("Get", ["conversation", $id]);
    if(!empty($conversation)) {
     $conversation["Purge"] = 1;
     $this->core->Data("Save", ["conversation", $id, $conversation]);
    }
    $product = $this->core->Data("Get", ["product", $id]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM Products WHERE Product_ID=:ID", [
     ":ID" => $id
    ]);
    $sql->execute();
    if(!empty($product)) {
     $product["Purge"] = 1;
     $this->core->Data("Save", ["product", $id, $product]);
    }
    $translations = $this->core->Data("Get", ["translate", $id]);
    if(!empty($translations)) {
     $translations["Purge"] = 1;
     $this->core->Data("Save", ["translate", $id, $translations]);
    }
    $votes = $this->core->Data("Get", ["votes", $id]);
    if(!empty($votes)) {
     $votes["Purge"] = 1;
     $this->core->Data("Save", ["votes", $id, $votes]);
    }
    $this->core->Data("Save", ["shop", md5($you), $shop]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", "The Product <em>".$product["Title"]."</em> and dependencies were marked for purging.",
      ["class" => "CenterText"]
     ]).$this->core->Element([
      "button", "Okay", ["class" => "CloseDialog v2 v2w"]
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "Success" => "CloseDialog",
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Product or Shop Identifiers are missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $shopID = $data["ShopID"] ?? "";
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"]) && !empty($shopID)) {
    $_Dialog = [
     "Body" => "You are not authorized to manage Products.",
     "Header" => "Forbidden"
    ];
    $shop = $this->core->Data("Get", ["shop", $shopID]);
    $check = 0;
    $contributors = $shop["Contributors"] ?? [];
    $isAdmin  = ($shopID == md5($you)) ? 1 : 0;
    $isContributor = 0;
    foreach($contributors as $member => $role) {
     if($check == 0 && $member == $you) {
      $isContributor++;
      if($role == "Admin") {
       $isAdmin++;
      }
     }
    } if($isAdmin > 0 || $isContributor > 0) {
     $i = 0;
     $new = $data["new"] ?? 0;
     $products = $this->core->DatabaseSet("PROD");
     $title = $data["Title"] ?? "Untitled";
     foreach($products as $key => $value) {
      $product = str_replace("nyc.outerhaven.product.", "", $value);
      $product = $this->core->Data("Get", ["product", $product]);
      $callSignsMatch = ($data["CallSign"] == $this->core->CallSign($product["Title"])) ? 1 : 0;
      $ck = ($callSignsMatch == 0 && $id != $product["ID"]) ? 1 : 0;
      $ck3 = ($product["UN"] == $you) ? 1 : 0;
      if($ck == 0 && $ck2 == 0 && $ck3 == 0) {
       $i++;
      }
     } if($i > 0) {
      $_Dialog = [
       "Body" => "The Product <em>$title</em> has already been taken. Please choose a different one."
      ];
     } else {
      $_AccessCode = "Accepted";
      $actionTaken = ($new == 1) ? "posted" : "updated";
      $albums = [];
      $albumsData = $data["Album"] ?? [];
      $architecture = [];
      $articles = [];
      $articlesData = $data["Article"] ?? [];
      $attachments = [];
      $attachmentsData = $data["Attachment"] ?? [];
      $blogs = [];
      $blogsData = $data["Blog"] ?? [];
      $blogPosts = [];
      $blogPostsData = $data["BlogPost"] ?? [];
      $chats = [];
      $chatsData = $data["Chat"] ?? [];
      $coverPhoto = $data["CoverPhoto"] ?? "";
      $demoFiles = [];
      $demoFilesData = $data["DemoFile"] ?? [];
      $forums = [];
      $forumsData = $data["Forum"] ?? [];
      $forumPosts = [];
      $forumPostsData = $data["ForumPost"] ?? [];
      $id = $data["ID"] ?? "";
      $now = $this->core->timestamp;
      $product = $this->core->Data("Get", ["product", $id]);
      $bundledProducts = [];
      $bundledProducts = $data["BundledProduct"] ?? [];
      $category = $data["Category"] ?? "Product";
      $categories = [
       "Architecture",
       "Donation",
       "Download",
       "Product",
       "Subscription"
      ];
      $cost = $data["Cost"] ?? 0;
      $cost = ($cost == "") ? 0 : $cost;
      $cost = ($cost > 0) ? number_format(str_replace(",", "", $cost), 2) : $cost;
      $created = $product["Created"] ?? $now;
      $dlc = [];
      $expirationQuantity = $data["ExpirationQuantity"] ?? 1;
      $expirationTimeSpan = $data["ExpirationTimeSpan"] ?? "year";
      $illegal = $product["Illegal"] ?? 0;
      $instructions = $data["Instructions"] ?? 0;
      $instructions = ($category == "Product") ? 1 : $instructions;
      $members = []; 
      $membersData = $data["Member"] ?? [];
      $modifiedBy = $product["ModifiedBy"] ?? [];
      $modifiedBy[$now] = $you;
      $newProducts = $shop["Products"] ?? [];
      $passPhrase = $data["PassPhrase"] ?? "";
      $points = $this->core->config["PTS"];
      $polls = []; 
      $pollsData = $data["Poll"] ?? [];
      $products = [];
      $productsData = $data["Product"] ?? [];
      $profit = $data["Profit"] ?? 0;
      $profit = ($profit == "") ? 0 : $profit;
      $profit = ($profit > 0) ? number_format(str_replace(",", "", $profit), 2) : $profit;
      $purge = $product["Purge"] ?? 0;
      $quantity = $data["Quantity"] ?? "-1";
      $quantity = ($quantity == "-1") ? $quantity : number_format($quantity);
      $shopOwner = $this->core->Data("Get", ["mbr", $shopID]);
      $shopOwner = $shopOwner["Login"]["Username"] ?? $you;
      $shops = [];
      $shopsData = $data["Shop"] ?? [];
      $subscriptionTerm = $data["SubscriptionTerm"] ?? "";
      $success = ($new == 1) ? "CloseCard" : "";
      $updates = [];
      $updatesData = $data["Update"] ?? [];
      if(!empty($albumsData)) {
       $media = $albumsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($albums, $media[$i]);
        }
       }
      } if(!empty($articlesData)) {
       $media = $articlesData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($articles, $media[$i]);
        }
       }
      } if(!empty($attachmentsData)) {
       $media = $attachmentsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($attachments, $media[$i]);
        }
       }
      } if(!empty($blogsData)) {
       $media = $blogsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($blogs, $media[$i]);
        }
       }
      } if(!empty($blogPostsData)) {
       $media = $blogPostsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($blogPosts, $media[$i]);
        }
       }
      } if(!empty($bundledProductsData)) {
       $media = $bundledProductsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
        array_push($bundledProducts, $media[$i]);
        }
       }
      } if(!empty($chatsData)) {
       $media = $chatsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($chats, $media[$i]);
        }
       }
      } if(!empty($demoFilesData)) {
       $media = $demoFilesData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($demoFiles, $media[$i]);
        }
       }
      } if(!empty($forumsData)) {
       $media = $forumsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($forums, $media[$i]);
        }
       }
      } if(!empty($forumPostsData)) {
       $media = $forumPostsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($forumPosts, $media[$i]);
        }
       }
      } if(!empty($membersData)) {
       $media = $membersData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($members, $media[$i]);
        }
       }
      } if(!empty($pollsData)) {
       $media = $pollsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($polls, $media[$i]);
        }
       }
      } if(!empty($shopsData)) {
       $media = $shopsData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($shops, $media[$i]);
        }
       }
      } if(!empty($updatesData)) {
       $media = $updatesData;
       for($i = 0; $i < count($media); $i++) {
        if(!empty($media[$i])) {
         array_push($updates, $media[$i]);
        }
       }
      } if(in_array($category, $categories)) {
       $points = $points["Products"][$category];
      } else {
       $points = $points["Default"];
      } if(!in_array($id, $newProducts)) {
       array_push($newProducts, $id);
       $shop["Products"] = array_unique($newProducts);
      } foreach($data as $key => $value) {
       if(strpos($key, "Architecture_") !== false) {
        $key = explode("_", $key);
        $architecture[$key] = $value ?? "";
       }
      }
      $product = [
       "Albums" => $albums,
       "ArchitecturalScpecifications" => $architecture,
       "Articles" => $articles,
       "Attachments" => $attachments,
       "Blogs" => $blogs,
       "BlogPosts" => $blogPosts,
       "Body" => $this->core->PlainText([
        "Data" => $data["Body"],
        "Encode" => 1
       ]),
       "Bundled" => $bundledProducts,
       "Category" => $category,
       "Chats" => $chats,
       "Cost" => str_replace(",", "", $cost),
       "CoverPhoto" => $coverPhoto,
       "Created" => $created,
       "DemoFiles" => $demoFiles,
       "Description" => htmlentities($data["Description"]),
       "Disclaimer" => $data["Disclaimer"],
       "Expires" => $this->core->TimePlus($now, $expirationQuantity, $expirationTimeSpan),
       "Forums" => $forums,
       "ForumPosts" => $forumPosts,
       "ID" => $id,
       "Illegal" => $illegal,
       "Instructions" => $instructions,
       "Members" => $members,
       "ModifiedBy" => $modifiedBy,
       "NSFW" => $data["nsfw"],
       "PassPhrase" => $passPhrase,
       "Points" => $points,
       "Polls" => $polls,
       "Privacy" => $data["Privacy"],
       "Products" => $bundledProducts,
       "Profit" => str_replace(",", "", $profit),
       "Purge" => $purge,
       "Quantity" => $quantity,
       "Role" => $data["Role"],
       "Shops" => $shops,
       "SubscriptionTerm" => $subscriptionTerm,
       "Title" => $title,
       "Updates" => $updates,
       "UN" => $shopOwner
      ];
      /*--$sql = New SQL($this->core->cypher->SQLCredentials());
      $query = "REPLACE INTO Products(
       Product_Category,
       Product_Created,
       Product_Description,
       Product_ID,
       Product_NSFW,
       Product_Privacy,
       Product_Shop,
       Product_Title,
       Product_Username
      ) VALUES(
       :Category,
       :Created,
       :Description,
       :ID,
       :NSFW,
       :Privacy,
       :Shop,
       :Title,
       :Username
      )";
      $sql->query($query, [
       ":Category" => $category,
       ":Created" => $created,
       ":Description" => $product["Description"],
       ":ID" => $id,
       ":NSFW" => $product["NSFW"],
       ":Privacy" => $product["Privacy"],
       ":Shop" => $shopID,
       ":Title" => $product["Title"],
       ":Username" => $product["UN"]
      ]);
      $sql->execute();
      $this->core->Data("Save", ["product", $id, $product]);
      $this->core->Data("Save", ["shop", $shopID, $shop]);
      if($new == 1) {
       $subscribers = $shop["Subscribers"] ?? [];
       $y["Points"] = $y["Points"] + $points;
       $this->core->Data("Save", ["mbr", md5($you), $y]);
       foreach($subscribers as $key => $value) {
        $this->core->SendBulletin([
         "Data" => [
          "ProductID" => $id,
          "ShopID" => base64_encode(md5($you))
         ],
         "To" => $value,
         "Type" => "NewProduct"
        ]);
       }
       $this->core->Statistic("New Product");
      } else {
       $this->core->Statistic("Edit Product");
      }--*/
      $_Dialog = [
       "Body" => "The Product <em>$title</em> has been $actionTaken!",
       "Header" => "Done",
       "Scrollable" => json_encode($product, true)
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => $success
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
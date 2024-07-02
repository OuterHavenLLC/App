<?php
 Class Product extends OH {
  function __construct() {
   parent::__construct();
   $this->illegal = $this->core->config["App"]["Illegal"] ?? 777;
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $edit = base64_encode("Product:Edit");
   $editor = $data["Editor"] ?? "";
   $id = $data["ID"] ?? md5("ShopProduct-".$this->core->timestamp);
   $new = $data["new"] ?? 0;
   $parentView = $data["ParentView"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $shop = $data["Shop"] ?? md5($you);
   $template = "00f3b49a6e3b39944e3efbcc98b4948d";
   $template = ($y["Rank"] == md5("High Command")) ? "5f00a072066b37c0b784aed2276138a6" : $template;
   $r = [
    "Front" => $this->core->Change([[
     "[Product.Architecture]" => base64_encode("v=$edit&Editor=Architecture&Shop=$shop&new=1"),
     "[Product.Donation]" => base64_encode("v=$edit&Editor=Donation&Shop=$shop&new=1"),
     "[Product.Download]" => base64_encode("v=$edit&Editor=Download&Shop=$shop&new=1"),
     "[Product.Product]" => base64_encode("v=$edit&Editor=Product&Shop=$shop&new=1"),
     "[Product.Service]" => base64_encode("v=".base64_encode("Invoice:Edit")."&Shop=$shop"),
     "[Product.Subscription]" => base64_encode("v=$edit&Editor=Subscription&new=1")
    ], $this->core->Extension($template)])
   ];
   if($this->core->ID == $you) {
    $accessCode = "Denied";
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($editor)) {
    $action = ($new == 1) ? "Post" : "Update";
    $attachments = "";
    $additionalContent = $this->view(base64_encode("WebUI:AdditionalContent"), [
     "ID" => $id
    ]);
    $additionalContent = $this->core->RenderView($additionalContent);
    $back = (!empty($parentView)) ? $this->core->Element(["button", "Back", [
     "class" => "GoToParent v2 v2w",
     "data-type" => $parentView
    ]]) : $back;
    $back = ($new == 1) ? $this->core->Element(["button", "Back", [
     "class" => "GoToParent v2 v2w",
     "data-type" => "ProductEditors"
    ]]) : $back;
    $bundledProducts = "";
    $dlc = "";
    $editorLiveView = base64_encode("LiveView:EditorMossaic");
    $product = $this->core->Data("Get", ["product", $id]) ?? [];
    $product = $this->core->FixMissing($product, [
     "Description",
     "Disclaimer",
     "Body",
     "Instructions",
     "Role",
     "Price",
     "Title"
    ]);
    $category = $product["Category"] ?? $editor;
    $cost = $product["Cost"] ?? 0.00;
    $coverPhoto = $product["ICO-SRC"] ?? "";
    $created = $product["Created"] ?? $this->core->timestamp;
    $expirationQuantities = [];
    $extension = "3e5dc31db9719800f28abbaa15ce1a37";
    $extension = ($editor == "Architecture") ? "c6d935b62b8dcb47785ccd6fa99fc468" : $extension;
    $extension = ($editor == "Donation") ? "6f4772a067646699073521d87b943433" : $extension;
    $extension = ($editor == "Download") ? "5921c3ce04d9a878055ebc691b9f445a" : $extension;
    $extension = ($editor == "Subscription") ? "dd2cb760e5291e265889c262fc30d9a2" : $extension;
    $header = ($new == 1) ? "New Product" : "Edit ".$product["Title"];
    $nsfw = $product["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $product["PassPhrase"] ?? "";
    $privacy = $product["Privacy"] ?? $y["Privacy"]["Products"];
    $profit = $product["Profit"] ?? 0.00;
    $quantities = [];
    $quantities["-1"] = "Unlimited";
    $quantity = $product["Quantity"] ?? "-1";
    $search = base64_encode("Search:Containers");
    $subscriptionTerm = $product["SubscriptionTerm"] ?? "";
    for($i = 1; $i <= 100; $i++) {
     $expirationQuantities[$i] = $i;
    } for($i = 0; $i <= 100; $i++) {
     $quantities[$i] = $i;
    } if(!empty($product["Attachments"])) {
     $attachments = base64_encode(implode(";", $product["Attachments"]));
    } if(!empty($product["Bundled"])) {
     $bundledProducts = base64_encode(implode(";", $product["Bundled"]));
    } if(!empty($product["DLC"])) {
     $dlc = base64_encode(implode(";", $product["DLC"]));
    }
    $changeData = [
     "[Product.Action]" => $action,
     "[Product.AdditionalContent]" => $additionalContent["Extension"],
     "[Product.Attachments]" => $attachments,
     "[Product.Attachments.LiveView]" => $additionalContent["LiveView"]["DemoFiles"],
     "[Product.Back]" => $back,
     "[Product.Body]" => base64_encode($this->core->PlainText([
      "Data" => $product["Body"],
      "Decode" => 1
     ])),
     "[Product.BundledProducts]" => $bundledProducts,
     "[Product.BundledProducts.LiveView]" => $additionalContent["LiveView"]["Products"],
     "[Product.Category]" => $category,
     "[Product.Cost]" => base64_encode($cost),
     "[Product.CoverPhoto]" => $coverPhoto,
     "[Product.CoverPhoto.LiveView]" => $additionalContent["LiveView"]["CoverPhoto"],
     "[Product.Created]" => $created,
     "[Product.Description]" => base64_encode($product["Description"]),
     "[Product.DesignView]" => "Edit$id",
     "[Product.Disclaimer]" => base64_encode($product["Disclaimer"]),
     "[Product.Downloads]" => $dlc,
     "[Product.Downloads.LiveView]" => $additionalContent["LiveView"]["DLC"],
     "[Product.ExpirationQuantities]" => json_encode($expirationQuantities, true),
     "[Product.Header]" => $header,
     "[Product.ID]" => $id,
     "[Product.Instructions]" => $product["Instructions"],
     "[Product.New]" => $new,
     "[Product.PassPhrase]" => base64_encode($passPhrase),
     "[Product.Profit]" => base64_encode($profit),
     "[Product.Quantity]" => $quantity,
     "[Product.Quantities]" => json_encode($quantities, true),
     "[Product.Role]" => $product["Role"],
     "[Product.Save]" => base64_encode("v=".base64_encode("Product:Save")),
     "[Product.Shop]" => $shop,
     "[Product.SubscriptionTerm]" => $subscriptionTerm,
     "[Product.Title]" => base64_encode($product["Title"]),
     "[Product.Visibility.NSFW]" => $nsfw,
     "[Product.Visibility.Privacy]" => $privacy
    ];
    $r = $this->core->Change([
     $changeData,
     $this->core->Extension($extension)
    ]);
    $r = ($card == 1) ? [
     "Front" => $r
    ] : $r;
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
   $data = $this->core->FixMissing($data, [
    "AddTo",
    "Added",
    "CallSign",
    "b2",
    "back",
   ]);
   $card = $data["CARD"] ?? 0;
   $i = 0;
   $id = $data["ID"] ?? "";
   $lpg = $data["lPG"] ?? "";
   $back = ($data["back"] == 1) ? $this->core->Element(["button", "Back to ".$data["b2"], [
    "class" => "GoToParent LI head",
    "data-type" => $lpg
   ]]) : "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Product could not be found."
   ];
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
    $accessCode = "Accepted";
    $products = $this->core->DatabaseSet("PROD") ?? [];
    foreach($products as $key => $value) {
     $product = str_replace("nyc.outerhaven.product.", "", $value);
     $product = $this->core->Data("Get", ["shop", $product]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->core->CallSign($product["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
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
     $product = $_Product["DataModel"];
     $passPhrase = $product["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
      $r = $this->core->RenderView($r);
     } elseif($verifyPassPhrase == 1) {
      $accessCode = "Denied";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $r = $this->core->Element(["p", "The Key is missing."]);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $r = $this->core->Element(["p", "The Keys do not match."]);
      } else {
       $accessCode = "Accepted";
       $r = $this->view(base64_encode("Product:Home"), ["Data" => [
        "ID" => $data["ID"],
        "UN" => $data["UN"],
        "ViewProtectedContent" => 1
       ]]);
       $r = $this->core->RenderView($r);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $accessCode = "Accepted";
      $options = $_Product["ListItem"]["Options"];
      $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
      $ck = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
      $t = ($username == $you) ? $y : $this->core->Member($username);
      $ck3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
      $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal < $this->illegal) ? 1 : 0;
      $illegal = ($illegal == 1 && $t["Login"]["Username"] != $this->core->ShopID) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $illegal == 0) {
       $actions = "";
       $active = 0;
       $partners = $shop["Contributors"] ?? [];
       foreach($partners as $member => $role) {
        if($active == 0 && $member == $you) {
         $active++;
        }
       }
       $addTo = $data["AddTo"] ?? base64_encode("");
       $addTo = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
       if(!empty($data["AddTo"]) && $t["Login"]["Username"] == $you) {
        $actions .= $this->core->Element(["button", $addTo[0], [
         "class" => "AddTo Small v2",
         "data-a" => base64_encode("$username-$value"),
         "data-c" => $data["Added"],
         "data-f" => base64_encode($addTo[1]),
         "data-m" => base64_encode(json_encode([
          "t" => $t["Login"]["Username"],
          "y" => $you
         ]))
        ]]);
       }
       $blockCommand = ($bl == 0) ? "Block" : "Unblock";
       $actions .= ($username == $you) ? $this->core->Element([
        "button", "Delete", [
         "class" => "CloseCard OpenDialog Small v2",
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
         "data-type" => "Product$id;".$options["Edit"]
        ]
       ]) : "";
       $back = ($data["CARD"] != 1 && $pub == 1) ? $this->core->Element([
        "button", "See more at <em>".$shop["Title"]."</em>", [
         "class" => "CloseCard LI header",
         "onclick" => "W('$base/MadeInNewYork/".$t["Login"]["Username"]."/', '_top');"
        ]
       ]) : $back;
       $bundle = "";
       $share = ($product["UN"] == $you || $product["Privacy"] == md5("Public")) ? 1 : 0;
       $share = ($share == 1) ? $this->core->Element([
        "button", "Share", [
         "class" => "OpenCard Small v2",
         "data-view" => $options["Share"]
        ]
       ]) : "";
       $r = $this->core->Change([[
        "[Product.Actions]" => $actions,
        "[Product.Back]" => $back,
        "[Product.Body]" => $this->core->PlainText([
         "Data" => $product["Body"],
         "Decode" => 1,
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Product.Brief.AddToCart]" => base64_encode("v=".base64_encode("Cart:Add")."&ID=$id&T=$username"),
        "[Product.Brief.Category]" => $this->core->Element([
         "p", $product["Category"],
         ["class" => "CenterText"]
        ]),
        "[Product.Brief.Description]" => $_Product["ListItem"]["Description"],
        "[Product.Brief.Icon]" => "{product_category}",
        "[Product.Bundled]" => $bundle,
        "[Product.Conversation]" => $this->core->Change([[
         "[Conversation.CRID]" => $id,
         "[Conversation.CRIDE]" => base64_encode($id),
         "[Conversation.Level]" => base64_encode(1),
         "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
        ], $this->core->Extension("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
        "[Product.Created]" => $this->core->TimeAgo($product["Created"]),
        "[Product.CoverPhoto]" => $_Product["ListItem"]["CoverPhoto"],
        "[Product.Disclaimer]" => htmlentities($product["Disclaimer"]),
        "[Product.ID]" => $id,
        "[Product.Illegal]" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("Product;$id")),
        "[Product.Modified]" => $_Product["ListItem"]["Modified"],
        "[Product.Title]" => $_Product["ListItem"]["Title"],
        "[Product.Share]" => $share,
        "[Product.Votes]" => $options["Vote"]
       ], $this->core->Extension("96a6768e7f03ab4c68c7532be93dee40")]);
      }
     }
     $r = ($card == 1) ? [
      "Front" => $r
     ] : $r;
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
  function Purge(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Product Identifier is missing."
   ];
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $shop = $this->core->Data("Get", ["shop", md5($you)]) ?? [];
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
    $r = $this->core->Element([
     "p", "The Product <em>".$product["Title"]."</em> and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $r = [
    "Body" => "The Product or Shop Identifiers are missing."
   ];
   $shopID = $data["Shop"] ?? "";
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"]) && !empty($shopID)) {
    $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
    $check = 0;
    $contributors = $shop["Contributors"] ?? [];
    $isAdmin  = ($shopID == md5($you)) ? 1 : 0;
    $isContributor = 0;
    $r = [
     "Body" => "You are not authorized to manage Products.",
     "Header" => "Forbidden"
    ];
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
     $products = $this->core->DatabaseSet("PROD") ?? [];
     $title = $data["Title"] ?? "Untitled";
     foreach($products as $key => $value) {
      $product = str_replace("nyc.outerhaven.product.", "", $value);
      $product = $this->core->Data("Get", ["product", $product]) ?? [];
      $callSignsMatch = ($data["CallSign"] == $this->core->CallSign($product["Title"])) ? 1 : 0;
      $ck = ($callSignsMatch == 0 && $id != $product["ID"]) ? 1 : 0;
      $ck3 = ($product["UN"] == $you) ? 1 : 0;
      if($ck == 0 && $ck2 == 0 && $ck3 == 0) {
       $i++;
      }
     } if($i > 0) {
      $r = [
       "Body" => "The Product <em>$title</em> has already been taken. Please choose a different one."
      ];
     } else {
      $accessCode = "Accepted";
      $actionTaken = ($new == 1) ? "posted" : "updated";
      $id = $data["ID"] ?? "";
      $now = $this->core->timestamp;
      $product = $this->core->Data("Get", ["product", $id]) ?? [];
      $attachments = [];
      $bundle = [];
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
      $coverPhoto = "";
      $coverPhotoSource = "";
      $created = $product["Created"] ?? $now;
      $dlc = [];
      $expirationQuantity = $data["ExpirationQuantity"] ?? 1;
      $expirationTimeSpan = $data["ExpirationTimeSpan"] ?? "year";
      $illegal = $product["Illegal"] ?? 0;
      $instructions = $data["Instructions"] ?? 0;
      $instructions = ($category == "Product") ? 1 : $instructions;
      $modifiedBy = $product["ModifiedBy"] ?? [];
      $modifiedBy[$now] = $you;
      $newProducts = $shop["Products"] ?? [];
      $passPhrase = $data["PassPhrase"] ?? "";
      $points = $this->core->config["PTS"];
      $profit = $data["Profit"] ?? 0;
      $profit = ($profit == "") ? 0 : $profit;
      $profit = ($profit > 0) ? number_format(str_replace(",", "", $profit), 2) : $profit;
      $purge = $product["Purge"] ?? 0;
      $quantity = $data["Quantity"] ?? "-1";
      $quantity = ($quantity == "-1") ? $quantity : number_format($quantity);
      $subscriptionTerm = $data["SubscriptionTerm"] ?? "";
      $success = ($new == 1) ? "CloseCard" : "";
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
          $t = $this->core->Member($dbi[0]);
          $efs = $this->core->Data("Get", [
           "fs",
           md5($t["Login"]["Username"])
          ]) ?? [];
          $fileName = $efs["Files"][$dbi[1]]["Name"] ?? "";
          if(!empty($fileName)) {
           $coverPhoto = $dbi[0]."/$fileName";
           $coverPhotoSource = base64_encode($dbi[0]."-".$dbi[1]);
           $i2++;
          }
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
      } if(in_array($category, $categories)) {
       $points = $points["Products"][$category];
      } else {
       $points = $points["Default"];
      } if(!in_array($id, $newProducts)) {
       array_push($newProducts, $id);
       $shop["Products"] = array_unique($newProducts);
      }
      $product = [
       "Attachments" => $attachments,
       "Body" => $this->core->PlainText([
        "Data" => $data["Body"],
        "Encode" => 1
       ]),
       "Bundled" => $bundle,
       "Category" => $category,
       "Cost" => str_replace(",", "", $cost),
       "Created" => $created,
       "Description" => htmlentities($data["Description"]),
       "Disclaimer" => $data["Disclaimer"],
       "Expires" => $this->core->TimePlus($now, $expirationQuantity, $expirationTimeSpan),
       "DLC" => $dlc,
       "ICO" => $coverPhoto,
       "ICO-SRC" => base64_encode($coverPhotoSource),
       "ID" => $id,
       "Illegal" => $illegal,
       "Instructions" => $instructions,
       "ModifiedBy" => $modifiedBy,
       "NSFW" => $data["nsfw"],
       "PassPhrase" => $passPhrase,
       "Points" => $points,
       "Privacy" => $data["Privacy"],
       "Profit" => str_replace(",", "", $profit),
       "Purge" => $purge,
       "Quantity" => $quantity,
       "Role" => $data["Role"],
       "SubscriptionTerm" => $subscriptionTerm,
       "Title" => $title,
       "UN" => $username
      ];
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
      }
      $r = [
       "Body" => "The Product <em>$title</em> has been $actionTaken!",
       "Header" => "Done"
      ];
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
    "Success" => $success
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
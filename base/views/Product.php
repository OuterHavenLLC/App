<?php
 Class Product extends GW {
  function __construct() {
   parent::__construct();
   $this->illegal = $this->system->core["SYS"]["Illegal"];
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $fr = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Product Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $fr = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id) || $new == 1) {
    $_YourPrivacy = $y["Privacy"] ?? [];
    $action = ($new == 1) ? "Post" : "Update";
    $attachmentsachments = "";
    $bundle = "";
    $dlc = "";
    $id = ($new == 1) ? md5("MiNY_PROD_$you".$this->system->timestamp) : $id;
    $dv = base64_encode("Common:DesignView");
    $dvi = "UIE$id";
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
    $category = $product["Category"] ?? "";
    $coverPhoto = $product["ICO-SRC"] ?? "";
    $title = $product["Title"] ?? "Product";
    $at = base64_encode("Added to $title!");
    $at2input = ".ATTI$id";
    $at2 = base64_encode("Set as Product Cover Photo:$at2input");
    $at2input = "$at2input .rATT";
    $at2lv = base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$at2input&ID=");
    $at3input = ".ATTDLC$id";
    $at3 = base64_encode("Add Downloadable Content to $title:$at3input");
    $at3input = "$at3input .rATT";
    $at3lv = base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&AddTo=$at3input&ID=");
    $at4input = ".ATTF$id";
    $at4 = base64_encode("Add to $title Demo Files:$at4input");
    $at4input = "$at4input .rATT";
    $at4lv = base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&AddTo=$at4input&ID=");
    $at5input = ".ATTP$id";
    $at5 = base64_encode("Add to $title Bundle:.ATTP$id");
    $at5input = "$at4input .rATT";
    $at5lv = base64_encode("v=".base64_encode("LiveView:EditorProducts")."&AddTo=$at5input&BNDL=");
    $cost = $product["Cost"] ?? 0.00;
    $created = $product["Created"] ?? $this->system->timestamp;
    $header = ($new == 1) ? "New Product" : "Edit ".$product["Title"];
    $nsfw = $product["NSFW"] ?? $_YourPrivacy["NSFW"];
    $privacy = $product["Privacy"] ?? $_YourPrivacy["Products"];
    $profit = $product["Profit"] ?? 0.00;
    $quantity = $product["Quantity"] ?? "-1";
    $search = base64_encode("Search:Containers");
    $subscriptionTerm = $product["SubscriptionTerm"] ?? "";
    if(!empty($product["Attachments"])) {
     $attachmentsachments = base64_encode(implode(";", $product["Attachments"]));
    } if(!empty($product["Bundled"])) {
     $bundle = base64_encode(implode(";", $product["Bundled"]));
    } if(!empty($product["DLC"])) {
     $dlc = base64_encode(implode(";", $product["DLC"]));
    }
    $bck = $this->system->Change([
     [
      "[CP.ContentType]" => "Product",
      "[CP.Files]" => base64_encode("v=$search&st=XFS&AddTo=$at2&Added=$at&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".$y["Login"]["Username"]),
      "[CP.ID]" => $id
     ], $this->system->Page("dc027b0a1f21d65d64d539e764f4340a")
    ]).$this->system->Change([
     [
      "[DLC.ContentType]" => "Product",
      "[DLC.Files]" => base64_encode("v=$search&st=XFS&AddTo=$at3&Added=$at&UN=$you"),
      "[DLC.ID]" => $id
     ], $this->system->Page("47470fec24054847fc1232df998eafbd")
    ]).$this->system->Change([
     [
      "[MiNY.ContentType]" => "Product",
      "[MiNY.Products]" => base64_encode("v=$search&CARD=1&st=MBR-MiNY&AddTo=$at5&Added=$at"),
      "[MiNY.ID]" => $id
     ], $this->system->Page("0bbe6ad6e68508ac19d3f89af425e369")
    ]).$this->system->Change([
     [
      "[UIV.IN]" => $dvi,
      "[UIV.OUT]" => "UIV$id",
      "[UIV.U]" => base64_encode("v=$dv&DV=")
     ], $this->system->Page("7780dcde754b127656519b6288dffadc")
    ]).$this->system->Change([
     [
      "[XFS.ContentType]" => "Product",
      "[XFS.Files]" => base64_encode("v=$search&st=XFS&AddTo=$at4&Added=$at&UN=$you"),
      "[XFS.ID]" => $id
     ], $this->system->Page("8356860c249e93367a750f3b4398e493")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $fr = $this->system->Change([[
     "[Product.Attachments]" => $attachmentsachments,
     "[Product.Attachments.LiveView]" => $at4lv,
     "[Product.Bundled]" => $bundle,
     "[Product.Bundled.LiveView]" => $at5lv,
     "[Product.Cost]" => $cost,
     "[Product.CoverPhoto]" => $coverPhoto,
     "[Product.CoverPhoto.LiveView]" => $at2lv,
     "[Product.Created]" => $created,
     "[Product.Description]" => $product["Description"],
     "[Product.Disclaimer]" => $product["Disclaimer"],
     "[Product.DownloadableContent]" => $dlc,
     "[Product.DownloadableContent.LiveView]" => $at3lv,
     "[Product.Body]" => $this->system->WYSIWYG([
      "UN" => $you,
      "Body" => $product["Body"],
      "adm" => 1,
      "opt" => [
       "id" => "XPRODBody",
       "class" => "$dvi Body Xdecode req",
       "name" => "Body",
       "placeholder" => "Body",
       "rows" => 20
      ]
     ]),
     "[Product.Header]" => $header,
     "[Product.ID]" => $id,
     "[Product.New]" => $new,
     "[Product.Category]" => $this->system->Select("ProductCategory", "req v2w", $category),
     "[Product.Expiration.Quantity]" => $this->system->Select("ProductExpiresQuantity", "req v2w"),
     "[Product.Expiration.TimeSpan]" => $this->system->Select("ProductExpiresTimeSpan", "req v2w"),
     "[Product.Instructions]" => $this->system->Select("ProductInstructions", "req v2w", $product["Instructions"]),
     "[Product.NSFW]" => $this->system->Select("nsfw", "req v2w", $nsfw),
     "[Product.Privacy]" => $this->system->Select("Privacy", "req v2w", $privacy),
     "[Product.Role]" => $this->system->Select("Role", "req v2w", $product["Role"]),
     "[Product.SubscriptionTerm]" => $this->system->Select("ProductSubscriptionTerm", "req v2w", $subscriptionTerm),
     "[Product.Profit]" => $profit,
     "[Product.Quantity]" => $quantity,
     "[Product.Title]" => $product["Title"]
    ], $this->system->Page("3e5dc31db9719800f28abbaa15ce1a37")]);
    $frbtn = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".Product$id",
     "data-processor" => base64_encode("v=".base64_encode("Product:Save"))
    ]]);
   }
   return $this->system->Card([
    "Back" => $bck,
    "Front" => $fr,
    "FrontButton" => $frbtn
   ]);
  }
  function Home(array $a) {
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
   $r = $this->system->Change([[
    "[Error.Back]" => $bck,
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The requested Product could not be found."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
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
       "class" => "Small dBO dB2C v2",
       "data-type" => "v=".base64_encode("Authentication:DeleteProduct")."&ID=".$product["ID"]
      ]
     ]) : "";
     $actions .= ($active == 1) ? $this->system->Element([
      "button", "Edit", [
       "class" => "Small dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Product:Edit")."&ID=".$product["ID"])
      ]
     ]) : "";
     $bck = ($data["CARD"] != 1 && $pub == 1) ? $this->system->Element([
      "button", "See more at <em>".$shop["Title"]."</em>", [
       "class" => "LI dB2C header",
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
     $reactions = ($t["Login"]["Username"] != $you) ? $this->view(base64_encode("Common:Reactions"), ["Data" => [
      "CRID" => $product["ID"],
      "T" => $t["Login"]["Username"],
      "Type" => 4
     ]]) : "";
     $r = $this->system->Change([[
      "[Product.Actions]" => $actions,
      "[Product.Back]" => $bck,
      "[Product.Body]" => $this->system->PlainText([
       "Data" => $product["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[Product.Brief.Category]" => $this->system->Element([
       "p", $this->system->ProductCategory($product["Category"]),
       ["class" => "CenterText"]
      ]),
      "[Product.Brief.Description]" => $product["Description"],
      "[Product.Brief.Icon]" => "{product_category}",
      "[Product.Brief.Options]" => $this->view(base64_encode("Cart:Add"), [
       "Data" => [
        "ID" => $product["ID"],
        "T" => $t["Login"]["Username"]
       ]
      ]),
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
      "[Product.Modified]" => $modified,
      "[Product.Reactions]" => $reactions,
      "[Product.Title]" => $product["Title"],
      "[Product.Share]" => base64_encode("v=".base64_encode("Product:Share")."&ID=".base64_encode($product["ID"])."&UN=".$data["UN"])
     ], $this->system->Page("96a6768e7f03ab4c68c7532be93dee40")]);
    }
   }
   $r = ($data["CARD"] == 1) ? $this->system->Card(["Front" => $r]) : $r;
   return $r;
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "Title", "new"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Product Identifier is missing."
    ]),
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
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Product <em>$title</em> has already been taken. Please choose a different one."
      ]),
      "Header" => "Error"
     ]);
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
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Product <em>$title</em> has been successfully $actionTaken!"
      ]),
      "Header" => "Done"
     ]);
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
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Product Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $shop = $this->system->Data("Get", ["shop", md5($you)]) ?? [];
    $newProducts = [];
    $products = $shop["Products"] ?? [];
    foreach($products as $key => $value) {
     if($id != $value) {
      array_push($newProducts, $value);
     }
    }
    $shop["Products"] = $newProducts;
    /*$this->view(base64_encode("Conversation:SaveDelete"), [
     "Data" => ["ID" => $id]
    ]);
    $this->system->Data("Purge", ["miny", $id]);
    $this->system->Data("Purge", ["local", $id]);
    $this->system->Data("Purge", ["react", $id]);
    $this->system->Data("Save", ["shop", md5($you), $shop]);*/
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The Product was deleted.".json_encode($shop, true)]),
     "Header" => "Done"
    ]);
   }
   return $this->system->JSONResponse([$ec, $r]);
  }
  function Share(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $ec = "Denied";
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($username)) {
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
   }
   return $this->system->Card(["Front" => $r]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
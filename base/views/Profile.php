<?php
 Class Profile extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function AddContent(): string {
   $_View = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID != $you) {
    $_IsArtist = $y["Subscriptions"]["Artist"]["A"] ?? 0;
    $_IsVIP = $y["Subscriptions"]["VIP"]["A"] ?? 0;
    $_IsSubscribed = (($_IsArtist + $_IsVIP) > 0) ? 1 : 0;
    $_View = $this->core->Element([
     "h1", "Create Something New!", ["class" => "CenterText UpperCase"]
    ]).$this->core->Element([
     "p", "Your central hub of content creation.", ["class" => "CenterText"]
    ]).$this->core->Element(["button", "Album", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Album:Edit")."&new=1")
    ]]).$this->core->Element(["button", "Article", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Page:Edit")."&new=1")
    ]]);
    $_View .= ($_IsSubscribed == 1) ? $this->core->Element(["button", "Blog", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Blog:Edit")."&Member=".base64_encode($you)."&new=1")
    ]]).$this->core->Element(["button", "Forum", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Forum:Edit")."&new=1")
    ]]) : "";
    $_View .= $this->core->Element(["button", "Group Chat", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Chat:Edit")."&GenerateID=1&Username=".base64_encode($you))
    ]]).$this->core->Element(["button", "Link", [
     "class" => "LI GoToView",
     "data-type" => "ContentHub;".$this->core->AESencrypt("v=".base64_encode("Search:Links"))
    ]]).$this->core->Element(["button", "Media", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("File:Upload")."&AID=".md5("unsorted"))
    ]]).$this->core->Element(["button", "Poll", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Poll:Create"))
    ]]);
    $_View .= ($_IsArtist == 1) ? $this->core->Element(["button", "Product or Service", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Product:Edit")."&Card=1")
    ]]) : "";
    $_View .= $this->core->Element(["button", "Status Update", [
     "class" => "LI OpenCard",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("StatusUpdate:Edit")."&new=1")
    ]]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "div", $_View, [
       "class" => "ParentPageContentHub"
      ]
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $_View
   ]);
  }
  function AddContentCheck(): string {
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $_View = ($this->core->ID != $you) ? [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element(["button", NULL, [
      "class" => "AddContent OpenFirSTEPTool",
      "data-fst" => $this->core->AESencrypt("v=".base64_encode("Profile:AddContent"))
     ]]))
    ] : "";
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $_View
   ]);
  }
  function BlacklistCategories(): string {
   $_View = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $blacklists = $y["Blocked"] ?? [];
   foreach($blacklists as $list => $info) {
    $_View .= $this->core->Element(["button", $list, [
     "class" => "GoToView LI v2 v2w",
     "data-encryption" => "AES",
     "data-type" => "Blacklists;".$this->core->AESencrypt("v=".base64_encode("Search:Containers")."&st=BL&BL=".base64_encode($list))
    ]]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function Blacklists(): string {
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $id = md5($you);
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => [
     [
      "Name" => "UpdateContentAES",
      [
       ".Blacklists$id",
       $this->core->AESencrypt("v=".base64_encode("Profile:BlacklistCategories"))
      ]
     ]
    ],
    "View" => [
     "ChangeData" => [
      "[Blacklists.ID]" => $id
     ],
     "ExtensionID" => "03d53918c3da9fbc174f94710182a8f2"
    ]
   ]);
  }
  function BulletinCenter(): string {
   $search = base64_encode("Search:Containers");
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => [
     [
      "Name" => "UpdateContentAES",
      [
       ".BulletinCenterBulletinsList",
       $this->core->AESencrypt("v=$search&st=Bulletins")
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      [
       ".BulletinCenterContactsList",
       $this->core->AESencrypt("v=$search&Chat=0&st=ContactsRequests")
      ]
     ]
    ],
    "View" => [
     "ChangeData" => [],
     "ExtensionID" => "6cbe240071d79ac32edbe98679fcad39"
    ]
   ]);
  }
  function BulletinMessage(array $data): string {
   $data = $data["Data"] ?? [];
   $type = $data["Type"] ?? "";
   $message = "Message required for Bulletin type <em>$type</em>.";
   $request = $data["Data"]["Request"] ?? "";
   if($type == "ArticleUpdate") {
    $message = "Updated their article.";
   } elseif($type == "BlogUpdate") {
    $message = "Updated their blog.";
   } elseif($type == "BlogPostUpdate") {
    $message = "Updated their blog post.";
   } elseif($type == "ContactRequest") {
    $message = "Sent you a contact request.";
    $message = ($request == "Accepted") ? "Accepted your contact request." : $message;
   } elseif($type == "InviteToArticle") {
    $message = "Invited you to contribute to their Article.";
   } elseif($type == "InviteToBlog") {
    $message = "Invited you to contribute to their Blog.";
   } elseif($type == "InviteToForum") {
    $message = "Invited you to their Forum.";
   } elseif($type == "Invoice") {
    $message = "Sent you an invoice. Click or tap below to view the invoice or make any necessary payments.";
   } elseif($type == "InvoiceForward") {
    $message = "Forwarded an Invoice to you.";
   } elseif($type == "InvoiceUpdate") {
    $message = "Updated your Invoice.";
   } elseif($type == "NewArticle") {
    $message = "Wrote a new Artcile.";
   } elseif($type == "NewBlogPost") {
    $message = "Posted to their blog.";
   } elseif($type == "NewJob") {
    $message = "Requested a Service.";
   } elseif($type == "NewPoll") {
    $message = "Created a new Poll.";
   } elseif($type == "NewMessage") {
    $message = "Sent you a message.";
   } elseif($type == "NewProduct") {
    $message = "Added a product to their shop.";
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $message
   ]);
  }
  function BulletinOptions(array $data): string {
   $_View = "";
   $data = $data["Data"] ?? [];
   $bulletin = $data["Bulletin"] ?? "";
   $bulletin = (!empty($bulletin)) ? base64_decode($bulletin) : [];
   $bulletin = json_decode($bulletin, true);
   $id = $bulletin["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($bulletin["Read"] == 0) {
    $data = $bulletin["Data"] ?? [];
    $mar = "v=".base64_encode("Profile:MarkBulletinAsRead")."&ID=$id";
    if($bulletin["Type"] == "ArticleUpdate") {
     $article = $this->core->Data("Get", ["pg", $data["ArticleID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$article["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-encryption" => "AES",
       "data-target" => ".Bulletin$id .Options",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Page:Home")."&CARD=1&ID=".$data["ArticleID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "BlogUpdate") {
     $blog = $this->core->Data("Get", ["blg", $data["BlogID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$blog["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-encryption" => "AES",
       "data-target" => ".Bulletin$id .Options",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Blog:Home")."&CARD=1&ID=".$data["ArticleID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "BlogPostUpdate") {
     $post = $this->core->Data("Get", ["bp", $data["PostID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$post["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-encryption" => "AES",
       "data-target" => ".Bulletin$id .Options",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("BlogPost:Home")."&CARD=1&ID=".$data["ArticleID"])
      ]
     ]);
    } if($bulletin["Type"] == "ContactRequest") {
     $contactStatus = $this->view(base64_encode("Contact:Status"), [
      "Them" => $bulletin["Data"]["From"],
      "You" => $y["Login"]["Username"]
     ]);
     $contactStatus = $this->core->RenderView($contactStatus);
     $true = $this->core->PlainText([
      "Data" => 1,
      "Encode" => 1
     ]);
     if($contactStatus["TheyRequested"] > 0) {
      $_View = "v=".base64_encode("Contact:Requests");
      $accept = $_View."&accept=$true&bulletin=$true";
      $decline = $_View."&decline=$true&bulletin=$true";
      $_View = "<input name=\"Username\" type=\"hidden\" value=\"".$data["From"]."\"/>\r\n";
      $_View .= $this->core->Element(["div", $this->core->Element([
       "button", "Accept", [
        "class" => "BBB Close MarkAsRead SendData v2 v2w",
        "data-MAR" => base64_encode($mar),
        "data-encryption" => "AES",
        "data-form" => ".Bulletin$id .Options",
        "data-processor" => base64_encode($accept),
        "data-target" => ".Bulletin$id .Options"
       ]]), ["class" => "Desktop50"]
      ]).$this->core->Element(["div", $this->core->Element([
       "button", "Decline", [
        "class" => "Close MarkAsRead SendData v2 v2w",
        "data-MAR" => base64_encode($mar),
        "data-encryption" => "AES",
        "data-form" => ".Bulletin$id .Options",
        "data-processor" => base64_encode($decline),
        "data-target" => ".Bulletin$id .Options"
       ]]), ["class" => "Desktop50"]
      ]);
     }
    } elseif($bulletin["Type"] == "InviteToArticle") {
     $article = $this->core->Data("Get", ["pg", $data["ArticleID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$article["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Page:Home")."&CARD=1&ID=".$article["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToBlog") {
     $blog = $this->core->Data("Get", ["blg", $data["BlogID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$blog["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Blog:Home")."&CARD=1&ID=".$blog["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToForum") {
     $forum = $this->core->Data("Get", ["pf", $data["ForumID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$forum["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Forum:Home")."&CARD=1&ID=".$forum["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToShop") {
     $shop = $this->core->Data("Get", ["shop", $data["ShopID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$shop["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Shop:Home")."&CARD=1&ID=".$data["ShopID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "Invoice" || $bulletin["Type"] == "NewJob") {
     $shop = $this->core->Data("Get", ["shop", $data["Shop"]]);
     $_View = $this->core->Element([
      "button", "View Invoice", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Invoice:Home")."&Card=1&ID=".$data["Invoice"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InvoiceForward") {
     $shop = $this->core->Data("Get", ["shop", $data["Shop"]]);
     $_View = $this->core->Element([
      "button", "View Forwarded Invoice", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Invoice:Home")."&Card=1&ID=".$data["Invoice"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InvoiceUpdate") {
     $shop = $this->core->Data("Get", ["shop", $data["Shop"]]);
     $_View = $this->core->Element([
      "button", "View Updated Invoice", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Invoice:Home")."&Card=1&ID=".$data["Invoice"])
      ]
     ]);
    } elseif($bulletin["Type"] == "NewArticle") {
     $article = $this->core->Data("Get", ["pg", $data["ArticleID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$article["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-encryption" => "AES",
       "data-target" => ".Bulletin$id .Options",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Page:Home")."&CARD=1&ID=".$data["ArticleID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "NewBlogPost") {
     $post = $this->core->Data("Get", ["bp", $data["PostID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$post["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-encryption" => "AES",
       "data-target" => ".Bulletin$id .Options",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("BlogPost:Home")."&CARD=1&Blog=".$data["BlogID"]."&Post=".$data["PostID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "NewMessage") {
     $_View = $this->core->Element([
      "button", "Chat with <em>".$data["From"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Chat:Home")."&1on1=1&Card=1&Username=".base64_encode($data["From"]))
      ]
     ]);
    } elseif($bulletin["Type"] == "NewPoll") {
     $poll = $this->core->Data("Get", ["poll", $data["PollID"]]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$poll["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-encryption" => "AES",
       "data-target" => ".Bulletin$id .Options",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Poll:Home")."&ID=".base64_encode($data["PollID"]))
      ]
     ]);
    } elseif($bulletin["Type"] == "NewProduct") {
     $product = $this->core->Data("Get", [
      "miny",
      $data["ProductID"]
     ]);
     $_View = $this->core->Element([
      "button", "Take me to <em>".$product["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-encryption" => "AES",
       "data-target" => ".Bulletin$id .Options",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Product:Home")."&CARD=1&ID=".$product["ID"]."&UN=".$data["ShopID"])
      ]
     ]);
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $_View
   ]);
  }
  function Bulletins(array $data): string {
   $count = 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $minimalDesign = $y["Personal"]["MinimalDesign"] ?? 0;
   if($minimalDesign == 0 && $this->core->ID != $you) {
    $bulletins = $this->core->Data("Get", ["bulletins", md5($you)]);
    $newBulletins = [];
    if(!empty($bulletins)) {
     foreach($bulletins as $key => $value) {
      if($key != "Purge") {
       $seen = $value["Seen"] ?? 0;
       if($seen == 0) {
        $count++;
        $bulletin = $bulletins[$key] ?? [];
        $bulletin["Seen"] = 1;
        $newBulletins[$key] = $bulletin;
       }
      }
     } if($bulletins != $newBulletins) {
      $this->core->Data("Save", ["bulletins", md5($you), $newBulletins]);
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $count
   ]);
  }
  function ChangeRank(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Member Identifier or Rank are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $pin = $data["PIN"] ?? "";
   $rank = $data["Rank"] ?? md5("Member");
   $_ResponseType = "Dialog";
   $username = $data["Username"] ?? "";
   $y = $this->you;
   if(md5($pin) != $y["Login"]["PIN"]) {
    $_Dialog = [
     "Body" => $this->core->Element(["p", "The PINs do not match."]),
    ];
   } elseif(!empty($rank) && !empty($username)) {
    $_AccessCode = "Accepted";
    $_ResponseType = "ReplaceContent";
    $member = $this->core->Member($username);
    $member["Rank"] = md5($rank);
    $this->core->Data("Save", ["mbr", md5($username), $member]);
    $_View = [
     "ChangeData" => [
      "[Member.DisplayName]" => $member["Personal"]["DisplayName"]
     ],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "h3", "Success", [
       "class" => "CenterText UpperCase"
      ]
     ]).$this->core->Element([
      "p", "[Member.DisplayName]'s Rank within <em>[App.Name]</em> was Changed to $rank.",
      [
       "class" => "CenterText"
      ]
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "Success" => "CloseDialog",
    "View" => $_View
   ]);
  }
  function Deactivate(): string {
   $_Dialog = "";
   $_View = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } else {
    $y["Inactive"] = 1;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $_View = $this->view(base64_encode("WebUI:Gateway"), []);
    $_View = $this->core->Element([
     "div", $this->core->Element([
      "p", "Your profile is now inactive and you can sign in at any time to activate it, we hope to see you again soon!"
     ]), ["class" => "FrostedBright RoundedLarge Shadowed"]
    ]).$this->core->RenderView($_View, 1)["View"];
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Donate(array $data): string {
   $data = $data["Data"] ?? [];
   $options = "";
   $username = $data["UN"] ?? "";
   $t = $this->core->Member(base64_decode($username));
   $displayName = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
   $donations = $t["Donations"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(empty($donations)) {
    if($t["Login"]["Username"] == $you) {
     $message = "You have not set up Donations yet.";
    } else {
     $message = "$displayName has not set up Donations yet.";
    }
    $options .= $this->core->Element(["p", $message]);
   } else {
    $options .= (!empty($donations["Patreon"])) ? $this->core->Element([
     "button", "Donate via Patreon", [
      "class" => "LI",
      "onclick" => "W('https://patreon.com/".$donations["Patreon"]."', '_blank');"
     ]
    ]) : "";
    $options .= (!empty($donations["PayPal"])) ? $this->core->Element([
     "button", "Donate via PayPal", [
      "class" => "LI",
      "onclick" => "W('https://paypal.me/".$donations["PayPal"]."/5', '_blank');"
     ]
    ]) : "";
    $options .= (!empty($donations["SubscribeStar"])) ? $this->core->Element([
     "button", "Donate via SubscribeStar", [
      "class" => "LI",
      "onclick" => "W('https://subscribestar.com/".$donations["SubscribeStar"]."', '_blank');"
     ]
    ]) : "";
   }
   return $this->core->JSONResponse([
    "Dialog" => [
     "Body" => $this->core->Element(["div", $options, ["class" => "scr"]]),
    ]
   ]);
  }
  function Home(array $data): string {
   $_AddTopMargin = "0";
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The requested Member could not be found.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $_ViewTitle = $this->core->config["App"]["Name"];
   $data = $data["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $member = $data["UN"] ?? "";
   $parentPage = $data["lPG"] ?? "";
   $b2 = $data["b2"] ?? "";
   $back = $data["back"] ?? 0;
   $back = ($back == 1) ? $this->core->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI head",
    "data-type" => $parentPage
   ]]) : "";
   $card = $data["Card"] ?? 0;
   $chat = $data["Chat"] ?? 0;
   $pub = $data["pub"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $bl = $this->core->CheckBlocked([$y, "Members", $member]);
   $_Member = $this->core->GetContentData([
    "Blacklisted" => $bl,
    "ID" => base64_encode("Member;".md5(base64_decode($member)))
   ]);
   $member = $_Member["DataModel"];
   if(strpos(base64_decode($data["UN"]), "Ghost_")) {
    $_Dialog = [
     "Body" => "You cannot talk to ghosts."
    ];
   } elseif($_Member["Empty"] == 0) {
    $_Dialog = [
     "Body" => "The Member may have reduced their visibility.",
     "Header" => "Not Found"
    ];
    $id = $member["Login"]["Username"];
    $_TheirContacts = $this->core->Data("Get", ["cms", md5($id)]);
    $_TheyBlockedYou = $this->core->CheckBlocked([$_Member["DataModel"], "Members", $you]);
    $_YouBlockedThem = $this->core->CheckBlocked([$y, "Members", $id]);
    $displayName = $_Member["ListItem"]["Title"];
    $b2 = ($id == $you) ? "Your Profile" : "$displayName's Profile";
    $lpg = "Profile".md5($id);
    $privacy = $member["Privacy"] ?? [];
    $subscriptions = $member["Subscriptions"] ?? [];
    $check = ($id == $you) ? 1 : 0;
    $check2 = ($privacy["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->config["minAge"])) ? 1 : 0;
    $checkart = 0;
    $public = md5("Public");
    $search = base64_encode("Search:Containers");
    $theirContacts = $_TheirContacts["Contacts"] ?? [];
    $theirRequests = $_TheirContacts["Requests"] ?? [];
    $visible = $this->core->CheckPrivacy([
     "Contacts" => $theirContacts,
     "Privacy" => $privacy["Profile"],
     "UN" => $id,
     "Y" => $you
    ]);
    if($_TheyBlockedYou == 0 && $_YouBlockedThem == 0 && ($check == 1 || $check2 == 1 || $visible == 1)) {
     $_IsArtist = $subscriptions["Artist"]["A"] ?? 0;
     $_IsVIP = $subscriptions["VIP"]["A"] ?? 0;
     $_IsSubscribed = (($_IsArtist + $_IsVIP) > 0) ? 1 : 0;
     $_ViewTitle = "$displayName @ ".$_ViewTitle;
     $_AccessCode = "Accepted";
     $_AddTopMargin = "1";
     $passPhrase = $member["Privacy"]["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you by <em>$displayName</em> to access their Profile."),
       "ViewData" => base64_encode(json_encode([
        "AddTo" => $addTo,
        "SecureKey" => base64_encode($passPhrase),
        "UN" => base64_encode($id),
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Profile:Home")
       ], true))
      ]]);
      $_View = $this->core->RenderView($_View);
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = [
       "Body" => "The Key is missing."
      ];
      $_AddTopMargin = "0";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $_Dialog = "";
      } else {
       $_View = $this->view(base64_encode("Profile:Home"), ["Data" => [
        "AddTo" => $addTo,
        "UN" => base64_encode($id),
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $blockCommand = ($_YouBlockedThem == 0) ? "Block" : "Unblock";
      $memberID = md5($id);
      $coverPhotos = $member["Personal"]["CoverPhotos"] ?? [];
      $newCoverPhotos = [];
      foreach($coverPhotos as $key => $image) {
       $newCoverPhotos[$key] = $this->core->CoverPhoto($image);
      }
      $_Commands = [
       [
        "Name" => "RefreshCoverPhoto",
        "Parameters" => [
         ".MemberCP$memberID",
         $newCoverPhotos,
         $coverPhotosSlideShowDisabled
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Conversation$memberID",
         $this->core->AESencrypt("v=".base64_encode("Conversation:Home")."&CRID=".base64_encode($memberID)."&LVL=".base64_encode(1))
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Nominate$memberID",
         $this->core->AESencrypt("v=".base64_encode("Congress:Nominate")."&Username=".base64_encode($id))
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Stream$memberID",
         $this->core->AESencrypt("v=$search&UN=".base64_encode($id)."&st=MBR-SU")
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Vote$memberID",
         $options["Vote"]
        ]
       ]
      ];
      $_Dialog = "";
      $actions = ($chat == 0) ? $this->core->Element(["button", "Chat", [
       "class" => "OpenCard Small v2",
       "data-encryption" => "AES",
       "data-view" => $options["Chat"]
      ]]) : "";
      $actions .= ($_IsArtist == 1) ? $this->core->Element(["button", "Donate", [
       "class" => "OpenCardSmall Small v2",
       "data-encryption" => "AES",
       "data-view" => $options["Donate"]
      ]]) : "";
      $actions .= ($_IsVIP == 0 && $y["Rank"] == md5("High Command")) ? $this->core->Element(["button", "Make VIP", [
       "class" => "OpenDialog Small v2",
       "data-encryption" => "AES",
       "data-processor" => $this->core->AESencrypt("v=".base64_encode("Profile:MakeVIP")."&ID=".base64_encode($id))
      ]]) : "";
      $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
      $addTo = (!empty($addToData) && $id != $this->core->ID) ? $this->core->Element([
       "button", "Attach", [
        "class" => "Attach Small v2",
        "data-input" => base64_encode($addToData[1]),
        "data-media" => base64_encode("Member;".md5($id)),
       ]
      ]) : "";
      $actions = ($id != $you) ? $addTo.$actions : $addTo;
      $addContact = "";
      $albums = $this->core->Change([[
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their media albums to themselves."
      ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-680be0e87756d")]);
      if($check == 1 || $privacy["Albums"] == $public || $visible == 1) {
       $albums = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "st" => "MBR-ALB"
       ]]);
       $albums = $this->core->RenderView($albums);
      }
      $articles = $this->core->Change([[
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their archive contributions to themselves."
      ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-680be0e87756d")]);
      if($check == 1 || $privacy["Archive"] == $public || $visible == 1) {
       $articles = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "b2" => $b2,
        "lPG" => $lpg,
        "st" => "MBR-CA"
       ]]);
       $articles = $this->core->RenderView($articles);
      }
      $blogs = $this->core->Change([[
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their blogs to themselves."
      ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-680be0e87756d")]);
      if($check == 1 || $privacy["Posts"] == $public || $visible == 1) {
       $blogs = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "b2" => $b2,
        "lPG" => $lpg,
        "st" => "MBR-BLG"
       ]]);
       $blogs = $this->core->RenderView($blogs);
      }
      $changeRank = "";
      $contacts = $this->core->Change([[
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their contacts to themselves.",
       "Header" => "Forbidden"
      ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-680be0e87756d")]);
      if($check == 1 || $privacy["Contacts"] == $public || $visible == 1) {
       $_Dialog = "";
       $contacts = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "b2" => $b2,
        "lPG" => $lpg,
        "st" => "ContactsProfileList"
       ]]);
       $contacts = $this->core->RenderView($contacts);
      }
      $contactRequestsAllowed = $this->core->CheckPrivacy([
       "Contacts" => $theirContacts,
       "Privacy" => $member["Privacy"]["ContactRequests"],
       "UN" => $id,
       "Y" => $you
      ]);
      $contactStatus = $this->view(base64_encode("Contact:Status"), [
       "Them" => $id,
       "You" => $you
      ]);
      $contactStatus = $this->core->RenderView($contactStatus);
      if($contactRequestsAllowed == 1 && $id != $you) {
       $cancel = (in_array($you, $theirRequests)) ? 1 : 0;
       if($contactStatus["TheyHaveYou"] == 0 && $contactStatus["YouHaveThem"] == 0) {
        if($contactStatus["TheyRequested"] > 0) {
         $changeData = [
          "[ContactRequest.Header]" => "Pending Request",
          "[ContactRequest.ID]" => $memberID,
          "[ContactRequest.Option]" => $this->core->Element([
           "div", $this->core->Element(["button", "Accept", [
            "class" => "BBB SendData v2 v2w",
            "data-encryption" => "AES",
            "data-form" => ".ContactRequest$memberID",
            "data-processor" => $this->core->AESencrypt("v=".base64_encode("Contact:Requests")."&accept=1")
           ]]), ["class" => "Desktop50"]
          ]).$this->core->Element([
           "div", $this->core->Element(["button", "Decline", [
            "class" => "BB SendData v2 v2w",
            "data-encryption" => "AES",
            "data-form" => ".ContactRequest$id",
            "data-processor" => $this->core->AESencrypt("v=".base64_encode("Contact:Requests")."&decline=1")
           ]]), ["class" => "Desktop50"]
          ]),
          "[ContactRequest.Text]" => "$display sent you a contact request.",
          "[ContactRequest.Username]" => $id
         ];
        } elseif($cancel == 1 || $contactStatus["YouRequested"] > 0) {
         $changeData = [
          "[ContactRequest.Header]" => "Cancel Request",
          "[ContactRequest.ID]" => $id,
          "[ContactRequest.Option]" => $this->core->Element([
           "button", "Cancel Request", [
            "class" => "BB SendData v2 v2w",
            "data-encryption" => "AES",
            "data-form" => ".ContactRequest$id",
            "data-processor" => $this->core->AESencrypt("v=".base64_encode("Contact:Requests"))
           ]
          ]),
          "[ContactRequest.Text]" => "Cancel the contact request you snet to $display.",
          "[ContactRequest.Username]" => $id
         ];
        } else {
         $changeData = [
          "[ContactRequest.Header]" => "Add $displayName",
          "[ContactRequest.ID]" => $id,
          "[ContactRequest.Option]" => $this->core->Element([
           "button", "Add $displayName", [
            "class" => "BB SendData v2 v2w",
            "data-encryption" => "AES",
            "data-form" => ".ContactRequest$id",
            "data-processor" => $this->core->AESencrypt("v=".base64_encode("Contact:Requests"))
           ]
          ]),
          "[ContactRequest.Text]" => "Send $displayName a Contact Request.",
          "[ContactRequest.Username]" => $id
         ];
        }
        $addContact = $this->core->Change([
         $changeData,
         $this->core->Extension("a73ffa3f28267098851bf3550eaa9a02")
        ]);
       }
       $addContact = ($id != $this->core->ID && $this->core->ID != $you) ? $addContact : "";
      } if($id != $you && $y["Rank"] == md5("High Command") || $y["Rank"] == md5("Support")) {
       if($id != $this->core->ID && $id != $this->core->ShopID) {
        if($y["Rank"] == md5("High Command")) {
         $ranks = [
          "High Command" => "High Command",
          "Member" => "Member",
          "Support" => "Support"
         ];
        } elseif($y["Rank"] == md5("Support")) {
         $ranks = [
          "Member" => "Member",
          "Support" => "Support"
         ];
        }array_push($_Commands, [
         "Name" => "RenderInputs",
         "Parameters" => [
          "..ChangeRank$memberID",
          [
           [
            "Attributes" => [
             "class" => "AuthPIN$memberID",
             "name" => "PIN",
             "type" => "hidden"
            ],
            "Options" => [],
            "Type" => "Text",
            "Value" => ""
           ],
           [
            "Attributes" => [
             "name" => "Username",
             "type" => "hidden"
            ],
            "Options" => [],
            "Type" => "Text",
            "Value" => $id
           ],
           [
            "Attributes" => [],
            "OptionGroup" => $ranks,
            "Options" => [
             "Header" => 1,
             "HeaderText" => "Rank"
            ],
            "Name" => "Rank",
            "Title" => "Rank",
            "Type" => "Select",
            "Value" => $member["Rank"]
           ]
          ]
         ]
        ]);
        $changeRank = $this->core->Change([[
         "[Ranks.Authentication]" => $this->core->AESencrypt("v=".base64_encode("Profile:ChangeRank")),
         "[Ranks.DisplayName]" => $displayName,
         "[Ranks.ID]" => $memberID
        ], $this->core->Extension("914dd9428c38eecf503e3a5dda861559")]);
       }
      }
      $coverPhotosSlideShowDisabled = $member["Personal"]["CoverPhotoSelection"] ?? "Single";
      $coverPhotosSlideShowDisabled = ($coverPhotosSlideShowDisabled == "Multiple") ? "false" : "true";
      $embeddedView = $data["EmbeddedView"] ?? 0;
      $gender = $member["Personal"]["Gender"] ?? "Male";
      $gender = $this->core->Gender($gender);
      $journal = $this->core->Change([[
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their Journal to themselves."
      ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-680be0e87756d")]);
      if($check == 1 || $privacy["Journal"] == $public || $visible == 1) {
       $journal = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "b2" => $b2,
        "lPG" => $lpg,
        "st" => "MBR-JE"
       ]]);
       $journal = $this->core->RenderView($journal);
      }
      $options = $_Member["ListItem"]["Options"];
      $share = ($id == $you || $privacy["Profile"] == $public) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element([
       "button", "Share", [
        "class" => "OpenCard Small v2",
        "data-encryption" => "AES",
        "data-view" => $options["Share"]
       ]
      ]) : "";
      $verified = $member["Verified"] ?? 0;
      $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
      $_View = [
       "ChangeData" => [
        "[Member.Actions]" => $actions,
        "[Member.AddContact]" => $addContact,
        "[Member.Albums]" => $albums,
        "[Member.Articles]" => $articles,
        "[Member.Block]" => $options["Block"],
        "[Member.Block.Text]" => $blockCommand,
        "[Member.Blogs]" => $blogs,
        "[Member.Back]" => $back,
        "[Member.ChangeRank]" => $changeRank,
        "[Member.CoverPhoto]" => $_Member["ListItem"]["CoverPhoto"],
        "[Member.Contacts]" => $contacts,
        "[Member.Description]" => $_Member["ListItem"]["Description"],
        "[Member.DisplayName]" => $displayName.$verified,
        "[Member.ID]" => $memberID,
        "[Member.Journal]" => $journal,
        "[Member.ProfilePicture]" => $options["ProfilePicture"],
        "[Member.Share]" => $share,
        "[Member.Username]" => $id
       ],
       "ExtensionID" => "72f902ad0530ad7ed5431dac7c5f9576"
      ];
     }
     $_Card = ($card == 1) ? [
      "Front" => $_View
     ] : "";
     $_View = ($card == 0) ? $_View : "";
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "Title" => $_ViewTitle,
    "View" => $_View
   ]);
  }
  function MakeVIP(array $data): string {
   $_Dialog = [
    "Body" => "The Member Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $manifest = [];
   $y = $this->you;
   if(!empty($id)) {
    $_Dialog = [
     "Body" => "$displayName is already a VIP Member."
    ];
    $t = base64_decode($id);
    $t = ($t == $y["Login"]["Username"]) ? $y : $this->core->Member($t);
    $displayName = $t["Personal"]["DisplayName"];
    if($t["Subscriptions"]["VIP"]["A"] == 0) {
     $_VIPForum = "cb3e432f76b38eaa66c7269d658bd7ea";
     $t["Points"] = $t["Points"] + 1000000;
     $manifest = $this->core->Data("Get", ["pfmanifest", $_VIPForum]);
     array_push($manifest, [$t["Login"]["Username"] => "Member"]);
     foreach($t["Subscriptions"] as $subscription => $info) {
      if(!in_array($subscription, ["Artist", "Developer"])) {
       $t["Subscriptions"][$subscription] = [
        "A" => 1,
        "B" => $this->core->timestamp,
        "E" => $this->core->TimePlus($this->core->timestamp, 1, "month")
       ];
      }
     }
     $this->core->Data("Save", ["pfmanifest", $_VIPForum, $manifest]);
     $this->core->Data("Save", ["mbr", md5($t["Login"]["Username"]), $t]);
     $_Dialog = [
      "Body" => "$displayName is now a VIP Member.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function MarkBulletinAsRead(array $data): string {
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $bulletins = $this->core->Data("Get", ["bulletins", md5($you)]);
   if(!empty($id)) {
    foreach($bulletins as $key => $info) {
     if($key == $id) {
      $bulletin = $info;
      $bulletin["Read"] = 1;
      $bulletins[$key] = $bulletin;
     }
    }
   }
   $this->core->Data("Save", [
    "bulletins",
    md5($you),
    $bulletins
   ]);
   return json_encode($bulletins);
  }
  function NewPassword(array $data): string {
   $_Card = "";
   $_Dialog = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } else {
    $_Card = [
     "Front" => [
      "ChangeData" => [
       "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
       "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
       "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePassword")),
       "[Member.Username]" => $y["Login"]["Username"]
      ],
      "ExtensionID" => "08302aec8e47d816ea0b3f80ad87503c"
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function NewPIN(array $data): string {
   $_Card = "";
   $_Dialog = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } else {
    $_Card = [
     "Front" => [
      "ChangeData" => [
       "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
       "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
       "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePIN"))
      ],
      "ExtensionID" => "867bd8480f46eea8cc3d2a2ed66590b7"
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function Preferences(array $data): string {
   $_AddTopMargin = "1";
   $_Commands = "";
   $_Dialog = "";
   $_View = "";
   $data = $data["Data"] ?? [];
   $minAge = $this->core->config["minRegAge"] ?? 13;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($y["Personal"]["Age"] < $minAge) {
    $_Dialog = [
     "Body" => "As a security measure, you must be aged $minAge or older in order to take full control of your profile and absolve yourself of your parent account.",
     "Header" => "Not of Age"
    ];
   } else {
    $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
     "Header" => base64_encode($this->core->Element([
      "h1", "Preferences", ["class" => "CenterText"]
     ])),
     "Text" => base64_encode("Please enter your PIN to access your Prefernces."),
     "ViewData" => base64_encode(json_encode([
      "SecureKey" => base64_encode($y["Login"]["PIN"]),
      "VerifyPassPhrase" => 1,
      "v" => base64_encode("Profile:Preferences")
     ], true))
    ]]);
    $_View = $this->core->RenderView($_View);
    $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
    if($verifyPassPhrase == 1) {
     $_Dialog = "";
     $_View = "";
     $key = $data["Key"] ?? base64_encode("");
     $key = base64_decode($key);
     $secureKey = $data["SecureKey"] ?? base64_encode("");
     $secureKey = base64_decode($secureKey);
     if(md5($key) == $secureKey) {
      $_AddTopMargin = "0";
      $_LiveView = base64_encode("v=".base64_encode("LiveView:Editor")."&MediaType=".base64_encode("CoverPhoto")."&Media=");
      $_SymbolicLink = "v=".base64_encode("Search:Containers")."&AddTo=".base64_encode("Attach:.AddTo[Clone.ID]")."&CARD=1&lPG=Files&st=XFS&UN=".base64_encode($you)."&ftype=".base64_encode(json_encode(["Photo"]));
      $id = md5($you);
      $autoResponse = $y["Personal"]["AutoResponse"] ?? "";
      $birthMonths = [];
      $birthYears = [];
      $chooseElectable = $y["Personal"]["Electable"] ?? 0;
      $chooseMinimalDesign = $y["Personal"]["MinimalDesign"] ?? "";
      $chooseMinimalDesign = (!empty($chooseMinimalDesign)) ? 1 : 0;
      $coverPhotos = $y["Personal"]["CoverPhotos"] ?? [];
      $coverPhotosList = "";
      $coverPhotosSelection = $y["Personal"]["CoverPhotoSelection"] ?? "Single";
      $lastPasswordChange = $y["Activity"]["LastPasswordChange"] ?? $this->core->timestamp;
      $passPhrase = $y["Privacy"]["PassPhrase"] ?? "";
      $passwordOnSignIn = $y["Login"]["RequirePassword"] ?? "No";
      $polls = $y["Privacy"]["Posts"] ?? md5("Public");
      $relationshipWith = $y["Personal"]["RelationshipWith"] ?? "";
      $setUIVariant = $y["Personal"]["UIVariant"] ?? 0;
      for($i = 1; $i <= 12; $i++) {
       $birthMonths[$i] = $i;
      } for($i = 1776; $i <= date("Y"); $i++) {
       $birthYears[$i] = $i;
      } foreach($coverPhotos as $key => $image) {
       $cloneID = $this->core->UUID("CoverPhotos::$you::$key");
       $coverPhotosList .= $this->core->Element([
        "div", $this->core->Element(["button", "X", [
         "class" => "Delete v1",
         "data-target" => ".CoverPhotos$cloneID"
        ]]).$this->core->Element([
         "div", $this->core->Change([[
          "[Clone.ID]" => $key,
          "[Media.Add]" => base64_encode("v=".base64_encode("Search:Containers")."&lPG=Files&st=XFS&AddTo=".base64_encode("Attach:.AddTo$key")."&UN=".base64_encode($you)."&ftype=".base64_encode(json_encode(["Photo"]))),
          "[Media.File]" => $image,
          "[Media.ID]" => $cloneID,
          "[Media.Input]" => "CoverPhotos[]",
          "[Media.Input.LiveView]" => $_LiveView,
          "[Media.Name]" => "Cover Photo"
         ], $this->core->Extension("02ec63fe4f0fffe5e6f17621eb3b50ad")]), [
          "class" => "NONAME"
         ]
        ]), [
         "class" => "CoverPhotos$cloneID Frosted Rounded"
        ]
       ]);
      }
      $_Commands = [
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".MemberInformation$id",
          [
           [
            "Attributes" => [
             "class" => "req",
             "name" => "name",
             "placeholder" => "John",
             "type" => "text"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "First Name"
            ],
            "Type" => "Text",
            "Value" => $this->core->AESencrypt($y["Personal"]["FirstName"])
           ],
           [
            "Attributes" => [
             "class" => "req",
             "name" => "Personal_DisplayName",
             "placeholder" => "John Doe",
             "type" => "text"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "Display Name"
            ],
            "Type" => "Text",
            "Value" => $this->core->AESencrypt($y["Personal"]["DisplayName"])
           ],
           [
            "Attributes" => [
             "class" => "req",
             "name" => "Personal_Email",
             "placeholder" => "johnny.test@outerhaven.nyc",
             "type" => "email"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "E-Mail"
            ],
            "Type" => "Text",
            "Value" => $this->core->AESencrypt($y["Personal"]["Email"])
           ],
           [
            "Attributes" => [],
            "OptionGroup" => [
             "Female" => "Female",
             "Male" => "Male"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "Gender"
            ],
            "Name" => "Personal_Gender",
            "Type" => "Select",
            "Value" => $y["Personal"]["Gender"]
           ],
           [
            "Attributes" => [
             "name" => "LastPasswordChange",
             "type" => "hidden"
            ],
            "OptionGroup" => [],
            "Options" => [],
            "Type" => "Text",
            "Value" => $lastPasswordChange
           ],
           [
            "Attributes" => [],
            "OptionGroup" => [
             "0" => "Offline",
             "1" => "Online"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "Online Status"
            ],
            "Name" => "OnlineStatus",
            "Type" => "Select",
            "Value" => $y["Activity"]["OnlineStatus"],
           ],
           [
            "Attributes" => [
             "name" => "Personal_Description",
             "placeholder" => "Describe yourself..."
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "NONAME",
             "Header" => 1,
             "HeaderText" => "Description"
            ],
            "Type" => "TextBox",
            "Value" => $this->core->AESencrypt($y["Personal"]["Description"])
           ],
           [
            "Attributes" => [],
            "OptionGroup" => [
             "cc8ff50881a92c6da502af83e5736dfa" => "Engaged",
             "6b3dd3c40eca496b70653422b4e8ac60" => "In a Relationship",
             "e570489bea0f3850f322e397aa275e12" => "It's Complicated",
             "3ad9e20e1f957c1b5f2c069bae8f8205" => "Married",
             "66ba162102bbf6ae31b522aec561735e" => "Single",
             "7d03b2de73afb1449622783576301e75" => "Swinger",
             "2ef86623469f785760c19802da21e7fd" => "Widowed"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "Relationship Status"
            ],
            "Name" => "Personal_RelationshipStatus",
            "Type" => "Select",
            "Value" => $y["Personal"]["RelationshipStatus"]
           ],
           [
            "Attributes" => [
             "name" => "Personal_RelationshipWith",
             "placeholder" => "Who with? (if anyone)",
             "type" => "text"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "Who with? (if anyone)"
            ],
            "Type" => "Text",
            "Value" => $this->core->AESencrypt($relationshipWith)
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".Birthday$id",
          [
           [
            "Attributes" => [],
            "OptionGroup" => $birthMonths,
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "Month"
            ],
            "Name" => "BirthMonth",
            "Type" => "Select",
            "Value" => $y["Personal"]["Birthday"]["Month"]
           ],
           [
            "Attributes" => [],
            "OptionGroup" => $birthYears,
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull",
             "Header" => 1,
             "HeaderText" => "Year"
            ],
            "Name" => "BirthYear",
            "Type" => "Select",
            "Value" => $y["Personal"]["Birthday"]["Year"]
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".Patreon$id",
          [
           [
            "Attributes" => [
             "name" => "Donations_Patreon",
             "placeholder" => "JohnDoe",
             "type" => "text"
            ],
            "Options" => [
             "Header" => 1,
             "HeaderText" => "Patreon"
            ],
            "Type" => "Text",
            "Value" => $this->core->AESencrypt($y["Donations"]["Patreon"])
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".PayPal$id",
          [
           [
            "Attributes" => [
             "name" => "Donations_PayPal",
             "placeholder" => "JohnDoe",
             "type" => "text"
            ],
            "Options" => [
             "Header" => 1,
             "HeaderText" => "PayPal"
            ],
            "Type" => "Text",
            "Value" => $this->core->AESencrypt($y["Donations"]["PayPal"])
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".Personal$id",
          [
           [
            "Attributes" => [
             "name" => "Personal_AutoResponse",
             "placeholder" => "On vacation, back in October!",
             "type" => "text"
            ],
            "Options" => [
             "Header" => 1,
             "HeaderText" => "Automatic Response"
            ],
            "Type" => "TextBox",
            "Value" => $this->core->AESencrypt($autoResponse)
           ],
           [
            "Attributes" => [],
            "OptionGroup" => [
             "Multiple" => "Slide Show",
             "Single" => "Single"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 FrostedBright MobileFull RoundedLarge",
             "Header" => 1,
             "HeaderText" => "Amount of Cover Photos to display"
            ],
            "Name" => "CoverPhotoSelection",
            "Type" => "Select",
            "Value" => $coverPhotosSelection
           ],
           [
            "Attributes" => [],
            "OptionGroup" => [
             "0" => "No",
             "1" => "Yes"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 FrostedBright MobileFull RoundedLarge",
             "Header" => 1,
             "HeaderText" => "Accept nominations?"
            ],
            "Name" => "Electable",
            "Type" => "Select",
            "Value" => $chooseElectable
           ],
           [
            "Attributes" => [
             "name" => "Personal_MinimalDesign"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 FrostedBright MobileFull RoundedLarge",
             "Header" => 1,
             "HeaderText" => "Minimal Design",
             "Selected" => $chooseMinimalDesign
            ],
            "Text" => "Choose whether or not to render design and social media elements such as votes",
            "Type" => "Check",
            "Value" => 1
           ],
           [
            "Attributes" => [
             "class" => "PersonalUIVariant",
             "name" => "Personal_UIVariant",
             "type" => "hidden"
            ],
            "Options" => [],
            "Type" => "Text",
            "Value" => $setUIVariant
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".SecurityPassPhrase$id",
          [
           [
            "Attributes" => [
             "class" => "EmptyOnSuccess",
             "name" => "PIN",
             "pattern" => "\d*",
             "placeholder" => "PIN",
             "type" => "text"
            ],
            "Options" => [],
            "Type" => "Text",
            "Value" => ""
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".PrivacyForumsType$id",
          [
           [
            "Attributes" => [],
            "OptionGroup" => [
             "Private" => "Private",
             "Public" => "Public"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull"
            ],
            "Name" => "Privacy_ForumsType",
            "Type" => "Select",
            "Value" => $y["Privacy"]["ForumsType"]
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".PrivacyLookMeUp$id",
          [
           [
            "Attributes" => [],
            "OptionGroup" => [
             "0" => "No",
             "1" => "Yes"
            ],
            "Options" => [
             "Container" => 1,
             "ContainerClass" => "Desktop50 MobileFull"
            ],
            "Name" => "Privacy_LookMeUp",
            "Type" => "Select",
            "Value" => $y["Privacy"]["LookMeUp"]
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".SecurityPassPhrase$id",
          [
           [
            "Attributes" => [
             "name" => "Privacy_PassPhrase",
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
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".Container$id",
          [
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".SecurityRequirePassword$id",
          [
           [
            "Attributes" => [],
            "OptionGroup" => [
             "No" => "No",
             "Yes" => "Yes"
            ],
            "Options" => [],
            "Name" => "RequirePassword",
            "Title" => "Require Password to complete Sign In?",
            "Type" => "Select",
            "Value" => $passwordOnSignIn
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderInputs",
         "Parameters" => [
         [
          ".SubscribeStar$id",
          [
           [
            "Attributes" => [
             "name" => "Donations_SubscribeStar",
             "placeholder" => "JohnDoe",
             "type" => "text"
            ],
            "Options" => [
             "Header" => 1,
             "HeaderText" => "SubscribeStar"
            ],
            "Type" => "Text",
            "Value" => $this->core->AESencrypt($y["Donations"]["SubscribeStar"])
           ]
          ]
         ]
        ]
       ],
       [
        "Name" => "RenderVisibilityFilters",
         "Parameters" => [
         [
          ".VisibilityFilters$id",
          [
           [
            "Name" => "Privacy_Albums",
            "Title" => "Albums",
            "Value" => $y["Privacy"]["Albums"]
           ],
           [
            "Name" => "Privacy_Archive",
            "Title" => "Archive",
            "Value" => $y["Privacy"]["Archive"]
           ],
           [
            "Name" => "Privacy_Articles",
            "Title" => "Articles",
            "Value" => $y["Privacy"]["Articles"]
           ],
           [
            "Name" => "Privacy_Comments",
            "Title" => "Comments",
            "Value" => $y["Privacy"]["Comments"]
           ],
           [
            "Name" => "Privacy_ContactInfo",
            "Title" => "Contact Info",
            "Value" => $y["Privacy"]["ContactInfo"]
           ],
           [
            "Name" => "Privacy_ContactInfoDonate",
            "Title" => "Donate",
            "Value" => $y["Privacy"]["ContactInfoDonate"]
           ],
           [
            "Name" => "Privacy_ContactInfoEmails",
            "Title" => "E-Mails",
            "Value" => $y["Privacy"]["ContactInfoEmails"]
           ],
           [
            "Name" => "Privacy_ContactRequests",
            "Title" => "Contact Requests",
            "Value" => $y["Privacy"]["ContactRequests"]
           ],
           [
            "Name" => "Privacy_Contacts",
            "Title" => "Contacts",
            "Value" => $y["Privacy"]["Contacts"]
           ],
           [
            "Name" => "Privacy_Contributions",
            "Title" => "Contributions",
            "Value" => $y["Privacy"]["Contributors"]
           ],
           [
            "Name" => "Privacy_DLL",
            "Title" => "Downloads",
            "Value" => $y["Privacy"]["DLL"]
           ],
           [
            "Name" => "Privacy_Gender",
            "Title" => "Gender",
            "Value" => $y["Privacy"]["Gender"]
           ],
           [
            "Name" => "Privacy_Journal",
            "Title" => "Journal",
            "Value" => $y["Privacy"]["Journal"]
           ],
           [
            "Name" => "Privacy_LastActivity",
            "Title" => "Last Activity",
            "Value" => $y["Privacy"]["LastActivity"]
           ],
           [
            "Name" => "Privacy_MSG",
            "Title" => "Messages",
            "Value" => $y["Privacy"]["MSG"]
           ],
           [
            "Filter" => "NSFW",
            "Name" => "Privacy_NSFW",
            "Title" => "Profile Status",
            "Value" => $y["Privacy"]["NSFW"]
           ],
           [
            "Name" => "Privacy_OnlineStatus",
            "Title" => "Online Status",
            "Value" => $y["Privacy"]["OnlineStatus"]
           ],
           [
            "Name" => "Privacy_Polls",
            "Title" => "Polls",
            "Value" => $y["Privacy"]["Polls"]
           ],
           [
            "Name" => "Privacy_Posts",
            "Title" => "Posts",
            "Value" =>  $y["Privacy"]["Posts"]
           ],
           [
            "Name" => "Privacy_Products",
            "Title" => "Products",
            "Value" => $y["Privacy"]["Products"]
           ],
           [
            "Name" => "Privacy_Profile",
            "Title" => "Profile",
            "Value" => $y["Privacy"]["Profile"]
           ],
           [
            "Name" => "Privacy_Registered",
            "Title" => "Registered",
            "Value" => $y["Privacy"]["Registered"]
           ], 
           [
            "Name" => "Privacy_RelationshipStatus",
            "Title" => "Relationship Status",
            "Value" => $y["Privacy"]["RelationshipStatus"]
           ], 
           [
            "Name" => "Privacy_RelationshipWith",
            "Title" => "Relationship With",
            "Value" => $y["Privacy"]["RelationshipWith"]
           ],
           [
            "Name" => "Privacy_Shop",
            "Title" => "Shop",
            "Value" => $y["Privacy"]["Shop"]
           ]
          ]
         ]
        ]
       ]
      ];
      $_View = [
       "ChangeData" => [
        "[Preferences.Deactivate]" => $this->core->AESencrypt("v=".base64_encode("Profile:Deactivate")),
        "[Preferences.ID]" => $id,
        "[Preferences.Links.EditShop]" => $this->core->AESencrypt("v=".base64_encode("Shop:Edit")."&Shop=".base64_encode(md5($you))),
        "[Preferences.Links.NewPassword]" => $this->core->AESencrypt("v=".base64_encode("Profile:NewPassword")),
        "[Preferences.Links.NewPIN]" => $this->core->AESencrypt("v=".base64_encode("Profile:NewPIN")),
        "[Preferences.Personal.CoverPhotos]" => $coverPhotosList,
        "[Preferences.Personal.CoverPhotos.Clone]" => base64_encode($this->core->Element([
         "div", $this->core->Element(["button", "X", [
          "class" => "Delete v1",
          "data-target" => ".CoverPhotos[Clone.ID]"
         ]]).$this->core->Element([
          "div", $this->core->Change([[
           "[Media.Add]" => $this->core->AESencrypt($_SymbolicLink),
           "[Media.File]" => "",
           "[Media.ID]" => "[Clone.ID]",
           "[Media.Input]" => "CoverPhotos[]",
           "[Media.Input.LiveView]" => $_LiveView,
           "[Media.Name]" => "Cover Photo"
          ], $this->core->Extension("02ec63fe4f0fffe5e6f17621eb3b50ad")]), [
           "class" => "NONAME"
          ]
         ]), [
          "class" => "CoverPhotos[Clone.ID] Frosted Rounded"
         ]
        ])),
        "[Preferences.Personal.UIVariants]" => $this->core->Extension("4d3675248e05b4672863c6a7fd1df770"),
        "[Preferences.Privacy.Polls]" => $polls,
        "[Preferences.Privacy.RelationshipStatus]" => $y["Privacy"]["RelationshipStatus"],
        "[Preferences.Privacy.RelationshipWith]" => $y["Privacy"]["RelationshipWith"],
        "[Preferences.Privacy.Shop]" => $y["Privacy"]["Shop"],
        "[Preferences.Purge]" => $this->core->AESencrypt("v=".base64_encode("Profile:Purge")),
        "[Preferences.Save]" => $this->core->AESencrypt("v=".base64_encode("Profile:Save"))
       ],
       "ExtensionID" => "e54cb66a338c9dfdcf0afa2fec3b6d8a"
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Purge(array $data): string {
   $_Dialog = "";
   $data = $data["Data"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } else {
    $_Dialog = "";
    $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
    $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
     "Header" => base64_encode($this->core->Element([
      "h1", "Delete Profile", ["class" => "CenterText"]
     ])),
     "SignOut" => "Yes",
     "Text" => base64_encode("You are about to permanently delete your profile. This action cannot be undone, and you will need to sign up for a new profile if you wish to re-join our community. If you are sure you want to permanently delete your profile, please enter your PIN below."),
     "ViewData" => base64_encode(json_encode([
      "SecureKey" => base64_encode($y["Login"]["PIN"]),
      "VerifyPassPhrase" => 1,
      "v" => base64_encode("Profile:Purge")
     ], true))
    ]]);
    $_View = $this->core->RenderView($_View);
    if($verifyPassPhrase == 1) {
     $_Dialog = "";
     $key = $data["Key"] ?? base64_encode("");
     $key = base64_decode($key);
     $secureKey = $data["SecureKey"] ?? base64_encode("");
     $secureKey = base64_decode($secureKey);
     if(md5($key) == $secureKey) {
      $articles = $y["Pages"] ?? [];
      $blogs = $y["Blogs"] ?? [];
      $chats = $y["GroupChats"] ?? [];
      $forums = $y["Forums"] ?? [];
      $passPhrase = base64_encode($key);
      $securePassPhrase = base64_encode($secureKey);
      $polls = $y["Polls"] ?? [];;
      $restrictedIDs = $this->core->RestrictedIDs;
      $shop = $this->core->Data("Get", ["shop", md5($you)]);
      $shop["Live"] = 0;
      $shop["Open"] = 0;
      $shop["Purge"] = 1;
      $shopProducts = $shop["Products"] ?? [];
      $statusUpdates = $y["StatusUpdates"] ?? [];
      $bulletins = $this->core->Data("Get", ["bulletins", md5($you)]);
      if(!empty($bulletins)) {
       $bulletins["Purge"] = 1;
       $this->core->Data("Save", ["bulletins", md5($you), $bulletins]);
      }
      $chat = $this->core->Data("Get", ["chat", md5($you)]);
      if(!empty($chat)) {
       $chat["Purge"] = 1;
       $this->core->Data("Save", ["chat", md5($you), $chat]);
      }
      $contacts = $this->core->Data("Get", ["cms", md5($you)]);
      if(!empty($contacts)) {
       $contacts["Purge"] = 1;
       $this->core->Data("Save", ["cms", md5($you), $contacts]);
      }
      $conversation = $this->core->Data("Get", ["conversation", md5($you)]);
      if(!empty($conversation)) {
       $conversation["Purge"] = 1;
       $this->core->Data("Save", ["conversation", md5($you), $conversation]);
      }
      $discountCodes = $this->core->Data("Get", ["dc", md5($you)]);
      if(!empty($discountCodes)) {
       $discountCodes["Purge"] = 1;
       $this->core->Data("Save", ["dc", md5($you), $discountCodes]);
      } foreach($articles as $key => $id) {
       $article = $this->core->Data("Get", ["pg", $id]);
       if(!empty($article) && !in_array($id, $restrictedIDs)) {
        $this->view(base64_encode("Page:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($id),
         "SecureKey" => $securePassPhrase
        ]]);
       }
      } foreach($blogs as $key => $id) {
       $blog = $this->core->Data("Get", ["blg", $id]);
       if(!empty($blog) && !in_array($id, $restrictedIDs)) {
        $this->view(base64_encode("Blog:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($id),
         "SecureKey" => $securePassPhrase
        ]]);
       }
      } foreach($chats as $key => $id) {
       $chat = $this->core->Data("Get", ["chat", $id]);
       if(!empty($chat) && !in_array($id, $restrictedIDs)) {
        $this->view(base64_encode("Chat:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($id),
         "SecureKey" => $securePassPhrase
        ]]);
       }
      } foreach($forums as $key => $id) {
       $forum = $this->core->Data("Get", ["pf", $id]);
       if(!empty($forum) && !in_array($id, $restrictedIDs)) {
        $this->view(base64_encode("Forum:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($id),
         "SecureKey" => $securePassPhrase
        ]]);
       }
      }
      $media = $this->core->Data("Get", ["fs", md5($you)]);
      if(!empty($media)) {
       $efsAnnex = $this->core->DocumentRoot."/efs/$you/";
       $media["Purge"] = 1;
       $mediaAlbums = $media["Albums"] ?? [];
       $mediaFiles = $media["Files"] ?? [];
       foreach($mediaAlbums as $key => $info) {
        $this->view(base64_encode("Album:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($info["ID"]),
         "SecureKey" => $securePassPhrase
        ]]);
       } foreach($mediaFiles as $key => $info) {
        $this->view(base64_encode("File:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode("$you-".$info["ID"]),
         "SecureKey" => $securePassPhrase
        ]]);
       } if(file_exists($efsAnnex) || is_dir($efsAnnex)) {
        $this->core->RecursiveDirectoryPurge($efsAnnex);
       }
       $this->core->Data("Save", ["fs", md5($you), $media]);
      } foreach($polls as $key => $id) {
       $poll = $this->core->Data("Get", ["poll", $id]);
       if(!empty($poll) && !in_array($id, $restrictedIDs)) {
        $this->view(base64_encode("Poll:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($id),
         "SecureKey" => $securePassPhrase
        ]]);
       }
      } foreach($statusUpdates as $key => $id) {
       $statusUpdates = $this->core->Data("Get", ["su", $id]);
       if(!empty($statusUpdates) && !in_array($id, $restrictedIDs)) {
        $this->view(base64_encode("StatusUpdate:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($id),
         "SecureKey" => $securePassPhrase
        ]]);
       }
      }
      $purchaseOrders = $this->core->Data("Get", ["po", md5($you)]);
      if(!empty($stream)) {
       $purchaseOrders["Purge"] = 1;
       $this->core->Data("Save", ["po", md5($you), $purchaseOrders]);
      }
      $shop = $this->core->Data("Get", ["shop", md5($you)]);
      if(!empty($shop)) {
       foreach($shopProducts as $key => $id) {
        $this->view(base64_encode("Product:Purge"), ["Data" => [
         "Key" => $passPhrase,
         "ID" => base64_encode($id),
         "SecureKey" => $securePassPhrase
        ]]);
       }
       $this->core->Data("Save", ["shop", md5($you), $shop]);
      }
      $stream = $this->core->Data("Get", ["stream", md5($you)]);
      if(!empty($stream)) {
       $stream["Purge"] = 1;
       $this->core->Data("Save", ["stream", md5($you), $stream]);
      }
      $votes = $this->core->Data("Get", ["votes", md5($you)]);
      if(!empty($votes)) {
       $votes["Purge"] = 1;
       $this->core->Data("Save", ["votes", md5($you), $votes]);
      }
      $yourData = $this->core->Data("Get", ["mbr", md5($you)]);
      if(!empty($yourData)) {
       $yourData["Purge"] = 1;
       $this->core->Data("Save", ["mbr", md5($you), $yourData]);
      }
      $_View = $this->view(base64_encode("WebUI:Gateway"), []);
      $_View = $this->core->Element([
       "div", $this->core->Element([
        "h3", "Success!", ["class" => "CenterText UpperCase"]
       ]).$this->core->Element([
        "p", "Your profile is now slated for purging. We hope to see you again!",
        ["class" => "CenterText"]
       ]), ["class" => "Red RoundedLarge Shadowed"]
      ]).$this->core->RenderView($_View);
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_Header = "Error";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $email = $data["Personal_Email"] ?? "";
   $emailIsTaken = 0;
   $members = $this->core->DatabaseSet("Member");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $_UIVariant = $y["Personal"]["UIVariant"] ?? 0;
   foreach($members as $key => $value) {
    $value = str_replace("nyc.outerhaven.mbr.", "", $value);
    $member = $this->core->Data("Get", ["mbr", $value]);
    $check = ($member["Login"]["Username"] != $you) ? 1 : 0;
    $check2 = ($email == $member["Personal"]["Email"]) ? 1 : 0;
    if($check == 1 && $check2 == 1) {
     $emailIsTaken++;
    }
   } if(empty($data["Personal_DisplayName"])) {
    $message = "Your Display Name is missing.";
   } elseif(empty($email)) {
    $message = "Your E-Mail is missing.";
   } elseif($emailIsTaken > 0) {
    $message = "Another Member is already using <em>$email</em>.";
   } elseif($this->core->ID == $you) {
    $message = "You must be signed in to continue.";
   } else {
    $_Header = "Done";
    $coverPhotos = [];
    $coverPhotosData = $data["CoverPhotos"] ?? [];
    $newMember = $this->core->NewMember(["Username" => $you]);
    $now -= $this->core->timestamp;
    $firstName = $data["name"] ?? "";
    foreach($data as $key => $value) {
     if(strpos($key, "Donations_") !== false) {
      $k1 = explode("_", $key);
      $newMember["Donations"][$k1[1]] = $value ?? $y["Donations"][$k1[1]];
     } elseif(strpos($key, "Personal_") !== false) {
      $k1 = explode("_", $key);
      $newMember["Personal"][$k1[1]] = $value ?? $y["Personal"][$k1[1]];
     } elseif(strpos($key, "Privacy_") !== false) {
      $k1 = explode("_", $key);
      $newMember["Privacy"][$k1[1]] = $value ?? $y["Privacy"][$k1[1]];
     }
    } foreach($coverPhotosData as $key => $image) {
     if(!empty($image)) {
      array_push($coverPhotos, $image);
     }
    } foreach($newMember["Blocked"] as $key => $value) {
     $newMember["Blocked"][$key] = $y["Blocked"][$key] ?? [];
    } foreach($newMember["Login"] as $key => $value) {
     $newMember["Login"][$key] = $y["Login"][$key] ?? [];
    } foreach($newMember["Shopping"] as $key => $value) {
     $newMember["Shopping"][$key] = $y["Shopping"][$key] ?? [];
    } foreach($newMember["Subscriptions"] as $key => $value) {
     $active = $y["Subscriptions"][$key]["A"] ?? $value["A"];
     $begins = $y["Subscriptions"][$key]["B"] ?? $value["B"];
     $ends = $y["Subscriptions"][$key]["E"] ?? $value["E"];
     $newMember["Subscriptions"][$key]["A"] = $active;
     $newMember["Subscriptions"][$key]["B"] = $begins;
     $newMember["Subscriptions"][$key]["E"] = $ends;
    }
    $newMember["Activity"]["LastPasswordChange"] = $data["LastPasswordChange"] ?? $now;
    $newMember["Activity"]["OnlineStatus"] = $data["OnlineStatus"];
    $newMember["Activity"]["Registered"] = $y["Activity"]["Registered"];
    $newMember["ArtistCommissionsPaid"] = $y["ArtistCommissionsPaid"] ?? [];
    $newMember["Blogs"] = $y["Blogs"] ?? [];
    $newMember["Forums"] = $y["Forums"] ?? [];
    $newMember["Inactive"] = 0;
    $newMember["Pages"] = $y["Pages"] ?? [];
    $newMember["Personal"]["Birthday"] = [
     "Month" => $data["BirthMonth"],
     "Year" => $data["BirthYear"]
    ];
    $newMember["Login"]["RequirePassword"] = $data["RequirePassword"] ?? "No";
    $newMember["Personal"]["Age"] = date("Y") - $y["Personal"]["Birthday"]["Year"];
    $newMember["Personal"]["CoverPhoto"] = $y["Personal"]["CoverPhoto"] ?? "";
    $newMember["Personal"]["CoverPhotoSelection"] = $data["CoverPhotoSelection"] ?? "Single";
    $newMember["Personal"]["CoverPhotos"] = $coverPhotos;
    $newMember["Personal"]["Electable"] = $data["Electable"] ?? 0;
    $newMember["Personal"]["FirstName"] = explode(" ", $firstName)[0];
    $newMember["Personal"]["ProfilePicture"] = $y["Personal"]["ProfilePicture"];
    $newMember["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
    $newMember["Polls"] = $y["Polls"] ?? [];
    $newMember["Rank"] = $y["Rank"];
    $newMember["Verified"] = $y["Verified"] ?? 0;
    #$this->core->Data("Save", ["mbr", md5($you), $newMember]);
    $message = "Your Preferences were saved!";
   }
   $newMember = $newMember ?? [];
   return $this->core->JSONResponse([
    "Dialog" => [
     "Body" => $message,
     "Header" => $_Header,
     "Scrollable" => json_encode($newMember, true)
    ],
    "SetUIVariant" => $_UIVariant
   ]);
  }
  function SavePassword(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = "";
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "CurrentPassword",
    "NewPassword",
    "NewPassword2"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(empty($data["CurrentPassword"])) {
    $_Dialog = [
     "Body" => "You must enter your current Password."
    ];
   } elseif(empty($data["NewPassword"]) || empty($data["NewPassword2"])) {
    $_Dialog = [
     "Body" => "You must enter and confirm your new Password."
    ];
   } elseif(md5($data["CurrentPassword"]) != $y["Login"]["Password"]) {
    $_Dialog = [
     "Body" => "The Passwords do not match."
    ];
   } elseif($data["NewPassword"] != $data["NewPassword2"]) {
    $_Dialog = [
     "Body" => "The new Passwords do not match."
    ];
   } else {
    $_Dialog = "";
    $y["Activity"]["LastPasswordChange"] = $this->core->timestamp;
    $y["Login"]["Password"] = md5($data["NewPassword"]);
    #$this->core->Data("Save", ["mbr", md5($you), $y]);
    $_Dialog = "";
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", "Your Password has been updated."
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "ReplaceContent",
    "View" => $_View
   ]);
  }
  function SavePIN(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = "";
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "CurrentPIN",
    "NewPIN",
    "NewPIN2"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(empty($data["CurrentPIN"])) {
    $_Dialog = [
     "Body" => "You must enter your current PIN."
    ];
   } elseif(empty($data["NewPIN"]) || empty($data["NewPIN2"])) {
    $_Dialog = [
     "Body" => "You must enter and confirm your new PIN."
    ];
   } elseif(!is_numeric($data["NewPIN"]) || !is_numeric($data["NewPIN2"])) {
    $_Dialog = [
     "Body" => "PINs must be numeric (0-9)."
    ];
   } elseif(md5($data["CurrentPIN"]) != $y["Login"]["PIN"]) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($data["NewPIN"] != $data["NewPIN2"]) {
    $_Dialog = [
     "Body" => "The new PINs do not match."
    ];
   } else {
    $_AccessCode = "Accepted";
    $y["Login"]["PIN"] = md5($data["NewPIN"]);
    #$this->core->Data("Save", ["mbr", md5($you), $y]);
    $_Dialog = "";
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", "Your PIN has been updated."
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "ReplaceContent",
    "View" => $_View
   ]);
  }
  function SignIn(array $data): string {
   $_AddTopMargin = "0";
   $_Commands = "";
   $_Dialog = "";
   $_ResponseType = "GoToView";
   $_View = "";
   $data = $data["Data"] ?? [];
   $parentView = $viewData["ParentView"] ?? base64_encode("SignIn");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $_AddTopMargin = "1";
    $_Dialog = [
     "Body" => "We could not find the username you entered."
    ];
    $data = $this->core->DecodeBridgeData($data);
    $username = $data["Username"] ?? "";
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
    $viewData = json_decode(base64_decode($viewData), true);
    $member = $this->core->GetContentData([
     "Blacklisted" => 0,
     "ID" => base64_encode("Member;".md5($username))
    ]);
    if($member["Empty"] == 0) {
     $member = $member["DataModel"];
     $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
     $viewData = json_decode(base64_decode($viewData), true);
     $viewData["Step"] = base64_encode(3);
     $viewData["Username"] = $this->core->AESencrypt($member["Login"]["Username"]);
     $data = [];
     $data["Email"] = base64_encode($member["Personal"]["Email"]);
     $data["ParentView"] = $parentView;
     $data["ReturnView"] = base64_encode(base64_encode("Profile:SignIn"));
     $data["ViewData"] = base64_encode(json_encode($viewData));
     $_View = $this->view(base64_encode("WebUI:TwoFactorAuthentication"), ["Data" => $data]);
     $_View = $this->core->RenderView($_View);
    }
   } elseif($step == 3) {
    $_AddTopMargin = "1";
    $_Dialog = [
     "Body" => "We could not find the username you entered."
    ];
    $data = $this->core->DecodeBridgeData($data);
    $password = $data["Password"] ?? "";
    $username = $data["Username"] ?? "";
    $member = $this->core->GetContentData([
     "Blacklisted" => 0,
     "ID" => base64_encode("Member;".md5($username))
    ]);
    if($member["Empty"] == 0) {
     $member = $member["DataModel"];
     $passwordRequired = $member["Login"]["RequirePassword"] ?? "Yes";
     if(empty($password) && $passwordRequired == "Yes") {
      $_View = [
       "ChangeData" => [
        "[SignIn.Processor]" => base64_encode("v=".base64_encode("Profile:SignIn")."&Step=".base64_encode(3)),
        "[SignIn.Username]" => $username
       ],
       "ExtensionID" => "45787465-6e73-496f-ae42-794d696b65-67a88c2a5cfaf"
      ];
     } else {
      $_Commands = [
       [
        "Name" => "SignIn",
        "Parameters" => [
         $this->core->Authenticate("Save", [
          "Password" => $member["Login"]["Password"],
          "Username" => $member["Login"]["Username"]
         ])
        ]
       ],
       [
        "Name" => "UpdateContent",
        "Parameters" => [
         ".Content",
         base64_encode("v=".base64_encode("WebUI:Landing"))
        ]
       ]
      ];
      $_ResponseType = "N/A";
      $_View = "";
     }
    }
   } else {
    $_ResponseType = "N/A";
    $_View = [
     "ChangeData" => [
      "[SignIn.ParentView]" => "MainView",
      "[SignIn.Processor]" => base64_encode("v=".base64_encode("Profile:SignIn")."&Step=".base64_encode(2))
     ],
     "ExtensionID" => "45787465-6e73-496f-ae42-794d696b65-67a2fadba8755"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "AddTopMargin" => $_AddTopMargin,
    "Commands" => $_Commands,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function SignUp(array $data): string {
   $_AddTopMargin = "0";
   $_Card = "";
   $_Commands = "";
   $_Dialog = "";
   $_MinimumAge = $this->core->config["minRegAge"] ?? 13;
   $_View = "";
   $data = $data["Data"] ?? [];
   $_ResponseType = "GoToView";
   $securityKey = "";
   $parentView = $viewData["ParentView"] ?? base64_encode("SignUp");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $_AccessCode = "Denied";
    $_AddTopMargin = "1";
    $data = $this->core->DecodeBridgeData($data);
    $birthMonth = $data["BirthMonth"] ?? 10;
    $birthYear = $data["BirthYear"] ?? 1995;
    $age = date("Y") - $birthYear;
    $check = ($age > $_MinimumAge) ? 1 : 0;
    $email = $data["Email"] ?? "";
    $gender = $data["Gender"] ?? "Male";
    $message = "Internal Error";
    $name = $data["Name"] ?? "John";
    $i = 0;
    $members = $this->core->DatabaseSet("Member");
    $password = $data["Password"] ?? "";
    $password2 = $data["Password2"] ?? "";
    $pin = $data["PIN"] ?? "";
    $pin2 = $data["PIN2"] ?? "";
    $username = $data["Username"] ?? "";
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
    $viewData = json_decode(base64_decode($viewData), true);
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     $usernameIsTaken = ($member["Login"]["Username"] == $username) ? 1 : 0;
     if(($usernameIsTaken == 1 || $usernameIsTaken == 1) && $i == 0) {
      $i++;
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $message = "A valid Email address is required.";
    } elseif(empty($password)) {
     $message = "A Password is required.";
    } elseif($password != $password2) {
     $message = "Your Passwords must match.";
    } elseif(empty($pin)) {
     $message = "A PIN is required.";
    } elseif(!is_numeric($pin) || !is_numeric($pin2)) {
     $message = "Your PINs must be numeric.";
    } elseif($pin != $pin2) {
     $message = "Your PINs must match.";
    } elseif(empty($username)) {
     $message = "A Username is required.";
    } elseif(!preg_match("/^[a-zA-Z0-9-_]+$/", $username)) {
     $message = "Usernames may only contain letters, numbers, hyphens (-), and underscores (_).";
    } elseif(strpos($username, "Ghost_")) {
     $message = "You cannot be a ghost.";
    } elseif($username == $this->core->ID) {
     $message = $this->core->ID." is the system profile and cannot be used.";
    } elseif($check == 0) {
     $message = "You must be $_MinimumAge or older to sign up.";
    } elseif($i > 0) {
     $message = "The Username <em>$username</em> is already in use.";
    } else {
     $_AccessCode = "Accepted";
     $viewData["Age"] = base64_encode($age);
     $viewData["BirthMonth"] = base64_encode($birthMonth);
     $viewData["BirthYear"] = base64_encode($birthYear);
     $viewData["Gender"] = base64_encode($gender);
     $viewData["Email"] = base64_encode($email);
     $viewData["Name"] = base64_encode($name);
     $viewData["ParentView"] = "SignUp";
     $viewData["Password"] = base64_encode($password);
     $viewData["PIN"] = base64_encode($pin);
     $viewData["Username"] = base64_encode($username);
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ReturnView"] = base64_encode(base64_encode("Profile:SignUp"));
     $data["ViewData"] = base64_encode(json_encode($viewData));
     $_View = $this->view(base64_encode("WebUI:TwoFactorAuthentication"), ["Data" => $data]);
     $_View = $this->core->RenderView($_View);
    } if($_AccessCode != "Accepted") {
     $_Dialog = [
      "Body" => $message
     ];
     $_View = "";
    }
   } elseif($step == 3) {
    $_AccessCode = "Denied";
    $_AddTopMargin = "1";
    $birthMonth = $data["BirthMonth"] ?? base64_encode(10);
    $birthMonth = base64_decode($birthMonth);
    $birthYear = $data["BirthYear"] ?? base64_encode(1995);
    $birthYear = base64_decode($birthYear);
    $age = date("Y") - $birthYear;
    $check = ($age > $_MinimumAge) ? 1 : 0;
    $email = $data["Email"] ?? base64_encode("");
    $email = base64_decode($email);
    $gender = $data["Gender"] ?? base64_encode("Male");
    $gender = base64_decode($gender);
    $firstName = $data["Name"] ?? base64_encode("John");
    $firstName = explode(" ", base64_decode($firstName))[0];
    $i = 0;
    $members = $this->core->DatabaseSet("Member");
    $now = $this->core->timestamp;
    $password = $data["Password"] ?? base64_encode("");
    $password = base64_decode($password);
    $pin = $data["PIN"] ?? base64_encode("");
    $pin = base64_decode($pin);
    $username = $data["Username"] ?? base64_encode("");
    $username = base64_decode($username);
    $usernameID = md5($username);
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]);
     $emailIsTaken = ($member["Personal"]["Email"] == $email) ? 1 : 0;
     $usernameIsTaken = ($member["Login"]["Username"] == $username) ? 1 : 0;
     if(($usernameIsTaken == 1 || $usernameIsTaken == 1) && $i == 0) {
      $i++;
     }
    } if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
     $message = "A valid Email address is required.";
    } if($i > 0) {
     $message = "The Username <em>$username</em> is already in use.";
    } else {
     $_AccessCode = "Accepted";
     $_ResponseType = "N/A";
     /*--$this->core->Data("Save", ["cms", $usernameID, [
      "Contacts" => [],
      "Requests" => []
     ]]);
     $this->core->Data("Save", ["fs", $usernameID, [
      "Albums" => [
       md5("unsorted") => [
        "ID" => md5("unsorted"),
        "Created" => $this->core->timestamp,
        "ICO" => "",
        "Modified" => $this->core->timestamp,
        "Title" => "Unsorted",
        "Description" => "Files are uploaded here by default.",
        "NSFW" => 0,
        "Privacy" => md5("Public")
       ]
      ],
      "Files" => []
     ]]);
     $this->core->Data("Save", [
      "mbr", $usernameID, $this->core->NewMember([
       "Age" => $age,
       "BirthMonth" => $birthMonth,
       "BirthYear" => $birthYear,
       "DisplayName" => $username,
       "Email" => $email,
       "FirstName" => $firstName,
       "Gender" => $gender,
       "Password" => $password,
       "PIN" => md5($pin),
       "Username" => $username
      ])
     ]);
     $this->core->Data("Save", ["stream", $usernameID, []]);
     $this->core->Data("Save", ["shop", $usernameID, [
      "Contributors" => [
       $username => [
        "Company" => "$username's Company",
        "Description" => "Oversees general operations and administrative duties.",
        "Hired" => $now,
        "Paid" => 0,
        "Title" => "CEO"
       ]
      ],
      "CoverPhoto" => "",
      "CoverPhotoSource" => "",
      "Description" => "",
      "HireTerms" => "",
      "Live" => 0,
      "Modified" => $now,
      "Open" => 1,
      "Privacy" => md5("Private"),
      "Processing" => [],
      "Products" => [],
      "Tax" => 0,
      "Title" => "$username's Shop",
      "Welcome" => "<h1>Welcome</h1>\r\n<p>Welcome to my shop!</p>"
     ]]);
     if(!empty($email)) {
      $this->core->SendEmail([
       "Message" => $this->core->Change([[
        "[Mail.Name]" => $firstName
       ], $this->core->Extension("35fb42097f5a625e9bd0a38554226743")]),
       "Title" => "Welcome to ".$this->core->config["App"]["Name"]."!",
       "To" => $email
      ]);
     }
     $this->core->Statistic("New Member");--*/
     $_Card = [
      "Front" => [
       "ChangeData" => [
        "[Success.Username]" => $username
       ],
       "ExtensionID" => "872fd40c7c349bf7220293f3eb64ab45"
      ]
     ];
     $_Commands = [
      [
       "Name" => "SignIn",
       "Parameters" => [
        $this->core->Authenticate("Save", [
         "Password" => $password,
         "Username" => $username
        ])
       ]
      ],
      [
       "Name" => "UpdateContent",
       "Parameters" => [
        ".Content",
        base64_encode("v=".base64_encode("WebUI:Landing"))
       ]
      ]
     ];
     $_View = "";
    } if($_AccessCode != "Accepted") {
     $_Dialog = [
      "Body" => $message
     ];
     $_View = "";
    }
   } else {
    $_ResponseType = "N/A";
    $birthMonths = [];
    $birthYears = [];
    for($i = 1; $i <= 12; $i++) {
     $birthMonths[$i] = $i;
    } for($i = 1776; $i <= (date("Y") - $_MinimumAge); $i++) {
     $birthYears[$i] = $i;
    }
    $_View = [
     "ChangeData" => [
      "[SignUp.BirthMonths]" => json_encode($birthMonths, true),
      "[SignUp.BirthYears]" => json_encode($birthYears, true),
      "[SignUp.MinimumAge]" => $this->core->config["minAge"],
      "[SignUp.ParentView]" => "MainView",
      "[SignUp.Processor]" => $this->core->AESencrypt("v=".base64_encode("Profile:SignUp")."&Step=".base64_encode(2)),
      "[SignUp.ReturnView]" => base64_encode("Profile:SignUp"),
      "[SignUp.ViewData]" => base64_encode(json_encode([
       "Step" => base64_encode(3)
      ], true))
     ],
     "ExtensionID" => "c48eb7cf715c4e41e2fb62bdfa60f198"
    ];
   }
   $_Dialog = [
    "Body" => "This experience is temporarily down while we finish updates. Your profile will not be created if you proceed.",
    "Header" => "Sign Up"
   ];
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
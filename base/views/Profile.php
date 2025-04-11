<?php
 Class Profile extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function AddContent() {
   $_View = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID != $you) {
    $_IsArtist = $y["Subscriptions"]["Artist"]["A"] ?? 0;
    $_IsVIP = $y["Subscriptions"]["VIP"]["A"] ?? 0;
    $_IsSubscribed = (($_IsArtist + $_IsVIP) > 0) ? 1 : 0;
    $_View = $this->core->Element([
     "h1", "Create Content", ["class" => "CenterText UpperCase"]
    ]).$this->core->Element([
     "p", "Your central hub of content creation.", ["class" => "CenterText"]
    ]).$this->core->Element(["button", "Album", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("Album:Edit")."&new=1")
    ]]).$this->core->Element(["button", "Article", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("Page:Edit")."&new=1")
    ]]);
    $_View .= ($_IsSubscribed == 1) ? $this->core->Element(["button", "Blog", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("Blog:Edit")."&Member=".base64_encode($you)."&new=1")
    ]]).$this->core->Element(["button", "Forum", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("Forum:Edit")."&new=1")
    ]]) : "";
    $_View .= $this->core->Element(["button", "Group Chat", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("Chat:Edit")."&GenerateID=1&Username=".base64_encode($you))
    ]]).$this->core->Element(["button", "Link", [
     "class" => "LI OpenFirSTEPTool",
     "data-fst" => base64_encode("v=".base64_encode("Search:Links"))
    ]]).$this->core->Element(["button", "Media", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("File:Upload")."&AID=".md5("unsorted"))
    ]]).$this->core->Element(["button", "Poll", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("Poll:Create"))
    ]]);
    $_View .= ($_IsArtist == 1) ? $this->core->Element(["button", "Product or Service", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("Product:Edit")."&Card=1")
    ]]) : "";
    $_View .= $this->core->Element(["button", "Status Update", [
     "class" => "LI OpenCard",
     "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1")
    ]]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $_View
   ]);
  }
  function AddContentCheck() {
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $_View = ($this->core->ID != $you) ? $this->core->Element(["button", NULL, [
    "class" => "AddContent OpenFirSTEPTool h",
    "data-fst" => base64_encode("v=".base64_encode("Profile:AddContent"))
   ]]) : "";
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => $_View
   ]);
  }
  function Blacklist(array $a) {
   $_Dialog = [
    "Body" => "Some required data is missing."
   ];
   $_View = "";
   $data = $a["Data"] ?? [];
   $missing = 0;
   $requiredData = [
    "Command",
    "Content",
    "List"
   ];
   $_ResponseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   foreach($requiredData as $required) {
    if(empty($data[$required])) {
     $missing++;
    }
   } if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
   } elseif($missing == 0) {
    $_Dialog = "";
    $command = base64_decode($data["Command"]);
    $content = base64_decode($data["Content"]);
    $list = base64_decode($data["List"]);
    $blacklist = $y["Blocked"][$list] ?? [];
    $newBlacklist = [];
    $_ResponseType = "UpdateText";
    $text = "Error";
    foreach($blacklist as $key => $value) {
     if($content != $value) {
      array_push($newBlacklist, $value);
     }
    } if($command == "Block") {
     array_push($newBlacklist, $content);
     $text = "Unblock";
    } elseif($command == "Unblock") {
     $text = "Block";
    }
    $y["Blocked"][$list] = array_unique($newBlacklist);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $_View = [
     "Attributes" => [
      "class" => "Small UpdateButton v2",
      "data-processor" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($text)."&Content=".$data["Content"]."&List=".$data["List"])
     ],
     "Text" => $text
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function BlacklistCategories(array $a) {
   $accessCode = "Accepted";
   $y = $this->you;
   $r = "";
   $blacklidt = $y["Blocked"] ?? [];
   foreach($blacklidt as $key => $list) {
    $r .= $this->core->Element(["button", $key, [
     "class" => "LI OpenFirSTEPTool v2 v2w",
     "data-fst" => base64_encode("v=".base64_encode("Search:Containers")."&st=BL&BL=".base64_encode($key))
    ]]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Blacklists(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $r = $this->core->Change([[
    "[Blacklist.Categories]" => base64_encode("v=".base64_encode("Profile:BlacklistCategories"))
   ], $this->core->Extension("03d53918c3da9fbc174f94710182a8f2")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function BulletinCenter(array $a) {
   $accessCode = "Accepted";
   $search = base64_encode("Search:Containers");
   $r = $this->core->Change([[
    "[BulletinCenter.Bulletins]" => base64_encode("v=$search&st=Bulletins"),
    "[BulletinCenter.ContactRequests]" => base64_encode("v=$search&Chat=0&st=ContactsRequests")
   ], $this->core->Extension("6cbe240071d79ac32edbe98679fcad39")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function BulletinMessage(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
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
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $message
    ],
    "ResponseType" => "View"
   ]);
  }
  function BulletinOptions(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $bulletin = $data["Bulletin"] ?? "";
   $bulletin = (!empty($bulletin)) ? base64_decode($bulletin) : [];
   $bulletin = json_decode($bulletin, true);
   $id = $bulletin["ID"] ?? "";
   $r = "&nbsp;";
   $y = $this->you;
   if($bulletin["Read"] == 0) {
    $data = $bulletin["Data"] ?? [];
    $mar = "v=".base64_encode("Profile:MarkBulletinAsRead")."&ID=$id";
    if($bulletin["Type"] == "ArticleUpdate") {
     $article = $this->core->Data("Get", ["pg", $data["ArticleID"]]);
     $r = $this->core->Element([
      "button", "Take me to <em>".$article["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Page:Home")."&CARD=1&ID=".$data["ArticleID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
    } elseif($bulletin["Type"] == "BlogUpdate") {
     $blog = $this->core->Data("Get", ["blg", $data["BlogID"]]);
     $r = $this->core->Element([
      "button", "Take me to <em>".$blog["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Blog:Home")."&CARD=1&ID=".$data["ArticleID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
    } elseif($bulletin["Type"] == "BlogPostUpdate") {
     $post = $this->core->Data("Get", ["bp", $data["PostID"]]);
     $r = $this->core->Element([
      "button", "Take me to <em>".$post["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("BlogPost:Home")."&CARD=1&ID=".$data["ArticleID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
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
      $r = "<input name=\"Username\" type=\"hidden\" value=\"".$data["From"]."\"/>\r\n";
      $r .= $this->core->Element(["div", $this->core->Element([
       "button", "Accept", [
        "class" => "BBB Close MarkAsRead SendData v2 v2w",
        "data-form" => ".Bulletin$id .Options",
        "data-MAR" => base64_encode($mar),
        "data-processor" => base64_encode($accept),
        "data-target" => ".Bulletin$id .Options"
       ]]), ["class" => "Desktop50"]
      ]).$this->core->Element(["div", $this->core->Element([
       "button", "Decline", [
        "class" => "Close MarkAsRead SendData v2 v2w",
        "data-form" => ".Bulletin$id .Options",
        "data-MAR" => base64_encode($mar),
        "data-processor" => base64_encode($decline),
        "data-target" => ".Bulletin$id .Options"
       ]]), ["class" => "Desktop50"]
      ]);
     }
    } elseif($bulletin["Type"] == "InviteToArticle") {
     $article = $this->core->Data("Get", [
      "pg",
      $data["ArticleID"]
     ]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$article["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Page:Home")."&CARD=1&ID=".$article["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToBlog") {
     $blog = $this->core->Data("Get", ["blg", $data["BlogID"]]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$blog["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Blog:Home")."&CARD=1&ID=".$blog["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToForum") {
     $forum = $this->core->Data("Get", ["pf", $data["ForumID"]]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$forum["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Forum:Home")."&CARD=1&ID=".$forum["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToShop") {
     $shop = $this->core->Data("Get", ["shop", $data["ShopID"]]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$shop["Title"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Shop:Home")."&CARD=1&ID=".$data["ShopID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "Invoice" || $bulletin["Type"] == "NewJob") {
     $shop = $this->core->Data("Get", ["shop", $data["Shop"]]) ?? [];
     $r = $this->core->Element([
      "button", "View Invoice", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Invoice:Home")."&Card=1&ID=".$data["Invoice"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InvoiceForward") {
     $shop = $this->core->Data("Get", ["shop", $data["Shop"]]) ?? [];
     $r = $this->core->Element([
      "button", "View Forwarded Invoice", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Invoice:Home")."&Card=1&ID=".$data["Invoice"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InvoiceUpdate") {
     $shop = $this->core->Data("Get", ["shop", $data["Shop"]]) ?? [];
     $r = $this->core->Element([
      "button", "View Updated Invoice", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Invoice:Home")."&Card=1&ID=".$data["Invoice"])
      ]
     ]);
    } elseif($bulletin["Type"] == "NewArticle") {
     $article = $this->core->Data("Get", ["pg", $data["ArticleID"]]);
     $r = $this->core->Element([
      "button", "Take me to <em>".$article["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Page:Home")."&CARD=1&ID=".$data["ArticleID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
    } elseif($bulletin["Type"] == "NewBlogPost") {
     $post = $this->core->Data("Get", ["bp", $data["PostID"]]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$post["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options",
       "data-view" => base64_encode("v=".base64_encode("BlogPost:Home")."&CARD=1&Blog=".$data["BlogID"]."&Post=".$data["PostID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "NewMessage") {
     $r = $this->core->Element([
      "button", "Chat with <em>".$data["From"]."</em>", [
       "class" => "BBB Close OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Chat:Home")."&1on1=1&Card=1&Username=".base64_encode($data["From"]))
      ]
     ]);
    } elseif($bulletin["Type"] == "NewPoll") {
     $poll = $this->core->Data("Get", ["poll", $data["PollID"]]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$poll["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options",
       "data-view" => base64_encode("v=".base64_encode("Poll:Home")."&ID=".base64_encode($data["PollID"]))
      ]
     ]);
    } elseif($bulletin["Type"] == "NewProduct") {
     $product = $this->core->Data("Get", [
      "miny",
      $data["ProductID"]
     ]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$product["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options",
       "data-view" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=".$product["ID"]."&UN=".$data["ShopID"])
      ]
     ]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Bulletins(array $a) {
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
  function ChangeRank(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $r = [
    "Body" => "The Member Identifier or Rank are missing."
   ];
   $pin = $data["PIN"] ?? "";
   $rank = $data["Rank"] ?? md5("Member");
   $_ResponseType = "Dialog";
   $username = $data["Username"] ?? "";
   $y = $this->you;
   if(md5($pin) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => $this->core->Element(["p", "The PINs do not match."]),
    ];
   } elseif(!empty($rank) && !empty($username)) {
    $accessCode = "Accepted";
    $member = $this->core->Member($username);
    $_ResponseType = "ReplaceContent";
    $member["Rank"] = md5($rank);
    #$this->core->Data("Save", ["mbr", md5($username), $member]);
    $r = $this->core->Element([
     "h3", "Success", ["class" => "CenterText UpperCase"]
    ]).$this->core->Element([
     "p", $member["Personal"]["DisplayName"]."'s Rank within <em>".$this->core->config["App"]["Name"]."</em> was Changed to $rank.",
     ["class" => "CenterText"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $_ResponseType,
    "Success" => "CloseDialog"
   ]);
  }
  function Deactivate() {
   $accessCode = "Denied";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } else {
    $accessCode = "Accepted";
    $y["Inactive"] = 1;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->view(base64_encode("WebUI:Gateway"), []);
    $r = $this->core->Element([
     "div", $this->core->Element([
      "p", "Your profile is now inactive and you can sign in at any time to activate it, we hope to see you again soon!"
     ]), ["class" => "FrostedBright RoundedLarge Shadowed"]
    ]).$this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Donate(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $opt = "";
   $t = $this->core->Member(base64_decode($data["UN"]));
   $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
   $don = $t["Donations"] ?? [];
   $y = $this->you;
   if(empty($don)) {
    if($t["Login"]["Username"] == $y["Login"]["Username"]) {
     $p = "You have not set up Donations yet.";
    } else {
     $p = "$display has not set up Donations yet.";
    }
    $opt .= $this->core->Element(["p", $p]);
   } else {
    $opt .= (!empty($don["Patreon"])) ? $this->core->Element([
     "button", "Donate via Patreon", [
      "class" => "LI",
      "onclick" => "W('https://patreon.com/".$don["Patreon"]."', '_blank');"
     ]
    ]) : "";
    $opt .= (!empty($don["PayPal"])) ? $this->core->Element([
     "button", "Donate via PayPal", [
      "class" => "LI",
      "onclick" => "W('https://paypal.me/".$don["PayPal"]."/5', '_blank');"
     ]
    ]) : "";
    $opt .= (!empty($don["SubscribeStar"])) ? $this->core->Element([
     "button", "Donate via SubscribeStar", [
      "class" => "LI LIL",
      "onclick" => "W('https://subscribestar.com/".$don["SubscribeStar"]."', '_blank');"
     ]
    ]) : "";
   }
   $r = [
    "Body" => $this->core->Element(["div", $opt, ["class" => "scr"]]),
   ];
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Home(array $a) {
   $_ViewTitle = $this->core->config["App"]["Name"];
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $addTopMargin = "0";
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
   $r = [
    "Body" => "The requested Member could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $bl = $this->core->CheckBlocked([$y, "Members", $member]);
   $_Member = $this->core->GetContentData([
    "Blacklisted" => $bl,
    "ID" => base64_encode("Member;".md5(base64_decode($member)))
   ]);
   $member = $_Member["DataModel"];
   if(strpos(base64_decode($data["UN"]), "Ghost_")) {
    $r = [
     "Body" => "You cannot talk to ghosts."
    ];
   } elseif($_Member["Empty"] == 0) {
    $id = $member["Login"]["Username"];
    $_TheirContacts = $this->core->Data("Get", ["cms", md5($id)]);
    $_TheyBlockedYou = $this->core->CheckBlocked([$_Member["DataModel"], "Members", $you]);
    $_YouBlockedThem = $this->core->CheckBlocked([$y, "Members", $id]);
    $displayName = $_Member["ListItem"]["Title"];
    $b2 = ($id == $you) ? "Your Profile" : "$displayName's Profile";
    $lpg = "Profile".md5($id);
    $privacy = $member["Privacy"] ?? [];
    $subscriptions = $member["Subscriptions"] ?? [];
    $ck = ($id == $you) ? 1 : 0;
    $ck2 = ($privacy["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->config["minAge"])) ? 1 : 0;
    $ckart = 0;
    $public = md5("Public");
    $r = [
     "Body" => "The Member may have reduced their visibility.",
     "Header" => "Not Found"
    ];
    $search = base64_encode("Search:Containers");
    $theirContacts = $_TheirContacts["Contacts"] ?? [];
    $theirRequests = $_TheirContacts["Requests"] ?? [];
    $visible = $this->core->CheckPrivacy([
     "Contacts" => $theirContacts,
     "Privacy" => $privacy["Profile"],
     "UN" => $id,
     "Y" => $you
    ]);
    if($_TheyBlockedYou == 0 && $_YouBlockedThem == 0 && ($ck == 1 || $ck2 == 1 || $visible == 1)) {
     $_IsArtist = $subscriptions["Artist"]["A"] ?? 0;
     $_IsVIP = $subscriptions["VIP"]["A"] ?? 0;
     $_IsSubscribed = (($_IsArtist + $_IsVIP) > 0) ? 1 : 0;
     $_ViewTitle = "$displayName @ ".$_ViewTitle;
     $accessCode = "Accepted";
     $addTopMargin = "1";
     $passPhrase = $member["Privacy"]["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
      $r = $this->core->RenderView($r);
     } elseif($verifyPassPhrase == 1) {
      $accessCode = "Denied";
      $addTopMargin = "0";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $r = $this->core->Element(["p", "The Key is missing."]);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $r = $this->core->Element(["p", "The Keys do not match."]);
      } else {
       $accessCode = "Accepted";
       $r = $this->view(base64_encode("Profile:Home"), ["Data" => [
        "AddTo" => $addTo,
        "UN" => base64_encode($id),
        "ViewProtectedContent" => 1
       ]]);
       $r = $this->core->RenderView($r);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $accessCode = "Accepted";
      $blockCommand = ($_YouBlockedThem == 0) ? "Block" : "Unblock";
      $actions = $this->core->Element([
       "button", $blockCommand, [
        "class" => "Small UpdateButton v2",
        "data-processor" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($id)."&List=".base64_encode("Members"))
       ]
      ]);
      $actions .= ($chat == 0) ? $this->core->Element(["button", "Chat", [
       "class" => "OpenCard Small v2",
       "data-view" => base64_encode("v=".base64_encode("Chat:Home")."&1on1=1&Card=1&ID=".base64_encode($id))
      ]]) : "";
      $actions .= ($_IsArtist == 1) ? $this->core->Element(["button", "Donate", [
       "class" => "OpenCardSmall Small v2",
       "data-view" => base64_encode("v=".base64_encode("Profile:Donate")."&UN=".base64_encode($id))
      ]]) : "";
      $actions .= ($_IsVIP == 0 && $y["Rank"] == md5("High Command")) ? $this->core->Element(["button", "Make VIP", [
       "class" => "SendData Small v2",
       "data-form" => ".Profile$id",
       "data-processor" => base64_encode("v=".base64_encode("Profile:MakeVIP")."&ID=".base64_encode($id))
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
       "[Error.Back]" => "",
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their media albums to themselves."
      ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
      if($ck == 1 || $privacy["Albums"] == $public || $visible == 1) {
       $albums = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "st" => "MBR-ALB"
       ]]);
       $albums = $this->core->RenderView($albums);
      }
      $articles = $this->core->Change([[
       "[Error.Back]" => "",
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their archive contributions to themselves."
      ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
      if($ck == 1 || $privacy["Archive"] == $public || $visible == 1) {
       $articles = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "b2" => $b2,
        "lPG" => $lpg,
        "st" => "MBR-CA"
       ]]);
       $articles = $this->core->RenderView($articles);
      }
      $blogs = $this->core->Change([[
       "[Error.Back]" => "",
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their blogs to themselves."
      ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
      if($ck == 1 || $privacy["Posts"] == $public || $visible == 1) {
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
       "[Error.Back]" => "",
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their contacts to themselves."
      ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
      if($ck == 1 || $privacy["Contacts"] == $public || $visible == 1) {
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
          "[ContactRequest.ID]" => $id,
          "[ContactRequest.Option]" => $this->core->Element([
           "div", $this->core->Element(["button", "Accept", [
            "class" => "BBB SendData v2 v2w",
            "data-form" => ".ContactRequest$id",
            "data-processor" => base64_encode("v=".base64_encode("Contact:Requests")."&accept=1")
           ]]), ["class" => "Desktop50"]
          ]).$this->core->Element([
           "div", $this->core->Element(["button", "Decline", [
            "class" => "BB SendData v2 v2w",
            "data-form" => ".ContactRequest$id",
            "data-processor" => base64_encode("v=".base64_encode("Contact:Requests")."&decline=1")
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
            "data-form" => ".ContactRequest$id",
            "data-processor" => base64_encode("v=".base64_encode("Contact:Requests"))
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
            "data-form" => ".ContactRequest$id",
            "data-processor" => base64_encode("v=".base64_encode("Contact:Requests"))
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
        }
        $changeRank = $this->core->Change([[
         "[Ranks.Authentication]" => base64_encode("v=".base64_encode("Profile:ChangeRank")),
         "[Ranks.DisplayName]" => $displayName,
         "[Ranks.ID]" => md5($id),
         "[Ranks.Options]" => json_encode($ranks, true),
         "[Ranks.Username]" => $id,
         "[Ranks.YourRank]" => $y["Rank"]
        ], $this->core->Extension("914dd9428c38eecf503e3a5dda861559")]);
       }
      }
      $coverPhotos = $member["Personal"]["CoverPhotos"] ?? [];
      $coverPhotosSlideShowDisabled = $member["Personal"]["CoverPhotoSelection"] ?? "Single";
      $coverPhotosSlideShowDisabled = ($coverPhotosSlideShowDisabled == "Multiple") ? "false" : "true";
      $embeddedView = $data["EmbeddedView"] ?? 0;
      $gender = $member["Personal"]["Gender"] ?? "Male";
      $gender = $this->core->Gender($gender);
      $journal = $this->core->Change([[
       "[Error.Back]" => "",
       "[Error.Header]" => "Forbidden",
       "[Error.Message]" => "$displayName keeps their Journal to themselves."
      ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
      if($ck == 1 || $privacy["Journal"] == $public || $visible == 1) {
       $journal = $this->view($search, ["Data" => [
        "UN" => base64_encode($id),
        "b2" => $b2,
        "lPG" => $lpg,
        "st" => "MBR-JE"
       ]]);
       $journal = $this->core->RenderView($journal);
      }
      $newCoverPhotos = [];
      $options = $_Member["ListItem"]["Options"];
      $share = ($id == $you || $privacy["Profile"] == $public) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element([
       "button", "Share", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Share"]
       ]
      ]) : "";
      $verified = $member["Verified"] ?? 0;
      $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
      foreach($coverPhotos as $key => $image) {
       $newCoverPhotos[$key] = $this->core->CoverPhoto($image);
      }
      $r = $this->core->Change([[
       "[Conversation.CRID]" => md5($id),
       "[Conversation.CRIDE]" => base64_encode(md5($id)),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]"),
       "[Member.Actions]" => $actions,
       "[Member.AddContact]" => $addContact,
       "[Member.Albums]" => $albums,
       "[Member.Articles]" => $articles,
       "[Member.Blogs]" => $blogs,
       "[Member.Back]" => $back,
       "[Member.ChangeRank]" => $changeRank,
       "[Member.CoverPhoto]" => $_Member["ListItem"]["CoverPhoto"],
       "[Member.CoverPhotos]" => json_encode($newCoverPhotos, true),
       "[Member.CoverPhotos.DisableSlideShow]" => $coverPhotosSlideShowDisabled,
       "[Member.Contacts]" => $contacts,
       "[Member.Description]" => $_Member["ListItem"]["Description"],
       "[Member.DisplayName]" => $displayName.$verified,
       "[Member.Footer]" => $this->core->Extension("a095e689f81ac28068b4bf426b871f71"),
       "[Member.ID]" => md5($id),
       "[Member.Journal]" => $journal,
       "[Member.Nominate]" => base64_encode("v=".base64_encode("Congress:Nominate")."&Username=".base64_encode($id)),
       "[Member.ProfilePicture]" => $options["ProfilePicture"],
       "[Member.Share]" => $share,
       "[Member.Stream]" => base64_encode("v=$search&UN=".base64_encode($id)."&st=MBR-SU"),
       "[Member.Username]" => $id,
       "[Member.Votes]" => $options["Vote"]
      ], $this->core->Extension("72f902ad0530ad7ed5431dac7c5f9576")]);
     }
    }
   }
   $r = ($card == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    if($this->core->ID == $you) {
     $r = $this->view(base64_encode("WebUI:Gateway"), []);
     $r = $this->core->RenderView($r);
    }
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => $addTopMargin,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "Title" => $_ViewTitle
   ]);
  }
  function MakeVIP(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
   $manifest = [];
   $r = [
    "Body" => "The Member Identifier is missing."
   ];
   $_ResponseType = "Dialog";
   $y = $this->you;
   if(!empty($data["ID"])) {
    $t = base64_decode($data["ID"]);
    $t = ($t == $y["Login"]["Username"]) ? $y : $this->core->Member($t);
    $display = $t["Personal"]["DisplayName"];
    $r = [
     "Body" => "$display is already a VIP Member."
    ];
    if($t["Subscriptions"]["VIP"]["A"] == 0) {
     $_VIPForum = "cb3e432f76b38eaa66c7269d658bd7ea";
     $accessCode = "Accepted";
     $t["Points"] = $t["Points"] + 1000000;
     $manifest = $this->core->Data("Get", ["pfmanifest", $_VIPForum]);
     array_push($manifest, [$t["Login"]["Username"] => "Member"]);
     foreach($t["Subscriptions"] as $key => $value) {
      if(!in_array($key, ["Artist", "Developer"])) {
       $t["Subscriptions"][$key] = [
        "A" => 1,
        "B" => $this->core->timestamp,
        "E" => $this->core->TimePlus($this->core->timestamp, 1, "month")
       ];
      }
     }
     $this->core->Data("Save", ["pfmanifest", $_VIPForum, $manifest]);
     $this->core->Data("Save", ["mbr", md5($t["Login"]["Username"]), $t]);
     $r = [
      "Body" => "$display is now a VIP Member.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => $manifest,
     "Web" => $r
    ],
    "ResponseType" => $_ResponseType
   ]);
  }
  function MarkBulletinAsRead(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
   $y = $this->you;
   $bulletins = $this->core->Data("Get", ["bulletins", md5($y["Login"]["Username"])]) ?? [];
   if(!empty($data["ID"])) {
    foreach($bulletins as $key => $value) {
     if($data["ID"] == $key) {
      $bulletin = $value;
      $bulletin["Read"] = 1;
      $bulletins[$key] = $bulletin;
     }
    }
   }
   $this->core->Data("Save", [
    "bulletins",
    md5($y["Login"]["Username"]),
    $bulletins
   ]);
   return json_encode($bulletins);
  }
  function NewPassword(array $a) {
   $accessCode = "Denied";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Error"
    ];
   } else {
    $accessCode = "Accepted";
    $r = [
     "Front" => $this->core->Change([[
      "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
      "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
      "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePassword")),
      "[Member.Username]" => $y["Login"]["Username"]
     ], $this->core->Extension("08302aec8e47d816ea0b3f80ad87503c")])
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function NewPIN(array $a) {
   $accessCode = "Denied";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue."
    ];
   } else {
    $accessCode = "Accepted";
    $r = [
     "Front" => $this->core->Change([[
      "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
      "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
      "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePIN"))
     ], $this->core->Extension("867bd8480f46eea8cc3d2a2ed66590b7")])
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Preferences(array $a) {
   $accessCode = "Denied";
   $addTopMargin = 1;
   $data = $a["Data"] ?? [];
   $minAge = $this->core->config["minRegAge"] ?? 13;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($y["Personal"]["Age"] < $minAge) {
    $r = [
     "Body" => "As a security measure, you must be aged $minAge or older in order to take full control of your profile and absolve yourself of your parent account.",
     "Header" => "Not of Age"
    ];
   } else {
    $accessCode = "Accepted";
    $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
    $r = $this->core->RenderView($r);
    $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
    if($verifyPassPhrase == 1) {
     $accessCode = "Denied";
     $key = $data["Key"] ?? base64_encode("");
     $key = base64_decode($key);
     $r = $this->core->Element(["p", "The Key is missing."]);
     $secureKey = $data["SecureKey"] ?? base64_encode("");
     $secureKey = base64_decode($secureKey);
     if(md5($key) != $secureKey) {
      $r = $this->core->Element(["p", "The Keys do not match."]);
     } else {
      $_LiveView = base64_encode("v=".base64_encode("LiveView:Editor")."&MediaType=".base64_encode("CoverPhoto")."&Media=");
      $_SymbolicLink = "v=".base64_encode("Search:Containers")."&AddTo=".base64_encode("Attach:.AddTo[Clone.ID]")."&CARD=1&lPG=Files&st=XFS&UN=".base64_encode($you)."&ftype=".base64_encode(json_encode(["Photo"]));
      $accessCode = "Accepted";
      $addTopMargin = "0";
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
      $r = $this->core->Change([[
       "[Preferences.Birthday.Month]" => $y["Personal"]["Birthday"]["Month"],
       "[Preferences.Birthday.Months]" => json_encode($birthMonths, true),
       "[Preferences.Birthday.Year]" => $y["Personal"]["Birthday"]["Year"],
       "[Preferences.Birthday.Years]" => json_encode($birthYears, true),
       "[Preferences.Deactivate]" => base64_encode("v=".base64_encode("Profile:Deactivate")),
       "[Preferences.Donations.Patreon]" => base64_encode($y["Donations"]["Patreon"]),
       "[Preferences.Donations.PayPal]" => base64_encode($y["Donations"]["PayPal"]),
       "[Preferences.Donations.SubscribeStar]" => base64_encode($y["Donations"]["SubscribeStar"]),
       "[Preferences.General.Name]" => base64_encode($y["Personal"]["FirstName"]),
       "[Preferences.General.Description]" => base64_encode($y["Personal"]["Description"]),
       "[Preferences.General.DisplayName]" => base64_encode($y["Personal"]["DisplayName"]),
       "[Preferences.General.Email]" => base64_encode($y["Personal"]["Email"]),
       "[Preferences.General.Gender]" => $y["Personal"]["Gender"],
       "[Preferences.General.LastPasswordChange]" => $lastPasswordChange,
       "[Preferences.General.OnlineStatus]" => $y["Activity"]["OnlineStatus"],
       "[Preferences.General.RelationshipStatus]" => $y["Personal"]["RelationshipStatus"],
       "[Preferences.General.RelationshipWith]" => base64_encode($relationshipWith),
       "[Preferences.ID]" => $id,
       "[Preferences.Links.EditShop]" => base64_encode("v=".base64_encode("Shop:Edit")."&Shop=".base64_encode(md5($you))),
       "[Preferences.Links.NewPassword]" => base64_encode("v=".base64_encode("Profile:NewPassword")),
       "[Preferences.Links.NewPIN]" => base64_encode("v=".base64_encode("Profile:NewPIN")),
       "[Preferences.Personal.AutoResponse]" => base64_encode($autoResponse),
       "[Preferences.Personal.CoverPhotoSelection]" => $coverPhotosSelection,
       "[Preferences.Personal.CoverPhotos]" => $coverPhotosList,
       "[Preferences.Personal.CoverPhotos.Clone]" => base64_encode($this->core->Element([
        "div", $this->core->Element(["button", "X", [
         "class" => "Delete v1",
         "data-target" => ".CoverPhotos[Clone.ID]"
        ]]).$this->core->Element([
         "div", $this->core->Change([[
          "[Media.Add]" => base64_encode($_SymbolicLink),
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
       "[Preferences.Personal.Electable]" => $chooseElectable,
       "[Preferences.Personal.MinimalDesign]" => $chooseMinimalDesign,
       "[Preferences.Personal.UIVariant]" => $setUIVariant,
       "[Preferences.Personal.UIVariants]" => $this->core->Extension("4d3675248e05b4672863c6a7fd1df770"),
       "[Preferences.Privacy.Albums]" => $y["Privacy"]["Albums"],
       "[Preferences.Privacy.Archive]" => $y["Privacy"]["Archive"],
       "[Preferences.Privacy.Articles]" => $y["Privacy"]["Articles"],
       "[Preferences.Privacy.Comments]" => $y["Privacy"]["Comments"],
       "[Preferences.Privacy.ContactInfo]" => $y["Privacy"]["ContactInfo"],
       "[Preferences.Privacy.ContactInfoDonate]" => $y["Privacy"]["ContactInfoDonate"],
       "[Preferences.Privacy.ContactInfoEmails]" => $y["Privacy"]["ContactInfoEmails"],
       "[Preferences.Privacy.ContactRequests]" => $y["Privacy"]["ContactRequests"],
       "[Preferences.Privacy.Contacts]" => $y["Privacy"]["Contacts"],
       "[Preferences.Privacy.Contributions]" => $y["Privacy"]["Contributions"],
       "[Preferences.Privacy.DLL]" => $y["Privacy"]["DLL"],
       "[Preferences.Privacy.ForumsType]" => $y["Privacy"]["ForumsType"],
       "[Preferences.Privacy.NonEssentialCommunications]" => 0,//TEMP
       "[Preferences.Privacy.Gender]" => $y["Privacy"]["Gender"],
       "[Preferences.Privacy.Journal]" => $y["Privacy"]["Journal"],
       "[Preferences.Privacy.LastActivity]" => $y["Privacy"]["LastActivity"],
       "[Preferences.Privacy.LookMeUp]" => $y["Privacy"]["LookMeUp"],
       "[Preferences.Privacy.MSG]" => $y["Privacy"]["MSG"],
       "[Preferences.Privacy.NSFW]" => $y["Privacy"]["NSFW"],
       "[Preferences.Privacy.OnlineStatus]" => $y["Privacy"]["OnlineStatus"],
       "[Preferences.Privacy.Polls]" => $polls,
       "[Preferences.Privacy.Posts]" => $y["Privacy"]["Posts"],
       "[Preferences.Privacy.Products]" => $y["Privacy"]["Products"],
       "[Preferences.Privacy.Profile]" => $y["Privacy"]["Profile"],
       "[Preferences.Privacy.Registered]" => $y["Privacy"]["Registered"],
       "[Preferences.Privacy.RelationshipStatus]" => $y["Privacy"]["RelationshipStatus"],
       "[Preferences.Privacy.RelationshipWith]" => $y["Privacy"]["RelationshipWith"],
       "[Preferences.Privacy.Shop]" => $y["Privacy"]["Shop"],
       "[Preferences.Purge]" => base64_encode("v=".base64_encode("Profile:Purge")),
       "[Preferences.Save]" => base64_encode("v=".base64_encode("Profile:Save")),
       "[Preferences.Security.PassPhrase]" => base64_encode($passPhrase),
       "[Preferences.Security.RequirePassword]" => $passwordOnSignIn
      ], $this->core->Extension("e54cb66a338c9dfdcf0afa2fec3b6d8a")]);
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => $addTopMargin,
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
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } else {
    $accessCode = "Accepted";
    $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
    $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
    $r = $this->core->RenderView($r);
    if($verifyPassPhrase == 1) {
     $accessCode = "Denied";
     $key = $data["Key"] ?? base64_encode("");
     $key = base64_decode($key);
     $r = $this->core->Element(["p", "The Key is missing."]);
     $secureKey = $data["SecureKey"] ?? base64_encode("");
     $secureKey = base64_decode($secureKey);
     if(md5($key) != $secureKey) {
      $r = $this->core->Element(["p", "The Keys do not match."]);
     } else {
      $accessCode = "Accepted";
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
      $r = $this->view(base64_encode("WebUI:Gateway"), []);
      $r = $this->core->Element([
       "div", $this->core->Element([
        "h3", "Success!", ["class" => "CenterText UpperCase"]
       ]).$this->core->Element([
        "p", "Your profile is now slated for purging. We hope to see you again!",
        ["class" => "CenterText"]
       ]), ["class" => "Red RoundedLarge Shadowed"]
      ]).$this->core->RenderView($r);
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
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
   $email = $data["Personal_Email"] ?? "";
   $emailIsTaken = 0;
   $header = "Error";
   $members = $this->core->DatabaseSet("Member");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $_UIVariant = $y["Personal"]["UIVariant"] ?? 0;
   foreach($members as $key => $value) {
    $value = str_replace("nyc.outerhaven.mbr.", "", $value);
    $member = $this->core->Data("Get", ["mbr", $value]) ?? [];
    $ck = ($member["Login"]["Username"] != $you) ? 1 : 0;
    $ck2 = ($email == $member["Personal"]["Email"]) ? 1 : 0;
    if($ck == 1 && $ck2 == 1) {
     $emailIsTaken++;
    }
   } if(empty($data["Personal_DisplayName"])) {
    $r = "Your Display Name is missing.";
   } elseif(empty($email)) {
    $r = "Your E-Mail is missing.";
   } elseif($emailIsTaken > 0) {
    $r = "Another Member is already using <em>$email</em>.";
   } elseif($this->core->ID == $you) {
    $r = "You must be signed in to continue.";
   } else {
    $accessCode = "Accepted";
    $coverPhotos = [];
    $coverPhotosData = $data["CoverPhotos"] ?? [];
    $header = "Done";
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
    $this->core->Data("Save", ["mbr", md5($you), $newMember]);
    $r = "Your Preferences were saved!";
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => [
      "Body" => $r,
      "Header" => $header
     ]
    ],
    "ResponseType" => "Dialog",
    "SetUIVariant" => $_UIVariant
   ]);
  }
  function SavePassword(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "CurrentPassword",
    "NewPassword",
    "NewPassword2"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(empty($data["CurrentPassword"])) {
    $r = [
     "Body" => "You must enter your current Password."
    ];
   } elseif(empty($data["NewPassword"]) || empty($data["NewPassword2"])) {
    $r = [
     "Body" => "You must enter and confirm your new Password."
    ];
   } elseif(md5($data["CurrentPassword"]) != $y["Login"]["Password"]) {
    $r = [
     "Body" => "The Passwords do not match."
    ];
   } elseif($data["NewPassword"] != $data["NewPassword2"]) {
    $r = [
     "Body" => "The new Passwords do not match.",
     "Header" => "Error"
    ];
   } else {
    $accessCode = "Accepted";
    $y["Activity"]["LastPasswordChange"] = $this->core->timestamp;
    $y["Login"]["Password"] = md5($data["NewPassword"]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "Your Password has been updated.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function SavePIN(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "CurrentPIN",
    "NewPIN",
    "NewPIN2"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(empty($data["CurrentPIN"])) {
    $r = [
     "Body" => "You must enter your current PIN."
    ];
   } elseif(empty($data["NewPIN"]) || empty($data["NewPIN2"])) {
    $r = [
     "Body" => "You must enter and confirm your new PIN."
    ];
   } elseif(!is_numeric($data["NewPIN"]) || !is_numeric($data["NewPIN2"])) {
    $r = [
     "Body" => "PINs must be numeric (0-9)."
    ];
   } elseif(md5($data["CurrentPIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($data["NewPIN"] != $data["NewPIN2"]) {
    $r = [
     "Body" => "The new PINs do not match."
    ];
   } else {
    $accessCode = "Accepted";
    $y["Login"]["PIN"] = md5($data["NewPIN"]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "Your PIN has been updated.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function SignIn(array $a) {
   $_AddTopMargin = "0";
   $_Commands = [];
   $_ResponseType = "GoToView";
   $_View = "";
   $data = $a["Data"] ?? [];
   $parentView = $viewData["ParentView"] ?? base64_encode("SignIn");
   $step = $data["Step"] ?? base64_encode(1);
   $step = base64_decode($step);
   if($step == 2) {
    $_AddTopMargin = "1";
    $_View = [
     "ChangeData" => [
      "[Error.Text]" => "We could not find the username you entered.",
      "[Error.ParentView]" => base64_decode($parentView)
     ],
     "ExtensionID" => "45787465-6e73-496f-ae42-794d696b65-67ac610803c33"
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
     $data = [];
     $member = $member["DataModel"];
     $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
     $viewData = json_decode(base64_decode($viewData), true);
     $viewData["Step"] = base64_encode(3);
     $viewData["Username"] = $this->core->AESencrypt($member["Login"]["Username"]);
     $data["Email"] = base64_encode($member["Personal"]["Email"]);
     $data["ParentView"] = $parentView;
     $data["ReturnView"] = base64_encode(base64_encode("Profile:SignIn"));
     $data["ViewData"] = base64_encode(json_encode($viewData));
     $_View = $this->view(base64_encode("WebUI:TwoFactorAuthentication"), ["Data" => $data]);
     $_View = $this->core->RenderView($_View);
    }
   } elseif($step == 3) {
    $_AddTopMargin = "1";
    $_View = [
     "ChangeData" => [
      "[Error.Text]" => "We could not find the username you entered.",
      "[Error.ParentView]" => base64_decode($parentView)
     ],
     "ExtensionID" => "45787465-6e73-496f-ae42-794d696b65-67ac610803c33"
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
      $_ResponseType = "View";
      #$_View = ["ChangeData"=>[],"Extension"=>""];
      $_View = "";
     }
    }
   } else {
    $_ResponseType = "View";
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
  function SignUp(array $data) {
   $_AddTopMargin = "0";
   $_Card = "";
   $_Dialog = "";
   $_MinimumAge = $this->core->config["minRegAge"] ?? 13;
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
    $ck = ($age > $_MinimumAge) ? 1 : 0;
    $email = $data["Email"] ?? "";
    $gender = $data["Gender"] ?? "Male";
    $name = $data["Name"] ?? "John";
    $i = 0;
    $members = $this->core->DatabaseSet("Member");
    $password = $data["Password"] ?? "";
    $password2 = $data["Password2"] ?? "";
    $pin = $data["PIN"] ?? "";
    $pin2 = $data["PIN2"] ?? "";
    $r = "Internal Error";
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
     $r = "A valid Email address is required.";
    } elseif(empty($password)) {
     $r = "A Password is required.";
    } elseif($password != $password2) {
     $r = "Your Passwords must match.";
    } elseif(empty($pin)) {
     $r = "A PIN is required.";
    } elseif(!is_numeric($pin) || !is_numeric($pin2)) {
     $r = "Your PINs must be numeric.";
    } elseif($pin != $pin2) {
     $r = "Your PINs must match.";
    } elseif(empty($username)) {
     $r = "A Username is required.";
    } elseif(!preg_match("/^[a-zA-Z0-9-_]+$/", $username)) {
     $r = "Usernames may only contain letters, numbers, hyphens (-), and underscores (_).";
    } elseif(strpos($username, "Ghost_")) {
     $r = "You cannot be a ghost.";
    } elseif($username == $this->core->ID) {
     $r = $this->core->ID." is the system profile and cannot be used.";
    } elseif($ck == 0) {
     $r = "You must be $_MinimumAge or older to sign up.";
    } elseif($i > 0) {
     $r = "The Username <em>$username</em> is already in use.";
    } else {
     $accessCode = "Accepted";
     $data = [];
     $data["Email"] = base64_encode($email);
     $data["ReturnView"] = base64_encode(base64_encode("Profile:SignUp"));
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
     $data["ViewData"] = base64_encode(json_encode($viewData));
     $r = $this->view(base64_encode("WebUI:TwoFactorAuthentication"), ["Data" => $data]);
     $r = $this->core->RenderView($r);
    } if($accessCode == "Denied") {
     $r = $this->core->Change([[
      "[Error.Message]" => $r,
      "[Error.ParentView]" => "SignUp"
     ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-67ac610803c33")]);
    }
   } elseif($step == 3) {
    $_AccessCode = "Denied";
    $_AddTopMargin = "1";
    $birthMonth = $data["BirthMonth"] ?? base64_encode(10);
    $birthMonth = base64_decode($birthMonth);
    $birthYear = $data["BirthYear"] ?? base64_encode(1995);
    $birthYear = base64_decode($birthYear);
    $age = date("Y") - $birthYear;
    $ck = ($age > $_MinimumAge) ? 1 : 0;
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
     $r = "A valid Email address is required.";
    } if($i > 0) {
     $r = "The Username <em>$username</em> is already in use.";
    } else {
     $accessCode = "Accepted";
     $this->core->Data("Save", ["cms", $usernameID, [
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
     $this->core->Statistic("New Member");
     $r = $this->core->Change([[
      "[Success.SignIn]" => base64_encode("v=".base64_encode("Profile:SignIn")),
      "[Success.Username]" => $username
     ], $this->core->Extension("872fd40c7c349bf7220293f3eb64ab45")]);
    } if($accessCode == "Denied") {
     $r = $this->core->Change([[
      "[Error.Message]" => $r,
      "[Error.ParentView]" => "SignUp"
     ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-67ac610803c33")]);
    }
   } else {
    $_ResponseType = "View";
    $birthMonths = [];
    $birthYears = [];
    for($i = 1; $i <= 12; $i++) {
     $birthMonths[$i] = $i;
    } for($i = 1776; $i <= (date("Y") - $_MinimumAge); $i++) {
     $birthYears[$i] = $i;
    }
    $r = $this->core->Change([[
     "[SignUp.BirthMonths]" => json_encode($birthMonths, true),
     "[SignUp.BirthYears]" => json_encode($birthYears, true),
     "[SignUp.MinimumAge]" => $this->core->config["minAge"],
     "[SignUp.ParentView]" => "MainView",
     "[SignUp.Processor]" => base64_encode("v=".base64_encode("Profile:SignUp")."&Step=".base64_encode(2)),
     "[SignUp.ReturnView]" => base64_encode("Profile:SignUp"),
     "[SignUp.ViewData]" => base64_encode(json_encode([
      "Step" => base64_encode(3)
     ], true))
    ], $this->core->Extension("c48eb7cf715c4e41e2fb62bdfa60f198")]);
   }
   $_Dialog = [
    "Body" => "This experience is temporarily down while we perform a whole-of-platform update regarding server response data.",
    "Header" => "Sign Up"
   ];
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "Card" => $_Card,
    "Dialog" => $_Dialog,
    "AddTopMargin" => $_AddTopMargin,
    "ResponseType" => $_ResponseType,
    "View" => ""
    #"View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
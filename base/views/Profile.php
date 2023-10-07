<?php
 Class Profile extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function BulletinCenter(array $a) {
   $accessCode = "Accepted";
   $search = base64_encode("Search:Containers");
   $r = $this->core->Change([[
    "[BulletinCenter.Bulletins]" => base64_encode("v=$search&st=Bulletins"),
    "[BulletinCenter.ContactRequests]" => base64_encode("v=".base64_encode("Profile:BulletinsList")."&type=".base64_encode("ContactsRequests")),
    "[BulletinCenter.Contacts]" => base64_encode("v=$search&Chat=0&st=ContactsChatList")
   ], $this->core->Page("6cbe240071d79ac32edbe98679fcad39")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
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
   } elseif($type == "NewBlogPost") {
    $message = "Posted to their blog.";
   } elseif($type == "NewJob") {
    $message = "Requested a Service.";
   } elseif($type == "NewProduct") {
    $message = "Added a product to their shop.";
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
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
     $page = $this->core->Data("Get", ["pg", $data["ArticleID"]]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$page["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead OpenCard v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("BlogPost:Home")."&CARD=1&ID=".$data["ArticleID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
     $r = "Button";
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
    } elseif($type == "NewBlogPost") {
     $post = $this->core->Data("Get", ["bp", $data["PostID"]]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$post["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("BlogPost:Home")."&CARD=1&Blog=".$data["BlogID"]."&Post=".$data["PostID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
    } elseif($type == "NewProduct") {
     $product = $this->core->Data("Get", [
      "miny",
      $data["ProductID"]
     ]) ?? [];
     $r = $this->core->Element([
      "button", "Take me to <em>".$product["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=".$product["ID"]."&UN=".$data["ShopID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
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
  function Bulletins(array $a) {
   $accessCode = "Denied";
   $r = [];
   $tpl = $this->core->Page("ae30582e627bc060926cfacf206920ce");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID != $you) {
    $accessCode = "Accepted";
    $bulletins = $this->core->Data("Get", [
     "bulletins",
     md5($you)
    ]) ?? [];
    if(!empty($bulletins)) {
     foreach($bulletins as $key => $value) {
      if($value["Seen"] == 0) {
       $bulletins[$key]["Seen"] = 1;
       $value["ID"] = $key;
       $t = $this->core->Member($value["From"]);
       $pic = $this->core->ProfilePicture($t, "margin:5%;width:90%");
       $message = $this->view(base64_encode("Profile:BulletinMessage"), [
        "Data" => $value
       ]);
       $options = $this->view(base64_encode("Profile:BulletinOptions"), [
        "Data" => [
         "Bulletin" => base64_encode(json_encode($value))
        ]
       ]);
       array_push($r, [
        "Data" => $value["Data"],
        "Date" => $this->core->TimeAgo($value["Sent"]),
        "From" => $t["Personal"]["DisplayName"],
        "ID" => $key,
        "Message" => $this->core->RenderView($message),
        "Options" => $this->core->RenderView($options),
        "Picture" => $pic
       ]);
      }
     }
     $this->core->Data("Save", ["bulletins", md5($you), $bulletins]);
    }
   }
   return $this->core->JSONResponse([
    $accessCode,
    base64_encode(json_encode($r, true)),
    base64_encode($tpl)
   ]);
  }
  function BulletinsList(array $a) {
   $data = $a["Data"] ?? [];
   $search = base64_encode("Search:Containers");
   $type = $data["type"] ?? base64_encode("");
   $type = base64_decode($type);
   if($type == "ContactsRequests") {
    $r = $this->view($search, ["Data" => [
     "st" => "ContactsRequests"
    ]]);
    $r = $this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => "Accepted",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function ChangeRank(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["PIN", "Rank", "Username"]);
   $r = [
    "Body" => "The Member Identifier or Rank are missing."
   ];
   $rank = $data["Rank"];
   $responseType = "Dialog";
   $username = $data["Username"];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => $this->core->Element(["p", "The PINs do not match."]),
    ];
   } elseif(!empty($rank) && !empty($username)) {
    $accessCode = "Accepted";
    $member = $this->core->Member($username);
    $responseType = "ReplaceContent";
    $member["Rank"] = md5($rank);
    $this->core->Data("Save", ["mbr", md5($username), $member]);
    $r = $this->core->Element([
     "h3", "Success", ["class" => "CenterText UpperCase"]
    ]).$this->core->Element([
     "p", $member["Personal"]["DisplayName"]."'s Rank within <em>Outer Haven</em> was Changed to $rank.",
     ["class" => "CenterText"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType,
    "Success" => "CloseDialog"
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
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $_ViewTitle = $this->core->config["App"]["Name"];
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "CARD",
    "UN",
    "b2",
    "lPG"
   ]);
   $b2 = $data["b2"];
   $back = $data["back"] ?? 0;
   $back = ($back == 1) ? $this->core->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI head",
    "data-type" => $data["lPG"]
   ]]) : "";
   $pub = $data["pub"] ?? 0;
   $t = $this->core->Member(base64_decode($data["UN"]));
   $id = $t["Login"]["Username"];
   $display = ($id == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
   $r = [
    "Body" => "The requested Member could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_theirContacts = $this->core->Data("Get", ["cms", md5($id)]) ?? [];
    $_theyBlockedYou = $this->core->CheckBlocked([$t, "Members", $you]);
    $_youBlockedThem = $this->core->CheckBlocked([$y, "Members", $id]);
    $b2 = ($id == $you) ? "Your Profile" : $t["Personal"]["DisplayName"]."'s Profile";
    $lpg = "Profile".md5($id);
    $privacy = $t["Privacy"] ?? [];
    $ck = ($id == $you) ? 1 : 0;
    $ck2 = ($privacy["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->config["minAge"])) ? 1 : 0;
    $ckart = 0;
    $public = md5("Public");
    $r = [
     "Body" => "The Member may have reduced their visibility.",
     "Header" => "Not Found"
    ];
    $search = base64_encode("Search:Containers");
    $theirContacts = $_theirContacts["Contacts"] ?? [];
    $theirRequests = $_theirContacts["Requests"] ?? [];
    $visible = $this->core->CheckPrivacy([
     "Contacts" => $theirContacts,
     "Privacy" => $privacy["Profile"],
     "UN" => $id,
     "Y" => $you
    ]);
    if($_theyBlockedYou == 0 && ($ck == 1 || $ck2 == 1 || $visible == 1)) {
     $accessCode = "Accepted";
     $_Artist = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $_Block = ($_youBlockedThem == 0) ? "B" : "U";
     $_BlockText = ($_youBlockedThem == 0) ? "Block" : "Unblock";
     $_ViewTitle = $t["Personal"]["DisplayName"]." @ ".$_ViewTitle;
     $_VIP = $t["Subscriptions"]["VIP"]["A"];
     $actions = $this->core->Element(["button", $_BlockText, [
      "class" => "BLK Small v2",
      "data-cmd" => base64_encode($_Block),
      "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($display)."&content=".base64_encode($id)."&list=".base64_encode("Members")."&BC=")
     ]]);
     $actions .= ($_Artist == 1) ? $this->core->Element(["button", "Donate", [
      "class" => "OpenCardSmall v2",
      "data-view" => base64_encode("v=".base64_encode("Profile:Donate")."&UN=".base64_encode($id))
     ]]) : "";
     $actions .= ($_VIP == 0 && $id != $you && $y["Rank"] == md5("High Command")) ? $this->core->Element(["button", "Make VIP", [
      "class" => "SendData Small v2",
      "data-form" => ".Profile$id",
      "data-processor" => base64_encode("v=".base64_encode("Profile:MakeVIP")."&ID=".base64_encode($id))
     ]]) : "";
     $actions .= $this->core->Element(["button", "Message", [
      "class" => "CloseCard OpenFirSTEPTool Small v2",
      "data-fst" => base64_encode("v=".base64_encode("Chat:Home")."&GroupChat=0&to=".base64_encode($id))
     ]]);
     $actions = ($id != $you) ? $actions : "";
     $addContact = "";
     $albums = $this->core->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Forbidden",
      "[Error.Message]" => "$display keeps their media albums to themselves."
     ], $this->core->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
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
      "[Error.Message]" => "$display keeps their archive contributions to themselves."
     ], $this->core->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
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
      "[Error.Message]" => "$display keeps their blogs to themselves."
     ], $this->core->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
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
      "[Error.Message]" => "$display keeps their contacts to themselves."
     ], $this->core->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
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
      "Privacy" => $t["Privacy"]["ContactRequests"],
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
        $addContact = $this->core->Element([
         "div", $this->core->Element(["button", "Accept", [
          "class" => "BB BBB SendData v2 v2w",
          "data-form" => ".ContactRequest$id",
          "data-processor" => base64_encode("v=".base64_encode("Contact:Requests")."&accept=1")
         ]]), ["class" => "Desktop50"]
        ]).$this->core->Element([
         "div", $this->core->Element(["button", "Decline", [
          "class" => "BB SendData v2 v2w",
          "data-form" => ".ContactRequest$id",
          "data-processor" => base64_encode("v=".base64_encode("Contact:Requests")."&decline=1")
         ]]), ["class" => "Desktop50"]
        ]);
       } if($cancel == 1 || $contactStatus["YouRequested"] > 0) {
        $addContact = $this->core->Change([[
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
        ], $this->core->Page("a73ffa3f28267098851bf3550eaa9a02")]);
       } else {
        $addContact = $this->core->Change([[
         "[ContactRequest.Header]" => "Add $display",
         "[ContactRequest.ID]" => $id,
         "[ContactRequest.Option]" => $this->core->Element([
          "button", "Add $display", [
           "class" => "BB SendData v2 v2w",
           "data-form" => ".ContactRequest$id",
           "data-processor" => base64_encode("v=".base64_encode("Contact:Requests"))
          ]
         ]),
         "[ContactRequest.Text]" => "Send $display a Contact Request.",
         "[ContactRequest.Username]" => $id
        ], $this->core->Page("a73ffa3f28267098851bf3550eaa9a02")]);
       }
      }
      $addContact = ($you != $this->core->ID) ? $addContact : "";
     } if($id != $you && $y["Rank"] == md5("High Command") || $y["Rank"] == md5("Partner")) {
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
       "[Ranks.Authentication]" => "v=".base64_encode("Authentication:AuthorizeChange")."&Form=".base64_encode(".MemberRank".md5($id))."&ID=".md5($id)."&Processor=".base64_encode("v=".base64_encode("Profile:ChangeRank"))."&Text=".base64_encode("Do you authorize the Change of $display's rank?"),
       "[Ranks.DisplayName]" => $display,
       "[Ranks.ID]" => md5($id),
       "[Ranks.Options]" => json_encode($ranks, true),
       "[Ranks.Username]" => $id,
       "[Ranks.YourRank]" => $y["Rank"]
      ], $this->core->Page("914dd9428c38eecf503e3a5dda861559")]);
     }
     $gender = $t["Personal"]["Gender"] ?? "Male";
     $gender = $this->core->Gender($gender);
     $description = "You have not added a Description.";
     $description = ($id != $you) ? "$display has not added a Description." : $description;
     $description = (!empty($t["Personal"]["Description"])) ? $this->core->PlainText([
      "BBCodes" => 1,
      "Data" => $t["Personal"]["Description"],
      "Display" => 1
     ]) : $description;
     $journal = $this->core->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Forbidden",
      "[Error.Message]" => "$display keeps their Journal to themselves."
     ], $this->core->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
     if($ck == 1 || $privacy["Journal"] == $public || $visible == 1) {
      $journal = $this->view($search, ["Data" => [
       "UN" => base64_encode($id),
       "b2" => $b2,
       "lPG" => $lpg,
       "st" => "MBR-JE"
      ]]);
      $journal = $this->core->RenderView($journal);
     }
     $share = ($id == $you || $t["Privacy"]["Profile"] == md5("Public")) ? 1 : 0;
     $share = ($share == 1) ? $this->core->Element([
      "button", "Share", [
       "class" => "OpenCard Small v2",
       "data-view" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($id)."&Type=".base64_encode("Profile")."&Username=".base64_encode($id))
      ]
     ]) : "";
     $votes = ($ck == 0) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $r = $this->core->Change([[
      "[Member.Actions]" => $actions,
      "[Member.AddContact]" => $addContact,
      "[Member.Albums]" => $albums,
      "[Member.Articles]" => $articles,
      "[Member.Blogs]" => $blogs,
      "[Member.Back]" => $back,
      "[Member.ChangeRank]" => $changeRank,
      "[Member.CoverPhoto]" => $this->core->CoverPhoto($t["Personal"]["CoverPhoto"]),
      "[Member.Contacts]" => $contacts,
      "[Member.Conversation]" => $this->core->Change([[
       "[Conversation.CRID]" => md5($id),
       "[Conversation.CRIDE]" => base64_encode(md5($id)),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->core->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[Member.Description]" => $description,
      "[Member.DisplayName]" => $display,
      "[Member.Footer]" => $this->core->Page("a095e689f81ac28068b4bf426b871f71"),
      "[Member.ID]" => md5($id),
      "[Member.Journal]" => $journal,
      "[Member.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:2em;width:calc(100% - 4em)"),
      "[Member.Share]" => $share,
      "[Member.Stream]" => base64_encode("v=$search&UN=".base64_encode($id)."&st=MBR-SU"),
      "[Member.Votes]" => base64_encode("v=$votes&ID=".md5($id)."&Type=4")
     ], $this->core->Page("72f902ad0530ad7ed5431dac7c5f9576")]);
    }
   }
   $r = ($data["CARD"] == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    if($this->core->ID == $you) {
     $r = $this->view(base64_encode("WebUI:OptIn"), []);
     $r = $this->core->RenderView($r);
    }
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
   $responseType = "Dialog";
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
     $manifest = $this->core->Data("Get", ["pfmanifest", $_VIPForum]) ?? [];
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
     $this->core->Data("Save", [
      "mbr",
      md5($t["Login"]["Username"]),
      $t
     ]);
     $r = [
      "Body" => "$display is now a VIP Member.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => $manifest,
     "Web" => $r
    ],
    "ResponseType" => $responseType
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
    $r = $this->core->Change([[
     "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
     "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePassword")),
     "[Member.Username]" => $y["Login"]["Username"]
    ], $this->core->Page("08302aec8e47d816ea0b3f80ad87503c")]);
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
    $r = $this->core->Change([[
     "[Member.ProfilePicture]" => $this->core->ProfilePicture($y, "margin:5%;width:90%"),
     "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePIN"))
    ], $this->core->Page("867bd8480f46eea8cc3d2a2ed66590b7")]);
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
  function Preferences(array $a) {
   $accessCode = "Denied";
   $action = "";
   $minAge = $this->core->config["minRegAge"] ?? 13;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $ck = ($y["Personal"]["Age"] >= $minAge) ? 1 : 0;
   $ck2 = ($this->core->ID != $you) ? 1 : 0;
   if($ck == 0) {
    $r = [
     "Body" => "As a security measure, you must be aged $minAge or older in order to take full control of your profile and absolve yourself of your parent account.",
     "Header" => "Not of Age"
    ];
   } elseif($ck2 == 0) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($ck == 1 && $ck2 == 1) {
    $accessCode = "Accepted";
    $id = md5($you);
    $action = $this->core->Element(["button", "Save", [
     "class" => "CardButton OpenDialog",
     "data-view" => base64_encode("v=".base64_encode("Authentication:AuthorizeChange")."&Form=".base64_encode(".Preferences$id")."&ID=$id&Processor=".base64_encode("v=".base64_encode("Profile:Save"))."&Text=".base64_encode("Are you sure you want to update your preferences?"))
    ]]);
    $birthMonths = [];
    $birthYears = [];
    $choseMinimalDesign = $y["Personal"]["MinimalDesign"] ?? "";
    $choseMinimalDesign = (!empty($choseMinimalDesign)) ? 1 : 0;
    $relationshipWith = $y["Personal"]["RelationshipWith"] ?? "";
    for($i = 1; $i <= 12; $i++) {
     $birthMonths[$i] = $i;
    } for($i = 1776; $i <= date("Y"); $i++) {
     $birthYears[$i] = $i;
    }
    $r = $this->core->Change([[
     "[Preferences.Birthday.Month]" => $y["Personal"]["Birthday"]["Month"],
     "[Preferences.Birthday.Months]" => json_encode($birthMonths, true),
     "[Preferences.Birthday.Year]" => $y["Personal"]["Birthday"]["Year"],
     "[Preferences.Birthday.Years]" => json_encode($birthYears, true),
     "[Preferences.Donations.Patreon]" => base64_encode($y["Donations"]["Patreon"]),
     "[Preferences.Donations.PayPal]" => base64_encode($y["Donations"]["PayPal"]),
     "[Preferences.Donations.SubscribeStar]" => base64_encode($y["Donations"]["SubscribeStar"]),
     "[Preferences.General.Name]" => base64_encode($y["Personal"]["FirstName"]),
     "[Preferences.General.Description]" => base64_encode($y["Personal"]["Description"]),
     "[Preferences.General.DisplayName]" => base64_encode($y["Personal"]["DisplayName"]),
     "[Preferences.General.Email]" => base64_encode($y["Personal"]["Email"]),
     "[Preferences.General.Gender]" => $y["Personal"]["Gender"],
     "[Preferences.General.OnlineStatus]" => $y["Activity"]["OnlineStatus"],
     "[Preferences.General.RelationshipStatus]" => $y["Personal"]["RelationshipStatus"],
     "[Preferences.General.RelationshipWith]" => base64_encode($relationshipWith),
     "[Preferences.ID]" => $id,
     "[Preferences.Links.EditShop]" => base64_encode("v=".base64_encode("Shop:Edit")."&ID=".base64_encode(md5($y["Login"]["Username"]))),
     "[Preferences.Links.NewPassword]" => base64_encode("v=".base64_encode("Profile:NewPassword")),
     "[Preferences.Links.NewPIN]" => base64_encode("v=".base64_encode("Profile:NewPIN")),
     "[Preferences.Personal.MinimalDesign]" => $choseMinimalDesign,

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
     "[Preferences.Privacy.Gender]" => $y["Privacy"]["Gender"],
     "[Preferences.Privacy.Journal]" => $y["Privacy"]["Journal"],
     "[Preferences.Privacy.LastActivity]" => $y["Privacy"]["LastActivity"],
     "[Preferences.Privacy.LookMeUp]" => $y["Privacy"]["LookMeUp"],
     "[Preferences.Privacy.MSG]" => $y["Privacy"]["MSG"],
     "[Preferences.Privacy.NSFW]" => $y["Privacy"]["NSFW"],
     "[Preferences.Privacy.OnlineStatus]" => $y["Privacy"]["OnlineStatus"],
     "[Preferences.Privacy.Posts]" => $y["Privacy"]["Posts"],
     "[Preferences.Privacy.Products]" => $y["Privacy"]["Products"],
     "[Preferences.Privacy.Profile]" => $y["Privacy"]["Profile"],
     "[Preferences.Privacy.Registered]" => $y["Privacy"]["Registered"],
     "[Preferences.Privacy.RelationshipStatus]" => $y["Privacy"]["RelationshipStatus"],
     "[Preferences.Privacy.RelationshipWith]" => $y["Privacy"]["RelationshipWith"],
     "[Preferences.Privacy.Shop]" => $y["Privacy"]["Shop"]
    ], $this->core->Page("e54cb66a338c9dfdcf0afa2fec3b6d8a")]);
   }
   $r = [
    "Action" => $action,
    "Front" => $r
   ];
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
   $email = $data["Personal_Email"] ?? "";
   $emailIsTaken = 0;
   $header = "Error";
   $members = $this->core->DatabaseSet("MBR") ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   foreach($members as $key => $value) {
    $value = str_replace("c.oh.mbr.", "", $value);
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
   } elseif(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = "The PINs do not match.";
   } elseif($emailIsTaken > 0) {
    $r = "Another Member is already using <em>$email</em>.";
   } elseif($this->core->ID == $you) {
    $r = "You must be signed in to continue.";
   } else {
    $accessCode = "Accepted";
    $header = "Done";
    $newMember = $this->core->NewMember(["Username" => $you]);
    $firstName = explode(" ", $data["name"])[0];
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
    $newMember["Activity"]["OnlineStatus"] = $data["OnlineStatus"];
    $newMember["Activity"]["Registered"] = $y["Activity"]["Registered"];
    $newMember["Blogs"] = $y["Blogs"] ?? [];
    $newMember["Forums"] = $y["Forums"] ?? [];
    $newMember["Pages"] = $y["Pages"] ?? [];
    $newMember["Personal"]["Birthday"] = [
     "Month" => $data["BirthMonth"],
     "Year" => $data["BirthYear"]
    ];
    $newMember["Personal"]["Age"] = date("Y") - $data["BirthYear"];
    $newMember["Personal"]["FirstName"] = $firstName;
    $newMember["Personal"]["CoverPhoto"] = $y["Personal"]["CoverPhoto"];
    $newMember["Personal"]["ProfilePicture"] = $y["Personal"]["ProfilePicture"];
    $newMember["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
    $newMember["Rank"] = $y["Rank"];
    $this->core->Data("Save", ["mbr", md5($you), $newMember]);
    $r = "Your Preferences were saved!";
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => [
      "Body" => $r,
      "Header" => $header
     ]
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function SaveDeactivate(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(1 == 1) {
    $accessCode = "Accepted";
    // DEACTIVATE PROFILE
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   // DELETE PROFILE
   /* DELETE CONVERSATION
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(1 == 1) {
    $accessCode = "Accepted";
    if(!empty($this->core->Data("Get", ["conversation", md5("MBR_$you")]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => md5("MBR_$you")]
     ]);
    }
   }
   */
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
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
    $y["Login"]["Password"] = md5($data["NewPassword"]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "Your Password has been updated.",
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
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function SaveSignIn(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $i = 0;
   $password = $data["Password"] ?? "";
   $r = "An internal error has ocurred.";
   $responseType = "Dialog";
   $username = $data["Username"] ?? "";
   if(empty($password) || empty($username)) {
    if(empty($password)) {
     $field = "Password";
    } elseif(empty($username)) {
     $field = "Username";
    }
    $r = "The $field is missing.";
   } else {
    $members = $this->core->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->core->Data("Get", ["mbr", $value]) ?? [];
     $member = $member["Login"]["Username"] ?? "";
     if($username == $member) {
      $i++;
     }
    } if($i > 0) {
     $member = $this->core->Data("Get", ["mbr", md5($username)]) ?? [];
     $password = md5($password);
     if($password == $member["Login"]["Password"]) {
      $accessCode = "Accepted";
      $responseType = "SignIn";
      $this->core->Statistic("LI");
      $r = $this->core->Encrypt($member["Login"]["Username"].":".$member["Login"]["Password"]);
     } elseif($password != $member["Login"]["Password"]) {
      $r = "The Passwords do not match.";
     } elseif($username != $member["Login"]["Username"]) {
      $r = "The Usernames do not match.";
     } else {
      $r = $r;
     }
    } else {
     $r = "The Member <em>$username</em> could not be found.";
    }
   } if($accessCode == "Denied") {
    $r = [
     "Body" => $r,
     "Header" => "Sign In Failed"
    ];
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
  function SaveSignUp(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "BirthMonth",
    "BirthYear",
    "Email",
    "Name",
    "Password",
    "Password2",
    "Gender",
    "PIN",
    "PIN2",
    "SOE",
    "Username"
   ]);
   $_MinimumAge = $this->core->config["minRegAge"];
   $birthYear = $data["BirthYear"] ?? 1995;
   $age = date("Y") - $birthYear;
   $ck = ($age > $_MinimumAge) ? 1 : 0;
   $firstName = ($data["Gender"] == "Male") ? "John" : "Jane";
   $i = 0;
   $members = $this->core->DatabaseSet("MBR");
   $password = $data["Password"];
   $r = "Internal Error";
   $username = $this->core->CallSign($data["Username"]);
   foreach($members as $key => $value) {
    $value = str_replace("c.oh.mbr.", "", $value);
    $member = $this->core->Data("Get", ["mbr", $value]) ?? [];
    if($i == 0 && $member["Login"]["Username"] == $username) {
     $i++;
    }
   } if(empty($data["Email"])) {
    $r = "An Email address is required.";
   } elseif(empty($data["Password"])) {
    $r = "A Password is required.";
   } elseif($data["Password"] != $data["Password2"]) {
    $r = "Your Passwords must match.";
   } elseif(empty($data["PIN"])) {
    $r = "A PIN is required.";
   } elseif(!is_numeric($data["PIN"]) || !is_numeric($data["PIN2"])) {
    $r = "Your PINs must be numeric.";
   } elseif($data["PIN"] != $data["PIN2"]) {
    $r = "Your PINs must match.";
   } elseif(empty($data["Username"])) {
    $r = "A Username is required.";
   } elseif($data["Username"] == $this->core->ID) {
    $r = $this->core->ID." is the system profile and cannot be used.";
   } elseif($ck == 0) {
    $r = "You must be $_MinimumAge or older to sign up.";
   } elseif($i > 0) {
    $r = "The Username <em>$username</em> is already in use.";
   } else {
    $accessCode = "Accepted";
    $birthMonth = $data["BirthMonth"] ?? 10;
    $now = $this->core->timestamp;
    if($data["SOE"] == 1) {
     $x = $this->core->Data("Get", ["x", md5("ContactList")]) ?? [];
     $x[$data["Email"]] = [
      "Email" => $data["Email"],
      "Name" => $firstName,
      "Phone" => "N/A",
      "SendOccasionalEmails" => $data["SOE"],
      "UN" => $username,
      "Updated" => $now
     ];
     $this->core->Data("Save", ["x", md5("ContactList"), $x]);
    }
    $this->core->Data("Save", ["cms", md5($username), [
     "Contacts" => [],
     "Requests" => []
    ]]);
    $this->core->Data("Save", ["fs", md5($username), [
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
     "mbr", md5($username), $this->core->NewMember([
      "Age" => $age,
      "BirthMonth" => $birthMonth,
      "BirthYear" => $birthYear,
      "DisplayName" => $username,
      "Email" => $data["Email"],
      "FirstName" => $firstName,
      "Gender" => $data["Gender"],
      "Password" => $password,
      "PIN" => md5($data["PIN"]),
      "Username" => $username
     ])
    ]);
    $this->core->Data("Save", ["stream", md5($username), []]);
    $this->core->Data("Save", ["shop", md5($username), [
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
    if(!empty($data["Email"])) {
     $this->core->SendEmail([
      "Message" => $this->core->Change([[
       "[Email.Header]" => $this->core->Page("c790e0a597e171ff1d308f923cfc20c9"),
       "[Email.Name]" => $name
      ], $this->core->Page("35fb42097f5a625e9bd0a38554226743")]),
      "Title" => "Welcome to ".$this->core->config["App"]["Name"]."!",
      "To" => $data["Email"]
     ]);
    }
    $this->core->Statistic("MBR");
    $r = $this->core->Change([[
     "[Success.SignIn]" => base64_encode("v=".base64_encode("Profile:SignIn")),
     "[Success.Username]" => $username
    ], $this->core->Page("872fd40c7c349bf7220293f3eb64ab45")]);
   } if($accessCode != "Accepted") {
    $r = $this->core->Change([[
     "[2FA.Error.Message]" => $r,
     "[2FA.Error.ViewPairID]" => "2FAStep1"
    ], $this->core->Page("ef055d5546ab5fead63311a3113f3f5f")]);
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
  function SignIn(array $a) {
   $accessCode = "Denied";
   $r = [
    "Actions" => [
     $this->core->Element(["button", "Sign In", [
      "class" => "BBB SendData v2 v2w",
      "data-form" => ".SignIn",
      "data-processor" => base64_encode("v=".base64_encode("Profile:SaveSignIn"))
     ]])
    ],
    "Header" => "Sign In",
    "Scrollable" => $this->core->Page("ff434d30a54ee6d6bbe5e67c261b2005")
   ];
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SignUp(array $a) {
   $accessCode = "Accepeted";
   $birthMonths = [];
   $birthYears = [];
   for($i = 1; $i <= 12; $i++) {
    $birthMonths[$i] = $i;
   } for($i = 1776; $i <= (date("Y") - 18); $i++) {
    $birthYears[$i] = $i;
   }
   $r = [
    "Front" => $this->core->Change([[
     "[SignUp.2FA]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:FirstTime")),
     "[SignUp.BirthMonths]" => json_encode($birthMonths, true),
     "[SignUp.BirthYears]" => json_encode($birthYears, true),
     "[SignUp.MinimumAge]" => $this->core->config["minAge"],
     "[SignUp.ReturnView]" => base64_encode(json_encode([
      "Group" => "Profile",
      "View" => "SaveSignUp"
     ], true))
    ], $this->core->Page("c48eb7cf715c4e41e2fb62bdfa60f198")])
   ];
   return $this->core->JSONResponse([
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
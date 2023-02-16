<?php
 Class Profile extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function BulletinCenter(array $a) {
   $list = base64_encode("Profile:BulletinsList");
   $search = base64_encode("Search:Containers");
   return $this->system->Change([[
    "[BulletinCenter.Bulletins]" => $this->view($search, ["Data" => [
     "st" => "Bulletins"
    ]]),
    "[BulletinCenter.ContactRequests]" => "v=$list&type=".base64_encode("ContactsRequests"),
    "[BulletinCenter.Contacts]" => $this->view($search, ["Data" => [
     "Chat" => 0,
     "st" => "ContactsChatList"
    ]])
   ], $this->system->Page("6cbe240071d79ac32edbe98679fcad39")]);
  }
  function BulletinMessage(array $a) {
   $data = $a["Data"] ?? [];
   $request = $data["Data"]["Request"] ?? "";
   $type = $data["Type"] ?? "";
   $message = "Message required for Bulletin type <em>$type</em>.";
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
   } elseif($type == "NewBlogPost") {
    $message = "Posted to their blog.";
   } elseif($type == "NewProduct") {
    $message = "Added a product to their shop.";
   }
   return $message;
  }
  function BulletinOptions(array $a) {
   $data = $a["Data"] ?? [];
   $bulletin = $data["Bulletin"] ?? "";
   $bulletin = (!empty($bulletin)) ? base64_decode($bulletin) : [];
   $bulletin = json_decode($bulletin, true);
   $id = $bulletin["ID"];
   $r = "&nbsp;";
   $y = $this->you;
   if($bulletin["Read"] == 0) {
    $data = $bulletin["Data"] ?? [];
    $mar = "v=".base64_encode("Profile:MarkBulletinAsRead")."&ID=$id";
    if($bulletin["Type"] == "ArticleUpdate") {
     $page = $this->system->Data("Get", ["pg", $data["ArticleID"]]) ?? [];
     $r = $this->system->Element([
      "button", "Take me to <em>".$page["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("BlogPost:Home")."&CARD=1&ID=".$data["ArticleID"]),
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
     $true = $this->system->PlainText([
      "Data" => 1,
      "Encode" => 1
     ]);
     if($contactStatus["TheyRequested"] > 0) {
      $_View = "v=".base64_encode("Contact:Requests");
      $accept = $_View."&accept=$true&bulletin=$true";
      $decline = $_View."&decline=$true&bulletin=$true";
      $r = "<input name=\"Username\" type=\"hidden\" value=\"".$data["From"]."\"/>\r\n";
      $r .= $this->system->Element(["div", $this->system->Element([
       "button", "Accept", [
        "class" => "BBB Close MarkAsRead SendData v2 v2w",
        "data-form" => ".Bulletin$id .Options",
        "data-MAR" => base64_encode($mar),
        "data-processor" => base64_encode($accept),
        "data-target" => ".Bulletin$id .Options"
       ]]), ["class" => "Desktop50"]
      ]).$this->system->Element(["div", $this->system->Element([
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
     $article = $this->system->Data("Get", ["pg", $data["ArticleID"]]) ?? [];
     $r = $this->system->Element([
      "button", "Take me to <em>".$article["Title"]."</em>", [
       "class" => "BBB Close dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("Page:Home")."&CARD=1&ID=".$article["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToBlog") {
     $blog = $this->system->Data("Get", ["blg", $data["BlogID"]]) ?? [];
     $r = $this->system->Element([
      "button", "Take me to <em>".$blog["Title"]."</em>", [
       "class" => "BBB Close dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("Blog:Home")."&CARD=1&ID=".$blog["ID"])
      ]
     ]);
    } elseif($bulletin["Type"] == "InviteToForum") {
     $forum = $this->system->Data("Get", ["pf", $data["ForumID"]]) ?? [];
     $r = $this->system->Element([
      "button", "Take me to <em>".$forum["Title"]."</em>", [
       "class" => "BBB Close dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("Forum:Home")."&CARD=1&ID=".$forum["ID"])
      ]
     ]);
    } elseif($type == "NewBlogPost") {
     $post = $this->system->Data("Get", ["bp", $data["PostID"]]) ?? [];
     $r = $this->system->Element([
      "button", "Take me to <em>".$post["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("BlogPost:Home")."&CARD=1&Blog=".$data["BlogID"]."&Post=".$data["PostID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
    } elseif($type == "NewProduct") {
     $product = $this->system->Data("Get", [
      "miny",
      $data["ProductID"]
     ]) ?? [];
     $r = $this->system->Element([
      "button", "Take me to <em>".$product["Title"]."</em>", [
       "class" => "BBB Close MarkAsRead dB2O v2 v2w",
       "data-type" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=".$product["ID"]."&UN=".$data["ShopID"]),
       "data-MAR" => base64_encode($mar),
       "data-target" => ".Bulletin$id .Options"
      ]
     ]);
    }
   }
   return $r;
  }
  function Bulletins(array $a) {
   $d = $a["Data"] ?? [];
   $ec = "Denied";
   $r = [];
   $tpl = $this->system->Page("ae30582e627bc060926cfacf206920ce");
   $y = $this->you;
   if($y["Login"]["Username"] != $this->system->ID) {
    $ec = "Accepted";
    $bulletins = $this->system->Data("Get", [
     "bulletins",
     md5($y["Login"]["Username"])
    ]) ?? [];
    foreach($bulletins as $key => $value) {
     if($value["Seen"] == 0) {
      $bulletins[$key]["Seen"] = 1;
      $value["ID"] = $key;
      $t = $this->system->Member($value["From"]);
      $pic = $this->system->ProfilePicture($t, "margin:5%;width:90%");
      array_push($r, [
       "Data" => $value["Data"],
       "Date" => $this->system->TimeAgo($value["Sent"]),
       "From" => $t["Personal"]["DisplayName"],
       "ID" => $key,
       "Message" => $this->view(base64_encode("Profile:BulletinMessage"), [
        "Data" => $value
       ]),
       "Options" => $this->view(base64_encode("Profile:BulletinOptions"), [
        "Data" => [
         "Bulletin" => base64_encode(json_encode($value))
        ]
       ]),
       "Picture" => $pic
      ]);
     }
    }
    $this->system->Data("Save", [
     "bulletins",
     md5($y["Login"]["Username"]),
     $bulletins
    ]);
   }
   return $this->system->JSONResponse([
    $ec,
    base64_encode(json_encode($r, true)),
    base64_encode($tpl)
   ]);
  }
  function BulletinsList(array $a) {
   $data = $a["Data"] ?? [];
   $search = base64_encode("Search:Containers");
   $type = $data["type"] ?? base64_encode("");
   $type = base64_decode($type);
   $r = ($type == "ContactsRequests") ? $this->view($search, ["Data" => [
    "st" => "ContactsRequests"
   ]]) : "";
   return $r;
  }
  function ChangeRank(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["PIN", "Rank", "Username"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Member Identifier or Rank are missing."
    ]),
    "Header" => "Error"
   ]);
   $rank = $data["Rank"];
   $responseType = "Dialog";
   $username = $data["Username"];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif(!empty($rank) && !empty($username)) {
    $accessCode = "Accepted";
    $member = $this->system->Member($username);
    $responseType = "ReplaceContent";
    $member["Rank"] = md5($rank);
    $this->system->Data("Save", ["mbr", md5($username), $member]);
    $r = $this->system->Element([
     "h3", "Success", ["class" => "CenterText UpperCase"]
    ]).$this->system->Element([
     "p", $member["Personal"]["DisplayName"]."'s Rank within <em>Outer Haven</em> was Changed to $rank.",
     ["class" => "CenterText"]
    ]);
   }
   return $this->system->JSONResponse([
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
   $data = $a["Data"] ?? [];
   $opt = "";
   $t = $this->system->Member(base64_decode($data["UN"]));
   $display = ($t["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
   $don = $t["Donations"] ?? [];
   $y = $this->you;
   if(empty($don)) {
    if($t["Login"]["Username"] == $y["Login"]["Username"]) {
     $p = "You have not set up Donations yet.";
    } else {
     $p = "$display has not set up Donations yet.";
    }
    $opt .= $this->system->Element(["p", $p]);
   } else {
    $opt .= (!empty($don["Patreon"])) ? $this->system->Element([
     "button", "Donate via Patreon", [
      "class" => "LI",
      "onclick" => "W('https://patreon.com/".$don["Patreon"]."', '_blank');"
     ]
    ]) : "";
    $opt .= (!empty($don["PayPal"])) ? $this->system->Element([
     "button", "Donate via PayPal", [
      "class" => "LI",
      "onclick" => "W('https://paypal.me/".$don["PayPal"]."/5', '_blank');"
     ]
    ]) : "";
    $opt .= (!empty($don["SubscribeStar"])) ? $this->system->Element([
     "button", "Donate via SubscribeStar", [
      "class" => "LI LIL",
      "onclick" => "W('https://subscribestar.com/".$don["SubscribeStar"]."', '_blank');"
     ]
    ]) : "";
   }
   return $this->system->Dialog([
    "Body" => $this->system->Element(["div", $opt, ["class" => "scr"]]),
    "Header" => "Donate to $display"
   ]);
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["CARD", "UN", "b2", "lPG"]);
   $b2 = $data["b2"];
   $back = $data["back"] ?? 0;
   $back = ($back == 1) ? $this->system->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI head",
    "data-type" => $data["lPG"]
   ]]) : "";
   $pub = $data["pub"] ?? 0;
   $t = $this->system->Member(base64_decode($data["UN"]));
   $id = $t["Login"]["Username"];
   $display = ($id == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
   $r = $this->system->Change([[
    "[Error.Back]" => $back,
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The requested Member could not be found."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_theirContacts = $this->system->Data("Get", ["cms", md5($id)]) ?? [];
    $_theyBlockedYou = $this->system->CheckBlocked([$t, "Members", $you]);
    $_youBlockedThem = $this->system->CheckBlocked([$y, "Members", $id]);
    $b2 = ($id == $you) ? "Your Profile" : $t["Personal"]["DisplayName"]."'s Profile";
    $lpg = "Profile".md5($id);
    $privacy = $t["Privacy"] ?? [];
    $ck = ($id == $you) ? 1 : 0;
    $ck2 = ($privacy["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->config["minAge"])) ? 1 : 0;
    $ckart = 0;
    $public = md5("Public");
    $r = $this->system->Change([[
     "[Error.Back]" => $back,
     "[Error.Header]" => "Not Found",
     "[Error.Message]" => "The requested Member could not be found."
    ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
    $search = base64_encode("Search:Containers");
    $theirContacts = $_theirContacts["Contacts"] ?? [];
    $theirRequests = $_theirContacts["Requests"] ?? [];
    $visible = $this->system->CheckPrivacy([
     "Contacts" => $theirContacts,
     "Privacy" => $privacy["Profile"],
     "UN" => $id,
     "Y" => $you
    ]);
    if($_theyBlockedYou == 0 && ($ck == 1 || $ck2 == 1 || $visible == 1)) {
     $_Artist = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $_Block = ($_youBlockedThem == 0) ? "B" : "U";
     $_BlockText = ($_youBlockedThem == 0) ? "Block" : "Unblock";
     $_VIP = $t["Subscriptions"]["VIP"]["A"];
     $actions = $this->view(base64_encode("Common:Reactions"), ["Data" => [
      "CRID" => md5($id),
      "T" => $id,
      "Type" => 4
     ]]);
     $actions .= $this->system->Element(["button", $_BlockText, [
      "class" => "BLK Small v2",
      "data-cmd" => base64_encode($_Block),
      "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($display)."&content=".base64_encode($id)."&list=".base64_encode("Members")."&BC=")
     ]]);
     $actions .= ($_Artist == 1) ? $this->system->Element(["button", "Donate", [
      "class" => "Small dBO v2",
      "data-type" => "v=".base64_encode("Profile:Donate")."&UN=".base64_encode($id)
     ]]) : "";
     $actions .= ($_VIP == 0 && $id != $you && $y["Rank"] == md5("High Command")) ? $this->system->Element(["button", "Make VIP", [
      "class" => "SendData Small v2",
      "data-form" => ".Profile$id",
      "data-processor" => base64_encode("v=".base64_encode("Profile:MakeVIP")."&ID=".base64_encode($id))
     ]]) : "";
     $actions .= $this->system->Element(["button", "Message", [
      "class" => "Small dB2C v2",
      "onclick" => "FST('N/A', 'v=".base64_encode("Chat:Home")."&GroupChat=0&to=".base64_encode($id)."', '".md5("Chat$id")."');"
     ]]);
     $actions = ($id != $you) ? $actions : "";
     $addContact = "";
     $albums = ($ck == 1 || $privacy["Albums"] == $public || $visible == 1) ? $this->view($search, ["Data" => [
      "UN" => base64_encode($id),
      "st" => "MBR-ALB"
     ]]) : $this->system->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Forbidden",
      "[Error.Message]" => "$display keeps their media albums to themselves."
     ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
     $articles = ($ck == 1 || $privacy["Archive"] == $public || $visible == 1) ? $this->view($search, ["Data" => [
      "UN" => base64_encode($id),
      "b2" => $b2,
      "lPG" => $lpg,
      "st" => "MBR-CA"
     ]]) : $this->system->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Forbidden",
      "[Error.Message]" => "$display keeps their archive contributions to themselves."
     ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
     $bio = "You have not added an Autobiography";
     $bio = ($ck == 0) ? "$display has not added an Autobiography." : $bio;
     $bio = (!empty($t["Bio"])) ? $this->system->PlainText([
      "BBCodes" => 1,
      "Data" => $t["Bio"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]) : $bio;
     $blogs = ($ck == 1 || $privacy["Posts"] == $public || $visible == 1) ? $this->view($search, ["Data" => [
      "UN" => base64_encode($id),
      "b2" => $b2,
      "lPG" => $lpg,
      "st" => "MBR-BLG"
     ]]) : $this->system->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Forbidden",
      "[Error.Message]" => "$display keeps their blogs to themselves."
     ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
     $ChangeRank = "";
     $contacts = ($ck == 1 || $privacy["Contacts"] == $public || $visible == 1) ? $this->view($search, ["Data" => [
      "UN" => base64_encode($id),
      "b2" => $b2,
      "lPG" => $lpg,
      "st" => "ContactsProfileList"
     ]]) : $this->system->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Forbidden",
      "[Error.Message]" => "$display keeps their contacts to themselves."
     ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
     $contactRequestsAllowed = $this->system->CheckPrivacy([
      "Contacts" => $theirContacts,
      "Privacy" => $t["Privacy"]["ContactRequests"],
      "UN" => $id,
      "Y" => $you
     ]);
     $contactStatus = $this->view(base64_encode("Contact:Status"), [
      "Them" => $id,
      "You" => $you
     ]);
     if($contactRequestsAllowed == 1 && $id != $you) {
      $cancel = (in_array($you, $theirRequests)) ? 1 : 0;
      if($contactStatus["TheyHaveYou"] == 0 && $contactStatus["YouHaveThem"] == 0) {
       if($contactStatus["TheyRequested"] > 0) {
        $addContact = $this->system->Element([
         "div", $this->system->Element(["button", "Accept", [
          "class" => "BB BBB SendData v2 v2w",
          "data-form" => ".ContactRequest$id",
          "data-processor" => base64_encode("v=".base64_encode("Contact:Requests")."&accept=1")
         ]]), ["class" => "Desktop50"]
        ]).$this->system->Element([
         "div", $this->system->Element(["button", "Decline", [
          "class" => "BB SendData v2 v2w",
          "data-form" => ".ContactRequest$id",
          "data-processor" => base64_encode("v=".base64_encode("Contact:Requests")."&decline=1")
         ]]), ["class" => "Desktop50"]
        ]);
       } if($cancel == 1 || $contactStatus["YouRequested"] > 0) {
        $addContact = $this->system->Change([[
         "[ContactRequest.Header]" => "Cancel Request",
         "[ContactRequest.ID]" => $id,
         "[ContactRequest.Option]" => $this->system->Element([
          "button", "Cancel Request", [
           "class" => "BB SendData v2 v2w",
           "data-form" => ".ContactRequest$id",
           "data-processor" => base64_encode("v=".base64_encode("Contact:Requests"))
          ]
         ]),
         "[ContactRequest.Text]" => "Cancel the contact request you snet to $display.",
         "[ContactRequest.Username]" => $id
        ], $this->system->Page("a73ffa3f28267098851bf3550eaa9a02")]);
       } else {
        $addContact = $this->system->Change([[
         "[ContactRequest.Header]" => "Add $display",
         "[ContactRequest.ID]" => $id,
         "[ContactRequest.Option]" => $this->system->Element([
          "button", "Add $display", [
           "class" => "BB SendData v2 v2w",
           "data-form" => ".ContactRequest$id",
           "data-processor" => base64_encode("v=".base64_encode("Contact:Requests"))
          ]
         ]),
         "[ContactRequest.Text]" => "Send $display a Contact Request.",
         "[ContactRequest.Username]" => $id
        ], $this->system->Page("a73ffa3f28267098851bf3550eaa9a02")]);
       }
      }
      $addContact = ($you != $this->system->ID) ? $addContact : "";
     } if($id != $you && $y["Rank"] == md5("High Command") || $y["Rank"] == md5("Partner")) {
      $ChangeRank = $this->system->Change([[
       "[Ranks.Authentication]" => "v=".base64_encode("Authentication:AuthorizeChange")."&Form=".base64_encode(".MemberRank".md5($id))."&ID=".md5($id)."&Processor=".base64_encode("v=".base64_encode("Profile:ChangeRank"))."&Text=".base64_encode("Do you authorize the Change of $display's rank?"),
       "[Ranks.DisplayName]" => $display,
       "[Ranks.ID]" => md5($id),
       "[Ranks.Username]" => $id,
       "[Ranks.Option]" => $this->system->Select("Rank", "req v2 v2w")
      ], $this->system->Page("914dd9428c38eecf503e3a5dda861559")]);
     }
     $gender = $t["Personal"]["Gender"] ?? "Male";
     $gender = $this->system->Gender($gender);
     $description = "You have not added a Description.";
     $description = ($id != $you) ? "$display has not added a Description." : $description;
     $description = (!empty($t["Personal"]["Description"])) ? $this->system->PlainText([
      "BBCodes" => 1,
      "Data" => $t["Personal"]["Description"],
      "Display" => 1
     ]) : $description;
     $journal = ($ck == 1 || $privacy["Journal"] == $public || $visible == 1) ? $this->view($search, ["Data" => [
      "UN" => base64_encode($id),
      "b2" => $b2,
      "lPG" => $lpg,
      "st" => "MBR-JE"
     ]]) : $this->system->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Forbidden",
      "[Error.Message]" => "$display keeps their Journal to themselves."
     ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
     $r = $this->system->Change([[
      "[Member.Actions]" => $actions,
      "[Member.AddContact]" => $addContact,
      "[Member.Albums]" => $albums,
      "[Member.Articles]" => $articles,
      "[Member.Blogs]" => $blogs,
      "[Member.Back]" => $back,
      "[Member.Bio]" => $bio,
      "[Member.ChangeRank]" => $ChangeRank,
      "[Member.CoverPhoto]" => $this->system->CoverPhoto($t["Personal"]["CoverPhoto"]),
      "[Member.Contacts]" => $contacts,
      "[Member.Conversation]" => $this->system->Change([[
       "[Conversation.CRID]" => $id,
       "[Conversation.CRIDE]" => base64_encode(md5($id)),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->system->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[Member.Description]" => $description,
      "[Member.DisplayName]" => $display,
      "[Member.Footer]" => $this->system->Page("a095e689f81ac28068b4bf426b871f71"),
      "[Member.ID]" => md5($id),
      "[Member.Journal]" => $journal,
      "[Member.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:2em;width:calc(100% - 4em)"),
      "[Member.Stream]" => $this->view($search, ["Data" => [
       "UN" => base64_encode($id),
       "st" => "MBR-SU"
      ]])
     ], $this->system->Page("72f902ad0530ad7ed5431dac7c5f9576")]);
    }
   }
   $r = ($data["CARD"] == 1) ? $this->system->Card(["Front" => $r]) : $r;
   $r = ($you == $this->system->ID && $pub == 1) ? $this->view(base64_encode("WebUI:OptIn"), []) : $r;
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function MakeVIP(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $manifest = [];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Member Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $responseType = "Dialog";
   $y = $this->you;
   if(!empty($data["ID"])) {
    $t = base64_decode($data["ID"]);
    $t = ($t == $y["Login"]["Username"]) ? $y : $this->system->Member($t);
    $display = $t["Personal"]["DisplayName"];
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "$display is already a VIP Member."
     ]),
     "Header" => "Error"
    ]);
    if($t["Subscriptions"]["VIP"]["A"] == 0) {
     $_VIPForum = "cb3e432f76b38eaa66c7269d658bd7ea";
     $accessCode = "Accepted";
     $t["Points"] = $t["Points"] + 1000000;
     $manifest = $this->system->Data("Get", ["pfmanifest", $_VIPForum]) ?? [];
     array_push($manifest, [$t["Login"]["Username"] => "Member"]);
     foreach($t["Subscriptions"] as $key => $value) {
      if(!in_array($key, ["Artist", "Developer"])) {
       $t["Subscriptions"][$key] = [
        "A" => 1,
        "B" => $this->system->timestamp,
        "E" => $this->system->TimePlus($this->system->timestamp, 1, "month")
       ];
      }
     }
     $this->system->Data("Save", ["pfmanifest", $_VIPForum, $manifest]);
     $this->system->Data("Save", ["mbr", md5($t["Login"]["Username"]), $t]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "$display is now a VIP Member."]),
      "Header" => "Done"
     ]);
    }
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, ["ID"]);
   $y = $this->you;
   $bulletins = $this->system->Data("Get", ["bulletins", md5($y["Login"]["Username"])]) ?? [];
   if(!empty($data["ID"])) {
    foreach($bulletins as $key => $value) {
     if($data["ID"] == $key) {
      $bulletin = $value;
      $bulletin["Read"] = 1;
      $bulletins[$key] = $bulletin;
     }
    }
   }
   $this->system->Data("Save", [
    "bulletins",
    md5($y["Login"]["Username"]),
    $bulletins
   ]);
   return json_encode($bulletins);
  }
  function NewPassword(array $a) {
   $y = $this->you;
   if($this->system->ID == $y["Login"]["Username"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Error"
    ]);
   } else {
    $r = $this->system->Change([[
     "[Member.ProfilePicture]" => $this->system->ProfilePicture($y, "margin:5%;width:90%"),
     "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePassword")),
     "[Member.Username]" => $y["Login"]["Username"]
    ], $this->system->Page("08302aec8e47d816ea0b3f80ad87503c")]);
   }
   return $r;
  }
  function NewPIN(array $a) {
   $y = $this->you;
   if($this->system->ID == $y["Login"]["Username"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Error"
    ]);
   } else {
    $r = $this->system->Change([[
     "[Member.ProfilePicture]" => $this->system->ProfilePicture($y, "margin:5%;width:90%"),
     "[Member.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Member.Update]" => base64_encode("v=".base64_encode("Profile:SavePIN"))
    ], $this->system->Page("867bd8480f46eea8cc3d2a2ed66590b7")]);
   }
   return $r;
  }
  function Preferences(array $a) {
   $button = "";
   $minAge = $this->system->core["minRegAge"] ?? 13;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $ck = ($y["Personal"]["Age"] >= $minAge) ? 1 : 0;
   $ck2 = ($this->system->ID != $you) ? 1 : 0;
   if($ck == 0) {
    $r = $this->system->Change([[
     "[Error.Back]" => "",
     "[Error.Header]" => "Not of Age",
     "[Error.Message]" => "As a security measure, you must be aged $minAge or older in order to take full control of your profile and absolve yourself of your parent account."
    ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   } elseif($ck2 == 0) {
    $r = $this->system->Change([[
     "[Error.Back]" => "",
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   } elseif($ck == 1 && $ck2 == 1) {
    $button = $this->system->Element(["button", "Save", [
     "class" => "CardButton dBO",
     "data-type" => "v=".base64_encode("Authentication:AuthorizeChange")."&Form=".base64_encode(".Preferences".md5($you))."&ID=".md5($you)."&Processor=".base64_encode("v=".base64_encode("Profile:Save"))."&Text=".base64_encode("Are you sure you want to update your preferences?")
    ]]);
    $minimalDesign = $y["Personal"]["MinimalDesign"] ?? "";
    $relationshipWith = $y["Personal"]["RelationshipWith"] ?? "";
    $r = $this->system->Change([[
     "[Preferences.Donations.Patreon]" => $y["Donations"]["Patreon"],
     "[Preferences.Donations.PayPal]" => $y["Donations"]["PayPal"],
     "[Preferences.Donations.SubscribeStar]" => $y["Donations"]["SubscribeStar"],
     "[Preferences.General.Bio]" => $y["Personal"]["Bio"],
     "[Preferences.General.Birthday.Month]" => $y["Personal"]["Birthday"]["Month"],
     "[Preferences.General.Birthday.Year]" => $y["Personal"]["Birthday"]["Year"],
     "[Preferences.General.Description]" => $y["Personal"]["Description"],
     "[Preferences.General.DisplayName]" => $y["Personal"]["DisplayName"],
     "[Preferences.General.Email]" => $y["Personal"]["Email"],
     "[Preferences.General.FirstName]" => $y["Personal"]["FirstName"],
     "[Preferences.General.Gender]" => $this->system->Select("gender", "req", $y["Personal"]["Gender"]),
     "[Preferences.General.OnlineStatus]" => $this->system->Select("OnlineStatus", "req v2w", $y["Activity"]["OnlineStatus"]),
     "[Preferences.General.RelationshipStatus]" => $this->system->Select("Personal_RelationshipStatus", "req v2w", $y["Personal"]["RelationshipStatus"]),
     "[Preferences.General.RelationshipWith]" => $relationshipWith,
     "[Preferences.General.Username]" => md5($you),
     "[Preferences.ID]" => md5($you),
     "[Preferences.Links.EditShop]" => base64_encode("v=".base64_encode("Shop:Edit")."&ID=".base64_encode(md5($y["Login"]["Username"]))),
     "[Preferences.Links.NewPassword]" => "v=".base64_encode("Profile:NewPassword"),
     "[Preferences.Links.NewPIN]" => "v=".base64_encode("Profile:NewPIN"),
     "[Preferences.Personal.AboutPage]" => $y["Personal"]["AboutPage"],
     "[Preferences.Personal.MinimalDesign]" => $this->system->Select("Personal_MinimalDesign", "req v2 v2w", $minimalDesign),
     "[Preferences.Privacy.Albums]" => $this->system->Select("Privacy_Albums", "req v2w", $y["Privacy"]["Albums"]),
     "[Preferences.Privacy.Archive]" => $this->system->Select("Privacy_Archive", "req v2w", $y["Privacy"]["Archive"]),
     "[Preferences.Privacy.Articles]" => $this->system->Select("Privacy_Articles", "req v2w", $y["Privacy"]["Articles"]),
     "[Preferences.Privacy.Comments]" => $this->system->Select("Privacy_Comments", "req v2w", $y["Privacy"]["Comments"]),
     "[Preferences.Privacy.ContactInfo]" => $this->system->Select("Privacy_ContactInfo", "req v2w", $y["Privacy"]["ContactInfo"]),
     "[Preferences.Privacy.ContactInfoEmails]" => $this->system->Select("Privacy_ContactInfoEmails", "req v2w", $y["Privacy"]["ContactInfoEmails"]),
     "[Preferences.Privacy.ContactRequests]" => $this->system->Select("Privacy_ContactRequests", "req v2w", $y["Privacy"]["ContactRequests"]),
     "[Preferences.Privacy.Contacts]" => $this->system->Select("Privacy_Contacts", "req v2w", $y["Privacy"]["Contacts"]),
     "[Preferences.Privacy.Contributions]" => $this->system->Select("Privacy_Contributions", "req v2w", $y["Privacy"]["Contributions"]),
     "[Preferences.Privacy.DLL]" => $this->system->Select("Privacy_DLL", "req v2w", $y["Privacy"]["DLL"]),
     "[Preferences.Privacy.Donate]" => $this->system->Select("Privacy_ContactInfoDonate", "req v2w", $y["Privacy"]["ContactInfoDonate"]),
     "[Preferences.Privacy.ForumsType]" => $this->system->Select("Privacy_ForumsType", "req v2w", $y["Privacy"]["ForumsType"]),
     "[Preferences.Privacy.Gender]" => $this->system->Select("Privacy_Gender", "req v2w", $y["Privacy"]["Gender"]),
     "[Preferences.Privacy.Journal]" => $this->system->Select("Privacy_Journal", "req v2w", $y["Privacy"]["Journal"]),
     "[Preferences.Privacy.LastActivity]" => $this->system->Select("Privacy_LastActivity", "req v2w", $y["Privacy"]["LastActivity"]),
     "[Preferences.Privacy.LookMeUp]" => $this->system->Select("Index", "req v2w", $y["Privacy"]["LookMeUp"]),
     "[Preferences.Privacy.MSG]" => $this->system->Select("Privacy_MSG", "req v2w", $y["Privacy"]["MSG"]),
     "[Preferences.Privacy.NSFW]" => $this->system->Select("nsfw", "req v2w", $y["Privacy"]["NSFW"]),
     "[Preferences.Privacy.OnlineStatus]" => $this->system->Select("Privacy_OnlineStatus", "req v2w", $y["Privacy"]["OnlineStatus"]),
     "[Preferences.Privacy.Posts]" => $this->system->Select("Privacy_Posts", "req v2w", $y["Privacy"]["Posts"]),
     "[Preferences.Privacy.Products]" => $this->system->Select("Privacy_Products", "req v2w", $y["Privacy"]["Products"]),
     "[Preferences.Privacy.Profile]" => $this->system->Select("Privacy_Profile", "req v2w", $y["Privacy"]["Profile"]),
     "[Preferences.Privacy.Registered]" => $this->system->Select("Privacy_Registered", "req v2w", $y["Privacy"]["Registered"]),
     "[Preferences.Privacy.RelationshipStatus]" => $this->system->Select("Privacy_RelationshipStatus", "req v2w", $y["Privacy"]["RelationshipStatus"]),
     "[Preferences.Privacy.RelationshipWith]" => $this->system->Select("Privacy_RelationshipWith", "req v2w", $y["Privacy"]["RelationshipWith"]),
     "[Preferences.Privacy.Shop]" => $this->system->Select("Privacy_Shop", "req v2w", $y["Privacy"]["Shop"])
    ], $this->system->Page("e54cb66a338c9dfdcf0afa2fec3b6d8a")]);
   }
   return $this->system->Card([
    "Back" => "",
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "DN",
    "PIN",
    "UN",
    "email"
   ]);
   $header = "Error";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(empty($data["Personal_DisplayName"])) {
    $r = "Your Display Name is missing.";
   } elseif(empty($data["Personal_Email"])) {
    $r = "Your E-Mail is missing.";
   } elseif(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = "The PINs do not match.";
   } elseif($this->system->ID == $you) {
    $r = "You must be signed in to continue.";
   } else {
    $accessCode = "Accepted";
    $header = "Done";
    $newMember = $this->system->NewMember(["Username" => $you]);
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
    $newMember["Points"] = $y["Points"] + $this->system->core["PTS"]["NewContent"];
    $newMember["Privacy"]["LookMeUp"] = $data["Index"];
    $newMember["Privacy"]["NSFW"] = $data["nsfw"];
    $newMember["Rank"] = $y["Rank"];
    $this->system->Data("Save", ["mbr", md5($you), $newMember]);
    $r = "Your Preferences were saved!";
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $this->system->Dialog([
      "Body" => $this->system->Element(["p", $r]),
      "Header" => $header
     ])
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function SaveDeactivate(array $a) {
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(1 == 1) {
    // DEACTIVATE PROFILE
   }
  }
  function SaveDelete(array $a) {
   $data = $a["Data"] ?? [];
   $y = $this->you;
   // DELETE PROFILE
   /* DELETE CONVERSATION
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(1 == 1) {
    if(!empty($this->system->Data("Get", ["conversation", md5("MBR_".$y["Login"]["Username"])]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => md5("MBR_".$y["Login"]["Username"])]
     ]);
    }
   }
   */
  }
  function SavePassword(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "CurrentPassword",
    "NewPassword",
    "NewPassword2"
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
   } elseif(empty($data["CurrentPassword"])) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must enter your current Password."
     ]),
     "Header" => "Error"
    ]);
   } elseif(empty($data["NewPassword"]) || empty($data["NewPassword2"])) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must enter and confirm your new Password."
     ]),
     "Header" => "Error"
    ]);
   } elseif(md5($data["CurrentPassword"]) != $y["Login"]["Password"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Passwords do not match."
     ]),
     "Header" => "Error"
    ]);
   } elseif($data["NewPassword"] != $data["NewPassword2"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The new Passwords do not match."
     ]),
     "Header" => "Error"
    ]);
   } else {
    $accessCode = "Accepted";
    $newPassword = md5($data["NewPassword"]);
    $y["Login"]["Password"] = $newPassword;
    $this->system->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "Your Password has been updated."
     ]),
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
    "Success" => "CloseDialog"
   ]);
  }
  function SavePIN(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "CurrentPIN",
    "NewPIN",
    "NewPIN2"
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
   } elseif(empty($data["CurrentPIN"])) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must enter your current PIN."
     ]),
     "Header" => "Error"
    ]);
   } elseif(empty($data["NewPIN"]) || empty($data["NewPIN2"])) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must enter and confirm your new PIN."
     ]),
     "Header" => "Error"
    ]);
   } elseif(!is_numeric($data["NewPIN"]) || !is_numeric($data["NewPIN2"])) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "PINs must be numeric (0-9)."]),
     "Header" => "Error"
    ]);
   } elseif(md5($data["CurrentPIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif($data["NewPIN"] != $data["NewPIN2"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The new PINs do not match."]),
     "Header" => "Error"
    ]);
   } else {
    $accessCode = "Accepted";
    $newPIN = md5($data["NewPIN"]);
    $y["Login"]["PIN"] = $newPIN;
    $this->system->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "Your PIN has been updated."]),
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
    "Success" => "CloseDialog"
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
   $un = $data["UN"];
   $y = $this->you;
   if(!empty($un)) {
    $un = base64_decode($un);
    $t = ($un == $y["Login"]["Username"]) ? $y : $this->system->Member($un);
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out ".$t["Personal"]["DisplayName"]."'s profile!"
     ]).$this->system->Element([
      "div", "[Member:$un]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$un&Type=Member",
     "[Share.ContentID]" => "Member",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $un,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $t["Personal"]["DisplayName"]."'s Profile"
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
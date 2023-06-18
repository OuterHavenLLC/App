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
     $article = $this->system->Data("Get", [
      "pg",
      $data["ArticleID"]
     ]) ?? [];
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
   $accessCode = "Denied";
   $r = [];
   $tpl = $this->system->Page("ae30582e627bc060926cfacf206920ce");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID != $you) {
    $accessCode = "Accepted";
    $bulletins = $this->system->Data("Get", [
     "bulletins",
     md5($you)
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
    $this->system->Data("Save", ["bulletins", md5($you), $bulletins]);
   }
   return $this->system->JSONResponse([
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
    $r = $this->system->Change([[
     "[Preferences.AuthPIN]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "class" => "AuthPIN".md5($you),
        "name" => "PIN",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text"
      ]
     ]),
     "[Preferences.Donations.Patreon]" => $this->system->RenderInputs([
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
       "Value" => $y["Donations"]["Patreon"]
      ]
     ]),
     "[Preferences.Donations.PayPal]" => $this->system->RenderInputs([
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
       "Value" => $y["Donations"]["PayPal"]
      ]
     ]),
     "[Preferences.Donations.SubscribeStar]" => $this->system->RenderInputs([
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
       "Value" => $y["Donations"]["SubscribeStar"]
      ]
     ]),
     "[Preferences.General]" => $this->system->RenderInputs([
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
       "Value" => $y["Personal"]["FirstName"]
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
       "Value" => $y["Personal"]["DisplayName"]
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
       "Value" => $y["Personal"]["Email"]
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        0 => "Offline",
        1 => "Online"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Online Status"
       ],
       "Name" => "OnlineStatus",
       "Type" => "Select",
       "Value" => $y["Activity"]["OnlineStatus"]
      ],
      [
       "Attributes" => [
        "class" => "Bio Xdecode",
        "id" => "EditBio",
        "name" => "Personal_Bio",
        "placeholder" => "A short Autobiography..."
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Biography",
        "WYSIWYG" => 1
       ],
       "Type" => "TextBox"
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
       "Value" => $y["Personal"]["Description"]
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        md5("Engaged") => "Engaged",
        md5("In a Relationship") => "In a Relationship",
        md5("It's Complicated") => "It's Complicated",
        md5("Married") => "Married",
        md5("Single") => "Single",
        md5("Swinger") => "Swinger",
        md5("Widowed") => "Widowed"
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
       "Value" => $relationshipWith
      ]
     ]),
     "[Preferences.General.Birthday]" => $this->system->RenderInputs([
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
     ]),
     "[Preferences.ID]" => md5($you),
     "[Preferences.Links.EditShop]" => base64_encode("v=".base64_encode("Shop:Edit")."&ID=".base64_encode(md5($y["Login"]["Username"]))),
     "[Preferences.Links.NewPassword]" => "v=".base64_encode("Profile:NewPassword"),
     "[Preferences.Links.NewPIN]" => "v=".base64_encode("Profile:NewPIN"),
     "[Preferences.Personal]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "Personal_MinimalDesign"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Minimal Design",
        "Selected" => $choseMinimalDesign
       ],
       "Text" => "Choose whether or not to render design and social media elements such as reactions",
       "Type" => "Check",
       "Value" => 1
      ]
     ]),
     "[Preferences.Privacy]" => $this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Albums",
      "Title" => "Albums",
      "Value" => $y["Privacy"]["Albums"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Archive",
      "Title" => "Archive",
      "Value" => $y["Privacy"]["Archive"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Albums",
      "Title" => "Albums",
      "Value" => $y["Privacy"]["Albums"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Articles",
      "Title" => "Articles",
      "Value" => $y["Privacy"]["Articles"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Comments",
      "Title" => "Comments",
      "Value" => $y["Privacy"]["Comments"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_ContactInfoEmails",
      "Title" => "Contact Emails",
      "Value" => $y["Privacy"]["ContactInfoEmails"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_ContactInfo",
      "Title" => "Contact Information",
      "Value" => $y["Privacy"]["ContactInfo"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_ContactRequests",
      "Title" => "Contact Requests",
      "Value" => $y["Privacy"]["ContactRequests"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Contacts",
      "Title" => "Contacts",
      "Value" => $y["Privacy"]["Contacts"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Contributions",
      "Title" => "Contributions",
      "Value" => $y["Privacy"]["Contributions"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_DLL",
      "Title" => "Downloads",
      "Value" => $y["Privacy"]["DLL"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_ContactInfoDonate",
      "Title" => "Donations",
      "Value" => $y["Privacy"]["ContactInfoDonate"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_ForumsType",
      "Title" => "Forum Type",
      "Value" => $y["Privacy"]["ForumsType"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Gender",
      "Title" => "Gender",
      "Value" => $y["Privacy"]["Gender"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Journal",
      "Title" => "Journal",
      "Value" => $y["Privacy"]["Journal"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_LastActivity",
      "Title" => "Last Activity",
      "Value" => $y["Privacy"]["LastActivity"]
     ]).$this->system->RenderInputs([
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
        "HeaderText" => "Look Me Up"
       ],
       "Name" => "Privacy_LookMeUp",
       "Title" => "Allow others to search for you?",
       "Type" => "Select",
       "Value" => $y["Privacy"]["LookMeUp"]
      ]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_MSG",
      "Title" => "Messages",
      "Value" => $y["Privacy"]["MSG"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "Privacy_NSFW",
      "Title" => "NSFW",
      "Value" => $y["Privacy"]["NSFW"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_OnlineStatus",
      "Title" => "Online Status",
      "Value" => $y["Privacy"]["OnlineStatus"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Posts",
      "Title" => "Posts",
      "Value" => $y["Privacy"]["Posts"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Products",
      "Title" => "Products",
      "Value" => $y["Privacy"]["Products"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Profile",
      "Title" => "Profile",
      "Value" => $y["Privacy"]["Profile"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Registered",
      "Title" => "Registered",
      "Value" => $y["Privacy"]["Registered"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_RelationshipStatus",
      "Title" => "Relationship Status",
      "Value" => $y["Privacy"]["RelationshipStatus"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_RelationshipWith",
      "Title" => "Relationship With",
      "Value" => $y["Privacy"]["RelationshipWith"]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "Privacy",
      "Name" => "Privacy_Shop",
      "Title" => "Shop",
      "Value" => $y["Privacy"]["Shop"]
     ])
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
    "Personal_DisplayName",
    "PIN",
    "email"
   ]);
   $email = $data["Personal_Email"] ?? "";
   $emailIsTaken = 0;
   $header = "Error";
   $members = $this->system->DatabaseSet("MBR") ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   foreach($members as $key => $value) {
    $value = str_replace("c.oh.mbr.", "", $value);
    $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
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
   $you = $y["Login"]["Username"];
   // DELETE PROFILE
   /* DELETE CONVERSATION
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(1 == 1) {
    if(!empty($this->system->Data("Get", ["conversation", md5("MBR_$you")]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => md5("MBR_$you")]
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
    $y["Login"]["Password"] = md5($data["NewPassword"]);
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
     "Body" => $this->system->Element([
      "p", "PINs must be numeric (0-9)."
     ]),
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
    $y["Login"]["PIN"] = md5($data["NewPIN"]);
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
  function SaveSignIn(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["PW", "UN"]);
   $i = 0;
   $password = $data["PW"];
   $r = "An internal error has ocurred.";
   $responseType = "Dialog";
   $username = $data["UN"];
   if(empty($password) || empty($username)) {
    if(empty($password)) {
     $field = "Password";
    } elseif(empty($username)) {
     $field = "Username";
    }
    $r = "The $field is missing.";
   } else {
    $members = $this->system->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
     $member = $member["Login"]["Username"] ?? "";
     if($username == $member) {
      $i++;
     }
    } if($i > 0) {
     $member = $this->system->Data("Get", ["mbr", md5($username)]) ?? [];
     $password = md5($password);
     if($password == $member["Login"]["Password"]) {
      $accessCode = "Accepted";
      $responseType = "SignIn";
      $this->system->Statistic("LI");
      $r = $this->system->Encrypt($member["Login"]["Username"].":".$member["Login"]["Password"]);
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
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", $r]),
     "Header" => "Sign In Failed"
    ]);
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->FixMissing($data, [
    "BirthMonth",
    "BirthYear",
    "Email",
    "Name",
    "Password",
    "Password2",
    "Personal_Gender",
    "PIN",
    "PIN2",
    "SOE",
    "Username"
   ]);
   $_MinimumAge = $this->system->core["minRegAge"];
   $birthYear = $data["BirthYear"] ?? 1995;
   $age = date("Y") - $birthYear;
   $ck = ($age > $_MinimumAge) ? 1 : 0;
   $firstName = ($data["Personal_Gender"] == "Male") ? "John" : "Jane";
   $i = 0;
   $members = $this->system->DatabaseSet("MBR");
   $password = $data["Password"];
   $r = "Internal Error";
   $username = $this->system->CallSign($data["Username"]);
   foreach($members as $key => $value) {
    $value = str_replace("c.oh.mbr.", "", $value);
    $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
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
   } elseif($data["Username"] == $this->system->ID) {
    $r = $this->system->ID." is the system profile and cannot be used.";
   } elseif($ck == 0) {
    $r = "You must be $_MinimumAge or older to sign up.";
   } elseif($i > 0) {
    $r = "The Username <em>$username</em> is already in use.";
   } else {
    $accessCode = "Accepted";
    $birthMonth = $data["BirthMonth"] ?? 10;
    $now = $this->system->timestamp;
    if($data["SOE"] == 1) {
     $x = $this->system->Data("Get", ["x", md5("ContactList")]) ?? [];
     $x[$data["Email"]] = [
      "Email" => $data["Email"],
      "Name" => $firstName,
      "Phone" => "N/A",
      "SendOccasionalEmails" => $data["SOE"],
      "UN" => $username,
      "Updated" => $now
     ];
     $this->system->Data("Save", ["x", md5("ContactList"), $x]);
    }
    $this->system->Data("Save", ["cms", md5($username), [
     "Contacts" => [],
     "Requests" => []
    ]]);
    $this->system->Data("Save", ["fs", md5($username), [
     "Albums" => [
      md5("unsorted") => [
       "ID" => md5("unsorted"),
       "Created" => $this->system->timestamp,
       "ICO" => "",
       "Modified" => $this->system->timestamp,
       "Title" => "Unsorted",
       "Description" => "Files are uploaded here by default.",
       "NSFW" => 0,
       "Privacy" => md5("Public")
      ]
     ],
     "Files" => []
    ]]);
    $this->system->Data("Save", [
     "mbr", md5($username), $this->system->NewMember([
      "Age" => $age,
      "BirthMonth" => $birthMonth,
      "BirthYear" => $birthYear,
      "DisplayName" => $username,
      "Email" => $data["Email"],
      "FirstName" => $firstName,
      "Gender" => $data["Personal_Gender"],
      "Password" => $password,
      "PIN" => md5($data["PIN"]),
      "Username" => $username
     ])
    ]);
    $this->system->Data("Save", ["stream", md5($username), []]);
    $this->system->Data("Save", ["shop", md5($username), [
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
    $this->system->Statistic("MBR");
    $r = $this->system->Change([[
     "[Success.SignIn]" => "v=".base64_encode("Common:SignIn"),
     "[Success.Username]" => $username
    ], $this->system->Page("872fd40c7c349bf7220293f3eb64ab45")]);
   } if($accessCode != "Accepted") {
    $r = $this->system->Change([[
     "[2FA.Error.Message]" => $r,
     "[2FA.Error.ViewPairID]" => "2FAStep1"
    ], $this->system->Page("ef055d5546ab5fead63311a3113f3f5f")]);
   }
   return $r;
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
  function SignIn(array $a) {
   return $this->system->Dialog([
    "Body" => $this->system->Change([[
     "[SignIn.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "class" => "req",
        "name" => "UN",
        "placeholder" => "Username",
        "type" => "text"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Username"
       ],
       "Type" => "Text"
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PW",
        "placeholder" => "Password",
        "type" => "password"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "NONAME",
        "Header" => 1,
        "HeaderText" => "Password"
       ],
       "Type" => "Text"
      ]
     ])
    ], $this->system->Page("ff434d30a54ee6d6bbe5e67c261b2005")]),
    "Header" => "Sign In",
    "Option" => $this->system->Element(["button", "Cancel", [
     "class" => "dBC v2 v2w"
    ]]),
    "Option2" => $this->system->Element(["button", "Sign In", [
     "class" => "BBB SendData v2 v2w",
     "data-form" => ".SignIn",
     "data-processor" => base64_encode("v=".base64_encode("Profile:SaveSignIn"))
    ]])
   ]);
  }
  function SignUp(array $a) {
   $birthMonths = [];
   $birthYears = [];
   for($i = 1; $i <= 12; $i++) {
    $birthMonths[$i] = $i;
   } for($i = 1776; $i <= date("Y"); $i++) {
    $birthYears[$i] = $i;
   }
   return $this->system->Card([
    "Front" => $this->system->Change([[
     "[SignUp.2FA]" => base64_encode("v=".base64_encode("TwoFactorAuthentication:FirstTime")),
     "[SignUp.Age.Month]" => $this->system->Select("BirthMonth", "req v2w"),
     "[SignUp.Age.Year]" => $this->system->Select("BirthYear", "req v2w"),
     "[SignUp.Gender]" => $this->system->Select("gender", "req"),
     "[SignUp.MinAge]" => $this->system->core["minAge"],
     "[SignUp.ReturnView]" => base64_encode(json_encode([
      "Group" => "Profile",
      "View" => "SaveSignUp"
     ], true)),
     "[SignUp.SendOccasionalEmails]" => $this->system->Select("SOE", "req v2w")
    ], $this->system->Page("c48eb7cf715c4e41e2fb62bdfa60f198")])
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
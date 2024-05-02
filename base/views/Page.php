<?php
 Class Page extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Banish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $article = $this->core->Data("Get", ["pg", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself.",
     "Header" => "Error"
    ];
    if($mbr != $article["UN"] && $mbr != $y["Login"]["Username"]) {
    $accessCode = "Accepted";
     $r = [
      "Actions" => [
       $this->core->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->core->Element(["button", "Banish $mbr", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Page:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $mbr from <em>".$article["Title"]."</em>?",
      "Header" => "Banish $mbr?"
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
  function Card(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Not Found"
   ];
   if(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $article = $this->core->Data("Get", [
     "pg",
     base64_decode($data["ID"])
    ]) ?? [];
    $r = $this->core->Element([
     "h1", $article["Title"], ["class" => "UpperCase"]
    ]).$this->core->Element([
     "div", $this->core->PlainText([
      "BBCodes" => 1,
      "Data" => $article["Body"],
      "Decode" => 1,
      "Display" => 1,
      "HTMLDecode" => 1
     ]), ["class" => "NONAME"]
    ]);
   }
   $r = [
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
  function ChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN", "Member"]);
   $id = $data["ID"];
   $member = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $accessCode = "Accepted";
    $article = $this->core->Data("Get", ["pg", $id]) ?? [];
    $contributors = $article["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $article["Contributors"] = $contributors;
    $this->core->Data("Save", ["pg", $id, $article]);
    $r = [
     "Body" => "$member's Role within <em>".$article["Title"]."</em> was Changed to $role.",
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
  function Edit(array $a) {
   $accessCode = "Denied";
   $buttion = "";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $time = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $id = ($new == 1) ? md5($you."_PG_".$time) : $id;
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditPage$id",
     "data-processor" => base64_encode("v=".base64_encode("Page:Save"))
    ]]);
    $article = $this->core->Data("Get", ["pg", $id]) ?? [];
    $article = $this->core->FixMissing($article, [
     "Body",
     "Category",
     "Description",
     "ICO-SRC",
     "Title"
    ]);
    $additionalContent = $this->view(base64_encode("WebUI:AdditionalContent"), [
     "ID" => $id
    ]);
    $additionalContent = $this->core->RenderView($additionalContent);
    $attachments = "";
    $author = $article["UN"] ?? $you;
    $designViewEditor = "ArticleEditor$id".md5($time);
    $header = ($new == 1) ? "New Article" : "Edit ".$article["Title"];
    $products = "";
    if(!empty($article["Attachments"])) {
     $attachments = base64_encode(implode(";", $article["Attachments"]));
    } if(!empty($article["Products"])) {
     $products = base64_encode(implode(";", $article["Products"]));
    }
    $categories = ($y["Rank"] == md5("High Command")) ? [
     "CA" => "Article",
     "JE" => "Journal Entry",
     "PR" => "Press Release"
    ] : [
     "CA" => "Article",
     "JE" => "Journal Entry"
    ];
    $category = $article["Category"] ?? "CA";
    $em = base64_encode("LiveView:EditorMossaic");
    $ep = base64_encode("LiveView:EditorProducts");
    $es = base64_encode("LiveView:EditorSingle");
    $nsfw = $article["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $article["Privacy"] ?? $y["Privacy"]["Posts"];
    $sc = base64_encode("Search:Containers");
    $r = $this->core->Change([[
     "[Article.AdditionalContent]" => $additionalContent["Extension"],
     "[Article.Body]" => base64_encode($this->core->PlainText([
      "Data" => $article["Body"],
      "Decode" => 1
     ])),
     "[Article.Categories]" => json_encode($categories, true),
     "[Article.Category]" => $category,
     "[Article.Chat]" => base64_encode("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($article["Description"])."&ID=".base64_encode($id)."&Title=".base64_encode($article["Title"])."&Username=".base64_encode($author)),
     "[Article.CoverPhoto]" => $article["ICO-SRC"],
     "[Article.CoverPhoto.LiveView]" => $additionalContent["LiveView"]["CoverPhoto"],
     "[Article.Description]" => base64_encode($article["Description"]),
     "[Article.DesignView]" => $designViewEditor,
     "[Article.Downloads]" => $attachments,
     "[Article.Downloads.LiveView]" => $additionalContent["LiveView"]["DLC"],
     "[Article.Header]" => $header,
     "[Article.ID]" => $id,
     "[Article.New]" => $new,
     "[Article.Products]" => $products,
     "[Article.Products.LiveView]" => $additionalContent["LiveView"]["Products"],
     "[Article.Title]" => base64_encode($article["Title"]),
     "[Article.Visibility.NSFW]" => $nsfw,
     "[Article.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("68526a90bfdbf5ea5830d216139585d7")]);
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
  function Home(array $a) {
   $_ViewTitle = $this->core->config["App"]["Name"];
   $accessCode = "Denied";
   $base = $this->core->efs;
   $data = $a["Data"] ?? [];
   $lpg = $data["lPG"] ?? "";
   $b2 = $data["b2"] ?? "the Archive";
   $back = $data["back"] ?? 0;
   $back = ($back == 1) ? $this->core->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI header",
    "data-type" => $lpg
   ]]) : "";
   $card = $data["CARD"] ?? 0;
   $id = $data["ID"];
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Article could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $active = 0;
    $admin = 0;
    $bl = $this->core->CheckBlocked([$y, "Pages", $id]);
    $_Article = $this->core->GetContentData([
     "BackTo" => "",
     "Blacklisted" => $bl,
     "ID" => base64_encode("Page;$id")
    ]);
    if($_Article["Empty"] == 0) {
     $article = $_Article["DataModel"];
     $options = $_Article["ListItem"]["Options"];
     $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
     $_ViewTitle = $_Article["ListItem"]["Title"] ?? $_ViewTitle;
     $contributors = $article["Contributors"] ?? [];
     $ck = ($article["UN"] == $you) ? 1 : 0;
     if(in_array($article["Category"], ["CA", "JE"]) && $bl == 0) {
      foreach($contributors as $member => $role) {
       if($active == 0 && $member == $you) {
        $active = 1;
        if($admin == 0 && $role == "Admin") {
         $admin = 1;
        }
       }
      }
      $blockCommand = ($bl == 0) ? "Block" : "Unblock";
      $actions = ($ck == 0) ? $this->core->Element([
       "button", $blockCommand, [
        "class" => "Small UpdateButton v2",
        "data-processor" => $options["Block"]
       ]
      ]) : "";
      $actions .= (!empty($chat) && ($active == 1 || $ck == 1)) ? $this->core->Element([
       "button", "Chat", [
        "class" => "OpenCard Small v2 v2w",
        "data-view" => $options["Chat"]
       ]
      ]) : "";
      $actions .= ($admin == 1 || $active == 1 || $ck == 1) ? $this->core->Element([
       "button", "Edit", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Edit"]
       ]
      ]) : "";
      $actions .= ($admin == 1) ? $this->core->Element([
       "button", "Contributors", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Contributors"]
       ]
      ]) : "";
      $author = ($article["UN"] == $you) ? $y : $this->core->Member($article["UN"]);
      $ck = ($author["Login"]["Username"] == $you) ? 1 : 0;
      $contributors = $article["Contributors"] ?? [];
      $contributors[$article["UN"]] = "Admin";
      $description = ($ck == 1) ? "You have not added a Description." : "";
      $description = ($ck == 0) ? $author["Personal"]["DisplayName"]." has not added a Description." : $description;
      $description = (!empty($author["Personal"]["Description"])) ? $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $author["Personal"]["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]) : $description;
      $share = ($ck == 1 || $article["Privacy"] == md5("Public")) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element(["button", "Share", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Share"]
      ]]) : "";
      $r = $this->core->Change([[
       "[Article.Actions]" => $actions,
       "[Article.Attachments]" => $_Article["ListItem"]["Attachments"],
       "[Article.Back]" => $back,
       "[Article.Body]" => $_Article["ListItem"]["Body"],
       "[Article.Contributors]" => base64_encode("v=".base64_encode("LiveView:MemberGrid")."&List=".base64_encode(json_encode($contributors, true))),
       "[Article.Conversation]" => $this->core->Change([[
        "[Conversation.CRID]" => $id,
        "[Conversation.CRIDE]" => base64_encode($id),
        "[Conversation.Level]" => base64_encode(1),
        "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
       ], $this->core->Extension("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
       "[Article.CoverPhoto]" => $_Article["ListItem"]["CoverPhoto"],
       "[Article.Created]" => $this->core->TimeAgo($article["Created"]),
       "[Article.Description]" => $_Article["ListItem"]["Description"],
       "[Article.ID]" => $id,
       "[Article.Modified]" => $_Article["ListItem"]["Modified"],
       "[Article.Notes]" => $options["Notes"],
       "[Article.Report]" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("Page;$id")),
       "[Article.Share]" => $share,
       "[Article.Subscribe]" => $options["Subscribe"],
       "[Article.Title]" => $_Article["ListItem"]["Title"],
       "[Article.Votes]" => $options["Vote"],
       "[Member.DisplayName]" => $author["Personal"]["DisplayName"],
       "[Member.ProfilePicture]" => $this->core->ProfilePicture($author, "margin:0.5em;max-width:12em;width:calc(100% - 1em)"),
       "[Member.Description]" => $description
      ], $this->core->Extension("b793826c26014b81fdc1f3f94a52c9a6")]);
     } else {
      $r = $_Article["ListItem"]["Body"];
     }
    }
   }
   $r = ($card == 1) ? [
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
    "ResponseType" => "View",
    "Title" => $_ViewTitle
   ]);
  }
  function Invite(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? base64_encode("");
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $action = $this->core->Element(["button", "Send Invite", [
     "class" => "CardButton SendData dB2C",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Page:SendInvite"))
    ]]);
    $content = [];
    $contentOptions = $y["Pages"] ?? [];
    foreach($contentOptions as $key => $value) {
     $article = $this->core->Data("Get", ["pg", $value]) ?? [];
     $content[$value] = $article["Title"];
    }
    $r = $this->core->Change([[
     "[Invite.Content]" => json_encode($content, true),
     "[Invite.ID]" => $id,
     "[Invite.Member]" => $member
    ], $this->core->Extension("80e444c34034f9345eee7399b4467646")]);
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $category = $data["Category"] ?? "";
    $i = 0;
    $isPublic = (in_array($category, ["CA", "JE"])) ? 1 : 0;
    $now = $this->core->timestamp;
    $articles = $this->core->DatabaseSet("Article");
    $title = $data["Title"] ?? "";
    foreach($articles as $key => $value) {
     $article = str_replace("nyc.outerhaven.pg.", "", $value);
     $article = $this->core->Data("Get", ["pg", $article]) ?? [];
     if($id != $article["ID"] && $article["Title"] == $title) {
      $i++;
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Article <em>$title</em> is taken.",
      "Header" => "Error"
     ];
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $article = $this->core->Data("Get", ["pg", $id]) ?? [];
     $coverPhoto = "";
     $coverPhotoSource = "";
     $attachments = $article["Attachments"] ?? [];
     $author = $article["UN"] ?? $you;
     $newCategory = "Article";
     $newCategory = ($category == "JE") ? "Journal Entry" : $newCategory;
     $contributors = $article["Contributors"] ?? [];
     $contributors[$author] = "Admin";
     $created = $article["Created"] ?? $now;
     $i = 0;
     $illegal = $article["Illegal"] ?? 0;
     $modifiedBy = $article["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["Posts"];
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Articles"];
     $products = $article["Products"] ?? [];
     $subscribers = $article["Subscribers"] ?? [];
     if(!empty($data["rATTI"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTI"])));
      foreach($dlc as $dlc) {
       if($i == 0 && !empty($dlc)) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         $t = $this->core->Member($f[0]);
         $efs = $this->core->Data("Get", [
          "fs",
          md5($t["Login"]["Username"])
         ]) ?? [];
         $coverPhoto = $f[0]."/".$efs["Files"][$f[1]]["Name"];
         $coverPhotoSource = base64_encode($f[0]."-".$f[1]);
        }
        $i++;
       }
      }
     } if(!empty($data["rATTF"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTF"])));
      foreach($dlc as $dlc) {
       if(!empty($dlc)) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         array_push($attachments, base64_encode($f[0]."-".$f[1]));
        }
       }
      }
     } if(!empty($data["rATTP"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTP"])));
      foreach($dlc as $dlc) {
       if(!empty($dlc)) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         array_push($prod, base64_encode($f[0]."-".$f[1]));
        }
       }
      }
     } if($isPublic == 1 && $new == 1) {
      $ck = ($author == $you) ? 1 : 0;
      $ck = (!in_array($id, $y["Pages"]) && $ck == 1) ? 1 : 0;
      foreach($subscribers as $key => $value) {
       $this->core->SendBulletin([
        "Data" => [
         "ArticleID" => $id
        ],
        "To" => $value,
        "Type" => "ArticleUpdate"
       ]);
      }
      if($ck == 1) {
       $newPages = $y["Pages"];
       array_push($newPages, $id);
       $y["Activity"]["LastActive"] = $now;
       $y["Pages"] = array_unique($newPages);
       $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
      }
     }
     $article = [
      "Attachments" => array_unique($attachments),
      "Body" => $this->core->PlainText([
       "Data" => $data["Body"],
       "Encode" => 1,
       "HTMLEncode" => 1
      ]),
      "Category" => $category,
      "Contributors" => $contributors,
      "Created" => $created,
      "Description" => htmlentities($data["Description"]),
      "ICO" => $coverPhoto,
      "ICO-SRC" => base64_encode($coverPhotoSource),
      "ID" => $id,
      "Illegal" => $illegal,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "NSFW" => $nsfw,
      "Privacy" => $privacy,
      "Products" => $products,
      "Title" => $title,
      "UN" => $author
     ];
     $this->core->Data("Save", ["pg", $id, $article]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     $r = [
      "Body" => "The $newCategory has been $actionTaken!",
      "Header" => "Done"
     ];
     if($new == 1) {
      $this->core->Statistic("New Article");
     } else {
      $this->core->Statistic("Edit Article");
      if($isPublic == 1) {
       foreach($subscribers as $key => $value) {
        $this->core->SendBulletin([
         "Data" => [
          "ArticleID" => $id,
          "Author" => $you
         ],
         "To" => $value,
         "Type" => "ArticleUpdate"
        ]);
       }
      }
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
  function SaveBanish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $article = $this->core->Data("Get", ["pg", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself.",
     "Header" => "Error"
    ];
    if($mbr != $article["UN"] && $mbr != $you) {
     $accessCode = "Accepted";
     $contributors = $article["Contributors"] ?? [];
     $newContributors = [];
     foreach($contributors as $member => $role) {
      if($mbr != $member) {
       $newContributors[$member] = $role;
      }
     }
     $article["Contributors"] = $newContributors;
     $this->core->Data("Save", ["pg", $id, $article]);
     $r = [
      "Body" => "$mbr was banished from <em>".$article["Title"]."</em>.",
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $pin = $data["PIN"] ?? "";
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($pin) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match.",
     "Header" => "Error"
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $newArticles = [];
    $articles = $y["Pages"] ?? [];
    if(!empty($this->core->Data("Get", ["conversation", $id]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
    } foreach($articles as $key => $value) {
     if($id != $value) {
      array_push($newArticles, $value);
     }
    }
    $y["Pages"] = $newArticles;
    $this->core->Data("Purge", ["chat", $id]);
    $this->core->Data("Purge", ["local", $id]);
    $this->core->Data("Purge", ["pg", $id]);
    $this->core->Data("Purge", ["votes", $id]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "The Page was deleted.",
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
  function SendInvite(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "ID",
    "Member",
    "Role"
   ]);
   $i = 0;
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $article = $this->core->Data("Get", ["pg", $id]) ?? [];
    $members = $this->core->DatabaseSet("Member");
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     if($i == 0) {
      $t = $this->core->Data("Get", ["mbr", $value]) ?? [];
      if($mbr == $t["Login"]["Username"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $r = [
      "Body" => "The Member $mbr does not exist.",
      "Header" => "Error"
     ];
    } elseif(empty($article["ID"])) {
     $r = [
      "Body" => "The Article does not exist.",
      "Header" => "Error"
     ];
    } elseif($mbr == $article["UN"]) {
     $r = [
      "Body" => "$mbr owns <em>".$article["Title"]."</em>.",
      "Header" => "Error"
     ];
    } elseif($mbr == $y["Login"]["Username"]) {
     $r = [
      "Body" => "You are already a contributor.",
      "Header" => "Error"
     ];
    } else {
     $active = 0;
     $contributors = $article["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      if($mbr == $member) {
       $active++;
      }
     } if($active == 1) {
      $r = [
       "Body" => "$mbr is already a contributor.",
       "Header" => "Error"
      ];
     } else {
      $accessCode = "Accepted";
      $role = ($data["Role"] == 1) ? "Member" : "Admin";
      $contributors[$mbr] = $role;
      $article["Contributors"] = $contributors;
      $this->core->SendBulletin([
       "Data" => [
        "ArticleID" => $id,
        "Member" => $mbr,
        "Role" => $role
       ],
       "To" => $mbr,
       "Type" => "InviteToArticle"
      ]);
      $this->core->Data("Save", ["pg", $id, $article]) ?? [];
      $r = [
       "Body" => "$mbr was notified of your invitation.",
       "Header" => "Invitation Sent"
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
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
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
    $article = $this->core->Data("Get", ["pg", $id]) ?? [];
    $responseType = "UpdateText";
    $subscribers = $article["Subscribers"] ?? [];
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
    $article["Subscribers"] = $subscribers;
    $this->core->Data("Save", ["pg", $id, $article]);
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
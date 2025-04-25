<?php
 Class Page extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Banish(array $a) {
   $_AccessCode = "Denied";
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
    $_AccessCode = "Accepted";
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Card(array $a) {
   $_AccessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Not Found"
   ];
   if(!empty($data["ID"])) {
    $_AccessCode = "Accepted";
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function ChangeMemberRole(array $a) {
   $_AccessCode = "Denied";
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
    $_AccessCode = "Accepted";
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function Edit(array $a) {
   $_AccessCode = "Denied";
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
    $_AccessCode = "Accepted";
    $id = base64_decode($id);
    $id = ($new == 1) ? $this->core->UUID("ArticleBy$you") : $id;
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditPage$id",
     "data-processor" => base64_encode("v=".base64_encode("Page:Save"))
    ]]);
    $article = $this->core->Data("Get", ["pg", $id]);
    $article = $this->core->FixMissing($article, [
     "Body",
     "Category",
     "Description",
     "Title"
    ]);
    $albums = $article["Albums"] ?? [];
    $articles = $article["Articles"] ?? [];
    $attachments = $article["Attachments"] ?? [];
    $author = $article["UN"] ?? $you;
    $blogs = $article["Blogs"] ?? [];
    $blogPosts = $article["BlogPosts"] ?? [];
    $chats = $article["Chat"] ?? [];
    $coverPhoto = $article["CoverPhoto"] ?? "";
    $designViewEditor = "ArticleEditor$id";
    $forums = $article["Forums"] ?? [];
    $forumPosts = $article["ForumPosts"] ?? [];
    $header = ($new == 1) ? "New Article" : "Edit ".$article["Title"];
    $members = $article["Members"] ?? [];
    $polls = $article["Polls"] ?? [];
    $products = $article["Products"] ?? [];
    $shops = $article["Shops"] ?? [];
    $updates = $article["Updates"] ?? [];
    $categories = ($y["Rank"] == md5("High Command")) ? [
     "CA" => "Article",
     "JE" => "Journal Entry",
     "PR" => "Press Release"
    ] : [
     "CA" => "Article",
     "JE" => "Journal Entry"
    ];
    $category = $article["Category"] ?? "CA";
    $nsfw = $article["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $article["PassPhrase"] ?? "";
    $privacy = $article["Privacy"] ?? $y["Privacy"]["Posts"];
    $attachments = $this->view(base64_encode("WebUI:Attachments"), [
     "Header" => "Attachments",
     "ID" => $id,
     "Media" => [
      "Album" => $albums,
      "Article" => $articles,
      "Attachment" => $attachments,
      "Blog" => $blogs,
      "BlogPost" => $blogPosts,
      "Chat" => $chats,
      "CoverPhoto" => $coverPhoto,
      "Forum" => $forums,
      "ForumPost" => $forumPosts,
      "Member" => $members,
      "Poll" => $polls,
      "Product" => $products,
      "Shop" => $shops,
      "Update" => $updates
     ]
    ]);
    $translateAndViewDeign = $this->view(base64_encode("WebUI:Attachments"), [
     "ID" => $id,
     "Media" => [
      "Translate" => [],
      "ViewDesign" => []
     ]
    ]);
    $r = $this->core->Change([[
     "[Article.Attachments]" => $this->core->RenderView($attachments),
     "[Article.Body]" => base64_encode($this->core->PlainText([
      "Data" => $article["Body"],
      "Decode" => 1
     ])),
     "[Article.Categories]" => json_encode($categories, true),
     "[Article.Category]" => $category,
     "[Article.Chat]" => base64_encode("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($article["Description"])."&ID=".base64_encode($id)."&Title=".base64_encode($article["Title"])."&Username=".base64_encode($author)),
     "[Article.Description]" => base64_encode($article["Description"]),
     "[Article.DesignView]" => $designViewEditor,// TO BE DISOLVED
     "[Article.Header]" => $header,
     "[Article.ID]" => $id,
     "[Article.New]" => $new,
     "[Article.PassPhrase]" => base64_encode($passPhrase),
     "[Article.Title]" => base64_encode($article["Title"]),
     "[Article.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign),
     "[Article.Visibility.NSFW]" => $nsfw,
     "[Article.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("68526a90bfdbf5ea5830d216139585d7")]);
    $r = [
     "Action" => $action,
     "Front" => $r
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
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
   $_AccessCode = "Denied";
   $data = $a["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $backTo = $data["BackTo"] ?? "the Archive";
   $base = $this->core->efs;
   $card = $data["CARD"] ?? 0;
   $id = $data["ID"];
   $parentPage = $data["ParentPage"] ?? "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Article could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_AccessCode = "Accepted";
    $active = 0;
    $admin = 0;
    $bl = $this->core->CheckBlocked([$y, "Pages", $id]);
    $_Article = $this->core->GetContentData([
     "BackTo" => $backTo,
     "Blacklisted" => $bl,
     "ID" => base64_encode("Page;$id")
    ]);
    if($_Article["Empty"] == 0) {
     $article = $_Article["DataModel"];
     $passPhrase = $article["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "ParentPage" => $parentPage,
       "Text" => base64_encode("Please enter the Pass Phrase the Author gave you to access <em>".$_Article["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "AddTo" => $addTo,
        "BackTo" => $backTo,
        "SecureKey" => base64_encode($passPhrase),
        "ID" => $id,
        "ParentPage" => $parentPage,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Page:Home")
       ], true))
      ]]);
      $r = $this->core->RenderView($r);
     } elseif($verifyPassPhrase == 1) {
      $_AccessCode = "Denied";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $r = $this->core->Element(["p", "The Key is missing."]);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $r = $this->core->Element(["p", "The Keys do not match."]);
      } else {
       $_AccessCode = "Accepted";
       $r = $this->view(base64_encode("Page:Home"), ["Data" => [
        "AddTo" => $addTo,
        "BackTo" => $backTo,
        "ID" => $id,
        "ParentPage" => $parentPage,
        "ViewProtectedContent" => 1
       ]]);
       $r = $this->core->RenderView($r);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_ViewTitle = $_Article["ListItem"]["Title"] ?? $_ViewTitle;
      $options = $_Article["ListItem"]["Options"];
      $chat = $this->core->Data("Get", ["chat", $id]);
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
       $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
       $actions = (!empty($addToData)) ? $this->core->Element([
        "button", "Attach", [
         "class" => "Attach Small v2",
         "data-input" => base64_encode($addToData[1]),
         "data-media" => base64_encode("Page;$id")
        ]
       ]) : "";
       $actions .= ($ck == 0) ? $this->core->Element([
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
       $back = (!empty($parentPage)) ? $this->core->Element(["button", "Back to $backTo", [
        "class" => "GoToParent LI header",
        "data-type" => $parentPage
       ]]) : "";
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
       $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($article, "LiveView");
       $share = ($ck == 1 || $article["Privacy"] == md5("Public")) ? 1 : 0;
       $share = ($share == 1) ? $this->core->Element(["button", "Share", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Share"]
       ]]) : "";
       $verified = $author["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       $r = $this->core->Change([[
        "[Article.Actions]" => $actions,
        "[Article.Attachments]" => $_Article["ListItem"]["Attachments"],
        "[Article.Back]" => $back,
        "[Article.Body]" => $this->core->PlainText([
         "Data" => $article["Body"],
         "Decode" => 1,
         "HTMLDecode" => 1
        ]),
        "[Article.Contributors]" => base64_encode("v=".base64_encode("LiveView:MemberGrid")."&List=".base64_encode(json_encode($contributors, true))),
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
        "[Attached.Albums]" => $liveViewSymbolicLinks["Albums"],
        "[Attached.Articles]" => $liveViewSymbolicLinks["Articles"],
        "[Attached.Attachments]" => $liveViewSymbolicLinks["Attachments"],
        "[Attached.Blogs]" => $liveViewSymbolicLinks["Blogs"],
        "[Attached.BlogPosts]" => $liveViewSymbolicLinks["BlogPosts"],
        "[Attached.Chats]" => $liveViewSymbolicLinks["Chats"],
        "[Attached.DemoFiles]" => $liveViewSymbolicLinks["DemoFiles"],
        "[Attached.Forums]" => $liveViewSymbolicLinks["Forums"],
        "[Attached.ForumPosts]" => $liveViewSymbolicLinks["ForumPosts"],
        "[Attached.ID]" => $this->core->UUID("ArticleAttachments"),
        "[Attached.Members]" => $liveViewSymbolicLinks["Members"],
        "[Attached.Polls]" => $liveViewSymbolicLinks["Polls"],
        "[Attached.Products]" => $liveViewSymbolicLinks["Products"],
        "[Attached.Shops]" => $liveViewSymbolicLinks["Shops"],
        "[Attached.Updates]" => $liveViewSymbolicLinks["Updates"],
        "[Conversation.CRID]" => $id,
        "[Conversation.CRIDE]" => base64_encode($id),
        "[Conversation.Level]" => base64_encode(1),
        "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]"),
        "[Member.DisplayName]" => $author["Personal"]["DisplayName"].$verified,
        "[Member.ProfilePicture]" => $this->core->ProfilePicture($author, "margin:0.5em;max-width:12em;width:calc(100% - 1em)"),
        "[Member.Description]" => $description
       ], $this->core->Extension("b793826c26014b81fdc1f3f94a52c9a6")]);
      }
     } else {
      $r = $article["Body"];
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View",
    "Title" => $_ViewTitle
   ]);
  }
  function Invite(array $a) {
   $_AccessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? base64_encode("");
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id)) {
    $_AccessCode = "Accepted";
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Purge(array $a) {
   $_AccessCode = "Denied";
   $data = $a["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
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
    $_AccessCode = "Accepted";
    $id = base64_decode($id);
    $newArticles = [];
    $articles = $y["Pages"] ?? [];
    foreach($articles as $key => $value) {
     if($id != $value) {
      array_push($newArticles, $value);
     }
    }
    $y["Pages"] = $newArticles;
    $article = $this->core->Data("Get", ["pg", $id]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM Articles WHERE Article_ID=:ID", [
     ":ID" => $id
    ]);
    $sql->execute();
    if(!empty($article)) {
     $article["Purge"] = 1;
     $this->core->Data("Save", ["pg", $id, $article]);
    }
    $conversation = $this->core->Data("Get", ["conversation", $id]);
    if(!empty($conversation)) {
     $conversation["Purge"] = 1;
     $this->core->Data("Save", ["conversation", $id, $conversation]);
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
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->core->Element([
     "p", "The Article <em>".$article["Title"]."</em> and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function Save(array $a) {
   $_AccessCode = "Denied";
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
      break;
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Article <em>$title</em> is taken.",
      "Header" => "Error"
     ];
    } else {
     $_AccessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $article = $this->core->Data("Get", ["pg", $id]);
     $albums = [];
     $albumsData = $data["Album"] ?? [];
     $articles = [];
     $articlesData = $data["Article"] ?? [];
     $attachments = [];
     $attachmentsData = $data["Attachment"] ?? [];
     $author = $article["UN"] ?? $you;
     $blogs = [];
     $blogsData = $data["Blog"] ?? [];
     $blogPosts = [];
     $blogPostsData = $data["BlogPost"] ?? [];
     $chats = [];
     $chatsData = $data["Chat"] ?? [];
     $contributors = $article["Contributors"] ?? [];
     $contributors[$author] = "Admin";
     $coverPhoto = $data["CoverPhoto"] ?? "";
     $created = $article["Created"] ?? $now;
     $forums = [];
     $forumsData = $data["Forum"] ?? [];
     $forumPosts = [];
     $forumPostsData = $data["ForumPost"] ?? [];
     $illegal = $article["Illegal"] ?? 0;
     $members = []; 
     $membersData = $data["Member"] ?? [];
     $modifiedBy = $article["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $newCategory = "Article";
     $newCategory = ($category == "JE") ? "Journal Entry" : $newCategory;
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["Posts"];
     $notes = $article["Notes"] ?? [];
     $passPhrase = $data["PassPhrase"] ?? "";
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Articles"];
     $polls = []; 
     $pollsData = $data["Poll"] ?? [];
     $products = [];
     $productsData = $data["Product"] ?? [];
     $purge = $data["Purge"] ?? 0;
     $shops = [];
     $shopsData = $data["Shop"] ?? [];
     $updates = [];
     $updatesData = $data["Update"] ?? [];
     $subscribers = $article["Subscribers"] ?? [];
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
     } if(!empty($chatsData)) {
      $media = $chatsData;
      for($i = 0; $i < count($media); $i++) {
       if(!empty($media[$i])) {
        array_push($chats, $media[$i]);
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
     } if(!empty($productsData)) {
      $media = $productsData;
      for($i = 0; $i < count($media); $i++) {
       if(!empty($media[$i])) {
        array_push($products, $media[$i]);
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
     } if($isPublic == 1 && $new == 1) {
      $ck = ($author == $you) ? 1 : 0;
      $ck = (!in_array($id, $y["Pages"]) && $ck == 1) ? 1 : 0;
      if($ck == 1) {
       $newPages = $y["Pages"];
       array_push($newPages, $id);
       $y["Activity"]["LastActive"] = $now;
       $y["Pages"] = array_unique($newPages);
       $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
      }
     }
     $article = [
      "Albums" => $albums,
      "Articles" => $articles,
      "Attachments" => $attachments,
      "Body" => $this->core->PlainText([
       "Data" => $data["Body"],
       "Encode" => 1,
       "HTMLEncode" => 1
      ]),
      "Blogs" => $blogs,
      "BlogPosts" => $blogPosts,
      "Category" => $category,
      "Chats" => $chats,
      "CoverPhoto" => $coverPhoto,
      "Contributors" => $contributors,
      "Created" => $created,
      "Description" => htmlentities($data["Description"]),
      "Forums" => $forums,
      "ForumPosts" => $forumPosts,
      "ID" => $id,
      "Illegal" => $illegal,
      "Members" => $members,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "Notes" => $notes,
      "NSFW" => $nsfw,
      "PassPhrase" => $passPhrase,
      "Privacy" => $privacy,
      "Polls" => $polls,
      "Products" => $products,
      "Purge" => $purge,
      "Shops" => $shops,
      "Title" => $title,
      "UN" => $author,
      "Updates" => $updates
     ];
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $query = "REPLACE INTO Articles(
      Article_Body,
      Article_Created,
      Article_Description,
      Article_ID,
      Article_NSFW,
      Article_Privacy,
      Article_Title,
      Article_Username
     ) VALUES(
      :Body,
      :Created,
      :Description,
      :ID,
      :NSFW,
      :Privacy,
      :Title,
      :Username
     )";
     $sql->query($query, [
      ":Body" => $this->core->Excerpt($this->core->PlainText([
       "Data" => $article["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]), 1000),
      ":Created" => $created,
      ":Description" => $article["Description"],
      ":ID" => $id,
      ":NSFW" => $article["NSFW"],
      ":Privacy" => $article["Privacy"],
      ":Title" => $article["Title"],
      ":Username" => $article["UN"]
     ]);
     $sql->execute();
     $this->core->Data("Save", ["pg", $id, $article]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     if($new == 1) {
      $this->core->Statistic("New Article");
      if($isPublic == 1) {
       foreach($subscribers as $key => $value) {
        $this->core->SendBulletin([
         "Data" => [
          "ArticleID" => $id,
          "Author" => $you
         ],
         "To" => $value,
         "Type" => "NewArticle"
        ]);
       }
      }
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
     $r = [
      "Body" => "The $newCategory has been $actionTaken!",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function SaveBanish(array $a) {
   $_AccessCode = "Denied";
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
     $_AccessCode = "Accepted";
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function SendInvite(array $a) {
   $_AccessCode = "Denied";
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
      $_AccessCode = "Accepted";
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function Subscribe(array $a) {
   $_AccessCode = "Denied";
   $_ResponseType = "Dialog";
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
    $_AccessCode = "Accepted";
    $article = $this->core->Data("Get", ["pg", $id]) ?? [];
    $_ResponseType = "UpdateText";
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
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $_ResponseType
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
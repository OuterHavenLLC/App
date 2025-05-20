<?php
 Class ForumPost extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $forumID = $data["FID"] ?? "";
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $topic = $data["Topic"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif((!empty($forumID) && !empty($id)) || $new == 1) {
    $_Dialog = "";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? $this->core->UUID("ForumPostBy$you") : $id;
    $post = $this->core->Data("Get", ["post", $id]);
    $post = $this->core->FixMissing($post, ["Body", "Title"]);
    $albums = $post["Albums"] ?? [];
    $articles = $post["Articles"] ?? [];
    $attachments = $post["Attachments"] ?? [];
    $blogs = $post["Blogs"] ?? [];
    $blogPosts = $post["BlogPosts"] ?? [];
    $body = $post["Body"] ?? "";
    $chats = $post["Chat"] ?? [];
    $coverPhoto = $post["CoverPhoto"] ?? "";
    $forum = $this->core->Data("Get", ["pf", $forumID]);
    $forums = $post["Forums"] ?? [];
    $forumPosts = $post["ForumPosts"] ?? [];
    $header = ($new == 1) ? "New Post" : "Edit Post";
    $members = $post["Members"] ?? [];
    $nsfw = $post["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $post["PassPhrase"] ?? "";
    $polls = $post["Polls"] ?? [];
    $privacy = $post["Privacy"] ?? $y["Privacy"]["Posts"];
    $products = $post["Products"] ?? [];
    $shops = $post["Shops"] ?? [];
    $title = $post["Title"] ?? "";
    $topicOptions = $forum["Topics"] ?? [];
    $topics = [];
    $updates = $post["Updates"] ?? [];
    foreach($topicOptions as $topicID => $info) {
     $topics[$topicID] = $info["Title"] ?? "Untitled";
    }
    $topic = $post["Topic"] ?? $topic;
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
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-encryption" => "AES",
      "data-form" => ".EditForumPost$id",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("ForumPost:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[ForumPost.Attachments]" => $this->core->RenderView($attachments),
       "[ForumPost.Header]" => $header,
       "[ForumPost.ID]" => $id,
       "[ForumPost.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign)
      ],
      "ExtensionID" => "cabbfc915c2edd4d4cba2835fe68b1cc"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".ForumPostInformation$id",
       [
        [
         "Attributes" => [
          "name" => "FID",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $forumID
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
          "data-editor-identifier" => "EditForumPostBody$id",
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
          "Data" => $post["Body"]
         ]))
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
         "OptionGroup" => $topics,
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50",
          "Header" => 1,
          "HeaderText" => "Topic"
         ],
         "Name" => "Topic",
         "Type" => "Select",
         "Value" => $topic
        ]
       ]
      ]
     ],
     [
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
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".Privacy$id",
       [
        "Value" => $privacy
       ]
      ]
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Forum or Post Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $fid = $data["FID"] ?? "";
   $id = $data["ID"] ?? "";
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($fid) && !empty($id)) {
    $_ForumPost = $this->core->GetContentData([
     "ID" => base64_encode("ForumPost;$fid;$id")
    ]);
    if($_ForumPost["Empty"] == 0) {
     $forum = $this->core->Data("Get", ["pf", $fid]);
     $post = $_ForumPost["DataModel"];
     $passPhrase = $post["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_Dialog = "";
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_ForumPost["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "SecureKey" => base64_encode($passPhrase),
        "FID" => $fid,
        "ID" => $id,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("ForumPost:Home")
       ], true))
      ]]);
      $_Card = [
       "Front" => $this->core->RenderView($_View)
      ];
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = "";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key == $secureKey) {
       $_View = $this->view(base64_encode("ForumPost:Home"), ["Data" => [
        "EmbeddedView" => 1,
        "FID" => $fid,
        "ID" => $id,
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View, 1);
       $_Commands = $_View["Commands"];
       $_View = $_View["View"];
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Dialog = [
       "Body" => "The requested Forum Post could not be found."
      ];
      $active = 0;
      $admin = 0;
      $check = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
      $check2 = ($active == 1 || $forum["Type"] == "Public") ? 1 : 0;
      $cms = $this->core->Data("Get", ["cms", md5($post["From"])]);
      $check3 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $post["Privacy"],
       "UN" => $post["From"],
       "Y" => $you
      ]);
      $manifest = $this->core->Data("Get", ["pfmanifest", $fid]);
      foreach($manifest as $member => $role) {
       if($active == 0 && $member == $you) {
        $active++;
        if($role == "Admin") {
         $admin++;
        }
       }
      }
      $op = ($check == 1) ? $y : $this->core->Member($post["From"]);
      $options = $_ForumPost["ListItem"]["Options"];
      $privacy = $post["Privacy"] ?? $op["Privacy"]["Posts"];
      if($check == 1 || $check2 == 1) {
       $_Dialog = "";
       $actions = ($post["From"] != $you) ? $this->core->Element(["button", $blockCommand, [
        "class" => "InnerMargin UpdateButton v2",
        "data-processor" => $options["Block"]
       ]]) : "";
       $embeddedView = $data["EmbeddedView"] ?? 0;
       $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($post, "LiveView");
       $share = "";
       if($check == 1) {
        $actions .= $this->core->Element(["button", "Delete", [
         "class" => "InnerMargin OpenDialog v2",
         "data-view" => $options["Delete"]
        ]]);
        $actions .= ($admin == 1 || $check == 1) ? $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin OpenDialog v2",
         "data-view" => $options["Edit"]
        ]]) : "";
        $share = ($forum["Type"] == "Public") ? $this->core->Element(["button", "Share", [
         "class" => "InnerMargin OpenCard v2",
         "data-view" => $options["Share"]
        ]]) : "";
       }
       $actions = ($this->core->ID != $you) ? $actions : "";
       $blocked = $this->core->CheckBlocked([$y, "Forum Posts", $id]);
       $blockCommand = ($blocked == 0) ? "Block" : "Unblock";
       $purgeRenderCode = ($post["From"] == $you) ? "PURGE" : "DO NOT PURGE";
       $op = ($post["From"] == $you) ? $y : $this->core->Member($post["From"]);
       $displayName = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $memberRole = $manifest[$op["Login"]["Username"]];
       $verified = $op["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       $_Commands = [
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Albums$id",
          $liveViewSymbolicLinks["Albums"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Articles$id",
          $liveViewSymbolicLinks["Articles"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Attachments$id",
          $liveViewSymbolicLinks["Attachments"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Blogs$id",
          $liveViewSymbolicLinks["Blogs"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".BlogPosts$id",
          $liveViewSymbolicLinks["BlogPosts"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Chats$id",
          $liveViewSymbolicLinks["Chats"]
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
          ".Forums$id",
          $liveViewSymbolicLinks["Forums"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".ForumPosts$id",
          $liveViewSymbolicLinks["ForumPosts"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Members$id",
          $liveViewSymbolicLinks["Members"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Notes$id",
          $options["Notes"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Polls$id",
          $liveViewSymbolicLinks["Polls"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Products$id",
          $liveViewSymbolicLinks["Products"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Shops$id",
          $liveViewSymbolicLinks["Shops"]
         ]
        ],
        [
         "Name" => "UpdateContentAES",
         "Parameters" => [
          ".Updates$id",
          $liveViewSymbolicLinks["Updates"]
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
       $_View = [
        "ChangeData" => [
         "[ForumPost.Actions]" => $actions,
         "[ForumPost.Block]" => $options["Block"],
         "[ForumPost.Block.Text]" => $blockCommand,
         "[ForumPost.Body]" => $this->core->PlainText([
          "BBCodes" => 1,
          "Data" => $post["Body"],
          "Display" => 1,
          "HTMLDecode" => 1
         ]),
         "[ForumPost.CoverPhoto]" => $_ForumPost["ListItem"]["CoverPhoto"],
         "[ForumPost.Created]" => $this->core->TimeAgo($post["Created"]),
         "[ForumPost.ID]" => $id,
         "[ForumPost.MemberRole]" => $memberRole,
         "[ForumPost.Modified]" => $_ForumPost["ListItem"]["Modified"],
         "[ForumPost.OriginalPoster]" => $displayName.$verified,
         "[ForumPost.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
         "[ForumPost.Report]" => $options["Report"],
         "[ForumPost.Title]" => $_ForumPost["ListItem"]["Title"],
         "[ForumPost.Share]" => $share,
         "[PurgeRenderCode]" => $purgeRenderCode
        ],
        "ExtensionID" => "d2be822502dd9de5e8b373ca25998c37"
       ];
       $_Card = ($embeddedView == 0) ? [
        "Front" => $_View
       ] : "";
       $_View = ($embeddedView == 1) ? $_View : "";
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Purge(array $data): string {
   $_Dialog = [
    "Body" => "The Post Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $forumID = $data["ForumID"] ?? "";
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $postID = $data["PostID"] ?? "";
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
   } elseif(!empty($forumID) && !empty($postID)) {
    $_Dialog = "";
    $forumID = base64_decode($forumID);
    $forum = $this->core->Data("Get", ["pf", $forumID]);
    $newPosts = [];
    $postID = base64_decode($postID);
    $posts = $forum["Posts"] ?? [];
    foreach($posts as $key => $value) {
     if($postID != $value) {
      $newPosts[$key] = $value;
     }
    }
    $forumPost = $this->core->Data("Get", ["post", $postID]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM ForumPosts WHERE ForumPost_ID=:ID", [
     ":ID" => $postID
    ]);
    $sql->execute();
    if(!empty($forumPost)) {
     $forumPost["Purge"] = 1;
     $this->core->Data("Save", ["post", $postID, $forumPost]);
    }
    $forum["Posts"] = $newPosts;
    $conversation = $this->core->Data("Get", ["conversation", $postID]);
    if(!empty($conversation)) {
     $conversation["Purge"] = 1;
     $this->core->Data("Save", ["conversation", $postID, $conversation]);
    }
    $translations = $this->core->Data("Get", ["translate", $postID]);
    if(!empty($translations)) {
     $translations["Purge"] = 1;
     $this->core->Data("Save", ["translate", $postID, $translations]);
    }
    $votes = $this->core->Data("Get", ["votes", $postID]);
    if(!empty($votes)) {
     $votes["Purge"] = 1;
     $this->core->Data("Save", ["votes", $postID, $votes]);
    }
    $this->core->Data("Save", ["pf", $forumID, $forum]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", "The Forum Post and dependencies were marked for purging.",
      ["class" => "CenterText"]
     ]).$this->core->Element([
      "button", "Okay", ["class" => "CloseDialog v2 v2w"]
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Forum Post Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $fid = $data["FID"] ?? "";
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($fid) && !empty($id)) {
    $_AccessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $attachments = [];
    $forum = $this->core->Data("Get", ["pf", $fid]);
    $i = 0;
    $now = $this->core->timestamp;
    $post = $this->core->Data("Get", ["post", $id]);
    $posts = $forum["Posts"] ?? [];
    $albums = [];
    $albumsData = $data["Album"] ?? [];
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
    $created = $post["Created"] ?? $now;
    $forums = [];
    $forumsData = $data["Forum"] ?? [];
    $forumPosts = [];
    $forumPostsData = $data["ForumPost"] ?? [];
    $from = $post["From"] ?? $y["Login"]["Username"];
    $illegal = $post["Illegal"] ?? 0;
    $members = []; 
    $membersData = $data["Member"] ?? [];
    $modifiedBy = $post["ModifiedBy"] ?? [];
    $modifiedBy[$now] = $you;
    $notes = $post["Notes"] ?? [];
    $nsfw = $data["NSFW"] ?? 0;
    $passPhrase = $data["PassPhrase"] ?? "";
    $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
    $polls = []; 
    $pollsData = $data["Poll"] ?? [];
    $products = [];
    $productsData = $data["Product"] ?? [];
    $purge = $data["Purge"] ?? 0;
    $shops = [];
    $shopsData = $data["Shop"] ?? [];
    $title = $data["Title"] ?? "Untitled";
    $topic = $data["Topic"] ?? "";
    $updates = [];
    $updatesData = $data["Update"] ?? [];
    foreach($forum["Topics"] as $topicID => $info) {
     if(!in_array($id, $info["Posts"]) && $topic == $topicID) {
      array_push($forum["Topics"][$topicID]["Posts"], $id);
     } elseif(in_array($id, $info["Posts"]) && $topic != $topicID) {
      unset($forum["Topics"][$topicID]["Posts"][$id]);
     }
    } if(!empty($albumsData)) {
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
    }
    $post = [
     "Albums" => $albums,
     "Articles" => $articles,
     "Attachments" => $attachments,
     "Blogs" => $blogs,
     "BlogPosts" => $blogPosts,
     "Body" => $this->core->PlainText([
      "Data" => $data["Body"],
      "HTMLEncode" => 1
     ]),
     "Chats" => $chats,
     "CoverPhoto" => $coverPhoto,
     "Created" => $created,
     "ForumID" => $forum["ID"],
     "Forums" => $forums,
     "ForumPosts" => $forumPosts,
     "From" => $from,
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
     "Topic" => $topic,
     "Updates" => $updates
    ];
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $query = "REPLACE INTO ForumPosts(
     ForumPost_Body,
     ForumPost_Created,
     ForumPost_Forum,
     ForumPost_ID,
     ForumPost_NSFW,
     ForumPost_Privacy,
     ForumPost_Title,
     ForumPost_Topic,
     ForumPost_Username
    ) VALUES(
     :Body,
     :Created,
     :Forum,
     :ID,
     :NSFW,
     :Privacy,
     :Title,
     :Topic,
     :Username
    )";
    $sql->query($query, [
     ":Body" => $this->core->PlainText([
      "Data" => $data["Body"],
      "HTMLDecode" => 1
     ]),
     ":Created" => $created,
     ":Forum" => $fid,
     ":ID" => $id,
     ":NSFW" => $post["NSFW"],
     ":Privacy" => $post["Privacy"],
     ":Title" => $post["Title"],
     ":Topic" => $post["Topic"],
     ":Username" => $post["From"]
    ]);
    $sql->execute();
    $this->core->Data("Save", ["pf", $fid, $forum]);
    $this->core->Data("Save", ["post", $id, $post]);
    foreach($posts as $key => $value) {
     if($i == 0 && $id == $value) {
      $i++;
     }
    } if($i == 0) {
     array_push($posts, $id);
     $forum["Posts"] = $posts;
     $y["Activity"]["LastActive"] = $now;
     $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
     $this->core->Data("Save", ["mbr", md5($you), $y]);
    }
    $statistic = ($new == 1) ? "Save Forum Post" : "Update Forum Post";
    $this->core->Statistic($statistic);
    $_Dialog = [
     "Body" => "Your post has been $actionTaken.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
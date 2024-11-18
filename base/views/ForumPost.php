<?php
 Class ForumPost extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $forumID = $data["FID"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $topic = $data["Topic"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif((!empty($forumID) && !empty($id)) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".ForumPost$id",
     "data-processor" => base64_encode("v=".base64_encode("ForumPost:Save"))
    ]]);
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
    $r = $this->core->Change([[
     "[ForumPost.Attachments]" => $this->core->RenderView($attachments),
     "[ForumPost.Body]" => base64_encode($this->core->PlainText([
      "Data" => $post["Body"]
     ])),
     "[ForumPost.Header]" => $header,
     "[ForumPost.ForumID]" => $forumID,
     "[ForumPost.ID]" => $id,
     "[ForumPost.New]" => $new,
     "[ForumPost.NSFW]" => $nsfw,
     "[ForumPost.PassPhrase]" => base64_encode($passPhrase),
     "[ForumPost.Privacy]" => $privacy,
     "[ForumPost.Title]" => base64_encode($title),
     "[ForumPost.Topic]" => $topic,
     "[ForumPost.Topics]" => json_encode($topics, true),
     "[ForumPost.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign),
     "[ForumPost.Visibility.NSFW]" => $nsfw,
     "[ForumPost.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("cabbfc915c2edd4d4cba2835fe68b1cc")]);
    $r = [
     "Action" => $action,
     "Front" => $r
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
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Forum or Post Identifier is missing."
   ];
   $fid = $data["FID"] ?? "";
   $id = $data["ID"] ?? "";
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($fid) && !empty($id)) {
    $bl = $this->core->CheckBlocked([$y, "Forum Posts", $id]);
    $_ForumPost = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("ForumPost;$fid;$id")
    ]);
    if($_ForumPost["Empty"] == 0) {
     $accessCode = "Accepted";
     $forum = $this->core->Data("Get", ["pf", $fid]);
     $post = $_ForumPost["DataModel"];
     $passPhrase = $post["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
      $r = [
       "Front" => $this->core->RenderView($r)
      ];
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
       $r = $this->view(base64_encode("ForumPost:Home"), ["Data" => [
        "EmbeddedView" => 1,
        "FID" => $fid,
        "ID" => $id,
        "ViewProtectedContent" => 1
       ]]);
       $r = $this->core->RenderView($r);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $accessCode = "Denied";
      $active = 0;
      $admin = 0;
      $r = [
       "Body" => "The requested Forum Post could not be found."
      ];
      $ck = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
      $ck2 = ($active == 1 || $forum["Type"] == "Public") ? 1 : 0;
      $cms = $this->core->Data("Get", ["cms", md5($post["From"])]);
      $ck3 = $this->core->CheckPrivacy([
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
      $op = ($ck == 1) ? $y : $this->core->Member($post["From"]);
      $options = $_ForumPost["ListItem"]["Options"];
      $privacy = $post["Privacy"] ?? $op["Privacy"]["Posts"];
      if($ck == 1 || $ck2 == 1) {
       $accessCode = "Accepted";
       $blockCommand = ($bl == 0) ? "Block" : "Unblock";
       $actions = ($post["From"] != $you) ? $this->core->Element(["button", $blockCommand, [
        "class" => "InnerMargin UpdateButton v2",
        "data-processor" => $options["Block"]
       ]]) : "";
       $embeddedView = $data["EmbeddedView"] ?? 0;
       $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($post, "LiveView");
       $share = "";
       if($ck == 1) {
        $actions .= $this->core->Element(["button", "Delete", [
         "class" => "InnerMargin OpenDialog v2",
         "data-view" => $options["Delete"]
        ]]);
        $actions .= ($admin == 1 || $ck == 1) ? $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin OpenDialog v2",
         "data-view" => $options["Edit"]
        ]]) : "";
        $share = ($forum["Type"] == "Public") ? $this->core->Element(["button", "Share", [
         "class" => "InnerMargin OpenCard v2",
         "data-view" => $options["Share"]
        ]]) : "";
       }
       $actions = ($this->core->ID != $you) ? $actions : "";
       $op = ($post["From"] == $you) ? $y : $this->core->Member($post["From"]);
       $displayName = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $memberRole = $manifest[$op["Login"]["Username"]];
       $verified = $op["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       $r = $this->core->Change([[
        "[Attached.Albums]" => $liveViewSymbolicLinks["Albums"],
        "[Attached.Articles]" => $liveViewSymbolicLinks["Articles"],
        "[Attached.Attachments]" => $liveViewSymbolicLinks["Attachments"],
        "[Attached.Blogs]" => $liveViewSymbolicLinks["Blogs"],
        "[Attached.BlogPosts]" => $liveViewSymbolicLinks["BlogPosts"],
        "[Attached.Chats]" => $liveViewSymbolicLinks["Chats"],
        "[Attached.DemoFiles]" => $liveViewSymbolicLinks["DemoFiles"],
        "[Attached.Forums]" => $liveViewSymbolicLinks["Forums"],
        "[Attached.ForumPosts]" => $liveViewSymbolicLinks["ForumPosts"],
        "[Attached.ID]" => $this->core->UUID("ForumPostAttachments"),
        "[Attached.Members]" => $liveViewSymbolicLinks["Members"],
        "[Attached.Polls]" => $liveViewSymbolicLinks["Polls"],
        "[Attached.Products]" => $liveViewSymbolicLinks["Products"],
        "[Attached.Shops]" => $liveViewSymbolicLinks["Shops"],
        "[Attached.Updates]" => $liveViewSymbolicLinks["Updates"],
        "[Conversation.CRID]" => $id,
        "[Conversation.CRIDE]" => base64_encode($id),
        "[Conversation.Level]" => base64_encode(1),
        "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]"),
        "[ForumPost.Actions]" => $actions,
        "[ForumPost.Attachments]" => $_ForumPost["ListItem"]["Attachments"],
        "[ForumPost.Body]" => $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $post["Body"],
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[ForumPost.CoverPhoto]" => $_ForumPost["ListItem"]["CoverPhoto"],
        "[ForumPost.Created]" => $this->core->TimeAgo($post["Created"]),
        "[ForumPost.ID]" => $id,
        "[ForumPost.Illegal]" => $options["Report"],
        "[ForumPost.MemberRole]" => $memberRole,
        "[ForumPost.Modified]" => $_ForumPost["ListItem"]["Modified"],
        "[ForumPost.OriginalPoster]" => $displayName.$verified,
        "[ForumPost.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
        "[ForumPost.Title]" => $_ForumPost["ListItem"]["Title"],
        "[ForumPost.Share]" => $share,
        "[ForumPost.Vote]" => $options["Vote"]
       ], $this->core->Extension("d2be822502dd9de5e8b373ca25998c37")]);
       $r = ($embeddedView == 1) ? $r : [
        "Front" => $r
       ];
      }
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
  function Purge(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $forumID = $data["ForumID"] ?? "";
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $postID = $data["PostID"] ?? "";
   $r = [
    "Body" => "The Post Identifier is missing."
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
   } elseif(!empty($forumID) && !empty($postID)) {
    $accessCode = "Accepted";
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
    $r = $this->core->Element([
     "p", "The Forum Post and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
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
   $fid = $data["FID"] ?? "";
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Forum Post Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($fid) && !empty($id)) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $attachments = [];
    $forum = $this->core->Data("Get", ["pf", $fid]);
    $i = 0;
    $now = $this->core->timestamp;
    $post = $this->core->Data("Get", ["post", $id]);
    $posts = $forum["Posts"] ?? [];
    foreach($posts as $key => $value) {
     if($i == 0 && $id == $value) {
      $i++;
     }
    } if($i == 0) {
     array_push($posts, $id);
     $forum["Posts"] = $posts;
     $y["Activity"]["LastActive"] = $now;
     $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
     #$this->core->Data("Save", ["mbr", md5($you), $y]);
    }
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
    $modifiedBy = $post["ModifiedBy"] ?? [];
    $modifiedBy[$now] = $you;
    $members = []; 
    $membersData = $data["Member"] ?? [];
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
    $statistic = ($new == 1) ? "Save Forum Post" : "Update Forum Post";
    $this->core->Data("Save", ["pf", $fid, $forum]);
    $this->core->Data("Save", ["post", $id, $post]);
    $this->core->Statistic($statistic);
    $r = [
     "Body" => "Your post has been $actionTaken.",
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
    "Success" => "CloseCard"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
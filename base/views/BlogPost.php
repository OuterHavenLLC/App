<?php
 Class BlogPost extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data) {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $blog = $data["Blog"] ?? "";
   $new = $data["new"] ?? 0;
   $post = $data["Post"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif((!empty($blog) && !empty($post)) || $new == 1) {
    $id = ($new == 1) ? $this->core->UUID("BlogPostBy$you") : $post;
    $action = ($new == 1) ? "Post" : "Update";
    $attachments = "";
    $blog = $this->core->Data("Get", ["blg", $blog]);
    $post = $this->core->Data("Get", ["bp", $id]);
    $albums = $post["Albums"] ?? [];
    $articles = $post["Articles"] ?? [];
    $attachments = $post["Attachments"] ?? [];
    $body = $post["Body"] ?? "";
    $blogs = $post["Blogs"] ?? [];
    $blogPosts = $post["BlogPosts"] ?? [];
    $chats = $post["Chat"] ?? [];
    $coverPhoto = $post["CoverPhoto"] ?? "";
    $description = $post["Description"] ?? "";
    $designViewEditor = "ViewBlogPost$id";
    $forums = $post["Forums"] ?? [];
    $forumPosts = $post["ForumPosts"] ?? [];
    $header = ($new == 1) ? "New Post to ".$blog["Title"] : "Edit ".$post["Title"];
    $members = $post["Members"] ?? [];
    $nsfw = $post["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $post["PassPhrase"] ?? "";
    $polls = $post["Polls"] ?? [];
    $privacy = $post["Privacy"] ?? $y["Privacy"]["Profile"];
    $products = $post["Products"] ?? [];
    $search = base64_encode("Search:Containers");
    $shops = $post["Shops"] ?? [];
    $template = $post["TPL"] ?? "";
    $templateOptions = $this->core->DatabaseSet("Extensions");
    $templates = [];
    $updates = $post["Updates"] ?? [];
    foreach($templateOptions as $key => $value) {
     $value = str_replace("nyc.outerhaven.extension.", "", $value);
     $template = $this->core->Data("Get", ["extension", $value]);
     if($template["Category"] == "ArticleTemplate") {
      $templates[$value] = $template["Title"];
     }
    }
    $title = $post["Title"] ?? "";
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
    $r = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".EditBlogPost$id",
      "data-processor" => base64_encode("v=".base64_encode("BlogPost:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Blog.ID]" => $blog["ID"],
       "[BlogPost.Attachments]" => $this->core->RenderView($attachments),
       "[BlogPost.Body]" => base64_encode($this->core->PlainText([
        "Data" => $body,
        "Decode" => 1
       ])),
       "[BlogPost.Description]" => base64_encode($description),
       "[BlogPost.DesignView]" => $header,
       "[BlogPost.Header]" => $header,
       "[BlogPost.ID]" => $id,
       "[BlogPost.New]" => $new,
       "[BlogPost.PassPhrase]" => base64_encode($passPhrase),
       "[BlogPost.Title]" => base64_encode($title),
       "[BlogPost.Template]" => $template,
       "[BlogPost.Templates]" => json_encode($templates, true),
       "[BlogPost.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign),
       "[BlogPost.Visibility.NSFW]" => $nsfw,
       "[BlogPost.Visibility.Privacy]" => $privacy
      ],
      "ExtensionID" => "15961ed0a116fbd6cfdb793f45614e44"
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data) {
   $_Dialog = [
    "Body" => "The requested Blog Post could not be found.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $blog = $data["Blog"] ?? "";
   $backTo = $data["b2"] ?? "Blog";
   $back = $this->core->Element(["button", "Back to <em>$backTo</em>", [
    "class" => "GoToParent LI head",
    "data-type" => "Blog$blog"
   ]]);
   $i = 0;
   $postID = $data["Post"] ?? "";
   $public = $data["pub"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($public == 1) {
    $blogPosts = $this->core->DatabaseSet("BlogPost");
    foreach($blogPosts as $key => $value) {
     $blogPost = $this->core->Data("Get", ["bp", $value]) ?? [];
     if(($blogPost["ID"] == $postID || $callSignsMatch == 1) && $i == 0) {
      $i++;
      $postID = $value;
     }
    }
   } if((!empty($blog) && !empty($postID)) || $i > 0) {
    $bl = $this->core->CheckBlocked([$y, "Blog Posts", $postID]);
    $_BlogPost = $this->core->GetContentData([
     "BackTo" => $backTo,
     "Blacklisted" => $bl,
     "ID" => base64_encode("BlogPost;$blog;$postID")
    ]);
    if($_BlogPost["Empty"] == 0) {
     $post = $_BlogPost["DataModel"];
     $passPhrase = $post["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_Dialog = "";
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_BlogPost["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "SecureKey" => base64_encode($passPhrase),
        "Blog" => $blog,
        "Post" => $postID,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("BlogPost:Home")
       ], true))
      ]]);
      $_View = $this->core->RenderView($_View);
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = [
       "Body" => "The Key is missing."
      ];
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $_Dialog = "";
       $_View = "";
      } else {
       $_Dialog = "";
       $_View = $this->view(base64_encode("BlogPost:Home"), ["Data" => [
        "Blog" => $blog,
        "Post" => $postID,
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Dialog = "";
      $options = $_BlogPost["ListItem"]["Options"];
      $author = ($post["UN"] == $you) ? $y : $this->core->Member($post["UN"]);
      $ck = ($author["Login"]["Username"] == $you) ? 1 : 0;
      $description = $author["Personal"]["DisplayName"] ?? "";
      $extensionID = $post["TPL"] ?? "b793826c26014b81fdc1f3f94a52c9a6";
      $blockCommand = ($bl == 0) ? "Block" : "Unblock";
      $actions = ($post["UN"] != $you) ? $this->core->Element([
       "button", $blockCommand, [
        "class" => "Small UpdateButton v2",
        "data-processor" => $options["Block"]
       ]
      ]) : "";
      $actions = $this->core->Element([
       "button", "View Profile", [
        "class" => "OpenCard Small v2",
        "data-view" => base64_encode("v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($post["UN"]))
       ]
      ]);
      $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($post, "LiveView");
      $share = ($post["UN"] == $you || $post["Privacy"] == md5("Public")) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element(["div", $this->core->Element([
       "button", "Share", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Share"]
      ]]), ["class" => "Desktop33"]]) : "";
      $verified = $author["Verified"] ?? 0;
      $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
      $_View = [
       "ChangeData" => [
        "[Article.Actions]" => $actions,
        "[Article.Attachments]" => $_BlogPost["ListItem"]["Attachments"],
        "[Article.Back]" => $back,
        "[Article.Body]" => $_BlogPost["ListItem"]["Body"],
        "[Article.Contributors]" => $options["Contributors"],
        "[Article.CoverPhoto]" => $_BlogPost["ListItem"]["CoverPhoto"],
        "[Article.Created]" => $this->core->TimeAgo($post["Created"]),
        "[Article.Description]" => $_BlogPost["ListItem"]["Description"],
        "[Article.ID]" => $postID,
        "[Article.Modified]" => $_BlogPost["ListItem"]["Modified"],
        "[Article.Notes]" => $options["Notes"],
        "[Article.Report]" => $options["Report"],
        "[Article.Share]" => $share,
        "[Article.Subscribe]" => $options["Subscribe"],
        "[Article.Title]" => $_BlogPost["ListItem"]["Title"],
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
        "[Attached.ID]" => $this->core->UUID("BlogPostAttachments"),
        "[Attached.Members]" => $liveViewSymbolicLinks["Members"],
        "[Attached.Polls]" => $liveViewSymbolicLinks["Polls"],
        "[Attached.Products]" => $liveViewSymbolicLinks["Products"],
        "[Attached.Shops]" => $liveViewSymbolicLinks["Shops"],
        "[Attached.Updates]" => $liveViewSymbolicLinks["Updates"],
        "[Conversation.CRID]" => $postID,
        "[Conversation.CRIDE]" => base64_encode($postID),
        "[Conversation.Level]" => base64_encode(1),
        "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]"),
        "[Member.DisplayName]" => $author["Personal"]["DisplayName"].$verified,
        "[Member.ProfilePicture]" => $this->core->ProfilePicture($author, "margin:0.5em;max-width:12em;width:calc(100% - 1em)"),
        "[Member.Description]" => $description
       ],
       "ExtensionID" => $extensionID
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Purge(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog or Post Identifier are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $blogID = $data["BlogID"] ?? base64_encode("");
   $blogID = base64_decode($blogID);
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $now = $this->core->timestamp;
   $postID = $data["PostID"] ?? base64_encode("");
   $postID = base64_decode($postID);
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $_Dialog = "";
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($blogID) && !empty($postID)) {
    $_AccessCode = "Accepted";
    $_Dialog = "";
    $blog = $this->core->Data("Get", ["blg", $blogID]);
    $blog["Modified"] = $now;
    $blog["ModifiedBy"][$now] = $you;
    $newPosts = [];
    $posts = $blog["Posts"] ?? [];
    foreach($posts as $key => $value) {
     if($postID != $value) {
      array_push($newPosts, $value);
     }
    }
    $blog["Posts"] = $newPosts;
    $blogPost = $this->core->Data("Get", ["bp", $postID]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM BlogPosts WHERE BlogPost_ID=:ID", [
     ":ID" => $postID
    ]);
    $sql->execute();
    if(!empty($blogPost)) {
     $blogPost["Purge"] = 1;
     $this->core->Data("Save", ["bp", $postID, $blogPost]);
    }
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
    $this->core->Data("Save", ["blg", $blogID, $blogID]);
    $_View = $this->core->Element([
     "p", "The Blog Post and dependencies were marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "Success" => "CloseDialog",
    "View' => $_View"
   ]);
  }
  function Save(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $blog = $data["BLG"] ?? "";
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $title = $data["Title"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($blog) && !empty($id) && !empty($title)) {
    $i = 0;
    $coverPhoto = "";
    $coverPhotoSource = "";
    $blog = $this->core->Data("Get", ["blg", $blog]);
    $now = $this->core->timestamp;
    $posts = $blog["Posts"] ?? [];
    $subscribers = $blog["Subscribers"] ?? [];
    foreach($posts as $key => $value) {
     $value = $this->core->Data("Get", ["bp", $value]);
     if($i == 0) {
      if($id != $value["ID"] && $title == $value["Title"]) {
       $i++;
       break;
      }
     }
    } if($i > 0) {
     $_Dialog = [
      "Body" => "The Post <em>$title</em> is taken."
     ];
    } else {
     $_AccessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted to <em>".$blog["Title"]."</em>" : "updated";
     $post = $this->core->Data("Get", ["bp", $id]);
     $albums = [];
     $albumsData = $data["Album"] ?? [];
     $articles = [];
     $articlesData = $data["Article"] ?? [];
     $attachments = [];
     $attachmentsData = $data["Attachment"] ?? [];
     $author = $post["UN"] ?? $you;
     $blogs = [];
     $blogsData = $data["Blog"] ?? [];
     $blogPosts = [];
     $blogPostsData = $data["BlogPost"] ?? [];
     $chats = [];
     $chatsData = $data["Chat"] ?? [];
     $contributors = $post["Contributors"] ?? [];
     $contributors[$you] = $blog["Contributors"][$you] ?? "Contributor";
     $coverPhoto = $data["CoverPhoto"] ?? "";
     $created = $post["Created"] ?? $now;
     $forums = [];
     $forumsData = $data["Forum"] ?? [];
     $forumPosts = [];
     $forumPostsData = $data["ForumPost"] ?? [];
     $illegal = $post["Illegal"] ?? 0;
     $members = []; 
     $membersData = $data["Member"] ?? [];
     $modifiedBy = $post["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $notes = $post["Notes"] ?? [];
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
     $passPhrase = $data["PassPhrase"] ?? "";
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
     $polls = []; 
     $pollsData = $data["Poll"] ?? [];
     $products = [];
     $productsData = $data["Product"] ?? [];
     $purge = $data["Purge"] ?? 0;
     $shops = [];
     $shopsData = $data["Shop"] ?? [];
     $updates = [];
     $updatesData = $data["Update"] ?? [];
     $subscribers = $post["Subscribers"] ?? [];
     $post["Contributors"][$you] = ($author == $you) ? "Admin" : "Member";
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
     } if(!in_array($id, $blog["Posts"])) {
      array_push($blog["Posts"], $id);
      $blog["Posts"] = array_unique($blog["Posts"]);
     }
     $post = [
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
      "Chats" => $chats,
      "CoverPhoto" => $coverPhoto,
      "Created" => $created,
      "Contributors" => $contributors,
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
      "Subscribers" => $subscribers,
      "Title" => $title,
      "TPL" => $data["TPL-BLG"],
      "UN" => $author,
      "Updates" => $updates
     ];
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $query = "REPLACE INTO BlogPosts(
      BlogPost_Blog,
      BlogPost_Body,
      BlogPost_Created,
      BlogPost_Description,
      BlogPost_ID,
      BlogPost_NSFW,
      BlogPost_Privacy,
      BlogPost_Title,
      BlogPost_Username
     ) VALUES(
      :Blog,
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
      ":Blog" => $data["BLG"],
      ":Body" => $this->core->PlainText([
       "Data" => $data["Body"],
       "HTMLDecode" => 1
      ]),
      ":Created" => $created,
      ":Description" => $post["Description"],
      ":ID" => $id,
      ":NSFW" => $post["NSFW"],
      ":Privacy" => $post["Privacy"],
      ":Title" => $post["Title"],
      ":Username" => $post["UN"]
     ]);
     $sql->execute();
     $y["Activity"]["LastActive"] = $now;
     $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
     $this->core->Data("Save", ["blg", $data["BLG"], $blog]);
     $this->core->Data("Save", ["bp", $id, $post]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     if($new == 1) {
      foreach($subscribers as $key => $value) {
       $this->core->SendBulletin([
        "Data" => [
         "BlogID" => $data["BLG"],
         "PostID" => $id
        ],
        "To" => $value,
        "Type" => "NewBlogPost"
       ]);
      }
     } else {
      foreach($subscribers as $key => $value) {
       $this->core->SendBulletin([
        "Data" => [
         "BlogID" => $data["BLG"],
         "PostID" => $id
        ],
        "To" => $value,
        "Type" => "BlogPostUpdate"
       ]);
      }
     }
     $_Dialog = [
      "Body" => "The Post <em>$title</em> was $actionTaken!",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function Subscribe(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $_ResponseType = "N/A";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $_ResponseType = "UpdateText";
    $post = $this->core->Data("Get", ["bp", $id]);
    $subscribers = $post["Subscribers"] ?? [];
    $subscribed = (in_array($you, $subscribers)) ? 1 : 0;
    if($subscribed == 1) {
     $newSubscribers = [];
     $_View = "Subscribe";
     foreach($subscribers as $key => $value) {
      if($value != $you) {
       $newSubscribers[$key] = $value;
      }
     }
     $subscribers = $newSubscribers;
    } else {
     array_push($subscribers, $you);
     $_View = "Unsubscribe";
    }
    $post["Subscribers"] = $subscribers;
    $this->core->Data("Save", ["bp", $id, $post]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
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
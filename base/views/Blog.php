<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol.$_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
 Class Blog extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Banish(array $data): string {
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($member)) {
    $id = base64_decode($id);
    $blog = $this->core->Data("Get", ["blg", $id]);
    $member = base64_decode($member);
    $_Dialog = [
     "Body" => "You cannot banish yourself."
    ];
    if($member != $blog["UN"] && $member != $you) {
     $_Dialog = [
      "Actions" => [
       $this->core->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->core->Element(["button", "Banish $member", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-type" => base64_encode("v=".base64_encode("Blog:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $member from <em>".$blog["Title"]."</em>?",
      "Header" => "Banish $member?"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function ChangeMemberRole(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $pin = $data["PIN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($pin) != $y["Login"]["PIN"]) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $_AccessCode = "Accepted";
    $blog = $this->core->Data("Get", ["blg", $id]);
    $contributors = $blog["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $blog["Contributors"] = $contributors;
    $this->core->Data("Save", ["blg", $id, $blog]);
    $_Dialog = [
     "Body" => "$member's Role within <em>".$blog["Title"]."</em> was Changed to $role.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseDialog"
   ]);
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing.",
    "Header" => "Not Found"
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["BLG", "new"]);
   $id = $data["BLG"];
   $new = $data["new"] ?? 0;
   $es = base64_encode("LiveView:EditorSingle");
   $sc = base64_encode("Search:Containers");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) || $new == 1) {
    $_Dialog = "";
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? $this->core->UUID("ANewBlogBy$you") : $id;
    $blog = $this->core->Data("Get", ["blg", $id]);
    $albums = $blog["Albums"] ?? [];
    $articles = $blog["Articles"] ?? [];
    $attachments = $blog["Attachments"] ?? [];
    $author = $blog["UN"] ?? $you;
    $blogs = $blog["Blogs"] ?? [];
    $blogPosts = $blog["BlogPosts"] ?? [];
    $chats = $blog["Chat"] ?? [];
    $coverPhoto = $blog["CoverPhoto"] ?? "";
    $description = $blog["Description"] ?? "";
    $forums = $blog["Forums"] ?? [];
    $forumPosts = $blog["ForumPosts"] ?? [];
    $header = ($new == 1) ? "New Blog" : "Edit ".$blog["Title"];
    $members = $blog["Members"] ?? [];
    $nsfw = $blog["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $blog["PassPhrase"] ?? "";
    $polls = $blog["Polls"] ?? [];
    $privacy = $blog["Privacy"] ?? $y["Privacy"]["Posts"];
    $products = $blog["Products"] ?? [];
    $shops = $blog["Shops"] ?? [];
    $template = $blog["TPL"] ?? "";
    $templateOptions = $this->core->DatabaseSet("Extensions");
    $templates = [];
    $title = $blog["Title"] ?? "";
    $updates = $blog["Updates"] ?? [];
    foreach($templateOptions as $key => $value) {
     $value = str_replace("nyc.outerhaven.extension.", "", $value);
     $template = $this->core->Data("Get", ["extension", $value]) ?? [];
     if($template["Category"] == "BlogTemplate") {
      $templates[$value] = $template["Title"];
     }
    }
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
    $translate = $this->view(base64_encode("WebUI:Attachments"), [
     "ID" => $id,
     "Media" => [
      "Translate" => []
     ]
    ]);
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".EditBlog$id",
      "data-encryption" => "AES",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("Blog:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Blog.Attachments]" => $this->core->RenderView($attachments),
       "[Blog.Chat]" => $this->core->AESencrypt("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($description)."&ID=".base64_encode($id)."&Title=".base64_encode($title)."&Username=".base64_encode($author)),
       "[Blog.Header]" => $header,
       "[Blog.ID]" => $id,
       "[Blog.Translate]" => $this->core->RenderView($translate)
      ],
      "ExtensionID" => "7759aead7a3727dd2baed97550872677"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".BlogEditor$id",
       [
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
          "name" => "New",
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
          "name" => "Description",
          "placeholder" => "Description"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Description"
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($description)
        ],
        [
         "Attributes" => [
          "name" => "PassPhrase",
          "placeholder" => "Pass Phrase",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Pass Phrase"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($passPhrase)
        ],
        [
         "Attributes" => [],
         "OptionGroup" => $templates,
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Template"
         ],
         "Name" => "Template",
         "Type" => "Select",
         "Value" => $template
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
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The requested Blog could not be found.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "CallSign",
    "back",
    "lPG"
   ]);
   $addTo = $data["AddTo"] ?? "";
   $back = ($data["back"] == 1) ? $this->core->Element([
    "button", "Back to Blogs", [
     "class" => "GoToParent LI",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $card = $data["CARD"] ?? 0;
   $i = 0;
   $id = $data["ID"] ?? "";
   $public = $data["pub"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($public == 1) {
    $blogs = $this->core->DatabaseSet("Blog");
    foreach($blogs as $key => $value) {
     $value = str_replace("nyc.outerhaven.blg.", "", $value);
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     $callSignsMatch = ($data["CallSign"] == $this->core->CallSign($blog["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
      break;
     }
    }
   } if(!empty($id) || $i > 0) {
    $_Blog = $this->core->GetContentData([
     "ID" => base64_encode("Blog;$id")
    ]);
    $active = 0;
    $admin = 0;
    $blog = $_Blog["DataModel"];
    $contributors = $blog["Contributors"] ?? [];
    $options = $_Blog["ListItem"]["Options"];
    $owner = ($blog["UN"] == $you) ? $y : $this->core->Member($blog["UN"]);
    foreach($contributors as $member => $role) {
     if($active == 0 && $member == $you) {
      $active = 1;
      if($admin == 0 && $role == "Admin") {
       $admin = 1;
      }
     }
    } if($_Blog["Empty"] == 0) {
     $_AccessCode = "Accepted";
     $_IsArtist = $owner["Subscriptions"]["Artist"]["A"] ?? 0;
     $_IsVIP = $owner["Subscriptions"]["VIP"]["A"] ?? 0;
     $_IsSubscribed = ($_IsArtist == 1 || $_IsVIP == 1) ? 1 : 0;
     $passPhrase = $blog["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Blog["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "AddTo" => $addTo,
        "SecureKey" => base64_encode($passPhrase),
        "ID" => $id,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Blog:Home")
       ], true))
      ]]);
      $_Card = [
       "Front" => $this->core->RenderView($_View)
      ];
      $_Dialog = "";
      $_View = "";
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = "";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key == $secureKey) {
       $_View = $this->view(base64_encode("Blog:Home"), ["Data" => [
        "AddTo" => $addTo,
        "ID" => $id,
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View, 1);
       $_Commands = $_View["Commands"];
       $_View = $_View["View"];
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Dialog = "";
      $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
      $admin = ($active == 1 || $admin == 1 || $blog["UN"] == $you) ? 1 : 0;
      $blocked = $this->core->CheckBlocked([$y, "Blogs", $id]);
      $blockCommand = ($blocked == 0) ? "Block" : "Unblock";
      $chat = $this->core->Data("Get", ["chat", $id]);
      $purgeRenderCode = ($blog["UN"] == $you) ? "PURGE" : "DO NOT PURGE";
      $actions = (!empty($addToData)) ? $this->core->Element([
       "button", "Attach", [
        "class" => "Attach Small v2",
        "data-input" => base64_encode($addToData[1]),
        "data-media" => base64_encode("Blog;$id")
       ]
      ]) : "";
      $actions .= (!empty($chat)) ? $this->core->Element([
       "button", "Chat", [
        "class" => "OpenCard Small v2",
        "data-encryption" => "AES",
        "data-view" => $options["Chat"]
       ]
      ]) : "";
      $actions .= ($blog["UN"] == $you && $public == 0) ? $this->core->Element([
       "button", "Delete", [
        "class" => "CloseCard OpenDialog Small v2",
        "data-encryption" => "AES",
        "data-view" => $options["Delete"]
       ]
      ]) : "";
      $actions .= ($_IsArtist == 1) ? $this->core->Element([
       "button", "Donate", [
        "class" => "OpenDialog Small v2",
        "data-encryption" => "AES",
        "data-view" => $this->core->AESencrypt("v=".base64_encode("Profile:Donate")."&UN=".base64_encode($owner["Login"]["Username"]))
       ]
      ]) : "";
      $actions .= ($_IsSubscribed == 1 && $admin == 1) ? $this->core->Element([
       "button", "Edit", [
        "class" => "OpenCard Small v2",
        "data-encryption" => "AES",
        "data-view" => $options["Edit"]
       ]
      ]).$this->core->Element([
       "button", "Invite", [
        "class" => "OpenCard Small v2",
        "data-encryption" => "AES",
        "data-view" => $options["Invite"]
       ]
      ]).$this->core->Element([
       "button", "Post", [
        "class" => "OpenCard Small v2",
        "data-encryption" => "AES",
        "data-view" => $options["Post"]
       ]
      ]) : "";
      $actions .= $this->core->Element(["button", "Share", [
       "class" => "OpenCard Small v2",
        "data-encryption" => "AES",
       "data-view" => $options["Share"]
      ]]);
      $extensionID = $blog["TPL"] ?? "02a29f11df8a2664849b85d259ac8fc9";
      $search = base64_encode("Search:Containers");
      $_Commands = [
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Stream$id",
         $this->core->AESencrypt("v=$search&ID=".base64_encode($id)."&st=BGP")
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Vote$id",
         $options["Vote"]
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".Contributors$id",
         $this->core->AESencrypt("v=$search&ID=".base64_encode($id)."&Type=".base64_encode("Blog")."&st=Contributors"),
         15000
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".MemberGrid$id",
         $this->core->AESencrypt("v=".base64_encode("LiveView:MemberGrid")."&List=".base64_encode(json_encode($contributors, true))),
         15000
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".Subscribe$id",
         $options["Subscribe"],
         15000
        ]
       ]
      ];
      $_View = [
       "ChangeData" => [
        "[Blog.About]" => "About ".$owner["Personal"]["DisplayName"],
        "[Blog.Actions]" => $actions,
        "[Blog.Back]" => $back,
        "[Blog.Block]" => $options["Block"],
        "[Blog.Block.Text]" => $blockCommand,
        "[Blog.CoverPhoto]" => $_Blog["ListItem"]["CoverPhoto"],
        "[Blog.Description]" => $_Blog["ListItem"]["Description"],
        "[Blog.ID]" => $id,
        "[Blog.Title]" => $_Blog["ListItem"]["Title"],
        "[PurgeRenderCode]" => $purgeRenderCode
       ],
       "ExtensionID" => $extensionID
      ];
      $_Card = ($card == 1) ? [
       "Front" => $_View
      ] : "";
      $_View = ($card === 0) ? $_View : "";
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Invite(array $data): string {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing.",
    "Header" => "Not Found"
   ];
   $action = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $content = [];
    $contentOptions = $y["Blogs"] ?? [];
    foreach($contentOptions as $key => $value) {
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     $content[$value] = $blog["Title"];
    }
    $_Card = [
     "Action" => $this->core->Element(["button", "Send Invite", [
      "class" => "CardButton SendData",
      "data-form" => ".Invite$id",
      "data-processor" => base64_encode("v=".base64_encode("Blog:SendInvite"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Invite.Content]" => json_encode($content, true),
       "[Invite.ID]" => $id,
       "[Invite.Member]" => $member
      ],
      "ExtensionID" => "80e444c34034f9345eee7399b4467646"
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function Purge(array $data): string {
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    if(md5($key) == $secureKey) {
     $id = base64_decode($id);
     $blogs = $y["Blogs"] ?? [];
     $blog = $this->core->Data("Get", ["blg", $id]);
     $blogPosts = $blog["Posts"] ?? [];
     $newBlogs = [];
     $passPhrase = base64_encode($key);
     $securePassPhrase = base64_encode($secureKey);
     foreach($blogPosts as $key => $value) {
      $blogPost = $this->core->Data("Get", ["bp", $value]);
      if(!empty($blogPost)) {
       $this->view(base64_encode("BlogPost:Purge"), ["Data" => [
        "Key" => $passPhrase,
        "ID" => base64_encode($value),
        "SecureKey" => $securePassPhrase
       ]]);
      }
     } foreach($blogs as $key => $value) {
      if($id != $value) {
       array_push($newBlogs, $value);
      }
     }
     $y["Blogs"] = $newBlogs;
     $blog = $this->core->Data("Get", ["blg", $id]);
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $sql->query("DELETE FROM Blogs WHERE Blog_ID=:ID", [
      ":ID" => $id
     ]);
     $sql->execute();
     if(!empty($blog)) {
      $blog["Purge"] = 1;
      $this->core->Data("Save", ["blg", $id, $blog]);
     }
     $chat = $this->core->Data("Get", ["chat", $id]);
     if(!empty($chat)) {
      $chat["Purge"] = 1;
      $this->core->Data("Save", ["chat", $id, $chat]);
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
     $_View = $this->core->Element([
      "p", "The Blog <em>".$blog["Title"]."</em> and dependencies were marked for purging.",
      [
       "class" => "CenterText"
      ]
     ]).$this->core->Element([
      "button", "Okay", [
       "class" => "CloseDialog v2 v2w"
      ]
     ]);
    }
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
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $blogs = $this->core->DatabaseSet("Blog");
    $coverPhoto = "";
    $coverPhotoSource = "";
    $i = 0;
    $now = $this->core->timestamp;
    $title = $data["Title"] ?? "";
    foreach($blogs as $key => $value) {
     $value = str_replace("nyc.outerhaven.blg.", "", $value);
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     if($id != $blog["ID"] && $title == $blog["Title"]) {
      $i++;
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Blog <em>$title</em> is taken."
     ];
    } else {
     $_AccessCode = "Accepted";
     $blog = $this->core->Data("Get", ["blg", $id]);
     $albums = [];
     $albumsData = $data["Album"] ?? [];
     $articles = [];
     $articlesData = $data["Article"] ?? [];
     $attachments = [];
     $attachmentsData = $data["Attachment"] ?? [];
     $author = $blog["UN"] ?? $you;
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $blogs = [];
     $blogsData = $data["Blog"] ?? [];
     $blogPosts = [];
     $blogPostsData = $data["BlogPost"] ?? [];
     $chats = [];
     $chatsData = $data["Chat"] ?? [];
     $contributors = $blog["Contributors"] ?? [];
     $contributors[$author] = "Admin";
     $coverPhoto = $dats["CoverPhoto"] ?? "";
     $created = $blog["Created"] ?? $now;
     $forums = [];
     $forumsData = $data["Forum"] ?? [];
     $forumPosts = [];
     $forumPostsData = $data["ForumPost"] ?? [];
     $illegal = $blog["Illegal"] ?? 0;
     $members = []; 
     $membersData = $data["Member"] ?? [];
     $modifiedBy = $blog["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
     $passPhrase = $data["PassPhrase"] ?? "";
     $polls = []; 
     $pollsData = $data["Poll"] ?? [];
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
     $products = [];
     $productsData = $data["Product"] ?? [];
     $purge = $blog["Purge"] ?? 0;
     $posts = $blog["Posts"] ?? [];
     $shops = [];
     $shopsData = $data["Shop"] ?? [];
     $subscribers = $blog["Subscribers"] ?? [];
     $updates = [];
     $updatesData = $data["Update"] ?? [];
     foreach($subscribers as $key => $value) {
      $this->core->SendBulletin([
       "Data" => [
        "BlogID" => $id
       ],
       "To" => $value,
       "Type" => "BlogUpdate"
      ]);
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
     } if(!in_array($id, $y["Blogs"]) && $new == 1) {
      if($author == $you) {
       array_push($y["Blogs"], $id);
       $y["Blogs"] = array_unique($y["Blogs"]);
       $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
      }
     }
     $blog = [
      "Albums" => $albums,
      "Articles" => $articles,
      "Attachments" => $attachments,
      "Blogs" => $blogs,
      "BlogPosts" => $blogPosts,
      "Chats" => $chats,
      "Contributors" => $contributors,
      "CoverPhoto" => $coverPhoto,
      "Created" => $created,
      "Forums" => $forums,
      "ForumPosts" => $forumPosts,
      "ID" => $id,
      "Illegal" => $illegal,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "Description" => htmlentities($data["Description"]),
      "NSFW" => $nsfw,
      "PassPhrase" => $passPhrase,
      "Privacy" => $privacy,
      "Polls" => $polls,
      "Posts" => $posts,
      "Products" => $products,
      "Purge" => $purge,
      "Shops" => $shops,
      "Title" => $title,
      "TPL" => $data["Template"],
      "UN" => $author,
      "Updates" => $updates
     ];
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $query = "REPLACE INTO Blogs(
      Blog_Created,
      Blog_Description,
      Blog_ID,
      Blog_NSFW,
      Blog_Privacy,
      Blog_Title,
      Blog_Username
     ) VALUES(
      :Created,
      :Description,
      :ID,
      :NSFW,
      :Privacy,
      :Title,
      :Username
     )";
     $sql->query($query, [
      ":Created" => $created,
      ":Description" => $blog["Description"],
      ":ID" => $id,
      ":NSFW" => $blog["NSFW"],
      ":Privacy" => $blog["Privacy"],
      ":Title" => $blog["Title"],
      ":Username" => $author
     ]);
     $sql->execute();
     $this->core->Data("Save", ["blg", $id, $blog]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     if($new == 1) {
      $this->core->Statistic("Save Blog");
     } else {
      $this->core->Statistic("Update Blog");
     }
     $_Dialog = [
      "Body" => "The Blog <em>$title</em> was $actionTaken!",
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
  function SaveBanish(array $data): string {
   $_Dialog = [
    "Body" => "The Article Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $username = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($username)) {
    $id = base64_decode($id);
    $username = base64_decode($username);
    $blog = $this->core->Data("Get", ["blg", $id]);
    $_Dialog = [
     "Body" => "You cannot banish yourself."
    ];
    if($username != $blog["UN"] && $username != $you) {
     $_AccessCode = "Accepted";
     $contributors = $blog["Contributors"] ?? [];
     $newContributors = [];
     foreach($contributors as $member => $role) {
      if($username != $member) {
       $newContributors[$member] = $role;
      }
     }
     $blog["Contributors"] = $newContributors;
     $this->core->Data("Save", ["blg", $id, $blog]);
     $_Dialog = [
      "Body" => "$member was banished from <em>".$blog["Title"]."</em>.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SendInvite(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "ID",
    "Member",
    "Role"
   ]);
   $i = 0;
   $id = $data["ID"];
   $member = $data["Member"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($member)) {
    $blog = $this->core->Data("Get", ["blg", $id]);
    $members = $this->core->DatabaseSet("Member");
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     if($i == 0) {
      $t = $this->core->Data("Get", ["mbr", $value]);
      if($member == $t["Login"]["Username"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $_Dialog = [
      "Body" => "The Member $member does not exist."
     ];
    } elseif(empty($blog["ID"])) {
     $_Dialog = [
      "Body" => "The Blog does not exist."
     ];
    } elseif($blog["UN"] == $member) {
     $_Dialog = [
      "Body" => "$member owns <em>".$blog["Title"]."</em>."
     ];
    } elseif($member == $you) {
     $_Dialog = [
      "Body" => "You are already a contributor."
     ];
    } else {
     $active = 0;
     $contributors = $blog["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      if($member == $member) {
       $active++;
      }
     } if($active == 1) {
      $_Dialog = [
       "Body" => "$member is already a contributor."
      ];
     } else {
      $_AccessCode = "Accepted";
      $role = ($data["Role"] == 1) ? "Member" : "Admin";
      $contributors[$member] = $role;
      $blog["Contributors"] = $contributors;
      $this->core->SendBulletin([
       "Data" => [
        "BlogID" => $id,
        "Member" => $member,
        "Role" => $role
       ],
       "To" => $member,
       "Type" => "InviteToBlog"
      ]);
      $this->core->Data("Save", ["blg", $id, $blog]);
      $_Dialog = [
       "Body" => "$member was notified of your invitation.",
       "Header" => "Invitation Sent"
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function Subscribe(array $data): string {
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
    $blog = $this->core->Data("Get", ["blg", $id]);
    $subscribers = $blog["Subscribers"] ?? [];
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
    $blog["Subscribers"] = $subscribers;
    $this->core->Data("Save", ["blg", $id, $blog]);
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
<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol . $_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
 Class StatusUpdate extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Post Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["SU"] ?? "";
   $new = $data["new"] ?? 0;
   $to = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $_Dialog = "";
    $id = ($new == 1) ? $this->core->UUID("StatusUpdatesBy$you") : $id;
    $action = ($new == 1) ? "Post" : "Update";
    $header = ($new == 1) ? "What's on your mind?" : "Edit Update";
    $update = $this->core->Data("Get", ["su", $id]);
    $albums = $update["Albums"] ?? [];
    $articles = $update["Articles"] ?? [];
    $attachments = $update["Attachments"] ?? [];
    $body = $update["Body"] ?? "";
    $body = (!empty($data["Body"])) ? base64_decode($data["Body"]) : base64_decode($body);
    $blogs = $update["Blogs"] ?? [];
    $blogPosts = $update["BlogPosts"] ?? [];
    $chats = $update["Chat"] ?? [];
    $coverPhoto = $update["CoverPhoto"] ?? "";
    $forums = $update["Forums"] ?? [];
    $forumPosts = $update["ForumPosts"] ?? [];
    $members = $update["Members"] ?? [];
    $now = $this->core->timestamp;
    $nsfw = $update["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $update["PassPhrase"] ?? "";
    $privacy = $update["Privacy"] ?? $y["Privacy"]["Posts"];
    $polls = $update["Polls"] ?? [];
    $products = $update["Products"] ?? [];
    $shops = $update["Shops"] ?? [];
    $updates = $update["Updates"] ?? [];
    $to = (!empty($to)) ? base64_decode($to) : $to;
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
      "data-form" => ".EditStatusUpdate$id",
      "data-processor" => base64_encode("v=".base64_encode("StatusUpdate:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Update.Attachments]" => $this->core->RenderView($attachments),
       "[Update.Header]" => $header,
       "[Update.ID]" => $id,
       "[Update.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign)
      ],
      "ExtensionID" => "7cc50dca7d9bbd7b7d0e3dd7e2450112"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".StatusUpdateInformation$id",
       [
        [
         "Attributes" => [
          "name" => "From",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $you
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
          "name" => "To",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $to
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
          "data-editor-identifier" => "EditUpdateBody$id",
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
         "Value" => $this->core->AESencrypt($body)
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
          "HeaderText" => "Pass Phrase",
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($passPhrase)
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
    "Body" => "The Post Identifier is missing.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["SU"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_StatusUpdate = $this->core->GetContentData([
     "ID" => base64_encode("StatusUpdate;$id")
    ]);
    if($_StatusUpdate["Empty"] == 0) {
     $update = $_StatusUpdate["DataModel"];
     $blocked = $this->core->CheckBlocked([$y, "Status Updates", $data["SU"]]);
     $blockCommand = ($blocked == 0) ? "Block" : "Unblock";
     $options = $_StatusUpdate["ListItem"]["Options"];
     $purgeRenderCode = ($update["From"] == $you) ? "PURGE" : "DO NOT PURGE";
     $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($update, "LiveView");
     $passPhrase = $update["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_Card = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access this Status Update."),
       "ViewData" => base64_encode(json_encode([
        "SecureKey" => base64_encode($passPhrase),
        "SU" => $id,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("StatusUpdate:Home")
       ], true))
      ]]);
      $_Card = [
       "Front" => $this->core->RenderView($_Card)
      ];
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = "";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key == $secureKey) {
       $_View = $this->view(base64_encode("StatusUpdate:Home"), ["Data" => [
        "SU" => $id,
        "EmbeddedView" => 1,
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View, 1);
       $_Commands = $_View["Commands"];
       $_View = $_View["View"];
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Card = "";
      $_Commands = [
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Albums".$update["ID"],
         $liveViewSymbolicLinks["Albums"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Articles".$update["ID"],
         $liveViewSymbolicLinks["Articles"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Attachments".$update["ID"],
         $liveViewSymbolicLinks["Attachments"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Blogs".$update["ID"],
         $liveViewSymbolicLinks["Blogs"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".BlogPosts".$update["ID"],
         $liveViewSymbolicLinks["BlogPosts"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Chats".$update["ID"],
         $liveViewSymbolicLinks["Chats"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Conversation".$update["ID"],
         $this->core->AESencrypt("v=".base64_encode("Conversation:Home")."&CRID=".base64_encode($update["ID"])."&LVL=".base64_encode(1))
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Forums".$update["ID"],
         $liveViewSymbolicLinks["Forums"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".ForumPosts".$update["ID"],
         $liveViewSymbolicLinks["ForumPosts"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Members".$update["ID"],
         $liveViewSymbolicLinks["Members"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Notes".$update["ID"],
         $options["Notes"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Polls".$update["ID"],
         $liveViewSymbolicLinks["Polls"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Products".$update["ID"],
         $liveViewSymbolicLinks["Products"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Shops".$update["ID"],
         $liveViewSymbolicLinks["Shops"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Updates".$update["ID"],
         $liveViewSymbolicLinks["Updates"]
        ]
       ],
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Vote".$update["ID"],
         $options["Vote"]
        ]
       ]
      ];
      $_Dialog = "";
      $displayName = $update["From"];
      $displayName = (!empty($update["To"]) && $update["From"] != $update["To"]) ? "$displayName to ".$update["To"] : $displayName;
      $embeddedView = $data["EmbeddedView"] ?? 0;
      $op = ($update["From"] == $you) ? $y : $this->core->Member($update["From"]);
      $share = ($update["From"] == $you || $update["Privacy"] == md5("Public")) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element(["div", $this->core->Element([
       "button", "Share", [
        "class" => "InnerMargin OpenCard",
        "data-encryption" => "AES",
        "data-view" => $options["Share"]
       ]]), [
        "class" => "CenterText Desktop33"
       ]]) : "";
      $verified = $op["Verified"] ?? 0;
      $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
      $_View = [
       "ChangeData" => [
        "[StatusUpdate.Block]" => $options["Block"],
        "[StatusUpdate.Block.Text]" => $blockCommand,
        "[StatusUpdate.Body]" => $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => base64_decode($update["Body"]),
         "Display" => 1
        ]),
        "[StatusUpdate.CoverPhoto]" => $_StatusUpdate["ListItem"]["CoverPhoto"],
        "[StatusUpdate.Created]" => $this->core->TimeAgo($update["Created"]),
        "[StatusUpdate.DisplayName]" => $displayName.$verified,
        "[StatusUpdate.ID]" => $update["ID"],
        "[StatusUpdate.Illegal]" => $this->core->AESencrypt("v=".base64_encode("Congress:Report")."&ID=".base64_encode("StatusUpdate;".$update["ID"])),
        "[StatusUpdate.Modified]" => $_StatusUpdate["ListItem"]["Modified"],
        "[StatusUpdate.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
        "[StatusUpdate.Share]" => $share,
        "[PurgeRenderCode]" => $purgeRenderCode
       ],
       "ExtensionID" => "2e76fb1523c34ed0c8092cde66895eb1"
      ];
      $_Card = ($embeddedView == 0) ? [
       "Front" => $_View
      ] : "";
      $_View = ($embeddedView == 1) ? $_View : "";
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
  function Public(array $data): string {
   $_Query = "SELECT * FROM StatusUpdates
                       JOIN Members
                       ON Member_Username=StatusUpdate_Username
                       WHERE StatusUpdate_ID=:ID AND
                                     StatusUpdate_Username=:Username";
   $_View = $this->view(base64_encode("WebUI:Error"), ["Data" => [
    "Error" => 404
   ]]);
   $_View = $this->core->RenderView($_View);
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $id = base64_decode($id);
   $username = $data["UN"] ?? base64_encode("");
   $username = base64_decode($username);
   $sql = New SQL($this->core->cypher->SQLCredentials());
   $sql->query($_Query, [
    ":ID" => $id,
    ":Username" => $username
   ]);
   $sql = $sql->set();
   if(count($sql) === 1) {
    $_View = $this->view(base64_encode("StatusUpdate:Home"), ["Data" => [
     "SU" => $id
    ]]);
    $_View = $this->core->RenderView($_View);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => 1,
    "View" => $_View
   ]);
  }
  function Purge(array $data): string {
   $_Dialog = [
    "Body" => "The Status Update Identifier is missing."
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
   if(md5($key) != $secureKey) {
    $_Dialog = "";
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $newStream = [];
    $stream = $this->core->Data("Get", ["stream", md5($you)]);
    foreach($stream as $key => $value) {
     if($id != $value["UpdateID"]) {
      $newStream[$key] = $value;
     }
    }
    $y["Activity"]["LastActive"] = $this->core->timestamp;
    $conversation = $this->core->Data("Get", ["conversation", $id]);
    if(!empty($conversation)) {
     $conversation["Purge"] = 1;
     $this->core->Data("Save", ["conversation", $id, $conversation]);
    }
    $statusUpdate = $this->core->Data("Get", ["su", $id]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM StatusUpdates WHERE StatusUpdate_ID=:ID", [
     ":ID" => $id
    ]);
    $sql->execute();
    if(!empty($statusUpdate)) {
     $statusUpdate["Purge"] = 1;
     $this->core->Data("Save", ["su", $id, $statusUpdate]);
    }
    $stream = $newStream;
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
    $this->core->Data("Save", ["stream", md5($you), $stream]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", "The Update and dependencies were marked for purging.",
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
    "Body" => "The Update Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $to = $data["To"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_AccessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $update = $this->core->Data("Get", ["su", $id]);
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
    $body = $data["Body"] ?? "";
    $chats = [];
    $chatsData = $data["Chat"] ?? [];
    $coverPhoto = $data["CoverPhoto"] ?? "";
    $created = $update["Created"] ?? $this->core->timestamp;
    $forums = [];
    $forumsData = $data["Forum"] ?? [];
    $forumPosts = [];
    $forumPostsData = $data["ForumPost"] ?? [];
    $illegal = $update["Illegal"] ?? 0;
    $members = []; 
    $membersData = $data["Member"] ?? [];
    $notes = $update["Notes"] ?? [];
    $now = $this->core->timestamp;
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
    } if($new == 1) {
     if(!empty($to) && $to != $you) {
      $stream = $this->core->Data("Get", ["stream", md5($to)]);
      $stream[$created] = $update;
      $this->core->Data("Save", ["stream", md5($to), $stream]);
     }
     $stream = $this->core->Data("Get", ["stream", md5($you)]);
     $stream[$created] = $update;
     $this->core->Data("Save", ["stream", md5($you), $stream]);
    }
    $update = [
     "Albums" => $albums,
     "Articles" => $articles,
     "Attachments" => $attachments,
     "Blogs" => $blogs,
     "BlogPosts" => $blogPosts,
     "Body" => base64_encode($body),
     "Chats" => $chats,
     "CoverPhoto" => $coverPhoto,
     "Created" => $created,
     "Forums" => $forums,
     "ForumPosts" => $forumPosts,
     "From" => $you,
     "ID" => $id,
     "Illegal" => $illegal,
     "Members" => $members,
     "Modified" => $now,
     "Notes" => $notes,
     "NSFW" => $nsfw,
     "PassPhrase" => $passPhrase,
     "Privacy" => $privacy,
     "Polls" => $polls,
     "Products" => $products,
     "Purge" => $purge,
     "Shops" => $shops,
     "To" => $to,
     "Updates" => $updates
    ];
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $query = "REPLACE INTO StatusUpdates(
     StatusUpdate_Body,
     StatusUpdate_Created,
     StatusUpdate_ID,
     StatusUpdate_NSFW,
     StatusUpdate_Privacy,
     StatusUpdate_To,
     StatusUpdate_Username
    ) VALUES(
     :Body,
     :Created,
     :ID,
     :NSFW,
     :Privacy,
     :To,
     :Username
    )";
    $sql->query($query, [
     ":Body" => $update["Body"],
     ":Created" => $created,
     ":ID" => $update["ID"],
     ":NSFW" => $update["NSFW"],
     ":Privacy" => $update["Privacy"],
     ":To" => $update["To"],
     ":Username" => $update["From"]
    ]);
    $sql->execute();
    $statistic = ($new == 1) ? "New Status Update" : "Edit Status Update";
    $y["Activity"]["LastActivity"] = $this->core->timestamp;
    $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
    $this->core->Data("Save", ["su", $update["ID"], $update]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $this->core->Statistic($statistic);
    $_Dialog = [
     "Body" => "The Status Update was $actionTaken.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
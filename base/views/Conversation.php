<?php
 Class Conversation extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Conversation Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $commentID = $data["CommentID"] ?? "";
   $conversationID = $data["ConversationID"] ?? "";
   $level = $data["Level"] ?? base64_encode(1);
   $new = $data["new"] ?? 0;
   $replyingTo = $data["ReplyingTo"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($conversationID)) {
    $_Dialog = "";
    $attachments = "";
    $commentID = base64_decode($commentID);
    $commentID = ($new == 1) ? $this->core->UUID("CommentOrReplyBy$you") : $commentID;
    $conversationID = base64_decode($conversationID);
    $action = ($new == 1) ? "Post" : "Update";
    $level = base64_decode($level);
    $commentType = ($level == 1) ? "Comment" : "Reply";
    $conversation = $this->core->Data("Get", ["conversation", $conversationID]);
    $comment = $cconversation[$commentID] ?? [];
    $albums = $comment["Albums"] ?? [];
    $articles = $comment["Articles"] ?? [];
    $attachments = $comment["Attachments"] ?? [];
    $blogs = $comment["Blogs"] ?? [];
    $blogPosts = $post["BlogPosts"] ?? [];
    $body = $comment["Body"] ?? base64_encode("");
    $body = base64_decode($body);
    $chats = $comment["Chat"] ?? [];
    $forums = $comment["Forums"] ?? [];
    $forumPosts = $comment["ForumPosts"] ?? [];
    $header = ($new == 1) ? "New $commentType" : "Edit $commentType";
    $members = $comment["Members"] ?? [];
    $nsfw = $comment["NSFW"] ?? $y["Privacy"]["NSFW"];
    $polls = $comment["Polls"] ?? [];
    $privacy = $comment["Privacy"] ?? $y["Privacy"]["Comments"];
    $products = $comment["Products"] ?? [];
    $replyingTo = base64_decode($replyingTo);
    $shops = $comment["Shops"] ?? [];
    $updates = $comment["Updates"] ?? [];
    $attachments = $this->view(base64_encode("WebUI:Attachments"), [
     "Header" => "Attachments",
     "ID" => $commentID,
     "Media" => [
      "Album" => $albums,
      "Article" => $articles,
      "Attachment" => $attachments,
      "Blog" => $blogs,
      "BlogPost" => $blogPosts,
      "Chat" => $chats,
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
     "ID" => $commentID,
     "Media" => [
      "Translate" => []
     ]
    ]);
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".ConversationEditor$conversationID",
      "data-processor" => base64_encode("v=".base64_encode("Conversation:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Conversation.Attachments]" => $this->core->RenderView($attachments),
       "[Conversation.Header]" => $header,
       "[Conversation.ID]" => $conversationID,
       "[Conversation.Translate]" => $this->core->RenderView($translate)
      ],
      "ExtensionID" => "0426a7fc6b31e5034b6c2cec489ea638"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".NewComment$conversationID",
       [
        [
         "Attributes" => [
          "name" => "CommentID",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $commentID
        ],
        [
         "Attributes" => [
          "name" => "ID",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $conversationID
        ],
        [
         "Attributes" => [
          "name" => "Level",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $level
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
          "name" => "ReplyingTo",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $replyingTo
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Body",
          "placeholder" => "Say something..."
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Body"
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($this->core->PlainText([
          "Data" => $body,
          "Decode" => 1,
          "HTMLDecode" => 1
         ]))
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".NSFW$conversationID",
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
       ".Privacy$conversationID",
       [
        "Value" => $privacy
       ]
      ]
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "commentID" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data): string {
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Conversation Identifier is missing.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $commentID = $data["CommentID"] ?? "";
   $conversationID = $data["CRID"] ?? "";
   $edit = base64_encode("Conversation:Edit");
   $hide = base64_encode("Conversation:MarkAsHidden");
   $i = 0;
   $level = $data["Level"] ?? base64_encode(1);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $minimalDesign = $y["Personal"]["MinimalDesign"] ?? 0;
   if(!empty($conversationID)) {
    $_Commands = [];
    $_Dialog = "";
    $anon = "Anonymous";
    $commentID = base64_decode($commentID);
    $commentType = "";
    $conversationID = base64_decode($conversationID);
    $level = base64_decode($level);
    $level = $level ?? 1;
    $conversation = $this->core->Data("Get", ["conversation", $conversationID]);
    $home = base64_encode("Conversation:Home");
    $im = base64_encode("LiveView:InlineMossaic");
    $vote = base64_encode("Vote:Containers");
    if($level == 1) {
     $_Extension = $this->core->Extension("8938c49b85c52a5429cc8a9f46c14616");
     $_View = [
      "ChangeData" => [
       "[Comment.Editor]" => $this->core->AESencrypt("v=$edit&ConversationID=".base64_encode($conversationID)."&new=1")
      ],
      "ExtensionID" => "97e7d7d9a85b30e10ab51b23623ccee5"
     ];
     foreach($conversation as $key => $value) {
      $t = ($value["From"] == $you) ? $y : $this->core->Member($value["From"]);
      $bl = $this->core->CheckBlocked([$y, "Comments", $key]);
      $cms = $this->core->Data("Get", ["cms", md5($value["From"])]);
      $ck = ($value["NSFW"] == 0 || ($y["age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $value["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $ck3 = (empty($value["CommentID"])) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1) {
       $attachments = $value["DLC"] ?? "";
       if(!empty($attachments)) {
        $attachments = (!empty($attachments)) ?  $this->view($in, ["Data" => [
         "ID" => base64_encode(implode(";", $attachments))
        ]]) : "";
        $attachments = $this->core->RenderView($attachments);
       }
       $op = ($value["From"] == $this->core->ID) ? $anon : $value["From"];
       $opt = ($this->core->ID != $you && $value["From"] == $you) ? $this->core->Element([
        "div", $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin OpenDialog",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=$edit&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin OpenDialog",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=$hide&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID))
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
       $verified = $op["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       $commentType .= $this->core->Change([[
        "[Comment.Attachments]" => $attachments,
        "[Comment.Body]" => $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => base64_decode($value["Body"]),
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Comment.Created]" => $this->core->TimeAgo($value["Created"]),
        "[Comment.ID]" => $key,
        "[Comment.Options]" => $opt,
        "[Comment.OriginalPoster]" => $op.$verified,
        "[Comment.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);")
       ], $_Extension]);
       array_push($_Commands, [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Replies$key",
         $this->core->AESencrypt("v=$home&CommentID=".base64_encode($key)."&CRID=".base64_encode($conversationID)."&Level=".base64_encode(2))
        ]
       ]);
       array_push($_Commands, [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Vote$key",
         $this->core->AESencrypt("v=$vote&ID=$key&Type=3")
        ]
       ]);
       $i++;
      }
     }
     $commentType .= $this->core->Change([[
      "[Reply.Editor]" => $this->core->AESencrypt("v=$edit&CommentID=".base64_encode($commentID)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&new=1")
     ], $this->core->Extension("5efa423862a163dd55a2785bc7327727")]);
     $_View = ($i > 0) ? [
      "ChangeData" => [],
      "Extension"=>$this->core->AESencrypt($commentType)
     ] : $_View;
     $_View = ($minimalDesign == 1) ? "" : $_View;
    } elseif($level == 2) {
     $_Extension = $this->core->Extension("8938c49b85c52a5429cc8a9f46c14616");
     $t = $this->core->Member($conversation[$commentID]["From"]);
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $_View = $this->core->Extension("cc3c7b726c1d7f9c50f5f7869513bd80");
     foreach($conversation as $key => $value) {
      $t = ($value["From"] == $you) ? $y : $this->core->Member($value["From"]);
      $bl = $this->core->CheckBlocked([$y, "Comments", $key]);
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]);
      $ck = ($commentID == $value["CommentID"]) ? 1 : 0;
      $ck2 = ($value["NSFW"] == 0 || $y["Personal"]["Age"] >= $this->core->config["minAge"]) ? 1 : 0;
      $ck3 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $value["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1) {
       $attachments = $value["DLC"] ?? "";
       if(!empty($attachments)) {
        $attachments = (!empty($attachments)) ?  $this->view($in, ["Data" => [
         "ID" => base64_encode(implode(";", $attachments))
        ]]) : "";
        $attachments = $this->core->RenderView($attachments);
       }
       $op = ($value["From"] == $this->core->ID) ? $anon : $value["From"];
       $opt = ($this->core->ID != $you && $value["From"] == $you) ? $this->core->Element([
        "div", $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin OpenCard",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=$edit&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($value["CommentID"]))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin OpenDialog",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=$hide&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID))
        ]]), ["class" => "CenterText Desktop33"]
      ]) : "";
      $verified = $op["Verified"] ?? 0;
      $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
      $commentType .= $this->core->Change([[
       "[Comment.Attachments]" => $attachments,
       "[Comment.Body]" => $this->core->PlainText([
        "BBCodes" => 1,
        "Data" => base64_decode($value["Body"]),
        "Display" => 1,
        "HTMLDecode" => 1
       ]),
       "[Comment.Created]" => $this->core->TimeAgo($value["Created"]),
       "[Comment.ID]" => $key,
       "[Comment.Options]" => $opt,
       "[Comment.OriginalPoster]" => $op.$verified,
       "[Comment.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);")
      ], $_Extension]);
      array_push($_Commands, [
       "Name" => "UpdateContentAES",
       "Parameters" => [
        ".Replies$key",
        $this->core->AESencrypt("v=$home&CommentID=".base64_encode($key)."&CRID=".base64_encode($conversationID)."&Level=".base64_encode(3))
       ]
      ]);
      array_push($_Commands, [
       "Name" => "UpdateContentAES",
       "Parameters" => [
        ".Vote$key",
        $this->core->AESencrypt("v=$vote&ID=$key&Type=3")
       ]
      ]);
      $i++;
     }
    }
    $_View = ($i > 0) ? $commentType : $_View;
    $_View .= $this->core->Change([[
     "[Reply.DisplayName]" => $display,
     "[Reply.Editor]" => $this->core->AESencrypt("v=$edit&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($commentID)."&new=1")
    ], $this->core->Extension("f6876eb53ff51bf537b1b1848500bdab")]);
    $_View = [
     "ChangeData" => [],
     "Extension"=>$this->core->AESencrypt($_View)
    ];
   } elseif($level == 3) {
     $_Extension = $this->core->Extension("3847a50cd198853fe31434b6f4e922fd");
     $t = $this->core->Member($conversation[$commentID]["From"]);
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $_View = $this->core->Extension("cc3c7b726c1d7f9c50f5f7869513bd80");
     foreach($conversation as $key => $value) {
      $t = ($value["From"] == $you) ? $y : $this->core->Member($value["From"]);
      $bl = $this->core->CheckBlocked([$y, "Comments", $key]);
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]);
      $ck = ($commentID == $value["CommentID"]) ? 1 : 0;
      $ck2 = ($value["NSFW"] == 0 || $y["Personal"]["Age"] >= $this->core->config["minAge"]) ? 1 : 0;
      $ck3 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $value["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1) {
       $attachments = $value["DLC"] ?? "";
       if(!empty($attachments)) {
        $attachments = (!empty($attachments)) ?  $this->view($in, ["Data" => [
         "ID" => base64_encode(implode(";", $attachments))
        ]]) : "";
        $attachments = $this->core->RenderView($attachments);
       }
       $op = ($value["From"] == $this->core->ID) ? $anon : $value["From"];
       $opt = ($this->core->ID != $you && $value["From"] == $you) ? $this->core->Element([
        "div", $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin OpenCard",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=$edit&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($value["CommentID"]))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin OpenDialog",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=$hide&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID))
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
       $verified = $op["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       $commentType .= $this->core->Change([[
        "[Reply.Attachments]" => $attachments,
        "[Reply.Body]" => $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => base64_decode($value["Body"]),
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Reply.Created]" => $this->core->TimeAgo($value["Created"]),
        "[Reply.ID]" => $key,
        "[Reply.Options]" => $opt,
        "[Reply.OriginalPoster]" => $op.$verified,
        "[Reply.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);")
       ], $_Extension]);
       array_push($_Commands, [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Vote$key",
         $this->core->AESencrypt("v=$vote&ID=$key&Type=3")
        ]
       ]);
       $i++;
      }
     }
     $_View = ($i > 0) ? $commentType : $_View;
     $_View .= $this->core->Change([[
      "[Reply.DisplayName]" => $display,
      "[Reply.Editor]" => $this->core->AESencrypt("v=$edit&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($commentID)."&new=1")
     ], $this->core->Extension("f6876eb53ff51bf537b1b1848500bdab")]);
     $_View = [
      "ChangeData" => [],
      "Extension"=>$this->core->AESencrypt($_View)
     ];
    }
   }
   return $this->core->JSONResponse([
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function MarkAsHidden(array $data): string {
   $_Dialog = [
    "Body" => "The Conversation or comment Identifier are missing."
   ];
   $data = $data["Data"] ?? [];
   $commentID = $data["CommentID"] ?? "";
   $conversationID = $data["ConversationID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($commentID) && !empty($conversationID)) {
    $commentID = base64_decode($commentID);
    $conversationID = base64_decode($conversationID);
    $conversation = $this->core->Data("Get", [
     "conversation",
     $conversationID
    ]);
    $comment = $conversation[$commentID] ?? [];
    $comment["Privacy"] = md5("Private");
    $conversation[$commentID] = $comment;
    $this->core->Data("Save", ["conversation", $conversationID, $conversation]);
    $_Dialog = [
     "Body" => "The comment is hidden, and only you can see it.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Conversation or $commentType Identifier are missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $commentID = $data["CommentID"] ?? "";
   $id = $data["ID"] ?? "";
   $level = $data["Level"] ?? 1;
   $commentType = ($level == 1) ? "comment" : "reply";
   $new = $data["New"] ?? 0;
   $replyingTo = $data["ReplyingTo"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($commentID) && !empty($id)) {
    $_AccessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
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
    $cc = ($level > 1) ? "Comment$commentID" : "Conversation$id";
    $chats = [];
    $chatsData = $data["Chat"] ?? [];
    $conversation = $this->core->Data("Get", ["conversation", $id]);
    $comment = $conversation[$commentID] ?? [];
    $created = $conversation["Created"] ?? $this->core->timestamp;
    $forums = [];
    $forumsData = $data["Forum"] ?? [];
    $forumPosts = [];
    $forumPostsData = $data["ForumPost"] ?? [];
    $home = base64_encode("Conversation:Home");
    $members = []; 
    $membersData = $data["Member"] ?? [];
    $nsfw = $conversation["NSFW"] ?? $y["Privacy"]["NSFW"];
    $nsfw = $data["NSFW"] ?? $nsfw;
    $polls = []; 
    $pollsData = $data["Poll"] ?? [];
    $privacy = $conversation["Privacy"] ?? $y["Privacy"]["Comments"];
    $privacy = $data["Privacy"] ?? $privacy;
    $products = [];
    $productsData = $data["Product"] ?? [];
    $purge = $conversation["Privacy"] ?? 0;
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
    }
    $conversation[$commentID] = [
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
     "CommentID" => $replyingTo,
     "Created" => $created,
     "Forums" => $forums,
     "ForumPosts" => $forumPosts,
     "From" => $you,
     "Members" => $members,
     "Modified" => $this->core->timestamp,
     "NSFW" => $nsfw,
     "Polls" => $polls,
     "Privacy" => $privacy,
     "Products" => $products,
     "Shops" => $shops,
     "Updates" => $updates
    ];
    if($purge != 0) {
     $conversation["Purge"] = $purge;
    }
    #$this->core->Data("Save", ["conversation", $id, $conversation]);
    $_Dialog = [
     "Body" => "Your $commentType was $actionTaken.",
     "Header" => "Done",
     "Scrollable" => json_encode($conversation, true)
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
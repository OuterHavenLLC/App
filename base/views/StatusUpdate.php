<?php
 Class StatusUpdate extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $button = "";
   $data = $a["Data"] ?? [];
   $id = $data["SU"] ?? "";
   $new = $data["new"] ?? 0;
   $now = $this->core->timestamp;
   $r = [
    "Body" => "The Post Identifier is missing."
   ];
   $to = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $id = ($new == 1) ? md5($you."_SU_$now") : $id;
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditStatusUpdate$id",
     "data-processor" => base64_encode("v=".base64_encode("StatusUpdate:Save"))
    ]]);
    $attachments = "";
    $header = ($new == 1) ? "What's on your mind?" : "Edit Update";
    $update = $this->core->Data("Get", ["su", $id]);
    $attachments = $update["Attachments"] ?? [];
    $body = $update["Body"] ?? "";
    $body = (!empty($data["Body"])) ? base64_decode($data["Body"]) : $body;
    $blogs = $update["Blogs"] ?? [];
    $blogPosts = $update["BlogPosts"] ?? [];
    $coverPhoto = $update["CoverPhoto"] ?? [];
    $forums = $update["Forums"] ?? [];
    $forumPosts = $update["ForumPosts"] ?? [];
    $nsfw = $update["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $update["PassPhrase"] ?? "";
    $privacy = $update["Privacy"] ?? $y["Privacy"]["Posts"];
    $polls = $update["Polls"] ?? [];
    $products = $update["Products"] ?? [];
    $shops = $update["Shops"] ?? [];
    $to = (!empty($to)) ? base64_decode($to) : $to;
    $attachments = $this->view(base64_encode("WebUI:Attachments"), [
     "Header" => "Attachments",
     "ID" => $id,
     "Media" => [
      "Attachments" => $attachments,
      "Blogs" => $blogs,
      "BlogPosts" => $blogPosts,
      "CoverPhoto" => $coverPhoto,
      "Forums" => $forums,
      "ForumPosts" => $forumPosts,
      "Polls" => $polls,
      "Products" => $products,
      "Shops" => $shops
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
     "[Update.Attachments]" => $this->core->RenderView($attachments),
     "[Update.Header]" => $header,
     "[Update.ID]" => $id,
     "[Update.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body
     ])),
     "[Update.DesignView]" => "Edit$id",
     "[Update.From]" => $you,
     "[Update.ID]" => $id,
     "[Update.New]" => $new,
     "[Update.PassPhrase]" => base64_encode($passPhrase),
     "[Update.To]" => $to,
     "[Update.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign),
     "[Update.Visibility.NSFW]" => $nsfw,
     "[Update.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("7cc50dca7d9bbd7b7d0e3dd7e2450112")]);
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
   $id = $data["SU"] ?? "";
   $r = [
    "Body" => "The Post Identifier is missing.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $bl = $this->core->CheckBlocked([$y, "Status Updates", $data["SU"]]);
    $blockCommand = ($bl == 0) ? "Block" : "Unblock";
    $_StatusUpdate = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("StatusUpdate;$id")
    ]);
    if($_StatusUpdate["Empty"] == 0) {
     $accessCode = "Accepted";
     $update = $_StatusUpdate["DataModel"];
     $passPhrase = $update["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
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
       $r = $this->view(base64_encode("StatusUpdate:Home"), ["Data" => [
        "SU" => $id,
        "EmbeddedView" => 1,
        "ViewProtectedContent" => 1
       ]]);
       $r = $this->core->RenderView($r);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $accessCode = "Accepted";
      $displayName = $update["From"];
      $displayName = (!empty($update["To"]) && $update["From"] != $update["To"]) ? "$displayName to ".$update["To"] : $displayName;
      $embeddedView = $data["EmbeddedView"] ?? 0;
      $op = ($update["From"] == $you) ? $y : $this->core->Member($update["From"]);
      $options = $_StatusUpdate["ListItem"]["Options"];
      $opt = ($update["From"] != $you) ? $this->core->Element([
       "button", $blockCommand, [
        "class" => "Small UpdateButton v2",
        "data-processor" => $options["Block"]
       ]
      ]) : "";
      $opt = ($this->core->ID != $you) ? $opt : "";
      $share = ($update["From"] == $you || $update["Privacy"] == md5("Public")) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element([
       "div", $this->core->Element(["button", "Share", [
        "class" => "InnerMargin OpenCard",
        "data-view" => $options["Share"]
       ]]), ["class" => "Desktop33"]
      ]) : "";
      $verified = $op["Verified"] ?? 0;
      $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
      $r = $this->core->Change([[
       "[StatusUpdate.Attachments]" => $_StatusUpdate["ListItem"]["Attachments"],
       "[StatusUpdate.Body]" => $this->core->PlainText([
        "BBCodes" => 1,
        "Data" => $update["Body"],
        "Display" => 1,
        "HTMLDecode" => 1
       ]),
       "[StatusUpdate.Created]" => $this->core->TimeAgo($update["Created"]),
       "[StatusUpdate.Conversation]" => $this->core->Change([[
        "[Conversation.CRID]" => $update["ID"],
        "[Conversation.CRIDE]" => base64_encode($update["ID"]),
        "[Conversation.Level]" => base64_encode(1),
        "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
       ], $this->core->Extension("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
       "[StatusUpdate.DisplayName]" => $displayName.$verified,
       "[StatusUpdate.ID]" => $update["ID"],
       "[StatusUpdate.Illegal]" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("StatusUpdate;".$update["ID"])),
       "[StatusUpdate.Modified]" => $_StatusUpdate["ListItem"]["Modified"],
       "[StatusUpdate.Notes]" => $options["Notes"],
       "[StatusUpdate.Options]" => $opt,
       "[StatusUpdate.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
       "[StatusUpdate.Share]" => $share,
       "[StatusUpdate.Votes]" => $options["Vote"]
      ], $this->core->Extension("2e76fb1523c34ed0c8092cde66895eb1")]);
      $r = ($embeddedView == 1) ? $r : [
       "Front" => $r
      ];
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Update Identifier is missing."
   ];
   $to = $data["To"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $update = $this->core->Data("Get", ["su", $id]) ?? [];
    $attachments = [];
    $attachmentsData = $data["Attachments"] ?? [];
    $blogs = [];
    $blogsData = $data["Blogs"] ?? [];
    $blogPosts = [];
    $blogPostsData = $data["BlogPosts"] ?? [];
    $coverPhoto = "";
    $coverPhotoData = $data["CoverPhoto"] ?? [];
    $created = $update["Created"] ?? $this->core->timestamp;
    $forums = [];
    $forumsData = $data["Forums"] ?? [];
    $forumPosts = [];
    $forumPostsData = $data["ForumPosts"] ?? [];
    $illegal = $update["Illegal"] ?? 0;
    $notes = $update["Notes"] ?? [];
    $now = $this->core->timestamp;
    $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $data["PassPhrase"] ?? "";
    $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
    $polls = []; 
    $pollsData = $data["Polls"] ?? [];
    $products = [];
    $productsData = $data["Products"] ?? [];
    $purge = $data["Purge"] ?? 0;
    $shops = [];
    $shopsData = $data["Shops"] ?? [];
    if(!empty($attachmentsData)) {
     $media = array_reverse($attachmentsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = trim(base64_decode($media[$i]), ";");
       array_push($attachments, $media[$i]);
      }
     }
    } if(!empty($blogsData)) {
     $media = array_reverse($blogsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = base64_decode($media[$i]);
       array_push($blogs, $media[$i]);
      }
     }
    } if(!empty($blogPostsData)) {
     $media = array_reverse($blogPostsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = base64_decode($media[$i]);
       array_push($blogPosts, $media[$i]);
      }
     }
    } if(!empty($coverPhotoData)) {
     $coverPhoto = base64_decode($media[$i]);
    } if(!empty($forumsData)) {
     $media = array_reverse($forumsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = base64_decode($media[$i]);
       array_push($forums, $media[$i]);
      }
     }
    } if(!empty($forumPostsData)) {
     $media = array_reverse($forumPostsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = base64_decode($media[$i]);
       array_push($forumPosts, $media[$i]);
      }
     }
    } if(!empty($pollsData)) {
     $media = array_reverse($pollsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = base64_decode($media[$i]);
       array_push($polls, $media[$i]);
      }
     }
    } if(!empty($productsData)) {
     $media = array_reverse($productsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = base64_decode($media[$i]);
       array_push($products, $media[$i]);
      }
     }
    } if(!empty($shopsData)) {
     $media = array_reverse($shopsData);
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       $media[$i] = base64_decode($media[$i]);
       array_push($shops, $media[$i]);
      }
     }
    } if($new == 1) {
     $mainstream = $this->core->Data("Get", ["app", "mainstream"]) ?? [];
     array_push($mainstream, $id);
     #$this->core->Data("Save", ["app", "mainstream", $mainstream]);
     $update = [
      "From" => $you,
      "To" => $to,
      "UpdateID" => $id
     ];
     if(!empty($to) && $to != $you) {
      $stream = $this->core->Data("Get", ["stream", md5($to)]) ?? [];
      $stream[$created] = $update;
      #$this->core->Data("Save", ["stream", md5($to), $stream]);
     }
     $stream = $this->core->Data("Get", ["stream", md5($you)]) ?? [];
     $stream[$created] = $update;
     #$this->core->Data("Save", ["stream", md5($you), $stream]);
    }
    $update = [
     "Attachments" => $attachments,
     "Blogs" => $blogs,
     "BlogPosts" => $blogPosts,
     "Body" => $this->core->PlainText([
      "Data" => $data["Body"],
      "HTMLEncode" => 1
     ]),
     "CoverPhoto" => end($coverPhoto),
     "Created" => $created,
     "Forums" => $forums,
     "ForumPosts" => $forumPosts,
     "From" => $you,
     "ID" => $id,
     "Illegal" => $illegal,
     "Modified" => $now,
     "Notes" => $notes,
     "NSFW" => $nsfw,
     "PassPhrase" => $passPhrase,
     "Privacy" => $privacy,
     "Products" => $products,
     "Purge" => $purge,
     "Shops" => $shops,
     "To" => $to
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
    #$sql->execute();
    $y["Activity"]["LastActivity"] = $this->core->timestamp;
    $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
    #$this->core->Data("Save", ["su", $update["ID"], $update]);
    #$this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "The Status Update was $actionTaken.",
     "Header" => "Done",
     "Scrollable" => json_encode($update, true)
    ];
    if($new == 1) {
     #$this->core->Statistic("New Status Update");
    } else {
     #$this->core->Statistic("Edit Status Update");
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    #"Success" => "CloseCard"
   ]);
  }
  function Purge(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Status Update Identifier is missing."
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
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $newStream = [];
    $stream = $this->core->Data("Get", ["stream", md5($you)]) ?? [];
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
    $r = $this->core->Element([
     "p", "The Update and dependencies were marked for purging.",
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
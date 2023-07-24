<?php
 Class ForumPost extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["FID", "ID", "new"]);
   $forumID = $data["FID"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif((!empty($forumID) && !empty($id)) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".ForumPost$id",
     "data-processor" => base64_encode("v=".base64_encode("ForumPost:Save"))
    ]]);
    $att = "";
    $id = ($new == 1) ? md5($you."_Post_".$this->system->timestamp) : $id;
    $dv = base64_encode("Common:DesignView");
    $em = base64_encode("LiveView:EditorMossaic");
    $sc = base64_encode("Search:Containers");
    $post = $this->system->Data("Get", ["post", $id]) ?? [];
    $post = $this->system->FixMissing($post, ["Body", "Title"]);
    $body = $post["Body"] ?? "";
    $header = ($new == 1) ? "New Post" : "Edit Post";
    if(!empty($post["Attachments"])) {
     $att = base64_encode(implode(";", $post["Attachments"]));
    }
    $at2 = base64_encode("All done! Feel free to close this card.");
    $atinput = ".ForumPost$id-ATTF";
    $at3 = base64_encode("Attach to your Post.:$atinput");
    $atinput = "$atinput .rATT";
    $designViewEditor = "UIE$id";
    $nsfw = $post["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $post["Privacy"] ?? $y["Privacy"]["Posts"];
    $title = $post["Title"] ?? "";
    $r = $this->system->Change([[
     "[ForumPost.AdditionalContent]" => $this->system->Change([
      [
       "[Extras.ContentType]" => "Forum Post",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=N/A&Added=N/A&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => $designViewEditor,
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=$id")
      ], $this->system->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[ForumPost.Header]" => $header,
     "[ForumPost.ID]" => $id,
     "[ForumPost.Inputs]" => $this->system->RenderInputs([
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
        "class" => "rATT rATT$id-ATTF",
        "data-a" => "#ATTL$id-ATTF",
        "data-u" => base64_encode("v=$em&AddTo=$atinput&ID="),
        "name" => "rATTF",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditForumPost$id-ATTF"
       ],
       "Type" => "Text",
       "Value" => $att
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
       "Value" => $post["Title"]
      ],
      [
       "Attributes" => [
        "class" => "$designViewEditor Body Xdecode req",
        "id" => "EditForumPostBody$id",
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
       "Value" => $this->system->PlainText([
        "Data" => $post["Body"],
        "Decode" => 1
       ])
      ]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->system->RenderVisibilityFilter([
      "Value" => $privacy
     ])
    ], $this->system->Page("cabbfc915c2edd4d4cba2835fe68b1cc")]);
    $r = [
     "Action" => $action,
     "Front" => $r
    ];
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
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
   $now = $this->system->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($fid) && !empty($id)) {
    $active = 0;
    $admin = 0;
    $forum = $this->system->Data("Get", ["pf", $fid]) ?? [];
    $post = $this->system->Data("Get", ["post", $id]) ?? [];
    $r = [
     "Body" => "The requested Forum Post could not be found."
    ];
    $ck = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
    $ck2 = ($active == 1 || $forum["Type"] == "Public") ? 1 : 0;
    $cms = $this->system->Data("Get", ["cms", md5($post["From"])]) ?? [];
    $ck3 = $this->system->CheckPrivacy([
     "Contacts" => $cms["Contacts"],
     "Privacy" => $post["Privacy"],
     "UN" => $post["From"],
     "Y" => $you
    ]);
    $manifest = $this->system->Data("Get", ["pfmanifest", $fid]) ?? [];
    foreach($manifest as $member => $role) {
     if($active == 0 && $member == $you) {
      $active++;
      if($role == "Admin") {
       $admin++;
      }
     }
    }
    $op = ($ck == 1) ? $y : $this->system->Member($post["From"]);
    $privacy = $post["Privacy"] ?? $op["Privacy"]["Posts"];
    if($ck == 1 || $ck2 == 1) {
     $accessCode = "Accepted";
     $bl = $this->system->CheckBlocked([$y, "Status Updates", $id]);
     $blc = ($bl == 0) ? "B" : "U";
     $blt = ($bl == 0) ? "Block" : "Unblock";
     $con = base64_encode("Conversation:Home");
     $actions = ($post["From"] != $you) ? $this->system->Element([
      "button", "$blt this Post", [
       "class" => "BLK InnerMargin",
       "data-cmd" => base64_encode($blc),
       "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($post["ID"])."&list=".base64_encode("Forum Posts")."&BC=")
      ]
     ]) : "";
     $actions = ($this->system->ID != $you) ? $actions : "";
     if($ck == 1) {
      $actions .= $this->system->Element([
       "button", "Delete", [
        "class" => "InnerMargin dBO",
        "data-type" => "v=".base64_encode("Authentication:DeleteForumPost")."&FID=$fid&ID=$id"
       ]
      ]);
      $actions .= ($admin == 1 || $ck == 1) ? $this->system->Element([
       "button", "Edit", [
        "class" => "InnerMargin dB2O",
        "data-type" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$fid&ID=$id")
       ]
      ]) : "";
      $actions .= ($forum["Type"] == "Public") ? $this->system->Element([
       "button", "Share", [
        "class" => "InnerMargin dB2O",
        "data-type" => base64_encode("v=".base64_encode("ForumPost:Share")."&ID=".base64_encode($fid."-".$id))
       ]
      ]) : "";
     }
     $att = (!empty($post["Attachments"])) ? $this->view(base64_encode("LiveView:InlineMossaic"), ["Data" => [
      "ID" => base64_encode(implode(";", $post["Attachments"])),
      "Type" => base64_encode("DLC")
     ]]) : "";
     $op = ($post["From"] == $you) ? $y : $this->system->Member($post["From"]);
     $display = ($op["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
     $memberRole = $manifest[$op["Login"]["Username"]];
     $modified = $post["ModifiedBy"] ?? [];
     if(empty($modified)) {
      $modified = "";
     } else {
      $_Member = end($modified);
      $_Time = $this->system->TimeAgo(array_key_last($modified));
      $modified = " &bull; Modified ".$_Time." by ".$_Member;
      $modified = $this->system->Element(["em", $modified]);
     }
     $votes = ($op["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $votes = base64_encode("v=$votes&ID=$id&Type=3");
     $r = $this->system->Change([[
      "[ForumPost.Actions]" => $actions,
      "[ForumPost.Attachments]" => $att,
      "[ForumPost.Body]" => $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $post["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[ForumPost.Created]" => $this->system->TimeAgo($post["Created"]),
      "[ForumPost.Conversation]" => $this->system->Change([[
       "[Conversation.CRID]" => $id,
       "[Conversation.CRIDE]" => base64_encode($id),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=$con&CRID=[CRID]&LVL=[LVL]")
      ], $this->system->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[ForumPost.ID]" => $id,
      "[ForumPost.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("ForumPost;$id")),
      "[ForumPost.MemberRole]" => $memberRole,
      "[ForumPost.Modified]" => $modified,
      "[ForumPost.OriginalPoster]" => $display,
      "[ForumPost.ProfilePicture]" => $this->system->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
      "[ForumPost.Title]" => $post["Title"],
      "[ForumPost.Share]" => base64_encode("v=".base64_encode("ForumPost:Share")."&ID=".base64_encode($id)),
      "[ForumPost.Votes]" => $votes
     ], $this->system->Page("d2be822502dd9de5e8b373ca25998c37")]);
     $r = [
      "Front" => $r
     ];
    }
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["FID", "ID"]);
   $fid = $data["FID"];
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Forum Post Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif((!empty($fid) && !empty($id)) || $new == 1) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $att = [];
    $forum = $this->system->Data("Get", ["pf", $fid]) ?? [];
    $i = 0;
    $now = $this->system->timestamp;
    $post = $this->system->Data("Get", ["post", $id]) ?? [];
    $posts = $forum["Posts"] ?? [];
    foreach($posts as $key => $value) {
     if($i == 0 && $id == $value) {
      $i++;
     }
    } if(!empty($data["rATTF"])) {
     $dlc = array_reverse(explode(";", base64_decode($data["rATTF"])));
     foreach($dlc as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        array_push($att, base64_encode($f[0]."-".$f[1]));
       }
      }
     }
    } if($i == 0) {
     array_push($posts, $id);
     $forum["Posts"] = $posts;
     $y["Activity"]["LastActive"] = $now;
     $y["Points"] = $y["Points"] + $this->system->core["PTS"]["NewContent"];
     $this->system->Data("Save", ["pf", $fid, $forum]);
     $this->system->Data("Save", ["mbr", md5($you), $y]);
    }
    $created = $post["Created"] ?? $now;
    $from = $post["From"] ?? $y["Login"]["Username"];
    $illegal = $post["Illegal"] ?? 0;
    $modifiedBy = $post["ModifiedBy"] ?? [];
    $modifiedBy[$now] = $you;
    $r = [
     "Body" => "Your post has been $actionTaken.",
     "Header" => "Done"
    ];
    $this->system->Data("Save", ["post", $id, [
     "Attachments" => $att,
     "Body" => $this->system->PlainText([
      "Data" => $data["Body"],
      "HTMLEncode" => 1
     ]),
     "Created" => $created,
     "ForumID" => $forum["ID"],
     "From" => $from,
     "ID" => $id,
     "Illegal" => $illegal,
     "Modified" => $now,
     "ModifiedBy" => $modifiedBy,
     "NSFW" => $data["nsfw"],
     "Privacy" => $data["pri"],
     "Title" => $data["Title"]
    ]]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["FID", "ID", "PIN"]);
   $fid = $data["FID"];
   $id = $data["ID"];
   $r = [
    "Body" => "The Post Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->system->ID == $y["Login"]["Username"]) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = explode("-", base64_decode($id));
    $fid = $id[0];
    $id = $id[1];
    $forum = $this->system->Data("Get", ["pf", $fid]) ?? [];
    $newPosts = [];
    $posts = $forum["Posts"] ?? [];
    foreach($posts as $key => $value) {
     if($id != $value) {
      $newPosts[$key] = $value;
     }
    }
    $forum["Posts"] = $newPosts;
    $this->view(base64_encode("Conversation:SaveDelete"), [
     "Data" => ["ID" => $id]
    ]);
    $this->system->Data("Purge", ["local", $id]);
    $this->system->Data("Purge", ["post", $id]);
    $this->system->Data("Purge", ["react", $id]);
    $this->system->Data("Save", ["pf", $fid, $forum]);
    $r = [
     "Body" => "The post was deleted.",
     "Header" => "Done"
    ];
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function Share(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Share Sheet Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id)) {
    $accessCode = "Accepted";$id = base64_decode($id);
    $post = explode("-", $id);
    $post = $this->system->Data("Get", ["post", $post[1]]) ?? [];
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out this Forum Post!"
     ]).$this->system->Element([
      "div", "[ForumPost:$id]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=ForumPost",
     "[Share.ContentID]" => "Forum Post",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => "Forum Post"
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
    $r = [
     "Front" => $r
    ];
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class ForumPost extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["FID", "ID", "new"]);
   $forumID = $data["FID"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
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
    $att = "";
    $id = ($new == 1) ? md5($you."_Post_".$this->core->timestamp) : $id;
    $dv = base64_encode("Common:DesignView");
    $em = base64_encode("LiveView:EditorMossaic");
    $sc = base64_encode("Search:Containers");
    $post = $this->core->Data("Get", ["post", $id]) ?? [];
    $post = $this->core->FixMissing($post, ["Body", "Title"]);
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
    $r = $this->core->Change([[
     "[ForumPost.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => "Forum Post",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=N/A&Added=N/A&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => $designViewEditor,
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[ForumPost.Header]" => $header,
     "[ForumPost.ID]" => $id,
     "[ForumPost.Inputs]" => $this->core->RenderInputs([
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
       "Value" => $this->core->PlainText([
        "Data" => $post["Body"],
        "Decode" => 1
       ])
      ]
     ]).$this->core->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->core->RenderVisibilityFilter([
      "Value" => $privacy
     ])
    ], $this->core->Page("cabbfc915c2edd4d4cba2835fe68b1cc")]);
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
    $active = 0;
    $admin = 0;
    $forum = $this->core->Data("Get", ["pf", $fid]) ?? [];
    $post = $this->core->Data("Get", ["post", $id]) ?? [];
    $r = [
     "Body" => "The requested Forum Post could not be found."
    ];
    $ck = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
    $ck2 = ($active == 1 || $forum["Type"] == "Public") ? 1 : 0;
    $cms = $this->core->Data("Get", ["cms", md5($post["From"])]) ?? [];
    $ck3 = $this->core->CheckPrivacy([
     "Contacts" => $cms["Contacts"],
     "Privacy" => $post["Privacy"],
     "UN" => $post["From"],
     "Y" => $you
    ]);
    $manifest = $this->core->Data("Get", ["pfmanifest", $fid]) ?? [];
    foreach($manifest as $member => $role) {
     if($active == 0 && $member == $you) {
      $active++;
      if($role == "Admin") {
       $admin++;
      }
     }
    }
    $op = ($ck == 1) ? $y : $this->core->Member($post["From"]);
    $privacy = $post["Privacy"] ?? $op["Privacy"]["Posts"];
    if($ck == 1 || $ck2 == 1) {
     $accessCode = "Accepted";
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $id]);
     $blc = ($bl == 0) ? "B" : "U";
     $blt = ($bl == 0) ? "Block" : "Unblock";
     $con = base64_encode("Conversation:Home");
     $actions = ($post["From"] != $you) ? $this->core->Element([
      "button", "$blt this Post", [
       "class" => "BLK InnerMargin",
       "data-cmd" => base64_encode($blc),
       "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($post["ID"])."&list=".base64_encode("Forum Posts")."&BC=")
      ]
     ]) : "";
     $actions = ($this->core->ID != $you) ? $actions : "";
     if($ck == 1) {
      $actions .= $this->core->Element([
       "button", "Delete", [
        "class" => "InnerMargin dBO",
        "data-type" => "v=".base64_encode("Authentication:DeleteForumPost")."&FID=$fid&ID=$id"
       ]
      ]);
      $actions .= ($admin == 1 || $ck == 1) ? $this->core->Element([
       "button", "Edit", [
        "class" => "InnerMargin dB2O",
        "data-type" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$fid&ID=$id")
       ]
      ]) : "";
      $actions .= ($forum["Type"] == "Public") ? $this->core->Element([
       "button", "Share", [
        "class" => "InnerMargin OpenCard",
        "data-view" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode("$fid-$id")."&Type=".base64_encode("ForumPost")."&Username=".base64_encode($post["From"]))
       ]
      ]) : "";
     }
     $att = (!empty($post["Attachments"])) ? $this->view(base64_encode("LiveView:InlineMossaic"), ["Data" => [
      "ID" => base64_encode(implode(";", $post["Attachments"])),
      "Type" => base64_encode("DLC")
     ]]) : "";
     $op = ($post["From"] == $you) ? $y : $this->core->Member($post["From"]);
     $display = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
     $memberRole = $manifest[$op["Login"]["Username"]];
     $modified = $post["ModifiedBy"] ?? [];
     if(empty($modified)) {
      $modified = "";
     } else {
      $_Member = end($modified);
      $_Time = $this->core->TimeAgo(array_key_last($modified));
      $modified = " &bull; Modified ".$_Time." by ".$_Member;
      $modified = $this->core->Element(["em", $modified]);
     }
     $votes = ($op["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $votes = base64_encode("v=$votes&ID=$id&Type=3");
     $r = $this->core->Change([[
      "[ForumPost.Actions]" => $actions,
      "[ForumPost.Attachments]" => $att,
      "[ForumPost.Body]" => $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $post["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[ForumPost.Created]" => $this->core->TimeAgo($post["Created"]),
      "[ForumPost.Conversation]" => $this->core->Change([[
       "[Conversation.CRID]" => $id,
       "[Conversation.CRIDE]" => base64_encode($id),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=$con&CRID=[CRID]&LVL=[LVL]")
      ], $this->core->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[ForumPost.ID]" => $id,
      "[ForumPost.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("ForumPost;$id")),
      "[ForumPost.MemberRole]" => $memberRole,
      "[ForumPost.Modified]" => $modified,
      "[ForumPost.OriginalPoster]" => $display,
      "[ForumPost.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
      "[ForumPost.Title]" => $post["Title"],
      "[ForumPost.Share]" => base64_encode("v=".base64_encode("ForumPost:Share")."&ID=".base64_encode($id)),
      "[ForumPost.Votes]" => $votes
     ], $this->core->Page("d2be822502dd9de5e8b373ca25998c37")]);
     $r = [
      "Front" => $r
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["FID", "ID"]);
   $fid = $data["FID"];
   $id = $data["ID"];
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
   } elseif((!empty($fid) && !empty($id)) || $new == 1) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $att = [];
    $forum = $this->core->Data("Get", ["pf", $fid]) ?? [];
    $i = 0;
    $now = $this->core->timestamp;
    $post = $this->core->Data("Get", ["post", $id]) ?? [];
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
     $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
     $this->core->Data("Save", ["pf", $fid, $forum]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
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
    $this->core->Data("Save", ["post", $id, [
     "Attachments" => $att,
     "Body" => $this->core->PlainText([
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
   return $this->core->JSONResponse([
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
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["FID", "ID", "PIN"]);
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
   } elseif($this->core->ID == $y["Login"]["Username"]) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = explode("-", base64_decode($id));
    $fid = $id[0];
    $id = $id[1];
    $forum = $this->core->Data("Get", ["pf", $fid]) ?? [];
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
    $this->core->Data("Purge", ["local", $id]);
    $this->core->Data("Purge", ["post", $id]);
    $this->core->Data("Purge", ["votes", $id]);
    $this->core->Data("Save", ["pf", $fid, $forum]);
    $r = [
     "Body" => "The post was deleted.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
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
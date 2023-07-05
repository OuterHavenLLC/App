<?php
 Class ForumPost extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["FID", "ID", "new"]);
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Forum Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $fid = $data["FID"];
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif((!empty($fid) && !empty($id)) || $new == 1) {
    $action = ($new == 1) ? "Post" : "Update";
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
    $additionalContent = $this->system->Change([
     [
      "[UIV.IN]" => $designViewEditor,
      "[UIV.OUT]" => "UIV$id",
      "[UIV.U]" => base64_encode("v=$dv&DV=")
     ], $this->system->Page("7780dcde754b127656519b6288dffadc")
    ]).$this->system->Change([
     [
      "[XFS.Files]" => base64_encode("v=$sc&st=XFS&AddTo=$at3&Added=$at2&UN=".$you),
      "[XFS.ID]" => $id
     ], $this->system->Page("8356860c249e93367a750f3b4398e493")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $r = $this->system->Change([[
     "[ForumPost.AdditionalContent]" => $additionalContent,
     "[ForumPost.Attachments]" => $att,
     "[ForumPost.Attachments.LiveView]" => base64_encode("v=$em&AddTo=$atinput&ID="),
     "[ForumPost.Body]" => $this->system->WYSIWYG([
      "UN" => $you,
      "Body" => $this->system->PlainText(["Data" => $body]),
      "adm" => 1,
      "opt" => [
       "id" => "XSUBody",
       "class" => "$designViewEditor Body Xdecode req",
       "name" => "Body",
       "placeholder" => "Body",
       "rows" => 20
      ]
     ]),
     "[ForumPost.ForumID]" => $fid,
     "[ForumPost.Header]" => $header,
     "[ForumPost.ID]" => $id,
     "[ForumPost.New]" => $new,
     "[ForumPost.Options.NSFW]" => $this->system->Select("nsfw", "req v2 v2w", $nsfw),
     "[ForumPost.Options.Privacy]" => $this->system->Select("Privacy", "req v2 v2w", $privacy),
     "[ForumPost.Title]" => $title,
     "[UIV.IN]" => "UIE$id"
    ], $this->system->Page("cabbfc915c2edd4d4cba2835fe68b1cc")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".ForumPost$id",
     "data-processor" => base64_encode("v=".base64_encode("ForumPost:Save"))
    ]]);
   }
   return [
    "Action" => $button,
    "Front" => $r
   ];
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $fr = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Forum or Post Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $frbtn = "";
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
    $fr = $this->system->Change([[
     "[Error.Header]" => "Not Found",
     "[Error.Message]" => "The requested Forum Post could not be found."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
    $ck = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
    $ck2 = ($active == 1 || $forum["Type"] == "Public") ? 1 : 0;
    $cms = $this->system->Data("Get", ["cms", md5($post["From"])]) ?? [];
    $ck3 = $this->system->CheckPrivacy([
     "Contacts" => $cms["Contacts"],
     "Privacy" => $privacy,
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
     $fr = $this->system->Change([[
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
    }
   }
   return [
    "Front" => $fr
   ];
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
     "Body" => ["p", "The PINs do not match."
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
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $ec = "Denied";
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($id);
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
   }
   return [
    "Front" => $r
   ];
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
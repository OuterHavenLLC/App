<?php
 Class ForumPost extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
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
    $attachments = "";
    $id = ($new == 1) ? md5($you."_Post_".$this->core->timestamp) : $id;
    $dv = base64_encode("Common:DesignView");
    $em = base64_encode("LiveView:EditorMossaic");
    $sc = base64_encode("Search:Containers");
    $post = $this->core->Data("Get", ["post", $id]) ?? [];
    $post = $this->core->FixMissing($post, ["Body", "Title"]);
    $body = $post["Body"] ?? "";
    $header = ($new == 1) ? "New Post" : "Edit Post";
    if(!empty($post["Attachments"])) {
     $attachments = base64_encode(implode(";", $post["Attachments"]));
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
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=NA&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".base64_encode($you)),
       "[Extras.DesignView.Origin]" => $designViewEditor,
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at3&Added=$at2&UN=".base64_encode($you)),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Extension("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[ForumPost.Attachments]" => $attachments,
     "[ForumPost.Attachments.LiveView]" => base64_encode("v=$em&AddTo=$atinput&ID="),
     "[ForumPost.Body]" => base64_encode($this->core->PlainText([
      "Data" => $post["Body"]
     ])),
     "[ForumPost.DesignView]" => $designViewEditor,
     "[ForumPost.Header]" => $header,
     "[ForumPost.ForumID]" => $forumID,
     "[ForumPost.ID]" => $id,
     "[ForumPost.New]" => $new,
     "[ForumPost.Title]" => base64_encode($title),
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
     "Forum" => $fid,
     "ID" => base64_encode("ForumPost;$id")
    ]);
    $active = 0;
    $admin = 0;
    $forum = $this->core->Data("Get", ["pf", $fid]) ?? [];
    $post = $_ForumPost["DataModel"];
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
    $options = $_ForumPost["ListItem"]["Options"];
    $privacy = $post["Privacy"] ?? $op["Privacy"]["Posts"];
    if($ck == 1 || $ck2 == 1) {
     $accessCode = "Accepted";
     $blockCommand = ($bl == 0) ? "Block" : "Unblock";
     $actions = ($post["From"] != $you) ? $this->core->Element(["button", $blockCommand, [
      "class" => "InnerMargin UpdateButton v2",
      "data-processor" => $options["Block"]
     ]]) : "";
     $actions = ($this->core->ID != $you) ? $actions : "";
     if($ck == 1) {
      $actions .= $this->core->Element(["button", "Delete", [
       "class" => "InnerMargin OpenDialog v2",
       "data-view" => $options["Delete"]
      ]]);
      $actions .= ($admin == 1 || $ck == 1) ? $this->core->Element(["button", "Edit", [
       "class" => "InnerMargin OpenDialog v2",
       "data-view" => $options["Edit"]
      ]]) : "";
      $actions .= ($forum["Type"] == "Public") ? $this->core->Element(["button", "Share", [
       "class" => "InnerMargin OpenCard",
       "data-view" => $options["Share"]
      ]]) : "";
     }
     $op = ($post["From"] == $you) ? $y : $this->core->Member($post["From"]);
     $display = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
     $memberRole = $manifest[$op["Login"]["Username"]];
     $r = $this->core->Change([[
      "[ForumPost.Actions]" => $actions,
      "[ForumPost.Attachments]" => $_ForumPost["ListItem"]["Attachments"],
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
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->core->Extension("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[ForumPost.ID]" => $id,
      "[ForumPost.Illegal]" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("ForumPost;$id")),
      "[ForumPost.MemberRole]" => $memberRole,
      "[ForumPost.Modified]" => $_ForumPost["ListItem"]["Modified"],
      "[ForumPost.OriginalPoster]" => $display,
      "[ForumPost.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
      "[ForumPost.Title]" => $_ForumPost["ListItem"]["Title"],
      "[ForumPost.Share]" => base64_encode("v=".base64_encode("ForumPost:Share")."&ID=".base64_encode($id)),
      "[ForumPost.Vote]" => $options["Vote"]
     ], $this->core->Extension("d2be822502dd9de5e8b373ca25998c37")]);
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
    $attachments = [];
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
        array_push($attachments, base64_encode($f[0]."-".$f[1]));
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
    $post = [
     "Attachments" => $attachments,
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
     "NSFW" => $data["NSFW"],
     "Privacy" => $data["Privacy"],
     "Title" => $data["Title"]
    ];
    $this->core->Data("Save", ["post", $id, $post]);
    $r = [
     "Body" => "Your post has been $actionTaken.",
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
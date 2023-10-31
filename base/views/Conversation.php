<?php
 Class Conversation extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "CommentID",
    "CRID",
    "ID",
    "Level",
    "new"
   ]);
   $new = $data["new"] ?? 0;
   $crid = $data["CRID"];
   $cid = $data["CommentID"];
   $id = $data["ID"];
   $level = $data["Level"] ?? base64_encode(1);
   $save = base64_encode("Conversation:Save");
   $r = [
    "Body" => "The Conversation Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($crid)) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".ConversationEditor$id",
     "data-processor" => base64_encode("v=".base64_encode("Conversation:Save"))
    ]]);
    $attachments = "";
    $cid = (!empty($cid)) ? base64_decode($cid) : $cid;
    $level = (!empty($level)) ? base64_decode($level) : 1;
    $commentType = ($level == 1) ? "Comment" : "Reply";
    $crid = base64_decode($crid);
    $id = (!empty($id)) ? base64_decode($id) : $id;
    $id = ($new == 1) ? md5($you."_CR_".$this->core->timestamp) : $id;
    $c = $this->core->Data("Get", ["conversation", $crid]) ?? [];
    $c = $c[$id] ?? [];
    if(!empty($c["Attachments"])) {
     $attachments = base64_encode(implode(";", $c["Attachments"]));
    }
    $body = $c["Body"] ?? "";
    $body = (!empty($body)) ? base64_decode($body) : $body;
    $at = base64_encode("Added to $commentType!");
    $at2 = base64_encode("Add Downloadable Content to $commentType:.EditComment$id");
    $header = ($new == 1) ? "New $commentType" : "Edit $commentType";
    $nsfw = $c["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $c["Privacy"] ?? $y["Privacy"]["Comments"];
    $r = $this->core->Change([[
     "[Conversation.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => $commentType,
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => "NA",
       "[Extras.DesignView.Destination]" => "NA",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=NA&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Conversation.Attachments]" => $attachments,
     "[Conversation.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
     "[Conversation.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body
     ])),
     "[Conversation.CRID]" => $crid,
     "[Conversation.CommentID]" => $cid,
     "[Conversation.Header]" => $header,
     "[Conversation.ID]" => $id,
     "[Conversation.Level]" => $level,
     "[Conversation.New]" => $new,
     "[Conversation.Visibility.NSFW]" => $nsfw,
     "[Conversation.Visibility.Privacy]" => $privacy
    ], $this->core->Page("0426a7fc6b31e5034b6c2cec489ea638")]);
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
   $data = $this->core->FixMissing($data, [
    "CommentID",
    "CRID"
   ]);
   $cid = $data["CommentID"];
   $crid = $data["CRID"];
   $edit = base64_encode("Conversation:Edit");
   $hide = base64_encode("Conversation:MarkAsHidden");
   $i = 0;
   $l = $data["Level"] ?? base64_encode(1);
   $r = [
    "Body" => "The Conversation Identifier is missing.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($crid)) {
    $accessCode = "Accepted";
    $anon = "Anonymous";
    $cr = "";
    $cid = (!empty($cid)) ? base64_decode($cid) : $cid;
    $crid = (!empty($crid)) ? base64_decode($crid) : $crid;
    $l = base64_decode($l);
    $l = $l ?? 1;
    $c = $this->core->Data("Get", ["conversation", $crid]) ?? [];
    $ch = base64_encode("Conversation:Home");
    $im = base64_encode("LiveView:InlineMossaic");
    $vote = base64_encode("Vote:Containers");
    if($l == 1) {
     $r = $this->core->Change([[
      "[Comment.Editor]" => base64_encode("v=$edit&CRID=".$data["CRID"]."&new=1")
     ], $this->core->Page("97e7d7d9a85b30e10ab51b23623ccee5")]);
     $tpl = $this->core->Page("8938c49b85c52a5429cc8a9f46c14616");
     foreach($c as $k => $v) {
      $t = ($v["From"] == $you) ? $y : $this->core->Member($v["From"]);
      $bl = $this->core->CheckBlocked([$y, "Comments", $k]);
      $cms = $this->core->Data("Get", ["cms", md5($v["From"])]) ?? [];
      $ck = ($v["NSFW"] == 0 || ($y["age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $v["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $ck3 = ($v["Level"] == 1) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1) {
       $dlc = $v["DLC"] ?? "";
       $dlc = (!empty($dlc)) ?  $this->view($in, ["Data" => [
        "ID" => base64_encode(implode(";", $dlc))
       ]]) : "";
       $op = ($v["From"] == $this->core->ID) ? $anon : $v["From"];
       $opt = ($v["From"] == $you && $you != $this->core->ID) ? $this->core->Element([
        "div", $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin OpenDialog",
         "data-view" => base64_encode("v=$edit&CRID=".$data["CRID"]."&ID=".base64_encode($k))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin OpenDialog",
         "data-view" => base64_encode("v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=$l")
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
       $cr .= $this->core->Change([[
        "[Comment.Attachments]" => $dlc,
        "[Comment.Body]" => $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => base64_decode($v["Body"]),
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Comment.Created]" => $this->core->TimeAgo($v["Created"]),
        "[Comment.ID]" => $k,
        "[Comment.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Comment;$crid;$k")),
        "[Comment.Options]" => $opt,
        "[Comment.OriginalPoster]" => $op,
        "[Comment.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
        "[Comment.Replies]" => $this->view($ch, ["Data" => [
         "CommentID" => base64_encode($k),
         "CRID" => base64_encode($crid),
         "Level" => base64_encode(2)
        ]]),
        "[Comment.Votes]" => base64_encode("v=$vote&ID=$k&Type=3")
       ], $tpl]);
       $i++;
      }
     }
     $cr .= $this->core->Change([[
      "[Reply.Editor]" => base64_encode("v=$edit&CRID=".$data["CRID"]."&new=1")
     ], $this->core->Page("5efa423862a163dd55a2785bc7327727")]);
     $r = ($i > 0) ? $cr : $r;
    } elseif($l == 2) {
     # REPLIES
     $t = $this->core->Member($c[$cid]["From"]);
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $r = $this->core->Page("cc3c7b726c1d7f9c50f5f7869513bd80");
     $tpl = $this->core->Page("ccf260c40f8fa63be5686f5ceb2b95b1");
     foreach($c as $k => $v) {
      $t = ($v["From"] == $you) ? $y : $this->core->Member($v["From"]);
      $bl = $this->core->CheckBlocked([$y, "Replies", $k]);
      $cms = $this->core->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $ck = ($cid == $v["CommentID"]) ? 1 : 0;
      $ck2 = ($v["NSFW"] == 0 || ($y["age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck3 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $v["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $ck4 = ($v["Level"] == 2) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) {
       $dlc = $v["DLC"] ?? "";
       $dlc = (!empty($dlc)) ?  $this->view($in, ["Data" => [
        "ID" => base64_encode(implode(";", $dlc))
       ]]) : "";
       $op = ($v["From"] == $this->core->ID) ? $anon : $v["From"];
       $opt = ($v["From"] == $you && $you != $this->core->ID) ? $this->core->Element([
        "div", $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=$edit&CommentID=".base64_encode($v["CommentID"])."&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=".$data["Level"])
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=$l"
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
      $cr .= $this->core->Change([[
       "[Reply.Attachments]" => $dlc,
       "[Reply.Body]" => $this->core->PlainText([
        "BBCodes" => 1,
        "Data" => base64_decode($v["Body"]),
        "Display" => 1,
        "HTMLDecode" => 1
       ]),
       "[Reply.Created]" => $this->core->TimeAgo($v["Created"]),
       "[Reply.ID]" => $k,
       "[Reply.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Comment;$crid;$k")),
       "[Reply.Options]" => $opt,
       "[Reply.OriginalPoster]" => $op,
       "[Reply.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
       "[Reply.Replies]" => $this->view($ch, ["Data" => [
        "CommentID" => base64_encode($k),
        "CRID" => base64_encode($crid),
        "Level" => base64_encode(3)
       ]]),
       "[Reply.Votes]" => base64_encode("v=$vote&ID=$k&Type=3")
      ], $tpl]);
      $i++;
     }
    }
    $r = ($i > 0) ? $cr : $r;
    $r .= $this->core->Change([[
     "[Reply.DisplayName]" => $display,
     "[Reply.Editor]" => base64_encode("v=$edit&new=1&CommentID=".$data["CommentID"]."&CRID=".$data["CRID"]."&Level=".$data["Level"])
    ], $this->core->Page("f6876eb53ff51bf537b1b1848500bdab")]);
   } elseif($l == 3) {
     # REPLIES TO REPLIES
     $t = $this->core->Member($c[$cid]["From"]);
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $r = $this->core->Page("cc3c7b726c1d7f9c50f5f7869513bd80");
     $tpl = $this->core->Page("3847a50cd198853fe31434b6f4e922fd");
     foreach($c as $k => $v) {
      $t = ($v["From"] == $you) ? $y : $this->core->Member($v["From"]);
      $bl = $this->core->CheckBlocked([$y, "Replies", $k]);
      $cms = $this->core->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $ck = ($cid == $v["CommentID"]) ? 1 : 0;
      $ck2 = ($v["NSFW"] == 0 || ($y["age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck3 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $v["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $ck4 = ($v["Level"] == 3) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) {
       $dlc = $v["DLC"] ?? "";
       $dlc = (!empty($dlc)) ?  $this->view($in, ["Data" => [
        "ID" => base64_encode(implode(";", $dlc))
       ]]) : "";
       $op = ($v["From"] == $this->core->ID) ? $anon : $v["From"];
       $opt = ($v["From"] == $you && $you != $this->core->ID) ? $this->core->Element([
        "div", $this->core->Element(["button", "Edit", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=$edit&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=".$data["Level"])
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=$l"
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
       $cr .= $this->core->Change([[
        "[Reply.Attachments]" => $dlc,
        "[Reply.Body]" => $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => base64_decode($v["Body"]),
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Reply.Created]" => $this->core->TimeAgo($v["Created"]),
        "[Reply.ID]" => $k,
        "[Reply.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Comment;$crid;$k")),
        "[Reply.Options]" => $opt,
        "[Reply.OriginalPoster]" => $op,
        "[Reply.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
        "[Reply.Votes]" => base64_encode("v=$vote&ID=$k&Type=3")
       ], $tpl]);
       $i++;
      }
     }
     $r = ($i > 0) ? $cr : $r;
     $r .= $this->core->Change([[
      "[Reply.DisplayName]" => $display,
      "[Reply.Editor]" => base64_encode("v=$edit&new=1&CommentID=".$data["CommentID"]."&CRID=".$data["CRID"]."&Level=".$data["Level"])
     ], $this->core->Page("f6876eb53ff51bf537b1b1848500bdab")]);
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
   $data = $this->core->FixMissing($data, [
    "CommentID",
    "CRID",
    "ID",
    "Level"
   ]);
   $cid = $data["CommentID"];
   $crid = $data["CRID"];
   $id = $data["ID"];
   $level = $data["Level"] ?? 1;
   $new = $data["New"] ?? 0;
   $commentType = ($level == 1) ? "comment" : "reply";
   $r = [
    "Body" => "The Conversation or $commentType Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($crid) && !empty($id)) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $attachments = [];
    $cc = ($level > 1) ? "Comment$cid" : "Conversation$crid";
    $con = $this->core->Data("Get", ["conversation", $crid]) ?? [];
    $created = $con[$id]["Created"] ?? $this->core->timestamp;
    $home = base64_encode("Conversation:Home");
    $illegal = $con[$id]["Illegal"] ?? 0;
    $nsfw = $con[$id]["NSFW"] ?? $y["Privacy"]["NSFW"];
    $nsfw = $data["NSFW"] ?? $nsfw;
    $privacy = $con[$id]["Privacy"] ?? $y["Privacy"]["Comments"];
    $privacy = $data["Privacy"] ?? $privacy;
    if(!empty($data["rATTDLC"])) {
     $dlc = array_reverse(explode(";", base64_decode($data["rATTDLC"])));
     foreach($dlc as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        array_push($attachments, base64_encode($f[0]."-".$f[1]));
       }
      }
     }
    }
    $attachments = array_unique($attachments);
    $con[$id] = [
     "Attachments" => $attachments,
     "Body" => $this->core->PlainText([
      "Data" => $data["Body"],
      "Encode" => 1,
      "HTMLEncode" => 1
     ]),
     "CommentID" => $cid,
     "Created" => $created,
     "From" => $you,
     "Illegal" => $illegal,
     "Level" => $level,
     "Modified" => $this->core->timestamp,
     "NSFW" => $nsfw,
     "Privacy" => $privacy
    ];
    $r = [
     "Body" => "Your $commentType was $actionTaken.",
     "Header" => "Done"
    ];
    $this->core->Data("Save", ["conversation", $crid, $con]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function MarkAsHidden(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "CRID",
    "ID",
    "Level"
   ]);
   $crid = $data["CRID"];
   $id = $data["ID"];
   $l = $data["Level"];
   $cr = ($l == 1) ? "comment" : "reply";
   $r = [
    "Body" => "The Conversation or $cr Identifier are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($crid) && !empty($id)) {
    $accessCode = "Accepted";
    $conversation = $this->core->Data("Get", [
     "conversation",
     $crid
    ]) ?? [];
    $comment = $conversation[$id] ?? [];
    $comment["Privacy"] = md5("Private");
    $conversation[$id] = $comment;
    $r = [
     "Body" => "The $cr is hidden, only you can see it.",
     "Header" => "Done"
    ];
    $this->core->Data("Save", ["conversation", $crid, $conversation]);
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Conversation Identifier is missing."
   ];
   if(!empty($id)) {
    $conversation = $this->core->Data("Get", [
     "conversation",
     $id
    ]) ?? [];
    foreach($conversation as $key => $value) {
     $this->core->Data("Purge", ["local", $key]);
     $this->core->Data("Purge", ["votes", $key]);
    }
    $this->core->Data("Purge", ["conversation", $id]);
    $r = [
     "Body" => "The Conversation was deleted.",
     "Header" => "Done"
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
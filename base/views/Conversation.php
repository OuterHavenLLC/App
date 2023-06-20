<?php
 Class Conversation extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "CommentID",
    "CRID",
    "ID",
    "Level",
    "new"
   ]);
   $button = "";
   $new = $data["new"] ?? 0;
   $crid = $data["CRID"];
   $cid = $data["CommentID"];
   $id = $data["ID"];
   $level = $data["Level"] ?? base64_encode(1);
   $save = base64_encode("Conversation:Save");
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Conversation Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($crid)) {
    $action = ($new == 1) ? "Post" : "Update";
    $attachments = "";
    $cid = (!empty($cid)) ? base64_decode($cid) : $cid;
    $level = (!empty($level)) ? base64_decode($level) : 1;
    $conmentType = ($level == 1) ? "Comment" : "Reply";
    $crid = base64_decode($crid);
    $id = (!empty($id)) ? base64_decode($id) : $id;
    $id = ($new == 1) ? md5($you."_CR_".$this->system->timestamp) : $id;
    $c = $this->system->Data("Get", ["conversation", $crid]) ?? [];
    $c = $c[$id] ?? [];
    if(!empty($c["Attachments"])) {
     $attachments = base64_encode(implode(";", $c["Attachments"]));
    }
    $body = $c["Body"] ?? "";
    $body = (!empty($body)) ? base64_decode($body) : $body;
    $at = base64_encode("Added to $conmentType!");
    $at2 = base64_encode("Add Downloadable Content to $conmentType:.EditComment$id");
    $header = ($new == 1) ? "New $conmentType" : "Edit $conmentType";
    $nsfw = $c["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $c["Privacy"] ?? $y["Privacy"]["Comments"];
    $additionalContent = $this->system->Change([[
     "[DLC.ContentType]" => $conmentType,
     "[DLC.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at2&Added=$at&UN=$you"),
     "[DLC.ID]" => $id
    ], $this->system->Page("47470fec24054847fc1232df998eafbd")]);
    $r = $this->system->Change([[
     "[Conversation.AdditionalContent]" => $additionalContent,
     "[Conversation.Header]" => $header,
     "[Conversation.ID]" => $id,
     "[Conversation.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTDLC",
        "data-a" => "#ATTL$id-ATTDLC",
        "data-u" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
        "name" => "rATTDLC",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditComment$id-ATTF"
       ],
       "Type" => "Text",
       "Value" => $attachments
      ],
      [
       "Attributes" => [
        "name" => "CommentID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $cid
      ],
      [
       "Attributes" => [
        "name" => "CRID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $crid
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
        "name" => "Level",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $level
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
        "class" => "Body Xdecode req",
        "id" => "EditPageBody$id",
        "name" => "Body",
        "placeholder" => "Say something..."
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
        "Data" => $body,
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
    ], $this->system->Page("0426a7fc6b31e5034b6c2cec489ea638")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".ConversationEditor$id",
     "data-processor" => base64_encode("v=".base64_encode("Conversation:Save"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "CommentID",
    "CRID"
   ]);
   $cid = $data["CommentID"];
   $crid = $data["CRID"];
   $edit = base64_encode("Conversation:Edit");
   $hide = base64_encode("Conversation:MarkAsHidden");
   $i = 0;
   $l = $data["Level"] ?? base64_encode(1);
   $r = $this->system->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Conversation Identifier is missing."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   if(!empty($crid)) {
    $anon = "Anonymous";
    $cr = "";
    $cid = (!empty($cid)) ? base64_decode($cid) : $cid;
    $crid = (!empty($crid)) ? base64_decode($crid) : $crid;
    $l = base64_decode($l);
    $c = $this->system->Data("Get", ["conversation", $crid]) ?? [];
    $ch = base64_encode("Conversation:Home");
    $im = base64_encode("LiveView:InlineMossaic");
    $vote = base64_encode("Vote:Containers");
    if($l == 1) {
     $r = $this->system->Change([[
      "[Comment.Editor]" => base64_encode("v=$edit&CRID=".$data["CRID"]."&new=1")
     ], $this->system->Page("97e7d7d9a85b30e10ab51b23623ccee5")]);
     $tpl = $this->system->Page("8938c49b85c52a5429cc8a9f46c14616");
     foreach($c as $k => $v) {
      $t = ($v["From"] == $y["Login"]["Username"]) ? $y : $this->system->Member($v["From"]);
      $bl = $this->system->CheckBlocked([$y, "Comments", $k]);
      $cms = $this->system->Data("Get", ["cms", md5($v["From"])]) ?? [];
      $ck = ($v["NSFW"] == 0 || ($y["age"] >= $this->system->core["minAge"])) ? 1 : 0;
      $ck2 = $this->system->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $v["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $y["Login"]["Username"]
      ]);
      $ck3 = ($v["Level"] == 1) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1) {
       $dlc = $v["DLC"] ?? "";
       $dlc = (!empty($dlc)) ?  $this->view($in, ["Data" => [
        "ID" => base64_encode(implode(";", $dlc))
       ]]) : "";
       $op = ($v["From"] == $this->system->ID) ? $anon : $v["From"];
       $opt = ($v["From"] == $y["Login"]["Username"] && $y["Login"]["Username"] != $this->system->ID) ? $this->system->Element([
        "div", $this->system->Element(["button", "Edit", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=$edit&CRID=".$data["CRID"]."&ID=".base64_encode($k))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->system->Element([
        "div", $this->system->Element(["button", "Hide", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=$l"
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
       $cr .= $this->system->Change([[
        "[Comment.Attachments]" => $dlc,
        "[Comment.Body]" => $this->system->PlainText([
         "BBCodes" => 1,
         "Data" => base64_decode($v["Body"]),
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Comment.Created]" => $this->system->TimeAgo($v["Created"]),
        "[Comment.ID]" => $k,
        "[Comment.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Comment;$crid;$k")),
        "[Comment.Options]" => $opt,
        "[Comment.OriginalPoster]" => $op,
        "[Comment.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
        "[Comment.Reactions]" => $this->view($vote, ["Data" => [
         "ID" => $k,
         "Type" => 3
        ]]),
        "[Comment.Replies]" => $this->view($ch, ["Data" => [
         "CommentID" => base64_encode($k),
         "CRID" => base64_encode($crid),
         "Level" => base64_encode(2)
        ]])
       ], $tpl]);
       $i++;
      }
     }
     $cr .= $this->system->Change([[
      "[Reply.Editor]" => base64_encode("v=$edit&CRID=".$data["CRID"]."&new=1")
     ], $this->system->Page("5efa423862a163dd55a2785bc7327727")]);
     $r = ($i > 0) ? $cr : $r;
    } elseif($l == 2) {
     # REPLIES
     $t = $this->system->Member($c[$cid]["From"]);
     $display = ($t["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $r = $this->system->Page("cc3c7b726c1d7f9c50f5f7869513bd80");
     $tpl = $this->system->Page("ccf260c40f8fa63be5686f5ceb2b95b1");
     foreach($c as $k => $v) {
      $t = ($v["From"] == $y["Login"]["Username"]) ? $y : $this->system->Member($v["From"]);
      $bl = $this->system->CheckBlocked([$y, "Replies", $k]);
      $cms = $this->system->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $ck = ($cid == $v["CommentID"]) ? 1 : 0;
      $ck2 = ($v["NSFW"] == 0 || ($y["age"] >= $this->system->core["minAge"])) ? 1 : 0;
      $ck3 = $this->system->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $v["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $y["Login"]["Username"]
      ]);
      $ck4 = ($v["Level"] == 2) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) {
       $dlc = $v["DLC"] ?? "";
       $dlc = (!empty($dlc)) ?  $this->view($in, ["Data" => [
        "ID" => base64_encode(implode(";", $dlc))
       ]]) : "";
       $op = ($v["From"] == $this->system->ID) ? $anon : $v["From"];
       $opt = ($v["From"] == $y["Login"]["Username"] && $y["Login"]["Username"] != $this->system->ID) ? $this->system->Element([
        "div", $this->system->Element(["button", "Edit", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=$edit&CommentID=".base64_encode($v["CommentID"])."&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=".$data["Level"])
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->system->Element([
        "div", $this->system->Element(["button", "Hide", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=$l"
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
      $cr .= $this->system->Change([[
       "[Reply.Attachments]" => $dlc,
       "[Reply.Body]" => $this->system->PlainText([
        "BBCodes" => 1,
        "Data" => base64_decode($v["Body"]),
        "Display" => 1,
        "HTMLDecode" => 1
       ]),
       "[Reply.Created]" => $this->system->TimeAgo($v["Created"]),
       "[Reply.ID]" => $k,
       "[Reply.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Comment;$crid;$k")),
       "[Reply.Options]" => $opt,
       "[Reply.OriginalPoster]" => $op,
       "[Reply.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
       "[Reply.Reactions]" => $this->view($vote, ["Data" => [
        "ID" => $k,
        "Type" => 3
       ]]),
       "[Reply.Replies]" => $this->view($ch, ["Data" => [
        "CommentID" => base64_encode($k),
        "CRID" => base64_encode($crid),
        "Level" => base64_encode(3)
       ]])
      ], $tpl]);
      $i++;
     }
    }
    $r = ($i > 0) ? $cr : $r;
    $r .= $this->system->Change([[
     "[Reply.DisplayName]" => $display,
     "[Reply.Editor]" => base64_encode("v=$edit&new=1&CommentID=".$data["CommentID"]."&CRID=".$data["CRID"]."&Level=".$data["Level"])
    ], $this->system->Page("f6876eb53ff51bf537b1b1848500bdab")]);
   } elseif($l == 3) {
     # REPLIES TO REPLIES
     $t = $this->system->Member($c[$cid]["From"]);
     $display = ($t["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $r = $this->system->Page("cc3c7b726c1d7f9c50f5f7869513bd80");
     $tpl = $this->system->Page("3847a50cd198853fe31434b6f4e922fd");
     foreach($c as $k => $v) {
      $t = ($v["From"] == $y["Login"]["Username"]) ? $y : $this->system->Member($v["From"]);
      $bl = $this->system->CheckBlocked([$y, "Replies", $k]);
      $cms = $this->system->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $ck = ($cid == $v["CommentID"]) ? 1 : 0;
      $ck2 = ($v["NSFW"] == 0 || ($y["age"] >= $this->system->core["minAge"])) ? 1 : 0;
      $ck3 = $this->system->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $v["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $y["Login"]["Username"]
      ]);
      $ck4 = ($v["Level"] == 3) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) {
       $dlc = $v["DLC"] ?? "";
       $dlc = (!empty($dlc)) ?  $this->view($in, ["Data" => [
        "ID" => base64_encode(implode(";", $dlc))
       ]]) : "";
       $op = ($v["From"] == $this->system->ID) ? $anon : $v["From"];
       $opt = ($v["From"] == $y["Login"]["Username"] && $y["Login"]["Username"] != $this->system->ID) ? $this->system->Element([
        "div", $this->system->Element(["button", "Edit", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=$edit&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=".$data["Level"])
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->system->Element([
        "div", $this->system->Element(["button", "Hide", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&Level=$l"
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
       $cr .= $this->system->Change([[
        "[Reply.Attachments]" => $dlc,
        "[Reply.Body]" => $this->system->PlainText([
         "BBCodes" => 1,
         "Data" => base64_decode($v["Body"]),
         "Display" => 1,
         "HTMLDecode" => 1
        ]),
        "[Reply.Created]" => $this->system->TimeAgo($v["Created"]),
        "[Reply.ID]" => $k,
        "[Reply.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Comment;$crid;$k")),
        "[Reply.Options]" => $opt,
        "[Reply.OriginalPoster]" => $op,
        "[Reply.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
        "[Reply.Reactions]" => $this->view($vote, ["Data" => [
         "ID" => $k,
         "Type" => 3
        ]])
       ], $tpl]);
       $i++;
      }
     }
     $r = ($i > 0) ? $cr : $r;
     $r .= $this->system->Change([[
      "[Reply.DisplayName]" => $display,
      "[Reply.Editor]" => base64_encode("v=$edit&new=1&CommentID=".$data["CommentID"]."&CRID=".$data["CRID"]."&Level=".$data["Level"])
     ], $this->system->Page("f6876eb53ff51bf537b1b1848500bdab")]);
    }
   }
   return $r;
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "CommentID",
    "CRID",
    "ID",
    "Level",
    "new"
   ]);
   $cid = $data["CommentID"];
   $crid = $data["CRID"];
   $id = $data["ID"];
   $level = $data["Level"] ?? 1;
   $new = $data["new"] ?? 0;
   $commentType = ($level == 1) ? "comment" : "reply";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Conversation or $commentType Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($crid) && !empty($id)) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $attachments = [];
    $cc = ($level > 1) ? "Comment$cid" : "Conversation$crid";
    $con = $this->system->Data("Get", ["conversation", $crid]) ?? [];
    $created = $con[$id]["Created"] ?? $this->system->timestamp;
    $home = base64_encode("Conversation:Home");
    $illegal = $con[$id]["Illegal"] ?? 0;
    $nsfw = $con[$id]["NSFW"] ?? $y["Privacy"]["NSFW"];
    $nsfw = $data["nsfw"] ?? $nsfw;
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
     "Body" => $this->system->PlainText([
      "Data" => $data["Body"],
      "Encode" => 1,
      "HTMLEncode" => 1
     ]),
     "CommentID" => $cid,
     "Created" => $created,
     "From" => $you,
     "Illegal" => $illegal,
     "Level" => $level,
     "Modified" => $this->system->timestamp,
     "NSFW" => $nsfw,
     "Privacy" => $privacy
    ];
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "Your $commentType was $actionTaken."
     ]),
     "Header" => "Done"
    ]);
    $this->system->Data("Save", ["conversation", $crid, $con]);
   }
   return $this->system->JSONResponse([
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
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "CRID",
    "ID",
    "Level"
   ]);
   $crid = $data["CRID"];
   $id = $data["ID"];
   $l = $data["Level"];
   $cr = ($l == 1) ? "comment" : "reply";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Conversation or $cr Identifier are missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($crid) && !empty($id)) {
    $conversation = $this->system->Data("Get", ["conversation", $crid]) ?? [];
    $comment = $conversation[$id] ?? [];
    $comment["Privacy"] = md5("Private");
    $conversation[$id] = $comment;
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The $cr is hidden, only you can see it."
     ]),
     "Header" => "Done"
    ]);
    $this->system->Data("Save", ["conversation", $crid, $conversation]);
   }
   return $r;
  }
  function SaveDelete(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   if(!empty($id)) {
    $conversation = $this->system->Data("Get", ["conversation", $id]) ?? [];
    foreach($conversation as $key => $value) {
     $this->system->Data("Purge", ["local", $key]);
     $this->system->Data("Purge", ["react", $key]);
    }
    $this->system->Data("Purge", ["conversation", $id]);
   }
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class Conversation extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "CID",
    "CRID",
    "ID",
    "LVL",
    "new"
   ]);
   $button = "";
   $new = $data["new"] ?? 0;
   $crid = $data["CRID"];
   $cid = $data["CID"];
   $id = $data["ID"];
   $l = $data["LVL"] ?? base64_encode(1);
   $save = base64_encode("Conversation:Save");
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Conversation Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($crid)) {
    $action = ($new == 1) ? "Post" : "Update";
    $cid = (!empty($cid)) ? base64_decode($cid) : $cid;
    $dlc = "";
    $l = (!empty($l)) ? base64_decode($l) : 1;
    $l2 = base64_encode($l);
    $cr = ($l == 1) ? "Comment" : "Reply";
    $crid = (!empty($crid)) ? base64_decode($crid) : $crid;
    $id = (!empty($id)) ? base64_decode($id) : $id;
    $id = ($new == 1) ? md5($y["Login"]["Username"]."_CR_".$this->system->timestamp) : $id;
    $ide = md5($crid);
    $c = $this->system->Data("Get", ["conversation", $crid]) ?? [];
    $c = $c[$id] ?? [];
    $cb = $c["Body"] ?? "";
    $cb = (!empty($cb)) ? base64_decode($cb) : $cb;
    if(!empty($c["DLC"])) {
     $dlc = base64_encode(implode(";", $c["DLC"]));
    }
    $at = base64_encode("Added to $cr!");
    $at2 = base64_encode("Add Downloadable Content to $cr:.ATTDLC$ide");
    $em = base64_encode("LiveView:EditorMossaic");
    $h = ($new == 1) ? "New $cr" : "Edit $cr";
    $lv = base64_encode("v=$em&ID=");
    $nsfw = $c["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $c["Privacy"] ?? $y["Privacy"]["Comments"];
    $sc = base64_encode("Search:Containers");
    $additionalContent = $this->system->Change([[
     "[DLC.ContentType]" => $cr,
     "[DLC.Files]" => base64_encode("v=$sc&st=XFS&AddTo=$at2&Added=$at&UN=".$y["Login"]["Username"]),
     "[DLC.ID]" => md5($crid)
    ], $this->system->Page("47470fec24054847fc1232df998eafbd")]);
    $r = $this->system->Change([[
     "[Conversation.AdditionalContent]" => $additionalContent,
     "[Conversation.Body]" => $cb,
     "[Conversation.CommentID]" => $cid,
     "[Conversation.CRID]" => $crid,
     "[Conversation.ConversationID]" => $crid,
     "[Conversation.DownloadableContent]" => $dlc,
     "[Conversation.DownloadableContent.LiveView]" => $lv,
     "[Conversation.Header]" => $h,
     "[Conversation.ID]" => $id,
     "[Conversation.IDE]" => $ide,
     "[Conversation.Level]" => $l,
     "[Conversation.New]" => $new,
     "[Conversation.NSFW]" => $this->system->Select("nsfw", "req v2w", $nsfw),
     "[Conversation.Privacy]" => $this->system->Select("Privacy", "req v2w", $privacy)
    ], $this->system->Page("0426a7fc6b31e5034b6c2cec489ea638")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton",
     "data-form" => ".ConversationEditor".md5($crid),
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
   $data = $this->system->FixMissing($data, ["CID", "CRID"]);
   $cid = $data["CID"];
   $crid = $data["CRID"];
   $edit = base64_encode("Conversation:Edit");
   $hide = base64_encode("Conversation:MarkAsHidden");
   $i = 0;
   $l = $data["LVL"] ?? base64_encode(1);
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
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&LVL=$l"
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
         "CID" => base64_encode($k),
         "CRID" => base64_encode($crid),
         "LVL" => base64_encode(2)
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
         "data-type" => base64_encode("v=$edit&CID=".base64_encode($v["CommentID"])."&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&LVL=".$data["LVL"])
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->system->Element([
        "div", $this->system->Element(["button", "Hide", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&LVL=$l"
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
        "CID" => base64_encode($k),
        "CRID" => base64_encode($crid),
        "LVL" => base64_encode(3)
       ]])
      ], $tpl]);
      $i++;
     }
    }
    $r = ($i > 0) ? $cr : $r;
    $r .= $this->system->Change([[
     "[Reply.DisplayName]" => $display,
     "[Reply.Editor]" => base64_encode("v=$edit&new=1&CID=".$data["CID"]."&CRID=".$data["CRID"]."&LVL=".$data["LVL"])
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
         "data-type" => base64_encode("v=$edit&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&LVL=".$data["LVL"])
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->system->Element([
        "div", $this->system->Element(["button", "Hide", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=$hide&CRID=".$data["CRID"]."&ID=".base64_encode($k)."&LVL=$l"
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
      "[Reply.Editor]" => base64_encode("v=$edit&new=1&CID=".$data["CID"]."&CRID=".$data["CRID"]."&LVL=".$data["LVL"])
     ], $this->system->Page("f6876eb53ff51bf537b1b1848500bdab")]);
    }
   }
   return $r;
  }
  function Save(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "CID", "CRID", "ID", "LVL", "new"
   ]);
   $ec = "Denied";
   $new = $data["new"] ?? 0;
   $cid = $data["CID"];
   $crid = $data["CRID"];
   $id = $data["ID"];
   $l = $data["LVL"] ?? 1;
   $cr = ($l == 1) ? "comment" : "reply";
   $pu = ($new == 1) ? "posted" : "updated";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Conversation or $cr Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($crid) && !empty($id)) {
    $ch = base64_encode("Conversation:Home");
    $ec = "Accepted";
    $cc = ($l > 1) ? "Comment$cid" : "Conversation$crid";
    $con = $this->system->Data("Get", ["conversation", $crid]) ?? [];
    $created = $con[$id]["Created"] ?? $this->system->timestamp;
    $illegal = $con[$id]["Illegal"] ?? 0;
    $nsfw = $con[$id]["NSFW"] ?? $y["Privacy"]["NSFW"];
    $nsfw = $data["nsfw"] ?? $nsfw;
    $pri = $con[$id]["Privacy"] ?? $y["Privacy"]["Comments"];
    $pri = $data["pri"] ?? $pri;
    $con[$id] = [
     "Body" => base64_encode($data["Body"]),
     "CommentID" => $cid,
     "Created" => $created,
     "From" => $y["Login"]["Username"],
     "Illegal" => $illegal,
     "Level" => $l,
     "Modified" => $this->system->timestamp,
     "NSFW" => $nsfw,
     "Privacy" => $pri
    ];
    $cid = base64_encode($cid);
    $crid = base64_encode($crid);
    $l = base64_encode($l);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "Your $cr was $pu."]),
     "Header" => "Done"
    ]);
    $this->system->Data("Save", ["conversation", $data["CRID"], $con]);
   }
   return $this->system->JSONResponse([$ec, $r]);
  }
  function MarkAsHidden(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["CRID", "ID", "LVL"]);
   $crid = $data["CRID"];
   $id = $data["ID"];
   $l = $data["LVL"];
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
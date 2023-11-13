<?php
 Class Conversation extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $commentID = $data["CommentID"] ?? base64_encode("");
   $conversationID = $data["ConversationID"] ?? base64_encode("");
   $level = $data["Level"] ?? base64_encode(1);
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Conversation Identifier is missing."
   ];
   $replyingTo = $data["ReplyingTo"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($conversationID)) {
    $accessCode = "Accepted";
    $attachments = "";
    $commentID = base64_decode($commentID);
    $commentID = ($new == 1) ? md5($you."CR".$this->core->timestamp) : $commentID;
    $conversationID = base64_decode($conversationID);
    $action = ($new == 1) ? "Post" : "Update";
    $level = base64_decode($level);
    $commentType = ($level == 1) ? "Comment" : "Reply";
    $conversation = $this->core->Data("Get", ["conversation", $conversationID]) ?? [];
    $comment = $cconversation[$commentID] ?? [];
    if(!empty($conversation["Attachments"])) {
     $attachments = base64_encode(implode(";", $conversation["Attachments"]));
    }
    $body = $data["Body"] ?? base64_encode("");
    $body = $conversation["Body"] ?? $body;
    $body = base64_decode($body);
    $at = base64_encode("Added to $commentType!");
    $at2 = base64_encode("Add Media to $commentType:.EditComment$conversationID");
    $header = ($new == 1) ? "New $commentType" : "Edit $commentType";
    $nsfw = $conversation["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $conversation["Privacy"] ?? $y["Privacy"]["Comments"];
    $replyingTo = base64_decode($replyingTo);
    $r = $this->core->Change([[
     "[Conversation.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => $commentType,
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=NA&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => "NA",
       "[Extras.DesignView.Destination]" => "NA",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&UN=$you"),
       "[Extras.ID]" => $conversationID,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($conversationID))
      ], $this->core->Extension("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Conversation.Attachments]" => $attachments,
     "[Conversation.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
     "[Conversation.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body,
      "Decode" => 1,
      "HTMLDecode" => 1
     ])),
     "[Conversation.CommentID]" => $commentID,
     "[Conversation.Header]" => $header,
     "[Conversation.ID]" => $conversationID,
     "[Conversation.Level]" => $level,
     "[Conversation.New]" => $new,
     "[Conversation.ReplyingTo]" => $replyingTo,
     "[Conversation.Visibility.NSFW]" => $nsfw,
     "[Conversation.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("0426a7fc6b31e5034b6c2cec489ea638")]);
    $r = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".ConversationEditor$conversationID",
      "data-processor" => base64_encode("v=".base64_encode("Conversation:Save"))
     ]]),
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
   $commentID = $data["CommentID"] ?? base64_encode("");
   $conversationID = $data["CRID"] ?? base64_encode("");
   $edit = base64_encode("Conversation:Edit");
   $hide = base64_encode("Conversation:MarkAsHidden");
   $i = 0;
   $level = $data["Level"] ?? base64_encode(1);
   $r = [
    "Body" => "The Conversation Identifier is missing.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($conversationID)) {
    $accessCode = "Accepted";
    $anon = "Anonymous";
    $commentID = base64_decode($commentID);
    $commentType = "";
    $conversationID = base64_decode($conversationID);
    $level = base64_decode($level);
    $level = $level ?? 1;
    $conversation = $this->core->Data("Get", ["conversation", $conversationID]) ?? [];
    $home = base64_encode("Conversation:Home");
    $im = base64_encode("LiveView:InlineMossaic");
    $vote = base64_encode("Vote:Containers");
    if($level == 1) {
     # COMMENTS
     $extension = $this->core->Extension("8938c49b85c52a5429cc8a9f46c14616");
     $r = $this->core->Change([[
      "[Comment.Editor]" => base64_encode("v=$edit&ConversationID=".base64_encode($conversationID)."&new=1")
     ], $this->core->Extension("97e7d7d9a85b30e10ab51b23623ccee5")]);
     foreach($conversation as $key => $value) {
      $t = ($value["From"] == $you) ? $y : $this->core->Member($value["From"]);
      $bl = $this->core->CheckBlocked([$y, "Comments", $key]);
      $cms = $this->core->Data("Get", ["cms", md5($value["From"])]) ?? [];
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
         "data-view" => base64_encode("v=$edit&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin OpenDialog",
         "data-view" => base64_encode("v=$hide&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID))
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
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
        "[Comment.OriginalPoster]" => $op,
        "[Comment.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
        "[Comment.Replies]" => base64_encode("v=$home&CommentID=".base64_encode($key)."&CRID=".base64_encode($conversationID)."&Level=".base64_encode(2)),
        "[Comment.Votes]" => base64_encode("v=$vote&ID=$key&Type=3")
       ], $extension]);
       $i++;
      }
     }
     $commentType .= $this->core->Change([[
      "[Reply.Editor]" => base64_encode("v=$edit&CommentID=".base64_encode($commentID)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&new=1")
     ], $this->core->Extension("5efa423862a163dd55a2785bc7327727")]);
     $r = ($i > 0) ? $commentType : $r;
    } elseif($level == 2) {
     # REPLIES
     $extension = $this->core->Extension("ccf260c40f8fa63be5686f5ceb2b95b1");
     $t = $this->core->Member($conversation[$commentID]["From"]);
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $r = $this->core->Extension("cc3c7b726c1d7f9c50f5f7869513bd80");
     foreach($conversation as $key => $value) {
      $t = ($value["From"] == $you) ? $y : $this->core->Member($value["From"]);
      $bl = $this->core->CheckBlocked([$y, "Comments", $key]);
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]) ?? [];
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
         "data-view" => base64_encode("v=$edit&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($value["CommentID"]))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin OpenDialog",
         "data-view" => base64_encode("v=$hide&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID))
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
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
       "[Reply.OriginalPoster]" => $op,
       "[Reply.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
       "[Reply.Replies]" => base64_encode("v=$home&CommentID=".base64_encode($key)."&CRID=".base64_encode($conversationID)."&Level=".base64_encode(3)),
       "[Reply.Votes]" => base64_encode("v=$vote&ID=$key&Type=3")
      ], $extension]);
      $i++;
     }
    }
    $r = ($i > 0) ? $commentType : $r;
    $r .= $this->core->Change([[
     "[Reply.DisplayName]" => $display,
     "[Reply.Editor]" => base64_encode("v=$edit&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($commentID)."&new=1")
    ], $this->core->Extension("f6876eb53ff51bf537b1b1848500bdab")]);
   } elseif($level == 3) {
     # REPLIES TO REPLIES
     $extension = $this->core->Extension("3847a50cd198853fe31434b6f4e922fd");
     $t = $this->core->Member($conversation[$commentID]["From"]);
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $r = $this->core->Extension("cc3c7b726c1d7f9c50f5f7869513bd80");
     foreach($conversation as $key => $value) {
      $t = ($value["From"] == $you) ? $y : $this->core->Member($value["From"]);
      $bl = $this->core->CheckBlocked([$y, "Comments", $key]);
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]) ?? [];
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
         "data-view" => base64_encode("v=$edit&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($value["CommentID"]))
        ]]), ["class" => "CenterText Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Hide", [
         "class" => "InnerMargin OpenDialog",
         "data-view" => base64_encode("v=$hide&CommentID=".base64_encode($key)."&ConversationID=".base64_encode($conversationID))
        ]]), ["class" => "CenterText Desktop33"]
       ]) : "";
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
        "[Reply.OriginalPoster]" => $op,
        "[Reply.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;width:calc(100% - 1em);"),
        "[Reply.Votes]" => base64_encode("v=$vote&ID=$key&Type=3")
       ], $extension]);
       $i++;
      }
     }
     $r = ($i > 0) ? $commentType : $r;
     $r .= $this->core->Change([[
      "[Reply.DisplayName]" => $display,
     "[Reply.Editor]" => base64_encode("v=$edit&ConversationID=".base64_encode($conversationID)."&Level=".base64_encode($level)."&ReplyingTo=".base64_encode($commentID)."&new=1")
     ], $this->core->Extension("f6876eb53ff51bf537b1b1848500bdab")]);
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
   $commentID = $data["CommentID"] ?? "";
   $id = $data["ID"] ?? "";
   $level = $data["Level"] ?? 1;
   $commentType = ($level == 1) ? "comment" : "reply";
   $new = $data["New"] ?? 0;
   $r = [
    "Body" => "The Conversation or $commentType Identifier are missing."
   ];
   $replyingTo = $data["ReplyingTo"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($commentID) && !empty($id)) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $attachments = [];
    $cc = ($level > 1) ? "Comment$commentID" : "Conversation$id";
    $conversation = $this->core->Data("Get", ["conversation", $id]) ?? [];
    $comment = $conversation[$commentID] ?? [];
    $created = $conversation["Created"] ?? $this->core->timestamp;
    $home = base64_encode("Conversation:Home");
    $nsfw = $conversation["NSFW"] ?? $y["Privacy"]["NSFW"];
    $nsfw = $data["NSFW"] ?? $nsfw;
    $privacy = $conversation["Privacy"] ?? $y["Privacy"]["Comments"];
    $privacy = $data["Privacy"] ?? $privacy;
    if(!empty($data["rATTDLC"])) {
     $attachments = array_reverse(explode(";", base64_decode($data["rATTDLC"])));
     foreach($attachments as $attachments) {
      if(!empty($attachments)) {
       $f = explode("-", base64_decode($attachments));
       if(!empty($f[0]) && !empty($f[1])) {
        array_push($attachments, base64_encode($f[0]."-".$f[1]));
       }
      }
     }
    }
    $attachments = array_unique($attachments);
    $conversation[$commentID] = [
     "Attachments" => $attachments,
     "Body" => $this->core->PlainText([
      "Data" => $data["Body"],
      "HTMLEncode" => 1
     ]),
     "CommentID" => $replyingTo,
     "Created" => $created,
     "From" => $you,
     "Modified" => $this->core->timestamp,
     "NSFW" => $nsfw,
     "Privacy" => $privacy
    ];
    $r = [
     "Body" => "Your $commentType was $actionTaken.",
     "Header" => "Done",
     "Scrollable" => json_encode($conversation, true)
    ];
    $this->core->Data("Save", ["conversation", $id, $conversation]);
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
   $commentID = $data["CommentID"] ?? base64_encode("");
   $conversationID = $data["ConversationID"] ?? base64_encode("");
   $r = [
    "Body" => "The Conversation or comment Identifier are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($commentID) && !empty($conversationID)) {
    $accessCode = "Accepted";
    $commentID = base64_decode($commentID);
    $conversationID = base64_decode($conversationID);
    $conversation = $this->core->Data("Get", [
     "conversation",
     $conversationID
    ]) ?? [];
    $comment = $conversation[$commentID] ?? [];
    $comment["Privacy"] = md5("Private");
    $conversation[$commentID] = $comment;
    $this->core->Data("Save", ["conversation", $conversationID, $conversation]);
    $r = [
     "Body" => "The comment is hidden, and only you can see it.",
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
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
    $additionalContent = $this->view(base64_encode("WebUI:AdditionalContent"), [
     "ID" => $id
    ]);
    $additionalContent = $this->core->RenderView($additionalContent);
    $attachments = "";
    $header = ($new == 1) ? "What's on your mind?" : "Edit Update";
    $update = $this->core->Data("Get", ["su", $id]) ?? [];
    $body = $update["Body"] ?? "";
    $body = (!empty($data["Body"])) ? base64_decode($data["Body"]) : $body;
    if(!empty($update["Attachments"])) {
     $attachments = base64_encode(implode(";", $update["Attachments"]));
    }
    $nsfw = $update["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $update["Privacy"] ?? $y["Privacy"]["Posts"];
    $to = (!empty($to)) ? base64_decode($to) : $to;
    $r = $this->core->Change([[
     "[Update.AdditionalContent]" => $additionalContent["Extension"],
     "[Update.Header]" => $header,
     "[Update.ID]" => $id,
     "[Update.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body
     ])),
     "[Update.DesignView]" => "Edit$id",
     "[Update.Downloads]" => $attachments,
     "[Update.Downloads.LiveView]" => $additionalContent["LiveView"]["DLC"],
     "[Update.From]" => $you,
     "[Update.ID]" => $id,
     "[Update.New]" => $new,
     "[Update.To]" => $to,
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
    "Body" => "The Post Identifier is missing.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["SU"])) {
    $bl = $this->core->CheckBlocked([$y, "Status Updates", $data["SU"]]);
    $blockCommand = ($bl == 0) ? "Block" : "Unblock";
    $_StatusUpdate = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("StatusUpdate;".$data["SU"])
    ]);
    if($_StatusUpdate["Empty"] == 0) {
     $accessCode = "Accepted";
     $update = $_StatusUpdate["DataModel"];
     $displayName = $update["From"];
     $displayName = (!empty($update["To"]) && $update["From"] != $update["To"]) ? "$displayName to ".$update["To"] : $displayName;
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
    $att = $update["Attachments"] ?? [];
    $created = $update["Created"] ?? $this->core->timestamp;
    $illegal = $update["Illegal"] ?? 0;
    $now = $this->core->timestamp;
    $nsfw = $data["nsfw"] ?? $y["Privacy"]["NSFW"];
    $privacy = $data["pri"] ?? $y["Privacy"]["Posts"];
    if(!empty($data["rATTF"])) {
     $dlc = array_reverse(explode(";", base64_decode($data["rATTF"])));
     foreach($dlc as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        array_push($att, base64_encode($f[0]."-".$f[1]));
       }
      }
     }
    } if($new == 1) {
     $mainstream = $this->core->Data("Get", ["app", "mainstream"]) ?? [];
     array_push($mainstream, $id);
     $this->core->Data("Save", ["app", "mainstream", $mainstream]);
     $update = [
      "From" => $you,
      "To" => $to,
      "UpdateID" => $id
     ];
     if(!empty($to) && $to != $you) {
      $stream = $this->core->Data("Get", ["stream", md5($to)]) ?? [];
      $stream[$created] = $update;
      $this->core->Data("Save", ["stream", md5($to), $stream]);
     }
     $stream = $this->core->Data("Get", ["stream", md5($you)]) ?? [];
     $stream[$created] = $update;
     $this->core->Data("Save", ["stream", md5($you), $stream]);
    }
    $update = [
     "Attachments" => array_unique($att),
     "Body" => $this->core->PlainText([
      "Data" => $data["Body"],
      "HTMLEncode" => 1
     ]),
     "Created" => $created,
     "From" => $you,
     "ID" => $id,
     "Illegal" => $illegal,
     "Modified" => $now,
     "NSFW" => $nsfw,
     "Privacy" => $privacy,
     "To" => $to
    ];
    $y["Activity"]["LastActivity"] = $this->core->timestamp;
    $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
    $this->core->Data("Save", ["su", $update["ID"], $update]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "The Status Update was $actionTaken.",
     "Header" => "Done"
    ];
    if($new == 1) {
     $this->core->Statistic("New Status Update");
    } else {
     $this->core->Statistic("Edit Status Update");
    }
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
    $stream = $newStream;
    $y["Activity"]["LastActive"] = $this->core->timestamp;
    $conversation = $this->core->Data("Get", ["conversation", $id]);
    if(!empty($conversation)) {
     $conversation["Purge"] = 1;
     $this->core->Data("Save", ["conversation", $id, $conversation]);
    }
    $statusUpdate = $this->core->Data("Get", ["su", $id]);
    if(!empty($statusUpdate)) {
     $statusUpdate["Purge"] = 1;
     $this->core->Data("Save", ["su", $id, $statusUpdate]);
    }
    $this->core->Data("Purge", ["translate", $id]);
    $this->core->Data("Purge", ["votes", $id]);
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
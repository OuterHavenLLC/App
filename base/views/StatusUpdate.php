<?php
 Class StatusUpdate extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["UN", "SU", "body", "new"]);
   $id = $data["SU"];
   $new = $data["new"] ?? 0;
   $now = $this->system->timestamp;
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Post Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $to = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) || $new == 1) {
    $action = ($new == 1) ? "Post" : "Update";
    $id = ($new == 1) ? md5($you."_SU_$now") : $id;
    $at2 = base64_encode("All done! Feel free to close this card.");
    $at3input = ".StatusUpdate$id-ATTF";
    $at3 = base64_encode("Attach to your Post.:$at3input");
    $at3input = "$at3input .rATT";
    $att = "";
    $dv = base64_encode("Common:DesignView");
    $dvi = "UIE$id";
    $additionalContent = $this->system->Change([
     [
      "[UIV.IN]" => $dvi,
      "[UIV.OUT]" => "UIV$id",
      "[UIV.U]" => base64_encode("v=$dv&DV=")
     ], $this->system->Page("7780dcde754b127656519b6288dffadc")
    ]).$this->system->Change([
     [
      "[XFS.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
      "[XFS.ID]" => $id
     ], $this->system->Page("8356860c249e93367a750f3b4398e493")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $em = base64_encode("LiveView:EditorMossaic");
    $header = ($new == 1) ? "What's on your mind?" : "Edit Update";
    $update = $this->system->Data("Get", ["su", $id]) ?? [];
    $body = $update["Body"] ?? "";
    $body = (!empty($data["body"])) ? base64_decode($data["body"]) : $body;
    if(!empty($update["Attachments"])) {
     $att = base64_encode(implode(";", $update["Attachments"]));
    }
    $nsfw = $update["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $update["Privacy"] ?? $y["Privacy"]["Posts"];
    $to = (!empty($to)) ? base64_decode($to) : $to;
    $r = $this->system->Change([[
     "[Update.AdditionalContent]" => $additionalContent,
     "[Update.Attachments]" => $att,
     "[Update.Attachments.LiveView]" => base64_encode("v=$em&AddTo=$at3input&ID="),
     "[Update.Body]" => $this->system->WYSIWYG([
      "UN" => $you,
      "Body" => $this->system->PlainText(["Data" => $body]),
      "adm" => 1,
      "opt" => [
       "id" => "XSUBody",
       "class" => "$dvi Body Xdecode req",
       "name" => "Body",
       "placeholder" => "Body",
       "rows" => 20
      ]
     ]),
     "[Update.From]" => $you,
     "[Update.Header]" => $header,
     "[Update.ID]" => $id,
     "[Update.New]" => $new,
     "[Update.NSFW]" => $this->system->Select("nsfw", "req v2 v2w", $nsfw),
     "[Update.Privacy]" => $this->system->Select("Privacy", "req v2 v2w", $privacy),
     "[Update.To]" => $to,
     "[UIV.IN]" => "UIE$id"
    ], $this->system->Page("7cc50dca7d9bbd7b7d0e3dd7e2450112")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditStatusUpdate$id",
     "data-processor" => base64_encode("v=".base64_encode("StatusUpdate:Save"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Post Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["SU"])) {
    $att = "";
    $con = base64_encode("Conversation:Home");
    $update = $this->system->Data("Get", ["su", $data["SU"]]) ?? [];
    $bl = $this->system->CheckBlocked([$y, "Status Updates", $update["ID"]]);
    $ft = $update["From"];
    $ft = (!empty($update["To"]) && $update["From"] != $update["To"]) ? "$ft to ".$update["To"] : $ft;
    $op = ($update["From"] == $you) ? $y : $this->system->Member($update["From"]);
    $modified = $update["Modified"] ?? "";
    if(empty($modified)) {
     $modified = "";
    } else {
     $_Time = $this->system->TimeAgo($modified);
     $modified = " &bull; Modified ".$_Time;
     $modified = $this->system->Element(["em", $modified]);
    }
    $opt = ($update["From"] != $you) ? $this->system->Element([
     "button", "Block this Update", [
      "class" => "BLK LI",
      "data-cmd" => base64_encode("B"),
      "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($update["ID"])."&list=".base64_encode("Status Updates")."&BC=")
     ]
    ]) : "";
    $opt = ($this->system->ID != $you) ? $opt : "";
    if(!empty($update["Attachments"])) {
     $att = base64_encode("LiveView:InlineMossaic");
     $att = $this->view($att, ["Data" => [
      "ID" => base64_encode(implode(";", $update["Attachments"])),
      "Type" => base64_encode("DLC")
     ]]);
    }
    $reactions = ($op["Login"]["Username"] != $you) ? base64_encode($this->view(base64_encode("Common:Reactions"), ["Data" => [
     "CRID" => $update["ID"],
     "T" => $op["Login"]["Username"],
     "Type" => 3
    ]])) : $this->system->Element([
     "div", "&nbsp;", ["class" => "Desktop66"]
    ]);
    $r = $this->system->Change([[
     "[StatusUpdate.Attachments]" => $att,
     "[StatusUpdate.Body]" => $this->system->PlainText([
      "BBCodes" => 1,
      "Data" => $update["Body"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]),
     "[StatusUpdate.Created]" => $this->system->TimeAgo($update["Created"]),
     "[StatusUpdate.Conversation]" => $this->system->Change([[
      "[Conversation.CRID]" => $update["ID"],
      "[Conversation.CRIDE]" => base64_encode($update["ID"]),
      "[Conversation.Level]" => base64_encode(1),
      "[Conversation.URL]" => base64_encode("v=$con&CRID=[CRID]&LVL=[LVL]")
     ], $this->system->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
     "[StatusUpdate.DisplayName]" => $ft,
     "[StatusUpdate.ID]" => $update["ID"],
     "[StatusUpdate.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("StatusUpdate;".$update["ID"])),
     "[StatusUpdate.Modified]" => $modified,
     "[StatusUpdate.Options]" => $opt,
     "[StatusUpdate.ProfilePicture]" => $this->system->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
     "[StatusUpdate.Reactions]" => $reactions,
     "[StatusUpdate.Share]" => base64_encode("v=".base64_encode("StatusUpdate:Share")."&ID=".base64_encode($update["ID"])."&UN=".base64_encode($update["From"]))
    ], $this->system->Page("2e76fb1523c34ed0c8092cde66895eb1")]);
   }
   return $this->system->Card([
    "Front" => $r
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Update Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $to = $data["To"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $update = $this->system->Data("Get", ["su", $id]) ?? [];
    $att = $update["Attachments"] ?? [];
    $created = $update["Created"] ?? $this->system->timestamp;
    $illegal = $update["Illegal"] ?? 0;
    $now = $this->system->timestamp;
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
     $mainstream = $this->system->Data("Get", ["x", "mainstream"]) ?? [];
     array_push($mainstream, $id);
     $this->system->Data("Save", ["x", "mainstream", $mainstream]);
     $update = [
      "From" => $you,
      "To" => $to,
      "UpdateID" => $id
     ];
     if(!empty($to) && $to != $you) {
      $stream = $this->system->Data("Get", ["stream", md5($to)]) ?? [];
      $stream[$created] = $update;
      $this->system->Data("Save", ["stream", md5($to), $stream]);
     }
     $stream = $this->system->Data("Get", ["stream", md5($you)]) ?? [];
     $stream[$created] = $update;
     $this->system->Data("Save", ["stream", md5($you), $stream]);
    }
    $update = [
     "Attachments" => array_unique($att),
     "Body" => $this->system->PlainText([
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
    $y["Activity"]["LastActivity"] = $this->system->timestamp;
    $y["Points"] = $y["Points"] + $this->system->core["PTS"]["NewContent"];
    $this->system->Data("Save", ["su", $update["ID"], $update]);
    $this->system->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Status Update was $actionTaken."
     ]),
     "Header" => "Done"
    ]);
    if($new == 1) {
     $this->system->Statistic("SU");
    } else {
     $this->system->Statistic("SUu");
    }
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Update Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $mainstream = $this->system->Data("Get", ["x", "mainstream"]) ?? [];
    $newMainstream = [];
    $newStream = [];
    $stream = $this->system->Data("Get", ["stream", md5($you)]) ?? [];
    foreach($mainstream as $key => $value) {
     if($id != $value) {
      $newMainstream[$key] = $value;
     }
    } foreach($stream as $key => $value) {
     if($id != $value["UpdateID"]) {
      $newStream[$key] = $value;
     }
    }
    $mainstream = $newMainstream;
    $stream = $newStream;
    $y["Activity"]["LastActive"] = $this->system->timestamp;
    $this->system->Data("Purge", ["su", $id]);
    $this->view(base64_encode("Conversation:SaveDelete"), [
     "Data" => ["ID" => $id]
    ]);
    $this->system->Data("Purge", ["local", $id]);
    $this->system->Data("Purge", ["react", $id]);
    $this->system->Data("Save", ["mbr", md5($you), $y]);
    $this->system->Data("Save", ["stream", md5($you), $stream]);
    $this->system->Data("Save", ["x", "mainstream", $mainstream]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The Post was deleted."]),
     "Header" => "Done"
    ]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function Share(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $id = $data["ID"];
   $un = $data["UN"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id) && !empty($un)) {
    $id = base64_decode($id);
    $un = base64_decode($un);
    $t = ($un == $y["Login"]["Username"]) ? $y : $this->system->Member($un);
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out ".$t["Personal"]["DisplayName"]."'s status update!"
     ]).$this->system->Element([
      "div", "[StatusUpdate:$id]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=Album",
     "[Share.ContentID]" => "Status Update",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $t["Personal"]["DisplayName"]."'s status update"
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
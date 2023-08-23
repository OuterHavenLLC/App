<?php
 Class StatusUpdate extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["UN", "SU", "body", "new"]);
   $id = $data["SU"];
   $new = $data["new"] ?? 0;
   $now = $this->core->timestamp;
   $r = [
    "Body" => "The Post Identifier is missing."
   ];
   $to = $data["UN"];
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
    $at2 = base64_encode("All done! Feel free to close this card.");
    $at3input = ".StatusUpdate$id-ATTF";
    $at3 = base64_encode("Attach to your Post.:$at3input");
    $at3input = "$at3input .rATT";
    $att = "";
    $designViewEditor = "UIE$id";
    $header = ($new == 1) ? "What's on your mind?" : "Edit Update";
    $update = $this->core->Data("Get", ["su", $id]) ?? [];
    $body = $update["Body"] ?? "";
    $body = $data["body"] ?? $body;
    if(!empty($update["Attachments"])) {
     $att = base64_encode(implode(";", $update["Attachments"]));
    }
    $nsfw = $update["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $update["Privacy"] ?? $y["Privacy"]["Posts"];
    $to = (!empty($to)) ? base64_decode($to) : $to;
    $r = $this->core->Change([[
     "[Update.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => "Status Update",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=N/A&Added=N/A&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => $designViewEditor,
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Update.Header]" => $header,
     "[Update.ID]" => $id,
     "[Update.Inputs]" => $this->core->RenderInputs([
      [
       "Attributes" => [
        "name" => "From",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $you
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
        "name" => "To",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $to
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
        "data-u" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&AddTo=$at3input&ID="),
        "name" => "rATTF",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditUpdate$id-ATTF"
       ],
       "Type" => "Text",
       "Value" => $att
      ],
      [
       "Attributes" => [
        "class" => "$designViewEditor Body Xdecode req",
        "id" => "EditPageBody$id",
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
        "Data" => $body
       ])
      ]
     ]).$this->core->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->core->RenderVisibilityFilter([
      "Value" => $privacy
     ]),
     "[UIV.IN]" => "UIE$id"
    ], $this->core->Page("7cc50dca7d9bbd7b7d0e3dd7e2450112")]);
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
    $accessCode = "Accepted";
    $att = "";
    $con = base64_encode("Conversation:Home");
    $update = $this->core->Data("Get", ["su", $data["SU"]]) ?? [];
    $bl = $this->core->CheckBlocked([$y, "Status Updates", $update["ID"]]);
    $ft = $update["From"];
    $ft = (!empty($update["To"]) && $update["From"] != $update["To"]) ? "$ft to ".$update["To"] : $ft;
    $op = ($update["From"] == $you) ? $y : $this->core->Member($update["From"]);
    $modified = $update["Modified"] ?? "";
    if(empty($modified)) {
     $modified = "";
    } else {
     $_Time = $this->core->TimeAgo($modified);
     $modified = " &bull; Modified ".$_Time;
     $modified = $this->core->Element(["em", $modified]);
    }
    $opt = ($update["From"] != $you) ? $this->core->Element([
     "button", "Block this Update", [
      "class" => "BLK LI",
      "data-cmd" => base64_encode("B"),
      "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($update["ID"])."&list=".base64_encode("Status Updates")."&BC=")
     ]
    ]) : "";
    $opt = ($this->core->ID != $you) ? $opt : "";
    if(!empty($update["Attachments"])) {
     $att = base64_encode("LiveView:InlineMossaic");
     $att = $this->view($att, ["Data" => [
      "ID" => base64_encode(implode(";", $update["Attachments"])),
      "Type" => base64_encode("DLC")
     ]]);
    }
    $votes = ($op["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
    $votes = base64_encode("v=$votes&ID=".$update["ID"]."&Type=1");
    $r = $this->core->Change([[
     "[StatusUpdate.Attachments]" => $att,
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
      "[Conversation.URL]" => base64_encode("v=$con&CRID=[CRID]&LVL=[LVL]")
     ], $this->core->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
     "[StatusUpdate.DisplayName]" => $ft,
     "[StatusUpdate.ID]" => $update["ID"],
     "[StatusUpdate.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("StatusUpdate;".$update["ID"])),
     "[StatusUpdate.Modified]" => $modified,
     "[StatusUpdate.Options]" => $opt,
     "[StatusUpdate.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:0.5em;width:calc(100% - 1em);"),
     "[StatusUpdate.Share]" => base64_encode("v=".base64_encode("StatusUpdate:Share")."&ID=".base64_encode($update["ID"])."&UN=".base64_encode($update["From"])),
     "[StatusUpdate.Votes]" => $votes
    ], $this->core->Page("2e76fb1523c34ed0c8092cde66895eb1")]);
    $r = [
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
     $mainstream = $this->core->Data("Get", ["x", "mainstream"]) ?? [];
     array_push($mainstream, $id);
     $this->core->Data("Save", ["x", "mainstream", $mainstream]);
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
     $this->core->Statistic("SU");
    } else {
     $this->core->Statistic("SUu");
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Update Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
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
    $mainstream = $this->core->Data("Get", ["x", "mainstream"]) ?? [];
    $newMainstream = [];
    $newStream = [];
    $stream = $this->core->Data("Get", ["stream", md5($you)]) ?? [];
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
    $y["Activity"]["LastActive"] = $this->core->timestamp;
    $this->core->Data("Purge", ["su", $id]);
    $this->view(base64_encode("Conversation:SaveDelete"), [
     "Data" => ["ID" => $id]
    ]);
    $this->core->Data("Purge", ["local", $id]);
    $this->core->Data("Purge", ["votes", $id]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $this->core->Data("Save", ["stream", md5($you), $stream]);
    $this->core->Data("Save", ["x", "mainstream", $mainstream]);
    $r = [
     "Body" => "The Post was deleted.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
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
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "UN"]);
   $id = $data["ID"];
   $un = $data["UN"];
   $r = [
    "Body" => "The Share Sheet Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($un)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $un = base64_decode($un);
    $t = ($un == $you) ? $y : $this->core->Member($un);
    $body = $this->core->PlainText([
     "Data" => $this->core->Element([
      "p", "Check out ".$t["Personal"]["DisplayName"]."'s status update!"
     ]).$this->core->Element([
      "div", "[StatusUpdate:$id]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->core->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=Album",
     "[Share.ContentID]" => "Status Update",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $t["Personal"]["DisplayName"]."'s status update"
    ], $this->core->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
    $r = [
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
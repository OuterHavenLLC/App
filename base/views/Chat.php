<?php
 Class Chat extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Home(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->core->FixMissing($d, ["GroupChat", "to"]);
   $group = $d["GroupChat"] ?? 0;
   $un = base64_decode($d["to"]);
   $y = $this->you;
   if($group == 1) {
    $active = "Active";
    $chat = md5("Chat_$un");
    $dn = $this->core->Data("Get", ["pf", $un]) ?? [];
    $dn = $dn["Title"] ?? "Group Chat";
    $lobby = base64_encode("v=".base64_encode("Forum:About")."&ID=".$d["to"]);
    $t = [];
   } else {
    $t = ($un == $y["UN"]) ? $y : $this->core->Member($un);
    $active = ($t["oStatus"] == 1) ? "Online" : "Offline";
    $chat = md5("Chat_".$y["UN"]."-$un");
    $dn = $t["DN"];
    $lobby = base64_encode("v=".base64_encode("Profile:Lobby")."&Chat=1&onProf=1&UN=".$d["to"]);
   }
   $at1 = base64_encode("Share with $dn in Chat:.ChatAttachments$chat-ATTF");
   $at2 = base64_encode("Added to Chat Message!");
   $sc = base64_encode("Search:Containers");
   $r = $this->core->Change([[
    "[Chat.ActivityStatus]" => $active,
    "[Chat.Attachments]" => base64_encode("v=$sc&AddTo=$at1&Added=$at2&UN=".$y["UN"]."&st=XFS"),
    "[Chat.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&ID="),
    "[Chat.DisplayName]" => $dn,
    "[Chat.GroupChat]" => $group,
    "[Chat.ID]" => $chat,
    "[Chat.List]" => "v=".base64_encode("Chat:List")."&GroupChat=$group&to=".$d["to"],
    "[Chat.Profile]" => $lobby,
    "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:10%;max-width:4em;width:90%"),
    "[Chat.Send]" => base64_encode("v=".base64_encode("Chat:Save")),
    "[Chat.To]" => $un,
    "[Chat.Type]" => $group
   ], $this->core->Page("a4c140822e556243e3edab7cae46466d")]);
   return $r;
  }
  function List(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->core->FixMissing($d, ["GroupChat", "to"]);
   $group = $d["GroupChat"] ?? 0;
   $msg = [];
   $r = $this->core->Page("2ce9b2d2a7f5394df6a71df2f0400873");
   $t = $d["to"];
   $tpl = $this->core->Page("1f4b13bf6e6471a7f5f9743afffeecf9");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($t)) {
    $attlv = base64_encode("LiveView:InlineMossaic");
    $t = base64_decode($t);
    $chat = ($group == 1) ? $t : md5($t);
    $c = $this->core->Data("Get", ["msg", $chat]) ?? [];
    $c2 = $this->core->Data("Get", ["msg", md5($y["UN"])]) ?? [];
    $c2 = ($group == 0) ? $c2 : [];
    $chat = array_merge($c, $c2);
    foreach($chat as $k => $v) {
     $ck = ($v["From"] == $t && $v["To"] == $y["UN"]) ? 1 : 0;
     $ck2 = ($v["From"] == $y["UN"] && $v["To"] == $t) ? 1 : 0;
     if($group == 1 || $ck == 1 || $ck2 == 1) {
      $class = ($v["From"] != $you) ? "MSGt" : "MSGy";
      $att = "";
      if(!empty($v["Attachments"])) {
       $att = $this->view($attlv, ["Data" => [
        "ID" => base64_encode(implode(";", $v["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $mc = (!empty($v["MSG"])) ? $this->core->Element([
       "p", base64_decode($v["MSG"])
      ]) : "";
      $msg[$k] = [
       "[Message.Attachments]" => $att,
       "[Message.Class]" => $class,
       "[Message.MSG]" => $mc,
       "[Message.Sent]" => $this->core->TimeAgo($v["Timestamp"])
      ];
     }
    }
   } if(!empty($msg)) {
    $r = "";
    ksort($msg);
    foreach($msg as $k => $v) {
     $message = $tpl;
     $r .= $this->core->Change([$v, $message]);
    }
   }
   return $r;
  }
  function Menu(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $integrated = $data["Integrated"] ?? 0;
   $r = [
    "Body" => "Unknown Error."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } else {
    $accessCode = "Accepted";
    $search = base64_encode("Search:Containers");
    $r = $this->core->Change([[
     "[Chat.1:1]" => base64_encode("v=$search&1on1=1&st=Chat"),
     "[Chat.Groups]" => base64_encode("v=$search&Group=1&st=Chat"),
     "[Chat.ID]" => md5($you)
    ], $this->core->Page("2e1855b9baa7286162fb571c5f80da0f")]);
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
   $d = $a["Data"] ?? [];
   $d = $this->core->DecodeBridgeData($d);
   $d = $this->core->FixMissing($d, [
    "GroupChat",
    "MSG",
    "Share",
    "To",
    "rATTF"
   ]);
   $att = $d["rATTF"];
   $m = $d["MSG"];
   $ck = (!empty($att) && empty($m)) ? 1 : 0;
   $ck2 = (empty($att) && !empty($m)) ? 1 : 0;
   $ck3 = (!empty($att) && !empty($m)) ? 1 : 0;
   $group = $d["GroupChat"] ?? 0;
   $r = "Failed to Send";
   $to = $d["To"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = "You must be signed in to continue.";
   } elseif(($ck == 1 || $ck2 == 1 || $ck3 == 1) && !empty($to)) {
    $accessCode = "Accepted";
    $att = [];
    $chat = ($group == 1) ? $to : md5($to);
    $sent = $this->core->timestamp;
    $to = ($group == 1 && $d["Share"] == 1) ? "" : $to;
    if(!empty($d["rATTF"])) {
     $dlc = array_reverse(explode(";", base64_decode($d["rATTF"])));
     foreach($dlc as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        array_push($att, base64_encode($f[0]."-".$f[1]));
       }
      }
     }
    }
    $att = array_unique($att);
    $msg = $this->core->Data("Get", ["msg", $chat]) ?? [];
    $msg[$sent."_".$y["UN"]] = [
     "Attachments" => $att,
     "From" => $y["UN"],
     "MSG" => base64_encode($m),
     "Read" => 0,
     "Timestamp" => $sent,
     "To" => $to
    ];
    $r = "Sent";
    #$this->core->Data("Save", ["msg", $chat, $msg]);
   }
   return $this->core->JSONResponse([
    $accessCode,
    $r
   ]);
  }
  function SaveShare(array $a) {
   $accessCode = "Denied";
   $d = $a["Data"] ?? [];
   $d = $this->core->DecodeBridgeData($d);
   $d = $this->core->FixMissing($d, ["ID", "UN"]);
   $ec = "Denied";
   $id = $d["ID"];
   $r = [
    "Body" => "The Member or Message Identifiers are missing."
   ];
   $un = $d["UN"];
   $y = $this->you;
   if($y["UN"] == $this->core->ID) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($un)) {
    $i = 0;
    $x = $this->core->DatabaseSet("MBR");
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.mbr.", "", $v);
     if($i == 0) {
      $t = $this->core->Data("Get", ["mbr", $v]) ?? [];
      if($un == $t["UN"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $r = [
      "Body" => "The Member $un does not exist.",
      "Header" => "Forbidden"
     ];
    } else {
     $ec = "Accepted";
     $this->view(base64_encode("Chat:Save"), ["Data" => [
      "MSG" => $this->core->PlainText(["Data" => $id, "Processor" => 1]),
      "Share" => $this->core->PlainText(["Data" => 1, "Processor" => 1]),
      "To" => $this->core->PlainText(["Data" => $un, "Processor" => 1])
     ]]);
     $r = [
      "Body" => "The message was sent to $un.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([$ec, $r]);
  }
  function SaveShareGroup(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->core->DecodeBridgeData($d);
   $d = $this->core->FixMissing($d, ["ID", "UN"]);
   $ec = "Denied";
   $id = $d["ID"];
   $r = [
    "Body" => "The Forum or Message Identifiers are missing."
   ];
   $un = $d["UN"];
   $y = $this->you;
   if($y["UN"] == $this->core->ID) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($un)) {
    $active = 0;
    $i = 0;
    $un = $this->core->CallSign($un);
    $x = $this->core->DatabaseSet("PF");
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.pf.", "", $v);
     if($active == 0 && $i == 0) {
      $f = $this->core->Data("Get", ["pf", $v]) ?? [];
      if($un == $this->core->CallSign($f["Title"])) {
       $manifest = $this->core->Data("Get", ["pfmanifest", $v]) ?? [];
       foreach($manifest as $mk => $mv) {
        foreach($mv as $mk2 => $mv2) {
         if($active == 0 && $mk2 == $y["UN"]) {
          $active++;
          $i++;
          $ttl = $f["Title"];
          $un = $v;
         }
        }
       }
      }
     }
    } if($active == 0 && $i == 0) {
     $r = [
      "Body" => "The Forum does not exist.",
      "Header" => "Forbidden"
     ];
    } else {
     $ec = "Accepted";
     $this->view(base64_encode("Chat:Save"), ["Data" => [
      "GroupChat" => $this->core->PlainText(["Data" => 1, "Processor" => 1]),
      "MSG" => $this->core->PlainText(["Data" => $id, "Processor" => 1]),
      "Share" => $this->core->PlainText(["Data" => 1, "Processor" => 1]),
      "To" => $this->core->PlainText(["Data" => $un, "Processor" => 1])
     ]]);
     $r = [
      "Body" => "The message was sent to $ttl.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([$ec, $r]);
  }
  function Share(array $a) {
   $btn = "";
   $d = $a["Data"] ?? [];
   $d = $this->core->FixMissing($d, ["GroupChat", "ID", "UN"]);
   $id = $d["ID"];
   $r = $this->core->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Data is missing."
   ], $this->core->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($this->core->PlainText([
     "Data" => $id, "HTMLDencode" => 1
    ]));
    $sid = md5($this->core->timestamp);
    $r = $this->core->Change([[
     "[Share.AvailabilityView]" => base64_encode("v=".base64_encode("Common:AvailabilityCheck")."&at=".base64_encode("SendMessage")."&av="),
     "[Share.ID]" => $sid,
     "[Share.Message]" => $id
    ], $this->core->Page("16b534e5d1b3838a98abfb3bcf3f7b99")]);
    $btn = $this->core->Element(["button", "Send", [
     "class" => "BB Xedit v2",
     "data-type" => ".ShareMessage$sid",
     "data-u" => base64_encode("v=".base64_encode("Chat:SaveShare")),
     "id" => "fSub"
    ]]);
   }
   return [
    "Action" => $btn,
    "Front" => $r
   ];
  }
  function ShareGroup(array $a) {
   $btn = "";
   $d = $a["Data"] ?? [];
   $d = $this->core->FixMissing($d, ["GroupChat", "ID", "UN"]);
   $id = $d["ID"];
   $r = $this->core->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Data is missing."
   ], $this->core->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = base64_decode($this->core->PlainText([
     "Data" => $id, "HTMLDencode" => 1
    ]));
    $sid = md5($this->core->timestamp);
    $r = $this->core->Change([[
     "[Share.AvailabilityView]" => base64_encode("v=".base64_encode("Common:AvailabilityCheck")."&at=".base64_encode("SendMessageGroup")."&av="),
     "[Share.ID]" => $sid,
     "[Share.Message]" => $id
    ], $this->core->Page("16b534e5d1b3838a98abfb3bcf3f7b99")]);
    $btn = $this->core->Element(["button", "Send", [
     "class" => "BB Xedit v2",
     "data-type" => ".ShareMessage$sid",
     "data-u" => base64_encode("v=".base64_encode("Chat:SaveShareGroup")),
     "id" => "fSub"
    ]]);
   }
   return [
    "Action" => $btn,
    "Front" => $r
   ];
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
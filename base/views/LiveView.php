<?php
 Class LiveView extends OH {
  function __construct() {
   parent::__construct();
   $this->NoMedia = $this->core->Element(["h3", "Add Media to get a Preview", [
    "class" => "CenterText InnerMargin UpperCase"
   ]]);
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function CoreMedia(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $dlc = $data["DLC"] ?? "";
   $i = 0;
   $r = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($dlc)) {
    $dlc = base64_decode($dlc);
    $fileSystem = $this->core->Data("Get", ["app", "fs"]);
    $username = $this->core->ID;
    $dlc = explode(".", $dlc);
    $dlc = $fileSystem[$dlc[0]] ?? [];
    if(!empty($dlc)) {
     $i++;
     $r = $this->core->GetAttachmentPreview([
      "DLL" => $dlc,
      "T" => $username,
      "Y" => $you
     ]);
    }
   }
   $r = ($i == 0) ? $this->NoMedia : $r;
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Editor(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $addTo = $data["AddTo"] ?? base64_encode("");
   $addTo = base64_decode($addTo);
   $media = $data["Media"] ?? base64_encode("");
   $media = base64_decode($media);
   $mediaType = $data["MediaType"] ?? base64_encode("");
   $mediaType = base64_decode($mediaType);
   $i = 0;
   $r = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($media) && !empty($mediaType)) {
    $addTo = $data["AddTo"] ?? "";
    if($mediaType == "Attachment" || $mediaType == "CoverPhoto") {
     $attachment = explode("-", base64_decode($media));
     $efs = $this->core->Data("Get", ["fs", md5($attachment[0])])["Files"] ?? [];
     $i++;
     $r = $this->core->GetAttachmentPreview([
      "DLL" => $efs[$attachment[1]],
      "T" => $attachment[0],
      "Y" => $you
     ]);
     if($mediaType == "Attachment") {
      $r .= $this->core->Element([
       "p", "[Attachment:$media]", ["class" => "CenterText"]
      ]);
     }
    } else {
     $_BlackListID = ($mediaType == "BlogPost") ? explode(";", $media)[1] : $media;
     $_BlackListID = ($mediaType == "ForumPost") ? explode(";", $_BlackListID)[1] : $_BlackListID;
     $bl = $this->core->CheckBlocked([$y, $mediaType."s", $_BlackListID]);
     $contentID = ($mediaType == "Member") ? md5($media) : $media;
     $mediaType = ($mediaType == "Article") ? "Page" : $mediaType;
     $mediaType = ($mediaType == "Update") ? "StatusUpdate" : $mediaType;
     $_Media = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("$mediaType;$contentID")
     ]);
     if($_Media["Empty"] == 0) {
      $i++;
      $r = $_Media["Preview"]["Content"] ?? "";
      $r .= $this->core->Element([
       "h4", "Embed Code<br/>[$mediaType:$media]", ["class" => "CenterText"]
      ]);
     }
    }
   }
   $r = ($i == 0) ? $this->NoMedia : $r;
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function InlineMossaic(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $i = 0;
   $media = $data["ID"] ?? base64_encode("");
   $media = base64_decode($media);
   $r = $this->core->Element(["div", NULL, ["class" => "NONAME"]]);
   $mediaType = $data["Type"] ?? base64_encode("");
   $mediaType = base64_decode($mediaType);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($media) && !empty($mediaType)) {
    $attachments = (str_ends_with($media, ";")) ? rtrim($media, ";") : $media;
    $attachments = explode(";", $attachments);
    $count = count($attachments);
    $r = "";
    if($mediaType == "Artist" || $mediaType == "Member") {
     for($i = 0; $i < $count; $i++) {
      if(!empty($attachments[$i])) {
       $member = base64_decode($attachments[$i]);
       $bl = $this->core->CheckBlocked([$y, "Members", $member]);;
       $member = $this->core->GetContentData([
        "Blacklisted" => $bl,
        "ID" => base64_encode("Member;".md5($member))
       ]);
       if($member["Empty"] == 0) {
        $_Member = $member["DataModel"];
        $view = "v=".base64_encode("Shop:Home")."&CARD=1&UN=".base64_encode($_Member["Login"]["Username"]);
        $view = ($mediaType == "Member") ? "v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($_Member["Login"]["Username"]) : $view;
        $r .= $this->core->Element([
         "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "Small OpenCard",
          "data-view" => base64_encode($view)
         ]
        ]);
       }
      }
     }
     $r = $this->core->Element([
      "h4", "Featured ".$mediaType."s", ["class" => "UpperCase"]
     ]).$this->core->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    } elseif($mediaType == "CoverPhoto") {
     $attachment = explode("-", base64_decode($media));
     $efs = $this->core->Data("Get", ["fs", md5($attachment[0])])["Files"] ?? [];
     $i++;
     $r = $this->core->GetAttachmentPreview([
      "DLL" => $efs[$attachment[1]],
      "T" => $attachment[0],
      "Y" => $you
     ]);
    } elseif($mediaType == "DLC") {
     foreach($attachments as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        $efs = $this->core->Data("Get", ["fs", md5($f[0])])["Files"] ?? [];
        $i++;
        $r .= $this->core->Element([
         "button", $this->core->GetAttachmentPreview([
          "DLL" => $efs[$f[1]],
          "T" => $f[0],
          "Y" => $you
         ]), [
          "class" => "FrostedBright Medium OpenCard Rounded",
          "data-view" => base64_encode("v=".base64_encode("File:Home")."&CARD=1&ID=".$f[1]."&UN=".$f[0])
         ]
        ]);
       }
      }
     } if($i > 0) {
      $r = $this->core->Element([
       "h4", "Attachments", ["class" => "UpperCase"]
      ]).$this->core->Element([
       "div", $r, ["class" => "SideScroll"]
      ]);
     }
    } elseif($mediaType == "Product") {
     $coverPhoto = $this->core->PlainText([
      "Data" => "[Media:MiNY]",
      "Display" => 1
     ]);
     for($i = 0; $i < $count; $i++) {
      if(!empty($attachments[$i])) {
       $p = explode("-", base64_decode($attachments[$i]));
       if(!empty($p[0]) && !empty($p[1])) {
        $product = $this->core->Data("Get", ["miny", $p[1]]) ?? [];
        $coverPhoto = $product["ICO"] ?? $coverPhoto;
        $coverPhoto = base64_encode($coverPhoto);
        $r .= $this->core->Change([[
         "[X.LI.I]" => $this->core->CoverPhoto($coverPhoto),
         "[X.LI.T]" => $product["Title"],
         "[X.LI.D]" => $this->core->PlainText([
          "BBCodes" => 1,
          "Data" => $product["Description"],
          "Display" => 1,
          "HTMLDecode" => 1
         ]),
         "[X.LI.DT]" => base64_encode("v=".base64_encode("Product:Home")."&CS=".$this->core->CallSign($product["Title"])."&CARD=1&UN=".base64_encode($p[0]))
        ], $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844")]);//NEW
       }
      }
     }
     $r = $this->core->Element([
      "h4", "Included in this Bundle", ["class" => "UpperCase"]
     ]).$this->core->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function MemberGrid(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $list = $data["List"] ?? "";
   $rows = $data["Rows"] ?? 9;
   $type = $data["Type"] ?? "Web";
   $r = $this->core->Element(["p", "None, yet..."]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($list)) {
    $i = 0;
    $list = json_decode(base64_decode($list), true);
    $list = $this->core->ShuffleList($list);
    $r = "";
    foreach($list as $key => $value) {
     $t = ($key == $you) ? $y : $this->core->Member($key);
     if(!empty($t["Login"])) {
      $i++;
      $r .= $this->core->Element([
       "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
        "class" => "OpenCard Small",
        "data-view" => base64_encode("v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($t["Login"]["Username"]))
       ]
      ]);
     }
    }
    $r = ($i == 0) ? $this->core->Element([
     "p", "None, yet..."
    ]) : $r;
    $r = $this->core->Element([
     "h4", "Contributors", ["class" => "UpperCase"]
    ]).$this->core->Element([
     "div", $r, ["class" => "SideScroll"]
    ]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
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
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
   $mediaType = $data["MediaType"] ?? base64_encode("");
   $mediaType = base64_decode($mediaType);
   $i = 0;
   $id = $data["ID"] ?? "";
   $r = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $addTo = $data["AddTo"] ?? "";
    $attachments = base64_decode($id);
    foreach($attachments as $attachment) {
     $attachment = explode("-", base64_decode($attachment));
     if($mediaType == "Attachments") {
      $efs = $this->core->Data("Get", ["fs", md5($attachment[0])])["Files"] ?? [];
      $i++;
      $r = $this->core->GetAttachmentPreview([
       "DLL" => $efs[$attachment[1]],
       "T" => $attachment[0],
       "Y" => $you
      ]).$this->core->Element([
       "p", "[Attachment:".$attachment[1]."]", ["class" => "CenterText"]
      ]);
     } elseif($mediaType == "Blogs") {
      $r = $this->core->Element(["p", "Blog #$attachments"]);
     } elseif($mediaType == "BlogPosts") {
      $r = $this->core->Element(["p", "Blog Post #".$attachments]);
     } elseif($mediaType == "CoverPhoto") {
      $efs = $this->core->Data("Get", ["fs", md5($attachment[0])])["Files"] ?? [];
      $i++;
      $r = $this->core->GetAttachmentPreview([
       "DLL" => $efs[$attachment[1]],
       "T" => $attachment[0],
       "Y" => $you
      ]);
     } elseif($mediaType == "Forums") {
      $r = $this->core->Element(["p", "Forum #$attachments"]);
     } elseif($mediaType == "ForumPosts") {
      $r = $this->core->Element(["p", "Forum Post #$attachments"]);
     } elseif($mediaType == "Polls") {
      $r = $this->core->Element(["p", "Poll #$attachments"]);
     } elseif($mediaType == "Products") {
      $r = $this->core->Element(["p", "Product #$attachments"]);
     } elseif($mediaType == "Shops") {
      $r = $this->core->Element(["p", "Shop #$attachments"]);
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
   $id = $data["ID"] ?? base64_encode("");
   $r = $this->core->Element(["div", NULL, ["class" => "NONAME"]]);
   $type = $data["Type"] ?? base64_encode("DLC");
   $type = base64_decode($type);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $attachments = base64_decode($id);
    $attachments = (str_ends_with($attachments, ";")) ? rtrim($attachments, ";") : $attachments;
    $attachments = explode(";", $attachments);
    $count = count($attachments);
    $r = "";
    if($type == "Artist") {
     for($i = 0; $i < $count; $i++) {
      if(!empty($attachments[$i])) {
       $member = base64_decode($attachments[$i]);
       if(!empty($member)) {
        $t = ($member == $you) ? $y : $this->core->Member($mbr);
        $r .= $this->core->Element([
         "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "Small OpenCard",
          "data-view" => base64_encode("v=".base64_encode("Shop:Home")."&CARD=1&UN=".base64_encode($f[0]))
         ]
        ]);
       }
      }
     }
     $r = $this->core->Element([
      "h4", "Featured Artists", ["class" => "UpperCase"]
     ]).$this->core->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    } elseif($type == "DLC") {
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
    } elseif($type == "Product") {
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
    } elseif($type == "Profile") {
     for($i = 0; $i < $count; $i++) {
      if(!empty($attachments[$i])) {
       $member = base64_decode($attachments[$i]);
       if(!empty($member)) {
        $t = ($member == $you) ? $y : $this->core->Member($mbr);
        $r .= $this->core->Element([
         "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "OpenCard Small",
          "data-view" => base64_encode("v=".base64_encode("Profile:Home")."&CARD=1&UN=".base64_encode($f[0]))
         ]
        ]);
       }
      }
     }
     $r = $this->core->Element([
      "h4", "Featured Members", ["class" => "UpperCase"]
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
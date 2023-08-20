<?php
 Class LiveView extends GW {
  function __construct() {
   parent::__construct();
   $this->NoResults = $this->core->Element(["h3", "No Attachments", [
    "class" => "CenterText InnerMargin UpperCase"
   ]]);
   $this->you = $this->core->Member($this->core->Username());
  }
  function GetCode(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["Code", "Type"]);
   $r = [
    "Body" => "The Code or Code Type are missing."
   ];
   if(!empty($data["Code"]) && !empty($data["Type"])) {
    $accessCode = "Accepted";
    $r = [
     "Body" => "Paste the code below anywhere within the text you want it to appear in.<br/>[".$data["Type"].":".$data["Code"]."]",
     "Header" => "Embed Code"
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
  function EditorSingle(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["AddTo", "ID"]);
   $i = 0;
   $id = $data["ID"];
   $r = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $attachments = array_filter(explode(";", base64_decode($id)));
    $attachments = array_reverse($attachments);
    foreach($attachments as $dlc) {
     if(!empty($dlc) && $i == 0) {
      $f = explode("-", base64_decode($dlc));
      if(!empty($f[0]) && !empty($f[1])) {
       $efs = $this->core->Data("Get", ["fs", md5($f[0])])["Files"] ?? [];
       $i++;
       $r = $this->core->Change([[
        "[Attachment.CodeProcessor]" => "v=".base64_encode("LiveView:GetCode")."&Code=$dlc&Type=ATT",
        "[Attachment.ID]" => $f[1],
        "[Attachment.Input]" => $data["AddTo"],
        "[Attachment.Preview]" => $this->core->GetAttachmentPreview([
         "DLL" => $efs[$f[1]],
         "T" => $f[0],
         "Y" => $you
        ])
       ], $this->core->Page("8d25bf64ec06d4600180aa5881215a73")]);
      }
     }
    }
   }
   $r = ($i == 0) ? $this->NoResults : $r;
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function EditorMossaic(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["AddTo", "ID"]);
   $i2 = 0;
   $r = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["ID"])) {
    $attachments = explode(";", base64_decode($data["ID"]));
    $count = count($attachments);
    $r = "";
    for($i = 0; $i < $count; $i++) {
     if(!empty($attachments[$i])) {
      $f = explode("-", base64_decode($attachments[$i]));
      $t = $this->core->Member($f[0]);
      $efs = $this->core->Data("Get", ["fs", md5($f[0])])["Files"] ?? [];
      $r .= $this->core->Change([[
       "[Attachment.CodeProcessor]" => "v=".base64_encode("LiveView:GetCode")."&Code=".$attachments[$i]."&Type=ATT",
       "[Attachment.Description]" => $efs[$f[1]]["Description"],
       "[Attachment.DN]" => $t["Personal"]["DisplayName"],
       "[Attachment.ID]" => $attachments[$i],
       "[Attachment.Input]" => $data["AddTo"],
       "[Attachment.Preview]" => $this->core->GetAttachmentPreview([
        "DLL" => $efs[$f[1]],
        "T" => $f[0],
        "Y" => $you
       ]),
       "[Attachment.Title]" => $efs[$f[1]]["Title"],
       "[Attachment.View]" => base64_encode("v=".base64_encode("File:Home")."&CARD=1&ID=".$f[1]."&UN=".$f[0])
      ], $this->core->Page("63668c4c623066fa275830696fda5b4a")]);
      $i2++;
     }
    }
   }
   $r = ($i2 == 0) ? $this->NoResults : $r;
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function EditorProducts(array $a) {
   $accessCode = "Accepted";
   $coverPhoto = $this->core->PlainText([
    "Data" => "[sIMG:MiNY]",
    "Display" => 1
   ]);
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["AddTo", "BNDL"]);
   $i2 = 0;
   $bundle = explode(";", base64_decode($data["BNDL"]));
   $r = "";
   if(!empty($data["BNDL"])) {
    for($i = 0; $i < count($bundle); $i++) {
     if(!empty($bundle[$i])) {
      $p = explode("-", base64_decode($bundle[$i]));
      if(!empty($p[0]) && !empty($p[1])) {
       $p = $this->core->Data("Get", ["miny", $p[1]]) ?? [];
       $coverPhoto = $p["ICO"] ?? $coverPhoto;
       $coverPhoto = base64_encode($coverPhoto);
       $r .= $this->core->Change([[
        "[Attachment.CodeProcessor]" => "v=".base64_encode("LiveView:GetCode")."&Code=".$attachments[$i]."&Type=Product",
        "[Attachment.Description]" => $p["Description"],
        "[Attachment.DN]" => $t["Personal"]["DisplayName"],
        "[Attachment.ID]" => $bundle[$i],
        "[Attachment.Input]" => $data["AddTo"],
        "[Attachment.Preview]" => $this->core->CoverPhoto($coverPhoto),
        "[Attachment.Title]" => $p["Title"],
        "[Attachment.View]" => base64_encode("v=".base64_encode("Product:Home")."&ID=".$p["ID"])
       ], $this->core->Page("63668c4c623066fa275830696fda5b4a")]);
       $i2++;
      }
     }
    }
   }
   $r = ($i2 == 0) ? $this->NoResults : $r;
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
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
   $data = $this->core->FixMissing($data, ["ID", "Type"]);
   $id = $data["ID"];
   $r = $this->core->Element(["div", NULL, ["class" => "NONAME"]]);
   $type = $data["Type"] ?? base64_encode("DLC");
   $type = base64_decode($type);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $attachments = explode(";", base64_decode($id));
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
        $r .= $this->core->Element([
         "button", $this->core->GetAttachmentPreview([
          "DLL" => $efs[$f[1]],
          "T" => $f[0],
          "Y" => $you
         ]), [
          "class" => "K4i Medium OpenCard",
          "data-view" => base64_encode("v=".base64_encode("File:Home")."&CARD=1&ID=".$f[1]."&UN=".$f[0])
         ]
        ]);
       }
      }
     }
     $r = $this->core->Element([
      "h4", "Attachments", ["class" => "UpperCase"]
     ]).$this->core->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    } elseif($type == "Product") {
     $coverPhoto = $this->core->PlainText([
      "Data" => "[sIMG:MiNY]",
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
        ], $this->core->Page("ed27ee7ba73f34ead6be92293b99f844")]);//NEW
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
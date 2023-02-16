<?php
 Class LiveView extends GW {
  function __construct() {
   parent::__construct();
   $this->NoResults = $this->system->Element(["h3", "No Attachments", [
    "class" => "CenterText InnerMargin UpperCase"
   ]]);
   $this->you = $this->system->Member($this->system->Username());
  }
  function GetCode(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["Code", "Type"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Code or Code Type are missing."
    ]),
    "Header" => "Error"
   ]);
   if(!empty($data["Code"]) && !empty($data["Type"])) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "Paste the code below anywhere within the text you want it to appear in."
     ]).$this->system->Element([
      "p", "[".$data["Type"].":".$data["Code"]."]"
     ]),
     "Header" => "Embed Code"
    ]);
   }
   return $r;
  }
  function EditorSingle(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["AddTo", "ID"]);
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
       $efs = $this->system->Data("Get", ["fs", md5($f[0])])["Files"] ?? [];
       $i++;
       $r = $this->system->Change([[
        "[Attachment.CodeProcessor]" => "v=".base64_encode("LiveView:GetCode")."&Code=$dlc&Type=ATT",
        "[Attachment.ID]" => $f[1],
        "[Attachment.Input]" => $data["AddTo"],
        "[Attachment.Preview]" => $this->system->GetAttachmentPreview([
         "DLL" => $efs[$f[1]],
         "T" => $f[0],
         "Y" => $you
        ])
       ], $this->system->Page("8d25bf64ec06d4600180aa5881215a73")]);
      }
     }
    }
   }
   $r = ($i == 0) ? $this->NoResults : $r;
   return $r;
  }
  function EditorMossaic(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["AddTo", "ID"]);
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
      $t = $this->system->Member($f[0]);
      $efs = $this->system->Data("Get", ["fs", md5($f[0])])["Files"] ?? [];
      $r .= $this->system->Change([[
       "[Attachment.CodeProcessor]" => "v=".base64_encode("LiveView:GetCode")."&Code=".$attachments[$i]."&Type=ATT",
       "[Attachment.Description]" => $efs[$f[1]]["Description"],
       "[Attachment.DN]" => $t["Personal"]["DisplayName"],
       "[Attachment.ID]" => $attachments[$i],
       "[Attachment.Input]" => $data["AddTo"],
       "[Attachment.Preview]" => $this->system->GetAttachmentPreview([
        "DLL" => $efs[$f[1]],
        "T" => $f[0],
        "Y" => $you
       ]),
       "[Attachment.Title]" => $efs[$f[1]]["Title"],
       "[Attachment.View]" => base64_encode("v=".base64_encode("File:Home")."&CARD=1&ID=".$f[1]."&UN=".$f[0])
      ], $this->system->Page("63668c4c623066fa275830696fda5b4a")]);
      $i2++;
     }
    }
   }
   $r = ($i2 == 0) ? $this->NoResults : $r;
   return $r;
  }
  function EditorProducts(array $a) {
   $coverPhoto = $this->system->PlainText([
    "Data" => "[sIMG:MiNY]",
    "Display" => 1
   ]);
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["AddTo", "BNDL"]);
   $i2 = 0;
   $bundle = explode(";", base64_decode($data["BNDL"]));
   $r = "";
   if(!empty($data["BNDL"])) {
    for($i = 0; $i < count($bundle); $i++) {
     if(!empty($bundle[$i])) {
      $p = explode("-", base64_decode($bundle[$i]));
      if(!empty($p[0]) && !empty($p[1])) {
       $p = $this->system->Data("Get", ["miny", $p[1]]) ?? [];
       $coverPhoto = $p["ICO"] ?? $coverPhoto;
       $coverPhoto = base64_encode($coverPhoto);
       $r .= $this->system->Change([[
        "[Attachment.CodeProcessor]" => "v=".base64_encode("LiveView:GetCode")."&Code=".$attachments[$i]."&Type=Product",
        "[Attachment.Description]" => $p["Description"],
        "[Attachment.DN]" => $t["Personal"]["DisplayName"],
        "[Attachment.ID]" => $bundle[$i],
        "[Attachment.Input]" => $data["AddTo"],
        "[Attachment.Preview]" => $this->system->CoverPhoto($coverPhoto),
        "[Attachment.Title]" => $p["Title"],
        "[Attachment.View]" => base64_encode("v=".base64_encode("Product:Home")."&ID=".$p["ID"])
       ], $this->system->Page("63668c4c623066fa275830696fda5b4a")]);
       $i2++;
      }
     }
    }
   }
   $r = ($i2 == 0) ? $this->NoResults : $r;
   return $r;
  }
  function InlineMossaic(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Type"]);
   $id = $data["ID"];
   $r = $this->system->Element(["div", NULL, ["class" => "NONAME"]]);
   $type = $data["Type"] ?? base64_encode("DLC");
   $type = base64_decode($type);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $attachments = explode(";", base64_decode($id));
    $count = count($attachments);
    if($type == "Artist") {
     for($i = 0; $i < $count; $i++) {
      if(!empty($attachments[$i])) {
       $member = base64_decode($attachments[$i]);
       if(!empty($member)) {
        $t = ($member == $you) ? $y : $this->system->Member($mbr);
        $r .= $this->system->Element([
         "button", $this->system->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "Small dB2O",
          "data-e" => base64_encode("v=".base64_encode("Shop:Home")."&CARD=1&UN=".base64_encode($f[0]))
         ]
        ]);
       }
      }
     }
     $r = $this->system->Element([
      "h4", "Featured Artists", ["class" => "UpperCase"]
     ]).$this->system->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    } elseif($type == "DLC") {
     foreach($attachments as $dlc) {
      if(!empty($dlc)) {
       $f = explode("-", base64_decode($dlc));
       if(!empty($f[0]) && !empty($f[1])) {
        $efs = $this->system->Data("Get", ["fs", md5($f[0])])["Files"] ?? [];
        $r .= $this->system->Element([
         "button", $this->system->GetAttachmentPreview([
          "DLL" => $efs[$f[1]],
          "T" => $f[0],
          "Y" => $you
         ]), [
          "class" => "K4i Medium dB2O",
          "data-type" => base64_encode("v=".base64_encode("File:Home")."&CARD=1&ID=".$f[1]."&UN=".$f[0])
         ]
        ]);
       }
      }
     }
     $r = $this->system->Element([
      "h4", "Attachments", ["class" => "UpperCase"]
     ]).$this->system->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    } elseif($type == "Product") {
     $coverPhoto = $this->system->PlainText([
      "Data" => "[sIMG:MiNY]",
      "Display" => 1
     ]);
     for($i = 0; $i < $count; $i++) {
      if(!empty($attachments[$i])) {
       $p = explode("-", base64_decode($attachments[$i]));
       if(!empty($p[0]) && !empty($p[1])) {
        $product = $this->system->Data("Get", ["miny", $p[1]]) ?? [];
        $coverPhoto = $product["ICO"] ?? $coverPhoto;
        $coverPhoto = base64_encode($coverPhoto);
        $r .= $this->system->Change([[
         "[X.LI.I]" => $this->system->CoverPhoto($coverPhoto),
         "[X.LI.T]" => $product["Title"],
         "[X.LI.D]" => $this->system->PlainText([
          "BBCodes" => 1,
          "Data" => $product["Description"],
          "Display" => 1,
          "HTMLDecode" => 1
         ]),
         "[X.LI.DT]" => base64_encode("v=".base64_encode("Product:Home")."&CS=".$this->system->CallSign($product["Title"])."&CARD=1&UN=".base64_encode($p[0]))
        ], $this->system->Page("ed27ee7ba73f34ead6be92293b99f844")]);//NEW
       }
      }
     }
     $r = $this->system->Element([
      "h4", "Included in this Bundle", ["class" => "UpperCase"]
     ]).$this->system->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    } elseif($type == "Profile") {
     for($i = 0; $i < $count; $i++) {
      if(!empty($attachments[$i])) {
       $member = base64_decode($attachments[$i]);
       if(!empty($member)) {
        $t = ($member == $you) ? $y : $this->system->Member($mbr);
        $r .= $this->system->Element([
         "button", $this->system->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "Small dB2O",
          "data-e" => base64_encode("v=".base64_encode("Profile:Home")."&CARD=1&UN=".base64_encode($f[0]))
         ]
        ]);
       }
      }
     }
     $r = $this->system->Element([
      "h4", "Featured Members", ["class" => "UpperCase"]
     ]).$this->system->Element([
      "div", $r, ["class" => "SideScroll"]
     ]);
    }
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
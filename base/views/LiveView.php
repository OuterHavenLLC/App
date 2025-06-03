<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol . $_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
 Class LiveView extends OH {
  function __construct() {
   parent::__construct();
   $this->NoMedia = $this->core->Element(["h3", "Add Media to get a Preview", [
    "class" => "CenterText InnerMargin UpperCase"
   ]]);
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function CoreMedia(array $data): string {
   $_View = "";
   $data = $data["Data"] ?? [];
   $dlc = $data["DLC"] ?? "";
   $i = 0;
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
     $_View = $this->core->GetAttachmentPreview([
      "DLL" => $dlc,
      "T" => $username,
      "Y" => $you
     ]);
    }
   }
   $_View = ($i == 0) ? $this->NoMedia : $_View;
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function Editor(array $data): string {
   $_View = "";
   $data = $data["Data"] ?? [];
   $addTo = $data["AddTo"] ?? base64_encode("");
   $addTo = base64_decode($addTo);
   $media = $data["Media"] ?? base64_encode("");
   $media = base64_decode($media);
   $mediaType = $data["MediaType"] ?? base64_encode("");
   $mediaType = base64_decode($mediaType);
   $i = 0;
   $_View = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($media) && !empty($mediaType)) {
    $addTo = $data["AddTo"] ?? "";
    $downloads = [
     "Attachment",
     "CoverPhoto",
     "DemoFile"
    ];
    if(in_array($mediaType, $downloads)) {
     $attachment = explode("-", $media);
     $efs = $this->core->Data("Get", ["fs", md5($attachment[0])])["Files"] ?? [];
     if(!empty($attachment[1])) {
      $i++;
      $_View = $this->core->GetAttachmentPreview([
       "DLL" => $efs[$attachment[1]],
       "T" => $attachment[0],
       "Y" => $you
      ]);
      if($mediaType == "Attachment") {
       $_View .= $this->core->Element([
        "h4", "Embed Code", ["class" => "CenterText"]
       ]).$this->core->Element([
        "p", "[Embed&colon;".base64_encode(implode(";", $attachment))."]", [
         "class" => "CenterText"
        ]
       ]);
      }
     }
    } else {
     $contentID = base64_encode($media);
     $_Media = $this->core->GetContentData([
      "ID" => $contentID
     ]);
     if($_Media["Empty"] == 0) {
      $i++;
      $_View = $_Media["Preview"]["Content"] ?? "";
      $_View .= $this->core->Element([
       "h4", "Embed Code", [
        "class" => "CenterText"
       ]
      ]).$this->core->Element([
       "p", "[Embed&colon;$contentID]", [
        "class" => "CenterText"
       ]
      ]);
     }
    }
   }
   $_View = ($i == 0) ? $this->NoMedia : $_View;
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function InlineMossaic(array $data): string {
   $_View = $this->core->Element(["div", NULL, ["class" => "NONAME"]]);
   $data = $data["Data"] ?? [];
   $i = 0;
   $media = $data["ID"] ?? base64_encode("");
   $media = base64_decode($media);
   $mediaType = $data["Type"] ?? base64_encode("");
   $mediaType = base64_decode($mediaType);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($media) && !empty($mediaType)) {
    $attachments = explode(";", $media);
    $count = count($attachments);
    $previewReady = [
     "Album",
     "Article",
     "Blog",
     "BlogPost",
     "Chat",
     "Forum",
     "ForumPost",
     "NonArtist",
     "Poll",
     "Product",
     "ProductNotBundled",
     "Shop",
     "StatusUpdate"
    ];
    $_View = "";
    if(in_array($mediaType, $previewReady)) {
     foreach($attachments as $key => $attachment) {
      if(!empty($attachment)) {
       $_Media = $this->core->GetContentData([
        "ID" => $attachment
       ]);
       if($_Media["Empty"] == 0) {
        $i++;
        $options = $_Media["ListItem"]["Options"] ?? [];
        $preview = $_Media["Preview"]["Content"] ?? "";
        $_View .= (!empty($options["View"])) ? $this->core->Element([
         "button", $this->core->Element([
          "div", $preview, [
           "class" => "NONAME"
          ]
         ]).$this->core->Element([
          "p", "View in Full", [
           "class" => "CenterText"
          ]
         ]), [
          "class" => "FrostedBright OpenCard Rounded",
          "data-view" => $options["View"]
         ]
        ]) : $preview;
       }
      }
     } if($i > 0) {
      $mediaType = ($mediaType == "BlogPost") ? "Blog Posts" : $mediaType;
      $mediaType = ($mediaType == "ForumPost") ? "Forum Posts" : $mediaType;
      $mediaType = ($mediaType == "Product") ? "Bundled Products" : $mediaType;
      $mediaType = ($mediaType == "ProductNotBundled") ? "Products" : $mediaType;
      $mediaType = ($mediaType == "StatusUpdate") ? "Status Updates" : $mediaType;
      $_View = $this->core->Element([
       "h4", $mediaType, [
        "class" => "UpperCase"
       ]
      ]).$this->core->Element([
       "div", $_View, [
        "class" => "SideScroll"
       ]
      ]);
     }
    } elseif($mediaType == "Artist" || $mediaType == "Member") {
     foreach($attachments as $key => $attachment) {
      if(!empty($attachment)) {
       $i++;
       $member = $this->core->GetContentData([
        "ID" => $attachment
       ]);
       if($member["Empty"] == 0) {
        $_Member = $member["DataModel"];
        $view = "v=".base64_encode("Shop:Home")."&CARD=1&UN=".base64_encode($_Member["Login"]["Username"]);
        $view = ($mediaType == "Member") ? "v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($_Member["Login"]["Username"]) : $view;
        $_View .= $this->core->Element([
         "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "Small OpenCard",
          "data-encryption" => "AES",
          "data-view" => $this->core->AESencrypt($view)
         ]
        ]);
       }
      }
     } if($i > 0) {
      $_View = $this->core->Element([
       "h4", "Featured ".$mediaType."s", [
        "class" => "UpperCase"
       ]
      ]).$this->core->Element([
       "div", $_View, [
        "class" => "SideScroll"
       ]
      ]);
     }
    } elseif($mediaType == "CoverPhoto") {
     $attachment = explode("-", base64_decode($media));
     if(!empty($attachment[0]) && !empty($attachment[1])) {
      $efs = $this->core->Data("Get", ["fs", md5($attachment[0])])["Files"] ?? [];
      $i++;
      $_View = $this->core->GetAttachmentPreview([
       "DLL" => $efs[$attachment[1]],
       "T" => $attachment[0],
       "Y" => $you
      ]);
     }
    } elseif($mediaType == "DemoFile" || $mediaType == "DLC") {
     foreach($attachments as $attachment) {
      if(!empty($attachment)) {
       $attachment = explode("-", base64_decode($attachment));
       if(!empty($attachment[0]) && !empty($attachment[1])) {
        $efs = $this->core->Data("Get", ["fs", md5($attachment[0])])["Files"] ?? [];
        $i++;
        $_View .= $this->core->Element([
         "button", $this->core->GetAttachmentPreview([
          "DLL" => $efs[$attachment[1]],
          "T" => $attachment[0],
          "Y" => $you
         ]), [
          "class" => "FrostedBright Medium OpenCard Rounded",
          "data-encryption" => "AES",
          "data-view" => $this->core->AESencrypt("v=".base64_encode("File:Home")."&CARD=1&ID=".$attachment[1]."&UN=".$attachment[0])
         ]
        ]);
       }
      }
     } if($i > 0) {
      $mediaType = ($mediaType == "DemoFile") ? "Demo Media" : "Attachments";
      $_View = $this->core->Element([
       "h4", $mediaType, [
        "class" => "UpperCase"
       ]
      ]).$this->core->Element([
       "div", $_View, [
        "class" => "SideScroll"
       ]
      ]);
     }
    } elseif($mediaType == "NonArtist") {
     foreach($attachments as $key => $attachment) {
      if(!empty($attachment)) {
       $_Media = $this->core->GetContentData([
        "ID" => $attachment
       ]);
       if($_Media["Empty"] == 0) {
        $i++;
        $_View = $_Media["Preview"]["Content"] ?? "";
       }
      }
     } if($i > 0) {
      $_View = $this->core->Element([
       "h4", "Featured Members", [
        "class" => "UpperCase"
       ]
      ]).$this->core->Element([
       "div", $_View, [
        "class" => "SideScroll"
       ]
      ]);
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function MemberGrid(array $data): string {
   $_View = "";
   $data = $data["Data"] ?? [];
   $list = $data["List"] ?? "";
   $rows = $data["Rows"] ?? 9;
   $type = $data["Type"] ?? "Web";
   $_View = $this->core->Element(["p", "None, yet..."]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($list)) {
    $i = 0;
    $list = json_decode(base64_decode($list), true);
    $list = $this->core->ShuffleList($list);
    $_View = "";
    foreach($list as $key => $value) {
     $t = ($key == $you) ? $y : $this->core->Member($key);
     if(!empty($t["Login"])) {
      $i++;
      $_View .= $this->core->Element([
       "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
        "class" => "OpenCard Small",
        "data-encryption" => "AES",
        "data-view" => $this->core->AESencrypt("v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($t["Login"]["Username"]))
       ]
      ]);
     }
    }
    $_View = ($i == 0) ? $this->core->Element([
     "p", "None, yet..."
    ]) : $_View;
    $_View = $this->core->Element([
     "h4", "Contributors", [
      "class" => "UpperCase"
     ]
    ]).$this->core->Element([
     "div", $_View, [
      "class" => "SideScroll"
     ]
    ]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class File extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Download(array $a) {
   $data = $a["Data"] ?? [];
   $filePath = $data["FilePath"] ?? "";
   if(empty($filePath)) {
    return "Not Found";
   } else {
    $filePath = $this->core->efs.base64_decode($filePath);
    header("Content-Disposition: attachment; filename=".basename($filePath));
    header("Content-type: application/x-file-to-save");
    ob_end_clean();
    readfile($filePath);
    exit;
   }
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "UN"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The File Identifier is missing."
   ];
   $username = $data["UN"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $username = $data["UN"] ?? base64_encode($you);
    $username = base64_decode($username);
    $fileSystem = $this->core->Data("Get", ["fs", md5($username)]) ?? [];
    $files = ($this->core->ID == $username) ? $this->core->Data("Get", [
     "app",
     "fs"
    ]) : $fileSystem["Files"];
    $file = $files[$id] ?? [];
    $albums = [];
    if($this->core->ID != $username) {
     foreach($fileSystem["Albums"] as $key => $album) {
      $albums[$key] = $album["Title"];
     }
    } else {
     $albums[md5("unsorted")] = "System Media Library";
    }
    $album = $file["AID"] ?? md5("unsorted");
    $description = $file["Description"] ?? "";
    $nsfw = $file["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $file["Privacy"] ?? $y["Privacy"]["DLL"];
    $title = $file["Title"] ?? "Untitles";
    $r = $this->core->Change([[
     "[File.Album]" => $album,
     "[File.Albums]" => json_encode($albums, true),
     "[File.Description]" => base64_encode($description),
     "[File.ID]" => $id,
     "[File.NSFW]" => $nsfw,
     "[File.Privacy]" => $privacy,
     "[File.Title]" => base64_encode($title),
     "[File.Username]" => $username
    ], $this->core->Extension("7c85540db53add027bddeb42221dd104")]);
    $action = $this->core->Element(["button", "Update", [
     "class" => "CardButton SendData",
     "data-form" => ".EditFile$id",
     "data-processor" => base64_encode("v=".base64_encode("File:Save"))
    ]]);
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
   $data = $this->core->FixMissing($data, [
    "AddTo",
    "Added",
    "CARD",
    "back",
    "lPG"
   ]);
   $back = ($data["back"] == 1) ? $this->core->Element([
    "button", "Back to Files", [
     "class" => "GoToParent LI",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $id = $data["ID"] ?? "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The File Identifier or Username are missing."
   ];
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($username)) {
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $attachmentID = base64_encode($t["Login"]["Username"]."-".$id);
    $bl = $this->core->CheckBlocked([$y, "Files", $id]);
    $blockCommand = ($bl == 0) ? "Block" : "Unblock";
    $_File = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("File;$username;$id")
    ]);
    if($_File["Empty"] == 0) {
     $file = $_File["DataModel"];
     $options = $_File["ListItem"]["Options"];
     $r = [
      "Body" => "The File <em>$id</em> could not be found."
     ];
     $accessCode = "Accepted";
     $actions = ($username != $you) ? $this->core->Element([
      "button", $blockCommand, [
       "class" => "Small UpdateButton v2",
       "data-processor" => $options["Block"]
      ]
     ]) : "";
     $addToData = $data["AddTo"] ?? "";
     $addToData = (!empty($addToData)) ? explode(":", base64_decode($addToData)) : [];
     $addToMedia = ($this->core->ID == $username) ? $file["Name"] : $attachmentID;
     /*--$addTo = (!empty($addToData[1])) ? $this->core->Element([
      "button", $addToData[0], [
       "class" => "AddTo v2",
       "data-added" => $data["Added"],
       "data-dlc" => $addToMedia,
       "data-input" => base64_encode($addToData[1])
      ]
     ]) : "";--*/
     $addTo = "";
     $ck = ($this->core->ID == $username && $y["Rank"] == md5("High Command")) ? 1 : 0;
     $actions .= ($ck == 1 || $username == $you) ? $this->core->Element([
      "button", "Delete", [
       "class" => "OpenDialog Small v2",
       "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteFile")."&AID=".$file["AID"]."&ID=$id&ParentView=".$this->core->PlainText([
        "Data" => $data["lPG"],
        "Encode" => 1
       ])."&UN=".base64_encode($username))
      ]
     ]) : "";
     $actions .= $this->core->Element([
      "button", "Download", [
       "class" => "Download Small v2",
       "data-media" => base64_encode(base64_encode("$username/".$file["Name"])),
       "data-view" => base64_encode("v=".base64_encode("File:Download"))
      ]
     ]);
     $actions .= ($ck == 1 || $username == $you) ? $this->core->Element([
      "button", "Edit", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Edit"]
      ]
     ]) : "";
     $fileCheck = $this->core->CheckFileType([$file["EXT"], "Photo"]);
     $nsfw = $file["NSFW"] ?? $y["Privacy"]["NSFW"];
     $setAsProfileImage = "";
     if($nsfw == 0 && $fileCheck == 1) {
      $_Source = $options["Source"];
      $httpResponse = $this->core->RenderHTTPResponse($_Source);
      if($httpResponse != 200) {
       $_Source = $this->core->efs."D.jpg";
      }
      list($height, $width) = getimagesize($_Source);
      $_Size = ($height <= ($width / 1.5) || $height == $width) ? 1 : 0;
      $cp = ($height <= ($width / 1.5)) ? "Cover Photo" : "Profile Picture";
      $type = ($height <= ($width / 1.5)) ? "CoverPhoto" : "ProfilePicture";
      $type = base64_encode($type);
      $setAsProfileImage = ($_Size == 1) ? $this->core->Element([
       "button", "Set as Your $cp", [
        "class" => "OpenDialog Disable v2",
        "data-view" => base64_encode("v=".base64_encode("File:SaveProfileImage")."&DLC=$attachmentID&FT=$type")
       ]
      ]) : "";
     }
     $nsfw = ($nsfw == 1) ? "Adults Only" : "Kid-Friendly";
     $share = ($t["Login"]["Username"] == $you || $file["Privacy"] == md5("Public")) ? 1 : 0;
     $share = ($share == 1) ? $this->core->Element([
      "button", "Share", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Share"]
     ]]) : "";
     $r = $this->core->Change([[
      "[File.Actions]" => $actions,
      "[File.AddTo]" => $addTo,
      "[File.Back]" => $back,
      "[File.Conversation]" => $this->core->Change([[
       "[Conversation.CRID]" => $id,
       "[Conversation.CRIDE]" => base64_encode($id),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->core->Extension("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[File.Description]" => $file["Description"],
      "[File.Extension]" => $file["EXT"],
      "[File.ID]" => $id,
      "[File.Illegal]" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("File;".$t["Login"]["Username"].";$id")),
      "[File.Modified]" => $this->core->TimeAgo($file["Modified"]),
      "[File.Name]" => $file["Name"],
      "[File.NSFW]" => $nsfw,
      "[File.Preview]" => $_File["ListItem"]["Attachments"],
      "[File.SetAsProfileImage]" => $setAsProfileImage,
      "[File.Share]" => $share,
      "[File.Title]" => $_File["ListItem"]["Title"],
      "[File.Type]" => $file["Type"],
      "[File.Uploaded]" => $this->core->TimeAgo($file["Timestamp"]),
      "[File.Votes]" => $options["Vote"]
     ], $this->core->Extension("c31701a05a48069702cd7590d31ebd63")]);
    }
   }
   $r = ($data["CARD"] == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
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
   $data = $this->core->FixMissing($data, [
    "Description",
    "ID",
    "Title"
   ]);
   $id = $data["ID"];
   $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
   $privacy = $data["NSFW"] ?? $y["Privacy"]["DLL"];
   $r = [
    "Body" => "The File Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $username = $data["Username"] ?? $you;
    $fileSystem = $this->core->Data("Get", ["fs", md5($username)]) ?? [];
    $files = ($this->core->ID == $username) ? $this->core->Data("Get", [
     "app",
     "fs"
    ]) : $fileSystem["Files"];
    $now = $this->core->timestamp;
    $file = $files[$id] ?? [];
    $file["AID"] = $data["Album"] ?? md5("unsorted");
    $file["Created"] = $files[$id]["Created"] ?? $now;
    $file["Description"] = $data["Description"];
    $file["Illegal"] = $files[$id]["Illegal"] ?? 0;
    $file["Modified"] = $now;
    $file["NSFW"] = $nsfw;
    $file["Privacy"] = $privacy;
    $file["Title"] = $data["Title"];
    $files[$id] = $file;
    if($this->core->ID == $username) {
     $this->core->Data("Save", ["app", "fs", $files]);
    } else {
     $fileSystem["Files"] = $files;
     $this->core->Data("Save", ["fs", md5($you), $fileSystem]);
    }
    $this->core->Statistic("Edit Media");
    $r = [
     "Body" => "The file <em>".$file["Title"]."</em> was updated.<br/>",
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
    "Success" => "CloseCard"
   ]);
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $acknowledge = $this->Element(["button", "Okay", [
    "class" => "CloseDialog v2 v2w"
   ]]);
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $parentView = $data["ParentView"] ?? "";
   $r = "The File Identifier is missing.";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = "The PINs do not match.";
   } elseif($this->core->ID == $you) {
    $r = "You must be signed in to continue.";
   } elseif(!empty($id) && !empty($parentView)) {
    $_ID = explode("-", base64_decode($id));
    $accessCode = "Accepted";
    $files = $_FileSystem["Files"] ?? [];
    $id = $_ID[1];
    $username = $_ID[0];
    $fileSystem = $this->core->Data("Get", ["fs", md5($username)]) ?? [];
    $files = $fileSystem["Files"] ?? [];
    $files = ($this->core->ID == $username) ? $this->core->Data("Get", [
     "app",
     "fs"
    ]) : $files;
    $file = $files[$id] ?? [];
    $newFiles = [];
    $points = $this->core->config["PTS"]["DeleteFile"];
    $r = "The File <strong>#$id</strong> could not be found.";
    if(!empty($file["AID"])) {
     $albumID = $file["AID"];
     $albums = $fileSystem["Albums"] ?? [];
     foreach($files as $key => $value) {
      if($id != $value["ID"]) {
       $newFiles[$key] = $value;
      } else {
       $_Name = $value["Name"] ?? "";
       $coverPhoto = $albums[$albumID]["ICO"] ?? "";
       $baseName = explode(".", $_Name)[0];
       if($this->core->ID != $username) {
        if($_Name == $coverPhoto && $username == $you) {
         $albums[$albumID]["ICO"] = "";
        }
       }
       $this->view(base64_encode("Conversation:SaveDelete"), [
        "Data" => ["ID" => $key]
       ]);
       $this->core->Data("Purge", ["votes", $key]);
       unlink($this->core->DocumentRoot."/efs/$username/thumbnail.$baseName.png");
       unlink($this->core->DocumentRoot."/efs/$username/$_Name");
      }
     } if($this->core->ID == $username) {
      $this->core->Data("Save", ["app", "fs", $newFiles]);
     } else {
      $fileSystem["Albums"] = $albums;
      $fileSystem["Files"] = $newFiles;
      $y["Points"] = $y["Points"] + $points;
      $this->core->Data("Save", ["fs", md5($you), $fileSystem]);
      $this->core->Data("Save", ["mbr", md5($you), $y]);
     }
     $acknowledge = $this->core->Element(["button", "Okay", [
      "class" => "GoToParent dBC v2 v2w",
      "data-type" => $parentView
     ]]);
     $r = ($accessCode == "Accepted") ? "The File was deleted." : $r;
    }
   }
   $header = ($accessCode == "Denied") ? "Error" : "Done";
   $r = [
    "Body" => $r,
    "Header" => $header,
    "Options" => [
     $acknowledge
    ]
   ];
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
  function SaveProfileImage(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"];
   $file = $data["DLC"] ?? "";
   $type = $data["FT"] ?? "";
   $r = [
    "Body" => "The Photo type is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($file) && !empty($type)) {
    $accessCode = "Accepted";
    $type = base64_decode($type);
    $cp = ($type == "CoverPhoto") ? "Cover Photo" : "Profile Picture";
    $dbi = explode("-", base64_decode($file));
    if(!empty($dbi[0]) && !empty($dbi[1])) {
     $t = $this->core->Member($dbi[0]);
     $fs = $this->core->Data("Get", [
      "fs",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $image = $dbi[0]."/".$fs["Files"][$dbi[1]]["Name"];
     $y["Personal"][$type] = base64_encode($image);
    }
    $r = [
     "Body" => "The Photo was set as your $cp.<br/>".json_encode($y["Personal"], true),
     "Header" => "Done"
    ];
    #$this->core->Data("Save", ["mbr", md5($you), $y]);
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
  function SaveUpload(array $a) {
   $_Failed = [];
   $_Passed = [];
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $err = "Internal Error";
   $id = $data["AID"] ?? md5("unsorted");
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Failed" => $_Failed,
     "MSG" => "You must be signed in to upload media.",
     "Passed" => $_Passed
    ];
   } elseif(empty($data["AID"]) || empty($username)) {
    $r = [
     "Failed" => $_Failed,
     "MSG" => "You don't have permission to access this view.",
     "Passed" => $_Passed
    ];
   } else {
    header("Content-Type: application/json");
    $_FileSystem = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
    $_DLC = $this->core->config["XFS"]["FT"] ?? [];
    $username = base64_decode($username);
    $_HC = ($this->core->ID == $username && $y["Rank"] == md5("High Command")) ? 1 : 0;
    $albumID = $data["AID"] ?? base64_encode(md5("unsorted"));
    $albumID = base64_decode($albumID);
    $albums = $_FileSystem["Albums"] ?? [];
    $files = $_FileSystem["Files"] ?? [];
    if($_HC == 1) {
     $files = $this->core->Data("Get", ["app", "fs"]) ?? [];
    }
    $now = $this->core->timestamp;
    $nsfw = $data["NSFW"] ?? base64_encode($y["Privacy"]["NSFW"]);
    $nsfw = base64_decode($nsfw);
    $privacy = $data["Privacy"] ?? base64_encode($y["Privacy"]["DLL"]);
    $privacy = base64_decode($privacy);
    $root = $_SERVER["DOCUMENT_ROOT"]."/efs/$username/";
    $uploads = $a["Files"] ?? [];
    $xfsLimits = $this->core->config["XFS"]["limits"] ?? [];
    $xfsLimit = str_replace(",", "", $xfsLimits["Total"]);
    $xfsUsage = 0;
    foreach($files as $key => $info) {
     $size = $info["Size"] ?? 0;
     $xfsUsage = $xfsUsage + $size;
    }
    $xfsUsage = str_replace(",", "", $this->core->ByteNotation($xfsUsage));
    $ck = ($_HC == 1 || $xfsUsage < $xfsLimit) ? 1 : 0;
    $ck = $y["Subscriptions"]["XFS"]["A"] ?? $ck;
    $allowed = array_merge($_DLC["A"], $_DLC["D"], $_DLC["P"], $_DLC["V"]);
    foreach($uploads["name"] as $key => $value) {
     $n = $uploads["name"][$key];
     $ext = explode(".", $n);
     $ext = strtolower(end($ext));
     $ck = ($_HC == 1 || $ck == 1) ? 1 : 0;
     $ck2 = (in_array($ext, $allowed) && $uploads["error"][$key] == 0) ? 1 : 0;
     $id = md5("$you-$n-$now");
     $mime = $uploads["type"][$key];
     $name = "$id.$ext";
     $size = $this->core->ByteNotation($uploads["size"][$key]);
     $size2 = str_replace(",", "", $size);
     $tmp = $uploads["tmp_name"][$key];
     if(in_array($ext, $_DLC["A"])) {
      $ck3 = ($size2 < $xfsLimits["Audio"]) ? 1 : 0;
      $type = $this->core->config["XFS"]["FT"]["_FT"][0];
     } elseif(in_array($ext, $_DLC["P"])) {
      $ck3 = ($size2 < $xfsLimits["Images"]) ? 1 : 0;
      $type = $this->core->config["XFS"]["FT"]["_FT"][2];
     } elseif(in_array($ext, $_DLC["D"])) {
      $ck3 = ($size2 < $xfsLimits["Documents"]) ? 1 : 0;
      $type = $this->core->config["XFS"]["FT"]["_FT"][1];
     } elseif(in_array($ext, $_DLC["V"])) {
      $ck3 = ($size2 < $xfsLimits["Videos"]) ? 1 : 0;
      $type = $this->core->config["XFS"]["FT"]["_FT"][3];
     } else {
      $ck3 = ($size2 < $xfsLimits["Documents"]) ? 1 : 0;
      $type = $this->core->config["XFS"]["FT"]["_FT"][1];
     }
     $fileCheck = [
      "Checks" => [
       "AdministratorClearance" => $_HC,
       "Album" => $id,
       "File" => [
        "Clearance" => $ck2,
        "Data" => $uploads["name"],
        "Name" => $name,
        "Limits" => [
         "Categories" => [
          "Audio" => $xfsLimits["Audio"],
          "Documents" => $xfsLimits["Documents"],
          "Images" => $xfsLimits["Images"],
          "Videos" => $xfsLimits["Videos"]
         ],
         "Clearance" => $ck3,
         "Size" => $size2,
         "Totals" => [$xfsUsage, $xfsLimit]
        ],
        "Size" => $size,
        "Type" => $type
       ],
       "MemberClearance" => $ck,
       "Subscription" => $y["Subscriptions"]["XFS"]["A"]
      ],
      "UploadErrorStatus" => $uploads["error"][$key],
      "TemporaryName" => $uploads["tmp_name"][$key]
     ];
     if($ck == 0 || $ck2 == 0 || $ck3 == 0) {
      if(!in_array($ext, $allowed)) {
       $err = "Invalid file type";
      } elseif($ck == 0) {
       $err = "Forbidden";
      } elseif($ck2 == 0) {
       $err = "File Clearance failed";
      } elseif($ck3 == 0) {
       $err = "File storage limit exceeded";
      } elseif($xfsUsage > $xfsLimit) {
       $err = "Total storage limit exceeded";
      }
      array_push($_Failed, [$uploads["name"][$key], $err, $fileCheck]);
     } else {
      if(!move_uploaded_file($tmp, $root.basename($name))) {
       array_push($fileCheck, "Failed to move $name to your library.");
       array_push($_Failed, [$uploads["name"][$key], $err, $fileCheck]);
      } else {
       $accessCode = "Accepted";
       $file = [
        "AID" => $albumID,
        "Description" => "",
        "EXT" => $ext,
        "ID" => $id,
        "MIME" => $mime,
        "Modified" => $now,
        "Name" => $name,
        "NSFW" => $nsfw,
        "Privacy" => $privacy,
        "Size" => $size,
        "Title" => $name,
        "Timestamp" => $now,
        "Type" => $type
       ];
       $files[$id] = $file;
       if($_HC == 1) {
        $files[$id]["UN"] = $you;
        $this->core->Data("Save", ["app", "fs", $files]);
       } else {
        $_FileSystem = $_FileSystem ?? [];
        $_FileSystem["Albums"] = $albums;
        $_FileSystem["Files"] = $files;
        if(in_array($ext, $this->core->config["XFS"]["FT"]["P"])) {
         $thumbnail = $this->core->Thumbnail([
          "File" => $name,
          "Username" => $you
         ])["AlbumCover"] ?? $name;
         $_FileSystem["Albums"][$albumID]["ICO"] = $thumbnail;
        }
        $_FileSystem["Albums"][$albumID]["Modified"] = $now;
        $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
        $this->core->Data("Save", ["fs", md5($you), $_FileSystem]);
        $this->core->Data("Save", ["mbr", md5($you), $y]);
       }
       array_push($_Passed, [
        "HTML" => $this->core->Element([
         "div", $this->core->GetAttachmentPreview([
          "DLL" => $file,
          "T" => $username,
          "Y" => $you
         ]), [
          "class" => "InnerMargin Medium"
         ]
        ]),
        "Raw" => $file
       ]);
      }
     }
    }
    $r = [
     "Data" => $data,
     "Failed" => $_Failed,
     "Passed" => $_Passed
    ];
    $this->core->Statistic("Upload");
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Upload(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $albumID = $data["AID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($albumID)) {
    $_HC = ($y["Rank"] == md5("High Command")) ? 1 : 0;
    $username = $data["UN"] ?? $you;
    $fileSystem = $this->core->Data("Get", ["fs", md5($username)]) ?? [];
    $files = $fileSystem["Files"] ?? [];
    $xfsLimit = $this->core->config["XFS"]["limits"]["Total"] ?? 0;
    $xfsLimit = $xfsLimit."MB";
    $xfsUsage = 0;
    foreach($files as $key => $value) {
     $xfsUsage = $xfsUsage + $value["Size"];
    }
    $xfsUsage = $this->core->ByteNotation($xfsUsage)."MB";
    $limit = $this->core->Change([["MB" => "", "," => ""], $xfsLimit]);
    $r = [
     "Body" => "You have reached your upload limit. You have used $xfsUsage and exceeded the limit of $xfsLimit."
    ];
    $used = $this->core->Change([["MB" => "", "," => ""], $xfsUsage]);
    $uploadsAllowed = $y["Subscriptions"]["XFS"]["A"] ?? 0;
    $uploadsAllowed = ($_HC == 1 || $used < $limit) ? 1 : $uploadsAllowed;
    if(!empty($username) && $uploadsAllowed == 1) {
     $ck = ($_HC == 1 && $this->core->ID == $username) ? 1 : 0;
     $ck2 = ($username == $you) ? 1 : 0;
     $files = ($this->core->ID == $username) ? $this->core->Data("Get", [
      "app",
      "fs"
     ]) : $files;
     $r = [
      "Body" => "You do not have permission to upload files to $username's Library.",
      "Header" => "Forbidden"
     ];
     if($ck == 1 || $ck2 == 1) {
      $accessCode = "Accepted";
      $limit = ($ck == 1 || $y["Subscriptions"]["Artist"]["A"] == 1) ? "You do not have a cumulative upload limit" : "Your cumulative file upload limit is $xfsLimit";
      $options = "<input name=\"UN\" type=\"hidden\" value=\"$username\"/>\r\n";
      if($ck == 1) {
       $options .= "<input name=\"AID\" type=\"hidden\" value=\"".md5("unsorted")."\"/>\r\n";
       $options .= "<input name=\"NSFW\" type=\"hidden\" value=\"0\"/>\r\n";
       $options .= "<input name=\"Privacy\" type=\"hidden\" value=\"".md5("public")."\"/>\r\n";
       $title = "System Media Library";
      } elseif($ck2 == 1) {
       $options .= "<input name=\"AID\" type=\"hidden\" value=\"$albumID\"/>\r\n";
       $options .= "<input name=\"NSFW\" type=\"hidden\" value=\"".$y["Privacy"]["NSFW"]."\"/>\r\n";
       $options .= "<input name=\"Privacy\" type=\"hidden\" value=\"".$y["Privacy"]["Posts"]."\"/>\r\n";
       $title = $fileSystem["Albums"][$albumID]["Title"] ?? "Unsorted";
      }
      $r = [
       "Front" => $this->core->Change([[
        "[Upload.Limit]" => $limit,
        "[Upload.Options]" => $options,
        "[Upload.Processor]" => base64_encode("v=".base64_encode("File:SaveUpload")),
        "[Upload.Title]" => $title
       ], $this->core->Extension("bf6bb3ddf61497a81485d5eded18e5f8")])
      ];
     }
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
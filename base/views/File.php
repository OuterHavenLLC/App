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
    $passPhrase = $file["PassPhrase"] ?? "";
    $privacy = $file["Privacy"] ?? $y["Privacy"]["DLL"];
    $title = $file["Title"] ?? "Untitles";
    $r = $this->core->Change([[
     "[File.Album]" => $album,
     "[File.Albums]" => json_encode($albums, true),
     "[File.Description]" => base64_encode($description),
     "[File.ID]" => $id,
     "[File.NSFW]" => $nsfw,
     "[File.PassPhrase]" => base64_encode($passPhrase),
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
    "AddTopMargin" => "0",
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
   $addTo = $data["AddTo"] ?? "";
   $card = $data["CARD"] ?? 0;
   $parentView = $data["ParentView"] ?? "Files";
   $back = (!empty($parentView)) ? $this->core->Element([
    "button", "Back to Files", [
     "class" => "GoToParent LI",
     "data-type" => $parentView
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
    $attachmentID = $t["Login"]["Username"]."-".$id;
    $bl = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
    $blockCommand = ($bl == 0) ? "Block" : "Unblock";
    $_File = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("File;$username;$id"),
     "ParentPage" => $parentView
    ]);
    if($_File["Empty"] == 0) {
     $accessCode = "Accepted";
     $file = $_File["DataModel"];
     $passPhrase = $file["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $r = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "ParentPage" => "Files",
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_File["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "Added" => $added,
        "AddTo" => $addTo,
        "SecureKey" => base64_encode($passPhrase),
        "ID" => $id,
        "ParentView" => $parentView,
        "UN" => $username,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("File:Home")
       ], true))
      ]]);
      $r = $this->core->RenderView($r);
     } elseif($verifyPassPhrase == 1) {
      $accessCode = "Denied";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $r = $this->core->Element(["p", "The Key is missing."]);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $r = $this->core->Element(["p", "The Keys do not match."]);
      } else {
       $accessCode = "Accepted";
       $r = $this->view(base64_encode("File:Home"), ["Data" => [
        "Added" => $added,
        "AddTo" => $addTo,
        "ID" => $id,
        "ParentView" => "Files",
        "UN" => $username,
        "ViewProtectedContent" => 1
       ]]);
       $r = $this->core->RenderView($r);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $accessCode = "Accepted";
      $options = $_File["ListItem"]["Options"];
      $actions = ($username != $you) ? $this->core->Element([
       "button", $blockCommand, [
        "class" => "Small UpdateButton v2",
        "data-processor" => $options["Block"]
       ]
      ]) : "";
      $ck = ($this->core->ID == $username && $y["Rank"] == md5("High Command")) ? 1 : 0;
      $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
      $addToMedia = ($ck == 1) ? $file["Name"] : $attachmentID;
      $actions .= (!empty($addToData)) ? $this->core->Element([
       "button", "Attach", [
        "class" => "Attach Small v2",
        "data-input" => base64_encode($addToData[1]),
        "data-media" => base64_encode($addToMedia)
       ]
      ]) : "";
      $actions .= ($ck == 1 || $username == $you) ? $this->core->Element([
       "button", "Delete", [
        "class" => "GoToView Small v2",
        "data-type" => "Media$id;".$options["Delete"]
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
       list($width, $height) = getimagesize($_Source);
       $_Size = ($height <= ($width / 1.5) || $height == $width) ? 1 : 0;
       $cp = ($height <= ($width / 1.5)) ? "Cover Photo" : "Profile Picture";
       $type = ($height <= ($width / 1.5)) ? "CoverPhoto" : "ProfilePicture";
       $type = base64_encode($type);
       $setAsProfileImage = ($_Size == 1) ? $this->core->Element([
        "button", "Set as Your $cp", [
         "class" => "OpenDialog Disable v2",
         "data-view" => base64_encode("v=".base64_encode("File:SaveProfileImage")."&DLC=".base64_encode($attachmentID)."&FT=$type")
        ]
       ]) : "";
      }
      $nsfw = ($nsfw == 1) ? "Adults Only" : "Kid-Friendly";
      $share = ($file["Privacy"] == md5("Public") || $t["Login"]["Username"] == $you) ? 1 : 0;
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
       "[File.Illegal]" => $options["Report"],
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
   }
   $r = ($card == 1) ? [
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
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Purge(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Media File Identifier is missing."
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
    $_ID = explode("-", base64_decode($id));
    $_Name = "Unknown";
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
    $r = $this->core->Element([
     "p", "The Media File <strong>$id</strong> could not be found."
    ]);
    if(!empty($file["ID"])) {
     $albumID = $file["AID"];
     $albums = $fileSystem["Albums"] ?? [];
     foreach($files as $key => $value) {
      if($id != $value["ID"]) {
       $newFiles[$key] = $value;
      } else {
       $_Database = ($this->core->ID == $username) ? "CoreMedia" : "Media";
       $_Name = $value["Name"] ?? $_Name;
       $coverPhoto = $albums[$albumID]["ICO"] ?? "";
       $baseName = explode(".", $_Name)[0];
       $sql = New SQL($this->core->cypher->SQLCredentials());
       $sql->query("DELETE FROM $_Database WHERE Media_ID=:ID", [
        ":ID" => $id
       ]);
       $sql->execute();
       if($this->core->ID != $username) {
        if($_Name == $coverPhoto && $username == $you) {
         $albums[$albumID]["ICO"] = "";
        }
       }
       $conversation = $this->core->Data("Get", ["conversation", $key]);
       if(!empty($conversation)) {
        $conversation["Purge"] = 1;
        $this->core->Data("Save", ["conversation", $key, $conversation]);
       }
       $mediaFile = $this->core->DocumentRoot."/efs/$username/$_Name";
       $translations = $this->core->Data("Get", ["translate", $key]);
       if(!empty($translations)) {
        $translations["Purge"] = 1;
        $this->core->Data("Save", ["translate", $key, $translations]);
       }
       $votes = $this->core->Data("Get", ["votes", $key]);
       if(!empty($votes)) {
        $votes["Purge"] = 1;
        $this->core->Data("Save", ["votes", $key, $votes]);
       }
       $thumbnail = $this->core->DocumentRoot."/efs/$username/thumbnail.$baseName.png";
       if(file_exists($mediaFile) || is_dir($mediaFile)) {
        unlink($mediaFile);
       } if(file_exists($thumbnail) || is_dir($thumbnail)) {
        unlink($thumbnail);
       }
      }
     } if($this->core->ID == $username) {
      $sql = New SQL($this->core->cypher->SQLCredentials());
      $sql->query("DELETE FROM CoreMedia WHERE Media_ID=:ID", [
       ":ID" => $id
      ]);
      $sql->execute();
      $this->core->Data("Save", ["app", "fs", $newFiles]);
     } else {
      $fileSystem["Albums"] = $albums;
      $fileSystem["Files"] = $newFiles;
      $y["Points"] = $y["Points"] + $points;
      $this->core->Data("Save", ["fs", md5($you), $fileSystem]);
      $this->core->Data("Save", ["mbr", md5($you), $y]);
     }
     $r = $this->core->Element([
      "p", "The Media File <em>$_Name</em> was deleted.",
      ["class" => "CenterText"]
     ]).$this->core->Element(["button", "Okay", [
      "class" => "GoToParent v2 v2w",
      "data-type" => "Files"
     ]]);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
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
    $file["Description"] = $data["Description"] ?? "";
    $file["Illegal"] = $files[$id]["Illegal"] ?? 0;
    $file["Modified"] = $now;
    $file["NSFW"] = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
    $file["PassPhrase"] = $data["PassPhrase"] ?? "";
    $file["Privacy"] = $data["Privacy"] ?? $y["Privacy"]["DLL"];
    $file["Purge"] = $file["Purge"] ?? 0;
    $file["Title"] = $data["Title"] ?? "Untitled";
    $files[$id] = $file;
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $query = "REPLACE INTO Forums(
     Media_Created,
     Media_Description,
     Media_ID,
     Media_NSFW,
     Media_Privacy,
     Media_Title,
     Media_Username
    ) VALUES(
     :Created,
     :Description,
     :ID,
     :NSFW,
     :Privacy,
     :Title,
     :Username
    )";
    $sql->query($query, [
     ":Created" => $created,
     ":Description" => $file["Description"],
     ":ID" => $id,
     ":NSFW" => $file["NSFW"],
     ":Privacy" => $file["Privacy"],
     ":Title" => $file["Title"],
     ":Username" => $username
    ]);
    $sql->execute();
    if($this->core->ID == $username) {
     #$this->core->Data("Save", ["app", "fs", $files]);
    } else {
     $fileSystem["Files"] = $files;
     #$this->core->Data("Save", ["fs", md5($you), $fileSystem]);
    }
    #$this->core->Statistic("Edit Media");
    $r = [
     "Body" => "The file <em>".$file["Title"]."</em> was updated.<br/>",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function SaveProfileImage(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"];
   $media = $data["DLC"] ?? "";
   $r = [
    "Body" => "The Photo type is missing."
   ];
   $type = $data["FT"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($media) && !empty($type)) {
    $accessCode = "Accepted";
    $media = explode("-", base64_decode($media));
    $type = base64_decode($type);
    $imageType = ($type == "CoverPhoto") ? "Cover Photo" : "Profile Picture";
    if(!empty($media[0]) && !empty($media[1])) {
     $t = $this->core->Member($media[0]);
     $fs = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]);
     $image = $fs["Files"][$media[1]]["Name"] ?? "";
     if(!empty($image)) {
      $newImage = $media[0]."/$image";
      $newImage = ($type == "CoverPhoto") ? $media[0]."-".$media[1] : $newImage;
      $y["Personal"][$type] = base64_encode($newImage);
      $this->core->Data("Save", ["mbr", md5($you), $y]);
     }
    }
    $r = [
     "Body" => "The Photo was set as your $imageType.",
     "Header" => "Done"
    ];
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
  function SaveUpload(array $a) {
   $_Failed = [];
   $_Passed = [];
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $albumID = $data["AID"] ?? $this->core->AESencrypt(md5("unsorted"));
   $albumID = $this->core->AESdecrypt($albumID);
   $err = "Internal Error";
   $username = $data["UN"] ?? $this->core->AESencrypt("");
   $username = $this->core->AESdecrypt($username);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Failed" => $_Failed,
     "MSG" => "You must be signed in to upload media.",
     "Passed" => $_Passed
    ];
   } elseif(empty($data["AID"]) || empty($data["UN"])) {
    $r = [
     "Failed" => $_Failed,
     "MSG" => "You don't have permission to access this view. ($albumID, $username, ".$y["Rank"].")",
     "Passed" => $_Passed
    ];
   } else {
    header("Content-Type: application/json");
    $_FileSystem = $this->core->Data("Get", ["fs", md5($you)]);
    $_DLC = $this->core->config["XFS"]["FT"] ?? [];
    if($this->core->ID == $username && $y["Rank"] != md5("High Command")) {
     $r = [
      "Failed" => $_Failed,
      "MSG" => "You don't have permission to upload to this Media Library.",
      "Passed" => $_Passed
     ];
    } else {
     $_HC = ($this->core->ID == $username && $y["Rank"] == md5("High Command")) ? 1 : 0;
     $allowed = array_merge($_DLC["A"], $_DLC["D"], $_DLC["P"], $_DLC["V"]);
     $albums = $_FileSystem["Albums"] ?? [];
     $files = $_FileSystem["Files"] ?? [];
     if($_HC == 1) {
      $files = $this->core->Data("Get", ["app", "fs"]);
     }
     $now = $this->core->timestamp;
     $nsfw = $data["NSFW"] ?? $this->core->AESencrypt($y["Privacy"]["NSFW"]);
     $nsfw = $this->core->AESdecrypt($nsfw);
     $privacy = $data["Privacy"] ?? $this->core->AESencrypt($y["Privacy"]["DLL"]);
     $privacy = $this->core->AESdecrypt($privacy);
     $root = $this->core->DocumentRoot."/efs/$username/";
     $uploads = $a["Files"] ?? [];
     $uploadsAllowed = $y["Subscriptions"]["Artist"]["A"] ?? 0;
     $uploadsAllowed = (($uploadsAllowed + $y["Subscriptions"]["VIP"]["A"]) > 0) ? 1 : 0;
     $limits = $this->core->config["XFS"]["limits"] ?? [];
     $limit = str_replace(",", "", $limits["Total"]);
     $usage = 0;
     foreach($files as $key => $info) {
      $size = $info["Size"] ?? 0;
      $usage = $usage + $size;
     }
     $usage = str_replace(",", "", $this->core->ByteNotation($usage));
     $ck = ($_HC == 1 || $usage < $limit) ? 1 : $uploadsAllowed;
     for($key = 0; $key < count($uploads); $key++) {
      $n = $uploads["name"][$key] ?? "";
      if(!empty($n)) {
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
        $ck3 = ($size2 < $limits["Audio"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][0];
       } elseif(in_array($ext, $_DLC["P"])) {
        $ck3 = ($size2 < $limits["Images"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][2];
       } elseif(in_array($ext, $_DLC["D"])) {
        $ck3 = ($size2 < $limits["Documents"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][1];
       } elseif(in_array($ext, $_DLC["V"])) {
        $ck3 = ($size2 < $limits["Videos"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][3];
       } else {
        $ck3 = ($size2 < $limits["Documents"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][1];
       }
       $fileCheck = [
        "Checks" => [
         "AdministratorClearance" => $_HC,
         "Album" => $id,
         "File" => [
          "Clearance" => $ck2,
          "Data" => $uploads["name"][$key],
          "Name" => $name,
          "Limits" => [
           "Categories" => [
            "Audio" => $limits["Audio"],
            "Documents" => $limits["Documents"],
            "Images" => $limits["Images"],
            "Videos" => $limits["Videos"]
           ],
           "Clearance" => $ck3,
           "Size" => $size2,
           "Totals" => [$usage, $limit]
          ],
          "Size" => $size,
          "Type" => $type
         ],
         "MemberClearance" => $ck,
         "MemberIsSubscribed" => [
          "Artist" => $y["Subscriptions"]["Artist"]["A"],
          "VIP" => $y["Subscriptions"]["VIP"]["A"]
         ]
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
        } elseif($usage > $limit) {
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
         $database = ($_HC == 1) ? "CoreMedia" : "Media";
         $sql = New SQL($this->core->cypher->SQLCredentials());
         $query = "REPLACE INTO $database(
          Media_Created,
          Media_Description,
          Media_ID,
          Media_NSFW,
          Media_Privacy,
          Media_Title,
          Media_Username
         ) VALUES(
          :Created,
          :Description,
          :ID,
          :NSFW,
          :Privacy,
          :Title,
          :Username
         )";
         $sql->query($query, [
          ":Created" => $now,
          ":Description" => "",
          ":ID" => $id,
          ":NSFW" => $file["NSFW"],
          ":Privacy" => $file["Privacy"],
          ":Title" => $file["Title"],
          ":Username" => $username
         ]);
         $sql->execute();
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
    "AddTopMargin" => "0",
    "JSON" => $r,
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
    $limit = $this->core->config["XFS"]["limits"]["Total"] ?? 0;
    $limit = $limit."MB";
    $usage = 0;
    foreach($files as $key => $value) {
     $usage = $usage + $value["Size"];
    }
    $usage = $this->core->ByteNotation($usage)."MB";
    $limit = $this->core->Change([["MB" => "", "," => ""], $limit]);
    $r = [
     "Body" => "You have reached your upload limit. You have used $usage and exceeded the limit of $limit."
    ];
    $used = $this->core->Change([["MB" => "", "," => ""], $usage]);
    $uploadsAllowed = $y["Subscriptions"]["Artist"]["A"] ?? 0;
    $uploadsAllowed = (($uploadsAllowed + $y["Subscriptions"]["VIP"]["A"]) > 0) ? 1 : 0;
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
      $limit = ($ck == 1 || $y["Subscriptions"]["Artist"]["A"] == 1) ? "You do not have a cumulative upload limit" : "Your cumulative file upload limit is $limit";
      $options = "<input name=\"UN\" type=\"hidden\" value=\"$username\"/>\r\n";
      if($ck == 1) {
       $options .= "<input name=\"AID\" type=\"hidden\" value=\"".md5("unsorted")."\"/>\r\n";
       $options .= "<input name=\"NSFW\" type=\"hidden\" value=\"0\"/>\r\n";
       $options .= "<input name=\"Privacy\" type=\"hidden\" value=\"".md5("Public")."\"/>\r\n";
       $title = "<em>".$this->core->config["App"]["Name"]."</em> Media Library";
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
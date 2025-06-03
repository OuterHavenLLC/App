<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol.$_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
 Class File extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Download(array $data): string {
   $data = $data["Data"] ?? [];
   $media = $data["FilePath"] ?? "";
   $mediaLink = $this->core->efs.base64_decode($media);
   if(empty($mediaPath) || readfile($mediaLink)) {
    return $this->core->JSONResponse([
     "Dialog" => [
      "Body" => "Media Not Found at <em>$mediaLink</em>."
     ]
    ]);
   } else {
    $mediaLink = $this->core->efs.base64_decode($mediaLink);
    header("Content-Disposition: attachment; filename=".basename($mediaLink));
    header("Content-type: application/x-file-to-save");
    ob_end_clean();
    readfile($mediaLink);
    exit;
   }
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The File Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_DIalog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $username = $data["UN"] ?? base64_encode($you);
    $username = base64_decode($username);
    $mediaSystem = $this->core->Data("Get", ["fs", md5($username)]);
    $medias = ($this->core->ID == $username) ? $this->core->Data("Get", [
     "app",
     "fs"
    ]) : $mediaSystem["Files"];
    $media = $medias[$id] ?? [];
    $albums = [];
    if($this->core->ID != $username) {
     foreach($mediaSystem["Albums"] as $key => $album) {
      $albums[$key] = $album["Title"];
     }
    } else {
     $albums[md5("unsorted")] = "System Media Library";
    }
    $album = $media["AID"] ?? md5("unsorted");
    $description = $media["Description"] ?? "";
    $nsfw = $media["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $media["PassPhrase"] ?? "";
    $privacy = $media["Privacy"] ?? $y["Privacy"]["DLL"];
    $title = $media["Title"] ?? "Untitles";
    $_Card = [
     "Action" => $this->core->Element(["button", "Update", [
      "class" => "CardButton SendData",
      "data-encryption" => "AES",
      "data-form" => ".EditMedia$id",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("File:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[File.ID]" => $id
      ],
      "ExtensionID" => "7c85540db53add027bddeb42221dd104"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".MediaInformation$id",
       [
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
          "name" => "Username",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $username
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Title",
          "placeholder" => "Title",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Title"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($title)
        ],
        [
         "Attributes" => [
          "name" => "Description",
          "placeholder" => "Description"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Description"
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($description)
        ],
        [
         "Attributes" => [
          "name" => "PassPhrase",
          "placeholder" => "Pass Phrase",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Pass Phrase"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($passPhrase)
        ],
        [
         "Attributes" => [],
         "OptionGroup" => $albums,
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Album"
         ],
         "Name" => "Album",
         "Title" => "Album",
         "Type" => "Select",
         "Value" => $album
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".NSFW$id",
       [
        "Filter" => "NSFW",
        "Name" => "NSFW",
        "Title" => "Content Status",
        "Value" => $nsfw
       ]
      ]
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".Privacy$id",
       [
        "Value" => $privacy
       ]
      ]
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The File Identifier or Username are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $card = $data["CARD"] ?? 0;
   $parentView = $data["ParentView"] ?? "";
   $back = (!empty($parentView)) ? $this->core->Element([
    "button", "Back to Files", [
     "class" => "GoToParent LI",
     "data-type" => $parentView
    ]
   ]) : "";
   $id = $data["ID"] ?? "";
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($username)) {
    $_Media = $this->core->GetContentData([
     "ID" => base64_encode("File;$username;$id"),
     "ParentPage" => $parentView
    ]);
    if($_Media["Empty"] == 0) {
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $attachmentID = $t["Login"]["Username"]."-".$id;
     $blocked = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
     $blockCommand = ($blocked == 0) ? "Block" : "Unblock";
     $media = $_Media["DataModel"];
     $passPhrase = $media["PassPhrase"] ?? "";
     $purgeRenderCode = ($t["Login"]["Username"] == $you) ? "PURGE" : "DO NOT PURGE";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "ParentPage" => "Files",
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Media["ListItem"]["Title"]."</em>."),
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
      $_View = $this->core->RenderView($_View);
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = "";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key == $secureKey) {
       $_View = $this->view(base64_encode("File:Home"), ["Data" => [
        "Added" => $added,
        "AddTo" => $addTo,
        "ID" => $id,
        "ParentView" => "Files",
        "UN" => $username,
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View, 1);
       $_Commands = $_View["Commands"];
       $_View = $_View["View"];
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Dialog = "";
      $options = $_Media["ListItem"]["Options"];
      $actions = ($username != $you) ? $this->core->Element([
       "button", "Block", [
        "class" => "Small UpdateButton v2",
        "data-processor" => $options["Block"]
       ]
      ]) : "";
      $check = ($this->core->ID == $username && $y["Rank"] == md5("High Command")) ? 1 : 0;
      $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
      $addToMedia = ($check == 1) ? $media["Name"] : $attachmentID;
      $actions .= (!empty($addToData)) ? $this->core->Element([
       "button", "Attach", [
        "class" => "Attach Small v2",
        "data-input" => base64_encode($addToData[1]),
        "data-media" => base64_encode($addToMedia)
       ]
      ]) : "";
      $actions .= ($check == 1 || $username == $you) ? $this->core->Element([
       "button", "Delete", [
        "class" => "GoToView Small v2",
         "data-encryption" => "AES",
        "data-type" => "Media$id;".$options["Delete"]
       ]
      ]) : "";
      $actions .= $this->core->Element([
       "button", "Download", [
        "class" => "Download Small v2",
        "data-media" => base64_encode(base64_encode("$username/".$media["Name"])),
        "data-view" => base64_encode("v=".base64_encode("File:Download"))
       ]
      ]);
      $actions .= ($check == 1 || $username == $you) ? $this->core->Element([
       "button", "Edit", [
        "class" => "OpenCard Small v2",
         "data-encryption" => "AES",
        "data-view" => $options["Edit"]
       ]
      ]) : "";
      $mediaCheck = $this->core->CheckFileType([$media["EXT"], "Photo"]);
      $nsfw = $media["NSFW"] ?? $y["Privacy"]["NSFW"];
      $setAsProfileImage = "";
      if($nsfw == 0 && $mediaCheck == 1) {
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
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=".base64_encode("File:SaveProfileImage")."&DLC=".base64_encode($attachmentID)."&FT=$type")
        ]
       ]) : "";
      }
      $nsfw = ($nsfw == 1) ? "Adults Only" : "Kid-Friendly";
      $share = ($media["Privacy"] == md5("Public") || $t["Login"]["Username"] == $you) ? 1 : 0;
      $share = ($share == 1) ? $this->core->Element([
       "button", "Share", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Share"]
      ]]) : "";
      $_Commands = [
       [
        "Name" => "UpdateContentAES",
        "Parameters" => [
         ".Conversation$id",
         $this->core->AESencrypt("v=".base64_encode("Conversation:Home")."&CRID=".base64_encode($id)."&LVL=".base64_encode(1))
        ]
       ]
      ];
      $_View = [
       "ChangeData" => [
        "[Media.Actions]" => $actions,
        "[Media.AddTo]" => $addTo,
        "[Media.Back]" => $back,
        "[Media.Block]" => $options["Block"],
        "[Media.Block.Text]" => $blockCommand,
        "[Media.Description]" => $media["Description"],
        "[Media.Extension]" => $media["EXT"],
        "[Media.ID]" => $id,
        "[Media.Modified]" => $this->core->TimeAgo($media["Modified"]),
        "[Media.Name]" => $media["Name"],
        "[Media.NSFW]" => $nsfw,
        "[Media.Report]" => $options["Report"],
        "[Media.Preview]" => $_Media["ListItem"]["Attachments"],
        "[Media.SetAsProfileImage]" => $setAsProfileImage,
        "[Media.Share]" => $share,
        "[Media.Title]" => $_Media["ListItem"]["Title"],
        "[Media.Type]" => $media["Type"],
        "[Media.Uploaded]" => $this->core->TimeAgo($media["Timestamp"]),
        "[Media.Votes]" => $options["Vote"],
        "[PurgeRenderCode]" => $purgeRenderCode
       ],
       "ExtensionID" => "c31701a05a48069702cd7590d31ebd63"
      ];
     }
    }
   }
   $_Card = ($card == 1) ? [
    "Front" => $_View
   ] : "";
   $_View = ($card == 0) ? $_View : "";
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Purge(array $data): string {
   $_View = "";
   $_Dialog = [
    "Body" => "The Media File Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "The Media File <strong>$id</strong> could not be found."
    ];
    $_ID = explode("-", base64_decode($id));
    $_Name = "Unknown";
    $medias = $_MediaStore["Files"] ?? [];
    $id = $_ID[1];
    $username = $_ID[0];
    $mediaSystem = $this->core->Data("Get", ["fs", md5($username)]);
    $medias = $mediaSystem["Files"] ?? [];
    $medias = ($this->core->ID == $username) ? $this->core->Data("Get", [
     "app",
     "fs"
    ]) : $medias;
    $media = $medias[$id] ?? [];
    $newFiles = [];
    $points = $this->core->config["PTS"]["DeleteFile"];
    if(!empty($media["ID"])) {
     $_Dialog = "";
     $albumID = $media["AID"];
     $albums = $mediaSystem["Albums"] ?? [];
     foreach($medias as $key => $value) {
      if($id != $value["ID"]) {
       $newFiles[$key] = $value;
      } else {
       $_Database = ($this->core->ID == $username) ? "CoreMedia" : "Media";
       $_Name = $value["Name"] ?? $_Name;
       $coverPhoto = $albums[$albumID]["CoverPhoto"] ?? "";
       $baseName = explode(".", $_Name)[0];
       $sql = New SQL($this->core->cypher->SQLCredentials());
       $sql->query("DELETE FROM $_Database WHERE Media_ID=:ID", [
        ":ID" => $id
       ]);
       $sql->execute();
       if($this->core->ID != $username) {
        if($_Name == $coverPhoto && $username == $you) {
         $albums[$albumID]["CoverPhoto"] = "";
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
      $mediaSystem["Albums"] = $albums;
      $mediaSystem["Files"] = $newFiles;
      $y["Points"] = $y["Points"] + $points;
      $this->core->Data("Save", ["fs", md5($you), $mediaSystem]);
      $this->core->Data("Save", ["mbr", md5($you), $y]);
     }
     $_View = $this->core->Element([
      "p", "The Media File <em>$_Name</em> was deleted.",
      ["class" => "CenterText"]
     ]).$this->core->Element(["button", "Okay", [
      "class" => "GoToParent v2 v2w",
      "data-type" => "Files"
     ]]);
     $_View = [
      "ChangeData" => [],
      "Extension" => $this->core->AESencrypt($_View)
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The File Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $username = $data["Username"] ?? $you;
    $mediaSystem = $this->core->Data("Get", ["fs", md5($username)]);
    $medias = ($this->core->ID == $username) ? $this->core->Data("Get", [
     "app",
     "fs"
    ]) : $mediaSystem["Files"];
    $now = $this->core->timestamp;
    $media = $medias[$id] ?? [];
    $media["AID"] = $data["Album"] ?? md5("unsorted");
    $media["Created"] = $medias[$id]["Created"] ?? $now;
    $media["Description"] = $data["Description"] ?? "";
    $media["Illegal"] = $medias[$id]["Illegal"] ?? 0;
    $media["Modified"] = $now;
    $media["NSFW"] = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
    $media["PassPhrase"] = $data["PassPhrase"] ?? "";
    $media["Privacy"] = $data["Privacy"] ?? $y["Privacy"]["DLL"];
    $media["Purge"] = $media["Purge"] ?? 0;
    $media["Title"] = $data["Title"] ?? "Untitled";
    $medias[$id] = $media;
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
     ":Description" => $media["Description"],
     ":ID" => $id,
     ":NSFW" => $media["NSFW"],
     ":Privacy" => $media["Privacy"],
     ":Title" => $media["Title"],
     ":Username" => $username
    ]);
    $sql->execute();
    if($this->core->ID == $username) {
     #$this->core->Data("Save", ["app", "fs", $medias]);
    } else {
     $mediaSystem["Files"] = $medias;
     #$this->core->Data("Save", ["fs", md5($you), $mediaSystem]);
    }
    $this->core->Statistic("Edit Media");
    $_Dialog = [
     "Body" => "The file <em>".$media["Title"]."</em> was updated.<br/>",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function SaveProfileImage(array $data): string {
   $_Dialog = [
    "Body" => "The Photo type is missing."
   ];
   $data = $data["Data"] ?? [];
   $media = $data["DLC"] ?? "";
   $type = $data["FT"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($media) && !empty($type)) {
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
    $_Dialog = [
     "Body" => "The Photo was set as your $imageType.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveUpload(array $data): string {
   $_AccessCode = "Denied";
   $_Failed = [];
   $_Passed = [];
   $data = $data["Data"] ?? [];
   $albumID = $data["AID"] ?? $this->core->AESencrypt(md5("unsorted"));
   $albumID = $this->core->AESdecrypt($albumID);
   $error = "Internal Error";
   $username = $data["UN"] ?? $this->core->AESencrypt("");
   $username = $this->core->AESdecrypt($username);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_JSON = [
     "Failed" => $_Failed,
     "MSG" => "You must be signed in to upload media.",
     "Passed" => $_Passed
    ];
   } elseif(empty($data["AID"]) || empty($data["UN"])) {
    $_JSON = [
     "Failed" => $_Failed,
     "MSG" => "You don't have permission to access this view. ($albumID, $username, ".$y["Rank"].")",
     "Passed" => $_Passed
    ];
   } else {
    header("Content-Type: application/json");
    $_MediaStore = $this->core->Data("Get", ["fs", md5($you)]);
    $_DLC = $this->core->config["XFS"]["FT"] ?? [];
    if($this->core->ID == $username && $y["Rank"] != md5("High Command")) {
     $_JSON = [
      "Failed" => $_Failed,
      "MSG" => "You don't have permission to upload to this Media Library.",
      "Passed" => $_Passed
     ];
    } else {
     $_IsHighConmmand = ($this->core->ID == $username && $y["Rank"] == md5("High Command")) ? 1 : 0;
     $allowed = array_merge($_DLC["A"], $_DLC["D"], $_DLC["P"], $_DLC["V"]);
     $albums = $_MediaStore["Albums"] ?? [];
     $medias = $_MediaStore["Files"] ?? [];
     if($_IsHighConmmand == 1) {
      $medias = $this->core->Data("Get", ["app", "fs"]);
     }
     $now = $this->core->timestamp;
     $nsfw = $data["NSFW"] ?? $this->core->AESencrypt($y["Privacy"]["NSFW"]);
     $nsfw = $this->core->AESdecrypt($nsfw);
     $privacy = $data["Privacy"] ?? $this->core->AESencrypt($y["Privacy"]["DLL"]);
     $privacy = $this->core->AESdecrypt($privacy);
     $root = $this->core->DocumentRoot."/efs/$username/";
     $uploads = $data["Files"] ?? [];
     $uploadsAllowed = $y["Subscriptions"]["Artist"]["A"] ?? 0;
     $uploadsAllowed = (($uploadsAllowed + $y["Subscriptions"]["VIP"]["A"]) > 0) ? 1 : 0;
     $limits = $this->core->config["XFS"]["limits"] ?? [];
     $limit = str_replace(",", "", $limits["Total"]);
     $usage = 0;
     foreach($medias as $key => $info) {
      $size = $info["Size"] ?? 0;
      $usage = $usage + $size;
     }
     $usage = str_replace(",", "", $this->core->ByteNotation($usage));
     $check = ($_IsHighConmmand == 1 || $usage < $limit) ? 1 : $uploadsAllowed;
     for($key = 0; $key < count($uploads); $key++) {
      $n = $uploads["name"][$key] ?? "";
      if(!empty($n)) {
       $ext = explode(".", $n);
       $ext = strtolower(end($ext));
       $check = ($_IsHighConmmand == 1 || $check == 1) ? 1 : 0;
       $check2 = (in_array($ext, $allowed) && $uploads["error"][$key] == 0) ? 1 : 0;
       $id = md5("$you-$n-$now");
       $mime = $uploads["type"][$key];
       $name = "$id.$ext";
       $size = $this->core->ByteNotation($uploads["size"][$key]);
       $size2 = str_replace(",", "", $size);
       $tmp = $uploads["tmp_name"][$key];
       if(in_array($ext, $_DLC["A"])) {
        $check3 = ($size2 < $limits["Audio"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][0];
       } elseif(in_array($ext, $_DLC["P"])) {
        $check3 = ($size2 < $limits["Images"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][2];
       } elseif(in_array($ext, $_DLC["D"])) {
        $check3 = ($size2 < $limits["Documents"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][1];
       } elseif(in_array($ext, $_DLC["V"])) {
        $check3 = ($size2 < $limits["Videos"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][3];
       } else {
        $check3 = ($size2 < $limits["Documents"]) ? 1 : 0;
        $type = $this->core->config["XFS"]["FT"]["_FT"][1];
       }
       $mediaCheck = [
        "Checks" => [
         "AdministratorClearance" => $_IsHighConmmand,
         "Album" => $id,
         "File" => [
          "Clearance" => $check2,
          "Data" => $uploads["name"][$key],
          "Name" => $name,
          "Limits" => [
           "Categories" => [
            "Audio" => $limits["Audio"],
            "Documents" => $limits["Documents"],
            "Images" => $limits["Images"],
            "Videos" => $limits["Videos"]
           ],
           "Clearance" => $check3,
           "Size" => $size2,
           "Totals" => [$usage, $limit]
          ],
          "Size" => $size,
          "Type" => $type
         ],
         "MemberClearance" => $check,
         "MemberIsSubscribed" => [
          "Artist" => $y["Subscriptions"]["Artist"]["A"],
          "VIP" => $y["Subscriptions"]["VIP"]["A"]
         ]
        ],
        "UploadErrorStatus" => $uploads["error"][$key],
        "TemporaryName" => $uploads["tmp_name"][$key]
       ];
       if($check == 0 || $check2 == 0 || $check3 == 0) {
        if(!in_array($ext, $allowed)) {
         $error = "Invalid file type";
        } elseif($check == 0) {
         $error = "Forbidden";
        } elseif($check2 == 0) {
         $error = "File Clearance failed";
        } elseif($check3 == 0) {
         $error = "File storage limit exceeded";
        } elseif($usage > $limit) {
         $error = "Total storage limit exceeded";
        }
        array_push($_Failed, [$uploads["name"][$key], $error, $mediaCheck]);
       } else {
        if(!move_uploaded_file($tmp, $root.basename($name))) {
         array_push($mediaCheck, "Failed to move $name to your library.");
         array_push($_Failed, [$uploads["name"][$key], $error, $mediaCheck]);
        } else {
         $_AccessCode = "Accepted";
         $media = [
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
         $medias[$id] = $media;
         if($_IsHighConmmand == 1) {
          $medias[$id]["UN"] = $you;
          $this->core->Data("Save", ["app", "fs", $medias]);
         } else {
          $_MediaStore = $_MediaStore ?? [];
          $_MediaStore["Albums"] = $albums;
          $_MediaStore["Files"] = $medias;
          if(in_array($ext, $this->core->config["XFS"]["FT"]["P"])) {
           $thumbnail = $this->core->Thumbnail([
            "File" => $name,
            "Username" => $you
           ])["AlbumCover"] ?? $name;
           $_MediaStore["Albums"][$albumID]["CoverPhoto"] = $thumbnail;
          }
          $_MediaStore["Albums"][$albumID]["Modified"] = $now;
          $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
          $this->core->Data("Save", ["fs", md5($you), $_MediaStore]);
          $this->core->Data("Save", ["mbr", md5($you), $y]);
         }
         $database = ($_IsHighConmmand == 1) ? "CoreMedia" : "Media";
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
          ":NSFW" => $media["NSFW"],
          ":Privacy" => $media["Privacy"],
          ":Title" => $media["Title"],
          ":Username" => $username
         ]);
         $sql->execute();
         array_push($_Passed, [
          "HTML" => $this->core->Element([
           "button", $this->core->GetAttachmentPreview([
            "DLL" => $media,
            "T" => $username,
            "Y" => $you
           ]), [
            "class" => "Medium OpenCard RoundedLarge Shadowed",
            "data-encryption" => "AES",
            "data-view" => $this->core->AESencrypt("v=".base64_encode("File:Home")."&CARD=1&&ID=$id&UN=$username")
           ]
          ]),
          "Raw" => $media
         ]);
        }
       }
      }
     }
    }
    $_JSON = [
     "Data" => $data,
     "Failed" => $_Failed,
     "Passed" => $_Passed
    ];
    $this->core->Statistic("Upload");
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "JSON" => $_JSON
   ]);
  }
  function Upload(array $data): string {
   $_AccessCode = "Denied";
   $_Card = "";
   $_Dialog = [
    "Body" => "The Album Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $albumID = $data["AID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($albumID)) {
    $_IsHighConmmand = ($y["Rank"] == md5("High Command")) ? 1 : 0;
    $username = $data["UN"] ?? $you;
    $mediaSystem = $this->core->Data("Get", ["fs", md5($username)]);
    $medias = $mediaSystem["Files"] ?? [];
    $limit = $this->core->config["XFS"]["limits"]["Total"] ?? 0;
    $limit = $limit."MB";
    $usage = 0;
    foreach($medias as $key => $value) {
     $usage = $usage + $value["Size"];
    }
    $usage = $this->core->ByteNotation($usage)."MB";
    $limit = $this->core->Change([["MB" => "", "," => ""], $limit]);
    $_Dialog = [
     "Body" => "You have reached your upload limit. You have used $usage and exceeded the limit of $limit."
    ];
    $used = $this->core->Change([["MB" => "", "," => ""], $usage]);
    $uploadsAllowed = $y["Subscriptions"]["Artist"]["A"] ?? 0;
    $uploadsAllowed = (($uploadsAllowed + $y["Subscriptions"]["VIP"]["A"]) > 0) ? 1 : 0;
    $uploadsAllowed = ($_IsHighConmmand == 1 || $used < $limit) ? 1 : $uploadsAllowed;
    if(!empty($username) && $uploadsAllowed == 1) {
     $check = ($_IsHighConmmand == 1 && $this->core->ID == $username) ? 1 : 0;
     $check2 = ($username == $you) ? 1 : 0;
     $medias = ($this->core->ID == $username) ? $this->core->Data("Get", [
      "app",
      "fs"
     ]) : $medias;
     $_Dialog = [
      "Body" => "You do not have permission to upload files to $username's Library.",
      "Header" => "Forbidden"
     ];
     if($check == 1 || $check2 == 1) {
      $limit = ($check == 1 || $y["Subscriptions"]["Artist"]["A"] == 1) ? "You do not have a cumulative upload limit" : "Your cumulative file upload limit is $limit";
      $options = "<input name=\"UN\" type=\"hidden\" value=\"$username\"/>\r\n";
      if($check == 1) {
       $options .= "<input name=\"AID\" type=\"hidden\" value=\"".md5("unsorted")."\"/>\r\n";
       $options .= "<input name=\"NSFW\" type=\"hidden\" value=\"0\"/>\r\n";
       $options .= "<input name=\"Privacy\" type=\"hidden\" value=\"".md5("Public")."\"/>\r\n";
       $title = "<em>".$this->core->config["App"]["Name"]."</em> Media Library";
      } elseif($check2 == 1) {
       $options .= "<input name=\"AID\" type=\"hidden\" value=\"$albumID\"/>\r\n";
       $options .= "<input name=\"NSFW\" type=\"hidden\" value=\"".$y["Privacy"]["NSFW"]."\"/>\r\n";
       $options .= "<input name=\"Privacy\" type=\"hidden\" value=\"".$y["Privacy"]["Posts"]."\"/>\r\n";
       $title = $mediaSystem["Albums"][$albumID]["Title"] ?? "Unsorted";
      }
      $_Card = [
       "Front" => [
        "ChangeData" => [
         "[Upload.Limit]" => $limit,
         "[Upload.Options]" => $options,
         "[Upload.Processor]" => $this->core->AESencrypt("v=".base64_encode("File:SaveUpload")),
         "[Upload.Title]" => $title
        ],
        "ExtensionID" => "bf6bb3ddf61497a81485d5eded18e5f8"
       ]
      ];
      $_Dialog = "";
     }
    }
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
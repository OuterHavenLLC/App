<?php
 Class Album extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data) {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Album Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["AID"] ?? "";
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) || $new == 1) {
    $_Dialog = "";
    $action = ($new == 1) ? "Post" : "Update";
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $fileSystem = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]);
    $id = ($new == 1) ? md5($t["Login"]["Username"].$this->core->timestamp) : $id;
    $album = $fileSystem["Albums"][$id] ?? [];
    $description = $album["Description"] ?? "";
    $nsfw = $album["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $album["PassPhrase"] ?? "";
    $privacy = $album["Privacy"] ?? $y["Privacy"]["Albums"];
    $title = $album["Title"] ?? "";
    $translate = $this->view(base64_encode("WebUI:Attachments"), [
     "ID" => $id,
     "Media" => [
      "Translate" => []
     ]
    ]);
    $header = ($new == 1) ? "Create New Album" : "Edit $title";
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".EditAlbum$id",
      "data-processor" => base64_encode("v=".base64_encode("Album:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Album.Description]" => base64_encode($description),
       "[Album.Header]" => $header,
       "[Album.ID]" => $id,
       "[Album.New]" => $new,
       "[Album.PassPhrase]" => base64_encode($passPhrase),
       "[Album.Title]" => base64_encode($title),
       "[Album.Translate]" => $this->core->RenderView($translate),
       "[Album.Visibility.NSFW]" => $nsfw,
       "[Album.Visibility.Privacy]" => $privacy
      ],
      "ExtensionID" => "760cd577207eb0d2121509d7212038d4"
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data) {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Album Identifier is missing.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $id = $data["AID"] ?? "";
   $b2 = $data["b2"] ?? "Albums";
   $b2 = urlencode($b2);
   $bck = $data["back"] ?? 0;
   $fsLimit = $this->core->config["XFS"]["limits"]["Total"] ?? 0;
   $fsLimit = str_replace(",", "", $fsLimit)."MB";
   $fsUsage = 0;
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $fileSystem = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
   foreach($fileSystem["Files"] as $key => $value) {
    $fsUsage = $fsUsage + $value["Size"];
   }
   $fsUsage = number_format(round($fsUsage / 1000));
   $fsUsage = str_replace(",", "", $fsUsage);
   if(!empty($id) && !empty($username)) {
    $bl = $this->core->CheckBlocked([$y, "Albums", $id]);
    $_Album = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Album;$username;$id"),
     "Owner" => $username
    ]);
    if($_Album["Empty"] == 0) {
     $album = $_Album["DataModel"];
     $passPhrase = $album["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_Card = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Album["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "AddTo" => $addTo,
        "SecureKey" => base64_encode($passPhrase),
        "AID" => $id,
        "UN" => $username,
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Album:Home")
       ], true))
      ]]);
      $_Card = [
       "Front" => $this->core->RenderView($_Card)
      ];
      $_Dialog = "";
      $_View = "";
     } elseif($verifyPassPhrase == 1) {
      $_Dialog = [
       "Body" => "The Key is missing."
      ];
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key != $secureKey) {
       $_Dialog = "";
       $_View = "";
      } else {
       $_Dialog = "";
       $_View = $this->view(base64_encode("Album:Home"), ["Data" => [
        "AddTo" => $addTo,
        "AID" => $id,
        "EmbeddedView" => 1,
        "UN" => $username,
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View);
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_Dialog = "";
      $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
      $embeddedView = $data["EmbeddedView"] ?? 0;
      $options = $_Album["ListItem"]["Options"];
      $t = ($username == $you) ? $y : $this->core->Member($username);
      $secureUsername = base64_encode($t["Login"]["Username"]);
      $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
      $ck2 = $y["Subscriptions"]["XFS"]["A"] ?? 0;
      $ck2 = ($ck2 == 1 || $fsUsage < $fsLimit) ? 1 : 0;
      $actions = (!empty($addToData)) ? $this->core->Element([
       "button", "Attach", [
        "class" => "Attach Small v2",
        "data-input" => base64_encode($addToData[1]),
        "data-media" => base64_encode("Album;$username;$id")
       ]
      ]) : "";
      $actions .= ($ck == 0) ? $this->core->Element([
       "button", "Block", [
        "class" => "Small UpdateButton v2",
        "data-processor" => $options["Block"]
       ]
      ]) : "";
      if($ck == 1) {
       $actions .= ($id != md5("unsorted")) ? $this->core->Element([
        "button", "Delete", [
         "class" => "CloseCard OpenDialog Small v2 v2w",
         "data-view" => $options["Delete"]
        ]
       ]) : "";
       $actions .= $this->core->Element(["button", "Edit", [
        "class" => "OpenCard Small v2 v2w",
        "data-view" => $options["Edit"]
       ]]);
      }
      $actions = ($this->core->ID != $you) ? $actions : "";
      $share = ($t["Login"]["Username"] == $you || $file["Privacy"] == md5("Public")) ? 1 : 0;
      $actions .= ($share == 1) ? $this->core->Element([
       "button", "Share", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Share"]
       ]
      ]) : "";
      $actions .= ($ck == 1 && $ck2 == 1) ? $this->core->Element([
       "button", "Upload", [
        "class" => "OpenCard Small v2",
        "data-view" => $options["Upload"]
       ]
      ]) : "";
      $media = $this->core->Data("Get", ["fs", md5($username)]);
      $coverPhoto = $this->core->PlainText([
       "Data" => "[Media:CP]",
       "Display" => 1
      ]);
      $coverPhotoList = explode(".", $album["ICO"]);
      if(!empty($album["ICO"]) && $coverPhotoList[0] != "thumbnail") {
       $coverPhoto = "$username/";
       $coverPhoto .= $media["Files"][$coverPhotoList[0]]["Name"];
      }
      $_View = [
       "ChangeData" => [
        "[Album.Actions]" => $actions,
        "[Album.CoverPhoto]" => $this->core->CoverPhoto(base64_encode($coverPhoto)),
        "[Album.Created]" => $this->core->TimeAgo($album["Created"]),
        "[Album.Description]" => $album["Description"],
        "[Album.ID]" => $id,
        "[Album.Modified]" => $this->core->TimeAgo($album["Modified"]),
        "[Album.Illegal]" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("Album;$username;$id")),
        "[Album.Owner]" => $t["Personal"]["DisplayName"],
        "[Album.Stream]" => base64_encode("v=".base64_encode("Album:List")."&AID=$id&UN=$secureUsername"),
        "[Album.Title]" => $_Album["ListItem"]["Title"],
        "[Album.Votes]" => $options["Vote"]
       ],
       "ExtensionID" => "91c56e0ee2a632b493451aa044c32515"
      ];
      $_Card = ($embeddedView == 0) ? [
       "Front" => $_View
      ] : "";
      $_View = ($embeddedView === 1) ? $_View : "";
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function List(array $data) {
   $_Commands = "";
   $_Dialog = [
    "Body" => "The requested Album could not be found.",
    "Header" => "Not Found"
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $back = $data["back"] ?? 0;
   $id = $data["AID"] ?? "";
   $b2 = $data["b2"] ?? "Albums";
   $parentPage = $data["lPG"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $username = $data["UN"] ?? $you;
   if(!empty($id)) {
    $_Commands = [
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".AlbumListView$id",
       $this->core->AESencrypt("v=".base64_encode("Search:Containers")."&AID=$id&UN=$username&st=MBR-XFS")
      ]
     ]
    ];
    $_Dialog = "";
    $_View = ($back == 1) ? $this->core->Element(["button", "Back to $b2", [
     "class" => "GoToParent LI head",
     "data-type" => $parentPage
    ]]) : "";
    $_View .= $this->core->Element(["div", NULL, [
     "class" => "AlbumListView$id"
    ]]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Purge(array $data) {
   $_Dialog = [
    "Body" => "The Album Identifier is missing."
   ];
   $_View = "";
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
     "Body" => "The default Album cannot be deleted."
    ];
    $id = base64_decode($id);
    if($id != md5("unsorted")) {
     $_Dialog = "";
     $_FileSystem = $this->core->Data("Get", ["fs", md5($you)]);
     $albums = $_FileSystem["Albums"] ?? [];
     $files = $_FileSystem["Files"] ?? [];
     $newAlbums = [];
     $newFiles = [];
     $title = $albums[$id]["Title"] ?? "Album";
     foreach($albums as $key => $value) {
      if($key != $id && $value["ID"] != $id) {
       $newAlbums[$key] = $value;
      }
     } foreach($files as $key => $value) {
      if($value["AID"] == $id) {
       $value["AID"] = md5("unsorted");
       $newFiles[$key] = $value;
      }
     }
     $_FileSystem["Albums"] = $newAlbums;
     $_FileSystem["Files"] = $newFiles;
     $conversation = $this->core->Data("Get", ["conversation", $id]);
     if(!empty($conversation)) {
      $conversation["Purge"] = 1;
      $this->core->Data("Save", ["conversation", $id, $conversation]);
     }
     $translations = $this->core->Data("Get", ["translate", $id]);
     if(!empty($translations)) {
      $translations["Purge"] = 1;
      $this->core->Data("Save", ["translate", $id, $translations]);
     }
     $votes = $this->core->Data("Get", ["votes", $id]);
     if(!empty($votes)) {
      $votes["Purge"] = 1;
      $this->core->Data("Save", ["votes", $id, $votes]);
     }
     $this->core->Data("Save", ["fs", md5($you), $_FileSystem]);
     $_View = $this->core->Element([
      "p", "The Album <em>[Album.Title]</em> was successfully deleted, and dependencies were marked for purging.",
      ["class" => "CenterText"]
     ]).$this->core->Element([
      "button", "Okay", ["class" => "CloseDialog v2 v2w"]
     ]);
     $_View = [
      "ChangeData" => [
       "[Album.Title]" => $title
      ],
      "Extension" => $this->core->AESencrypt($_View)
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Album Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $_FileSystem = $this->core->Data("Get", ["fs", md5($you)]);
    $actionTaken = ($new == 1) ? "saved" : "updated";
    $albums = $_FileSystem["Albums"] ?? [];
    $created = $albums[$id]["Created"] ?? $now;
    $coverPhoto = $albums[$id]["ICO"] ?? "";
    $illegal = $albums[$id]["Illegal"] ?? 0;
    $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $data["PassPhrase"] ?? "";
    $privacy = $data["Privacy"] ?? $y["Privacy"]["Albums"];
    $purge = $albums[$id]["Purge"] ?? 0;
    $title = $data["Title"] ?? "Untitled";
    $albums[$id] = [
     "Created" => $created,
     "Description" => $data["Description"],
     "ICO" => $coverPhoto,
     "ID" => $id,
     "Illegal" => $illegal,
     "Modified" => $now,
     "NSFW" => $nsfw,
     "PassPhrase" => $passPhrase,
     "Privacy" => $privacy,
     "Purge" => $purge,
     "Title" => $title
    ];
    $_FileSystem["Albums"] = $albums;
    $this->core->Data("Save", ["fs", md5($you), $_FileSystem]);
    $_Dialog = [
     "Body" => "The Album was $actionTaken.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
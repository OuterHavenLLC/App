<?php
 Class Album extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["AID", "new"]);
   $id = $data["AID"];
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Album Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->system->Member($t);
    $fileSystem = $this->system->Data("Get", [
     "fs",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $id = ($new == 1) ? md5($t["Login"]["Username"].$this->system->timestamp) : $id;
    $alb = $fileSystem["Albums"][$id] ?? [];
    $description = $alb["Description"] ?? "";
    $nsfw = $alb["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $alb["Privacy"] ?? $y["Privacy"]["Albums"];
    $title = $alb["Title"] ?? "";
    $header = ($new == 1) ? "Create New Album" : "Edit $title";
    $additionalContent = $this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $r = $this->system->Change([[
     "[Album.AdditionalContent]" => $additionalContent,
     "[Album.Header]" => $header,
     "[Album.ID]" => $id,
     "[Album.Inputs]" => $this->system->RenderInputs([
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
        "name" => "new",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $new
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
       "Value" => $title
      ],
      [
       "Attributes" => [
        "class" => "req",
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
       "Value" => $description
      ]
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->system->RenderVisibilityFilter([
      "Value" => $privacy
     ])
    ], $this->system->Page("760cd577207eb0d2121509d7212038d4")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditAlbum$id",
     "data-processor" => base64_encode("v=".base64_encode("Album:Save"))
    ]]);
   }
   $r = [
    "Action" => $button,
    "Front" => $r
   ];
   return $this->system->JSONResponse([
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
   $id = $data["AID"] ?? "";
   $b2 = $data["b2"] ?? "Albums";
   $b2 = urlencode($b2);
   $bck = $data["back"] ?? 0;
   $r = [
    "Body" => "The Album Identifier is missing.",
    "Header" => "Not Found"
   ];
   $xfsLimit = $this->system->core["XFS"]["limits"]["Total"] ?? 0;
   $xfsLimit = str_replace(",", "", $xfsLimit)."MB";
   $xfsUsage = 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $fileSystem = $this->system->Data("Get", ["fs", md5($you)]) ?? [];
   foreach($fileSystem["Files"] as $k => $v) {
    $xfsUsage = $xfsUsage + $v["Size"];
   }
   $xfsUsage = number_format(round($xfsUsage / 1000));
   $xfsUsage = str_replace(",", "", $xfsUsage);
   if(!empty($id) || $new == 1) {
    $t = ($data["UN"] == $you) ? $y : $this->system->Member($data["UN"]);
    $fileSystem = $this->system->Data("Get", [
     "fs",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $tun = base64_encode($t["Login"]["Username"]);
    $abl = base64_encode($t["Login"]["Username"]."-$id");
    $alb = $fileSystem["Albums"][$id] ?? [];
    $bl = $this->system->CheckBlocked([$y, "Albums", $abl]);
    $blc = ($bl == 0) ? "B" : "U";
    $blt = ($bl == 0) ? "Block" : "Unblock";
    $blt .= " <em>".$alb["Title"]."</em>";
    $blu = base64_encode("Common:SaveBlacklist");
    $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
    $ck2 = $y["subscr"]["XFS"]["A"] ?? 0;
    $ck2 = ($ck2 == 1 || $xfsUsage < $xfsLimit) ? 1 : 0;
    $coverPhoto = $alb["ICO"] ?? $this->system->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $coverPhoto = $this->system->GetSourceFromExtension([
     $t["Login"]["Username"],
     $coverPhoto
    ]);
    $actions = ($ck == 0) ? $this->system->Element([
     "button", $blt, [
      "class" => "BLK Small v2",
      "data-cmd" => base64_encode($blc),
      "data-u" => base64_encode("v=$blu&BU=".base64_encode("<em>".$alb["Title"]."</em>")."&content=".base64_encode($abl)."&list=".base64_encode("Albums")."&BC=")
     ]
    ]) : "";
    if($ck == 1) {
     $accessCode = "Accepted";
     $actions .= ($ck2 == 1) ? $this->system->Element([
      "button", "Add Files", [
       "class" => "Small dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("File:Upload")."&AID=$id&UN=".$t["Login"]["Username"])
      ]
     ]) : "";
     $actions .= ($id != md5("unsorted")) ? $this->system->Element([
      "button", "Delete Album", [
       "class" => "Small dBO dB2C v2 v2w",
       "data-type" => "v=".base64_encode("Authentication:DeleteAlbum")."&AID=$id&UN=$tun"
      ]
     ]) : "";
     $actions .= $this->system->Element(["button", "Edit Album", [
      "class" => "Small dB2O v2 v2w",
      "data-type" => base64_encode("v=".base64_encode("Album:Edit")."&AID=$id&UN=$tun")
     ]]);
    }
    $actions = ($this->system->ID != $you) ? $actions : "";
    $votes = ($ck == 0) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
    $votes = base64_encode("v=$votes&ID=$id&Type=4");
    $r = $this->system->Change([[
     "[Album.Actions]" => $actions,
     "[Album.CoverPhoto]" => $coverPhoto,
     "[Album.Created]" => $this->system->TimeAgo($alb["Created"]),
     "[Album.Description]" => $alb["Description"],
     "[Album.ID]" => $id,
     "[Album.Modified]" => $this->system->TimeAgo($alb["Modified"]),
     "[Album.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Album;".$t["Login"]["Username"].";$id")),
     "[Album.Owner]" => $t["Personal"]["DisplayName"],
     "[Album.Reactions]" => $votes,
     "[Album.Share]" => base64_encode("v=".base64_encode("Album:Share")."&ID=$id&UN=$tun"),
     "[Album.Title]" => $alb["Title"],
     "[Album.View]" => $this->view(base64_encode("Album:List"), ["Data" => [
      "AID" => $id,
      "UN" => $tun
     ]])
    ], $this->system->Page("91c56e0ee2a632b493451aa044c32515")]);
    $r = [
     "Front" => $r
    ];
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function List(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "AID",
    "UN",
    "b2",
    "back",
    "lPG",
    "lPP"
   ]);
   $y = $this->you;
   $id = $data["AID"] ?? "";
   $b2 = $data["b2"] ?? "Albums";
   $bck = $data["back"] ?? 0;
   $un = $data["UN"] ?? $y["Login"]["Username"];
   $r = [
    "Body" => "The requested Album could not be found.",
    "Header" => "Not Found"
   ];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $back = ($bck == 1) ? $this->system->Element(["button", "Back to $b2", [
     "class" => "GoToParent LI head",
     "data-type" => $data["lPG"]
    ]]) : "";
    $r = $this->view(base64_encode("Search:Containers"), ["Data" => [
     "AID" => $id,
     "UN" => $un,
     "st" => "MBR-XFS"
    ]]);
    $r = $back.$this->system->RenderView($r);
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "ID",
    "Title",
    "new",
    "nsfw",
    "pri"
   ]);
   $id = $data["ID"];
   $new = $data["new"] ?? 0;
   $now = $this->system->timestamp;
   $r = [
    "Body" => "The Album Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_FileSystem = $this->system->Data("Get", ["fs", md5($you)]) ?? [];
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "saved" : "updated";
    $albums = $_FileSystem["Albums"] ?? [];
    $created = $albums[$id]["Created"] ?? $now;
    $coverPhoto = $albums[$id]["ICO"] ?? "";
    $illegal = $albums[$id]["Illegal"] ?? 0;
    $nsfw = $data["nsfw"] ?? $y["Privacy"]["NSFW"];
    $privacy = $data["pri"] ?? $y["Privacy"]["Albums"];
    $albums[$id] = [
     "Created" => $created,
     "Description" => $data["Description"],
     "ICO" => $coverPhoto,
     "ID" => $id,
     "Illegal" => $illegal,
     "Modified" => $now,
     "NSFW" => $nsfw,
     "Privacy" => $privacy,
     "Title" => $data["Title"]
    ];
    $_FileSystem["Albums"] = $albums;
    $this->system->Data("Save", ["fs", md5($you), $_FileSystem]);
    $r = [
     "Body" => "The Album was $actionTaken.",
     "Header" => "Done"
    ];
   }
   return $this->system->JSONResponse([
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
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Album Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->system->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $r = [
     "Body" => "The default Album cannot be deleted."
    ];
    if($id != md5("unsorted")) {
     $accessCode = "Accepted";
     $_FileSystem = $this->system->Data("Get", ["fs", md5($you)]) ?? [];
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
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
     $this->system->Data("Purge", ["local", $id]);
     $this->system->Data("Purge", ["react", $id]);
     $this->system->Data("Save", ["fs", md5($you), $_FileSystem]);
     $r = [
      "Body" => "The Album <em>$title</em> was successfully deleted.",
      "Header" => "Done"
     ];
    }
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function Share(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $id = $data["ID"];
   $un = $data["UN"];
   $r = [
    "Body" => "The Share Sheet Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($un)) {
    $accessCode = "Accepted";
    $un = base64_decode($un);
    $code = base64_encode("$un;$id");
    $t = ($un == $you) ? $y : $this->system->Member($un);
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out ".$t["Personal"]["DisplayName"]."'s media album!"
     ]).$this->system->Element([
      "div", "[Album:$code]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $fileSystem = $this->system->Data("Get", ["fs", md5($un)]) ?? [];
    $fileSystem = $fileSystem["Albums"][$id] ?? [];
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$code&Type=Album",
     "[Share.ContentID]" => "Album",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $fileSystem["Title"]
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
    $r = [
     "Front" => $r
    ];
   }
   return $this->system->JSONResponse([
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
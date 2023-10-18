<?php
 Class Album extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $button = "";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["AID", "new"]);
   $id = $data["AID"];
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Album Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $action = ($new == 1) ? "Post" : "Update";
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $fileSystem = $this->core->Data("Get", [
     "fs",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $id = ($new == 1) ? md5($t["Login"]["Username"].$this->core->timestamp) : $id;
    $alb = $fileSystem["Albums"][$id] ?? [];
    $description = $alb["Description"] ?? "";
    $nsfw = $alb["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $alb["Privacy"] ?? $y["Privacy"]["Albums"];
    $title = $alb["Title"] ?? "";
    $header = ($new == 1) ? "Create New Album" : "Edit $title";
    $r = $this->core->Change([[
     "[Album.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => "Album",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=N/A&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => "NA",
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=NA&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Album.Description]" => base64_encode($description),
     "[Album.Header]" => $header,
     "[Album.ID]" => $id,
     "[Album.New]" => $new,
     "[Album.Title]" => base64_encode($title),
     "[Album.Visibility.NSFW]" => $nsfw,
     "[Album.Visibility.Privacy]" => $privacy
    ], $this->core->Page("760cd577207eb0d2121509d7212038d4")]);
    $button = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditAlbum$id",
     "data-processor" => base64_encode("v=".base64_encode("Album:Save"))
    ]]);
   }
   $r = [
    "Action" => $button,
    "Front" => $r
   ];
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
   $id = $data["AID"] ?? "";
   $b2 = $data["b2"] ?? "Albums";
   $b2 = urlencode($b2);
   $bck = $data["back"] ?? 0;
   $r = [
    "Body" => "The Album Identifier is missing.",
    "Header" => "Not Found"
   ];
   $fsLimit = $this->core->config["XFS"]["limits"]["Total"] ?? 0;
   $fsLimit = str_replace(",", "", $fsLimit)."MB";
   $fsUsage = 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $fileSystem = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
   foreach($fileSystem["Files"] as $key => $value) {
    $fsUsage = $fsUsage + $value["Size"];
   }
   $fsUsage = number_format(round($fsUsage / 1000));
   $fsUsage = str_replace(",", "", $fsUsage);
   if(!empty($id) || $new == 1) {
    $t = ($data["UN"] == $you) ? $y : $this->core->Member($data["UN"]);
    $fileSystem = $this->core->Data("Get", [
     "fs",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $tun = base64_encode($t["Login"]["Username"]);
    $alb = $fileSystem["Albums"][$id] ?? [];
    $blockID = base64_encode($t["Login"]["Username"]."-$id");
    $bl = $this->core->CheckBlocked([$y, "Albums", $blockID]);
    $blockCommand = ($bl == 0) ? "Block" : "Unblock";
    $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
    $ck2 = $y["subscr"]["XFS"]["A"] ?? 0;
    $ck2 = ($ck2 == 1 || $fsUsage < $fsLimit) ? 1 : 0;
    $coverPhoto = $alb["ICO"] ?? $this->core->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $coverPhoto = $this->core->GetSourceFromExtension([
     $t["Login"]["Username"],
     $coverPhoto
    ]);
    $actions = ($ck == 0) ? $this->core->Element([
     "button", $blockCommand, [
      "class" => "Small UpdateButton v2",
      "data-processor" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($blockID)."&List=".base64_encode("Albums"))
     ]
    ]) : "";
    if($ck == 1) {
     $accessCode = "Accepted";
     $actions .= ($id != md5("unsorted")) ? $this->core->Element([
      "button", "Delete", [
       "class" => "CloseCard OpenDialog Small v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteAlbum")."&AID=$id&UN=$tun")
      ]
     ]) : "";
     $actions .= $this->core->Element(["button", "Edit", [
      "class" => "OpenCard Small v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("Album:Edit")."&AID=$id&UN=$tun")
     ]]);
    }
    $actions = ($this->core->ID != $you) ? $actions : "";
    $share = ($t["Login"]["Username"] == $you || $file["Privacy"] == md5("Public")) ? 1 : 0;
    $actions .= ($share == 1) ? $this->core->Element([
     "button", "Share", [
      "class" => "OpenCard Small v2",
      "data-view" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($id)."&Type=".base64_encode("Album")."&Username=$tun")
     ]
    ]) : "";
    $actions .= ($ck == 1 && $ck2 == 1) ? $this->core->Element([
     "button", "Upload", [
      "class" => "OpenCard Small v2",
      "data-view" => base64_encode("v=".base64_encode("File:Upload")."&AID=$id&UN=".$t["Login"]["Username"])
     ]
    ]) : "";
    $votes = ($ck == 0) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
    $r = $this->core->Change([[
     "[Album.Actions]" => $actions,
     "[Album.CoverPhoto]" => $coverPhoto,
     "[Album.Created]" => $this->core->TimeAgo($alb["Created"]),
     "[Album.Description]" => $alb["Description"],
     "[Album.ID]" => $id,
     "[Album.Modified]" => $this->core->TimeAgo($alb["Modified"]),
     "[Album.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Album;".$t["Login"]["Username"].";$id")),
     "[Album.Owner]" => $t["Personal"]["DisplayName"],
     "[Album.Stream]" => base64_encode("v=".base64_encode("Album:List")."&AID=$id&UN=$tun"),
     "[Album.Title]" => $alb["Title"],
     "[Album.Votes]" => base64_encode("v=$votes&ID=$id&Type=4")
    ], $this->core->Page("91c56e0ee2a632b493451aa044c32515")]);
    $r = [
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
  function List(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "AID",
    "UN",
    "b2",
    "back",
    "lPG",
    "lPP"
   ]);
   $back = $data["back"] ?? 0;
   $id = $data["AID"] ?? "";
   $b2 = $data["b2"] ?? "Albums";
   $y = $this->you;
   $un = $data["UN"] ?? $y["Login"]["Username"];
   $r = [
    "Body" => "The requested Album could not be found.",
    "Header" => "Not Found"
   ];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $back = ($back == 1) ? $this->core->Element(["button", "Back to $b2", [
     "class" => "GoToParent LI head",
     "data-type" => $data["lPG"]
    ]]) : "";
    $r = $this->view(base64_encode("Search:Containers"), ["Data" => [
     "AID" => $id,
     "UN" => $un,
     "st" => "MBR-XFS"
    ]]);
    $r = $back.$this->core->RenderView($r);
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
    "ID",
    "Title"
   ]);
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $now = $this->core->timestamp;
   $r = [
    "Body" => "The Album Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_FileSystem = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "saved" : "updated";
    $albums = $_FileSystem["Albums"] ?? [];
    $created = $albums[$id]["Created"] ?? $now;
    $coverPhoto = $albums[$id]["ICO"] ?? "";
    $illegal = $albums[$id]["Illegal"] ?? 0;
    $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $data["Privacy"] ?? $y["Privacy"]["Albums"];
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
    $this->core->Data("Save", ["fs", md5($you), $_FileSystem]);
    $r = [
     "Body" => "The Album was $actionTaken.",
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
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
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
   } elseif($this->core->ID == $you) {
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
     $_FileSystem = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
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
     $this->core->Data("Purge", ["local", $id]);
     $this->core->Data("Purge", ["votes", $id]);
     $this->core->Data("Save", ["fs", md5($you), $_FileSystem]);
     $r = [
      "Body" => "The Album <em>$title</em> was successfully deleted.",
      "Header" => "Done"
     ];
    }
   }
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
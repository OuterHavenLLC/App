<?php
 Class FAB extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Edit(array $a) {
   $accessCode = "Accepted";
   $d = $a["Data"] ?? [];
   $d = $this->core->FixMissing($d, ["ID", "new"]);
   $y = $this->you;
   $new = $d["new"] ?? 0;
   $id = ($new == 1) ? base64_encode(md5($y["Login"]["Username"]."_FAB_".$this->core->timestamp)) : $d["ID"];
   $id = base64_decode($id);
   $fab = $this->core->Data("Get", [
    "x",
    md5("FreeAmericaBroadcasting")
   ]) ?? [];
   $fab = $fab[$id] ?? [];
   $fab = $this->core->FixMissing($fab, [
    "Description",
    "ICO-SRC",
    "Listen",
    "NSFW",
    "Title",
    "URL"
   ]);
   $ttl = $fab["Title"] ?? "Broadcaster";
   $at = base64_encode("Added to $ttl!");
   $at2 = base64_encode("Set as Product Cover Photo:.ATTI$id");
   $h = ($new == 1) ? "New Broadcaster" : "Edit $ttl";
   $pu = ($new == 1) ? "Post" : "Update";
   $r = $this->core->Change([[
    "[FAB.AdditionalContent]" => $this->core->Change([
     [
      "[Extras.ContentType]" => "Broadcaster",
      "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
      "[Extras.DesignView.Origin]" => "N/A",
      "[Extras.DesignView.Destination]" => "UIV$id",
      "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
      "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=N/A&Added=$at2&UN=$you"),
      "[Extras.ID]" => $id,
      "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
     ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
    ]),
    "[FAB.Header]" => $h,
    "[FAB.Description]" => $fab["Description"],
    "[FAB.ICO]" => $fab["ICO-SRC"],
    "[FAB.ID]" => $id,
    "[FAB.Listen]" => $fab["Listen"],
    "[FAB.New]" => $new,
    "[FAB.NSFW]" => $this->core->RenderVisibilityFilter([
     "Filter" => "NSFW",
     "Name" => "nsfw",
     "Title" => "Content Status",
     "Value" => 0
    ]).$this->core->RenderVisibilityFilter([
     "Value" => md5("Public")
    ]),
    "[FAB.Title]" => $fab["Title"],
    "[FAB.URL]" => $fab["URL"]
   ], $this->core->Page("9989bd7cf0facb4cbca6d6c8825a588b")]);
   $action = $this->core->Element(["button", $pu, [
    "class" => "BB SendData v2",
    "data-form" => ".FAB$id",
    "data-processor" => base64_encode("v=".base64_encode("FAB:Save"))
   ]]);
   $r = [
    "Action" => $action,
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
  function Save(array $a) {
   $accessCode = "Denied";
   $d = $a["Data"] ?? [];
   $d = $this->core->DecodeBridgeData($d);
   $d = $this->core->FixMissing($d, [
    "ID", "Listen", "Role", "Title", "URL", "new", "nsfw"
   ]);
   $new = $d["new"] ?? 0;
   $y = $this->you;
   $id = $d["ID"];
   $pu = ($new == 1) ? "posted" : "updated";
   $r = [
    "Body" => "The Station Identifier is missing."
   ];
   if($y["Login"]["Username"] == $this->core->ID) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $fab = $this->core->Data("Get", [
     "x",
     md5("FreeAmericaBroadcasting")
    ]) ?? [];
    $ico = "";
    $src = "";
    if(!empty($d["rATTI$id"])) {
     $db = explode(";", base64_decode($d["rATTI$id"]));
     $dbc = count($db);
     for($i = 0; $i < $dbc; $i++) {
      if(!empty($db[$i]) && $i2 == 0) {
       $dbi = explode("-", base64_decode($db[$i]));
       if(!empty($dbi[0]) && !empty($dbi[1])) {
        $t = $this->core->Member($dbi[0]);
        $efs = $this->core->Data("Get", [
         "fs",
         md5($t["Login"]["Username"])
        ]) ?? [];
        $ico = $dbi[0]."/".$efs["Files"][$dbi[1]]["Name"];
        $src = base64_encode($dbi[0]."-".$dbi[1]);
        $i2++;
       }
      }
     }
    }
    $ttl = $d["Title"];
    $fab[$id] = [
     "Description" => htmlentities($d["Description"]),
     "ICO" => $ico,
     "ICO-SRC" => $src,
     "Listen" => $d["Listen"],
     "NSFW" => $d["nsfw"],
     "Role" => $d["Role"],
     "Title" => $ttl,
     "UN" => $y["Login"]["Username"],
     "URL" => $d["URL"]
    ];
    $r = [
     "Body" => "The Station <em>$ttl</em> was $pu!",
     "Header" => "Done"
    ];
    $this->core->Data("Save", [
     "x",
     md5("FreeAmericaBroadcasting"),
     $fab
    ]);
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $d = $a["Data"] ?? [];
   $d = $this->core->DecodeBridgeData($d);
   $d = $this->core->FixMissing($d, ["ID", "new"]);
   $r = [
    "Body" => "The Station Identifier is missing."
   ];
   if($y["Login"]["Username"] == $this->core->ID) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($d["ID"])) {
    $accessCode = "Accepted";
    $fab = $this->core->Data("Get", [
     "x",
     md5("FreeAmericaBroadcasting")
    ]);
    $fab2 = [];
    $id = base64_decode($d["ID"]);
    $ttl = "The Broadcaster";
    foreach($fab as $k => $v) {
     if($k != $id) {
      $fab2[$k] = $v;
     } else {
      $this->core->Data("Purge", ["local", $id]);
      $this->core->Data("Purge", ["votes", $id]);
      $ttl = $v["Title"];
     }
    }
    $fab = $fab2;
    $r = [
     "Body" => "<em>$ttl</em> was deleted.",
     "Header" => "Done"
    ];
    $this->core->Data("Save", [
     "x",
     md5("FreeAmericaBroadcasting"),
     $fab
    ]);
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
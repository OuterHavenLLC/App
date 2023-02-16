<?php
 Class FAB extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->FixMissing($d, ["ID", "new"]);
   $y = $this->you;
   $new = $d["new"] ?? 0;
   $id = ($new == 1) ? base64_encode(md5($y["Login"]["Username"]."_FAB_".$this->system->timestamp)) : $d["ID"];
   $id = base64_decode($id);
   $fab = $this->system->Data("Get", [
    "x",
    md5("FreeAmericaBroadcasting")
   ]) ?? [];
   $fab = $fab[$id] ?? [];
   $fab = $this->system->FixMissing($fab, [
    "Description",
    "ICO-SRC",
    "Listen",
    "NSFW",
    "Title",
    "URL"
   ]);ßßß
   $ttl = $fab["Title"] ?? "Broadcaster";
   $at = base64_encode("Added to $ttl!");
   $at2 = base64_encode("Set as Product Cover Photo:.ATTI$id");
   $h = ($new == 1) ? "New Broadcaster" : "Edit [FAB.Title]";
   $pu = ($new == 1) ? "Post" : "Update";
   $sc = base64_encode("Search:Containers");
   $bck = $this->system->Change([
    [
     "[CP.ContentType]" => "Broadcaster",
     "[CP.Files]" => base64_encode("v=$sc&st=XFS&AddTo=$at2&Added=$at&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".$y["Login"]["Username"]),
     "[CP.ID]" => $id
    ], $this->system->Page("dc027b0a1f21d65d64d539e764f4340a")
   ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
    "ID" => base64_encode($id)
   ]]);
   $fr = $this->system->Change([[
    "[FAB.Header]" => $h,
    "[FAB.Description]" => $fab["Description"],
    "[FAB.ICO]" => $fab["ICO-SRC"],
    "[FAB.ID]" => $id,
    "[FAB.Listen]" => $fab["Listen"],
    "[FAB.New]" => $new,
    "[FAB.NSFW]" => $this->system->Select("nsfw", "req v2 v2w", $fab["NSFW"]),
    "[FAB.Title]" => $fab["Title"],
    "[FAB.URL]" => $fab["URL"]
   ], $this->system->Page("9989bd7cf0facb4cbca6d6c8825a588b")]);
   $frbtn = $this->system->Element(["button", $pu, [
    "class" => "BB Xedit v2",
    "data-type" => ".FAB$id",
    "data-u" => base64_encode("v=".base64_encode("FAB:Save")),
    "id" => "fSub"
   ]]);
   return $this->system->Card(["Front" => $fr, "FrontButton" =>$frbtn]);
  }
  function Save(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->DecodeBridgeData($d);
   $d = $this->system->FixMissing($d, [
    "ID", "Listen", "Role", "Title", "URL", "new", "nsfw"
   ]);
   $ec = "Denied";
   $new = $d["new"] ?? 0;
   $y = $this->you;
   $id = $d["ID"];
   $pu = ($new == 1) ? "posted" : "updated";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Station Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $ec = "Accepted";
    $fab = $this->system->Data("Get", [
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
        $t = $this->system->Member($dbi[0]);
        $efs = $this->system->Data("Get", [
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
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Station <em>$ttl</em> was $pu!"
     ]),
     "Header" => "Done"
    ]);
    $this->system->Data("Save", ["x", md5("FreeAmericaBroadcasting"), $fab]);
   }
   return $this->system->JSONResponse([$ec, $r]);
  }
  function SaveDelete(array $a) {
   $d = $a["Data"] ?? [];
   $d = $this->system->DecodeBridgeData($d);
   $d = $this->system->FixMissing($d, ["ID", "all", "new"]);
   $ec = "Denied";
   $all = $d["all"] ?? 0;
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Station Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif($all == 1) {
    $ec = "Accepted";
    $fab = $this->system->core["SYS"]["FAB"];
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "<em>Free America Broadcasting</em> was reset."
     ]),
     "Header" => "Done"
    ]);
   } elseif(!empty($d["ID"])) {
    $ec = "Accepted";
    $fab = $this->system->Data("Get", ["x", md5("FreeAmericaBroadcasting")]);
    $fab2 = [];
    $id = base64_decode($d["ID"]);
    $ttl = "The Broadcaster";
    foreach($fab as $k => $v) {
     if($k != $id) {
      $fab2[$k] = $v;
     } else {
      $this->system->Data("Purge", ["local", $id]);
      $this->system->Data("Purge", ["react", $id]);
      $ttl = $v["Title"];
     }
    }
    $fab = $fab2;
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "<em>$ttl</em> was deleted."]),
     "Header" => "Done"
    ]);
    $this->system->Data("Save", [
     "x",
     md5("FreeAmericaBroadcasting"),
     $fab
    ]);
   }
   return $this->system->JSONResponse([$ec, $r]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
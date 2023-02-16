<?php
 Class Language extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $ls = base64_encode("Language:Save");
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Languages Package Identifier is missing."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $fr = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id)) {
    $id = base64_decode($id);
    $locals = "";
    $lt = $this->system->Data("Get", ["local", $id]) ?? [];
    $tpl = $this->system->Page("63dde5af1a1ec0968ccb006248b55f48");
    $tpl2 = $this->system->Page("5f6ea04c169f32041a39e16d20f95a05");
    if(empty($lt)) {
     $k = md5($you."_Local_".$this->system->timestamp);
     $code = "&#91;Languages:$id-$k&#93;";
     $regions = "";
     foreach($this->system->Languages() as $re => $la) {
      $t = $lt[$k][$re] ?? "";
      $t = (!empty($t)) ? $this->system->PlainText([
       "Data" => $t,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]) : $t;
      $regions .= $this->system->Change([[
       "[Region.Language]" => $la,
       "[Region.LocalID]" => $k,
       "[Region.Code]" => $re,
       "[Region.Text]" => $t
      ], $tpl2]);
     }
     $locals .= $this->system->Change([[
      "[Local.Code]" => $code,
      "[Local.ID]" => $k,
      "[Local.Regions]" => $regions
     ], $tpl]);
    } else {
     foreach($lt as $k => $v) {
      $code = "&#91;Languages:$id-$k&#93;";
      $regions = "";
      foreach($this->system->Languages() as $re => $la) {
       $t = $v[$re] ?? "";
       $t = (!empty($t)) ? $this->system->PlainText([
        "Data" => $t,
        "Decode" => 1,
        "HTMLDecode" => 1
       ]) : $t;
       $regions .= $this->system->Change([[
        "[Region.Language]" => $la,
        "[Region.LocalID]" => $k,
        "[Region.Code]" => $re,
        "[Region.Text]" => $t
       ], $tpl2]);
      }
      $locals .= $this->system->Change([[
       "[Local.Code]" => $code,
       "[Local.ID]" => $k,
       "[Local.Regions]" => $regions
      ], $tpl]);
     }
    }
    $regions = "";
    foreach($this->system->Languages() as $re => $la) {
     $regions .= $this->system->Change([[
      "[Region.Language]" => $la,
      "[Region.LocalID]" => "LocalID",
      "[Region.Code]" => $re,
      "[Region.Text]" => ""
     ], $tpl2]);
    }
    $tpl = $this->system->PlainText([
     "Data" => $this->system->Change([[
      "[Local.Code]" => "LocalCode",
      "[Local.ID]" => "LocalID",
      "[Local.Regions]" => $regions
     ], $tpl]),
     "HTMLEncode" => 1
    ]);
    $r = $this->system->Change([[
     "[Languages.CloneVariables]" => base64_encode(json_encode([
      "LocalCode" => htmlentities("[Languages:$id-LocalID]"),
      "LocalID" => "GenerateUniqueID"
     ])),
     "[Languages.CloneTPL]" => $tpl,
     "[Languages.ID]" => $id,
     "[Languages.Locals]" => $locals,
     "[Languages.Processor]" => base64_encode("v=$ls"),
    ], $this->system->Page("d4ccf0731cd5ee5c10c04a9193bd61d5")]);
   }
   return $r;
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Languages Package Identirifer is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $lt = $this->system->Data("Get", ["local", $data["ID"]]);
    $lt = $lt ?? [];
    foreach($d as $k => $v) {
     if(strpos($k, "Locals_") !== false) {
      $k = explode("_", $k);
      foreach($this->system->Languages() as $re => $la) {
       $ltd = $data[$k[1]."-$re"] ?? "";
       $lt[$k[1]][$re] = $this->system->PlainText([
        "Data" => $ltd,
        "Encode" => 1,
        "HTMLEncode" => 1
       ]);
      }
     }
    }
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "The Localization was saved."
     ]),
     "Header" => "Done"
    ]);
    #$this->system->Data("Save", ["local", $data["ID"], $lt]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
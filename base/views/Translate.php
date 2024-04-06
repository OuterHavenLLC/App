<?php
 Class Translate extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Translation Package Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $_Locals = $this->core->Extension("63dde5af1a1ec0968ccb006248b55f48");
    $_Regions = $this->core->Extension("5f6ea04c169f32041a39e16d20f95a05");
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $locals = "";
    $lt = $this->core->Data("Get", ["translate", $id]) ?? [];
    $regions = "";
    if(empty($lt)) {
     $k = md5($you."_Local_".$this->core->timestamp);
     $code = "&#91;Languages:$id-$k&#93;";
     foreach($this->core->Languages() as $re => $la) {
      $t = $lt[$k][$re] ?? "";
      $t = (!empty($t)) ? $this->core->PlainText([
       "Data" => $t,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]) : $t;
      $regions .= $this->core->Change([[
       "[Region.Language]" => $la,
       "[Region.LocalID]" => $k,
       "[Region.Code]" => $re,
       "[Region.Text]" => $t
      ], $_Regions]);
     }
     $locals .= $this->core->Change([[
      "[Local.Code]" => $code,
      "[Local.ID]" => $k,
      "[Local.Regions]" => $regions
     ], $_Locals]);
    } else {
     foreach($lt as $k => $v) {
      $code = "&#91;Languages:$id-$k&#93;";
      foreach($this->core->Languages() as $re => $la) {
       $t = $v[$re] ?? "";
       $t = (!empty($t)) ? $this->core->PlainText([
        "Data" => $t,
        "Decode" => 1,
        "HTMLDecode" => 1
       ]) : $t;
       $regions .= $this->core->Change([[
        "[Region.Language]" => $la,
        "[Region.LocalID]" => $k,
        "[Region.Code]" => $re,
        "[Region.Text]" => $t
       ], $_Regions]);
      }
      $locals .= $this->core->Change([[
       "[Local.Code]" => $code,
       "[Local.ID]" => $k,
       "[Local.Regions]" => $regions
      ], $_Locals]);
     }
    } foreach($this->core->Languages() as $re => $la) {
     $regions .= $this->core->Change([[
      "[Region.Language]" => $la,
      "[Region.LocalID]" => "LocalID",
      "[Region.Code]" => $re,
      "[Region.Text]" => ""
     ], $_Regions]);
    }
    $_Locals = $this->core->PlainText([
     "Data" => $this->core->Change([[
      "[Local.Code]" => "LocalCode",
      "[Local.ID]" => "LocalID",
      "[Local.Regions]" => $regions
     ], $_Locals]),
     "HTMLEncode" => 1
    ]);
    $r = $this->core->Change([[
     "[Languages.CloneVariables]" => base64_encode(json_encode([
      "LocalCode" => htmlentities("[Languages:$id-LocalID]"),
      "LocalID" => "GenerateUniqueID"
     ])),
     "[Languages.CloneTPL]" => $_Locals,
     "[Languages.ID]" => $id,
     "[Languages.Locals]" => $locals,
     "[Languages.Processor]" => base64_encode("v=".base64_encode("Translate:Save")),
    ], $this->core->Extension("d4ccf0731cd5ee5c10c04a9193bd61d5")]);
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
   $data = $this->core->FixMissing($data, ["ID"]);
   $r = [
    "Body" => "The Translation Package Identirifer is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $lt = $this->core->Data("Get", ["translate", $data["ID"]]);
    $lt = $lt ?? [];
    foreach($d as $k => $v) {
     if(strpos($k, "Locals_") !== false) {
      $k = explode("_", $k);
      foreach($this->core->Languages() as $re => $la) {
       $ltd = $data[$k[1]."-$re"] ?? "";
       $lt[$k[1]][$re] = $this->core->PlainText([
        "Data" => $ltd,
        "Encode" => 1,
        "HTMLEncode" => 1
       ]);
      }
     }
    }
    $r = [
     "Body" => "The Localization was saved.",
     "Header" => "Done"
    ];
    #$this->core->Data("Save", ["translate", $data["ID"], $lt]);
   }
   return $this->core->JSONResponse([
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
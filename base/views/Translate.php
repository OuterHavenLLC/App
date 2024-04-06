<?php
 Class Translate extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $disabled = $data["Disabled"] ?? base64_encode(0);
   $disabled = base64_decode($disabled);
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
   } elseif($disabled == 1) {
    $accessCode = "Accepted";
    $r = $this->core->Element([
     "h1", "Translate"
    ]).$this->core->Element([
     "p", "Translate is disabled for this experience."
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $translations = $this->core->Data("Get", ["translate", $id]) ?? [];
    /*--foreach($translations as $textID => $translation) {
    }--*/
    $r = $this->core->Change([[
     "[Trsnalate.Clone]" => base64_encode($this->RenderClone()),
     "[Trsnalate.ID]" => $id,
     "[Trsnalate.Translations]" => json_encode($translations, true),
     "[Trsnalate.Save]" => base64_encode("v=".base64_encode("Translate:Save")),
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
  function RenderClone() {
   $_Regions = $this->core->Extension("5f6ea04c169f32041a39e16d20f95a05");
   $_Translations = $this->core->Extension("63dde5af1a1ec0968ccb006248b55f48");
   $clone = "{TRANSLATIONS_CLONE}";
   return $clone;
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
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
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $lt = $this->core->Data("Get", ["translate", $id]) ?? [];
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
    #$this->core->Data("Save", ["translate", $data["ID"], $lt]);
    $r = [
     "Body" => "The Localization was saved.",
     "Header" => "Done"
    ];
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
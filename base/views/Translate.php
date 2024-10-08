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
    ]).$this->core->Element(["button", "Back", [
     "class" => "LI PS",
     "data-type" => ".MainCardContent;.CardNavigation;.Editor"
    ]
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $clone = $this->RenderClone();
    $id = base64_decode($id);
    $translations = $this->core->Data("Get", ["translate", $id]);
    $translationsList = "";
    foreach($translations as $translationID => $package) {
     $packageClone = $clone;
     $packageClone = str_replace("[Translate.Clone.PackageID]", $translationID, $packageClone);
     foreach($this->core->Languages() as $region => $language) {
      $translation = $package[$region] ?? "";
      $packageClone = str_replace("[Translate.Clone.$region]", $translation, $packageClone);
     }
     $translationsList .= $packageClone;
    } foreach($this->core->Languages() as $region => $language) {
     $clone = str_replace("[Translate.Clone.$region]", "", $clone);
    }
    $clone = str_replace("[Translate.Clone.PackageID]", "", $clone);
    $translationsList = (empty($translationsList)) ? str_replace("[Clone.ID]", uniqid("Clone"), $clone) : $translationsList;
    $r = $this->core->Change([[
     "[Trsnalate.Clone]" => base64_encode($clone),
     "[Trsnalate.ID]" => $id,
     "[Trsnalate.Translations]" => $translationsList,
     "[Trsnalate.Save]" => base64_encode("v=".base64_encode("Translate:Save")),
    ], $this->core->Extension("d4ccf0731cd5ee5c10c04a9193bd61d5")]);
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
  function RenderClone() {
   $translations = "";
   foreach($this->core->Languages() as $region => $language) {
    $translations .= $this->core->Element(["div", $this->core->Element([
     "h4", $language
    ]).$this->core->Element(["textarea", "[Translate.Clone.$region]", [
     "name" => $region."[]"
    ]]), ["class" => "Medium"]]);
   }
   return $this->core->Change([[
    "[Translate.Clone.Translations]" => $translations
   ], $this->core->Extension("63dde5af1a1ec0968ccb006248b55f48")]);
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
    $purge = $translations["Purge"] ?? 0;
    $translations = [];
    for($i = 0; $i < count($data["TranslatePackageID"]); $i++) {
     foreach($this->core->Languages() as $region => $language) {
      $translations[$data["TranslatePackageID"][$i]][$region] = $data[$region][$i] ?? "";
     }
    } if($purge != 0) {
     $translations["Purge"] = $purge;
    }
    $this->core->Data("Save", ["translate", $id, $translations]);
    $r = [
     "Body" => "The Translations were saved.",
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
    "ResponseType" => "Dialog"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class Authentication extends OH {
  function __construct() {
   parent::__construct();
   $this->authID = md5($this->core->timestamp.uniqid());
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function ArticleChangeMemberRole(array $data) {
   $_Dialog = [
    "Body" => "The Article Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Article = $this->core->Data("Get", ["pg", base64_decode($id)]);
    $_View = [
     "ChangeData" => [
      "[Roles.ID]" => $_Article["ID"],
      "[Roles.Member]" => base64_decode($member),
      "[Roles.Processor]" => base64_encode("v=".base64_encode("Page:ChangeMemberRole")),
      "[Roles.Title]" => $_Article["Title"]
     ],
     "ExtensionID" => "270d16c83b59b067231b0c6124a4038d"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function BlogChangeMemberRole(array $data) {
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $blog = $this->core->Data("Get", ["blg", base64_decode($id)]);
    $_View = [
     "ChangeData" => [
      "[Roles.ID]" => $blog["ID"],
      "[Roles.Member]" => base64_decode($member),
      "[Roles.Processor]" => base64_encode("v=".base64_encode("Blog:ChangeMemberRole")),
      "[Roles.Title]" => $blog["Title"]
     ],
     "ExtensionID" => "270d16c83b59b067231b0c6124a4038d"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function PFChangeMemberRole(array $data) {
   $_Dialog = [
    "Body" => "The Forum Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $forum = $this->core->Data("Get", ["pf", base64_decode($id)]);
    $_View = [
     "ChangeData" => [
      "[Roles.ID]" => $forum["ID"],
      "[Roles.Member]" => base64_decode($member),
      "[Roles.Processor]" => base64_encode("v=".base64_encode("Forum:ChangeMemberRole")),
      "[Roles.Title]" => $forum["Title"]
     ],
     "ExtensionID" => "270d16c83b59b067231b0c6124a4038d"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function ProtectedContent(array $data) {
   $_Dialog = [
    "Body" => "The View Data is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $dialog = $data["Dialog"] ?? 0;
   $header = $data["Header"] ?? base64_encode("");
   $parentPage = $data["ParentPage"] ?? "";
   $responseType = "Dialog";
   $signOut = $data["SignOut"] ?? 0;
   $text = $data["Text"] ?? base64_encode("Please enter your PIN below to continue.");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } else {
    $_Dialog = "";
    $back = (!empty($parentPage)) ? $this->core->Element(["button", "Back", [
     "class" => "GoToParent LI",
     "data-type" => $parentPage
    ]]) : "";
    $closeDialog = ($dialog == 1) ? $this->core->Element([
     "button", "Cancel", ["class" => "CloseDialog v2 v2w"]
    ]) : "";
    $view = "";
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([], true));
    $viewData = json_decode(base64_decode($viewData));
    foreach($viewData as $key => $value) {
     $view .= "$key=$value&";
    }
    $_View = [
     "ChangeData" => [
      "[ProtectedContent.Back]" => $back,
      "[ProtectedContent.CloseDialog]" => $closeDialog,
      "[ProtectedContent.Header]" => base64_decode($header),
      "[ProtectedContent.SignOut]" => $signOut,
      "[ProtectedContent.Text]" => base64_decode($text),
      "[ProtectedContent.View]" => base64_encode(rtrim($view, "&"))
     ],
     "ExtensionID" => "a1f9348036f81e1e9b79550e03f825fb"
    ];
    if($dialog == 1) {
     $_Dialog = [
      "Body" => $_View,
      "Header" => "Authentication Required",
      "NoClose" => 1
     ];
     $_View = "";
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
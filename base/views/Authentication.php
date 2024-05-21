<?php
 Class Authentication extends OH {
  function __construct() {
   parent::__construct();
   $this->authID = md5($this->core->timestamp.uniqid());
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function ArticleChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $r = $this->core->Change([[
     "[Roles.ID]" => $Page["ID"],
     "[Roles.Member]" => base64_decode($member),
     "[Roles.Processor]" => base64_encode("v=".base64_encode("Page:ChangeMemberRole")),
     "[Roles.Title]" => $Page["Title"]
    ], $this->core->Extension("270d16c83b59b067231b0c6124a4038d")]);
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
  function BlogChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
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
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
    $r = $this->core->Change([[
     "[Roles.ID]" => $blog["ID"],
     "[Roles.Member]" => base64_decode($member),
     "[Roles.Processor]" => base64_encode("v=".base64_encode("Blog:ChangeMemberRole")),
     "[Roles.Title]" => $blog["Title"]
    ], $this->core->Extension("270d16c83b59b067231b0c6124a4038d")]);
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
  function PFChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $member = $data["Member"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $r = $this->core->Change([[
     "[Roles.ID]" => $forum["ID"],
     "[Roles.Member]" => base64_decode($member),
     "[Roles.Processor]" => base64_encode("v=".base64_encode("Forum:ChangeMemberRole")),
     "[Roles.Title]" => $forum["Title"]
    ], $this->core->Extension("270d16c83b59b067231b0c6124a4038d")]);
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
  function ProtectedContent(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $dialog = $data["Dialog"] ?? 0;
   $header = $data["Header"] ?? base64_encode("");
   $header = base64_decode($header);
   $parentPage = $data["ParentPage"] ?? "";
   $r = [
    "Body" => "The View Data is missing."
   ];
   $responseType = "Dialog";
   $signOut = $data["SignOut"] ?? base64_encode(0);
   $signOut = base64_decode($signOut);
   $text = $data["Text"] ?? base64_encode("Please enter your PIN below to continue.");
   $text = base64_decode($text);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } else {
    $accessCode = "Accepted";
    $back = (!empty($parentPage)) ? $this->core->Element(["button", "Back", [
     "class" => "GoToParent LI",
     "data-type" => $parentPage
    ]]) : "";
    $closeDialog = ($dialog == 1) ? $this->core->Element([
     "button", "Cancel", ["class" => "CloseDialog v2 v2w"]
    ]) : "";
    $view = "";
    $viewData = $data["ViewData"] ?? base64_encode(json_encode([]));
    $viewData = json_decode(base64_decode($viewData), true);
    foreach($viewData as $key => $value) {
     $view .= "$key=$value&";
    }
    $r = $this->core->Change([[
     "[ProtectedContent.Back]" => $back,
     "[ProtectedContent.CloseDialog]" => $closeDialog,
     "[ProtectedContent.Header]" => $header,
     "[ProtectedContent.SignOut]" => base64_decode($signOut),
     "[ProtectedContent.Text]" => $text,
     "[ProtectedContent.View]" => base64_encode(rtrim($view, "&"))
    ], $this->core->Extension("a1f9348036f81e1e9b79550e03f825fb")]);
    $r = ($dialog == 1) ? [
     "Body" => $r,
     "Header" => "Authentication Required",
     "NoClose" => 1
    ] : $r;
    $responseType = ($dialog == 1) ? "Dialog" : "View";
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
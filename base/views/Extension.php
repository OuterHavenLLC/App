<?php
 Class Extension extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $new = $data["New"] ?? 0;
   $r = [
    "Body" => "The Extension Identifier is missing."
   ];
   $time = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $id = ($new == 1) ? md5($you."Extension".$time) : base64_decode($id);
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditExtension$id",
     "data-processor" => base64_encode("v=".base64_encode("Extension:Save"))
    ]]);
    $extension = $this->core->Extensions();
    $extension = $extension[$id] ?? [];
    $body = $extension["Body"] ?? "";
    $categories = [
     "ArticleTemplate" => "Article Template",
     "BlogTemplate" => "Blog Template",
     "Extension" => "Extension"
    ];
    $category = $extension["Category"] ?? base64_encode("Extension");
    $description = $extension["Description"] ?? "";
    $title = $extension["Title"] ?? "";
    $header = ($new == 1) ? "New Extension" : "Edit ".base64_decode($title);
    $r = $this->core->Change([[
     "[Extension.Body]" => base64_encode($this->core->PlainText([
      "Data" => base64_decode($body)
     ])),
     "[Extension.Categories]" => json_encode($categories, true),
     "[Extension.Category]" => base64_decode($category),
     "[Extension.Description]" => $description,
     "[Extension.Header]" => $header,
     "[Extension.ID]" => $id,
     "[Extension.New]" => $new,
     "[Extension.Title]" => $title
    ], $this->core->Extension("5f7686825eb763cda93b62656a96a05f")]);
    $r = [
     "Action" => $action,
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $r = [
    "Body" => "The Article Identifier is missing.",
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
    $accessCode = "Accepted";
    $actionTaken = ($new == 1) ? "posted" : "updated";
    $body = $data["Body"] ?? "";
    $category = $data["Category"] ?? "Extension";
    $extensions = $this->core->Extensions();
    $extension = $extensions[$id] ?? [];
    $author = $extension["UN"] ?? $you;
    $now = $this->core->timestamp;
    $title = $data["Title"] ?? "";
    $newCategory = "Extension";
    $newCategory = ($category == "ArticleTemplate") ? "Article Template" : $newCategory;
    $newCategory = ($category == "BlogTemplate") ? "Blog Template" : $newCategory;
    $created = $extension["Created"] ?? $now;
    $modifiedBy = $extension["ModifiedBy"] ?? [];
    $modifiedBy[$now] = $you;
    $extension = [
     "Body" => base64_encode($this->core->PlainText([
      "Data" => $body,
      "HTMLEncode" => 1
     ])),
     "Category" => base64_encode($category),
     "Created" => base64_encode($created),
     "Description" => base64_encode($data["Description"]),
     "ModifiedBy" => $modifiedBy,
     "Title" => base64_encode($title),
     "UN" => base64_encode($author)
    ];
    $extensions[$id] = $extension;
    file_put_contents($this->core->DocumentRoot."/data/c.oh.app.".md5("Extensions"), json_encode($extensions, true));
    $r = [
     "Body" => "The $newCategory has been $actionTaken!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "Success" => "CloseCard"
   ]);
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $pin = $data["PIN"] ?? "";
   $r = [
    "Body" => "The Extension Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($pin) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match.",
     "Header" => "Error"
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $extensions = $this->core->Extensions();
    $newExtensions = [];
    foreach($extensions as $key => $extension) {
     if($id != $key) {
      $newExtensions[$key] = $extension;
     }
    }
    $newExtensions = json_encode($newExtensions, true);
    file_put_contents($this->core->DocumentRoot."/data/c.oh.app.".md5("Extensions"), $newExtensions);
    $r = [
     "Body" => "The App Extension was deleted.",
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
    "Success" => "CloseDialog"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
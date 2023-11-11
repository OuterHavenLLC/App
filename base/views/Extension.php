<?php
 Class Extension extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $buttion = "";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $new = $data["new"] ?? 0;
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
    $id = base64_decode($id);
    $id = ($new == 1) ? md5($you."Extension".$time) : $id;
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditPage$id",
     "data-processor" => base64_encode("v=".base64_encode("Extension:Save"))
    ]]);
    $extension = $this->core->Extensions();
    $extension = $extension[$id] ?? [];
    $author = $extension["UN"] ?? $you;
    $body = $extension["Body"] ?? "";
    $categories = [
     "ArticleTemplate" => "Article Template",
     "BlogTemplate" => "Blog Template",
     "Extension" => "Extension"
    ];
    $category = $extension["Category"] ?? base64_encode("Extension");
    $category = base64_decode($category);
    $description = $extension["Description"] ?? "";
    $title = $extension["Title"] ?? "";
    $header = ($new == 1) ? "New Extension" : "Edit ".base64_decode($title);
    $r = $this->core->Change([[
     //BEGIN TEMP
      "[Article.Attachments]" => "",
      "[Article.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=.NA&ID="),
      "[Article.CoverPhoto]" => "",
      "[Article.CoverPhoto.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=.NA&ID="),
      "[Article.Products]" => "",
      "[Article.Products.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=.NA&ID="),
     //END TEMP
     "[Article.Body]" => $body,
     "[Article.Categories]" => json_encode($categories, true),
     "[Article.Category]" => $category,
     "[Article.Description]" => $description,
     "[Article.Header]" => $header,
     "[Article.ID]" => $id,
     "[Article.New]" => $new,
     "[Article.Title]" => $title
    ], $this->core->Extension("68526a90bfdbf5ea5830d216139585d7")]);
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
     "Body" => base64_encode($data["Body"]),
     "Category" => base64_encode($category),
     "Created" => base64_encode($created),
     "Description" => base64_encode($data["Description"]),
     "ModifiedBy" => $modifiedBy,
     "Title" => base64_encode($title),
     "UN" => base64_encode($author)
    ];
    $extensions[$id] = $extension;
    #file_put_contents($this->core->DocumentRoot."/data/c.oh.app.".md5("Extensions"), json_encode($extensions, true));
    $r = [
     "Body" => "The $newCategory has been $actionTaken!",
     "Header" => "Done",
     "Scrollable" => json_encode($extension, true)
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    #"Success" => "CloseCard"
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
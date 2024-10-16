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
    $id = ($new == 1) ? $this->core->UUID($you."Extension") : base64_decode($id);
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditExtension$id",
     "data-processor" => base64_encode("v=".base64_encode("Extension:Save"))
    ]]);
    $extension = $this->core->Data("Get", ["extension", $id]);
    $body = $extension["Body"] ?? "";
    $categories = [
     "ArticleTemplate" => "Article Template",
     "BlogTemplate" => "Blog Template",
     "Extension" => "Extension"
    ];
    $category = $extension["Category"] ?? "Extension";
    $created = $extension["Created"] ?? $this->core->timestamp;
    $description = $extension["Description"] ?? "";
    $title = $extension["Title"] ?? "";
    $translateAndViewDeign = $this->view(base64_encode("WebUI:Attachments"), [
     "ID" => $id,
     "Media" => [
      "Translate" => [],
      "ViewDesign" => []
     ]
    ]);
    $header = ($new == 1) ? "New Extension" : "Edit $title";
    $r = $this->core->Change([[
     "[Extension.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body
     ])),
     "[Extension.Categories]" => json_encode($categories, true),
     "[Extension.Category]" => $category,
     "[Extension.Created]" => $created,
     "[Extension.Description]" => base64_encode($description),
     "[Extension.Header]" => $header,
     "[Extension.ID]" => $id,
     "[Extension.New]" => $new,
     "[Extension.Title]" => base64_encode($title),
     "[Extension.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign)
    ], $this->core->Extension("5f7686825eb763cda93b62656a96a05f")]);
    $r = [
     "Action" => $action,
     "Front" => $r
    ];
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
  function Purge(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? base64_encode("");
   $id = base64_decode($id);
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $r = [
    "Body" => "The Extension Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(empty($key)) {
    $r = [
     "Body" => "The Key is missing."
    ];
   } elseif(md5($key) != $secureKey) {
    $r = [
     "Body" => "The Keys do not match."
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $extension = $this->core->Data("Get", ["extension", $id]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM Extensions WHERE Extension_ID=:ID", [
     ":ID" => $id
    ]);
    $sql->execute();
    if(!empty($extension)) {
     $extension["Purge"] = 1;
     $this->core->Data("Save", ["extension", $id, $extension]);
    }
    $r = $this->core->Element([
     "p", "The App Extension was marked for purging.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
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
    $body = $data["Body"] ?? "";
    $category = $data["Category"] ?? "Extension";
    $created = $data["Created"] ?? $this->core->timestamp;
    $description = $data["Description"] ?? "";
    $newCategory = "Extension";
    $newCategory = ($category == "ArticleTemplate") ? "Article Template" : $newCategory;
    $newCategory = ($category == "BlogTemplate") ? "Blog Template" : $newCategory;
    $title = $data["Title"] ?? "";
    $extension = [
     "Body" => $this->core->PlainText([
      "Data" => $body,
      "HTMLEncode" => 1
     ]),
     "Category" => $category,
     "Created" => $created,
     "Description" => $description,
     "Title" => $title,
     "UN" => $you
    ];
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $query = "REPLACE INTO Extensions(
     Extension_Body,
     Extension_Created,
     Extension_Description,
     Extension_ID,
     Extension_Title,
     Extension_Username
    ) VALUES(
     :Body,
     :Created,
     :Description,
     :ID,
     :Title,
     :Username
    )";
    $sql->query($query, [
     ":Body" => $this->core->Excerpt($this->core->PlainText([
      "Data" => $extension["Body"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]), 1000),
     ":Created" => $created,
     ":Description" => $extension["Description"],
     ":ID" => $id,
     ":Title" => $extension["Title"],
     ":Username" => $extension["UN"]
    ]);
    $sql->execute();
    $this->core->Data("Save", ["extension", $id, $extension]);
    $r = [
     "Body" => "The $newCategory has been saved!",
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
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
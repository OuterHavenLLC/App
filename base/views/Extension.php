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
    $extension = $this->core->Data("Get", ["extension", $id]) ?? [];
    $body = $extension["Body"] ?? "";
    $categories = [
     "ArticleTemplate" => "Article Template",
     "BlogTemplate" => "Blog Template",
     "Extension" => "Extension"
    ];
    $category = $extension["Category"] ?? "Extension";
    $description = $extension["Description"] ?? "";
    $title = $extension["Title"] ?? "";
    $header = ($new == 1) ? "New Extension" : "Edit $title";
    $r = $this->core->Change([[
     "[Extension.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => "Page",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=NA&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".base64_encode($you)),
       "[Extras.DesignView.Origin]" => "NA",
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=NA&Added=NA&UN=".base64_encode($you)),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Translate:Edit")."&ID=".base64_encode($id))
      ], $this->core->Extension("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Extension.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body
     ])),
     "[Extension.Categories]" => json_encode($categories, true),
     "[Extension.Category]" => $category,
     "[Extension.Description]" => base64_encode($description),
     "[Extension.Header]" => $header,
     "[Extension.ID]" => $id,
     "[Extension.New]" => $new,
     "[Extension.Title]" => base64_encode($title)
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
    if(!empty($extension)) {
     $extension["Purge"] = 1;
     $this->core->Data("Save", ["extension", $id, $extension]);
    }
    $r = $this->core->Element([
     "p", "The App Extension was deleted.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
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
     "Description" => $description,
     "Title" => $title,
     "UN" => $you
    ];
    $this->core->Data("Save", ["extension", $id, $extension]);
    $r = [
     "Body" => "The $newCategory has been saved!",
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
    "Success" => "CloseCard"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
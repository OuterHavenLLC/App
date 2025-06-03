<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol . $_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
 Class Authentication extends OH {
  function __construct() {
   parent::__construct();
   $this->authID = md5($this->core->timestamp.uniqid());
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function ArticleChangeMemberRole(array $data): string {
   $_Commands = "";
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
    $id = base64_decode($id);
    $_Article = $this->core->Data("Get", ["pg", $id]);
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".Authentication$id",
       [
        [
         "Attributes" => [
          "name" => "ID",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $id
        ],
        [
         "Attributes" => [
          "name" => "Member",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => base64_decode($member)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "PIN",
          "pattern" => "d*",
          "placeholder" => "PIN",
          "type" => "number"
         ],
         "Options" => [
          "Header" => 1,
          "HeaderText" => "Enter Your PIN"
         ],
         "Type" => "Text"
        ],
        [
         "Attributes" => [],
         "OptionGroup" => [
          "0" => "Administrator",
          "1" => "Contributor"
         ],
         "Options" => [
          "Header" => 1,
          "HeaderText" => "Chose a Role"
         ],
         "Name" => "Role",
         "Type" => "Select"
        ]
       ]
      ]
     ]
    ];
    $_Dialog = "";
    $_View = [
     "ChangeData" => [
      "[Roles.ID]" => $id,
      "[Roles.Processor]" => base64_encode("v=".base64_encode("Page:ChangeMemberRole")),
      "[Roles.Title]" => $_Article["Title"]
     ],
     "ExtensionID" => "270d16c83b59b067231b0c6124a4038d"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function BlogChangeMemberRole(array $data): string {
   $_Dialog = [
    "Body" => "The Blog Identifier is missing."
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
    $id = base64_decode($id);
    $_Blog = $this->core->Data("Get", ["blg", $id]);
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".Authentication$id",
       [
        [
         "Attributes" => [
          "name" => "ID",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $id
        ],
        [
         "Attributes" => [
          "name" => "Member",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => base64_decode($member)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "PIN",
          "pattern" => "d*",
          "placeholder" => "PIN",
          "type" => "number"
         ],
         "Options" => [
          "Header" => 1,
          "HeaderText" => "Enter Your PIN"
         ],
         "Type" => "Text"
        ],
        [
         "Attributes" => [],
         "OptionGroup" => [
          "0" => "Administrator",
          "1" => "Contributor"
         ],
         "Options" => [
          "Header" => 1,
          "HeaderText" => "Chose a Role"
         ],
         "Name" => "Role",
         "Type" => "Select"
        ]
       ]
      ]
     ]
    ];
    $_Dialog = "";
    $_View = [
     "ChangeData" => [
      "[Roles.ID]" => $id,
      "[Roles.Processor]" => base64_encode("v=".base64_encode("Blog:ChangeMemberRole")),
      "[Roles.Title]" => $_Blog["Title"]
     ],
     "ExtensionID" => "270d16c83b59b067231b0c6124a4038d"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function PFChangeMemberRole(array $data): string {
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
    $id = base64_decode($id);
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".Authentication$id",
       [
        [
         "Attributes" => [
          "name" => "ID",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $id
        ],
        [
         "Attributes" => [
          "name" => "Member",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => base64_decode($member)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "PIN",
          "pattern" => "d*",
          "placeholder" => "PIN",
          "type" => "number"
         ],
         "Options" => [
          "Header" => 1,
          "HeaderText" => "Enter Your PIN"
         ],
         "Type" => "Text"
        ],
        [
         "Attributes" => [],
         "OptionGroup" => [
          "0" => "Administrator",
          "1" => "Contributor"
         ],
         "Options" => [
          "Header" => 1,
          "HeaderText" => "Chose a Role"
         ],
         "Name" => "Role",
         "Type" => "Select"
        ]
       ]
      ]
     ]
    ];
    $_Dialog = "";
    $_Forum = $this->core->Data("Get", ["pf", $id]);
    $_View = [
     "ChangeData" => [
      "[Roles.ID]" => $id,
      "[Roles.Processor]" => base64_encode("v=".base64_encode("Forum:ChangeMemberRole")),
      "[Roles.Title]" => $_Forum["Title"]
     ],
     "ExtensionID" => "270d16c83b59b067231b0c6124a4038d"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function ProtectedContent(array $data): string {
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
     "button", "Cancel", ["class" => "BB CloseDialog v2 v2w"]
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
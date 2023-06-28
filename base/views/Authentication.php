<?php
 Class Authentication extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function ArticleChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $mbr = $data["Member"] ?? "";
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
    $r = $this->system->Change([[
     "[Roles.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $Page["ID"]
      ],
      [
       "Attributes" => [
        "name" => "Member",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => base64_decode($mbr)
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
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
        0 => "Administrator",
        1 => "Contributor"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Chose a Role"
       ],
       "Name" => "Role",
       "Type" => "Select"
      ]
     ]),
     "[Roles.Processor]" => base64_encode("v=".base64_encode("Page:ChangeMemberRole")),
     "[Roles.Title]" => $Page["Title"]
    ], $this->system->Page("270d16c83b59b067231b0c6124a4038d")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function AuthorizeChange(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $form = $data["Form"] ?? "";
   $id = $data["ID"] ?? "";
   $processor = $data["Processor"] ?? "";
   $text = $data["Text"] ?? base64_encode("Do you authorize this Change?");
   $r = [
    "Body" => "The Form Identifier or Processor are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($form) && !empty($id) && !empty($processor)) {
    $accessCode = "Accepted";
    $r = $this->system->Change([[
     "[Authorize.PIN]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "class" => "req",
        "data-id" => $id,
        "name" => "AuthorizationPIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Authorize.Form]" => base64_decode($form),
     "[Authorize.Text]" => base64_decode($text),
     "[Authorize.Processor]" => $processor
    ], $this->system->Page("7f6ec4e23b8b7c616bb7d79b2d1d3157")]);
   }
   return $this->system->JSONResponse([
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
   $mbr = $data["Member"] ?? "";
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $r = $this->system->Change([[
     "[Roles.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $blog["ID"]
      ],
      [
       "Attributes" => [
        "name" => "Member",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => base64_decode($mbr)
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
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
        0 => "Administrator",
        1 => "Contributor"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Chose a Role"
       ],
       "Name" => "Role",
       "Type" => "Select"
      ]
     ]),
     "[Roles.Processor]" => base64_encode("v=".base64_encode("Blog:ChangeMemberRole")),
     "[Roles.Title]" => $blog["Title"]
    ], $this->system->Page("270d16c83b59b067231b0c6124a4038d")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteAlbum(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $aid = $data["AID"] ?? md5("unsorted");
   $r = [
    "Body" => "The Album Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($aid)) {
    $accessCode = "Accepted";
    $album = $this->system->Data("Get", ["fs", md5($you)]) ?? [];
    $album = $album["Albums"][$aid] ?? [];
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $album["ID"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("Album:SaveDelete")),
     "[Delete.Title]" => $album["Title"]
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteBlog(array $a) {
   $accessCode = "Denied";
   $delete = base64_encode("Blog:SaveDelete");
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $blog["ID"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("Blog:SaveDelete")),
     "[Delete.Title]" => $blog["Title"]
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteBlogPost(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Blog-Post Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $post = explode("-", base64_decode($id));
    $post = $this->system->Data("Get", ["bp", $post[1]]) ?? [];
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => base64_decode($id)
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("BlogPost:SaveDelete")),
     "[Delete.Title]" => $post["Title"]
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteDiscountCode(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $r = [
    "Body" => "The Code Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $data["ID"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("DiscountCode:SaveDelete")),
     "[Delete.Title]" => "this Discount Code"
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteFAB(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $r = [
    "Body" => "The Broadcaster Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $id = base64_decode($data["ID"]);
    $fab = $this->system->Data("Get", [
     "x",
     md5("FreeAmericaBroadcasting")
    ]) ?? [];
    $fab = $fab[$id]["Title"] ?? "Broadcaster";
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $data["ID"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("FAB:SaveDelete")),
     "[Delete.Title]" => $fab
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteFile(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $username = $data["UN"] ?? "";
   $r = [
    "Body" => "The File Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($username)) {
    $accessCode = "Accepted";
    $username = base64_decode($username);
    $files = $this->system->Data("Get", ["fs", md5($you)]) ?? [];
    $files = $files["Files"] ?? [];
    $files = ($this->system->ID == $username) ? $this->system->Data("Get", [
     "x",
     "fs"
    ]) : $files;
    $file = $files[$id] ?? [];
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => "$username-$id"
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("File:SaveDelete")."&ParentView=".$data["ParentView"]),
     "[Delete.Title]" => $file["Title"]
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteForum(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($id == "cb3e432f76b38eaa66c7269d658bd7ea") {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You cannot delete this forum."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $title = $forum["Title"] ?? "all forums";
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
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
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("Forum:SaveDelete")),
     "[Delete.Title]" => $title
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteForumPost(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["FID", "ID"]);
   $all = $data["all"] ?? 0;
   $fid = $data["FID"];
   $id = $data["ID"];
   $r = [
    "Body" => "The Forum or Post Identifiers are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif((!empty($fid) && !empty($id))) {
    $accessCode = "Accepted";
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => base64_encode("$fid-$id")
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("ForumPost:SaveDelete")),
     "[Delete.Title]" => $post["Title"]
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeletePage(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $page = $this->system->Data("Get", ["pg", $data["ID"]]) ?? [];
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $page["ID"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Title]" => $page["Title"],
     "[Delete.Processor]" => base64_encode("v=".base64_encode("Page:SaveDelete"))
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteProduct(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID"]);
   $all = $data["all"] ?? 0;
   $pd = base64_encode("Product:SaveDelete");
   $r = [
    "Body" => "The Product Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $product = $this->system->Data("Get", ["miny", $data["ID"]]) ?? [];
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $product["ID"]
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("Product:SaveDelete")),
     "[Delete.Title]" => $product["Title"]
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function DeleteStatusUpdate(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Update Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $r = $this->system->Change([[
     "[Delete.Inputs]" => $this->system->RenderInputs([
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
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
        "placeholder" => "PIN",
        "type" => "number"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Enter Your PIN"
       ],
       "Type" => "Text"
      ]
     ]),
     "[Delete.Processor]" => base64_encode("v=".base64_encode("StatusUpdate:SaveDelete")),
     "[Delete.Title]" => "this post"
    ], $this->system->Page("fca4a243a55cc333f5fa35c8e32dd2a0")]);
   }
   return $this->system->JSONResponse([
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
   $mbr = $data["Member"] ?? "";
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $r = $this->system->Change([[
     "[Roles.Inputs]" => $this->system->RenderInputs([
      [
       "Attributes" => [
        "name" => "ID",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $forum["ID"]
      ],
      [
       "Attributes" => [
        "name" => "Member",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => base64_decode($mbr)
      ],
      [
       "Attributes" => [
        "class" => "req",
        "name" => "PIN",
        "pattern" => "\d*",
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
        0 => "Administrator",
        1 => "Contributor"
       ],
       "Options" => [
        "Header" => 1,
        "HeaderText" => "Chose a Role"
       ],
       "Name" => "Role",
       "Type" => "Select"
      ]
     ]),
     "[Roles.Processor]" => base64_encode("v=".base64_encode("Forum:ChangeMemberRole")),
     "[Roles.Title]" => $forum["Title"]
    ], $this->system->Page("270d16c83b59b067231b0c6124a4038d")]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
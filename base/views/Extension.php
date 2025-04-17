<?php
 Class Extension extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data) {
   $_Dialog = [
    "Body" => "The Extension Identifier is missing."
   ];
   $_Card = "";
   $_Commands = "";
   $_Dialog = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $new = $data["New"] ?? 0;
   $time = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $_Dialog = "";
    $id = ($new == 1) ? $this->core->UUID("ExtensionBy$you") : base64_decode($id);
    $action = ($new == 1) ? "Post" : "Update";
    $extension = $this->core->Data("Get", ["extension", $id]);
    $albums = $extension["Albums"] ?? [];
    $articles = $extension["Articles"] ?? [];
    $attachments = $extension["Attachments"] ?? [];
    $blogs = $extension["Blogs"] ?? [];
    $blogPosts = $extension["BlogPosts"] ?? [];
    $body = $extension["Body"] ?? "";
    $category = $extension["Category"] ?? "Extension";
    $chats = $extension["Chat"] ?? [];
    $description = $extension["Description"] ?? "";
    $forums = $extension["Forums"] ?? [];
    $forumPosts = $extension["ForumPosts"] ?? [];
    $members = $extension["Members"] ?? [];
    $polls = $extension["Polls"] ?? [];
    $products = $extension["Products"] ?? [];
    $shops = $extension["Shops"] ?? [];
    $title = $extension["Title"] ?? "";
    $updates = $extension["Updates"] ?? [];
    $attachments = $this->view(base64_encode("WebUI:Attachments"), [
     "Header" => "Attachments",
     "ID" => $id,
     "Media" => [
      "Album" => $albums,
      "Article" => $articles,
      "Attachment" => $attachments,
      "Blog" => $blogs,
      "BlogPost" => $blogPosts,
      "Chat" => $chats,
      "Forum" => $forums,
      "ForumPost" => $forumPosts,
      "Member" => $members,
      "Poll" => $polls,
      "Product" => $products,
      "Shop" => $shops,
      "Update" => $updates
     ]
    ]);
    $translateAndViewDeign = $this->view(base64_encode("WebUI:Attachments"), [
     "ID" => $id,
     "Media" => [
      "Translate" => [],
      "ViewDesign" => []
     ]
    ]);
    $header = ($new == 1) ? "New Extension" : "Edit $title";
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".EditExtension$id",
      "data-processor" => base64_encode("v=".base64_encode("Extension:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Extension.Attachments]" => $this->core->RenderView($attachments),
       "[Extension.Header]" => $header,
       "[Extension.ID]" => $id,
       "[Extension.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign)
      ],
      "ExtensionID" => "5f7686825eb763cda93b62656a96a05f"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".General$id",
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
          "name" => "New",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $new
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Title",
          "placeholder" => "Title",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Title"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($title)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Description",
          "placeholder" => "Description"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Description"
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($description)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "data-editor-identifier" => $id,
          "name" => "Body",
          "placeholder" => "Body"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Body",
          "WYSIWYG" => 1
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($this->core->PlainText([
          "Data" => $body
         ]))
        ],
        [
         "Attributes" => [],
         "OptionGroup" => [
          "ArticleTemplate" => "Article Template",
          "BlogTemplate" => "Blog Template",
          "Extension" => "Extension"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Category"
         ],
         "Name" => "Category",
         "Title" => "Category",
         "Type" => "Select",
         "Value" => $category
        ]
       ]
      ]
     ]
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function Purge(array $data) {
   $_Dialog = [
    "Body" => "The Extension Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? base64_encode("");
   $id = base64_decode($id);
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(empty($key)) {
    $_Dialog = [
     "Body" => "The Key is missing."
    ];
   } elseif(md5($key) != $secureKey) {
    $_Dialog = "";
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
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
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", "The Extension <em>".$extension["Title"]."</em> was marked for purging.",
      ["class" => "CenterText"]
     ]).$this->core->Element([
      "button", "Okay", ["class" => "CloseDialog v2 v2w"]
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Article Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $now = $this->core->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $extension = $this->core->Data("Get", ["extension", $id]);
    $albums = [];
    $albumsData = $data["Album"] ?? [];
    $articles = [];
    $articlesData = $data["Article"] ?? [];
    $attachments = [];
    $attachmentsData = $data["Attachment"] ?? [];
    $blogs = [];
    $blogsData = $data["Blog"] ?? [];
    $blogPosts = [];
    $blogPostsData = $data["BlogPost"] ?? [];
    $body = $data["Body"] ?? "";
    $chats = [];
    $chatsData = $data["Chat"] ?? [];
    $category = $data["Category"] ?? "Extension";
    $created = $extension["Created"] ?? $now;
    $description = $data["Description"] ?? "";
    $forums = [];
    $forumsData = $data["Forum"] ?? [];
    $forumPosts = [];
    $forumPostsData = $data["ForumPost"] ?? [];
    $members = []; 
    $membersData = $data["Member"] ?? [];
    $modifiedBy = $extension["ModifiedBy"] ?? [];
    $modifiedBy[$now] = $you;
    $newCategory = "Extension";
    $newCategory = ($category == "ArticleTemplate") ? "Article Template" : $newCategory;
    $newCategory = ($category == "BlogTemplate") ? "Blog Template" : $newCategory;
    $polls = []; 
    $pollsData = $data["Poll"] ?? [];
    $products = [];
    $productsData = $data["Product"] ?? [];
    $purge = $extension["Purge"] ?? 0;
    $shops = [];
    $shopsData = $data["Shop"] ?? [];
    $title = $data["Title"] ?? "";
    $updates = [];
    $updatesData = $data["Update"] ?? [];
    if(!empty($albumsData)) {
     $media = $albumsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($albums, $media[$i]);
      }
     }
    } if(!empty($articlesData)) {
     $media = $articlesData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($articles, $media[$i]);
      }
     }
    } if(!empty($attachmentsData)) {
     $media = $attachmentsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($attachments, $media[$i]);
      }
     }
    } if(!empty($blogsData)) {
     $media = $blogsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($blogs, $media[$i]);
      }
     }
    } if(!empty($blogPostsData)) {
     $media = $blogPostsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($blogPosts, $media[$i]);
      }
     }
    } if(!empty($chatsData)) {
     $media = $chatsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($chats, $media[$i]);
      }
     }
    } if(!empty($forumsData)) {
     $media = $forumsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($forums, $media[$i]);
      }
     }
    } if(!empty($forumPostsData)) {
     $media = $forumPostsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($forumPosts, $media[$i]);
      }
     }
    } if(!empty($membersData)) {
     $media = $membersData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($members, $media[$i]);
      }
     }
    } if(!empty($pollsData)) {
     $media = $pollsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($polls, $media[$i]);
      }
     }
    } if(!empty($productsData)) {
     $media = $productsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($products, $media[$i]);
      }
     }
    } if(!empty($shopsData)) {
     $media = $shopsData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($shops, $media[$i]);
      }
     }
    } if(!empty($updatesData)) {
     $media = $updatesData;
     for($i = 0; $i < count($media); $i++) {
      if(!empty($media[$i])) {
       array_push($updates, $media[$i]);
      }
     }
    }
    $extension = [
     "Albums" => $albums,
     "Articles" => $articles,
     "Attachments" => $attachments,
     "Blogs" => $blogs,
     "BlogPosts" => $blogPosts,
     "Body" => $this->core->PlainText([
      "Data" => $body,
      "HTMLEncode" => 1
     ]),
     "Category" => $category,
     "Chats" => $chats,
     "Created" => $created,
     "Description" => $description,
     "Forums" => $forums,
     "ForumPosts" => $forumPosts,
     "Members" => $members,
     "Modified" => $now,
     "ModifiedBy" => $modifiedBy,
     "Polls" => $polls,
     "Products" => $products,
     "Purge" => $purge,
     "Shops" => $shops,
     "Title" => $title,
     "UN" => $you,
     "Updates" => $updates
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
    $_Dialog = [
     "Body" => "The $newCategory has been saved!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
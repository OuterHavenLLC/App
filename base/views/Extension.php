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
    $id = ($new == 1) ? $this->core->UUID("ExtensionBy$you") : base64_decode($id);
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditExtension$id",
     "data-processor" => base64_encode("v=".base64_encode("Extension:Save"))
    ]]);
    $extension = $this->core->Data("Get", ["extension", $id]);
    $albums = $extension["Albums"] ?? [];
    $articles = $extension["Articles"] ?? [];
    $attachments = $extension["Attachments"] ?? [];
    $blogs = $extension["Blogs"] ?? [];
    $blogPosts = $extension["BlogPosts"] ?? [];
    $body = $extension["Body"] ?? "";
    $categories = [
     "ArticleTemplate" => "Article Template",
     "BlogTemplate" => "Blog Template",
     "Extension" => "Extension"
    ];
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
    $r = $this->core->Change([[
     "[Extension.Attachments]" => $this->core->RenderView($attachments),
     "[Extension.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body
     ])),
     "[Extension.Categories]" => json_encode($categories, true),
     "[Extension.Category]" => $category,
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
   $now = $this->core->timestamp;
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
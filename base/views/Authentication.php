<?php
 Class Authentication extends GW {
  function __construct() {
   parent::__construct();
   $this->authID = md5($this->core->timestamp.uniqid());
   $this->you = $this->core->Member($this->core->Username());
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
    ], $this->core->Page("270d16c83b59b067231b0c6124a4038d")]);
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
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($form) && !empty($id) && !empty($processor)) {
    $accessCode = "Accepted";
    $r = [
     "Body" => base64_decode($text),
     "Header" => "Authorize",
     "Scrollable" => $this->core->Change([[
      "[Authorize.Form]" => base64_decode($form),
      "[Authorize.ID]" => $id,
      "[Authorize.Processor]" => $processor
     ], $this->core->Page("7f6ec4e23b8b7c616bb7d79b2d1d3157")])
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
    ], $this->core->Page("270d16c83b59b067231b0c6124a4038d")]);
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
  function DeleteAlbum(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $aid = $data["AID"] ?? md5("unsorted");
   $r = [
    "Body" => "The Album Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($aid)) {
    $accessCode = "Accepted";
    $album = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
    $album = $album["Albums"][$aid] ?? [];
    $dialogID = "Delete$aid";
    $r = [
     "Body" => "You are about to permanently delete ".$album["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $album["ID"],
      "[Delete.Processor]" => base64_encode("v=".base64_encode("Album:SaveDelete")),
      "[Delete.Title]" => $album["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $dialogID = "Delete$id";
    $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
    $r = [
     "Body" => "You are about to permanently delete ".$blog["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $blog["ID"],
      "[Delete.Processor]" => base64_encode("v=".base64_encode("Blog:SaveDelete")),
      "[Delete.Title]" => $blog["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteBlogPost(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Blog-Post Identifier is missing."
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
    $post = explode("-", base64_decode($id));
    $post = $this->core->Data("Get", ["bp", $post[1]]) ?? [];
    $dialogID = "Delete".$post[1];
    $r = [
     "Body" => "You are about to permanently delete ".$post["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => base64_decode($id),
      "[Delete.Processor]" => base64_encode("v=".base64_encode("BlogPost:SaveDelete")),
      "[Delete.Title]" => $post["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteChat(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Chat Identifier is missing."
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
    $chat = $this->core->Data("Get", ["chat", $id]) ?? [];
    $dialogID = "Delete$id";
    $r = [
     "Body" => "You are about to permanently delete ".$chat["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $id,
      "[Delete.Processor]" => base64_encode("v=".base64_encode("Chat:SaveDelete")),
      "[Delete.Title]" => $chat["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteDiscountCode(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
   $r = [
    "Body" => "The Code Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $dialogID = "Delete".$data["ID"];
    $r = [
     "Body" => "You are about to permanently delete this Discount Code.",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $data["ID"],
      "[Delete.Processor]" => base64_encode("v=".base64_encode("DiscountCode:SaveDelete")),
      "[Delete.Title]" => "this Discount Code"
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($username)) {
    $accessCode = "Accepted";
    $dialogID = "Delete$id";
    $username = base64_decode($username);
    $files = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
    $files = $files["Files"] ?? [];
    $files = ($this->core->ID == $username) ? $this->core->Data("Get", [
     "x",
     "fs"
    ]) : $files;
    $file = $files[$id] ?? [];
    $r = [
     "Body" => "You are about to permanently delete ".$file["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => base64_encode("$username-$id"),
      "[Delete.Processor]" => base64_encode("v=".base64_encode("File:SaveDelete")."&ParentView=".$data["ParentView"]),
      "[Delete.Title]" => $file["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteForum(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
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
   } elseif($id == "cb3e432f76b38eaa66c7269d658bd7ea") {
    $r = [
     "Body" => "You cannot delete this forum.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = base64_decode($id);
    $dialogID = "Delete$id";
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $title = $forum["Title"] ?? "this Forum";
    $r = [
     "Body" => "You are about to permanently delete ".$forum["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $id,
      "[Delete.Processor]" => base64_encode("v=".base64_encode("Forum:SaveDelete")),
      "[Delete.Title]" => $title
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteForumPost(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["FID", "ID"]);
   $all = $data["all"] ?? 0;
   $fid = $data["FID"];
   $id = $data["ID"];
   $r = [
    "Body" => "The Forum or Post Identifiers are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif((!empty($fid) && !empty($id))) {
    $accessCode = "Accepted";
    $dialogID = "Delete$id";
    $r = [
     "Body" => "You are about to permanently delete ".$post["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => base64_encode("$fid-$id"),
      "[Delete.Processor]" => base64_encode("v=".base64_encode("ForumPost:SaveDelete")),
      "[Delete.Title]" => $post["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeletePage(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
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
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $dialogID = "Delete".$data["ID"];
    $page = $this->core->Data("Get", ["pg", $data["ID"]]) ?? [];
    $r = [
     "Body" => "You are about to permanently delete ".$page["Title"].".",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $page["ID"],
      "[Delete.Processor]" => base64_encode("v=".base64_encode("Page:SaveDelete")),
      "[Delete.Title]" => $page["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteProduct(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID"]);
   $pd = base64_encode("Product:SaveDelete");
   $r = [
    "Body" => "The Product Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $dialogID = "Delete".$data["ID"];
    $product = $this->core->Data("Get", ["miny", $data["ID"]]) ?? [];
    $r = [
     "Body" => "You are about to permanently delete <em>".$product["Title"]."</em>.",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $product["ID"],
      "[Delete.Processor]" => base64_encode("v=".base64_encode("Product:SaveDelete")),
      "[Delete.Title]" => $product["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteStatusUpdate(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Update Identifier is missing."
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
    $dialogID = "Delete$id";
    $r = [
     "Body" => "You are about to permanently delete this update.",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => $id,
      "[Delete.Processor]" => base64_encode("v=".base64_encode("StatusUpdate:SaveDelete")),
      "[Delete.Title]" => "this post"
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
  function DeleteService(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Service or Shop Identifier are missing."
   ];
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($shopID)) {
    $accessCode = "Accepted";
    $dialogID = "Delete$id";
    $preset = $this->core->Data("Get", [
     "invoice-preset",
     $id
    ]) ?? [];
    $r = [
     "Body" => "You are about to permanently delete <em>".$preset["Title"]."</em>.",
     "Header" => "Delete",
     "ID" => $dialogID,
     "Scrollable" => $this->core->Change([[
      "[Delete.AuthorizationID]" => md5($this->core->timestamp.$you),
      "[Delete.ID]" => base64_encode("$shopID-$id"),
      "[Delete.Processor]" => base64_encode("v=".base64_encode("Invoice:DeletePreset")),
      "[Delete.Title]" => $preset["Title"]
     ], $this->core->Page("fca4a243a55cc333f5fa35c8e32dd2a0")])
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
    ], $this->core->Page("270d16c83b59b067231b0c6124a4038d")]);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
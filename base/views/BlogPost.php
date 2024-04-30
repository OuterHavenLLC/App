<?php
 Class BlogPost extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "Blog",
    "Post",
    "new"
   ]);
   $blog = $data["Blog"];
   $button = "";
   $new = $data["new"] ?? 0;
   $post = $data["Post"];
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
   } elseif((!empty($blog) && !empty($post)) || $new == 1) {
    $accessCode = "Accepted";
    $id = ($new == 1) ? md5($you."_BP_".uniqid()) : $post;
    $additionalContent = $this->view(base64_encode("WebUI:AdditionalContent"), [
     "ID" => $id
    ]);
    $additionalContent = $this->core->RenderView($additionalContent);
    $action = ($new == 1) ? "Post" : "Update";
    $action = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditBlogPost$id",
     "data-processor" => base64_encode("v=".base64_encode("BlogPost:Save"))
    ]]);
    $attachments = "";
    $blog = $this->core->Data("Get", ["blg", $blog]) ?? [];
    $post = $this->core->Data("Get", ["bp", $id]) ?? [];
    if(!empty($post["Attachments"])) {
     $attachments = base64_encode(implode(";", $post["Attachments"]));
    }
    $body = $post["Body"] ?? "";
    $coverPhoto = $post["ICO-SRC"] ?? "";
    $description = $post["Description"] ?? "";
    $designViewEditor = "ViewBlogPost$id";
    $header = ($new == 1) ? "New Post to ".$blog["Title"] : "Edit ".$post["Title"];
    $nsfw = $post["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $post["Privacy"] ?? $y["Privacy"]["Profile"];
    $search = base64_encode("Search:Containers");
    $template = $post["TPL"] ?? "";
    $templateOptions = $this->core->DatabaseSet("Extensions");
    $templates = [];
    foreach($templateOptions as $key => $value) {
     if($value["Category"] == "BlogTemplate") {
      $templates[$key] = $value["Title"];
     }
    }
    $title = $post["Title"] ?? "";
    $r = $this->core->Change([[
     "[Blog.ID]" => $blog["ID"],
     "[BlogPost.AdditionalContent]" => $additionalContent["Extension"],
     "[BlogPost.Attachments]" => $attachments,
     "[BlogPost.Attachments.LiveView]" => $additionalContent["LiveView"]["DLC"],
     "[BlogPost.Body]" => base64_encode($this->core->PlainText([
      "Data" => $body,
      "Decode" => 1
     ])),
     "[BlogPost.CoverPhoto]" => $coverPhoto,
     "[BlogPost.CoverPhoto.LiveView]" => $additionalContent["LiveView"]["CoverPhoto"],
     "[BlogPost.Description]" => base64_encode($description),
     "[BlogPost.DesignView]" => $header,
     "[BlogPost.Header]" => $header,
     "[BlogPost.ID]" => $id,
     "[BlogPost.New]" => $new,
     "[BlogPost.Title]" => base64_encode($title),
     "[BlogPost.Template]" => $template,
     "[BlogPost.Templates]" => json_encode($templates, true),
     "[BlogPost.Visibility.NSFW]" => $nsfw,
     "[BlogPost.Visibility.Privacy]" => $privacy
    ], $this->core->Extension("15961ed0a116fbd6cfdb793f45614e44")]);
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
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $blog = $data["Blog"] ?? "";
   $backTo = $data["b2"] ?? "Blog";
   $back = $this->core->Element(["button", "Back to <em>$backTo</em>", [
    "class" => "GoToParent LI head",
    "data-type" => "Blog$blog"
   ]]);
   $i = 0;
   $postID = $data["Post"] ?? "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Blog Post could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
    $accessCode = "Accepted";
    $blogPosts = $this->core->DatabaseSet("BlogPost");
    foreach($blogPosts as $key => $value) {
     $blogPost = $this->core->Data("Get", ["bp", $value]) ?? [];
     if(($blogPost["ID"] == $postID || $callSignsMatch == 1) && $i == 0) {
      $i++;
      $postID = $value;
     }
    }
   } if((!empty($blog) && !empty($postID)) || $i > 0) {
    $accessCode = "Accepted";
    $bl = $this->core->CheckBlocked([$y, "Blog Posts", $postID]);
    $_BlogPost = $this->core->GetContentData([
     "BackTo" => $backTo,
     "Blacklisted" => $bl,
     "ID" => base64_encode("BlogPost;$blog;$postID")
    ]);
    if($_BlogPost["Empty"] == 0) {
     $options = $_BlogPost["ListItem"]["Options"];
     $post = $_BlogPost["DataModel"];
     $author = ($post["UN"] == $you) ? $y : $this->core->Member($post["UN"]);
     $ck = ($author["Login"]["Username"] == $you) ? 1 : 0;
     $description = $author["Personal"]["DisplayName"] ?? "";
     $extension = $post["TPL"] ?? "b793826c26014b81fdc1f3f94a52c9a6";
     $contributors = $post["Contributors"] ?? [];
     $contributors = base64_encode(json_encode($contributors, true));
     $contributors = $post["Contributors"] ?? $blog["Contributiors"];
     $blockCommand = ($bl == 0) ? "Block" : "Unblock";
     $actions = ($post["UN"] != $you) ? $this->core->Element([
      "button", $blockCommand, [
       "class" => "Small UpdateButton v2",
       "data-processor" => $options["Block"]
      ]
     ]) : "";
     $actions = $this->core->Element([
      "button", "View Profile", [
       "class" => "OpenCard Small v2",
       "data-view" => base64_encode("v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($post["UN"]))
      ]
     ]);
     $share = ($post["UN"] == $you || $post["Privacy"] == md5("Public")) ? 1 : 0;
     $share = ($share == 1) ? $this->core->Element(["div", $this->core->Element([
      "button", "Share", [
       "class" => "OpenCard Small v2",
       "data-view" => $options["Share"]
     ]]), ["class" => "Desktop33"]]) : "";
     $r = $this->core->Change([[
      "[Article.Actions]" => $actions,
      "[Article.Attachments]" => $_BlogPost["ListItem"]["Attachments"],
      "[Article.Back]" => $back,
      "[Article.Body]" => $_BlogPost["ListItem"]["Body"],
      "[Article.Contributors]" => $contributors,
      "[Article.Conversation]" => $this->core->Change([[
       "[Conversation.CRID]" => $postID,
       "[Conversation.CRIDE]" => base64_encode($postID),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->core->Extension("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[Article.CoverPhoto]" => $_BlogPost["ListItem"]["CoverPhoto"],
      "[Article.Created]" => $this->core->TimeAgo($post["Created"]),
      "[Article.Description]" => $_BlogPost["ListItem"]["Description"],
      "[Article.ID]" => $postID,
      "[Article.Modified]" => $_BlogPost["ListItem"]["Modified"],
      "[Article.Notes]" => $options["Notes"],
      "[Article.Report]" => $options["Report"],
      "[Article.Share]" => $share,
      "[Article.Subscribe]" => "",
      "[Article.Title]" => $_BlogPost["ListItem"]["Title"],
      "[Article.Votes]" => $options["Vote"],
      "[Member.DisplayName]" => $author["Personal"]["DisplayName"],
      "[Member.ProfilePicture]" => $this->core->ProfilePicture($author, "margin:0.5em;max-width:12em;width:calc(100% - 1em)"),
      "[Member.Description]" => $description
     ], $this->core->Extension($extension)]);
    }
   } if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
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
   $blog = $data["BLG"] ?? "";
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $title = $data["Title"] ?? "";
   $r = [
    "Body" => "The Blog Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($blog) && !empty($id) && !empty($title)) {
    $i = 0;
    $coverPhoto = "";
    $coverPhotoSource = "";
    $blog = $this->core->Data("Get", ["blg", $blog]) ?? [];
    $now = $this->core->timestamp;
    $posts = $blog["Posts"] ?? [];
    $subscribers = $blog["Subscribers"] ?? [];
    foreach($posts as $key => $value) {
     $value = $this->core->Data("Get", ["bp", $value]) ?? [];
     if($i == 0) {
      if($id != $value["ID"] && $title == $value["Title"]) {
       $i++;
      }
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Post <em>$title</em> is taken."
     ];
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted to <em>".$blog["Title"]."</em>" : "updated";
     $post = $this->core->Data("Get", ["bp", $id]) ?? [];
     $author = $post["UN"] ?? $you;
     $attachments = $post["Attachments"] ?? [];
     $contributors = $post["Contributors"] ?? [];
     $contributors[$you] = $blog["Contributors"][$you] ?? "Contributor";
     $created = $post["Created"] ?? $now;
     $illegal = $post["Illegal"] ?? 0;
     $modifiedBy = $post["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["NSFW"] ?? $y["Privacy"]["NSFW"];
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Posts"];
     if(!empty($data["rATTI"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTI"])));
      foreach($dlc as $dlc) {
       if(!empty($dlc) && $i == 0) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         $t = $this->core->Member($f[0]);
         $efs = $this->core->Data("Get", [
          "fs",
          md5($t["Login"]["Username"])
         ]) ?? [];
         $coverPhoto = $f[0]."/".$efs["Files"][$f[1]]["Name"];
         $coverPhotoSource = base64_encode($f[0]."-".$f[1]);
        }
        $i++;
       }
      }
     } if(!empty($data["rATTF"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTF"])));
      foreach($dlc as $dlc) {
       if(!empty($dlc)) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         array_push($attachments, base64_encode($f[0]."-".$f[1]));
        }
       }
      }
     }
     $post = [
      "Attachments" => array_unique($attachments),
      "Body" => $this->core->PlainText([
       "Data" => $data["Body"],
       "Encode" => 1,
       "HTMLEncode" => 1
      ]),
      "Created" => $created,
      "Contributors" => $contributors,
      "Description" => htmlentities($data["Description"]),
      "ICO" => $coverPhoto,
      "ICO-SRC" => base64_encode($coverPhotoSource),
      "ID" => $id,
      "Illegal" => $illegal,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "NSFW" => $nsfw,
      "Privacy" => $privacy,
      "Title" => $title,
      "TPL" => $data["TPL-BLG"],
      "UN" => $author
     ];
     if(!in_array($id, $blog["Posts"])) {
      array_push($blog["Posts"], $id);
      $blog["Posts"] = array_unique($blog["Posts"]);
     }
     $y["Activity"]["LastActive"] = $now;
     $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
     $this->core->Data("Save", ["blg", $data["BLG"], $blog]);
     $this->core->Data("Save", ["bp", $id, $post]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     $r = [
      "Body" => "The Post <em>$title</em> was $actionTaken!",
      "Header" => "Done"
     ];
     if($new == 1) {
      $this->core->Statistic("Save Blog Post");
      foreach($subscribers as $key => $value) {
       $this->core->SendBulletin([
        "Data" => [
         "BlogID" => $data["BLG"],
         "PostID" => $id
        ],
        "To" => $value,
        "Type" => "NewBlogPost"
       ]);
      }
     } else {
      $this->core->Statistic("Update Blog Post");
     }
    }
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Blog or Post Identifier are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = explode("-", $id);
    $blog = $id[0];
    $blog = $this->core->Data("Get", ["blg", $blog]) ?? [];
    $blog["Modified"] = $this->core->timestamp;
    $newPosts = [];
    $post = $id[1];
    $posts = $blog["Posts"] ?? [];
    if(!empty($this->core->Data("Get", ["conversation", $post]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
    } foreach($posts as $key => $value) {
     if($post != $value) {
      array_push($newPosts, $value);
     }
    }
    $blog["Posts"] = $newPosts;
    $this->core->Data("Purge", ["bp", $post]);
    $this->core->Data("Purge", ["local", $post]);
    $this->core->Data("Purge", ["votes", $post]);
    $this->core->Data("Save", ["blg", $blog["ID"], $blog]);
    $r = [
     "Body" => "The Blog Post was deleted.",
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
<?php
 Class BlogPost extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["Blog", "Post", "new"]);
   $blog = $data["Blog"];
   $button = "";
   $new = $data["new"] ?? 0;
   $post = $data["Post"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Blog Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif((!empty($blog) && !empty($post)) || $new == 1) {
    $action = ($new == 1) ? "Post" : "Update";
    $attf = "";
    $blog = $this->system->Data("Get", ["blg", $blog]) ?? [];
    $id = ($new == 1) ? md5($you."_BP_".$this->system->timestamp) : $post;
    $post = $this->system->Data("Get", ["bp", $id]) ?? [];
    $post = $this->system->FixMissing($post, [
     "Body",
     "Description",
     "Title",
     "TPL"
    ]);
    $atinput = ".BGE_$id-ATTI";
    $at = base64_encode("Set as the Blog Post's Cover Photo:$atinput");
    $atinput = "$atinput .rATT";
    $at2 = base64_encode("All done! Feel free to close this card.");
    $at3input = ".BGE_$id-ATTF";
    $at3 = base64_encode("Attach to the Blog Post.:$at3input");
    $at3input = "$at3input .rATT";
    if(!empty($post["Attachments"])) {
     $attf = base64_encode(implode(";", $post["Attachments"]));
    }
    $coverPhoto = $post["ICO-SRC"] ?? "";
    $dv = base64_encode("Common:DesignView");
    $dvi = "UIE$id";
    $header = ($new == 1) ? "New Post to ".$blog["Title"] : "Edit ".$post["Title"];
    $nsfw = $post["NSFW"] ?? $y["Privacy"]["NSFW"];
    $privacy = $post["Privacy"] ?? $y["Privacy"]["Profile"];
    $search = base64_encode("Search:Containers");
    $additionalContent = $this->system->Change([
     [
      "[CP.ContentType]" => "Blog Post",
      "[CP.Files]" => base64_encode("v=$search&st=XFS&AddTo=$at2&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".$y["Login"]["Username"]),
      "[CP.ID]" => $id
     ], $this->system->Page("dc027b0a1f21d65d64d539e764f4340a")
    ]).$this->system->Change([
     [
      "[UIV.IN]" => $dvi,
      "[UIV.OUT]" => "UIV$id",
      "[UIV.U]" => base64_encode("v=$dv&DV=")
     ], $this->system->Page("7780dcde754b127656519b6288dffadc")
    ]).$this->system->Change([
     [
      "[XFS.Files]" => base64_encode("v=$search&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
      "[XFS.ID]" => $id
     ], $this->system->Page("8356860c249e93367a750f3b4398e493")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]);
    $r = $this->system->Change([[
     "[Blog.ID]" => $blog["ID"],
     "[BlogPost.AdditionalContent]" => $additionalContent,
     "[BlogPost.Attachments]" => $attf,
     "[BlogPost.Attachments.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorMossaic")."&AddTo=$at3input&ID="),
     "[BlogPost.Body]" => $this->system->WYSIWYG([
      "UN" => $you,
      "Body" => $this->system->PlainText([
       "Data" => $post["Body"],
       "Decode" => 1
      ]),
      "adm" => 1,
      "opt" => [
       "id" => "XBPBody",
       "class" => "$dvi Body Xdecode req",
       "name" => "Body",
       "placeholder" => "Body",
       "rows" => 20
      ]
     ]),
     "[BlogPost.Description]" => $this->system->Element(["textarea", $post["Description"], [
      "maxlen" => 180,
      "name" => "Description",
      "placeholder" => "Description"
     ]]),
     "[BlogPost.Header]" => $header,
     "[BlogPost.ICO]" => $coverPhoto,
     "[BlogPost.ICO.LiveView]" => base64_encode("v=".base64_encode("LiveView:EditorSingle")."&AddTo=$atinput&ID="),
     "[BlogPost.ID]" => $id,
     "[BlogPost.Inputs]" => "",
     "[BlogPost.New]" => $new,
     "[BlogPost.Options.NSFW]" => $this->system->Select("nsfw", "req v2 v2w", $nsfw),
     "[BlogPost.Options.Privacy]" => $this->system->Select("Privacy", "req v2 v2w", $privacy),
     "[BlogPost.Options.Template]" => $this->system->Select("TPL-CA", "req v2 v2w", $post["TPL"]),
     "[BlogPost.Title]" => $post["Title"],
     "[UIV.IN]" => $dvi
    ], $this->system->Page("15961ed0a116fbd6cfdb793f45614e44")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditBlogPost$id",
     "data-processor" => base64_encode("v=".base64_encode("BlogPost:Save"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
    "Blog",
    "Post",
    "b2",
    "pub"
   ]);
   $backTo = $data["b2"] ?? "Blog";
   $blog = $data["Blog"];
   $bck = $this->system->Element(["button", "Back to <em>$backTo</em>", [
    "class" => "GoToParent LI head",
    "data-type" => "Blog$blog"
   ]]);
   $i = 0;
   $post = $data["Post"];
   $pub = $data["pub"] ?? 0;
   $r = $this->system->Change([[
    "[Error.Back]" => $bck,
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The requested Blog Post could not be found."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($pub == 1) {
    $blogPosts = $this->system->DatabaseSet("BlogPosts");
    foreach($blogPosts as $key => $value) {
     $blogPost = $this->system->Data("Get", ["bp", $value]) ?? [];
     if(($blogPost["ID"] == $post || $callSignsMatch == 1) && $i == 0) {
      $i++;
      $post = $value;
     }
    }
   } if((!empty($blog) && !empty($post)) || $i > 0) {
    $combinedID = base64_encode("$blog-$post");
    $post = $this->system->Data("Get", ["bp", $post]) ?? [];
    $t = ($post["UN"] == $you) ? $y : $this->system->Member($t);
    $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
    $tpl = $post["TPL"] ?? "b793826c26014b81fdc1f3f94a52c9a6";
    $attachments = (!empty($post["Attachments"])) ? $this->view(base64_encode("LiveView:InlineMossaic"), ["Data" => [
     "ID" => base64_encode(implode(";", $post["Attachments"])),
     "Type" => base64_encode("DLC")
    ]]) : "";
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $coverPhoto = (!empty($post["ICO"])) ? $this->system->CoverPhoto(base64_encode($post["ICO"])) : $coverPhoto;
    $coverPhoto = "<img src=\"$coverPhoto\" style=\"width:100%\"/>\r\n";
    $contributors = $post["Contributors"] ?? $blog["Contributiors"];
    $description = ($ck == 1) ? "You have not added a Description." : "";
    $description = ($ck == 0) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
    $description = (!empty($t["Description"])) ? $this->system->PlainText([
     "BBCodes" => 1,
     "Data" => $t["Description"],
     "Display" => 1,
     "HTMLDecode" => 1
    ]) : $description;
    $modified = $post["ModifiedBy"] ?? [];
    if(empty($modified)) {
     $modified = "";
    } else {
     $_Member = end($modified);
     $_Time = $this->system->TimeAgo(array_key_last($modified));
     $modified = " &bull; Modified ".$_Time." by ".$_Member;
     $modified = $this->system->Element(["em", $modified]);
    }
    $profile = $this->system->Element([
     "button", "See more...", [
      "class" => "dB2O v2",
      "data-type" => base64_encode("v=".base64_encode("Profile:Home")."&UN=".base64_encode($post["UN"]))
     ]
    ]);
    $reactions = ($post["UN"] != $you) ? base64_encode($this->view(base64_encode("Common:Reactions"), ["Data" => [
     "CRID" => $id,
     "T" => $t["Login"]["Username"],
     "Type" => 2
    ]])) : "";
    $r = $this->system->Change([[
     "[Article.Actions]" => $profile,
     "[Article.Attachments]" => $attachments,
     "[Article.Back]" => $bck,
     "[Article.Body]" => $this->system->PlainText([
      "BBCodes" => 1,
      "Data" => $post["Body"],
      "Decode" => 1,
      "Display" => 1,
      "HTMLDecode" => 1
     ]),
     "[Article.Contributors]" => $this->view(base64_encode("Common:MemberGrid"), ["Data" => [
      "List" => $contributors
     ]]),
     "[Article.Conversation]" => $this->system->Change([[
      "[Conversation.CRID]" => $post["ID"],
      "[Conversation.CRIDE]" => base64_encode($post["ID"]),
      "[Conversation.Level]" => base64_encode(1),
      "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
     ], $this->system->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
     "[Article.CoverPhoto]" => $coverPhoto,
     "[Article.Created]" => $this->system->TimeAgo($post["Created"]),
     "[Article.Description]" => $post["Description"],
     "[Article.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("BlogPost;".$post["ID"])),
     "[Article.Modified]" => $modified,
     "[Article.Reactions]" => $reactions,
     "[Article.Share]" => base64_encode("v=".base64_encode("BlogPost:Share")."&ID=$combinedID&UN=".base64_encode($post["UN"])),
     "[Article.Subscribe]" => "",
     "[Article.Title]" => $post["Title"],
     "[Member.DisplayName]" => $t["Personal"]["DisplayName"],
     "[Member.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:0.5em;max-width:12em;width:calc(100% - 1em)"),
     "[Member.Description]" => $description
    ], $this->system->Page($tpl)]);
   }
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["BLG", "ID", "Title", "new"]);
   $new = $data["new"] ?? 0;
   $id = $data["ID"];
   $title = $data["Title"] ?? "";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Blog Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id) && !empty($title)) {
    $i = 0;
    $coverPhoto = "";
    $coverPhotoSource = "";
    $blog = $this->system->Data("Get", ["blg", $data["BLG"]]) ?? [];
    $now = $this->system->timestamp;
    $posts = $blog["Posts"] ?? [];
    $subscribers = $blog["Subscribers"] ?? [];
    foreach($posts as $key => $value) {
     $value = $this->system->Data("Get", ["bp", $value]) ?? [];
     if($i == 0) {
      if($id != $value["ID"] && $title == $value["Title"]) {
       $i++;
      }
     }
    } if($i > 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Post <em>$title</em> is taken."
      ]),
      "Header" => "Error"
     ]);
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted to <em>".$blog["Title"]."</em>" : "updated";
     $post = $this->system->Data("Get", ["bp", $id]) ?? [];
     $author = $post["UN"] ?? $you;
     $att = $post["Attachments"] ?? [];
     $contributors = $post["Contributors"] ?? [];
     $contributors[$you] = $blog["Contributors"][$you] ?? "Contributor";
     $created = $post["Created"] ?? $now;
     $illegal = $post["Illegal"] ?? 0;
     $modifiedBy = $post["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["nsfw"] ?? $y["Privacy"]["NSFW"];
     $privacy = $data["pri"] ?? $y["Privacy"]["Articles"];
     if(!empty($data["rATTI"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTI"])));
      foreach($dlc as $dlc) {
       if(!empty($dlc) && $i == 0) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         $t = $this->system->Member($f[0]);
         $efs = $this->system->Data("Get", [
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
         array_push($att, base64_encode($f[0]."-".$f[1]));
        }
       }
      }
     }
     $att = array_unique($att);
     $privacy = $data["pri"] ?? $y["Privacy"]["Posts"];
     $post = [
      "Attachments" => $att,
      "Body" => $this->system->PlainText([
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
      "TPL" => $data["TPL-CA"],
      "UN" => $author
     ];
     if(!in_array($id, $blog["Posts"])) {
      array_push($blog["Posts"], $id);
      $blog["Posts"] = array_unique($blog["Posts"]);
     }
     $y["Activity"]["LastActive"] = $now;
     $y["Points"] = $y["Points"] + $this->system->core["PTS"]["NewContent"];
     $this->system->Data("Save", ["blg", $data["BLG"], $blog]);
     $this->system->Data("Save", ["bp", $id, $post]);
     $this->system->Data("Save", ["mbr", md5($you), $y]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Post <em>$title</em> was $actionTaken!"
      ]),
      "Header" => "Done"
     ]);
     if($new == 1) {
      $this->system->Statistic("BGP");
      foreach($subscribers as $key => $value) {
       $this->system->SendBulletin([
        "Data" => [
         "BlogID" => $data["BLG"],
         "PostID" => $id
        ],
        "To" => $value,
        "Type" => "NewBlogPost"
       ]);
      }
     } else {
      $this->system->Statistic("BGPu");
     }
    }
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"] ?? "";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Blog or Post Identifier are missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $id = explode("-", $id);
    $blog = $id[0];
    $blog = $this->system->Data("Get", ["blg", $blog]) ?? [];
    $blog["Modified"] = $this->system->timestamp;
    $newPosts = [];
    $post = $id[1];
    $posts = $blog["Posts"] ?? [];
    if(!empty($this->system->Data("Get", ["conversation", $post]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
    } foreach($posts as $key => $value) {
     if($post != $value) {
      array_push($newPosts, $value);
     }
    }
    $blog["Posts"] = $newPosts;
    $this->system->Data("Purge", ["bp", $post]);
    $this->system->Data("Purge", ["local", $post]);
    $this->system->Data("Purge", ["react", $post]);
    $this->system->Data("Save", ["blg", $blog["ID"], $blog]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The Blog Post was deleted."]),
     "Header" => "Done"
    ]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseDialog"
   ]);
  }
  function Share(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $ec = "Denied";
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $un = $data["UN"];
   $y = $this->you;
   if(!empty($id) && !empty($un)) {
    $id = base64_decode($id);
    $post = $this->system->Data("Get", ["bp", $id]) ?? [];
    $un = base64_decode($un);
    $t = ($un == $y["Login"]["Username"]) ? $y : $this->system->Member($un);
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out <em>".$post["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!"
     ]).$this->system->Element([
      "div", "[BlogPost:$id]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=BlogPost",
     "[Share.ContentID]" => "Blog Post",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $post["Title"]
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
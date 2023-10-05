<?php
 Class Page extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Banish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = [
     "Body" => "You cannot banish yourself.",
     "Header" => "Error"
    ];
    if($mbr != $Page["UN"] && $mbr != $y["Login"]["Username"]) {
    $accessCode = "Accepted";
     $r = [
      "Actions" => [
       $this->core->Element(["button", "Cancel", [
        "class" => "CloseDialog v2 v2w"
       ]]),
       $this->core->Element(["button", "Banish $mbr", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Page:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"])
       ]])
      ],
      "Body" => "Are you sure you want to banish $mbr from <em>".$Page["Title"]."</em>?",
      "Header" => "Banish $mbr?"
     ];
    }
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
  function Card(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Not Found"
   ];
   if(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $Page = $this->core->Data("Get", [
     "pg",
     base64_decode($data["ID"])
    ]) ?? [];
    $r = $this->core->Element([
     "h1", $Page["Title"], ["class" => "UpperCase"]
    ]).$this->core->Element([
     "div", $this->core->PlainText([
      "BBCodes" => 1,
      "Data" => $Page["Body"],
      "Decode" => 1,
      "Display" => 1,
      "HTMLDecode" => 1
     ]), ["class" => "NONAME"]
    ]);
   }
   $r = [
    "Front" => $r
   ];
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function ChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN", "Member"]);
   $id = $data["ID"];
   $member = $data["Member"];
   $r = [
    "Body" => "The Forum Identifier is missing."
   ];
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif(!empty($id) && !empty($member)) {
    $accessCode = "Accepted";
    $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $contributors = $Page["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $Page["Contributors"] = $contributors;
    $this->core->Data("Save", ["pg", $id, $Page]);
    $r = [
     "Body" => "$member's Role within <em>".$Page["Title"]."</em> was Changed to $role.",
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
  function Edit(array $a) {
   $accessCode = "Denied";
   $buttion = "";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Article Identifier is missing."
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
    $action = ($new == 1) ? "Post" : "Update";
    $attf = "";
    $id = (!empty($id)) ? base64_decode($id) : $id;
    $id = ($new == 1) ? md5($you."_PG_".$time) : $id;
    $crid = md5("PG_$id");
    $dvi = "UIE$crid".md5($time);
    $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $Page = $this->core->FixMissing($Page, [
     "Body",
     "Category",
     "Description",
     "ICO-SRC",
     "Title"
    ]);
    $header = ($new == 1) ? "New Article" : "Edit ".$Page["Title"];
    $products = "";
    if(!empty($Page["Attachments"])) {
     $attf = base64_encode(implode(";", $Page["Attachments"]));
    } if(!empty($Page["Products"])) {
     $products = base64_encode(implode(";", $Page["Products"]));
    }
    $atinput = ".EditPage$id-ATTI";
    $at = base64_encode("Set as the Article's Cover Photo:$atinput");
    $atinput = "$atinput .rATT";
    $at2 = base64_encode("All done! Feel free to close this card.");
    $at3input = ".EditPage$id-ATTF";
    $at3 = base64_encode("Attach to the Article.:$at3input");
    $at3input = "$at3input .rATT";
    $at4input = ".EditPage$id-ATTP";
    $at4 = base64_encode("Attach to the Article.:$at4input");
    $at4input = "$at4input .rATT";
    $categories = ($y["Rank"] == md5("High Command")) ? [
     "CA" => "Article",
     "EXT" => "Extension",
     "JE" => "Journal Entry",
     "PR" => "Press Release",
     "TPL-BLG" => "Blog Template",
     "TPL-CA" => "Community Archive Template"
    ] : [
     "CA" => "Article",
     "JE" => "Journal Entry",
     "TPL-BLG" => "Blog Template",
     "TPL-CA" => "Community Archive Template"
    ];
    $category = $Page["Category"] ?? "CA";
    $em = base64_encode("LiveView:EditorMossaic");
    $ep = base64_encode("LiveView:EditorProducts");
    $es = base64_encode("LiveView:EditorSingle");
    $nsfw = $Page["NSFW"] ?? $y["Privacy"]["NSFW"];
    $options = "";
    $privacy = $Page["Privacy"] ?? $y["Privacy"]["Posts"];
    $sc = base64_encode("Search:Containers");
    $_HC = ($y["Rank"] == md5("High Command")) ? 1 : 0;
    $r = $this->core->Change([[
     "[Article.AdditionalContent]" => $this->core->Change([
      [
       "[Extras.ContentType]" => "Page",
       "[Extras.CoverPhoto.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=$you"),
       "[Extras.DesignView.Origin]" => $dvi,
       "[Extras.DesignView.Destination]" => "UIV$id",
       "[Extras.DesignView.Processor]" => base64_encode("v=".base64_encode("Common:DesignView")."&DV="),
       "[Extras.Files]" => base64_encode("v=".base64_encode("Search:Containers")."&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
       "[Extras.ID]" => $id,
       "[Extras.Translate]" => base64_encode("v=".base64_encode("Language:Edit")."&ID=".base64_encode($id))
      ], $this->core->Page("257b560d9c9499f7a0b9129c2a63492c")
     ]),
     "[Article.Header]" => $header,
     "[Article.ID]" => $id,
     "[Article.Inputs]" => $this->core->RenderInputs([
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
        "name" => "HC",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $_HC
      ],
      [
       "Attributes" => [
        "name" => "new",
        "type" => "hidden"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $new
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTF",
        "data-a" => "#ATTL$id-ATTF",
        "data-u" => base64_encode("v=$em&AddTo=$at3input&ID="),
        "name" => "rATTF",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditPage$id-ATTF"
       ],
       "Type" => "Text",
       "Value" => $attf
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTI",
        "data-a" => "#ATTL$id-ATTI",
        "data-u" => base64_encode("v=$es&AddTo=$atinput&ID="),
        "name" => "rATTI",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditPage$id-ATTI"
       ],
       "Type" => "Text",
       "Value" => $Page["ICO-SRC"]
      ],
      [
       "Attributes" => [
        "class" => "rATT rATT$id-ATTP",
        "data-a" => "#ATTL$id-ATTP",
        "data-u" => base64_encode("v=$ep&AddTo=$at4input&BNDL="),
        "name" => "rATTP",
        "type" => "hidden"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "EditPage$id-ATTP"
       ],
       "Type" => "Text",
       "Value" => $products
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
       "Value" => $Page["Title"]
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
       "Value" => $Page["Description"]
      ],
      [
       "Attributes" => [
        "class" => "$dvi Body Xdecode req",
        "id" => "EditPageBody$id",
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
       "Value" => $this->core->PlainText([
        "Data" => $Page["Body"],
        "Decode" => 1
       ])
      ]
     ]),
     "[Article.Options]" => $options,
     "[Article.Options.Standard]" => $this->core->RenderInputs([
      [
       "Attributes" => [],
       "OptionGroup" => $categories,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Category"
       ],
       "Name" => "PageCategory",
       "Title" => "Article Category",
       "Type" => "Select",
       "Value" => $category
      ]
     ]).$this->core->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->core->RenderVisibilityFilter([
      "Value" => $privacy
     ])
    ], $this->core->Page("68526a90bfdbf5ea5830d216139585d7")]);
    $button = $this->core->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditPage$id",
     "data-processor" => base64_encode("v=".base64_encode("Page:Save"))
    ]]);
    $r = [
     "Action" => $button,
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
   $_ViewTitle = $this->core->config["App"]["Name"];
   $accessCode = "Denied";
   $base = $this->core->efs;
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "CARD",
    "ID",
    "b2",
    "back",
    "lPG",
    "pub"
   ]);
   $b2 = $data["b2"] ?? "the Archive";
   $bck = $data["back"] ?? 0;
   $card = $data["CARD"] ?? 0;
   $id = $data["ID"];
   $bck = ($bck == 1) ? $this->core->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI header",
    "data-type" => $data["lPG"]
   ]]) : "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Article could not be found.",
    "Header" => "Not Found"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $active = 0;
    $admin = 0;
    $bl = $this->core->CheckBlocked([$y, "Pages", $id]);
    $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $_ViewTitle = $Page["Title"] ?? $_ViewTitle;
    $contributors = $Page["Contributors"] ?? [];
    $ck = ($Page["UN"] == $you) ? 1 : 0;
    $subscribers = $Page["Subscribers"] ?? [];
    if(in_array($Page["Category"], ["CA", "JE"]) && $bl == 0) {
     foreach($contributors as $member => $role) {
      if($active == 0 && $member == $you) {
       $active = 1;
       if($admin == 0 && $role == "Admin") {
        $admin = 1;
       }
      }
     }
     $actions = ($ck == 0) ? $this->core->Element([
      "button", "Block <em>".$Page["Title"]."</em>", [
       "class" => "BLK v2",
       "data-cmd" => base64_encode("B"),
       "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($Page["Title"])."&content=".base64_encode($id)."&list=".base64_encode("Pages")."&BC=")
      ]
     ]) : "";
     $actions .= ($admin == 1 || $active == 1 || $ck == 1) ? $this->core->Element([
      "button", "Edit", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Page:Edit")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($admin == 1) ? $this->core->Element([
      "button", "Manage Contributors", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Search:Containers")."&CARD=1&ID=".base64_encode($id)."&Type=".base64_encode("Article")."&st=Contributors")
      ]
     ]) : "";
     $actions = ($this->core->ID != $you) ? $actions : "";
     $attachments = (!empty($Page["Attachments"])) ? $this->view(base64_encode("LiveView:InlineMossaic"), ["Data" => [
      "ID" => base64_encode(implode(";", $Page["Attachments"])),
      "Type" => base64_encode("DLC")
     ]]) : "";
     $t = ($Page["UN"] == $you) ? $y : $this->core->Member($t);
     $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
     $contributors = $Page["Contributors"] ?? [];
     $coverPhoto = (!empty($Page["ICO"])) ? "<img src=\"$base".$Page["ICO"]."\" style=\"width:100%\"/>" : "";
     $description = ($ck == 1) ? "You have not added a Description." : "";
     $description = ($ck == 0) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
     $description = (!empty($t["Description"])) ? $this->core->PlainText([
      "BBCodes" => 1,
      "Data" => $t["Description"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]) : $description;
     $modified = $Page["ModifiedBy"] ?? [];
     if(empty($modified)) {
      $modified = "";
     } else {
      $_Member = end($modified);
      $_Time = $this->core->TimeAgo(array_key_last($modified));
      $modified = " &bull; Modified ".$_Time." by ".$_Member;
      $modified = $this->core->Element(["em", $modified]);
     }
     $share = ($Page["UN"] == $you || $Page["Privacy"] == md5("Public")) ? 1 : 0;
     $share = ($share == 1) ? $this->core->Element([
      "div", $this->core->Element([
       "button", "Share", [
        "class" => "OpenCard",
        "data-view" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($id)."&Type=".base64_encode("Article")."&Username=".base64_encode($Page["UN"]))
      ]]), ["class" => "Desktop33"]
     ]) : "";
     $votes = ($Page["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $votes = base64_encode("v=$votes&ID=$id&Type=2");
     $r = $this->core->Change([[
      "[Article.Actions]" => $actions,
      "[Article.Attachments]" => $attachments,
      "[Article.Back]" => $bck,
      "[Article.Body]" => $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $Page["Body"],
       "Decode" => 1,
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[Article.Contributors]" => base64_encode("v=".base64_encode("Common:MemberGrid")."&List=".base64_encode(json_encode($contributors, true))),
      "[Article.Conversation]" => $this->core->Change([[
       "[Conversation.CRID]" => $id,
       "[Conversation.CRIDE]" => base64_encode($id),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->core->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[Article.CoverPhoto]" => $coverPhoto,
      "[Article.Created]" => $this->core->TimeAgo($Page["Created"]),
      "[Article.Description]" => $Page["Description"],
      "[Article.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Page;$id")),
      "[Article.Modified]" => $modified,
      "[Article.Share]" => $share,
      "[Article.Subscribe]" => base64_encode("v=".base64_encode("Common:SubscribeSection")."&ID=$id&Type=Article"),
      "[Article.Title]" => $Page["Title"],
      "[Article.Votes]" => $votes,
      "[Member.DisplayName]" => $t["Personal"]["DisplayName"],
      "[Member.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:12em;width:calc(100% - 1em)"),
      "[Member.Description]" => $description
     ], $this->core->Page("b793826c26014b81fdc1f3f94a52c9a6")]);
    } else {
     $r = $this->core->PlainText([
      "BBCodes" => 1,
      "Data" => $Page["Body"],
      "Decode" => 1,
      "Display" => 1,
      "HTMLDecode" => 1
     ]);
    }
   }
   $r = ($card == 1) ? [
    "Front" => $r
   ] : $r;
   if($pub == 1) {
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
    "ResponseType" => "View",
    "Title" => $_ViewTitle
   ]);
  }
  function Invite(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Article Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id)) {
    $accessCode = "Accepted";
    $content = [];
    $contentOptions = $y["Pages"] ?? [];
    $id = base64_decode($id);
    foreach($contentOptions as $key => $value) {
     $page = $this->Data("Get", ["pg", $value]) ?? [];
     $content[$page["ID"]] = $page["Title"];
    }
    $r = $this->core->Change([[
     "[Invite.ID]" => $id,
     "[Invite.Inputs]" => $this->core->RenderInputs([
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
        "placeholder" => $this->core->ID,
        "type" => "text"
       ],
       "Options" => [],
       "Type" => "Text",
       "Value" => $data["Member"]
      ],
      [
       "Attributes" => [],
       "OptionGroup" => $content,
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Invite To"
       ],
       "Name" => "ListArticles",
       "Type" => "Select",
       "Value" => $id
      ],
      [
       "Attributes" => [],
       "OptionGroup" => [
        0 => "Administrator",
        1 => "Contributor"
       ],
       "Options" => [
        "Container" => 1,
        "ContainerClass" => "Desktop50 MobileFull",
        "Header" => 1,
        "HeaderText" => "Role"
       ],
       "Name" => "Role",
       "Type" => "Select",
       "Value" => 1
      ]
     ])
    ], $this->core->Page("80e444c34034f9345eee7399b4467646")]);
    $action = $this->core->Element(["button", "Send Invite", [
     "class" => "CardButton SendData dB2C",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Page:SendInvite"))
    ]]);
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
   $data = $this->core->FixMissing($data, [
    "ID",
    "PageCategory",
    "Title",
    "new"
   ]);
   $new = $data["new"] ?? 0;
   $id = $data["ID"];
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
    $category = ($y["Rank"] != md5("High Command") && $category == "EXT") ? "CA" : $data["PageCategory"];
    $i = 0;
    $isPublic = (in_array($category, ["CA", "JE"])) ? 1 : 0;
    $now = $this->core->timestamp;
    $Pages = $this->core->DatabaseSet("PG");
    $title = $data["Title"];
    foreach($Pages as $key => $value) {
     $article = str_replace("c.oh.pg.", "", $value);
     $Page = $this->core->Data("Get", ["pg", $article]) ?? [];
     if($id != $Page["ID"] && $Page["Title"] == $title) {
      $i++;
     }
    } if($i > 0) {
     $r = [
      "Body" => "The Article <em>$title</em> is taken.",
      "Header" => "Error"
     ];
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $coverPhoto = "";
     $coverPhotoSource = "";
     $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
     $att = $Page["Attachments"] ?? [];
     $author = $Page["UN"] ?? $you;
     $newCategory = ($category == "EXT") ? "Extention" : "Article";
     $newCategory = ($category == "JE") ? "Journal Entry" : $newCategory;
     $contributors = $Page["Contributors"] ?? [];
     $created = $Page["Created"] ?? $now;
     $i = 0;
     $illegal = $Page["Illegal"] ?? 0;
     $modifiedBy = $Page["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["nsfw"] ?? $y["Privacy"]["NSFW"];
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Articles"];
     $products = $Page["Products"] ?? [];
     $subscribers = $Page["Subscribers"] ?? [];
     if(!empty($data["rATTI"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTI"])));
      foreach($dlc as $dlc) {
       if($i == 0 && !empty($dlc)) {
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
         array_push($att, base64_encode($f[0]."-".$f[1]));
        }
       }
      }
     } if(!empty($data["rATTP"])) {
      $dlc = array_reverse(explode(";", base64_decode($data["rATTP"])));
      foreach($dlc as $dlc) {
       if(!empty($dlc)) {
        $f = explode("-", base64_decode($dlc));
        if(!empty($f[0]) && !empty($f[1])) {
         array_push($prod, base64_encode($f[0]."-".$f[1]));
        }
       }
      }
     } if($isPublic == 1 && $new == 1) {
      $ck = ($author == $you) ? 1 : 0;
      $ck = (!in_array($id, $y["Pages"]) && $ck == 1) ? 1 : 0;
      foreach($subscribers as $key => $value) {
       $this->core->SendBulletin([
        "Data" => [
         "ArticleID" => $id
        ],
        "To" => $value,
        "Type" => "ArticleUpdate"
       ]);
      }
      if($ck == 1) {
       $newPages = $y["Pages"];
       array_push($newPages, $id);
       $y["Activity"]["LastActive"] = $now;
       $y["Pages"] = array_unique($newPages);
       $y["Points"] = $y["Points"] + $this->core->config["PTS"]["NewContent"];
      }
     }
     $att = array_unique($att);
     $highCommand = ($y["Rank"] == md5("High Command")) ? 1 : 0;
     $this->core->Data("Save", ["pg", $id, [
      "Attachments" => $att,
      "Body" => $this->core->PlainText([
       "Data" => $data["Body"],
       "Encode" => 1,
       "HTMLEncode" => 1
      ]),
      "Category" => $category,
      "Contributors" => $contributors,
      "Created" => $created,
      "Description" => htmlentities($data["Description"]),
      "High Command" => $highCommand,
      "ICO" => $coverPhoto,
      "ICO-SRC" => base64_encode($coverPhotoSource),
      "ID" => $id,
      "Illegal" => $illegal,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "NSFW" => $nsfw,
      "Privacy" => $privacy,
      "Products" => $products,
      "Title" => $title,
      "UN" => $author
     ]]);
     $this->core->Data("Save", ["mbr", md5($you), $y]);
     $r = [
      "Body" => "The $newCategory has been $actionTaken!",
      "Header" => "Done"
     ];
     if($new == 1) {
      $this->core->Statistic("PG");
     } else {
      $this->core->Statistic("PGu");
      if($isPublic == 1) {
       foreach($subscribers as $key => $value) {
        $this->core->SendBulletin([
         "Data" => [
          "ArticleID" => $id,
          "Author" => $you
         ],
         "To" => $value,
         "Type" => "ArticleUpdate"
        ]);
       }
      }
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
  function SaveBanish(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
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
   } elseif(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $mbr = base64_decode($mbr);
    $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $r = [
     "Body" => "You cannot banish yourself.",
     "Header" => "Error"
    ];
    if($mbr != $Page["UN"] && $mbr != $you) {
     $accessCode = "Accepted";
     $contributors = $Page["Contributors"] ?? [];
     $newContributors = [];
     foreach($contributors as $member => $role) {
      if($mbr != $member) {
       $newContributors[$member] = $role;
      }
     }
     $Page["Contributors"] = $newContributors;
     $this->core->Data("Save", ["pg", $id, $Page]);
     $r = [
      "Body" => "$mbr was banished from <em>".$Page["Title"]."</em>.",
      "Header" => "Done"
     ];
    }
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
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
    $newPages = [];
    $Pages = $y["Pages"] ?? [];
    if(!empty($this->core->Data("Get", ["conversation", $id]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
    } foreach($Pages as $key => $value) {
     if($id != $value) {
      array_push($newPages, $value);
     }
    }
    $y["Pages"] = $newPages;
    $this->core->Data("Purge", ["local", $id]);
    $this->core->Data("Purge", ["pg", $id]);
    $this->core->Data("Purge", ["votes", $id]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $r = [
     "Body" => "The Page was deleted.",
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
  function SendInvite(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "ID",
    "Member",
    "Role"
   ]);
   $i = 0;
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = [
    "Body" => "The Article Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $members = $this->core->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     if($i == 0) {
      $t = $this->core->Data("Get", ["mbr", $value]) ?? [];
      if($mbr == $t["Login"]["Username"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $r = [
      "Body" => "The Member $mbr does not exist.",
      "Header" => "Error"
     ];
    } elseif(empty($Page["ID"])) {
     $r = [
      "Body" => "The Article does not exist.",
      "Header" => "Error"
     ];
    } elseif($mbr == $Page["UN"]) {
     $r = [
      "Body" => "$mbr owns <em>".$Page["Title"]."</em>.",
      "Header" => "Error"
     ];
    } elseif($mbr == $y["Login"]["Username"]) {
     $r = [
      "Body" => "You are already a contributor.",
      "Header" => "Error"
     ];
    } else {
     $active = 0;
     $contributors = $Page["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      if($mbr == $member) {
       $active++;
      }
     } if($active == 1) {
      $r = [
       "Body" => "$mbr is already a contributor.",
       "Header" => "Error"
      ];
     } else {
      $accessCode = "Accepted";
      $role = ($data["Role"] == 1) ? "Member" : "Admin";
      $contributors[$mbr] = $role;
      $Page["Contributors"] = $contributors;
      $this->core->SendBulletin([
       "Data" => [
        "ArticleID" => $id,
        "Member" => $mbr,
        "Role" => $role
       ],
       "To" => $mbr,
       "Type" => "InviteToArticle"
      ]);
      $this->core->Data("Save", ["pg", $id, $Page]) ?? [];
      $r = [
       "Body" => "$mbr was notified of your invitation.",
       "Header" => "Invitation Sent"
      ];
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
  function Subscribe(array $a) {
   $accessCode = "Denied";
   $responseType = "Dialog";
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
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $responseType = "UpdateText";
    $page = $this->core->Data("Get", ["pg", $id]) ?? [];
    $subscribers = $page["Subscribers"] ?? [];
    $subscribed = (in_array($you, $subscribers)) ? 1 : 0;
    if($subscribed == 1) {
     $newSubscribers = [];
     $r = "Subscribe";
     foreach($subscribers as $key => $value) {
      if($value != $you) {
       $newSubscribers[$key] = $value;
      }
     }
     $subscribers = $newSubscribers;
    } else {
     array_push($subscribers, $you);
     $r = "Unsubscribe";
    }
    $page["Subscribers"] = $subscribers;
    $this->core->Data("Save", ["pg", $id, $page]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
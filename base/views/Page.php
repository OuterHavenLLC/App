<?php
 Class Page extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Banish(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Forum Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
    $mbr = base64_decode($mbr);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You cannot banish yourself."]),
     "Header" => "Error"
    ]);
    if($mbr != $Page["UN"] && $mbr != $y["Login"]["Username"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "Are you sure you want to banish $mbr from <em>".$Page["Title"]."</em>?"
      ]),
      "Header" => "Banish $mbr?",
      "Option" => $this->system->Element(["button", "Cancel", [
       "class" => "dBC v2 v2w"
      ]]),
      "Option2" => $this->system->Element(["button", "Banish $mbr", [
       "class" => "BBB dBC dBO v2 v2w",
       "data-type" => "v=".base64_encode("Page:SaveBanish")."&ID=".$data["ID"]."&Member=".$data["Member"]
      ]])
     ]);
    }
   }
   return $r;
  }
  function Card(array $a) {
   $data = $a["Data"] ?? [];
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Article Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   if(!empty($data["ID"])) {
    $Page = $this->system->Data("Get", [
     "pg",
     base64_decode($data["ID"])
    ]) ?? [];
    $r = $this->system->Element([
     "h1", $Page["Title"], ["class" => "UpperCase"]
    ]).$this->system->Element([
     "div", $this->system->PlainText([
      "BBCodes" => 1,
      "Data" => $Page["Body"],
      "Decode" => 1,
      "Display" => 1,
      "HTMLDecode" => 1
     ]), ["class" => "NONAME"]
    ]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function ChangeMemberRole(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN", "Member"]);
   $id = $data["ID"];
   $member = $data["Member"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Forum Identifier is missing."]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(md5($data["PIN"]) != $y["Login"]["PIN"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The PINs do not match."]),
     "Header" => "Error"
    ]);
   } elseif(!empty($id) && !empty($member)) {
    $accessCode = "Accepted";
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
    $contributors = $Page["Contributors"] ?? [];
    $role = ($data["Role"] == 1) ? "Member" : "Admin";
    $contributors[$member] = $role;
    $Page["Contributors"] = $contributors;
    $this->system->Data("Save", ["pg", $id, $Page]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "$member's Role within <em>".$Page["Title"]."</em> was Changed to $role."
     ]),
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
  function Edit(array $a) {
   $buttion = "";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Article Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $time = $this->system->timestamp;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Change([[
     "[Error.Header]" => "Forbidden",
     "[Error.Message]" => "You must sign in to continue."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   } elseif(!empty($id) || $new == 1) {
    $action = ($new == 1) ? "Post" : "Update";
    $attf = "";
    $id = (!empty($id)) ? base64_decode($id) : $id;
    $id = ($new == 1) ? md5($you."_PG_".$time) : $id;
    $crid = md5("PG_$id");
    $dv = base64_encode("Common:DesignView");
    $dvi = "UIE$crid".md5($time);
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
    $Page = $this->system->FixMissing($Page, [
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
    if($y["Rank"] == md5("High Command")) {
     $options .= "<input class=\"HC\" name=\"HC\" type=\"hidden\" value=\"1\"/>\r\n";
     $options .= "<input name=\"new\" type=\"hidden\" value=\"$new\"/>\r\n";
    } else {
     $options .= "<input class=\"HC\" name=\"HC\" type=\"hidden\" value=\"0\"/>\r\n";
    }
    $additionalContent = $this->system->Change([
     [
      "[CP.ContentType]" => "Page",
      "[CP.Files]" => base64_encode("v=$sc&st=XFS&AddTo=$at&Added=$at2&ftype=".base64_encode(json_encode(["Photo"]))."&UN=".$y["Login"]["Username"]),
      "[CP.ID]" => $id
     ], $this->system->Page("dc027b0a1f21d65d64d539e764f4340a")
    ]).$this->view(base64_encode("Language:Edit"), ["Data" => [
     "ID" => base64_encode($id)
    ]]).$this->system->Change([
     [
      "[UIV.IN]" => $dvi,
      "[UIV.OUT]" => "UIV$crid".md5($time),
      "[UIV.U]" => base64_encode("v=$dv&DV=")
     ], $this->system->Page("7780dcde754b127656519b6288dffadc")
    ]).$this->system->Change([
     [
      "[XFS.Files]" => base64_encode("v=$sc&st=XFS&AddTo=$at3&Added=$at2&UN=$you"),
      "[XFS.ID]" => $id
     ], $this->system->Page("8356860c249e93367a750f3b4398e493")
    ]);
    $fr = $this->system->Change([[
     "[Article.AdditionalContent]" => $additionalContent,
     "[Article.Header]" => $header,
     "[Article.ID]" => $id,
     "[Article.Inputs]" => $this->system->RenderInputs([
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
       "Value" => $this->system->PlainText([
        "Data" => $Page["Body"],
        "Decode" => 1
       ])
      ]
     ]),
     "[Article.Options]" => $options,
     "[Article.Options.Standard]" => $this->system->RenderInputs([
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
     ]).$this->system->RenderVisibilityFilter([
      "Filter" => "NSFW",
      "Name" => "nsfw",
      "Title" => "Content Status",
      "Value" => $nsfw
     ]).$this->system->RenderVisibilityFilter([
      "Value" => $privacy
     ])
    ], $this->system->Page("68526a90bfdbf5ea5830d216139585d7")]);
    $button = $this->system->Element(["button", $action, [
     "class" => "CardButton SendData",
     "data-form" => ".EditPage$id",
     "data-processor" => base64_encode("v=".base64_encode("Page:Save"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $fr,
    "FrontButton" => $button
   ]);
  }
  function Home(array $a) {
   $base = $this->system->efs;
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, [
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
   $bck = ($bck == 1) ? $this->system->Element(["button", "Back to $b2", [
    "class" => "GoToParent LI header",
    "data-type" => $data["lPG"]
   ]]) : "";
   $pub = $data["pub"] ?? 0;
   $r = $this->system->Change([[
    "[Error.Back]" => $bck,
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The requested Article could not be found."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $active = 0;
    $admin = 0;
    $bl = $this->system->CheckBlocked([$y, "Pages", $id]);
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
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
     $actions = ($ck == 0) ? $this->system->Element([
      "button", "Block <em>".$Page["Title"]."</em>", [
       "class" => "BLK v2",
       "data-cmd" => base64_encode("B"),
       "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode($Page["Title"])."&content=".base64_encode($id)."&list=".base64_encode("Pages")."&BC=")
      ]
     ]) : "";
     $actions .= ($admin == 1 || $active == 1 || $ck == 1) ? $this->system->Element([
      "button", "Edit", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Page:Edit")."&ID=".base64_encode($id))
      ]
     ]) : "";
     $actions .= ($admin == 1) ? $this->system->Element([
      "button", "Manage Contributors", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Search:Containers")."&CARD=1&ID=".base64_encode($id)."&Type=".base64_encode("Article")."&st=Contributors")
      ]
     ]) : "";
     $actions = ($this->system->ID != $you) ? $actions : "";
     $attachments = (!empty($Page["Attachments"])) ? $this->view(base64_encode("LiveView:InlineMossaic"), ["Data" => [
      "ID" => base64_encode(implode(";", $Page["Attachments"])),
      "Type" => base64_encode("DLC")
     ]]) : "";
     $t = ($Page["UN"] == $you) ? $y : $this->system->Member($t);
     $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
     $contributors = $Page["Contributors"] ?? [];
     $coverPhoto = (!empty($Page["ICO"])) ? "<img src=\"$base".$Page["ICO"]."\" style=\"width:100%\"/>" : "";
     $description = ($ck == 1) ? "You have not added a Description." : "";
     $description = ($ck == 0) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
     $description = (!empty($t["Description"])) ? $this->system->PlainText([
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
      $_Time = $this->system->TimeAgo(array_key_last($modified));
      $modified = " &bull; Modified ".$_Time." by ".$_Member;
      $modified = $this->system->Element(["em", $modified]);
     }
     $votes = ($Page["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
     $votes = base64_encode($this->view($votes, ["Data" => [
      "ID" => $id,
      "Type" => 2
     ]]));
     $subscribe = ($Page["UN"] != $you && $this->system->ID != $you) ? 1 : 0;
     $subscribeText = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
     $subscribe = ($subscribe == 1) ? $this->system->Change([[
      "[Subscribe.ContentID]" => $id,
      "[Subscribe.ID]" => md5($you),
      "[Subscribe.Processor]" => base64_encode("v=".base64_encode("Page:Subscribe")),
      "[Subscribe.Text]" => $subscribeText,
      "[Subscribe.Title]" => $Page["Title"]
     ], $this->system->Page("489a64595f3ec2ec39d1c568cd8a8597")]) : "";
     $r = $this->system->Change([[
      "[Article.Actions]" => $actions,
      "[Article.Attachments]" => $attachments,
      "[Article.Back]" => $bck,
      "[Article.Body]" => $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $Page["Body"],
       "Decode" => 1,
       "Display" => 1,
       "HTMLDecode" => 1
      ]),
      "[Article.Contributors]" => $this->view(base64_encode("Common:MemberGrid"), ["Data" => [
       "List" => $contributors
      ]]),
      "[Article.Conversation]" => $this->system->Change([[
       "[Conversation.CRID]" => $id,
       "[Conversation.CRIDE]" => base64_encode($id),
       "[Conversation.Level]" => base64_encode(1),
       "[Conversation.URL]" => base64_encode("v=".base64_encode("Conversation:Home")."&CRID=[CRID]&LVL=[LVL]")
      ], $this->system->Page("d6414ead3bbd9c36b1c028cf1bb1eb4a")]),
      "[Article.CoverPhoto]" => $coverPhoto,
      "[Article.Created]" => $this->system->TimeAgo($Page["Created"]),
      "[Article.Description]" => $Page["Description"],
      "[Article.Illegal]" => base64_encode("v=".base64_encode("Common:Illegal")."&ID=".base64_encode("Page;$id")),
      "[Article.Modified]" => $modified,
      "[Article.Reactions]" => $votes,
      "[Article.Share]" => base64_encode("v=".base64_encode("Page:Share")."&ID=".base64_encode($id)."&UN=".base64_encode($Page["UN"])),
      "[Article.Subscribe]" => $subscribe,
      "[Article.Title]" => $Page["Title"],
      "[Member.DisplayName]" => $t["Personal"]["DisplayName"],
      "[Member.ProfilePicture]" => $this->system->ProfilePicture($t, "margin:0.5em;max-width:12em;width:calc(100% - 1em)"),
      "[Member.Description]" => $description
     ], $this->system->Page("b793826c26014b81fdc1f3f94a52c9a6")]);
    } else {
     $r = $this->system->PlainText([
      "BBCodes" => 1,
      "Data" => $Page["Body"],
      "Decode" => 1,
      "Display" => 1,
      "HTMLDecode" => 1
     ]);
    }
   }
   $r = ($card == 1) ? $this->system->Card(["Front" => $r]) : $r;
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function Invite(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $fr = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Article Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $frbtn = "";
   $y = $this->you;
   if(!empty($id)) {
    $content = [];
    $contentOptions = $y["Pages"] ?? [];
    $id = base64_decode($id);
    foreach($contentOptions as $key => $value) {
     $page = $this->Data("Get", ["pg", $value]) ?? [];
     $content[$page["ID"]] = $page["Title"];
    }
    $fr = $this->system->Change([[
     "[Invite.ID]" => $id,
     "[Invite.Inputs]" => $this->system->RenderInputs([
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
        "placeholder" => $this->system->ID,
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
    ], $this->system->Page("80e444c34034f9345eee7399b4467646")]);
    $frbtn = $this->system->Element(["button", "Send Invite", [
     "class" => "CardButton SendData dB2C",
     "data-form" => ".Invite$id",
     "data-processor" => base64_encode("v=".base64_encode("Page:SendInvite"))
    ]]);
   }
   return $this->system->Card([
    "Front" => $fr,
    "FrontButton" => $frbtn
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "ID",
    "PageCategory",
    "Title",
    "new"
   ]);
   $new = $data["new"] ?? 0;
   $id = $data["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Article Identifier is missing."
    ]),
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
   } elseif(!empty($id)) {
    $category = ($y["Rank"] != md5("High Command") && $category == "EXT") ? "CA" : $data["PageCategory"];
    $i = 0;
    $isPublic = (in_array($category, ["CA", "JE"])) ? 1 : 0;
    $now = $this->system->timestamp;
    $Pages = $this->system->DatabaseSet("PG");
    $title = $data["Title"];
    foreach($Pages as $key => $value) {
     $article = str_replace("c.oh.pg.", "", $value);
     $Page = $this->system->Data("Get", ["pg", $article]) ?? [];
     if($id != $Page["ID"] && $Page["Title"] == $title) {
      $i++;
     }
    } if($i > 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Article <em>$title</em> is taken."
      ]),
      "Header" => "Error"
     ]);
    } else {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $coverPhoto = "";
     $coverPhotoSource = "";
     $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
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
       $this->system->SendBulletin([
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
       $y["Points"] = $y["Points"] + $this->system->core["PTS"]["NewContent"];
      }
     }
     $att = array_unique($att);
     $highCommand = ($y["Rank"] == md5("High Command")) ? 1 : 0;
     $this->system->Data("Save", ["pg", $id, [
      "Attachments" => $att,
      "Body" => $this->system->PlainText([
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
     $this->system->Data("Save", ["mbr", md5($you), $y]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The $newCategory has been $actionTaken!"
      ]),
      "Header" => "Done"
     ]);
     if($new == 1) {
      $this->system->Statistic("PG");
     } else {
      $this->system->Statistic("PGu");
      if($isPublic == 1) {
       foreach($subscribers as $key => $value) {
        $this->system->SendBulletin([
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
  function SaveBanish(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Member"]);
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Article Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if($y["Login"]["Username"] == $this->system->ID) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id) && !empty($mbr)) {
    $id = base64_decode($id);
    $mbr = base64_decode($mbr);
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "You cannot banish yourself."]),
     "Header" => "Error"
    ]);
    if($mbr != $Page["UN"] && $mbr != $y["Login"]["Username"]) {
     $contributors = $Page["Contributors"] ?? [];
     $newContributors = [];
     foreach($contributors as $member => $role) {
      if($mbr != $member) {
       $newContributors[$member] = $role;
      }
     }
     $Page["Contributors"] = $newContributors;
     $this->system->Data("Save", ["pg", $id, $Page]);
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "$mbr was banished from <em>".$Page["Title"]."</em>."
      ]),
      "Header" => "Done"
     ]);
    }
   }
   return $r;
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID", "PIN"]);
   $id = $data["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Article Identifier is missing."
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
   } elseif($this->system->ID == $y["Login"]["Username"]) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $newPages = [];
    $Pages = $y["Pages"] ?? [];
    if(!empty($this->system->Data("Get", ["conversation", $id]))) {
     $this->view(base64_encode("Conversation:SaveDelete"), [
      "Data" => ["ID" => $id]
     ]);
    } foreach($Pages as $key => $value) {
     if($id != $value) {
      array_push($newPages, $value);
     }
    }
    $y["Pages"] = $newPages;
    $this->system->Data("Purge", ["local", $id]);
    $this->system->Data("Purge", ["pg", $id]);
    $this->system->Data("Purge", ["react", $id]);
    $this->system->Data("Save", ["mbr", md5($you), $y]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The Page was deleted."]),
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
  function SendInvite(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "ID",
    "Member",
    "Role"
   ]);
   $i = 0;
   $id = $data["ID"];
   $mbr = $data["Member"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Article Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($id) && !empty($mbr)) {
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
    $members = $this->system->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     if($i == 0) {
      $t = $this->system->Data("Get", ["mbr", $value]) ?? [];
      if($mbr == $t["Login"]["Username"]) {
       $i++;
      }
     }
    } if($i == 0) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Member $mbr does not exist."
      ]),
      "Header" => "Error"
     ]);
    } elseif(empty($Page["ID"])) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The Article does not exist."]),
      "Header" => "Error"
     ]);
    } elseif($mbr == $Page["UN"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "$mbr owns <em>".$Page["Title"]."</em>."
      ]),
      "Header" => "Error"
     ]);
    } elseif($mbr == $y["Login"]["Username"]) {
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "You are already a contributor."]),
      "Header" => "Error"
     ]);
    } else {
     $active = 0;
     $contributors = $Page["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      if($mbr == $member) {
       $active++;
      }
     } if($active == 1) {
      $r = $this->system->Dialog([
       "Body" => $this->system->Element([
        "p", "$mbr is already a contributor."
       ]),
       "Header" => "Error"
      ]);
     } else {
      $accessCode = "Accepted";
      $role = ($data["Role"] == 1) ? "Member" : "Admin";
      $contributors[$mbr] = $role;
      $Page["Contributors"] = $contributors;
      $this->system->SendBulletin([
       "Data" => [
        "ArticleID" => $id,
        "Member" => $mbr,
        "Role" => $role
       ],
       "To" => $mbr,
       "Type" => "InviteToArticle"
      ]);
      $this->system->Data("Save", ["pg", $id, $Page]) ?? [];
      $r = $this->system->Dialog([
       "Body" => $this->system->Element([
        "p", "$mbr was notified of your invitation."
       ]),
       "Header" => "Invitation Sent"
      ]);
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
  function Share(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "UN"]);
   $accessCode = "Denied";
   $id = $data["ID"];
   $r = $this->system->Change([[
    "[Error.Header]" => "Error",
    "[Error.Message]" => "The Share Sheet Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $un = $data["UN"];
   $y = $this->you;
   if(!empty($id) && !empty($un)) {
    $id = base64_decode($id);
    $un = base64_decode($un);
    $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
    $t = ($un == $y["Login"]["Username"]) ? $y : $this->system->Member($un);
    $body = $this->system->PlainText([
     "Data" => $this->system->Element([
      "p", "Check out <em>".$Page["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!"
     ]).$this->system->Element([
      "div", "[Article:$id]", ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->system->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=Article",
     "[Share.ContentID]" => "Article",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $Page["Title"]
    ], $this->system->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function Subscribe(array $a) {
   $accessCode = "Denied";
   $responseType = "Dialog";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Article Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to subscribe."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $responseType = "UpdateText";
    $page = $this->system->Data("Get", ["pg", $id]) ?? [];
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
    $this->system->Data("Save", ["pg", $id, $page]);
   }
   return $this->system->JSONResponse([
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
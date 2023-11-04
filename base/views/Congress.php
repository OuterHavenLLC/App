<?php
 Class Congress extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $chamber = $data["Chamber"] ?? "";
   $chambers = $data["Chambers"] ?? 0;
   $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
   $congressmen = $congress["Members"] ?? [];
   $houseRepresentatives = 0;
   $senators = 0;
   $pub = $data["pub"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $yourRole = $congressmen[$you] ?? "";
   // DEMOCRATIZED CONTENT MODERATION
   // HOUSE = 2X POPULATION OF SENATE, EX: 200:100 OR 800:400 RATIOS
   foreach($congressmen as $member => $role) {
    if($role == "HouseRepresentative") {
     $houseRepresentatives++;
    } elseif($role = "Senator") {
     $senators++;
    }
   } if($chambers == 1) {
    if($chamber == "House") {
     $r = $this->core->Element([
      "h2", "$chamber of Representatives"
     ]).$this->core->Element([
      "p", "Welcome to the Chamber of the $chamber of Congress."
     ]);
     if($yourRole == "HouseRepresentative") {
      $r .= $this->core->Element([
       "p", "A list of House members, the ability to vote in new members, and more will be present here in the future."
      ]);
     }
    } elseif($chamber == "Senate") {
     $r = $this->core->Element([
      "h2", $chamber
     ]).$this->core->Element([
      "p", "Welcome to the Congressional $chamber."
     ]);
     if($yourRole == "Senator") {
      $r .= $this->core->Element([
        "p", "Welcome to the Chamber of the $chamber of Congress. A list of Senators, the ability to vote in new Senators if you are a House member, and more will be present here in the future."
      ]);
     }
    }
   } else {
    $notAnon = ($this->core->ID !== $you) ? 1 : 0;
    $joinTheHouse = ($houseRepresentatives < 50 && $notAnon == 1) ? $this->core->Element([
     "button", "Become a House Representative", [
      "class" => "UpdateButton v2",
      "data-processor" => base64_encode("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Join")."&Role=".base64_encode("HouseRepresentative"))
     ]
    ]) : "";
    $joinTheHouse = ($yourRole == "HouseRepresentative") ? $this->core->Element([
     "button", "Resign", [
      "class" => "UpdateButton v2",
      "data-processor" => base64_encode("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Leave")."&Role=".base64_encode("HouseRepresentative"))
     ]
    ]) : $joinTheHouse;
    $joinTheSenate = ($senators < 100 && $notAnon == 1) ? $this->core->Element([
     "button", "Become a Senator", [
      "class" => "UpdateButton v2",
      "data-processor" => base64_encode("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Join")."&Role=".base64_encode("Senator"))
     ]
    ]) : "";
    $joinTheSenate = ($yourRole == "Senator") ? $this->core->Element([
     "button", "Resign", [
      "class" => "UpdateButton v2",
      "data-processor" => base64_encode("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Leave")."&Role=".base64_encode("Senator"))
     ]
    ]) : $joinTheSenate;
    $r = $this->core->Change([[
     "[Congress.Chambers.House]" => base64_encode("v=".base64_encode("Congress:Home")."&Chamber=House&Chambers=1"),
     "[Congress.Chambers.House.Join]" => $joinTheHouse,
     "[Congress.Chambers.Senate]" => base64_encode("v=".base64_encode("Congress:Home")."&Chamber=Senate&Chambers=1"),
     "[Congress.Chambers.Senate.Join]" => $joinTheSenate,
     "[Congress.CoverPhoto]" => $this->core->PlainText([
      "Data" => "[Media:Congress]",
      "Display" => 1
     ])
    ], $this->core->Page("8a38a3053ce5449ca2d321719f5aea0f")]);
   }
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
    "Title" => "Congress of ".$this->core->config["App"]["Name"]
   ]);
  }
  function Join(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $command = $data["Command"] ?? "";
   $r = [
    "Body" => "The Command or Role are missing."
   ];
   $role = $data["Role"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Attributes" => [
      "class" => "v2",
      "disabled" => "true"
     ],
     "Text" => "Sign In to Join"
    ];
   } elseif(!empty($command) && !empty($role)) {
    $accessCode = "Accepted";
    $command = base64_decode($command);
    $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
    $congressmen = $congress["Members"] ?? [];
    $houseRepresentatives = 0;
    $newCongressmen = [];
    $role = base64_decode($role);
    $senators = 0;
    $becomeRole = ($role == "HouseRepresentative") ? "House Representative" : "Senator";
    foreach($congressmen as $member => $memberRole) {
     if($member != $you) {
      $newCongressmen[$member] = $memberRole;
     } if($memberRole == "HouseRepresentative") {
      $houseRepresentatives++;
     } elseif($memberRole == "Senators") {
      $senators++;
     }
    } if($command == "Join") {
     $check = ($houseRepresentatives < 50 && $role == "HouseRepresentative") ? 1 : 0;
     $check2 = ($senators < 100 && $role == "Senator") ? 1 : 0;
     $r = [
      "Attributes" => [
       "class" => "v2",
       "disabled" => "true"
      ],
      "Text" => "You must be Elected"
     ];
     if($check == 1 || $check2 == 1) {
      $congressmen[$you] = $role;
      $congress["Members"] = $congressmen;
      $r = [
       "Attributes" => [
        "class" => "UpdateButton v2",
        "data-processor" => base64_encode("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Leave")."&Role=".base64_encode($role))
       ],
       "Text" => "Resign"
      ];
     }
    } elseif($command == "Leave") {
     $congress["Members"] = $newCongressmen;
     $r = [
      "Attributes" => [
       "class" => "UpdateButton v2",
       "data-processor" => base64_encode("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Join")."&Role=".base64_encode($role))
      ],
      "Text" => "Become a $becomeRole"
     ];
    }
    $this->core->Data("Save", ["app", md5("Congress"), $congress]);
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
  function Report(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Content Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $id = explode(";", base64_decode($id));
    $att = "";
    $body = "";
    if(!empty($id[0]) && !empty($id[1])) {
     $id2 = $id[2] ?? "N/A";
     $content = $this->core->ContentData([
      "ID" => $id[1].";$id2",
      "Type" => $id[0]
     ]) ?? [];
     if($id[0] == "Album" && !empty($id[2])) {
      $x = $this->core->Data("Get", ["fs", md5($id[1])]) ?? [];
      $x = $x["Albums"][$id[2]] ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Blog") {
      $x = $this->core->Data("Get", ["blg", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "BlogPost") {
      $x = $this->core->Data("Get", ["bp", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Comment" && !empty($id[2])) {
      $x = $this->core->Data("Get", ["conversation", $id[1]]) ?? [];
      $x = $x[$id[2]] ?? [];
      if(!empty($x["DLC"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["DLC"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1
      ]);
     } elseif($id[0] == "File" && !empty($id[2])) {
      $x = $this->core->Data("Get", ["fs", md5($id[1])]) ?? [];
      $x = $x["Files"][$id[2]] ?? [];
      $att = $this->core->GetAttachmentPreview([
       "DLL" => $x,
       "T" => $id[1],
       "Y" => $y["Login"]["Username"]
      ]).$this->core->Element(["div", NULL, [
       "class" => "NONAME",
       "style" => "height:0.5em"
      ]]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Forum") {
      $x = $this->core->Data("Get", ["pf", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "ForumPost") {
      $x = $this->core->Data("Get", ["post", $id[1]]) ?? [];
      if(!empty($x["Attachments"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
     } elseif($id[0] == "Page") {
      $x = $this->core->Data("Get", ["pg", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Product") {
      $x = $this->core->Data("Get", ["miny", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "StatusUpdate") {
      $x = $this->core->Data("Get", ["su", $id[1]]) ?? [];
      if(!empty($x["Attachments"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
     }
    }
    $processor = "v=".base64_encode("Common:SaveIllegal")."&ID=[ID]";
    $r = $this->core->Change([[
     "[Illegal.Content]" => $body,
     "[Illegal.Content.LiveView]" => $att,
     "[Illegal.ID]" => base64_encode(implode(";", $id)),
     "[Illegal.Processor]" => base64_encode($processor)
    ], $this->core->Page("0eaea9fae43712d8c810c737470021b3")]);
    $r = [
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
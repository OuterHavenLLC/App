<?php
 Class Congress extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Elect(): string {
   $_Dialog = [
    "Body" => "There are currently no eligible candidates to elect."
   ];
   $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]);
   $candidates = $ballot["Candidates"] ?? [];
   $congress = $this->core->Data("Get", ["app", md5("Congress")]);
   $congressionalStaff = $congress["Members"] ?? [];
   $newBallot = [];
   $now = $this->core->timestamp;
   $nextElection = $this->core->Timeplus($now, 1, "month");
   $electionTime = $congress["ElectionTime"] ?? $now;
   $electionTime = (empty($congress["ElectionTime"])) ? strtotime($electionTime) : $electionTime;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif(strtotime($now) < $electionTime) {
    $_Dialog = [
     "Body" => "The next Election may be held after ".date("F jS\, Y", $electionTime)."."
    ];
   } else {
    $eligibleCandidates = 0;
    $threshold = $this->core->config["App"]["Illegal"] ?? 777;
    foreach($candidates as $member => $info) {
     $role = $info["Chamber"] ?? "";
     $role = ($role == "House") ? "HouseRepresentative" : "Senator";
     $votes = $info["Votes"] ?? 0;
     if($votes >= $threshold) {
      $congressionalStaff[$member] = $role;
      $eligibleCandidates++;
     } else {
      $newBallot[$member] = $info;
     }
    } if($eligibleCandidates > 0) {
     $congress["Members"] = $congressionalStaff;
     $this->core->Data("Save", ["app", md5("Congress"), $congress]);
     $this->core->Data("Save", ["app", md5("CongressionalBallot"), $newBallot]);
     $_Dialog = [
      "Body" => "$eligibleCandidates candidates have been elected into Congress. The next election may be held after $nextElection.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function Home(array $data): string {
   $_AddTopMargin = "0";
   $_Commands = "";
   $_View = "";
   $data = $data["Data"] ?? [];
   $chamber = $data["Chamber"] ?? "";
   $chambers = $data["Chambers"] ?? 0;
   $congress = $this->core->Data("Get", ["app", md5("Congress")]);
   $congressmen = $congress["Members"] ?? [];
   $houseRepresentatives = 0;
   $pub = $data["pub"] ?? 0;
   $senators = 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $yourRole = $congressmen[$you] ?? "";
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   foreach($congressmen as $member => $role) {
    if($role == "HouseRepresentative") {
     $houseRepresentatives++;
    } elseif($role = "Senator") {
     $senators++;
    }
   } if(!empty($chamber) && $chambers == 1) {
    $_AddTopMargin = 1;
    $options = "";
    $search = base64_encode("Search:Containers");
    $options = ($notAnon == 1) ? $this->core->Element(["button", "Ballot", [
     "class" => "OpenCard v2",
     "data-view" => base64_encode("v=$search&CARD=1&Chamber=$chamber&st=CongressionalBallot")
    ]]) : "";
    $options .= (!empty($yourRole)) ? $this->core->Element([
     "button", "Elect Candidates", [
      "class" => "OpenDialog v2",
      "data-encryption" => "AES",
      "data-view" => $this->core->AESencrypt("v=".base64_encode("Congress:Elect"))
     ]
    ]).$this->core->Element([
     "button", "Reported Content", [
      "class" => "OpenCard v2",
      "data-encryption" => "AES",
      "data-view" => $this->core->AESencrypt("v=$search&CARD=1&Chamber=$chamber&st=Congress")
     ]
    ]) : "";
    $_Commands = [
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".CongressionalChamberStaff$chamber",
       [
        $this->core->AESencrypt("v=$search&Chamber=$chamber&st=CongressionalStaff$chamber")
       ]
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[Congress.Chamber]" => $chamber,
      "[Congress.Staff.Options]" => $options
     ],
     "ExtensionID" => "4ded3808da05154205a26c869289b6a2"
    ];
   } else {
    $notAnon = ($this->core->ID !== $you) ? 1 : 0;
    $joinTheHouse = ($houseRepresentatives < 100 && $notAnon == 1) ? $this->core->Element([
     "button", "Become a House Representative", [
      "class" => "UpdateButton v2",
      "data-encryption" => "AES",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Join")."&Role=".base64_encode("HouseRepresentative"))
     ]
    ]) : "";
    $joinTheHouse = ($yourRole == "HouseRepresentative") ? $this->core->Element([
     "button", "Resign", [
      "class" => "UpdateButton v2",
      "data-encryption" => "AES",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Leave")."&Role=".base64_encode("HouseRepresentative"))
     ]
    ]) : $joinTheHouse;
    $joinTheSenate = ($senators < 50 && $notAnon == 1) ? $this->core->Element([
     "button", "Become a Senator", [
      "class" => "UpdateButton v2",
      "data-encryption" => "AES",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Join")."&Role=".base64_encode("Senator"))
     ]
    ]) : "";
    $joinTheSenate = ($yourRole == "Senator") ? $this->core->Element([
     "button", "Resign", [
      "class" => "UpdateButton v2",
      "data-encryption" => "AES",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Leave")."&Role=".base64_encode("Senator"))
     ]
    ]) : $joinTheSenate;
    $_Commands = [
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".House",
       $this->core->AESencrypt("v=".base64_encode("Congress:Home")."&Chamber=House&Chambers=1"),
       5000
      ]
     ],
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".Senate",
       $this->core->AESencrypt("v=".base64_encode("Congress:Home")."&Chamber=Senate&Chambers=1"),
       5000
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[Congress.Chambers.House.Join]" => $joinTheHouse,
      "[Congress.Chambers.Senate.Join]" => $joinTheSenate,
      "[Congress.CoverPhoto]" => $this->core->PlainText([
       "Data" => "[Media:Congress]",
       "Display" => 1
      ])
     ],
     "ExtensionID" => "8a38a3053ce5449ca2d321719f5aea0f"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Commands" => $_Commands,
    "Title" => "Congress of ".$this->core->config["App"]["Name"],
    "View" => $_View
   ]);
  }
  function Join(array $data): string {
   $_Dialog = [
    "Body" => "The Command or Role are missing."
   ];
   $data = $data["Data"] ?? [];
   $command = $data["Command"] ?? "";
   $role = $data["Role"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_View = [
     "Attributes" => [
      "class" => "v2",
      "disabled" => "true"
     ],
     "Text" => "Sign In to Join"
    ];
   } elseif(!empty($command) && !empty($role)) {
    $_Dialog = "";
    $command = base64_decode($command);
    $congress = $this->core->Data("Get", ["app", md5("Congress")]);
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
     $check = ($houseRepresentatives < 100 && $role == "HouseRepresentative") ? 1 : 0;
     $check2 = ($senators < 50 && $role == "Senator") ? 1 : 0;
     $_View = [
      "Attributes" => [
       "class" => "v2",
       "disabled" => "true"
      ],
      "Text" => "You must be Elected"
     ];
     if($check == 1 || $check2 == 1) {
      $congressmen[$you] = $role;
      $congress["Members"] = $congressmen;
      $_View = [
       "Attributes" => [
        "class" => "UpdateButton v2",
        "data-encryption" => "AES",
        "data-processor" => $this->core->AESencrypt("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Leave")."&Role=".base64_encode($role))
       ],
       "Text" => "Resign"
      ];
     }
    } elseif($command == "Leave") {
     $congress["Members"] = $newCongressmen;
     $_View = [
      "Attributes" => [
       "class" => "UpdateButton v2",
       "data-encryption" => "AES",
       "data-processor" => $this->core->AESencrypt("v=".base64_encode("Congress:Join")."&Command=".base64_encode("Join")."&Role=".base64_encode($role))
      ],
      "Text" => "Become a $becomeRole"
     ];
    }
    $this->core->Data("Save", ["app", md5("Congress"), $congress]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Nominate(array $data): string {
   $_AccessCode = "Accepted";
   $_Commands = "";
   $_Dialog = "";
   $_View = "";
   $_ResponseType = "N/A";
   $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]);
   $data = $data["Data"] ?? [];
   $addToBallot = $data["AddToBallot"] ?? "";
   $chamber = $data["Chamber"] ?? "";
   $congress = $this->core->Data("Get", ["app", md5("Congress")]);
   $congressionalStaff = $congress["Members"] ?? [];
   $member = $data["Username"] ?? "";
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($member)) {
    if(!empty($addToBallot)) {
     $_AccessCode = "Denied";
     $_Dialog = [
      "Body" => "The Chamber Identifier is missing."
     ];
     $data = $this->core->DecodeBridgeData($data);
     $addToBallot = $data["AddToBallot"] ?? $addToBallot;
     $chamber = $data["Chamber"] ?? $chamber;
     $member = $data["Username"] ?? $member;
     if(($chamber == "House" || $chamber == "Senate") && $addToBallot == 1) {
      $_AccessCode = "Accepted";
      $_Dialog = "";
      $_ResponseType = "ReplaceContent";
      $isOnStaff = 0;
      foreach($congressionalStaff as $staff => $role) {
       if($staff == $member) {
        $isOnStaff++;
       }
      } if($isOnStaff > 0) {
       $_View = "$member is already in the Congressional Staff.";
      } else {
       $nominee = $ballot[$member] ?? [];
       $votes = $nominee["Votes"] ?? 0;
       $votes++;
       $nominee["Role"] = $chamber;
       $nominee["Votes"] = $votes;
       $ballot[$member] = $nominee;
       $this->core->Data("Save", ["app", md5("CongressionalBallot"), $ballot]);
       $_View = "You nominated $member for Congress!";
      }
      $_View = $this->core->Element(["div", $this->core->Element([
        "h2", "Done", ["class" => "CenterText UpperCase"]
       ]).$this->core->Element([
        "p", $_View, ["class" => "CenterText"]
       ]), ["class" => "FrostedBright Rounded"]
      ]);
      $_View = [
       "ChangeData" => [],
       "Extension" => $this->core->AESencrypt($_View)
      ];
     }
    } else {
     $_Dialog = "";
     $member = base64_decode($member);
     $member = ($member == $you) ? $y : $this->core->Member($member);
     if(!empty($member["Login"])) {
      $electionTime = $congress["NextElection"] ?? strtotime($this->core->timestamp);
      $electionTime = (strtotime($this->core->timestamp) >= $electionTime) ? 1 : 0;
      $isElectable = $member["Personal"]["Electable"] ?? 0;
      $isElectable = ($electionTime == 1 && $isElectable == 1) ? 1 : 0;
      $them = $member["Login"]["Username"];
      $diaplayName = $member["Personal"]["DisplayName"] ?? $them;
      if($isElectable == 1 && $them != $this->core->ID && $them != $you) {
       $isNominated = 0;
       $isOnStaff = 0;
       foreach($ballot as $nominee => $info) {
        if($nominee == $them) {
         $isNominated++;
        }
       } foreach($congressionalStaff as $staff => $role) {
        if($staff == $them) {
         $isOnStaff++;
        }
       } if($isNominated == 0 && $isOnStaff == 0) {
        $_Commands = [
         "Name" => "RenderInputs",
         "Parameters" => [
          ".NominateMember".md5($them),
          [
           [
            "Attributes" => [
             "name" => "AddToBallot",
             "type" => "hidden"
            ],
            "OptionGroup" => [],
            "Options" => [],
            "Type" => "Text",
            "Value" => 1
           ],
           [
            "Attributes" => [
             "name" => "Username",
             "type" => "hidden"
            ],
            "OptionGroup" => [],
            "Options" => [],
            "Type" => "Text",
            "Value" => $them
           ],
           [
            "Attributes" => [],
            "OptionGroup" => [
             "House" => "House",
             "Senate" => "Senate"
            ],
            "Options" => [
             "Header" => 1,
             "HeaderText" => "Congressional Chamber"
            ],
            "Name" => "Chamber",
            "Title" => "Chamber",
            "Type" => "Select",
            "Value" => ""
           ]
          ]
         ]
        ];
        $_View = [
         "ChangeData" => [
          "[Nomination.DisplayName]" => $diaplayName,
          "[Nomination.ID]" => md5($them),
          "[Nomination.Save]" => $this->core->AESencrypt("v=".base64_encode("Congress:Nominate")),
          "[Nomination.Username]" => $them
         ],
         "ExtensionID" => "f10284649796c26dd863d3872379e7d9"
        ];
       }
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function Notes(array $data): string {
   $_AccessCode = "Dernied";
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Content or Database Identifier are missing."
   ];
   $_ResponseType = "N/A";
   $data = $data["Data"] ?? [];
   $databaseID = $data["dbID"] ?? "";
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($databaseID) && !empty($id)) {
    $_AddNote = $this->core->AESencrypt("v=".base64_encode("Congress:Notes")."&Add=1&ID=$id&dbID=$databaseID");
    $_Dialog = "";
    $_Extension = $this->core->Extension("bdd25e7c79eeafb218f1c2c76a49067b");
    $_View = "";
    $_Congress = $this->core->Data("Get", ["app", md5("Congress")]);
    $databaseID = base64_decode($databaseID);
    $id = base64_decode($id);
    $congressmen = $_Congress["Members"] ?? [];
    $notesSourceContent = $this->core->Data("Get", [$databaseID, $id]);
    $notes = $notesSourceContent["Notes"] ?? [];
    if(empty($congressmen[$you]) && !empty($notes)) {
     if(count($notes) > 1) {
      $rank = 0;
      foreach($notes as $note => $info) {
       $helpful = 0;
       $notHelpful = 0;
       $votes = $info["Votes"] ?? [];
       foreach($votes as $member => $vote) {
        if($vote == "Down") {
         $notHelpful++;
        } elseif($vote == "Up") {
         $helpful++;
        }
       }
       $noteRank = $helpful - $notHelpful;
       if($noteRank >= $rank) {
        $rank = $noteRank;
        $author = $this->core->Member($info["UN"]);
        $displayName = $author["Personal"]["DisplayName"] ?? "[REDACTED]";
        $_View = [
         "ChangeData" => [
         "[Notes.Body]" => $info["Note"],
         "[Notes.Created]" => $info["Created"],
         "[Notes.DisplayName]" => $displayName,
         "[Notes.NoteID]" => ""
         ],
         "Extension" => $this->core->AESencrypt($this->core->Element([
          "h4", "Congressional Notes"
         ]).$_Extension)
        ];
       }
      }
     } else {
      foreach($notes as $note => $info) {
       $author = $this->core->Member($info["UN"]);
       $displayName = $author["Personal"]["DisplayName"] ?? "[REDACTED]";
       $noteList .= $this->core->Change([[
        "[Notes.Body]" => $info["Note"],
        "[Notes.Created]" => $info["Created"],
        "[Notes.DisplayName]" => $displayName,
        "[Notes.NoteID]" => "",
       ], $_Extension]);
      }
     }
     $_View = $this->core->Element(["div", $_View, [
      "class" => "FrostedBright Rounded"
     ]]);
     $_View = [
      "ChangeData" => [],
      "Extension" => $this->core->AESencrypt($_View)
     ];
    } elseif(!empty($congressmen[$you])) {
     $add = $data["Add"] ?? 0;
     $save = $data["Save"] ?? 0;
     $saveVote = $data["SaveVote"] ?? 0;
     $vote = $data["Vote"] ?? 0;
     if($add == 1) {
      $_Type = ($databaseID == "pg") ? "Page" : "";
      $_Type = ($databaseID == "su") ? "StatusUpdate" : $_Type;
      $dataModel = $this->core->GetContentData([
       "BackTo" => "",
       "Blacklisted" => 0,
       "ID" => base64_encode($_Type.";$id")
      ]);
      $noteID = $this->core->UUID("CongressionalNoteBy$you");
      $preview = $dataModel["Preview"];
      $preview = ($dataModel["Empty"] == 1) ? $preview["Empty"] : $preview["Content"];
      $attachments = $this->view(base64_encode("WebUI:Attachments"), [
       "Header" => "Attachments",
       "ID" => $noteID,
       "Media" => [
        "Album" => [],
        "Article" => [],
        "Attachment" => [],
        "Blog" => [],
        "BlogPost" => [],
        "Chat" => [],
        "Forum" => [],
        "ForumPost" => [],
        "Member" => [],
        "Poll" => [],
        "Product" => [],
        "Shop" => [],
        "Update" => []
       ]
      ]);
      $translateAndViewDeign = $this->view(base64_encode("WebUI:Attachments"), [
       "ID" => $noteID,
       "Media" => [
        "Translate" => [],
        "ViewDesign" => []
       ]
      ]);
      $_Card = [
       "Action" => $this->core->Element(["button", "Add", [
        "class" => "CardButton SendData",
        "data-form" => ".EditCongressionalNote$noteID",
        "data-processor" => base64_encode("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($id)."&Save=1&dbID=".base64_encode($databaseID))
       ]]),
       "Front" => [
        "ChangeData" => [
         "[Notes.Attachments]" => $this->core->RenderView($attachments),
         "[Notes.NoteID]" => $noteID,
         "[Notes.Preview]" => $preview,
         "[Notes.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign)
        ],
        "ExtensionID" => "8a016ee410595abcc9a119f63ca21a26"
       ]
      ];
      $_Commands = [
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         ".CongressionalNote$noteID",
         [
          [
           "Attributes" => [
            "name" => "SecureDatabaseID",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $databaseID
          ],
          [
           "Attributes" => [
            "name" => "SecureID",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $id
          ],
          [
           "Attributes" => [
            "class" => "req",
            "name" => "Body",
            "placeholder" => "Body"
           ],
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "NONAME",
            "Header" => 1,
            "HeaderText" => "Body"
           ],
           "Type" => "TextBox",
           "Value" => ""
          ]
         ]
        ]
       ]
      ];
     } elseif($save == 1) {
      $_Dialog = [
       "Body" => "The Content Identifier is missing."
      ];
      $data = $this->core->DecodeBridgeData($data);
      $contentID = $data["SecureID"] ?? "";
      $databaseID = $data["SecureDatabaseID"] ?? "";
      $content = $this->core->Data("Get", [$databaseID, $contentID]);
      $_ResponseType = "Dialog";
      if(empty($databaseID)) {
       $_Dialog = [
        "Body" => "The Database Identifier is missing."
       ];
      } elseif(!empty($content)) {
       $_AccessCode = "Accepted";
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
       $chats = [];
       $chatsData = $data["Chat"] ?? [];
       $forums = [];
       $forumsData = $data["Forum"] ?? [];
       $forumPosts = [];
       $forumPostsData = $data["ForumPost"] ?? [];
       $members = []; 
       $membersData = $data["Member"] ?? [];
       $noteList = $content["Notes"] ?? [];
       $polls = []; 
       $pollsData = $data["Poll"] ?? [];
       $products = [];
       $productsData = $data["Product"] ?? [];
       $shops = [];
       $shopsData = $data["Shop"] ?? [];
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
       array_push($noteList, [
        "Albums" => $albums,
        "Articles" => $articles,
        "Attachments" => $attachments,
        "Blogs" => $blogs,
        "BlogPosts" => $blogPosts,
        "Chats" => $chats,
        "Created" => $this->core->timestamp,
        "Forums" => $forums,
        "ForumPosts" => $forumPosts,
        "Members" => $members,
        "Note" => htmlentities($data["Body"]),
        "Polls" => $polls,
        "Products" => $products,
        "Shops" => $shops,
        "UN" => $you,
        "Updates" => $updates,
        "Votes" => []
       ]);
       $content["Notes"] = $noteList;
       $this->core->Data("Save", [$databaseID, $contentID, $content]);
       $_Dialog = [
        "Body" => "Your Note has been added!",
        "Header" => "Done"
       ];
      }
     } elseif($saveVote == 1) {
      $noteID = $data["NoteID"] ?? "";
      $voteID = $data["VoteID"] ?? "";
      if(empty($noteID)) {
       $_Dialog = [
        "Body" => "The Note Identifier is missing."
       ];
      } elseif(empty($voteID)) {
       $_Dialog = [
        "Body" => "The Vote Identifier is missing."
       ];
      } elseif(!in_array($voteID, ["Down", "Up"])) {
       $_Dialog = [
        "Body" => "An invalid Vote Identifier was supplied."
       ];
      } else {
       $noteID = base64_decode($noteID);
       for($i = 0; $i <= count($notes); $i++) {
        if(!empty($notes[$i]["Votes"][$you])) {
         unset($notes[$i]["Votes"][$you]);
        }
       }
       $votes = $notes[$noteID]["Votes"] ?? [];
       $votes[$you] = $voteID;
       $notes[$noteID]["Votes"] = $votes;
       $notesSourceContent["Notes"] = $notes;
       $this->core->Data("Save", [$databaseID, $id, $notesSourceContent]);
       $_Dialog = [
        "Body" => "Your vote has been cast!",
        "Header" => "Done"
       ];
      }
     } elseif($vote == 1) {
      $noteID = $data["NoteID"] ?? "";
      if(!empty($noteID)) {
       $check = 0;
       $noteID = base64_decode($noteID);
       $votes = $notesSourceContent["Notes"][$noteID]["Votes"] ?? [];
       $yourVote = "";
       foreach($votes as $member => $vote) {
        if($member == $you) {
         $check = 1;
         $vote = ($vote == "Up") ? "Helpful" : "Not Helpful";
         $yourVote = $vote;
        }
       } if($check == 1) {
        $_Dialog = "";
        $_View = $this->core->Element(["p", "You voted this Note <em>$yourVote</em>."]);
       } else {
        $_Dialog = "";
        $_Vote = "v=".base64_encode("Congress:Notes")."&ID=".base64_encode($id)."&dbID=".base64_encode($databaseID)."&NoteID=".base64_encode($noteID)."&SaveVote=1&VoteID=";
        $_View = [
         "ChangeData" => [
          "[Notes.Helpful]" => $this->core->AESencrypt($_Vote."Up"),
          "[Notes.NoteID]" => $noteID,
          "[Notes.NotHelpful]" => $this->core->AESencrypt($_Vote."Down")
         ],
         "ExtensionID" => "77de16b56ee1c9f80e89ef8eed97662b"
        ];
       }
      }
     } elseif(!empty($notes)) {
      $_Commands = [];
      $_Dialog = "";
      $_View = "";
      $noteList = [];
      foreach($notes as $note => $info) {
       $author = $this->core->Member($info["UN"]);
       $displayName = $author["Personal"]["DisplayName"] ?? "[REDACTED]";
       $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($info, "LiveView");
       $verified = $author["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       array_push($_Commands, [
        "Name" => "UpdateCOntentAES",
        "Parameters" => [
         ".Vote$note",
         $this->core->AESencrypt("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($id)."&NoteID=".base64_encode($note)."&Vote=1&dbID=".base64_encode($databaseID))
        ]
       ]);
       array_push($noteList, $this->core->Change([[
        "[Notes.Body]" => $info["Note"],
        "[Notes.Created]" => $info["Created"],
        "[Notes.DisplayName]" => $displayName.$verified,
        "[Notes.NoteID]" => $note
       ], $_Extension]));
      }
      $noteList = array_reverse($noteList);
      foreach($noteList as $note) {
       $_View .= $note;
      }
      $_View = [
       "ChangeData" => [
        "[Notes.Add]" => $_AddNote,
        "[Notes.List]" => $_View
       ],
       "ExtensionID" => "d6531d7ef40646ecdefbff5b496cec79"
      ];
     } else {
      $_View = [
       "ChangeData" => [
        "[Notes.Add]" => $_AddNote
       ],
       "ExtensionID" => "583691b6bd614b1e3e6f3f9ebc60cd69"
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "Success" => "CloseCard",
    "View" => $_View
   ]);
  }
  function Report(array $data): string {
   $_Card = "";
   $_Dialog = [
    "Body" => "The Content Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif(!empty($id)) {
    $content = $this->core->GetContentData([
     "BackTo" => "",
     "ID" => $id
    ]);
    $wasDeemedLegal = $content["DataModel"]["CongressDeemedLegal"] ?? 0;
    if($wasDeemedLegal == 1) {
     $_Dialog = [
      "Body" => "Congressional action has already been taken, and Congress deemed this content legal in accordance with the United States Constitution and <em>".$this->core->config["App"]["Name"]."</em>'s Bill of Rights.",
      "Header" => "Forbidden",
     ];
    } else {
     $preview = $content["Preview"] ?? [];
     $preview = ($content["Empty"] == 1) ? $preview["Empty"] : $preview["Content"];
     $_Card = [
      "Front" => [
       "ChangeData" => [
        "[Content.ID]" => $id,
        "[Content.Processor]" => $this->core->AESencrypt("v=".base64_encode("Congress:SaveReport")."&ID=[ID]"),
        "[Content.SecureID]" => $this->core->AESencrypt($id),
        "[Content.Preview]" => $preview
       ],
       "ExtensionID" => "0eaea9fae43712d8c810c737470021b3"
      ]
     ];
     $_Dialog = "";
    }
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Dialog" => $_Dialog
   ]);
  }
  function SaveReport(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Content Identifier or Type are missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $type = $data["Type"] ?? "";
   $y = $this->you;
   if(!empty($id) && !empty($type)) {
    $_Dialog = [
     "Body" => "The Report Type is incorrect."
    ];
    $types = [
     "CriminalActs",
     "ChildPorn",
     "FairUse",
     "Privacy",
     "Terrorism"
    ];
    if(in_array($type, $types)) {
     $_Dialog = [
      "Body" => "The Content ID is missing."
     ];
     $contentID = explode(";", base64_decode($id));
     $additionalContentID = $contentID[2] ?? "";
     $contentType = $contentID[0] ?? "";
     $content = $this->core->GetContentData([
      "ID" => $id
     ]);
     $data = [];
     $dlc = [];
     $id = $contentID[1] ?? "";
     $limit = $this->core->config["App"]["Illegal"] ?? 777;
     $wasDeemedLegal = 0;
     $weight = ($type == "CriminalActs") ? ($limit / 1000) : 0;
     $weight = ($type == "ChildPorn") ? ($limit / 3) : $weight;
     $weight = ($type == "FairUse") ? ($limit / 100000) : $weight;
     $weight = ($type == "Privacy") ? ($limit / 10000) : $weight;
     $weight = ($type == "Terrorism") ? ($limit / 100) : $weight;
     if(!empty($contentType) && !empty($id)) {
      $data = $content["DataModel"];
      if($contentType == "Album") {
       if(!empty($data)) {
        $dlc = $data["Albums"][$additionalContentID] ?? [];
        $dlc["CongressDeemedLegal"] = $dlc["CongressDeemedLegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] + $weight;
        $dlc["Illegal"] = round($dlc["Illegal"]);
        $wasDeemedLegal = $dlc["CongressDeemedLegal"] ?? 0;
        $data["Albums"][$additionalContentID] = $dlc;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["fs", md5($id), $data]);
        }
       }
      } elseif($contentType == "Blog") {
       if(!empty($data)) {
        $data["CongressDeemedLegal"] = $data["CongressDeemedLegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] + $weight;
        $data["Illegal"] = round($data["Illegal"]);
        $wasDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["blg", $id, $data]);
        }
       }
      } elseif($contentType == "BlogPost") {
       if(!empty($data)) {
        $data["CongressDeemedLegal"] = $data["CongressDeemedLegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] + $weight;
        $data["Illegal"] = round($data["Illegal"]);
        $wasDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["bp", $id, $data]);
        }
       }
      } elseif($contentType == "File" && !empty($additionalContentID)) {
       if(!empty($data)) {
        $dlc = $data["Files"][$additionalContentID] ?? [];
        $dlc["CongressDeemedLegal"] = $dlc["CongressDeemedLegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] + $weight;
        $dlc["Illegal"] = round($dlc["Illegal"]);
        $wasDeemedLegal = $dlc["CongressDeemedLegal"] ?? 0;
        $data["Files"][$additionalContentID] = $dlc;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["fs", md5($id), $data]);
        }
       }
      } elseif($contentType == "Forum") {
       if(!empty($data)) {
        $data["CongressDeemedLegal"] = $data["CongressDeemedLegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] + $weight;
        $data["Illegal"] = round($data["Illegal"]);
        $wasDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["pf", $id, $data]);
        }
       }
      } elseif($contentType == "ForumPost") {
       if(!empty($data)) {
        $data["CongressDeemedLegal"] = $data["CongressDeemedLegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] + $weight;
        $data["Illegal"] = round($data["Illegal"]);
        $wasDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["post", $id, $data]);
        }
       }
      } elseif($contentType == "Page") {
       if(!empty($data)) {
        $data["CongressDeemedLegal"] = $data["CongressDeemedLegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] + $weight;
        $data["Illegal"] = round($data["Illegal"]);
        $wasDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["pg", $id, $data]);
        }
       }
      } elseif($contentType == "Product") {
       if(!empty($data)) {
        $data["CongressDeemedLegal"] = $data["CongressDeemedLegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] + $weight;
        $data["Illegal"] = round($data["Illegal"]);
        $wasDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["product", $id, $data]);
        }
       }
      } elseif($contentType == "StatusUpdate") {
       if(!empty($data)) {
        $data["CongressDeemedLegal"] = $data["CongressDeemedLegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] ?? 0;
        $data["Illegal"] = $data["Illegal"] + $weight;
        $data["Illegal"] = round($data["Illegal"]);
        $wasDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
        if($wasDeemedLegal == 0) {
         $this->core->Data("Save", ["su", $id, $data]);
        }
       }
      }
      $_AccessCode = "Accepted";
      $_Dialog = [
       "Body" => "The Content was reported.",
       "Header" => "Done"
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog
   ]);
  }
  function Vote(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Content Identifier or Vote Persuasion are missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $vote = $data["Vote"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($vote)) {
    $_AccessCode = "Accepted";
    $_Congress = $this->core->Data("Get", ["app", md5("Congress")]);
    $_Dialog = "";
    $_View = "";
    $congressmen = $_Congress["Members"] ?? [];
    $houseVotes = 0;
    $id = explode(";", base64_decode($id));
    $contentID = explode("-", $id[1]);
    $data = explode("-", $id[1]);
    $data = $this->core->Data("Get", [$contentID[0], $contentID[1]]);
    $illegal = 0;
    $legal = 0;
    $houseRepresentatives = 0;
    $senateVotes = 0;
    $senators = 0;
    foreach($congressmen as $member => $role) {
     if($role == "HouseRepresentative") {
      $houseRepresentatives++;
     } elseif($role = "Senator") {
      $senators++;
     }
    } if($id[0] == "File") {
     $files = $data;
     $data = $files["Files"] ?? [];
     $data = $data[$id[2]] ?? [];
    }
    $congress = $data["Congress"] ?? [];
    $congressDeemedLegal = $data["CongressDeemedLegal"] ?? 0;
    $votes = $congress["Votes"] ?? [];
    $yourRole = $congressmen[$you] ?? "";
    $yourVote = base64_decode($vote);
    $votes[$you] = [
     "Role" => $yourRole,
     "Vote" => $yourVote
    ];
    $congress["Votes"] = $votes;
    $data["Congress"] = $congress;
    foreach($votes as $member => $info) {
     $role = $info["Role"] ?? "";
     $vote = $info["Vote"] ?? "";
     if($role == "HouseRepresentative") {
      $houseVotes++;
     } elseif($role = "Senator") {
      $senateVotes++;
     }  if($vote == "Illegal") {
      $illegal++;
     } elseif($vote = "Legal") {
      $legal++;
     }
    } if($congressDeemedLegal == 1) {
     $_Dialog = [
      "Body" => "Congress has deemed this content legal, and no further action may be taken."
     ];
     $_View = "";
    } else {
     if($yourRole == "HouseRepresentative" && $houseVotes == $houseRepresentatives) {
      if($legal > $illegal) {
       $newData = [];
       foreach($data as $key => $value) {
        if($key != "Congress" && $key != "Illegal") {
         $newData[$key] = $value;
        }
       }
       $data = $newData;
       $data["CongressDeemedLegal"] = 1;
       $response = "The content has been placed back in circulation with prejudice.";
      } else {
       $response = "This content has been put forth for a Senate vote.";
      } if($id[0] == "File") {
       $files["Files"][$id[2]] = $data;
       $data = $files;
      }
      $this->core->Data("Save", [$contentID[0], $contentID[1], $data]);
     } elseif($yourRole == "Senator" && $senateVotes == $senators) {
      if($legal > $illegal || $legal == $illegal) {
       $newData = [];
       foreach($data as $key => $value) {
        if($key != "Congress" && $key != "Illegal") {
         $newData[$key] = $value;
        }
       }
       $data = $newData;
       $data["CongressDeemedLegal"] = 1;
       $response = "The content has been placed back in circulation with prejudice.";
      } else {
       $data["Purge"] = 1;
       $response = "The content has been purged as it was deemed illegal.";
      }
      if($id[0] == "File") {
       $files["Files"][$id[2]] = $data;
       $data = $files;
      }
      $this->core->Data("Save", [$contentID[0], $contentID[1], $data]);
     }
     $_Dialog = [
      "Body" => "Your <em>$yourVote</em> vote has been cast! $response",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function VoteForCandidate(array $data): string {
   $_Dialog = [
    "Body" => "The Candidate or Chamber Identifiers are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $candidate = $data["Candidate"] ?? "";
   $chamber = $data["Chamber"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($candidate) && !empty($chamber)) {
    $_Dialog = "";
    $_View = [
     "Body" => "Error voting for @$candidate..."
    ];
    $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]);
    $candidate = base64_decode($candidate);
    $chamber = base64_decode($chamber);
    $registeredVotes = $ballot["RegisteredVotes"] ?? [];
    if(empty($registeredVotes[$you])) {
     $_Dialog = "";
     $chamber = $ballot["Candidates"][$candidate]["Chamber"] ?? "House";
     $votes = $ballot["Candidates"][$candidate]["Votes"] ?? 0;
     $votes++;
     $ballot["Candidates"][$candidate]["Votes"] = $votes;
     $ballot["Candidates"][$candidate]["Chamber"] = $chamber;
     $ballot["RegisteredVotes"][$you] = $candidate;
     $this->core->Data("Save", ["app", md5("CongressionalBallot"), $ballot]);
     $_View = $this->core->Element(["p", "Your vote was cast for @$candidate!"]);
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
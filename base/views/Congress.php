<?php
 Class Congress extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Elect() {
   $accessCode = "Denied";
   $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]) ?? [];
   $candidates = $ballot["Candidates"] ?? [];
   $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
   $congressionalStaff = $congress["Members"] ?? [];
   $newBallot = [];
   $now = $this->core->timestamp;
   $nextElection = $this->core->Timeplus($now, 1, "month");
   $electionTime = $congress["ElectionTime"] ?? $now;
   $electionTime = (empty($congress["ElectionTime"])) ? strtotime($electionTime) : $electionTime;
   $r = [
    "Body" => "There are currently no eligible candidates to elect."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif(strtotime($now) < $electionTime) {
    $r = [
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
     $accessCode = "Accepted";
     $congress["Members"] = $congressionalStaff;
     $this->core->Data("Save", ["app", md5("Congress"), $congress]);
     $this->core->Data("Save", ["app", md5("CongressionalBallot"), $newBallot]);
     $r = [
      "Body" => "$eligibleCandidates candidates have been elected into Congress. The next election may be held after $nextElection.",
      "Header" => "Done"
     ];
    }
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
  function Home(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $chamber = $data["Chamber"] ?? "";
   $chambers = $data["Chambers"] ?? 0;
   $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
   $congressmen = $congress["Members"] ?? [];
   $addTopMargin = "0";
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
    $addTopMargin = "1";
    $options = "";
    $search = base64_encode("Search:Containers");
    $options = ($notAnon == 1) ? $this->core->Element(["button", "Ballot", [
     "class" => "OpenCard v2",
     "data-view" => base64_encode("v=$search&CARD=1&Chamber=$chamber&st=CongressionalBallot")
    ]]) : "";
    $options .= (!empty($yourRole)) ? $this->core->Element([
     "button", "Elect Candidates", [
      "class" => "OpenDialog v2",
      "data-view" => base64_encode("v=".base64_encode("Congress:Elect"))
     ]
    ]).$this->core->Element([
     "button", "Reported Content", [
      "class" => "OpenCard v2",
      "data-view" => base64_encode("v=$search&CARD=1&Chamber=$chamber&st=Congress")
     ]
    ]) : "";
    $r = $this->core->Change([[
     "[Congress.Chamber]" => $chamber,
     "[Congress.Staff]" => base64_encode("v=$search&Chamber=$chamber&st=CongressionalStaff$chamber"),
     "[Congress.Staff.Options]" => $options,
    ], $this->core->Extension("4ded3808da05154205a26c869289b6a2")]);
   } else {
    $notAnon = ($this->core->ID !== $you) ? 1 : 0;
    $joinTheHouse = ($houseRepresentatives < 100 && $notAnon == 1) ? $this->core->Element([
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
    $joinTheSenate = ($senators < 50 && $notAnon == 1) ? $this->core->Element([
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
    ], $this->core->Extension("8a38a3053ce5449ca2d321719f5aea0f")]);
   } if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => $addTopMargin,
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
     $check = ($houseRepresentatives < 100 && $role == "HouseRepresentative") ? 1 : 0;
     $check2 = ($senators < 50 && $role == "Senator") ? 1 : 0;
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
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Nominate(array $a) {
   $accessCode = "Accepted";
   $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]) ?? [];
   $data = $a["Data"] ?? [];
   $addToBallot = $data["AddToBallot"] ?? "";
   $chamber = $data["Chamber"] ?? "";
   $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
   $congressionalStaff = $congress["Members"] ?? [];
   $member = $data["Username"] ?? "";
   $r = "";
   $responseType = "View";
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($member)) {
    if(!empty($addToBallot)) {
     $accessCode = "Denied";
     $data = $this->core->DecodeBridgeData($data);
     $addToBallot = $data["AddToBallot"] ?? $addToBallot;
     $chamber = $data["Chamber"] ?? $chamber;
     $member = $data["Username"] ?? $member;
     $r = [
      "Body" => "The Chamber is missing."
     ];
     $responseType = "Dialog";
     if(($chamber == "House" || $chamber == "Senate") && $addToBallot == 1) {
      $accessCode = "Accepted";
      $isOnStaff = 0;
      foreach($congressionalStaff as $staff => $role) {
       if($staff == $member) {
        $isOnStaff++;
       }
      } if($isOnStaff > 0) {
       $r = "$member is already in the Congressional Staff.";
      } else {
       $nominee = $ballot[$member] ?? [];
       $votes = $nominee["Votes"] ?? 0;
       $votes++;
       $nominee["Role"] = $chamber;
       $nominee["Votes"] = $votes;
       $ballot[$member] = $nominee;
       $this->core->Data("Save", ["app", md5("CongressionalBallot"), $ballot]);
       $r = "You nominated $member for Congress!";
      }
      $r = $this->core->Element(["div", $this->core->Element([
        "h2", "Done", ["class" => "CenterText UpperCase"]
       ]).$this->core->Element([
        "p", $r, ["class" => "CenterText"]
       ]), ["class" => "FrostedBright Rounded"]
      ]);
      $responseType = "ReplaceContent";
     }
    } else {
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
        $r = $this->core->Change([[
         "[Nomination.DisplayName]" => $diaplayName,
         "[Nomination.ID]" => md5($them),
         "[Nomination.Save]" => base64_encode("v=".base64_encode("Congress:Nominate")),
         "[Nomination.Username]" => $them
        ], $this->core->Extension("f10284649796c26dd863d3872379e7d9")]);
       }
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function Notes(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $databaseID = $data["dbID"] ?? "";
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Content or Database Identifier are missing."
   ];
   $responseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($databaseID) && !empty($id)) {
    $_AddNote = base64_encode("v=".base64_encode("Congress:Notes")."&Add=1&ID=$id&dbID=$databaseID");
    $_Congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
    $accessCode = "Accepted";
    $congressmen = $_Congress["Members"] ?? [];
    $databaseID = base64_decode($databaseID);
    $extension = $this->core->Extension("bdd25e7c79eeafb218f1c2c76a49067b");
    $id = base64_decode($id);
    $notesSourceContent = $this->core->Data("Get", [$databaseID, $id]) ?? [];
    $notes = $notesSourceContent["Notes"] ?? [];
    $r = $this->core->Element(["div", NULL, ["class" => "NONAME"]]);
    $responseType = "View";
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
        $r = $this->core->Element([
         "h4", "Congressional Notes"
        ]).$this->core->Change([[
         "[Notes.Body]" => $info["Note"],
         "[Notes.Created]" => $info["Created"],
         "[Notes.DisplayName]" => $displayName,
         "[Notes.NoteID]" => "",
         "[Notes.Vote]" => ""
        ], $extension]);
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
        "[Notes.Vote]" => ""
       ], $extension]);
      }
     }
     $r = $this->core->Element(["div", $r, ["class" => "FrostedBright Rounded"]]);
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
      $r = [
       "Action" => $this->core->Element(["button", "Add", [
        "class" => "CardButton SendData",
        "data-form" => ".EditCongressionalNote$noteID",
        "data-processor" => base64_encode("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($id)."&Save=1&dbID=".base64_encode($databaseID))
       ]]),
       "Front" => $this->core->Change([[
        "[Notes.Attachments]" => $this->core->RenderView($attachments),
        "[Notes.DatabaseID]" => $databaseID,
        "[Notes.ID]" => $id,
        "[Notes.NoteID]" => $noteID,
        "[Notes.Preview]" => $preview,
        "[Notes.TranslateAndViewDesign]" => $this->core->RenderView($translateAndViewDeign)
       ], $this->core->Extension("8a016ee410595abcc9a119f63ca21a26")])
      ];
      $responseType = "Card";
     } elseif($save == 1) {
      $data = $this->core->DecodeBridgeData($data);
      $contentID = $data["SecureID"] ?? "";
      $databaseID = $data["SecureDatabaseID"] ?? "";
      $content = $this->core->Data("Get", [$databaseID, $contentID]) ?? [];
      $r = [
       "Body" => "The Content Identifier is missing."
      ];
      $responseType = "Dialog";
      if(empty($databaseID)) {
       $r = [
        "Body" => "The Database Identifier is missing."
       ];
      } elseif(!empty($content)) {
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
       $r = [
        "Body" => "Your Note has been added!",
        "Header" => "Done"
       ];
      }
     } elseif($saveVote == 1) {
      $noteID = $data["NoteID"] ?? "";
      $voteID = $data["VoteID"] ?? "";
      $responseType = "Dialog";
      $r = [
       "Body" => "Unknown error."
      ];
      if(empty($noteID)) {
       $r = [
        "Body" => "The Note Identifier is missing."
       ];
      } elseif(empty($voteID)) {
       $r = [
        "Body" => "The Vote Identifier is missing."
       ];
      } elseif(!in_array($voteID, ["Down", "Up"])) {
       $r = [
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
       $r = [
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
        $r = $this->core->Element(["p", "You voted this Note <em>$yourVote</em>."]);
       } else {
        $_Vote = "v=".base64_encode("Congress:Notes")."&ID=".base64_encode($id)."&dbID=".base64_encode($databaseID)."&NoteID=".base64_encode($noteID)."&SaveVote=1&VoteID=";
        $r = $this->core->Change([[
         "[Notes.Helpful]" => base64_encode($_Vote."Up"),
         "[Notes.NoteID]" => $noteID,
         "[Notes.NotHelpful]" => base64_encode($_Vote."Down")
        ], $this->core->Extension("77de16b56ee1c9f80e89ef8eed97662b")]);
       }
      }
     } elseif(!empty($notes)) {
      $noteList = [];
      $r = "";
      foreach($notes as $note => $info) {
       $author = $this->core->Member($info["UN"]);
       $displayName = $author["Personal"]["DisplayName"] ?? "[REDACTED]";
       $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($info, "LiveView");
       $verified = $author["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       array_push($noteList, $this->core->Change([[
        "[Attached.Albums]" => $liveViewSymbolicLinks["Albums"],
        "[Attached.Articles]" => $liveViewSymbolicLinks["Articles"],
        "[Attached.Attachments]" => $liveViewSymbolicLinks["Attachments"],
        "[Attached.Blogs]" => $liveViewSymbolicLinks["Blogs"],
        "[Attached.BlogPosts]" => $liveViewSymbolicLinks["BlogPosts"],
        "[Attached.Chats]" => $liveViewSymbolicLinks["Chats"],
        "[Attached.DemoFiles]" => $liveViewSymbolicLinks["DemoFiles"],
        "[Attached.Forums]" => $liveViewSymbolicLinks["Forums"],
        "[Attached.ForumPosts]" => $liveViewSymbolicLinks["ForumPosts"],
        "[Attached.ID]" => $this->core->UUID("ForumPostAttachments"),
        "[Attached.Members]" => $liveViewSymbolicLinks["Members"],
        "[Attached.Polls]" => $liveViewSymbolicLinks["Polls"],
        "[Attached.Products]" => $liveViewSymbolicLinks["Products"],
        "[Attached.Shops]" => $liveViewSymbolicLinks["Shops"],
        "[Attached.Updates]" => $liveViewSymbolicLinks["Updates"],
        "[Notes.Body]" => $info["Note"],
        "[Notes.Created]" => $info["Created"],
        "[Notes.DisplayName]" => $displayName.$verified,
        "[Notes.NoteID]" => $note,
        "[Notes.Vote]" => $this->core->RenderView($this->view(base64_encode("Congress:Notes"), ["Data" => [
         "ID" => base64_encode($id),
         "NoteID" => base64_encode($note),
         "Vote" => 1,
         "dbID" => base64_encode($databaseID)
        ]]))
       ], $extension]));
      }
      $noteList = array_reverse($noteList);
      foreach($noteList as $note) {
       $r .= $note;
      }
      $r = $this->core->Change([[
       "[Notes.Add]" => $_AddNote,
       "[Notes.List]" => $r
      ], $this->core->Extension("d6531d7ef40646ecdefbff5b496cec79")]);
     } else {
      $r = $this->core->Change([[
       "[Notes.Add]" => $_AddNote
      ], $this->core->Extension("583691b6bd614b1e3e6f3f9ebc60cd69")]);
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "AddTopMargin" => "0",
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType,
    "Success" => "CloseCard"
   ]);
  }
  function Report(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $r = [
    "Body" => "The Content Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue."
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $contentID = explode(";", base64_decode($id));
    $content = $this->core->GetContentData([
     "BackTo" => "",
     "ID" => $id
    ]) ?? [];
    $listItem = $content["ListItem"] ?? [];
    $description = (!empty($listItem["Description"])) ? $this->core->Element([
     "p", $listItem["Description"]
    ]) : "";
    $title = (!empty($listItem["Title"])) ? $this->core->Element([
     "h3", $listItem["Title"]
    ]) : "";
    $wasDeemedLegal = $content["DataModel"]["CongressDeemedLegal"] ?? 0;
    if($wasDeemedLegal == 1) {
     $r = $this->core->Element([
      "h1", "Forbidden", ["class" => "CenterText UpperCase"]
     ]).$this->core->Element([
      "p", "Congressional action has already been taken, and Congress deemed this content legal in accordance with the United States Constitution and ".$this->core->config["App"]["Name"]."'s Bill of Rights",
      ["class" => "CenterText"]
     ]);;
    } else {
     $preview = $content["Preview"] ?? [];
     $preview = ($content["Empty"] == 1) ? $preview["Empty"] : $preview["Content"];
     $r = $this->core->Change([[
      "[Content.ID]" => $id,
      "[Content.Processor]" => base64_encode("v=".base64_encode("Congress:SaveReport")."&ID=[ID]"),
      "[Content.SecureID]" => base64_encode($id),
      "[Content.Preview]" => $preview
     ], $this->core->Extension("0eaea9fae43712d8c810c737470021b3")]);
    }
    $r = [
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
  function SaveReport(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $type = $data["Type"] ?? "";
   $r = [
    "Body" => "The Content Identifier or Type are missing."
   ];
   $y = $this->you;
   if(!empty($id) && !empty($type)) {
    $id = base64_decode($id);
    $type = base64_decode($type);
    $r = [
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
     $accessCode = "Accepted";
     $contentID = explode(";", base64_decode($id));
     $additionalContentID = $contentID[2] ?? "";
     $contentType = $contentID[0] ?? "";
     $content = $this->core->GetContentData([
      "ID" => $id
     ]) ?? [];
     $id = $contentID[1] ?? "";
     $limit = $this->core->config["App"]["Illegal"] ?? 777;
     $r = [
      "Body" => "The Content ID is missing."
     ];
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
      $r = [
       "Body" => "The Content was reported.",
       "Header" => "Done"
      ];
     }
    }
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
  function Vote(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $r = [
    "Body" => "The Content Identifier or Vote Persuasion are missing."
   ];
   $vote = $data["Vote"] ?? base64_encode("");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($vote)) {
    $_Congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
    $accessCode = "Accepted";
    $congressmen = $_Congress["Members"] ?? [];
    $houseVotes = 0;
    $id = explode(";", base64_decode($id));
    $contentID = explode("-", $id[1]);
    $data = explode("-", $id[1]);
    $data = $this->core->Data("Get", [$contentID[0], $contentID[1]]) ?? [];
    $illegal = 0;
    $legal = 0;
    $r = "";
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
     $r = [
      "Body" => "Congress has deemed this content legal, and no further action may be taken."
     ];
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
       $r = "The content has been placed back in circulation with prejudice.";
      } else {
       $r = "This content has been put forth for a Senate vote.";
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
       $r = "The content has been placed back in circulation with prejudice.";
      } else {
       $data["Purge"] = 1;
       $r = "The content has been purged as it was deemed illegal.";
      }
      if($id[0] == "File") {
       $files["Files"][$id[2]] = $data;
       $data = $files;
      }
      $this->core->Data("Save", [$contentID[0], $contentID[1], $data]);
     }
     $r = [
      "Body" => "Your <em>$yourVote</em> vote has been cast! $r",
      "Header" => "Done"
     ];
    }
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
  function VoteForCandidate(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $candidate = $data["Candidate"] ?? "";
   $chamber = $data["Chamber"] ?? "";
   $r = [
    "Body" => "The Candidate or Chamber Identifiers are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($candidate) && !empty($chamber)) {
    $accessCode = "Accepted";
    $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]);
    $candidate = base64_decode($candidate);
    $chamber = base64_decode($chamber);
    $r = $this->core->Element(["p", "Error voting for @$candidate..."]);
    $registeredVotes = $ballot["RegisteredVotes"] ?? [];
    if(empty($registeredVotes[$you])) {
     $chamber = $ballot["Candidates"][$candidate]["Chamber"] ?? "House";
     $votes = $ballot["Candidates"][$candidate]["Votes"] ?? 0;
     $votes++;
     $ballot["Candidates"][$candidate]["Votes"] = $votes;
     $ballot["Candidates"][$candidate]["Chamber"] = $chamber;
     $ballot["RegisteredVotes"][$you] = $candidate;
     $this->core->Data("Save", ["app", md5("CongressionalBallot"), $ballot]);
     $r = $this->core->Element(["p", "Your vote was cast for @$candidate!"]);
    }
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
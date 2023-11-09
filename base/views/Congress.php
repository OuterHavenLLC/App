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
     "Blacklisted" => 0,
     "BlogID" => "",
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
     $r = $this->core->Change([[
      "[Content.Attachments]" => $listItem["Attachments"],
      "[Content.Body]" => $listItem["Body"],
      "[Content.Description]" => $description,
      "[Content.ID]" => base64_encode($id),
      "[Content.Processor]" => base64_encode("v=".base64_encode("Congress:SaveReport")."&ID=[ID]"),
      "[Content.Title]" => $title
     ], $this->core->Page("0eaea9fae43712d8c810c737470021b3")]);
    }
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
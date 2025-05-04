<?php
 Class Poll extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Create(): string {
   $option = $this->core->Extension("3013dd986b7729f1fc38b82586ee9d8d");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $id = $this->core->UUID("ANewPollBy$you");
   return $this->core->JSONResponse([
    "Card" => [
     "Action" => $this->core->Element(["button", "Post", [
      "class" => "CardButton SendData",
      "data-form" => ".NewPoll$id",
      "data-processor" => base64_encode("v=".base64_encode("Poll:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Poll.ID]" => $id,
       "[Poll.Option]" => str_replace("[Clone.ID]", "DefaultOption", $option),
       "[Poll.OptionClone]" => base64_encode($option),
       "[Poll.Visibility.NSFW]" => $y["Privacy"]["NSFW"],
       "[Poll.Visibility.Privacy]" => $y["Privacy"]["Posts"]
      ],
      "ExtensionID" => "823bed33cd089cc8973d0fbc56dbfa28"
     ]
    ]
   ]);
  }
  function Home(array $data): string {
   $_Dialog = [
    "Body" => "The Poll Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $containers = $data["Containers"] ?? 1;
   $id = $data["ID"] ?? "";
   $public = $data["pub"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $id = base64_decode($id);
    $bl = $this->core->CheckBlocked([$y, "Polls", $id]);
    $_Poll = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Poll;$id")
    ]);
    if($_Poll["Empty"] == 0) {
     $_Dialog = "";
     $blockCommand = ($bl == 0) ? "Block" : "Unblock";
     $extension = $this->core->Element([
      "div", $extension, ["class" => "FrostedBright Poll$value Rounded"]
     ]);
     $options = $_Poll["ListItem"]["Options"];
     $blockOrDelete = ($poll["UN"] == $you) ? $this->core->Element([
      "div", $this->core->Element(["button", $blockCommand, [
       "class" => "UpdateButton v2 v2w",
       "data-processor" => $options["Block"]
      ]]), ["class" => "Desktop33"]
     ]).$this->core->Element([
      "div", $this->core->Element(["button", "Delete", [
       "class" => "OpenDialog v2 v2w",
       "data-view" => $options["Delete"]
      ]]), ["class" => "Desktop33"]
     ]) : "";
     $vote = "";
     $voteCounts = [];
     $votes = 0;
     $youVoted = 0;
     foreach($poll["Votes"] as $number => $info) {
      if($info[0] == $you) {
       $choice = $info[1] ?? 0;
       $voteCounts[$choice] = $voteCounts[$choice] ?? 0;
       $voteCounts[$choice]++;
       $votes++;
       $youVoted++;
      }
     } foreach($poll["Options"] as $number => $option) {
      $voteShare = $voteCounts[$number] ?? 0;
      $option = $this->core->Element([
       "h4", $option
      ]).$this->core->Element(["progress", $voteShare."%", [
       "max" => $votes,
       "value" => $voteShare
      ]]);
      if($this->core->ID == $you || $youVoted == 0) {
       $option = $this->core->Element(["button", $option, [
        "class" => "LI UpdateContent",
        "data-container" => ".Poll$id",
        "data-view" => base64_encode("v=".base64_encode("Poll:Vote")."&Choice=".base64_encode($number)."&ID=".base64_encode($id))
       ]]);
      }
      $vote .= $option;
     }
     $_Extension = $this->core->Extension("184ada666b3eb85de07e414139a9a0dc");
     $_Extension = ($containers == 1) ? $this->core->Element(["div", $_Extension, [
      "class" => "FrostedBright Poll[Poll.ID] Rounded"
     ]]) : $_Extension;
     $_View = [
      "ChangeData" => [
       "[Poll.BlockOrDelete]" => $blockOrDelete,
       "[Poll.Description]" => $_Poll["ListItem"]["Description"],
       "[Poll.ID]" => $id,
       "[Poll.Share]" => $options["Share"],
       "[Poll.Title]" => $_Poll["ListItem"]["Title"],
       "[Poll.Vote]" => $vote
      ],
      "Extension" => $this->core->AESencrypt($_Extension)
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_DIalog,
    "View" => $_View
   ]);
  }
  function Purge(array $data): string {
   $_Dialog = [
    "Body" => "The Poll Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $newPolls = [];
    $polls = $y["Polls"] ?? [];
    foreach($polls as $key => $poll) {
     if($id != $poll) {
      array_push($newPolls, $poll);
     }
    }
    $y["Polls"] = array_unique($newPolls);
    $poll = $this->core->Data("Get", ["poll", $id]);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query("DELETE FROM Polls WHERE Poll_ID=:ID", [
     ":ID" => $id
    ]);
    $sql->execute();
    if(!empty($poll)) {
     $poll["Purge"] = 1;
     $this->core->Data("Save", ["poll", $id, $poll]);
    }
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", "Your Poll was successfully deleted.",
      ["class" => "CenterText"]
     ]).$this->core->Element([
      "button", "Okay", ["class" => "CloseDialog v2 v2w"]
     ]))
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_DIalog,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Poll Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $max = 10;
   $optionData = $data["Option"] ?? [];
   $optionCount = count($optionData);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($optionCount < 2) {
    $_Dialog = [
     "Body" => "Please add more options to your poll."
    ];
   } elseif($optionCount == $max) {
    $_Dialog = [
     "Body" => "Only $max options are allowed."
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $contacts = $this->core->Data("Get", ["cms", md5($you)]);
    $contacts = $contacts["Contacts"] ?? [];
    $description = $data["Description"] ?? "";
    $nsfw = $data["NSFW"] ?? "";
    $options = [];
    for($i = 0; $i < $optionCount; $i++) {
     $option = $data["Option"][$i] ?? "";
     array_push($options, $option);
    }
    $options = array_unique($options);
    $privacy = $data["Privacy"] ?? "";
    $purge = $data["Purge"] ?? 0;
    $title = $data["Title"] ?? "";
    $poll = [
     "Created" => $this->core->timestamp,
     "Description" => $description,
     "NSFW" => $nsfw,
     "Options" => $options,
     "Privacy" => $privacy,
     "Purge" => $purge,
     "Title" => $title,
     "UN" => $you,
     "Votes" => []
    ];
    $polls = $y["Polls"] ?? [];
    array_push($polls, $id);
    $y["Polls"] = array_unique($polls);
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $query = "REPLACE INTO Polls(
     Poll_Created,
     Poll_Description,
     Poll_ID,
     Poll_NSFW,
     Poll_Privacy,
     Poll_Title,
     Poll_Username
    ) VALUES(
     :Created,
     :Description,
     :ID,
     :NSFW,
     :Privacy,
     :Title,
     :Username
    )";
    $sql->query($query, [
     ":Created" => $poll["Created"],
     ":Description" => $poll["Description"],
     ":ID" => $id,
     ":NSFW" => $poll["NSFW"],
     ":Privacy" => $poll["Privacy"],
     ":Title" => $poll["Title"],
     ":Username" => $poll["UN"]
    ]);
    $sql->execute();
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $this->core->Data("Save", ["poll", $id, $poll]);
    foreach($contacts as $key => $member) {
     $this->core->SendBulletin([
      "Data" => [
       "PollID" => $id
      ],
      "To" => $member,
      "Type" => "NewPoll"
     ]);
    }
    $_Dialog = [
     "Body" => "Your new poll has been saved.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_DIalog,
    "Success" => "CloseCard"
   ]);
  }
  function Vote(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Poll Identifier or Choice are missing."
   ];
   $_ResponseType = "N/A";
   $_View = "";
   $data = $data["Data"] ?? [];
   $choice = $data["Choice"] ?? "";
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($choice) && !empty($id)) {
    $_AccessCode = "Accepted";
    $_Dialog = "';"
    $_ResponseType = "ReplaceContent";
    $choice = base64_decode($choice);
    $id = base64_decode($id);
    $poll = $this->core->Data("Get", ["poll", $id]);
    $options = $poll["Options"] ?? [];
    foreach($options as $number => $option) {
     if($choice == $number) {
      array_push($poll["Votes"], [$you, $choice]);
     }
    }
    $this->core->Data("Save", ["poll", $id, $poll]);
    $_View = $this->view(base64_encode("Poll:Home"), ["Data" => [
     "Containers" => 0,
     "ID" => base64_encode($id)
    ]]);
    $_View = $this->RenderView($_View);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_DIalog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
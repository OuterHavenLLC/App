<?php
 Class Poll extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Create() {
   $accessCode = "Accepted";
   $option = $this->core->Extension("3013dd986b7729f1fc38b82586ee9d8d");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $id = md5("Polls$you".$this->core->timestamp);
   $r = [
    "Action" => $this->core->Element(["button", "Post", [
     "class" => "CardButton SendData",
     "data-form" => ".NewPoll$id",
     "data-processor" => base64_encode("v=".base64_encode("Poll:Save"))
    ]]),
    "Front" => $this->core->Change([[
     "[Poll.ID]" => $id,
     "[Poll.Option]" => $option,
     "[Poll.OptionClone]" => base64_encode($option),
     "[Poll.Visibility.NSFW]" => $y["Privacy"]["NSFW"],
     "[Poll.Visibility.Privacy]" => $y["Privacy"]["Posts"]
    ], $this->core->Extension("823bed33cd089cc8973d0fbc56dbfa28")])
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
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $containers = $data["Containers"] ?? 1;
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Poll Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
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
     $options = $_Poll["ListItem"]["Options"];
     $poll = $_Poll["DataModel"];
     $delete = ($poll["UN"] == $you) ? $this->core->Element([
      "div", $this->core->Element(["button", "Delete", [
       "class" => "OpenDialog v2 v2w",
       "data-view" => $options["Delete"]
      ]]), ["class" => "Desktop50"]
     ]) : "";
     $vote = "";
     $youVoted = 0;
     foreach($poll["Votes"] as $number => $info) {
      if($info[0] == $you) {
       $youVoted++;
      }
     } foreach($poll["Options"] as $number => $option) {
      $option = $this->core->Element(["p", "$number: $option"]);
      if($this->core->ID == $you || $youVoted == 0) {
       $option = $this->core->Element(["button", $option, [
        "class" => "LI UpdateContent v2 v2w",
        "data-container" => ".Poll$id",
        "data-view" => base64_encode("v=".base64_encode("Poll:Vote")."&Choice=".base64_encode($number)."&ID=".base64_encode($id))
       ]]);
      }
      $vote .= $option;
     }
     $r = $this->core->Change([[
      "[Poll.Delete]" => $delete,
      "[Poll.Description]" => $_Poll["ListItem"]["Description"],
      "[Poll.ID]" => $id,
      "[Poll.Share]" => $options["Share"],
      "[Poll.Title]" => $_Poll["ListItem"]["Title"],
      "[Poll.Vote]" => $vote
     ], $this->core->Extension("184ada666b3eb85de07e414139a9a0dc")]);
     $r = ($containers == 1) ? $this->core->Element([
      "div", $r, ["class" => "K4i Poll$id"]
     ]) : $r;
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $max = 10;
   $optionData = $data["Option"] ?? [];
   $optionCount = count($optionData);
   $r = [
    "Body" => "The Poll Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif($optionCount == 1) {
    $r = [
     "Body" => "Please add more options to continue."
    ];
   } elseif($optionCount == $max) {
    $r = [
     "Body" => "Only $max options are allowed."
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    $contacts = $this->core->Data("Get", ["cms", md5($you)]) ?? [];
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
    $title = $data["Title"] ?? "";
    $poll = [
     "Created" => $this->core->timestamp,
     "Description" => $description,
     "NSFW" => $nsfw,
     "Options" => $options,
     "Privacy" => $privacy,
     "Title" => $title,
     "UN" => $you,
     "Votes" => []
    ];
    $polls = $y["Polls"] ?? [];
    array_push($polls, $id);
    $y["Polls"] = array_unique($polls);
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
    $r = [
     "Body" => "Your new poll has been saved.",
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
    "Success" => "CloseCard"
   ]);
  }
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $pin = $data["PIN"] ?? "";
   $r = [
    "Body" => "The Poll Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($pin) != $y["Login"]["PIN"]) {
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
    $newPolls = [];
    $polls = $y["Polls"] ?? [];
    foreach($polls as $key => $poll) {
     if($id != $poll) {
      array_push($newPolls, $poll);
     }
    }
    $y["Polls"] = array_unique($newPolls);
    $this->core->Data("Purge", ["poll", $id]);
    $r = [
     "Body" => "Your Poll was deleted.",
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
  function Vote(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $choice = $data["Choice"] ?? "";
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Poll Identifier or Choice are missing."
   ];
   $responseType = "Dialog";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($choice) && !empty($id)) {
    $accessCode = "Accepted";
    $choice = base64_decode($choice);
    $id = base64_decode($id);
    $poll = $this->core->Data("Get", ["poll", $id]) ?? [];
    $options = $poll["Options"] ?? [];
    $responseType = "ReplaceContent";
    foreach($options as $number => $option) {
     if($choice == $number) {
      array_push($poll["Votes"], [$you, $choice]);
     }
    }
    $this->core->Data("Save", ["poll", $id, $poll]);
    $r = $this->view(base64_encode("Poll:Home"), ["Data" => [
     "Containers" => 0,
     "ID" => base64_encode($id)
    ]]);
    $r = $this->RenderView($r);
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
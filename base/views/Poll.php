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
     "[Poll.Option]" => $option.$option.$option.$option.$option,
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
    $bl = $this->core->CheckBlocked([$y, "Polls", $value]);
    $_Poll = $this->core->GetContentData([
     "Blacklisted" => $bl,
     "ID" => base64_encode("Poll;$id")
    ]);
    if($_Poll["Empty"] == 0) {
     $poll = $_Poll["DataModel"];
     $r = $this->core->Element(["p", "Coming soon..."]);
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
   $optionData = $data["Option"] ?? [];
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
   } elseif(count($optionData) == 1) {
    $r = [
     "Body" => "Please add more options to continue."
    ];
   } elseif(!empty($id)) {
    $contacts = $this->core->Data("Get", ["cms", md5($you)]) ?? [];
    $contacts = $contacts["Contacts"] ?? [];
    $description = $data["Description"] ?? "";
    $nsfw = $data["NSFW"] ?? "";
    $options = [];
    for($i = 0; $i < count($optionData); $i++) {
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
    "ResponseType" => "Dialog"
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
    $id = base64_decode($id);
    $newPolls = [];
    $polls = $y["Polls"] ?? [];
    foreach($polls as $key => $poll) {
     if($id != $poll) {
      array_push($newPolls, $poll);
     }
    }
    $y["Polls"] = array_unique($newPolls);
    #$this->core->Data("Purge", ["poll", $id]);
    $r = [
     "Body" => "Your Poll was deleted.",
     "Header" => "Done",
     "Scrollable" => json_ancode([$polls, $y["Polls"]], true)
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
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
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($choice) && !empty($id)) {
    // VOTE
    $r = [
     "Body" => "Comming soon...",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
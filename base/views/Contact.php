<?php
 Class Contact extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Delete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["Username"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Username is missing. Data: ".json_encode($data, true)]),
    "Header" => "Error"
   ]);
   $responseType = "Dialog";
   $username = $data["Username"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($username) && $username != $you) {
    $accessCode = "Accepted";
    $responseType = "Destruct";
    $username = base64_decode($username);
    $_theirContacts = $this->system->Data("Get", ["cms", md5($username)]) ?? [];
    $_yourContacts = $this->system->Data("Get", ["cms", md5($you)]) ?? [];
    $theirContacts = $_theirContacts["Contacts"] ?? [];
    $theirNewContacts = [];
    $yourContacts = $_yourContacts["Contacts"] ?? [];
    $yourNewContacts = [];
    $r = "&nbsp;";
    foreach($theirContacts as $key => $value) {
     if($key != $you) {
      $theirNewContacts[$key] = $value;
     }
    } foreach($yourContacts as $key => $value) {
     if($key != $username) {
      $yourNewContacts[$key] = $value;
     }
    }
    $_theirContacts["Contacts"] = $theirNewContacts;
    $_yourContacts["Contacts"] = $yourNewContacts;
    $this->system->Data("Save", ["cms", md5($username), $_theirContacts]);
    $this->system->Data("Save", ["cms", md5($you), $_yourContacts]);
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
  function Options(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["UN"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Username is missing."]),
    "Header" => "Error"
   ]);
   $username = $data["UN"];
   $y = $this->you;
   if(!empty($username)) {
    $username = base64_decode($username);
    $card = base64_encode("Profile:Home");
    $contacts = $this->system->Data("Get", [
     "cms",
     md5($y["Login"]["Username"])
    ]) ?? [];
    $contacts = $contacts["Contacts"] ?? [];
    $contact = $contacts[$username];
    $t = $this->system->Member($username);
    $r = $this->system->Change([[
     "[Contact.Card]" => base64_encode("CARD=1&v=$card&UN=".$data["UN"]),
     "[Contact.DisplayName]" => $t["Personal"]["DisplayName"],
     "[Contact.ID]" => md5($username),
     "[Contact.List]" => $this->system->Select("ContactList", "req v2 v2w", $contact["List"]),
     "[Contact.Notes]" => $contact["Notes"],
     "[Contact.ProfilePicture]" => $this->system->ProfilePicture($t, "width:100%"),
     "[Contact.Update]" => base64_encode("v=".base64_encode("Contact:Save")),
     "[Contact.Username]" => $username
    ], $this->system->Page("297c6906ec2f4cb2013789358c5ea77b")]);
   }
   return $r;
  }
  function Requests(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "Username",
    "accept",
    "bulletin",
    "decline"
   ]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Username is missing."]),
    "Header" => "Error"
   ]);
   $responseType = "Dialog";
   $username = $data["Username"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($username)) {
    $accept = $data["accept"] ?? 0;
    $accessCode = "Accepted";
    $bulletin = $data["bulletin"] ?? 0;
    $contactStatus = $this->view(base64_encode("Contact:Status"), [
     "Them" => $username,
     "You" => $you
    ]);
    $decline = $data["decline"] ?? 0;
    $r = $this->system->Element([
     "h3", "Success", ["class" => "CenterText UpperCase"]
    ]);
    $responseType = "ReplaceContent";
    $theirContacts = $this->system->Data("Get", [ "cms", md5($username)]) ?? [];
    $yourContacts = $this->system->Data("Get", [ "cms", md5($you)]) ?? [];
    $cancel = (in_array($you, $theirContacts["Requests"])) ? 1 : 0;
    $cancel = ($cancel == 1 || $contactStatus["YouRequested"] > 0) ? 1 : 0;
    if($accept == 1 || $decline == 1) {
     $r = $this->system->Element([
      "p", "$username retracted their request.",
      ["class" => "CenterText"]
     ]);
     $theirRequests = [];
     $yourRequests = [];
     foreach($theirContacts["Requests"] as $key => $value) {
      if($value != $you) {
       $theirRequests[$key] = $value;
      }
     } foreach($yourContacts["Requests"] as $key => $value) {
      if($username != $value) {
       $yourRequests[$key] = $value;
      }
     } if($contactStatus["TheyRequested"] > 0) {
      if($accept == 1) {
       $theirContacts["Contacts"][$you] = [
        "Added" => $this->system->timestamp,
        "List" => "Acquaintances",
        "Notes" => ""
       ];
       $yourContacts["Contacts"][$username] = [
        "Added" => $this->system->timestamp,
        "List" => "Acquaintances",
        "Notes" => ""
       ];
       $theirContacts["Requests"] = $theirRequests;
       $yourContacts["Requests"] = $yourRequests;
       $this->system->SendBulletin([
        "Data" => [
         "From" => $you,
         "Request" => "Accepted"
        ],
        "To" => $username,
        "Type" => "ContactRequest"
       ]);
       $r = $this->system->Element([
        "p", "You added $username to your contacts!",
        ["class" => "CenterText"]
       ]);
      } else {
       $r = $this->system->Element([
        "p", "You have declined $username's contact request.",
        ["class" => "CenterText"]
       ]);
      }
     }
    } elseif($cancel == 1) {
     $theirRequests = [];
     $yourRequests = [];
     foreach($theirContacts["Requests"] as $key => $value) {
      if($value != $you) {
       $theirRequests[$key] = $value;
      }
     }
     $theirContacts["Requests"] = $theirRequests;
     $yourContacts["Requests"] = $yourRequests;
     $r .= $this->system->Element([
      "p", "You canceled your contact request.",
      ["class" => "CenterText"]
     ]);
    } else {
     if(!in_array($you, $theirContacts["Requests"])) {
      array_push($theirContacts["Requests"], $you);
     }
     $this->system->SendBulletin([
      "Data" => [
       "From" => $you,
       "Request" => "PendingApproval"
      ],
      "To" => $username,
      "Type" => "ContactRequest"
     ]);
     $r .= $this->system->Element([
      "p", "Your contact request to $username was sent!",
      ["class" => "CenterText"]
     ]);
    }
    $this->system->Data("Save", ["cms", md5($username), $theirContacts]);
    $this->system->Data("Save", ["cms", md5($you), $yourContacts]);
   }
   $r = ($bulletin == 1) ? "&nbsp;" : $r;
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["Username", "notes"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element(["p", "The Username is missing."]),
    "Header" => "Error"
   ]);
   $username = $data["Username"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($username) && $username != $you) {
    $accessCode = "Accepted";
    $cms = $this->system->Data("Get", ["cms", md5($you)]) ?? [];
    $list = $data["ContactList"];
    $contacts = $cms["Contacts"] ?? [];
    $contacts[$username] = [
     "Added" => $contacts[$username]["Added"],
     "List" => $list,
     "Notes" => $data["Notes"]
    ];
    $cms["Contacts"] = $contacts;
    $this->system->Data("Save", ["cms", md5($you), $cms]);
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "$username's information has been updated."
     ]),
     "Header" => "Contact Updated"
    ]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function Status(array $a) {
   $r = [];
   $them = $a["Them"] ?? "";
   $you = $a["You"] ?? "";
   if(!empty($them) && !empty($you)) {
    $theirContacts = $this->system->Data("Get", ["cms", md5($them)]) ?? [];
    $theirRequests = $theirContacts["Requests"] ?? [];
    $theirContacts = $theirContacts["Contacts"] ?? [];
    $theyHaveYou = 0;
    $theyRequested = 0;
    $yourContacts = $this->system->Data("Get", ["cms", md5($you)]) ?? [];
    $yourRequests = $yourContacts["Requests"] ?? [];
    $yourContacts = $yourContacts["Contacts"] ?? [];
    $youHaveThem = 0;
    $youRequested = 0;
    foreach($theirContacts as $key => $value) {
     if($key == $you) {
      $theyHaveYou++;
     }
    } foreach($yourContacts as $key => $value) {
     if($them == $key) {
      $youHaveThem++;
     }
    } if($theyHaveYou == 0 && $youHaveThem == 0) {
     if(in_array($them, $yourRequests)) {
      $theyRequested++;
     } if(in_array($you, $theirRequests)) {
      $youRequested++;
     }
    }
    $r = [
     "TheyHaveYou" => $theyHaveYou,
     "TheyRequested" => $theyRequested,
     "YouHaveThem" => $youHaveThem,
     "YouRequested" => $youRequested
    ];
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
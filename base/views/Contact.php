<?php
 Class Contact extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Delete(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Username is missing."
   ];
   $_ResponseType = "N/A";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $username = $data["Username"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($username) && $username != $you) {
    $_AccessCode = "Accepted";
    $_Dialog = "";
    $_ResponseType = "Destruct";
    $_theirContacts = $this->core->Data("Get", ["cms", md5($username)]);
    $_yourContacts = $this->core->Data("Get", ["cms", md5($you)]);
    $theirContacts = $_theirContacts["Contacts"] ?? [];
    $theirNewContacts = [];
    $yourContacts = $_yourContacts["Contacts"] ?? [];
    $yourNewContacts = [];
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
    $this->core->Data("Save", ["cms", md5($username), $_theirContacts]);
    $this->core->Data("Save", ["cms", md5($you), $_yourContacts]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "ResponseType" => $responseType
   ]);
  }
  function Options(array $data): string {
   $_Dialog = [
    "Body" => "The Username is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["UN"]);
   $username = $data["UN"];
   $y = $this->you;
   if(!empty($username)) {
    $username = base64_decode($username);
    $card = base64_encode("Profile:Home");
    $contacts = $this->core->Data("Get", [
     "cms",
     md5($y["Login"]["Username"])
    ]);
    $contacts = $contacts["Contacts"] ?? [];
    $contact = $contacts[$username];
    $t = $this->core->Member($username);
    $profilePicture = $t["Personal"]["ProfilePicture"] ?? "";
    $profilePicture = (!empty($profilePicture)) ? $this->core->efs.base64_decode($profilePicture) : "[Media:LOGO]";
    $profilePicture = $this->PlainText([
     "Data" => $profilePicture,
     "Display" => 1
    ]);
    $_View = [
     "ChangeData" => [
      "[Contact.Card]" => base64_encode("CARD=1&v=$card&UN=".$data["UN"]),
      "[Contact.DisplayName]" => $t["Personal"]["DisplayName"],
      "[Contact.ID]" => md5($username),
      "[Contact.List]" => $contact["List"],
      "[Contact.Notes]" => base64_encode($contact["Notes"]),
      "[Contact.ProfilePicture]" => $profilePicture,
      "[Contact.Update]" => base64_encode("v=".base64_encode("Contact:Save")),
      "[Contact.Username]" => $username
     ],
     "ExtensionID" => "297c6906ec2f4cb2013789358c5ea77b"
    ];
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Requests(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Username is missing."
   ];
   $_ResponseType = "N/A";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "Username",
    "accept",
    "bulletin",
    "decline"
   ]);
   $username = $data["Username"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($username)) {
    $_AccessCode = "Accepted";
    $_Dialog = "";
    $_ResponseType = "ReplaceContent";
    $_View = $this->core->Element([
     "h3", "Success", ["class" => "CenterText UpperCase"]
    ]);
    $accept = $data["accept"] ?? 0;
    $bulletin = $data["bulletin"] ?? 0;
    $contactStatus = $this->view(base64_encode("Contact:Status"), [
     "Them" => $username,
     "You" => $you
    ]);
    $contactStatus = $this->core->RenderView($contactStatus);
    $decline = $data["decline"] ?? 0;
    $theirContacts = $this->core->Data("Get", [
     "cms",
     md5($username)
    ]);
    $yourContacts = $this->core->Data("Get", ["cms", md5($you)]);
    $cancel = (in_array($you, $theirContacts["Requests"])) ? 1 : 0;
    $cancel = ($cancel == 1 || $contactStatus["YouRequested"] > 0) ? 1 : 0;
    if($accept == 1 || $decline == 1) {
     $_View = $this->core->Element([
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
        "Added" => $this->core->timestamp,
        "List" => "Acquaintances",
        "Notes" => ""
       ];
       $yourContacts["Contacts"][$username] = [
        "Added" => $this->core->timestamp,
        "List" => "Acquaintances",
        "Notes" => ""
       ];
       $theirContacts["Requests"] = $theirRequests;
       $yourContacts["Requests"] = $yourRequests;
       $this->core->SendBulletin([
        "Data" => [
         "From" => $you,
         "Request" => "Accepted"
        ],
        "To" => $username,
        "Type" => "ContactRequest"
       ]);
       $_View = $this->core->Element([
        "p", "You added $username to your contacts!",
        ["class" => "CenterText"]
       ]);
      } else {
       $_View = $this->core->Element([
        "p", "You have declined $username's contact request.",
        ["class" => "CenterText"]
       ]);
      }
     }
    } elseif($cancel == 1) {
     $theirRequests = [];
     foreach($theirContacts["Requests"] as $key => $value) {
      if($value != $you) {
       $theirRequests[$key] = $value;
      }
     }
     $theirContacts["Requests"] = $theirRequests;
     $_View .= $this->core->Element([
      "p", "You canceled your contact request.",
      ["class" => "CenterText"]
     ]);
    } else {
     if(!in_array($you, $theirContacts["Requests"])) {
      array_push($theirContacts["Requests"], $you);
     }
     $this->core->SendBulletin([
      "Data" => [
       "From" => $you,
       "Request" => "PendingApproval"
      ],
      "To" => $username,
      "Type" => "ContactRequest"
     ]);
     $_View .= $this->core->Element([
      "p", "Your contact request to $username was sent!",
      ["class" => "CenterText"]
     ]);
    }
    $this->core->Data("Save", ["cms", md5($username), $theirContacts]);
    $this->core->Data("Save", ["cms", md5($you), $yourContacts]);
    $_View = [
     "ChangeData" => [],
     "ExtensionID" => $this->core->AESencrypt($_VIew)
    ];
   }
   $_View = ($bulletin == 1) ? "" : $_View;
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_Dialog = [
    "Body" => "The Username is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["Username", "notes"]);
   $username = $data["Username"];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($username) && $username != $you) {
    $cms = $this->core->Data("Get", ["cms", md5($you)]);
    $list = $data["ContactList"];
    $contacts = $cms["Contacts"] ?? [];
    $contacts[$username] = [
     "Added" => $contacts[$username]["Added"],
     "List" => $list,
     "Notes" => $data["Notes"]
    ];
    $cms["Contacts"] = $contacts;
    $this->core->Data("Save", ["cms", md5($you), $cms]);
    $_Dialog = [
     "Body" => "$username's information has been updated.",
     "Header" => "Contact Updated"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function Status(array $data): string {
   $_Dialog = [
    "Body" => "One or both Usernames are missing."
   ];
   $_View = "";
   $them = $data["Them"] ?? "";
   $you = $data["You"] ?? "";
   if(!empty($them) && !empty($you)) {
    $_Dialog = "";
    $theirContacts = $this->core->Data("Get", ["cms", md5($them)]);
    $theirRequests = $theirContacts["Requests"] ?? [];
    $theirContacts = $theirContacts["Contacts"] ?? [];
    $theyHaveYou = 0;
    $theyRequested = 0;
    $yourContacts = $this->core->Data("Get", ["cms", md5($you)]);
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
    $_View = [
     "TheyHaveYou" => $theyHaveYou,
     "TheyRequested" => $theyRequested,
     "YouHaveThem" => $youHaveThem,
     "YouRequested" => $youRequested
    ];
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
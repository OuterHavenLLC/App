<?php
 Class Vote extends OH {
  function __construct() {
   parent::__construct();
   $this->NoID = $this->core->Element([
    "div", "The Vote Identifier is missing.", ["class" => "CenterText InnerMargin"]
   ]);
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Containers(array $data): string {
   $_View = $this->NoID;
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $refresh = $data["Refresh"] ?? 0;
   $type = $data["Type"] ?? 1;
   $you = $this->you["Login"]["Username"];
   $minimalDesign = $y["Personal"]["MinimalDesign"] ?? 0;
   if($minimalDesign == 1) {
    $_View = $this->core->Element([
     "div", "&nbsp;", ["class" => "NONAME"]
    ]);
   } elseif(!empty($id)) {
    $_VoteDown = 0;
    $_VoteUp = 0;
    $_Votes = $this->core->Data("Get", ["votes", $id]);
    $_Votes[$you] = $_Votes[$you] ?? "";
    foreach($_Votes as $member => $vote) {
     if($vote == "Down") {
      $_VoteDown++;
     } elseif($vote == "Up") {
      $_VoteUp++;
     }
    }
    $class = "Frosted Pill VoteFor$id";
    $class .= "";
    $class .= ($type == 2) ? "" : $class;
    $class .= ($type == 3) ? " Desktop66" : $class;
    $class .= ($type == 4) ? " Medium" : $class;
    $isAnon = ($this->core->ID == $you) ? 1 : 0;
    $retract = $this->core->AESencrypt("v=".base64_encode("Vote:Retract")."&ID=$id&Type=$type");
    $save = "v=".base64_encode("Vote:Save")."&ID=$id&Type=$type&Vote=";
    $down = ($_Votes[$you] == "Down") ? $this->core->Element([
     "button", "Down", [
      "class" => "Selected UpdateContent v2 v2w",
      "data-container" => ".VoteFor$id",
      "data-encryption" => "AES",
      "data-view" => $retract
     ]
    ]) : $this->core->Element([
     "button", "Down", [
      "class" => "UpdateContent v2 v2w",
      "data-container" => ".VoteFor$id",
      "data-encryption" => "AES",
      "data-view" => $this->core->AESencrypt($save."Down")
     ]
    ]);
    $down = ($isAnon == 0) ? $down : "&nbsp;";
    $up = ($_Votes[$you] == "Up") ? $this->core->Element([
     "button", "Up", [
      "class" => "Selected UpdateContent v2 v2w",
      "data-container" => ".VoteFor$id",
      "data-encryption" => "AES",
      "data-view" => $retract
     ]
    ]) : $this->core->Element([
     "button", "Up", [
      "class" => "UpdateContent v2 v2w",
      "data-container" => ".VoteFor$id",
      "data-encryption" => "AES",
      "data-view" => $this->core->AESencrypt($save."Up")
     ]
    ]);
    $up = ($isAnon == 0) ? $up : "&nbsp;";
    $votes = $_VoteUp - $_VoteDown;
    $_View = $this->core->Element([
     "div", $up, [
      "class" => "Desktop33"
     ]
    ]).$this->core->Element([
     "div", $this->core->Element([
      "div", $this->core->ShortNumber($votes),
      [
       "class" => "CenterText InnerMargin"
      ]
     ]), [
      "class" => "Desktop33"
     ]
    ]).$this->core->Element([
     "div", $down, [
      "class" => "Desktop33"
     ]
    ]);
    $_View = ($refresh == 0) ? $this->core->Element([
     "div", $_View, ["class" => $class]
    ]) : $_View;
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function Retract(array $data): string {
   $_View = $this->NoID;
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $type = $data["Type"] ?? "";
   $you = $this->you["Login"]["Username"];
   if(!empty($id)) {
    $newVotes = [];
    $votes = $this->core->Data("Get", ["votes", $id]);
    foreach($votes as $member => $vote) {
     if($member != $you) {
      $newVotes[$member] = $vote;
     }
    }
    $this->core->Data("Save", ["votes", $id, $newVotes]);
    $_View = $this->view(base64_encode("Vote:Containers"), ["Data" => [
     "ID" => $id,
     "Refresh" => 1,
     "Type" => $type
    ]]);
    $_View = $this->core->RenderView($_View);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function Save(array $data): string {
   $_View = $this->NoID;
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $type = $data["Type"] ?? "";
   $vote = $data["Vote"] ?? "";
   $you = $this->you["Login"]["Username"];
   if(!empty($id)) {
    $votes = $this->core->Data("Get", ["votes", $id]);
    $purge = $votes["Purge"] ?? 0;
    $votes[$you] = $vote;
    if($purge != 0) {
     $votes["Purge"] = $purge;
    }
    $this->core->Data("Save", ["votes", $id, $votes]);
    $_View = $this->view(base64_encode("Vote:Containers"), ["Data" => [
     "ID" => $id,
     "Refresh" => 1,
     "Type" => $type
    ]]);
    $_View = $this->core->RenderView($_View);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function ViewCount(array $data): string {
   $_View = $this->NoID;
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $type = $data["Type"] ?? 1;
   $you = $this->you["Login"]["Username"];
   if(!empty($id)) {
    $_VoteDown = 0;
    $_VoteUp = 0;
    $_Votes = $this->core->Data("Get", ["votes", $id]);
    $_Votes[$you] = $_Votes[$you] ?? "";
    foreach($_Votes as $member => $vote) {
     if($vote == "Down") {
      $_VoteDown++;
     } elseif($vote == "Up") {
      $_VoteUp++;
     }
    }
    $class = "Frosted Pill VoteFor$id";
    $class .= "";
    $class .= ($type == 2) ? "" : $class;
    $class .= ($type == 3) ? " Desktop66" : $class;
    $class .= ($type == 4) ? " Medium" : $class;
    $votes = $_VoteUp - $_VoteDown;
    $_View = $this->core->Element([
     "div", $this->core->Element(["div", "&nbsp;", [
      "class" => "Desktop33"
     ]]).$this->core->Element(["div", $this->core->Element([
      "div", $this->core->ShortNumber($votes),
      ["class" => "CenterText InnerMargin"]
     ]), [
      "class" => "Desktop33"
     ]]).$this->core->Element(["div", "&nbsp;", [
      "class" => "Desktop33"
     ]]), ["class" => $class]
    ]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
    ]
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class Vote extends GW {
  function __construct() {
   parent::__construct();
   $this->NoID = $this->system->Element([
    "div", "Missing Vote ID", ["class" => "CenterText InnerMargin"]
   ]);
   $this->you = $this->system->Member($this->system->Username());
  }
  function Containers(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->NoID;
   $refresh = $data["Refresh"] ?? 0;
   $type = $data["Type"] ?? 1;
   $you = $this->you["Login"]["Username"];
   if(!empty($id)) {
    $_VoteDown = 0;
    $_VoteUp = 0;
    $_Votes = $this->system->Data("Get", ["votes", $id]) ?? [];
    $_Votes[$you] = $_Votes[$you] ?? "";
    foreach($_Votes as $member => $vote) {
     if($vote == "Down") {
      $_VoteDown++;
     } elseif($vote == "Up") {
      $_VoteUp++;
     }
    }
    $class = "Bar Vote VoteFor$id";
    $class .= ($type == 1) ? "" : "";
    $class .= ($type == 2) ? "" : $class;
    $class .= ($type == 3) ? " Desktop66" : $class;
    $class .= ($type == 4) ? "" : $class;
    $retract = "v=".base64_encode("Vote:Retract")."&ID=$id&Type=$type";
    $save = "v=".base64_encode("Vote:Save")."&ID=$id&Type=$type&Vote=";
    $down = ($_Votes[$you] == "Down") ? $this->system->Element([
     "button", "Down", [
      "class" => "Selected VoteDown v2 v2w",
      "onclick" => "xLoad('.VoteFor$id', '$retract');"
     ]
    ]) : $this->system->Element([
     "button", "Down", [
      "class" => "VoteDown v2 v2w",
      "onclick" => "xLoad('.VoteFor$id', '".$save."Down');"
     ]
    ]);
    $up = ($_Votes[$you] == "Up") ? $this->system->Element([
     "button", "Up", [
      "class" => "Selected VoteUp v2 v2w",
      "onclick" => "xLoad('.VoteFor$id', '$retract');"
     ]
    ]) : $this->system->Element([
     "button", "Up", [
      "class" => "VoteUp v2 v2w",
      "onclick" => "xLoad('.VoteFor$id', '".$save."Up');"
     ]
    ]);
    $votes = $_VoteUp - $_VoteDown;
    $r = $this->system->Element(["div", $up, [
     "class" => "Desktop33"
    ]]).$this->system->Element(["div", $this->system->Element([
     "div", $this->system->ShortNumber($votes),
     ["class" => "CenterText InnerMargin"]
    ]), [
     "class" => "Desktop33"
    ]]).$this->system->Element(["div", $down, [
     "class" => "Desktop33"
    ]]);
    $r = ($refresh == 0) ? $this->system->Element([
     "div", $r, ["class" => $class]
    ]) : $r;
   }
   return $r;
  }
  function Retract(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->NoID;
   $type = $data["Type"] ?? "";
   $you = $this->you["Login"]["Username"];
   if(!empty($id)) {
    $newVotes = [];
    $votes = $this->system->Data("Get", ["votes", $id]) ?? [];
    foreach($votes as $member => $vote) {
     if($member != $you) {
      $newVotes[$member] = $vote;
     }
    }
    $this->system->Data("Save", ["votes", $id, $newVotes]);
    $r = $this->view(base64_encode("Vote:Containers"), ["Data" => [
     "ID" => $id,
     "Refresh" => 1,
     "Type" => $type
    ]]);
   }
   return $r;
  }
  function Save(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->NoID;
   $type = $data["Type"] ?? "";
   $vote = $data["Vote"] ?? "";
   $you = $this->you["Login"]["Username"];
   if(!empty($id)) {
    $votes = $this->system->Data("Get", ["votes", $id]) ?? [];
    $votes[$you] = $vote;
    $this->system->Data("Save", ["votes", $id, $votes]);
    $r = $this->view(base64_encode("Vote:Containers"), ["Data" => [
     "ID" => $id,
     "Refresh" => 1,
     "Type" => $type
    ]]);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
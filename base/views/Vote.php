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
    $class = "Vote VoteFor$id";
    $class .= ($type == 1) ? "" : "";
    $class .= ($type == 2) ? "" : $class;
    $class .= ($type == 3) ? " Desktop66" : $class;
    $class .= ($type == 4) ? " v2 v2w" : $class;
    $processor = "v=".base64_encode("Vote:Save")."&ID=$id&Type=$type&Vote=";
    $voteDown = ($_Votes[$you] == "Down") ? "BBB " : "";
    $voteUp = ($_Votes[$you] == "Up") ? "BBB " : "";
    $votes = $_VoteUp - $_VoteDown;
    $r = $this->system->Change([[
     "[Vote.Down]" => $voteDown,
     "[Vote.Down.Processor]" => $processor."Down",
     "[Vote.ID]" => $id,
     "[Vote.Up]" => $voteUp,
     "[Vote.Up.Processor]" => $processor."Up",
     "[Vote.Total]" => $this->system->ShortNumber($votes)
    ], $this->system->Page("39a550decb7f3f764445b33e847a7042")]);
    $r = ($refresh == 0) ? $this->system->Element([
     "div", $r, ["class" => $class]
    ]) : $r;
   }
   return $r;
  }
  function Save(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->NoID;
   $type = $data["Type"] ?? "";
   $vote = $data["Vote"] ?? "";
   if(!empty($id)) {
    // RECORD OR REMOVE VOTE
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
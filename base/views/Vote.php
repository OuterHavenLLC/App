<?php
 Class Vote extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Containers(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->system->Element([
    "p", "Missing Vote ID.", ["class" => "CenterText"]
   ]);
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
    $class = ($type == 1) ? "" : "";
    $class = ($type == 2) ? "" : $class;
    $class = ($type == 3) ? "Desktop66 " : $class;
    $class = ($type == 4) ? "" : $class;
    $voteDown = ($_Votes[$you] == "Down") ? "BBB " : "";
    $voteUp = ($_Votes[$you] == "Up") ? "BBB " : "";
    $votes = $_VoteUp - $_VoteDown;
    $r = $this->system->Change([[
     "[Vote.Down]" => $voteDown,
     "[Vote.Down.Processor]" => "",
     "[Vote.ID]" => $id,
     "[Vote.Up]" => $voteUp,
     "[Vote.Up.Processor]" => "",
     "[Vote.Total]" => $this->system->ShortNumber($votes)
    ], $this->system->Page("39a550decb7f3f764445b33e847a7042")]);
    $r = ($refresh == 0) ? $this->system->Element(["div", $r, [
     "class" => $class."Vote VoteFor$id"
    ]]) : $r;
   }
   return $r;
  }
  function Save(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->system->Element([
    "p", "Missing Vote ID.", ["class" => "CenterText"]
   ]);
   $type = $data["Type"] ?? "";
   // RECORD OR REMOVE VOTE
   // REFRESH INNER CONTAINER
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
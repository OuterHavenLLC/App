<?php
 Class Feedback extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home(array $a) {
   $button = "";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Feedback Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   if(!empty($id)) {
    $feedback = $this->system->Data("Get", ["knowledge", $id]) ?? [];
    $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
    $title = $feedback["Subject"] ?? "New Feedback";
    $r = $this->system->Change([[
     "[Feedback.ID]" => $id,
     "[Feedback.ParaphrasedQuestion]" => $paraphrasedQuestion,
     "[Feedback.Stream]" => "v=".base64_encode("Feedback:Stream")."&ID=$id",
     "[Feedback.Title]" => $title
    ], $this->system->Page("56718d75fb9ac2092c667697083ec73f")]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function Save(array $a) {
   $r = $this->system->Element([
    "p", "Saves the response to the Feedback thread, among other admin-level preferences, emails the user to notify them of our response."
   ]);
   return $r;
  }
  function Stream(array $a) {
   $r = $this->system->Element(["div", "Test", [
    "class" => "MSGt"
   ]]).$this->system->Element(["div", "Response", [
    "class" => "MSGy"
   ]]);
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
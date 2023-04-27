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
    $title = $paraphrasedQuestion ?? $title;
    $r = $this->system->Change([[
     "[Feedback.ID]" => $id,
     "[Feedback.Options.Priority]" => $this->system->Select("Priority", "req v2w"),
     "[Feedback.Options.Resolved]" => $this->system->Select("Resolved", "req v2w"),
     "[Feedback.Stream]" => "v=".base64_encode("Feedback:Stream")."&ID=$id",
     "[Feedback.ParaphrasedQuestion]" => $paraphrasedQuestion,
     "[Feedback.Title]" => $title,
    ], $this->system->Page("56718d75fb9ac2092c667697083ec73f")]);
   }
   return $this->system->Card([
    "Front" => $r,
    "FrontButton" => $button
   ]);
  }
  function PublicHome(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->system->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Feedback Identifier is missing."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   if(!empty($id)) {
    # User-Facing Home/Editor
    $feedback = $this->system->Data("Get", ["knowledge", $id]) ?? [];
    $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
    $title = $feedback["Subject"] ?? "New Feedback";
    $title = $paraphrasedQuestion ?? $title;
    $r = $this->system->Change([[
     "[Feedback.ID]" => $id,
     "[Feedback.Options.Resolved]" => $this->system->Select("Resolved", "req v2w"),
     "[Feedback.Stream]" => "v=".base64_encode("Feedback:Stream")."&ID=$id",
     "[Feedback.ParaphrasedQuestion]" => $paraphrasedQuestion,
     "[Feedback.Title]" => $title,
    ], $this->system->Page("XXXX")]);
   }
   return $r;
  }
  function Save(array $a) {
   $accessCode = "Denied";
   #$accessCode = "Accepted";
   $r = $this->system->Element([
    "p", "Saves the response to the Feedback thread, among other admin-level preferences, emails the user to notify them of our response."
   ]);
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
  }
  function SaveUserResponse(array $a) {
   $accessCode = "Denied";
   #$accessCode = "Accepted";
   $r = $this->system->Element([
    "p", "Saves the user response to the thread."
   ]);
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog"
   ]);
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
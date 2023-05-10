<?php
 Class Feedback extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Home(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $pub = $data["pub"] ?? 0;
   if($pub == 0) {
    $button = "";
    $r = $this->system->Change([[
     "[Error.Header]" => "Not Found",
     "[Error.Message]" => "The Feedback Identifier is missing."
    ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
    if(!empty($id)) {
     $button = $this->system->Element(["button", "Respond", [
      "class" => "CardButton SendData",
      "data-form" => ".FeedbackEditor$id",
      "data-processor" => base64_encode("v=".base64_encode("Feedback:SaveResponse"))
     ]]);
     $feedback = $this->system->Data("Get", ["knowledge", $id]) ?? [];
     $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
     $title = $feedback["Subject"] ?? "New Feedback";
     if($feedback["UseParaphrasedQuestion"] == 1) {
      $title = $feedback["ParaphrasedQuestion"];
     }
     $r = $this->system->Change([[
      "[Feedback.ID]" => $id,
      "[Feedback.Options.Priority]" => $this->system->Select("Priority", "req v2w", $feedback["Priority"]),
      "[Feedback.Options.Resolved]" => $this->system->Select("Resolved", "req v2w", $feedback["Resolved"]),
      "[Feedback.Options.UseParaphrasedQuestion]" => $this->system->Select("UseParaphrasedQuestion", "req v2w", $feedback["UseParaphrasedQuestion"]),
      "[Feedback.Stream]" => "v=".base64_encode("Feedback:Stream")."&ID=$id",
      "[Feedback.ParaphrasedQuestion]" => $paraphrasedQuestion,
      "[Feedback.Title]" => $title
     ], $this->system->Page("56718d75fb9ac2092c667697083ec73f")]);
    }
    $r = $this->system->Card([
     "Front" => $r,
     "FrontButton" => $button
    ]);
   } elseif($pub == 1) {
    $r = $this->system->Change([[
     "[Error.Back]" =. "",
     "[Error.Header]" => "Let's Talk!",
     "[Error.Message]" => "We want to hear from you, send us your feedback."
    ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
    $r .= $this->system->Element([
     "div", "&nbsp;", ["class" => "Desktop33 MobilfHide"]
    ]).$this->system->Element([
     "div", $this->system->Element(["button", "Send Feedback", [
      "class" => "BBB dB2O v2 v2w",
      "data-type" => base64_encode("v=".base64_encode("Feedback:NewThread"))
     ]]), ["class" => "Desktop33 MobilfFull"]
    ]).$this->system->Element([
     "div", "&nbsp;", ["class" => "Desktop33 MobilfHide"]
    ]);
    if(!empty($id)) {
     $feedback = $this->system->Data("Get", ["knowledge", $id]) ?? [];
     $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
     $title = $feedback["Subject"] ?? "New Feedback";
     if($feedback["UseParaphrasedQuestion"] == 1) {
      $title = $feedback["ParaphrasedQuestion"];
     }
     $r = $this->system->Change([[
      "[Feedback.ID]" => $id,
      "[Feedback.Options.Priority]" => $this->system->Select("Priority", "req v2w", $feedback["Priority"]),
      "[Feedback.Options.Resolved]" => $this->system->Select("Resolved", "req v2w", $feedback["Resolved"]),
      "[Feedback.Processor]" => base64_encode("v=".base64_encode("Feedback:SaveResponse")),
      "[Feedback.Stream]" => "v=".base64_encode("Feedback:Stream")."&ID=$id",
      "[Feedback.Title]" => $title
     ], $this->system->Page("599e260591d6dca59a8e0a52f5bd64be")]);
    }
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
   }
   return $r;
  }
  function NewThread(array $a) {
   $id = md5("Feedback");
   $y = $this->you;
   return $this->system->Card([
    "Front" => $this->system->Change([[
     "[Contact.Body]" => $this->system->WYSIWYG([
      "Body" => NULL,
      "adm" => 1,
      "opt" => [
       "id" => "CompanyFeedbackBody",
       "class" => "req",
       "name" => "MSG",
       "placeholder" => "What's on your mind?",
       "rows" => 20
      ]
     ]),
     "[Contact.ID]" => $id,
     "[Contact.Options.Index]" => $this->system->Select("Index", "req v2w"),
     "[Contact.Options.Priority]" => $this->system->Select("Priority", "req v2w"),
     "[Contact.Options.SendOccasionalEmails]" => $this->system->Select("SOE", "req v2w"),
     "[Member.Email]" => $y["Personal"]["Email"],
     "[Member.Name]" => $y["Personal"]["FirstName"]
    ], $this->system->Page("2b5ca0270981e891ce01dba62ef32fe4")]),
    "FrontButton" => $this->system->Element(["button", "Send", [
     "class" => "CardButton SendData",
     "data-form" => ".ContactForm$id",
     "data-processor" => base64_encode("v=".base64_encode("Feedback:Save"))
    ]])
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "Email",
    "Index",
    "MSG",
    "Name",
    "Phone",
    "SOE",
    "Subject",
    "Priority"
   ]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "An internal error has ocurred."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["MSG"])) {
    $accessCode = "Accepted";
    $now = $this->system->timestamp;
    if($data["SOE"] == 1) {
     $contacts  = $this->system->Data("Get", [
      "x",
      md5("ContactList")
     ]) ?? [];
     $contacts[$data["Email"]] = [
      "Email" => $data["Email"],
      "Name" => $data["Name"],
      "Phone" => $data["Phone"],
      "SendOccasionalEmails" => $data["SOE"],
      "UN" => $you,
      "Updated" => $now
     ];
     $this->system->Data("Save", ["x", md5("ContactList"), $contacts]);
    }
    $feedback = [
     "AllowIndexing" => $data["Index"],
     "Email" => $data["Email"],
     "Name" => $data["Name"],
     "ParaphrasedQuestion" => "",
     "Phone" => $data["Phone"],
     "Priority" => $data["Priority"],
     "Resolved" => 0,
     "Subject" => $data["Subject"],
     "Thread" => [],
     "Username" => $you,
     "UseParaphrasedQuestion" => 0
    ];
    array_push($feedback["Thread"], [
     "Body" => $this->system->PlainText([
      "Data" => $data["MSG"],
      "Encode" => 1,
      "HTMLEncode" => 1
     ]),
     "From" => $you,
     "Sent" => $now
    ]);
    $this->system->Data("Save", [
     "knowledge",
     md5("KnowledgeBase-$now-".uniqid()),
     $feedback
    ]);
    $this->system->Statistic("FS");
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "We will be in touch as soon as possible!"
     ]),
     "Header" => "Thank you"
    ]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "Dialog",
    "Success" => "CloseCard"
   ]);
  }
  function SaveResponse(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "ID",
    "Message",
    "ParaphrasedQuestion",
    "Priority",
    "Resolved",
    "UseParaphrasedQuestion"
   ]);
   $id = $data["ID"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Feedback Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Message"]) && !empty($id)) {
    $accessCode = "Accepted";
    $feedback = $this->system->Data("Get", ["knowledge", $id]) ?? [];
    if(!empty($data["ParaphrasedQuestion"])) {
     $feedback["ParaphrasedQuestion"] = $data["ParaphrasedQuestion"];
    } if(!empty($data["Priority"])) {
     $feedback["Priority"] = $data["Priority"];
    } if(!empty($data["Resolved"])) {
     $feedback["Resolved"] = $data["Resolved"];
    } if(!empty($data["UseParaphrasedQuestion"])) {
     $feedback["UseParaphrasedQuestion"] = $data["UseParaphrasedQuestion"];
    }
    array_push($feedback["Thread"], [
     "Body" => $this->system->PlainText([
      "Data" => $data["Message"],
      "Encode" => 1,
      "HTMLEncode" => 1
     ]),
     "From" => $you,
     "Sent" => $this->system->timestamp
    ]);
    if($feedback["Username"] != $you) {
     $this->system->SendEmail([
      "Message" => $this->system->Change([[
       "[Email.Header]" => "{email_header}",
       "[Email.Message]" => $this->system->PlainText([
        "Data" => $data["Message"],
        "Display" => 1
       ]),
       "[Email.Name]" => $feedback["Name"],
       "[Email.Link]" => $this->system->base."/feedback/$id"
      ], $this->system->Page("dc901043662c5e71b5a707af782fdbc1")]),
      "Title" => "Re: ".$feedback["Subject"],
      "To" => $feedback["Email"]
     ]);
    }
    $this->system->Data("Save", ["knowledge", $id, $feedback]);
    $r = $this->system->Dialog([
     "Body" => "Your response has been sent.",
     "Header" => "Done"
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
  function Stream(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->system->Page("2ce9b2d2a7f5394df6a71df2f0400873");
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $feedback = $this->system->Data("Get", ["knowledge", $id]) ?? [];
    $r = "";
    $thread = $feedback["Thread"] ?? [];
    $tpl = $this->system->Page("1f4b13bf6e6471a7f5f9743afffeecf9");
    foreach($thread as $key => $message) {
     $class = ($message["From"] != $you) ? "MSGt" : "MSGy";
     $r .= $this->system->Change([[
      "[Message.Attachments]" => "",
      "[Message.Class]" => $class,
      "[Message.MSG]" => $this->system->PlainText([
       "Data" => $message["Body"],
       "Decode" => 1,
       "HTMLDecode" => 1
      ]),
      "[Message.Sent]" => $this->system->TimeAgo($message["Sent"])
     ], $tpl]);
    }
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
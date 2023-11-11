<?php
 Class Feedback extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $pub = $data["pub"] ?? 0;
   if($pub == 0) {
    $r = [
     "Body" => "The Feedback Identifier is missing."
    ];
    if(!empty($id)) {
     $accessCode = "Accepted";
     $action = $this->core->Element(["button", "Respond", [
      "class" => "CardButton SendData",
      "data-form" => ".FeedbackEditor$id",
      "data-processor" => base64_encode("v=".base64_encode("Feedback:SaveResponse"))
     ]]);
     $feedback = $this->core->Data("Get", ["knowledge", $id]) ?? [];
     $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
     $title = $feedback["Subject"] ?? "New Feedback";
     if($feedback["UseParaphrasedQuestion"] == 1) {
      $title = $feedback["ParaphrasedQuestion"];
     }
     $r = $this->core->Change([[
      "[Feedback.ID]" => $id,
      "[Feedback.ParaphrasedQuestion]" => base64_encode($paraphrasedQuestion),
      "[Feedback.Priority]" => $feedback["Priority"],
      "[Feedback.Resolved]" => $feedback["Resolved"],
      "[Feedback.Stream]" => base64_encode("v=".base64_encode("Feedback:Stream")."&ID=$id"),
      "[Feedback.Title]" => $title,
      "[Feedback.UseParaphrasedQuestion]" => $feedback["UseParaphrasedQuestion"]
     ], $this->core->Extension("56718d75fb9ac2092c667697083ec73f")]);
    }
    $r = [
     "Action" => $action,
     "Front" => $r
    ];
   } elseif($pub == 1) {
    $accessode = "Accepted";
    $r = $this->core->Change([[
     "[Error.Back]" => "",
     "[Error.Header]" => "Let's Talk!",
     "[Error.Message]" => "We want to hear from you, send us your feedback."
    ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
    $r .= $this->core->Element([
     "div", "&nbsp;", ["class" => "Desktop33 MobilfHide"]
    ]).$this->core->Element([
     "div", $this->core->Element(["button", "Send Feedback", [
      "class" => "BBB OpenDialog v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("Feedback:NewThread"))
     ]]), ["class" => "Desktop33 MobilfFull"]
    ]).$this->core->Element([
     "div", "&nbsp;", ["class" => "Desktop33 MobilfHide"]
    ]);
    if(!empty($id)) {
     $feedback = $this->core->Data("Get", ["knowledge", $id]) ?? [];
     $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
     $title = $feedback["Subject"] ?? "New Feedback";
     if($feedback["UseParaphrasedQuestion"] == 1) {
      $title = $feedback["ParaphrasedQuestion"];
     }
     $r = $this->core->Change([[
      "[Feedback.ID]" => $id,
      "[Feedback.Priority]" => $feedback["Priority"],
      "[Feedback.Resolved]" => $feedback["Resolved"],
      "[Feedback.Processor]" => base64_encode("v=".base64_encode("Feedback:SaveResponse")),
      "[Feedback.Stream]" => base64_encode("v=".base64_encode("Feedback:Stream")."&ID=$id"),
      "[Feedback.Title]" => $title
     ], $this->core->Extension("599e260591d6dca59a8e0a52f5bd64be")]);
    }
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
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
  function NewThread(array $a) {
   $accessCode = "Accepted";
   $id = md5("Feedback");
   $y = $this->you;
   $r = [
    "Action" => $this->core->Element(["button", "Send", [
     "class" => "CardButton SendData",
     "data-form" => ".ContactForm$id",
     "data-processor" => base64_encode("v=".base64_encode("Feedback:Save"))
    ]]),
    "Front" => $this->core->Change([[
     "[Feedback.Email]" => base64_encode($y["Personal"]["Email"]),
     "[Feedback.ID]" => $id,
     "[Feedback.Name]" => base64_encode($y["Personal"]["FirstName"])
    ], $this->core->Extension("2b5ca0270981e891ce01dba62ef32fe4")])
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "Email",
    "Index",
    "MSG",
    "Name",
    "Phone",
    "SOE",
    "Subject",
    "Priority"
   ]);
   $r = [
    "Body" => "An internal error has ocurred."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["MSG"])) {
    $accessCode = "Accepted";
    $now = $this->core->timestamp;
    if($data["SOE"] == 1) {
     $contacts  = $this->core->Data("Get", [
      "app",
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
     $this->core->Data("Save", ["app", md5("ContactList"), $contacts]);
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
     "Body" => $this->core->PlainText([
      "Data" => $data["Message"],
      "Encode" => 1,
      "HTMLEncode" => 1
     ]),
     "From" => $you,
     "Sent" => $now
    ]);
    $this->core->Data("Save", [
     "knowledge",
     md5("KnowledgeBase-$now-".uniqid()),
     $feedback
    ]);
    $this->core->Statistic("FS");
    $r = [
     "Body" => "We will be in touch as soon as possible!",
     "Header" => "Thank you"
    ];
   }
   return $this->core->JSONResponse([
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
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "ID",
    "Message",
    "ParaphrasedQuestion",
    "Priority",
    "Resolved",
    "UseParaphrasedQuestion"
   ]);
   $id = $data["ID"];
   $r = [
    "Body" => "The Feedback Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Message"]) && !empty($id)) {
    $accessCode = "Accepted";
    $feedback = $this->core->Data("Get", ["knowledge", $id]) ?? [];
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
     "Body" => $this->core->PlainText([
      "Data" => $data["Message"],
      "Encode" => 1,
      "HTMLEncode" => 1
     ]),
     "From" => $you,
     "Sent" => $this->core->timestamp
    ]);
    if($feedback["Username"] != $you) {
     $this->core->SendEmail([
      "Message" => $this->core->Change([[
       "[Email.Header]" => "{email_header}",
       "[Email.Message]" => $this->core->PlainText([
        "Data" => $data["Message"],
        "Display" => 1
       ]),
       "[Email.Name]" => $feedback["Name"],
       "[Email.Link]" => $this->core->base."/feedback/$id"
      ], $this->core->Extension("dc901043662c5e71b5a707af782fdbc1")]),
      "Title" => "Re: ".$feedback["Subject"],
      "To" => $feedback["Email"]
     ]);
    }
    $this->core->Data("Save", ["knowledge", $id, $feedback]);
    $r = [
     "Body" => "Your response has been sent.",
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
  function Stream(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Scrollable" => $this->core->Extension("2ce9b2d2a7f5394df6a71df2f0400873")
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $feedback = $this->core->Data("Get", ["knowledge", $id]) ?? [];
    $r = "";
    $thread = $feedback["Thread"] ?? [];
    $tpl = $this->core->Extension("1f4b13bf6e6471a7f5f9743afffeecf9");
    foreach($thread as $key => $message) {
     $class = ($message["From"] != $you) ? "MSGt" : "MSGy";
     $r .= $this->core->Change([[
      "[Message.Attachments]" => "",
      "[Message.Class]" => $class,
      "[Message.MSG]" => $this->core->PlainText([
       "Data" => $message["Body"],
       "Decode" => 1,
       "HTMLDecode" => 1
      ]),
      "[Message.Sent]" => $this->core->TimeAgo($message["Sent"])
     ], $tpl]);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
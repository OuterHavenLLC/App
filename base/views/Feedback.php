<?php
 Class Feedback extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Feedback Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $public = $data["Public"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($public == 0) {
    if(!empty($id)) {
     $_Dialog = "";
     $feedback = $this->core->Data("Get", ["feedback", $id]);
     $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
     $title = $feedback["Subject"] ?? "New Feedback";
     if($feedback["UseParaphrasedQuestion"] == 1) {
      $title = $feedback["ParaphrasedQuestion"];
     }
     $_Card = [
      "Action" => $this->core->Element(["button", "Respond", [
       "class" => "CardButton SendData",
       "data-form" => ".FeedbackEditor$id",
       "data-encryption" => "AES",
       "data-processor" => $this->core->AESencrypt("v=".base64_encode("Feedback:SaveResponse"))
      ]]),
      "Front" => [
       "ChangeData" => [
       "[Feedback.ID]" => $id,
       "[Feedback.Title]" => $title
       ],
       "ExtensionID" => "56718d75fb9ac2092c667697083ec73f"
      ]
     ];
     $_Commands = [
      [
       "Name" => "RenderInpouts",
       "Parameters" => [
        ".SendResponse$id",
        [
         [
          "Attributes" => [
           "name" => "ID",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => $id
         ],
         [
          "Attributes" => [
           "name" => "ParaphrasedQuestion",
           "placeholder" => "Paraphrased Question",
           "type" => "text"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => $this->core->AESencrypt($feedback["ParaphrasedQuestion"])
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "Message",
           "placeholder" => "Say something..."
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "NONAME"
          ],
          "Type" => "TextBox",
          "Value" => ""
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "0" => "No",
           "1" => "Yes"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Paraphrase"
          ],
          "Name" => "UseParaphrasedQuestion",
          "Type" => "Select",
          "Value" => $feedback["UseParaphrasedQuestion"]
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "1" => "High",
           "2" => "Normal",
           "3" => "Low"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Priority"
          ],
          "Name" => "Priority",
          "Type" => "Select",
          "Value" => $feedback["Priority"]
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "0" => "No",
           "1" => "Yes"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Resolved"
          ],
          "Name" => "Resolved",
          "Type" => "Select",
          "Value" => $feedback["Resolved"]
         ]
        ]
       ]
      ],
      [
       "Name" => "UpdateContentRecursiveAES",
       "Parameters" => [
        ".FeedbackStream$id",
        $this->core->AESencrypt("v=".base64_encode("Feedback:Stream")."&ID=$id")
       ]
      ]
     ];
    }
   } elseif($public == 1) {
    $_Dialog = "";
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "h1" => "Let's Talk!"
     ]).$this->core->Element([
       "p" => "We want to hear from you, send us your feedback."
     ]).$this->core->Element([
      "div", "&nbsp;", ["class" => "Desktop33 MobilfHide"]
     ]).$this->core->Element([
      "div", $this->core->Element(["button", "Send Feedback", [
       "class" => "BBB OpenDialog v2 v2w",
       "data-view" => base64_encode("v=".base64_encode("Feedback:NewThread"))
      ]]), ["class" => "Desktop33 MobilfFull"]
     ]).$this->core->Element([
      "div", "&nbsp;", ["class" => "Desktop33 MobilfHide"]
     ]))
    ];
    if(!empty($id)) {
     $_Dialog = "";
     $feedback = $this->core->Data("Get", ["feedback", $id]);
     $paraphrasedQuestion = $feedback["ParaphrasedQuestion"] ?? "";
     $title = $feedback["Subject"] ?? "New Feedback";
     if($feedback["UseParaphrasedQuestion"] == 1) {
      $title = $feedback["ParaphrasedQuestion"];
     }
     $_Commands = [
      [
       "Name" => "RenderInpouts",
       "Parameters" => [
        ".SendResponse$id",
        [
         [
          "Attributes" => [
           "name" => "ID",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => $id
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "Message",
           "placeholder" => "Say something..."
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "NONAME"
          ],
          "Type" => "TextBox",
          "Value" => ""
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "1" => "High",
           "2" => "Normal",
           "3" => "Low"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Priority"
          ],
          "Name" => "Priority",
          "Type" => "Select",
          "Value" => $feedback["Priority"]
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "0" => "No",
           "1" => "Yes"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Resolved"
          ],
          "Name" => "Resolved",
          "Type" => "Select",
          "Value" => $feedback["Resolved"]
         ]
        ]
       ]
      ],
      [
       "Name" => "UpdateContentRecursiveAES",
       "Parameters" => [
        ".FeedbackStream$id",
        $this->core->AESencrypt("v=".base64_encode("Feedback:Stream")."&ID=$id")
       ]
      ]
     ];
     $_View = [
      "ChangeData" => [
      "[Feedback.ID]" => $id,
      "[Feedback.Processor]" => $this->core->AESencrypt("v=".base64_encode("Feedback:SaveResponse")),
      "[Feedback.Title]" => $title
      ],
      "ExtensionID" => "599e260591d6dca59a8e0a52f5bd64be"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function NewThread(): string {
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $id = $this->core->UUID("FeedbackThreadFor$you");
   return $this->core->JSONResponse([
    "Card" => [
     "Action" => $this->core->Element(["button", "Send", [
      "class" => "CardButton SendData",
      "data-encryption" => "AES",
      "data-form" => ".ContactForm$id",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("Feedback:Save"))
     ]]),
     "Commands" => [
      [
       "Name" => "RenderInpouts",
       "Parameters" => [
        ".NewFeedbackThread$id",
        [
         [
          "Attributes" => [
           "class" => "req",
           "name" => "Email",
           "placeholder" => "johnny.test@outerhaven.nyc",
           "type" => "email"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "NONAME",
           "Header" => 1,
           "HeaderText" => "E-Mail"
          ],
          "Type" => "Text",
          "Value" => $this->core->AESencrypt($y["Personal"]["Email"])
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "Name",
           "placeholder" => "John Doe",
           "type" => "text"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "NONAME",
           "Header" => 1,
           "HeaderText" => "Name"
          ],
          "Type" => "Text",
          "Value" => $this->core->AESencrypt($y["Personal"]["FirstName"])
         ],
         [
          "Attributes" => [
           "class" => "CheckIfNumeric req",
           "name" => "Phone",
           "pattern" => "\d*",
           "placeholder" => "7777777777",
           "type" => "number"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "NONAME",
           "Header" => 1,
           "HeaderText" => "Phone Number"
          ],
          "Type" => "Text",
          "Value" => ""
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "Subject",
           "placeholder" => "Subject",
           "type" => "text"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "NONAME",
           "Header" => 1,
           "HeaderText" => "Subject"
          ],
          "Type" => "Text",
          "Value" => ""
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "Message",
           "placeholder" => "Say Something..."
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "NONAME",
           "Header" => 1,
           "HeaderText" => "Body"
          ],
          "Type" => "TextBox",
          "Value" => ""
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "0" => "No",
           "1" => "Yes"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Allow Indexing?"
          ],
          "Name" => "Index",
          "Type" => "Select",
          "Value" => 0
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "1" => "High",
           "2" => "Normal",
           "3" => "Low"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Priority"
          ],
          "Name" => "Priority",
          "Type" => "Select",
          "Value" => 2
         ],
         [
          "Attributes" => [],
          "OptionGroup" => [
           "0" => "No",
           "1" => "Yes"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Send Occasional Emails?"
          ],
          "Name" => "SendOccasionalEmails",
          "Type" => "Select",
          "Value" => 0
         ]
        ]
       ]
      ]
     ],
     "Front" => [
      "ChangeData" => [
       "[Feedback.ID]" => $id
      ],
      "ExtensionID" => "2b5ca0270981e891ce01dba62ef32fe4"
     ]
    ]
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "A message is required."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "Email",
    "Index",
    "Message",
    "Name",
    "Phone",
    "Subject",
    "Priority"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Message"])) {
    $_AccessCode = "Accepted";
    $now = $this->core->timestamp;
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
    $id = $this->core->UUID("NewFeedback$you");
    array_push($feedback["Thread"], [
     "Body" => $this->core->PlainText([
      "Data" => $data["Message"],
      "Encode" => 1,
      "HTMLEncode" => 1
     ]),
     "From" => $you,
     "Sent" => $now
    ]);
    /*--$sql = New SQL($this->core->cypher->SQLCredentials());
    $query = "REPLACE INTO Feedback(
     Feedback_Created,
     Feedback_ID,
     Feedback_Message,
     Feedback_ParaphrasedQuestion,
     Feedback_Subject,
     Feedback_Username
    ) VALUES(
     :Created,
     :ID,
     :Message,
     :ParaphrasedQuestion,
     :Subject,
     :Username
    )";
    $sql->query($query, [
     ":Created" => $now,
     ":ID" => $id,
     ":Message" => $this->core->Excerpt($this->core->PlainText([
      "Data" => $feedback["Thread"][0]["Body"],
      "Display" => 1,
      "HTMLDecode" => 1
     ]), 1000),
     ":ParaphrasedQuestion" => $feedback["ParaphrasedQuestion"],
     ":Subject" => $feedback["Subject"],
     ":Username" => $feedback["Username"]
    ]);
    $sql->execute();
    $this->core->Data("Save", ["feedback", $id, $feedback]);
    $this->core->Statistic("New Feedback");--*/
    $_Dialog = [
     "Body" => "We will be in touch as soon as possible!",
     "Header" => "Thank you",
     "Scrollable" => json_encode($feedback, true)
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function SaveResponse(array $data): string {
   $_Dialog = [
    "Body" => "The Feedback Identifier or Message are missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "Message",
    "ParaphrasedQuestion",
    "Priority",
    "Resolved",
    "UseParaphrasedQuestion"
   ]);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($data["Message"]) && !empty($id)) {
    $feedback = $this->core->Data("Get", ["feedback", $id]);
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
       "[Mail.Message]" => $this->core->PlainText([
        "Data" => $data["Message"],
        "Display" => 1
       ]),
       "[Mail.Name]" => $feedback["Name"],
       "[Mail.Link]" => $this->core->base."/feedback/$id"
      ], $this->core->Extension("dc901043662c5e71b5a707af782fdbc1")]),
      "Title" => "Re: ".$feedback["Subject"],
      "To" => $feedback["Email"]
     ]);
    }
    $this->core->Data("Save", ["feedback", $id, $feedback]);
    $this->core->Statistic("New Feedback Response");
    $_Dialog = [
     "Body" => "Your response has been sent.",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function Stream(array $data): string {
   $_Dialog = [
    "Body" => "The Feedback Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $feedback = $this->core->Data("Get", ["feedback", $id]);
    $thread = $feedback["Thread"] ?? [];
    $extension = $this->core->Extension("1f4b13bf6e6471a7f5f9743afffeecf9");
    foreach($thread as $key => $message) {
     $class = ($message["From"] != $you) ? "MSGt" : "MSGy";
     $_View .= $this->core->Change([[
      "[Message.Attachments]" => "",
      "[Message.Class]" => $class,
      "[Message.MSG]" => $this->core->PlainText([
       "Data" => $message["Body"],
       "Decode" => 1,
       "HTMLDecode" => 1
      ]),
      "[Message.Sent]" => $this->core->TimeAgo($message["Sent"])
     ], $extension]);
    }
    $_View = [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($_View)
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
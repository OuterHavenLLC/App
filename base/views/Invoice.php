<?php
 Class Invoice extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Add(array $data): string {
   $_AccessCode = "Denied";
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Invoice Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["Invoice"] ?? "";
   $shopID = $data["Shop"] ?? "";
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) && !empty($type)) {
    $_Dialog = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $_Dialog = [
      "Body" => "You are not authorized to add a $type.",
      "Header" => "Forbidden"
     ];
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $shop = $this->core->Data("Get", ["shop", $shopID]);
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      $_Dialog = [
       "Body" => "The content type is missing."
      ];
      if($type == "Charge") {
       $_Dialog = "";
       $viewCharges = $data["ViewCharges"] ?? 0;
       if($viewCharges == 1) {
        $invoice = $this->core->Data("Get", ["invoice", $id]);
        $chargeList = "";
        $charges = $invoice["Charges"] ?? [];
        if(empty($charges)) {
         $_Extension = $this->core->Element(["h4", "No Charges", [
          "class" => "CenterText UpperCase"
         ]]);
        } else {
         $_Extension = "";
         foreach($charges as $key => $charge) {
          $description = $charge["Description"] ?? "Unknown";
          $paid = $charge["Paid"] ?? 0;
          $title = $charge["Title"] ?? "Unknown";
          $value = $charge["Value"] ?? 0.00;
          $_Extension .= $this->core->Change([[
           "[Invoice.Charge.Description]" => $description,
           "[Invoice.Charge.Title]" => $title,
           "[Invoice.Charge.Value]" => $this->core->Element([
            "p", "$$value",
            ["class" => "DesktopRightText"]
           ])
          ], $this->core->Extension("7a421d1b6fd3b4958838e853ae492588")]);
         }
        }
        $_View = [
         "ChangeData" => [],
         "Extension" => $this->core->AESencrypt($_Extension)
        ];
       } else {
        $_Commands = [
         [
          "Name" => "RenderInputs",
          "Parameters" => [
           ".Charge$id",
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
              "name" => "Charge",
              "type" => "hidden"
             ],
             "Options" => [],
             "Type" => "Text",
             "Value" => 1
            ],
            [
             "Attributes" => [
              "name" => "Shop",
              "type" => "hidden"
             ],
             "Options" => [],
             "Type" => "Text",
             "Value" => $shopID
            ],
            [
             "Attributes" => [
              "name" => "ReadyForPayment"
             ],
             "Options" => [
              "Container" => 1,
              "ContainerClass" => "NONAME",
              "Header" => 1,
              "HeaderText" => "",
              "Selected" => ""
             ],
             "Text" => "Set the Invoice status to <em>Ready for Payment</em>. You will not be able to add further charges or change the Invoice status again.",
             "Type" => "Check",
             "Value" => 1
            ]
           ]
          ]
         ],
         [
          "Name" => "UpdateContentRecursiveAES",
          "Parameters" => [
           ".Charges$id",
           $this->core->AESencrypt("v=".base64_encode("Invoice:Add")."&Invoice=$id&Shop=$shopID&Type=Charge&ViewCharges=1"),
           6000
          ]
         ]
        ];
        $_View = [
         "ChangeData" => [
          "[Invoice.ChargeClone]" => base64_encode($this->core->Extension("cfc6f5b795f1254de32ef292325292a6")),
          "[Invoice.ID]" => $id,
          "[Invoice.Save]" => $this->core->AESencrypt("v=".base64_encode("Invoice:Save")),
          "[Invoice.Shop]" => $shopID
         ],
         "ExtensionID" => "60fe8170fa7a51cdd75097855c74a95c"
        ];
       }
      } elseif($type == "Note") {
       $_Dialog = "";
       $viewNotes = $data["ViewNotes"] ?? 0;
       if($viewNotes == 1) {
        $invoice = $this->core->Data("Get", ["invoice", $id]);
        $noteList = "";
        $notes = $invoice["Notes"] ?? [];
        $notes = array_reverse($notes);
        if(empty($notes)) {
         $_Extension = $this->core->Element(["h4", "No Notes", [
          "class" => "CenterText UpperCase"
         ]]);
        } else {
         $_Extension = "";
         foreach($notes as $key => $note) {
          $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($note, "LiveView");
          $_Extension .= $this->core->Change([[
           "[Attached.Albums]" => $liveViewSymbolicLinks["Albums"],
           "[Attached.Articles]" => $liveViewSymbolicLinks["Articles"],
           "[Attached.Attachments]" => $liveViewSymbolicLinks["Attachments"],
           "[Attached.Blogs]" => $liveViewSymbolicLinks["Blogs"],
           "[Attached.BlogPosts]" => $liveViewSymbolicLinks["BlogPosts"],
           "[Attached.Chats]" => $liveViewSymbolicLinks["Chats"],
           "[Attached.DemoFiles]" => $liveViewSymbolicLinks["DemoFiles"],
           "[Attached.Forums]" => $liveViewSymbolicLinks["Forums"],
           "[Attached.ForumPosts]" => $liveViewSymbolicLinks["ForumPosts"],
           "[Attached.ID]" => $this->core->UUID("NoteAttachments"),
           "[Attached.Members]" => $liveViewSymbolicLinks["Members"],
           "[Attached.Polls]" => $liveViewSymbolicLinks["Polls"],
           "[Attached.Products]" => $liveViewSymbolicLinks["Products"],
           "[Attached.Shops]" => $liveViewSymbolicLinks["Shops"],
           "[Attached.Updates]" => $liveViewSymbolicLinks["Updates"]
          ], $this->core->PlainText([
           "Data" => $this->core->Element([
            "h4", $note["Created"]
           ]).$this->core->Element([
            "p", $note["Note"]
           ]).$this->core->Element([
            "div", "[Extension:af6c0c610ebcc5e110fccec405b9dbf4]", ["class" => "NONAME"]
           ]),
           "Display" => 1
          ])]);
         }
        }
        $_View = [
         "ChangeData" => [],
         "Extension" => $this->core->AESencrypt($_Extension)
        ];
       } else {
        $attachments = $this->view(base64_encode("WebUI:Attachments"), [
         "ID" => $id,
         "Media" => [
          "Album" => [],
          "Article" => [],
          "Attachment" => [],
          "Blog" => [],
          "BlogPost" => [],
          "Chat" => [],
          "Forum" => [],
          "ForumPost" => [],
          "Member" => [],
          "Poll" => [],
          "Product" => [],
          "Update" => []
         ]
        ]);
        $_Commands = [
         [
          "Name" => "RenderInputs",
          "Parameters" => [
           ".Note$id",
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
              "name" => "Note",
              "type" => "hidden"
             ],
             "Options" => [],
             "Type" => "Text",
             "Value" => 1
            ],
            [
             "Attributes" => [
              "name" => "Shop",
              "type" => "hidden"
             ],
             "Options" => [],
             "Type" => "Text",
             "Value" => $shopID
            ],
            [
             "Attributes" => [
              "class" => "req",
              "name" => "InvoiceNote",
              "placeholder" => "Say something..."
             ],
             "Options" => [
              "Container" => 1,
              "ContainerClass" => "NONAME",
              "Header" => 1,
              "HeaderText" => "Add Note"
             ],
             "Type" => "TextBox",
             "Value" => ""
            ]
           ]
          ]
         ],
         [
          "Name" => "UpdateContentRecursiveAES",
          "Parameters" => [
           ".Notes$id",
           $this->core->AESencrypt("v=".base64_encode("Invoice:Add")."&Invoice=$id&Shop=$shopID&Type=Note&ViewNotes=1"),
           6000
          ]
         ]
        ];
        $_View = [
         "ChangeData" => [
          "[Invoice.Attachments]" => $this->core->RenderView($attachments),
          "[Invoice.ID]" => $id,
          "[Invoice.Save]" => $this->core->AESencrypt("v=".base64_encode("Invoice:Save")),
          "[Invoice.Shop]" => $shopID
         ],
         "ExtensionID" => "82e29a8d9c5737b07a4db0a1de45c7db"
        ];
       }
      }
      $_Card = ($card == 1) ? [
       "Front" => $_View
      ] : "";
      $_View = ($card == 0) ? $_View : "";
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard",
    "View" => $_View
   ]);
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? "";
   $isPreset = $data["Preset"] ?? 0;
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($shop)) {
    $charges = [];
    if(!empty($id) && $isPreset == 1) {
     $preset = $this->core->Data("Get", ["invoice-preset", $id]);
     $_ChangeData = [
      "[Invoice.Charges]" => json_encode($charges, true),
      "[Invoice.Shop]" => $shop
     ];
     $_Extension = "UpdatePreset";
    } elseif($isPreset == 0) {
     $id = md5("Invoice$you".uniqid());
     $invoice = $this->core->Data("Get", ["invoice", $id]);
     $invoice = $this->core->FixMissing($invoice, [
      "ChargeTo",
      "Email",
      "Phone"
     ]);
     $_ChangeData = [
      "[Invoice.ChargeClone]" => base64_encode($this->core->Extension("cfc6f5b795f1254de32ef292325292a6")),
      "[Invoice.ID]" => $id,
      "[Invoice.Save]" => $this->core->AESencrypt("v=".base64_encode("Invoice:Save"))
     ];
     $_Commands = [
      [
       "Name" => "RenderInputs",
       "Parameters" => [
        ".Charges$id",
        [
         [
          "Attributes" => [
           "class" => "req",
           "name" => "ChargeTitle[]",
           "placeholder" => "Deposit",
           "type" => "text"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Title"
          ],
          "Type" => "Text",
          "Value" => ""
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "ChargeDescription[]",
           "placeholder" => "Why are you placing this charge?",
           "type" => "text"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Description"
          ],
          "Type" => "TextBox",
          "Value" => ""
         ],
         [
          "Attributes" => [
           "name" => "ChargePaid[]",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => 0
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "ChargeValue[]",
           "placeholder" => "50.00",
           "type" => "number"
          ],
          "Options" => [
           "Container" => 1,
           "ContainerClass" => "Desktop50 MobileFull",
           "Header" => 1,
           "HeaderText" => "Amount"
          ],
          "Type" => "Text",
          "Value" => $this->core->AESencrypt(50.00)
         ]
        ]
       ]
      ],
      [
       "Name" => "RenderInputs",
       "Parameters" => [
        ".InvoiceInformation$id",
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
           "name" => "Shop",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => $shopID
         ],
         [
          "Attributes" => [
           "name" => "UN",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => $you
         ],
         [
          "Attributes" => [
           "name" => "new",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => 1
         ],
         [
          "Attributes" => [
           "name" => "Title",
           "placeholder" => "A New Pre-set Invoice",
           "type" => "text"
          ],
          "Options" => [
           "Header" => 1,
           "HeaderText" => "Service Name"
          ],
          "Type" => "Text",
          "Value" => ""
         ],
         [
          "Attributes" => [
           "name" => "ChargeTo",
           "placeholder" => "Which Member is paying?",
           "type" => "text"
          ],
          "Options" => [
           "Header" => 1,
           "HeaderText" => "Charge To"
          ],
          "Type" => "Text",
          "Value" => $this->core->AESencrypt($invoice["ChargeTo"])
         ],
         [
          "Attributes" => [
           "class" => "req",
           "name" => "Email",
           "placeholder" => "johnny.test@outerhaven.nyc",
           "type" => "email"
          ],
          "Options" => [
           "Header" => 1,
           "HeaderText" => "E-Mail"
          ],
          "Type" => "Text",
          "Value" => $this->core->AESencrypt($invoice["Email"])
         ],
         [
          "Attributes" => [
           "name" => "Phone",
           "placeholder" => "777-777-7777",
           "type" => "text"
          ],
          "Options" => [
           "Header" => 1,
           "HeaderText" => "Phone Number"
          ],
          "Type" => "Text",
          "Value" => $this->core->AESencrypt($invoice["Phone"])
         ]
        ]
       ]
      ],
      [
       "Name" => "RenderInputs",
       "Parameters" => [
        ".Preset$id",
        [
         [
          "Attributes" => [
           "name" => "Preset",
           "type" => "hidden"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => 1
         ]
        ]
       ]
      ]
     ];
     $_Extension = "e372b28484951c22fe9920317c852436";
    }
    $_View = [
     "ChangeData" => $_ChangeData,
     "Extension" => $this->core->AESencrypt($_Extension)
    ];
    $_Card = ($card == 1) ? [
     "Front" => $_View
    ] : "";
    $_View = ($card == 0) ? $_View :"";
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Forward(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Invoice Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["Invoice"] ?? "";
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $_Dialog = "";
     $invoice = $this->core->Data("Get", ["invoice", $id]);
     $_Card = [
      "Action" => $this->core->Element(["button", "Forward", [
       "class" => "CardButton SendData",
       "data-form" => ".ForwardInvoice$id",
       "data-processor" => base64_encode("v=".base64_encode("Invoice:Save"))
      ]]),
      "Front" => [
       "ChangeData" => [
        "[Invoice.ID]" => $id,
        "[Invoice.Shop]" => $invoice["Shop"]
       ],
       "ExtensionID" => "bef71930eb3342a550ba9e8a971cebe2"
      ]
     ];
     $_Commands = [
      [
       "Name" => "RenderInputs",
       "Parameters" => [
        ".ForwardTo$id",
        [
   [
    "Attributes" => [
     "name" => "Forward",
     "type" => "hidden"
    ],
    "Options" => [],
    "Type" => "Text",
    "Value" => 1
   ],
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
     "name" => "Shop",
     "type" => "hidden"
    ],
    "Options" => [],
    "Type" => "Text",
    "Value" => $shopID
   ],
   [
    "Attributes" => [
     "name" => "Email",
     "placeholder" => "johnny.test@outerhaven.nyc",
     "type" => "email"
    ],
    "Options" => [],
    "Type" => "Text",
    "Value" => ""
   ],
   [
    "Attributes" => [
     "name" => "Username",
     "placeholder" => "JohnnyTest",
     "type" => "text"
    ],
    "Options" => [],
    "Type" => "Text",
    "Value" => ""
   ]
        ]
       ]
      ]
     ];
    }
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function Hire(array $data): string {
   $_AccessCode = "Denied";
   $_AddTopMargin = "0";
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $_ResponseType = "";
   $_View = "";
   $_ViewTitle = $this->core->config["App"]["Name"];
   $data = $data["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? md5($this->core->ShopID);
   $shopID = $id;
   $_Success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_ViewTitle = "Hire";
    $_AccessCode = "Accepted";
    $action = "";
    $createJob = $data["CreateJob"] ?? 0;
    $saveJob = $data["SaveJob"] ?? 0;
    $shop = $this->core->Data("Get", ["shop", $id]);
    $enableHireSection = $shop["EnableHireSection"] ?? "No";
    $partners = $shop["Contributors"] ?? [];
    $services = $shop["InvoicePresets"] ?? 0;
    $hire = (md5($you) != $id) ? 1 : 0;
    $hire = (count($services) > 0 && $hire == 1) ? 1 : 0;
    $hire = ($enableHireSection == "Yes" && $hire == 1) ? 1 : 0;
    $limit = $shop["HireLimit"] ?? 5;
    $openInvoices = 0;
    $_Dialog = [
     "Body" => $shop["Title"]." is not currently accepting job offers.",
     "Header" => "Sorry!"
    ];
    foreach($shop["Invoices"] as $key => $invoice) {
     $invoice = $this->core->Data("Get", ["invoice", $invoice]);
     if($invoice["Status"] == "Open") {
      $openInvoices++;
     }
    } if($hire == 1 && $openInvoices < $limit && $shop["Open"] == "Yes") {
     $_Dialog = "";
     if(!empty($saveJob)) {
      $data = $this->core->DecodeBridgeData($data);
      $saveJob = $data["SaveJob"] ?? 0;
      if($saveJob == 1) {
       $preset = $this->core->Data("Get", ["invoice-preset", $data["Service"]]);
       $chargeTo = $data["ChargeTo"] ?? "";
       $charges = [];
       array_push($charges, $preset["Charges"]);
       $id = md5(uniqid("Invoice$you"));
       $invoice = [
        "ChargeTo" => $chargeTo,
        "Charges" => $charges,
        "Email" => $data["Email"],
        "Notes" => [],
        "PaidInFull" => $preset["PaidInFull"],
        "Phone" => $data["Phone"],
        "Shop" => $preset["Shop"],
        "Status" => $preset["Status"],
        "UN" => $preset["UN"]
       ];
       $invoices = $shop["Invoices"] ?? [];
       array_push($invoices, $id);
       $invoices = array_unique($invoices);
       $name = $chargeTo ?? $data["Email"];
       $_Success = "CloseCard";
       if(!empty($data["Email"])) {
        if($y["Subscriptions"]["VIP"]["A"] == 1) {
         $invoice["Charges"][0]["Paid"] = 1;
         $preset["Charges"][0]["Value"] = 0;
         $this->core->SendEmail([
          "Message" => $this->core->Change([[
           "[Mail.Message]" => "Your Service request has been sent! Please review the Invoice linked below. Thank you for being a V.I.P. Member, we covered your deposit!",
           "[Mail.Invoice]" => "Total due: $0.00",
           "[Mail.Name]" => $name,
           "[Mail.Link]" => $this->core->base."/invoice/$id",
           "[Mail.Shop.Name]" => $shop["Title"],
           "[Mail.View]" => $this->core->Element([
            "a", "View Invoice", [
             "class" => "BB BBB v2 v2w",
             "href" => $this->core->base."/invoice/$id"
            ]
           ])
          ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
          "Title" => $shop["Title"].": Invoice $id",
          "To" => $data["Email"]
         ]);
        } else {
         $this->core->SendEmail([
          "Message" => $this->core->Change([[
           "[Mail.Message]" => "Your Service request has been sent! Please review the Invoice linked below and pay the requested deposit amount.",
           "[Mail.Invoice]" => "Total due: $".number_format($preset["Charges"][0]["Value"], 2),
           "[Mail.Name]" => $name,
           "[Mail.Link]" => $this->core->base."/invoice/$id",
           "[Mail.Shop.Name]" => $shop["Title"],
           "[Mail.View]" => $this->core->Element([
             "a", "View Invoice", [
              "class" => "BB BBB v2 v2w",
              "href" => $this->core->base."/invoice/$id"
            ]
           ])
          ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
          "Title" => $shop["Title"].": Invoice $id",
          "To" => $data["Email"]
         ]);
        }
       } if(!empty($chargeTo)) {
        $this->core->SendBulletin([
         "Data" => [
          "Invoice" => $id,
          "Shop" => $shopID
         ],
         "To" => $chargeTo,
         "Type" => "NewJob"
        ]);
       } foreach($partners as $key => $value) {
        $partner = $this->core->Member($key);
        $this->core->SendEmail([
         "Message" => $this->core->Change([[
          "[Mail.Message]" => "<em>".$shop["Title"]."</em> was hired by a potential client! Please verify payment of the deposit before proceeding with the service. An email containing the initial balance has been sent to the potential client.",
          "[Mail.Invoice]" => "Total due: $".number_format($preset["Charges"][0]["Value"], 2),
          "[Mail.Name]" => $partner["Personal"]["FirstName"],
          "[Mail.Link]" => $this->core->base."/invoice/$id",
          "[Mail.Shop.Name]" => $shop["Title"],
          "[Mail.View]" => $this->core->Element([
           "a", "View Invoice", [
            "class" => "BB BBB v2 v2w",
            "href" => $this->core->base."/invoice/$id"
           ]
          ])
         ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
         "Title" => $shop["Title"].": Invoice $id",
         "To" => $data["Email"]
        ]);
        $this->core->SendBulletin([
         "Data" => [
          "Invoice" => $id,
          "Shop" => $shopID
         ],
         "To" => $key,
         "Type" => "NewJob"
        ]);
       }
       $shop["Invoices"] = $invoices;
       $this->core->Data("Save", ["invoice", $id, $invoice]);
       $this->core->Data("Save", ["shop", $shopID, $shop]);
       $_Dialog = [
        "Body" => "Your request has been submitted! Please check your email for the Invoice and pay the deposit amount. Your Invoice is also available at <em>".$this->core->base."/invoice/$id</em>.",
        "Header" => "Done"
       ];
      }
     } elseif($createJob == 1) {
      $action = (count($partners) == 1) ? "Me" : "Us";
      $action = $this->core->Element(["button", "Hire $action", [
       "class" => "CardButton SendData",
       "data-encryption" => "AES",
       "data-form" => ".Hire$id",
       "data-processor" => $this->core->AESencrypt("v=".base64_encode("Invoice:Hire"))
      ]]);
      $card = 1;
      $chargeTo = ($this->core->ID != $you) ? $you : "";
      $hireText = (count($partners) == 1) ? "Me" : "Us";
      $presets = [];
      foreach($services as $key => $value) {
       $service = $this->core->Data("Get", [
        "invoice-preset",
        $value
       ]);
       $presets[$value] = $service["Title"];
      }
      $_Commands = [
       [
        "Name" => "RenderInputs",
        "Parameters" => [
         ".ServiceInformation$id",
         [
          [
           "Attributes" => [
            "name" => "ChargeTo",
            "placeholder" => "Which Member is paying?",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $chargeTo
          ],
          [
           "Attributes" => [
            "name" => "SaveJob",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => 1
          ],
          [
           "Attributes" => [
            "name" => "Shop",
            "type" => "hidden"
           ],
           "Options" => [],
           "Type" => "Text",
           "Value" => $id
          ],
          [
           "Attributes" => [
            "class" => "req",
            "name" => "Email",
            "placeholder" => "Johnny.Test@outerhaven.nyc",
            "type" => "email"
           ],
           "Options" => [
            "Header" => 1,
            "HeaderText" => "E-Mail"
           ],
           "Type" => "Text",
           "Value" => $this->core->AESencrypt($y["Personal"]["Email"])
          ],
          [
           "Attributes" => [
            "name" => "Phone",
            "placeholder" => "777-777-7777",
            "type" => "text"
           ],
           "Options" => [
            "Header" => 1,
            "HeaderText" => "Phone Number"
           ],
           "Type" => "Text",
           "Value" => ""
          ],
          [
           "Attributes" => [],
           "OptionGroup" => $presets,
           "Options" => [
            "Container" => 1,
            "ContainerClass" => "Desktop50 MobileFull",
            "Header" => 1,
            "HeaderText" => "Which Service do you need?"
           ],
           "Name" => "Service",
           "Type" => "Select",
           "Value" => ""
          ]
         ]
        ]
       ]
      ];
      $_View = [
       "ChangeData" => [
        "[Hire.Shop]" => $id,
        "[Hire.Text]" => $hireText
       ],
       "ExtensionID" => "dab6e25feafcbb2741022bf6083c2975"
      ];
     } else {
      $_ViewTitle = "Hire ".$shop["Title"];
      $hireText = (count($partners) == 1) ? "Me" : "Us";
      $terms = $shop["HireTerms"] ?? "";
      if(!empty($terms)) {
       $terms = $this->core->Change([
        [
         "[Shop.Name]" => $shop["Title"]
        ], $this->core->PlainText([
         "Data" => $terms,
         "Display" => 1,
         "HTMLDecode" => 1
        ])
       ]);
      } else {
       $terms = $this->core->Extension("285adc3ef002c11dfe1af302f8812c3a");
      }
      $_AddTopMargin = ($card == 0) ? 1 : "0";
      $_View = [
       "ChangeData" => [
        "[Shop.Name]" => $shop["Title"],
        "[Shop.Hire]" => $this->core->AESencrypt("v=".base64_encode("Invoice:Hire")."&ID=$id&CreateJob=1"),
        "[Shop.Hire.Terms]" => $terms,
        "[Shop.Hire.Text]" => $hireText,
       ],
       "ExtensionID" => "045f6c5cf3728bd31b0d9663498a940c"
      ];
     }
    }
    $_Card = ($card == 1) ? [
     "Action" => $action,
     "Front" => $_View
    ] : "";
    $_View = ($card == 0) ? $_View : "";
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => $_AddTopMargin,
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "Success" => $_Success,
    "Title" => $_ViewTitle,
    "View" => $_View
   ]);
  }
  function Home(array $data): string {
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Invoice Identifier is missing."
   ];
   $_View = "";
   $_ViewTitle = $this->core->config["App"]["Name"];
   $data = $data["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = [
     "Body" => "We could not find data for Invoice <em>$id</em>."
    ];
    $_ViewTitle = "Invoice $id";
    $invoice = $this->core->Data("Get", ["invoice", $id]);
    $shopID = $invoice["Shop"] ?? "";
    if(!empty($shopID)) {
     $_Dialog = "";
     $_Shop = $this->core->Data("Get", ["shop", $shopID]);
     $_ViewTitle = "Invoice from ".$_Shop["Title"];
     $dependency = $data["Dependency"] ?? "";
     $invoice = $this->core->Data("Get", ["invoice", $id]);
     if($dependency == "Charges") {
      $_Extension = "";
      $check = 0;
      $isAdmin = ($invoice["Shop"] == md5($you)) ? 1 : 0;
      foreach($shop["Contributors"] as $member => $role) {
       if($check == 0 && $member == $you) {
        $check++;
       }
      }
      $check = ($check == 1 || $isAdmin == 1) ? 1 : 0;
      $charges = $invoice["Charges"] ?? [];
      foreach($charges as $key => $charge) {
       $description = $charge["Description"] ?? "Unknown";
       $paid = $charge["Paid"] ?? 0;
       $title = $charge["Title"] ?? "Unknown";
       $value = $charge["Value"] ?? 0.00;
       if($invoice["UN"] == $you) {
        $value = $this->core->Element([
         "p", "$".number_format($value, 2),
         ["class" => "DesktopRightText"]
        ]);
       } else {
        $value = ($paid == 1) ? $this->core->Element([
         "p", "$".number_format($value, 2),
         ["class" => "DesktopRightText"]
        ]) : $this->core->Element([
         "button", "$".number_format($value, 2),
         [
          "class" => "GoToView DesktopRight v2",
          "data-type" => "InvoicePayments$id;".base64_encode("v=".base64_encode("Shop:Pay")."&Charge=$key&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Invoice&ViewPairID=".base64_encode("InvoicePayments$id"))
         ]
        ]);
       }
       $description .= ($check == 1 && $paid == 0) ? $this->core->Element([
        "div", NULL, ["class" => "NONAME"]
       ]).$this->core->Element([
        "button", "Refund", [
         "class" => "OpenDialog v2",
         "data-view" => base64_encode("v=".base64_encode("Invoice:Refund")."&Charge=$key&Invoice=$id&Shop=".$invoice["Shop"])
        ]
       ]) : "";
       $_Extension .= $this->core->Change([[
        "[Invoice.Charge.Description]" => $description,
        "[Invoice.Charge.Title]" => $title,
        "[Invoice.Charge.Value]" => $value
       ], $this->core->Extension("7a421d1b6fd3b4958838e853ae492588")]);
      }
      $_View = [
       "ChangeData" => [],
       "Extension" => $this->core->AESencrypt($_Extension)
      ];
     } elseif($dependency == "Options") {
      $check = 0;
      $isAdmin = ($invoice["Shop"] == md5($you)) ? 1 : 0;
      foreach($shop["Contributors"] as $member => $role) {
       if($check == 0 && $member == $you) {
        $check++;
       }
      }
      $_Extension = ($check == 1 && $isAdmin == 1 && $invoice["Status"] == "Open") ? $this->core->Element([
       "button", "Charges", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Add")."&Card=1&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Charge")
       ]
      ]) : "";
      $_Extension .= ($check == 1 && $isAdmin == 1) ? $this->core->Element([
       "button", "Notes", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Add")."&Card=1&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Note")
       ]
      ]) : "";
      $_Extension .= $this->core->Element(["button", "Forward", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Forward")."&Invoice=$id&Shop=".$invoice["Shop"])
      ]]);
      $_View = [
       "ChangeData" => [],
       "Extension" => $this->core->AESencrypt($_Extension)
      ];
     } elseif($dependency == "Status") {
      $status = $invoice["Status"] ?? "Closed";
      $action = "No action needed.";
      $action = ($status == "Closed") ? "This Invoice has been paid in full. $action" : $action;
      $action = ($status == "Open") ? "This invoice is active and subject to change. You may make partial payments where necessary." : $action;
      $action = ($status == "ReadyForPayment") ? "Make any necessary payments or contact the merchant for further assistance." : $action;
      $status = ($status == "Open") ? "Open" : $status;
      $status = ($status == "ReadyForPayment") ? "Ready for Payment" : $status;
      $_View = [
       "ChangeData" => [],
       "Extension" => $this->core->AESencrypt($this->core->Element([
        "h4", "Invoice ID: $id", ["class" => "CenterText"]
       ]).$this->core->Element([
        "p", "<em>$status</em>", ["class" => "CenterText"]
       ]).$this->core->Element([
        "p", $action, ["class" => "CenterText"]
       ]))
      ];
     } elseif($dependency == "Total") {
      $balance = 0;
      $charges = $invoice["Charges"] ?? [];
      $isEmailed = $data["Emailed"] ?? 0;
      $tax = 0;
      $subtotal = 0;
      foreach($charges as $key => $charge) {
       $value = $charge["Value"] ?? 0.00;
       if($charge["Paid"] == 0) {
        $balance = $balance + $value;
       }
      }
      $subtotal = $balance;
      if($balance > 0) {
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
      }
      $balance = number_format(($subtotal + $tax), 2);
      $balance = ($invoice["Status"] == "ReadyForPayment" && $invoice["UN"] != $you && $balance > 0) ? $this->core->Element([
       "button", "$$balance", [
        "class" => "BBB CloseCard OpenFirSTEPTool v2",
        "data-fst" => base64_encode("v=".base64_encode("Shop:Pay")."&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Invoice&PayInFull=1")
       ]
      ]) : "<strong>$$balance</strong>";
      $balance = ($isEmailed == 1) ? "<strong>$$balance</strong>" : $balance;
      $_View = [
       "ChangeData" => [
        "[Invoice.Balance]" => $balance,
        "[Invoice.Subtotal]" => number_format($subtotal, 2),
        "[Invoice.Taxes]" => number_format($tax, 2)
       ],
       "ExtensionID" => "6faa1179113386dad098302e12049b8b"
      ];
     } else {
      $home = "v=".base64_encode("Invoice:Home")."&ID=$id&Shop=".$invoice["Shop"];
      $_Commands = [
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".Charges$id",
         $this->core->AESencrypt("$home&Dependency=Charges"),
         6000
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".Options$id",
         $this->core->AESencrypt("$home&Dependency=Options"),
         6000
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".Status$id",
         $this->core->AESencrypt("$home&Dependency=Status"),
         6000
        ]
       ],
       [
        "Name" => "UpdateContentRecursiveAES",
        "Parameters" => [
         ".Total$id",
         $this->core->AESencrypt("$home&Dependency=Total"),
         6000
        ]
       ]
      ];
      $_View = [
       "ChangeData" => [
        "[Invoice.ID]" => $id
       ],
       "ExtensionID" => "4a78b78f1ebff90e04a33b52fb5c5e97"
      ];
     }
    }
   }
   $_Card = ($card == 1) ? [
    "Front" => $_View
   ] : "";
   $_View = ($card == 0) ? $_View : "";
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "Title" => $_ViewTitle,
    "View" => $_View
   ]);
  }
  function Refund(array $data): string {
   $_Dialog = [
    "Body" => "The Charge or Invoice Identifier are missing."
   ];
   $data = $data["Data"] ?? [];
   $charge = $data["Charge"] ?? "";
   $id = $data["Invoice"] ?? "";
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif((!empty($charge) || $charge == 0) && !empty($id)) {
    $_Dialog = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $_Dialog = [
      "Body" => "You are not authorized to refund charges.",
      "Header" => "Forbidden"
     ];
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $shop = $this->core->Data("Get", ["shop", $shopID]);
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      $invoice = $this->core->Data("Get", ["invoice", $id]);
      $check = $invoice["Charges"][$charge]["Paid"];
      $_Dialog = [
       "Body" => "A refund was already issued for <em>".$invoice["Charges"][$charge]["Title"]."</em>."
      ];
      if($check == 0) {
       $chargeList = "";
       $charges = $invoice["Charges"] ?? [];
       $member = $invoice["ChargeTo"] ?? "";
       $name = $member ?? $invoice["Email"];
       $newCharge = $charges[$charge] ?? [];
       $newCharge["Paid"] = 1;
       $charges[$charge] = $newCharge;
       array_push($charges, [
        "Description" => "Authorized refund for <em>".$newCharge["Title"]."</em>.",
        "Paid" => 1,
        "Title" => "Refund",
        "Value" => number_format((0 - $newCharge["Value"]), 2)
       ]);
       $invoice["Charges"] = $charges;
       $total = 0;
       foreach($charges as $key => $charge) {
        $description = $charge["Description"] ?? "Unknown";
        $paid = $charge["Paid"] ?? 0;
        $title = $charge["Title"] ?? "Unknown";
        $value = $charge["Value"] ?? 0.00;
        $total = $total + $value;
        $chargeList .= $this->core->Change([[
         "[Invoice.Charge.Description]" => $description,
         "[Invoice.Charge.Title]" => $title,
         "[Invoice.Charge.Value]" => $this->core->Element([
          "p", "$$value",
          ["class" => "DesktopRightText"]
         ])
        ], $this->core->Extension("7a421d1b6fd3b4958838e853ae492588")]);
       }
       $total = $this->view(base64_encode("Invoice:Home"), [
        "Data" => [
         "Dependency" => "Total",
         "Emailed" => 1,
         "ID" => $id,
         "Shop" => $shopID
        ]
       ]);
       $total = $this->core->RenderView($total);
       $this->core->SendEmail([
        "Message" => $this->core->Change([[
         "[Mail.Message]" => $y["Personal"]["DisplayName"]." refunded the <em>".$newCharge["Title"]."</em> charge.",
         "[Mail.Invoice]" => $chargeList.$total,
         "[Mail.Name]" => $name,
         "[Mail.Link]" => $this->core->base."/invoice/$id",
         "[Mail.Shop.Name]" => $shop["Title"],
         "[Mail.View]" => $this->core->Element([
          "a", "View Invoice", [
           "class" => "BB BBB v2 v2w",
           "href" => $this->core->base."/invoice/$id"
          ]
         ])
        ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
        "Title" => $shop["Title"].": Invoice $id",
        "To" => $invoice["Email"]
       ]);
       if(!empty($member)) {
        $this->core->SendBulletin([
         "Data" => [
          "Invoice" => $id,
          "Shop" => $invoice["Shop"]
         ],
         "To" => $member,
         "Type" => "InvoiceUpdate"
        ]);
       } if($total == 0) {
        $invoice["PaidInFull"] = 1;
        $invoice["Status"] = "Closed";
       }
       $this->core->Data("Save", ["invoice", $id, $invoice]);
       $_Dialog = [
        "Body" => "Refund for <em>".$newCharge["Title"]."</em> issued. We will send a confirmation to <em>".$invoice["Email"]."</em>.",
        "Header" => "Done"
       ];
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function PurgePreset(array $data): string {
   $_Dialog = [
    "Body" => "The Shop or Service Identifiers are missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $secureKey) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id) && !empty($shopID)) {
    $_Dialog = [
     "Body" => "You are not authorized to delete Pre-sets.",
     "Header" => "Forbidden"
    ];
    $check = 0;
    $id = base64_decode($id);
    $shopID = base64_decode($shopID);
    $isAdmin = ($shopID == md5($you)) ? 1 : 0;
    $shop = $this->core->Data("Get", ["shop", $shopID]);
    $shopPartners = $shop["Contributors"] ?? [];
    foreach($shopPartners as $member => $role) {
     if($check == 0 && $member == $you) {
      $check++;
      break;
     }
    } if($check == 1 && $isAdmin == 1) {
     $newPresets = [];
     $presets = $shop["InvoicePresets"] ?? [];
     foreach($presets as $key => $value) {
      if($value != $id) {
       $newPresets[$key] = $value;
      }
     }
     $preset = $this->core->Data("Get", ["invoice-preset", $id]);
     $shop["InvoicePresets"] = $newPresets;
     $this->core->Data("Purge", ["invoice-preset", $id]);
     $this->core->Data("Save", ["shop", $shopID, $shop]);
     $_View = [
      "ChangeData" => [
      ],
      "Extension" => $this->core->AESencrypt($this->core->Element([
       "p", "The service <em>".$preset["Title"]."</em> was deleted.",
       ["class" => "CenterText"]
      ]).$this->core->Element([
       "button", "Okay", ["class" => "CloseDialog v2 v2w"]
      ]))
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Invoice or Pre-set Identifier are missing."
   ];
   $_ResponseType = "N/A";
   $_View = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "Phone",
    "Username"
   ]);
   $id = $data["ID"] ?? "";
   $shopID = $data["Shop"] ?? "";
   $_Success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $_Dialog = [
      "Body" => "You are not authorized to manage Invoices.",
      "Header" => "Forbidden"
     ];
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $shop = $this->core->Data("Get", ["shop", $shopID]);
     $shopContributors = $shop["Contributors"] ?? [];
     foreach($shopContributors as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
       break;
      }
     } if($check == 1 && $isAdmin == 1) {
      $_Dialog = [
       "Body" => "The Service Title is missing."
      ];
      $chargeList = "";
      $charges = [];
      $isCharge = $data["Charge"] ?? 0;
      $isNote = $data["Note"] ?? 0;
      $isForwarding = $data["Forward"] ?? 0;
      $isPreset = $data["Preset"] ?? 0;
      $title = $data["Title"] ?? "";
      if($isCharge == 1) {
       $_AccessCode = "Accepted";
       $bulletin = "";
       $invoice = $this->core->Data("Get", ["invoice", $id]);
       $member = $invoice["ChargeTo"] ?? "";
       $name = $member ?? $invoice["Email"];
       $chargeData = $data["ChargeTitle"] ?? [];
       $charges = $invoice["Charges"] ?? [];
       $readyForPayment = $data["ReadyForPayment"] ?? 0;
       $status = $invoice["Status"] ?? "Closed";
       $total = 0;
       for($i = 0; $i < count($chargeData); $i++) {
        $description = $data["ChargeDescription"][$i] ?? "Unknown";
        $paid = $data["ChargePaid"][$i] ?? 0;
        $title = $data["ChargeTitle"][$i] ?? "Unknown";
        $value = $data["ChargeValue"][$i] ?? 0.00;
        array_push($charges, [
         "Description" => $description,
         "Paid" => $paid,
         "Title" => $title,
         "Value" => $value
        ]);
       } foreach($charges as $key => $charge) {
        $description = $charge["Description"] ?? "Unknown";
        $paid = $charge["Paid"] ?? 0;
        $title = $charge["Title"] ?? "Unknown";
        $value = $charge["Value"] ?? 0.00;
        $total = $total + $value;
        $chargeList .= $this->core->Change([[
         "[Invoice.Charge.Description]" => $description,
         "[Invoice.Charge.Title]" => $title,
         "[Invoice.Charge.Value]" => $this->core->Element([
          "p", "$$value",
          ["class" => "DesktopRightText"]
         ])
        ], $this->core->Extension("7a421d1b6fd3b4958838e853ae492588")]);
       } if($readyForPayment == 1) {
        $invoice["PaidInFull"] = ($total == 0) ? 1 : 0;
        $status = ($total > 0) ? "ReadyForPayment" : "Closed";
        $invoice["Status"] = $status;
       } 
       $total = $this->view(base64_encode("Invoice:Home"), [
        "Data" => [
         "Dependency" => "Total",
         "Emailed" => 1,
         "ID" => $id,
         "Shop" => $shopID
        ]
       ]);
       $total = $this->core->RenderView($total);
       $this->core->SendEmail([
        "Message" => $this->core->Change([[
         "[Mail.Message]" => "Your Invoice is ready for payment.",
         "[Mail.Invoice]" => $chargeList.$total,
         "[Mail.Name]" => $name,
         "[Mail.Link]" => $this->core->base."/invoice/$id",
         "[Mail.Shop.Name]" => $shop["Title"],
         "[Mail.View]" => $this->core->Element([
          "a", "View Invoice", [
           "class" => "BB BBB v2 v2w",
           "href" => $this->core->base."/invoice/$id"
          ]
         ])
        ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
        "Title" => $shop["Title"].": Invoice $id",
        "To" => $invoice["Email"]
       ]);
       if(!empty($member)) {
        $this->core->SendBulletin([
         "Data" => [
          "Invoice" => $id,
          "Shop" => $invoice["Shop"]
         ],
         "To" => $member,
         "Type" => "InvoiceUpdate"
        ]);
        $bulletin = " <em>$member</em> will receive a Bulletin shortly.";
       }
       $invoice["Charges"] = $charges;
       $this->core->Data("Save", ["invoice", $id, $invoice]);
       $_ResponseType = "ReplaceContent";
       $_View = [
        "ChangeData" => [],
        "Extension" => $this->core->AESencrypt($this->core->Element([
         "h4", "Success!", ["class" => "CenterText UpperCase"]
        ]).$this->core->Element([
         "p", "Your Invoice has been updated.$bulletin",
         ["class" => "CenterText"]
        ]))
       ];
      } elseif($isForwarding == 1) {
       $_Dialog = [
        "Body" => "An e-mail address or username are required."
       ];
       $email = $data["Email"] ?? "";
       $invoice = $this->core->Data("Get", ["invoice", $id]);
       $member = $data["Username"] ?? "";
       if(!empty($email) || !empty($member)) {
        $_AccessCode = "Accepted";
        $bulletin = "";
        $charges = $invoice["Charges"] ?? [];
        foreach($charges as $key => $charge) {
         $description = $charge["Description"] ?? "Unknown";
         $paid = $charge["Paid"] ?? 0;
         $title = $charge["Title"] ?? "Unknown";
         $value = $charge["Value"] ?? 0.00;
         $chargeList .= $this->core->Change([[
          "[Invoice.Charge.Description]" => $description,
          "[Invoice.Charge.Title]" => $title,
          "[Invoice.Charge.Value]" => $this->core->Element([
           "p", "$$value",
           ["class" => "DesktopRightText"]
          ])
         ], $this->core->Extension("7a421d1b6fd3b4958838e853ae492588")]);
        } if(!empty($email)) {
         $total = $this->view(base64_encode("Invoice:Home"), [
          "Data" => [
           "Dependency" => "Total",
           "Emailed" => 1,
           "ID" => $id,
           "Shop" => $shopID
          ]
         ]);
         $total = $this->core->RenderView($total);
         $this->core->SendEmail([
          "Message" => $this->core->Change([[
           "[Mail.Message]" => $y["Personal"]["DisplayName"]." forwarded this Inovice to you.",
           "[Mail.Invoice]" => $chargeList.$total,
           "[Mail.Name]" => $email,
           "[Mail.Link]" => $this->core->base."/invoice/$id",
           "[Mail.Shop.Name]" => $shop["Title"],
           "[Mail.View]" => $this->core->Element([
            "a", "View Invoice", [
             "class" => "BB BBB v2 v2w",
             "href" => $this->core->base."/invoice/$id"
            ]
           ])
          ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
          "Title" => $shop["Title"].": Invoice $id",
          "To" => $email
         ]);
        } if(!empty($member)) {
         $check = 0;
         $members = $this->core->DatabaseSet("Member");
         foreach($members as $key => $value) {
          $value = str_replace("nyc.outerhaven.mbr.", "", $value);
          if($check == 0) {
           $them = $this->core->Data("Get", ["mbr", $value]);
           $them = $t["Login"]["Username"] ?? "";
           if($member == $them) {
            $check++;
           }
          }
         } if($check == 1) {
          $this->core->SendBulletin([
           "Data" => [
            "Invoice" => $id,
            "Shop" => $shopID
           ],
           "To" => $member,
           "Type" => "InvoiceForward"
          ]);
         }
         $bulletin = "<em>$member</em> will receive a Bulletin shortly.";
        }
        $_Dialog = [
         "Body" => "The Invoice has been forwarded to $email.$bulletin",
         "Header" => "Forwarded"
        ];
       }
       $_Success = "CloseCard";
      } elseif($isNote == 1) {
       $_AccessCode = "Accepted";
       $invoice = $this->core->Data("Get", ["invoice", $id]);
       $albums = [];
       $albumsData = $data["Album"] ?? [];
       $articles = [];
       $articlesData = $data["Article"] ?? [];
       $attachments = [];
       $attachmentsData = $data["Attachment"] ?? [];
       $blogs = [];
       $blogsData = $data["Blog"] ?? [];
       $blogPosts = [];
       $blogPostsData = $data["BlogPost"] ?? [];
       $chats = [];
       $chatsData = $data["Chat"] ?? [];
       $forums = [];
       $forumsData = $data["Forum"] ?? [];
       $forumPosts = [];
       $forumPostsData = $data["ForumPost"] ?? [];
       $members = []; 
       $membersData = $data["Member"] ?? [];
       $notes = $invoice["Notes"] ?? [];
       $polls = []; 
       $pollsData = $data["Poll"] ?? [];
       $products = [];
       $productsData = $data["Product"] ?? [];
       $updates = [];
       $updatesData = $data["Update"] ?? [];
       if(!empty($albumsData)) {
        $media = $albumsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($albums, $media[$i]);
         }
        }
       } if(!empty($articlesData)) {
        $media = $articlesData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($articles, $media[$i]);
         }
        }
       } if(!empty($attachmentsData)) {
        $media = $attachmentsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($attachments, $media[$i]);
         }
        }
       } if(!empty($blogsData)) {
        $media = $blogsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($blogs, $media[$i]);
         }
        }
       } if(!empty($blogPostsData)) {
        $media = $blogPostsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($blogPosts, $media[$i]);
         }
        }
       } if(!empty($chatsData)) {
        $media = $chatsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($chats, $media[$i]);
         }
        }
       } if(!empty($forumsData)) {
        $media = $forumsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($forums, $media[$i]);
         }
        }
       } if(!empty($forumPostsData)) {
        $media = $forumPostsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($forumPosts, $media[$i]);
         }
        }
       } if(!empty($membersData)) {
        $media = $membersData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($members, $media[$i]);
         }
        }
       } if(!empty($pollsData)) {
        $media = $pollsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($polls, $media[$i]);
         }
        }
       } if(!empty($productsData)) {
        $media = $productsData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($products, $media[$i]);
         }
        }
       } if(!empty($updatesData)) {
        $media = $updatesData;
        for($i = 0; $i < count($media); $i++) {
         if(!empty($media[$i])) {
          array_push($updates, $media[$i]);
         }
        }
       }
       array_push($notes, [
        "Albums" => $albums,
        "Articles" => $articles,
        "Attachments" => $attachments,
        "Blogs" => $blogs,
        "BlogPosts" => $blogPosts,
        "Chats" => $chats,
        "Created" => $this->core->timestamp,
        "Forums" => $forums,
        "ForumPosts" => $forumPosts,
        "Members" => $members,
        "Note" => $data["InvoiceNote"],
        "Polls" => $polls,
        "Products" => $products,
        "Updates" => $updates
       ]);
       $invoice["Notes"] = $notes;
       $this->core->Data("Save", ["invoice", $id, $invoice]);
       $_Dialog = [
        "Body" => "Your note has been added to the Invoice.",
        "Header" => "Done"
       ];
      } elseif(!empty($title) && $isPreset == 1) {
       $_AccessCode = "Accepted";
       $description = $data["ChargeDescription"][0] ?? "Unknown";
       $service = $this->core->Data("Get", ["invoice-preset", $id]);
       $serviceTitle = $title ?? "New Service";
       $title = $data["ChargeTitle"][0] ?? "Unknown";
       $value = $data["ChargeValue"][0] ?? 0.00;
       $service = [
        "Charges" => [
         "Description" => $description,
         "Paid" => 0,
         "Title" => $title,
         "Value" => $value
        ],
        "Notes" => [],
        "PaidInFull" => 0,
        "Shop" => $shopID,
        "Status" => "Open",
        "Title" => $serviceTitle,
        "UN" => $you
       ];
       $services = $shop["InvoicePresets"] ?? [];
       array_push($services, $id);
       $services = array_unique($services);
       $shop["InvoicePresets"] = $services;
       $this->core->Data("Save", ["invoice-preset", $id, $service]);
       $this->core->Data("Save", ["shop", $shopID, $shop]);
       $_View = "Update Pre-set".json_encode($service, true);
       $_ResponseType = "UpdateText";
      } elseif($isPreset == 0) {
       $_Dialog = [
        "Body" => "We could not find the Member <strong>$member</strong>."
       ];
       $check = 0;
       $member = $data["ChargeTo"] ?? "";
       $members = $this->core->DatabaseSet("Member");
       foreach($members as $key => $value) {
        $value = str_replace("nyc.outerhaven.mbr.", "", $value);
        if($check == 0) {
         $them = $this->core->Data("Get", ["mbr", $value]);
         $them = $t["Login"]["Username"] ?? "";
         if(!empty($them) && $member == $them) {
          $check++;
         }
        }
       } if((!empty($member) && $check == 1) || $check == 0) {
        $_Dialog = [
         "Body" => "An e-mail address is required in order for us ensure your Invoice is sent to the proper recipient."
        ];
        if(!empty($data["Email"])) {
         $_AccessCode = "Accepted";
         $chargeData = $data["ChargeTitle"] ?? 0;
         $charges = [];
         for($i = 0; $i < count($chargeData); $i++) {
          $description = $data["ChargeDescription"][$i] ?? "Unknown";
          $paid = $data["ChargePaid"][$i] ?? 0;
          $title = $data["ChargeTitle"][$i] ?? "Unknown";
          $value = $data["ChargeValue"][$i] ?? 0.00;
          array_push($charges, [
           "Description" => $description,
           "Paid" => $paid,
           "Title" => $title,
           "Value" => $value
          ]);
          $chargeList .= $this->core->Change([[
           "[Invoice.Charge.Description]" => $description,
           "[Invoice.Charge.Title]" => $title,
           "[Invoice.Charge.Value]" => $this->core->Element([
            "p", "$".number_format($value, 2),
            ["class" => "DesktopRightText"]
           ])
          ], $this->core->Extension("7a421d1b6fd3b4958838e853ae492588")]);
         }
         $invoice = [
          "ChargeTo" => $member,
          "Created" => $this->core->timestamp,
          "Charges" => $charges,
          "Email" => $data["Email"],
          "Notes" => [],
          "PaidInFull" => 0,
          "Phone" => $data["Phone"],
          "Shop" => $shopID,
          "Status" => "Open",
          "UN" => $you
         ];
         $invoices = $shop["Invoices"] ?? [];
         array_push($invoices, $id);
         $invoices = array_unique($invoices);
         $name = $data["ChargeTo"] ?? $data["Email"];
         $shop["Invoices"] = $invoices;
         if(!empty($data["Email"])) {
          $this->core->SendEmail([
           "Message" => $this->core->Change([[
            "[Mail.Message]" => "Please review the Invoice linked below.",
            "[Mail.Invoice]" => $chargeList,
            "[Mail.Name]" => $name,
            "[Mail.Link]" => $this->core->base."/invoice/$id",
            "[Mail.Shop.Name]" => $shop["Title"],
            "[Mail.View]" => $this->core->Element([
             "a", "View Invoice", [
              "class" => "BB BBB v2 v2w",
              "href" => $this->core->base."/invoice/$id"
             ]
            ])
           ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
           "Title" => $shop["Title"].": Invoice $id",
           "To" => $data["Email"]
          ]);
         } if(!empty($member)) {
          $this->core->SendBulletin([
           "Data" => [
            "Invoice" => $id,
            "Shop" => $shopID
           ],
           "To" => $member,
           "Type" => "Invoice"
          ]);
         }
         $this->core->Statistic("New Invoice");
         $this->core->Data("Save", ["invoice", $id, $invoice]);
         $this->core->Data("Save", ["shop", $shopID, $shop]);
         $_Dialog = [
          "Body" => "The Invoice $id has been saved and forwarded to the recipient. You may view this Invoice at ".$this->core->base."/invoice/$id.",
          "Header" => "Done"
         ];
         $_Success = "CloseCard";
        }
       }
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "Success" => $_Success,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class Invoice extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Invoice or Pre-set Identifier are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $accessCode = "Accepted";
    $charges = [];
    $isPreset = $data["Preset"] ?? 0;
    if($isPreset == 1) {
     $preset = $this->core->Data("Get", ["invoice-preset", $id]) ?? [];
     $changeData = [
      "[Invoice.Charges]" => json_encode($charges, true)
     ];
     $template = "UpdatePreset";
    } else {
     $id = ($new == 1) ? md5("Invoice$you".uniqid()) : $id;
     $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
     $invoice = $this->core->FixMissing($invoice, [
      "ChargeTo",
      "Email",
      "Phone"
     ]);
     if($new == 1) {
      $charges = [
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
        "Value" => base64_encode("50.00")
       ]
      ];
     } else {
      $invoiceCharges = $invoice["Charges"] ?? [];
      for($i = 0; $i < count($invoiceCharges); $i++) {
       $title = [
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
        "Value" => base64_encode($invoiceCharges["ChargeTitle"][$i])
       ];
       $description = [
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
        "Value" => base64_encode($invoiceCharges["ChargeDescription"][$i])
       ];
       $value = [
        "Attributes" => [
         "class" => "CheckIfNumeric req",
         "data-symbols" => "Y",
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
        "Value" => base64_encode($invoiceCharges["ChargeValue"][$i])
       ];
       array_push($charges, $title);
       array_push($charges, $description);
       array_push($charges, $value);
      }
     }
     $back = ($new == 1) ? $this->core->Element(["button", "Back", [
      "class" => "GoToParent v2 v2w",
      "data-type" => "ProductEditors"
     ]]) : "&nbsp;";
     $username = $invoice["UN"] ?? $you;
     $changeData = [
      "[Invoice.Back]" => $back,
      "[Invoice.Charges]" => json_encode($charges, true),
      "[Invoice.ChargeTo]" => base64_encode($invoice["ChargeTo"]),
      "[Invoice.Email]" => base64_encode($invoice["Email"]),
      "[Invoice.ID]" => $id,
      "[Invoice.New]" => $new,
      "[Invoice.Phone]" => base64_encode($invoice["Phone"]),
      "[Invoice.Save]" => base64_encode("v=".base64_encode("Invoice:Save")),
      "[Invoice.Username]" => $username
     ];
     $template = ($new == 1) ? "e372b28484951c22fe9920317c852436" : "AddCharges";
    }
    $r = $this->core->Change([
     $changeData,
     $this->core->Page($template)
    ]);
    $r = ($card == 1) ? [
     "Front" => $r
    ] : $r;
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
  function Hire(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $accessCode = "Accepted";
    // BEGIN TPL
    $r = $this->core->Element([
     "h1", "Hire"
    ]).$this->core->Element([
     "p", "A new experience is coming soon..."
    ]);
    // END TPL
    $r = $this->core->Change([[
    ], $r]);
    #], $this->core->Page("Hire")]);
    $r = [
     "Front" => $r
    ];
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
  function Home(array $a) {
   $_ViewTitle = $this->core->config["App"]["Name"];
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The Invoice Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_ViewTitle = "Invoice $id";
    $accessCode = "Accepted";
    // Allows you to view invoice via the Card
    // Allows client to view the invoice via DOMAIN/invoice/$id
    $r = $this->core->Element([
     "h1", "Invoice $id"
    ]).$this->core->Element([
     "p", "We are working on this, the Invoice Identifier is $id."
    ]);
   }
   if($pub == 1) {
    if($this->core->ID == $you) {
     $r = $this->view(base64_encode("WebUI:OptIn"), []);
     $r = $this->core->RenderView($r);
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
    "ResponseType" => "View",
    "Title" => $_ViewTitle
   ]);
  }
  function RefundCharge(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $charge = $data["Charge"] ?? "";
   $invoice = $data["Invoice"] ?? "";
   $r = [
    "Body" => "The Charge or Invoice Identifier are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($charge) && !empty($invoice)) {
    // CHECK IF YOU CREATED THE INVOICE OR ARE A SHOP CONTRIBUTOR
    $accessCode = "Accepted";
    $r = [
     "Body" => "Refund for charge $charge on Invoice $invoice."
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
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Invoice or Pre-set Identifier are missing."
   ];
   $responseType = "Dialog";
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $charges = [];
    $isPreset = $data["Preset"] ?? 0;
    $r = [
     "Body" => "The Service Title is missing."
    ];
    $title = $data["Title"] ?? "";
    if(!empty($title) && $isPreset == 1) {
     $accessCode = "Accepted";
     $description = $data["ChargeDescription"][0] ?? "Unknown";
     $serviceTitle = $title ?? "New Service";
     $title = $data["ChargeTitle"][0] ?? "Unknown";
     $value = $data["ChargeValue"][0] ?? 0.00;
     array_push($charges, [
      "Description" => $description,
      "Paid" => 0,
      "Title" => $title,
      "Value" => $value
     ]);
     $r = "Update Pre-set<br/>Data Model: ".json_encode([
      "Charges" => $charges,
      "Notes" => [],
      "PaidInFull" => 0,
      "Status" => "Open",
      "Title" => $serviceTitle,
      "UN" => $you
     ], true);
     $responseType = "UpdateText";
    } elseif($isPreset == 0) {
     $check = 0;
     $member = $data["ChargeTo"] ?? "";
     $members = $this->core->DatabaseSet("MBR");
     $r = [
      "Body" => "We could not find the Member <strong>$member</strong>."
     ];
     foreach($members as $key => $value) {
      $value = str_replace("c.oh.mbr.", "", $value);
      if($check == 0) {
       $t = $this->core->Data("Get", ["mbr", $value]) ?? [];
       if($member == $t["Login"]["Username"]) {
        $check++;
       }
      }
     } if((!empty($member) && $check == 1) || $check == 0) {
      $accessCode = "Accepted";
      $r = [
       "Body" => "New invoice processor under construction.",
       "Header" => "Done"
      ];
      $success = "CloseCard";
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType,
    "Success" => $success
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class Invoice extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Add(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $invoice = $data["Invoice"] ?? "";
   $r = [
    "Body" => "The Charge or Invoice Identifier are missing."
   ];
   $shopID = $data["Shop"] ?? "";
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($invoice) && !empty($type)) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $r = [
      "Body" => "You are not authorized to add a $type.",
      "Header" => "Forbidden"
     ];
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      if($type == "Charge") {} elseif($type == "Note") {}
      $accessCode = "Accepted";
      $r = [
       "Body" => "Add a new $type to Invoice $invoice."
      ];
     }
    }
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
  function DeletePreset(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $preset = $data["Preset"] ?? "";
   $r = [
    "Body" => "The Charge or Invoice Identifier are missing."
   ];
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($preset)) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $r = [
      "Body" => "You are not authorized to delete Pre-sets.",
      "Header" => "Forbidden"
     ];
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      $accessCode = "Accepted";
      $r = [
       "Body" => "Delete Pre-Set $preset."
      ];
     }
    }
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
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? "";
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Invoice or Pre-set Identifier are missing."
   ];
   $shop = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shop)) {
     $accessCode = "Accepted";
     $charges = [];
     $isPreset = $data["Preset"] ?? 0;
     if($isPreset == 1) {
      $preset = $this->core->Data("Get", ["invoice-preset", $id]) ?? [];
      $changeData = [
       "[Invoice.Charges]" => json_encode($charges, true),
       "[Invoice.Shop]" => $shop
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
         "Value" => base64_encode($invoiceCharges["Title"][$i])
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
         "Value" => base64_encode($invoiceCharges["Description"][$i])
        ];
        $paid = [
         "Attributes" => [
          "name" => "ChargePaid[]",
          "type" => "hidden"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Amount"
         ],
         "Type" => "Text",
         "Value" => base64_encode($invoiceCharges["Paid"][$i])
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
         "Value" => base64_encode($invoiceCharges["Value"][$i])
        ];
        array_push($charges, $title);
        array_push($charges, $description);
        array_push($charges, $paid);
        array_push($charges, $value);
       }
      }
      $back = ($new == 1) ? $this->core->Element(["button", "Back", [
       "class" => "GoToParent v2 v2w",
       "data-type" => "ProductEditors"
      ]]) : "&nbsp;";
      $username = $invoice["UN"] ?? $you;
      $changeData = [
       "[Invoice.ChargeClone]" => base64_encode($this->core->Page("cfc6f5b795f1254de32ef292325292a6")),
       "[Invoice.Back]" => $back,
       "[Invoice.Charges]" => json_encode($charges, true),
       "[Invoice.ChargeTo]" => base64_encode($invoice["ChargeTo"]),
       "[Invoice.Email]" => base64_encode($invoice["Email"]),
       "[Invoice.ID]" => $id,
       "[Invoice.Phone]" => base64_encode($invoice["Phone"]),
       "[Invoice.Save]" => base64_encode("v=".base64_encode("Invoice:Save")),
       "[Invoice.Shop]" => $shop,
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
  function Forward(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
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
   } elseif(!empty($invoice) && !empty($type)) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $r = [
      "Body" => "You are not authorized to forward this Invoice.",
      "Header" => "Forbidden"
     ];
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      $accessCode = "Accepted";
      $r = [
       "Body" => "Invoice forwarder under construction."
      ];
     }
    }
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
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($charge) && !empty($invoice)) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $r = [
      "Body" => "You are not authorized to add a $type.",
      "Header" => "Forbidden"
     ];
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      $accessCode = "Accepted";
      $r = [
       "Body" => "Refund for charge $charge on Invoice $invoice."
      ];
     }
    }
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
   $data = $this->core->FixMissing($data, [
    "Phone",
    "Username"
   ]);
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Invoice or Pre-set Identifier are missing."
   ];
   $responseType = "Dialog";
   $shopID = $data["Shop"] ?? "";
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id) || $new == 1) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $r = [
      "Body" => "You are not authorized to manage Invoices.",
      "Header" => "Forbidden"
     ];
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      $charges = [];
      $isPreset = $data["Preset"] ?? 0;
      $r = [
       "Body" => "The Service Title is missing."
      ];
      $title = $data["Title"] ?? "";
      if(!empty($title) && $isPreset == 1) {
       $accessCode = "Accepted";
       $description = $data["ChargeDescription"][0] ?? "Unknown";
       $service = $this->core->Data("Get", ["invoice-preset", $id]) ?? [];
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
       $this->core->Data("Save", ["invoice-preset", $id, $service]);
       $this->core->Data("Save", ["shop", $shopID, $shop]);
       $r = "Update Pre-set";
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
        $r = [
         "Body" => "An e-mail address is required in order for us ensure your Invoice is sent to the proper recipient."
        ];
        if(!empty($data["Email"])) {
         $accessCode = "Accepted";
         $chargeCount = count($data["ChargeTitle"]);
         $charges = [];
         for($i = 0; $i < $chargeCount; $i++) {
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
         #$this->core->Data("Save", ["invoice", $id, $invoice]);
         #$this->core->Data("Save", ["shop", $shopID, $shop]);
         if(!empty($data["Email"])) {
          $this->core->SendEmail([
           "Message" => $this->core->Change([[
            "[Email.Header]" => "{email_header}",
            "[Email.Message]" => "Please review the Invoice linked below.",
            "[Email.Name]" => $name,
            "[Email.Link]" => $this->core->base."/invoice/$id"
           ], $this->core->Page("dc901043662c5e71b5a707af782fdbc1")]),
           "Title" => "Invoice $id",
           "To" => $data["Email"]
          ]);
         } if(!empty($member)) {
          # Forward via Bulletin
         }
         $r = [
          "Body" => "The Invoice $id has been saved and forwarded to the recipient. You may view this Invoice at ".$this->core->base."/invoice/$id.",
          "Scrollable" => json_encode([
           $invoice,
           $invoices
          ], true),
          "Header" => "Done"
         ];
         #$success = "CloseCard";
        }
       }
      }
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
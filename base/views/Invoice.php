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
    "Body" => "The Invoice Identifier is missing."
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
      $r = [
       "Body" => "The content type is missing."
      ];
      if($type == "Charge") {
       $accessCode = "Accepted";
       $r = $this->core->Element([
        "h1", "Add $type"
       ]);
      } elseif($type == "Note") {
       $accessCode = "Accepted";
       $viewNotes = $data["ViewNotes"] ?? 0;
       if($viewNotes == 1) {
        # Fetch Notes list from here.
       } else {
        $r = $this->core->Element([
         "h1", "Add $type"
        ]);
       }
      }
      $r = [
       "Front" => $r
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
    "Body" => "The Invoice Identifier are missing."
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
   $isPreset = $data["Preset"] ?? 0;
   $new = $data["new"] ?? 0;
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $shop = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($shop)) {
    $accessCode = "Accepted";
    $charges = [];
    if(!empty($id) && $isPreset == 1) {
     $preset = $this->core->Data("Get", ["invoice-preset", $id]) ?? [];
     $changeData = [
      "[Invoice.Charges]" => json_encode($charges, true),
      "[Invoice.Shop]" => $shop
     ];
     $template = "UpdatePreset";
    } elseif($isPreset == 0) {
     $id = md5("Invoice$you".uniqid());
     $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
     $invoice = $this->core->FixMissing($invoice, [
      "ChargeTo",
      "Email",
      "Phone"
     ]);
     $changeData = [
      "[Invoice.ChargeClone]" => base64_encode($this->core->Page("cfc6f5b795f1254de32ef292325292a6")),
      "[Invoice.Charges]" => json_encode([
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
        "Value" => base64_encode(50.00)
       ]
      ], true),
      "[Invoice.ChargeTo]" => base64_encode($invoice["ChargeTo"]),
      "[Invoice.Email]" => base64_encode($invoice["Email"]),
      "[Invoice.ID]" => $id,
      "[Invoice.Phone]" => base64_encode($invoice["Phone"]),
      "[Invoice.Save]" => base64_encode("v=".base64_encode("Invoice:Save")),
      "[Invoice.Shop]" => $shop,
      "[Invoice.Username]" => $you
     ];
     $template = "e372b28484951c22fe9920317c852436";
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
  function Forward(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["Invoice"] ?? "";
   $r = [
    "Body" => "The Invoice Identifier is missing."
   ];
   $shopID = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $accessCode = "Accepted";
     $action = $this->core->Element(["button", "Forward", [
      "class" => "CardButton SendData",
      "data-form" => ".ForwardInvoice$id",
      "data-processor" => base64_encode("v=".base64_encode("Invoice:Save"))
     ]]);
     $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
     $r = $this->core->Change([[
      "[Invoice.ID]" => $id,
      "[Invoice.Shop]" => $invoice["Shop"]
     ], $this->core->Page("bef71930eb3342a550ba9e8a971cebe2")]);
     $r = [
      "Action" => $action,
      "Front" => $r
     ];
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
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? "";
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The Invoice Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_ViewTitle = "Invoice $id";
    $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
    $r = [
     "Body" => "We could not find any data for Invoice $id."
    ];
    if(!empty($invoice)) {
     $_Shop = $this->core->Data("Get", ["shop", $invoice["Shop"]]) ?? [];
     $_ViewTitle = "Invoice from ".$_Shop["Title"];
     $accessCode = "Accepted";
     $dependency = $data["Dependency"] ?? "";
     $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
     if($dependency == "Charges") {
      $check = 0;
      $isAdmin = ($invoice["Shop"] == md5($you)) ? 1 : 0;
      $shop = $this->core->Data("Get", ["shop", $invoice["Shop"]]) ?? [];
      foreach($shop["Contributors"] as $member => $role) {
       if($check == 0 && $member == $you) {
        $check++;
       }
      }
      $check = ($check == 1 || $isAdmin == 1) ? 1 : 0;
      $charges = $invoice["Charges"] ?? [];
      $r = "";
      foreach($charges as $key => $charge) {
       $description = $charge["Description"] ?? "Unknown";
       $paid = $charge["Paid"] ?? 0;
       $title = $charge["Title"] ?? "Unknown";
       $value = $charge["Value"] ?? 0.00;
       if($invoice["UN"] != $you) {//TEMP
       #if($invoice["UN"] == $you) {
        $value = $this->core->Element([
         "p", "$$value",
         ["class" => "DesktopRightText"]
        ]);
       } else {
        $value = ($paid == 1) ? $this->core->Element([
         "p", "$".number_format($value, 2),
         ["class" => "DesktopRightText"]
        ]) : $this->core->Element([
         "button", "$$value",
         [
          "class" => "DesktopRight OpenFirSTEPTool v2",
          "data-fst" => base64_encode("v=".base64_encode("Shop:Pay")."&Charge=$key&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Invoice")
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
       $r .= $this->core->Change([[
        "[Invoice.Charge.Description]" => $description,
        "[Invoice.Charge.Title]" => $title,
        "[Invoice.Charge.Value]" => $value
       ], $this->core->Page("7a421d1b6fd3b4958838e853ae492588")]);
      }
     } elseif($dependency == "Options") {
      $check = 0;
      $isAdmin = ($invoice["Shop"] == md5($you)) ? 1 : 0;
      $shop = $this->core->Data("Get", ["shop", $invoice["Shop"]]) ?? [];
      foreach($shop["Contributors"] as $member => $role) {
       if($check == 0 && $member == $you) {
        $check++;
       }
      }
      $r = ($check == 1 && $isAdmin == 1) ? $this->core->Element([
       "button", "Add Charge", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Add")."&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Charge")
       ]
      ]).$this->core->Element([
       "button", "Notes", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Add")."&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Note")
       ]
      ]) : "";
      $r .= $this->core->Element(["button", "Forward", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Forward")."&Invoice=$id&Shop=".$invoice["Shop"])
      ]]);
     } elseif($dependency == "Status") {
      $status = $invoice["Status"] ?? "Closed";
      $action = "No action needed.";
      $action = ($status == "Closed") ? "This Invoice has been paid in full. $action" : $action;
      $action = ($status == "Open") ? "This invoice is active and subject to change. You may make partial payments where necessary." : $action;
      $action = ($status == "ReadyForPayment") ? "Make any necessary payments or contact the merchant for further assistance." : $action;
      $status = ($status == "Open") ? "Open" : $status;
      $status = ($status == "ReadyForPayment") ? "Ready for Payment" : $status;
      $r = $this->core->Element([
       "h4", "Invoice ID: $id", ["class" => "CenterText"]
      ]).$this->core->Element([
       "p", "<em>$status</em>", ["class" => "CenterText"]
      ]).$this->core->Element([
       "p", $action, ["class" => "CenterText"]
      ]);
     } elseif($dependency == "Total") {
      # Displays: Subtotal, (- Total Paid), Taxes, and Remaining Balance
      $balance = 0;
      $paid = 0;
      $taxes = 0;
      $subtotal = 0;
      // LOGIC
      #$total = number_format($balance, 2);
     } else {
      $home = "v=".base64_encode("Invoice:Home")."&ID=$id&Shop=".$invoice["Shop"];
      $r = $this->core->Change([[
       "[Invoice.Charges]" => base64_encode("$home&Dependency=Charges"),
       "[Invoice.ID]" => $id,
       "[Invoice.Options]" => base64_encode("$home&Dependency=Options"),
       "[Invoice.Status]" => base64_encode("$home&Dependency=Status"),
       "[Invoice.Total]" => base64_encode("$home&Dependency=Total")
      ], $this->core->Page("4a78b78f1ebff90e04a33b52fb5c5e97")]);
     }
    }
   } if($card == 1) {
    $r = [
     "Front" => $r
    ];
   } elseif($pub == 1) {
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
  function Refund(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $charge = $data["Charge"] ?? "";
   $id = $data["Invoice"] ?? "";
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
   } elseif((!empty($charge) || $charge == 0) && !empty($id)) {
    $r = [
     "Body" => "The Shop Identifier is missing."
    ];
    if(!empty($shopID)) {
     $check = 0;
     $isAdmin = ($shopID == md5($you)) ? 1 : 0;
     $r = [
      "Body" => "You are not authorized to refund charges.",
      "Header" => "Forbidden"
     ];
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     foreach($shop["Contributors"] as $member => $role) {
      if($check == 0 && $member == $you) {
       $check++;
      }
     } if($check == 1 && $isAdmin == 1) {
      $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
      $check = $invoice["Charges"][$charge]["Paid"];
      $r = [
       "Body" => "A refund was already issued for <em>".$invoice["Charges"][$charge]["Title"]."</em>."
      ];
      if($check == 0) {
       $accessCode = "Accepted";
       $chargeList = "";
       $charges = $invoice["Charges"] ?? [];
       $name = $invoice["ChargeTo"] ?? $invoice["Email"];
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
        ], $this->core->Page("7a421d1b6fd3b4958838e853ae492588")]);
       }
       $this->core->SendEmail([
        "Message" => $this->core->Change([[
         "[Email.Header]" => "{email_header}",
         "[Email.Message]" => $y["Personal"]["DisplayName"]." refunded the <em>".$newCharge["Title"]."</em> charge.",
         "[Email.Invoice]" => $chargeList,
         "[Email.Name]" => $name,
         "[Email.Link]" => $this->core->base."/invoice/$id",
         "[Email.Shop.Name]" => $shop["Title"],
         "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
        ], $this->core->Page("d13bb7e89f941b7805b68c1c276313d4")]),
        "Title" => $shop["Title"].": Invoice $id",
        "To" => $invoice["Email"]
       ]);
       if(!empty($invoice["ChargeTo"])) {
        $this->core->SendBulletin([
         "Data" => [
          "Invoice" => $id,
          "Shop" => $invoice["Shop"]
         ],
         "To" => $name,
         "Type" => "InvoiceUpdate"
        ]);
       } if($total == 0) {
        $invoice["PaidInFull"] = 1;
        $invoice["Status"] = "Closed";
       }
       $this->core->Data("Save", ["invoice", $id, $invoice]);
       $r = [
        "Body" => "Refund for <em>".$newCharge["Title"]."</em> issued. We will send a confirmation to <em>".$invoice["Email"]."</em>.",
        "Header" => "Done"
       ];
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
      $chargeList = "";
      $charges = [];
      $isCharge = $data["Charge"] ?? 0;
      $isNote = $data["Note"] ?? 0;
      $isForwarding = $data["Forward"] ?? 0;
      $isPreset = $data["Preset"] ?? 0;
      $r = [
       "Body" => "The Service Title is missing."
      ];
      $title = $data["Title"] ?? "";
      if($isCharge == 1) {
       $accessCode = "Accepted";
       $r = [
        "Body" => "Soon you may add charges to the Invoice.",
        "Header" => "Charge Added"
       ];
      } elseif($isForwarding == 1) {
       $email = $data["Email"] ?? "";
       $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
       $member = $data["Username"] ?? "";
       $name = $member ?? $email;
       $r = [
        "Body" => "An e-mail address or username are required."
       ];
       if(!empty($email) || !empty($member)) {
        $accessCode = "Accepted";
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
         ], $this->core->Page("7a421d1b6fd3b4958838e853ae492588")]);
        } if(!empty($email)) {
         $this->core->SendEmail([
          "Message" => $this->core->Change([[
           "[Email.Header]" => "{email_header}",
           "[Email.Message]" => $y["Personal"]["DisplayName"]." forwarded this Inovice to you.",
           "[Email.Invoice]" => $chargeList,
           "[Email.Name]" => $name,
           "[Email.Link]" => $this->core->base."/invoice/$id",
           "[Email.Shop.Name]" => $shop["Title"],
           "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
          ], $this->core->Page("d13bb7e89f941b7805b68c1c276313d4")]),
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
          "Type" => "InvoiceForward"
         ]);
         $bulletin = " If <em>$member</em> exists, they should receive this Invoice shortly.";
        }
        $r = [
         "Body" => "The Invoice has been forwarded to $name.$bulletin",
         "Header" => "Forwarded"
        ];
       }
       $success = "CloseCard";
      } elseif($isNote == 1) {
       $accessCode = "Accepted";
       $r = [
        "Body" => "Soon you may add notes to the Invoice.",
        "Header" => "Note Added"
       ];
      } elseif(!empty($title) && $isPreset == 1) {
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
       $shop["InvoicePresets"] = $services;
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
          $chargeList .= $this->core->Change([[
           "[Invoice.Charge.Description]" => $description,
           "[Invoice.Charge.Title]" => $title,
           "[Invoice.Charge.Value]" => $this->core->Element([
            "p", "$".number_format($value, 2),
            ["class" => "DesktopRightText"]
           ])
          ], $this->core->Page("7a421d1b6fd3b4958838e853ae492588")]);
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
            "[Email.Header]" => "{email_header}",
            "[Email.Message]" => "Please review the Invoice linked below.",
            "[Email.Invoice]" => $chargeList,
            "[Email.Name]" => $name,
            "[Email.Link]" => $this->core->base."/invoice/$id",
            "[Email.Shop.Name]" => $shop["Title"],
            "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
           ], $this->core->Page("d13bb7e89f941b7805b68c1c276313d4")]),
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
         $this->core->Statistic("NewInvoice");
         $this->core->Data("Save", ["invoice", $id, $invoice]);
         $this->core->Data("Save", ["shop", $shopID, $shop]);
         $r = [
          "Body" => "The Invoice $id has been saved and forwarded to the recipient. You may view this Invoice at ".$this->core->base."/invoice/$id.",
          "Header" => "Done"
         ];
         $success = "CloseCard";
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
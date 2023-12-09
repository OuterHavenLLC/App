<?php
 Class Invoice extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Add(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["Invoice"] ?? "";
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
   } elseif(!empty($id) && !empty($type)) {
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
       $viewCharges = $data["ViewCharges"] ?? 0;
       if($viewCharges == 1) {
        $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
        $chargeList = "";
        $charges = $invoice["Charges"] ?? [];
        $r = $this->core->Element(["h4", "No Charges", [
         "class" => "CenterText UpperCase"
        ]]);
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
        } if(!empty($chargeList)) {
         $r = $chargeList;
        }
       } else {
        $r = $this->core->Change([[
         "[Invoice.ChargeClone]" => base64_encode($this->core->Extension("cfc6f5b795f1254de32ef292325292a6")),
         "[Invoice.Charges]" => base64_encode("v=".base64_encode("Invoice:Add")."&Invoice=$id&Shop=$shopID&Type=Charge&ViewCharges=1"),
         "[Invoice.ID]" => $id,
         "[Invoice.Save]" => base64_encode("v=".base64_encode("Invoice:Save")),
         "[Invoice.Shop]" => $shopID
        ], $this->core->Extension("60fe8170fa7a51cdd75097855c74a95c")]);
       }
      } elseif($type == "Note") {
       $accessCode = "Accepted";
       $viewNotes = $data["ViewNotes"] ?? 0;
       if($viewNotes == 1) {
        $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
        $noteList = "";
        $notes = $invoice["Notes"] ?? [];
        $notes = array_reverse($notes);
        $r = $this->core->Element(["h4", "No Notes", [
         "class" => "CenterText UpperCase"
        ]]);
        foreach($notes as $key => $note) {
         $noteList .= $this->core->Element([
          "h4", $note["Created"]
         ]).$this->core->Element([
          "p", $note["Note"]
         ]);
        } if(!empty($noteList)) {
         $r = $noteList;
        }
       } else {
        $r = $this->core->Change([[
         "[Invoice.ID]" => $id,
         "[Invoice.Notes]" => base64_encode("v=".base64_encode("Invoice:Add")."&Invoice=$id&Shop=$shopID&Type=Note&ViewNotes=1"),
         "[Invoice.Save]" => base64_encode("v=".base64_encode("Invoice:Save")),
         "[Invoice.Shop]" => $shopID
        ], $this->core->Extension("82e29a8d9c5737b07a4db0a1de45c7db")]);
       }
      }
      $r = ($card == 1) ? [
       "Front" => $r
      ] : $r;
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
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $pin = $data["PIN"] ?? "";
   $r = [
    "Body" => "The Shop-Service Identifier are missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($pin) != $y["Login"]["PIN"]) {
    $r = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($id)) {
    $check = 0;
    $combinedID = explode("-", base64_decode($id));
    $id = $combinedID[1];
    $shopID = $combinedID[0];
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
     $newPresets = [];
     $presets = $shop["InvoicePresets"] ?? [];
     foreach($presets as $key => $value) {
      if($value != $id) {
       $newPresets[$key] = $value;
      }
     }
     $preset = $this->core->Data("Get", [
      "invoice-preset",
      $id
     ]) ?? [];
     $shop["InvoicePresets"] = $newPresets;
     $this->core->Data("Purge", ["invoice-preset", $id]);
     $this->core->Data("Save", ["shop", $shopID, $shop]);
     $r = [
      "Body" => "The service <em>".$preset["Title"]."</em> was deleted.",
      "Header" => "Done"
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
    "Success" => "CloseDialog"
   ]);
  }
  function Edit(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? "";
   $isPreset = $data["Preset"] ?? 0;
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
      "[Invoice.ChargeClone]" => base64_encode($this->core->Extension("cfc6f5b795f1254de32ef292325292a6")),
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
     $this->core->Extension($template)
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
     ], $this->core->Extension("bef71930eb3342a550ba9e8a971cebe2")]);
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
   $_ViewTitle = $this->core->config["App"]["Name"];
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $card = $data["Card"] ?? 0;
   $id = $data["ID"] ?? md5($this->core->ShopID);
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The Shop Identifier is missing."
   ];
   $responseType = "Dialog";
   $shopID = $id;
   $success = "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_ViewTitle = "Hire";
    $accessCode = "Accepted";
    $action = "";
    $createJob = $data["CreateJob"] ?? 0;
    $saveJob = $data["SaveJob"] ?? 0;
    $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
    $enableHireSection = $shop["EnableHireSection"] ?? 0;
    $partners = $shop["Contributors"] ?? [];
    $services = $shop["InvoicePresets"] ?? 0;
    $hire = (md5($you) != $id) ? 1 : 0;
    $hire = (count($services) > 0 && $hire == 1) ? 1 : 0;
    $hire = ($enableHireSection == 1 && $hire == 1) ? 1 : 0;
    $limit = $shop["HireLimit"] ?? 5;
    $openInvoices = 0;
    $r = ($hire == 1 && $shop["Open"] == 1) ? $r : $this->core->Element([
     "h1", "Sorry!", ["class" => "CenterText UpperCase"]
    ]).$this->core->Element([
     "p", $shop["Title"]." is not currently accepting job offers.",
     ["class" => "CenterText"]
    ]);
    foreach($shop["Invoices"] as $key => $invoice) {
     $invoice = $this->core->Data("Get", ["invoice", $invoice]) ?? [];
     if($invoice["Status"] == "Open") {
      $openInvoices++;
     }
    } if($hire == 1 && $openInvoices < $limit && $shop["Open"] == 1) {
     if(!empty($saveJob)) {
      $data = $this->core->DecodeBridgeData($data);
      $saveJob = $data["SaveJob"] ?? 0;
      if($saveJob == 1) {
       $preset = $this->core->Data("Get", [
        "invoice-preset",
        $data["Service"]
       ]) ?? [];
       $chargeTo = $data["ChargeTo"] ?? "";
       $charges = [];
       array_push($charges, $preset["Charges"]);
       $id = md5("Invoice$you".uniqid());
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
       $success = "CloseCard";
       if(!empty($data["Email"])) {
        $this->core->SendEmail([
         "Message" => $this->core->Change([[
          "[Email.Header]" => $this->core->Extension("c790e0a597e171ff1d308f923cfc20c9"),
          "[Email.Message]" => "Your Service request has been sent! Please review the Invoice linked below and pay the requested deposit amount.",
          "[Email.Invoice]" => "Total due: $".number_format($preset["Charges"]["Value"], 2),
          "[Email.Name]" => $name,
          "[Email.Link]" => $this->core->base."/invoice/$id",
          "[Email.Shop.Name]" => $shop["Title"],
          "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
         ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
         "Title" => $shop["Title"].": Invoice $id",
         "To" => $data["Email"]
        ]);
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
          "[Email.Header]" => $this->core->Extension("c790e0a597e171ff1d308f923cfc20c9"),
          "[Email.Message]" => "<em>".$shop["Title"]."</em> has been hired by a potential client! Please verify payment of the deposit before proceeding with the service.",
          "[Email.Invoice]" => "Total due: $".number_format($preset["Charges"]["Value"], 2),
          "[Email.Name]" => $partner["Personal"]["FirstName"],
          "[Email.Link]" => $this->core->base."/invoice/$id",
          "[Email.Shop.Name]" => $shop["Title"],
          "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
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
       $r = [
        "Body" => "Your request has been submitted! Please check your email for the Invoice and pay the deposit amount. Your Invoice is also available at ".$this->core->base."/invoice/$id",
        "Header" => "Done"
       ];
      }
     } elseif($createJob == 1) {
      $action = (count($partners) == 1) ? "Me" : "Us";
      $action = $this->core->Element(["button", "Hire $action", [
       "class" => "CardButton SendData",
       "data-form" => ".Hire$id",
       "data-processor" => base64_encode("v=".base64_encode("Invoice:Hire"))
      ]]);
      $card = 1;
      $chargeTo = ($this->core->ID != $you) ? $you : "";
      $hireText = (count($partners) == 1) ? "Me" : "Us";
      $presets = [];
      foreach($services as $key => $value) {
       $service = $this->core->Data("Get", [
        "invoice-preset",
        $value
       ]) ?? [];
       $presets[$value] = $service["Title"];
      }
      $r = $this->core->Change([[
       "[Hire.ChargeTo]" => $chargeTo,
       "[Hire.Email]" => base64_encode($y["Personal"]["Email"]),
       "[Hire.Shop]" => $id,
       "[Hire.Text]" => $hireText,
       "[Hire.Services]" => json_encode($presets, true)
      ], $this->core->Extension("dab6e25feafcbb2741022bf6083c2975")]);
     } else {
      $_ViewTitle = "Hire ".$shop["Title"];
      $hireText = (count($partners) == 1) ? "Me" : "Us";
      $terms = $shop["HireTerms"] ?? "";
      if(!empty($terms)) {
       $terms = $this->core->PlainText([
        "Data" => $terms,
        "Display" => 1,
        "HTMLDecode" => 1
       ]);
      } else {
       $terms = $this->core->Extension("285adc3ef002c11dfe1af302f8812c3a");
      }
      $r = $this->core->Change([[
       "[Shop.Name]" => $shop["Title"],
       "[Shop.Hire]" => base64_encode("v=".base64_encode("Invoice:Hire")."&ID=$id&CreateJob=1"),
       "[Shop.Hire.Terms]" => $terms,
       "[Shop.Hire.Text]" => $hireText,
      ], $this->core->Extension("045f6c5cf3728bd31b0d9663498a940c")]);
     }
     $responseType = "View";
    }
    $r = ($card == 1) ? [
     "Action" => $action,
     "Front" => $r
    ] : $r;
    if($pub == 1) {
     $r = $this->view(base64_encode("WebUI:Containers"), [
      "Data" => ["Content" => $r]
     ]);
     $r = $this->core->RenderView($r);
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType,
    "Success" => $success,
    "Title" => $_ViewTitle
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
    $shop = $this->core->Data("Get", ["shop", $invoice["Shop"]]) ?? [];
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
       if($invoice["UN"] == $you) {
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
          "class" => "CloseCard DesktopRight OpenFirSTEPTool v2",
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
       ], $this->core->Extension("7a421d1b6fd3b4958838e853ae492588")]);
      }
     } elseif($dependency == "Options") {
      $check = 0;
      $isAdmin = ($invoice["Shop"] == md5($you)) ? 1 : 0;
      foreach($shop["Contributors"] as $member => $role) {
       if($check == 0 && $member == $you) {
        $check++;
       }
      }
      $r = ($check == 1 && $isAdmin == 1 && $invoice["Status"] == "Open") ? $this->core->Element([
       "button", "Charges", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Add")."&Card=1&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Charge")
       ]
      ]) : "";
      $r .= ($check == 1 && $isAdmin == 1) ? $this->core->Element([
       "button", "Notes", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Invoice:Add")."&Card=1&Invoice=$id&Shop=".$invoice["Shop"]."&Type=Note")
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
      $r = $this->core->Change([[
       "[Invoice.Balance]" => $balance,
       "[Invoice.Subtotal]" => number_format($subtotal, 2),
       "[Invoice.Taxes]" => $tax
      ], $this->core->Extension("6faa1179113386dad098302e12049b8b")]);
     } else {
      $home = "v=".base64_encode("Invoice:Home")."&ID=$id&Shop=".$invoice["Shop"];
      $r = $this->core->Change([[
       "[Invoice.Charges]" => base64_encode("$home&Dependency=Charges"),
       "[Invoice.ID]" => $id,
       "[Invoice.Options]" => base64_encode("$home&Dependency=Options"),
       "[Invoice.Status]" => base64_encode("$home&Dependency=Status"),
       "[Invoice.Total]" => base64_encode("$home&Dependency=Total")
      ], $this->core->Extension("4a78b78f1ebff90e04a33b52fb5c5e97")]);
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
         "[Email.Header]" => $this->core->Extension("c790e0a597e171ff1d308f923cfc20c9"),
         "[Email.Message]" => $y["Personal"]["DisplayName"]." refunded the <em>".$newCharge["Title"]."</em> charge.",
         "[Email.Invoice]" => $chargeList.$total,
         "[Email.Name]" => $name,
         "[Email.Link]" => $this->core->base."/invoice/$id",
         "[Email.Shop.Name]" => $shop["Title"],
         "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
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
   } elseif(!empty($id)) {
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
       $bulletin = "";
       $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
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
         "[Email.Header]" => $this->core->Extension("c790e0a597e171ff1d308f923cfc20c9"),
         "[Email.Message]" => "Your Invoice is ready for payment.",
         "[Email.Invoice]" => $chargeList.$total,
         "[Email.Name]" => $name,
         "[Email.Link]" => $this->core->base."/invoice/$id",
         "[Email.Shop.Name]" => $shop["Title"],
         "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
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
       $r = $this->core->Element([
        "h4", "Success!", ["class" => "CenterText UpperCase"]
       ]).$this->core->Element([
        "p", "Your Invoice has been updated.$bulletin",
        ["class" => "CenterText"]
       ]);
       $responseType = "ReplaceContent";
      } elseif($isForwarding == 1) {
       $email = $data["Email"] ?? "";
       $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
       $member = $data["Username"] ?? "";
       $name = $data["Username"] ?? $email;
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
           "[Email.Header]" => $this->core->Extension("c790e0a597e171ff1d308f923cfc20c9"),
           "[Email.Message]" => $y["Personal"]["DisplayName"]." forwarded this Inovice to you.",
           "[Email.Invoice]" => $chargeList.$total,
           "[Email.Name]" => $name,
           "[Email.Link]" => $this->core->base."/invoice/$id",
           "[Email.Shop.Name]" => $shop["Title"],
           "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
          ], $this->core->Extension("d13bb7e89f941b7805b68c1c276313d4")]),
          "Title" => $shop["Title"].": Invoice $id",
          "To" => $data["Email"]
         ]);
        } if(!empty($member)) {
         $check = 0;
         $members = $this->core->DatabaseSet("MBR");
         foreach($members as $key => $value) {
          $value = str_replace("nyc.outerhaven.mbr.", "", $value);
          if($check == 0) {
           $t = $this->core->Data("Get", ["mbr", $value]) ?? [];
           if($member == $t["Login"]["Username"]) {
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
        $r = [
         "Body" => "The Invoice has been forwarded to $name.$bulletin",
         "Header" => "Forwarded"
        ];
       }
       $success = "CloseCard";
      } elseif($isNote == 1) {
       $accessCode = "Accepted";
       $invoice = $this->core->Data("Get", ["invoice", $id]) ?? [];
       $notes = $invoice["Notes"] ?? [];
       array_push($notes, [
        "Created" => $this->core->timestamp,
        "Note" => $data["InvoiceNote"]
       ]);
       $invoice["Notes"] = $notes;
       $this->core->Data("Save", ["invoice", $id, $invoice]);
       $r = [
        "Body" => "Your note has been added to the Invoice.",
        "Header" => "Done"
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
        $value = str_replace("nyc.outerhaven.mbr.", "", $value);
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
            "[Email.Header]" => $this->core->Extension("c790e0a597e171ff1d308f923cfc20c9"),
            "[Email.Message]" => "Please review the Invoice linked below.",
            "[Email.Invoice]" => $chargeList,
            "[Email.Name]" => $name,
            "[Email.Link]" => $this->core->base."/invoice/$id",
            "[Email.Shop.Name]" => $shop["Title"],
            "[Email.View]" => "<button class=\"BBB v2 v2w\" onclick=\"window.location='".$this->core->base."/invoice/$id'\">View Invoice</button>",
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
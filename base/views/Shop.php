<?php
 Class Shop extends OH {
  function __construct() {
   parent::__construct();
   $this->root = $_SERVER["DOCUMENT_ROOT"]."/base/pay/Braintree.php";
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Banish(array $data): string {
   $_Dialog = [
    "Body" => "The Username is missing."
   ];
   $data = $data["Data"] ?? [];
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($username)) {
    $_Dialog = [
     "Body" => "You cannot fire yourself."
    ];
    $username = base64_decode($username);
    if($username != $you) {
     $_Dialog = [
      "Actions" => [
       $this->core->Element(["button", "Fire $username", [
        "class" => "BBB CloseDialog OpenDialog v2 v2w",
        "data-view" => base64_encode("v=".base64_encode("Shop:SaveBanish")."&UN=".$data["UN"])
       ]])
      ],
      "Body" => "You are about to fire $username. Are you sure?",
      "Header" => "Fire $username?"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function CompleteOrder(array $data): string {
   $_Dialog = [
    "Body" => "The Order Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? base64_encode("");
   $id = base64_decode($id);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $physicalOrders = $this->core->Data("Get", ["po", md5($you)]);
    $physicalOrders[$id]["Complete"] = 1;
    $this->core->Data("Save", ["po", md5($you), $physicalOrders]);
    $_Dialog = [
     "Body" => "The order has been marked as complete!",
     "Header" => "Done"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function Edit(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["Shop"] ?? base64_encode("");
   $id = base64_decode($id);
   $username = $data["Username"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = "";
    $shop = $this->core->Data("Get", ["shop", $id]);
    $owner = $shop["Contributors"] ?? [];
    $owner = array_key_first($owner);
    $shop = $this->core->FixMissing($shop, [
     "Description",
     "Live",
     "Open",
     "Title",
     "Welcome"
    ]);
    $adminExpenses = $shop["AdministrativeExpenses"] ?? [];
    $adminExpensesList = "";
    $albums = $shop["Albums"] ?? [];
    $articles = $shop["Articles"] ?? [];
    $attachments = $shop["Attachments"] ?? [];
    $blogs = $shop["Blogs"] ?? [];
    $blogPosts = $shop["BlogPosts"] ?? [];
    $chats = $shop["Chat"] ?? [];
    $coverPhoto = $shop["CoverPhoto"] ?? "";
    $enableHireSection = $shop["EnableHireSection"] ?? 0;
    $forums = $shop["Forums"] ?? [];
    $forumPosts = $shop["ForumPosts"] ?? [];
    $header = "Edit ".$shop["Title"];
    $hireLimit = $shop["HireLimit"] ?? 5;
    $hireLimits = [];
    for($i = 1; $i < 100; $i++) {
     $hireLimits[$i] = $i;
    }
    $hireTerms = $shop["HireTerms"] ?? $this->core->Extension("285adc3ef002c11dfe1af302f8812c3a");
    $members = $shop["Members"] ?? [];
    $nsfw = $shop["NSFW"] ?? $y["Privacy"]["NSFW"];
    $passPhrase = $shop["PassPhrase"] ?? "";
    $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
    $percentages = [];
    for($i = 1; $i < 100; $i++) {
     $percentages[$i] = "$i%";
    }
    $polls = $shop["Polls"] ?? [];
    $privacy = $shop["Privacy"] ?? $y["Privacy"]["Shop"];
    $processing = $shop["Processing"] ?? [];
    $processing = $this->core->FixMissing($processing, [
     "BraintreeMerchantIDLive",
     "BraintreePrivateKeyLive",
     "BraintreePublicKeyLive",
     "BraintreeTokenLive",
     "PayPalClientID",
     "PayPalClientIDLive",
     "PayPalEmailLive"
    ]);
    $search = base64_encode("Search:Containers");
    $shops = $shop["Shops"] ?? [];
    $tax = $shop["Tax"] ?? 10.00;
    $translate = $this->view(base64_encode("WebUI:Attachments"), [
     "ID" => $id,
     "Media" => [
      "Translate" => []
     ]
    ]);
    $updates = $shop["Updates"] ?? [];
    $attachments = $this->view(base64_encode("WebUI:Attachments"), [
     "Header" => "Attachments",
     "ID" => $id,
     "Media" => [
      "Album" => $albums,
      "Article" => $articles,
      "Attachment" => $attachments,
      "Blog" => $blogs,
      "BlogPost" => $blogPosts,
      "Chat" => $chats,
      "CoverPhoto" => $coverPhoto,
      "Forum" => $forums,
      "ForumPost" => $forumPosts,
      "Member" => $members,
      "Poll" => $polls,
      "Shop" => $shops,
      "Update" => $updates
     ]
    ]);
    foreach($adminExpenses as $expense => $info) {
     $adminExpensePercentagesList = "";
     for($i = 1; $i < 100; $i++) {
      $selected = ($i == $info["Percentage"]) ? " selected" : "";
      $adminExpensePercentagesList .= "<option$selected value='$i'>$i%</option>\r\n";
     }
     $adminExpensesList .= $this->core->Change([[
      "[Clone.ID]" => $expense,
      "[AdminExpense.Name]" => $info["Name"],
      "[AdminExpense.Percentages]" => $adminExpensePercentagesList,
     ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-68170110407f0")]);
    }
    $adminExpensePercentagesList = "";
    for($i = 1; $i < 100; $i++) {
     $adminExpensePercentagesList .= "<option value='$i'>$i%</option>\r\n";
    }
    $_Card = [
     "Action" => $this->core->Element(["button", "Update", [
      "class" => "CardButton SendData",
      "data-form" => ".EditShop$id",
      "data-processor" => base64_encode("v=".base64_encode("Shop:Save")."&Username=$username")
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Shop.AdministrativeExpenses]" => $adminExpensesList,
       "[Shop.AdministrativeExpenses.Clone]" => base64_encode($this->core->Change([[
        "[AdminExpense.Name]" => "",
        "[AdminExpense.Percentages]" => $adminExpensePercentagesList
       ], $this->core->Extension("45787465-6e73-496f-ae42-794d696b65-68170110407f0")])),
       "[Shop.Attachments]" => $this->core->RenderView($attachments),
       "[Shop.Chat]" => $this->core->AESencrypt("v=".base64_encode("Chat:Edit")."&Description=".base64_encode($shop["Description"])."&ID=".base64_encode(md5("Shop$id"))."&Title=".base64_encode($shop["Title"])."&Username=".base64_encode($owner)),
       "[Shop.ID]" => $id,
       "[Shop.Header]" => $header,
       "[Shop.Translate]" => $this->core->RenderView($translate)
      ],
     "ExtensionID" => "201c1fca2d1214dddcbabdc438747c9f"
     ],
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".ShopInformation$id",
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
          "name" => "Title",
          "placeholder" => "Title",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Title"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($shop["Title"])
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Description",
          "placeholder" => "Description"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Description"
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($shop["Description"])
        ],
        [
         "Attributes" => [
          "class" => "req",
          "data-editor-identifier" => "HiringAgreement$id",
          "name" => "HireTerms",
          "placeholder" => "Describe your requirements, what potential clients can expect when hiring you, and any other terms and conditions of hire."
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Hiring Agreement",
          "WYSIWYG" => 1
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($this->core->PlainText([
          "Data" => $hireTerms
         ]))
        ],
        [
         "Attributes" => [
          "class" => "req",
          "data-editor-identifier" => "ShopWelcomeMessage$id",
          "name" => "Welcome",
          "placeholder" => "Welcome"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Welcome Message",
          "WYSIWYG" => 1
         ],
         "Type" => "TextBox",
         "Value" => $this->core->AESencrypt($this->core->PlainText([
          "Data" => $shop["Welcome"]
         ]))
        ],
        [
         "Attributes" => [
          "name" => "PassPhrase",
          "placeholder" => "Pass Phrase",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Pass Phrase"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($passPhrase)
        ],
        [
         "Attributes" => [],
         "OptionGroup" => [
          "Braintree" => "Braintree",
          "PayPal" => "PayPal"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Payment Processor"
         ],
         "Name" => "PaymentProcessor",
         "Title" => "Payment Processor",
         "Type" => "Select",
         "Value" => $paymentProcessor
        ],
        [
         "Attributes" => [],
         "OptionGroup" => $percentages,
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Tax"
         ],
         "Name" => "Tax",
         "Title" => "Tax",
         "Type" => "Select",
         "Value" => $tax
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".BraintreeLive$id",
       [
        [
         "Attributes" => [
          "name" => "Processing_BraintreeMerchantIDLive",
          "placeholder" => "Merchant ID",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
           "HeaderText" => "Merchant ID"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_encode($processing["BraintreeMerchantIDLive"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_BraintreePrivateKeyLive",
          "placeholder" => "Private Key",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Private Key"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_encode($processing["BraintreePrivateKeyLive"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_BraintreePublicKeyLive",
          "placeholder" => "Public Key",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Public Key"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_encode($processing["BraintreePublicKeyLive"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_BraintreeTokenLive",
          "placeholder" => "Token",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Token"
         ],
        "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_encode($processing["BraintreeTokenLive"]))
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".BraintreeSandbox$id",
       [
        [
         "Attributes" => [
          "name" => "Processing_BraintreeMerchantID",
          "placeholder" => "Merchant ID",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
           "HeaderText" => "Merchant ID"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["BraintreeMerchantID"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_BraintreePrivateKey",
          "placeholder" => "Private Key",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Private Key"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["BraintreePrivateKey"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_BraintreePublicKey",
          "placeholder" => "Public Key",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Public Key"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["BraintreePublicKey"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_BraintreeToken",
          "placeholder" => "Token",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Token"
         ],
        "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["BraintreeToken"]))
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".EnableHireSection$id",
       [
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
          "HeaderText" => "Enable Hire Section"
         ],
         "Name" => "EnableHireSection",
         "Title" => "Enable Hire BSection",
         "Type" => "Select",
         "Value" => $enableHireSection
        ],
        [
         "Attributes" => [],
         "OptionGroup" => $hireLimits,
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Limit"
         ],
         "Name" => "HireLimit",
         "Title" => "Limit",
         "Type" => "Select",
         "Value" => $hireLimit
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".PayPalLive$id",
       [
        [
         "Attributes" => [
          "name" => "Processing_PayPalClientIDLive",
          "placeholder" => "Client ID",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Client ID"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["PayPalClientIDLive"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_PayPalEmailLive",
          "placeholder" => "Email",
          "type" => "email"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Email"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["PayPalEmailLive"]))
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".PayPalSandbox$id",
       [
        [
         "Attributes" => [
          "name" => "Processing_PayPalClientID",
          "placeholder" => "Client ID",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Client ID"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["PayPalClientID"]))
        ],
        [
         "Attributes" => [
          "name" => "Processing_PayPalEmail",
          "placeholder" => "Email",
          "type" => "email"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Email"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt(base64_decode($processing["PayPalEmail"]))
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".Visibility$id",
       [
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
          "HeaderText" => "Live"
         ],
         "Name" => "Live",
         "Title" => "Live",
         "Type" => "Select",
         "Value" => $shop["Live"]
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
          "HeaderText" => "Open"
         ],
         "Name" => "Open",
         "Title" => "Open",
         "Type" => "Select",
         "Value" => $shop["Open"]
        ]
       ]
      ]
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".NSFW$id",
       [
        "Filter" => "NSFW",
        "Name" => "NSFW",
        "Title" => "Content Status",
        "Value" => $nsfw
       ]
      ]
     ],
     [
      "Name" => "RenderVisibilityFilter",
      "Parameters" => [
       ".Privacy$id",
       [
        "Value" => $privacy
       ]
      ]
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function EditPartner(array $data): string {
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Partner Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $new = $data["new"] ?? 0;
   $username = $data["UN"] ?? "";
   $username = (!empty($username)) ? base64_decode($username) : "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($username) || $new == 1) {
    $_Dialog = "";
    $action = "";
    $id = md5($username);
    if($new == 1) {
     $action = "Hire";
     $company = "";
     $description = "";
     $header = "New Partner";
     $inputType = "text";
     $title = "Partner";
    } else {
     $action = "Update";
     $shop = $this->core->Data("Get", ["shop", md5($you)]);
     $partner = $shop["Contributors"][$username] ?? [];
     $company = $partner["Company"];
     $description = $partner["Description"];
     $header = "Edit $username";
     $inputType = "hidden";
     $title = $partner["Title"] ?? "Partner";
    }
    $username = ($inputType != "hidden") ? $this->core->AESencrypt($username) : $username;
    $_Card = [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".Partner$id",
      "data-processor" => base64_encode("v=".base64_encode("Shop:SavePartner"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Partner.Header]" => $header,
       "[Partner.ID]" => $id
      ],
      "ExtensionID" => "a361fab3e32893af6c81a15a81372bb7"
     ]
    ];
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".PartnerInformation$id",
       [
        [
         "Attributes" => [
          "class" => "req",
          "name" => "UN",
          "placeholder" => "Username",
          "type" => $inputType
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $username
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "New",
          "type" => "hidden"
         ],
         "Options" => [],
         "Type" => "Text",
         "Value" => $new
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Company",
          "placeholder" => "NewCo LLC",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Company"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($company)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Description",
          "placeholder" => "A corporate-level partner of ".$this->core->config["App"]["Name"].".",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Description"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($description)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "Title",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Title"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($title)
        ]
       ]
      ]
     ]
    ];
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function HireSection(array $data): string {
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $id = $data["Shop"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = "";
    $shop = $this->core->Data("Get", ["shop", $id]);
    $enableHireSection = $shop["EnableHireSection"] ?? 0;
    $services = $shop["InvoicePresets"] ?? [];
    $hire = (md5($you) != $id) ? 1 : 0;
    $hire = (count($services) > 0 && $hire == 1) ? 1 : 0;
    $hire = (!empty($shop["InvoicePresets"]) && $hire == 1) ? 1 : 0;
    $invoices = $shop["Invoices"] ?? [];
    $limit = $shop["HireLimit"] ?? 5;
    $openInvoices = 0;
    $partners = $shop["Contributors"] ?? [];
    $hireText = (count($partners) == 1) ? "Me" : "Us";
    foreach($invoices as $key => $invoice) {
     $invoice = $this->core->Data("Get", ["invoice", $invoice]);
     if($invoice["Status"] == "Open") {
      $openInvoices++;
     }
    } if($hire == 1 && $shop["Open"] == 1) {
     $_View = ($enableHireSection == 1 && $openInvoices < $limit) ? [
      "ChangeData" => [
       "[Hire.Text]" => $hireText,
       "[Hire.View]" => $this->core->AESencrypt("v=".base64_encode("Invoice:Hire")."&Card=1&CreateJob=1&ID=$id")
      ],
      "ExtensionID" => "357a87447429bc7b6007242dbe4af715"
     ] : "";
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function History(array $data): string {
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $i = 0;
   $id = $data["ID"] ?? md5($this->core->ShopID);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to view your shopping history."
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $_View = "";
    $history = $y["Shopping"]["History"] ?? [];
    $history = $history[$id] ?? [];
    $illegalContent = $this->core->config["App"]["Illegal"] ?? 777;
    $newHistory = [];
    foreach(array_reverse($history) as $key => $value) {
     $product = $value["ID"] ?? "";
     $blocked = $this->core->CheckBlocked([$y, "Products", $product]);
     $quantity = $value["Quantity"] ?? 1;
     $_Product = $this->core->GetContentData([
      "ID" => base64_encode("Product;$product")
     ]);
     if($_Product["Empty"] == 0 && $blocked == 0) {
      $options = "";
      $product = $_Product["DataModel"];
      $check = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal >= $illegalContent) ? 1 : 0;
      if($id == $this->core->ShopID || ($bl == 0 && $check == 1 && $illegal == 0)) {
       $category = $product["Category"];
       $newHistory[$key] = $value;
       $i++;
       $id = $product["ID"];
       $orderID = $value["OrderID"] ?? "N/A";
       $media = $product["DLC"] ?? [];
       $pts = $this->core->config["PTS"]["Products"];
       if($category == "Architecture") {
        $options = $this->core->Element(["p", "Your project media is ready for download."]);
       } elseif($category == "Donation") {
        $options = $this->core->Element(["p", "Thank you for donating!"]);
       } elseif($category == "Product") {
        $options = $this->core->Element(["button", "Contact the Seller", [
         "class" => "BBB v2 v2w"
        ]]);
       } elseif($category == "Subscription") {
        $subscriptions = $this->core->config["Subscriptions"] ?? [];
        $subscription = ($id == $subscriptions["Artist"]["ID"]) ? "Artist" : "";
        $subscription = ($id == $subscriptions["Blogger"]["ID"]) ? "Developer" : $subscription;
        $subscription = ($id == $subscriptions["Developer"]["ID"]) ? "Blogger" : $subscription;
        $subscription = ($id == $subscriptions["VIP"]["ID"]) ? "VIP" : $subscription;
        $subscription = ($id == $subscriptions["XFS"]["ID"]) ? "XFS" : $subscription;
        $options = $this->core->Element(["button", "Go to ".$subscriptions[$subscription]["Title"], [
         "class" => "BBB OpenCard v2 v2w",
         "data-encryption" => "AES",
         "data-view" => $this->core->AESencrypt("v=".base64_encode("Subscription:Home")."&sub=".base64_encode($subscription))
        ]]);
       }
       $options .= (!empty($media)) ? $this->core->Element(["button", "Download Media", [
        "class" => "BBB Download v2 v2w",
        "data-media" => base64_encode(base64_encode(implode(";", $media))),
        "data-view" => base64_encode("v=".base64_encode("File:Download"))
       ]]) : "";
       $_View .= $this->core->Change([[
        "[Product.CoverPhoto]" => $_Product["ListItem"]["CoverPhoto"],
        "[Product.Description]" => $_Product["ListItem"]["Description"],
        "[Product.Options]" => $options,
        "[Product.OrderID]" => $orderID,
        "[Product.Quantity]" => $quantity,
        "[Product.Title]" => $_Product["ListItem"]["Title"]
       ], $this->core->Extension("4c304af9fcf2153e354e147e4744eab6")]);
      }
     }
    } if($i == 0) {
     $_View = $this->core->Element(["h3", "No Results", [
      "class" => "CenterText UpperCase",
      "style" => "margin:1em"
     ]]);
    }
    $y["Shopping"]["History"][$data["ID"]] = $newHistory;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
    $_View = [
     "ChangeData" => [
      "[ShoppingHistory.List]" => $_View
     ],
     "ExtensionID" => "20664fb1019341a3ea2e539360108ac3"
    ];
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Home(array $data): string {
   $_Card = "";
   $_Commands = "";
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "b2",
    "lPG",
   ]);
   $addTo = $data["AddTo"] ?? "";
   $back = $data["back"] ?? "";
   $back = ($back == 1) ? $this->core->Element([
    "button", "Back", [
     "class" => "GoToParent LI head",
     "data-type" => $data["lPG"]
    ]
   ]) : "";
   $card = $data["CARD"] ?? 0;
   $public = $data["Public"] ?? 0;
   $i = 0;
   $username = $data["UN"] ?? base64_encode("");
   $username = base64_decode($username);
   $id = md5($username);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $_View = $this->MadeInNewYork(["back" => $back]);
   $_View = $this->core->RenderView($_View);
   if($public == 1) {
    $callSign = $data["CallSign"] ?? "";
    $callSign = $this->core->CallSign($callSign);
    $shops = $this->core->DatabaseSet("Shop");
    foreach($shops as $key => $value) {
     $shop = str_replace("nyc.outerhaven.shop.", "", $value);
     $shop = $this->core->Data("Get", ["shop", $shop]);
     $t = $this->core->Data("Get", ["mbr", $shop]);
     $callSignsMatch = ($callSign == $this->core->CallSign($shop["Title"])) ? 1 : 0;
     if(($callSignsMatch == 1 || $id == $value) && $i == 0) {
      $i++;
      $id = $value;
      break;
     }
    }
   } if(!empty($username) || $i > 0) {
    $_Shop = $this->core->GetContentData([
     "ID" => base64_encode("Shop;$id")
    ]);
    if($_Shop["Empty"] == 0) {
     $shop = $_Shop["DataModel"];
     $passPhrase = $shop["PassPhrase"] ?? "";
     $verifyPassPhrase = $data["VerifyPassPhrase"] ?? 0;
     $viewProtectedContent = $data["ViewProtectedContent"] ?? 0;
     if(!empty($passPhrase) && $verifyPassPhrase == 0 && $viewProtectedContent == 0) {
      $_View = $this->view(base64_encode("Authentication:ProtectedContent"), ["Data" => [
       "Header" => base64_encode($this->core->Element([
        "h1", "Protected Content", ["class" => "CenterText"]
       ])),
       "Text" => base64_encode("Please enter the Pass Phrase given to you to access <em>".$_Shop["ListItem"]["Title"]."</em>."),
       "ViewData" => base64_encode(json_encode([
        "AddTo" => $addTo,
        "SecureKey" => base64_encode($passPhrase),
        "UN" => $data["UN"],
        "VerifyPassPhrase" => 1,
        "v" => base64_encode("Shop:Home")
       ], true))
      ]]);
      $_View = $this->core->RenderView($_View);
     } elseif($verifyPassPhrase == 1) {
      $_View = "";
      $key = $data["Key"] ?? base64_encode("");
      $key = base64_decode($key);
      $secureKey = $data["SecureKey"] ?? base64_encode("");
      $secureKey = base64_decode($secureKey);
      if($key == $secureKey) {
       $_View = $this->view(base64_encode("Shop:Home"), ["Data" => [
        "AddTo" => $addTo,
        "UN" => $data["UN"],
        "ViewProtectedContent" => 1
       ]]);
       $_View = $this->core->RenderView($_View, 1);
       $_Commands = $_View["Commands"];
       $_View = $_View["View"];
      }
     } elseif(empty($passPhrase) || $viewProtectedContent == 1) {
      $_View = "";
      $chat = $this->core->Data("Get", ["chat", md5("Shop$id")]);
      $enableHireSection = $shop["EnableHireSection"] ?? 0;
      $partners = $shop["Contributors"] ?? [];
      $t = ($username == $you) ? $y : $this->core->Member($username);
      $check = ($username == $you) ? 1 : 0;
      $check2 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
      if($check == 1 || $check2 == 1) {
       $_Search = base64_encode("Search:Containers");
       $cms = $this->core->Data("Get", ["cms", $id]);
       $check2 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $t["Privacy"]["Shop"],
        "UN" => $username,
        "Y" => $you
       ]);
       $check2 = ($this->core->ShopID == $username) ? 1 : $check2;
       $options = $_Shop["ListItem"]["Options"];
       $partners = $shop["Contributors"] ?? [];
       $services = $shop["InvoicePresets"] ?? [];
       if($check == 1 || $check2 == 1 && $shop["Open"] == 1) {
        $active = 0;
        $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
        foreach($partners as $member => $role) {
         if($active == 0 && $member == $you) {
          $active++;
          break;
         }
        }
        $blocked = $this->core->CheckBlocked([$y, "Members", $username]);
        $blockCommand = ($blocked == 0) ? "Block" : "Unblock";
        $check = ($active == 1 || $username == $you) ? 1 : 0;
        $hire = ($username == $you) ? $this->core->Element([
         "button", "Hire", [
          "class" => "OpenCard Medium v2",
          "data-encryption" => "AES",
          "data-view" => $this->core->AESencrypt("v=".base64_encode("Shop:EditPartner")."&new=1")
         ]
        ]) : "";
        $dashboard = ($active == 1 || $username == $you) ? $this->core->Change([[
         "[Dashboard.Charts]" => "",// SUBJECT TO CHANGE
         "[Dashboard.Hire]" => $hire,
         "[Dashboard.Invoices]" => base64_encode("v=".base64_encode("Search:Containers")."&Shop=$id&st=SHOP-Invoices"),
         "[Dashboard.NewProduct]" => base64_encode("v=".base64_encode("Product:Edit")."&Shop=$id&new=1"),
         "[Dashboard.Orders]" => base64_encode("v=$_Search&st=SHOP-Orders"),
         "[Dashboard.Services]" => base64_encode("v=".base64_encode("Search:Containers")."&Shop=$id&st=SHOP-InvoicePresets")
        ], $this->core->Extension("20820f4afd96c9e32440beabed381d36")]) : "";
        $dashboardView = ($active == 1 || $username == $you) ? $this->core->Element([
         "button", "Dashboard", [
          "class" => "PS Small v2",
          "data-type" => ".Shop$id;.ShopNavigation;.Dashboard"
         ]
        ]) : "";
        $disclaimer = "Products and Services sold on the <em>Made in New York</em> Shop Network by third parties do not represent the views of <em>Outer Haven</em>, unless sold under the signature Shop.";
        $liveViewSymbolicLinks = $this->core->GetSymbolicLinks($shop, "LiveView");
        $purgeRenderCode = ($username == $you) ? "PURGE" : "DO NOT PURGE";
        $share = (md5($you) == $id || $shop["Privacy"] == md5("Public")) ? 1 : 0;
        $actions = (!empty($addToData)) ? $this->core->Element([
         "button", "Attach", [
          "class" => "Attach Small v2",
          "data-input" => base64_encode($addToData[1]),
          "data-media" => base64_encode("Shop;".md5($username))
         ]
        ]) : "";
        $actions .= ($check == 1) ? $this->core->Element([
         "button", "Edit", [
          "class" => "OpenCard Small v2",
          "data-encryption" => "AES",
          "data-view" => $options["Edit"]
         ]
        ]) : "";
        $actions .= (!empty($chat) && $check == 1) ? $this->core->Element([
         "button", "Partner Chat", [
          "class" => "OpenCard Small v2",
          "data-encryption" => "AES",
          "data-view" => $options["Chat"]
         ]
        ]) : "";
        $actions .= ($share == 1) ? $this->core->Element([
         "button", "Share", [
          "class" => "OpenCard Small v2",
          "data-encryption" => "AES",
          "data-view" => $options["Share"]
         ]
        ]) : "";
        $_Commands = [
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Conversation$id",
           $this->core->AESencrypt("v=".base64_encode("Conversation:Home")."&CRID=".base64_encode($id)."&LVL=".base64_encode(1))
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Partners$id",
           $this->core->AESencrypt("v=$_Search&ID=".base64_encode($id)."&Type=".base64_encode("Shop")."&st=Contributors")
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".ProductList$id",
           $this->core->AESencrypt("v=$_Search&UN=".base64_encode($t["Login"]["Username"])."&b2=".$shop["Title"]."&lPG=SHOP-Products$id&st=SHOP-Products")
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Vote$id",
           $options["Vote"]
          ]
         ],
         [
          "Name" => "UpdateContentRecursiveAES",
          "Parameters" => [
           ".Hire$id",
           $options["Hire"],
           10000
          ]
         ],
         [
          "Name" => "UpdateContentRecursiveAES",
          "Parameters" => [
           ".Subscribe$id",
           $this->core->AESencrypt("v=".base64_encode("WebUI:SubscribeSection")."&ID=$id&Type=Shop"),
           10000
          ]
         ]
        ];
        $_View = [
         "ChangeData" => [
          "[Shop.Actions]" => $actions,
          "[Shop.Back]" => $back,
          "[Shop.Block]" => $options["Block"],
          "[Shop.Block.Text]" => $blockCommand,
          "[Shop.Cart]" => base64_encode("v=".base64_encode("Cart:Home")."&UN=".$data["UN"]."&ViewPiarID=".base64_encode("Shop$id")),
          "[Shop.CoverPhoto]" => $_Shop["ListItem"]["CoverPhoto"],
          "[Shop.Dashboard]" => $dashboard,
          "[Shop.DashboardView]" => $dashboardView,
          "[Shop.Disclaimer]" => $disclaimer,
          "[Shop.History]" => $this->core->AESencrypt("v=".base64_encode("Shop:History")."&ID=$id"),
          "[Shop.ID]" => $id,
          "[Shop.Revenue]" => $options["Revenue"],
          "[Shop.Title]" => $_Shop["ListItem"]["Title"],
          "[Shop.Welcome]" => $this->core->PlainText([
           "Data" => $shop["Welcome"],
           "HTMLDecode" => 1
          ]),
          "[PurgeRenderCode]" => $purgeRenderCode
         ],
         "ExtensionID" => "f009776d658c21277f8cfa611b843c24"
        ];
        $_Card = ($card == 1) ? [
         "Front" => $_View
        ] : "";
        $_View = ($card == 0) ? $_View : "";
       }
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "View" => $_View
   ]);
  }
  function MadeInNewYork(array $data): string {
   $_Search = base64_encode("Search:Containers");
   $data = $data["Data"] ?? [];
   $back = $data["back"] ?? "";
   $id = md5($this->core->ShopID);
   $shop = $this->core->Data("Get", ["shop", $id]);
   $partners = $shop["Contributors"] ?? [];
   $username = base64_encode($this->core->ShopID);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $payroll = ($id == md5($you)) ? $this->core->Element([
    "button", "Payroll", [
     "class" => "OpenCard Small v2",
     "data-encryption" => "AES",
     "data-view" => $this->core->AESencrypt("v=".base64_encode("Shop:Payroll"))
    ]
   ]) : "";
   return $this->core->JSONResponse([
    "Commands" => [
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".MiNYArtists",
       $this->core->AESencrypt("v=".$_Search."&b2=Made in New York&lPG=MadeInNY&st=SHOP")
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".MiNYProducts",
       $this->core->AESencrypt("v=".$_Search."&b2=Made in New York&lPG=MadeInNY&st=Products")
      ]
     ],
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".MiNYHire",
       $this->core->AESencrypt("v=".base64_encode("Shop:HireSection")."&Shop=$id"),
       10000
      ]
     ],
     [
      "Name" => "UpdateContentRecursiveAES",
      "Parameters" => [
       ".MiNYSubscribe",
       $this->core->AESencrypt("v=".base64_encode("WebUI:SubscribeSection")."&ID=$id&Type=Shop"),
       10000
      ]
     ]
    ],
    "View" => [
     "ChangeData" => [
      "[MadeInNY.Back]" => $back,
      "[MadeInNY.VIP]" => $this->core->AESencrypt("v=".base64_encode("Product:Home")."&CARD=1&ID=355fd2f096bdb49883590b8eeef72b9c&UN=$username")
     ],
     "ExtensionID" => "62ee437edb4ce6d30afa8b3ea4ec2b6e"
    ]
   ]);
  }
  function Pay(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $_ResponseType = "N/A";
   $_View = "";
   $data = $data["Data"] ?? [];
   $now = $this->core->timestamp;
   $shopID = $data["Shop"] ?? "";
   $title = "Payments @ ".$this->core->config["App"]["Name"];
   $type = $data["Type"] ?? "";
   $viewPairID = $data["ViewPairID"] ?? base64_encode("");
   $viewPairID = base64_decode($viewPairID);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you && $type != "Donation") {
    $_Dialog = [
     "Body" => "You must sign in to continue."
    ];
   } elseif(!empty($shopID)) {
    $_Dialog = [
     "Body" => "The Payment Type is missing."
    ];
    if(!empty($type)) {
     $_Braintree = $this->core->DocumentRoot."/base/pay/Braintree.php";
     $changeData = [];
     $shop = $this->core->Data("Get", ["shop", $shopID]);
     $shopOwner = $this->core->Data("Get", ["mbr", $shopID]);
     $step = $data["Step"] ?? 0;
     $live = $shop["Live"] ?? 0;
     $payments = $shop["Processing"] ?? [];
     $payments = $this->core->FixMissing($payments, [
      "BraintreeMerchantIDLive",
      "BraintreePrivateKeyLive",
      "BraintreePublicKeyLive",
      "BraintreeTokenLive",
      "PayPalClientID",
      "PayPalClientIDLive",
      "PayPalEmailLive"
     ]);
     $paymentProcessor = $shop["PaymentProcessor"] ?? "PayPal";
     $paymentProcessors = $this->core->config["Shop"]["PaymentProcessors"] ?? [];
     if($paymentProcessor == "Braintree") {
      require_once($_Braintree);
      $envrionment = ($live == 1) ? "production" : "sandbox";
      $braintree = ($live == 1) ? [
       "MerchantID" => $payments["BraintreeMerchantIDLive"],
       "Token" => $payments["BraintreeTokenLive"],
       "PrivateKey" => $payments["BraintreePrivateKeyLive"],
       "PublicKey" => $payments["BraintreePublicKeyLive"]
      ] : [
       "MerchantID" => $payments["BraintreeMerchantID"],
       "Token" => $payments["BraintreeToken"],
       "PrivateKey" => $payments["BraintreePrivateKey"],
       "PublicKey" => $payments["BraintreePublicKey"]
      ];
      $token = base64_decode($braintree["Token"]);
      $merchantID = base64_decode($braintree["MerchantID"]);
      $braintree = new Braintree\Gateway([
       "environment" => $envrionment,
       "merchantId" => $merchantID,
       "privateKey" => base64_decode($braintree["PrivateKey"]),
       "publicKey" => base64_decode($braintree["PublicKey"])
      ]);
      $token = $braintree->clientToken()->generate([
       "merchantAccountId" => $merchantID
      ]) ?? $token;
     } elseif($paymentProcessor == "PayPal") {
      $paypal = ($live == 1) ? [
       "ClientID" => $payments["PayPalClientIDLive"]
      ] : [
       "ClientID" => $payments["PayPalClientID"]
      ];
      $token = "";
     } if(!in_array($paymentProcessor, $paymentProcessors)) {
      $_Dialog = [
       "Body" => "The Payment Processor is missing or unsupported."
      ];
     } else {
      $_AccessCode = "Accepted";
      $_Dialog = "";
      $check = 0;
      $message = "";
      $orderID = $data["OrderID"] ?? $this->core->UUID("Order$shopID");
      $paymentNonce = $data["payment_method_nonce"] ?? "";
      $data["ViewPairID"] = ($type == "Checkout") ?  base64_encode("Shop$shopID") : $data["ViewPairID"];
      $processor = "v=".base64_encode("Shop:Pay")."&Shop=$shopID&Step=2&Type=$type&ViewPairID=".$data["ViewPairID"];
      $subtotal = 0;
      $tax = 0;
      $title = $shop["Title"] ?? $title;
      $total = 0;
      if($type == "Checkout") {
       $_ExtensionID = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $cart = $y["Shopping"]["Cart"][$shopID]["Products"] ?? [];
       $cartCount = count($cart);
       $credits = $y["Shopping"]["Cart"][$shopID]["Credits"] ?? 0;
       $credits = number_format($credits, 2);
       $discountCode = $y["Shopping"]["Cart"][$shopID]["DiscountCode"] ?? 0;
       foreach($cart as $key => $value) {
        $product = $this->core->Data("Get", ["product", $key]);
        $quantity = $product["Quantity"] ?? 0;
        if(!empty($product) && $quantity != 0) {
         $productIsActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
         if($productIsActive == 1) {
          $price = str_replace(",", "", $product["Cost"]);
          $price = $price + str_replace(",", "", $product["Profit"]);
          $subtotal = $subtotal + ($price * $value["QTY"]);
         }
        }
       } if($discountCode != 0) {
        $discountCode = $discountCode ?? [];
        $dollarAmount = $discountCode["DollarAmount"] ?? 0.00;
        $dollarAmount = number_format($dollarAmount, 2);
        $percentile = $discountCode["Percentile"] ?? 0;
        $percentile = $subtotal * ($percentile / 100);
        $discountCodeAmount = ($dollarAmount > $percentile) ? "Dollars" : "Percentile";
        $discountCode = [
         "Amount" => $discountCodeAmount,
         "Dollars" => $dollarAmount,
         "Percentile" => $percentile
        ];
        if($discountCode["Amount"] == "Dollars") {
         $discountCode = $discountCode["Dollars"];
        } else {
         $discountCode = number_format($discountCode["Percentile"], 2);
        }
       }
       $subtotal = $subtotal - $credits - $discountCode;
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $_ExtensionID = "229e494ec0f0f43824913a622a46dfca";
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $history = $y["Shopping"]["History"][$shopID] ?? [];
          $message = "";
          $points = $y["Points"] ?? 0;
          $physicalOrders = $this->core->Data("Get", ["po", $shopID]);
          foreach($cart as $key => $info) {
           $product = $this->core->Data("Get", ["product", $key]);
           if(!empty($product)) {
            $bundle = $info["Bundled"] ?? [];
            $isActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
            $isInStock = $product["Quantity"] ?? 0;
            $isInStock = ($isInStock != 0) ? 1 : 0;
            $quantity = $info["Quantity"] ?? 1;
            $info["ID"] = $info["ID"] ?? $key;
            $info["Quantity"] = $quantity;
            if($isActive == 0 || $isInStock == 0) {
             $price = str_replace(",", "", $product["Cost"]);
             $price = $price + str_replace(",", "", $product["Profit"]);
             $points = $points + ($price * 10000);
            } else {
             array_push($history, [
              "ID" => $key,
              "OrderID" => $orderID,
              "Quantity" => $quantity
             ]);
             foreach($bundle as $bundleID => $bundledProductID) {
              $bundledProductID = explode(";", base64_decode($bundledProductID));
              $bundledProductID = end($bundledProductID) ?? "";
              array_push($history, [
               "ID" => $bundledProductID,
               "Quantity" => 1
              ]);
             }
             $cartOrder = $this->ProcessCartOrder([
              "OrderID" => $orderID,
              "PhysicalOrders" => $physicalOrders,
              "Product" => $info,
              "UN" => $shopOwner["Login"]["Username"],
              "You" => $y
             ]);
             $physicalOrders = ($cartOrder["ERR"] == 0) ? $cartOrder["PhysicalOrders"] : $physicalOrders;
             $message .= $cartOrder["Response"];
             $y = $cartOrder["Member"];
            }
           }
          }
          $y["Points"] = $points;
          $y["Shopping"]["Cart"][$shopID]["Credits"] = 0;
          $y["Shopping"]["Cart"][$shopID]["DiscountCode"] = 0;
          $y["Shopping"]["Cart"][$shopID]["Products"] = [];
          $y["Shopping"]["History"][$shopID] = $history;
          $y["Verified"] = 1;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->core->Data("Save", ["po", $shopID, $physicalOrders]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "You are about to complete your purchase with <em>".$shop["Title"]."</em>. Please verify that the total listed below is accurate."
        ]);
       }
      } elseif($type == "Commission") {
       $_ExtensionID = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $subtotal = $data["Amount"] ?? base64_encode(0);
       $subtotal = base64_decode($subtotal);
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       $viewPairID = "CommissionPayment";
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
          $_ExtensionID = "229e494ec0f0f43824913a622a46dfca";
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $_LastMonth = $this->core->LastMonth()["LastMonth"];
          $points = ($strippedTotal * 1000);
          $y["ArtistCommissionsPaid"][$_LastMonth] = $total;
          $y["Points"] = $y["Points"] + $points;
          $y["Subscriptions"]["Artist"] = [
            "A" => 1,
            "B" => $now,
            "E" => $this->TimePlus($now, 1, "month")
          ];
          $y["Verified"] = 1;
          $yourShop = $this->core->Data("Get", ["shop", md5($you)]);
          $yourShop["Open"] = 1;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->core->Data("Save", ["shop", md5($you), $yourShop]);
          $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
           "Cost" => 0,
           "OrderID" => $orderID,
           "Profit" => $total,
           "Quantity" => 1,
           "Shop" => $this->core->ShopID,
           "Title" => "Commission payment from @$you via ".$yourShop["Title"],
           "Type" => "Credit"
          ]]);
          $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
           "Cost" => $total,
           "OrderID" => $orderID,
           "Profit" => 0,
           "Quantity" => 1,
           "Shop" => $you,
           "Title" => "Monthly Commission Payment",
           "Type" => "Disbursement"
          ]]);
          $message = $this->core->Element([
           "p", "We appreciate your commission payment of $$total to <em>".$shop["Title"]."</em>, as well as your continued business with us! As a token of gratitude, we are also giving you $points which you may redeem for Credits at any shop within our network.<br/>"
          ]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "Thank you very much for your commission payment of $$total (includes tax) to <em>".$shop["Title"]."</em>. We hope to continue providing great ways to maximize your business with us."
        ]);
        $subtotal = str_replace(",", "", $subtotal);
        $processor .= "&Amount=".$data["Amount"]."&Month=".$data["Month"]."&Year=".$data["Year"];
       }
      } elseif($type == "Disbursement") {
       $_ExtensionID = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $partner = base64_decode($data["Partner"]);
       $subtotal = $data["Amount"] ?? base64_encode(0);
       $subtotal = base64_decode($subtotal);
       $total = number_format($subtotal, 2);
       $strippedTotal = str_replace(",", "", $total);
       $viewPairID = "PartnerPayment";
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $_ExtensionID = "229e494ec0f0f43824913a622a46dfca";
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $_PayPeriod = $data["PayPeriod"] ?? base64_encode("");
          $_PayPeriod = base64_decode($_PayPeriod);
          $_Year = $data["Year"] ?? base64_encode("");
          $_Year = base64_decode($_Year);
          $forPayPeriod = "for Revenue Pay Period $_Year-$_PayPeriod";
          $partnerShop = $this->core->Data("Get", ["shop", md5($partner)]);
          $revenue = $this->core->Data("Get", ["revenue", "$_Year-".md5($you)]);
          $y["Points"] = $y["Points"] + ($strippedTotal * 1000);
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
           "Cost" => 0,
           "OrderID" => $orderID,
           "Profit" => $total,
           "Quantity" => 1,
           "Shop" => $partner,
           "Title" => "Payment from @$you $forPayPeriod",
           "Type" => "Credit"
          ]]);
          $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
           "Cost" => $total,
           "OrderID" => $orderID,
           "Profit" => 0,
           "Quantity" => 1,
           "Shop" => $you,
           "Title" => "Payment to @$partner $forPayPeriod",
           "Type" => "Disbursement"
          ]]);
          $partner = $this->core->Data("Get", ["mbr", md5($partner)]) ?? $this->core->RenderGhostMember();
          $message = $this->core->Element([
           "p", "We appreciate you for recognizing ".$partner["Personal"]["DisplayName"]."'s work with your $$total payment."
          ]);
         }
        }
       } else {
        $partner = $this->core->Data("Get", ["mbr", md5($partner)]) ?? $this->core->RenderGhostMember();
        $message = $this->core->Element([
         "p", "You are about to pay ".$partner["Personal"]["DisplayName"]." $$total for their previous work."
        ]);
        $subtotal = str_replace(",", "", $subtotal);
        $processor .= "&Amount=".$data["Amount"]."&PayPeriod=".$data["PayPeriod"]."&Partner=".$data["Partner"]."&Year=".$data["Year"];
       }
      } elseif($type == "Donation") {
       $_ExtensionID = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $subtotal = $data["Amount"] ?? base64_encode(0);
       $subtotal = base64_decode($subtotal);
       $tax = $shop["Tax"] ?? 10.00;
       $tax = number_format($subtotal * ($tax / 100), 2);
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $_ExtensionID = "229e494ec0f0f43824913a622a46dfca";
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => count($y["Shopping"]["Cart"][$shopID]["Products"]),
           "[Checkout.Order.Success]" => $order->success
          ];
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $points = $strippedTotal * 100;
          $y["Points"] = $y["Points"] + $points;
          $y["Verified"] = 1;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
           "Cost" => 0,
           "OrderID" => $orderID,
           "Profit" => $total,
           "Quantity" => 1,
           "Shop" => $shopOwner["Login"]["Username"],
           "Title" => "Paid via ".$shop["Title"],
           "Type" => "Donation"
          ]]);
          $message = $this->core->Element([
           "p", "We appreciate your donation of $$total to <em>".$shop["Title"]."</em>! This will help fund our continuing effort to preserve free speech on the internet. We are also giving you $points towards Credits which you may use for future purchases if you are currently signed in."
          ]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "Thank you very much for considering a donation of $$total (includes tax) to <em>".$shop["Title"]."</em>."
        ]);
        $subtotal = str_replace(",", "", $subtotal);
        $processor .= "&Amount=".$data["Amount"];
       }
      } elseif($type == "Invoice") {
       $_ExtensionID = "f9ee8c43d9a4710ca1cfc435037e9abd";
       $changeData = [
        "[Checkout.Data]" => json_encode($data, true)
       ];
       $charge = $data["Charge"] ?? "";
       $invoiceID = $data["Invoice"] ?? "";
       $invoice = $this->core->Data("Get", ["invoice", $invoiceID]);
       $charges = $invoice["Charges"] ?? [];
       $payInFull = $data["PayInFull"] ?? 0;
       $unpaid = 0;
       foreach($charges as $key => $info) {
        $value = $info["Value"] ?? 0.00;
        $unpaid = $unpaid + $value;
        if($charge == $key || $payInFull == 1) {
         if($info["Paid"] == 0) {
          $subtotal = $subtotal + $value;
         }
        }
       }
       if($subtotal > 0) {
        $tax = $shop["Tax"] ?? 10.00;
        $tax = number_format($subtotal * ($tax / 100), 2);
       }
       $total = number_format(($subtotal + $tax), 2);
       $strippedTotal = str_replace(",", "", $total);
       if($step == 2) {
        $_ExtensionID = "f9ee8c43d9a4710ca1cfc435037e9abd";
        $changeData = [
         "[Checkout.Data]" => json_encode($data, true)
        ];
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $_ExtensionID = "229e494ec0f0f43824913a622a46dfca";
          $name = $invoice["ChargeTo"] ?? $invoice["Email"];
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
            "customer" => [
            "firstName" => $name
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => 1,
           "[Checkout.Order.Success]" => $order->success
          ];
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          if(!empty($charge)) {
           $invoice["Charges"][$charge]["Paid"] = 1;
           if($invoice["Charges"][$charge]["Value"] == $unpaid) {
            $invoice["Status"] = "Closed";
           }
          } elseif($payInFull == 1) {
           $invoice["PaidInFull"] = 1;
           $invoice["Status"] = "Closed";
           $charges = $invoice["Charges"] ?? [];
           foreach($charges as $key => $charge) {
            $invoice["Charges"][$key]["Paid"] = 1;
           }
          }
          $points = $subtotal + ($subtotal * 100);
          $y["Points"] = $points;
          $y["Verified"] = 1;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->core->Data("Save", [
           "invoice",
           $invoiceID,
           $invoice
          ]);
          $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
           "Cost" => 0,
           "OrderID" => $orderID,
           "Profit" => $total,
           "Quantity" => 1,
           "Shop" => $shopOwner["Login"]["Username"],
           "Title" =>  "Payment for Invoice #$invoiceID",
           "Type" => "Credit"
          ]]);
          $message = $this->core->Element([
           "p", "Thank you for your payment towards Invoice $invoiceID!"
          ]);
         }
        }
       } else {
        $message = $this->core->Element([
         "p", "You are about to make a $$total payment towards Invoice $invoiceID."
        ]);
        $processor .= "&Charge=$charge&Invoice=$invoiceID";
       }
      } elseif($type == "PaidMessage") {
       if($step == 2) {
        $subtotal = $data["Amount"] ?? base64_encode(5.00);
        $subtotal = base64_decode($subtotal);
        $tax = $shop["Tax"] ?? 10.00;
        $tax = number_format($subtotal * ($tax / 100), 2);
        $total = number_format(($subtotal + $tax), 2);
        $strippedTotal = str_replace(",", "", $total);
        if(!empty($orderID) || !empty($paymentNonce)) {
         if($paymentProcessor == "Braintree") {
          $_ExtensionID = "229e494ec0f0f43824913a622a46dfca";
          $order = $braintree->transaction()->sale([
           "amount" => $strippedTotal,
           "customer" => [
            "firstName" => $y["Personal"]["FirstName"]
           ],
           "options" => [
            "submitForSettlement" => true
           ],
           "paymentMethodNonce" => $paymentNonce
          ]);
          $check = ($order->success) ? 1 : 0;
          $order->message = $order->message ?? "N/A";
          $changeData = [
           "[Checkout.Order.Message]" => $order->message,
           "[Checkout.Order.Products]" => 1,
           "[Checkout.Order.Success]" => $order->success
          ];
         } elseif($paymentProcessor == "PayPal") {
          $check = (!empty($orderID)) ? 1 : 0;
          $orderID = base64_decode($orderID);
         } if($check == 1) {
          $points = $strippedTotal * 1000;
          $y["Points"] = $y["Points"] + $points;
          $y["Verified"] = 1;
          $this->core->Data("Save", ["mbr", md5($you), $y]);
          $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
           "Cost" => 0,
           "OrderID" => $orderID,
           "Profit" => $total,
           "Quantity" => 1,
           "Shop" => $shopOwner["Login"]["Username"],
           "Title" => "Paid Chat via ".$shop["Title"],
           "Type" => "Sale"
          ]]);
          $message = $this->core->Element([
           "p", "Please click or tap <em>Back to hat</em> until you're back home, your Message will be pinned to the top of the Group Chat once you send it."
          ]);
          $title = "Confirm Payment";
         }
        }
       } else {
        $_ResponseType = "GoToView";
        $data = $this->core->DecodeBridgeData($data);
        $subtotal = $data["Amount"] ?? 5.00;
        $tax = $shop["Tax"] ?? 10.00;
        $tax = number_format($subtotal * ($tax / 100), 2);
        $total = number_format(($subtotal + $tax), 2);
        $strippedTotal = str_replace(",", "", $total);
        $message = $this->core->Element([
         "p", "You are about to pay $$total (includes taxes) for your Paid Message"
        ]);
        $processor .= "&Amount=".base64_encode($data["Amount"])."&Form=".base64_encode($data["Form"]);
        $viewPairID = $data["ViewPairID"] ?? "";
       }
      } if($step == 2) {
       $_ExtensionID = "83d6fedaa3fa042d53722ec0a757e910";
       $_ExtensionID = ($type == "PaidMessage") ? "4b055a0b7ebacc45458ab2017b9bf7eb" : $$_ExtensionID;
       $form = $data["Form"] ?? base64_encode("");
       $form = base64_decode($form);
       $changeData = [
        "[Payment.Form]" => $form,
        "[Payment.Message]" => $message,
        "[Payment.Shop]" => $shop["Title"],
        "[Payment.Total]" => number_format($tax + $subtotal, 2),
        "[Payment.ViewPairID]" => $viewPairID
       ];
       $this->core->Statistic("Transaction");
      } else {
       $_ExtensionID = ($paymentProcessor == "Braintree") ? "a1a7a61b89ce8e2715efc0157aa92383" : "";
       $_ExtensionID = ($paymentProcessor == "PayPal") ? "7c0f626e2bbb9bd8c04291565f84414a" : $_ExtensionID;
       $_ViewTitle = $title ?? $shop["Title"];
       $changeData = [
        "[Payment.Message]" => $message,
        "[Payment.PayPal.ClientID]" => base64_decode($paypal["ClientID"]),
        "[Payment.Processor]" => base64_encode($processor),
        "[Payment.Region]" => $this->core->language,
        "[Payment.Shop]" => $shopID,
        "[Payment.Title]" => $title,
        "[Payment.Token]" => $token,
        "[Payment.Total]" => $total,
        "[Payment.Total.Stripped]" => str_replace(",", "", $total),
        "[Payment.ViewPairID]" => $viewPairID
       ];
      }
      $_View = [
       "ChangeData" => $changeData,
       "ExtensionID" => $_ExtensionID
      ];
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "Title" => $_ViewTitle,
    "View" => $_View
   ]);
  }
  function ProcessCartOrder(array $data): string {
   $_AccessCode = "Accepted";
   $_View = "";
   $isBundled = $data["IsBundled"] ?? 0;
   $orderID = $data["OrderID"] ?? $this->core->UUID("CartOrder");
   $physicalOrders = $data["PhysicalOrders"] ?? [];
   $purchaseQuantity = $data["Product"]["Quantity"] ?? 1;
   $shopOwner = $data["UN"] ?? "";
   $shopID = md5($shopOwner);
   $y = $data["You"] ?? $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($shopOwner) && is_array($data["Product"])) {
    $history = $y["Shopping"]["History"][$shopID] ?? [];
    $id = $data["Product"]["ID"] ?? "";
    $product = $this->core->Data("Get", ["product", $id]);
    $quantity = $product["Quantity"] ?? 0;
    $shop = $this->core->Data("Get", ["shop", $shopID]);
    $t = ($shopOwner == $you) ? $y : $this->core->Member($shopOwner);
    if(!empty($product) && $quantity != 0) {
     $bundledProducts = $product["Bundled"] ?? [];
     $contributors = $shop["Contributors"] ?? [];
     $now = $this->core->timestamp;
     $options = "";
     $productExpires = $product["Expires"] ?? $now;
     if(strtotime($now) < $productExpires) {
      $category = $product["Category"];
      $coverPhoto = $product["CoverPhoto"] ?? $this->core->PlainText([
       "Data" => "[Media:MiNY]",
       "Display" => 1
      ]);
      $coverPhoto = base64_encode($coverPhoto);
      $points = $this->core->config["PTS"]["Products"];
      $subscriptionTerm = $product["SubscriptionTerm"] ?? "month";
      if($category == "Architecture") {
       # Architecture
      } elseif($category == "Donation") {
       # Donations
      } elseif($category == "Download") {
       # Downloadable Content
       $options = $this->core->Element(["p", "Thank You for donating!"]);
      } elseif($category == "Product") {
       # Physical Products
       $options = $this->core->Element(["button", "Contact the Seller", [
        "class" => "BB v2 v2w"
       ]]);
       $physicalOrders[md5($you.$this->core->timestamp.rand(0, 9999))] = [
        "Complete" => 0,
        "Instructions" => base64_encode($data["Product"]["Instructions"]),
        "ProductID" => $id,
        "Quantity" => $purchaseQuantity,
        "UN" => $you
       ];
      } elseif($category == "Subscription") {
       if($id == "355fd2f096bdb49883590b8eeef72b9c") {
        $y["Subscriptions"]["VIP"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->core->TimePlus($now, 1, $subscriptionTerm)
        ];
       } elseif($id == "c7054e9c7955203b721d142dedc9e540") {
        $y["Subscriptions"]["Artist"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->core->TimePlus($now, 1, $subscriptionTerm)
        ];
       } elseif($id == "e4302295d2812e4f374ef1035891c4d1") {
        $y["Subscriptions"]["Developer"] = [
         "A" => 1,
         "B" => $now,
         "E" => $this->core->TimePlus($now, 1, $subscriptionTerm)
        ];
       }
      }
      $history[$this->core->UUID($id)] = [
       "ID" => $id,
       "Instructions" => $data["Product"]["Instructions"],
       "Quantity" => $purchaseQuantity,
       "Timestamp" => $now
      ];
      $product["Quantity"] = ($quantity > 0) ? $quantity - $purchaseQuantity : $quantity;
      $_View .= $this->core->Change([[
       "[Product.Added]" => $this->core->TimeAgo($now),
       "[Product.CoverPhoto]" => $coverPhoto,
       "[Product.Description]" => $this->core->PlainText([
        "Data" => $product["Description"],
        "Display" => 1
       ]),
       "[Product.Options]" => $options,
       "[Product.OrderID]" => $orderID,
       "[Product.Quantity]" => $purchaseQuantity,
       "[Product.Title]" => $product["Title"]
      ], $this->core->Extension("4c304af9fcf2153e354e147e4744eab6")]);
      $y["Shopping"]["History"][$shopID] = $history;
      $y["Points"] = $y["Points"] + $points[$category];
      if($isBundled == 0) {
       $this->view(base64_encode("Revenue:SaveTransaction"), ["Data" => [
        "Cost" => $product["Cost"],
        "OrderID" => $orderID,
        "Profit" => $product["Profit"],
        "Quantity" => $purchaseQuantity,
        "Shop" => $shopOwner,
        "Title" => $product["Title"],
        "Type" => "Sale"
       ]]);
      } if($product["Quantity"] > 0) {
       $this->core->Data("Save", ["product", $id, $product]);
      }
     } foreach($bundledProducts as $bundled) {
      $_Product = $this->core->GetContentData([
       "ID" => $bundled
      ]);
      if($_Product["Empty"] == 0) {
       $_Product = $_Product["DataModel"];
       $bundledProduct = $_Product["ID"] ?? "";
       $bundledProductShopOwner = $_Product["UN"] ?? "";
       $cartOrder = $this->ProcessCartOrder([
        "IsBundled" => 1,
        "OrderID" => $orderID,
        "PhysicalOrders" => $physicalOrders,
        "Product" => [
         "DiscountCode" => 0,
         "DiscountCredit" => 0,
         "ID" => $bundledProduct,
         "Instructions" => "",
         "Quantity" => 1
        ],
        "UN" => $bundledProductShopOwner,
        "You" => $y
       ]);
       $_View .= $cartOrder["Response"];
       $physicalOrders = ($cartOrder["ERR"] == 0) ? $cartOrder["PhysicalOrders"] : $physicalOrders;
       $y = $cartOrder["Member"];
      }
     }
    }
    $response = [
     "ERR" => 0,
     "Member" => $y,
     "PhysicalOrders" => $physicalOrders,
     "Response" => $_View
    ];
   } else {
    $response = [
     "ERR" => 1,
     "Parameters" => [],
     "Response" => $_View
    ];
   }
   return $response;
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $username = $data["Username"] ?? "";
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $shops = $this->core->DatabaseSet("Member");
    $title = $data["Title"] ?? "";
    $i = 0;
    foreach($shops as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     $shop = $this->core->Data("Get", ["shop", $value]);
     $shopTitle = $shop["Title"] ?? "";
     if($id != $value && $title == $shopTitle) {
      $i++;
     }
    } if($i > 0) {
     $_Dialog = [
      "Body" => "The Shop <em>$title</em> is taken."
     ];
    } else {
     $_AccessCode = "Accepted";
     $owner = $this->core->Data("Get", ["mbr", $id]);
     $shop = $this->core->Data("Get", ["shop", $id]);
     $administrativeExpenses = [];
     $administrativeExpensesData = $data["AdminExpenseName"] ?? [];
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
     $coverPhoto = $data["CoverPhoto"] ?? "";
     $contributors = $shop["Contributors"] ?? [];
     $description = $data["Description"] ?? $shop["Description"];
     $enableHireSection = $data["EnableHireSection"] ?? 0;
     $forums = [];
     $forumsData = $data["Forum"] ?? [];
     $forumPosts = [];
     $forumPostsData = $data["ForumPost"] ?? [];
     $hireLimit = $data["HireLimit"] ?? 5;
     $hireTerms = $data["HireTerms"] ?? "";
     $invoicePresets = $shop["InvoicePresets"] ?? [];
     $invoices = $shop["Invoices"] ?? [];
     $live = $data["Live"] ?? 0;
     $members = []; 
     $membersData = $data["Member"] ?? [];
     $now = $this->core->timestamp;
     $created = $owner["Activity"]["Registered"] ?? $now;
     $created = $shop["Created"] ?? $created;
     $modifiedBy = $shop["ModifiedBy"] ?? [];
     $modifiedBy[$now] = $you;
     $nsfw = $data["nsfw"] ?? 0;
     $open = $data["Open"] ?? 0;
     $passPhrase = $data["PassPhrase"] ?? "";
     $paymentProcessor = $data["PaymentProcessor"] ?? "PayPal";
     $privacy = $data["Privacy"] ?? $y["Privacy"]["Shop"];
     $polls = []; 
     $pollsData = $data["Poll"] ?? [];
     $purge = $shop["Purge"] ?? 0;
     $processing = $shop["Processing"] ?? [];
     $products = $shop["Products"] ?? [];
     $shops = [];
     $shopsData = $data["Shop"] ?? [];
     $tax = $data["Tax"] ?? 10.00;
     $title = $title ?? $shop["Title"];
     $updates = [];
     $updatesData = $data["Update"] ?? [];
     $welcome = $data["Welcome"] ?? "";
     foreach($data as $key => $value) {
      if(strpos($key, "Processing_") !== false) {
       $key = explode("_", $key);
       $shop["Processing"][$key[1]] = base64_encode($value);
      }
     } if(!empty($administrativeExpensesData)) {
      $expenses = $administrativeExpensesData;
      for($i = 0; $i < count($expenses); $i++) {
       array_push($administrativeExpenses, [
        "Name" => $administrativeExpensesData[$i],
        "Percentage" => $data["AdminExpensePercentage"][$i]
       ]);
      }
     } if(!empty($albumsData)) {
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
     } if(!empty($shopsData)) {
      $media = $shopsData;
      for($i = 0; $i < count($media); $i++) {
       if(!empty($media[$i])) {
        array_push($shops, $media[$i]);
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
     $shop = [
      "AdministrativeExpenses" => $administrativeExpenses,
      "Albums" => $albums,
      "Articles" => $articles,
      "Attachments" => $attachments,
      "Blogs" => $blogs,
      "BlogPosts" => $blogPosts,
      "Chats" => $chats,
      "CoverPhoto" => $coverPhoto,
      "Created" => $created,
      "Contributors" => $contributors,
      "CoverPhoto" => $coverPhoto,
      "Description" => $description,
      "EnableHireSection" => $enableHireSection,
      "Forums" => $forums,
      "ForumPosts" => $forumPosts,
      "HireLimit" => $hireLimit,
      "HireTerms" => $this->core->PlainText([
       "Data" => $hireTerms,
       "HTMLEncode" => 1
      ]),
      "InvoicePresets" => $invoicePresets,
      "Invoices" => $invoices,
      "Live" => $live,
      "Members" => $members,
      "Modified" => $now,
      "ModifiedBy" => $modifiedBy,
      "NSFW" => $nsfw,
      "Open" => $open,
      "PassPhrase" => $passPhrase,
      "PaymentProcessor" => $paymentProcessor,
      "Privacy" => $privacy,
      "Processing" => $processing,
      "Products" => $products,
      "Polls" => $polls,
      "Purge" => $purge,
      "Shops" => $shops,
      "Tax" => $tax,
      "Title" => $title,
      "Welcome" => $this->core->PlainText([
       "Data" => $welcome,
       "HTMLEncode" => 1
      ]),
      "Updates" => $updates,
      "Username" => base64_decode($username)
     ];
     $sql = New SQL($this->core->cypher->SQLCredentials());
     $query = "REPLACE INTO Shops(
      Shop_Created,
      Shop_Description,
      Shop_ID,
      Shop_Title,
      Shop_Username,
      Shop_Welcome
     ) VALUES(
      :Created,
      :Description,
      :ID,
      :Title,
      :Username,
      :Welcome
     )";
     $sql->query($query, [
      ":Created" => $created,
      ":Description" => $shop["Description"],
      ":ID" => $id,
      ":Title" => $shop["Title"],
      ":Username" => $shop["Username"],
      ":Welcome" => $welcome
     ]);
     $sql->execute();
     $this->core->Data("Save", ["shop", $id, $shop]);
     $this->core->Statistic("Edit Shop");
     $_Dialog = [
      "Body" => "<em>$title</em> has been updated.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function SaveBanish(array $data): string {
   $_Dialog = [
    "Body" => "The Username is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["UN"]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["UN"])) {
    $username = base64_decode($data["UN"]);
    if($username == $you) {
     $_Dialog = [
      "Body" => "You cannot fire yourself."
     ];
    } else {
     $newContributors = [];
     $shop = $this->core->Data("Get", ["shop", md5($you)]);
     $contributors = $shop["Contributors"] ?? [];
     foreach($contributors as $key => $value) {
      if($key != $username) {
       $newContributors[$key] = $value;
      }
     }
     $shop["Contributors"] = $newContributors;
     $this->core->Data("Save", ["shop", md5($you), $shop]);
     $_Dialog = [
      "Body" => "You fired $username.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveCreditExChange(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The points value must be numeric."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $points = $data["P"] ?? "";
   $points = base64_decode($points);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(is_numeric($points)) {
    $_Dialog = [
     "Body" => "You requested more credits than you can afford."
    ];
    $points = ($points < $y["Points"]) ? $points : $y["Points"];
    $credits = $points * 0.00001;
    $creditsDecimal = number_format($credits, 2);
    if($points < $y["Points"]) {
     $yourCredits = $y["Shopping"]["Cart"][$id]["Credits"] ?? 0;
     $y["Shopping"]["Cart"][$id]["Credits"] = $creditsDecimal + $yourCredits;
     $y["Points"] = $y["Points"] - $points;
     $_Dialog = [
      "Body" => "<em>$points</em> points were converted to $<em>$creditsDecimal</em> credits, and have <em>".$y["Points"]."</em> remaining.",
      "Header" => "Done"
     ];
     $this->core->Data("Save", ["mbr", md5($you), $y]);
    }
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog
   ]);
  }
  function SaveDiscountCodes(array $data): string {
   $data = $data["Data"] ?? [];
   $discountCode = $data["DC"] ?? "";
   $i = 0;
   $id = $data["ID"] ?? "";
   $_View = "The Code Identifier is missing.";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_View = "You must be signed in to continue.";
   } elseif(!empty($discountCode) && !empty($id)) {
    $id = base64_decode($id);
    $discount = $this->core->Data("Get", ["dc", $id]);
    $code = base64_decode($discountCode);
    $encryptedCode = $discountCode ?? base64_encode("OuterHaven.DC.Invalid");
    $_View = "<em>$code</em> is an Invalid code.";
    foreach($discount as $key => $value) {
     if($i == 0 && $encryptedCode == $value["Code"]) {
      $dollarAmount = $value["DollarAmount"] ?? 0;
      $percentile = $value["Percentile"] ?? 0;
      $quantity = $value["Quantity"] - 1;
      $quantity = ($quantity < 0) ? 0 : $quantity;
      $discount[$key]["Quantity"] = $quantity;
      $y["Shopping"]["Cart"][$id]["DiscountCode"] = [
       "Code" => $value["Code"],
       "DollarAmount" => $dollarAmount,
       "Percentile" => $percentile
      ];
      $_View = "<em>$code</em> was applied to your order!";
      $i++;
     }
    }
    $this->core->Data("Save", ["dc", $id, $discount]);
    $this->core->Data("Save", ["mbr", md5($you), $y]);
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "View" => [
     "ChangeData" => [],
     "Extension" => $this->core->AESencrypt($this->core->Element([
      "p", $_View, ["class" => "CenterText"]
     ]))
    ]
   ]);
  }
  function SavePartner(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Username is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $company = $data["Company"] ?? "";
   $description = $data["Description"] ?? "";
   $new = $data["New"] ?? 0;
   $y = $this->you;
   $title = $data["Title"] ?? "";
   $username = $data["UN"] ?? "";
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($username)) {
    $i = 0;
    $members = $this->core->DatabaseSet("Member");
    foreach($members as $key => $value) {
     $value = str_replace("nyc.outerhaven.mbr.", "", $value);
     if(md5($username) == $value) {
      $i++;
      break;
     }
    } if($i == 0) {
     $_Dialog = [
      "Body" => "The Member <em>$username</em> does not exist.",
      "Header" => "Done"
     ];
    } else {
     $_AccessCode = "Accepted";
     $actionTaken = ($new == 1) ? "hired" : "updated";
     $now = $this->core->timestamp;
     $shop = $this->core->Data("Get", ["shop", md5($you)]);
     $hired = $shop["Contributors"][$username]["Hired"] ?? $now;
     $contributors = $shop["Contributors"] ?? [];
     $contributors[$username] = [
      "Company" => $company,
      "Description" => $description,
      "Hired" => $hired,
      "Paid" => 0,
      "Title" => $title
     ];
     $shop["Contributors"] = $contributors;
     if($new == 1) {
      $this->core->SendBulletin([
       "Data" => [
        "ShopID" => md5($you),
        "Member" => $username,
        "Role" => "Partner"
       ],
       "To" => $username,
       "Type" => "InviteToShop"
      ]);
     }
     $this->core->Data("Save", ["shop", md5($you), $shop]);
     $_Dialog = [
      "Body" => "Your Partner $username was $actionTaken.",
      "Header" => "Done"
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Dialog" => $_Dialog,
    "Success" => "CloseCard"
   ]);
  }
  function Subscribe(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Shop Identifier is missing."
   ];
   $_ResponseType = "Dialog";
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $id = $data["ID"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to subscribe.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_AccessCode = "Accepted";
    $_Dialog = "";
    $_ResponseType = "UpdateText";
    $shop = $this->core->Data("Get", ["shop", $id]);
    $subscribers = $shop["Subscribers"] ?? [];
    $subscribed = (in_array($you, $subscribers)) ? 1 : 0;
    if($subscribed == 1) {
     $newSubscribers = [];
     $_View = "Subscribe";
     foreach($subscribers as $key => $value) {
      if($value != $you) {
       $newSubscribers[$key] = $value;
      }
     }
     $subscribers = $newSubscribers;
    } else {
     array_push($subscribers, $you);
     $_View = "Unsubscribe";
    }
    $shop["Subscribers"] = $subscribers;
    $this->core->Data("Save", ["shop", $id, $shop]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
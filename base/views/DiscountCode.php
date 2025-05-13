<?php
 Class DiscountCode extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data): string {
   $data = $data["Data"] ?? [];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $id = $data["ID"] ?? $this->core->UUID("ShopDiscountCodesBy$you");
   $shopID = $data["Shop"] ?? md5($you);
   $discountCodes = $this->core->Data("Get", ["dc", $shopID]);
   $discountCode = $discountCode[$id] ?? [];
   $code = $discountCode["Code"] ?? $this->core->AESencrypt("");
   $dollarAmount = $discountCode["DollarAmount"] ?? 1.00;
   $new = $data["new"] ?? 0;
   $percentage = $discountCodes["Percentile"] ?? 5;
   $percentages = [];
   $quantities = [];
   $quantity = $discountCode["Quantity"] ?? 0;
   $action = ($new == 1) ? "Post" : "Update";
   for($i = 1; $i < 100; $i++) {
    if($i <= 75) {
     $percentages[$i] = $i;
    }
    $quantities[$i] = $i;
   }
   return $this->core->JSONResponse([
    "Card" => [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-encryption" => "AES",
      "data-form" => ".EditDiscountCode$id",
      "data-processor" => $this->core->AESencrypt("v=".base64_encode("DiscountCode:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Discount.ID]" => $id
      ],
      "ExtensionID" => "47e35864b11d8bdc255b0aec513337c0"
     ]
    ],
    "Commands" => [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".DiscountCodeInformation$id",
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
          "name" => "DiscountCode",
          "placeholder" => "Diacount Code",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Discount Code"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($code)
        ],
        [
         "Attributes" => [
          "class" => "req",
          "name" => "DollarAmount",
          "placeholder" => "Dollar Amount",
          "type" => "text"
         ],
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "NONAME",
          "Header" => 1,
          "HeaderText" => "Dollar Amount"
         ],
         "Type" => "Text",
         "Value" => $this->core->AESencrypt($dollarAmount)
        ],
        [
         "Attributes" => [],
         "OptionGroup" => $percentages,
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Percent Off"
         ],
         "Name" => "Percentile",
         "Title" => "Percent Off",
         "Type" => "Select",
         "Value" => $percentage
        ],
        [
         "Attributes" => [],
         "OptionGroup" => $quantities,
         "Options" => [
          "Container" => 1,
          "ContainerClass" => "Desktop50 MobileFull",
          "Header" => 1,
          "HeaderText" => "Quantity"
         ],
         "Name" => "DiscountCodeQTY",
         "Title" => "Quantity",
         "Type" => "Select",
         "Value" => $quantity
        ]
       ]
      ]
     ]
    ]
   ]);
  }
  function Purge(array $data): string {
   $_Dialog = [
    "Body" => "The Code Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $key = $data["Key"] ?? base64_encode("");
   $key = base64_decode($key);
   $id = $data["ID"] ?? "";
   $secureKey = $data["SecureKey"] ?? base64_encode("");
   $secureKey = base64_decode($secureKey);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(md5($key) != $y["Login"]["PIN"]) {
    $_Dialog = [
     "Body" => "The PINs do not match."
    ];
   } elseif($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = "";
    $id = base64_decode($id);
    $discounts = $this->core->Data("Get", ["dc", md5($you)]);
    $newDiscounts = [];
    foreach($discounts as $key => $value) {
     if($id != $key) {
      $newDiscounts[$key] = $value;
     }
    }
    $discounts = $newDiscounts;
    $this->core->Data("Save", ["dc", md5($you), $newDiscounts]);
    $_View = $this->core->Element([
     "p", "The Discount Code was removed.",
     ["class" => "CenterText"]
    ]).$this->core->Element([
     "button", "Okay", ["class" => "CloseDialog v2 v2w"]
    ]);
   }
   return $this->core->JSONResponse([
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Save(array $data): string {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Discount Code Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "DollarAmount",
    "Percentile",
    "Quantity"
   ]);
   $discountCode = $data["DiscountCode"] ?? "";
   $id = $data["ID"] ?? "";
   $new = $data["New"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($id)) {
    $_Dialog = [
     "Body" => "The Code is missing."
    ];
    if(!empty($discountCode)) {
     $_AccessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $discountCodes = $this->core->Data("Get", ["dc", md5($you)]);
     $discountCodes[$id] = [
      "Code" => base64_encode($discountCode),
      "DollarAmount" => $data["DollarAmount"],
      "Percentile" => $data["Percentile"],
      "Quantity" => $data["DiscountCodeQTY"]
     ];
     $this->core->Data("Save", ["dc", md5($you), $discountCodes]);
     $_Dialog = [
      "Body" => "The Code <em>$discountCode</em> was $actionTaken!",
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
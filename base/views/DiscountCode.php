<?php
 Class DiscountCode extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $data) {
   $data = $data["Data"] ?? [];
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $id = $data["ID"] ?? md5($you."_DC_".$this->core->timestamp);
   $action = ($new == 1) ? "Post" : "Update";
   $discount = $this->core->Data("Get", ["dc", md5($you)]);
   $discount = $discount[$id] ?? [];
   $code = $discount["Code"] ?? base64_encode("");
   $dollarAmount = $discount["DollarAmount"] ?? 1.00;
   $percentages = [];
   $percentile = $discount["Percentile"] ?? 5;
   $quantities = [];
   $quantity = $discount["Quantity"] ?? 0;
   for($i = 1; $i < 100; $i++) {
    $percentages[$i] = $i;
   } for($i = 1; $i < 100; $i++) {
    $quantities[$i] = $i;
   }
   return $this->core->JSONResponse([
    "Card" => [
     "Action" => $this->core->Element(["button", $action, [
      "class" => "CardButton SendData",
      "data-form" => ".Discount$id",
      "data-processor" => base64_encode("v=".base64_encode("DiscountCode:Save"))
     ]]),
     "Front" => [
      "ChangeData" => [
       "[Discount.Code]" => $code,
       "[Discount.DollarAmount]" => $dollarAmount,
       "[Discount.ID]" => $id,
       "[Discount.New]" => $new,
       "[Discount.Percentages]" => json_encode($percentages, true),
       "[Discount.Percentile]" => $percentile,
       "[Discount.Quantities]" => json_encode($quantities, true),
       "[Discount.Quantity]" => $quantity
      ],
      "ExtensionID" => "47e35864b11d8bdc255b0aec513337c0"
     ]
    ]
   ]);
  }
  function Purge(array $data) {
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
  function Save(array $data) {
   $_AccessCode = "Denied";
   $_Dialog = [
    "Body" => "The Code Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, [
    "DC",
    "DollarAmount",
    "ID",
    "Percentile",
    "Quantity"
   ]);
   $new = $data["New"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $_Dialog = [
     "Body" => "The Code is missing."
    ];
    if(!empty($data["DC"])) {
     $_AccessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $discount = $this->core->Data("Get", ["dc", md5($you)]);
     $discount[$data["ID"]] = [
      "Code" => base64_encode($data["DC"]),
      "DollarAmount" => $data["DollarAmount"],
      "Percentile" => $data["Percentile"],
      "Quantity" => $data["DiscountCodeQTY"]
     ];
     $_Dialog = [
      "Body" => "The Code <em>".$data["DC"]."</em> was $actionTaken!",
      "Header" => "Done"
     ];
     $this->core->Data("Save", ["dc", md5($you), $discount]);
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
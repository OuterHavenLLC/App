<?php
 Class DiscountCode extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Edit(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, [
    "ID",
    "new"
   ]);
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $id = $data["ID"] ?? md5($you."_DC_".$this->core->timestamp);
   $action = ($new == 1) ? "Post" : "Update";
   $action = $this->core->Element(["button", $action, [
    "class" => "CardButton SendData",
    "data-form" => ".Discount$id",
    "data-processor" => base64_encode("v=".base64_encode("DiscountCode:Save"))
   ]]);
   $discount = $this->core->Data("Get", ["dc", md5($you)]) ?? [];
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
   $r = $this->core->Change([[
    "[Discount.Code]" => $code,
    "[Discount.DollarAmount]" => $dollarAmount,
    "[Discount.ID]" => $id,
    "[Discount.New]" => $new,
    "[Discount.Percentages]" => json_encode($percentages, true),
    "[Discount.Percentile]" => $percentile,
    "[Discount.Quantities]" => json_encode($quantities, true),
    "[Discount.Quantity]" => $quantity
   ], $this->core->Page("47e35864b11d8bdc255b0aec513337c0")]);
   $r = [
    "Action" => $action,
    "Front" => $r
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
    "DC",
    "DollarAmount",
    "ID",
    "Percentile",
    "Quantity"
   ]);
   $new = $data["New"] ?? 0;
   $r = [
    "Body" => "The Code Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $r = [
     "Body" => "The Code is missing.",
     "Header" => "Error"
    ];
    if(!empty($data["DC"])) {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $discount = $this->core->Data("Get", ["dc", md5($you)]) ?? [];
     $discount[$data["ID"]] = [
      "Code" => base64_encode($data["DC"]),
      "DollarAmount" => $data["DollarAmount"],
      "Percentile" => $data["Percentile"],
      "Quantity" => $data["DiscountCodeQTY"]
     ];
     $r = [
      "Body" => "The Code <em>".$data["DC"]."</em> was $actionTaken!",
      "Header" => "Done"
     ];
     $this->core->Data("Save", ["dc", md5($you), $discount]);
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
  function SaveDelete(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->DecodeBridgeData($data);
   $data = $this->core->FixMissing($data, ["ID"]);
   $r = [
    "Body" => "The Code Identifier is missing.",
    "Header" => "Error"
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must be signed in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $discount = $this->core->Data("Get", ["dc", md5($you)]) ?? [];
    $newDiscount = [];
    foreach($discount as $key => $value) {
     if($data["ID"] != $key) {
      $newDiscount[$key] = $value;
     }
    }
    $discount = $newDiscount;
    $r = [
     "Body" => "The Code was removed.",
     "Header" => "Done"
    ];
    $this->core->Data("Save", ["dc", md5($you), $discount2]);
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
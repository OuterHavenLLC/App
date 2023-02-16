<?php
 Class DiscountCode extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Edit(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "new"]);
   $new = $data["new"] ?? 0;
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $action = ($new == 1) ? "Post" : "Update";
   $id = ($new == 1) ? md5("DC_$you".$this->system->timestamp) : $data["ID"];
   $discount = $this->system->Data("Get", ["dc", md5($you)]) ?? [];
   $discount = $discount[$id] ?? [];
   $code = $discount["Code"] ?? base64_encode("");
   $dollarAmount = $discount["DollarAmount"] ?? 1.00;
   $percentile = $discount["Percentile"] ?? 5;
   $quantity = $discount["Quantity"] ?? 0;
   $fr = $this->system->Change([[
    "[Discount.Code]" => base64_decode($code),
    "[Discount.DollarAmount]" => $dollarAmount,
    "[Discount.ID]" => $id,
    "[Discount.New]" => $new,
    "[Discount.PercentOff]" => $this->system->Select("Percentile", "req v2w", $percentile),
    "[Discount.Quantity]" => $this->system->Select("DiscountCodeQTY", "req v2w", $quantity),
   ], $this->system->Page("47e35864b11d8bdc255b0aec513337c0")]);
   $frbtn = $this->system->Element(["button", $action, [
    "class" => "CardButton SendData",
    "data-form" => ".Discount$id",
    "data-processor" => base64_encode("v=".base64_encode("DiscountCode:Save"))
   ]]);
   return $this->system->Card([
    "Front" => $fr,
    "FrontButton" => $frbtn
   ]);
  }
  function Save(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "DC",
    "DollarAmount",
    "ID",
    "Percentile",
    "Quantity",
    "new"
   ]);
   $new = $data["new"] ?? 0;
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Code Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($data["ID"])) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The Code is missing."]),
     "Header" => "Error"
    ]);
    if(!empty($data["DC"])) {
     $accessCode = "Accepted";
     $actionTaken = ($new == 1) ? "posted" : "updated";
     $discount = $this->system->Data("Get", ["dc", md5($you)]) ?? [];
     $discount[$data["ID"]] = [
      "Code" => base64_encode($data["DC"]),
      "DollarAmount" => $data["DollarAmount"],
      "Percentile" => $data["Percentile"],
      "Quantity" => $data["DiscountCodeQTY"]
     ];
     $r = $this->system->Dialog([
      "Body" => $this->system->Element([
       "p", "The Code <em>".$data["DC"]."</em> was $actionTaken!"
      ]),
      "Header" => "Done"
     ]);
     $this->system->Data("Save", ["dc", md5($you), $discount]);
    }
   }
   return $this->system->JSONResponse([
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
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["ID"]);
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Code Identifier is missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->system->ID == $you) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element([
      "p", "You must be signed in to continue."
     ]),
     "Header" => "Forbidden"
    ]);
   } elseif(!empty($data["ID"])) {
    $accessCode = "Accepted";
    $discount = $this->system->Data("Get", ["dc", md5($you)]) ?? [];
    $newDiscount = [];
    foreach($discount as $key => $value) {
     if($data["ID"] != $key) {
      $newDiscount[$key] = $value;
     }
    }
    $discount = $newDiscount;
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The Code was removed."]),
     "Header" => "Done"
    ]);
    $this->system->Data("Save", ["dc", md5($you), $discount2]);
   }
   return $this->system->JSONResponse([$accessCode, $r]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
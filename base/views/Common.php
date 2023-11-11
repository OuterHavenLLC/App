<?php
 Class Common extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function DesignView(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $dv = $data["DV"] ?? "";
   $r = (!empty($dv)) ? $this->core->PlainText([
    "BBCodes" => 1,
    "Data" => $dv,
    "Decode" => 1,
    "Display" => 1,
    "HTMLDecode" => 1
   ]) : $this->core->Element([
    "p", "Add content to reveal its design...", ["class" => "CenterText"]
   ]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Income(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $r = [
    "Body" => "The requested Income Disclosure could not be found.",
    "Header" => "Not Found"
   ];
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($username)) {
    $accessCode = "Accepted";
    $_Day = $this->core->Extension("ca72b0ed3686a52f7db1ae3b2f2a7c84");
    $_Month = $this->core->Extension("2044776cf5f8b7307b3c4f4771589111");
    $_Partner = $this->core->Extension("a10a03f2d169f34450792c146c40d96d");
    $_Sale = $this->core->Extension("a2adc6269f67244fc703a6f3269c9dfe");
    $_Year = $this->core->Extension("676193c49001e041751a458c0392191f");
    $username = base64_decode($username);
    $income = $this->core->Data("Get", ["id", md5($username)]) ?? [];
    $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $yearTable = "";
    foreach($income as $year => $yearData) {
     if(is_array($yearData)) {
      $monthTable = "";
      if($year != "UN") {
       foreach($yearData as $month => $monthData) {
        $dayTable = "";
        $partnerTable = "";
        $partners = $monthData["Partners"] ?? [];
        $sales = $monthData["Sales"] ?? [];
        $subtotal = 0;
        $tax = 0;
        $total = 0;
        foreach($partners as $partner => $info) {
         $partnerTable .= $this->core->Change([[
          "[IncomeDisclosure.Partner.Company]" => $info["Company"],
          "[IncomeDisclosure.Partner.Description]" => $info["Description"],
          "[IncomeDisclosure.Partner.DisplayName]" => $partner,
          "[IncomeDisclosure.Partner.Hired]" => $this->core->TimeAgo($info["Hired"]),
          "[IncomeDisclosure.Partner.Title]" => $info["Title"]
         ], $_Partner]);
        } foreach($sales as $day => $salesGroup) {
         $saleTable = "";
         foreach($salesGroup as $daySales => $daySale) {
          foreach($daySale as $sale => $product) {
           $price = str_replace(",", "", $product["Cost"]);
           $price = $price + str_replace(",", "", $product["Profit"]);
           $price = $price * $product["Quantity"];
           $subtotal = $subtotal + $price;
           $saleTable .= $this->core->Change([[
            "[IncomeDisclosure.Sale.Price]" => number_format($price, 2),
            "[IncomeDisclosure.Sale.Title]" => $product["Title"]
           ], $_Sale]);
          }
         }
         $dayTable .= $this->core->Change([[
          "[IncomeDisclosure.Day]" => $day,
          "[IncomeDisclosure.Day.Sales]" => $saleTable
         ], $_Day]);
        }
        $subtotal = str_replace(",", "", $subtotal);
        $commission = number_format($subtotal * (5.00 / 100), 2);
        $tax = $shop["Tax"] ?? 10.00;
        $tax = number_format($subtotal * ($tax / 100), 2);
        $total = number_format($subtotal - $commission - $tax, 2);
        $monthTable .= $this->core->Change([[
         "[IncomeDisclosure.Table.Month]" => $this->ConvertCalendarMonths($month),
         "[IncomeDisclosure.Table.Month.Commission]" => $commission,
         "[IncomeDisclosure.Table.Month.Partners]" => $partnerTable,
         "[IncomeDisclosure.Table.Month.Sales]" => $dayTable,
         "[IncomeDisclosure.Table.Month.Subtotal]" => number_format($subtotal, 2),
         "[IncomeDisclosure.Table.Month.Tax]" => $tax,
         "[IncomeDisclosure.Table.Month.Total]" => $total
        ], $_Month]);
       }
       $yearTable .= $this->core->Change([[
        "[IncomeDisclosure.Table.Year]" => $year,
        "[IncomeDisclosure.Table.Year.Lists]" => $monthTable
       ], $_Year]);
      }
     }
    }
    $yearTable = (!empty($income)) ? $yearTable : $this->core->Element([
     "h3", "No earnings to report...", [
      "class" => "CenterText",
      "style" => "margin:0.5em"
     ]
    ]);
    $r = $this->core->Change([[
     "[IncomeDisclosure.DisplayName]" => $t["Personal"]["DisplayName"],
     "[IncomeDisclosure.Gallery.Title]" => $shop["Title"],
     "[IncomeDisclosure.Table]" => $yearTable
    ], $this->core->Extension("4ab1c6f35d284a6eae66ebd46bb88d5d")]);
   } if($pub == 1) {
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
    "ResponseType" => "View"
   ]);
  }
  function MemberGrid(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $list = $data["List"] ?? "";
   $rows = $data["Rows"] ?? 9;
   $type = $data["Type"] ?? "Web";
   $r = $this->core->Element(["p", "None, yet..."]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($list)) {
    $i = 0;
    $list = json_decode(base64_decode($list), true);
    $list = $this->core->ShuffleList($list);
    $r = "";
    foreach($list as $key => $value) {
     $t = ($key == $you) ? $y : $this->core->Member($key);
     if(!empty($t["Login"])) {
      $i++;
      $r .= $this->core->Element([
       "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
        "class" => "OpenCard Small",
        "data-view" => base64_encode("v=".base64_encode("Profile:Home")."&CARD=1&UN=".base64_encode($t["Login"]["Username"]))
       ]
      ]);
     }
    }
    $r = ($i == 0) ? $this->core->Element([
     "p", "None, yet..."
    ]) : $r;
    $r = $this->core->Element([
     "h4", "Contributors", ["class" => "UpperCase"]
    ]).$this->core->Element([
     "div", $r, ["class" => "SideScroll"]
    ]);
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
  function SubscribeSection(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Content Identifier or Type are missing."
   ];
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($type)) {
    $accessCode = "Accepted";
    $check = 0;
    $processor = "";
    $r = "";
    $subscribers = [];
    if($type == "Article") {
     $article = $this->core->Data("Get", ["pg", $id]) ?? [];
     $check = ($article["UN"] != $you) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("Page:Subscribe"));
     $subscribers = $article["Subscribers"] ?? [];
     $title = $article["Title"];
    } elseif($type == "Blog") {
     $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
     $check = ($blog["UN"] != $you) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("Blog:Subscribe"));
     $subscribers = $shop["Subscribers"] ?? [];
     $title = $blog["Title"];
    } elseif($type == "Shop") {
     $check = (md5($you) != $id) ? 1 : 0;
     $processor = base64_encode("v=".base64_encode("Shop:Subscribe"));
     $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
     $subscribers = $shop["Subscribers"] ?? [];
     $title = $shop["Title"];
    } if($check == 1 && $this->core->ID != $you) {
     $text = (in_array($you, $subscribers)) ? "Unsubscribe" : "Subscribe";
     $r = $this->core->Change([[
      "[Subscribe.ContentID]" => $id,
      "[Subscribe.ID]" => $id,
      "[Subscribe.Processor]" => $processor,
      "[Subscribe.Text]" => $text,
      "[Subscribe.Title]" => $title
     ], $this->core->Extension("489a64595f3ec2ec39d1c568cd8a8597")]);
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
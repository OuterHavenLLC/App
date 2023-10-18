<?php
 Class Common extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Blacklist(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $r = $this->core->Change([[
    "[Blacklist.Categories]" => "[base]/base/JD.php?_API=OH&v=".base64_encode("Common:BlacklistCategories")
   ], $this->core->Page("03d53918c3da9fbc174f94710182a8f2")]);
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function BlacklistCategories(array $a) {
   $accessCode = "Accepted";
   $r = "";
   $y = $this->you;
   $y = $y["Blocked"] ?? [];
   foreach($y as $key => $value) {
    $r .= $this->core->Element(["button", $key, [
     "class" => "LI",
     "data-fst" => base64_encode("v=".base64_encode("Search:Containers")."&st=BL&BL=".base64_encode($key)."', '".md5("Blacklist$key"))
    ]]);
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
  function Illegal(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Content Identifier is missing."
   ];
   $y = $this->you;
   if(!empty($id)) {
    $accessCode = "Accepted";
    $id = explode(";", base64_decode($id));
    $att = "";
    $body = "";
    if(!empty($id[0]) && !empty($id[1])) {
     if($id[0] == "Album" && !empty($id[2])) {
      $x = $this->core->Data("Get", ["fs", md5($id[1])]) ?? [];
      $x = $x["Albums"][$id[2]] ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Blog") {
      $x = $this->core->Data("Get", ["blg", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "BlogPost") {
      $x = $this->core->Data("Get", ["bp", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Comment" && !empty($id[2])) {
      $x = $this->core->Data("Get", ["conversation", $id[1]]) ?? [];
      $x = $x[$id[2]] ?? [];
      if(!empty($x["DLC"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["DLC"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1
      ]);
     } elseif($id[0] == "File" && !empty($id[2])) {
      $x = $this->core->Data("Get", ["fs", md5($id[1])]) ?? [];
      $x = $x["Files"][$id[2]] ?? [];
      $att = $this->core->GetAttachmentPreview([
       "DLL" => $x,
       "T" => $id[1],
       "Y" => $y["Login"]["Username"]
      ]).$this->core->Element(["div", NULL, [
       "class" => "NONAME",
       "style" => "height:0.5em"
      ]]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Forum") {
      $x = $this->core->Data("Get", ["pf", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "ForumPost") {
      $x = $this->core->Data("Get", ["post", $id[1]]) ?? [];
      if(!empty($x["Attachments"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
     } elseif($id[0] == "Page") {
      $x = $this->core->Data("Get", ["pg", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Product") {
      $x = $this->core->Data("Get", ["miny", $id[1]]) ?? [];
      $att = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->core->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "StatusUpdate") {
      $x = $this->core->Data("Get", ["su", $id[1]]) ?? [];
      if(!empty($x["Attachments"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->core->Element(["p", $this->core->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
     }
    }
    $processor = "v=".base64_encode("Common:SaveIllegal")."&ID=[ID]";
    $r = $this->core->Change([[
     "[Illegal.Content]" => $body,
     "[Illegal.Content.LiveView]" => $att,
     "[Illegal.ID]" => base64_encode(implode(";", $id)),
     "[Illegal.Processor]" => base64_encode($processor)
    ], $this->core->Page("0eaea9fae43712d8c810c737470021b3")]);
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
    $_Day = $this->core->Page("ca72b0ed3686a52f7db1ae3b2f2a7c84");
    $_Month = $this->core->Page("2044776cf5f8b7307b3c4f4771589111");
    $_Partner = $this->core->Page("a10a03f2d169f34450792c146c40d96d");
    $_Sale = $this->core->Page("a2adc6269f67244fc703a6f3269c9dfe");
    $_Year = $this->core->Page("676193c49001e041751a458c0392191f");
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
    ], $this->core->Page("4ab1c6f35d284a6eae66ebd46bb88d5d")]);
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
  function SaveIllegal(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->core->FixMissing($data, ["ID", "Type"]);
   $id = $data["ID"];
   $type = $data["Type"];
   $r = [
    "Body" => "The Content Identifier or Type are missing."
   ];
   $y = $this->you;
   if(!empty($id) && !empty($type)) {
    $r = [
     "Body" => "The Content Type is incorrect.<br/>ID: $id<br/>Type: ".base64_decode($type)
    ];
    $type = base64_decode($type);
    $types = [
     "CriminalActs",
     "ChildPorn",
     "FairUse",
     "Privacy",
     "Terrorism"
    ];
    if(in_array($type, $types)) {
     $accessCode = "Accepted";
     $id = explode(";", base64_decode($id));
     $limit = $this->core->config["App"]["Illegal"] ?? 777;
     $weight = ($type == "CriminalActs") ? ($limit / 1000) : 0;
     $weight = ($type == "ChildPorn") ? ($limit / 3) : $weight;
     $weight = ($type == "FairUse") ? ($limit / 100000) : $weight;
     $weight = ($type == "Privacy") ? ($limit / 10000) : $weight;
     $weight = ($type == "Terrorism") ? ($limit / 100) : $weight;
     if(!empty($id[0]) && !empty($id[1])) {
      if($id[0] == "Album" && !empty($id[2])) {
       $x = $this->core->Data("Get", ["fs", md5($id[1])]) ?? [];
       if(!empty($x)) {
        $dlc = $x["Albums"][$id[2]] ?? [];
        $dlc["Illegal"] = $dlc["Illegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] + $weight;
        $x["Albums"][$id[2]] = $dlc;
        #$this->core->Data("Save", ["fs", md5($id[1]), $x]);
       }
      } elseif($id[0] == "Blog") {
       $x = $this->core->Data("Get", ["blg", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        #$this->core->Data("Save", ["blg", $id[1], $x]);
       }
      } elseif($id[0] == "BlogPost") {
       $x = $this->core->Data("Get", ["bp", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        #$this->core->Data("Save", ["bp", $id[1], $x]);
       }
      } elseif($id[0] == "Comment" && !empty($id[2])) {
       $x = $this->core->Data("Get", ["conversation", $id[1]]) ?? [];
       if(!empty($x)) {
        $comment = $x[$id[2]] ?? [];
        $comment["Illegal"] = $comment["Illegal"] ?? 0;
        $comment["Illegal"] = $comment["Illegal"] + $weight;
        $x[$id[2]] = $comment;
        #$this->core->Data("Save", ["conversation", $id[1], $x]);
       }
      } elseif($id[0] == "File" && !empty($id[2])) {
       $x = $this->core->Data("Get", ["fs", md5($id[1])]) ?? [];
       if(!empty($x)) {
        $dlc = $x["Files"][$id[2]] ?? [];
        $dlc["Illegal"] = $dlc["Illegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] + $weight;
        $x["Files"][$id[2]] = $dlc;
        #$this->core->Data("Save", ["fs", md5($id[1]), $x]);
       }
      } elseif($id[0] == "Forum") {
       $x = $this->core->Data("Get", ["pf", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        #$this->core->Data("Save", ["pf", $id[1], $x]);
       }
      } elseif($id[0] == "ForumPost") {
       $x = $this->core->Data("Get", ["post", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        #$this->core->Data("Save", ["post", $id[1], $x]);
       }
      } elseif($id[0] == "Page") {
       $x = $this->core->Data("Get", ["pg", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        #$this->core->Data("Save", ["pg", $id[1], $x]);
       }
      } elseif($id[0] == "Product") {
       $x = $this->core->Data("Get", ["miny", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        #$this->core->Data("Save", ["miny", $id[1], $x]);
       }
      } elseif($id[0] == "StatusUpdate") {
       $x = $this->core->Data("Get", ["su", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        #$this->core->Data("Save", ["su", $id[1], $x]);
       }
      }
     }
     $r = [
      "Body" => "The Content was reported.",
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
     ], $this->core->Page("489a64595f3ec2ec39d1c568cd8a8597")]);
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
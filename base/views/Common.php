<?php
 Class Common extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->system->Member($this->system->Username());
  }
  function Blacklist(array $a) {
   $data = $a["Data"] ?? [];
   $y = $this->you;
   return $this->system->Change([[
    "[Blacklist.Categories]" => "[base]/base/JD.php?_API=OH&v=".base64_encode("Common:BlacklistCategories")
   ], $this->system->Page("03d53918c3da9fbc174f94710182a8f2")]);
  }
  function BlacklistCategories(array $a) {
   $r = "";
   $y = $this->you;
   $y = $y["Blocked"] ?? [];
   foreach($y as $key => $value) {
    $r .= $this->system->Element(["button", $key, [
     "class" => "LI",
     "onclick" => "FST('N/A', 'v=".base64_encode("Search:Containers")."&st=BL&BL=".base64_encode($key)."', '".md5("Blacklist$key")."');"
    ]]);
   }
   return $r;
  }
  function DesignView(array $a) {
   $data = $a["Data"] ?? [];
   $dv = $data["DV"] ?? "";
   $r = (!empty($dv)) ? $this->system->PlainText([
    "BBCodes" => 1,
    "Data" => $dv,
    "Decode" => 1,
    "Display" => 1,
    "HTMLDecode" => 1
   ]) : $this->system->Element([
    "p", "Add content to reveal its design...", ["class" => "CenterText"]
   ]);
   return $r;
  }
  function Illegal(array $a) {
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = $this->system->Change([[
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Content Identifier is missing."
   ], $this->system->Page("eac72ccb1b600e0ccd3dc62d26fa5464")]);
   $y = $this->you;
   if(!empty($id)) {
    $id = explode(";", base64_decode($id));
    $att = "";
    $body = "";
    if(!empty($id[0]) && !empty($id[1])) {
     if($id[0] == "Album" && !empty($id[2])) {
      $x = $this->system->Data("Get", ["fs", md5($id[1])]) ?? [];
      $x = $x["Albums"][$id[2]] ?? [];
      $att = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->system->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Blog") {
      $x = $this->system->Data("Get", ["blg", $id[1]]) ?? [];
      $att = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->system->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "BlogPost") {
      $x = $this->system->Data("Get", ["bp", $id[1]]) ?? [];
      $att = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->system->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Comment" && !empty($id[2])) {
      $x = $this->system->Data("Get", ["conversation", $id[1]]) ?? [];
      $x = $x[$id[2]] ?? [];
      if(!empty($x["DLC"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["DLC"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1
      ]);
     } elseif($id[0] == "File" && !empty($id[2])) {
      $x = $this->system->Data("Get", ["fs", md5($id[1])]) ?? [];
      $x = $x["Files"][$id[2]] ?? [];
      $att = $this->system->GetAttachmentPreview([
       "DLL" => $x,
       "T" => $id[1],
       "Y" => $y["Login"]["Username"]
      ]).$this->system->Element([
       "div", NULL, ["class" => "NONAME", "style" => "height:0.5em"]
      ]);
      $body = $this->system->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Forum") {
      $x = $this->system->Data("Get", ["pf", $id[1]]) ?? [];
      $att = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->system->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "ForumPost") {
      $x = $this->system->Data("Get", ["post", $id[1]]) ?? [];
      if(!empty($x["Attachments"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
     } elseif($id[0] == "Page") {
      $x = $this->system->Data("Get", ["pg", $id[1]]) ?? [];
      $att = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->system->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "Product") {
      $x = $this->system->Data("Get", ["miny", $id[1]]) ?? [];
      $att = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Description"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
      $body = $this->system->Element(["h3", $x["Title"], [
       "class" => "UpperCase"
      ]]);
     } elseif($id[0] == "StatusUpdate") {
      $x = $this->system->Data("Get", ["su", $id[1]]) ?? [];
      if(!empty($x["Attachments"])) {
       $att = base64_encode("LiveView:InlineMossaic");
       $att = $this->view($att, ["Data" => [
        "ID" => base64_encode(implode(";", $x["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]);
      }
      $body = $this->system->Element(["p", $this->system->PlainText([
       "BBCodes" => 1,
       "Data" => $x["Body"],
       "Display" => 1,
       "HTMLDecode" => 1
      ])]);
     }
    }
    $processor = "v=".base64_encode("Common:SaveIllegal")."&ID=[ID]";
    $r = $this->system->Change([[
     "[Illegal.Content]" => $body,
     "[Illegal.Content.LiveView]" => $att,
     "[Illegal.ID]" => base64_encode(implode(";", $id)),
     "[Illegal.Processor]" => base64_encode($processor)
    ], $this->system->Page("0eaea9fae43712d8c810c737470021b3")]);
   }
   return $this->system->Card(["Front" => $r]);
  }
  function Income(array $a) {
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $r = $this->system->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The requested Income Disclosure could not be found."
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $username = $data["UN"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($username)) {
    $_Day = $this->system->Page("ca72b0ed3686a52f7db1ae3b2f2a7c84");
    $_Month = $this->system->Page("2044776cf5f8b7307b3c4f4771589111");
    $_Partner = $this->system->Page("a10a03f2d169f34450792c146c40d96d");
    $_Sale = $this->system->Page("a2adc6269f67244fc703a6f3269c9dfe");
    $_Year = $this->system->Page("676193c49001e041751a458c0392191f");
    $username = base64_decode($username);
    $income = $this->system->Data("Get", ["id", md5($username)]) ?? [];
    $shop = $this->system->Data("Get", ["shop", md5($username)]) ?? [];
    $t = ($username == $you) ? $y : $this->system->member($username);
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
         $partnerTable .= $this->system->Change([[
          "[IncomeDisclosure.Partner.Company]" => $info["Company"],
          "[IncomeDisclosure.Partner.Description]" => $info["Description"],
          "[IncomeDisclosure.Partner.DisplayName]" => $partner,
          "[IncomeDisclosure.Partner.Hired]" => $this->system->TimeAgo($info["Hired"]),
          "[IncomeDisclosure.Partner.Title]" => $info["Title"]
         ], $_Partner]);
        } foreach($sales as $day => $salesGroup) {
         $saleTable = "";
         foreach($salesGroup as $daySales => $daySale) {
          foreach($daySale as $id => $product) {
           $price = str_replace(",", "", $product["Cost"]);
           $price = $price + str_replace(",", "", $product["Profit"]);
           $price = $price * $product["Quantity"];
           $subtotal = $subtotal + $price;
           $saleTable .= $this->system->Change([[
            "[IncomeDisclosure.Sale.Price]" => number_format($price, 2),
            "[IncomeDisclosure.Sale.Title]" => $product["Title"]
           ], $_Sale]);
          }
         }
         $dayTable .= $this->system->Change([[
          "[IncomeDisclosure.Day]" => $day,
          "[IncomeDisclosure.Day.Sales]" => $saleTable
         ], $_Day]);
        }
        $subtotal = str_replace(",", "", $subtotal);
        $commission = number_format($subtotal * (5.00 / 100), 2);
        $tax = $shop["Tax"] ?? 10.00;
        $tax = number_format($subtotal * ($tax / 100), 2);
        $total = number_format($subtotal - $commission - $tax, 2);
        $monthTable .= $this->system->Change([[
         "[IncomeDisclosure.Table.Month]" => $this->ConvertCalendarMonths($month),
         "[IncomeDisclosure.Table.Month.Commission]" => $commission,
         "[IncomeDisclosure.Table.Month.Partners]" => $partnerTable,
         "[IncomeDisclosure.Table.Month.Sales]" => $dayTable,
         "[IncomeDisclosure.Table.Month.Subtotal]" => number_format($subtotal, 2),
         "[IncomeDisclosure.Table.Month.Tax]" => $tax,
         "[IncomeDisclosure.Table.Month.Total]" => $total
        ], $_Month]);
       }
       $yearTable .= $this->system->Change([[
        "[IncomeDisclosure.Table.Year]" => $year,
        "[IncomeDisclosure.Table.Year.Lists]" => $monthTable
       ], $_Year]);
      }
     }
    }
    $yearTable = (empty($id)) ? $this->system->Element([
     "h3", "No earnings to report...", [
      "class" => "CenterText",
      "style" => "margin:0.5em"
     ]
    ]) : $yearTable;
    $r = $this->system->Change([[
     "[IncomeDisclosure.DisplayName]" => $t["Personal"]["DisplayName"],
     "[IncomeDisclosure.Gallery.Title]" => $shop["Title"],
     "[IncomeDisclosure.Table]" => $yearTable
    ], $this->system->Page("4ab1c6f35d284a6eae66ebd46bb88d5d")]);
   }
   $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
    "Data" => ["Content" => $r]
   ]) : $r;
   return $r;
  }
  function MemberGrid(array $a) {
   $data = $a["Data"] ?? [];
   $list = $data["List"] ?? [];
   $rows = $data["Rows"] ?? 9;
   $type = $data["Type"] ?? "Web";
   $r = "&nbsp;";
   $y = $this->you;
   if(!empty($list)) {
    $list = $this->system->ShuffleList($list);
    $r = "";
    foreach($list as $key => $value) {
     $t = ($key == $y["Login"]["Username"]) ? $y : $this->system->Member($key);
     $r .= $this->system->Element([
      "button", $this->system->ProfilePicture($t, "margin:5%;width:90%"), [
       "class" => "Small dB2O",
       "data-e" => base64_encode("v=".base64_encode("Profile:Home")."&CARD=1&UN=".base64_encode($t["Login"]["Username"]))
      ]
     ]);
    }
    $r = $this->system->Element([
     "h4", "Contributors", ["class" => "UpperCase"]
    ]).$this->system->Element([
     "div", $r, ["class" => "SideScroll"]
    ]);
   }
   return $r;
  }
  function Reactions(array $a) {
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $crid = $data["CRID"] ?? "";
   $r = $this->system->Data("Get", ["react", $crid]) ?? [];
   $rd = $r["Dislike"] ?? [];
   $rdi = count($rd);
   $rd = (in_array($y["Login"]["Username"], $rd)) ? 1 : 0;
   $rl = $r["Like"] ?? [];
   $rli = count($rl);
   $rl = (in_array($y["Login"]["Username"], $rl)) ? 1 : 0;
   $t = $data["Type"] ?? "";
   $u = "v=".base64_encode("Common:SaveReaction")."&CRID=[CRID]&type=[type]";
   $b3d = ($rd == 1) ? "BBB " : "";
   $b3l = ($rl == 1) ? "BBB " : "";
   $tpl = ($t == 1) ? "b6ce0e83f7b83ed314cafd5f94523752" : "";
   $tpl = ($t == 2) ? "7e2608e18e95e25fc3b04fe265e540f5" : $tpl;
   $tpl = ($t == 3) ? "39a550decb7f3f764445b33e847a7042" : $tpl;
   $tpl = ($t == 4) ? "cfa1ecf724126fd8a95d750c95f8179e" : $tpl;
   $tpl = $this->system->Page($tpl);
   return $this->system->Change([[
    "[Reaction.CRID]" => base64_encode($data["CRID"]),
    "[Reaction.BBB.Dislike]" => $b3d,
    "[Reaction.BBB.Like]" => $b3l,
    "[Reaction.Dislike]" => $this->system->ShortNumber($rdi),
    "[Reaction.Like]" => $this->system->ShortNumber($rli),
    "[Reaction.Processor]" => base64_encode($u)
   ], $tpl]);
  }
  function SaveBlacklist(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["BC", "BU", "content", "list"]);
   $b2 = [];
   $bc = base64_decode($data["BC"]);
   $bl = "Blocked";
   $bu = base64_decode($data["BU"]);
   $c = base64_decode($data["content"]);
   $l = base64_decode($data["list"]);
   $y = $this->you;
   $y[$bl][$l] = $y[$bl][$l] ?? [];
   foreach($y[$bl][$l] as $k => $v) {
    if($v != $c) {
     array_push($b2, $v);
    }
   } if($bc == "B") {
    array_push($b2, $c);
    $r = "Unblock $bu";
   } elseif($bc == "U") {
    $r = "Block $bu";
   }
   $y[$bl][$l] = array_unique($b2);
   $this->system->Data("Save", ["mbr", md5($y["Login"]["Username"]), $y]);
   return $r;
  }
  function SaveIllegal(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["ID", "Type"]);
   $id = $data["ID"];
   $type = $data["Type"];
   $r = $this->system->Dialog([
    "Body" => $this->system->Element([
     "p", "The Content Identifier or Type are missing."
    ]),
    "Header" => "Error"
   ]);
   $y = $this->you;
   if(!empty($id) && !empty($type)) {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", "The Content Type is incorrect.<br/>ID: $id<br/>Type: ".base64_decode($type)]),
     "Header" => "Error"
    ]);
    $type = base64_decode($type);
    $types = [
     "CriminalActs",
     "ChildPorn",
     "FairUse",
     "Privacy",
     "Terrorism"
    ];
    if(in_array($type, $types)) {
     $id = explode(";", base64_decode($id));
     $limit = $this->system->core["SYS"]["Illegal"] ?? 777;
     $weight = ($type == "CriminalActs") ? ($limit / 1000) : 0;
     $weight = ($type == "ChildPorn") ? ($limit / 3) : $weight;
     $weight = ($type == "FairUse") ? ($limit / 100000) : $weight;
     $weight = ($type == "Privacy") ? ($limit / 10000) : $weight;
     $weight = ($type == "Terrorism") ? ($limit / 100) : $weight;
     if(!empty($id[0]) && !empty($id[1])) {
      if($id[0] == "Album" && !empty($id[2])) {
       $x = $this->system->Data("Get", ["fs", md5($id[1])]) ?? [];
       if(!empty($x)) {
        $dlc = $x["Albums"][$id[2]] ?? [];
        $dlc["Illegal"] = $dlc["Illegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] + $weight;
        $x["Albums"][$id[2]] = $dlc;
        $this->system->Data("Save", ["fs", md5($id[1]), $x]);
       }
      } elseif($id[0] == "Blog") {
       $x = $this->system->Data("Get", ["blg", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        $this->system->Data("Save", ["blg", $id[1], $x]);
       }
      } elseif($id[0] == "BlogPost") {
       $x = $this->system->Data("Get", ["bp", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        $this->system->Data("Save", ["bp", $id[1], $x]);
       }
      } elseif($id[0] == "Comment" && !empty($id[2])) {
       $x = $this->system->Data("Get", ["conversation", $id[1]]) ?? [];
       if(!empty($x)) {
        $comment = $x[$id[2]] ?? [];
        $comment["Illegal"] = $comment["Illegal"] ?? 0;
        $comment["Illegal"] = $comment["Illegal"] + $weight;
        $x[$id[2]] = $comment;
        $this->system->Data("Save", ["conversation", $id[1], $x]);
       }
      } elseif($id[0] == "File" && !empty($id[2])) {
       $x = $this->system->Data("Get", ["fs", md5($id[1])]) ?? [];
       if(!empty($x)) {
        $dlc = $x["Files"][$id[2]] ?? [];
        $dlc["Illegal"] = $dlc["Illegal"] ?? 0;
        $dlc["Illegal"] = $dlc["Illegal"] + $weight;
        $x["Files"][$id[2]] = $dlc;
        $this->system->Data("Save", ["fs", md5($id[1]), $x]);
       }
      } elseif($id[0] == "Forum") {
       $x = $this->system->Data("Get", ["pf", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        $this->system->Data("Save", ["pf", $id[1], $x]);
       }
      } elseif($id[0] == "ForumPost") {
       $x = $this->system->Data("Get", ["post", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        $this->system->Data("Save", ["post", $id[1], $x]);
       }
      } elseif($id[0] == "Page") {
       $x = $this->system->Data("Get", ["pg", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        $this->system->Data("Save", ["pg", $id[1], $x]);
       }
      } elseif($id[0] == "Product") {
       $x = $this->system->Data("Get", ["miny", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        $this->system->Data("Save", ["miny", $id[1], $x]);
       }
      } elseif($id[0] == "StatusUpdate") {
       $x = $this->system->Data("Get", ["su", $id[1]]) ?? [];
       if(!empty($x)) {
        $x["Illegal"] = $x["Illegal"] ?? 0;
        $x["Illegal"] = $x["Illegal"] + $weight;
        $this->system->Data("Save", ["su", $id[1], $x]);
       }
      }
     }
     $r = $this->system->Dialog([
      "Body" => $this->system->Element(["p", "The Content was reported."]),
      "Header" => "Done"
     ]);
    }
   }
   return $r;
  }
  function SaveReaction(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->FixMissing($data, ["CRID", "type"]);
   $crid = $data["CRID"];
   $type = $data["type"];
   $r = "NO CRID";
   $y = $this->you;
   if(!empty($crid) && !empty($crid)) {
    $accessCode = "Accepted";
    $crid = base64_decode($crid);
    $type = base64_decode($type);
    $react = $this->system->Data("Get", ["react", $crid]) ?? [];
    $dl = $react[$type] ?? [];
    if(!in_array($y["Login"]["Username"], $dl)) {
     array_push($dl, $y["Login"]["Username"]);
     $react[$type] = $dl;
     $dl = 1;
    } else {
     $r2 = [];
     foreach($dl as $k => $v) {
      if($v != $y["Login"]["Username"]) {
       array_push($r2, $y["Login"]["Username"]);
      }
     }
     $react[$type] = $r2;
     $dl = 0;
    }
    $this->system->Data("Save", ["react", $crid, $react]);
    $r = $dl;
   }
   $dlc = count($react[$type]);
   return $this->JSONResponse([$ec, $r, $dl, $dlc]);
  }
  function SaveSignIn(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, ["PW", "UN"]);
   $i = 0;
   $password = $data["PW"];
   $r = "An internal error has ocurred.";
   $responseType = "Dialog";
   $username = $data["UN"];
   if(empty($password) || empty($username)) {
    if(empty($password)) {
     $field = "Password";
    } elseif(empty($username)) {
     $field = "Username";
    }
    $r = "The $field is missing.";
   } else {
    $members = $this->system->DatabaseSet("MBR");
    foreach($members as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $member = $this->system->Data("Get", ["mbr", $value]) ?? [];
     $member = $member["Login"]["Username"] ?? "";
     if($username == $member) {
      $i++;
     }
    } if($i > 0) {
     $member = $this->system->Data("Get", ["mbr", md5($username)]) ?? [];
     $password = md5($password);
     if($password == $member["Login"]["Password"]) {
      $accessCode = "Accepted";
      $responseType = "SignIn";
      $this->system->Statistic("LI");
      $r = $this->system->Encrypt($member["Login"]["Username"].":".$member["Login"]["Password"]);
     } elseif($password != $member["Login"]["Password"]) {
      $r = "The Passwords do not match.";
     } elseif($username != $member["Login"]["Username"]) {
      $r = "The Usernames do not match.";
     } else {
      $r = $r;
     }
    } else {
     $r = "The Member <em>$username</em> could not be found.";
    }
   } if($accessCode == "Denied") {
    $r = $this->system->Dialog([
     "Body" => $this->system->Element(["p", $r]),
     "Header" => "Sign In Failed"
    ]);
   }
   return $this->system->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function SaveSignUp(array $a) {
   $data = $a["Data"] ?? [];
   $data = $this->system->DecodeBridgeData($data);
   $data = $this->system->FixMissing($data, [
    "PIN",
    "PW",
    "SOE",
    "UN",
    "email",
    "gender",
    "name"
   ]);
   $ec = "Denied";
   $r = $this->system->JSONResponse([$ec, $r]);
   if(!empty($data)) {
    $age = date("Y") - $data["BirthYear"];
    $i = 0;
    $mAge = $this->system->core["MinRegAge"];
    $ck = ($age > $mAge) ? 1 : 0;
    $fn = ($data["gender"] == "Male") ? "John" : "Jane";
    $pw = $data["PW"];
    $un = $this->system->CallSign($data["UN"]);
    $mbr = $this->system->DatabaseSet("MBR");
    foreach($mbr as $k => $v) {
     $v = str_replace("c.oh.mbr.", "", $v);
     $m = $this->system->Data("Get", ["mbr", $v]) ?? [];
     if($i == 0 && $m["Login"]["Username"] == $un) {
      $i++;
     }
    } if($ck == 0) {
     $r = $this->system->JSONResponse([$ec, "AGE", [$age, $mAge]]);
    } elseif($i > 0) {
     $r = $this->system->JSONResponse([$ec, "ME"]);
    } elseif(empty($data["PIN"])) {
     $r = $this->system->JSONResponse([$ec, "PIN"]);
    } elseif(empty($data["PW"])) {
     $r = $this->system->JSONResponse([$ec, "PW"]);
    } elseif(empty($data["UN"])) {
     $r = $this->system->JSONResponse([$ec, "UN"]);
    } elseif(empty($data["email"])) {
     $r = $this->system->JSONResponse([$ec, "EM"]);
    } else {
     $birthMonth = $data["BirthMonth"] ?? 10;
     $birthYear = $data["BirthYear"] ?? 1995;
     if($data["SOE"] == 1) {
      $x = $this->system->Data("Get", ["x", md5("ContactList")]) ?? [];
      $x[$data["email"]] = [
       "SendOccasionalEmails" => $data["SOE"],
       "UN" => $un,
       "email" => $data["email"],
       "name" => $fn,
       "phone" => "N/A",
      ];
      $this->system->Data("Save", ["x", md5("ContactList"), $x]);
     }
     $this->system->Data("Save", [
      "cms",
      md5($un),
      ["Contacts" => [], "Requests" => []]
     ]);
     $this->system->Data("Save", ["fs", md5($un), [
      "Albums" => [
       md5("unsorted") => [
        "ID" => md5("unsorted"),
        "Created" => $this->system->timestamp,
        "ICO" => "",
        "Modified" => $this->system->timestamp,
        "Title" => "Unsorted",
        "Description" => "Files are uploaded here by default.",
        "NSFW" => 0,
        "Privacy" => md5("Public")
       ]
      ],
      "Files" => []
     ]]);
     $this->system->Data("Save", ["mbr", md5($un), $this->system->NewMember([
      "Age" => $age,
      "BirthMonth" => $birthMonth,
      "BirthYear" => $birthYear,
      "DisplayName" => $un,
      "Email" => $data["email"],
      "FirstName" => $fn,
      "Gender" => $data["gender"],
      "Password" => $pw,
      "PIN" => md5($data["PIN"]),
      "Username" => $un
     ])]);
     $this->system->Data("Save", ["stream", md5($un), []]);
     $this->system->Data("Save", ["shop", md5($un), [
      "Contributors" => [
       $un => [
        "Company" => "My Company",
        "Description" => "Oversees general operations and administrative duties.",
        "Hired" => $this->system->timestamp,
        "Paid" => 0,
        "Title" => "Founder & CEO"
       ]
      ],
      "CoverPhoto" => "",
      "Description" => "",
      "Live" => 0,
      "Modified" => $this->system->timestamp,
      "Open" => 1,
      "Processing" => [
       "BraintreeMerchantID" => "",
       "BraintreePrivateKey" => "",
       "BraintreePublicKey" => "",
       "BraintreeToken" => "",
       "PayPalEmail" => ""
      ],
      "Products" => [],
      "Title" => "My Shop",
      "Welcome" => "<h1>Welcome</h1>\r\n<p>Welcome to my shop!</p>"
     ]]);
     $this->system->Statistic("MBR");
     $r = $this->system->JSONResponse([
      "Accepted", $this->system->Encrypt("$un:$pw"), "Data" => $d
     ]);
    }
   }
   return $r;
  }
  function SignIn(array $a) {
   return $this->system->Dialog([
    "Body" => $this->system->Page("ff434d30a54ee6d6bbe5e67c261b2005"),
    "Header" => "Sign In",
    "Option" => $this->system->Element(["button", "Cancel", [
     "class" => "dBC v2 v2w"
    ]]),
    "Option2" => $this->system->Element(["button", "Sign In", [
     "class" => "BBB SendData v2 v2w",
     "data-form" => ".SignIn",
     "data-processor" => base64_encode("v=".base64_encode("Common:SaveSignIn"))
    ]])
   ]);
  }
  function SignUp(array $a) {
   $ac = base64_encode("Common:AvailabilityCheck");
   return $this->system->Card([
    "Front" => $this->system->Change([[
     "[SignUp.Age.Month]" => $this->system->Select("BirthMonth", "req v2w"),
     "[SignUp.Age.Year]" => $this->system->Select("BirthYear", "req v2w"),
     "[SignUp.AvailabilityView]" => base64_encode("v=$ac&at=".base64_encode("UN")."&av="),
     "[SignUp.Gender]" => $this->system->Select("gender", "req"),
     "[SignUp.MinAge]" => $this->system->core["minAge"],
     "[SignUp.SendOccasionalEmails]" => $this->system->Select("SOE", "req v2w")
    ], $this->system->Page("c48eb7cf715c4e41e2fb62bdfa60f198")]),
    "FrontButton" => $this->system->Element(["button", "Sign Up", [
     "class" => "BBB SendData v2",
     "data-form" => "#register",
     "data-processor" => base64_encode("v=".base64_encode("Common:SaveSignUp"))
    ]])
   ]);
  }
  function SwitchMember(array $a) {
   return $this->system->Dialog([
    "Body" => $this->system->Page("ff434d30a54ee6d6bbe5e67c261b2005"),
    "Header" => "Switch Members",
    "Option" => $this->system->Element(["button", "Cancel", [
     "class" => "dBC v2 v2w"
    ]]),
    "Option2" => $this->system->Element(["button", "Switch", [
     "class" => "BBB SendData v2 v2w",
     "data-form" => "#login",
     "data-processor" => base64_encode("v=".base64_encode("Common:SaveSignIn"))
    ]])
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
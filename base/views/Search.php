<?php
 Class Search extends GW {
  function __construct() {
   parent::__construct();
   $this->illegal = $this->core->config["App"]["Illegal"] ?? 777;
   $this->lists = base64_encode("Search:Lists");
   $this->you = $this->core->Member($this->core->Username());
  }
  function Containers(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $b2 = $data["b2"] ?? "";
   $card = $data["CARD"] ?? 0;
   $h = "";
   $i = 0;
   $st = $data["st"] ?? "";
   $lpg = $data["lPG"] ?? $st;
   $pub = $data["pub"] ?? 0;
   $query = $data["query"] ?? "";
   $sta = $this->core->config["App"]["SearchIDs"];
   $ck = (!empty($st) && in_array($st, $sta)) ? 1 : 0;
   $li = "v=".$this->lists."&query=$query&st=$st";
   $lit = md5($st.$this->core->timestamp.rand(0, 1776));
   $lo = "";
   $r = [
    "Body" => "The List Type is missing.",
    "Header" => "Not Found"
   ];
   $extension = "6dc4eecde24bf5f5e70da253aaac2b68";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   if($ck == 1) {
    $accessCode = "Accepted";
    if($st == "ADM-LLP") {
     $h = "Network Extensions";
     $lis = "Search Extensions";
     $lo =  ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Page:Edit")."&new=1")
      ]
     ]) : "";
    } elseif($st == "ADM-MassMail") {
     $h = "Mass Mail";
     $lis = "Search Pre-Sets";
     $lo =  ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Company:MassMail")."&new=1")
      ]
     ]) : "";
    } elseif($st == "BGP") {
     $data = $this->core->FixMissing($data, ["BLG"]);
     $h = "Blog Posts";
     $li .= "&ID=".$data["ID"];
     $lis = "Search Posts";
    } elseif($st == "BL") {
     $bl = base64_decode($data["BL"]);
     $h = "$bl Blacklist";
     $li .= "&BL=".$data["BL"];
     $lis = "Search $bl Blacklist";
     $extension = "6dc4eecde24bf5f5e70da253aaac2b68";
    } elseif($st == "BLG") {
     $h = "Blogs";
     $li .= "&b2=Blogs&lPG=$st";
     $lis = "Search Blogs";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "Bulletins") {
     $h = "Bulletins";
     $lis = "Search Bulletins";
    } elseif($st == "CA") {
     $h = "Community Archive";
     $li .= "&b2=".urlencode("the Archive")."&lPG=$lpg";
     $lis = "Search Articles";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "CART") {
     $t = $data["Username"] ?? $you;
     $t = ($t == $you) ? $y : $this->core->Member($t);
     $shopID = md5($t["Login"]["Username"]);
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     $li .= "&ID=$shopID";
     $lis = "Search ".$shop["Title"];
     $extension = "e58b4fc5070b14c01c88c28050547285";
    } elseif($st == "Chat" || $st == "GroupChat") {
     $group = $data["Group"] ?? 0;
     $integrated = $data["Integrated"] ?? 0;
     $oneOnOne = $data["1on1"] ?? 0;
     $h = "1:1 Chat";
     $h = ($group == 1) ? "Group Chat" : $h;
     $li .= "&1on1=$oneOnOne&Group=$group&Integrated=$integrated";
     $lis = "Search $h";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "Contacts") {
     $h = "Contact Manager";
     $lis = "Search Contacts";
    } elseif($st == "ContactsProfileList") {
     $data = $this->core->FixMissing($data, ["UN"]);
     $un = base64_decode($data["UN"]);
     $ck = ($un == $y["Login"]["Username"]) ? 1 : 0;
     $t = ($ck == 1) ? $y : $this->core->Member($un);
     $h = ($ck == 1) ? "Your Contacts" : $t["Personal"]["DisplayName"]."'s Contacts";
     $li .= "&b2=$b2&lPG=$lpg&UN=".$data["UN"];
     $lis = "Search Contacts";
    } elseif($st == "ContactsRequests") {
     $h = "Contact Requests";
     $lis = "Search Contact Requests";
    } elseif($st == "Contributors") {
     $id = $data["ID"] ?? "";
     $li .= "&ID=$id&Type=".$data["Type"];
     $lis = "Search Contributors";
     $type = base64_decode($data["Type"]);
     if($type == "Article") {
      $h = "Article Contributors";
      $id = base64_decode($id);
      $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
      $lo = ($Page["UN"] == $you && $notAnon == 1) ? $this->core->Element([
       "button", "+", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Page:Invite")."&ID=$id")
       ]
      ]) : "";
     } elseif($type == "Blog") {
      $id = base64_decode($id);
      $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
      $h = "Blog Contributors";
     } elseif($type == "Forum") {
      $id = base64_decode($id);
      $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
      $h = "Forum Members";
      $lo = ($forum["UN"] == $you && $notAnon == 1) ? $this->core->Element([
       "button", "Invite Members", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Forum:Invite")."&FID=".base64_encode($id))
       ]
      ]) : "";
     } elseif($type == "Shop") {
      $h = "Partners";
      $id = base64_decode($id);
      $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
      $lo = ($id == md5($you) && $notAnon == 1) ? $this->core->Element([
       "button", "Hire Members", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Shop:EditPartner")."&new=1")
       ]
      ]) : "";
     } else {
      $h = "Contributors";
      $lis = "Search Contributors";
     }
    } elseif($st == "DC") {
     $dce = base64_encode("DiscountCode:Edit");
     $h = "Discount Codes";
     $lis = "Search Codes";
     $lo = ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$dce&new=1")
      ]
     ]) : "";
    } elseif($st == "Feedback") {
     $h = "Feedback";
     $li .= "&lPG=$lpg";
     $lis = "Search Feedback";
    } elseif($st == "Forums") {
     $h = "Forums";
     $li .= "&lPG=$lpg";
     $lis = "Search Private and Public Forums";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "Forums-Admin") {
     $h = "Administrators";
     $li .= "&ID=".$data["ID"];
     $lis = "Search Administrators";
    } elseif($st == "Forums-Posts") {
     $id = $data["ID"] ?? "";
     $id = base64_decode($id);
     $f = $this->core->Data("Get", ["pf", $id]) ?? [];
     $h = "Forum Posts";
     $li .= "&ID=$id";
     $lis = "Search Posts from ".$f["Title"];
    } elseif($st == "Knowledge") {
     $h = "Knowledge Base";
     $lis = "Search Q&As";
     $extension = "8568ac7727dae51ee4d96334fa891395";
    } elseif($st == "Mainstream") {
     $h = "The ".$st;
     $lis = "Search the Mainstream";
     $lo = $this->core->Element(["button", "Say Something", [
      "class" => "BBB MobileFull OpenCard v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($y["Login"]["Username"]))
     ]]);
     $extension = "f2513ac8d0389416b680c75ed5667774";
    } elseif($st == "MBR") {
     $h = "Members";
     $lis = "Search Members";
    } elseif($st == "MBR-ALB") {
     $ae = base64_encode("Album:Edit");
     $un = base64_decode($data["UN"]);
     $t = ($un == $you) ? $y : $this->core->Member($un);
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Albums" : $t["Personal"]["DisplayName"]."'s Albums";
     $b2 = $b2 ?? $h;
     $b2 = urlencode($b2);
     $li .= "&UN=".base64_encode($t["Login"]["Username"])."&b2=$b2&lPG=$lpg";
     $lis = "Search Albums";
     $lo = ($ck == 1 && $notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$ae&new=1")
      ]
     ]) : "";
    } elseif($st == "MBR-BLG") {
     $bd = base64_encode("Authentication:DeleteBlogs");
     $be = base64_encode("Blog:Edit");
     $h = "Your Blogs";
     $li .= "&b2=Blogs&lPG=$lpg";
     $lis = "Search your Blogs";
     if($y["Subscriptions"]["Blogger"]["A"] == 1 && $notAnon == 1) {
      $lo .= $this->core->Element(["button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$be&new=1")
      ]]);
     }
    } elseif($st == "MBR-CA") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Contributions" : $t["Personal"]["DisplayName"]."'s Contributions";
     $li .= "&b2=$b2&lPG=$lpg&UN=".$data["UN"];
     $lis = "Search the Archive";
    } elseif($st == "MBR-Forums") {
     $fd = base64_encode("Authentication:DeleteForum");
     $fe = base64_encode("Forum:Edit");
     $h = "Your Forums";
     $li .= "&lPG=$lpg";
     $lis = "Search Your Private and Public Forums";
     $lo = ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$fe&new=1")
      ]
     ]) : "";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "MBR-JE") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Journal" : $t["Personal"]["DisplayName"]."'s Journal";
     $li .= "&b2=$b2&lPG=$lpg";
     $lis = "Search Entries";
    } elseif($st == "MBR-LLP") {
     $h = "Your Pages";
     $li .= "&b2=$b2&lPG=$lpg";
     $lis = "Search Pages";
     $pd = base64_encode("Authentication:DeletePage");
     $pe = base64_encode("Page:Edit");
     $lo = ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$pe&new=1")
      ]
     ]) : "";
    } elseif($st == "MBR-SU") {
     $t = base64_decode($data["UN"]);
     $t = ($t != $you) ? $this->core->Member($t) : $y;
     $bl = $this->core->CheckBlocked([$t, "Members", $you]);
     $cms = $this->core->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $h = ($ck == 1) ? "Your Stream" : $display."'s Stream";
     $li .= "&UN=".base64_encode($t["Login"]["Username"]);
     $lis = "Search Posts";
     $lo = (($bl == 0 || $ck == 1) && $notAnon == 1) ? $this->core->Element([
      "button", "Say Something", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($t["Login"]["Username"]))
      ]
     ]) : "";
     $extension = "8568ac7727dae51ee4d96334fa891395";
    } elseif($st == "MBR-XFS") {
     $aid = $data["AID"] ?? md5("unsorted");
     $fs = $this->core->Data("Get", ["fs", md5($you)]) ?? [];
     $xfsLimit = $this->core->config["XFS"]["limits"]["Total"] ?? 0;
     $xfsLimit = $xfsLimit."MB";
     $xfsUsage = 0;
     foreach($fs["Files"] as $key => $value) {
      $xfsUsage = $xfsUsage + $value["Size"];
     }
     $xfsUsage = $this->core->ByteNotation($xfsUsage)."MB";
     $limit = $this->core->Change([["MB" => "", "," => ""], $xfsLimit]);
     $usage = $this->core->Change([["MB" => "", "," => ""], $xfsUsage]);
     $un = $data["UN"] ?? base64_encode($you);
     $un = base64_decode($un);
     $t = ($un == $you) ? $y : $this->core->Member($un);
     $fs = $this->core->Data("Get", [
      "fs",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $alb = $fs["Albums"][$aid] ?? [];
     $ck = $y["Subscriptions"]["XFS"]["A"] ?? 0;
     $ck = ($ck == 1 && $notAnon == 1) ? 1 : 0;
     $ck2 = ($un == $this->core->ID && $y["Rank"] == md5("High Command")) ? 1 : 0;
     $de = $alb["Description"] ?? "";
     $display = ($ck2 == 1) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $h = $alb["Title"] ?? "Unsorted";
     $h = ($ck2 == 1) ? "System Media Library" : $h;
     $li .= "&AID=$aid&UN=".$data["UN"]."&lPG=$lpg";
     $lis = "Search $h";
     $unlimitedFiles = ($ck == 1) ? "You have unlimited storage." : "You used $xfsUsage out of $xfsLimit.";
     $unlimitedFiles = ($ck2 == 1) ? "No Upload Limit" : $unlimitedFiles;
     $ck = ($ck == 1 || $usage < $limit) ? 1 : 0;
     if(($ck == 1 && $un == $you) || $ck2 == 1) {
      $lo = $this->core->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display,
       "[Album.Uploader]" => base64_encode("v=".base64_encode("File:Upload")."&AID=$aid&UN=".$t["Login"]["Username"]),
       "[Album.FStats]" => $unlimitedFiles
      ], $this->core->Page("b9e1459dc1c687cebdaa9aade72c50a9")]);
     } else {
      $lo = $this->core->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display
      ], $this->core->Page("af26c6866abb335fb69327ed3963a182")]);
     }
     $extension = "46ef1d0890a2a5639f67bfda1634ca82";
    } elseif($st == "PR") {
     $h = "Press Releases";
     $li .= "&b2=".urlencode("Press Releases")."&lPG=$lpg";
     $lis = "Search Articles";
     $pe = base64_encode("Page:Edit");
     $lo = ($y["Rank"] == md5("High Command") && $notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$pe&new=1")
      ]
     ]) : "";
    } elseif($st == "Products") {
     $h = "Products";
     $li .= "&lPG=$lpg&st=$st";
     $lis = "Search Products";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "S-Blogger") {
     $be = base64_encode("Clog:Edit");
     $h = "Your Blogs";
     $li .= "&lPG=$st";
     $lis = "Search Blogs";
     if($y["Subscriptions"]["Blogger"]["A"] == 1 && $notAnon == 1) {
      $lo = $this->core->Element(["button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$be&new=1")
      ]]);
     }
    } elseif($st == "SHOP") {
     $h = "Artists";
     $li .= "&lPG=$lpg&st=$st";
     $lis = "Search Shops";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "SHOP-InvoicePresets") {
     $h = "Services";
     $shop = $data["Shop"] ?? "";
     $li .= "&Shop=$shop&st=$st";
     $lis = "Search Services";
    } elseif($st == "SHOP-Invoices") {
     $h = "Invoices";
     $shop = $data["Shop"] ?? "";
     $li .= "&Shop=$shop&st=$st";
     $lis = "Search Invoices";
    } elseif($st == "SHOP-Products") {
     $h = "Products";
     $username = $data["UN"] ?? base64_encode($you);
     $li .= "&UN=$username&b2=$b2&lPG=$lpg&pubP=".$data["pubP"]."&st=$st";
     $lis = "Search $b2";
     $t = base64_decode($data["UN"]);
     $t = $this->core->Member($t);
     $isArtist = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $shopID = md5($t["Login"]["Username"]);
     $shop = $this->core->Data("Get", [
      "shop",
      $shopID
     ]) ?? [];
     $contributors = $shop["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      $ck = ($isArtist == 1 && $member == $you && $notAnon == 1) ? 1 : 0;
      if($ck == 1 && $i == 0) {
       $lo .= $this->core->Element(["button", "+", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Product:Edit")."&Shop=$shopID&new=1")
       ]]).$this->core->Element(["button", "Invoices", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Search:Containers")."&Shop=$shopID&st=SHOP-Invoices")
       ]]).$this->core->Element(["button", "Services", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Search:Containers")."&Shop=$shopID&st=SHOP-InvoicePresets")
       ]]);
       $i++;
      }
     }
     $ck = ($t["Login"]["Username"] == $you && $notAnon == 1) ? 1 : 0;
     $lo .= ($isArtist == 1 && $ck == 1) ? $this->core->Element([
      "button", "Discount Codes", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Search:Containers")."&st=DC")
      ]
     ]) : "";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "SHOP-Orders") {
     $lis = "Search Orders";
     $extension = "e58b4fc5070b14c01c88c28050547285";
    } elseif($st == "XFS") {
     $_AddTo = $data["AddTo"] ?? "";
     $_Added = $data["Added"] ?? "";
     $h = "Files";
     $lPG = $data["lPG"] ?? $st;
     $li .= "&AddTo=".$_AddTo."&Added=".$_Added."&UN=".$data["UN"]."&lPG=$lpg";
     $li .= (isset($data["ftype"])) ? "&ftype=".$data["ftype"] : "";
     $lis = "Search Files";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    }
    $li = base64_encode($li);
    $r = $this->core->Change([[
     "[Search.Header]" => $h,
     "[Search.ID]" => md5($this->core->timestamp.rand(1000, 99999)),
     "[Search.List]" => $li,
     "[Search.Options]" => $lo,
     "[Search.ParentPage]" => $lpg,
     "[Search.Text]" => $lis
    ], $this->core->Page($extension)]);
   } if(in_array($st, [
     "DC",
     "SHOP-InvoicePresets",
     "SHOP-Invoices"
    ])) {
    $r = [
     "Front" => $r
    ];
   } elseif($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
   }
   $r = ($card == 1) ? [
    "Front" => $r
   ] : $r;
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Lists(array $a) {
   $base = $this->core->base;
   $blu = base64_encode("Common:SaveBlacklist");
   $data = $a["Data"] ?? [];
   $key = $this->core->config["SQL"]["Key"];
   $b2 = $data["b2"] ?? "Search";
   $i = 0;
   $msg = [];
   $na = "No Results";
   $st = $data["st"] ?? "";
   $lpg = $data["lPG"] ?? $st;
   $query = $data["query"] ?? "";
   $query = (!empty($query)) ? base64_decode($query) : "";
   $na .= (!empty($data["query"])) ? " for $query" : "";
   $query = (!empty($query)) ? "%$query%" : "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   if($st == "ADM-LLP") {
    $ec = "Accepted";
    $extension = $this->core->Page("da5c43f7719b17a9fab1797887c5c0d1");
    if($notAnon == 1) {
     $articles = $this->core->DatabaseSet("PG") ?? [];
     /*$extensions = $this->core->SQL("SELECT CAST(AES_DECRYPT(Body, :key) AS CHAR(8000)) AS Body,
     CAST(AES_DECRYPT(Description, :key) AS CHAR(8000)) AS Description,
     CAST(AES_DECRYPT(ID, :key) AS CHAR(8000)) AS ID,
     CAST(AES_DECRYPT(Title, :key) AS CHAR(8000)) AS Title
FROM Pages
HAVING CONVERT(AES_DECRYPT(Body, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(Description, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(ID, :key) USING utf8mb4) LIKE :search OR
       CONVERT(AES_DECRYPT(Title, :key) USING utf8mb4) LIKE :search", [
      ":key" => base64_decode($key),
      ":search" => $query
     ]);
     die($query.var_dump($Pages->fetchAll(PDO::FETCH_ASSOC)));
     while($article = $articles->fetchAll(PDO::FETCH_ASSOC)) {*/
     foreach($articles as $key => $value) {
      #$na.=" ".$query.json_encode($article, true);//TEMP
      $value = str_replace("c.oh.pg.", "", $value);
      #$Page = $this->core->Data("Get", ["pg", $article["ID"]]) ?? [];
      $article = $this->core->Data("Get", ["pg", $value]) ?? [];
      if($article["Category"] == "EXT" || $article["High Command"] == 1) {
       $id = $article["ID"] ?? $value;
       array_push($msg, [
        "[Extension.Category]" => base64_encode($article["Category"]),
        "[Extension.Delete]" => base64_encode(base64_encode("v=".base64_encode("Authentication:DeletePage")."&ID=$id")),
        "[Extension.Description]" => base64_encode($this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $article["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[Extension.Edit]" => base64_encode(base64_encode("v=".base64_encode("Page:Edit")."&ID=".base64_encode($id))),
        "[Extension.ID]" => base64_encode($id),
        "[Extension.Title]" => base64_encode($article["Title"])
       ]);
      }
     }
     #$na.=" ".$query.json_encode($articles, true);//TEMP
    }
   } elseif($st == "ADM-MassMail") {
    $ec = "Accepted";
    $preSets = $this->core->Data("Get", ["x", md5("MassMail")]) ?? [];
    $extension = $this->core->Page("3536f06229e7b9d9684f8ca1bb08a968");
    if($notAnon == 1) {
     foreach($preSets as $key => $preSet) {
      if($key != "NextSend") {
       array_push($msg, [
        "[Email.Description]" => base64_encode($preSet["Description"]),
        "[Email.Editor]" => base64_encode(base64_encode("v=".base64_encode("Company:MassMail")."&ID=$key")),
        "[Email.Title]" => base64_encode($preSet["Title"])
       ]);
      }
     }
    }
   } elseif($st == "BGP") {
    $ec = "Accepted";
    $blog = $this->core->Data("Get", [
     "blg",
     base64_decode($data["ID"])
    ]) ?? [];
    $owner = ($blog["UN"] == $you) ? $y : $this->core->Member($blog["UN"]);
    $extension = $this->core->Page("dba88e1a123132be03b9a2e13995306d");
    if($notAnon == 1) {
     $_IsBlogger = $owner["Subscriptions"]["Blogger"]["A"] ?? 0;
     $coverPhoto = $this->core->PlainText([
      "Data" => "[sIMG:CP]",
      "Display" => 1
     ]);
     $title = $blog["Title"];
     $title = urlencode($title);
     $posts = $blog["Posts"] ?? [];
     foreach($posts as $key => $value) {
      $post = $this->core->Data("Get", ["bp", $value]) ?? [];
      $actions = ($post["UN"] != $you) ? $this->core->Element([
       "button", "Block", [
        "class" => "BLK InnerMargin",
        "data-cmd" => base64_encode("B"),
        "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($post["ID"])."&list=".base64_encode("Blog Posts")."&BC=")
       ]
      ]) : "";
      $actions = ($this->core->ID != $you) ? $actions : "";
      $bl = $this->core->CheckBlocked([$y, "Blog Posts", $value]);
      $cms = $this->core->Data("Get", ["cms", md5($post["UN"])]) ?? [];
      $ck = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $post["Privacy"],
       "UN" => $post["UN"],
       "Y" => $you
      ]);
      $ck2 = ($post["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $illegal = $post["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && ($ck == 1 && $ck2 == 1) && $illegal == 0) {
       if($blog["UN"] == $you || $post["UN"] == $you) {
        $combinedID = base64_encode($blog["ID"]."-".$post["ID"]);
        $actions .= $this->core->Element([
         "button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteBlogPost")."&ID=$combinedID")
         ]
        ]);
        $actions .= ($_IsBlogger == 1) ? $this->core->Element([
         "button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-view" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=".$blog["ID"]."&Post=".$post["ID"])
         ]
        ]) : "";
       }
       $contributors = $post["Contributors"] ?? $blog["Contributors"];
       $coverPhoto = (!empty($post["ICO"])) ? base64_encode($post["ICO"]) : $coverPhoto;
       $op = ($post["UN"] == $you) ? $y : $this->core->Member($post["UN"]);
       $display = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $memberRole = ($blog["UN"] == $post["UN"]) ? "Owner" : $contributors[$author];
       $modified = $post["ModifiedBy"] ?? [];
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Member = end($modified);
        $_Time = $this->core->TimeAgo(array_key_last($modified));
        $modified = " &bull; Modified ".$_Time." by ".$_Member;
        $modified = $this->core->Element(["em", $modified]);
       }
       array_push($msg, [
        "[BlogPost.Actions]" => base64_encode($actions),
        "[BlogPost.Author]" => base64_encode($display),
        "[BlogPost.Description]" => base64_encode($post["Description"]),
        "[BlogPost.Created]" => base64_encode($this->core->TimeAgo($post["Created"])),
        "[BlogPost.ID]" => base64_encode($post["ID"]),
        "[BlogPost.MemberRole]" => base64_encode($memberRole),
        "[BlogPost.Modified]" => base64_encode($modified),
        "[BlogPost.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
        "[BlogPost.Title]" => base64_encode($post["Title"]),
        "[BlogPost.View]" => base64_encode("Blog".$blog["ID"].";".base64_encode("v=".base64_encode("BlogPost:Home")."&Blog=".$blog["ID"]."&Post=".$post["ID"]."&b2=".$blog["Title"]."&back=1"))
       ]);
      }
     }
    }
   } elseif($st == "BL") {
    $ec = "Accepted";
    $extension = $this->core->Page("e05bae15ffea315dc49405d6c93f9b2c");
    if($notAnon == 1) {
     $bl = base64_decode($data["BL"]);
     $x = $y["Blocked"][$bl] ?? [];
     foreach($x as $k => $v) {
      $p = base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode("Unblock")."&Content=".base64_encode($v)."&List=".base64_encode($bl));
      if($bl == "Albums") {
       $alb = explode("-", base64_decode($v));
       $t = ($alb[0] != $y["Login"]["Username"]) ? $this->core->Member($alb[0]) : $y;
       $fs = $this->core->Data("Get", [
        "fs",
        md5($t["Login"]["Username"])
       ]) ?? [];
       $alb = $fs["Albums"][$alb[1]];
       $de = $alb["Description"];
       $h = "<em>".$alb["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Blogs") {
       $bg = $this->core->Data("Get", ["blg", $v]) ?? [];
       $de = $bg["Description"];
       $h = "<em>".$bg["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Blog Posts") {
       $bp = $this->core->Data("Get", ["bp", $v]) ?? [];
       $de = $bp["Description"];
       $h = "<em>".$bp["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Files") {
       $de = "{comment}";
       $h = "<em>{poster}</em>";
       $vi = $this->core->Element(["button", "View $h's Comment", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Files") {
       $de = "{file_description}";
       $h = "<em>{file_name}</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Forums") {
       $forum = $this->core->Data("Get", ["pf", $v]) ?? [];
       $de = $forum["Description"];
       $h = "<em>".$forum["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Forum Posts") {
       $post = $this->core->Data("Get", ["post", $v]) ?? [];
       $de = $post["Description"];
       $h = "<em>".$post["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Links") {
       $de = "{link_description}";
       $h = "<em>{link_name}</em>";
       $vi = $this->core->Element(["button", "View $h's Profile", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Pages") {
       $member = $this->core->Data("Get", ["mbr", $v]) ?? [];
       $de = $member["Description"];
       $h = "<em>".$member["Personal"]["DisplayName"]."</em>";
       $vi = $this->core->Element(["button", "View $h's Profile", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Pages") {
       $page = $this->core->Data("Get", ["pg", $v]) ?? [];
       $de = $page["Description"];
       $h = "<em>".$page["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Polls") {
       $poll = $this->core->Data("Get", ["poll", $v]) ?? [];
       $de = $poll["Description"];
       $h = "<em>".$poll["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Products") {
       $product = $this->core->Data("Get", ["product", $v]) ?? [];
       $de = $product["Description"];
       $h = "<em>".$product["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Shops") {
       $shop = $this->core->Data("Get", ["shop", $v]) ?? [];
       $de = $shop["Description"];
       $h = "<em>".$shop["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Status Updates") {
       $su = $this->core->Data("Get", ["su", $v]) ?? [];
       $de = $this->core->Excerpt(base64_decode($su["Body"]), 180);
       $h = $su["From"];
       $vi = $this->core->Element(["button", "View $u", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      }
      array_push($msg, [
       "[X.LI.Description]" => base64_encode($de),
       "[X.LI.Header]" => base64_encode($h),
       "[X.LI.ID]" => base64_encode($v),
       "[X.LI.Unblock]" => base64_encode($u),
       "[X.LI.Unblock.Proc]" => base64_encode(base64_encode($p)),
       "[X.LI.View]" => base64_encode($vi)
      ]);
     }
    }
   } elseif($st == "BLG") {
    $blogs = $this->core->DatabaseSet("BLG") ?? [];
    $coverPhoto = $this->core->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $ec = "Accepted";
    $home = base64_encode("Blog:Home");
    $extension = $this->core->Page("ed27ee7ba73f34ead6be92293b99f844");
    foreach($blogs as $key => $value) {
     $value = str_replace("c.oh.blg.", "", $value);
     $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
     $cms = $this->core->Data("Get", ["cms", md5($blog["UN"])]);
     $bl = $this->core->CheckBlocked([$y, "Blogs", $blog["ID"]]);
     $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $bg["NSFW"] == 0) ? 1 : 0;
     $ck2 = $this->core->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $blog["Privacy"],
      "UN" => $blog["UN"],
      "Y" => $you
     ]);
     $illegal = $blog["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($bl == 0 && $ck == 1 && $ck2 == 1 && $illegal == 0) {
      $coverPhoto = $blog["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($blog["Title"]),
       "[X.LI.D]" => base64_encode($blog["Description"]),
       "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".$blog["ID"]))
      ]);
     }
    }
   } elseif($st == "Bulletins") {
    $bulletins = $this->core->Data("Get", [
     "bulletins",
     md5($you)
    ]) ?? [];
    $ec = "Accepted";
    $message = base64_encode("Profile:BulletinMessage");
    $options = base64_encode("Profile:BulletinOptions");
    $extension = $this->core->Page("ae30582e627bc060926cfacf206920ce");
    foreach($bulletins as $key => $value) {
     $t = $this->core->Member($value["From"]);
     if(!empty($t["Personal"])) {
      $display = ($t["Personal"]["DisplayName"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
      $message = $this->view($message, [
       "Data" => $value
      ]);
      $options = $this->view($options, [
       "Data" => [
        "Bulletin" => base64_encode(json_encode($value, true))
       ]
      ]);
      $pic = $this->core->ProfilePicture($t, "margin:5%;width:90%");
      $value["ID"] = $key;
      array_push($msg, [
       "[Bulletin.Date]" => base64_encode($this->core->TimeAgo($value["Sent"])),
       "[Bulletin.From]" => base64_encode($display),
       "[Bulletin.ID]" => base64_encode($key),
       "[Bulletin.Message]" => base64_encode($this->core->RenderView($message)),
       "[Bulletin.Options]" => base64_encode($this->core->RenderView($options)),
       "[Bulletin.Picture]" => base64_encode($pic)
      ]);
     }
    }
   } elseif($st == "CA" || $st == "PR") {
    $ec = "Accepted";
    $home = base64_encode("Page:Home");
    $extension = $this->core->Page("e7829132e382ee4ab843f23685a123cf");
    $articles = $this->core->DatabaseSet("PG") ?? [];
    foreach($articles as $key => $value) {
     $value = str_replace("c.oh.pg.", "", $value);
     $article = $this->core->Data("Get", ["pg", $value]) ?? [];
     if(!empty($article["UN"])) {
      $nsfw = $article["NSFW"] ?? 0;
      $t = ($article["UN"] == $you) ? $y : $this->core->Member($article["UN"]);
      $bl = $this->core->CheckBlocked([$y, "Pages", $article["ID"]]);
      $cat = $article["Category"] ?? "";
      $cms = $this->core->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $ck = ($article["Category"] == $st) ? 1 : 0;
      $ck2 = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck3 = (($st == "CA" && $article["Category"] == "CA") || ($st == "PR" && $article["Category"] == "PR")) ? 1 : 0;
      $ck4 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $article["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) ? 1 : 0;
      $illegal = $article["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $illegal == 0) {
      $coverPhoto = $article["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
       array_push($msg, [
        "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
        "[X.LI.T]" => base64_encode($article["Title"]),
        "[X.LI.D]" => base64_encode($this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $article["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[X.LI.DT]" => base64_encode("$lpg;".base64_encode("v=$home&b2=$b2&back=1&lPG=$lpg&ID=".$article["ID"]))
       ]);
      }
     }
    }
   } elseif($st == "CART") {
    $ec = "Accepted";
    $coverPhoto = $this->core->PlainText([
     "Data" => "[sIMG:MiNY]",
     "Display" => 1
    ]);
    $data = $this->core->FixMissing($data, ["ID"]);
    $newCartList = [];
    $now = $this->core->timestamp;
    $remove = base64_encode("Cart:Remove");
    $extension = $this->core->Page("dea3da71b28244bf7cf84e276d5d1cba");
    $products = $y["Shopping"]["Cart"][$data["ID"]] ?? [];
    $products = $products["Products"] ?? [];
    foreach($products as $key => $value) {
     $product = $this->core->Data("Get", ["product", $key]) ?? [];
     $isActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
     $illegal = $product["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $quantity = $product["Quantity"] ?? 0;
     if(!empty($product) && $isActive == 1 && $quantity != 0 && $illegal == 0) {
      $coverPhoto = $product["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      $newCartList[$key] = $value;
      $remove = $this->view($remove, ["Data" => [
       "ProductID" => base64_encode($key),
       "ShopID" => base64_encode($data["ID"])
      ]]);
      $remove = $this->core->RenderView($remove);
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($product["Title"]),
       "[X.LI.D]" => base64_encode($product["Description"]),
       "[X.LI.Remove]" => base64_encode($remove)
      ]);
     }
    }
    $y["Shopping"]["Cart"][$data["ID"]]["Products"] = $newCartList;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
   } elseif($st == "Chat" || $st == "GroupChat") {
    $ec = "Accepted";
    $group = $data["Group"] ?? 0;
    $integrated = $data["Integrated"] ?? 0;
    $oneOnOne = $data["1on1"] ?? 0;
    $extension = $this->core->Page("343f78d13872e3b4e2ac0ba587ff2910");
    if($notAnon == 1) {
     $extension = "343f78d13872e3b4e2ac0ba587ff2910";
     $extension = ($integrated == 0) ? "183d39e5527b3af3e7652181a0e36e25" : $extension;
     $extension = $this->core->Page($extension);
     if($group == 1) {
      $groups = $y["GroupChats"] ?? [];
      foreach($groups as $key => $group) {
       $chat = $this->core->Data("Get", ["chat", $group]) ?? [];
       $displayName = $chat["Title"] ?? "Group Chat";
       $t = $this->core->Member($this->core->ID);
       $view = "v=".base64_encode("Chat:Home")."&Group=1&ID=".base64_encode($group);
       $view .= ($integrated == 1) ? "&Card=1" : "";
       array_push($msg, [
        "[Chat.DisplayName]" => base64_encode($displayName),
        "[Chat.Online]" => base64_encode(""),
        "[Chat.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%")),
        "[Chat.View]" => base64_encode(base64_encode($view))
       ]);
      }
     } elseif($oneOnOne == 1) {
      $chat = $this->core->Data("Get", ["chat", md5($you)]) ?? [];
      $contacts = [];
      $messages = $chat["Messages"] ?? [];
      foreach($messages as $key => $message) {
       array_push($contacts, $message["To"]);
      }
      $contacts = array_unique($contacts);
      foreach($contacts as $key => $member) {
       $t = $this->core->Member($member);
       $view = "v=".base64_encode("Chat:Home")."&1on1=1&Username=".base64_encode($member);
       $view .= ($integrated == 1) ? "&Card=1" : "";
       $online = $t["Activity"]["OnlineStatus"] ?? 0;
       $online = ($online == 1) ? $this->core->Element([
        "span",
        NULL,
        ["class" => "online"]
       ]) : "";
       array_push($msg, [
        "[Chat.DisplayName]" => base64_encode($t["Personal"]["DisplayName"]),
        "[Chat.Online]" => base64_encode($online),
        "[Chat.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%")),
        "[Chat.View]" => base64_encode(base64_encode($view))
       ]);
      }
     }
    }
   } elseif($st == "Contacts") {
    $ec = "Accepted";
    $extension = $this->core->Page("ccba635d8c7eca7b0b6af5b22d60eb55");
    if($notAnon == 1) {
     $cms = $this->core->Data("Get", [
      "cms",
      md5($y["Login"]["Username"])
     ]) ?? [];
     $cms = $cms["Contacts"] ?? [];
     foreach($cms as $key => $value) {
      $t = $this->core->Member($key);
      $delete = base64_encode("v=".base64_encode("Contact:Delete"));
      $id = md5($key);
      $options = "v=".base64_encode("Contact:Options")."&UN=".base64_encode($key);
      array_push($msg, [
       "[Contact.Delete]" => base64_encode($delete),
       "[Contact.DisplayName]" => base64_encode($t["Personal"]["DisplayName"]),
       "[Contact.Form]" => base64_encode($id),
       "[Contact.ID]" => base64_encode($id),
       "[Contact.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%")),
       "[Contact.Username]" => base64_encode($key),
       "[Options]" => base64_encode($options)
      ]);
     }
    }
   } elseif($st == "ContactsProfileList") {
    $ec = "Accepted";
    $home = base64_encode("Profile:Home");
    $extension = $this->core->Page("ba17995aafb2074a28053618fb71b912");
    $x = $this->core->Data("Get", [
     "cms",
     md5(base64_decode($data["UN"]))
    ]) ?? [];
    $x = $x["Contacts"] ?? [];
    foreach($x as $k => $v) {
     $t = $this->core->Member($k);
     $cms = $this->core->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $bl = $this->core->CheckBlocked([
      $t, "Members", $y["Login"]["Username"]
     ]);
     $bl2 = $this->core->CheckBlocked([
      $y, "Members", $t["Login"]["Username"]
     ]);
     $ck = $this->core->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $t["Privacy"]["Profile"],
      "UN" => $t["Login"]["Username"],
      "Y" => $y["Login"]["Username"]
     ]);
     if($bl == 0 && $bl2 == 0 && $ck == 1) {
      $opt = $this->core->Element(["button", "View Profile", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("CARD=1&v=$home&back=1&b2=$b2&lPG=$lpg&pub=0&UN=".base64_encode($t["Login"]["Username"]))
      ]]);
      array_push($msg, [
       "[X.LI.DisplayName]" => base64_encode($t["Personal"]["DisplayName"]),
       "[X.LI.Description]" => base64_encode($t["Personal"]["Description"]),
       "[X.LI.Options]" => base64_encode($opt),
       "[X.LI.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%"))
      ]);
     }
    }
   } elseif($st == "ContactsRequests") {
    $ec = "Accepted";
    $extension = $this->core->Page("8b6ac25587a4524c00b311c184f6c69b");
    if($notAnon == 1) {
     $cms = $this->core->Data("Get", [
      "cms",
      md5($y["Login"]["Username"])
     ]) ?? [];
     $cms = $cms["Requests"] ?? [];
     foreach($cms as $key => $value) {
      $t = $this->core->Member($value);
      $pp = $this->core->ProfilePicture($t, "margin:5%;width:90%");
      $accept = "v=".base64_encode("Contact:Requests")."&accept=1";
      $decline = "v=".base64_encode("Contact:Requests")."&decline=1";
      $memberID = md5($t["Login"]["Username"]);
      array_push($msg, [
       "[X.LI.Contact.Accept]" => base64_encode(base64_encode($accept)),
       "[X.LI.Contact.Decline]" => base64_encode(base64_encode($decline)),
       "[X.LI.Contact.DisplayName]" => base64_encode($t["Personal"]["DisplayName"]),
       "[X.LI.Contact.Form]" => base64_encode($memberID),
       "[X.LI.Contact.ID]" => base64_encode($memberID),
       "[X.LI.Contact.IDaccept]" => base64_encode($memberID),
       "[X.LI.Contact.IDdecline]" => base64_encode($memberID),
       "[X.LI.Contact.ProfilePicture]" => base64_encode($pp),
       "[X.LI.Contact.Username]" => base64_encode($t["Login"]["Username"])
      ]);
     }
    }
   } elseif($st == "Contributors") {
    $ec = "Accepted";
    $admin = 0;
    $contributors = [];
    $data = $this->core->FixMissing($data, ["ID", "Type"]);
    $id = $data["ID"];
    $extension = $this->core->Page("ba17995aafb2074a28053618fb71b912");
    $type = $data["Type"];
    $ck = (!empty($id)) ? 1 : 0;
    $ck2 = (!empty($type)) ? 1 : 0;
    if($ck == 1 && $ck2 == 1) {
     $id = base64_decode($id);
     $type = base64_decode($type);
     if($type == "Article") {
      $Page = $this->core->Data("Get", ["pg", $id]) ?? [];
      $contributors = $Page["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Blog") {
      $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
      $contributors = $blog["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Forum") {
      $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
      $contributors = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Shop") {
      $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
      $contributors = $shop["Contributors"] ?? [];
     } foreach($contributors as $member => $role) {
      $description = "No Description";
      $displayname = "Anonymous";
      $opt = "";
      $t = ($member == $you) ? $y : $this->core->Member($member);
      if(!empty($t["Login"])) {
       if($type == "Article") {
        $ban = base64_encode("Page:Banish");
        $bl = $this->core->CheckBlocked([$t, "Members", $you]);
        $bl2 = $this->core->CheckBlocked([$y, "Members", $member]);
        $cr = base64_encode("Authentication:ArticleChangeMemberRole");
        $cms = $this->core->Data("Get", [
         "cms",
         md5($t["Login"]["Username"])
        ]) ?? [];
        $ck = $this->core->CheckPrivacy([
         "Contacts" => $cms["Contacts"],
         "Privacy" => $t["Privacy"]["Profile"],
         "UN" => $member,
         "Y" => $you
        ]);
        $ck2 = ($Page["UN"] == $you || $admin == 1) ? 1 : 0;
        $ck2 = ($ck2 == 1 && $member != $you) ? 1 : 0;
        if($bl == 0 && $bl2 == 0 && ($ck == 1 || $ck2 == 1)) {
         $ck = ($Page["UN"] != $member) ? 1 : 0;
         $description = "You have not added a Description.";
         $description = ($member != $you) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
         $description = (!empty($t["Description"])) ? $this->core->PlainText([
          "BBCodes" => 1,
          "Data" => $t["Description"],
          "Display" => 1
         ]) : $description;
         $displayname = $t["Personal"]["DisplayName"];
         $eid = base64_encode($Page["ID"]);
         $mbr = base64_encode($t["Login"]["Username"]);
         $opt = ($ck == 1 && $ck2 == 1) ? $this->core->Element([
          "button", "Banish", [
           "class" => "OpenDialog v2",
           "data-view" => base64_encode("v=$ban&ID=$eid&Member=$mbr")
          ]
         ]).$this->core->Element([
          "button", "Change Role", [
           "class" => "OpenDialog v2",
           "data-view" => base64_encode("v=$cr&ID=$eid&Member=$mbr")
          ]
         ]) : "";
        }
       } elseif($type == "Blog") {
        $ban = base64_encode("Blog:Banish");
        $bl = $this->core->CheckBlocked([$t, "Members", $you]);
        $bl2 = $this->core->CheckBlocked([$y, "Members", $member]);
        $cr = base64_encode("Authentication:BlogChangeMemberRole");
        $cms = $this->core->Data("Get", [
         "cms",
         md5($t["Login"]["Username"])
        ]) ?? [];
        $ck = $this->core->CheckPrivacy([
         "Contacts" => $cms["Contacts"],
         "Privacy" => $t["Privacy"]["Profile"],
         "UN" => $member,
         "Y" => $you
        ]);
        $ck2 = ($blog["UN"] == $you || $admin == 1) ? 1 : 0;
        $ck2 = ($ck2 == 1 && $member != $you) ? 1 : 0;
        if($bl == 0 && $bl2 == 0 && ($ck == 1 || $ck2 == 1)) {
         $ck = ($blog["UN"] != $member) ? 1 : 0;
         $description = "You have not added a Description.";
         $description = ($member != $you) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
         $description = (!empty($t["Description"])) ? $this->core->PlainText([
          "BBCodes" => 1,
          "Data" => $t["Description"],
          "Display" => 1
         ]) : $description;
         $displayname = $t["Personal"]["DisplayName"];
         $eid = base64_encode($blog["ID"]);
         $mbr = base64_encode($t["Login"]["Username"]);
         $opt = ($ck == 1 && $ck2 == 1) ? $this->core->Element([
          "button", "Banish", [
           "class" => "OpenDialog v2",
           "data-view" => base64_encode("v=$ban&ID=$eid&Member=$mbr")
          ]
         ]).$this->core->Element([
          "button", "Change Role", [
           "class" => "OpenDialog v2",
           "data-view" => base64_encode("v=$cr&ID=$eid&Member=$mbr")
          ]
         ]) : "";
        }
       } elseif($type == "Forum") {
        $ban = base64_encode("Forum:Banish");
        $bl = $this->core->CheckBlocked([$t, "Members", $you]);
        $bl2 = $this->core->CheckBlocked([$y, "Members", $member]);
        $cr = base64_encode("Authentication:PFChangeMemberRole");
        $cms = $this->core->Data("Get", [
         "cms",
         md5($t["Login"]["Username"])
        ]) ?? [];
        $ck = $this->core->CheckPrivacy([
         "Contacts" => $cms["Contacts"],
         "Privacy" => $t["Privacy"]["Profile"],
         "UN" => $member,
         "Y" => $you
        ]);
        $ck2 = ($forum["UN"] == $you || $admin == 1) ? 1 : 0;
        $ck2 = ($ck2 == 1 && $member != $you) ? 1 : 0;
        if($bl == 0 && $bl2 == 0 && ($ck == 1 || $ck2 == 1)) {
         $ck = ($forum["UN"] != $member) ? 1 : 0;
         $description = "You have not added a Description.";
         $description = ($member != $you) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
         $description = (!empty($t["Personal"]["Description"])) ? $this->core->PlainText([
          "BBCodes" => 1,
          "Data" => $t["Personal"]["Description"],
          "Display" => 1
         ]) : $description;
         $displayname = $t["Personal"]["DisplayName"];
         $eid = base64_encode($forum["ID"]);
         $mbr = base64_encode($t["Login"]["Username"]);
         $opt = ($ck == 1 && $ck2 == 1) ? $this->core->Element([
          "button", "Banish", [
           "class" => "OpenDialog v2",
           "data-view" => base64_encode("v=$ban&ID=$eid&Member=$mbr")
          ]
         ]).$this->core->Element([
          "button", "Change Role", [
           "class" => "OpenDialog v2",
           "data-view" => base64_encode("v=$cr&ID=$eid&Member=$mbr")
          ]
         ]) : "";
        }
       } elseif($type == "Shop") {
        $ck = ($id == md5($you)) ? 1 : 0;
        $ck = ($ck == 1 && $member != $you) ? 1 : 0;
        $description = "<b>".$role["Title"]."</b><br/>".$role["Description"];
        $eid = base64_encode($id);
        $displayname = $t["Personal"]["DisplayName"];
        $memberID = base64_encode($member);
        $opt = ($ck == 1) ? $this->core->Element(["button", "Edit", [
         "class" => "OpenCard v2",
         "data-view" => base64_encode("v=".base64_encode("Shop:EditPartner")."&UN=$memberID")
        ]]).$this->core->Element(["button", "Fire", [
         "class" => "OpenDialog v2",
         "data-view" => base64_encode("v=".base64_encode("Shop:Banish")."&ID=$eid&UN=$memberID")
        ]]) : "";
       }
      }
      array_push($msg, [
       "[X.LI.DisplayName]" => base64_encode($displayname),
       "[X.LI.Description]" => base64_encode($description),
       "[X.LI.Options]" => base64_encode($opt),
       "[X.LI.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%"))
      ]);
     }
    }
   } elseif($st == "CS1") {
    $ec = "Accepted";
    $msg = [
     [1, "Monday"],
     [2, "Tuesday"],
     [3, "Wednesday"],
     [4, "Thursday"],
     [5, "Friday"],
     [6, "Saturday"],
     [7, "Sunday"]
    ];
   } elseif($st == "DC") {
    $ec = "Accepted";
    $extension = $this->core->Page("e9f34ca1985c166bf7aa73116a745e92");
    if($notAnon == 1) {
     $x = $this->core->Data("Get", [
      "dc",
      md5($y["Login"]["Username"])
     ]) ?? [];
     foreach($x as $key => $value) {
      $options = $this->core->Element(["button", "Delete", [
       "class" => "A OpenDialog v2",
       "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteDiscountCode")."&ID=$key")
      ]]).$this->core->Element(["button", "Edit", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("DiscountCode:Edit")."&ID=$key")
      ]]);
      array_push($msg, [
       "[ListItem.Description]" => base64_encode($value["Percentile"]."% Off: ".$value["Quantity"]),
       "[ListItem.Options]" => base64_encode($options),
       "[ListItem.Title]" => $value["Code"]
      ]);
     }
    }
   } elseif($st == "Feedback") {
    $ec = "Accepted";
    $now = $this->core->timestamp;
    $x = $this->core->DatabaseSet("KB") ?? [];
    $extension = $this->core->Page("e7c4e4ed0a59537ffd00a2b452694750");
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.knowledge.", "", $value);
     $feedback = $this->core->Data("Get", ["knowledge", $value]) ?? [];
     $mesasge = $feedback["Thread"] ?? [];
     $mesasge = $feedback["Thread"][0] ?? [];
     $message = $feedback["Thread"][0]["Body"] ?? "";
     if(!empty($message)) {
      $message = $this->core->PlainText([
       "Data" => $message,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]);
     }
     $modified = $feedback["Sent"] ?? $now;
     $modified = $this->core->TimeAgo($modified);
     $resolved = $feedback["Resolved"] ?? 0;
     $resolved = ($resolved == 1) ? "Resolved" : "Not Resolved";
     $title = $feedback["Subject"] ?? "+";
     if($feedback["UseParaphrasedQuestion"] == 1) {
      $title = $feedback["ParaphrasedQuestion"];
     }
     array_push($msg, [
      "[Feedback.ID]" => base64_encode($value),
      "[Feedback.Home]" => base64_encode(base64_encode("v=".base64_encode("Feedback:Home")."&ID=".$value)),
      "[Feedback.Message]" => base64_encode($message),
      "[Feedback.Modified]" => base64_encode($modified),
      "[Feedback.Resolved]" => base64_encode($resolved),
      "[Feedback.Title]" => base64_encode($title)
     ]);
    }
   } elseif($st == "Forums") {
    $ec = "Accepted";
    $home = base64_encode("Forum:Home");
    $extension = $this->core->Page("ed27ee7ba73f34ead6be92293b99f844");
    $x = $this->core->DatabaseSet("PF") ?? [];
    foreach($x as $key => $value) {
     $active = 0;
     $value = str_replace("c.oh.pf.", "", $value);
     $bl = $this->core->CheckBlocked([$y, "Forums", $value]);
     $forum = $this->core->Data("Get", ["pf", $value]) ?? [];
     $manifest = $this->core->Data("Get", ["pfmanifest", $value]) ?? [];
     $t = ($forum["UN"] == $you) ? $y : $this->core->Member($forum["UN"]);
     $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]);
     $ck = $forum["Open"] ?? 0;
     $ck2 = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $forum["NSFW"] == 0) ? 1 : 0;
     $ck3 = $this->core->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $forum["Privacy"],
      "UN" => $forum["UN"],
      "Y" => $you
     ]);
     $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
     $illegal = $forum["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     foreach($manifest as $member => $role) {
      if($active == 0 && $member == $you) {
       $active++;
      }
     } if($bl == 0 && ($active == 1 || $ck == 1) && $illegal == 0) {
      $coverPhoto = $forum["ICO"] ?? "";
      $coverPhoto = base64_encode($coverPhoto);
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($forum["Title"]),
       "[X.LI.D]" => base64_encode($forum["Description"]),
       "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".base64_encode($value)))
      ]);
     }
    }
   } elseif($st == "Forums-Admin") {
    $admin = $data["Admin"] ?? "";
    $ec = "Accepted";
    $id = $data["ID"] ?? "";
    $extension = $this->core->Page("ba17995aafb2074a28053618fb71b912");
    if(!empty($id)) {
     $admin = base64_decode($admin);
     $id = base64_decode($id);
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
     foreach($manifest as $member => $role) {
      if($member == $admin || $role == "Admin") {
       $t = ($member == $you) ? $y : $this->core->Member($member);
       $bl = $this->core->CheckBlocked([
        $t,
        "Members",
        $you
       ]);
       $bl2 = $this->core->CheckBlocked([
        $y,
        "Members",
        $t["Login"]["Username"]
       ]);
       $contacts = $this->core->Data("Get", ["cms", md5($member)]) ?? [];
       $ck = $this->core->CheckPrivacy([
        "Contacts" => $contacts["Contacts"],
        "Privacy" => $t["Privacy"]["Profile"],
        "UN" => $member,
        "Y" => $you
       ]);
       if($bl == 0 && $bl2 == 0 && $ck == 1) {
        $description = "You have not added a Description.";
        $description = ($t["Login"]["Username"] != $you) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
        $description = (!empty($t["Personal"]["Description"])) ? $this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $t["Description"],
         "Display" => 1
        ]) : $description;
        $displayname = $t["Personal"]["DisplayName"];
        array_push($msg, [
         "[X.LI.DisplayName]" => base64_encode($displayname),
         "[X.LI.Description]" => base64_encode($description),
         "[X.LI.Options]" => base64_encode(""),
         "[X.LI.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%"))
        ]);
       }
      }
     }
    }
   } elseif($st == "Forums-Posts") {
    $ec = "Accepted";
    $active = 0;
    $admin = 0;
    $id = $data["ID"] ?? "";
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $home = base64_encode("ForumPost:Home");
    $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
    foreach($manifest as $k => $v) {
     if($active == 0 && $k == $y["Login"]["Username"]) {
      $active = 0;
      if($admin == 0 && $v == "Admin") {
       $admin++;
      }
     }
    }
    $posts = $forum["Posts"] ?? [];
    $extension = $this->core->Page("150dcee8ecbe0e324a47a8b5f3886edf");
    if($active == 1 || $admin == 1 || $forum["Type"] == "Public") {
     foreach($posts as $key => $value) {
      $actions = "";
      $bl = $this->core->CheckBlocked([$y, "Forum Posts", $value]);
      $post = $this->core->Data("Get", ["post", $value]) ?? [];
      $cms = $this->core->Data("Get", ["cms", md5($post["From"])]) ?? [];
      $illegal = $post["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $op = ($forum["UN"] == $you) ? $y : $this->core->Member($post["From"]);
      $ck = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
      $ck2 = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $post["NSFW"] == 0) ? 1 : 0;
      $ck3 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $post["Privacy"],
       "UN" => $post["From"],
       "Y" => $you
      ]);
      if($bl == 0 && ($ck2 == 1 && $ck3 == 1) && $illegal == 0) {
       $att = "";
       if(!empty($post["Attachments"])) {
        $att =  $this->view(base64_encode("LiveView:InlineMossaic"), [
         "Data" => [
          "ID" => base64_encode(implode(";", $post["Attachments"])),
          "Type" => base64_encode("DLC")
         ]
        ]);
        $att = $this->core->RenderView($att);
       }
       $bl = $this->core->CheckBlocked([$y, "Status Updates", $id]);
       $con = base64_encode("Conversation:Home");
       $actions = ($post["From"] != $you) ? $this->core->Element([
        "button", "Block", [
         "class" => "BLK InnerMargin",
         "data-cmd" => base64_encode("B"),
         "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($post["ID"])."&list=".base64_encode("Forum Posts")."&BC=")
        ]
       ]) : "";
       $actions = ($this->core->ID != $you) ? $actions : "";
       if($ck == 1) {
        $actions .= $this->core->Element([
         "button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteForumPost")."&FID=$id&ID=".$post["ID"])
         ]
        ]);
        $actions .= ($admin == 1 || $ck == 1) ? $this->core->Element([
         "button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-view" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$id&ID=".$post["ID"])
         ]
        ]) : "";
       }
       $actions .= ($forum["Type"] == "Public") ? $this->core->Element([
        "button", "Share", [
         "class" => "InnerMargin OpenCard",
         "data-view" => base64_encode("v=".base64_encode("ForumPost:Share")."&ID=".base64_encode($id."-".$post["ID"]))
        ]
       ]) : "";
       $display = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $memberRole = ($op["Login"]["Username"] == $forum["UN"]) ? "Owner" : $manifest[$op["Login"]["Username"]];
       $modified = $post["ModifiedBy"] ?? [];
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Member = end($modified);
        $_Time = $this->core->TimeAgo(array_key_last($modified));
        $modified = " &bull; Modified ".$_Time." by ".$_Member;
        $modified = $this->core->Element(["em", $modified]);
       }
       $votes = ($op["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
       $votes = base64_encode("v=$votes&ID=".$post["ID"]."&Type=1");
       array_push($msg, [
        "[ForumPost.Actions]" => base64_encode($actions),
        "[ForumPost.Attachments]" => base64_encode($att),
        "[ForumPost.Body]" => base64_encode($this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $post["Body"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[ForumPost.Comment]" => base64_encode(base64_encode("v=$home&FID=$id&ID=".$post["ID"])),
        "[ForumPost.Created]" => base64_encode($this->core->TimeAgo($post["Created"])),
        "[ForumPost.ID]" => base64_encode($post["ID"]),
        "[ForumPost.MemberRole]" => base64_encode($memberRole),
        "[ForumPost.Modified]" => base64_encode($modified),
        "[ForumPost.OriginalPoster]" => base64_encode($display),
        "[ForumPost.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
        "[ForumPost.Title]" => base64_encode($post["Title"]),
        "[ForumPost.VoteID]" => base64_encode($post["ID"]),
        "[ForumPost.Votes]" => base64_encode($votes)
       ]);
      }
     }
    }
   } elseif($st == "Knowledge") {
    $ec = "Accepted";
    $extension = $this->core->Page("#");
    $x = $this->core->DatabaseSet("KB");
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.kb.", "", $v);
    }
   } elseif($st == "Mainstream") {
    $ec = "Accepted";
    $edit = base64_encode("StatusUpdate:Edit");
    $attlv = base64_encode("LiveView:InlineMossaic");
    $extension = $this->core->Page("18bc18d5df4b3516c473b82823782657");
    $x = $this->core->Data("Get", ["x", "mainstream"]) ?? [];
    foreach($x as $k => $v) {
     $bl = $this->core->CheckBlocked([$y, "Status Opdates", $v]);
     $su = $this->core->Data("Get", ["su", $v]) ?? [];
     $from = $su["From"] ?? $this->core->ID;
     $illegal = $su["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($bl == 0 && $illegal == 0) {
      $att = "";
      $op = ($from == $you) ? $y : $this->core->Member($from);
      $cms = $this->core->Data("Get", [
       "cms",
       md5($op["Login"]["Username"])
      ]) ?? [];
      $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $su["NSFW"] == 0) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $op["Privacy"]["Posts"],
       "UN" => $from,
       "Y" => $you
      ]);
      if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
       $att = (!empty($su["Attachments"])) ? $this->view($attlv, ["Data" => [
        "ID" => base64_encode(implode(";", $su["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]) : "";
       $display = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $edit = ($op["Login"]["Username"] == $you) ? $this->core->Element([
        "button", "Delete", [
         "class" => "InnerMargin OpenDialog",
         "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteStatusUpdate")."&ID=".base64_encode($v))
        ]
       ]).$this->core->Element([
        "button", "Edit", [
         "class" => "InnerMargin OpenCard",
         "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$v")
        ]
       ]) : "";
       $modified = $su["Modified"] ?? "";
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Time = $this->core->TimeAgo($modified);
        $modified = " &bull; Modified ".$_Time;
        $modified = $this->core->Element(["em", $modified]);
       }
       $votes = ($op["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
       $votes = base64_encode("v=$votes&ID=".$su["ID"]."&Type=1");
       array_push($msg, [
        "[StatusUpdate.Attachments]" => base64_encode($att),
        "[StatusUpdate.Body]" => base64_encode($this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $su["Body"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($su["Created"])),
        "[StatusUpdate.DT]" => base64_encode(base64_encode("v=".base64_encode("StatusUpdate:Home")."&SU=".$su["ID"])),
        "[StatusUpdate.Edit]" => base64_encode($edit),
        "[StatusUpdate.ID]" => base64_encode($su["ID"]),
        "[StatusUpdate.Modified]" => base64_encode($modified),
        "[StatusUpdate.OriginalPoster]" => base64_encode($op["Personal"]["DisplayName"]),
        "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
        "[StatusUpdate.VoteID]" => base64_encode($su["ID"]),
        "[StatusUpdate.Votes]" => base64_encode($votes)
       ]);
      }
     }
    }
   } elseif($st == "MBR") {
    $ec = "Accepted";
    $home = base64_encode("Profile:Home");
    $extension = $this->core->Page("ba17995aafb2074a28053618fb71b912");
    $x = $this->core->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $t = $this->core->Data("Get", ["mbr", $value]) ?? [];
     if(!empty($t["Login"]["Username"])) {
      $bl = $this->core->CheckBlocked([
       $t, "Members", $y["Login"]["Username"]
      ]);
      $bl2 = $this->core->CheckBlocked([
       $y, "Members", $t["Login"]["Username"]
      ]);
      $cms = $this->core->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $contacts = $cms["Contacts"] ?? [];
      $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
      $ck = $this->core->CheckPrivacy([
       "Contacts" => $contacts,
       "Privacy" => $t["Privacy"]["Profile"],
       "UN" => $t["Login"]["Username"],
       "Y" => $y["Login"]["Username"]
      ]);
      $lookMeUp = $t["Privacy"]["LookMeUp"] ?? 0;
      if($bl == 0 && $bl2 == 0 && $ck == 1 && $lookMeUp == 1) {
       $de = "You have not added a Description.";
       $de = ($t["Login"]["Username"] != $y["Login"]["Username"]) ? "$display has not added a Description." : $de;
       $de = (!empty($t["Personal"]["Description"])) ? $this->core->PlainText([
        "BBCodes" => 1,
        "Data" => $t["Personal"]["Description"],
        "Display" => 1
       ]) : $de;
       $opt = $this->core->Element(["button", "View Profile", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("CARD=1&v=$home&UN=".base64_encode($t["Login"]["Username"]))
       ]]);
       array_push($msg, [
        "[X.LI.DisplayName]" => base64_encode($display),
        "[X.LI.Description]" => base64_encode($de),
        "[X.LI.Options]" => base64_encode($opt),
        "[X.LI.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%"))
       ]);
      }
     }
    }
   } elseif($st == "MBR-ALB") {
    $ec = "Accepted";
    $extension = $this->core->Page("b6728e167b401a5314ba47dd6e4a55fd");
    if($notAnon == 1) {
     $username = base64_decode($data["UN"]);
     $t = ($username != $you) ? $this->core->Member($username) : $y;
     $fs = $this->core->Data("Get", [
      "fs",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $albums = $fs["Albums"] ?? [];
     foreach($albums as $key => $value) {
      $cms = $this->core->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $tP = $t["Privacy"];
      $nsfw = $value["NSFW"] ?? $t["Privacy"]["NSFW"];
      $privacy = $value["Privacy"] ?? $t["Privacy"]["Albums"];
      $bl = $this->core->CheckBlocked([
       $y,
       "Albums",
       base64_encode($t["Login"]["Username"]."-$key")
      ]);
      $ck = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $privacy,
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $illegal = $value["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $ck = ($bl == 0 && $ck == 1 && $ck2 == 1 && $illegal == 0) ? 1 : 0;
      if($ck == 1 || $username == $you) {
       $coverPhoto = $value["ICO"] ?? "";
       $coverPhoto = $this->core->GetSourceFromExtension([
        $t["Login"]["Username"],
        $coverPhoto
       ]);
       array_push($msg, [
        "[Album.CRID]" => base64_encode($key),
        "[Album.CoverPhoto]" => base64_encode($coverPhoto),
        "[Album.Lobby]" => base64_encode(base64_encode("v=".base64_encode("Album:Home")."&AID=$key&UN=$username")),
        "[Album.Title]" => base64_encode($value["Title"])
       ]);
      }
     }
    }
   } elseif($st == "MBR-BLG") {
    $coverPhoto = $this->core->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $ec = "Accepted";
    $home = base64_encode("Blog:Home");
    $extension = $this->core->Page("ed27ee7ba73f34ead6be92293b99f844");
    if($notAnon == 1) {
     $blogs = $y["Blogs"] ?? [];
     foreach($blogs as $key => $value) {
      $blog = $this->core->Data("Get", ["blg", $value]) ?? [];
      $illegal = $blog["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($illegal == 0) {
      $coverPhoto = $blog["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
       array_push($msg, [
        "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
        "[X.LI.T]" => base64_encode($blog["Title"]),
        "[X.LI.D]" => base64_encode($blog["Description"]),
        "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".$blog["ID"]))
       ]);
      }
     }
    }
   } elseif($st == "MBR-CA" || $st == "MBR-JE") {
    $ec = "Accepted";
    $home = base64_encode("Page:Home");
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $extension = $this->core->Page("90bfbfb86908fdc401c79329bedd7df5");
    foreach($t["Pages"] as $key => $value) {
     $Page = $this->core->Data("Get", ["pg", $value]) ?? [];
     $st = str_replace("MBR-", "", $st);
     $t = $this->core->Member($Page["UN"]);
     $cms = $this->core->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $tP = $t["Privacy"];
     $b2 = ($t["Login"]["Username"] == $you) ? "Your Profile" : $t["Personal"]["DisplayName"]."'s Profile";
     $bl = $this->core->CheckBlocked([$t, "Members", $you]);
     $illegal = $Page["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $privacy = $tP["Profile"];
     $privacy = ($st == "CA") ? $tP["Contributions"] : $privacy;
     $privacy = ($st == "JE") ? $tP["Journal"] : $privacy;
     $ck = ($Page["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
     $ck2 = $this->core->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $privacy,
      "UN" => $Page["UN"],
      "Y" => $you
     ]);
     $ck3 = ($illegal == 0 && $Page["Category"] == $st) ? 1 : 0;
     $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
     $ck2 = ($bl == 0 || $t["Login"]["Username"] == $you) ? 1 : 0;
     if($ck == 1 && $ck2 == 1) {
      array_push($msg, [
       "[Article.Title]" => base64_encode($Page["Title"]),
       "[Article.Subtitle]" => base64_encode("Posted by ".$t["Personal"]["DisplayName"]." ".$this->core->TimeAgo($Page["Created"])."."),
       "[Article.Description]" => base64_encode($this->core->PlainText([
        "BBCodes" => 1,
        "Data" => $Page["Description"],
        "Display" => 1,
        "HTMLDecode" => 1
       ])),
       "[Article.ViewPage]" => base64_encode("$lpg;".base64_encode("v=$home&b2=$b2&back=1&lPG=$lpg&ID=".$Page["ID"]))
      ]);
     }
    }
   } elseif($st == "MBR-Forums") {
    $ec = "Accepted";
    $home = base64_encode("Forum:Home");
    $extension = $this->core->Page("ed27ee7ba73f34ead6be92293b99f844");
    $x = $y["Forums"] ?? [];
    foreach($x as $key => $value) {
     $illegal = $value["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($illegal == 0) {
      $forum = $this->core->Data("Get", ["pf", $value]) ?? [];
      $coverPhoto = $forum["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($forum["Title"]),
       "[X.LI.D]" => base64_encode($forum["Description"]),
       "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".base64_encode($forum["ID"])."&b2=".urlencode("Your Forums")."&lPG=$lpg"))
      ]);
     }
    }
   } elseif($st == "MBR-LLP") {
    $ec = "Accepted";
    $extension = $this->core->Page("da5c43f7719b17a9fab1797887c5c0d1");
    if($notAnon == 1) {
     $articles = $y["Pages"] ?? [];
     foreach($articles as $key => $value) {
      $article = $this->core->Data("Get", ["pg", $value]) ?? [];
      if($article["Category"] != "EXT") {
       array_push($msg, [
        "[Extension.Category]" => base64_encode($article["Category"]),
        "[Extension.Delete]" => base64_encode(base64_encode("v=".base64_encode("Authentication:DeletePage")."&ID=$value")),
        "[Extension.Description]" => base64_encode($this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $article["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[Extension.Edit]" => base64_encode(base64_encode("v=".base64_encode("Page:Edit")."&ID=".base64_encode($value))),
        "[Extension.ID]" => base64_encode($value),
        "[Extension.Title]" => base64_encode($article["Title"])
       ]);
      }
     }
    }
   } elseif($st == "MBR-SU") {
    $ec = "Accepted";
    $attlv = base64_encode("LiveView:InlineMossaic");
    $edit = base64_encode("StatusUpdate:Edit");
    $stream = $this->core->Data("Get", [
     "stream",
     md5(base64_decode($data["UN"]))
    ]) ?? [];
    $extension = $this->core->Page("18bc18d5df4b3516c473b82823782657");
    foreach($stream as $key => $value) {
     $id = $value["UpdateID"] ?? "";
     $att = "";
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $id]);
     $su = $this->core->Data("Get", ["su", $id]) ?? [];
     $ck = (empty($su["To"]) && $su["From"] == $you) ? 1 : 0;
     $illegal = $su["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if(($bl == 0 || $ck == 1) && $illegal == 0) {
      $op = ($ck == 1) ? $y : $this->core->Member($su["From"]);
      $cms = $this->core->Data("Get", [
       "cms",
       md5($op["Login"]["Username"])
      ]) ?? [];
      $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $su["NSFW"] == 0) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $su["Privacy"],
       "UN" => $su["From"],
       "Y" => $you
      ]);
      $ck2 = 1;
      if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
       $att = "";
       if(!empty($su["Attachments"])) {
        $att =  $this->view(base64_encode("LiveView:InlineMossaic"), [
         "Data" => [
          "ID" => base64_encode(implode(";", $su["Attachments"])),
          "Type" => base64_encode("DLC")
         ]
        ]);
        $att = $this->core->RenderView($att);
       }
       $display = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $edit = ($op["Login"]["Username"] == $you) ? $this->core->Element([
        "button", "Delete", [
         "class" => "InnerMargin OpenDialog",
         "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteStatusUpdate")."&ID=".base64_encode($id))
        ]
       ]).$this->core->Element([
        "button", "Edit", [
         "class" => "InnerMargin OpenCard",
         "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$id")
        ]
       ]) : "";
       $modified = $su["Modified"] ?? "";
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Time = $this->core->TimeAgo($modified);
        $modified = " &bull; Modified ".$_Time;
        $modified = $this->core->Element(["em", $modified]);
       }
       $votes = ($op["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
       $votes = base64_encode("v=$votes&ID=$id&Type=1");
       array_push($msg, [
        "[StatusUpdate.Attachments]" => base64_encode($att),
        "[StatusUpdate.Body]" => base64_encode($this->core->PlainText([
         "BBCodes" => 1,
         "Data" => $su["Body"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($su["Created"])),
        "[StatusUpdate.DT]" => base64_encode(base64_encode("v=".base64_encode("StatusUpdate:Home")."&SU=$id")),
        "[StatusUpdate.Edit]" => base64_encode($edit),
        "[StatusUpdate.ID]" => base64_encode($id),
        "[StatusUpdate.Modified]" => base64_encode($modified),
        "[StatusUpdate.OriginalPoster]" => base64_encode($display),
        "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
        "[StatusUpdate.VoteID]" => base64_encode($id),
        "[StatusUpdate.Votes]" => base64_encode($votes)
       ]);
      }
     }
    }
   } elseif($st == "MBR-XFS") {
    $ec = "Accepted";
    $albumID = $data["AID"] ?? md5("unsorted");
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $extension = $this->core->Page("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $fileSystem = $this->core->Data("Get", [
     "fs",
     md5($t["Login"]["Username"])
    ]) ?? [];
    if($t["Login"]["Username"] == $this->core->ID) {
     $files = $this->core->Data("Get", ["x", "fs"]) ?? [];
    } else {
     $files = $fileSystem["Files"] ?? [];
    } foreach($files as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Files", $value["ID"]]);
     $illegal = $value["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($albumID == $value["AID"] && $bl == 0 && $illegal == 0) {
      $source = $this->core->GetSourceFromExtension([
       $t["Login"]["Username"],
       $value
      ]);
      array_push($msg, [
       "[File.CoverPhoto]" => base64_encode("$source"),
       "[File.Title]" => base64_encode($value["Title"]),
       "[File.View]" => base64_encode("$lpg;".base64_encode("v=".base64_encode("File:Home")."&ID=".$value["ID"]."&UN=".$t["Login"]["Username"]."&back=1&lPG=$lpg"))
      ]);
     }
    }
   } elseif($st == "SHOP-Products") {
    $ec = "Accepted";
    $home = base64_encode("Product:Home");
    $coverPhoto = $this->core->PlainText([
     "Data" => "[sIMG:MiNY]",
     "Display" => 1
    ]);
    $un = $data["UN"] ?? base64_encode($you);
    $une = $un;
    $un = base64_decode($un);
    $t = ($un == $you) ? $y : $this->core->Member($un);
    $extension = $this->core->Page("ed27ee7ba73f34ead6be92293b99f844");
    $shop = $this->core->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $products = $shop["Products"] ?? [];
    foreach($products as $key => $value) {
     $p = $this->core->Data("Get", ["product", $value]) ?? [];
     $bl = $this->core->CheckBlocked([$y, "Products", $p["ID"]]);
     $ck = ($p["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
     $ck2 = (strtotime($this->core->timestamp) < $p["Expires"]) ? 1 : 0;
     $ck3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
     $ck = ($ck == 1 || $t["Login"]["Username"] == $this->core->ShopID) ? 1 : 0;
     $illegal = $p["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $illegal = ($t["Login"]["Username"] != $this->core->ShopID) ? 1 : 0;
     if($bl == 0 && $ck == 1 && $illegal == 0) {
      $coverPhoto = $p["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      $pub = $data["pubP"] ?? 0;
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($p["Title"]),
       "[X.LI.D]" => base64_encode($this->core->PlainText([
        "BBCodes" => 1,
        "Data" => $p["Description"],
        "Display" => 1,
        "HTMLDecode" => 1
       ])),
       "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".$p["ID"]."&UN=$une"))
      ]);
     }
    }
   } elseif($st == "Products") {
    $ec = "Accepted";
    $home = base64_encode("Product:Home");
    $coverPhoto = $this->core->PlainText([
     "Data" => "[sIMG:MiNY]",
     "Display" => 1
    ]);
    $extension = $this->core->Page("ed27ee7ba73f34ead6be92293b99f844");
    $members = $this->core->DatabaseSet("MBR") ?? [];
    foreach($members as $key => $value) {
     $v = $this->core->Data("Get", [
      "mbr",
      str_replace("c.oh.mbr.", "", $value)
     ]) ?? [];
     if($notAnon == 1) {
      $shop = $this->core->Data("Get", [
       "shop",
       md5($v["Login"]["Username"])
      ]) ?? [];
      $b2 = $b2 ?? "Products";
      $products = $shop["Products"] ?? [];
      foreach($products as $mbr => $p) {
       $p = $this->core->Data("Get", ["product", $p]) ?? [];
       $bl = $this->core->CheckBlocked([$y, "Products", $p["ID"]]);
       $une = base64_encode($v["Login"]["Username"]);
       $ck = ($p["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
       $ck2 = (strtotime($this->core->timestamp) < $p["Expires"]) ? 1 : 0;
       $ck3 = $v["Subscriptions"]["Artist"]["A"] ?? 0;
       $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
       $ck = ($ck == 1 || $v["Login"]["Username"] == $this->core->ShopID) ? 1 : 0;
       $illegal = $p["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       $illegal = ($v["Login"]["Username"] != $this->core->ShopID) ? 1 : 0;
       if($bl == 0 && $ck == 1 && $illegal == 0) {
        $coverPhoto = $p["ICO"] ?? $coverPhoto;
        $coverPhoto = base64_encode($coverPhoto);
        $pub = $data["pubP"] ?? 0;
        array_push($msg, [
         "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
         "[X.LI.T]" => base64_encode($p["Title"]),
         "[X.LI.D]" => base64_encode($this->core->PlainText([
          "BBCodes" => 1,
          "Data" => $p["Description"],
          "Display" => 1,
          "HTMLDecode" => 1
         ])),
         "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".$p["ID"]."&UN=".base64_encode($v["Login"]["Username"])."&lPG=$lpg&pubP=$pub"))
        ]);
       }
      }
     }
    }
   } elseif($st == "S-Blogger") {
    $blogs = $y["Blogs"] ?? [];
    $coverPhoto = $this->core->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $ec = "Accepted";
    $extension = $this->core->Page("ed27ee7ba73f34ead6be92293b99f844");
    foreach($blogs as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Blogs", $value]);
     $bg = $this->core->Data("Get", ["blg", $value]) ?? [];
     $ck = ($bg["UN"] == $you) ? 1 : 0;
     $ck2 = ($bg["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
     $illegal = $bg["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($bl == 0 && ($ck == 1 || $ck2 == 1) && $illegal == 0) {
      $coverPhoto = $bg["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($bg["Title"]),
       "[X.LI.D]" => base64_encode($this->core->PlainText([
        "BBCodes" => 1,
        "Data" => $bg["Description"],
        "Display" => 1,
        "HTMLDecode" => 1
       ])),
       "[X.LI.DT]" => base64_encode(base64_encode("v=".base64_encode("Blog:Home")))
      ]);
     }
    }
   } elseif($st == "SHOP") {
    $ec = "Accepted";
    $extension = $this->core->Page("6d8aedce27f06e675566fd1d553c5d92");
    if($notAnon == 1) {
     $b2 = $b2 ?? "Artists";
     $coverPhoto = $this->core->PlainText([
      "Data" => "[sIMG:MiNY]",
      "Display" => 1
     ]);
     $card = base64_encode("Shop:Home");
     $x = $this->core->DatabaseSet("MBR") ?? [];
     foreach($x as $k => $v) {
      $v = str_replace("c.oh.mbr.", "", $v);
      $t = $this->core->Data("Get", ["mbr", $v]) ?? [];
      if(!empty($t["Login"]["Username"])) {
       $cms = $this->core->Data("Get", [
        "cms",
        md5($t["Login"]["Username"])
       ]) ?? [];
       $cms = $cms["Contacts"] ?? [];
       $g = $this->core->Data("Get", [
        "shop",
        md5($t["Login"]["Username"])
       ]) ?? [];
       /*$shop = $this->core->Data("Get", [
        "shop",
        md5($t["Login"]["Username"])
       ]) ?? [];*/
       $bl = $this->core->CheckBlocked([
        $t, "Members", $y["Login"]["Username"]
       ]);
       $ck = $this->core->CheckPrivacy([
        "Contacts" => $cms,
        "Privacy" => $t["Privacy"]["Shop"],
        "UN" => $t["Login"]["Username"],
        "Y" => $y["Login"]["Username"]
       ]);
       $ck2 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
       $ck3 = $g["Open"] ?? 0;
       $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
       $illegal = $g["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       if($t["Login"]["Username"] == $y["Login"]["Username"] || ($bl == 0 && $ck == 1) && $illegal == 0) {
        $bl = $this->core->CheckBlocked([$y, "Shops", md5($t["Login"]["Username"])]);
        $coverPhoto = $g["CoverPhoto"] ?? $coverPhoto;
        $coverPhoto = base64_encode($coverPhoto);
        $tun = base64_encode($t["Login"]["Username"]);
        array_push($msg, [
         "[X.LI.CoverPhoto]" => base64_encode($this->core->CoverPhoto($coverPhoto)),
         "[X.LI.Description]" => base64_encode($g["Description"]),
         "[X.LI.Lobby]" => base64_encode(base64_encode("v=$card&CARD=1&UN=$tun")),
         "[X.LI.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%")),
         "[X.LI.Title]" => base64_encode($g["Title"])
        ]);
       }
      }
     }
    }
   } elseif($st == "SHOP-InvoicePresets") {
    $ec = "Accepted";
    $shop = $this->core->Data("Get", [
     "shop",
     $data["Shop"]
    ]) ?? [];
    $invoicePresets = $shop["InvoicePresets"] ?? [];
    $extension = $this->core->Page("e9f34ca1985c166bf7aa73116a745e92");
    foreach($invoicePresets as $key => $value) {
     $preset = $this->core->Data("Get", [
      "invoice-preset",
      $value
     ]) ?? [];
     $options = $this->core->Element(["button", "Delete", [
      "class" => "A OpenDialog v2",
      "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteService")."&ID=$value&Shop=".$data["Shop"])
     ]]);
     if(!empty($preset)) {
      array_push($msg, [
       "[ListItem.Description]" => base64_encode("A service currently on offer by ".$shop["Title"]),
       "[ListItem.Options]" => base64_encode($options),
       "[ListItem.Title]" => base64_encode($preset["Title"])
      ]);
     }
    }
   } elseif($st == "SHOP-Invoices") {
    $ec = "Accepted";
    $shop = $this->core->Data("Get", [
     "shop",
     $data["Shop"]
    ]) ?? [];
    $invoices = $shop["Invoices"] ?? [];
    $extension = $this->core->Page("e9f34ca1985c166bf7aa73116a745e92");
    foreach($invoices as $key => $value) {
     $invoice = $this->core->Data("Get", [
      "invoice",
      $value
     ]) ?? [];
     if(!empty($invoice)) {
      $options = $this->core->Element(["button", "Forward", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Invoice:Forward")."&Invoice=$value&Shop=".$data["Shop"])
      ]]).$this->core->Element(["button", "View", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Invoice:Home")."&Card=1&ID=$value")
      ]]);
      array_push($msg, [
       "[ListItem.Description]" => base64_encode("An Invoice created by ".$invoice["UN"]." &bull; Status: ".$invoice["Status"]."."),
       "[ListItem.Options]" => base64_encode($options),
       "[ListItem.Title]" => base64_encode("Invoice $value")
      ]);
     }
    }
   } elseif($st == "SHOP-Orders") {
    $ec = "Accepted";
    $extension = $this->core->Page("504e2a25db677d0b782d977f7b36ff30");
    $x = $this->core->Data("Get", [
     "po",
     md5($y["Login"]["Username"])
    ]) ?? [];
    foreach($x as $key => $value) {
     $t = $this->core->Member($value["UN"]);
     $t = $this->core->ProfilePicture($t, "margin:5%;width:90%");
     if(!empty($t["Login"])) {
      $ccomplete = ($value["Complete"] == 0) ? $this->core->Element(["button", "Mark as Complete", [
       "class" => "BB BBB CompleteOrder v2 v2w",
       "data-u" => base64_encode("v=".base64_encode("Shop:CompleteOrder")."&ID=".base64_encode($key))
      ]]) : "";
      array_push($msg, [
       "[X.LI.Order.Complete]" => base64_encode($complete),
       "[X.LI.Order.Instructions]" => $value["Instructions"],
       "[X.LI.Order.ProductID]" => base64_encode($value["ProductID"]),
       "[X.LI.Order.ProfilePicture]" => base64_encode($t),
       "[X.LI.Order.Quantity]" => base64_encode($value["QTY"]),
       "[X.LI.Order.UN]" => base64_encode($value["UN"])
      ]);
     }
    }
   } elseif($st == "US-SU") {
    $ec = "Accepted";
    $edit = base64_encode("StatusUpdate:Edit");
    $extension = $this->core->Page("18bc18d5df4b3516c473b82823782657");
    $x = $this->core->DatabaseSet("SU") ?? [];
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.su.", "", $v);
     $su = $this->core->Data("Get", ["su", $v]) ?? [];
     $from = $su["From"] ?? "";
     $ck = (!empty($from)) ? 1 : 0;
     $illegal = $su["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($ck == 1 && $illegal == 0) {
      $bl = $this->core->CheckBlocked([$y, "Status Updates", $v]);
      $from = $from ?? $this->core->ID;
      if($bl == 0 || $from == $you) {
       $att = "";
       if(!empty($su["Attachments"])) {
        $att =  $this->view(base64_encode("LiveView:InlineMossaic"), [
         "Data" => [
          "ID" => base64_encode(implode(";", $su["Attachments"])),
          "Type" => base64_encode("DLC")
         ]
        ]);
        $att = $this->core->RenderView($att);
       }
       $op = ($from == $y["Login"]["Username"]) ? $y : $this->core->Member($from);
       $cms = $this->core->Data("Get", [
        "cms",
        md5($op["Login"]["Username"])
       ]) ?? [];
       $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $su["NSFW"] == 0) ? 1 : 0;
       $ck2 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $op["Privacy"]["Posts"],
        "UN" => $from,
        "Y" => $y["Login"]["Username"]
       ]);
       if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
        $att = (!empty($su["Attachments"])) ? $this->view($attlv, ["Data" => [
         "ID" => base64_encode(implode(";", $su["Attachments"])),
         "Type" => base64_encode("DLC")
        ]]) : "";
        $bdy = base64_decode($su["Body"]);
        $display = ($op["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $edit = ($op["Login"]["Username"] == $you) ? $this->core->Element([
         "button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-view" => base64_encode("v=".base64_encode("Authentication:DeleteStatusUpdate")."&ID=".base64_encode($v))
         ]
        ]).$this->core->Element([
         "button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-view" => base64_encode(base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$v"))
         ]
        ]) : "";
        $modified = $su["Modified"] ?? "";
        if(empty($modified)) {
         $modified = "";
        } else {
         $_Time = $this->core->TimeAgo($modified);
         $modified = " &bull; Modified ".$_Time;
         $modified = $this->core->Element(["em", $modified]);
        }
        $votes = ($op["Login"]["Username"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
        $votes = base64_encode("v=$votes&ID=$v&Type=1");
        array_push($msg, [
         "[StatusUpdate.Attachments]" => base64_encode($att),
         "[StatusUpdate.Body]" => base64_encode($this->core->PlainText([
          "BBCodes" => 1,
          "Data" => $su["Body"],
          "Display" => 1,
          "HTMLDecode" => 1
         ])),
         "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($su["Created"])),
         "[StatusUpdate.DT]" => base64_encode(base64_encode("v=".base64_encode("StatusUpdate:Home")."&SU=".$su["ID"])),
         "[StatusUpdate.Edit]" => base64_encode($edit),
         "[StatusUpdate.ID]" => base64_encode($v),
         "[StatusUpdate.Modified]" => base64_encode($modified),
         "[StatusUpdate.OriginalPoster]" => base64_encode($display),
         "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[StatusUpdate.VoteID]" => base64_encode($v),
         "[StatusUpdate.Votes]" => base64_encode($votes)
        ]);
       }
      }
     }
    }
   } elseif($st == "XFS") {
    $ec = "Accepted";
    $extension = $this->core->Page("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $username = base64_decode($data["UN"]);
    if($this->core->ID == $username) {
     $files = $this->core->Data("Get", ["x", "fs"]) ?? [];
    } else {
     $files = $this->core->Data("Get", ["fs", md5($username)]) ?? [];
     $files = $files["Files"] ?? [];
    } foreach($files as $k => $v) {
     $bl = $this->core->CheckBlocked([$y, "Files", $v["ID"]]);
     $illegal = $v["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $source = $this->core->GetSourceFromExtension([
      $username,
      $v
     ]);
     $dlc = [
      "[File.CoverPhoto]" => base64_encode($source),
      "[File.View]" => base64_encode("$lpg;".base64_encode("v=".base64_encode("File:Home")."&AddTo=".$data["AddTo"]."&Added=".$data["Added"]."&ID=".$v["ID"]."&UN=$username&back=1&b2=Files&lPG=$st")),
      "[File.Title]" => base64_encode($v["Title"])
     ];
     if($bl == 0 && $illegal == 0) {
      if(!isset($data["ftype"]) && $bl == 0) {
       array_push($msg, $dlc);
      } else {
       $xf = json_decode(base64_decode($data["ftype"]));
       foreach($xf as $xf) {
        if($this->core->CheckFileType([$v["EXT"], $xf]) == 1 && $bl == 0) {
         array_push($msg, $dlc);
        }
       }
      }
      $i++;
     }
    }
   }
   return $this->core->JSONResponse([
    $ec,
    base64_encode($this->core->JSONResponse($msg)),
    base64_encode($extension),
    base64_encode($this->core->Element([
     "h3", $na, ["class" => "CenterText InnerMargin UpperCase"]
    ]))
   ]);
  }
  function ReSearch(array $a) {
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $goHome = ($pub == 1) ? $this->core->Element(["button", "Go Home", [
    "class" => "BBB v2 v2w",
    "onclick" => "W('".$this->core->base."', '_top');"
   ]]) : "";
   $query = $data["query"] ?? base64_encode("");
   $query = base64_decode(htmlentities($query));
   $search = $this->lists;
   $secureQuery = base64_encode("%$query%");
   $r = $this->core->Change([[
    "[ReSearch.GoHome]" => $goHome
   ], $this->core->Page("df4f7bc99b9355c34b571946e76b8481")]);
   if(!empty($query)) {
    $r = $this->core->Change([[
     "[ReSearch.Query]" => $query,
     "[ReSearch.Archive]" => base64_encode("v=$search&pub=1&query=$secureQuery&lPG=ReSearch&st=CA"),
     "[ReSearch.Artists]" => base64_encode("v=$search&pub=1&query=$secureQuery&lPG=ReSearch&st=SHOP"),
     "[ReSearch.Blogs]" => base64_encode("v=$search&pub=1&query=$secureQuery&lPG=ReSearch&st=BLG"),
     "[ReSearch.Forums]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Forums"),
     "[ReSearch.Members]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=MBR"),
     "[ReSearch.StatusUpdates]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=US-SU")
    ], $this->core->Page("bae5cdfa85bf2c690cbff302ba193b0b")]);
   } if($pub == 1) {
    $r = $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]);
    $r = $this->core->RenderView($r);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class Search extends GW {
  function __construct() {
   parent::__construct();
   $this->illegal = $this->system->core["SYS"]["Illegal"] ?? 777;
   $this->lists = base64_encode("Search:Lists");
   $this->you = $this->system->Member($this->system->Username());
  }
  function Containers(array $a) {
   $data = $a["Data"] ?? [];
   $b2 = $data["b2"] ?? "";
   $card = $data["CARD"] ?? 0;
   $h = "";
   $i = 0;
   $st = $data["st"] ?? "";
   $lpg = $data["lPG"] ?? $st;
   $pub = $data["pub"] ?? 0;
   $query = $data["query"] ?? "";
   $sl = $this->lists;
   $sta = $this->system->core["SYS"]["SearchIDs"];
   $ck = (!empty($st) && in_array($st, $sta)) ? 1 : 0;
   $li = "query=$query&st=$st&v=$sl";
   $lit = md5($st.$this->system->timestamp.rand(0, 1776));
   $lo = "";
   $r = $this->system->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "An empty or invalid list type was supplied.<br/>Data: ".json_encode($data, true)
   ], $this->system->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   $tpl = "6dc4eecde24bf5f5e70da253aaac2b68";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->system->ID != $you) ? 1 : 0;
   if($ck == 1) {
    if($st == "ADM-LLP") {
     $h = "Network Pages";
     $lis = "Search Pages";
     $lo =  ($notAnon == 1) ? $this->system->Element([
      "button", "New Page", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Page:Edit")."&new=1")
      ]
     ]) : "";
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "BGP") {
     $data = $this->system->FixMissing($data, ["BLG"]);
     $h = "Blog Posts";
     $li .= "&ID=".$data["ID"];
     $lis = "Search Posts";
    } elseif($st == "BL") {
     $bl = base64_decode($data["BL"]);
     $h = "$bl Blacklist";
     $li .= "&BL=".$data["BL"];
     $lis = "Search $bl Blacklist";
     $tpl = "6dc4eecde24bf5f5e70da253aaac2b68";
    } elseif($st == "BLG") {
     $h = "Blogs";
     $li .= "&b2=Blogs&lPG=$st";
     $lis = "Search Blogs";
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "Bulletins") {
     $h = "Bulletins";
     $lis = "Search Bulletins";
    } elseif($st == "CA") {
     $h = "Community Archive";
     $li .= "&b2=".urlencode("the Archive")."&lPG=$lpg";
     $lis = "Search Articles";
    } elseif($st == "CART") {
     $data = $this->system->FixMissing($data, ["UN"]);
     $t = $data["UN"] ?? $you;
     $t = ($t == $you) ? $y : $this->system->Member($t);
     $shop = $this->system->Data("Get", [
      "shop",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $li .= "&ID=".md5($t["Login"]["Username"]);
     $lis = "Search ".$shop["Title"];
     $tpl = "e58b4fc5070b14c01c88c28050547285";
    } elseif($st == "Contacts") {
     $h = "Contact Manager";
     $lis = "Search Contacts";
    } elseif($st == "ContactsChatList") {
     $data = $this->system->FixMissing($data, ["Chat"]);
     $h = "Contacts";
     $li .= "&Chat=".$data["Chat"];
     $lis = "Search Contacts";
    } elseif($st == "ContactsProfileList") {
     $data = $this->system->FixMissing($data, ["UN"]);
     $un = base64_decode($data["UN"]);
     $ck = ($un == $y["Login"]["Username"]) ? 1 : 0;
     $t = ($ck == 1) ? $y : $this->system->Member($un);
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
      $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
      $lo = ($Page["UN"] == $you && $notAnon == 1) ? $this->system->Element([
       "button", "Add Contributors", [
        "class" => "dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Page:Invite")."&ID=$id")
       ]
      ]) : "";
     } elseif($type == "Blog") {
      $id = base64_decode($id);
      $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
      $h = "Blog Contributors";
      $lo = ($blog["UN"] == $you && $notAnon == 1) ? $this->system->Element([
       "button", "Add Contributors", [
        "class" => "dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Blog:Invite")."&ID=$id")
       ]
      ]) : "";
     } elseif($type == "Forum") {
      $id = base64_decode($id);
      $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
      $h = "Forum Members";
      $lo = ($forum["UN"] == $you && $notAnon == 1) ? $this->system->Element([
       "button", "Invite Members", [
        "class" => "dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Forum:Invite")."&FID=".base64_encode($id))
       ]
      ]) : "";
     } elseif($type == "Shop") {
      $h = "Partners";
      $id = base64_decode($id);
      $shop = $this->system->Data("Get", ["shop", $id]) ?? [];
      $lo = ($id == md5($you) && $notAnon == 1) ? $this->system->Element([
       "button", "Hire Members", [
        "class" => "dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Shop:EditPartner")."&new=1")
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
     $lo = ($notAnon == 1) ? $this->system->Element([
      "button", "New Code", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=$dce&new=1")
      ]
     ]) : "";
    } elseif($st == "FAB") {
     $fd = base64_encode("Authentication:DeleteFAB");
     $fe = base64_encode("FAB:Edit");
     $h = "Free America Broadcasting";
     $lis = "Search Stations";
     $lo = ($notAnon == 1) ? $this->system->Element([
      "button", "Add a Broadcaster", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=$fe&new=1")
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
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "Forums-Admin") {
     $h = "Administrators";
     $li .= "&ID=".$data["ID"];
     $lis = "Search Administrators";
    } elseif($st == "Forums-Posts") {
     $id = $data["ID"] ?? "";
     $id = base64_decode($id);
     $f = $this->system->Data("Get", ["pf", $id]) ?? [];
     $h = "Forum Posts";
     $li .= "&ID=$id";
     $lis = "Search Posts from ".$f["Title"];
    } elseif($st == "Knowledge") {
     $h = "Knowledge Base";
     $lis = "Search Q&As";
     $tpl = "8568ac7727dae51ee4d96334fa891395";
    } elseif($st == "Mainstream") {
     $h = "The ".$st;
     $lis = "Search the Mainstream";
     $lo = $this->system->Element(["button", "Say Something", [
      "class" => "BBB MobileFull dB2O v2 v2w",
      "data-type" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($y["Login"]["Username"]))
     ]]);
     $tpl = "f2513ac8d0389416b680c75ed5667774";
    } elseif($st == "MBR") {
     $h = "Members";
     $lis = "Search Members";
    } elseif($st == "MBR-ALB") {
     $ae = base64_encode("Album:Edit");
     $un = base64_decode($data["UN"]);
     $t = ($un == $y["Login"]["Username"]) ? $y : $this->system->Member($un);
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Albums" : $t["Personal"]["DisplayName"]."'s Albums";
     $b2 = $b2 ?? $h;
     $b2 = urlencode($b2);
     $li .= "&UN=".base64_encode($t["Login"]["Username"])."&b2=$b2&lPG=$lpg";
     $lis = "Search Albums";
     $lo = ($ck == 1 && $notAnon == 1) ? $this->system->Element([
      "button", "New Album", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=$ae&new=1")
      ]
     ]) : "";
    } elseif($st == "MBR-BLG") {
     $bd = base64_encode("Authentication:DeleteBlogs");
     $be = base64_encode("Blog:Edit");
     $h = "Your Blogs";
     $li .= "&b2=Blogs&lPG=$lpg";
     $lis = "Search your Blogs";
     if($y["Subscriptions"]["Blogger"]["A"] == 1 && $notAnon == 1) {
      $lo .= $this->system->Element(["button", "New Blog", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=$be&new=1")
      ]]);
     }
    } elseif($st == "MBR-CA") {
     $t = $this->system->Member(base64_decode($data["UN"]));
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
     $lo = ($notAnon == 1) ? $this->system->Element([
      "button", "New Forum", [
       "class" => "dB2O v2", "data-type" => base64_encode("v=$fe&new=1")
      ]
     ]).$this->system->Element(["button", "Delete Forums", [
      "class" => "dBO v2", "data-type" => "v=$fd&all=1"
     ]]) : "";
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "MBR-JE") {
     $t = $this->system->Member(base64_decode($data["UN"]));
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
     $lo = ($notAnon == 1) ? $this->system->Element([
      "button", "New Page", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=$pe&new=1")
      ]
     ]) : "";
    } elseif($st == "MBR-SU") {
     $t = base64_decode($data["UN"]);
     $t = ($t != $you) ? $this->system->Member($t) : $y;
     $bl = $this->system->CheckBlocked([$t, "Members", $you]);
     $cms = $this->system->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $ck = ($t["Login"]["Username"] == $you) ? 1 : 0;
     $display = ($t["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $h = ($ck == 1) ? "Your Stream" : $display."'s Stream";
     $li .= "&UN=".base64_encode($t["Login"]["Username"]);
     $lis = "Search Posts";
     $lo = (($bl == 0 || $ck == 1) && $notAnon == 1) ? $this->system->Element([
      "button", "Say Something", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($t["Login"]["Username"]))
      ]
     ]) : "";
     $tpl = "8568ac7727dae51ee4d96334fa891395";
    } elseif($st == "MBR-XFS") {
     $aid = $data["AID"] ?? md5("unsorted");
     $fs = $this->system->Data("Get", ["fs", md5($you)]) ?? [];
     $xfsLimit = $this->system->core["XFS"]["limits"]["Total"] ?? 0;
     $xfsLimit = $xfsLimit."MB";
     $xfsUsage = 0;
     foreach($fs["Files"] as $key => $value) {
      $xfsUsage = $xfsUsage + $value["Size"];
     }
     $xfsUsage = $this->system->ByteNotation($xfsUsage)."MB";
     $limit = $this->system->Change([["MB" => "", "," => ""], $xfsLimit]);
     $usage = $this->system->Change([["MB" => "", "," => ""], $xfsUsage]);
     $un = $data["UN"] ?? base64_encode($you);
     $un = base64_decode($un);
     $t = ($un == $you) ? $y : $this->system->Member($un);
     $fs = $this->system->Data("Get", [
      "fs",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $alb = $fs["Albums"][$aid] ?? [];
     $ck = $y["Subscriptions"]["XFS"]["A"] ?? 0;
     $ck = ($ck == 1 && $notAnon == 1) ? 1 : 0;
     $ck2 = ($un == $this->system->ID && $y["Rank"] == md5("High Command")) ? 1 : 0;
     $de = $alb["Description"] ?? "";
     $display = ($t["Personal"]["DisplayName"] == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $h = $alb["Title"] ?? "Unsorted";
     $li .= "&AID=$aid&UN=".$data["UN"]."&lPG=$lpg";
     $lis = "Search $h";
     $uf = ($ck == 1) ? "You have unlimited storage." : "You used $xfsUsage out of $xfsLimit.";
     $ck = ($ck == 1 || $usage < $limit) ? 1 : 0;
     if(($ck == 1 && $un == $you) || $ck2 == 1) {
      $lo = $this->system->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display,
       "[Album.Uploader]" => base64_encode("v=".base64_encode("File:Upload")."&AID=$aid&UN=".$t["Login"]["Username"]),
       "[Album.XFSstats]" => $uf
      ], $this->system->Page("b9e1459dc1c687cebdaa9aade72c50a9")]);
     } else {
      $lo = $this->system->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display
      ], $this->system->Page("af26c6866abb335fb69327ed3963a182")]);
     }
     $tpl = "46ef1d0890a2a5639f67bfda1634ca82";
    } elseif($st == "MiNY") {
     $h = "Products";
     $username = $data["UN"] ?? base64_encode($you);
     $li .= "&UN=$username&b2=$b2&lPG=$lpg&pubP=".$data["pubP"]."&st=$st";
     $lis = "Search $b2";
     $t = base64_decode($data["UN"]);
     $t = $this->system->Member($t);
     $isArtist = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $shop = $this->system->Data("Get", [
      "shop",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $contributors = $shop["Contributors"] ?? [];
     foreach($contributors as $member => $role) {
      $ck = ($isArtist == 1 && $member == $you && $notAnon == 1) ? 1 : 0;
      if($ck == 1 && $i == 0) {
       $lo .= $this->system->Element(["button", "New Product", [
        "class" => "dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Product:Edit")."&new=1")
       ]]);
       $i++;
      }
     }
     $ck = ($t["Login"]["Username"] == $you && $notAnon == 1) ? 1 : 0;
     $lo .= ($isArtist == 1 && $ck == 1) ? $this->system->Element([
      "button", "Discount Codes", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=".base64_encode("Search:Containers")."&st=DC")
      ]
     ]) : "";
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "PR") {
     $h = "Press Releases";
     $li .= "&b2=".urlencode("Press Releases")."&lPG=$lpg";
     $lis = "Search Articles";
     $pe = base64_encode("Page:Edit");
     $lo = ($y["Rank"] == md5("High Command") && $notAnon == 1) ? $this->system->Element([
      "button", "New Article", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=$pe&new=1")
      ]
     ]) : "";
    } elseif($st == "S-Blogger") {
     $be = base64_encode("Clog:Edit");
     $h = "Your Blogs";
     $li .= "&lPG=$st";
     $lis = "Search Blogs";
     if($y["Subscriptions"]["Blogger"]["A"] == 1 && $notAnon == 1) {
      $lo = $this->system->Element(["button", "New Blog", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("v=$be&new=1")
      ]]);
     }
    } elseif($st == "SHOP") {
     $h = "Artists";
     $li .= "&lPG=$lpg&st=$st";
     $lis = "Search Shops";
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "SHOP-Orders") {
     $lis = "Search Orders";
     $tpl = "e58b4fc5070b14c01c88c28050547285";
    } elseif($st == "SHOP-Products") {
     $h = "Products";
     $li .= "&lPG=$lpg&st=$st";
     $lis = "Search Products";
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "XFS") {
     $_AddTo = $data["AddTo"] ?? "";
     $_Added = $data["Added"] ?? "";
     $h = "Files";
     $lPG = $data["lPG"] ?? $st;
     $li .= "&AddTo=".$_AddTo."&Added=".$_Added."&UN=".$data["UN"]."&lPG=$lpg";
     $li .= (isset($data["ftype"])) ? "&ftype=".$data["ftype"] : "";
     $lis = "Search Files";
     $tpl = "e3de2c4c383d11d97d62a198f15ee885";
    }
    $li = base64_encode($li);
    $r = $this->system->Change([[
     "[Search.Type]" => $st,
     "[UI.LIT]" => $lit,
     "[UI.LIU]" => $li,
     "[UI.header]" => $h,
     "[UI.options]" => $lo,
     "[UI.s]" => $lis,
     "[XS.UI]" => $li
    ], $this->system->Page($tpl)]);
   } if(in_array($st, ["DC", "FAB", "MBR-MiNY"])) {
    $r = $this->system->Card(["Front" => $r]);
   } else {
    $r = ($pub == 1) ? $this->view(base64_encode("WebUI:Containers"), [
     "Data" => ["Content" => $r]
    ]) : $r;
   }
   $r = ($card == 1) ? $this->system->Card(["Front" => $r]) : $r;
   return $r;
  }
  function Lists(array $a) {
   $base = $this->system->base;
   $blu = base64_encode("Common:SaveBlacklist");
   $cr = base64_encode("Common:Reactions");
   $data = $a["Data"] ?? [];
   $key = $this->system->core["SQL"]["Key"];
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
   $notAnon = ($this->system->ID != $you) ? 1 : 0;
   if($st == "ADM-LLP") {
    $ec = "Accepted";
    $tpl = $this->system->Page("da5c43f7719b17a9fab1797887c5c0d1");
    if($notAnon == 1) {
     $delete = base64_encode("Authentication:DeletePage");
     $edit = base64_encode("Page:Edit");
     $Pages = $this->system->DatabaseSet("PG") ?? [];
     /*$Pages = $this->system->SQL("SELECT CAST(AES_DECRYPT(Body, :key) AS CHAR(8000)) AS Body,
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
     while($Page = $Pages->fetchAll(PDO::FETCH_ASSOC)) {*/
     foreach($Pages as $k => $v) {
      #$na.=" ".$query.json_encode($Page, true);//TEMP
      $v = str_replace("c.oh.pg.", "", $v);
      #$Page = $this->system->Data("Get", ["pg", $Page["ID"]]) ?? [];
      $Page = $this->system->Data("Get", ["pg", $v]) ?? [];
      if($Page["Category"] == "EXT" || $Page["High Command"] == 1) {
       $id = $Page["ID"] ?? $v;
       array_push($msg, [
        "[X.LI.T]" => base64_encode($Page["Title"]),
        "[X.LI.D]" => base64_encode($this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $Page["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[X.LI.Delete]" => base64_encode("v=$delete&ID=$id"),
        "[X.LI.K]" => base64_encode($id),
        "[X.LI.C]" => base64_encode($Page["Category"]),
        "[X.LI.DT]" => base64_encode(base64_encode("v=$edit&ID=".base64_encode($id)))
       ]);
      }
     }
     #$na.=" ".$query.json_encode($Pages, true);//TEMP
    }
   } elseif($st == "BGP") {
    $ec = "Accepted";
    $blog = $this->system->Data("Get", [
     "blg",
     base64_decode($data["ID"])
    ]) ?? [];
    $owner = ($blog["UN"] == $you) ? $y : $this->system->Member($blog["UN"]);
    $tpl = $this->system->Page("dba88e1a123132be03b9a2e13995306d");
    if($notAnon == 1) {
     $_IsBlogger = $owner["Subscriptions"]["Blogger"]["A"] ?? 0;
     $coverPhoto = $this->system->PlainText([
      "Data" => "[sIMG:CP]",
      "Display" => 1
     ]);
     $title = $blog["Title"];
     $title = urlencode($title);
     $posts = $blog["Posts"] ?? [];
     foreach($posts as $key => $value) {
      $post = $this->system->Data("Get", ["bp", $value]) ?? [];
      $actions = ($post["UN"] != $you) ? $this->system->Element([
       "button", "Block", [
        "class" => "BLK InnerMargin",
        "data-cmd" => base64_encode("B"),
        "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($post["ID"])."&list=".base64_encode("Blog Posts")."&BC=")
       ]
      ]) : "";
      $actions = ($this->system->ID != $you) ? $actions : "";
      $bl = $this->system->CheckBlocked([$y, "Blog Posts", $value]);
      $cms = $this->system->Data("Get", ["cms", md5($post["UN"])]) ?? [];
      $ck = $this->system->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $post["Privacy"],
       "UN" => $post["UN"],
       "Y" => $you
      ]);
      $ck2 = ($post["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
      $illegal = $post["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && ($ck == 1 && $ck2 == 1) && $illegal == 0) {
       if($blog["UN"] == $you || $post["UN"] == $you) {
        $combinedID = base64_encode($blog["ID"]."-".$post["ID"]);
        $actions .= $this->system->Element([
         "button", "Delete", [
          "class" => "InnerMargin dBO",
          "data-type" => "v=".base64_encode("Authentication:DeleteBlogPost")."&ID=$combinedID"
         ]
        ]);
        $actions .= ($_IsBlogger == 1) ? $this->system->Element([
         "button", "Edit", [
          "class" => "InnerMargin dB2O",
          "data-type" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=".$blog["ID"]."&Post=".$post["ID"])
         ]
        ]) : "";
       }
       $contributors = $post["Contributors"] ?? $blog["Contributors"];
       $coverPhoto = (!empty($post["ICO"])) ? base64_encode($post["ICO"]) : $coverPhoto;
       $op = ($post["UN"] == $you) ? $y : $this->system->Member($post["UN"]);
       $display = ($op["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $memberRole = ($blog["UN"] == $post["UN"]) ? "Owner" : $contributors[$author];
       $modified = $post["ModifiedBy"] ?? [];
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Member = end($modified);
        $_Time = $this->system->TimeAgo(array_key_last($modified));
        $modified = " &bull; Modified ".$_Time." by ".$_Member;
        $modified = $this->system->Element(["em", $modified]);
       }
       array_push($msg, [
        "[BlogPost.Actions]" => base64_encode($actions),
        "[BlogPost.Author]" => base64_encode($display),
        "[BlogPost.Description]" => base64_encode($post["Description"]),
        "[BlogPost.Created]" => base64_encode($this->system->TimeAgo($post["Created"])),
        "[BlogPost.ID]" => base64_encode($post["ID"]),
        "[BlogPost.MemberRole]" => base64_encode($memberRole),
        "[BlogPost.Modified]" => base64_encode($modified),
        "[BlogPost.ProfilePicture]" => base64_encode($this->system->ProfilePicture($op, "margin:5%;width:90%")),
        "[BlogPost.Title]" => base64_encode($post["Title"]),
        "[BlogPost.View]" => base64_encode("Blog".$blog["ID"].";".base64_encode("v=".base64_encode("BlogPost:Home")."&Blog=".$blog["ID"]."&Post=".$post["ID"]."&b2=".$blog["Title"]."&back=1")),
       ]);
      }
     }
    }
   } elseif($st == "BL") {
    $ec = "Accepted";
    $tpl = $this->system->Page("e05bae15ffea315dc49405d6c93f9b2c");
    if($notAnon == 1) {
     $bl = base64_decode($data["BL"]);
     $x = $y["Blocked"][$bl] ?? [];
     foreach($x as $k => $v) {
      if($bl == "Albums") {
       $alb = explode("-", base64_decode($v));
       $t = ($alb[0] != $y["Login"]["Username"]) ? $this->system->Member($alb[0]) : $y;
       $fs = $this->system->Data("Get", [
        "fs",
        md5($t["Login"]["Username"])
       ]) ?? [];
       $alb = $fs["Albums"][$alb[1]];
       $de = $alb["Description"];
       $h = "<em>".$alb["Title"]."</em>";
       $p = "v=$blu&BU=".base64_encode($h)."&content=".base64_encode($v)."&list=".base64_encode($bl)."&BC=";
       $vi = $this->system->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Blogs") {
       $bg = $this->system->Data("Get", ["blg", $v]) ?? [];
       $de = $bg["Description"];
       $h = "<em>".$bg["Title"]."</em>";
       $p = "v=$blu&BU=".base64_encode($h)."&content=".base64_encode($v)."&list=".base64_encode($bl)."&BC=";
       $vi = $this->system->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Blog Posts") {
       $bp = $this->system->Data("Get", ["bp", $v]) ?? [];
       $de = $bp["Description"];
       $h = "<em>".$bp["Title"]."</em>";
       $p = "v=$blu&BU=".base64_encode($h)."&content=".base64_encode($v)."&list=".base64_encode($bl)."&BC=";
       $vi = $this->system->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Pages") {
       $Page = $this->system->Data("Get", ["pg", $v]) ?? [];
       $de = $Page["Description"];
       $h = "<em>".$Page["Title"]."</em>";
       $p = "v=$blu&BU=".base64_encode($h)."&content=".base64_encode($v)."&list=".base64_encode($bl)."&BC=";
       $vi = $this->system->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Status Updates") {
       $su = $this->system->Data("Get", ["su", $v]) ?? [];
       $de = $this->system->Excerpt(base64_decode($su["Body"]), 180);
       $h = $su["From"];
       $p = "v=$blu&BU=".base64_encode("this Post")."&content=".base64_encode($v)."&list=".base64_encode($bl)."&BC=";
       $u = "this Post";
       $vi = $this->system->Element(["button", "View $u", [
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
    $blogs = $this->system->DatabaseSet("BLG") ?? [];
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $ec = "Accepted";
    $home = base64_encode("Blog:Home");
    $tpl = $this->system->Page("ed27ee7ba73f34ead6be92293b99f844");
    foreach($blogs as $key => $value) {
     $value = str_replace("c.oh.blg.", "", $value);
     $blog = $this->system->Data("Get", ["blg", $value]) ?? [];
     $cms = $this->system->Data("Get", ["cms", md5($blog["UN"])]);
     $bl = $this->system->CheckBlocked([$y, "Blogs", $blog["ID"]]);
     $ck = ($y["Personal"]["Age"] >= $this->system->core["minAge"] || $bg["NSFW"] == 0) ? 1 : 0;
     $ck2 = $this->system->CheckPrivacy([
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
       "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($blog["Title"]),
       "[X.LI.D]" => base64_encode($blog["Description"]),
       "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".$blog["ID"]))
      ]);
     }
    }
   } elseif($st == "Bulletins") {
    $bulletins = $this->system->Data("Get", [
     "bulletins",
     md5($you)
    ]) ?? [];
    $ec = "Accepted";
    $message = base64_encode("Profile:BulletinMessage");
    $options = base64_encode("Profile:BulletinOptions");
    $tpl = $this->system->Page("ae30582e627bc060926cfacf206920ce");
    foreach($bulletins as $key => $value) {
     $t = $this->system->Member($value["From"]);
     $display = ($t["Personal"]["DisplayName"] == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $pic = $this->system->ProfilePicture($t, "margin:5%;width:90%");
     $value["ID"] = $key;
     array_push($msg, [
      "[Bulletin.Date]" => base64_encode($this->system->TimeAgo($value["Sent"])),
      "[Bulletin.From]" => base64_encode($display),
      "[Bulletin.ID]" => base64_encode($key),
      "[Bulletin.Message]" => base64_encode($this->view($message, [
       "Data" => $value
      ])),
      "[Bulletin.Options]" => base64_encode($this->view($options, [
       "Data" => [
        "Bulletin" => base64_encode(json_encode($value, true))
       ]
      ])),
      "[Bulletin.Picture]" => base64_encode($pic)
     ]);
    }
   } elseif($st == "CA" || $st == "PR") {
    $ec = "Accepted";
    $home = base64_encode("Page:Home");
    $tpl = $this->system->Page("e7829132e382ee4ab843f23685a123cf");
    $Pages = $this->system->DatabaseSet("PG") ?? [];
    foreach($Pages as $key => $value) {
     $value = str_replace("c.oh.pg.", "", $value);
     $Page = $this->system->Data("Get", ["pg", $value]) ?? [];
     if(!empty($Page["UN"])) {
      $nsfw = $Page["NSFW"] ?? 0;
      $t = ($Page["UN"] == $you) ? $y : $this->system->Member($Page["UN"]);
      $bl = $this->system->CheckBlocked([$y, "Pages", $Page["ID"]]);
      $cat = $Page["Category"] ?? "";
      $cms = $this->system->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $ck = ($Page["Category"] == $st) ? 1 : 0;
      $ck2 = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
      $ck3 = (($st == "CA" && $Page["Category"] == "CA") || ($st == "PR" && $Page["Category"] == "PR")) ? 1 : 0;
      $ck4 = $this->system->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $Page["Privacy"],
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) ? 1 : 0;
      $illegal = $Page["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $illegal == 0) {
      $coverPhoto = $Page["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
       array_push($msg, [
        "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
        "[X.LI.T]" => base64_encode($Page["Title"]),
        "[X.LI.D]" => base64_encode($this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $Page["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[X.LI.DT]" => base64_encode("$lpg;".base64_encode("v=$home&b2=$b2&back=1&lPG=$lpg&ID=".$Page["ID"]))
       ]);
      }
     }
    }
   } elseif($st == "CART") {
    $ec = "Accepted";
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:MiNY]",
     "Display" => 1
    ]);
    $data = $this->system->FixMissing($data, ["ID"]);
    $newCartList = [];
    $now = $this->system->timestamp;
    $remove = base64_encode("Cart:Remove");
    $tpl = $this->system->Page("dea3da71b28244bf7cf84e276d5d1cba");
    $x = $y["Shopping"]["Cart"][$data["ID"]] ?? [];
    $x = $x["Products"] ?? [];
    foreach($x as $k => $v) {
     $p = $this->system->Data("Get", ["miny", $k]) ?? [];
     $productIsActive = (strtotime($now) < $p["Expires"]) ? 1 : 0;
     $illegal = $p["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $quantity = $p["Quantity"] ?? 0;
     if(!empty($p) && $productIsActive == 1 && $quantity != 0 && $illegal == 0) {
      $coverPhoto = $p["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      $newCartList[$k] = $v;
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($p["Title"]),
       "[X.LI.D]" => base64_encode($p["Description"].$you),
       "[X.LI.Remove]" => base64_encode($this->view($remove, ["Data" => [
        "ProductID" => base64_encode($k),
        "ShopID" => base64_encode($data["ID"])
       ]]))
      ]);
     }
    }
    $y["Shopping"]["Cart"][$data["ID"]]["Products"] ?? $newCartList;
    $this->system->Data("Save", ["mbr", md5($you), $y]);
   } elseif($st == "Contacts") {
    $ec = "Accepted";
    $tpl = $this->system->Page("ccba635d8c7eca7b0b6af5b22d60eb55");
    if($notAnon == 1) {
     $cms = $this->system->Data("Get", [
      "cms",
      md5($y["Login"]["Username"])
     ]) ?? [];
     $cms = $cms["Contacts"] ?? [];
     foreach($cms as $key => $value) {
      $t = $this->system->Member($key);
      $delete = base64_encode("v=".base64_encode("Contact:Delete"));
      $id = md5($key);
      $options = "v=".base64_encode("Contact:Options")."&UN=".base64_encode($key);
      array_push($msg, [
       "[Contact.Delete]" => base64_encode($delete),
       "[Contact.DisplayName]" => base64_encode($t["Personal"]["DisplayName"]),
       "[Contact.Form]" => base64_encode($id),
       "[Contact.ID]" => base64_encode($id),
       "[Contact.ProfilePicture]" => base64_encode($this->system->ProfilePicture($t, "margin:5%;width:90%")),
       "[Contact.Username]" => base64_encode($key),
       "[Options]" => base64_encode($options)
      ]);
     }
    }
   } elseif($st == "ContactsChatList") {
    $ec = "Accepted";
    $tpl = $this->system->Page("343f78d13872e3b4e2ac0ba587ff2910");
    if($notAnon == 1) {
     $chat = base64_encode("Chat:Home");
     $chatHome = $data["Chat"] ?? 0;
     $cms = $this->system->Data("Get", [
      "cms",
      md5($y["Login"]["Username"])
     ]) ?? [];
     $cms = $cms["Contacts"] ?? [];
     foreach($cms as $k => $v) {
      $t = $this->system->Member($k);
      $c = "v=$chat&GroupChat=0&to=".base64_encode($k);
      $fst = ($chatHome == 1) ? "N/A" : $c;
      $nps = ($chatHome == 1) ? $c : "N/A";
      /*$o = ($t["Personal"]["OnlineStatus"] == 1) ? $this->system->Element([
       "span",
       NULL,
       ["class" => "online"]
      ]) : "";*/
      $pp = $this->system->ProfilePicture($t);
      array_push($msg, [
       "[X.LI.Click.FST]" => base64_encode($fst),
       "[X.LI.Click.Ground]" => base64_encode($nps),
       "[X.LI.Click.MD5]" => base64_encode(md5("Chat_$k")),
       "[X.LI.Member.DN]" => base64_encode($t["Personal"]["DisplayName"]),
       "[X.LI.Member.ProfilePicture]" => base64_encode($pp),
       "[X.LI.Online]" => base64_encode("")
       #"[X.LI.Online]" => base64_encode($o)
      ]);
     }
    }
   } elseif($st == "ContactsProfileList") {
    $ec = "Accepted";
    $home = base64_encode("Profile:Home");
    $tpl = $this->system->Page("ba17995aafb2074a28053618fb71b912");
    $x = $this->system->Data("Get", [
     "cms",
     md5(base64_decode($data["UN"]))
    ]) ?? [];
    $x = $x["Contacts"] ?? [];
    foreach($x as $k => $v) {
     $t = $this->system->Member($k);
     $cms = $this->system->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $bl = $this->system->CheckBlocked([
      $t, "Members", $y["Login"]["Username"]
     ]);
     $bl2 = $this->system->CheckBlocked([
      $y, "Members", $t["Login"]["Username"]
     ]);
     $ck = $this->system->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $t["Privacy"]["Profile"],
      "UN" => $t["Login"]["Username"],
      "Y" => $y["Login"]["Username"]
     ]);
     if($bl == 0 && $bl2 == 0 && $ck == 1) {
      $opt = $this->system->Element(["button", "View Profile", [
       "class" => "dB2O v2",
       "data-type" => base64_encode("CARD=1&v=$home&back=1&b2=$b2&lPG=$lpg&pub=0&UN=".base64_encode($t["Login"]["Username"]))
      ]]);
      array_push($msg, [
       "[X.LI.DisplayName]" => base64_encode($t["Personal"]["DisplayName"]),
       "[X.LI.Description]" => base64_encode($t["Personal"]["Description"]),
       "[X.LI.Options]" => base64_encode($opt),
       "[X.LI.ProfilePicture]" => base64_encode($this->system->ProfilePicture($t, "margin:5%;width:90%"))
      ]);
     }
    }
   } elseif($st == "ContactsRequests") {
    $ec = "Accepted";
    $tpl = $this->system->Page("8b6ac25587a4524c00b311c184f6c69b");
    if($notAnon == 1) {
     $cms = $this->system->Data("Get", [
      "cms",
      md5($y["Login"]["Username"])
     ]) ?? [];
     $cms = $cms["Requests"] ?? [];
     foreach($cms as $key => $value) {
      $t = $this->system->Member($value);
      $pp = $this->system->ProfilePicture($t, "margin:5%;width:90%");
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
    $data = $this->system->FixMissing($data, ["ID", "Type"]);
    $id = $data["ID"];
    $tpl = $this->system->Page("ba17995aafb2074a28053618fb71b912");
    $type = $data["Type"];
    $ck = (!empty($id)) ? 1 : 0;
    $ck2 = (!empty($type)) ? 1 : 0;
    if($ck == 1 && $ck2 == 1) {
     $id = base64_decode($id);
     $type = base64_decode($type);
     if($type == "Article") {
      $Page = $this->system->Data("Get", ["pg", $id]) ?? [];
      $contributors = $Page["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Blog") {
      $blog = $this->system->Data("Get", ["blg", $id]) ?? [];
      $contributors = $blog["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Forum") {
      $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
      $contributors = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Shop") {
      $shop = $this->system->Data("Get", ["shop", $id]) ?? [];
      $contributors = $shop["Contributors"] ?? [];
     } foreach($contributors as $member => $role) {
      $description = "No Description";
      $displayname = "Anonymous";
      $opt = "";
      $t = ($member == $you) ? $y : $this->system->Member($member);
      if($type == "Article") {
       $ban = base64_encode("Page:Banish");
       $bl = $this->system->CheckBlocked([$t, "Members", $you]);
       $bl2 = $this->system->CheckBlocked([$y, "Members", $member]);
       $cr = base64_encode("Authentication:ArticleChangeMemberRole");
       $cms = $this->system->Data("Get", [
        "cms",
        md5($t["Login"]["Username"])
       ]) ?? [];
       $ck = $this->system->CheckPrivacy([
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
        $description = (!empty($t["Description"])) ? $this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $t["Description"],
         "Display" => 1
        ]) : $description;
        $displayname = $t["Personal"]["DisplayName"];
        $eid = base64_encode($Page["ID"]);
        $mbr = base64_encode($t["Login"]["Username"]);
        $opt = ($ck == 1 && $ck2 == 1) ? $this->system->Element([
         "button", "Banish", [
          "class" => "dBO v2",
          "data-type" => "v=$ban&ID=$eid&Member=$mbr"
         ]
        ]).$this->system->Element([
         "button", "Change Role", [
          "class" => "dBO v2",
          "data-type" => "v=$cr&ID=$eid&Member=$mbr"
         ]
        ]) : "";
       }
      } elseif($type == "Blog") {
       $ban = base64_encode("Blog:Banish");
       $bl = $this->system->CheckBlocked([$t, "Members", $you]);
       $bl2 = $this->system->CheckBlocked([$y, "Members", $member]);
       $cr = base64_encode("Authentication:BlogChangeMemberRole");
       $cms = $this->system->Data("Get", [
        "cms",
        md5($t["Login"]["Username"])
       ]) ?? [];
       $ck = $this->system->CheckPrivacy([
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
        $description = (!empty($t["Description"])) ? $this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $t["Description"],
         "Display" => 1
        ]) : $description;
        $displayname = $t["Personal"]["DisplayName"];
        $eid = base64_encode($blog["ID"]);
        $mbr = base64_encode($t["Login"]["Username"]);
        $opt = ($ck == 1 && $ck2 == 1) ? $this->system->Element([
         "button", "Banish", [
          "class" => "dBO v2",
          "data-type" => "v=$ban&ID=$eid&Member=$mbr"
         ]
        ]).$this->system->Element([
         "button", "Change Role", [
          "class" => "dBO v2",
          "data-type" => "v=$cr&ID=$eid&Member=$mbr"
         ]
        ]) : "";
       }
      } elseif($type == "Forum") {
       $ban = base64_encode("Forum:Banish");
       $bl = $this->system->CheckBlocked([$t, "Members", $you]);
       $bl2 = $this->system->CheckBlocked([$y, "Members", $member]);
       $cr = base64_encode("Authentication:PFChangeMemberRole");
       $cms = $this->system->Data("Get", [
        "cms",
        md5($t["Login"]["Username"])
       ]) ?? [];
       $ck = $this->system->CheckPrivacy([
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
        $description = (!empty($t["Personal"]["Description"])) ? $this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $t["Personal"]["Description"],
         "Display" => 1
        ]) : $description;
        $displayname = $t["Personal"]["DisplayName"];
        $eid = base64_encode($forum["ID"]);
        $mbr = base64_encode($t["Login"]["Username"]);
        $opt = ($ck == 1 && $ck2 == 1) ? $this->system->Element([
         "button", "Banish", [
          "class" => "dBO v2",
          "data-type" => "v=$ban&ID=$eid&Member=$mbr"
         ]
        ]).$this->system->Element([
         "button", "Change Role", [
          "class" => "dBO v2",
          "data-type" => "v=$cr&ID=$eid&Member=$mbr"
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
       $opt = ($ck == 1) ? $this->system->Element(["button", "Edit", [
        "class" => "dB2O v2",
        "data-type" => base64_encode("v=".base64_encode("Shop:EditPartner")."&UN=$memberID")
       ]]).$this->system->Element(["button", "Fire", [
        "class" => "dBO v2",
        "data-type" => "v=".base64_encode("Shop:Banish")."&ID=$eid&UN=$memberID"
       ]]) : "";
      }
      array_push($msg, [
       "[X.LI.DisplayName]" => base64_encode($displayname),
       "[X.LI.Description]" => base64_encode($description),
       "[X.LI.Options]" => base64_encode($opt),
       "[X.LI.ProfilePicture]" => base64_encode($this->system->ProfilePicture($t, "margin:5%;width:90%"))
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
    $tpl = $this->system->Page("3bfe162215ac1c6a69e6eb0e2baf3cdb");
    if($notAnon == 1) {
     $dcd = base64_encode("Authentication:DeleteDiscountCode");
     $dce = base64_encode("DiscountCode:Edit");
     $x = $this->system->Data("Get", [
      "dc",
      md5($y["Login"]["Username"])
     ]) ?? [];
     foreach($x as $k => $v) {
      array_push($msg, [
       "[X.LI.Discount.Code]" => $v["Code"],
       "[X.LI.Discount.Delete]" => base64_encode("v=$dcd&ID=$k"),
       "[X.LI.Discount.Edit]" => base64_encode(base64_encode("v=$dce&ID=$k")),
       "[X.LI.Discount.Percentile]" => base64_encode($v["Percentile"]),
       "[X.LI.Discount.Quantity]" => base64_encode($v["Quantity"])
      ]);
     }
    }
   } elseif($st == "Feedback") {
    $ec = "Accepted";
    $now = $this->system->timestamp;
    $x = $this->system->DatabaseSet("KB") ?? [];
    $tpl = $this->system->Page("e7c4e4ed0a59537ffd00a2b452694750");
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.knowledge.", "", $value);
     $feedback = $this->system->Data("Get", ["knowledge", $value]) ?? [];
     $mesasge = $feedback["Thread"] ?? [];
     $mesasge = $feedback["Thread"][0] ?? [];
     $message = $feedback["Thread"][0]["Body"] ?? "";
     if(!empty($message)) {
      $message = $this->system->PlainText([
       "Data" => $message,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]);
     }
     $modified = $feedback["Sent"] ?? $now;
     $modified = $this->system->TimeAgo($modified);
     $resolved = $feedback["Resolved"] ?? 0;
     $resolved = ($resolved == 1) ? "Resolved" : "Not Resolved";
     $title = $feedback["Subject"] ?? "New Feedback";
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
    $tpl = $this->system->Page("ed27ee7ba73f34ead6be92293b99f844");
    $x = $this->system->DatabaseSet("PF") ?? [];
    foreach($x as $key => $value) {
     $active = 0;
     $value = str_replace("c.oh.pf.", "", $value);
     $bl = $this->system->CheckBlocked([$y, "Forums", $value]);
     $forum = $this->system->Data("Get", ["pf", $value]) ?? [];
     $manifest = $this->system->Data("Get", ["pfmanifest", $value]) ?? [];
     $t = ($forum["UN"] == $you) ? $y : $this->system->Member($forum["UN"]);
     $cms = $this->system->Data("Get", ["cms", md5($t["Login"]["Username"])]);
     $ck = $forum["Open"] ?? 0;
     $ck2 = ($y["Personal"]["Age"] >= $this->system->core["minAge"] || $forum["NSFW"] == 0) ? 1 : 0;
     $ck3 = $this->system->CheckPrivacy([
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
       "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
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
    $tpl = $this->system->Page("ba17995aafb2074a28053618fb71b912");
    if(!empty($id)) {
     $admin = base64_decode($admin);
     $id = base64_decode($id);
     $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
     foreach($manifest as $member => $role) {
      if($member == $admin || $role == "Admin") {
       $t = ($member == $you) ? $y : $this->system->Member($member);
       $bl = $this->system->CheckBlocked([
        $t,
        "Members",
        $you
       ]);
       $bl2 = $this->system->CheckBlocked([
        $y,
        "Members",
        $t["Login"]["Username"]
       ]);
       $contacts = $this->system->Data("Get", ["cms", md5($member)]) ?? [];
       $ck = $this->system->CheckPrivacy([
        "Contacts" => $contacts["Contacts"],
        "Privacy" => $t["Privacy"]["Profile"],
        "UN" => $member,
        "Y" => $you
       ]);
       if($bl == 0 && $bl2 == 0 && $ck == 1) {
        $description = "You have not added a Description.";
        $description = ($t["Login"]["Username"] != $you) ? $t["Personal"]["DisplayName"]." has not added a Description." : $description;
        $description = (!empty($t["Personal"]["Description"])) ? $this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $t["Description"],
         "Display" => 1
        ]) : $description;
        $displayname = $t["Personal"]["DisplayName"];
        array_push($msg, [
         "[X.LI.DisplayName]" => base64_encode($displayname),
         "[X.LI.Description]" => base64_encode($description),
         "[X.LI.Options]" => base64_encode(""),
         "[X.LI.ProfilePicture]" => base64_encode($this->system->ProfilePicture($t, "margin:5%;width:90%"))
        ]);
       }
      }
     }
    }
   } elseif($st == "Forums-Posts") {
    $ec = "Accepted";
    $active = 0;
    $admin = 0;
    $attlv = base64_encode("LiveView:InlineMossaic");
    $id = $data["ID"] ?? "";
    $forum = $this->system->Data("Get", ["pf", $id]) ?? [];
    $home = base64_encode("ForumPost:Home");
    $manifest = $this->system->Data("Get", ["pfmanifest", $id]) ?? [];
    foreach($manifest as $k => $v) {
     if($active == 0 && $k == $y["Login"]["Username"]) {
      $active = 0;
      if($admin == 0 && $v == "Admin") {
       $admin++;
      }
     }
    }
    $posts = $forum["Posts"] ?? [];
    $tpl = $this->system->Page("150dcee8ecbe0e324a47a8b5f3886edf");
    if($active == 1 || $admin == 1 || $forum["Type"] == "Public") {
     foreach($posts as $key => $value) {
      $actions = "";
      $att = "";
      $bl = $this->system->CheckBlocked([$y, "Forum Posts", $value]);
      $post = $this->system->Data("Get", ["post", $value]) ?? [];
      $cms = $this->system->Data("Get", ["cms", md5($post["From"])]) ?? [];
      $illegal = $post["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $op = ($forum["UN"] == $you) ? $y : $this->system->Member($post["From"]);
      $ck = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
      $ck2 = ($y["Personal"]["Age"] >= $this->system->core["minAge"] || $post["NSFW"] == 0) ? 1 : 0;
      $ck3 = $this->system->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $post["Privacy"],
       "UN" => $post["From"],
       "Y" => $you
      ]);
      if($bl == 0 && ($ck2 == 1 && $ck3 == 1) && $illegal == 0) {
       $bl = $this->system->CheckBlocked([$y, "Status Updates", $id]);
       $con = base64_encode("Conversation:Home");
       $actions = ($post["From"] != $you) ? $this->system->Element([
        "button", "Block", [
         "class" => "BLK InnerMargin",
         "data-cmd" => base64_encode("B"),
         "data-u" => base64_encode("v=".base64_encode("Common:SaveBlacklist")."&BU=".base64_encode("this Post")."&content=".base64_encode($post["ID"])."&list=".base64_encode("Forum Posts")."&BC=")
        ]
       ]) : "";
       $actions = ($this->system->ID != $you) ? $actions : "";
       if($ck == 1) {
        $actions .= $this->system->Element([
         "button", "Delete", [
          "class" => "InnerMargin dBO",
          "data-type" => "v=".base64_encode("Authentication:DeleteForumPost")."&FID=$id&ID=".$post["ID"]
         ]
        ]);
        $actions .= ($admin == 1 || $ck == 1) ? $this->system->Element([
         "button", "Edit", [
          "class" => "InnerMargin dB2O",
          "data-type" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$id&ID=".$post["ID"])
         ]
        ]) : "";
       }
       $actions .= ($forum["Type"] == "Public") ? $this->system->Element([
        "button", "Share", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=".base64_encode("ForumPost:Share")."&ID=".base64_encode($id."-".$post["ID"]))
        ]
       ]) : "";
       $att = (!empty($post["Attachments"])) ? $this->view($attlv, ["Data" => [
        "ID" => base64_encode(implode(";", $post["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]) : "";
       $display = ($op["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $memberRole = ($op["Login"]["Username"] == $forum["UN"]) ? "Owner" : $manifest[$op["Login"]["Username"]];
       $modified = $post["ModifiedBy"] ?? [];
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Member = end($modified);
        $_Time = $this->system->TimeAgo(array_key_last($modified));
        $modified = " &bull; Modified ".$_Time." by ".$_Member;
        $modified = $this->system->Element(["em", $modified]);
       }
       $reactions = ($op["Login"]["Username"] != $you) ? base64_encode($this->view(base64_encode("Common:Reactions"), ["Data" => [
        "CRID" => $post["ID"],
        "T" => $op["Login"]["Username"],
        "Type" => 3
       ]])) : base64_encode($this->system->Element([
        "div", "&nbsp;", ["class" => "Desktop66"]
       ]));
       array_push($msg, [
        "[ForumPost.Actions]" => base64_encode($actions),
        "[ForumPost.Attachments]" => base64_encode($att),
        "[ForumPost.Body]" => base64_encode($this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $post["Body"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[ForumPost.Comment]" => base64_encode(base64_encode("v=$home&FID=$id&ID=".$post["ID"])),
        "[ForumPost.Created]" => base64_encode($this->system->TimeAgo($post["Created"])),
        "[ForumPost.ID]" => base64_encode($post["ID"]),
        "[ForumPost.MemberRole]" => base64_encode($memberRole),
        "[ForumPost.Modified]" => base64_encode($modified),
        "[ForumPost.OriginalPoster]" => base64_encode($display),
        "[ForumPost.ProfilePicture]" => base64_encode($this->system->ProfilePicture($op, "margin:5%;width:90%")),
        "[ForumPost.Reactions]" => $reactions,
        "[ForumPost.Title]" => base64_encode($post["Title"])
       ]);
      }
     }
    }
   } elseif($st == "FAB") {
    $ec = "Accepted";
    $fd = base64_encode("Authentication:DeleteFAB");
    $fe = base64_encode("FAB:Edit");
    $ico = $this->system->PlainText([
     "Data" => "[sIMG:FAB]", "Display" => 1
    ]);
    $tpl = $this->system->Page("488e0e8946181efa5e2666ed0b997adc");
    $x = $this->system->Data("Get", [
     "x",
     md5("FreeAmericaBroadcasting")
    ]) ?? [];
    foreach($x as $k => $v) {
     $bl = $this->system->CheckBlocked([
      $k, "Broadcasters", $y["Login"]["Username"]
     ]);
     $ck = ($v["NSFW"] == 0 || $y["Personal"]["Age"] >= $this->system->core["MinAge"]) ? 1 : 0;
     if($bl == 0 && $ck == 1) {
      $ck = ($v["Role"] == 1 || $v["UN"] == $you) ? 1 : 0;
      $ico = (!empty($v["ICO"])) ? "$base/efs/".$v["ICO"] : $ico;
      $opt = ($ck == 1) ? $this->system->Element([
       "div", $this->system->Element(["button", "Delete", [
        "class" => "A BB dBO v2 v2w",
        "data-type" => "v=$fd&ID=$k"
       ]]), ["class" => "Desktop50"]
      ]).$this->system->Element([
       "div", $this->system->Element(["button", "Edit", [
        "class" => "BB dB2O v2 v2w",
        "data-type" => base64_encode("v=$fe&ID=".base64_encode($k))
       ]]), ["class" => "esktop0"]
      ]) : "";
      array_push($msg, [
       "[X.LI.Description]" => base64_encode($this->system->PlainText([
        "BBCodes" => 1,
        "Data" => $v["Description"],
        "Display" => 1,
        "HTMLDecode" => 1
       ])),
       "[X.LI.ICO]" => base64_encode($ico),
       "[X.LI.Listen]" => base64_encode($v["Listen"]),
       "[X.LI.NONAME]" => base64_encode(""),
       "[X.LI.Options]" => base64_encode($opt),
       "[X.LI.Title]" => base64_encode($v["Title"]),
       "[X.LI.URL]" => base64_encode($v["URL"])
      ]);
     }
    }
   } elseif($st == "Knowledge") {
    $ec = "Accepted";
    $tpl = $this->system->Page("#");
    $x = $this->system->DatabaseSet("KB");
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.kb.", "", $v);
    }
   } elseif($st == "Mainstream") {
    $ec = "Accepted";
    $edit = base64_encode("StatusUpdate:Edit");
    $attlv = base64_encode("LiveView:InlineMossaic");
    $tpl = $this->system->Page("18bc18d5df4b3516c473b82823782657");
    $x = $this->system->Data("Get", ["x", "mainstream"]) ?? [];
    foreach($x as $k => $v) {
     $bl = $this->system->CheckBlocked([$y, "Status Opdates", $v]);
     $su = $this->system->Data("Get", ["su", $v]) ?? [];
     $from = $su["From"] ?? $this->system->ID;
     $illegal = $su["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($bl == 0 && $illegal == 0) {
      $att = "";
      $op = ($from == $y["Login"]["Username"]) ? $y : $this->system->Member($from);
      $cms = $this->system->Data("Get", [
       "cms",
       md5($op["Login"]["Username"])
      ]) ?? [];
      $ck = ($y["Personal"]["Age"] >= $this->system->core["minAge"] || $su["NSFW"] == 0) ? 1 : 0;
      $ck2 = $this->system->CheckPrivacy([
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
       $display = ($op["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $edit = ($op["Login"]["Username"] == $you) ? $this->system->Element([
        "button", "Delete", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=".base64_encode("Authentication:DeleteStatusUpdate")."&ID=".base64_encode($v)
        ]
       ]).$this->system->Element([
        "button", "Edit", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$v")
        ]
       ]) : "";
       $modified = $su["Modified"] ?? "";
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Time = $this->system->TimeAgo($modified);
        $modified = " &bull; Modified ".$_Time;
        $modified = $this->system->Element(["em", $modified]);
       }
       $reactions = ($op["Login"]["Username"] != $you) ? base64_encode($this->view(base64_encode("Common:Reactions"), [
        "Data" => [
         "CRID" => $v,
         "T" => $op["Login"]["Username"],
         "Type" => 3
        ]
       ])) : base64_encode($this->system->Element([
        "div", "&nbsp;", ["class" => "Desktop66"]
       ]));
       array_push($msg, [
        "[StatusUpdate.Attachments]" => base64_encode($att),
        "[StatusUpdate.Body]" => base64_encode($this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $su["Body"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[StatusUpdate.Created]" => base64_encode($this->system->TimeAgo($su["Created"])),
        "[StatusUpdate.DT]" => base64_encode(base64_encode("v=".base64_encode("StatusUpdate:Home")."&SU=".$su["ID"])),
        "[StatusUpdate.Edit]" => base64_encode($edit),
        "[StatusUpdate.ID]" => base64_encode($su["ID"]),
        "[StatusUpdate.Modified]" => base64_encode($modified),
        "[StatusUpdate.OriginalPoster]" => base64_encode($op["Personal"]["DisplayName"]),
        "[StatusUpdate.ProfilePicture]" => base64_encode($this->system->ProfilePicture($op, "margin:5%;width:90%")),
        "[StatusUpdate.Reactions]" => $reactions
       ]);
      }
     }
    }
   } elseif($st == "MBR") {
    $ec = "Accepted";
    $home = base64_encode("Profile:Home");
    $tpl = $this->system->Page("ba17995aafb2074a28053618fb71b912");
    $x = $this->system->DatabaseSet("MBR") ?? [];
    foreach($x as $key => $value) {
     $value = str_replace("c.oh.mbr.", "", $value);
     $t = $this->system->Data("Get", ["mbr", $value]) ?? [];
     if(!empty($t["Login"]["Username"])) {
      $bl = $this->system->CheckBlocked([
       $t, "Members", $y["Login"]["Username"]
      ]);
      $bl2 = $this->system->CheckBlocked([
       $y, "Members", $t["Login"]["Username"]
      ]);
      $cms = $this->system->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $contacts = $cms["Contacts"] ?? [];
      $display = ($t["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
      $ck = $this->system->CheckPrivacy([
       "Contacts" => $contacts,
       "Privacy" => $t["Privacy"]["Profile"],
       "UN" => $t["Login"]["Username"],
       "Y" => $y["Login"]["Username"]
      ]);
      #$lookMeUp = $t["Privacy"]["LookMeUp"] ?? 1;
      $lookMeUp = 1;
      if($bl == 0 && $bl2 == 0 && $ck == 1 && $lookMeUp == 1) {
       $de = "You have not added a Description.";
       $de = ($t["Login"]["Username"] != $y["Login"]["Username"]) ? "$display has not added a Description." : $de;
       $de = (!empty($t["Personal"]["Description"])) ? $this->system->PlainText([
        "BBCodes" => 1,
        "Data" => $t["Personal"]["Description"],
        "Display" => 1
       ]) : $de;
       $opt = $this->system->Element(["button", "View Profile", [
        "class" => "dB2O v2",
        "data-type" => base64_encode("CARD=1&v=$home&UN=".base64_encode($t["Login"]["Username"]))
       ]]);
       array_push($msg, [
        "[X.LI.DisplayName]" => base64_encode($display),
        "[X.LI.Description]" => base64_encode($de),
        "[X.LI.Options]" => base64_encode($opt),
        "[X.LI.ProfilePicture]" => base64_encode($this->system->ProfilePicture($t, "margin:5%;width:90%"))
       ]);
      }
     }
    }
   } elseif($st == "MBR-ALB") {
    $ec = "Accepted";
    $tpl = $this->system->Page("b6728e167b401a5314ba47dd6e4a55fd");
    if($notAnon == 1) {
     $data["UN"] = base64_decode($data["UN"]);
     $t = ($data["UN"] != $y["Login"]["Username"]) ? $this->system->Member($data["UN"]) : $y;
     $fs = $this->system->Data("Get", [
      "fs",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $x = $fs["Albums"] ?? [];
     foreach($x as $k => $v) {
      $abl = base64_encode($t["Login"]["Username"]."-$k");
      $fr = $this->system->Data("Get", [
       "cms",
       md5($t["Login"]["Username"])
      ]) ?? [];
      $tP = $t["Privacy"];
      $nsfw = $v["NSFW"] ?? $t["Privacy"]["NSFW"];
      $pri = $v["Privacy"] ?? $t["Privacy"]["Albums"];
      $bl = $this->system->CheckBlocked([$y, "Albums", $abl]);
      $ck = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
      $ck2 = $this->system->CheckPrivacy([
       "Contacts" => $fr["Contacts"],
       "Privacy" => $pri,
       "UN" => $t["Login"]["Username"],
       "Y" => $y["Login"]["Username"]
      ]);
      $illegal = $v["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && ($ck == 1 && $ck2 == 1) && $illegal == 0) {
       $coverPhoto = $v["ICO"] ?? "";
       $coverPhoto = $this->system->GetSourceFromExtension([
        $t["Login"]["Username"],
        $coverPhoto
       ]);
       array_push($msg, [
        "[Album.CRID]" => base64_encode($k),
        "[Album.CoverPhoto]" => base64_encode($coverPhoto),
        "[Album.Lobby]" => base64_encode(base64_encode("v=".base64_encode("Album:Home")."&AID=$k&UN=".$data["UN"])),
        "[Album.Title]" => base64_encode($v["Title"])
       ]);
      }
     }
    }
   } elseif($st == "MBR-BLG") {
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $ec = "Accepted";
    $home = base64_encode("Blog:Home");
    $tpl = $this->system->Page("ed27ee7ba73f34ead6be92293b99f844");
    if($notAnon == 1) {
     $blogs = $y["Blogs"] ?? [];
     foreach($blogs as $key => $value) {
      $blog = $this->system->Data("Get", ["blg", $value]) ?? [];
      $illegal = $blog["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($illegal == 0) {
      $coverPhoto = $blog["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
       array_push($msg, [
        "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
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
    $t = ($t == $you) ? $y : $this->system->Member($t);
    $tpl = $this->system->Page("90bfbfb86908fdc401c79329bedd7df5");
    foreach($t["Pages"] as $key => $value) {
     $Page = $this->system->Data("Get", ["pg", $value]) ?? [];
     $st = str_replace("MBR-", "", $st);
     $t = $this->system->Member($Page["UN"]);
     $cms = $this->system->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $tP = $t["Privacy"];
     $b2 = ($t["Login"]["Username"] == $you) ? "Your Profile" : $t["Personal"]["DisplayName"]."'s Profile";
     $bl = $this->system->CheckBlocked([$t, "Members", $you]);
     $illegal = $Page["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $privacy = $tP["Profile"];
     $privacy = ($st == "CA") ? $tP["Contributions"] : $privacy;
     $privacy = ($st == "JE") ? $tP["Journal"] : $privacy;
     $ck = ($Page["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
     $ck2 = $this->system->CheckPrivacy([
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
       "[Article.Subtitle]" => base64_encode("Posted by ".$t["Personal"]["DisplayName"]." ".$this->system->TimeAgo($Page["Created"])."."),
       "[Article.Description]" => base64_encode($this->system->PlainText([
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
    $tpl = $this->system->Page("ed27ee7ba73f34ead6be92293b99f844");
    $x = $y["Forums"] ?? [];
    foreach($x as $key => $value) {
     $illegal = $value["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($illegal == 0) {
      $forum = $this->system->Data("Get", ["pf", $value]) ?? [];
      $coverPhoto = $forum["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($forum["Title"]),
       "[X.LI.D]" => base64_encode($forum["Description"]),
       "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".base64_encode($forum["ID"])."&b2=".urlencode("Your Forums")."&lPG=$lpg"))
      ]);
     }
    }
   } elseif($st == "MBR-LLP") {
    $ec = "Accepted";
    $tpl = $this->system->Page("da5c43f7719b17a9fab1797887c5c0d1");
    if($notAnon == 1) {
     $delete = base64_encode("Authentication:DeletePage");
     $Pages = $y["Pages"] ?? [];
     $edit = base64_encode("Page:Edit");
     foreach($Pages as $key => $value) {
      $Page = $this->system->Data("Get", ["pg", $value]) ?? [];
      if($Page["Category"] != "EXT") {
       array_push($msg, [
        "[X.LI.T]" => base64_encode($Page["Title"]),
        "[X.LI.D]" => base64_encode($this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $Page["Description"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[X.LI.Delete]" => base64_encode("v=$delete&ID=$value"),
        "[X.LI.K]" => base64_encode($value),
        "[X.LI.C]" => base64_encode($Page["Category"]),
        "[X.LI.DT]" => base64_encode(base64_encode("v=$edit&ID=$value&lPG=$lpg&b2=$b2"))
       ]);
      }
     }
    }
   } elseif($st == "MBR-SU") {
    $ec = "Accepted";
    $attlv = base64_encode("LiveView:InlineMossaic");
    $edit = base64_encode("StatusUpdate:Edit");
    $stream = $this->system->Data("Get", [
     "stream",
     md5(base64_decode($data["UN"]))
    ]) ?? [];
    $tpl = $this->system->Page("18bc18d5df4b3516c473b82823782657");
    foreach($stream as $key => $value) {
     $id = $value["UpdateID"] ?? "";
     $att = "";
     $bl = $this->system->CheckBlocked([$y, "Status Updates", $id]);
     $su = $this->system->Data("Get", ["su", $id]) ?? [];
     $ck = (empty($su["To"]) && $su["From"] == $you) ? 1 : 0;
     $illegal = $su["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if(($bl == 0 || $ck == 1) && $illegal == 0) {
      $op = ($ck == 1) ? $y : $this->system->Member($su["From"]);
      $cms = $this->system->Data("Get", [
       "cms",
       md5($op["Login"]["Username"])
      ]) ?? [];
      $ck = ($y["Personal"]["Age"] >= $this->system->core["minAge"] || $su["NSFW"] == 0) ? 1 : 0;
      $ck2 = $this->system->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $su["Privacy"],
       "UN" => $su["From"],
       "Y" => $you
      ]);
      $ck2 = 1;
      if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
       $att = (!empty($su["Attachments"])) ? $this->view($attlv, ["Data" => [
        "ID" => base64_encode(implode(";", $su["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]) : "";
       $display = ($op["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
       $edit = ($op["Login"]["Username"] == $you) ? $this->system->Element([
        "button", "Delete", [
         "class" => "InnerMargin dBO",
         "data-type" => "v=".base64_encode("Authentication:DeleteStatusUpdate")."&ID=".base64_encode($id)
        ]
       ]).$this->system->Element([
        "button", "Edit", [
         "class" => "InnerMargin dB2O",
         "data-type" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$id")
        ]
       ]) : "";
       $modified = $su["Modified"] ?? "";
       if(empty($modified)) {
        $modified = "";
       } else {
        $_Time = $this->system->TimeAgo($modified);
        $modified = " &bull; Modified ".$_Time;
        $modified = $this->system->Element(["em", $modified]);
       }
       $reactions = ($op["Login"]["Username"] != $you) ? base64_encode($this->view(base64_encode("Common:Reactions"), [
        "Data" => [
         "CRID" => $id,
         "T" => $op["Login"]["Username"],
         "Type" => 3
        ]
       ])) : base64_encode($this->system->Element([
        "div", "&nbsp;", ["class" => "Desktop66"]
       ]));
       array_push($msg, [
        "[StatusUpdate.Attachments]" => base64_encode($att),
        "[StatusUpdate.Body]" => base64_encode($this->system->PlainText([
         "BBCodes" => 1,
         "Data" => $su["Body"],
         "Display" => 1,
         "HTMLDecode" => 1
        ])),
        "[StatusUpdate.Created]" => base64_encode($this->system->TimeAgo($su["Created"])),
        "[StatusUpdate.DT]" => base64_encode(base64_encode("v=".base64_encode("StatusUpdate:Home")."&SU=$id")),
        "[StatusUpdate.Edit]" => base64_encode($edit),
        "[StatusUpdate.ID]" => base64_encode($id),
        "[StatusUpdate.Modified]" => base64_encode($modified),
        "[StatusUpdate.OriginalPoster]" => base64_encode($display),
        "[StatusUpdate.ProfilePicture]" => base64_encode($this->system->ProfilePicture($op, "margin:5%;width:90%")),
        "[StatusUpdate.Reactions]" => $reactions
       ]);
      }
     }
    }
   } elseif($st == "MBR-XFS") {
    $ec = "Accepted";
    $albumID = $data["AID"] ?? md5("unsorted");
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->system->Member($t);
    $tpl = $this->system->Page("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $fileSystem = $this->system->Data("Get", [
     "fs",
     md5($t["Login"]["Username"])
    ]) ?? [];
    if($t["Login"]["Username"] == $this->system->ID) {
     $files = $this->system->Data("Get", ["x", "fs"]) ?? [];
    } else {
     $files = $fileSystem["Files"] ?? [];
    } foreach($files as $key => $value) {
     $bl = $this->system->CheckBlocked([$y, "Files", $value["ID"]]);
     $illegal = $value["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($albumID == $value["AID"] && $bl == 0 && $illegal == 0) {
      $source = $this->system->GetSourceFromExtension([
       $t["Login"]["Username"],
       $value
      ]);
      array_push($msg, [
       "[X.LI.DT]" => base64_encode("$lpg;".base64_encode("v=".base64_encode("File:Home")."&ID=".$value["ID"]."&UN=".$t["Login"]["Username"]."&back=1&lPG=$lpg")),
       "[X.LI.Style]" => base64_encode("$source"),
       "[X.LI.Title]" => base64_encode($value["Title"]),
       "[X.LI.Type]" => base64_encode("Desktop33")
      ]);
     }
    }
   } elseif($st == "MiNY") {
    $ec = "Accepted";
    $home = base64_encode("Product:Home");
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:MiNY]",
     "Display" => 1
    ]);
    $un = $data["UN"] ?? base64_encode($you);
    $une = $un;
    $un = base64_decode($un);
    $t = ($un == $you) ? $y : $this->system->Member($un);
    $tpl = $this->system->Page("ed27ee7ba73f34ead6be92293b99f844");
    $shop = $this->system->Data("Get", [
     "shop",
     md5($t["Login"]["Username"])
    ]) ?? [];
    $products = $shop["Products"] ?? [];
    foreach($products as $key => $v) {
     $p = $this->system->Data("Get", ["miny", $v]) ?? [];
     $bl = $this->system->CheckBlocked([$y, "Products", $p["ID"]]);
     $ck = ($p["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
     $ck2 = (strtotime($this->system->timestamp) < $p["Expires"]) ? 1 : 0;
     $ck3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
     $ck = ($ck == 1 || $t["Login"]["Username"] == $this->system->ShopID) ? 1 : 0;
     $illegal = $p["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $illegal = ($t["Login"]["Username"] != $this->system->ShopID) ? 1 : 0;
     if($bl == 0 && $ck == 1 && $illegal == 0) {
      $coverPhoto = $p["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      $pub = $data["pubP"] ?? 0;
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($p["Title"]),
       "[X.LI.D]" => base64_encode($this->system->PlainText([
        "BBCodes" => 1,
        "Data" => $p["Description"],
        "Display" => 1,
        "HTMLDecode" => 1
       ])),
       "[X.LI.DT]" => base64_encode(base64_encode("v=$home&CARD=1&ID=".$p["ID"]."&UN=$une"))
      ]);
     }
    }
   } elseif($st == "S-Blogger") {
    $blogs = $y["Blogs"] ?? [];
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:CP]",
     "Display" => 1
    ]);
    $ec = "Accepted";
    $tpl = $this->system->Page("ed27ee7ba73f34ead6be92293b99f844");
    foreach($blogs as $key => $value) {
     $bl = $this->system->CheckBlocked([$y, "Blogs", $value]);
     $bg = $this->system->Data("Get", ["blg", $value]) ?? [];
     $ck = ($bg["UN"] == $you) ? 1 : 0;
     $ck2 = ($bg["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
     $illegal = $bg["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($bl == 0 && ($ck == 1 || $ck2 == 1) && $illegal == 0) {
      $coverPhoto = $bg["ICO"] ?? $coverPhoto;
      $coverPhoto = base64_encode($coverPhoto);
      array_push($msg, [
       "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
       "[X.LI.T]" => base64_encode($bg["Title"]),
       "[X.LI.D]" => base64_encode($this->system->PlainText([
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
    $tpl = $this->system->Page("6d8aedce27f06e675566fd1d553c5d92");
    if($notAnon == 1) {
     $b2 = $b2 ?? "Artists";
     $coverPhoto = $this->system->PlainText([
      "Data" => "[sIMG:MiNY]",
      "Display" => 1
     ]);
     $card = base64_encode("Shop:Home");
     $x = $this->system->DatabaseSet("MBR") ?? [];
     foreach($x as $k => $v) {
      $v = str_replace("c.oh.mbr.", "", $v);
      $t = $this->system->Data("Get", ["mbr", $v]) ?? [];
      if(!empty($t["Login"]["Username"])) {
       $cms = $this->system->Data("Get", [
        "cms",
        md5($t["Login"]["Username"])
       ]) ?? [];
       $cms = $cms["Contacts"] ?? [];
       $g = $this->system->Data("Get", [
        "shop",
        md5($t["Login"]["Username"])
       ]) ?? [];
       /*$shop = $this->system->Data("Get", [
        "shop",
        md5($t["Login"]["Username"])
       ]) ?? [];*/
       $bl = $this->system->CheckBlocked([
        $t, "Members", $y["Login"]["Username"]
       ]);
       $ck = $this->system->CheckPrivacy([
        "Contacts" => $cms,
        "Privacy" => $t["Privacy"]["Shop"],
        "UN" => $t["Login"]["Username"],
        "Y" => $y["Login"]["Username"]
       ]);
       $ck2 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
       $ck3 = $g["Open"] ?? 0;
       $ck = ($ck == 1 && $ck2 == 1 && $ck4 == 1) ? 1 : 0;
       $illegal = $g["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       if($t["Login"]["Username"] == $y["Login"]["Username"] || ($bl == 0 && $ck == 1) && $illegal == 0) {
        $bl = $this->system->CheckBlocked([$y, "Shops", md5($t["Login"]["Username"])]);
        $coverPhoto = $g["ICO"] ?? $coverPhoto;
        $coverPhoto = base64_encode($coverPhoto);
        $tun = base64_encode($t["Login"]["Username"]);
        array_push($msg, [
         "[X.LI.CoverPhoto]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
         "[X.LI.Description]" => base64_encode($g["Description"]),
         "[X.LI.Lobby]" => base64_encode(base64_encode("v=$card&CARD=1&UN=$tun")),
         "[X.LI.ProfilePicture]" => base64_encode($this->system->ProfilePicture($t, "margin:5%;width:90%")),
         "[X.LI.Title]" => base64_encode($g["Title"])
        ]);
       }
      }
     }
    }
   } elseif($st == "SHOP-Orders") {
    $ec = "Accepted";
    $tpl = $this->system->Page("504e2a25db677d0b782d977f7b36ff30");
    $x = $this->system->Data("Get", [
     "po",
     md5($y["Login"]["Username"])
    ]) ?? [];
    foreach($x as $k => $v) {
     $c = base64_encode("Shop:CompleteOrder");
     $c = ($v["Complete"] == 0) ? $this->system->Element(["button", "Mark as Complete", [
      "class" => "BB BBB CompleteOrder v2 v2w",
      "data-u" => base64_encode("v=$c&ID=".base64_encode($k))
     ]]) : "";
     $t = $this->system->Member($v["Login"]["Username"]);
     $t = $this->system->ProfilePicture($t, "margin:5%;width:90%");
     array_push($msg, [
      "[X.LI.Order.Complete]" => base64_encode($c),
      "[X.LI.Order.Instructions]" => $v["Instructions"],
      "[X.LI.Order.ProductID]" => base64_encode($v["ProductID"]),
      "[X.LI.Order.ProfilePicture]" => base64_encode($t),
      "[X.LI.Order.Quantity]" => base64_encode($v["QTY"]),
      "[X.LI.Order.UN]" => base64_encode($v["Login"]["Username"])
     ]);
    }
   } elseif($st == "SHOP-Products") {
    $ec = "Accepted";
    $home = base64_encode("Product:Home");
    $coverPhoto = $this->system->PlainText([
     "Data" => "[sIMG:MiNY]",
     "Display" => 1
    ]);
    $tpl = $this->system->Page("ed27ee7ba73f34ead6be92293b99f844");
    $members = $this->system->DatabaseSet("MBR") ?? [];
    foreach($members as $key => $value) {
     $v = $this->system->Data("Get", [
      "mbr",
      str_replace("c.oh.mbr.", "", $value)
     ]) ?? [];
     if($notAnon == 1) {
      $shop = $this->system->Data("Get", [
       "shop",
       md5($v["Login"]["Username"])
      ]) ?? [];
      $b2 = $b2 ?? "Products";
      $products = $shop["Products"] ?? [];
      foreach($products as $mbr => $p) {
       $p = $this->system->Data("Get", ["miny", $p]) ?? [];
       $bl = $this->system->CheckBlocked([$y, "Products", $p["ID"]]);
       $une = base64_encode($v["Login"]["Username"]);
       $ck = ($p["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->system->core["minAge"])) ? 1 : 0;
       $ck2 = (strtotime($this->system->timestamp) < $p["Expires"]) ? 1 : 0;
       $ck3 = $v["Subscriptions"]["Artist"]["A"] ?? 0;
       $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
       $ck = ($ck == 1 || $v["Login"]["Username"] == $this->system->ShopID) ? 1 : 0;
       $illegal = $p["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       $illegal = ($v["Login"]["Username"] != $this->system->ShopID) ? 1 : 0;
       if($bl == 0 && $ck == 1 && $illegal == 0) {
        $coverPhoto = $p["ICO"] ?? $coverPhoto;
        $coverPhoto = base64_encode($coverPhoto);
        $pub = $data["pubP"] ?? 0;
        array_push($msg, [
         "[X.LI.I]" => base64_encode($this->system->CoverPhoto($coverPhoto)),
         "[X.LI.T]" => base64_encode($p["Title"]),
         "[X.LI.D]" => base64_encode($this->system->PlainText([
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
   } elseif($st == "US-SU") {
    $ec = "Accepted";
    $edit = base64_encode("StatusUpdate:Edit");
    $attlv = base64_encode("LiveView:InlineMossaic");
    $tpl = $this->system->Page("18bc18d5df4b3516c473b82823782657");
    $x = $this->system->DatabaseSet("SU") ?? [];
    foreach($x as $k => $v) {
     $v = str_replace("c.oh.su.", "", $v);
     $su = $this->system->Data("Get", ["su", $v]) ?? [];
     $from = $su["From"] ?? "";
     $ck = (!empty($from)) ? 1 : 0;
     $illegal = $su["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($ck == 1 && $illegal == 0) {
      $bl = $this->system->CheckBlocked([$y, "Status Updates", $v]);
      $from = $from ?? $this->system->ID;
      if($bl == 0 || $from == $you) {
       $att = (!empty($su["Attachments"])) ? $this->view($attlv, ["Data" => [
        "ID" => base64_encode(implode(";", $su["Attachments"])),
        "Type" => base64_encode("DLC")
       ]]) : "";
       $op = ($from == $y["Login"]["Username"]) ? $y : $this->system->Member($from);
       $cms = $this->system->Data("Get", [
        "cms",
        md5($op["Login"]["Username"])
       ]) ?? [];
       $ck = ($y["Personal"]["Age"] >= $this->system->core["minAge"] || $su["NSFW"] == 0) ? 1 : 0;
       $ck2 = $this->system->CheckPrivacy([
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
        $display = ($op["Login"]["Username"] == $this->system->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $edit = ($op["Login"]["Username"] == $you) ? $this->system->Element([
         "button", "Delete", [
          "class" => "InnerMargin dBO",
          "data-type" => "v=".base64_encode("Authentication:DeleteStatusUpdate")."&ID=".base64_encode($v)
         ]
        ]).$this->system->Element([
         "button", "Edit", [
          "class" => "InnerMargin dB2O",
          "data-type" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$v")
         ]
        ]) : "";
        $modified = $su["Modified"] ?? "";
        if(empty($modified)) {
         $modified = "";
        } else {
         $_Time = $this->system->TimeAgo($modified);
         $modified = " &bull; Modified ".$_Time;
         $modified = $this->system->Element(["em", $modified]);
        }
        $reactions = ($op["Login"]["Username"] != $you) ? base64_encode($this->view(base64_encode("Common:Reactions"), [
         "Data" => [
          "CRID" => $v,
          "T" => $op["Login"]["Username"],
          "Type" => 3
         ]
        ])) : base64_encode($this->system->Element([
         "div", "&nbsp;", ["class" => "Desktop66"]
        ]));
        array_push($msg, [
         "[StatusUpdate.Attachments]" => base64_encode($att),
         "[StatusUpdate.Body]" => base64_encode($this->system->PlainText([
          "BBCodes" => 1,
          "Data" => $su["Body"],
          "Display" => 1,
          "HTMLDecode" => 1
         ])),
         "[StatusUpdate.Created]" => base64_encode($this->system->TimeAgo($su["Created"])),
         "[StatusUpdate.DT]" => base64_encode(base64_encode("v=".base64_encode("StatusUpdate:Home")."&SU=".$su["ID"])),
         "[StatusUpdate.Edit]" => base64_encode($edit),
         "[StatusUpdate.ID]" => base64_encode($v),
         "[StatusUpdate.Modified]" => base64_encode($modified),
         "[StatusUpdate.OriginalPoster]" => base64_encode($display),
         "[StatusUpdate.ProfilePicture]" => base64_encode($this->system->ProfilePicture($op, "margin:5%;width:90%")),
         "[StatusUpdate.Reactions]" => $reactions
        ]);
       }
      }
     }
    }
   } elseif($st == "XFS") {
    $ec = "Accepted";
    $tpl = $this->system->Page("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $username = base64_decode($data["UN"]);
    if($this->system->ID == $username) {
     $files = $this->system->Data("Get", ["x", "fs"]) ?? [];
    } else {
     $files = $this->system->Data("Get", ["fs", md5($username)]) ?? [];
     $files = $files["Files"] ?? [];
    } foreach($files as $k => $v) {
     $bl = $this->system->CheckBlocked([$y, "Files", $v["ID"]]);
     $illegal = $v["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $source = $this->system->GetSourceFromExtension([
      $username,
      $v
     ]);
     $view = "v=".base64_encode("File:Home")."&AddTo=".$data["AddTo"]."&Added=".$data["Added"]."&ID=".$v["ID"]."&UN=$username&back=1&b2=Files&lPG=$st";
     $dlc = [
      "[X.LI.DT]" => base64_encode("$st;".base64_encode($view)),
      "[X.LI.Style]" => base64_encode("$source"),
      "[X.LI.Title]" => base64_encode($v["Title"]),
      "[X.LI.Type]" => base64_encode("Desktop33")
     ];
     if($bl == 0 && $illegal == 0) {
      if(!isset($data["ftype"]) && $bl == 0) {
       array_push($msg, $dlc);
      } else {
       $xf = json_decode(base64_decode($data["ftype"]));
       foreach($xf as $xf) {
        if($this->system->CheckFileType([$v["EXT"], $xf]) == 1 && $bl == 0) {
         array_push($msg, $dlc);
        }
       }
      }
      $i++;
     }
    }
   }
   return $this->system->JSONResponse([
    $ec,
    base64_encode($this->system->JSONResponse($msg)),
    base64_encode($tpl),
    base64_encode($this->system->Element([
     "h3", $na, ["class" => "CenterText InnerMargin UpperCase"]
    ]))
   ]);
  }
  function ReSearch(array $a) {
   $data = $a["Data"] ?? [];
   $pub = $data["pub"] ?? 0;
   $goHome = ($pub == 1) ? $this->system->Element(["button", "Go Home", [
    "class" => "BB BBB v2",
    "onclick" => "W('".$this->system->base."', '_top');"
   ]]) : "";
   $q = $data["query"] ?? [];
   $q = (!empty($q)) ? base64_decode(htmlentities($q)) : "";
   $sq = base64_encode("%$q%");
   $sl = $this->lists;
   $r = $this->system->Change([[
    "[ReSearch.GoHome]" => $goHome
   ], $this->system->Page("df4f7bc99b9355c34b571946e76b8481")]);
   if(!empty($q)) {
    $r = $this->system->Change([[
     "[ReSearch.Query]" => $q,
     "[ReSearch.RS-Blogs]" => base64_encode("v=$sl&pub=1&query=$sq&st=BLG"),
     "[ReSearch.RS-Members]" => base64_encode("v=$sl&query=$sq&st=MBR"),
     "[ReSearch.RS-StatusUpdates]" => base64_encode("v=$sl&query=$sq&st=US-SU")
    ], $this->system->Page("bae5cdfa85bf2c690cbff302ba193b0b")]);
   }
   $r = ($pub == 1) ? $this->system->Change([[
    "[OH.MainContent]" => $r,
    "[OH.TopBar.Search]" => base64_encode("v=".base64_encode("Search:ReSearch")."&q=")
   ], $this->system->Page("937560239a386533aecf5017371f4d34")]) : $r;
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 Class Search extends OH {
  function __construct() {
   parent::__construct();
   $this->illegal = $this->core->config["App"]["Illegal"] ?? 777;
   $this->lists = base64_encode("Search:Lists");
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
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
     $h = "App Extensions";
     $lis = "Search Extensions";
     $lo =  ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Extension:Edit")."&New=1")
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
     $extension = "e58b4fc5070b14c01c88c28050547285";
     $username = $data["Username"] ?? $you;
     $shopID = md5($username);
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     $li .= "&ID=$shopID&Username=".base64_encode($username);
     $lis = "Search ".$shop["Title"];
    } elseif($st == "Chat") {
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
     $h = "Group Chats";
     $integrated = $data["Integrated"] ?? 0;
     $li .= "&Integrated=$integrated";
     $lis = "Search $h";
    } elseif($st == "Congress") {
     $chamber = $data["Chamber"] ?? "";
     $extension = "8568ac7727dae51ee4d96334fa891395";
     $h = "Content Moderation";
     $li .= "&Chamber=$chamber";
     $lis = "Search Content";
    } elseif($st == "CongressionalBallot") {
     $chamber = $data["Chamber"] ?? "";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
     $h = "Congressional Ballot";
     $li .= "&Chamber=$chamber";
     $lis = "Search Candidates";
    } elseif($st == "CongressionalStaffHouse" || $st == "CongressionalStaffSenate") {
     $chamber = $data["Chamber"] ?? "";
     $extension = "e58b4fc5070b14c01c88c28050547285";
     $li .= "&Chamber=$chamber";
     $lis = "Search  $chamber Staff";
    } elseif($st == "Contacts") {
     $h = "Contact Manager";
     $lis = "Search Contacts";
    } elseif($st == "ContactsProfileList") {
     $data = $this->core->FixMissing($data, ["UN"]);
     $username = base64_decode($data["UN"]);
     $ck = ($username == $y["Login"]["Username"]) ? 1 : 0;
     $t = ($ck == 1) ? $y : $this->core->Member($username);
     $h = ($ck == 1) ? "Your Contacts" : $t["Personal"]["DisplayName"]."'s Contacts";
     $li .= "&b2=$b2&lPG=$lpg&UN=".$data["UN"];
     $lis = "Search Contacts";
    } elseif($st == "ContactsRequests") {
     $h = "Contact Requests";
     $lis = "Search Contact Requests";
    } elseif($st == "Contributors") {
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
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
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "Forums-Posts") {
     $id = $data["ID"] ?? "";
     $id = base64_decode($id);
     $f = $this->core->Data("Get", ["pf", $id]) ?? [];
     $h = "Forum Posts";
     $li .= "&ID=$id";
     $lis = "Search Posts from ".$f["Title"];
    } elseif($st == "Knowledge") {
     $extension = "8568ac7727dae51ee4d96334fa891395";
     $h = "Knowledge Base";
     $lis = "Search Q&As";
    } elseif($st == "Links") {
     $h = "Links";
     $lis = "Search Links";
     $lo = $this->core->Element(["button", "+", [
      "class" => "OpenFirSTEPTool v2",
      "data-fst" => base64_encode("v=".base64_encode("Search:Links"))
     ]]);
    } elseif($st == "Mainstream") {
     $extension = "f2513ac8d0389416b680c75ed5667774";
     $h = "The ".$st;
     $lis = "Search the Mainstream";
     $lo = $this->core->Element(["button", "Say Something", [
      "class" => "BBB MobileFull OpenCard v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($you))
     ]]);
    } elseif($st == "MBR") {
     $h = "Members";
     $lis = "Search Members";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "MBR-ALB") {
     $ae = base64_encode("Album:Edit");
     $username = base64_decode($data["UN"]);
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Albums" : $t["Personal"]["DisplayName"]."'s Albums";
     $b2 = $b2 ?? $h;
     $b2 = urlencode($b2);
     $li .= "&UN=".base64_encode($t["Login"]["Username"])."&b2=$b2&lPG=$lpg";
     $lis = "Search Albums";
    } elseif($st == "MBR-BLG") {
     $bd = base64_encode("Authentication:DeleteBlogs");
     $be = base64_encode("Blog:Edit");
     $h = "Your Blogs";
     $li .= "&b2=Blogs&lPG=$lpg";
     $lis = "Search your Blogs";
    } elseif($st == "MBR-CA") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Contributions" : $t["Personal"]["DisplayName"]."'s Contributions";
     $li .= "&b2=$b2&lPG=$lpg&UN=".$data["UN"];
     $lis = "Search the Archive";
    } elseif($st == "MBR-Chat" || $st == "MBR-GroupChat") {
     $group = $data["Group"] ?? 0;
     $integrated = $data["Integrated"] ?? 0;
     $oneOnOne = $data["1on1"] ?? 0;
     $h = "1:1 Chat";
     $h = ($group == 1) ? "Group Chat" : $h;
     $li .= "&1on1=$oneOnOne&Group=$group&Integrated=$integrated";
     $lis = "Search $h";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "MBR-Forums") {
     $h = "Your Forums";
     $li .= "&lPG=$lpg";
     $lis = "Search Your Private and Public Forums";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "MBR-JE") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Journal" : $t["Personal"]["DisplayName"]."'s Journal";
     $li .= "&b2=$b2&lPG=$lpg";
     $lis = "Search Entries";
    } elseif($st == "MBR-LLP") {
     $h = "Your Articles";
     $li .= "&b2=$b2&lPG=$lpg";
     $lis = "Search Articles";
    } elseif($st == "MBR-Polls") {
     $h = "Your Polls";
     $lis = "Search Polls";
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
     foreach($fs["Files"] as $key => $info) {
      $xfsUsage = $xfsUsage + $info["Size"];
     }
     $xfsUsage = $this->core->ByteNotation($xfsUsage)."MB";
     $limit = $this->core->Change([["MB" => "", "," => ""], $xfsLimit]);
     $usage = $this->core->Change([["MB" => "", "," => ""], $xfsUsage]);
     $username = $data["UN"] ?? base64_encode($you);
     $username = base64_decode($username);
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $fs = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]) ?? [];
     $alb = $fs["Albums"][$aid] ?? [];
     $ck = $y["Subscriptions"]["XFS"]["A"] ?? 0;
     $ck = ($ck == 1 && $notAnon == 1) ? 1 : 0;
     $ck2 = ($username == $this->core->ID && $y["Rank"] == md5("High Command")) ? 1 : 0;
     $de = $alb["Description"] ?? "";
     $display = ($ck2 == 1) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $h = $alb["Title"] ?? "Unsorted";
     $h = ($ck2 == 1) ? "System Media Library" : $h;
     $li .= "&AID=$aid&UN=".$data["UN"]."&lPG=$lpg";
     $lis = "Search $h";
     $usernamelimitedFiles = ($ck == 1) ? "You have unlimited storage." : "You used $xfsUsage out of $xfsLimit.";
     $usernamelimitedFiles = ($ck2 == 1) ? "No Upload Limit" : $usernamelimitedFiles;
     $ck = ($ck == 1 || $usage < $limit) ? 1 : 0;
     if(($ck == 1 && $username == $you) || $ck2 == 1) {
      $lo = $this->core->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display,
       "[Album.Uploader]" => base64_encode("v=".base64_encode("File:Upload")."&AID=$aid&UN=".$t["Login"]["Username"]),
       "[Album.FStats]" => $usernamelimitedFiles
      ], $this->core->Extension("b9e1459dc1c687cebdaa9aade72c50a9")]);
     } else {
      $lo = $this->core->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display
      ], $this->core->Extension("af26c6866abb335fb69327ed3963a182")]);
     }
     $extension = "46ef1d0890a2a5639f67bfda1634ca82";
    } elseif($st == "Media") {
     $h = "Media";
     $lis = "Search Files";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "Polls") {
     $h = "Polls";
     $lis = "Search Polls";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($st == "PR") {
     $h = "Press Releases";
     $li .= "&b2=".urlencode("Press Releases")."&lPG=$lpg";
     $lis = "Search Articles";
    } elseif($st == "Products") {
     $h = "Products";
     $li .= "&lPG=$lpg&st=$st";
     $lis = "Search Products";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
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
     $li .= "&UN=$username&b2=$b2&lPG=$lpg&pub=$pub&st=$st";
     $lis = "Search $b2";
     $t = base64_decode($data["UN"]);
     $t = $this->core->Member($t);
     $isArtist = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $shopID = md5($t["Login"]["Username"]);
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
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
    ], $this->core->Extension($extension)]);
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
  function Links(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $add = $data["Add"] ?? "";
   $preview = $data["Preview"] ?? "";
   $responseType = "View";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $r = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($add)) {
    $data = $this->core->DecodeBridgeData($data);
    $add = $data["Add"] ?? 0;
    $link = $data["Link"] ?? "";
    if(!empty($link) && $add == 1) {
     $check = (filter_var($link, FILTER_VALIDATE_URL) !== false) ? 1 : 0;
     $check2 = (strpos($link, "http") !== false) ? 1 : 0;
     $check3 = (strpos($link, "https") !== false) ? 1 : 0;
     $r = [
      "Body" => "An invalid URL was supplied."
     ];
     if($check == 1 && ($check2 == 1 || $check3 == 1)) {
      $curl = curl_init($link);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
      $linkData = curl_exec($curl);
      curl_close($curl);
      $r = [
       "Body" => "No data was found."
      ];
      if(!empty($linkData)) {
       $accessCode = "Accepted";
       $dom = new DOMDocument();
       libxml_use_internal_errors(true);
       $dom->loadHTML($linkData);
       libxml_use_internal_errors(false);
       $icon = parse_url($link, PHP_URL_SCHEME)."://".parse_url($link, PHP_URL_HOST); 
       $icon = trim($icon, "/");
       $icon = "$icon/apple-touch-icon.png";
       $iconExists = ($this->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
       $tags = get_meta_tags($link) ?? [];
       $description = $tags["description"] ?? "No Description";
       $keywords = $tags["keywords"] ?? "No Keywords";
       $responseType = "ReplaceContent";
       $title = $dom->getElementsByTagName("title")->item(0)->nodeValue ?? "No Title";
       $links = $this->core->Data("Get", ["app", md5("Links")]) ?? [];
       $links[$link] = [
        "Description" => $description,
        "Keywords" => $keywords,
        "IconExsists" => $iconExists,
        "Title" => $title
       ];
       $this->core->Data("Save", ["app", md5("Links"), $links]);
       $r = $this->core->Element([
        "h1", "Done", ["class" => "CenterText"]
       ]).$this->core->Element([
        "p", "Your Link <em>$link</em> is now listed!", ["class" => "CenterText"]
       ]);
      }
     }
    }
   } elseif($preview == 1) {
    $accessCode = "Accepted";
    $link = $data["Link"] ?? base64_encode("");
    $link = base64_decode($link);
    $r = $this->core->Extension("e057199ee0c4a5f556a30cb990521485");
    if(!empty($link)) {
     $check = (filter_var($link, FILTER_VALIDATE_URL) !== false) ? 1 : 0;
     $check2 = (strpos($link, "http") !== false) ? 1 : 0;
     $check3 = (strpos($link, "https") !== false) ? 1 : 0;
     if($check == 1 && ($check2 == 1 || $check3 == 1)) {
      $curl = curl_init($link);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
      $linkData = curl_exec($curl);
      curl_close($curl);
      if(!empty($linkData)) {
       $dom = new DOMDocument();
       libxml_use_internal_errors(true);
       $dom->loadHTML($linkData);
       libxml_use_internal_errors(false);
       $icon = parse_url($link, PHP_URL_SCHEME)."://".parse_url($link, PHP_URL_HOST); 
       $icon = trim($icon, "/");
       $icon = "$icon/apple-touch-icon.png";
       $iconExists = ($this->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
       $icon = ($iconExists == 0) ? $this->core->base."/apple-touch-icon.png" : $icon;
       $tags = get_meta_tags($link) ?? [];
       $description = $tags["description"] ?? "No Description";
       $keywords = $tags["keywords"] ?? "No Keywords";
       $title = $dom->getElementsByTagName("title")->item(0)->nodeValue ?? "No Title";
       $r = $this->core->Change([[
        "[Link.Description]" => $description,
        "[Link.Keywords]" => $keywords,
        "[Link.Icon]" => $this->core->Element([
         "div", "<img src=\"$icon\" style=\"max-width:24em\" width=\"90%\"/>\r\n", [
          "class" => "InnerMargin"
         ]
        ]),
        "[Link.Title]" => $title
       ], $this->core->Extension("aacfffd7976e2702d91a5c7084471ebc")]);
       $r .= $this->core->Element(["button", "Save", [
        "class" => "SendData v2 v2w",
        "data-form" => ".AddLink",
        "data-processor" => base64_encode("v=".base64_encode("Search:Links"))
       ]]);
      }
     }
    }
   } else {
    $accessCode = "Accepted";
    $r = $this->core->Change([[
     "[Link.Preview]" => base64_encode("v=".base64_encode("Search:Links")."&Preview=1")
    ], $this->core->Extension("f5b2784b0bcc291432a3d2dafa33849a")]);
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => $responseType
   ]);
  }
  function Lists(array $a) {
   $base = $this->core->base;
   $data = $a["Data"] ?? [];
   $b2 = $data["b2"] ?? "Search";
   $i = 0;
   $msg = [];
   $na = "No Results";
   $st = $data["st"] ?? "";
   $lpg = $data["lPG"] ?? $st;
   $query = $data["query"] ?? base64_encode("");
   $query = base64_decode($query);
   $na .= (!empty($data["query"])) ? " for $query" : "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   if($st == "ADM-LLP") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("da5c43f7719b17a9fab1797887c5c0d1");
    if($notAnon == 1 && $y["Rank"] == md5("High Command")) {
     $extensions = $this->core->DatabaseSet("Extensions");
     foreach($extensions as $key => $value) {
      $value = str_replace("nyc.outerhaven.extension.", "", $value);
      $info = $this->core->Data("Get", ["extension", $value]) ?? [];
      array_push($msg, [
       "[Extension.Category]" => base64_encode($info["Category"]),
       "[Extension.Delete]" => base64_encode(base64_encode("v=".base64_encode("Authentication:DeleteExtension")."&ID=$value")),
       "[Extension.Description]" => base64_encode($info["Description"]),
       "[Extension.Edit]" => base64_encode(base64_encode("v=".base64_encode("Extension:Edit")."&ID=".base64_encode($value))),
       "[Extension.ID]" => base64_encode($value),
       "[Extension.Title]" => base64_encode($info["Title"])
      ]);
     }
    }
   } elseif($st == "ADM-MassMail") {
    $accessCode = "Accepted";
    $preSets = $this->core->Data("Get", ["app", md5("MassMail")]) ?? [];
    $extension = $this->core->Extension("3536f06229e7b9d9684f8ca1bb08a968");
    if($notAnon == 1 && $y["Rank"] == md5("High Command")) {
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
    $accessCode = "Accepted";
    $blog = $this->core->Data("Get", ["blg", base64_decode($data["ID"])]) ?? [];
    $owner = ($blog["UN"] == $you) ? $y : $this->core->Member($blog["UN"]);
    $extension = $this->core->Extension("dba88e1a123132be03b9a2e13995306d");
    if($notAnon == 1) {
     $_IsBlogger = $owner["Subscriptions"]["Blogger"]["A"] ?? 0;
     $title = $blog["Title"];
     $title = urlencode($title);
     $posts = $blog["Posts"] ?? [];
     foreach($posts as $key => $value) {
      $bl = $this->core->CheckBlocked([$y, "Blog Posts", $value]);
      $_BlogPost = $this->core->GetContentData([
       "BackTo" => $title,
       "Blacklisted" => $bl,
       "ID" => base64_encode("BlogPost;".$blog["ID"].";$value")
      ]);
      if($_BlogPost["Empty"] == 0) {
       $options = $_BlogPost["ListItem"]["Options"];
       $post = $_BlogPost["DataModel"];
       $actions = ($post["UN"] != $you) ? $this->core->Element([
        "button", "Block", [
         "class" => "BLK InnerMargin",
         "data-cmd" => base64_encode("B"),
         "data-u" => $options["Block"]
        ]
       ]) : "";
       $actions = ($this->core->ID != $you) ? $actions : "";
       $admin = ($blog["UN"] == $you || $post["UN"] == $you) ? 1 : 0;
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
       if($admin == 1 || ($bl == 0 && $ck == 1 && $ck2 == 1 && $illegal == 0)) {
        if($admin == 1) {
         $actions .= $this->core->Element(["button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-view" => $options["Delete"]
         ]]);
         $actions .= ($_IsBlogger == 1) ? $this->core->Element(["button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-view" => $options["Edit"]
         ]]) : "";
        }
        $contributors = $post["Contributors"] ?? $blog["Contributors"];
        $coverPhoto = (!empty($post["ICO"])) ? base64_encode($post["ICO"]) : $coverPhoto;
        $op = ($post["UN"] == $you) ? $y : $this->core->Member($post["UN"]);
        $display = ($post["UN"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $memberRole = ($blog["UN"] == $post["UN"]) ? "Owner" : $contributors[$author];
        array_push($msg, [
         "[BlogPost.Actions]" => base64_encode($actions),
         "[BlogPost.Attachments]" => base64_encode($_BlogPost["ListItem"]["Attachments"]),
         "[BlogPost.Author]" => base64_encode($display),
         "[BlogPost.Description]" => base64_encode($_BlogPost["ListItem"]["Description"]),
         "[BlogPost.Created]" => base64_encode($this->core->TimeAgo($post["Created"])),
         "[BlogPost.ID]" => base64_encode($post["ID"]),
         "[BlogPost.MemberRole]" => base64_encode($memberRole),
         "[BlogPost.Modified]" => base64_encode($_BlogPost["ListItem"]["Modified"]),
         "[BlogPost.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[BlogPost.Title]" => base64_encode($_BlogPost["ListItem"]["Title"]),
         "[BlogPost.View]" => base64_encode("Blog".$blog["ID"].";".$options["View"])
        ]);
       }
      }
     }
    }
   } elseif($st == "BL") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e05bae15ffea315dc49405d6c93f9b2c");
    if($notAnon == 1) {
     $bl = base64_decode($data["BL"]);
     $blacklist = $y["Blocked"][$bl] ?? [];
     foreach($blacklist as $key => $value) {
      $usernameblock = base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode("Unblock")."&Content=".base64_encode($value)."&List=".base64_encode($bl));
      if($bl == "Albums") {
       $alb = explode("-", base64_decode($value));
       $t = ($alb[0] != $you) ? $this->core->Member($alb[0]) : $y;
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
       $bg = $this->core->Data("Get", ["blg", $value]) ?? [];
       $de = $bg["Description"];
       $h = "<em>".$bg["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Blog Posts") {
       $bp = $this->core->Data("Get", ["bp", $value]) ?? [];
       $de = $bp["Description"];
       $h = "<em>".$bp["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
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
       $forum = $this->core->Data("Get", ["pf", $value]) ?? [];
       $de = $forum["Description"];
       $h = "<em>".$forum["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Forum Posts") {
       $post = $this->core->Data("Get", ["post", $value]) ?? [];
       $de = $post["Description"];
       $h = "<em>".$post["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Links") {
       $links = $this->core->Data("Get", ["app", md5("Links")]) ?? [];
       $de = $links[$value]["Description"] ?? "";
       $title = $links[$value]["Title"] ?? "Link";
       $vi = $this->core->Element(["button", "Visit <em>$title</em>", [
        "class" => "v2 v2w",
        "onclick" => "W('$value', '_blank');"
       ]]);
      } elseif($bl == "Members") {
       $member = $this->core->Data("Get", ["mbr", $value]) ?? [];
       $de = $member["Description"];
       $h = "<em>".$member["Personal"]["DisplayName"]."</em>";
       $vi = $this->core->Element(["button", "View $h's Profile", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Pages") {
       $page = $this->core->Data("Get", ["pg", $value]) ?? [];
       $de = $page["Description"];
       $h = "<em>".$page["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Products") {
       $product = $this->core->Data("Get", ["product", $value]) ?? [];
       $de = $product["Description"];
       $h = "<em>".$product["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Shops") {
       $shop = $this->core->Data("Get", ["shop", $value]) ?? [];
       $de = $shop["Description"];
       $h = "<em>".$shop["Title"]."</em>";
       $vi = $this->core->Element(["button", "View $h", [
        "class" => "BB v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Status Updates") {
       $update = $this->core->Data("Get", ["su", $value]) ?? [];
       $de = $this->core->Excerpt(base64_decode($update["Body"]), 180);
       $h = $update["From"];
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
       "[X.LI.Unblock.Proc]" => base64_encode(base64_encode($usernameblock)),
       "[X.LI.View]" => base64_encode($vi)
      ]);
     }
    }
   } elseif($st == "BLG") {
    $blogs = $this->core->RenderSearchIndex("Blog");
    $accessCode = "Accepted";
    $home = base64_encode("Blog:Home");
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    foreach($blogs as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Blogs", $value]);
     $_Blog = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Blog;$value")
     ]);
     if($_Blog["Empty"] == 0) {
      $blog = $_Blog["DataModel"];
      $cms = $this->core->Data("Get", ["cms", md5($blog["UN"])]);
      $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $blog["NSFW"] == 0) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $blog["Privacy"],
       "UN" => $blog["UN"],
       "Y" => $you
      ]);
      $illegal = $blog["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $ck2 == 1 && $illegal == 0) {
       $options = $_Blog["ListItem"]["Options"];
       array_push($msg, [
        "[X.LI.I]" => base64_encode($_Blog["ListItem"]["CoverPhoto"]),
        "[X.LI.T]" => base64_encode($_Blog["ListItem"]["Title"]),
        "[X.LI.D]" => base64_encode($_Blog["ListItem"]["Description"]),
        "[X.LI.DT]" => base64_encode($options["View"])
       ]);
      }
     }
    }
   } elseif($st == "Bulletins") {
    $bulletins = $this->core->Data("Get", ["bulletins", md5($you)]) ?? [];
    $accessCode = "Accepted";
    $extension = $this->core->Extension("ae30582e627bc060926cfacf206920ce");
    foreach($bulletins as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Members", md5($value["From"])]);;
     $_Member = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Member;".md5($value["From"]))
     ]);
     if($_Member["Empty"] == 0) {
      $member = $_Member["DataModel"];
      $value["ID"] = $key;
      $message = $this->view(base64_encode("Profile:BulletinMessage"), [
       "Data" => $value
      ]);
      $options = $this->view(base64_encode("Profile:BulletinOptions"), ["Data" => [
       "Bulletin" => base64_encode(json_encode($value, true))
      ]]);
      array_push($msg, [
       "[Bulletin.Date]" => base64_encode($this->core->TimeAgo($value["Sent"])),
       "[Bulletin.From]" => base64_encode($_Member["ListItem"]["Title"]),
       "[Bulletin.ID]" => base64_encode($key),
       "[Bulletin.Message]" => base64_encode($this->core->RenderView($message)),
       "[Bulletin.Options]" => base64_encode($this->core->RenderView($options)),
       "[Bulletin.Picture]" => base64_encode($_Member["ListItem"]["Options"]["ProfilePicture"])
      ]);
     }
    }
   } elseif($st == "CA" || $st == "PR") {
    $accessCode = "Accepted";
    $articles = $this->core->RenderSearchIndex("Article");
    $extension = $this->core->Extension("e7829132e382ee4ab843f23685a123cf");
    foreach($articles as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Pages", $value]);
     $_Article = $this->core->GetContentData([
      "BackTo" => $b2,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Page;$value"),
      "ParentPage" => $lpg
     ]);
     if($_Article["Empty"] == 0) {
      $article = $_Article["DataModel"];
      $i++;
      $nsfw = $article["NSFW"] ?? 0;
      $t = ($article["UN"] == $you) ? $y : $this->core->Member($article["UN"]);
      $cat = $article["Category"] ?? "";
      $cms = $this->core->Data("Get", ["cms", md5($article["UN"])]) ?? [];
      $ck = ($article["Category"] == $st) ? 1 : 0;
      $ck2 = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck3 = (($st == "CA" && $article["Category"] == "CA") || ($st == "PR" && $article["Category"] == "PR")) ? 1 : 0;
      $ck4 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $article["Privacy"],
       "UN" => $article["UN"],
       "Y" => $you
      ]);
      $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1 && $ck4 == 1) ? 1 : 0;
      $illegal = $article["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $illegal == 0) {
       array_push($msg, [
        "[X.LI.I]" => base64_encode($_Article["ListItem"]["CoverPhoto"]),
        "[X.LI.T]" => base64_encode($_Article["ListItem"]["Title"]),
        "[X.LI.D]" => base64_encode($_Article["ListItem"]["Description"]),
        "[X.LI.DT]" => base64_encode("$lpg;".$_Article["ListItem"]["Options"]["View"])
       ]);
      }
     }
     #$na.=" ".$query.json_encode($extensions, true);//TEMP
    }
   } elseif($st == "CART") {
    $accessCode = "Accepted";
    $newCartList = [];
    $now = $this->core->timestamp;
    $extension = $this->core->Extension("dea3da71b28244bf7cf84e276d5d1cba");
    $shop = $data["ID"] ?? md5($this->core->ShopID);
    $username = $data["Username"] ?? base64_encode($this->core->ShopID);
    $products = $y["Shopping"]["Cart"][$shop] ?? [];
    $products = $products["Products"] ?? [];
    foreach($products as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Products", $value]);;
     $_Product = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Product;$key"),
      "Owner" => $username
     ]);
     if($_Product["Empty"] == 0) {
      $product = $_Product["DataModel"];
      $isActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $quantity = $product["Quantity"] ?? 0;
      if(!empty($product) && $isActive == 1 && $quantity != 0 && $illegal == 0) {
       $newCartList[$key] = $value;
       array_push($msg, [
        "[Product.CoverPhoto]" => base64_encode($_Product["ListItem"]["CoverPhoto"]),
        "[Product.Description]" => base64_encode($_Product["ListItem"]["Description"]),
        "[Product.Title]" => base64_encode($_Product["ListItem"]["Title"]),
        "[Product.Remove]" => base64_encode(base64_encode("v=".base64_encode("Cart:SaveRemove")."&Product=$key&Shop=$shop"))
       ]);
      }
     }
    }
    $y["Shopping"]["Cart"][$shop]["Products"] = $newCartList;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
   } elseif($st == "Chat") {
    $accessCode = "Accepted";
    $integrated = $data["Integrated"] ?? 0;
    $extension = $this->core->Extension("343f78d13872e3b4e2ac0ba587ff2910");
    if($notAnon == 1) {
     $extension = "343f78d13872e3b4e2ac0ba587ff2910";
     $extension = ($integrated == 0) ? "183d39e5527b3af3e7652181a0e36e25" : $extension;
     $extension = $this->core->Extension($extension);
     $groups = $this->core->RenderSearchIndex("Chat");
     foreach($groups as $key => $group) {
      $bl = $this->core->CheckBlocked([$y, "Group Chats", $group["ID"]]);
      $_Chat = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Chat;".$group["ID"]),
       "Integrated" => $integrated
      ]);
      if($_Chat["Empty"] == 0) {
       $active = 0;
       $chat = $_Chat["DataModel"];
       $contributors = $chat["Contributors"] ?? [];
       foreach($contributors as $member => $role) {
        if($member == $you) {
         $active++;
        }
       }
       $nsfw = $chat["NSFW"] ?? 0;
       $nsfw = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
       $privacy = $chat["Privacy"] ?? 0;
       $privacy = ($active == 1 || $privacy != md5("Private")) ? 1 : 0;
       if($chat["UN"] == $you || ($bl == 0 && $nsfw == 1 && $privacy == 1)) {
        $contributors = $chat["Contributors"] ?? [];
        $isGroupChat = $chat["Group"] ?? 0;
        if(!empty($contributors) || $isGroupChat == 1) {
         $displayName = $chat["Title"] ?? "Group Chat";
         $t = $this->core->Member($this->core->ID);
         array_push($msg, [
          "[Chat.DisplayName]" => base64_encode($displayName),
          "[Chat.Online]" => base64_encode(""),
          "[Chat.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%")),
          "[Chat.View]" => base64_encode($_Chat["ListItem"]["Options"]["View"])
         ]);
        }
       }
      }
     }
    }
   } elseif($st == "Congress") {
    $accessCode = "Accepted";
    $chamber = $data["Chamber"] ?? "";
    $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
    $congressmen = $congress["Members"] ?? [];
    $exclude = [
     "app",
     "blg",
     "bulletins",
     "chat",
     "cms",
     "conversation",
     "dc",
     "invoice",
     "invoice-preset",
     "local",
     "mbr",
     "pf",
     "pfmanifest",
     "po",
     "shop",
     "stream",
     "votes"
    ];
    $extension = $this->core->Extension("1f32642e05747ba3cec15d7c9fffbd0f");
    $houseRepresentatives = 0;
    $senators = 0;
    $yourRole = $congressmen[$you] ?? "";
    foreach($congressmen as $member => $role) {
     if($role == "HouseRepresentative") {
      $houseRepresentatives++;
     } elseif($role = "Senator") {
      $senators++;
     }
    } if(($chamber == "House" || $chamber == "Senate") && $notAnon == 1) {
     $content = $this->core->DatabaseSet();
     $description = "";
     $illegalThreshold = $this->core->config["App"]["Illegal"] ?? 777;
     $options = "";
     $title = "";
     foreach($content as $key => $id) {
      if(strpos($id, "nyc.outerhaven") === 0) {
       $id = explode(".", $id);
       if(!in_array($id[2], $exclude)) {
        $type = "Unspecified";
        $type = ($id[2] == "bp") ? "Blog Post" : $type;
        $type = ($id[2] == "pg") ? "Article" : $type;
        $type = ($id[2] == "post") ? "Post" : $type;
        $type = ($id[2] == "product") ? "Product" : $type;
        $type = ($id[2] == "su") ? "Status Update" : $type;
        $contentID = base64_encode("$type;".$id[2]."-".$id[3]);
        if($id[2] == "fs") {
         $files = $this->core->Data("Get", ["fs", $id[3]]) ?? [];
         $files = $files["Files"] ?? [];
         $type = "File";
         foreach($files as $file => $info) {
          $congressDeemedLegal = $info["CongressDeemedLegal"] ?? 0;
          $illegal = $info["Illegal"] ?? 0;
          if($congressDeemedLegal == 0 && $illegal >= $illegalThreshold) {
           $_Congress = $info["Congress"] ?? [];
           $_Votes = $_Congress["Votes"] ?? [];
           $_HouseVotes = 0;
           $_Illegal = 0;
           $_Legal = 0;
           $_SenateVotes = 0;
           foreach($_Votes as $member => $memberInfonfo) {
            $role = $memberInfonfo["Role"] ?? "";
            $vote = $memberInfonfo["Vote"] ?? "";
            if($role == "HouseRepresentative") {
             $_HouseVotes++;
            } elseif($role = "Senator") {
             $_SenateVotes++;
            }  if($vote == "Illegal") {
             $_Illegal++;
            } elseif($vote = "Legal") {
             $_Legal++;
            }
           }
           $contentID = base64_encode("$type;".$id[2]."-".$id[3].";$file");
           $description = $info["Description"] ?? $type;
           $houseCleared = $_Congress["HouseCleared"] ?? 0;
           $voted = ($congressmen[$you] == "HouseRepresentative") ? "$_HouseVotes out of $houseRepresentatives House Representatives" : "$_SenateVotes out of $senators Senators";
           $optionCheck = ($_HouseVotes < $houseRepresentatives) ? 1 : 0;
           $optionCheck = ($yourRole == "HouseRepresentative" && $optionCheck == 1) ? 1 : 0;
           $optionCheck2 = ($_SenateVotes < $senators) ? 1 : 0;
           $optionCheck2 = ($yourRole == "Senator" && $optionCheck2 == 1) ? 1 : 0;
           $optionCheck2 = ($_HouseVotes == $houseRepresentatives && $_Illegal > $_Legal && $optionCheck2 == 1) ? 1 : 0;
           $title = $info["Title"] ?? $type;
           if($optionCheck == 1 || $optionCheck2 == 1) {
            array_push($msg, [
             "[Content.Description]" => base64_encode($description),
             "[Content.Illegal]" => base64_encode(base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Illegal"))),
             "[Content.Legal]" => base64_encode(base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Legal"))),
             "[Content.Title]" => base64_encode($title),
             "[Content.Voted]" => base64_encode($voted)
            ]);
           }
          }
         }
        } else {
         $info = $this->core->Data("Get", [$id[2], $id[3]]) ?? [];
         $congressDeemedLegal = $info["CongressDeemedLegal"] ?? 0;
         $illegal = $info["Illegal"] ?? 0;
         if($congressDeemedLegal == 0 && $illegal >= $illegalThreshold) {
          $_Congress = $info["Congress"] ?? [];
          $_Votes = $_Congress["Votes"] ?? [];
          $_HouseVotes = 0;
          $_Illegal = 0;
          $_Legal = 0;
          $_SenateVotes = 0;
          foreach($_Votes as $member => $memberInfonfo) {
           $role = $memberInfonfo["Role"] ?? "";
           $vote = $memberInfonfo["Vote"] ?? "";
           if($role == "HouseRepresentative") {
            $_HouseVotes++;
           } elseif($role = "Senator") {
            $_SenateVotes++;
           }  if($vote == "Illegal") {
            $_Illegal++;
           } elseif($vote = "Legal") {
            $_Legal++;
           }
          }
          $description = $info["Description"] ?? $type;
          $houseCleared = $_Congress["HouseCleared"] ?? 0;
          $voted = ($congressmen[$you] == "HouseRepresentative") ? "$_HouseVotes out of $houseRepresentatives House Representatives" : "$_SenateVotes out of $senators Senators";
          $optionCheck = ($_HouseVotes < $houseRepresentatives) ? 1 : 0;
          $optionCheck = ($yourRole == "HouseRepresentative" && $optionCheck == 1) ? 1 : 0;
          $optionCheck2 = ($_SenateVotes < $senators) ? 1 : 0;
          $optionCheck2 = ($yourRole == "Senator" && $optionCheck2 == 1) ? 1 : 0;
          $optionCheck2 = ($_HouseVotes == $houseRepresentatives && $_Illegal > $_Legal && $optionCheck2 == 1) ? 1 : 0;
          $title = $info["Title"] ?? $type;
          if($optionCheck == 1 || $optionCheck2 == 1) {
           array_push($msg, [
            "[Content.Description]" => base64_encode($description),
            "[Content.Illegal]" => base64_encode(base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Illegal"))),
            "[Content.Legal]" => base64_encode(base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Legal"))),
            "[Content.Title]" => base64_encode($title),
            "[Content.Voted]" => base64_encode($voted)
           ]);
          }
         }
        }
       }
      }
     }
    }
   } elseif($st == "CongressionalBallot") {
    $accessCode = "Accepted";
    $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]) ?? [];
    $chamber = $data["Chamber"] ?? "";
    $extension = $this->core->Extension("1f32642e05747ba3cec15d7c9fffbd0f");
    $na = "No Candidates for the $chamber";
    if(($chamber == "House" || $chamber == "Senate") && $notAnon == 1) {
     // CONGRESSIONAL BALLOT
    }
   } elseif($st == "CongressionalStaffHouse" || $st == "CongressionalStaffSenate") {
    $accessCode = "Accepted";
    $congress = $this->core->Data("Get", ["app", md5("Congress")]) ?? [];
    $congress = $congress["Members"] ?? [];
    $chamber = $data["Chamber"] ?? "";
    $extension = $this->core->Element([
     "div", "[ListItem.Button]", ["class" => "Desktop25"]
    ]);
    $na = "No $chamber Staff";
    if(($chamber == "House" || $chamber == "Senate")) {
     foreach($congress as $member => $role) {
      $check = ($chamber == "House" && $role == "HouseRepresentative") ? 1 : 0;
      $check2 = ($chamber == "Senate" && $role == "Senator") ? 1 : 0;
      if($check == 1 || $check2 == 1) {
       $t = ($key == $you) ? $y : $this->core->Member($member);
       if(!empty($t["Login"])) {
        array_push($msg, [
         "[ListItem.Button]" => base64_encode($this->core->Element([
          "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
           "class" => "OpenCard Small",
           "data-view" => base64_encode("v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($t["Login"]["Username"]))
          ]
         ]).$this->core->Element([
          "h4", $t["Personal"]["DisplayName"], ["class" => "CenterText UpperCase"]
         ]))
        ]);
       }
      }
     }
    }
   } elseif($st == "Contacts") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("ccba635d8c7eca7b0b6af5b22d60eb55");
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
    $accessCode = "Accepted";
    $extension = $this->core->Extension("ba17995aafb2074a28053618fb71b912");
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
       "data-view" => base64_encode("CARD=1&v=".base64_encode("Profile:Home")."&back=1&b2=$b2&lPG=$lpg&pub=0&UN=".base64_encode($t["Login"]["Username"]))
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
    $accessCode = "Accepted";
    $extension = $this->core->Extension("8b6ac25587a4524c00b311c184f6c69b");
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
    $accessCode = "Accepted";
    $admin = 0;
    $contributors = [];
    $extension = $this->core->Extension("ba17995aafb2074a28053618fb71b912");
    $id = $data["ID"] ?? "";
    $type = $data["Type"] ?? "";
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
      $bl = $this->core->CheckBlocked([$y, "Members", $member]);;
      $_Member = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Member;".md5($member))
      ]);
      if($_Member["Empty"] == 0) {
       $member = $_Member["DataModel"];
       $options = $_Member["ListItem"]["Options"];
       $them = $member["Login"]["Username"];
       $cms = $this->core->Data("Get", ["cms", md5($them)]) ?? [];
       $ck = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $member["Privacy"]["Profile"],
        "UN" => $them,
        "Y" => $you
       ]);
       $theyBlockedYou = $this->core->CheckBlocked([$member, "Members", $you]);
       $youBlockedThem = $this->core->CheckBlocked([$y, "Members", $them]);
       if($theyBlockedYou == 0 && $youBlockedThem == 0 ) {
        if($type == "Article") {
         $ban = base64_encode("Page:Banish");
         $ck2 = ($Page["UN"] == $you || $admin == 1) ? 1 : 0;
         $ck2 = ($ck2 == 1 && $member != $you) ? 1 : 0;
         if($ck == 1 || $ck2 == 1) {
          $ck = ($Page["UN"] != $member && $Page["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($Page["ID"]);
          $mbr = base64_encode($them);
          $opt = ($ck == 1 && $ck2 == 1) ? $this->core->Element([
           "button", "Banish", [
            "class" => "OpenDialog v2",
            "data-view" => base64_encode("v=$ban&ID=$eid&Member=$mbr")
           ]
          ]).$this->core->Element([
           "button", "Change Role", [
            "class" => "OpenDialog v2",
            "data-view" => base64_encode("v=".base64_encode("Authentication:ArticleChangeMemberRole")."&ID=$eid&Member=$mbr")
           ]
          ]) : "";
         }
        } elseif($type == "Blog") {
         $ck2 = ($blog["UN"] == $you || $admin == 1) ? 1 : 0;
         $ck2 = ($ck2 == 1 && $member != $you) ? 1 : 0;
         if($ck == 1 || $ck2 == 1) {
          $ck = ($blog["UN"] != $member && $blog["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($blog["ID"]);
          $mbr = base64_encode($them);
          $opt = ($ck == 1 && $ck2 == 1) ? $this->core->Element([
           "button", "Banish", [
            "class" => "OpenDialog v2",
            "data-view" => base64_encode("v=".base64_encode("Blog:Banish")."&ID=$eid&Member=$mbr")
           ]
          ]).$this->core->Element([
           "button", "Change Role", [
            "class" => "OpenDialog v2",
            "data-view" => base64_encode("v=".base64_encode("Authentication:BlogChangeMemberRole")."&ID=$eid&Member=$mbr")
           ]
          ]) : "";
         }
        } elseif($type == "Forum") {
         $ck2 = ($forum["UN"] == $you || $admin == 1) ? 1 : 0;
         $ck2 = ($ck2 == 1 && $member != $you) ? 1 : 0;
         if($ck == 1 || $ck2 == 1) {
          $ck = ($forum["UN"] != $member && $forum["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($forum["ID"]);
          $mbr = base64_encode($them);
          $opt = ($ck == 1 && $ck2 == 1) ? $this->core->Element([
           "button", "Banish", [
            "class" => "OpenDialog v2",
            "data-view" => base64_encode("v=".base64_encode("Forum:Banish")."&ID=$eid&Member=$mbr")
           ]
          ]).$this->core->Element([
           "button", "Change Role", [
            "class" => "OpenDialog v2",
            "data-view" => base64_encode("v=".base64_encode("Authentication:PFChangeMemberRole")."&ID=$eid&Member=$mbr")
           ]
          ]) : "";
         }
        } elseif($type == "Shop") {
         $ck = ($id == md5($you) && $them != $you) ? 1 : 0;
         $description = "<b>".$role["Title"]."</b><br/>".$role["Description"];
         $eid = base64_encode($id);
         $memberID = base64_encode($them);
         $opt = ($ck == 1) ? $this->core->Element(["button", "Edit", [
          "class" => "OpenCard v2",
          "data-view" => base64_encode("v=".base64_encode("Shop:EditPartner")."&UN=$memberID")
         ]]).$this->core->Element(["button", "Fire", [
          "class" => "OpenDialog v2",
          "data-view" => base64_encode("v=".base64_encode("Shop:Banish")."&ID=$eid&UN=$memberID")
         ]]) : "";
        }
       }
       $description = ($type == "Shop") ? $description : $_Member["ListItem"]["Description"];
       array_push($msg, [
        "[X.LI.DisplayName]" => base64_encode($_Member["ListItem"]["Title"]),
        "[X.LI.Description]" => base64_encode($description),
        "[X.LI.Options]" => base64_encode($opt),
        "[X.LI.ProfilePicture]" => base64_encode($options["ProfilePicture"])
       ]);
      }
     }
    }
   } elseif($st == "CS1") {
    $accessCode = "Accepted";
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
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e9f34ca1985c166bf7aa73116a745e92");
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
    $accessCode = "Accepted";
    $now = $this->core->timestamp;
    $feedback = $this->core->RenderSearchIndex("Feedback");
    $extension = $this->core->Extension("e7c4e4ed0a59537ffd00a2b452694750");
    foreach($feedback as $key => $value) {
     $feedback = $this->core->Data("Get", ["feedback", $value]) ?? [];
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
      "[Feedback.Home]" => base64_encode(base64_encode("v=".base64_encode("Feedback:Home")."&ID=$value")),
      "[Feedback.Message]" => base64_encode($message),
      "[Feedback.Modified]" => base64_encode($modified),
      "[Feedback.Resolved]" => base64_encode($resolved),
      "[Feedback.Title]" => base64_encode($title)
     ]);
    }
   } elseif($st == "Forums") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    $forums = $this->core->RenderSearchIndex("Forum");
    foreach($forums as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Forums", $value]);
     $_Forum = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Forum;$value")
     ]);
     if($_Forum["Empty"] == 0) {
      $active = 0;
      $forum = $_Forum["DataModel"];
      $manifest = $this->core->Data("Get", ["pfmanifest", $value]) ?? [];
      $t = ($forum["UN"] == $you) ? $y : $this->core->Member($forum["UN"]);
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]);
      $ck = ($forum["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $forum["Privacy"],
       "UN" => $forum["UN"],
       "Y" => $you
      ]);
      $illegal = $forum["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      foreach($manifest as $member => $role) {
       if($active == 0 && $member == $you) {
        $active++;
       }
      } if($bl == 0 && ($active == 1 || $ck == 1 && $ck2 == 1) && $illegal == 0) {
       $options = $_Forum["ListItem"]["Options"];
       array_push($msg, [
        "[X.LI.I]" => base64_encode($_Forum["ListItem"]["CoverPhoto"]),
        "[X.LI.T]" => base64_encode($_Forum["ListItem"]["Title"]),
        "[X.LI.D]" => base64_encode($_Forum["ListItem"]["Description"]),
        "[X.LI.DT]" => base64_encode($options["View"])
       ]);
      }
     }
    }
   } elseif($st == "Forums-Admin") {
    $admin = $data["Admin"] ?? base64_encode("");
    $accessCode = "Accepted";
    $id = $data["ID"] ?? "";
    $extension = $this->core->Extension("ba17995aafb2074a28053618fb71b912");
    if(!empty($id)) {
     $admin = base64_decode($admin);
     $id = base64_decode($id);
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
     foreach($manifest as $member => $role) {
      if($member == $admin || $role == "Admin") {
       $bl = $this->core->CheckBlocked([$y, "Members", $member]);;
       $_Member = $this->core->GetContentData([
        "Blacklisted" => $bl,
        "ID" => base64_encode("Member;".md5($member))
       ]);
       if($_Member["Empty"] == 0) {
        $member = $_Member["DataModel"];
        $them = $member["Login"]["Username"];
        $contacts = $this->core->Data("Get", ["cms", md5($them)]) ?? [];
        $check = $this->core->CheckPrivacy([
         "Contacts" => $contacts["Contacts"],
         "Privacy" => $member["Privacy"]["Profile"],
         "UN" => $them,
         "Y" => $you
        ]);
        $theyBlockedYou = $this->core->CheckBlocked([$member, "Members", $you]);
        $youBlockedThem = $this->core->CheckBlocked([$y, "Members", $them]);
        if($check == 1 && $theyBlockedYou == 0 && $youBlockedThem == 0) {
         $options = $_Member["ListItem"]["Options"];
         array_push($msg, [
          "[X.LI.DisplayName]" => base64_encode($_Member["ListItem"]["Title"]),
          "[X.LI.Description]" => base64_encode($_Member["ListItem"]["Description"]),
          "[X.LI.Options]" => base64_encode(""),
          "[X.LI.ProfilePicture]" => base64_encode($options["ProfilePicture"])
         ]);
        }
       }
      }
     }
    }
   } elseif($st == "Forums-Posts") {
    $accessCode = "Accepted";
    $active = 0;
    $admin = 0;
    $id = $data["ID"] ?? "";
    $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
    $manifest = $this->core->Data("Get", ["pfmanifest", $id]) ?? [];
    foreach($manifest as $member => $role) {
     if($active == 0 && $member == $you) {
      $active = 0;
      if($admin == 0 && $role == "Admin") {
       $admin++;
      }
     }
    }
    $posts = $forum["Posts"] ?? [];
    $extension = $this->core->Extension("150dcee8ecbe0e324a47a8b5f3886edf");
    if($active == 1 || $admin == 1 || $forum["Type"] == "Public") {
     foreach($posts as $key => $value) {
      $bl = $this->core->CheckBlocked([$y, "Forum Posts", $value]);
      $_ForumPost = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("ForumPost;$id;$value")
      ]);
      if($_ForumPost["Empty"] == 0) {
       $actions = "";
       $active = 0;
       $post = $_ForumPost["DataModel"];
       $cms = $this->core->Data("Get", ["cms", md5($post["From"])]) ?? [];
       $illegal = $post["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       $op = ($forum["UN"] == $you) ? $y : $this->core->Member($post["From"]);
       $options = $_ForumPost["ListItem"]["Options"];
       $ck = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
       $ck2 = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $post["NSFW"] == 0) ? 1 : 0;
       $ck3 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $post["Privacy"],
        "UN" => $post["From"],
        "Y" => $you
       ]);
       if($bl == 0 && ($ck2 == 1 && $ck3 == 1) && $illegal == 0) {
        $bl = $this->core->CheckBlocked([$y, "Status Updates", $id]);
        $con = base64_encode("Conversation:Home");
        $actions = ($post["From"] != $you) ? $this->core->Element([
         "button", "Block", [
          "class" => "InnerMargin",
          "data-cmd" => base64_encode("B"),
          "data-u" => $options["Block"]
         ]
        ]) : "";
        $actions = ($this->core->ID != $you) ? $actions : "";
        if($ck == 1) {
         $actions .= $this->core->Element([
          "button", "Delete", [
           "class" => "InnerMargin OpenDialog",
           "data-view" => $options["Delete"]
          ]
         ]);
         $actions .= ($admin == 1 || $ck == 1) ? $this->core->Element([
          "button", "Edit", [
           "class" => "InnerMargin OpenCard",
           "data-view" => $options["Edit"]
          ]
         ]) : "";
        }
        $actions .= ($forum["Type"] == "Public") ? $this->core->Element([
         "button", "Share", [
          "class" => "InnerMargin OpenCard",
          "data-view" => $options["Share"]
         ]
        ]) : "";
        $display = ($post["From"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $memberRole = ($forum["UN"] == $post["From"]) ? "Owner" : $manifest[$op["Login"]["Username"]];
        array_push($msg, [
         "[ForumPost.Actions]" => base64_encode($actions),
         "[ForumPost.Attachments]" => base64_encode($_ForumPost["ListItem"]["Attachments"]),
         "[ForumPost.Body]" => base64_encode($_ForumPost["ListItem"]["Body"]),
         "[ForumPost.Comment]" => base64_encode($options["View"]),
         "[ForumPost.Created]" => base64_encode($this->core->TimeAgo($post["Created"])),
         "[ForumPost.ID]" => base64_encode($value),
         "[ForumPost.MemberRole]" => base64_encode($memberRole),
         "[ForumPost.Modified]" => base64_encode($_ForumPost["ListItem"]["Modified"]),
         "[ForumPost.OriginalPoster]" => base64_encode($display),
         "[ForumPost.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[ForumPost.Title]" => base64_encode($_ForumPost["ListItem"]["Title"]),
         "[ForumPost.Votes]" => base64_encode($options["Vote"])
        ]);
       }
      }
     }
    }
   } elseif($st == "Links") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("aacfffd7976e2702d91a5c7084471ebc");
    $extension = $this->core->Element(["div", $extension, ["class" => "K4i"]]);
    $links = $this->core->Data("Get", ["app", md5("Links")]) ?? [];
    foreach($links as $link => $info) {
     $icon = parse_url($link, PHP_URL_SCHEME)."://".parse_url($link, PHP_URL_HOST); 
     $icon = trim($icon, "/");
     $icon = "$icon/apple-touch-icon.png";
     $iconExists = ($this->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
     $icon = ($iconExists == 0) ? $this->core->base."/apple-touch-icon.png" : $icon;
     array_push($msg, [
      "[Link.Description]" => base64_encode($info["Description"]),
      "[Link.Keywords]" => base64_encode($info["Keywords"]),
      "[Link.Icon]" => base64_encode($this->core->Element([
       "div", "<img src=\"$icon\" style=\"max-width:24em\" width=\"90%\"/>\r\n", [
        "class" => "InnerMargin"
       ]
      ])),
      "[Link.Title]" => base64_encode($info["Title"])
     ]);
    }
   } elseif($st == "Mainstream") {
    $accessCode = "Accepted";
    $edit = base64_encode("StatusUpdate:Edit");
    $attlv = base64_encode("LiveView:InlineMossaic");
    $extension = $this->core->Extension("18bc18d5df4b3516c473b82823782657");
    $statusUpdates = $this->core->Data("Get", ["app", "mainstream"]) ?? [];
    foreach($statusUpdates as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $value]);
     $_StatusUpdate = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("StatusUpdate;$value")
     ]);
     if($_StatusUpdate["Empty"] == 0) {
      $update = $_StatusUpdate["DataModel"];
      $from = $update["From"] ?? $this->core->ID;
      $illegal = $update["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($from == $you || ($bl == 0 && $illegal == 0)) {
       $attachments = "";
       $op = ($from == $you) ? $y : $this->core->Member($from);
       $cms = $this->core->Data("Get", ["cms", md5($from)]) ?? [];
       $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $update["NSFW"] == 0) ? 1 : 0;
       $ck2 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $op["Privacy"]["Posts"],
        "UN" => $from,
        "Y" => $you
       ]);
       if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
        $display = ($from == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $options = $_StatusUpdate["ListItem"]["Options"];
        $edit = ($from == $you) ? $this->core->Element([
         "button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-view" => $options["Delete"]
         ]
        ]).$this->core->Element([
         "button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-view" => $options["Edit"]
         ]
        ]) : "";
        array_push($msg, [
         "[StatusUpdate.Attachments]" => base64_encode($_StatusUpdate["ListItem"]["Attachments"]),
         "[StatusUpdate.Body]" => base64_encode($_StatusUpdate["ListItem"]["Body"]),
         "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($update["Created"])),
         "[StatusUpdate.DT]" => base64_encode($options["View"]),
         "[StatusUpdate.Edit]" => base64_encode($edit),
         "[StatusUpdate.ID]" => base64_encode($value),
         "[StatusUpdate.Modified]" => base64_encode($_StatusUpdate["ListItem"]["Modified"]),
         "[StatusUpdate.Notes]" => base64_encode($options["Notes"]),
         "[StatusUpdate.OriginalPoster]" => base64_encode($display),
         "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[StatusUpdate.Votes]" => base64_encode($options["Vote"])
        ]);
       }
      }
     }
    }
   } elseif($st == "MBR") {
    $accessCode = "Accepted";
    $home = base64_encode("Profile:Home");
    $extension = $this->core->Extension("ba17995aafb2074a28053618fb71b912");
    $members = $this->core->RenderSearchIndex("Member");
    foreach($members as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Members", $value]);
     $_Member = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Member;$value")
     ]);
     $member = $_Member["DataModel"];
     if($_Member["Empty"] == 0) {
      $them = $member["Login"]["Username"];
      $cms = $this->core->Data("Get", ["cms", md5($them)]) ?? [];
      $contacts = $cms["Contacts"] ?? [];
      $check = $this->core->CheckPrivacy([
       "Contacts" => $contacts,
       "Privacy" => $member["Privacy"]["Profile"],
       "UN" => $them,
       "Y" => $you
      ]);
      $lookMeUp = $member["Privacy"]["LookMeUp"] ?? 0;
      $theyBlockedYou = $this->core->CheckBlocked([$member, "Members", $you]);
      $youBlockedThem = $this->core->CheckBlocked([$y, "Members", $them]);
      if($theyBlockedYou == 0 && $youBlockedThem == 0 && $check == 1 && $lookMeUp == 1) {
       $options = $_Member["ListItem"]["Options"];
       array_push($msg, [
        "[X.LI.DisplayName]" => base64_encode($_Member["ListItem"]["Title"]),
        "[X.LI.Description]" => base64_encode($_Member["ListItem"]["Description"]),
        "[X.LI.Options]" => base64_encode($this->core->Element(["button", "View Profile", [
         "class" => "OpenCard v2",
         "data-view" => $options["View"]
        ]])),
        "[X.LI.ProfilePicture]" => base64_encode($options["ProfilePicture"])
       ]);
      }
     }
    }
   } elseif($st == "MBR-ALB") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("b6728e167b401a5314ba47dd6e4a55fd");
    if($notAnon == 1) {
     $username = base64_decode($data["UN"]);
     $t = ($username != $you) ? $this->core->Member($username) : $y;
     $fs = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]) ?? [];
     $albums = $fs["Albums"] ?? [];
     foreach($albums as $key => $value) {
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]) ?? [];
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
    $accessCode = "Accepted";
    $home = base64_encode("Blog:Home");
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    if($notAnon == 1) {
     $blogs = $y["Blogs"] ?? [];
     foreach($blogs as $key => $value) {
      $bl = $this->core->CheckBlocked([$y, "Blogs", $value]);
      $_Blog = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Blog;$value")
      ]);
      if($_Blog["Empty"] == 0) {
       $options = $_Blog["ListItem"]["Options"];
       $blog = $_Blog["DataModel"];
       $illegal = $blog["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       if($illegal == 0) {
       $coverPhoto = $blog["ICO"] ?? $coverPhoto;
       $coverPhoto = base64_encode($coverPhoto);
        array_push($msg, [
         "[X.LI.I]" => base64_encode($_Blog["ListItem"]["CoverPhoto"]),
         "[X.LI.T]" => base64_encode($_Blog["ListItem"]["Title"]),
         "[X.LI.D]" => base64_encode($_Blog["ListItem"]["Description"]),
         "[X.LI.DT]" => base64_encode($_Blog["ListItem"]["Options"]["View"])
        ]);
       }
      }
     }
    }
   } elseif($st == "MBR-CA" || $st == "MBR-JE") {
    $accessCode = "Accepted";
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $extension = $this->core->Extension("90bfbfb86908fdc401c79329bedd7df5");
    foreach($t["Pages"] as $key => $value) {
     $article = $this->core->Data("Get", ["pg", $value]) ?? [];
     if(!empty($article)) {
     $st = str_replace("MBR-", "", $st);
     $t = $this->core->Member($article["UN"]);
     $cms = $this->core->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]) ?? [];
     $b2 = ($t["Login"]["Username"] == $you) ? "Your Profile" : $t["Personal"]["DisplayName"]."'s Profile";
     $bl = $this->core->CheckBlocked([$t, "Members", $you]);
     $illegal = $article["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     $theirPrivacy = $t["Privacy"];
     $privacy = $theirPrivacy["Profile"];
     $privacy = ($st == "CA") ? $theirPrivacy["Contributions"] : $privacy;
     $privacy = ($st == "JE") ? $theirPrivacy["Journal"] : $privacy;
     $ck = ($article["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
     $ck2 = $this->core->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $privacy,
      "UN" => $article["UN"],
      "Y" => $you
     ]);
     $ck3 = ($illegal == 0 && $article["Category"] == $st) ? 1 : 0;
     $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
     $ck2 = ($bl == 0 || $t["Login"]["Username"] == $you) ? 1 : 0;
     if($ck == 1 && $ck2 == 1) {
      array_push($msg, [
       "[Article.Title]" => base64_encode($article["Title"]),
       "[Article.Subtitle]" => base64_encode("Posted by ".$t["Personal"]["DisplayName"]." ".$this->core->TimeAgo($article["Created"])."."),
       "[Article.Description]" => base64_encode($this->core->PlainText([
        "BBCodes" => 1,
        "Data" => $article["Description"],
        "Display" => 1,
        "HTMLDecode" => 1
       ])),
       "[Article.ViewPage]" => base64_encode("$lpg;".base64_encode("v=".base64_encode("Page:Home")."&b2=$b2&back=1&lPG=$lpg&ID=$value"))
      ]);
     }
     }
    }
   } elseif($st == "MBR-Chat" || $st == "MBR-GroupChat") {
    $accessCode = "Accepted";
    $group = $data["Group"] ?? 0;
    $integrated = $data["Integrated"] ?? 0;
    $oneOnOne = $data["1on1"] ?? 0;
    $extension = $this->core->Extension("343f78d13872e3b4e2ac0ba587ff2910");
    if($notAnon == 1) {
     $extension = "343f78d13872e3b4e2ac0ba587ff2910";
     $extension = ($integrated == 0) ? "183d39e5527b3af3e7652181a0e36e25" : $extension;
     $extension = $this->core->Extension($extension);
     if($group == 1) {
      $groups = $y["GroupChats"] ?? [];
      foreach($groups as $key => $group) {
       $active = 0;
       $bl = $this->core->CheckBlocked([$y, "Group Chats", $group]);
       $group = str_replace("nyc.outerhaven.chat.", "", $group);
       $chat = $this->core->Data("Get", ["chat", $group]) ?? [];
       $contributors = $chat["Contributors"] ?? [];
       foreach($contributors as $member => $role) {
        if($member == $you) {
         $active++;
        }
       }
       $nsfw = $chat["NSFW"] ?? 0;
       $nsfw = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
       $privacy = $chat["Privacy"] ?? 0;
       $privacy = ($active == 1 || $privacy != md5("Private")) ? 1 : 0;
       if($chat["UN"] == $you || ($bl == 0 && $nsfw == 1 && $privacy == 1)) {
        $displayName = $chat["Title"] ?? "Group Chat";
        $t = $this->core->Member($this->core->ID);
        $view = "v=".base64_encode("Chat:Home")."&Group=1&ID=".base64_encode($group)."&Integrated=$integrated";
        $view .= ($integrated == 1) ? "&Card=1" : "";
        array_push($msg, [
         "[Chat.DisplayName]" => base64_encode($displayName),
         "[Chat.Online]" => base64_encode(""),
         "[Chat.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%")),
         "[Chat.View]" => base64_encode(base64_encode($view))
        ]);
       }
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
       $bl = $this->core->CheckBlocked([$y, "Members", md5($member)]);;
       $_Member = $this->core->GetContentData([
        "Blacklisted" => $bl,
        "ID" => base64_encode("Member;".md5($member))
       ]);
       if($_Member["Empty"] == 0) {
        $view = "v=".base64_encode("Chat:Home")."&1on1=1&Username=".base64_encode($member);
        $view .= ($integrated == 1) ? "&Card=1" : "";
        $t = $_Member["DataModel"];
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
    }
   } elseif($st == "MBR-Forums") {
    $accessCode = "Accepted";
    $home = base64_encode("Forum:Home");
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    $x = $y["Forums"] ?? [];
    foreach($x as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Forums", $value]);;
     $_Forum = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Forum;$value")
     ]);
     if($_Forum["Empty"] == 0) {
      $active = 0;
      $forum = $_Forum["DataModel"];
      $illegal = $value["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($illegal == 0) {
       $options = $_Forum["ListItem"]["Options"];
       array_push($msg, [
        "[X.LI.I]" => base64_encode($_Forum["ListItem"]["CoverPhoto"]),
        "[X.LI.T]" => base64_encode($_Forum["ListItem"]["Title"]),
        "[X.LI.D]" => base64_encode($_Forum["ListItem"]["Description"]),
        "[X.LI.DT]" => base64_encode($options["View"])
       ]);
      }
     }
    }
   } elseif($st == "MBR-LLP") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("da5c43f7719b17a9fab1797887c5c0d1");
    if($notAnon == 1) {
     $articles = $y["Pages"] ?? [];
     foreach($articles as $key => $value) {
      $bl = $this->core->CheckBlocked([$y, "Pages", $value]);;
      $_Article = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Page;$value")
      ]);
      if($_Article["Empty"] == 0) {
       $article = $_Article["DataModel"];
       if($article["Category"] != "EXT") {
        $options = $_Article["ListItem"]["Options"];
        array_push($msg, [
         "[Extension.Category]" => base64_encode($article["Category"]),
         "[Extension.Delete]" => base64_encode($options["Delete"]),
         "[Extension.Description]" => base64_encode($_Article["ListItem"]["Description"]),
         "[Extension.Edit]" => base64_encode($options["Edit"]),
         "[Extension.ID]" => base64_encode($value),
         "[Extension.Title]" => base64_encode($_Article["ListItem"]["Title"])
        ]);
       }
      }
     }
    }
   } elseif($st == "MBR-Polls") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("184ada666b3eb85de07e414139a9a0dc");
    $polls = $y["Polls"] ?? [];
    foreach($polls as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Polls", $value]);
     $_Poll = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Poll;$value")
     ]);
     if($_Poll["Empty"] == 0) {
      $poll = $_Poll["DataModel"];
      $ck = ($poll["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      if($bl == 0 && $ck == 1) {
       $blockCommand = ($bl == 0) ? "Block" : "Unblock";
       $extension = $this->core->Element(["div", $extension, ["class" => "K4i Poll$value"]]);
       $options = $_Poll["ListItem"]["Options"];
       $blockOrDelete = ($poll["UN"] == $you) ? $this->core->Element([
        "div", $this->core->Element(["button", $blockCommand, [
         "class" => "UpdateButton v2 v2w",
         "data-processor" => $options["Block"]
        ]]), ["class" => "Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Delete", [
         "class" => "OpenDialog v2 v2w",
         "data-view" => $options["Delete"]
        ]]), ["class" => "Desktop33"]
       ]) : "";
       $vote = "";
       $voteCounts = [];
       $votes = 0;
       $youVoted = 0;
       foreach($poll["Votes"] as $number => $info) {
        if($info[0] == $you) {
         $choice = $info[1] ?? 0;
         $voteCounts[$choice] = $voteCounts[$choice] ?? 0;
         $voteCounts[$choice]++;
         $votes++;
         $youVoted++;
        }
       } foreach($poll["Options"] as $number => $option) {
        $voteShare = $voteCounts[$number] ?? 0;
        $option = $this->core->Element([
         "h4", $option
        ]).$this->core->Element(["progress", $voteShare."%", [
         "max" => $votes,
         "value" => $voteShare
        ]]);
        if($notAnon == 0 || $youVoted == 0) {
         $option = $this->core->Element(["button", $option, [
          "class" => "LI UpdateContent",
          "data-container" => ".Poll$value",
          "data-view" => base64_encode("v=".base64_encode("Poll:Vote")."&Choice=".base64_encode($number)."&ID=".base64_encode($value))
         ]]);
        }
        $vote .= $option;
       }
       array_push($msg, [
        "[Poll.BlockOrDelete]" => base64_encode($blockOrDelete),
        "[Poll.Description]" => base64_encode($_Poll["ListItem"]["Description"]),
        "[Poll.ID]" => base64_encode($value),
        "[Poll.Share]" => base64_encode($options["Share"]),
        "[Poll.Title]" => base64_encode($_Poll["ListItem"]["Title"]),
        "[Poll.Vote]" => base64_encode($vote)
       ]);
      }
     }
    }
   } elseif($st == "MBR-SU") {
    $accessCode = "Accepted";
    $attlv = base64_encode("LiveView:InlineMossaic");
    $edit = base64_encode("StatusUpdate:Edit");
    $stream = $this->core->Data("Get", [
     "stream",
     md5(base64_decode($data["UN"]))
    ]) ?? [];
    $extension = $this->core->Extension("18bc18d5df4b3516c473b82823782657");
    foreach($stream as $key => $value) {
     $id = $value["UpdateID"] ?? "";
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $value]);
     $_StatusUpdate = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("StatusUpdate;$id")
     ]);
     if($_StatusUpdate["Empty"] == 0) {
      $update = $_StatusUpdate["DataModel"];
      $from = $update["From"] ?? $this->core->ID;
      $check = ($from == $you) ? 1 : 0;
      $illegal = $update["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($check == 1 || ($bl == 0 && $illegal == 0)) {
       $op = ($check == 1) ? $y : $this->core->Member($from);
       $cms = $this->core->Data("Get", ["cms", md5($from)]) ?? [];
       $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $update["NSFW"] == 0) ? 1 : 0;
       $ck2 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $update["Privacy"],
        "UN" => $update["From"],
        "Y" => $you
       ]);
       $ck2 = 1;
       if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
        $display = ($from == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $options = $_StatusUpdate["ListItem"]["Options"];
        $edit = ($op["Login"]["Username"] == $you) ? $this->core->Element([
         "button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-view" => $options["Delete"]
         ]
        ]).$this->core->Element([
         "button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-view" => $options["Edit"]
         ]
        ]) : "";
        array_push($msg, [
         "[StatusUpdate.Attachments]" => base64_encode($_StatusUpdate["ListItem"]["Attachments"]),
         "[StatusUpdate.Body]" => base64_encode($_StatusUpdate["ListItem"]["Body"]),
         "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($update["Created"])),
         "[StatusUpdate.DT]" => base64_encode($options["View"]),
         "[StatusUpdate.Edit]" => base64_encode($edit),
         "[StatusUpdate.ID]" => base64_encode($id),
         "[StatusUpdate.Modified]" => base64_encode($_StatusUpdate["ListItem"]["Modified"]),
         "[StatusUpdate.Notes]" => base64_encode($options["Notes"]),
         "[StatusUpdate.OriginalPoster]" => base64_encode($display),
         "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[StatusUpdate.Votes]" => base64_encode($options["Vote"])
        ]);
       }
      }
     }
    }
   } elseif($st == "MBR-XFS") {
    $accessCode = "Accepted";
    $albumID = $data["AID"] ?? md5("unsorted");
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $extension = $this->core->Extension("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $fileSystem = $this->core->Data("Get", [
     "fs",
     md5($t["Login"]["Username"])
    ]) ?? [];
    if($t["Login"]["Username"] == $this->core->ID) {
     $files = $this->core->Data("Get", ["app", "fs"]) ?? [];
    } else {
     $files = $fileSystem["Files"] ?? [];
    } foreach($files as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Files", $value["ID"]]);
     $illegal = $value["Illegal"] ?? 0;
     $illegal = ($illegal >= $this->illegal) ? 1 : 0;
     if($albumID == $value["AID"] && $bl == 0 && $illegal == 0) {
      $source = $this->core->GetSourceFromExtension([$t["Login"]["Username"], $value]);
      array_push($msg, [
       "[File.CoverPhoto]" => base64_encode($source),
       "[File.Title]" => base64_encode($value["Title"]),
       "[File.View]" => base64_encode("$lpg;".base64_encode("v=".base64_encode("File:Home")."&ID=".$value["ID"]."&UN=".$t["Login"]["Username"]."&back=1&lPG=$lpg"))
      ]);
     }
    }
   } elseif($st == "Media") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $index = $this->core->RenderSearchIndex("Media");
    foreach($index as $key => $value) {
     $value = explode(";", $value);
     $bl = $this->core->CheckBlocked([$y, "Members", $value[0]]);;
     $_Member = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Member;".$value[0])
     ]);
     if($_Member["Empty"] == 0) {
      $bl = $this->core->CheckBlocked([$y, "Files", $value[1]]);
      $member = $_Member["DataModel"];
      if($bl == 0) {
       $file = $this->core->Data("Get", ["fs", $value[0]]) ?? [];
       $file = $file["Files"] ?? [];
       $file = $file[$value[1]] ?? [];
       $illegal = $file["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       if($illegal == 0) {
        $source = $this->core->GetSourceFromExtension([
         $member["Login"]["Username"],
         $file
        ]);
        array_push($msg, [
         "[File.CoverPhoto]" => base64_encode($source),
         "[File.Title]" => base64_encode($file["Title"]),
         "[File.View]" => base64_encode("$lpg;".base64_encode("v=".base64_encode("File:Home")."&ID=".$file["ID"]."&UN=".$member["Login"]["Username"]."&back=1&lPG=$lpg"))
        ]);
       }
      }
     }
    }
   } elseif($st == "Polls") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("184ada666b3eb85de07e414139a9a0dc");
    $polls = $this->core->RenderSearchIndex("Poll");
    foreach($polls as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Polls", $value]);
     $_Poll = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Poll;$value")
     ]);
     if($_Poll["Empty"] == 0) {
      $poll = $_Poll["DataModel"];
      $ck = ($poll["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      if($bl == 0 && $ck == 1) {
       $blockCommand = ($bl == 0) ? "Block" : "Unblock";
       $extension = $this->core->Element([
        "div", $extension, ["class" => "K4i Poll$value"]
       ]);
       $options = $_Poll["ListItem"]["Options"];
       $blockOrDelete = ($poll["UN"] == $you) ? $this->core->Element([
        "div", $this->core->Element(["button", $blockCommand, [
         "class" => "UpdateButton v2 v2w",
         "data-processor" => $options["Block"]
        ]]), ["class" => "Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Delete", [
         "class" => "OpenDialog v2 v2w",
         "data-view" => $options["Delete"]
        ]]), ["class" => "Desktop33"]
       ]) : "";
       $vote = "";
       $voteCounts = [];
       $votes = 0;
       $youVoted = 0;
       foreach($poll["Votes"] as $number => $info) {
        if($info[0] == $you) {
         $choice = $info[1] ?? 0;
         $voteCounts[$choice] = $voteCounts[$choice] ?? 0;
         $voteCounts[$choice]++;
         $votes++;
         $youVoted++;
        }
       } foreach($poll["Options"] as $number => $option) {
        $voteShare = $voteCounts[$number] ?? 0;
        $option = $this->core->Element([
         "h4", $option
        ]).$this->core->Element(["progress", $voteShare."%", [
         "max" => $votes,
         "value" => $voteShare
        ]]);
        if($notAnon == 0 || $youVoted == 0) {
         $option = $this->core->Element(["button", $option, [
          "class" => "LI UpdateContent",
          "data-container" => ".Poll$value",
          "data-view" => base64_encode("v=".base64_encode("Poll:Vote")."&Choice=".base64_encode($number)."&ID=".base64_encode($value))
         ]]);
        }
        $vote .= $option;
       }
       array_push($msg, [
        "[Poll.BlockOrDelete]" => base64_encode($blockOrDelete),
        "[Poll.Description]" => base64_encode($_Poll["ListItem"]["Description"]),
        "[Poll.ID]" => base64_encode($value),
        "[Poll.Share]" => base64_encode($options["Share"]),
        "[Poll.Title]" => base64_encode($_Poll["ListItem"]["Title"]),
        "[Poll.Vote]" => base64_encode($vote)
       ]);
      }
     }
    }
   } elseif($st == "Products") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    $members = $this->core->RenderSearchIndex("Member");
    foreach($members as $key => $value) {
     $value = $this->core->Data("Get", ["mbr", $value]) ?? [];
     if(!empty($value["Login"])) {
      $them = $value["Login"]["Username"];
      if($notAnon == 1) {
       $bl = $this->core->CheckBlocked([$y, "Members", $them]);
       $_Shop = $this->core->GetContentData([
        "Blacklisted" => $bl,
        "ID" => base64_encode("Shop;".md5($them)),
        "Owner" => $them
       ]);
       if($_Shop["Empty"] == 0) {
        $b2 = $b2 ?? "Products";
        $shop = $_Shop["DataModel"];
        $products = $shop["Products"] ?? [];
        foreach($products as $id => $product) {
         $bl = $this->core->CheckBlocked([$y, "Products", $product]);
         $_Product = $this->core->GetContentData([
          "BackTo" => $b2,
          "Blacklisted" => $bl,
          "ID" => base64_encode("Product;$product"),
          "Owner" => base64_encode($them)
         ]);
         if($_Product["Empty"] == 0) {
          $product = $_Product["DataModel"];
          $usernamee = base64_encode($them);
          $ck = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
          $ck2 = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
          $ck3 = $value["Subscriptions"]["Artist"]["A"] ?? 0;
          $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
          $ck = ($ck == 1 || $them == $this->core->ShopID) ? 1 : 0;
          $illegal = $product["Illegal"] ?? 0;
          $illegal = ($illegal >= $this->illegal) ? 1 : 0;
          $illegal = ($them != $this->core->ShopID) ? 1 : 0;
          if($bl == 0 && $ck == 1 && $illegal == 0) {
           $options = $_Product["ListItem"]["Options"];
           array_push($msg, [
            "[X.LI.I]" => base64_encode($_Product["ListItem"]["CoverPhoto"]),
            "[X.LI.D]" => base64_encode($_Product["ListItem"]["Description"]),
            "[X.LI.DT]" => base64_encode($options["View"]),
            "[X.LI.T]" => base64_encode($_Product["ListItem"]["Title"])
           ]);
          }
         }
        }
       }
      }
     }
    }
   } elseif($st == "SHOP") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("6d8aedce27f06e675566fd1d553c5d92");
    if($notAnon == 1) {
     $b2 = $b2 ?? "Artists";
     $card = base64_encode("Shop:Home");
     $shops = $this->core->RenderSearchIndex("Shop");
     foreach($shops as $key => $value) {
      $t = (md5($you) == $value) ? $y : $this->core->Data("Get", ["mbr", $value]);
      $them = $t["Login"]["Username"];
      $bl = $this->core->CheckBlocked([$y, "Members", $them]);
      $_Shop = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Shop;$value"),
       "Owner" => $them
      ]);
      if($_Shop["Empty"] == 0) {
       $cms = $this->core->Data("Get", ["cms", md5($them)]) ?? [];
       $cms = $cms["Contacts"] ?? [];
       $ck = $this->core->CheckPrivacy([
        "Contacts" => $cms,
        "Privacy" => $t["Privacy"]["Shop"],
        "UN" => $them,
        "Y" => $you
       ]);
       $shop = $_Shop["DataModel"];
       $ck2 = $shop["Open"] ?? 0;
       if(($bl == 0 && $ck == 1 && $ck2 == 1) || $them == $you) {
        array_push($msg, [
         "[X.LI.CoverPhoto]" => base64_encode($_Shop["ListItem"]["CoverPhoto"]),
         "[X.LI.Description]" => base64_encode($_Shop["ListItem"]["Description"]),
         "[X.LI.Lobby]" => base64_encode($_Shop["ListItem"]["Options"]["View"]),
         "[X.LI.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%")),
         "[X.LI.Title]" => base64_encode($_Shop["ListItem"]["Title"])
        ]);
       }
      }
     }
    }
   } elseif($st == "SHOP-InvoicePresets") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e9f34ca1985c166bf7aa73116a745e92");
    $shop = $this->core->Data("Get", ["shop", $data["Shop"]]) ?? [];
    $invoicePresets = $shop["InvoicePresets"] ?? [];
    foreach($invoicePresets as $key => $value) {
     $preset = $this->core->Data("Get", ["invoice-preset", $value]) ?? [];
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
    $accessCode = "Accepted";
    $shop = $this->core->Data("Get", ["shop", $data["Shop"]]) ?? [];
    $invoices = $shop["Invoices"] ?? [];
    $extension = $this->core->Extension("e9f34ca1985c166bf7aa73116a745e92");
    foreach($invoices as $key => $value) {
     $invoice = $this->core->Data("Get", ["invoice", $value]) ?? [];
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
    $accessCode = "Accepted";
    $extension = $this->core->Extension("504e2a25db677d0b782d977f7b36ff30");
    $purchaseOrders = $this->core->Data("Get", ["po", md5($you)]) ?? [];
    foreach($purchaseOrders as $key => $value) {
     $member = $this->core->Member($value["UN"]);
     if(!empty($member["Login"])) {
      $ccomplete = ($value["Complete"] == 0) ? $this->core->Element(["button", "Mark as Complete", [
       "class" => "BBB CompleteOrder v2 v2w",
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
   } elseif($st == "SHOP-Products") {
    $accessCode = "Accepted";
    $home = base64_encode("Product:Home");
    $username = $data["UN"] ?? base64_encode($you);
    $username = base64_decode($username);
    $t = ($username == $you) ? $y : $this->core->Member($username);
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    $shop = $this->core->Data("Get", ["shop", md5($username)]) ?? [];
    $products = $shop["Products"] ?? [];
    foreach($products as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Products", $value]);
     $_Product = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Product;$value"),
      "Owner" => base64_encode($username)
     ]);
     if($_Product["Empty"] == 0) {
      $product = $_Product["DataModel"];
      $ck = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
      $ck3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
      $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
      $ck = ($ck == 1 || $this->core->ShopID == $username) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $illegal = ($this->core->ShopID != $username) ? 1 : 0;
      if($bl == 0 && $ck == 1 && $illegal == 0) {
       $options = $_Product["ListItem"]["Options"];
       array_push($msg, [
        "[X.LI.I]" => base64_encode($_Product["ListItem"]["CoverPhoto"]),
        "[X.LI.T]" => base64_encode($_Product["ListItem"]["Title"]),
        "[X.LI.D]" => base64_encode($_Product["ListItem"]["Description"]),
        "[X.LI.DT]" => base64_encode(base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=$value&UN=".base64_encode($username)))
       ]);
      }
     }
    }
   } elseif($st == "StatusUpdates") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("18bc18d5df4b3516c473b82823782657");
    $statusUpdates = $this->core->RenderSearchIndex("StatusUpdate");
    foreach($statusUpdates as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $value]);
     $_StatusUpdate = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("StatusUpdate;$value")
     ]);
     if($_StatusUpdate["Empty"] == 0) {
      $update = $_StatusUpdate["DataModel"];
      $from = $update["From"] ?? "";
      $check = ($from == $you) ? 1 : 0;
      $illegal = $update["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($check == 1 || ($bl == 0 && $illegal == 0)) {
       $from = $from ?? $this->core->ID;
       if($bl == 0 || $from == $you) {
        $attachments = "";
        if(!empty($update["Attachments"])) {
         $attachments =  $this->view(base64_encode("LiveView:InlineMossaic"), [
          "Data" => [
           "ID" => base64_encode(implode(";", $update["Attachments"])),
           "Type" => base64_encode("DLC")
          ]
         ]);
         $attachments = $this->core->RenderView($attachments);
        }
        $op = ($from == $you) ? $y : $this->core->Member($from);
        $cms = $this->core->Data("Get", ["cms", md5($from)]) ?? [];
        $privacy = $op["Privacy"]["Posts"] ?? md5("Public");
        $ck = $update["NSFW"] ?? 0;
        $ck = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $ck == 0) ? 1 : 0;
        $ck2 = $cms["Contacts"] ?? [];
        $ck2 = $this->core->CheckPrivacy([
         "Contacts" => $ck2,
         "Privacy" => $privacy,
         "UN" => $from,
         "Y" => $you
        ]);
        if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
         $created = $update["Created"] ?? $this->core->timestamp;
         $options = $_StatusUpdate["ListItem"]["Options"];
         $display = $op["Personal"]["DisplayName"] ?? $from;
         $display = ($from == $this->core->ID) ? "Anonymous" : $display;
         $edit = ($from == $you) ? $this->core->Element([
          "button", "Delete", [
           "class" => "InnerMargin OpenDialog",
           "data-view" => $options["Delete"]
          ]
         ]).$this->core->Element([
          "button", "Edit", [
           "class" => "InnerMargin OpenCard",
           "data-view" => $options["Edit"]
          ]
         ]) : "";
         array_push($msg, [
          "[StatusUpdate.Attachments]" => base64_encode($_StatusUpdate["ListItem"]["Attachments"]),
          "[StatusUpdate.Body]" => base64_encode($_StatusUpdate["ListItem"]["Body"].$value),
          "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($created)),
          "[StatusUpdate.DT]" => base64_encode($options["View"]),
          "[StatusUpdate.Edit]" => base64_encode($edit),
          "[StatusUpdate.ID]" => base64_encode($value),
          "[StatusUpdate.Modified]" => base64_encode($_StatusUpdate["ListItem"]["Modified"]),
          "[StatusUpdate.Notes]" => base64_encode($options["Notes"]),
          "[StatusUpdate.OriginalPoster]" => base64_encode($display),
          "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
          "[StatusUpdate.Votes]" => base64_encode($options["Vote"])
         ]);
        }
       }
      }
     }
    }
   } elseif($st == "XFS") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $username = base64_decode($data["UN"]);
    if($this->core->ID == $username) {
     $files = $this->core->Data("Get", ["app", "fs"]) ?? [];
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
    $accessCode,
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
   $secureQuery = base64_encode($query);
   $r = $this->core->Change([[
    "[ReSearch.GoHome]" => $goHome
   ], $this->core->Extension("df4f7bc99b9355c34b571946e76b8481")]);
   if(!empty($query)) {
    $r = $this->core->Change([[
     "[ReSearch.Archive]" => base64_encode("v=$search&pub=1&query=$secureQuery&lPG=ReSearch&st=CA"),
     "[ReSearch.Artists]" => base64_encode("v=$search&pub=1&query=$secureQuery&lPG=ReSearch&st=SHOP"),
     "[ReSearch.Blogs]" => base64_encode("v=$search&pub=1&query=$secureQuery&lPG=ReSearch&st=BLG"),
     "[ReSearch.Chat]" => base64_encode("v=$search&pub=1&query=$secureQuery&lPG=ReSearch&st=Chat&Integrated=1"),
     "[ReSearch.Forums]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Forums"),
     "[ReSearch.Links]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Links"),
     "[ReSearch.Media]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Media"),
     "[ReSearch.Members]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=MBR"),
     "[ReSearch.Query]" => $query,
     "[ReSearch.Polls]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Polls"),
     "[ReSearch.Products]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Products"),
     "[ReSearch.StatusUpdates]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=StatusUpdates")
    ], $this->core->Extension("bae5cdfa85bf2c690cbff302ba193b0b")]);
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
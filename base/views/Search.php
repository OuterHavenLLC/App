<?php
 Class Search extends OH {
  function __construct() {
   parent::__construct();
   $this->ContentIsProtected = $this->core->Element([
    "h3", "Protected Content", ["class" => "CenterText UpperCase"]
   ]);
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
   $pub = $data["pub"] ?? 0;
   $searchType = $data["st"] ?? "";
   $lpg = $data["lPG"] ?? $searchType;
   $searchLists = $this->core->config["App"]["Search"];
   $query = $data["query"] ?? "";
   $ck = (!empty($searchType) && in_array($searchType, $searchLists)) ? 1 : 0;
   $li = "v=".$this->lists."&query=$query&st=$searchType";
   $lit = md5($searchType.$this->core->timestamp.rand(0, 1776));
   $lo = "";
   $r = [
    "Body" => "The List Type is missing.",
    "Header" => "Not Found"
   ];
   $extension = "6dc4eecde24bf5f5e70da253aaac2b68";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   foreach($searchLists as $key => $list) {
    if($key == $searchType) {
     $ck++;
    }
   } if($ck == 1) {
    $accessCode = "Accepted";
    if($searchType == "ADM-LLP") {
     $h = "App Extensions";
     $lis = "Search Extensions";
     $lo =  ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Extension:Edit")."&New=1")
      ]
     ]) : "";
    } elseif($searchType == "BGP") {
     $data = $this->core->FixMissing($data, ["BLG"]);
     $h = "Blog Posts";
     $li .= "&ID=".$data["ID"];
     $lis = "Search Posts";
    } elseif($searchType == "BL") {
     $bl = base64_decode($data["BL"]);
     $h = "$bl Blacklist";
     $li .= "&BL=".$data["BL"];
     $lis = "Search $bl Blacklist";
     $extension = "6dc4eecde24bf5f5e70da253aaac2b68";
    } elseif($searchType == "BLG") {
     $h = "Blogs";
     $li .= "&b2=Blogs&lPG=$searchType";
     $lis = "Search Blogs";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "Bulletins") {
     $h = "Bulletins";
     $lis = "Search Bulletins";
    } elseif($searchType == "CA") {
     $h = "Community Archive";
     $li .= "&b2=".urlencode("the Archive")."&lPG=$lpg";
     $lis = "Search Articles";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "CART") {
     $extension = "e58b4fc5070b14c01c88c28050547285";
     $username = $data["Username"] ?? $you;
     $shopID = md5($username);
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     $li .= "&ID=$shopID&Username=".base64_encode($username);
     $lis = "Search ".$shop["Title"];
    } elseif($searchType == "Chat") {
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
     $h = "Group Chats";
     $integrated = $data["Integrated"] ?? 0;
     $li .= "&Integrated=$integrated";
     $lis = "Search $h";
    } elseif($searchType == "Congress") {
     $chamber = $data["Chamber"] ?? "";
     $extension = "8568ac7727dae51ee4d96334fa891395";
     $h = "Content Moderation";
     $li .= "&Chamber=$chamber";
     $lis = "Search Content";
    } elseif($searchType == "CongressionalBallot") {
     $chamber = $data["Chamber"] ?? "";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
     $h = "Congressional $chamber Ballot";
     $li .= "&Chamber=$chamber";
     $lis = "Search Candidates";
    } elseif($searchType == "CongressionalStaffHouse" || $searchType == "CongressionalStaffSenate") {
     $chamber = $data["Chamber"] ?? "";
     $extension = "e58b4fc5070b14c01c88c28050547285";
     $li .= "&Chamber=$chamber";
     $lis = "Search  $chamber Staff";
    } elseif($searchType == "Contacts") {
     $h = "Contact Manager";
     $lis = "Search Contacts";
    } elseif($searchType == "ContactsProfileList") {
     $data = $this->core->FixMissing($data, ["UN"]);
     $username = base64_decode($data["UN"]);
     $ck = ($username == $y["Login"]["Username"]) ? 1 : 0;
     $t = ($ck == 1) ? $y : $this->core->Member($username);
     $h = ($ck == 1) ? "Your Contacts" : $t["Personal"]["DisplayName"]."'s Contacts";
     $li .= "&b2=$b2&lPG=$lpg&UN=".$data["UN"];
     $lis = "Search Contacts";
    } elseif($searchType == "ContactsRequests") {
     $h = "Contact Requests";
     $lis = "Search Contact Requests";
    } elseif($searchType == "Contributors") {
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
    } elseif($searchType == "DC") {
     $dce = base64_encode("DiscountCode:Edit");
     $h = "Discount Codes";
     $lis = "Search Codes";
     $lo = ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=$dce&new=1")
      ]
     ]) : "";
    } elseif($searchType == "Feedback") {
     $h = "Feedback";
     $li .= "&lPG=$lpg";
     $lis = "Search Feedback";
    } elseif($searchType == "Forums") {
     $h = "Forums";
     $li .= "&lPG=$lpg";
     $lis = "Search Private and Public Forums";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "Forums-Admin") {
     $h = "Administrators";
     $li .= "&ID=".$data["ID"];
     $lis = "Search Administrators";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "Forums-Posts") {
     $forumID = $data["ID"] ?? "";
     $forumID = base64_decode($forumID);
     $forum = $this->core->Data("Get", ["pf", $forumID]) ?? [];
     $h = "All Posts";
     $li .= "&ID=$forumID";
     $lis = "Search all Posts from ".$forum["Title"];
    } elseif($searchType == "Forums-Topic") {
     $extension = "e58b4fc5070b14c01c88c28050547285";
     $forumID = $data["Forum"] ?? "";
     $topicID = $data["Topic"] ?? "";
     $forum = $this->core->Data("Get", ["pf", $forumID]) ?? [];
     $li .= "&Forum=$forumID&Topic=$topicID";
     $topic = $forum["Topics"][$topicID] ?? [];
     $topic = $topic["Title"] ?? "Untitled";
     $lis = "Search Posts from $topic";
    } elseif($searchType == "Forums-Topics") {
     $extension = "e58b4fc5070b14c01c88c28050547285";
     $forumID = $data["Forum"] ?? "";
     $forum = $this->core->Data("Get", ["pf", $forumID]) ?? [];
     $li .= "&Forum=$forumID";
     $lis = "Search Topics from ".$forum["Title"];
    } elseif($searchType == "Knowledge") {
     $extension = "8568ac7727dae51ee4d96334fa891395";
     $h = "Knowledge Base";
     $lis = "Search Q&As";
    } elseif($searchType == "Links") {
     $extension = "f2513ac8d0389416b680c75ed5667774";
     $h = "Links";
     $lis = "Search Links";
    } elseif($searchType == "Mainstream") {
     $extension = "f2513ac8d0389416b680c75ed5667774";
     $h = "The ".$searchType;
     $lis = "Search the Mainstream";
     $lo = $this->core->Element(["button", "Say Something", [
      "class" => "BBB MobileFull OpenCard v2 v2w",
      "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($you))
     ]]);
    } elseif($searchType == "MBR") {
     $h = "Members";
     $lis = "Search Members";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "MBR-ALB") {
     $ae = base64_encode("Album:Edit");
     $username = base64_decode($data["UN"]);
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Albums" : $t["Personal"]["DisplayName"]."'s Albums";
     $b2 = $b2 ?? $h;
     $b2 = urlencode($b2);
     $li .= "&UN=".base64_encode($t["Login"]["Username"])."&b2=$b2&lPG=$lpg";
     $lis = "Search Albums";
    } elseif($searchType == "MBR-BLG") {
     $bd = base64_encode("Authentication:DeleteBlogs");
     $be = base64_encode("Blog:Edit");
     $h = "Your Blogs";
     $li .= "&b2=Blogs&lPG=$lpg";
     $lis = "Search your Blogs";
    } elseif($searchType == "MBR-CA") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Contributions" : $t["Personal"]["DisplayName"]."'s Contributions";
     $li .= "&b2=$b2&lPG=$lpg&UN=".$data["UN"];
     $lis = "Search the Archive";
    } elseif($searchType == "MBR-Chat" || $searchType == "MBR-GroupChat") {
     $group = $data["Group"] ?? 0;
     $integrated = $data["Integrated"] ?? 0;
     $oneOnOne = $data["1on1"] ?? 0;
     $h = "1:1 Chat";
     $h = ($group == 1) ? "Group Chat" : $h;
     $li .= "&1on1=$oneOnOne&Group=$group&Integrated=$integrated";
     $lis = "Search $h";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "MBR-Forums") {
     $h = "Your Forums";
     $li .= "&lPG=$lpg";
     $lis = "Search Your Private and Public Forums";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "MBR-JE") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $ck = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $h = ($ck == 1) ? "Your Journal" : $t["Personal"]["DisplayName"]."'s Journal";
     $li .= "&b2=$b2&lPG=$lpg";
     $lis = "Search Entries";
    } elseif($searchType == "MBR-LLP") {
     $h = "Your Articles";
     $li .= "&b2=$b2&lPG=$lpg";
     $lis = "Search Articles";
    } elseif($searchType == "MBR-Polls") {
     $h = "Your Polls";
     $lis = "Search Polls";
    } elseif($searchType == "MBR-SU") {
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
    } elseif($searchType == "MBR-XFS") {
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
     $li .= "&AID=$aid&UN=".$data["UN"];
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
    } elseif($searchType == "Media") {
     $h = "Media";
     $li .= "&lPG=Files";
     $lis = "Search Files";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "Polls") {
     $h = "Polls";
     $lis = "Search Polls";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "PR") {
     $h = "Press Releases";
     $li .= "&b2=".urlencode("Press Releases")."&lPG=$lpg";
     $lis = "Search Articles";
    } elseif($searchType == "Products") {
     $h = "Products";
     $li .= "&lPG=$lpg&st=$searchType";
     $lis = "Search Products";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "SHOP") {
     $h = "Artists";
     $li .= "&lPG=$lpg&st=$searchType";
     $lis = "Search Shops";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "SHOP-InvoicePresets") {
     $h = "Services";
     $shop = $data["Shop"] ?? "";
     $li .= "&Shop=$shop&st=$searchType";
     $lis = "Search Services";
    } elseif($searchType == "SHOP-Invoices") {
     $h = "Invoices";
     $shop = $data["Shop"] ?? "";
     $li .= "&Shop=$shop&st=$searchType";
     $lis = "Search Invoices";
    } elseif($searchType == "SHOP-Products") {
     $h = "Products";
     $username = $data["UN"] ?? base64_encode($you);
     $li .= "&UN=$username&b2=$b2&lPG=$lpg&pub=$pub&st=$searchType";
     $lis = "Search $b2";
     $t = base64_decode($data["UN"]);
     $t = $this->core->Member($t);
     $isArtist = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $shopID = md5($t["Login"]["Username"]);
     $shop = $this->core->Data("Get", ["shop", $shopID]) ?? [];
     $ck = ($t["Login"]["Username"] == $you && $notAnon == 1) ? 1 : 0;
     $lo .= ($isArtist == 1 && $ck == 1) ? $this->core->Element([
      "button", "Discount Codes", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Search:Containers")."&st=DC")
      ]
     ]) : "";
     $extension = "e3de2c4c383d11d97d62a198f15ee885";
    } elseif($searchType == "SHOP-Orders") {
     $lis = "Search Orders";
     $extension = "e58b4fc5070b14c01c88c28050547285";
    } elseif($searchType == "XFS") {
     $_AddTo = $data["AddTo"] ?? "";
     $_Added = $data["Added"] ?? "";
     $h = "Files";
     $lPG = $data["lPG"] ?? $searchType;
     $li .= "&AddTo=$_AddTo&Added=$_Added&UN=".$data["UN"];
     $li .= (isset($data["ftype"])) ? "&ftype=".$data["ftype"] : "";
     $lis = "Search Files";
     #$extension = "e3de2c4c383d11d97d62a198f15ee885";
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
   } if(in_array($searchType, [
     "DC",
     "SHOP-InvoicePresets",
     "SHOP-Invoices",
     "XFS"
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
       $keywords = $tags["keywords"] ?? "None";
       $responseType = "ReplaceContent";
       $title = $dom->getElementsByTagName("title")->item(0)->nodeValue ?? "Untitled";
       $query = "REPLACE INTO Links(
        Link_Description,
        Link_Keywords,
        Link_IconExists,
        Link_ID,
        Link_Title
       ) VALUES(
        :Description,
        :Keywords,
        :IconExists,
        :ID,
        :Title
       )";
       $sql = New SQL($this->core->cypher->SQLCredentials());
       $sql->query($query, [
        ":Description" => $description,
        ":Keywords" => $keywords,
        ":IconExists" => $iconExists,
        ":ID" => $link,
        ":Title" => $title
       ]);
       $sql->execute();
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
   $searchType = $data["st"] ?? "";
   $limit = $data["Limit"] ?? 30;
   $lpg = $data["lPG"] ?? $searchType;
   $offset = $data["Offset"] ?? 0;
   $query = $data["query"] ?? base64_encode("");
   $query = base64_decode($query);
   $querysql = "%$query%";
   $sql = New SQL($this->core->cypher->SQLCredentials());
   $na .= (!empty($data["query"])) ? " for $query" : "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   if($searchType == "ADM-LLP") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("da5c43f7719b17a9fab1797887c5c0d1");
    if($notAnon == 1 && $y["Rank"] == md5("High Command")) {
     $_Query = "SELECT E.*, M.* FROM Extensions E
                         JOIN Members M
                         ON M.Member_Username=E.Extension_Username
                         WHERE E.Extension_Body LIKE :Search OR
                                       E.Extension_Description LIKE :Search OR
                                       E.Extension_ID LIKE :Search OR
                                       E.Extension_Title LIKE :Search OR
                                       E.Extension_Username LIKE :Search
                         ORDER BY E.Extension_Created DESC
                         LIMIT $limit
                         OFFSET $offset
     ";
     $sql->query($_Query, [
      ":Search" => $querysql
     ]);
     $sql = $sql->set();
     foreach($sql as $sql) {
      $_Extension = $this->core->GetContentData([
       "Blacklisted" => 0,
       "ID" => base64_encode("Extension;".$sql["Extension_ID"])
      ]);
      if($_Extension["Empty"] == 0) {
       $info = $_Extension["DataModel"];
       $options = $_Extension["ListItem"]["Options"];
       array_push($msg, [
        "[Extension.Category]" => base64_encode($info["Category"]),
        "[Extension.Delete]" => base64_encode($options["Delete"]),
        "[Extension.Description]" => base64_encode($sql["Extension_Description"]),
        "[Extension.Edit]" => base64_encode($options["Edit"]),
        "[Extension.ID]" => base64_encode($sql["Extension_ID"]),
        "[Extension.Title]" => base64_encode($sql["Extension_Title"])
       ]);
      }
     }
    }
   } elseif($searchType == "BGP") {
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
         "class" => "InnerMargin UpdateButton v2",
         "data-processor" => $options["Block"]
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
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($msg, [
         "[BlogPost.Actions]" => base64_encode($actions),
         "[BlogPost.Attachments]" => base64_encode($_BlogPost["ListItem"]["Attachments"]),
         "[BlogPost.Author]" => base64_encode($display.$verified),
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
   } elseif($searchType == "BL") {
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
       $_Query = "SELECT * FROM Links
                           WHERE Link_ID=$:ID
       ";
       $sql->query($_Query, [
        ":ID" => $value
       ]);
       $sql = $sql->single();
       foreach($sql as $sql) {
        $de = $sql["Link_Description"] ?? "No Description";
        $title = $sql["Link_Title"] ?? "Untitled";
        $vi = $this->core->Element(["button", "Visit <em>$title</em>", [
         "class" => "v2 v2w",
         "onclick" => "W('$value', '_blank');"
        ]]);
       }
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
   } elseif($searchType == "BLG") {
    $_Query = "SELECT B.*, M.* FROM Blogs B
                        JOIN Members M
                        ON M.Member_Username=B.Blog_Username
                        WHERE B.Blog_Description LIKE :Search OR
                                      B.Blog_Title LIKE :Search
                        ORDER BY B.Blog_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $home = base64_encode("Blog:Home");
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Blogs", $sql["Blog_ID"]]);
     $_Blog = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Blog;".$sql["Blog_ID"])
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
   } elseif($searchType == "Bulletins") {
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
      $verified = $member["Verified"] ?? 0;
      $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
      array_push($msg, [
       "[Bulletin.Date]" => base64_encode($this->core->TimeAgo($value["Sent"])),
       "[Bulletin.From]" => base64_encode($_Member["ListItem"]["Title"].$verified),
       "[Bulletin.ID]" => base64_encode($key),
       "[Bulletin.Message]" => base64_encode($this->core->RenderView($message)),
       "[Bulletin.Options]" => base64_encode($this->core->RenderView($options)),
       "[Bulletin.Picture]" => base64_encode($_Member["ListItem"]["Options"]["ProfilePicture"])
      ]);
     }
    }
   } elseif($searchType == "CA" || $searchType == "PR") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e7829132e382ee4ab843f23685a123cf");
    $_Query = "SELECT A.*, M.* FROM Articles A
                        JOIN Members M
                        ON M.Member_Username=A.Article_Username
                        WHERE A.Article_Body LIKE :Search OR
                                      A.Article_Description LIKE :Search OR
                                      A.Article_Title LIKE :Search
                        ORDER BY A.Article_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Pages", $sql["Article_ID"]]);
     $_Article = $this->core->GetContentData([
      "BackTo" => $b2,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Page;".$sql["Article_ID"]),
      "ParentPage" => $lpg
     ]);
     if($_Article["Empty"] == 0) {
      $article = $_Article["DataModel"];
      $i++;
      $nsfw = $article["NSFW"] ?? 0;
      $t = ($article["UN"] == $you) ? $y : $this->core->Member($article["UN"]);
      $cat = $article["Category"] ?? "";
      $cms = $this->core->Data("Get", ["cms", md5($article["UN"])]) ?? [];
      $ck = ($article["Category"] == $searchType) ? 1 : 0;
      $ck2 = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck3 = (($searchType == "CA" && $article["Category"] == "CA") || ($searchType == "PR" && $article["Category"] == "PR")) ? 1 : 0;
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
   } elseif($searchType == "CART") {
    $accessCode = "Accepted";
    $newCartList = [];
    $now = $this->core->timestamp;
    $extension = $this->core->Extension("dea3da71b28244bf7cf84e276d5d1cba");
    $shop = $data["ID"] ?? md5($this->core->ShopID);
    $username = $data["Username"] ?? base64_encode($this->core->ShopID);
    $products = $y["Shopping"]["Cart"][$shop] ?? [];
    $products = $products["Products"] ?? [];
    foreach($products as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Products", $key]);;
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
        "[Product.ID]" => base64_encode($key),
        "[Product.Title]" => base64_encode($_Product["ListItem"]["Title"]),
        "[Product.Remove]" => base64_encode(base64_encode("v=".base64_encode("Cart:SaveRemove")."&Product=$key&Shop=$shop"))
       ]);
      }
     }
    }
    $y["Shopping"]["Cart"][$shop]["Products"] = $newCartList;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
   } elseif($searchType == "Chat") {
    $accessCode = "Accepted";
    $integrated = $data["Integrated"] ?? 0;
    $extension = $this->core->Extension("343f78d13872e3b4e2ac0ba587ff2910");
    if($notAnon == 1) {
     $_Query = "SELECT C.*, M.* FROM Chat C
                         JOIN Members M
                         ON M.Member_Username=C.Chat_Username
                         WHERE C.Chat_Description LIKE :Search OR
                                       C.Chat_Title LIKE :Search
                         ORDER BY C.Chat_Created DESC
                         LIMIT $limit
                         OFFSET $offset
     ";
     $extension = "343f78d13872e3b4e2ac0ba587ff2910";
     $extension = ($integrated == 0) ? "183d39e5527b3af3e7652181a0e36e25" : $extension;
     $extension = $this->core->Extension($extension);
     $sql->query($_Query, [
      ":Search" => $querysql,
      ":Username" => $you
     ]);
     $sql = $sql->set();
     foreach($sql as $sql) {
      $bl = $this->core->CheckBlocked([$y, "Group Chats", $sql["Chat_ID"]]);
      $_Chat = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Chat;".$sql["Chat_ID"]),
       "Integrated" => $integrated
      ]);
      if(!in_array($info["Chat_ID"], $this->core->RestrictedIDs) && $_Chat["Empty"] == 0) {
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
         $displayName = $chat["Title"] ?? "Untitled";
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
   } elseif($searchType == "Congress") {
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
   } elseif($searchType == "CongressionalBallot") {
    $accessCode = "Accepted";
    $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]);
    $candidates = $ballot["Candidates"] ?? [];
    $chamber = $data["Chamber"] ?? "House";
    $extension = $this->core->Extension("633ddf914ed8a2e2aa7e023471ec83b2");
    $na = "No Candidates for the $chamber";
    $registeredVotes = $ballot["RegisteredVotes"] ?? [];
    if(($chamber == "House" || $chamber == "Senate") && $notAnon == 1) {
     foreach($candidates as $member => $info) {
      $candidateChamber = $info["Chamber"] ?? $chamber;
      $_Member = $this->core->GetContentData([
       "ID" => base64_encode("Member;".md5($member))
      ]);
      if($_Member["Empty"] == 0 && $chamber == $candidateChamber && $member != $you) {
       $member = $_Member["DataModel"];
       $displayName = $member["Personal"]["DisplayName"];
       $memberID = $member["Login"]["Username"];
       $options = $_Member["ListItem"]["Options"];
       $action = (empty($registeredVotes[$you])) ? $this->core->Element([
        "button", "Vote for $displayName", [
         "class" => "UpdateContent v2 v2w",
         "data-container" => ".VoteFor".md5($memberID),
         "data-view" => base64_encode("v=".base64_encode("Congress:VoteForCandidate")."&Candidate=".base64_encode($memberID)."&Chamber=".base64_encode($chamber))
        ]
       ]) : "";
       $voteCount = $info["Votes"] ?? 0;
       array_push($msg, [
        "[Tile.Action]" => base64_encode($this->core->Element([
         "div", $action, ["class" => "VoteFor".md5($memberID)]
        ]).$this->core->Element(["button", "View $displayName's Profile", [
         "class" => "OpenCard v2 v2w",
         "data-view" => $options["View"]
        ]])),
        "[Tile.Data]" => base64_encode($this->core->Element([
         "h4", number_format($voteCount)." members have cast their vote for $displayName to join the $candidateChamber."
        ])),
        "[Tile.Header]" => base64_encode($displayName)
       ]);
      }
     }
    }
   } elseif($searchType == "CongressionalStaffHouse" || $searchType == "CongressionalStaffSenate") {
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
       $t = ($member == $you) ? $y : $this->core->Member($member);
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
   } elseif($searchType == "Contacts") {
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
   } elseif($searchType == "ContactsProfileList") {
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
   } elseif($searchType == "ContactsRequests") {
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
   } elseif($searchType == "Contributors") {
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
     } elseif($type == "BlogPost") {
      $post = $this->core->Data("Get", ["bp", $id]) ?? [];
      $contributors = $post["Contributors"] ?? [];
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
        } elseif($type == "BlogPost") {
         $ck2 = ($post["UN"] == $you || $admin == 1) ? 1 : 0;
         $ck2 = ($ck2 == 1 && $member != $you) ? 1 : 0;
         if($ck == 1 || $ck2 == 1) {
          $ck = ($post["UN"] != $member && $post["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($post["ID"]);
          $mbr = base64_encode($them);
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
   } elseif($searchType == "CS1") {
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
   } elseif($searchType == "DC") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e9f34ca1985c166bf7aa73116a745e92");
    if($notAnon == 1) {
     $x = $this->core->Data("Get", ["dc", md5($you)]) ?? [];
     foreach($x as $key => $value) {
      $viewData = json_encode([
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($key),
       "v" => base64_encode("DiscountCode:Purge")
      ], true);
      $options = $this->core->Element(["button", "Delete", [
       "class" => "A OpenDialog v2",
       "data-view" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData))
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
   } elseif($searchType == "Feedback") {
    $_Query = "SELECT F.*, M.* FROM Feedback F
                        JOIN Members M
                        ON M.Member_Username=F.Feedback_Username
                        WHERE F.Feedback_Message LIKE :Search OR
                                      F.Feedback_ParaphrasedQuestion LIKE :Search OR
                                      F.Feedback_Subject LIKE :Search
                        ORDER BY F.Feedback_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $now = $this->core->timestamp;
    $extension = $this->core->Extension("e7c4e4ed0a59537ffd00a2b452694750");
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $feedback = $this->core->Data("Get", ["feedback", $sql["Feedback_ID"]]) ?? [];
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
   } elseif($searchType == "Forums") {
    $_Query = "SELECT F.*, M.* FROM Forums F
                        JOIN Members M
                        ON M.Member_Username=F.Forum_Username
                        WHERE F.Forum_Description LIKE :Search OR
                                      F.Forum_Title LIKE :Search
                        ORDER BY F.Forum_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Forums", $sql["Forum_ID"]]);
     $_Forum = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Forum;".$sql["Forum_ID"])
     ]);
     if(!in_array($sql["Forum_ID"], $this->core->RestrictedIDs) && $_Forum["Empty"] == 0) {
      $active = 0;
      $forum = $_Forum["DataModel"];
      $manifest = $this->core->Data("Get", ["pfmanifest", $sql["Forum_ID"]]) ?? [];
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
   } elseif($searchType == "Forums-Admin") {
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
   } elseif($searchType == "Forums-Posts") {
    $accessCode = "Accepted";
    $active = 0;
    $admin = 0;
    $extension = $this->core->Extension("150dcee8ecbe0e324a47a8b5f3886edf");
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
       $passPhrase = $post["PassPhrase"] ?? "";
       if($bl == 0 && ($ck2 == 1 && $ck3 == 1) && $illegal == 0) {
        $bl = $this->core->CheckBlocked([$y, "Forum Posts", $id]);
        $body = (empty($passPhrase)) ? $_ForumPost["ListItem"]["Body"] : $this->ContentIsProtected;
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
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($msg, [
         "[ForumPost.Actions]" => base64_encode($actions),
         "[ForumPost.Attachments]" => base64_encode($_ForumPost["ListItem"]["Attachments"]),
         "[ForumPost.Body]" => base64_encode($body),
         "[ForumPost.Comment]" => base64_encode($options["View"]),
         "[ForumPost.Created]" => base64_encode($this->core->TimeAgo($post["Created"])),
         "[ForumPost.ID]" => base64_encode($value),
         "[ForumPost.MemberRole]" => base64_encode($memberRole),
         "[ForumPost.Modified]" => base64_encode($_ForumPost["ListItem"]["Modified"]),
         "[ForumPost.Notes]" => base64_encode($options["Notes"]),
         "[ForumPost.OriginalPoster]" => base64_encode($display.$verified),
         "[ForumPost.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[ForumPost.Title]" => base64_encode($_ForumPost["ListItem"]["Title"]),
         "[ForumPost.Votes]" => base64_encode($options["Vote"])
        ]);
       }
      }
     }
    }
   } elseif($searchType == "Forums-Topic") {
    $accessCode = "Accepted";
    $active = 0;
    $admin = 0;
    $extension = $this->core->Extension("150dcee8ecbe0e324a47a8b5f3886edf");
    $forumID = $data["Forum"] ?? "";
    $manifest = $this->core->Data("Get", ["pfmanifest", $forumID]) ?? [];
    foreach($manifest as $member => $role) {
     if($active == 0 && $member == $you) {
      $active = 0;
      if($admin == 0 && $role == "Admin") {
       $admin++;
      }
     }
    }
    $topicID = $data["Topic"] ?? "";
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => 0,
     "ID" => base64_encode("Forum;$forumID")
    ]);
    if($_Forum["Empty"] == 0) {
     $forum = $_Forum["DataModel"];
     $now = $this->core->timestamp;
     $topics = $forum["Topics"] ?? [];
     $topic = $topics[$topicID] ?? [];
     $posts = $topic["Posts"] ?? [];
     foreach($posts as $key => $postID) {
      $bl = $this->core->CheckBlocked([$y, "Forum Posts", $postID]);
      $_ForumPost = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("ForumPost;$forumID;$postID")
      ]);
      if($_ForumPost["Empty"] == 0 && $i <= 5) {
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
       $passPhrase = $post["PassPhrase"] ?? "";
       if($bl == 0 && ($ck2 == 1 && $ck3 == 1) && $illegal == 0) {
        $bl = $this->core->CheckBlocked([$y, "Forum Posts", $postID]);
        $body = (empty($passPhrase)) ? $_ForumPost["ListItem"]["Body"] : $this->ContentIsProtected;
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
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($msg, [
         "[ForumPost.Actions]" => base64_encode($actions),
         "[ForumPost.Attachments]" => base64_encode($_ForumPost["ListItem"]["Attachments"]),
         "[ForumPost.Body]" => base64_encode($body),
         "[ForumPost.Comment]" => base64_encode($options["View"]),
         "[ForumPost.Created]" => base64_encode($this->core->TimeAgo($post["Created"])),
         "[ForumPost.ID]" => base64_encode($postID),
         "[ForumPost.MemberRole]" => base64_encode($memberRole),
         "[ForumPost.Modified]" => base64_encode($_ForumPost["ListItem"]["Modified"]),
         "[ForumPost.Notes]" => base64_encode($options["Notes"]),
         "[ForumPost.OriginalPoster]" => base64_encode($display.$verified),
         "[ForumPost.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[ForumPost.Title]" => base64_encode($_ForumPost["ListItem"]["Title"]),
         "[ForumPost.Votes]" => base64_encode($options["Vote"])
        ]);
       }
      }
     }
    }
   } elseif($searchType == "Forums-Topics") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("099d6de4214f55e68ea49395a63b5e4d");
    $forumID = $data["Forum"] ?? "";
    $_Forum = $this->core->GetContentData([
     "Blacklisted" => 0,
     "ID" => base64_encode("Forum;$forumID")
    ]);
    if($_Forum["Empty"] == 0) {
     $forum = $_Forum["DataModel"];
     $now = $this->core->timestamp;
     $topics = $forum["Topics"] ?? [];
     foreach($topics as $topicID => $info) {
      $check = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $info["NSFW"] == 0) ? 1 : 0;
      if($check == 1) {
       $created = $info["Created"] ?? $now;
       $i = 0;
       $modified = $info["Modified"] ?? $this->core->TimeAgo($now);
       $posts = array_reverse($info["Posts"]);
       $postList = "";
       foreach($posts as $key => $post) {
        $bl = $this->core->CheckBlocked([$y, "Forum Posts", $post]);
        $_ForumPost = $this->core->GetContentData([
         "Blacklisted" => $bl,
         "ID" => base64_encode("ForumPost;$forumID;$post")
        ]);
        if($_ForumPost["Empty"] == 0 && $i < 5) {
         $i++;
         $post = $_ForumPost["DataModel"];
         $postList .= $this->core->Element([
          "div", $this->core->Element([
           "h4", $post["Title"]
          ]).$this->core->Element([
           "p", $this->core->Excerpt(htmlentities($post["Body"]))
          ]), ["class" => "Frosted Medium Rounded"]
         ]);
        }
       }
       array_push($msg, [
        "[Forum.ID]" => base64_encode($forumID),
        "[Topic.Created]" => base64_encode($created),
        "[Topic.Description]" => base64_encode($info["Description"]),
        "[Topic.LatestPosts]" => base64_encode($postList),
        "[Topic.Modified]" => base64_encode($modified),
        "[Topic.PostCount]" => base64_encode($this->core->ShortNumber(count($posts))),
        "[Topic.Title]" => base64_encode($info["Title"]),
        "[Topic.View]" => base64_encode(base64_encode("v=".base64_encode("Forum:Topic")."&Forum=".base64_encode($forumID)."&Topic=".base64_encode($topicID)))
       ]);
      }
     }
    }
   } elseif($searchType == "Links") {
    $_Query = "SELECT * FROM Links
                        WHERE Link_Description LIKE :Search OR
                                      Link_Keywords LIKE :Search OR
                                      Link_ID LIKE :Search OR
                                      Link_Title LIKE :Search
                        ORDER BY Link_Title DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $extension = $this->core->Extension("aacfffd7976e2702d91a5c7084471ebc");
    $extension = $this->core->Element([
     "div", $extension, ["class" => "FrostedBright Rounded"]
    ]);
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $icon = parse_url($sql["Link_ID"], PHP_URL_SCHEME)."://".parse_url($sql["Link_ID"], PHP_URL_HOST); 
     $icon = trim($icon, "/");
     $icon = "$icon/apple-touch-icon.png";
     $iconExists = ($this->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
     $icon = ($iconExists == 0) ? $this->core->base."/apple-touch-icon.png" : $icon;
     array_push($msg, [
      "[Link.Description]" => base64_encode($sql["Link_Description"]),
      "[Link.Keywords]" => base64_encode($sql["Link_Keywords"]),
      "[Link.Icon]" => base64_encode($this->core->Element([
       "div", "<img src=\"$icon\" style=\"max-width:24em\" width=\"90%\"/>\r\n", [
        "class" => "InnerMargin"
       ]
      ])),
      "[Link.Title]" => base64_encode($sql["Link_Title"])
     ]);
    }
   } elseif($searchType == "Mainstream") {
    $accessCode = "Accepted";
    $edit = base64_encode("StatusUpdate:Edit");
    $attlv = base64_encode("LiveView:InlineMossaic");
    $extension = $this->core->Extension("18bc18d5df4b3516c473b82823782657");
    $_Query = "SELECT M.*, U.* FROM StatusUpdates U
                        JOIN Members M
                        ON M.Member_Username=U.StatusUpdate_Username
                        WHERE (U.StatusUpdate_Body LIKE :Body OR
                                      U.StatusUpdate_Username LIKE :Username)
                        AND U.StatusUpdate_Privacy=:Privacy
                        ORDER BY U.StatusUpdate_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $sql->query($_Query, [
     ":Body" => $querysql,
     ":Privacy" => md5("Public"),
     ":Username" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $sql["StatusUpdate_ID"]]);
     $_StatusUpdate = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("StatusUpdate;".$sql["StatusUpdate_ID"])
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
       $passPhrase = $update["PassPhrase"] ?? "";
       if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
        $body = (empty($passPhrase)) ? $_StatusUpdate["ListItem"]["Body"] : $this->ContentIsProtected;
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
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($msg, [
         "[StatusUpdate.Attachments]" => base64_encode($_StatusUpdate["ListItem"]["Attachments"]),
         "[StatusUpdate.Body]" => base64_encode($body),
         "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($update["Created"])),
         "[StatusUpdate.DT]" => base64_encode($options["View"]),
         "[StatusUpdate.Edit]" => base64_encode($edit),
         "[StatusUpdate.ID]" => base64_encode($sql["StatusUpdate_ID"]),
         "[StatusUpdate.Modified]" => base64_encode($_StatusUpdate["ListItem"]["Modified"]),
         "[StatusUpdate.Notes]" => base64_encode($options["Notes"]),
         "[StatusUpdate.OriginalPoster]" => base64_encode($display.$verified),
         "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[StatusUpdate.Votes]" => base64_encode($options["Vote"])
        ]);
       }
      }
     }
    }
   } elseif($searchType == "MBR") {
    $_Query = "SELECT * FROM Members
                        WHERE Member_Description LIKE :Search OR
                                      Member_DisplayName LIKE :Search OR
                                      Member_Username LIKE :Search
                        ORDER BY Member_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $home = base64_encode("Profile:Home");
    $extension = $this->core->Extension("ba17995aafb2074a28053618fb71b912");
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $members = $sql->set();
    foreach($members as $key => $info) {
     $bl = $this->core->CheckBlocked([$y, "Members", $info["Member_Username"]]);
     $_Member = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Member;".md5($info["Member_Username"]))
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
       $verified = $member["Verified"] ?? 0;
       $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
       array_push($msg, [
        "[X.LI.DisplayName]" => base64_encode($_Member["ListItem"]["Title"].$verified),
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
   } elseif($searchType == "MBR-ALB") {
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
   } elseif($searchType == "MBR-BLG") {
    $accessCode = "Accepted";
    $home = base64_encode("Blog:Home");
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    if($notAnon == 1) {
     $_Query = "SELECT B.*, M.* FROM Blogs B
                         JOIN Members M
                         ON M.Member_Username=B.Blog_Username
                         WHERE (B.Blog_Description LIKE :Search OR
                                       B.Blog_Title LIKE :Search)
                         AND B.Blog_Username=:Username
                         ORDER BY B.Blog_Created DESC
                         LIMIT $limit
                         OFFSET $offset
     ";
     $sql->query($_Query, [
      ":Search" => $querysql,
      ":Username" => $you
     ]);
     $sql = $sql->set();
     foreach($sql as $sql) {
      $bl = $this->core->CheckBlocked([$y, "Blogs", $sql["Blog_ID"]]);
      $_Blog = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Blog;".$sql["Blog_ID"])
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
   } elseif($searchType == "MBR-CA" || $searchType == "MBR-JE") {
    $accessCode = "Accepted";
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $bl = $this->core->CheckBlocked([$t, "Members", $you]);
    $extension = $this->core->Extension("90bfbfb86908fdc401c79329bedd7df5");
    $_Query = "SELECT A.*, M.* FROM Articles A
                        JOIN Members M
                        ON M.Member_Username=A.Article_Username
                        WHERE (A.Article_Body LIKE :Search OR
                                      A.Article_Description LIKE :Search OR
                                      A.Article_Title LIKE :Search)
                        AND A.Article_Username=:Username
                        ORDER BY A.Article_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $sql->query($_Query, [
     ":Search" => $querysql,
     ":Username" => $t["Login"]["Username"]
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]) ?? [];
     $backTo = ($t["Login"]["Username"] == $you) ? "Your Profile" : $t["Personal"]["DisplayName"]."'s Profile";
     $_Article = $this->core->GetContentData([
      "BackTo" => $backTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Page;".$sql["Article_ID"]),
      "ParentPage" => $lpg
     ]);
     if($_Article["Empty"] == 0) {
      $options = $_Article["ListItem"]["Options"];
      $searchType = str_replace("MBR-", "", $searchType);
      $article = $_Article["DataModel"];
      $illegal = $article["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $theirPrivacy = $t["Privacy"];
      $privacy = $theirPrivacy["Profile"];
      $privacy = ($searchType == "CA") ? $theirPrivacy["Contributions"] : $privacy;
      $privacy = ($searchType == "JE") ? $theirPrivacy["Journal"] : $privacy;
      $ck = ($article["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $ck2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $privacy,
       "UN" => $article["UN"],
       "Y" => $you
      ]);
      $ck3 = ($illegal == 0 && $article["Category"] == $searchType) ? 1 : 0;
      $ck = ($ck == 1 && $ck2 == 1 && $ck3 == 1) ? 1 : 0;
      $ck2 = ($bl == 0 || $t["Login"]["Username"] == $you) ? 1 : 0;
      if($ck == 1 && $ck2 == 1) {
       array_push($msg, [
        "[Article.Subtitle]" => base64_encode("Posted by ".$t["Personal"]["DisplayName"]." ".$this->core->TimeAgo($article["Created"])."."),
        "[Article.Description]" => base64_encode($_Article["ListItem"]["Description"]),
        "[Article.ParentPage]" => base64_encode($lpg),
        "[Article.Title]" => base64_encode($_Article["ListItem"]["Title"]),
        "[Article.ViewPage]" => base64_encode($options["View"])
       ]);
      }
     }
    }
   } elseif($searchType == "MBR-Chat" || $searchType == "MBR-GroupChat") {
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
      $_Query = "SELECT C.*, M.* FROM Chat C
                          JOIN Members M
                          ON M.Member_Username=C.Chat_Username
                          WHERE (C.Chat_Description LIKE :Search OR
                                        C.Chat_Title LIKE :Search)
                          AND C.Chat_Username=:Username
                          ORDER BY C.Chat_Created DESC
                          LIMIT $limit
                          OFFSET $offset
      ";
      $sql->query($_Query, [
       ":Search" => $querysql,
       ":Username" => $you
      ]);
      $sql = $sql->set();
      foreach($sql as $sql) {
       $active = 0;
       $bl = $this->core->CheckBlocked([$y, "Group Chats", $sql["Chat_ID"]]);
       $_Chat = $this->core->GetContentData([
        "Blacklisted" => $bl,
        "ID" => base64_encode("Chat;".$sql["Chat_ID"]),
        "Integrated" => $integrated
       ]);
       if($_Chat["Empty"] == 0) {
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
         $displayName = $chat["Title"] ?? "Untitled";
         $t = $this->core->Member($this->core->ID);
         $verified = $t["Verified"] ?? 0;
         $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
         array_push($msg, [
          "[Chat.DisplayName]" => base64_encode($displayName.$verified),
          "[Chat.Online]" => base64_encode(""),
          "[Chat.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%")),
          "[Chat.View]" => base64_encode($_Chat["ListItem"]["Options"]["View"])
         ]);
        }
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
   } elseif($searchType == "MBR-Forums") {
    $_Query = "SELECT F.*, M.* FROM Forums F
                        JOIN Members M
                        ON M.Member_Username=F.Forum_Username
                        WHERE (F.Forum_Description LIKE :Search OR
                                      F.Forum_Title LIKE :Search)
                        AND F.Forum_Username=:Username
                        ORDER BY F.Forum_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $home = base64_encode("Forum:Home");
    $extension = $this->core->Extension("ed27ee7ba73f34ead6be92293b99f844");
    $sql->query($_Query, [
     ":Search" => $querysql,
     ":Username" => $you
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Forums", $sql["Forum_ID"]]);;
     $_Forum = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Forum;".$sql["Forum_ID"])
     ]);
     if($_Forum["Empty"] == 0) {
      $active = 0;
      $forum = $_Forum["DataModel"];
      $illegal = $forum["Illegal"] ?? 0;
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
   } elseif($searchType == "MBR-LLP") {
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
   } elseif($searchType == "MBR-Polls") {
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
       $extension = $this->core->Element([
        "div", $extension, ["class" => "FrostedBright Poll$value Rounded"]
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
   } elseif($searchType == "MBR-SU") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("18bc18d5df4b3516c473b82823782657");
    $_Query = "SELECT M.*, U.* FROM StatusUpdates U
                        JOIN Members M
                        ON M.Member_Username=U.StatusUpdate_Username
                        WHERE U.StatusUpdate_Body LIKE :Body
                        AND (U.StatusUpdate_To=:Username OR
                                 U.StatusUpdate_Username=:Username)
                        ORDER BY U.StatusUpdate_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $sql->query($_Query, [
     ":Body" => $querysql,
     ":Username" => base64_decode($data["UN"])
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $id = $value["UpdateID"] ?? "";
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $sql["StatusUpdate_ID"]]);
     $_StatusUpdate = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("StatusUpdate;".$sql["StatusUpdate_ID"])
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
       $passPhrase = $update["PassPhrase"] ?? "";
       if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
        $body = (empty($passPhrase)) ? $_StatusUpdate["ListItem"]["Body"] : $this->ContentIsProtected;
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
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($msg, [
         "[StatusUpdate.Attachments]" => base64_encode($_StatusUpdate["ListItem"]["Attachments"]),
         "[StatusUpdate.Body]" => base64_encode($body),
         "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($update["Created"])),
         "[StatusUpdate.DT]" => base64_encode($options["View"]),
         "[StatusUpdate.Edit]" => base64_encode($edit),
         "[StatusUpdate.ID]" => base64_encode($sql["StatusUpdate_ID"]),
         "[StatusUpdate.Modified]" => base64_encode($_StatusUpdate["ListItem"]["Modified"]),
         "[StatusUpdate.Notes]" => base64_encode($options["Notes"]),
         "[StatusUpdate.OriginalPoster]" => base64_encode($display.$verified),
         "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
         "[StatusUpdate.Votes]" => base64_encode($options["Vote"])
        ]);
       }
      }
     }
    }
   } elseif($searchType == "MBR-XFS") {
    $_Query = "SELECT F.*, M.* FROM :Database F
                        JOIN Members M
                        ON M.Member_Username=F.Media_Username
                        WHERE (F.Media_Description LIKE :Search OR
                                      F.Media_Title LIKE :Search)
                        AND F.Media_Username=:Username
                        ORDER BY F.Media_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $albumID = $data["AID"] ?? md5("unsorted");
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $extension = $this->core->Extension("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $fileSystem = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]) ?? [];
    $database = ($t["Login"]["Username"] == $this->core->ID) ? "CoreMedia" : "Media";
    $sql->query($_Query, [
     ":Database" => $database,
     ":Search" => $querysql,
     ":Username" => $t["Login"]["Username"]
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $attachmentID = base64_encode($sql["Media_Username"]."-".$sql["Media_ID"]);
     $bl = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
     $_File = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("File;".$sql["Media_Username"].";".$sql["Media_ID"])
     ]);
     $file = $_File["DataModel"];
     if($_File["Empty"] == 0 && $bl == 0 && $albumID == $file["AID"]) {
      $options = $_File["ListItem"]["Options"];
      $source = $this->core->GetSourceFromExtension([$t["Login"]["Username"], $file]);
      array_push($msg, [
       "[File.CoverPhoto]" => base64_encode($source),
       "[File.Title]" => base64_encode($file["Title"]),
       "[File.View]" => base64_encode("Files;".$options["View"])
      ]);
     }
    }
   } elseif($searchType == "Media") {
    $_Query = "SELECT F.*, M.* FROM Media F
                        JOIN Members M
                        ON M.Member_Username=F.Media_Username
                        WHERE F.Media_Description LIKE :Search OR
                                      F.Media_Title LIKE :Search OR
                                      F.Media_Username LIKE :Search
                        ORDER BY F.Media_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $attachmentID = base64_encode($sql["Media_Username"]."-".$sql["Media_ID"]);
     $bl = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
     $_File = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("File;".$sql["Media_Username"].";".$sql["Media_ID"])
     ]);
     if($_File["Empty"] == 0 && $bl == 0) {
      $file = $_File["DataModel"];
      $options = $_File["ListItem"]["Options"];
      $source = $this->core->GetSourceFromExtension([
       $sql["Media_Username"],
       $file
      ]);
      array_push($msg, [
       "[File.CoverPhoto]" => base64_encode($source),
       "[File.Title]" => base64_encode($file["Title"]),
       "[File.View]" => base64_encode("Files;".$options["View"])
      ]);
     }
    }
   } elseif($searchType == "Polls") {
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
        "div", $extension, ["class" => "FrostedBright Poll$value Rounded"]
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
   } elseif($searchType == "Products") {
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
          "Owner" => $them
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
   } elseif($searchType == "SHOP") {
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
         "[Shop.CoverPhoto]" => base64_encode($_Shop["ListItem"]["CoverPhoto"]),
         "[Shop.Description]" => base64_encode($_Shop["ListItem"]["Description"]),
         "[Shop.ProfilePicture]" => base64_encode($this->core->ProfilePicture($t, "margin:5%;width:90%")),
         "[Shop.Title]" => base64_encode($_Shop["ListItem"]["Title"]),
         "[Shop.View]" => base64_encode($_Shop["ListItem"]["Options"]["View"]),
        ]);
       }
      }
     }
    }
   } elseif($searchType == "SHOP-InvoicePresets") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("e9f34ca1985c166bf7aa73116a745e92");
    $shop = $this->core->Data("Get", ["shop", $data["Shop"]]) ?? [];
    $invoicePresets = $shop["InvoicePresets"] ?? [];
    foreach($invoicePresets as $key => $value) {
     $preset = $this->core->Data("Get", ["invoice-preset", $value]) ?? [];
     $viewData = json_encode([
      "SecureKey" => base64_encode($y["Login"]["PIN"]),
      "ID" => base64_encode($value),
      "Shop" => base64_encode($data["Shop"]),
      "v" => base64_encode("Invoice:PurgePreset")
     ], true);
     $options = $this->core->Element(["button", "Delete", [
      "class" => "A OpenDialog v2",
      "data-view" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData))
     ]]);
     if(!empty($preset)) {
      array_push($msg, [
       "[ListItem.Description]" => base64_encode("A service currently on offer by ".$shop["Title"]),
       "[ListItem.Options]" => base64_encode($options),
       "[ListItem.Title]" => base64_encode($preset["Title"])
      ]);
     }
    }
   } elseif($searchType == "SHOP-Invoices") {
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
   } elseif($searchType == "SHOP-Orders") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("504e2a25db677d0b782d977f7b36ff30");
    $purchaseOrders = $this->core->Data("Get", ["po", md5($you)]) ?? [];
    foreach($purchaseOrders as $key => $value) {
     $member = $this->core->Member($value["UN"]);
     if(!empty($member["Login"])) {
      $complete = ($value["Complete"] == 0) ? $this->core->Element(["button", "Mark as Complete", [
       "class" => "BBB CompleteOrder v2 v2w",
       "data-u" => base64_encode("v=".base64_encode("Shop:CompleteOrder")."&ID=".base64_encode($key))
      ]]) : "";
      array_push($msg, [
       "[X.LI.Order.Complete]" => base64_encode($complete),
       "[X.LI.Order.Instructions]" => $value["Instructions"],
       "[X.LI.Order.ProductID]" => base64_encode($value["ProductID"]),
       "[X.LI.Order.ProfilePicture]" => base64_encode($this->core->ProfilePicture($member, "margin:5%;width:90%")),
       "[X.LI.Order.Quantity]" => base64_encode($value["QTY"]),
       "[X.LI.Order.UN]" => base64_encode($value["UN"])
      ]);
     }
    }
   } elseif($searchType == "SHOP-Products") {
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
      "Owner" => $username
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
   } elseif($searchType == "StatusUpdates") {
    $accessCode = "Accepted";
    $extension = $this->core->Extension("18bc18d5df4b3516c473b82823782657");
    $_Query = "SELECT M.*, U.* FROM StatusUpdates U
                        JOIN Members M
                        ON M.Member_Username=U.StatusUpdate_Username
                        WHERE U.StatusUpdate_Body LIKE :Body OR
                                      U.StatusUpdate_Username LIKE :Username
                        ORDER BY U.StatusUpdate_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $sql->query($_Query, [
     ":Body" => $querysql,
     ":Username" => $querysql
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $sql["StatusUpdate_ID"]]);
     $_StatusUpdate = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("StatusUpdate;".$sql["StatusUpdate_ID"])
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
       $passPhrase = $update["PassPhrase"] ?? "";
        if($bl == 0 && ($ck == 1 && $ck2 == 1)) {
         $body = (empty($passPhrase)) ? $_StatusUpdate["ListItem"]["Body"] : $this->ContentIsProtected;
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
         $verified = $op["Verified"] ?? 0;
         $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
         array_push($msg, [
          "[StatusUpdate.Attachments]" => base64_encode($_StatusUpdate["ListItem"]["Attachments"]),
          "[StatusUpdate.Body]" => base64_encode($body),
          "[StatusUpdate.Created]" => base64_encode($this->core->TimeAgo($created)),
          "[StatusUpdate.DT]" => base64_encode($options["View"]),
          "[StatusUpdate.Edit]" => base64_encode($edit),
          "[StatusUpdate.ID]" => base64_encode($sql["StatusUpdate_ID"]),
          "[StatusUpdate.Modified]" => base64_encode($_StatusUpdate["ListItem"]["Modified"]),
          "[StatusUpdate.Notes]" => base64_encode($options["Notes"]),
          "[StatusUpdate.OriginalPoster]" => base64_encode($display.$verified),
          "[StatusUpdate.ProfilePicture]" => base64_encode($this->core->ProfilePicture($op, "margin:5%;width:90%")),
          "[StatusUpdate.Votes]" => base64_encode($options["Vote"])
         ]);
        }
       }
      }
     }
    }
   } elseif($searchType == "XFS") {
    $_Query = "SELECT F.*, M.* FROM :Database F
                        JOIN Members M
                        ON M.Member_Username=F.Media_Username
                        WHERE (F.Media_Description LIKE :Search OR
                                      F.Media_Title LIKE :Search)
                        AND F.Media_Username=:Username
                        ORDER BY F.Media_Created DESC
                        LIMIT $limit
                        OFFSET $offset
    ";
    $_Username = $data["UN"] ?? base64_encode("");
    $_Username = base64_decode($_Username);
    $accessCode = "Accepted";
    $added = $data["Added"] ?? "";
    $addTo = $data["AddTo"] ?? "";
    $database = ($_Username == $this->core->ID) ? "CoreMedia" : "Media";
    $extension = $this->core->Extension("e15a0735c2cb8fa2d508ee1e8a6d658d");
    $mediaType = $data["ftype"] ?? "";
    $sql->query($_Query, [
     ":Database" => $database,
     ":Search" => $querysql,
     ":Username" => $_Username
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $attachmentID = base64_encode($sql["Media_Username"]."-".$sql["Media_ID"]);
     $bl = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
     $_File = $this->core->GetContentData([
      "Added" => $added,
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("File;".$sql["Media_Username"].";".$sql["Media_ID"]),
      "ParentView" => "Files"
     ]);
     if($_File["Empty"] == 0 && $bl == 0) {
      $file = $_File["DataModel"];
      $options = $_File["ListItem"]["Options"];
      $source = $this->core->GetSourceFromExtension([$sql["Media_Username"], $file]);
      $media = [
       "[File.CoverPhoto]" => base64_encode($source),
       "[File.Title]" => base64_encode($file["Title"]),
       "[File.View]" => base64_encode("$searchType;".$options["View"])
      ];
      if(empty($mediaType)) {
       array_push($msg, $media);
      } else {
       $mediaTypes = json_decode(base64_decode($mediaType));
       foreach($mediaTypes as $mediaTypes) {
        if($this->core->CheckFileType([$file["EXT"], $mediaTypes]) == 1) {
         array_push($msg, $media);
        }
       }
      }
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
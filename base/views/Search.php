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
  function Containers(array $data): string {
   $_AddTopMargin = "0";
   $_Card = "";
   $_Commands = "";
   $_Dialog = [
    "Body" => "The List Type is missing.",
    "Header" => "Not Found"
   ];
   $_List = "v=".$this->lists;
   $_View = "";
   $data = $data["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $b2 = $data["b2"] ?? "";
   $card = $data["CARD"] ?? 0;
   $cardSearchTypes = [
    "DC",
    "SHOP-InvoicePresets",
    "SHOP-Invoices",
    "XFS"
   ];
   $header = "";
   $i = 0;
   $pub = $data["pub"] ?? 0;
   $searchType = $data["st"] ?? "";
   $parentView = $data["lPG"] ?? $searchType;
   $searchLists = $this->core->config["App"]["Search"] ?? [];
   $check = 0;
   $query = $data["query"] ?? "";
   $_List .= (!empty($addTo)) ? "&AddTo=$addTo" : "";
   $_List .= (!empty($query)) ? "&query=$query" : "";
   $_List .= (!empty($searchType)) ? "&st=$searchType" : "";
   $options = "";
   $variant = "Default";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   foreach($searchLists as $key => $info) {
    if($key == $searchType) {
     $check++;
     break;
    }
   } if($check == 1) {
    $_AccessCode = "Accepted";
    if($searchType == "ADM-LLP") {
     $header = "App Extensions";
     $searchBarText = "Extensions";
     $options =  ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Extension:Edit")."&New=1")
      ]
     ]) : "";
    } elseif($searchType == "BGP") {
     $header = "Blog Posts";
     $_List .= (!empty($data["ID"])) ? "&ID=".$data["ID"] : "";
     $searchBarText = "Posts";
    } elseif($searchType == "BL") {
     $bl = base64_decode($data["BL"]);
     $header = "$bl Blacklist";
     $_List .= (!empty($data["BL"])) ? "&BL=".$data["BL"] : "";
     $options =  ($notAnon == 1) ? $this->core->Element([
      "button", "Back to Blacklists", [
       "class" => "GoToParent v2",
       "data-type" => "Blacklists"
      ]
     ]) : "";
     $searchBarText = "$bl Blacklist";
    } elseif($searchType == "BLG") {
     $header = "Blogs";
     $_List .= "&b2=Blogs&lPG=$searchType";
     $searchBarText = "Blogs";
     $variant = "3Column";
    } elseif($searchType == "Bulletins") {
     $header = "Bulletins";
     $searchBarText = "Bulletins";
    } elseif($searchType == "CA") {
     $header = "Community Archive";
     $_List .= "&b2=".urlencode("the Archive")."&lPG=$parentView";
     $searchBarText = "Articles";
     $variant = "3Column";
    } elseif($searchType == "CART") {
     $username = $data["Username"] ?? $you;
     $shopID = md5($username);
     $shop = $this->core->Data("Get", ["shop", $shopID]);
     $_List .= "&ID=$shopID&Username=".base64_encode($username);
     $searchBarText = "".$shop["Title"];
     $variant = "Minimal";
    } elseif($searchType == "Chat") {
     $header = "Group Chats";
     $integrated = $data["Integrated"] ?? 0;
     $_List .= "&Integrated=$integrated";
     $searchBarText = "$header";
     $variant = "3Column";
    } elseif($searchType == "Congress") {
     $chamber = $data["Chamber"] ?? "";
     $header = "Content Moderation";
     $_List .= "&Chamber=$chamber";
     $searchBarText = "Content";
     $variant = "2Column";
    } elseif($searchType == "CongressionalBallot") {
     $chamber = $data["Chamber"] ?? "";
     $header = "Congressional $chamber Ballot";
     $_List .= "&Chamber=$chamber";
     $searchBarText = "Candidates";
     $variant = "3Column";
    } elseif($searchType == "CongressionalStaffHouse" || $searchType == "CongressionalStaffSenate") {
     $chamber = $data["Chamber"] ?? "";
     $_List .= "&Chamber=$chamber";
     $searchBarText = " $chamber Staff";
     $variant = "Minimal";
    } elseif($searchType == "Contacts") {
     $header = "Contact Manager";
     $searchBarText = "Contacts";
    } elseif($searchType == "ContactsProfileList") {
     $data = $this->core->FixMissing($data, ["UN"]);
     $username = base64_decode($data["UN"]);
     $check = ($username == $y["Login"]["Username"]) ? 1 : 0;
     $t = ($check == 1) ? $y : $this->core->Member($username);
     $header = ($check == 1) ? "Your Contacts" : $t["Personal"]["DisplayName"]."'s Contacts";
     $_List .= "&b2=$b2&lPG=$parentView&UN=".$data["UN"];
     $searchBarText = "Contacts";
    } elseif($searchType == "ContactsRequests") {
     $header = "Contact Requests";
     $searchBarText = "Contact Requests";
    } elseif($searchType == "Contributors") {
     $id = $data["ID"] ?? "";
     $_List .= "&ID=$id&Type=".$data["Type"];
     $searchBarText = "Contributors";
     $type = base64_decode($data["Type"]);
     $variant = "3Column";
     if($type == "Article") {
      $header = "Article Contributors";
      $id = base64_decode($id);
      $Page = $this->core->Data("Get", ["pg", $id]);
      $options = ($Page["UN"] == $you && $notAnon == 1) ? $this->core->Element([
       "button", "+", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Page:Invite")."&ID=$id")
       ]
      ]) : "";
     } elseif($type == "Blog") {
      $id = base64_decode($id);
      $blog = $this->core->Data("Get", ["blg", $id]);
      $header = "Blog Contributors";
     } elseif($type == "Forum") {
      $id = base64_decode($id);
      $forum = $this->core->Data("Get", ["pf", $id]);
      $header = "Forum Members";
      $options = ($forum["UN"] == $you && $notAnon == 1) ? $this->core->Element([
       "button", "Invite Members", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Forum:Invite")."&FID=".base64_encode($id))
       ]
      ]) : "";
     } elseif($type == "Shop") {
      $header = "Partners";
      $id = base64_decode($id);
      $shop = $this->core->Data("Get", ["shop", $id]);
      $options = ($id == md5($you) && $notAnon == 1) ? $this->core->Element([
       "button", "Hire Members", [
        "class" => "OpenCard v2",
        "data-view" => base64_encode("v=".base64_encode("Shop:EditPartner")."&new=1")
       ]
      ]) : "";
     } else {
      $header = "Contributors";
      $searchBarText = "Contributors";
     }
    } elseif($searchType == "DC") {
     $dce = base64_encode("DiscountCode:Edit");
     $header = "Discount Codes";
     $searchBarText = "Codes";
     $shopID = $data["Shop"] ?? md5($you);
     $options = ($notAnon == 1) ? $this->core->Element([
      "button", "+", [
       "class" => "OpenCard v2",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=$dce&Shop=$shopID&new=1")
      ]
     ]) : "";
     $_List .= "&Shop=$shopID";
    } elseif($searchType == "Feedback") {
     $header = "Feedback";
     $_List .= "&lPG=$parentView";
     $searchBarText = "Feedback";
    } elseif($searchType == "Forums") {
     $header = "Forums";
     $_List .= "&lPG=$parentView";
     $searchBarText = "Private and Public Forums";
     $variant = "3Column";
    } elseif($searchType == "Forums-Admin") {
     $header = "Administrators";
     $_List .= "&ID=".$data["ID"];
     $searchBarText = "Administrators";
     $variant = "3Column";
    } elseif($searchType == "Forums-Posts") {
     $forumID = $data["ID"] ?? "";
     $forumID = base64_decode($forumID);
     $forum = $this->core->Data("Get", ["pf", $forumID]);
     $header = "All Posts";
     $_List .= (!empty($forumID)) ? "&ID=$forumID" : "";
     $title = $forum["Title"] ?? "";
     $searchBarText = (!empty($title)) ? "All Posts from $title" : $header;
    } elseif($searchType == "Forums-Topic") {
     $forumID = $data["Forum"] ?? "";
     $topicID = $data["Topic"] ?? "";
     $forum = $this->core->Data("Get", ["pf", $forumID]);
     $_List .= "&Forum=$forumID&Topic=$topicID";
     $topic = $forum["Topics"][$topicID] ?? [];
     $topic = $topic["Title"] ?? "Untitled";
     $searchBarText = "Posts from $topic";
     $variant = "Minimal";
    } elseif($searchType == "Forums-Topics") {
     $forumID = $data["Forum"] ?? "";
     $forum = $this->core->Data("Get", ["pf", $forumID]);
     $_List .= "&Forum=$forumID";
     $searchBarText = "Topics from ".$forum["Title"];
     $variant = "Minimal";
    } elseif($searchType == "Knowledge") {
     $header = "Knowledge Base";
     $searchBarText = "Q&As";
     $variant = "2Column";
    } elseif($searchType == "Links") {
     $header = $searchType;
     $searchBarText = $searchType;
     $variant = "3Column";
    } elseif($searchType == "Mainstream") {
     $_AddTopMargin = ($card ==1) ? 0 : 1;
     $header = "The ".$searchType;
     $searchBarText = "the ".$searchType;
     $options = ($card == 0) ? $this->core->Element(["button", "Say Something", [
      "class" => "BBB MobileFull OpenCard v2",
      "data-encryption" => "AES",
      "data-view" => $this->core->AESencrypt("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($you))
     ]]) : "";
     //BEGIN TEMP
     $options .= ($y["Rank"] == md5("High Command")) ? $this->core->Element([
      "button", "Edit non-indexed View", [
       "class" => "MobileFull OpenCard v2",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Extension:Edit")."&ID=".base64_encode("184ada666b3eb85de07e414139a9a0dc"))
      ]
     ]) : "";
     //END TEMP
     $variant = "2Column";
    } elseif($searchType == "MBR") {
     $header = "Members";
     $searchBarText = "Members";
     $variant = "3Column";
    } elseif($searchType == "MBR-ALB") {
     $ae = base64_encode("Album:Edit");
     $username = base64_decode($data["UN"]);
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $check = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $header = ($check == 1) ? "Your Albums" : $t["Personal"]["DisplayName"]."'s Albums";
     $b2 = $b2 ?? $h;
     $b2 = urlencode($b2);
     $_List .= "&UN=".base64_encode($t["Login"]["Username"])."&b2=$b2&lPG=$parentView";
     $searchBarText = "Albums";
    } elseif($searchType == "MBR-BLG") {
     $bd = base64_encode("Authentication:DeleteBlogs");
     $be = base64_encode("Blog:Edit");
     $header = "Your Blogs";
     $_List .= "&b2=Blogs&lPG=$parentView";
     $searchBarText = "your Blogs";
    } elseif($searchType == "MBR-CA") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $check = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $header = ($check == 1) ? "Your Contributions" : $t["Personal"]["DisplayName"]."'s Contributions";
     $_List .= "&b2=$b2&lPG=$parentView&UN=".$data["UN"];
     $searchBarText = "the Archive";
    } elseif($searchType == "MBR-Chat" || $searchType == "MBR-GroupChat") {
     $group = $data["Group"] ?? 0;
     $integrated = $data["Integrated"] ?? 0;
     $oneOnOne = $data["1on1"] ?? 0;
     $header = "1:1 Chat";
     $header = ($group == 1) ? "Group Chat" : $header;
     $_List .= "&1on1=$oneOnOne&Group=$group&Integrated=$integrated";
     $searchBarText = "$header";
     $variant = "3Column";
    } elseif($searchType == "MBR-Forums") {
     $_AddTopMargin = 1;
     $header = "Your Forums";
     $_List .= "&lPG=$parentView";
     $searchBarText = "Your Private and Public Forums";
     $variant = "3Column";
    } elseif($searchType == "MBR-JE") {
     $t = $this->core->Member(base64_decode($data["UN"]));
     $check = ($t["Login"]["Username"] == $y["Login"]["Username"]) ? 1 : 0;
     $header = ($check == 1) ? "Your Journal" : $t["Personal"]["DisplayName"]."'s Journal";
     $_List .= "&b2=$b2&lPG=$parentView";
     $searchBarText = "Entries";
    } elseif($searchType == "MBR-LLP") {
     $header = "Your Articles";
     $_List .= "&b2=$b2&lPG=$parentView";
     $searchBarText = "Articles";
    } elseif($searchType == "MBR-Polls") {
     $header = "Your Polls";
     $searchBarText = "Polls";
    } elseif($searchType == "MBR-SU") {
     $t = base64_decode($data["UN"]);
     $t = ($t != $you) ? $this->core->Member($t) : $y;
     $bl = $this->core->CheckBlocked([$t, "Members", $you]);
     $cms = $this->core->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]);
     $check = ($t["Login"]["Username"] == $you) ? 1 : 0;
     $display = ($t["Login"]["Username"] == $this->core->ID) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $header = ($check == 1) ? "Your Stream" : $display."'s Stream";
     $_List .= "&UN=".base64_encode($t["Login"]["Username"]);
     $searchBarText = "Posts";
     $options = (($bl == 0 || $check == 1) && $notAnon == 1) ? $this->core->Element([
      "button", "Say Something", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&new=1&UN=".base64_encode($t["Login"]["Username"]))
      ]
     ]) : "";
     $variant = "2Column";
    } elseif($searchType == "MBR-XFS") {
     $aid = $data["AID"] ?? md5("unsorted");
     $fs = $this->core->Data("Get", ["fs", md5($you)]);
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
     $fs = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]);
     $alb = $fs["Albums"][$aid] ?? [];
     $check = $y["Subscriptions"]["XFS"]["A"] ?? 0;
     $check = ($check == 1 && $notAnon == 1) ? 1 : 0;
     $check2 = ($username == $this->core->ID && $y["Rank"] == md5("High Command")) ? 1 : 0;
     $de = $alb["Description"] ?? "";
     $display = ($check2 == 1) ? "Anonymous" : $t["Personal"]["DisplayName"];
     $header = $alb["Title"] ?? "Unsorted";
     $header = ($check2 == 1) ? "System Media Library" : $header;
     $_List .= "&AID=$aid&UN=".$data["UN"];
     $searchBarText = "$header";
     $usernamelimitedFiles = ($check == 1) ? "You have unlimited storage." : "You used $xfsUsage out of $xfsLimit.";
     $usernamelimitedFiles = ($check2 == 1) ? "No Upload Limit" : $usernamelimitedFiles;
     $check = ($check == 1 || $usage < $limit) ? 1 : 0;
     if(($check == 1 && $username == $you) || $check2 == 1) {
      $options = $this->core->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display,
       "[Album.Uploader]" => base64_encode("v=".base64_encode("File:Upload")."&AID=$aid&UN=".$t["Login"]["Username"]),
       "[Album.FStats]" => $usernamelimitedFiles
      ], $this->core->Extension("b9e1459dc1c687cebdaa9aade72c50a9")]);
     } else {
      $options = $this->core->Change([[
       "[Album.Description]" => $de,
       "[Album.Owner]" => $display
      ], $this->core->Extension("af26c6866abb335fb69327ed3963a182")]);
     }
     $variant = "Album";
    } elseif($searchType == "Media") {
     $header = "Media";
     $_List .= "&lPG=Files";
     $searchBarText = "Files";
     $variant = "3Column";
    } elseif($searchType == "Polls") {
     $header = "Polls";
     $searchBarText = "Polls";
     $variant = "3Column";
    } elseif($searchType == "PR") {
     $header = "Press Releases";
     $_List .= "&b2=".urlencode("Press Releases")."&lPG=$parentView";
     $searchBarText = "Articles";
    } elseif($searchType == "Products") {
     $header = "Products";
     $_List .= "&lPG=$parentView&st=$searchType";
     $searchBarText = "Products";
     $variant = "3Column";
    } elseif($searchType == "SHOP") {
     $header = "Artists";
     $_List .= "&lPG=$parentView&st=$searchType";
     $searchBarText = "Shops";
     $variant = "3Column";
    } elseif($searchType == "SHOP-InvoicePresets") {
     $header = "Services";
     $shop = $data["Shop"] ?? "";
     $_List .= "&Shop=$shop&st=$searchType";
     $searchBarText = "Services";
    } elseif($searchType == "SHOP-Invoices") {
     $header = "Invoices";
     $shop = $data["Shop"] ?? "";
     $_List .= "&Shop=$shop&st=$searchType";
     $searchBarText = "Invoices";
    } elseif($searchType == "SHOP-Products") {
     $username = $data["UN"] ?? base64_encode($you);
     $_List .= "&UN=$username&b2=$b2&lPG=$parentView&pub=$pub&st=$searchType";
     $searchBarText = "$b2";
     $t = base64_decode($username);
     $t = ($t == $you) ? $y :  $this->core->Member($t);
     $isArtist = $t["Subscriptions"]["Artist"]["A"] ?? 0;
     $shopID = md5($t["Login"]["Username"]);
     $shop = $this->core->Data("Get", ["shop", $shopID]);
     $check = ($t["Login"]["Username"] == $you && $notAnon == 1) ? 1 : 0;
     $options .= ($isArtist == 1 && $check == 1) ? $this->core->Element([
      "button", "Discount Codes", [
       "class" => "OpenCard v2",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Search:Containers")."&Shop=$shopID&st=DC")
      ]
     ]) : "";
     $variant = "3ColumnMinimal";
    } elseif($searchType == "SHOP-Orders") {
     $searchBarText = "Orders";
     $variant = "Minimal";
    } elseif($searchType == "StatusUpdates") {
     $header = "Status Updates";
     $searchBarText = "Updates";
     $variant = "2Column";
    } elseif($searchType == "VVA") {
     $searchBarText = "Portfolio";
     $variant = "Minimal";
    } elseif($searchType == "XFS") {
     $header = "Files";
     $parentView = $data["lPG"] ?? $searchType;
     $searchBarText = "Files";
     $variant = "3Column";
     $_List .= "&ParentView=$parentView";
     $_List .= (!empty($data["UN"])) ? "&UN=".$data["UN"] : "";
     $_List .= (!empty($data["ftype"])) ? "&ftype=".$data["ftype"] : "";
    }
    $id = $this->core->UUID("ReSearch".md5($you));
    $searchUI = $this->core->Element(["p", "No Search UI found for <em>$variant</em>."]);
    $variants = $this->core->Data("Get", ["app", md5("SearchUI")]);
    for($i = 0; $i < count($variants); $i++) {
     $info = $variants[$i] ?? [];
     if(!empty($info["UI"]) && $info["ID"] == $variant) {
      $searchUI = base64_decode($info["UI"]);
      break;
     }
    }
    $_Card = "";
    $_Commands = [
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.LightSearch$id')"
      ]
     ]
    ];
    $_Dialog = "";
    $_View = [
     "ChangeData" => [
      "[Search.Header]" => $header,
      "[Search.List]" => base64_encode($_List),
      "[Search.Options]" => $options,
      "[Search.ParentPage]" => $parentView,
      "[Search.Text]" => $searchBarText
     ],
     "Extension" => $this->core->AESencrypt($this->core->Change([[
      "[Search.ID]" => $id,
      "[Search.UI]" => $this->core->Change([[
       "[Search.ID]" => $id
      ], $searchUI])
     ], $this->core->Extension("caa64184e321777584508a3e89bd6aea")]))
    ];
   } if(in_array($searchType, $cardSearchTypes) || $card == 1) {
    $_Card = [
     "Front" => $_View
    ];
    $_Dialog = "";
    $_View = "";
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => $_AddTopMargin,
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Links(array $data): string {
   $_AccessCode = "Denied";
   $_Commands = "";
   $_Dialog = [
    "Body" => "Unknown."
   ];
   $_ResponseType = "N/A";
   $_View = "";
   $data = $data["Data"] ?? [];
   $add = $data["Add"] ?? "";
   $preview = $data["Preview"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if($this->core->ID == $you) {
    $_Dialog = [
     "Body" => "You must sign in to continue.",
     "Header" => "Forbidden"
    ];
   } elseif(!empty($add)) {
    $data = $this->core->DecodeBridgeData($data);
    $add = $data["Add"] ?? 0;
    $link = $data["Link"] ?? "";
    if(!empty($link) && $add == 1) {
     $_Dialog = [
      "Body" => "An invalid URL was supplied."
     ];
     $check = (filter_var($link, FILTER_VALIDATE_URL) !== false) ? 1 : 0;
     $check2 = (strpos($link, "http") !== false) ? 1 : 0;
     $check3 = (strpos($link, "https") !== false) ? 1 : 0;
     if($check == 1 && ($check2 == 1 || $check3 == 1)) {
      $_Dialog = [
       "Body" => "No data was found."
      ];
      $curl = curl_init($link);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
      $linkData = curl_exec($curl);
      curl_close($curl);
      if(!empty($linkData)) {
       $_AccessCode = "Accepted";
       $_Dialog = "";
       $_ResponseType = "ReplaceContent";
       $dom = new DOMDocument();
       libxml_use_internal_errors(true);
       $dom->loadHTML($linkData);
       libxml_use_internal_errors(false);
       $icon = parse_url($link, PHP_URL_SCHEME)."://".parse_url($link, PHP_URL_HOST); 
       $icon = trim($icon, "/");
       $icon = "$icon/apple-touch-icon.png";
       $iconExists = ($this->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
       $tags = get_meta_tags($link);
       $description = $tags["description"] ?? "No Description";
       $keywords = $tags["keywords"] ?? "None";
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
       $_View = [
        "ChangeData" => [],
        "Extension" => $this->core->AESencrypt($this->core->Element([
         "h1", "Done", ["class" => "CenterText"]
        ]).$this->core->Element([
         "p", "Your Link <em>$link</em> is now listed!", ["class" => "CenterText"]
        ]))
       ];
      }
     }
    }
   } elseif($preview == 1) {
    $_AccessCode = "Accepted";
    $_Dialog = "";
    $_View = [
     "ChangeData" => [],
     "ExtensionID" => "e057199ee0c4a5f556a30cb990521485"
    ];
    $link = $data["Link"] ?? base64_encode("");
    $link = base64_decode($link);
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
       $tags = get_meta_tags($link);
       $description = $tags["description"] ?? "No Description";
       $keywords = $tags["keywords"] ?? "No Keywords";
       $title = $dom->getElementsByTagName("title")->item(0)->nodeValue ?? "No Title";
       $_View = $this->core->Extension("aacfffd7976e2702d91a5c7084471ebc");
       $_View .= $this->core->Element(["button", "Save", [
        "class" => "SendData v2 v2w",
        "data-form" => ".AddLink",
        "data-processor" => base64_encode("v=".base64_encode("Search:Links"))
       ]]);
       $_View = [
        "ChangeData" => [
         "[Link.Description]" => $description,
         "[Link.Keywords]" => $keywords,
         "[Link.Icon]" => $this->core->Element([
          "div", "<img src=\"$icon\" style=\"max-width:24em\" width=\"90%\"/>\r\n", [
           "class" => "InnerMargin"
          ]
         ]),
         "[Link.Title]" => $title
        ],
        "Extension" => $this->core->AESencrypt($_View)
       ];
      }
     }
    }
   } else {
    $_AccessCode = "Accepted";
    $preview = $this->core->AESencrypt("v=".base64_encode("Search:Links")."&Preview=1");
    $_Commands = [
     [
      "Name" => "RenderInputs",
      "Parameters" => [
       ".AddLink > .Link",
       [
        [
         "Attributes" => [
          "name" => "Add",
          "type" => "hidden"
         ],
         "Type" => "Text",
         "Value" => 1
        ],
        [
         "Attributes" => [
          "class" => "LinkData",
          "data-preview" => "[Link.Preview]",
          "name" => "Link",
          "placeholder" => $this->core->base,
          "type" => "text"
         ],
         "Type" => "Text",
         "Value" => ""
        ]
       ]
      ]
     ],
     [
      "Name" => "UpdateContentAES",
      "Parameters" => [
       ".AddLink > .LinkPreview",
       $preview
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [],
     "ExtensionID" => "f5b2784b0bcc291432a3d2dafa33849a"
    ];
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Dialog" => $_Dialog,
    "ResponseType" => $_ResponseType,
    "View" => $_View
   ]);
  }
  function Lists(array $data): string {
   $_AccessCode = "Denied";
   $_Commands = [];
   $_Extension = "";
   $_ExtensionID = "";
   $_List = [];
   $base = $this->core->base;
   $data = $data["Data"] ?? [];
   $addTo = $data["AddTo"] ?? "";
   $b2 = $data["b2"] ?? "Search";
   $end = 0;
   $i = 0;
   $na = "No Results";
   $searchType = $data["st"] ?? "";
   $limit = $data["Limit"] ?? 30;
   $offset = $data["Offset"] ?? 0;
   $parentView = $data["lPG"] ?? $searchType;
   $query = $data["query"] ?? base64_encode("");
   $query = base64_decode($query);
   $querysql = "%$query%";
   $sql = New SQL($this->core->cypher->SQLCredentials());
   $na .= (!empty($data["query"])) ? " for $query" : "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   $notAnon = ($this->core->ID != $you) ? 1 : 0;
   if($searchType == "ADM-LLP") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "da5c43f7719b17a9fab1797887c5c0d1";
    if($notAnon == 1 && $y["Rank"] == md5("High Command")) {
     $_Query = "SELECT * FROM Extensions
                         JOIN Members
                         ON Member_Username=Extension_Username
                         WHERE Extension_Body LIKE :Search OR
                                       Extension_Description LIKE :Search OR
                                       Extension_ID LIKE :Search OR
                                       Extension_Title LIKE :Search OR
                                       Extension_Username LIKE :Search
                         ORDER BY Extension_Created DESC
                         LIMIT $limit
                         OFFSET $offset
     ";
     $sql->query($_Query, [
      ":Search" => $querysql
     ]);
     $sql = $sql->set();
     if(count($sql) <= $limit) {
      $end = 1;
     } foreach($sql as $sql) {
      $_Extension = $this->core->GetContentData([
       "AddTo" => $addTo,
       "Blacklisted" => 0,
       "ID" => base64_encode("Extension;".$sql["Extension_ID"])
      ]);
      if($_Extension["Empty"] == 0) {
       $info = $_Extension["DataModel"];
       $options = $_Extension["ListItem"]["Options"];
       array_push($_Commands, []);
       array_push($_List, [
        "[Extension.Category]" => $info["Category"],
        "[Extension.Delete]" => $options["Delete"],
        "[Extension.Description]" => $sql["Extension_Description"],
        "[Extension.Edit]" => $options["Edit"],
        "[Extension.ID]" => $sql["Extension_ID"],
        "[Extension.Title]" => $sql["Extension_Title"]
       ]);
      }
     }
     $_Extension = "";
    }
   } elseif($searchType == "BGP") {
    $_AccessCode = "Accepted";
    $_BlogID = $data["ID"] ?? base64_encode("");
    $_BlogID = base64_decode($_BlogID);
    $_ExtensionID = "dba88e1a123132be03b9a2e13995306d";
    if($notAnon == 1) {
     $_Query = "SELECT * FROM BlogPosts 
                         JOIN Blogs
                         ON Blog_ID=BlogPost_Blog
                         JOIN Members M
                         ON Member_Username=BlogPost_Username
                         WHERE (BlogPost_Body LIKE :Search OR
                                       BlogPost_Description LIKE :Search OR
                                       BlogPost_Title LIKE :Search)
                         AND BlogPost_Blog=:Blog
                         ORDER BY BlogPost_Created DESC
                         LIMIT $limit
                         OFFSET $offset";
     $_Query = (!empty($addTo)) ? "SELECT * FROM BlogPosts 
                         JOIN Blogs
                         ON Blog_ID=BlogPost_Blog
                         JOIN Members M
                         ON Member_Username=BlogPost_Username
                         WHERE (BlogPost_Body LIKE :Search OR
                                       BlogPost_Description LIKE :Search OR
                                       BlogPost_Title LIKE :Search)
                         ORDER BY BlogPost_Created DESC
                         LIMIT $limit
                         OFFSET $offset" : $_Query;
     $sql->query($_Query, [
      ":Blog" => $_BlogID,
      ":Search" => $querysql
     ]);
     $sql = $sql->set();
     if(count($sql) <= $limit) {
      $end = 1;
     } foreach($sql as $sql) {
      $blog = $this->core->Data("Get", ["blg", $sql["BlogPost_Blog"]]);
      $owner = ($blog["UN"] == $you) ? $y : $this->core->Member($blog["UN"]);
      $_IsBlogger = $owner["Subscriptions"]["Blogger"]["A"] ?? 0;
      $title = $blog["Title"] ?? "";
      $bl = $this->core->CheckBlocked([$y, "Blog Posts", $sql["BlogPost_ID"]]);
      $_BlogPost = $this->core->GetContentData([
       "BackTo" => $title,
       "Blacklisted" => $bl,
       "ID" => base64_encode("BlogPost;".$sql["BlogPost_Blog"].";".$sql["BlogPost_ID"])
      ]);
      if($_BlogPost["Empty"] == 0) {
       $options = $_BlogPost["ListItem"]["Options"];
       $post = $_BlogPost["DataModel"];
       $actions = ($sql["BlogPost_Username"] != $you) ? $this->core->Element([
        "button", "Block", [
         "class" => "Block InnerMargin v2",
         "data-view" => $options["Block"]
        ]
       ]) : "";
       $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
       $addTo = (!empty($addToData)) ? $this->core->Element([
        "button", "Attach", [
         "class" => "Attach InnerMargin",
         "data-input" => base64_encode($addToData[1]),
         "data-media" => base64_encode("BlogPost;".$sql["BlogPost_Blog"].";".$sql["BlogPost_ID"])
        ]
       ]) : "";
       $actions = ($this->core->ID != $you) ? $addTo.$actions : $addTo;
       $admin = ($blog["UN"] == $you || $post["UN"] == $you) ? 1 : 0;
       $cms = $this->core->Data("Get", ["cms", md5($post["UN"])]);
       $check = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $post["Privacy"],
        "UN" => $post["UN"],
        "Y" => $you
       ]);
       $check2 = ($post["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
       $illegal = $post["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       if($admin == 1 || ($bl == 0 && $check == 1 && $check2 == 1 && $illegal == 0)) {
        $actions .= ($admin == 1) ? $this->core->Element(["button", "Delete", [
         "class" => "InnerMargin OpenDialog",
         "data-encryption" => "AES",
         "data-view" => $options["Delete"]
        ]]).$this->core->Element(["button", "Edit", [
         "class" => "InnerMargin OpenCard",
         "data-encryption" => "AES",
         "data-view" => $options["Edit"]
        ]]) : "";
        $contributors = $post["Contributors"] ?? $blog["Contributors"];
        $coverPhoto = (!empty($post["CoverPhoto"])) ? base64_encode($post["CoverPhoto"]) : "";
        $op = ($post["UN"] == $you) ? $y : $this->core->Member($post["UN"]);
        $display = ($post["UN"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $memberRole = ($blog["UN"] == $post["UN"]) ? "Owner" : $contributors[$author];
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($_Commands, []);
        array_push($_List, [
         "[BlogPost.Actions]" => $actions,
         "[BlogPost.Attachments]" => $_BlogPost["ListItem"]["Attachments"],
         "[BlogPost.Author]" => $display.$verified,
         "[BlogPost.Description]" => $_BlogPost["ListItem"]["Description"],
         "[BlogPost.Created]" => $this->core->TimeAgo($post["Created"]),
         "[BlogPost.ID]" => $sql["BlogPost_ID"],
         "[BlogPost.MemberRole]" => $memberRole,
         "[BlogPost.Modified]" => $_BlogPost["ListItem"]["Modified"],
         "[BlogPost.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:5%;width:90%"),
         "[BlogPost.Title]" => $_BlogPost["ListItem"]["Title"],
         "[BlogPost.View]" => "Blog".$sql["BlogPost_Blog"].";".$options["View"]
        ]);
       }
      }
     }
    }
   } elseif($searchType == "BL") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e05bae15ffea315dc49405d6c93f9b2c";
    if($notAnon == 1) {
     $list = base64_decode($data["BL"]);
     $blacklist = $y["Blocked"][$list] ?? [];
     foreach($blacklist as $key => $id) {
      $blacklistProcessor = "v=".base64_encode("Profile:Block")."&ID=".$this->core->AESencrypt($id)."&List=".$this->core->AESencrypt($list);
      if($bl == "Albums") {
       $alb = explode("-", base64_decode($id));
       $t = ($alb[0] != $you) ? $this->core->Member($alb[0]) : $y;
       $fs = $this->core->Data("Get", [
        "fs",
        md5($t["Login"]["Username"])
       ]);
       $alb = $fs["Albums"][$alb[1]];
       $description = $alb["Description"];
       $header = "<em>".$alb["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Blogs") {
       $bg = $this->core->Data("Get", ["blg", $id]);
       $description = $bg["Description"];
       $header = "<em>".$bg["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Blog Posts") {
       $bp = $this->core->Data("Get", ["bp", $id]);
       $description = $bp["Description"];
       $header = "<em>".$bp["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Files") {
       $description = "{file_description}";
       $header = "<em>{file_name}</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Forums") {
       $forum = $this->core->Data("Get", ["pf", $id]);
       $description = $forum["Description"];
       $header = "<em>".$forum["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Forum Posts") {
       $post = $this->core->Data("Get", ["post", $id]);
       $description = $post["Description"];
       $header = "<em>".$post["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Links") {
       $_Query = "SELECT * FROM Links
                           WHERE Link_ID=$:ID";
       $sql->query($_Query, [
        ":ID" => $id
       ]);
       $sql = $sql->single();
       foreach($sql as $sql) {
        $description = $sql["Link_Description"] ?? "No Description";
        $title = $sql["Link_Title"] ?? "Untitled";
        $view = $this->core->Element(["button", "Visit <em>$title</em>", [
         "class" => "v2 v2w",
         "onclick" => "W('$value', '_blank');"
        ]]);
       }
      } elseif($bl == "Members") {
       $member = $this->core->Data("Get", ["mbr", $id]);
       $description = $member["Description"];
       $header = "<em>".$member["Personal"]["DisplayName"]."</em>";
       $view = $this->core->Element(["button", "View $h's Profile", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Pages") {
       $page = $this->core->Data("Get", ["pg", $id]);
       $description = $page["Description"];
       $header = "<em>".$page["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Products") {
       $product = $this->core->Data("Get", ["product", $id]);
       $description = $product["Description"];
       $header = "<em>".$product["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Shops") {
       $shop = $this->core->Data("Get", ["shop", $id]);
       $description = $shop["Description"];
       $header = "<em>".$shop["Title"]."</em>";
       $view = $this->core->Element(["button", "View $header", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      } elseif($bl == "Status Updates") {
       $update = $this->core->Data("Get", ["su", $id]);
       $description = $this->core->Excerpt(base64_decode($update["Body"]), 180);
       $header = $update["From"];
       $view = $this->core->Element(["button", "View $u", [
        "class" => "v2 v2w",
        "data-type" => base64_encode("#")
       ]]);
      }
      array_push($_Commands, []);
      array_push($_List, [
       "[Blacklist.Description]" => $description,
       "[Blacklist.Header]" => $header,
       "[Blacklist.ID]" => $id,
       "[Blacklist.Unblock]" => $this->core->AESencrypt($blacklistProcessor),
       "[Blacklist.View]" => $view
      ]);
     }
    }
   } elseif($searchType == "BLG") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ed27ee7ba73f34ead6be92293b99f844";
    $_Query = "SELECT * FROM Blogs
                        JOIN Members
                        ON Member_Username=Blog_Username
                        WHERE Blog_Description LIKE :Search OR
                                      Blog_Title LIKE :Search
                        ORDER BY Blog_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Blogs", $sql["Blog_ID"]]);
     $_Blog = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Blog;".$sql["Blog_ID"])
     ]);
     if($_Blog["Empty"] == 0) {
      $blog = $_Blog["DataModel"];
      $cms = $this->core->Data("Get", ["cms", md5($blog["UN"])]);
      $check = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $blog["NSFW"] == 0) ? 1 : 0;
      $check2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $blog["Privacy"],
       "UN" => $blog["UN"],
       "Y" => $you
      ]);
      $illegal = $blog["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && $check == 1 && $check2 == 1 && $illegal == 0) {
       $options = $_Blog["ListItem"]["Options"];
       array_push($_Commands, []);
       array_push($_List, [
        "[Info.CoverPhoto]" => $_Blog["ListItem"]["CoverPhoto"],
        "[Info.Description]" => $_Blog["ListItem"]["Description"],
        "[Info.Title]" => $_Blog["ListItem"]["Title"],
        "[Info.View]" => $options["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "Bulletins") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ae30582e627bc060926cfacf206920ce";
    $bulletins = $this->core->Data("Get", ["bulletins", md5($you)]);
    foreach($bulletins as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Members", $value["From"]]);;
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
      array_push($_Commands, []);
      array_push($_List, [
       "[Bulletin.Date]" => $this->core->TimeAgo($value["Sent"]),
       "[Bulletin.From]" => $_Member["ListItem"]["Title"].$verified,
       "[Bulletin.ID]" => $key,
       "[Bulletin.Message]" => $this->core->RenderView($message),
       "[Bulletin.Options]" => $this->core->RenderView($options),
       "[Bulletin.Picture]" => $_Member["ListItem"]["Options"]["ProfilePicture"]
      ]);
     }
    }
   } elseif($searchType == "CA" || $searchType == "PR") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e7829132e382ee4ab843f23685a123cf";
    $_Query = "SELECT * FROM Articles
                        JOIN Members
                        ON Member_Username=Article_Username
                        WHERE Article_Body LIKE :Search OR
                                      Article_Description LIKE :Search OR
                                      Article_Title LIKE :Search
                        ORDER BY Article_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Pages", $sql["Article_ID"]]);
     $_Article = $this->core->GetContentData([
      "AddTo" => $addTo,
      "BackTo" => $b2,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Page;".$sql["Article_ID"]),
      "ParentPage" => $parentView
     ]);
     if($_Article["Empty"] == 0) {
      $article = $_Article["DataModel"];
      $i++;
      $nsfw = $article["NSFW"] ?? 0;
      $t = ($article["UN"] == $you) ? $y : $this->core->Member($article["UN"]);
      $cat = $article["Category"] ?? "";
      $cms = $this->core->Data("Get", ["cms", md5($article["UN"])]);
      $check = ($article["Category"] == $searchType) ? 1 : 0;
      $check2 = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $check3 = (($searchType == "CA" && $article["Category"] == "CA") || ($searchType == "PR" && $article["Category"] == "PR")) ? 1 : 0;
      $check4 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $article["Privacy"],
       "UN" => $article["UN"],
       "Y" => $you
      ]);
      $check = ($check == 1 && $check2 == 1 && $check3 == 1 && $check4 == 1) ? 1 : 0;
      $illegal = $article["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      if($bl == 0 && $check == 1 && $illegal == 0) {
       array_push($_Commands, []);
       array_push($_List, [
        "[Info.CoverPhoto]" => $_Article["ListItem"]["CoverPhoto"],
        "[Info.Description]" => $_Article["ListItem"]["Description"],
        "[Info.Title]" => $_Article["ListItem"]["Title"],
        "[Info.View]" => "$parentView;".$_Article["ListItem"]["Options"]["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "CART") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "dea3da71b28244bf7cf84e276d5d1cba";
    $newCartList = [];
    $now = $this->core->timestamp;
    $shop = $data["ID"] ?? md5($this->core->ShopID);
    $username = $data["Username"] ?? base64_encode($this->core->ShopID);
    $products = $y["Shopping"]["Cart"][$shop] ?? [];
    $products = $products["Products"] ?? [];
    foreach($products as $key => $value) {
     $bl = $this->core->CheckBlocked([$y, "Products", $key]);;
     $_Product = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Product;$key")
     ]);
     if($_Product["Empty"] == 0) {
      $product = $_Product["DataModel"];
      $isActive = (strtotime($now) < $product["Expires"]) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $quantity = $product["Quantity"] ?? 0;
      if(!empty($product) && $isActive == 1 && $quantity != 0 && $illegal == 0) {
       $newCartList[$key] = $value;
       array_push($_Commands, []);
       array_push($_List, [
        "[Product.CoverPhoto]" => $_Product["ListItem"]["CoverPhoto"],
        "[Product.Description]" => $_Product["ListItem"]["Description"],
        "[Product.ID]" => $key,
        "[Product.Title]" => $_Product["ListItem"]["Title"],
        "[Product.Remove]" => base64_encode("v=".base64_encode("Cart:SaveRemove")."&Product=$key&Shop=$shop")
       ]);
      }
     }
    }
    $y["Shopping"]["Cart"][$shop]["Products"] = $newCartList;
    $this->core->Data("Save", ["mbr", md5($you), $y]);
   } elseif($searchType == "Chat") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "343f78d13872e3b4e2ac0ba587ff2910";
    $integrated = $data["Integrated"] ?? 0;
    if($notAnon == 1) {
     $_Query = "SELECT * FROM Chat
                         JOIN Members
                         ON Member_Username=Chat_Username
                         WHERE Chat_Description LIKE :Search OR
                                       Chat_Title LIKE :Search
                         ORDER BY Chat_Created DESC
                         LIMIT $limit
                         OFFSET $offset";
     $_ExtensionID = ($integrated == 0) ? "183d39e5527b3af3e7652181a0e36e25" : $_ExtensionID;
     $sql->query($_Query, [
      ":Search" => $querysql,
      ":Username" => $you
     ]);
     $sql = $sql->set();
     if(count($sql) <= $limit) {
      $end = 1;
     } foreach($sql as $sql) {
      $bl = $this->core->CheckBlocked([$y, "Group Chats", $sql["Chat_ID"]]);
      $_Chat = $this->core->GetContentData([
       "AddTo" => $addTo,
       "Blacklisted" => $bl,
       "ID" => base64_encode("Chat;".$sql["Chat_ID"]),
       "Integrated" => $integrated
      ]);
      if(!in_array($sql["Chat_ID"], $this->core->RestrictedIDs) && $_Chat["Empty"] == 0) {
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
         array_push($_Commands, []);
         array_push($_List, [
          "[Chat.DisplayName]" => $displayName,
          "[Chat.Online]" => "",
          "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%"),
          "[Chat.View]" => $_Chat["ListItem"]["Options"]["View"]
         ]);
        }
       }
      }
     }
    }
   } elseif($searchType == "Congress") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "1f32642e05747ba3cec15d7c9fffbd0f";
    $chamber = $data["Chamber"] ?? "";
    $congress = $this->core->Data("Get", ["app", md5("Congress")]);
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
         $files = $this->core->Data("Get", ["fs", $id[3]]);
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
            array_push($_Commands, []);
            array_push($_List, [
             "[Content.Description]" => $description,
             "[Content.Illegal]" => base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Illegal")),
             "[Content.Legal]" => base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Legal")),
             "[Content.Title]" => $title,
             "[Content.Voted]" => $voted
            ]);
           }
          }
         }
        } else {
         $info = $this->core->Data("Get", [$id[2], $id[3]]);
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
           array_push($_Commands, []);
           array_push($_List, [
            "[Content.Description]" => $description,
            "[Content.Illegal]" => base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Illegal")),
            "[Content.Legal]" => base64_encode("v=".base64_encode("Congress:Vote")."&ID=$contentID&Vote=".base64_encode("Legal")),
            "[Content.Title]" => $title,
            "[Content.Voted]" => $voted
           ]);
          }
         }
        }
       }
      }
     }
    }
   } elseif($searchType == "CongressionalBallot") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "633ddf914ed8a2e2aa7e023471ec83b2";
    $ballot = $this->core->Data("Get", ["app", md5("CongressionalBallot")]);
    $candidates = $ballot["Candidates"] ?? [];
    $chamber = $data["Chamber"] ?? "House";
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
       array_push($_Commands, []);
       array_push($_List, [
        "[Tile.Action]" => $this->core->Element([
         "div", $action, ["class" => "VoteFor".md5($memberID)]
        ]).$this->core->Element(["button", "View $displayName's Profile", [
         "class" => "OpenCard v2 v2w",
         "data-view" => $options["View"]
        ]]),
        "[Tile.Data]" => $this->core->Element([
         "h4", number_format($voteCount)." members have cast their vote for $displayName to join the $candidateChamber."
        ]),
        "[Tile.Header]" => $displayName
       ]);
      }
     }
    }
   } elseif($searchType == "CongressionalStaffHouse" || $searchType == "CongressionalStaffSenate") {
    $_AccessCode = "Accepted";
    $_Extension = $this->core->AESencrypt($this->core->Element([
     "div", "[ListItem.Button]", ["class" => "Desktop25"]
    ]));
    $_ExtensionID = "";
    $congress = $this->core->Data("Get", ["app", md5("Congress")]);
    $congress = $congress["Members"] ?? [];
    $chamber = $data["Chamber"] ?? "";
    $na = "No $chamber Staff";
    if(($chamber == "House" || $chamber == "Senate")) {
     foreach($congress as $member => $role) {
      $check = ($chamber == "House" && $role == "HouseRepresentative") ? 1 : 0;
      $check2 = ($chamber == "Senate" && $role == "Senator") ? 1 : 0;
      if($check == 1 || $check2 == 1) {
       $t = ($member == $you) ? $y : $this->core->Member($member);
       if(!empty($t["Login"])) {
        array_push($_Commands, []);
        array_push($_List, [
         "[ListItem.Button]" => $this->core->Element([
          "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
           "class" => "OpenCard Small",
           "data-encryption" => "AES",
           "data-view" => $this->core->AESencrypt("v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($t["Login"]["Username"]))
          ]
         ]).$this->core->Element([
          "h4", $t["Personal"]["DisplayName"], ["class" => "CenterText UpperCase"]
         ])
        ]);
       }
      }
     }
    }
   } elseif($searchType == "Contacts") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ccba635d8c7eca7b0b6af5b22d60eb55";
    if($notAnon == 1) {
     $cms = $this->core->Data("Get", ["cms", md5($y["Login"]["Username"])]);
     $cms = $cms["Contacts"] ?? [];
     foreach($cms as $key => $value) {
      $t = $this->core->Member($key);
      $id = md5($key);
      array_push($_Commands, []);
      array_push($_List, [
       "[Contact.Delete]" => $this->core->AESencrypt("v=".base64_encode("Contact:Delete")."&Username=".$this->core->AESencrypt($key)),
       "[Contact.DisplayName]" => $t["Personal"]["DisplayName"],
       "[Contact.ID]" => $id,
       "[Contact.Options]" => $this->core->AESencrypt("v=".base64_encode("Contact:Options")."&UN=".base64_encode($key)),
       "[Contact.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:5%;width:90%"),
       "[Contact.Username]" => $key
      ]);
     }
    }
   } elseif($searchType == "ContactsProfileList") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ba17995aafb2074a28053618fb71b912";
    $x = $this->core->Data("Get", [
     "cms",
     md5(base64_decode($data["UN"]))
    ]);
    $x = $x["Contacts"] ?? [];
    foreach($x as $k => $v) {
     $t = $this->core->Member($k);
     $cms = $this->core->Data("Get", [
      "cms",
      md5($t["Login"]["Username"])
     ]);
     $bl = $this->core->CheckBlocked([
      $t, "Members", $y["Login"]["Username"]
     ]);
     $bl2 = $this->core->CheckBlocked([
      $y, "Members", $t["Login"]["Username"]
     ]);
     $check = $this->core->CheckPrivacy([
      "Contacts" => $cms["Contacts"],
      "Privacy" => $t["Privacy"]["Profile"],
      "UN" => $t["Login"]["Username"],
      "Y" => $y["Login"]["Username"]
     ]);
     if($bl == 0 && $bl2 == 0 && $check == 1) {
      $opt = $this->core->Element(["button", "View Profile", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("CARD=1&v=".base64_encode("Profile:Home")."&back=1&b2=$b2&lPG=$parentView&pub=0&UN=".base64_encode($t["Login"]["Username"]))
      ]]);
      array_push($_Commands, []);
      array_push($_List, [
       "[Member.DisplayName]" => $t["Personal"]["DisplayName"],
       "[Member.Description]" => $t["Personal"]["Description"],
       "[Member.Options]" => $opt,
       "[Member.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:5%;width:90%")
      ]);
     }
    }
   } elseif($searchType == "ContactsRequests") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "8b6ac25587a4524c00b311c184f6c69b";
    if($notAnon == 1) {
     $cms = $this->core->Data("Get", [
      "cms",
      md5($y["Login"]["Username"])
     ]);
     $cms = $cms["Requests"] ?? [];
     foreach($cms as $key => $value) {
      $t = $this->core->Member($value);
      $pp = $this->core->ProfilePicture($t, "margin:5%;width:90%");
      $accept = "v=".base64_encode("Contact:Requests")."&accept=1";
      $decline = "v=".base64_encode("Contact:Requests")."&decline=1";
      $memberID = md5($t["Login"]["Username"]);
      array_push($_Commands, []);
      array_push($_List, [
       "[Contact.Accept]" => base64_encode($accept),
       "[Contact.Decline]" => base64_encode($decline),
       "[Contact.DisplayName]" => $t["Personal"]["DisplayName"],
       "[Contact.Form]" => $memberID,
       "[Contact.ID]" => $memberID,
       "[Contact.IDaccept]" => $memberID,
       "[Contact.IDdecline]" => $memberID,
       "[Contact.ProfilePicture]" => $pp,
       "[Contact.Username]" => $t["Login"]["Username"]
      ]);
     }
    }
   } elseif($searchType == "Contributors") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ba17995aafb2074a28053618fb71b912";
    $admin = 0;
    $contributors = [];
    $id = $data["ID"] ?? "";
    $type = $data["Type"] ?? "";
    $check = (!empty($id)) ? 1 : 0;
    $check2 = (!empty($type)) ? 1 : 0;
    if($check == 1 && $check2 == 1) {
     $id = base64_decode($id);
     $type = base64_decode($type);
     if($type == "Article") {
      $Page = $this->core->Data("Get", ["pg", $id]);
      $contributors = $Page["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Blog") {
      $blog = $this->core->Data("Get", ["blg", $id]);
      $contributors = $blog["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "BlogPost") {
      $post = $this->core->Data("Get", ["bp", $id]);
      $contributors = $post["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Forum") {
      $forum = $this->core->Data("Get", ["pf", $id]);
      $contributors = $this->core->Data("Get", ["pfmanifest", $id]);
      foreach($contributors as $member => $role) {
       if($admin == 0 && $member == $you && $role == "Admin") {
        $admin++;
       }
      }
     } elseif($type == "Shop") {
      $shop = $this->core->Data("Get", ["shop", $id]);
      $contributors = $shop["Contributors"] ?? [];
     } foreach($contributors as $member => $role) {
      $bl = $this->core->CheckBlocked([$y, "Members", $member]);;
      $_Member = $this->core->GetContentData([
       "AddTo" => $addTo,
       "Blacklisted" => $bl,
       "ID" => base64_encode("Member;".md5($member))
      ]);
      if($_Member["Empty"] == 0) {
       $member = $_Member["DataModel"];
       $options = $_Member["ListItem"]["Options"];
       $them = $member["Login"]["Username"];
       $cms = $this->core->Data("Get", ["cms", md5($them)]);
       $check = $this->core->CheckPrivacy([
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
         $check2 = ($Page["UN"] == $you || $admin == 1) ? 1 : 0;
         $check2 = ($check2 == 1 && $member != $you) ? 1 : 0;
         if($check == 1 || $check2 == 1) {
          $check = ($Page["UN"] != $member && $Page["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($Page["ID"]);
          $mbr = base64_encode($them);
          $opt = ($check == 1 && $check2 == 1) ? $this->core->Element([
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
         $check2 = ($blog["UN"] == $you || $admin == 1) ? 1 : 0;
         $check2 = ($check2 == 1 && $member != $you) ? 1 : 0;
         if($check == 1 || $check2 == 1) {
          $check = ($blog["UN"] != $member && $blog["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($blog["ID"]);
          $mbr = base64_encode($them);
          $opt = ($check == 1 && $check2 == 1) ? $this->core->Element([
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
         $check2 = ($post["UN"] == $you || $admin == 1) ? 1 : 0;
         $check2 = ($check2 == 1 && $member != $you) ? 1 : 0;
         if($check == 1 || $check2 == 1) {
          $check = ($post["UN"] != $member && $post["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($post["ID"]);
          $mbr = base64_encode($them);
         }
        } elseif($type == "Forum") {
         $check2 = ($forum["UN"] == $you || $admin == 1) ? 1 : 0;
         $check2 = ($check2 == 1 && $member != $you) ? 1 : 0;
         if($check == 1 || $check2 == 1) {
          $check = ($forum["UN"] != $member && $forum["UN"] != $you) ? 1 : 0;
          $eid = base64_encode($forum["ID"]);
          $mbr = base64_encode($them);
          $opt = ($check == 1 && $check2 == 1) ? $this->core->Element([
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
         $check = ($id == md5($you) && $them != $you) ? 1 : 0;
         $description = "<b>".$role["Title"]."</b><br/>".$role["Description"];
         $eid = base64_encode($id);
         $memberID = base64_encode($them);
         $opt = ($check == 1) ? $this->core->Element(["button", "Edit", [
          "class" => "OpenCard v2",
          "data-view" => base64_encode("v=".base64_encode("Shop:EditPartner")."&UN=$memberID")
         ]]).$this->core->Element(["button", "Fire", [
          "class" => "OpenDialog v2",
          "data-view" => base64_encode("v=".base64_encode("Shop:Banish")."&ID=$eid&UN=$memberID")
         ]]) : "";
        }
       }
       $description = ($type == "Shop") ? $description : $_Member["ListItem"]["Description"];
       array_push($_Commands, []);
       array_push($_List, [
        "[Member.DisplayName]" => $_Member["ListItem"]["Title"],
        "[Member.Description]" => $description,
        "[Member.Options]" => $opt,
        "[Member.ProfilePicture]" => $options["ProfilePicture"]
       ]);
      }
     }
    }
   } elseif($searchType == "CS1") {
    $_AccessCode = "Accepted";
    $_List = [
     [1, "Monday"],
     [2, "Tuesday"],
     [3, "Wednesday"],
     [4, "Thursday"],
     [5, "Friday"],
     [6, "Saturday"],
     [7, "Sunday"]
    ];
   } elseif($searchType == "DC") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e9f34ca1985c166bf7aa73116a745e92";
    $shopID = $data["Shop"] ?? md5($you);
    if($notAnon == 1) {
     $discountCodes = $this->core->Data("Get", ["dc", $shopID]);
     foreach($discountCodes as $key => $value) {
      $viewData = json_encode([
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($key),
       "v" => base64_encode("DiscountCode:Purge")
      ], true);
      $options = $this->core->Element(["button", "Delete", [
       "class" => "A OpenDialog v2",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData))
      ]]).$this->core->Element(["button", "Edit", [
       "class" => "OpenCard v2",
       "data-encryption" => "AES",
       "data-view" => $this->core->AESencrypt("v=".base64_encode("DiscountCode:Edit")."&ID=$key&Shop=$shopID")
      ]]);
      array_push($_Commands, []);
      array_push($_List, [
       "[ListItem.Description]" => $value["Percentile"]."% Off: ".$value["Quantity"],
       "[ListItem.Options]" => $options,
       "[ListItem.Title]" => base64_decode($value["Code"])
      ]);
     }
    }
   } elseif($searchType == "Feedback") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e7c4e4ed0a59537ffd00a2b452694750";
    $_Query = "SELECT * FROM Feedback
                        JOIN Members
                        ON Member_Username=Feedback_Username
                        WHERE Feedback_Message LIKE :Search OR
                                      Feedback_ParaphrasedQuestion LIKE :Search OR
                                      Feedback_Subject LIKE :Search
                        ORDER BY Feedback_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $now = $this->core->timestamp;
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $feedback = $this->core->Data("Get", ["feedback", $sql["Feedback_ID"]]);
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
     array_push($_Commands, []);
     array_push($_List, [
      "[Feedback.ID]" => $value,
      "[Feedback.Home]" => base64_encode("v=".base64_encode("Feedback:Home")."&ID=$value"),
      "[Feedback.Message]" => $message,
      "[Feedback.Modified]" => $modified,
      "[Feedback.Resolved]" => $resolved,
      "[Feedback.Title]" => $title
     ]);
    }
   } elseif($searchType == "Forums") {
    $_AccessCode = "Accepted";
    $_Query = "SELECT * FROM Forums
                        JOIN Members
                        ON Member_Username=Forum_Username
                        WHERE Forum_Description LIKE :Search OR
                                      Forum_Title LIKE :Search
                        ORDER BY Forum_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $_ExtensionID = "ed27ee7ba73f34ead6be92293b99f844";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Forums", $sql["Forum_ID"]]);
     $_Forum = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Forum;".$sql["Forum_ID"])
     ]);
     if(!in_array($sql["Forum_ID"], $this->core->RestrictedIDs) && $_Forum["Empty"] == 0) {
      $active = 0;
      $forum = $_Forum["DataModel"];
      $manifest = $this->core->Data("Get", ["pfmanifest", $sql["Forum_ID"]]);
      $t = ($forum["UN"] == $you) ? $y : $this->core->Member($forum["UN"]);
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]);
      $check = ($forum["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $check2 = $this->core->CheckPrivacy([
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
      } if($bl == 0 && ($active == 1 || $check == 1 && $check2 == 1) && $illegal == 0) {
       $options = $_Forum["ListItem"]["Options"];
       array_push($_Commands, []);
       array_push($_List, [
        "[Info.CoverPhoto]" => $_Forum["ListItem"]["CoverPhoto"],
        "[Info.Description]" => $_Forum["ListItem"]["Description"],
        "[Info.Title]" => $_Forum["ListItem"]["Title"],
        "[Info.View]" => $options["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "Forums-Admin") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ba17995aafb2074a28053618fb71b912";
    $admin = $data["Admin"] ?? base64_encode("");
    $id = $data["ID"] ?? "";
    if(!empty($id)) {
     $admin = base64_decode($admin);
     $id = base64_decode($id);
     $manifest = $this->core->Data("Get", ["pfmanifest", $id]);
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
        $contacts = $this->core->Data("Get", ["cms", md5($them)]);
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
         array_push($_Commands, []);
         array_push($_List, [
          "[Member.DisplayName]" => $_Member["ListItem"]["Title"],
          "[Member.Description]" => $_Member["ListItem"]["Description"],
          "[Member.Options]" => "",
          "[Member.ProfilePicture]" => $options["ProfilePicture"]
         ]);
        }
       }
      }
     }
    }
   } elseif($searchType == "Forums-Posts") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "150dcee8ecbe0e324a47a8b5f3886edf";
    $_Query = "SELECT * FROM ForumPosts
                        JOIN Forums
                        ON Forum_ID=ForumPost_Forum
                        JOIN Members M
                        ON Member_Username=ForumPost_Username
                        WHERE (ForumPost_Body LIKE :Search OR
                                      ForumPost_Title LIKE :Search)
                        AND ForumPost_Forum=:Forum
                        ORDER BY ForumPost_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $_Query = (!empty($addTo)) ? "SELECT * FROM ForumPosts
                        JOIN Forums
                        ON Forum_ID=ForumPost_Forum
                        JOIN Members M
                        ON Member_Username=ForumPost_Username
                        WHERE (ForumPost_Body LIKE :Search OR
                                      ForumPost_Title LIKE :Search)
                        ORDER BY ForumPost_Created DESC
                        LIMIT $limit
                        OFFSET $offset" : $_Query;
    $active = 0;
    $admin = 0;
    $id = $data["ID"] ?? "";
    $forum = $this->core->Data("Get", ["pf", $id]);
    $forumType = $forum["Type"] ?? "Private";
    $manifest = $this->core->Data("Get", ["pfmanifest", $id]);
    foreach($manifest as $member => $role) {
     if($active == 0 && $member == $you) {
      $active = 0;
      if($admin == 0 && $role == "Admin") {
       $admin++;
      }
     }
    } if($active == 1 || $admin == 1 || $forumType == "Public") {
     $sql->query($_Query, [
      ":Forum" => $id,
      ":Search" => $querysql
     ]);
     $sql = $sql->set();
     if(count($sql) <= $limit) {
      $end = 1;
     } foreach($sql as $sql) {
      $bl = $this->core->CheckBlocked([$y, "Forum Posts", $sql["ForumPost_ID"]]);
      $_ForumPost = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("ForumPost;".$sql["ForumPost_Forum"].";".$sql["ForumPost_ID"])
      ]);
      $forum = $this->core->Data("Get", ["pf", $sql["ForumPost_Forum"]]);
      if($_ForumPost["Empty"] == 0) {
       $actions = "";
       $active = 0;
       $post = $_ForumPost["DataModel"];
       $cms = $this->core->Data("Get", ["cms", md5($sql["ForumPost_Username"])]);
       $illegal = $post["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       $op = ($sql["ForumPost_Username"] == $you) ? $y : $this->core->Member($sql["ForumPost_Username"]);
       $options = $_ForumPost["ListItem"]["Options"];
       $check = ($sql["Forum_Username"] == $you || $post["From"] == $you) ? 1 : 0;
       $check2 = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $post["NSFW"] == 0) ? 1 : 0;
       $check3 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $post["Privacy"],
        "UN" => $post["From"],
        "Y" => $you
       ]);
       $passPhrase = $post["PassPhrase"] ?? "";
       if($bl == 0 && ($check2 == 1 && $check3 == 1) && $illegal == 0) {
        $bl = $this->core->CheckBlocked([$y, "Forum Posts", $sql["ForumPost_ID"]]);
        $body = (empty($passPhrase)) ? $_ForumPost["ListItem"]["Body"] : $this->ContentIsProtected;
        $con = base64_encode("Conversation:Home");
        $actions .= ($post["From"] != $you) ? $this->core->Element([
         "button", "Block", [
          "class" => "Block InnerMargin",
          "data-view" => $options["Block"]
         ]
        ]) : "";
        $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
        $addTo = (!empty($addToData)) ? $this->core->Element([
         "button", "Attach", [
          "class" => "Attach InnerMargin",
          "data-input" => base64_encode($addToData[1]),
          "data-media" => base64_encode("ForumPost;".$sql["ForumPost_Forum"].";".$sql["ForumPost_ID"])
         ]
        ]) : "";
        $actions = ($this->core->ID != $you) ? $addTo.$actions : $addTo;
        if($check == 1) {
         $actions .= $this->core->Element([
          "button", "Delete", [
           "class" => "InnerMargin OpenDialog",
           "data-encryption" => "AES",
           "data-view" => $options["Delete"]
          ]
         ]);
         $actions .= ($admin == 1 || $check == 1) ? $this->core->Element([
          "button", "Edit", [
           "class" => "InnerMargin OpenCard",
           "data-encryption" => "AES",
           "data-view" => $options["Edit"]
          ]
         ]) : "";
        }
        $actions .= ($forumType == "Public") ? $this->core->Element([
         "button", "Share", [
          "class" => "InnerMargin OpenCard",
          "data-encryption" => "AES",
          "data-view" => $options["Share"]
         ]
        ]) : "";
        $display = ($post["From"] == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $memberRole = ($forum["UN"] == $post["From"]) ? "Owner" : $manifest[$op["Login"]["Username"]];
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($_Commands, [
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Attachments".$sql["ForumPost_ID"],
           $_ForumPost["ListItem"]["Attachments"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Notes".$sql["ForumPost_ID"],
           $options["Notes"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Vote".$sql["ForumPost_ID"],
           $options["Vote"]
          ]
         ],
        ]);
        array_push($_List, [
         "[ForumPost.Actions]" => $actions,
         "[ForumPost.Body]" => $body,
         "[ForumPost.Comment]" => $options["View"],
         "[ForumPost.Created]" => $this->core->TimeAgo($post["Created"]),
         "[ForumPost.ID]" => $sql["ForumPost_ID"],
         "[ForumPost.MemberRole]" => $memberRole,
         "[ForumPost.Modified]" => $_ForumPost["ListItem"]["Modified"],
         "[ForumPost.OriginalPoster]" => $display.$verified,
         "[ForumPost.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:5%;width:90%"),
         "[ForumPost.Title]" => $_ForumPost["ListItem"]["Title"]
        ]);
       }
      }
     }
    }
   } elseif($searchType == "Forums-Topic") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "150dcee8ecbe0e324a47a8b5f3886edf";
    $_Query = "SELECT * FROM ForumPosts
                        JOIN Forum
                        ON Forum_ID=ForumPost_Forum
                        JOIN Members
                        ON Member_Username=ForumPost_Username
                        WHERE (ForumPost_Body LIKE :Search OR
                                      ForumPost_Title LIKE :Search)
                        AND ForumPost_Forum=:Forum
                        AND ForumPost_Topic=:Topic
                        ORDER BY ForumPost_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $active = 0;
    $admin = 0;
    $forumID = $data["Forum"] ?? "";
    $manifest = $this->core->Data("Get", ["pfmanifest", $forumID]);
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
     $sql->query($_Query, [
      ":Forum" => $id,
      ":Search" => $querysql,
      ":Topic" => $topicID
     ]);
     $sql = $sql->set();
     if(count($sql) <= $limit) {
      $end = 1;
     } foreach($sql as $sql) {
      $bl = $this->core->CheckBlocked([$y, "Forum Posts", $sql["ForumPost_ID"]]);
      $_ForumPost = $this->core->GetContentData([
       "AddTo" => $addTo,
       "Blacklisted" => $bl,
       "ID" => base64_encode("ForumPost;$forumID;".$sql["ForumPost_ID"])
      ]);
      if($_ForumPost["Empty"] == 0 && $i <= 5) {
       $actions = "";
       $active = 0;
       $post = $_ForumPost["DataModel"];
       $cms = $this->core->Data("Get", ["cms", md5($post["From"])]);
       $illegal = $post["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       $op = ($sql["ForumPost_Username"] == $you) ? $y : $this->core->Member($sql["ForumPost_ID"]);
       $options = $_ForumPost["ListItem"]["Options"];
       $check = ($forum["UN"] == $you || $post["From"] == $you) ? 1 : 0;
       $check2 = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $post["NSFW"] == 0) ? 1 : 0;
       $check3 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $post["Privacy"],
        "UN" => $post["From"],
        "Y" => $you
       ]);
       $passPhrase = $post["PassPhrase"] ?? "";
       if($bl == 0 && ($check2 == 1 && $check3 == 1) && $illegal == 0) {
        $body = (empty($passPhrase)) ? $_ForumPost["ListItem"]["Body"] : $this->ContentIsProtected;
        $con = base64_encode("Conversation:Home");
        $actions = ($post["From"] != $you) ? $this->core->Element([
         "button", "Block", [
          "class" => "Block InnerMargin",
          "data-view" => $options["Block"]
         ]
        ]) : "";
        $actions = ($this->core->ID != $you) ? $actions : "";
        if($check == 1) {
         $actions .= $this->core->Element([
          "button", "Delete", [
           "class" => "InnerMargin OpenDialog",
           "data-view" => $options["Delete"]
          ]
         ]);
         $actions .= ($admin == 1 || $check == 1) ? $this->core->Element([
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
        array_push($_Commands, []);
        array_push($_List, [
         "[ForumPost.Actions]" => $actions,
         "[ForumPost.Attachments]" => $_ForumPost["ListItem"]["Attachments"],
         "[ForumPost.Body]" => $body,
         "[ForumPost.Comment]" => $options["View"],
         "[ForumPost.Created]" => $this->core->TimeAgo($post["Created"]),
         "[ForumPost.ID]" => $sql["ForumPost_ID"],
         "[ForumPost.MemberRole]" => $memberRole,
         "[ForumPost.Modified]" => $_ForumPost["ListItem"]["Modified"],
         "[ForumPost.Notes]" => $options["Notes"],
         "[ForumPost.OriginalPoster]" => $display.$verified,
         "[ForumPost.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:5%;width:90%"),
         "[ForumPost.Title]" => $_ForumPost["ListItem"]["Title"],
         "[ForumPost.Votes]" => $options["Vote"]
        ]);
       }
      }
     }
    }
   } elseif($searchType == "Forums-Topics") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "099d6de4214f55e68ea49395a63b5e4d";
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
       $modified = $info["Modified"] ?? $this->core->TimeAgo($now);
       $postCount = 0;
       $posts = array_reverse($info["Posts"]);
       $postList = "";
       foreach($posts as $key => $post) {
        $bl = $this->core->CheckBlocked([$y, "Forum Posts", $post]);
        $_ForumPost = $this->core->GetContentData([
         "Blacklisted" => $bl,
         "ID" => base64_encode("ForumPost;$forumID;$post")
        ]);
        if($_ForumPost["Empty"] == 0 && $postCount < 5) {
         $postCount++;
         $post = $_ForumPost["DataModel"];
         $postList .= $this->core->Element([
          "div", $this->core->Element([
           "h4", $post["Title"]
          ]).$this->core->Element([
           "p", $this->core->Excerpt(htmlentities($post["Body"]))
          ]), ["class" => "FrostedBright Medium Rounded"]
         ]);
        }
       }
       array_push($_Commands, []);
       array_push($_List, [
        "[Forum.ID]" => $forumID,
        "[Topic.Created]" => $created,
        "[Topic.Description]" => $info["Description"],
        "[Topic.LatestPosts]" => $postList,
        "[Topic.Modified]" => $modified,
        "[Topic.PostCount]" => $this->core->ShortNumber(count($posts)),
        "[Topic.Title]" => $info["Title"],
        "[Topic.View]" => $this->core->AESencrypt("v=".base64_encode("Forum:Topic")."&Forum=".base64_encode($forumID)."&Topic=".base64_encode($topicID))
       ]);
      }
     }
    }
   } elseif($searchType == "Links") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "aacfffd7976e2702d91a5c7084471ebc";
    $_Query = "SELECT * FROM Links
                        WHERE Link_Description LIKE :Search OR
                                      Link_Keywords LIKE :Search OR
                                      Link_ID LIKE :Search OR
                                      Link_Title LIKE :Search
                        ORDER BY Link_Title DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $icon = parse_url($sql["Link_ID"], PHP_URL_SCHEME)."://".parse_url($sql["Link_ID"], PHP_URL_HOST); 
     $icon = trim($icon, "/");
     $icon = "$icon/apple-touch-icon.png";
     $iconExists = ($this->core->RenderHTTPResponse($icon) == 200) ? 1 : 0;
     $icon = ($iconExists == 0) ? $this->core->base."/apple-touch-icon.png" : $icon;
     array_push($_List, [
      "[Link.Description]" => $sql["Link_Description"],
      "[Link.Keywords]" => $sql["Link_Keywords"],
      "[Link.Icon]" => $this->core->Element([
       "div", "<img src=\"$icon\" style=\"max-width:24em\" width=\"90%\"/>\r\n", [
        "class" => "InnerMargin"
       ]
      ]),
      "[Link.Title]" => $sql["Link_Title"]
     ]);
    }
   } elseif($searchType == "Mainstream") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "18bc18d5df4b3516c473b82823782657";
    $_Query = "SELECT * FROM StatusUpdates
                        JOIN Members
                        ON Member_Username=StatusUpdate_Username
                        WHERE (StatusUpdate_Body LIKE :Body OR
                                      StatusUpdate_Username LIKE :Username)
                        AND StatusUpdate_Privacy=:Privacy
                        ORDER BY StatusUpdate_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Body" => $querysql,
     ":Privacy" => md5("Public"),
     ":Username" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
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
       $cms = $this->core->Data("Get", ["cms", md5($from)]);
       $privacy = $op["Privacy"]["Posts"] ?? md5("Public");
       $check = $update["NSFW"] ?? 0;
       $check = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $check == 0) ? 1 : 0;
       $check2 = $cms["Contacts"] ?? [];
       $check2 = $this->core->CheckPrivacy([
        "Contacts" => $check2,
        "Privacy" => $privacy,
        "UN" => $from,
        "Y" => $you
       ]);
       $passPhrase = $update["PassPhrase"] ?? "";
       if($check == 1 && $check2 == 1) {
        $body = (empty($passPhrase)) ? $_StatusUpdate["ListItem"]["Body"] : $this->ContentIsProtected;
        $display = ($from == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $options = $_StatusUpdate["ListItem"]["Options"];
        $edit = ($from == $you) ? $this->core->Element([
         "button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-encryption" => "AES",
          "data-view" => $options["Delete"]
         ]
        ]).$this->core->Element([
         "button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-encryption" => "AES",
          "data-view" => $options["Edit"]
         ]
        ]) : "";
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($_Commands, [
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Attachments".$sql["StatusUpdate_ID"],
           $_StatusUpdate["ListItem"]["Attachments"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Notes".$sql["StatusUpdate_ID"],
           $options["Notes"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Vote".$sql["StatusUpdate_ID"],
           $options["Vote"]
          ]
         ],
        ]);
        array_push($_List, [
         "[StatusUpdate.Body]" => $body,
         "[StatusUpdate.Created]" => $this->core->TimeAgo($update["Created"]),
         "[StatusUpdate.DT]" => $options["View"],
         "[StatusUpdate.Edit]" => $edit,
         "[StatusUpdate.ID]" => $sql["StatusUpdate_ID"],
         "[StatusUpdate.Modified]" => $_StatusUpdate["ListItem"]["Modified"],
         "[StatusUpdate.OriginalPoster]" => $display.$verified,
         "[StatusUpdate.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:5%;width:90%")
        ]);
       }
      }
     }
    }
   } elseif($searchType == "MBR") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ba17995aafb2074a28053618fb71b912";
    $_Query = "SELECT * FROM Members
                        WHERE Member_Description LIKE :Search OR
                                      Member_DisplayName LIKE :Search OR
                                      Member_Username LIKE :Search
                        ORDER BY Member_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $home = base64_encode("Profile:Home");
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Members", $sql["Member_Username"]]);
     $_Member = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Member;".md5($sql["Member_Username"]))
     ]);
     $member = $_Member["DataModel"];
     if($_Member["Empty"] == 0) {
      $them = $member["Login"]["Username"];
      $cms = $this->core->Data("Get", ["cms", md5($them)]);
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
       array_push($_Commands, []);
       array_push($_List, [
        "[Member.DisplayName]" => $_Member["ListItem"]["Title"].$verified,
        "[Member.Description]" => $_Member["ListItem"]["Description"],
        "[Member.Options]" => $this->core->Element(["button", "View Profile", [
         "class" => "OpenCard v2",
         "data-view" => $options["View"]
        ]]),
        "[Member.ProfilePicture]" => $options["ProfilePicture"]
       ]);
      }
     }
    }
   } elseif($searchType == "MBR-ALB") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "b6728e167b401a5314ba47dd6e4a55fd";
    if($notAnon == 1) {
     $username = base64_decode($data["UN"]);
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $fs = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]);
     $albums = $fs["Albums"] ?? [];
     foreach($albums as $key => $value) {
      $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]);
      $tP = $t["Privacy"];
      $nsfw = $value["NSFW"] ?? $t["Privacy"]["NSFW"];
      $privacy = $value["Privacy"] ?? $t["Privacy"]["Albums"];
      $bl = $this->core->CheckBlocked([
       $y,
       "Albums",
       base64_encode($t["Login"]["Username"]."-$key")
      ]);
      $check = ($nsfw == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $check2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $privacy,
       "UN" => $t["Login"]["Username"],
       "Y" => $you
      ]);
      $illegal = $value["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $check = ($bl == 0 && $check == 1 && $check2 == 1 && $illegal == 0) ? 1 : 0;
      if($check == 1 || $username == $you) {
       $coverPhoto = $value["CoverPhoto"] ?? "";
       $coverPhoto = base64_encode($t["Login"]["Username"]."-".explode(".", $coverPhoto)[0]);
       array_push($_Commands, []);
       array_push($_List, [
        "[Album.CRID]" => $key,
        "[Album.CoverPhoto]" => $this->core->CoverPhoto($coverPhoto),
        "[Album.Lobby]" => base64_encode("v=".base64_encode("Album:Home")."&AddTo=$addTo&AID=$key&UN=$username"),
        "[Album.Title]" => $value["Title"]
       ]);
      }
     }
    }
   } elseif($searchType == "MBR-BLG") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ed27ee7ba73f34ead6be92293b99f844";
    $_Query = "SELECT * FROM Blogs
                        JOIN Members
                        ON Member_Username=Blog_Username
                        WHERE (Blog_Description LIKE :Search OR
                                      Blog_Title LIKE :Search)
                        AND Blog_Username=:Username
                        ORDER BY Blog_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    if($notAnon == 1) {
     $home = base64_encode("Blog:Home");
     $sql->query($_Query, [
      ":Search" => $querysql,
      ":Username" => $you
     ]);
     $sql = $sql->set();
     if(count($sql) <= $limit) {
      $end = 1;
     } foreach($sql as $sql) {
      $bl = $this->core->CheckBlocked([$y, "Blogs", $sql["Blog_ID"]]);
      $_Blog = $this->core->GetContentData([
       "AddTo" => $addTo,
       "Blacklisted" => $bl,
       "ID" => base64_encode("Blog;".$sql["Blog_ID"])
      ]);
      if($_Blog["Empty"] == 0) {
       $options = $_Blog["ListItem"]["Options"];
       $blog = $_Blog["DataModel"];
       $illegal = $blog["Illegal"] ?? 0;
       $illegal = ($illegal >= $this->illegal) ? 1 : 0;
       if($illegal == 0) {
        array_push($_Commands, []);
        array_push($_List, [
         "[Info.CoverPhoto]" => $_Blog["ListItem"]["CoverPhoto"],
         "[Info.Description]" => $_Blog["ListItem"]["Description"],
         "[Info.Title]" => $_Blog["ListItem"]["Title"],
         "[Info.View]" => $_Blog["ListItem"]["Options"]["View"]
        ]);
       }
      }
     }
    }
   } elseif($searchType == "MBR-CA" || $searchType == "MBR-JE") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "90bfbfb86908fdc401c79329bedd7df5";
    $_Query = "SELECT * FROM Articles
                       JOIN Members
                       ON Member_Username=Article_Username
                       WHERE (Article_Body LIKE :Search OR
                                     Article_Description LIKE :Search OR
                                     Article_Title LIKE :Search)
                       AND Article_Username=:Username
                       ORDER BY Article_Created DESC
                       LIMIT $limit
                       OFFSET $offset";
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $bl = $this->core->CheckBlocked([$t, "Members", $you]);
    $sql->query($_Query, [
     ":Search" => $querysql,
     ":Username" => $t["Login"]["Username"]
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $cms = $this->core->Data("Get", ["cms", md5($t["Login"]["Username"])]);
     $backTo = ($t["Login"]["Username"] == $you) ? "Your Profile" : $t["Personal"]["DisplayName"]."'s Profile";
     $_Article = $this->core->GetContentData([
      "AddTo" => $addTo,
      "BackTo" => $backTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Page;".$sql["Article_ID"]),
      "ParentPage" => $parentView
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
      $check = ($article["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $check2 = $this->core->CheckPrivacy([
       "Contacts" => $cms["Contacts"],
       "Privacy" => $privacy,
       "UN" => $article["UN"],
       "Y" => $you
      ]);
      $check3 = ($illegal == 0 && $article["Category"] == $searchType) ? 1 : 0;
      $check = ($check == 1 && $check2 == 1 && $check3 == 1) ? 1 : 0;
      $check2 = ($bl == 0 || $t["Login"]["Username"] == $you) ? 1 : 0;
      if($check == 1 && $check2 == 1) {
       array_push($_Commands, []);
       array_push($_List, [
        "[Article.Subtitle]" => "Posted by ".$t["Personal"]["DisplayName"]." ".$this->core->TimeAgo($article["Created"]).".",
        "[Article.Description]" => $_Article["ListItem"]["Description"],
        "[Article.ParentPage]" => $parentView,
        "[Article.Title]" => $_Article["ListItem"]["Title"],
        "[Article.ViewPage]" => $options["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "MBR-Chat" || $searchType == "MBR-GroupChat") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "343f78d13872e3b4e2ac0ba587ff2910";
    $_Query = "SELECT * FROM Chat
                        JOIN Members
                        ON Member_Username=Chat_Username
                        WHERE (Chat_Description LIKE :Search OR
                                      Chat_Title LIKE :Search)
                        AND Chat_Username=:Username
                        ORDER BY Chat_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $group = $data["Group"] ?? 0;
    $integrated = $data["Integrated"] ?? 0;
    $oneOnOne = $data["1on1"] ?? 0;
    if($notAnon == 1) {
     $extension = "343f78d13872e3b4e2ac0ba587ff2910";
     $extension = ($integrated == 0) ? "183d39e5527b3af3e7652181a0e36e25" : $extension;
     $extension = $this->core->Extension($extension);
     if($group == 1) {
      $sql->query($_Query, [
       ":Search" => $querysql,
       ":Username" => $you
      ]);
      $sql = $sql->set();
      if(count($sql) <= $limit) {
       $end = 1;
      } foreach($sql as $sql) {
       $active = 0;
       $bl = $this->core->CheckBlocked([$y, "Group Chats", $sql["Chat_ID"]]);
       $_Chat = $this->core->GetContentData([
        "AddTo" => $addTo,
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
         array_push($_Commands, []);
         array_push($_List, [
          "[Chat.DisplayName]" => $displayName.$verified,
          "[Chat.Online]" => "",
          "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%"),
          "[Chat.View]" => $_Chat["ListItem"]["Options"]["View"]
         ]);
        }
       }
      }
     } elseif($oneOnOne == 1) {
      $chat = $this->core->Data("Get", ["chat", md5($you)]);
      $contacts = [];
      $messages = $chat["Messages"] ?? [];
      foreach($messages as $key => $message) {
       array_push($contacts, $message["To"]);
      }
      $contacts = array_unique($contacts);
      foreach($contacts as $key => $member) {
       $bl = $this->core->CheckBlocked([$y, "Members", $member]);;
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
        array_push($_Commands, []);
        array_push($_List, [
         "[Chat.DisplayName]" => $t["Personal"]["DisplayName"],
         "[Chat.Online]" => $online,
         "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%"),
         "[Chat.View]" => base64_encode($view)
        ]);
       }
      }
     }
    }
   } elseif($searchType == "MBR-Forums") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ed27ee7ba73f34ead6be92293b99f844";
    $_Query = "SELECT * FROM Forums
                        JOIN Members
                        ON Member_Username=Forum_Username
                        WHERE (Forum_Description LIKE :Search OR
                                      Forum_Title LIKE :Search)
                        AND Forum_Username=:Username
                        ORDER BY Forum_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $home = base64_encode("Forum:Home");
    $sql->query($_Query, [
     ":Search" => $querysql,
     ":Username" => $you
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Forums", $sql["Forum_ID"]]);;
     $_Forum = $this->core->GetContentData([
      "AddTo" => $addTo,
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
       array_push($_Commands, []);
       array_push($_List, [
        "[Info.CoverPhoto]" => $_Forum["ListItem"]["CoverPhoto"],
        "[Info.Description]" => $_Forum["ListItem"]["Description"],
        "[Info.Title]" => $_Forum["ListItem"]["Title"],
        "[Info.View]" => $options["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "MBR-LLP") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "da5c43f7719b17a9fab1797887c5c0d1";
    if($notAnon == 1) {
     $articles = $y["Pages"] ?? [];
     foreach($articles as $key => $value) {
      $bl = $this->core->CheckBlocked([$y, "Pages", $value]);;
      $_Article = $this->core->GetContentData([
       "AddTo" => $addTo,
       "Blacklisted" => $bl,
       "ID" => base64_encode("Page;$value")
      ]);
      if($_Article["Empty"] == 0) {
       $article = $_Article["DataModel"];
       $options = $_Article["ListItem"]["Options"];
       array_push($_List, [
        "[Extension.Category]" => $article["Category"],
        "[Extension.Delete]" => $options["Delete"],
        "[Extension.Description]" => $_Article["ListItem"]["Description"],
        "[Extension.Edit]" => $options["Edit"],
        "[Extension.ID]" => $value,
        "[Extension.Title]" => $_Article["ListItem"]["Title"]
       ]);
      }
     }
    }
   } elseif($searchType == "MBR-Polls") {
    $_AccessCode = "Accepted";
    $_Extension = $this->core->AESencrypt($this->core->Element([
     "div", $this->core->Extension("184ada666b3eb85de07e414139a9a0dc"), [
      "class" => "Frosted Poll[Poll.ID] Rounded"
     ]
    ]));
    $_Query = "SELECT * FROM Polls
                        JOIN Members
                        ON Member_Username=Poll_Username
                        WHERE (Poll_Description LIKE :Search OR
                                      Poll_Title LIKE :Search)
                        AND Poll_Username=:Username
                        ORDER BY Poll_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql,
     ":Username" => $you
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Polls", $sql["Poll_ID"]]);
     $_Poll = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Poll;".$sql["Poll_ID"])
     ]);
     if($_Poll["Empty"] == 0) {
      $poll = $_Poll["DataModel"];
      $check = ($poll["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      if($bl == 0 && $check == 1) {
       $options = $_Poll["ListItem"]["Options"];
       $blockOrDelete = ($sql["Poll_Username"] == $you) ? $this->core->Element([
        "div", $this->core->Element(["button", "Block", [
         "class" => "Block v2 v2w",
         "data-view" => $options["Block"]
        ]]), ["class" => "Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Delete", [
         "class" => "OpenDialog v2 v2w",
         "data-encryption" => "AES",
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
          "data-container" => ".Poll".$sql["Poll_ID"],
          "data-encryption" => "AES",
          "data-view" => $this->core->AESencrypt("v=".base64_encode("Poll:Vote")."&Choice=".base64_encode($number)."&ID=".base64_encode($sql["Poll_ID"]))
         ]]);
        }
        $vote .= $option;
       }
       array_push($_Commands, []);
       array_push($_List, [
        "[Poll.BlockOrDelete]" => $blockOrDelete,
        "[Poll.Description]" => $_Poll["ListItem"]["Description"],
        "[Poll.ID]" => $sql["Poll_ID"],
        "[Poll.Share]" => $options["Share"],
        "[Poll.Title]" => $_Poll["ListItem"]["Title"],
        "[Poll.Vote]" => $vote
       ]);
      }
     }
    }
   } elseif($searchType == "MBR-SU") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "18bc18d5df4b3516c473b82823782657";
    $_Query = "SELECT * FROM StatusUpdates
                        JOIN Members
                        ON Member_Username=StatusUpdate_Username
                        WHERE StatusUpdate_Body LIKE :Body
                        AND (StatusUpdate_To=:Username OR
                                 StatusUpdate_Username=:Username)
                        ORDER BY StatusUpdate_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Body" => $querysql,
     ":Username" => base64_decode($data["UN"])
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $id = $value["UpdateID"] ?? "";
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $sql["StatusUpdate_ID"]]);
     $_StatusUpdate = $this->core->GetContentData([
      "AddTo" => $addTo,
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
       $cms = $this->core->Data("Get", ["cms", md5($from)]);
       $check = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $update["NSFW"] == 0) ? 1 : 0;
       $check2 = $this->core->CheckPrivacy([
        "Contacts" => $cms["Contacts"],
        "Privacy" => $update["Privacy"],
        "UN" => $update["From"],
        "Y" => $you
       ]);
       $check2 = 1;
       $passPhrase = $update["PassPhrase"] ?? "";
       if($bl == 0 && ($check == 1 && $check2 == 1)) {
        $body = (empty($passPhrase)) ? $_StatusUpdate["ListItem"]["Body"] : $this->ContentIsProtected;
        $display = ($from == $this->core->ID) ? "Anonymous" : $op["Personal"]["DisplayName"];
        $options = $_StatusUpdate["ListItem"]["Options"];
        $edit = ($op["Login"]["Username"] == $you) ? $this->core->Element([
         "button", "Delete", [
          "class" => "InnerMargin OpenDialog",
          "data-encryption" => "AES",
          "data-view" => $options["Delete"]
         ]
        ]).$this->core->Element([
         "button", "Edit", [
          "class" => "InnerMargin OpenCard",
          "data-encryption" => "AES",
          "data-view" => $options["Edit"]
         ]
        ]) : "";
        $verified = $op["Verified"] ?? 0;
        $verified = ($verified == 1) ? $this->core->VerificationBadge() : "";
        array_push($_Commands, [
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Attachments".$sql["StatusUpdate_ID"],
           $_StatusUpdate["ListItem"]["Attachments"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Notes".$sql["StatusUpdate_ID"],
           $options["Notes"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Vote".$sql["StatusUpdate_ID"],
           $options["Vote"]
          ]
         ],
        ]);
        array_push($_List, [
         "[StatusUpdate.Body]" => $body,
         "[StatusUpdate.Created]" => $this->core->TimeAgo($update["Created"]),
         "[StatusUpdate.DT]" => $options["View"],
         "[StatusUpdate.Edit]" => $edit,
         "[StatusUpdate.ID]" => $sql["StatusUpdate_ID"],
         "[StatusUpdate.Modified]" => $_StatusUpdate["ListItem"]["Modified"],
         "[StatusUpdate.OriginalPoster]" => $display.$verified,
         "[StatusUpdate.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:5%;width:90%")
        ]);
       }
      }
     }
    }
   } elseif($searchType == "MBR-XFS") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e15a0735c2cb8fa2d508ee1e8a6d658d";
    $t = $data["UN"] ?? base64_encode($you);
    $t = base64_decode($t);
    $t = ($t == $you) ? $y : $this->core->Member($t);
    $database = ($t["Login"]["Username"] == $this->core->ID) ? "CoreMedia" : "Media";
    $_Query = "SELECT * FROM $database
                        JOIN Members
                        ON Member_Username=Media_Username
                        WHERE (Media_Description LIKE :Search OR
                                      Media_Title LIKE :Search)
                        AND Media_Username=:Username
                        ORDER BY Media_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $albumID = $data["AID"] ?? md5("unsorted");
    $fileSystem = $this->core->Data("Get", ["fs", md5($t["Login"]["Username"])]);
    $sql->query($_Query, [
     ":Database" => $database,
     ":Search" => $querysql,
     ":Username" => $t["Login"]["Username"]
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $attachmentID = base64_encode($sql["Media_Username"]."-".$sql["Media_ID"]);
     $bl = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
     $_File = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("File;".$sql["Media_Username"].";".$sql["Media_ID"])
     ]);
     $file = $_File["DataModel"];
     if($_File["Empty"] == 0 && $bl == 0 && $albumID == $file["AID"]) {
      $options = $_File["ListItem"]["Options"];
      $source = $this->core->GetSourceFromExtension([$t["Login"]["Username"], $file]);
      array_push($_Commands, []);
      array_push($_List, [
       "[File.CoverPhoto]" => $source,
       "[File.Title]" => $file["Title"],
       "[File.View]" => $options["View"]
      ]);
     }
    }
   } elseif($searchType == "Media") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e15a0735c2cb8fa2d508ee1e8a6d658d";
    $_Query = "SELECT * FROM Media
                        JOIN Members
                        ON Member_Username=Media_Username
                        WHERE Media_Description LIKE :Search OR
                                      Media_Title LIKE :Search OR
                                      Media_Username LIKE :Search
                        ORDER BY Media_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $attachmentID = base64_encode($sql["Media_Username"]."-".$sql["Media_ID"]);
     $bl = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
     $_File = $this->core->GetContentData([
      "AddTo" => $addTo,
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
      array_push($_Commands, []);
      array_push($_List, [
       "[File.CoverPhoto]" => $source,
       "[File.Title]" => $file["Title"],
       "[File.View]" => $options["View"]
      ]);
     }
    }
   } elseif($searchType == "Polls") {
    $_AccessCode = "Accepted";
    $_Extension = $this->core->AESencrypt($this->core->Element([
     "div", $this->core->Extension("184ada666b3eb85de07e414139a9a0dc"), [
      "class" => "Frosted Poll[Poll.ID] Rounded"
     ]
    ]));
    $_Query = "SELECT * FROM Polls
                        JOIN Members
                        ON Member_Username=Poll_Username
                        WHERE Poll_Description LIKE :Search OR
                                      Poll_Title LIKE :Search
                        ORDER BY Poll_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Polls", $sql["Poll_ID"]]);
     $_Poll = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Poll;".$sql["Poll_ID"])
     ]);
     if($_Poll["Empty"] == 0) {
      $poll = $_Poll["DataModel"];
      $check = ($poll["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      if($bl == 0 && $check == 1) {
       $options = $_Poll["ListItem"]["Options"];
       $blockOrDelete = ($sql["Poll_Username"] == $you) ? $this->core->Element([
        "div", $this->core->Element(["button", "Block", [
         "class" => "Block v2 v2w",
         "data-view" => $options["Block"]
        ]]), ["class" => "Desktop33"]
       ]).$this->core->Element([
        "div", $this->core->Element(["button", "Delete", [
         "class" => "OpenDialog v2 v2w",
         "data-view" => $options["Delete"]
        ]]), ["class" => "Desktop33"]
       ]) : "";
       $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
       $addTo = (!empty($addToData)) ? $this->core->Element([
        "div", $this->core->Element(["button", "Attach", [
         "class" => "Attach v2 v2w",
         "data-input" => base64_encode($addToData[1]),
         "data-media" => base64_encode("Poll;".$sql["Poll_ID"])
        ]]), ["class" => "Desktop33"]
       ]) : "";
       $blockOrDelete = ($this->core->ID != $you) ? $addTo.$blockOrDelete : $blockOrDelete;
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
          "data-container" => ".Poll".$sql["Poll_ID"],
          "data-view" => base64_encode("v=".base64_encode("Poll:Vote")."&Choice=".base64_encode($number)."&ID=".base64_encode($sql["Poll_ID"]))
         ]]);
        }
        $vote .= $option;
       }
       array_push($_Commands, []);
       array_push($_List, [
        "[Poll.BlockOrDelete]" => $blockOrDelete,
        "[Poll.Description]" => $_Poll["ListItem"]["Description"],
        "[Poll.ID]" => $sql["Poll_ID"],
        "[Poll.Share]" => $options["Share"],
        "[Poll.Title]" => $_Poll["ListItem"]["Title"],
        "[Poll.Vote]" => $vote
       ]);
      }
     }
    }
   } elseif($searchType == "Products") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ed27ee7ba73f34ead6be92293b99f844";
    $_Query = "SELECT * FROM Products
                        JOIN Members
                        ON Member_Username=Product_Username
                        WHERE Product_Description LIKE :Search OR
                                      Product_Title LIKE :Search
                        ORDER BY Product_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $b2 = $b2 ?? "Products";
     $bl = $this->core->CheckBlocked([$y, "Products", $sql["Product_ID"]]);
     $_Product = $this->core->GetContentData([
      "AddTo" => $addTo,
      "BackTo" => $b2,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Product;".$sql["Product_ID"])
     ]);
     if($_Product["Empty"] == 0) {
      $product = $_Product["DataModel"];
      $bl = $this->core->CheckBlocked([$y, "Members", $sql["Product_Username"]]);
      $owner = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Member;".md5($sql["Product_Username"]))
      ]);
      $check = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $check2 = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
      $check3 = $owner["Subscriptions"]["Artist"]["A"] ?? 0;
      $check = ($check == 1 && $check2 == 1 && $check3 == 1) ? 1 : 0;
      $check = ($check == 1 || $sql["Product_Username"] == $this->core->ShopID) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $illegal = ($sql["Product_Username"] != $this->core->ShopID) ? 1 : 0;
      if($bl == 0 && $check == 1 && $illegal == 0) {
       $options = $_Product["ListItem"]["Options"];
       array_push($_Commands, []);
       array_push($_List, [
        "[Info.CoverPhoto]" => $_Product["ListItem"]["CoverPhoto"],
        "[Info.Description]" => $_Product["ListItem"]["Description"],
        "[Info.Title]" => $_Product["ListItem"]["Title"],
        "[Info.View]" => $options["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "SHOP") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "6d8aedce27f06e675566fd1d553c5d92";
    $_Query = "SELECT * FROM Shops
                        JOIN Members
                        ON Member_Username=Shop_Username
                        WHERE Shop_Description LIKE :Search OR
                                      Shop_Title LIKE :Search OR
                                      Shop_Welcome LIKE :Search
                        ORDER BY Shop_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    if($notAnon == 1) {
     $b2 = $b2 ?? "Artists";
     $sql->query($_Query, [
      ":Search" => $querysql
     ]);
     $sql = $sql->set();
     if(count($sql) <= $limit) {
      $end = 1;
     } foreach($sql as $sql) {
      $bl = $this->core->CheckBlocked([$y, "Members", $sql["Shop_Username"]]);
      $_Shop = $this->core->GetContentData([
       "AddTo" => $addTo,
       "Blacklisted" => $bl,
       "ID" => base64_encode("Shop;".$sql["Shop_ID"])
      ]);
      if($_Shop["Empty"] == 0) {
       $cms = $this->core->Data("Get", ["cms", $sql["Shop_ID"]]);
       $cms = $cms["Contacts"] ?? [];
       $t = $this->core->Member($sql["Shop_Username"]);
       $check = $this->core->CheckPrivacy([
        "Contacts" => $cms,
        "Privacy" => $t["Privacy"]["Shop"],
        "UN" => $sql["Shop_Username"],
        "Y" => $you
       ]);
       $shop = $_Shop["DataModel"];
       $check2 = $shop["Open"] ?? 0;
       if(($bl == 0 && $check == 1 && $check2 == 1) || $sql["Shop_Username"] == $you) {
        array_push($_Commands, []);
        array_push($_List, [
         "[Shop.CoverPhoto]" => $_Shop["ListItem"]["CoverPhoto"],
         "[Shop.Description]" => $_Shop["ListItem"]["Description"],
         "[Shop.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:5%;width:90%"),
         "[Shop.Title]" => $_Shop["ListItem"]["Title"],
         "[Shop.View]" => $_Shop["ListItem"]["Options"]["View"]
        ]);
       }
      }
     }
    }
   } elseif($searchType == "SHOP-InvoicePresets") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e9f34ca1985c166bf7aa73116a745e92";
    $shop = $this->core->Data("Get", ["shop", $data["Shop"]]);
    $invoicePresets = $shop["InvoicePresets"] ?? [];
    foreach($invoicePresets as $key => $value) {
     $preset = $this->core->Data("Get", ["invoice-preset", $value]);
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
      array_push($_Commands, []);
      array_push($_List, [
       "[ListItem.Description]" => "A service currently on offer by ".$shop["Title"],
       "[ListItem.Options]" => $options,
       "[ListItem.Title]" => $preset["Title"]
      ]);
     }
    }
   } elseif($searchType == "SHOP-Invoices") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "e9f34ca1985c166bf7aa73116a745e92";
    $shop = $this->core->Data("Get", ["shop", $data["Shop"]]);
    $invoices = $shop["Invoices"] ?? [];
    foreach($invoices as $key => $value) {
     $invoice = $this->core->Data("Get", ["invoice", $value]);
     if(!empty($invoice)) {
      $options = $this->core->Element(["button", "Forward", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Invoice:Forward")."&Invoice=$value&Shop=".$data["Shop"])
      ]]).$this->core->Element(["button", "View", [
       "class" => "OpenCard v2",
       "data-view" => base64_encode("v=".base64_encode("Invoice:Home")."&Card=1&ID=$value")
      ]]);
      array_push($_Commands, []);
      array_push($_List, [
       "[ListItem.Description]" => "An Invoice created by ".$invoice["UN"]." &bull; Status: ".$invoice["Status"].".",
       "[ListItem.Options]" => $options,
       "[ListItem.Title]" => "Invoice $value"
      ]);
     }
    }
   } elseif($searchType == "SHOP-Orders") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "504e2a25db677d0b782d977f7b36ff30";
    $purchaseOrders = $this->core->Data("Get", ["po", md5($you)]);
    foreach($purchaseOrders as $key => $value) {
     $member = $this->core->Member($value["UN"]);
     if(!empty($member["Login"])) {
      $complete = ($value["Complete"] == 0) ? $this->core->Element(["button", "Mark as Complete", [
       "class" => "BBB CompleteOrder v2 v2w",
       "data-u" => base64_encode("v=".base64_encode("Shop:CompleteOrder")."&ID=".base64_encode($key))
      ]]) : "";
      array_push($_Commands, []);
      array_push($_List, [
       "[X.LI.Order.Complete]" => $complete,
       "[X.LI.Order.Instructions]" => base64_decode($value["Instructions"]),
       "[X.LI.Order.ProductID]" => $value["ProductID"],
       "[X.LI.Order.ProfilePicture]" => $this->core->ProfilePicture($member, "margin:5%;width:90%"),
       "[X.LI.Order.Quantity]" => $value["QTY"],
       "[X.LI.Order.UN]" => $value["UN"]
      ]);
     }
    }
   } elseif($searchType == "SHOP-Products") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ed27ee7ba73f34ead6be92293b99f844";
    $_Query = "SELECT * FROM Products
                        JOIN Members
                        ON Member_Username=Product_Username
                        WHERE (Product_Description LIKE :Search OR
                                      Product_Title LIKE :Search)
                        AND Product_Shop=:Shop
                        ORDER BY Product_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $username = $data["UN"] ?? base64_encode($you);
    $username = base64_decode($username);
    $sql->query($_Query, [
     ":Search" => $querysql,
     ":Shop" => md5($username)
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Products", $sql["Product_ID"]]);
     $_Product = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Product;".$sql["Product_ID"])
     ]);
     if($_Product["Empty"] == 0) {
      $product = $_Product["DataModel"];
      $check = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $check2 = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
      $check3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
      $check = ($check == 1 && $check2 == 1 && $check3 == 1) ? 1 : 0;
      $check = ($check == 1 || $sql["Product_Username"] == $this->core->ShopID) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $illegal = ($sql["Product_Username"] != $this->core->ShopID) ? 1 : 0;
      if($bl == 0 && $check == 1 && $illegal == 0) {
       $options = $_Product["ListItem"]["Options"];
       array_push($_Commands, []);
       array_push($_List, [
        "[Info.CoverPhoto]" => $_Product["ListItem"]["CoverPhoto"],
        "[Info.Description]" => $_Product["ListItem"]["Description"],
        "[Info.Title]" => $_Product["ListItem"]["Title"],
        "[Info.View]" => $options["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "StatusUpdates") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "18bc18d5df4b3516c473b82823782657";
    $_Query = "SELECT * FROM StatusUpdates
                        JOIN Members
                        ON Member_Username=StatusUpdate_Username
                        WHERE StatusUpdate_Body LIKE :Body OR
                                      StatusUpdate_Username LIKE :Username
                        ORDER BY StatusUpdate_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Body" => $querysql,
     ":Username" => $querysql
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Status Updates", $sql["StatusUpdate_ID"]]);
     $_StatusUpdate = $this->core->GetContentData([
      "AddTo" => $addTo,
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
       $cms = $this->core->Data("Get", ["cms", md5($from)]);
       $privacy = $op["Privacy"]["Posts"] ?? md5("Public");
       $check = $update["NSFW"] ?? 0;
       $check = ($y["Personal"]["Age"] >= $this->core->config["minAge"] || $check == 0) ? 1 : 0;
       $check2 = $cms["Contacts"] ?? [];
       $check2 = $this->core->CheckPrivacy([
        "Contacts" => $check2,
        "Privacy" => $privacy,
        "UN" => $from,
        "Y" => $you
       ]);
       $passPhrase = $update["PassPhrase"] ?? "";
       if($check == 1 && $check2 == 1) {
        $addToData = (!empty($addTo)) ? explode(":", base64_decode($addTo)) : [];
        $body = (empty($passPhrase)) ? $_StatusUpdate["ListItem"]["Body"] : $this->ContentIsProtected;
        $created = $update["Created"] ?? $this->core->timestamp;
        $options = $_StatusUpdate["ListItem"]["Options"];
        $display = $op["Personal"]["DisplayName"] ?? $from;
        $display = ($from == $this->core->ID) ? "Anonymous" : $display;
        $edit = (!empty($addToData)) ? $this->core->Element([
         "button", "Attach", [
          "class" => "Attach InnerMargin",
          "data-input" => base64_encode($addToData[1]),
          "data-media" => base64_encode("StatusUpdate;".$sql["StatusUpdate_ID"])
         ]
        ]) : "";
        $edit .= ($from == $you) ? $this->core->Element([
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
        array_push($_Commands, [
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Attachments".$sql["StatusUpdate_ID"],
           $_StatusUpdate["ListItem"]["Attachments"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Notes".$sql["StatusUpdate_ID"],
           $options["Notes"]
          ]
         ],
         [
          "Name" => "UpdateContentAES",
          "Parameters" => [
           ".Vote".$sql["StatusUpdate_ID"],
           $options["Vote"]
          ]
         ],
        ]);
        array_push($_List, [
         "[StatusUpdate.Body]" => $body,
         "[StatusUpdate.Created]" => $this->core->TimeAgo($update["Created"]),
         "[StatusUpdate.DT]" => $options["View"],
         "[StatusUpdate.Edit]" => $edit,
         "[StatusUpdate.ID]" => $sql["StatusUpdate_ID"],
         "[StatusUpdate.Modified]" => $_StatusUpdate["ListItem"]["Modified"],
         "[StatusUpdate.OriginalPoster]" => $display.$verified,
         "[StatusUpdate.ProfilePicture]" => $this->core->ProfilePicture($op, "margin:5%;width:90%")
        ]);
       }
      }
     }
    }
   } elseif($searchType == "VVA") {
    $_AccessCode = "Accepted";
    $_ExtensionID = "ed27ee7ba73f34ead6be92293b99f844";
    $_Query = "SELECT * FROM Products
                        JOIN Members
                        ON Member_Username=Product_Username
                        WHERE (Product_Description LIKE :Search OR
                                      Product_Title LIKE :Search)
                        AND Product_Category='Architecture'
                        AND Product_Shop=:Shop
                        ORDER BY Product_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $sql->query($_Query, [
     ":Search" => $querysql,
     ":Shop" => md5($this->core->ShopID)
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Products", $sql["Product_ID"]]);
     $_Product = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("Product;".$sql["Product_ID"])
     ]);
     if($_Product["Empty"] == 0) {
      $product = $_Product["DataModel"];
      $check = ($product["NSFW"] == 0 || ($y["Personal"]["Age"] >= $this->core->config["minAge"])) ? 1 : 0;
      $check2 = (strtotime($this->core->timestamp) < $product["Expires"]) ? 1 : 0;
      $check3 = $t["Subscriptions"]["Artist"]["A"] ?? 0;
      $check = ($check == 1 && $check2 == 1 && $check3 == 1) ? 1 : 0;
      $check = ($check == 1 || $sql["Product_Username"] == $this->core->ShopID) ? 1 : 0;
      $illegal = $product["Illegal"] ?? 0;
      $illegal = ($illegal >= $this->illegal) ? 1 : 0;
      $illegal = ($sql["Product_Username"] != $this->core->ShopID) ? 1 : 0;
      if($bl == 0 && $check == 1 && $illegal == 0) {
       $options = $_Product["ListItem"]["Options"];
       array_push($_Commands, []);
       array_push($_List, [
        "[Info.CoverPhoto]" => $_Product["ListItem"]["CoverPhoto"],
        "[Info.Description]" => $_Product["ListItem"]["Description"],
        "[Info.Title]" => $_Product["ListItem"]["Title"],
        "[Info.View]" => $options["View"]
       ]);
      }
     }
    }
   } elseif($searchType == "XFS") {
    $_AccessCode = "Accepted";
    $_Username = $data["UN"] ?? base64_encode($you);
    $_Username = base64_decode($_Username);
    $_Database = ($_Username == $this->core->ID) ? "CoreMedia" : "Media";
    $_ExtensionID = "e15a0735c2cb8fa2d508ee1e8a6d658d";
    $_Query = "SELECT * FROM $_Database
                        JOIN Members
                        ON Member_Username=Media_Username
                        WHERE (Media_Description LIKE :Search OR
                                      Media_Title LIKE :Search)
                        AND Media_Username=:Username
                        ORDER BY Media_Created DESC
                        LIMIT $limit
                        OFFSET $offset";
    $mediaType = $data["ftype"] ?? "";
    $sql->query($_Query, [
     ":Database" => $_Database,
     ":Search" => $querysql,
     ":Username" => $_Username
    ]);
    $sql = $sql->set();
    if(count($sql) <= $limit) {
     $end = 1;
    } foreach($sql as $sql) {
     $attachmentID = base64_encode($sql["Media_Username"]."-".$sql["Media_ID"]);
     $bl = $this->core->CheckBlocked([$y, "Files", $attachmentID]);
     $_File = $this->core->GetContentData([
      "AddTo" => $addTo,
      "Blacklisted" => $bl,
      "ID" => base64_encode("File;".$sql["Media_Username"].";".$sql["Media_ID"])
     ]);
     if($_File["Empty"] == 0) {
      $file = $_File["DataModel"];
      $options = $_File["ListItem"]["Options"];
      $source = $this->core->GetSourceFromExtension([$sql["Media_Username"], $sql["Media_ID"]]);
      $media = [
       "[File.CoverPhoto]" => $source,
       "[File.Title]" => $file["Title"],
       "[File.View]" => $options["View"]
      ];
      if(empty($mediaType)) {
       array_push($_Commands, []);
       array_push($_List, $media);
      } else {
       $mediaTypes = json_decode(base64_decode($mediaType));
       foreach($mediaTypes as $mediaTypes) {
        if($this->core->CheckFileType([$file["EXT"], $mediaTypes]) == 1) {
         array_push($_Commands, []);
         array_push($_List, $media);
        }
       }
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $_AccessCode,
    "Extension" => $_Extension,
    "ExtensionID" => $_ExtensionID,
    "Response" => [
     "Commands" => $_Commands,
     "End" => "$end",
     "Limit" => $limit,
     "List" => $_List,
     "NoResults" => $this->core->AESencrypt($this->core->Element([
      "h3", $na, ["class" => "CenterText InnerMargin UpperCase"]
     ])),
     "Offset" => $offset
    ]
   ]);
  }
  function ReSearch(array $data): string {
   $_Commands = "";
   $_View = "";
   $_ViewTitle = "Re:Search";
   $data = $data["Data"] ?? [];
   $component = $data["Component"] ?? base64_encode("");
   $component = base64_decode($component);
   $query = $data["query"] ?? $this->core->AESencrypt("");
   $query = $this->core->AESdecrypt(htmlentities($query));
   $y = $this->core->you;
   $you = $y["Login"]["Username"];
   if($component == "SuggestedMembers") {
    $_Query = "SELECT * FROM Members
                        #WHERE Member_Descriptction LIKE :Search OR
                        WHERE Member_DisplayName LIKE :Search OR
                                      Member_Username LIKE :Search
                        ORDER BY Member_Created DESC
                        LIMIT 100";
    $_ViewTitle = "$query via $_ViewTitle";
    $sql = New SQL($this->core->cypher->SQLCredentials());
    $sql->query($_Query, [
     ":Search" => $query
    ]);
    $sql = $sql->set();
    foreach($sql as $sql) {
     $bl = $this->core->CheckBlocked([$y, "Members", $sql["Member_Username"]]);
     $_Member = $this->core->GetContentData([
      "Blacklisted" => $bl,
      "ID" => base64_encode("Member;".md5($sql["Member_Username"]))
     ]);
     $member = $_Member["DataModel"];
     if($_Member["Empty"] == 0) {
      $them = $member["Login"]["Username"];
      $cms = $this->core->Data("Get", ["cms", md5($them)]);
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
       $profilePicture = $this->core->ProfilePicture($member, "max-width:4em;width:100%");
       $_View .= $this->core->Element(["div", $this->core->Element([
        "button", $profilePicture, [
         "class" => "OpenCard v1",
         "data-view" => $options["View"]
        ]
       ]), ["class" => "Small"]]);
      }
     }
    }
   } else {
    $search = $this->lists;
    $secureQuery = $this->core->AESdecrypt($query);
    $suggestedMembers = $this->view(base64_encode("Search:ReSearch"), ["Data" => [
     "Component" => base64_encode("SuggestedMembers"),
     "query" => $data["query"]
    ]]);
    $_Commands = [
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarArchive')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarArtists')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarBlogs')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarChat')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarForums')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarLinks')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarMedia')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarMembers')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarPolls')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarProducts')"
      ]
     ],
     [
      "Name" => "LightSearch",
      "Parameters" => [
       "$(document).find('.SearchBarStatusUpdates')"
      ]
     ]
    ];
    $_View = [
     "ChangeData" => [
      "[ReSearch.Archive]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=CA"),
      "[ReSearch.Artists]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=SHOP"),
      "[ReSearch.Blogs]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=BLG"),
      "[ReSearch.Chat]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Chat&Integrated=1"),
      "[ReSearch.Forums]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Forums"),
      "[ReSearch.Links]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Links"),
      "[ReSearch.Media]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Media"),
      "[ReSearch.Members]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=MBR"),
      "[ReSearch.Query]" => $query,
      "[ReSearch.Polls]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Polls"),
      "[ReSearch.Products]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=Products"),
      "[ReSearch.StatusUpdates]" => base64_encode("v=$search&query=$secureQuery&lPG=ReSearch&st=StatusUpdates"),
      "[ReSearch.SuggestedMembers]" => $this->core->RenderView($suggestedMembers)
     ],
     "ExtensionID" => "bae5cdfa85bf2c690cbff302ba193b0b"
    ];
    $_ViewTitle .= (!empty($data["query"])) ? " $query" : "";
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Commands" => $_Commands,
    "Title" => $_ViewTitle,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 require_once(__DIR__."/Cypher.php");
 Class Core {
  function __construct() {
   try {
    $this->cypher = New Cypher;
    $this->DocumentRoot = "/var/www/html";
    $this->ID = "App";
    $this->PayPalMID = base64_decode("Qk5aVjk0TkxYTDJESg==");
    $this->PayPalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $this->SecureityKey = $this->Authenticate("Get");
    $this->ShopID = "Mike";
    $this->base = $this->ConfigureBaseURL();
    $this->config = $this->Configuration();
    $this->efs = $this->ConfigureBaseURL("efs");
    $this->language = $header["Language"] ?? "en_US";
    $this->timestamp = date("Y-m-d h:i:sA");
    $this->you = $this->Member($this->Authenticate("Get"));
    $this->yourGhost = $this->RenderGhostMember();
   } catch(PDOException $error) {
    return $this->Element([
     "p", "Failed to initialize GW... ".$error->getMessage()
    ]);
   }
  }
  function Article(string $id) {
   $article = $this->Data("Get", ["pg", $id]) ?? [];
   $r = $this->Change([[
    "[Error.Back]" => "",
    "[Error.Header]" => "Not Found",
    "[Error.Message]" => "The Article <em>$id</em> could not be found."
   ], $this->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
   if(!empty($article)) {
    $r = $this->PlainText([
     "Data" => $article["Body"],
     "Decode" => 1,
     "Display" => 1,
     "HTMLDecode" => 1
    ]);
   }
   return $r;
  }
  function Authenticate(string $action, $data = []) {
   $action = $action ?? "";
   $data = $data ?? [];
   $r = "";
   if(!empty($action) && (empty($data) || is_array($data))) {
    if($action == "Get") {
     $headers = (function_exists('apache_request_headers') ) ? apache_request_headers() : [];
     $r = $this->ID;
     $token = $headers["Token"] ?? base64_encode("");
     $token = base64_decode($token);
     if(!empty($token)) {
      $token = explode("|", $this->Decrypt($token));
      $login = $token[1] ?? "";
      $login = json_decode($login, true);
      $secret = $token[2] ?? "";
      $secret = json_decode($secret, true);
      $password = $login["Password"] ?? base64_encode($this->ID);
      $password = base64_decode($password);
      $secret = $secret["Secret"] ?? base64_encode("");
      $secret = base64_decode($secret);
      $username = $login["Username"] ?? base64_encode($this->ID);
      $username = base64_decode($username);
      if(md5($this->cypher->key) == $secret) {
       $you = $this->Data("Get", ["mbr", md5($username)]) ?? [];
       $you = $you["Login"] ?? [];
       if(!empty($you["Username"]) && $password == $you["Password"]) {
        $r = $username;
       }
      }
     }
    } elseif($action == "Save") {
     $password = $data["Password"] ?? "";
     $username = $data["Username"] ?? "";
     if(!empty($password) && !empty($username)) {
      $r = json_encode([
       "Time" => base64_encode(strtotime($this->timestamp))
      ], true)."|".json_encode([
       "Password" => base64_encode($password),
       "Username" => base64_encode($username)
      ], true)."|".json_encode([
       "Secret" => base64_encode(md5($this->cypher->key))
      ], true);
      $r = $this->Encrypt($r);
     }
    }
   }
   return $r;
  }
  function ByteNotation(int $a, $b = "MB") {
   $units = [
    "GB" => number_format($a / 1073741824, 2),
    "KB" => number_format($a / 1024, 2),
    "MB" => number_format($a / 1048576, 2)
   ];
   $r = $units[$b] ?? $units["MB"];
   return $r ?? 0;
  }
  function CallSign($a) {
   return $this->Change([[
    " " => "",
    "'" => "",
    "\"" => "",
    "<" => "",
    ">" => "",
    ":" => "",
    "," => ""
   ], htmlentities($a)]);
  }
  function Change(array $a) {
   $r = $a[1] ?? "";
   $ls = $a[0] ?? "";
   foreach($ls as $k => $v) {
    if(!is_array($k) && !is_array($v)) {
     $r = str_replace($k, $v, $r);
    }
   }
   return $r;
  }
  function CheckBlocked(array $a) {
   $r = 0;
   if(!empty($a[1]) && !empty($a[2])) {
    $x = $a[0]["Blocked"][$a[1]] ?? [];
    foreach($x as $k => $v) {
     if($v == $a[2]) {
      $r++;
     }
    }
   }
   return $r;
  }
  function CheckFileType(array $a) {
   $efs = $this->config["XFS"]["FT"];
   if(isset($a[1]) && in_array($a[1], $efs["_FT"])) {
    if($a[1] == $efs["_FT"][0]) {
     $all = $efs["A"];
    } elseif($a[1] == $efs["_FT"][1]) {
     $all = $efs["D"];
    } elseif($a[1] == $efs["_FT"][2]) {
     $all = $efs["P"];
    } elseif($a[1] == $efs["_FT"][3]) {
     $all = $efs["V"];
    }
   } else {
    $all = array_merge($efs["A"], $efs["D"], $efs["P"], $efs["V"]);
   }
   $r = (in_array($a[0], $all)) ? 1 : 0;
   return $r;
  }
  function CheckPrivacy(array $a) {
   $check = (!empty($a["Contacts"])) ? 1 : 0;
   $check2 = (!empty($a["Privacy"])) ? 1 : 0;
   $check3 = (!empty($a["Y"])) ? 1 : 0;
   $r = 0;
   if($check == 1 || ($check2 == 1 && $check3 == 1)) {
    $privacy = $a["Privacy"] ?? md5("Private");
    $privacy2 = md5("Public");
    $acquaintancesCheck = 0;
    $contactsCheck = 0;
    $closeContactsCheck = 0;
    $lists = [
     "Acquaintances" => md5("Acquaintances"),
     "Close Contacts" => md5("Close Contacts"),
     "Contacts" => md5("Contacts")
    ];
    $contacts = $a["Contacts"] ?? [];
    foreach($contacts as $member => $info) {
     $list = $info["List"] ?? md5("Public");
     $acquaintances = ($member == $a["Y"] && $list == $lists["Acquaintances"]) ? 1 : 0;
     $contacts = ($member == $a["Y"] && $list == $lists["Contacts"]) ? 1 : 0;
     $closeContacts = ($member == $a["Y"] && $list == $lists["Close Contacts"]) ? 1 : 0;
     $acquaintancesCheck = ($acquaintances == 1) ? $acquaintancesCheck++ : $acquaintancesCheck;
     $closeContactsCheck = ($acquaintances == 1 || $closeContacts == 1 || $contacts == 1) ? $closeContactsCheck++ : $closeContactsCheck;
     $contactsCheck = ($closeContacts == 1 || $contacts == 1) ? $contactsCheck++ : $contactsCheck;
    }
    $check = ($privacy == $privacy2) ? 1 : 0;
    $check2 = ($privacy == $lists["Acquaintances"] && $acquaintancesCheck > 0) ? 1 : 0;
    $check3 = ($privacy == $lists["Close Contacts"] && $closeContactsCheck > 0) ? 1 : 0;
    $check4 = ($privacy == $lists["Contacts"] && $contactsCheck > 0) ? 1 : 0;
    $r = ($check == 1 || $check2 == 1 || $check3 == 1 || $check4 == 1) ? 1 : 0;
    $r = ($a["UN"] == $a["Y"] || $r == 1) ? 1 : 0;
   }
   return $r;
  }
  function Configuration() {
   $configurationData = $this->Data("Get", ["app", md5("config")]) ?? [];
   return $configurationData;
  }
  function ConfigureBaseURL($a = NULL) {
   $base = $_SERVER["HTTP_HOST"] ?? "outerhaven.nyc";
   if($a == "efs") {
    $r = "efs.$base/";
   } else {
    $r = $base;
   }
   return $this->ConfigureSecureHTTP().$r;
  }
  function ConfigureSecureHTTP() {
   $r = $_SERVER["HTTPS"] ?? "on";
   $r = (!empty($r) && $r == "on") ? "https" : "http";
   return "$r://";
  }
  function ConvertCalendarMonths(int $a) {
   $r = ($a == "01") ? "January" : $a;
   $r = ($a == "02") ? "February" : $r;
   $r = ($a == "03") ? "March" : $r;
   $r = ($a == "04") ? "April" : $r;
   $r = ($a == "05") ? "May" : $r;
   $r = ($a == "06") ? "June" : $r;
   $r = ($a == "07") ? "July" : $r;
   $r = ($a == "08") ? "August" : $r;
   $r = ($a == "09") ? "September" : $r;
   $r = ($a == 10) ? "October" : $r;
   $r = ($a == 11) ? "November" : $r;
   $r = ($a == 12) ? "December" : $r;
   return $r;
  }
  function CoverPhoto(string $a) {
   $efs = $this->efs;
   $r = $this->PlainText([
    "Data" => "[Media:CP]",
    "Display" => 1
   ]);
   if(!empty($a)) {
    $r = $efs.base64_decode($a);
   }
   return $r;
  }
  function Data(string $action, array $data) {
   if(!empty($data)) {
    $dataFile = $this->DocumentRoot."/data/nyc.outerhaven.".$data[0];
    $dataFile .= (!empty($data[1])) ? ".".$data[1] : "";
    if($action == "Get") {
     if(!file_exists($dataFile)) {
      $r = json_encode([]);
     } else {
      $r = fopen($dataFile, "r");
      $r = fread($r, filesize($dataFile));
      $r = $this->Decrypt($r) ?? json_encode([]);
     }
     return json_decode($r, true);
    } elseif($action == "Purge") {
     if(file_exists($dataFile)) {
      unlink($dataFile);
     }
    } elseif($action == "Save") {
     $data[2] = $data[2] ?? [];
     if(!empty($data[2])) {
      $r = fopen($dataFile, "w+");
      fwrite($r, $this->Encrypt(json_encode($data[2], true)));
      fclose($r);
     }
    }
   }
  }
  function DataIndex(string $action, string $index, $data = []) {
   if(!empty($action) && !empty($index)) {
    $dataFile = $this->DocumentRoot."/data/nyc.outerhaven.app.search-".strtolower($index);
    if($action == "Get") {
     if(!file_exists($dataFile)) {
      $r = json_encode([]);
     } else {
      $r = fopen($dataFile, "r");
      $r = fread($r, filesize($dataFile))?? json_encode([]);
     }
     return json_decode($r, true);
    } elseif($action == "Save") {
     $data = $data ?? [];
     if(!empty($data)) {
      $r = fopen($dataFile, "w+");
      fwrite($r, json_encode($data, true));
      fclose($r);
     }
    }
   }
  }
  function DatabaseSet($a = NULL) {
   $domain = "nyc.outerhaven";
   $list = array_diff(scandir($this->DocumentRoot."/data/"), [
    ".",
    "..",
    "index.html",
    "index.php"
   ]) ?? [];
   foreach($list as $key => $value) {
    if(!empty($a)) {
     if($a == "Article") {
      $a = "$domain.pg.";
     } elseif($a == "Blog") {
      $a = "$domain.blg.";
     } elseif($a == "BlogPost") {
      $a = "$domain.bp.";
     } elseif($a == "Chat") {
      $a = "$domain.chat.";
     } elseif($a == "Extensions") {
      $a = "$domain.extension.";
     } elseif($a == "Feedback") {
      $a = "$domain.feedback.";
     } elseif($a == "Files") {
      $a = "$domain.fs.";
     } elseif($a == "Forum") {
      $a = "$domain.pf.";
     } elseif($a == "MBR") {
      $a = "$domain.mbr.";
     } elseif($a == "Member") {
      $a = "$domain.mbr.";
     } elseif($a == "Poll") {
      $a = "$domain.poll.";
     } elseif($a == "Product") {
      $a = "$domain.product.";
     } elseif($a == "Shop") {
      $a = "$domain.shop.";
     } elseif($a == "StatusUpdate") {
      $a = "$domain.su.";
     } if(strpos($value, $a) !== false) { 
      $list[$key] = $value;
     } else {
      unset($list[$key]);
     }
    } else {
     $list[$key] = $value;
    }
   }
   return $list;
  }
  function DecodeBridgeData(array $data) {
   foreach($data as $key => $value)  {
    if(!is_array($value)) {
     $data[$key] = urldecode(base64_decode($value));
    } else {
     $data[$key] = $this->DecodeBridgeData($value);
    }
   }
   return $data;
  }
  function Decrypt($data) {
   return $this->cypher->Decrypt($data);
  }
  function Element(array $a) {
   $a[2] = $a[2] ?? [];
   $r = "";
   if(!empty($a[0])) {
    $a["DLL"] = $a["DLL"] ?? 0;
    $d = "";
    if(!empty($a[2])) {
     foreach($a[2] as $k => $v) {
      if(empty($v)) {
       $d .= " $k";
      } else {
       $d .= " $k=\"$v\"";
      }
     }
    }
    $r = "<".$a[0]."$d>".$a[1]."</".$a[0].">";
   } else {
    $r = $this->Element(["p", "An Element type must be defined."]);
   }
   return "$r\r\n";
  }
  function Encrypt($data) {
   return $this->cypher->Encrypt($data);
  }
  function Excerpt(string $data, $limit = 180) {
   if(strlen($data) <= $limit) {
    return $data;
   }
   $excerpt = substr($data, 0, $limit);
   $lastSpace = strrpos($excerpt, " ");
   if($lastSpace !== false) {
    $excerpt = substr($excerpt, 0, $lastSpace);
   }
   return $excerpt;
  }
  function Extension(string $id) {
   $extension = $this->Data("Get", ["extension", $id]) ?? [];
   $r = "";
   if(empty($extension)) {
    $r = $this->Change([[
     "[Error.Back]" => "",
     "[Error.Header]" => "Not Found",
     "[Error.Message]" => "The Extension <em>$id</em> could not be found."
    ], $this->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
   } else {
    $body = $extension["Body"] ?? "";
    $r = $this->PlainText([
     "Data" => $body,
     "Display" => 1,
     "HTMLDecode" => 1
    ]);
   }
   return $r;
  }
  function FixMissing(array $a, array $b) {
   foreach($b as $b) {
    $a[$b] = $a[$b] ?? "";
   }
   return $a;
  }
  function Gender(string $a) {
   if($a == "Female") {
    $r = "she;her;her";
   } else {
    $r = "he;him;his";
   }
   return explode(";", $r);
  }
  function GetAttachmentPreview(array $a) {
   $disableButtons = $a["DisableButtons"] ?? 0;
   $type = $a["DLL"]["Type"] ?? "";
   $r = $this->Element(["div", $this->Element([
    "h4", "No Preview Available"
   ]).$this->Element([
    "p", "It may have been removed or its visibility reduced by the owner."
   ]), [
    "class" => "K4i"
   ]]);
   $source = $this->efs.$a["T"]."/".$a["DLL"]["Name"];
   $readEFS = curl_init($source);
   curl_setopt($readEFS, CURLOPT_NOBODY, true);
   curl_exec($readEFS);
   $efsResponse = curl_getinfo($readEFS, CURLINFO_HTTP_CODE);
   curl_close($readEFS);
   if($efsResponse == 200) {
    if($type == "Audio") {
     $cover = $this->efs."A.jpg";
     $r = $this->Element(["source", NULL, [
      "src" => $s,
      "type" => $a["DLL"]["MIME"]
     ]]);
     $r = "<audio class=\"PreviewAudio\" controls>$r</audio>\r\n";
     # F.A.B. Source: $this->Element(["source", NULL, ["src" => "[App.Base]:8000/listen", "type" => "audio/aac"]]);
    } elseif($type == "Document") {
     $source = $this->efs."D.jpg";
     if($disableButtons == 0) {
      $r = $this->Element(["h3", $a["DLL"]["Title"], [
       "class" => "CenterText CoverPhotoCard PreviewDocument",
       "style" => "background:url('$source') no-repeat center center;background-size:cover"
      ]]);
     }
    } elseif($type == "Photo") {
     $r = "<img src=\"$source\" style=\"width:100%\"/>\r\n";
    } elseif($type == "Video") {
     $r = $this->Element(["video", $this->Element(["source", NULL, [
      "src" => $source,
      "type" => $a["DLL"]["MIME"]
     ]])]);
    }
   }
   return $r;
  }
  function GetContentData(array $content) {
   $attachments = "";
   $body = "";
   $blockCommand = $content["Blacklisted"] ?? 0;
   $blockCommand = ($blockCommand == 0) ? "Block" : "Unblock";
   $coverPhoto = $this->PlainText([
    "Data" => "[Media:CP]",
    "Display" => 1
   ]);
   $data = [];
   $description = "";
   $empty = 0;
   $id = $content["ID"] ?? "";
   $id = explode(";", base64_decode($id));
   $options = [];
   $title = "";
   $type = $id[0] ?? "";
   $vote = "";
   $web = $this->Element(["div", $this->Element([
     "h4", "Content Unavailable"
    ]).$this->Element([
     "p", "The Identifier or Type are missing."
    ]), ["class" => "K4i"]
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id[1]) && !empty($type)) {
    $contentID = $id[1] ?? "";
    $additionalContentID = $id[2] ?? "";
    if($type == "Album" && !empty($additionalContentID)) {
     $data = $this->Data("Get", ["fs", md5($contentID)]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $data = $data["Albums"][$additionalContentID] ?? [];
      $coverPhoto = $data["ICO"] ?? $coverPhoto;
      $coverPhoto = $this->GetSourceFromExtension([
       $contentID,
       $coverPhoto
      ]);
      $description = $data["Description"] ?? "";
      $vote = ($contentID != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "View" => base64_encode(base64_encode("v=".base64_encode("Album:Home")."&AID=$additionalContentID&UN=".$contentID)),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
     }
    } elseif($type == "Blog") {
     $data = $this->Data("Get", ["blg", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Blogs")),
       "Chat" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode($contentID)."&Integrated=1"),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeleteBlog")."&ID=".base64_encode($contentID)),
       "Edit" => base64_encode("v=".base64_encode("Blog:Edit")."&BLG=$contentID"),
       "Invite" => base64_encode("v=".base64_encode("Blog:Invite")."&ID=".base64_encode($contentID)),
       "Post" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=$contentID&new=1"),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($data["ID"])."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => base64_encode("v=".base64_encode("Blog:Home")."&CARD=1&ID=$contentID"),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
     }
    } elseif($type == "BlogPost" && !empty($additionalContentID)) {
     $data = $this->Data("Get", ["bp", $additionalContentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["Attachments"] ?? [];
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $body = $this->PlainText([
       "Data" => $body,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]);
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($additionalContentID)."&List=".base64_encode("Blog Posts")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeleteBlogPost")."&ID=".base64_encode("$contentID;$additionalContentID")),
       "Edit" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=$contentID&Post=$additionalContentID"),
       "Report" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("BlogPost;$contentID;$additionalContentID")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($additionalContentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => base64_encode("v=".base64_encode("BlogPost:Home")."&Blog=$contentID&Post=$additionalContentID&b2=".$content["BackTo"]."&back=1"),
       "Vote" => base64_encode("v=$vote&ID=$additionalContentID&Type=2")
      ];
     }
    } elseif($type == "Chat") {
     $active = 0;
     $data = $this->Data("Get", ["chat", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $contributors = $data["Contributors"] ?? [];
      foreach($contributors as $member => $role) {
       if($member == $you) {
        $active++;
       }
      }
      $bookmarkCommand = ($active == 0) ? "Add " : "Remove ";
      $bookmarkCommand .= "Bookmark";
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $view = "v=".base64_encode("Chat:Home")."&Group=1&ID=".base64_encode($contentID)."&Integrated=".$content["Integrated"];
      $view .= ($content["Integrated"] == 1) ? "&Card=1" : "";
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Group Chats")),
       "Bookmark" => base64_encode("v=".base64_encode("Chat:Bookmark")."&Command=".base64_encode($bookmarkCommand)."&ID=".base64_encode($contentID)),
       "Contributors" => $contributors,
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeleteChat")."&ID=".base64_encode($contentID)),
       "Edit" => base64_encode("v=".base64_encode("Chat:Edit")."&ID=".base64_encode($contentID)."&Username=".base64_encode($data["UN"])),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => base64_encode($view)
      ];
     }
    } elseif($type == "File" && !empty($additionalContentID)) {
     $data = $this->Data("Get", [
      "fs",
      md5($contentID)
     ]) ?? [];
     $data = ($contentID == $this->ID) ? $this->Data("Get", [
      "app",
      "fs"
     ]) : $data["Files"];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $data = $data[$additionalContentID] ?? [];
      $description = $data["Description"] ?? "";
      $attachments = $this->GetAttachmentPreview([
       "DisableButtons" => 1,
       "DLL" => $data,
       "T" => $contentID,
       "Y" => $you
      ]).$this->Element(["div", NULL, [
       "class" => "NONAME",
       "style" => "height:0.5em"
      ]]);
      $vote = ($contentID != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($content["ID"])."&List=".base64_encode("Files")),
       "Edit" => base64_encode("v=".base64_encode("File:Edit")."&ID=".base64_encode($additionalContentID)."&UN=".base64_encode($contentID)),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($additionalContentID)."&Type=".base64_encode($type)."&Username=".base64_encode($contentID)),
       "Source" => $this->GetSourceFromExtension([$contentID, $data]),
       "Vote" => base64_encode("v=$vote&ID=$additionalContentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "Forum") {
     $data = $this->Data("Get", ["pf", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $description = $data["Description"] ?? "";
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Forums")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeleteForum")."&ID=".base64_encode($contentID)),
       "Edit" => base64_encode("v=".base64_encode("Forum:Edit")."&ID=$contentID"),
       "Invite" => base64_encode("v=".base64_encode("Forum:Invite")."&ID=".base64_encode($contentID)),
       "Post" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$contentID&new=1"),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => base64_encode("v=".base64_encode("Forum:Home")."&CARD=1&ID=".base64_encode($contentID)),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "ForumPost" && !empty($additionalContentID)) {
     $data = $this->Data("Get", ["post", $additionalContentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["Attachments"] ?? [];
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $body = $this->PlainText([
       "Data" => $body,
       "HTMLDecode" => 1
      ]);
      $description = $data["Description"] ?? "";
      $vote = ($data["From"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($additionalContentID)."&List=".base64_encode("Forum Posts")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeleteForumPost")."&FID=$contentID&ID=$additionalContentID"),
       "Edit" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$contentID&ID=$additionalContentID"),
       "Report" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("ForumPost;$contentID;$additionalContentID")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode("$contentID-$additionalContentID")."&Type=".base64_encode($type)."&Username=".base64_encode($data["From"])),
       "View" => base64_encode("v=".base64_encode("ForumPost:Home")."&FID=$contentID&ID=$additionalContentID"),
       "Vote" => base64_encode("v=$vote&ID=$additionalContentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "Member") {
     $data = $this->Data("Get", ["mbr", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $them = $data["Login"]["Username"] ?? "";
      if($empty == 0) {
       $coverPhoto = base64_decode($data["Personal"]["CoverPhoto"]);
       $description = "You have not added a Description.";
       $displayName = $data["Personal"]["DisplayName"] ?? $them;
       $description = ($them != $you) ? "$displayName has not added a Description." : $description;
       $description = (!empty($data["Personal"]["Description"])) ? $data["Personal"]["Description"] : $description;
       $title = ($them == $this->ID) ? "Anonymous" : $displayName;
       $vote = ($them  != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
       $options = [
        "Blcok" => "",
        "Edit" => base64_encode("v=".base64_encode("Profile:Preferences")),
        "ProfilePicture" => $this->ProfilePicture($data, "margin:5%;width:90%"),
        "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($them)."&Type=".base64_encode($type)."&Username=".base64_encode($them)),
        "View" => base64_encode("v=".base64_encode("Profile:Home")."&Card=1&UN=".base64_encode($them)),
        "Vote" => base64_encode("v=$vote&ID=".md5($them)."&Type=4")
       ];
      }
     }
    } elseif($type == "Page") {
     $data = $this->Data("Get", ["pg", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["Attachments"] ?? [];
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $attachments = $this->RenderView($attachments);
      $body = $data["Body"] ?? "";
      $body = $this->PlainText([
       "Data" => $body,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]);
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $view = (!empty($content["BackTo"]) && !empty($content["ParentPage"])) ? base64_encode("v=".base64_encode("Page:Home")."&b2=".$content["BackTo"]."&back=1&lPG=".$content["ParentPage"]."&ID=$contentID") : base64_encode("v=".base64_encode("Page:Home")."&ID=$contentID");
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Pages")),
       "Chat" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode($contentID)."&Integrated=1"),
       "Contributors" => base64_encode("v=".base64_encode("Search:Containers")."&CARD=1&ID=".base64_encode($contentID)."&Type=".base64_encode("Article")."&st=Contributors"),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeletePage")."&ID=$contentID"),
       "Edit" => base64_encode("v=".base64_encode("Page:Edit")."&ID=".base64_encode($contentID)),
       "Report" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("Page;".$contentID)),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "Subscribe" => base64_encode("v=".base64_encode("WebUI:SubscribeSection")."&ID=$contentID&Type=Article"),
       "View" => $view,
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=2")
      ];
     }
    } elseif($type == "Poll") {
     $data = $this->Data("Get", ["poll", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", []))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Polls")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeletePoll")."&ID=$contentID"),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => base64_encode("v=".base64_encode("Poll:Home")."&ID=$contentID")
      ];
     }
    } elseif($type == "Product") {
     $data = $this->Data("Get", ["product", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["Attachments"] ?? [];
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $body = $this->PlainText([
       "Data" => $body,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]);
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Products")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeleteProduct")."&ID=$contentID"),
       "Edit" => base64_encode("v=".base64_encode("Product:Edit")."&Card=1&Editor=".$data["Category"]."&ID=$contentID&Shop=".md5($data["UN"])),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".$data["UN"]),
       "View" => base64_encode("v=".base64_encode("Product:Home")."&CARD=1&ID=$contentID&UN=".base64_encode($data["UN"])),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
     }
    } elseif($type == "Shop") {
     $data = $this->Data("Get", ["shop", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $description = $data["Description"] ?? "";
      $coverPhoto = $data["CoverPhoto"] ?? "";
      $vote = (md5($you) != $contentID) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Shops")),
       "Chat" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode(md5("Shop$contentID"))."&Integrated=1"),
       "Edit" => base64_encode("v=".base64_encode("Shop:Edit")."&ID=".base64_encode($contentID)),
       "Payroll" => base64_encode("v=".base64_encode("Shop:Payroll")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($content["Owner"])),
       "View" => base64_encode("v=".base64_encode("Shop:Home")."&CARD=1&UN=".base64_encode($content["Owner"])),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "StatusUpdate") {
     $data = $this->Data("Get", ["su", $contentID]) ?? [];
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["Attachments"] ?? [];
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $body = $this->PlainText([
       "Data" => $body,
       "Display" => 1,
       "HTMLDecode" => 1
      ]);
      $from = $data["From"] ?? "";
      $vote = ($from != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Status Updates")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:DeleteStatusUpdate")."&ID=".base64_encode($contentID)),
       "Edit" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$contentID"),
       "Notes" => base64_encode("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($contentID)."&dbID=".base64_encode("su")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($from)),
       "View" => base64_encode("v=".base64_encode("StatusUpdate:Home")."&SU=$contentID"),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
     }
    }
   }
   $coverPhoto = $data["ICO"] ?? $coverPhoto;
   $coverPhoto = base64_encode($coverPhoto);
   $coverPhoto = $this->CoverPhoto($coverPhoto);
   $empty = $contnet["Blacklisted"] ?? $empty;
   $modified = $data["Modified"] ?? "";
   if(empty($modified)) {
    $modified = "";
   } else {
    $_Time = $this->TimeAgo($modified);
    $modified = " &bull; Modified ".$_Time;
    $modified = $this->Element(["em", $modified]);
   }
   $body = $this->PlainText([
    "BBCodes" => 1,
    "Data" => $this->Excerpt($body, 3000),
    "Display" => 1
   ]);
   $description = $this->PlainText([
    "Data" => $this->Excerpt($description, 180),
    "HTMLDecode" => 1
   ]);
   return [
    "DataModel" => $data,
    "Empty" => $empty,
    "InputData" => [
     "ID" => $id,
     "Type" => $type
    ],
    "ListItem" => [
     "Attachments" => $attachments,
     "Body" => $body,
     "CoverPhoto" => $coverPhoto,
     "Description" => $description,
     "Modified" => $modified,
     "Options" => $options,
     "Title" => $title,
     "Vote" => $vote
    ],
    "Preview" => [
     "Empty" => $this->Element(["div", $this->Element([
       "h3", "Preview Unavailable"
      ]).$this->Element([
       "p", "The requested content has either been hidden from viewing or purged from the platform."
      ]), ["class" => "InnerMargin K4i"]
     ]),
     "Content" => $this->Element(["div", $this->Element([
       "h3", $title
      ]).$this->Element([
       "p", $description
      ]).$this->Element([
       "p", $body
      ]).$this->Element(["div",
       $this->Element([
        "h4", "&bull; &bull; &bull;"
       ]), ["class" => "Attachments".md5($contentID)." SideScroll"]
      ]).$this->Element([
       "script", "UpdateContent('.Attachments".md5($contentID)."', '$attachments');"
      ]), ["class" => "InnerMargin K4i"]
     ])
    ]
   ];
  }
  function GetCopyrightInformation() {
   return $this->Element([
    "p", "Copyright &copy; 2017-".date("Y")." <em>".$this->config["App"]["Name"]."</em>.",
    ["class" => "CenterText"]
   ]).$this->Element([
    "p", "All rights reserved.",
    ["class" => "CenterText"]
   ]).$this->Element([
    "p", "<em>We the People power this Bastion of Freedom.</em>",
    ["class" => "CenterText"]
   ]);
  }
  function GetSourceFromExtension(array $a) {
   $_ALL = $this->config["XFS"]["FT"] ?? [];
   $file = $a[1] ?? "";
   $source = "D.jpg";
   $r = $this->efs.$source;
   if(!empty($a[0]) && !empty($file)) {
    if(!is_array($file)) {
     $extension = explode(".", $file)[1] ?? "";
     $name = $file;
    } else {
     $extension = $file["EXT"];
     $name = $file["Name"];
    } if(in_array($extension, $_ALL["A"])) {
     $source = "A.jpg";
    } elseif(in_array($extension, $_ALL["D"])) {
     $source = "D.jpg";
    } elseif(in_array($extension, $_ALL["P"])) {
     $source = $this->Thumbnail([
      "File" => $name,
      "Username" => $a[0]
     ])["FullPath"];
    } elseif(in_array($extension, $_ALL["V"])) {
     $source = "V.jpg";
    } else {
     $source = "D.jpg";
    } if(in_array($extension, $_ALL["P"])) {
     $r = $source;
    }
   }
   return $r;
  }
  function JSONResponse(array $a) {
   return json_encode($a, true);
  }
  function Languages() {
   return [
    "en_US" => "English",
    "de_DU" => "Deutsch",
    "ja_JP" => "日本語",
    "es_SP" => "Español"
   ];
  }
  function LastMonth() {
   $r = date_create(date("Y-m")." first day of last month");
   return [
    "LastMonth" => $r->format("Y-m"),
    "Now" => date("Y-m")
   ];
  }
  function Member(string $username) {
   if($username == $this->ID) {
    $response = $this->NewMember(["Username" => $this->ID]);
   } else {
    $response = $this->Data("Get", ["mbr", md5($username)]) ?? [];
   }
   $response["Activity"]["LastActive"] = $this->timestamp;
   return $response;
  }
  function NewMember(array $a) {
   $a = $this->FixMissing($a, [
    "CoverPhoto",
    "Donations_Patreon",
    "Donations_PayPal",
    "Donations_SubscribeStar",
    "Patreon",
    "PayPal",
    "ProfilePicture",
    "SubscribeStar"
   ]);
   $age = $a["Age"] ?? $this->config["minRegAge"];
   $birthMonth = $a["BirthMonth"] ?? 10;
   $birthYear = $a["BirthYear"] ?? 1995;
   $blogs = $a["Blogs"] ?? [];
   $cart = $a["Cart"] ?? [];
   $displayName = $a["DisplayName"] ?? $this->ID;
   $forums = $a["Forums"] ?? [];
   $email = $a["Email"] ?? "johnny.test@apple.com";
   $firstName = $a["FirstName"] ?? "John";
   $gender = $a["Gender"] ?? "Male";
   $history = $a["History"] ?? [];
   $now = $this->timestamp;
   $lastActive = $a["LastActive"] ?? $now;
   $onlineStatus = $a["OnlineStatus"] ?? 1;
   $pages = $a["Pages"] ?? [];
   $password = $a["Password"] ?? md5("P@ssw0rd!");
   $pin = $a["PIN"] ?? md5(0000000);
   $polls = $a["Polls"] ?? [];
   $rank = $a["Rank"] ?? md5("Member");
   $registered = $a["Registered"] ?? $this->timestamp;
   $relationshipStatus = $a["RelationshipStatus"] ?? md5("Single");
   $username = $a["Username"] ?? $this->ID;
   return [
    "Activity" => [
     "LastActive" => $lastActive,
     "OnlineStatus" => $onlineStatus,
     "Registered" => $registered
    ],
    "Blocked" => [
     "Albums" => [],
     "Blogs" => [],
     "Blog Posts" => [],
     "Comments" => [],
     "Group Chats" => [],
     "Files" => [],
     "Forums" => [],
     "Forum Posts" => [],
     "Links" => [],
     "Members" => [],
     "Pages" => [],
     "Polls" => [],
     "Products" => [],
     "Shops" => [],
     "Status Updates" => []
    ],
    "Blogs" => $blogs,
    "Donations" => [
     "Patreon" => $a["Patreon"],
     "PayPal" => $a["PayPal"],
     "SubscribeStar" => $a["SubscribeStar"]
    ],
    "Forums" => $forums,
    "Login" => [
     "Password" => md5($password),
     "PIN" => md5($pin),
     "Username" => $username
    ],
    "Pages" => $pages,
    "Personal" => [
     "Age" => $age,
     "Birthday" => [
      "Month" => $birthMonth,
      "Year" => $birthYear
     ],
     "CoverPhoto" => $a["CoverPhoto"],
     "DisplayName" => $displayName,
     "Description" => "",
     "Electable" => 0,
     "Email" => $email,
     "FirstName" => $firstName,
     "Gender" => $gender,
     "MinimalDesign" => 0,
     "ProfilePicture" => $a["ProfilePicture"],
     "RelationshipStatus" => $relationshipStatus,
     "RelationshipWith" => ""
    ],
    "Points" => 1000,
    "Polls" => $polls,
    "Privacy" => [
     "Albums" => md5("Public"),
     "Archive" => md5("Public"),
     "Articles" => md5("Public"),
     "Comments" => md5("Public"),
     "ContactInfo" => md5("Private"),
     "ContactInfoDonate" => md5("Public"),
     "ContactInfoEmails" => md5("Private"),
     "ContactRequests" => md5("Public"),
     "Contacts" => md5("Contacts"),
     "Contributions" => md5("Public"),
     "Forums" => md5("Close Contacts"),
     "ForumsType" => "Private",
     "Gender" => md5("Public"),
     "DLL" => md5("Public"),
     "Journal" => md5("Contacts"),
     "LastActivity" => md5("Close Contacts"),
     "LookMeUp" => 1,
     "OnlineStatus" => md5("Contacts"),
     "MSG" => md5("Close Contacts"),
     "NSFW" => 0,
     "Polls" => md5("Public"),
     "Products" => md5("Public"),
     "Profile" => md5("Public"),
     "Posts" => md5("Acquaintances"),
     "RelationshipStatus" => md5("Contacts"),
     "RelationshipWith" => md5("Contacts"),
     "Registered" => md5("Close Contacts"),
     "Shop" => md5("Public")
    ],
    "Rank" => $rank,
    "Shopping" => [
     "Cart" => $cart,
     "History" => $history
    ],
    "Subscriptions" => [
     "Artist" => [
      "A" => 0,
      "B" => $now,
      "E" => $now
     ],
     "Blogger" => [
      "A" => 1,
      "B" => $now,
      "E" => $this->TimePlus($now, 1, "month")
     ],
     "Developer" => [
      "A" => 1,
      "B" => $now,
      "E" => $now
     ],
     "VIP" => [
      "A" => 0,
      "B" => $now,
      "E" => $this->TimePlus($now, 1, "month")
     ],
     "XFS" => [
      "A" => 0,
      "B" => $now,
      "E" => $now
     ]
    ],
    "Verified" => 0
   ];
  }
  function PlainText(array $a) {
   $ck = [
    "BBCodes",
    "Decode",
    "Display",
    "Encode",
    "HTMLDecode",
    "HTMLEncode",
    "Processor"
   ];
   $r = $a["Data"] ?? "";
   for($i = 0; $i < count($ck); $i++) {
    $a[$ck[$i]] = $a[$ck[$i]] ?? 0;
   } if($a["Decode"] == 1) {
    $r = urldecode(urldecode(base64_decode($r)));
   } if($a["HTMLDecode"] == 1) {
    $r = html_entity_decode($r);
   } if($a["Display"] == 1) {
    $articleCard = base64_encode("Page:Card");
    $r = preg_replace_callback("/\[Article:(.*?)\]/i", array(&$this, "GetArticle"), $r);
    $r = preg_replace_callback("/\[Embed:(.*?)\]/i", array(&$this, "GetEmbeddedLink"), $r);
    $r = preg_replace_callback("/\[Extension:(.*?)\]/i", array(&$this, "GetExtension"), $r);
    $r = preg_replace_callback("/\[Media:(.*?)\]/i", array(&$this, "Media"), $r);
    $r = preg_replace_callback("/\[Translate:(.*?)\]/i", array(&$this, "Translate"), $r);
    $r = $this->Change([[
     "[App.Base]" => $this->base,
     "[App.BillOfRights]" => base64_encode("v=$articleCard&ID=".base64_encode("1a35f673a438987ec93ef5fd3605b796")),
     "[App.Constitution]" => base64_encode("v=$articleCard&ID=".base64_encode("b490a7c4490eddea6cc886b4d82dbb78")),
     "[App.CopyrightInfo]" => $this->GetCopyrightInformation(),
     "[App.CurrentYear]" => date("Y"),
     "[App.Name]" => $this->config["App"]["Name"],
     "[App.Username]" => $this->config["App"]["Name"],
     "[base]" => $this->base,
     "[efs]" => $this->efs,
     "[plus]" => "+",
     "[space]" => "&nbsp;",
     "[percent]" => "%"
    ], $r]);
   } if($a["Display"] == 1 && $a["BBCodes"] == 1) {
    $r = $this->RecursiveChange([[
     "/\[b\](.*?)\[\/b\]/is" => "<strong>$1</strong>",
     "/\[d:.(.*?)\](.*?)\[\/d\]/is" => "<div class=\"$1\">$2</div>\r\n",
     "/\[d:#(.*?)\](.*?)\[\/d\]/is" => "<div id=\"$1\">$2</div>\r\n",
     "/\[i\](.*?)\[\/i\]/is" => "<em>$1</em>",
     "/\[u\](.*?)\[\/u\]/is" => "<u>$1</u>",
     "/\[(.*?)\[(.*?)\]:(.*?)\]/is" => "<$1 $2>$3</$1>",
     "/\[IMG:s=(.*?)&w=(.*?)\]/is" => "<img src=\"$1\" style=\"width:$2\"/>",
     "/\[P:(.*?)\]/is" => "<p>$1</p>",
     "/@+([A-Za-z0-9_]+)/" => $this->Element(["button", "@$1", [
      "onclick" => "W('".$this->base."/@$1', '_blank');"
     ]]),
     "/#+([A-Za-z0-9_]+)/" => $this->Element(["button", "#$1", [
      "onclick" => "W('".$this->base."/topics/$1', '_blank');"
     ]])
    ], $r, 0]);
   } if($a["HTMLEncode"] == 1) {
    $r = htmlentities($r);
   } if($a["Encode"] == 1) {
    $r = base64_encode(urlencode(urlencode($r)));
   } if($a["Processor"] == 1) {
    $r = base64_encode(urlencode($r));
   }
   return $r;
  }
  function ProfilePicture(array $a, $b = NULL) {
   $b = (!empty($b)) ? " style=\"$b\"" : "";
   $base = $this->efs;
   $pp = $a["Personal"] ?? [];
   $pp = $a["Personal"]["ProfilePicture"] ?? "";
   $r = "[Media:LOGO]";
   if(!empty($pp) && @fopen($base.base64_decode($pp), "r")) {
    $r = $base.base64_decode($pp);
   }
   return $this->PlainText([
    "Data" => "<img class=\"c2\" src=\"$r\"$b/>",
    "Display" => 1
   ]);
  }
  function RecursiveChange(array $a) {
   $_HTML = $a[2] ?? 0;
   $r = $a[1];
   foreach($a[0] as $key => $value) {
    $v = ($_HTML == 0) ? $value : htmlentities($value);
    $r = preg_replace($key, $value, $r);
   }
   return $r;
  }
  function RenderGhostMember() {
   $ghost = $this->Member(uniqid("Ghost_"));
   $ghost["Personal"]["DisplayName"] = "Anonymous";
   return $ghost;
  }
  function RenderEventMedia() {
   $events = $this->config["App"]["PublicEvents"] ?? [];
   $r = [
    "Banner" => "",
    "CoverPhoto" => $this->PlainText([
     "BBCodes" => 1,
     "Data" => "[Media:CP]"
    ])
   ];
   foreach($events as $event => $info) {
    if($info["Active"] == 1) {
     $r["Banner"] = $this->Change([[
      "[Banner.Link]" => $info["BannerLink"],
      "[Banner.Text]" => $info["BannerText"],
     ], $this->Extension("af8e6cb7d85b8131980d9e6b69fc5a1f")]);
     if(!empty($info["CoverPhoto"])) {
      $r["CoverPhoto"] = $this->efs."/".$this->ID."/".$info["CoverPhoto"];
     }
     break;
    }
   }
   return $r;
  }
  function RenderHTTPResponse(string $url) {
   $url = curl_init($url);
   curl_setopt($url, CURLOPT_NOBODY, true);
   curl_exec($url);
   $r = curl_getinfo($url, CURLINFO_HTTP_CODE);
   curl_close($url);
   return $r;
  }
  function RenderSearchIndex(string $index) {
   if(empty($index)) {
    $index = [];
   } else {
    $index = $this->DataIndex("Get", $index);
   }
   return $index;
  }
  function RenderView(string $data) {
   $r = json_decode($data, true);
   $r = $r["Response"] ?? [];
   $r = $r["Web"] ?? $this->Element([
    "p", "No View Data<br/>Source Data: $data"
   ]);
   return $r;
  }
  function Revenue(array $a) {
   $data = $a[1] ?? [];
   $shopOwner = $a[0] ?? "";
   if(!empty($shopOwner) && is_array($data)) {
    $cost = $data["Cost"] ?? 0;
    $cost = str_replace(",", "", $data["Cost"]);
    $id = $data["ID"] ?? md5($this->timestamp.rand(1776, 9999));
    $day = date("l")." the ".date("dS");
    $month = date("m");
    $profit = $data["Profit"] ?? 0;
    $profit = str_replace(",", "", $data["Profit"]);
    $quantity = $data["Quantity"] ?? 1;
    $revenue = $this->Data("Get", ["id", md5($shopOwner)]) ?? [];
    $title = $data["Title"] ?? "";
    $year = date("Y");
    $newRevenue = [];
    $newRevenue["UN"] = $shopOwner;
    $newRevenue[$year] = $revenue[$year] ?? [];
    $newRevenue[$year][$month] = $revenue[$year][$month] ?? [];
    $newRevenue[$year][$month]["PaidCommission"] = 0;
    $newRevenue[$year][$month]["Partners"] = $data["Partners"] ?? [];
    $newRevenue[$year][$month]["Sales"][$day] = $revenue[$year][$month]["Sales"][$day] ?? [];
    if(!empty($title)) {
     array_push($newRevenue[$year][$month]["Sales"][$day], [$id => [
      "Cost" => $cost,
      "Profit" => $profit,
      "Quantity" => $quantity,
      "Title" => $title
     ]]);
     $this->Data("Save", ["id", md5($shopOwner), $newRevenue]);
    }
   }
  }
  function SendBulletin(array $a) {
   $data = $a["Data"] ?? "";
   $to = $a["To"] ?? "";
   $type = $a["Type"] ?? "";
   if(!empty($data) && !empty($to) && !empty($type)) {
    $y = $this->you;
    $bulletins = $this->Data("Get", ["bulletins", md5($to)]) ?? [];
    $bulletins[md5($y["Login"]["Username"].$this->timestamp)] = [
     "Data" => $data,
     "From" => $y["Login"]["Username"],
     "Read" => 0,
     "Seen" => 0,
     "Sent" => $this->timestamp,
     "Type" => $type
    ];
    $this->Data("Save", ["bulletins", md5($to), $bulletins]);
   }
  }
  function SendEmail(array $a) {
   $keys = [
    "Message",
    "Title",
    "To"
   ];
   $i = 0;
   foreach($keys as $key) {
    if(!empty($a[$key])) {
     $i++;
    }
   } if(count($keys) == $i) {
    try {
     $message = $this->Element([
      "html", $this->Element([
       "head", $this->Element([
        "style", $this->Extension("669ae04b308fc630f8e06317313d9efe")
       ])
      ]).$this->Element([
       "body", $a["Message"]
      ])
     ]);
     $data = $this->cypher->MailCredentials();
     $data = [
      "Host" => $data["Host"],
      "Message" => base64_encode($message),
      "Password" => $data["Password"],
      "Title" => base64_encode($a["Title"]),
      "To" => base64_encode($a["To"]),
      "Username" => $data["Username"]
     ];
     $cURL = curl_init("https://mail.outerhaven.nyc/send.php");
     curl_setopt($cURL, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"]);
     curl_setopt($cURL, CURLOPT_POSTFIELDS, $data);
     curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
     curl_exec($cURL);
     curl_close($cURL);
    } catch(Exception $error) {
     return $this->Element([
      "p", "Failed to send mail: ".$error->getMessage()
     ]);
    }
   }
  }
  function Setup(string $a) {
   $documentRoot = $this->DocumentRoot;
   $extension = "";
   if(!empty($a)) {
    if($a == "App") {
     $a = "$documentRoot/.htaccess";
     $extension = "97291f4b155f663aa79cc8b624323c5b";
    }
    $d = fopen($a, "w+");
    fwrite($d, $this->Extension($extension));
    fclose($d);
    chmod($a, 0755);
   }
  }
  function ShortNumber($a) {
   $r = str_replace(",", "", $a);
   if($r > 1000000000000) {
    $r = round(($r / 1000000000000), 1)."T";
   } elseif($r > 1500000000000) {
    $r = round(($r / 1500000000000), 1.5)."T";
   } elseif($r > 1000000000) {
    $r = round(($r / 1000000000), 1)."B";
   } elseif($r > 1500000000) {
    $r = round(($r / 1500000000), 1.5)."B";
   } elseif($r > 1000000) {
    $r = round(($r / 1000000), 1)."M";
   } elseif($r > 1500000) {
    $r = round(($r / 1500000), 1.5)."M";
   } elseif($r > 1000) {
    $r = round(($r / 1000), 1)."K";
   } elseif($r > 1500) {
    $r = round(($r / 1500), 1.5)."K";
   } else {
    $r = number_format($r);
   }
   return $r;
  }
  function ShuffleList($list) { 
   if(!is_array($list)) return $list; 
   $keys = array_keys($list); 
   shuffle($keys); 
   $random = array(); 
   foreach($keys as $key) { 
    $random[$key] = $list[$key]; 
   }
   return $random; 
  }
  function Statistic($a) {
   $m = date("m");
   $x = $this->Data("Get", ["app", md5("stats")]) ?? [];
   $y = date("Y");
   $x[$y] = $x[$y] ?? [];
   $x[$y][$m] = $x[$y][$m] ?? [];
   $x[$y][$m][$a] = $x[$y][$m][$a] ?? 0;
   $x[$y][$m][$a]++;
   $this->Data("Save", ["app", md5("stats"), $x]);
  }
  function TimeAgo($datetime, $full = false) {
   $now = new DateTime;
   if(is_numeric($datetime)) {
    $datetime = "@$datetime";
   }
   $datetime = new DateTime($datetime);
   $interval = $now->diff($datetime);
   $suffix = " ago";
   if($interval->y >= 1) {
    $value = $interval->y;
    $unit = "year";
   } elseif($interval->m >= 1) {
    $value = $interval->m;
    $unit = "month";
   } elseif($interval->d >= 1) {
    $value = $interval->d;
    $unit = "day";
   } elseif($interval->h >= 1) {
    $value = $interval->h;
    $unit = "hour";
   } elseif($interval->i >= 1) {
    $value = $interval->i;
    $unit = "minute";
   } else {
    $value = $interval->s;
    $unit = "second";
   } if($value != 1) {
    $unit .= "s";
   } if($full) {
    $r = $interval->format("%d days, %H hours, %I minutes, %S seconds").$suffix;
   }
   $r = "$value ".$unit.$suffix;
   return $r;
  }
  function TimePlus($timestamp, $amount = 1, $period = "month") {
   $date = new DateTime($timestamp);
   $period = strtolower($period);
   switch($period) {
    case "month":
     $date->add(new DateInterval("P".$amount."M"));
     break;
    case "year":
     $date->add(new DateInterval("P".$amount."Y"));
     break;
    case "decade":
     $date->add(new DateInterval("P".($amount * 10)."Y"));
     break;
    case "century":
     $date->add(new DateInterval("P".($amount * 100)."Y"));
     break;
   }
   return $date->format("Y-m-d H:i:s");
  }
  function Thumbnail(array $a) {
   $_EFS = $this->efs;
   $file = $a["File"] ?? "";
   $isCronJob = $a["CronJob"] ?? 0;
   $r = [];
   $username = $a["Username"] ?? "";
   if(empty($file) || empty($username)) {
    $r = [
     "AlbumCover" => "D.jpg",
     "FullPath" => $this->efs."D.jpg"
    ];
   } else {
    $_Image = "thumbnail.".explode(".", $file)[0].".png";
    $thumbnail = $_EFS."$username/$_Image";
    $readEFS = curl_init($thumbnail);
    curl_setopt($readEFS, CURLOPT_NOBODY, true);
    curl_exec($readEFS);
    $efsResponse = curl_getinfo($readEFS, CURLINFO_HTTP_CODE);
    curl_close($readEFS);
    if($efsResponse == 200) {
     $r = [
      "AlbumCover" => $_Image,
      "FullPath" => $thumbnail
     ];
    } else {
     $r = [
      "AlbumCover" => $file,
      "FullPath" => $_EFS."$username/$file"
     ];
     $readEFS = curl_init($r["FullPath"]);
     curl_setopt($readEFS, CURLOPT_NOBODY, true);
     curl_exec($readEFS);
     $efsResponse = curl_getinfo($readEFS, CURLINFO_HTTP_CODE);
     curl_close($readEFS);
     if($efsResponse == 200) {
      $source = ImageCreateFromString(file_get_contents($r["FullPath"]));
      $height = imagesy($source);
      $newWidth = 100;
      $width = imagesx($source);
      $newHeight = floor($height * ($newWidth / $width));
      $newImage = imagecreatetruecolor($newWidth, $newHeight);
      $local = $this->DocumentRoot."/efs/$username/$_Image";
      imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
      imagepng($newImage, $local);
      $r = [
       "AlbumCover" => $_Image,
       "FullPath" => $thumbnail
      ];
     }
    }
   }
   return $r;
  }
  public static function GetArticle($a = NULL) {
   $oh = New Core;
   if(!empty($a)) {
    $r = $oh->Article($a[1]);
    $oh->__destruct();
    return $r;
   }
  }
  public static function GetEmbeddedLink($a = NULL) {
   $oh = New Core;
   $r = "";
   if(!empty($a)) {
    $data = explode("-", base64_decode($a[1]));
    $contentID = $data[1] ?? "";
    $username = $data[0] ?? "";
    if(!empty($contentID) && !empty($username)) {
     $content = $oh->GetContentData([
      "Blacklisted" => 0,
      "ID" => $contentID,
      "Owner" => $username
     ]);
     $preview = $content["Preview"] ?? [];
     $r = $preview["Empty"];
     if($content["Empty"] == 0) {
      $options = $content["ListItem"]["Options"] ?? [];
      $r = ($content["Empty"] == 1) ? $preview["Empty"] : $preview["Content"];
      $r = (!empty($options["View"])) ? $oh->Element(["button", $oh->Element([
       "div", $r, ["class" => "NONAME"]
      ]).$oh->Element([
       "p", "View in Full", ["class" => "CenterText"]
      ]), [
       "class" => "K4i OpenCard",
       "data-view" => $options["View"]
      ]]) : $r;
     }
    }
    $oh->__destruct();
    return $r;
   }
  }
  public static function GetExtension($a = NULL) {
   $oh = New Core;
   if(!empty($a)) {
    $r = $oh->Extension($a[1]);
    $oh->__destruct();
    return $r;
   }
  }
  public static function Translate($a = NULL) {
   $oh = New Core;
   if(!empty($a[1])) {
    $translationID = explode("-", $a[1]);
    $translations = $oh->Data("Get", ["translate", $translationID[0]]) ?? [];
    foreach($oh->Languages() as $region => $language) {
     $translation = $translations[$translationID[1]][$language]  ?? "";
     $r = $translation ?? "";
     if(!empty($translation)) {
      break;
     }
    }
    $r = $translations[$translationID[1]][$oh->language] ?? $r;
    $r = (!empty($r)) ? $oh->PlainText([
     "BBCodes" => 1,
     "Data" => $r
    ]) : "No Translations were found for <em>".$translationID[0]."-".$translationID[1]."</em>.";
    $oh->__destruct();
    return $r;
   }
  }
  public static function Media($a = NULL) {
   $oh = New Core;
   if(!empty($a)) {
    $r = $oh->efs.$oh->ID."/".$oh->config["Media"][$a[1]]["File"];
    $oh->__destruct();
    return $r;
   }
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
<?php
 require_once(__DIR__."/Cypher.php");
 Class Core {
  protected function __construct() {
   try {
    $this->cypher = New Cypher;
    $this->DocumentRoot = $_SERVER["DOCUMENT_ROOT"] ?? "/home/mike/public_html";
    $this->ID = "App";
    $this->PayPalMID = base64_decode("Qk5aVjk0TkxYTDJESg==");
    $this->PayPalURL = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $this->ShopID = "Mike";
    $this->base = $this->ConfigureBaseURL();
    $this->config = $this->Configuration();
    $this->efs = $this->ConfigureBaseURL("efs");
    $this->timestamp = date("Y-m-d h:i:sA");
    $this->region = $_COOKIE["region"] ?? "en_US";
    $this->sk = $_COOKIE["SK"] ?? "";
    $this->you = $this->Member($this->Username());
   } catch(PDOException $e) {
    return $this->Element([
     "p", "Failed to initialize GW... ".$e->getMessage()
    ]);
   }
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
  function CheckBraintreeKeys(array $a) {
   $r = 0;
   foreach($a as $k => $v) {
    if(strpos($k, "Braintree") !== false && !empty($v)) {
     if(!empty(base64_decode($v))) {
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
   $ck = (!empty($a["Contacts"])) ? 1 : 0;
   $ck2 = (!empty($a["Privacy"])) ? 1 : 0;
   $ck3 = (!empty($a["Y"])) ? 1 : 0;
   $r = 0;
   if($ck == 1 || ($ck2 == 1 && $ck3 == 1)) {
    $pri = $a["Privacy"] ?? md5("Private");
    $pri2 = md5("Public");
    $aci = 0;
    $cfi = 0;
    $fi = 0;
    $fl = [md5("Acquaintances"), md5("Close Contacts"), md5("Contacts")];
    $x = $a["Contacts"] ?? [];
    foreach($x as $k => $v) {
     $ls = $v["List"] ?? md5("Public");
     $fl2 = ($k == $a["Y"] && $ls == $fl[0]) ? 1 : 0;
     $fl3 = ($k == $a["Y"] && $ls == $fl[1]) ? 1 : 0;
     $fl4 = ($k == $a["Y"] && $ls == $fl[2]) ? 1 : 0;
     $aci = ($fl2 == 1) ? $aci++ : $aci;
     $cfi = ($fl2 == 1 || $fl3 == 1) ? $cfi++ : $cfi;
     $fi = ($fl2 == 1 || $fl3 == 1 || $fl4 == 1) ? $fi++ : $fi;
    }
    $f = ($pri == $pri2) ? 1 : 0;
    $f2 = ($pri == $fl[0] && $aci > 0) ? 1 : 0;
    $f3 = ($pri == $fl[1] && $cfi > 0) ? 1 : 0;
    $f4 = ($pri == $fl[2] && $fi > 0) ? 1 : 0;
    $r = ($f == 1 || $f2 == 1 || $f3 == 1 || $f4 == 1) ? 1 : 0;
    $r = ($a["UN"] == $a["Y"] || $r == 1) ? 1 : 0;
   }
   return $r;
  }
  function Configuration() {
   # CONFIG PREFERENCES (save to db and remove list)
   $r = [
    "App" => [
     "Description" => "The Wild-West of the Internet.",
     "Illegal" => 777,
     "Keywords" => "Outer Haven, social entertainment, artist tools, social media, social discovery, creative community, hidden, deep web, private network, empowering expression, connect to the world, videos, music, share, playlists, mixes, profiles, discovery, discover, join the world with privatized social networking, NSA-free",
     "Name" => "Outer Haven",
     "SearchIDs" => [
      "ADM-LLP",
      "ADM-MassMail",
      "BGP",
      "BL",
      "BLG",
      "Bulletins",
      "CA",
      "CART",
      "Contacts",
      "ContactsChatList",
      "ContactsProfileList",
      "ContactsRequests",
      "Contributors",
      "DC",
      "Feedback",
      "Forums",
      "Forums-Admin",
      "Forums-Posts",
      "Knowledge",
      "Mainstream",
      "MBR",
      "MBR-ALB",
      "MBR-BLG",
      "MBR-CA",
      "MBR-Forums",
      "MBR-JE",
      "MBR-LLP",
      "MBR-SU",
      "MBR-XFS",
      "MiNY",
      "PR",
      "Products",
      "S-Blogger",
      "SHOP",
      "SHOP-InvoicePresets",
      "SHOP-Invoices",
      "SHOP-Orders",
      "SHOP-Products",
      "XFS"
     ]
    ],
    "Media" => [
     "CHECK" => "875be5c73e0671200587f26139501a8c.png",
     "CODE" => "78569ee93f82cf2cd9415e7c4ca5e65b.png",
     "CP" => "738389d58a41d5f26908a79964532b0f.jpg",
     "CPW" => "7d4928e003e769f78cf40068cfdd2bc9.jpg",
     "DOC" => "0dc477cf7c6d1210b9312c1f579f8a1d.png",
     "FAB" => "8806a28fa51a9cf0ecbec012f1e4fd66.png",
     "KEY" => "db8d8212e57138b737870366f46b368d.png",
     "LOGO" => "04ca5194af02b1f4e50ed4135fe6c39a.png",
     "LOGO-D" => "11b1bd06816a4cc429d25626731f6458.png",
     "MSG" => "b3a19746167389a973c51f5ffced827b.png",
     "MiNY" => "444936471454a369761338d1896f5091.png",
     "PADLOCK" => "2b07788693ef89d6bf7ee6b96f92c3f0.png",
     "PLUS" => "701d70ba025a96a6af69528d89ac6ef3.png",
     "PROFILE" => "4736cb9bbb2b28ecb1fbab93a49d2571.png",
     "REGSEL" => "7fd8af13e99bdb762e2c68acd11c0a71.png",
     "VVA" => "b3f36e7638e6961eda52b62016aa1b50.png",
     "VVA-CP" => ""
    ],
    "PTS" => [
     "Default" => 10,
     "Donations" => 100,
     "DeleteFile" => 100,
     "NewContent" => 1,
     "Products" => [
      "Architecture" => 1000,
      "Donation" => 500,
      "Download" => 200,
      "Product" => 50,
      "Subscription" => 400
     ]
    ],
    "SQL" => [
     "Key" => base64_encode("ReSearch^2022@OH.nyc"),
     "Password" => "V2VCZVNlYXJjaGluQE9ILm55Y14yMDIy",
     "Username" => "research"
    ],
    "Statistics" => [
     "FS" => "Feedback Submissions",
     "LI" => "Logins",
     "MBR" => "New Members",
     "NewInvoice" => "New Invoices",
     "PG" => "New Articles",
     "PGu" => "Article Updates",
     "PROD" => "New Products",
     "PRODu" => "Product Updates",
     "SU" => "Status Updates",
     "SUu" => "Edits to Status Updates",
     "UL" => "File Uploads",
     "ULu" => "File Updates",
     "Visits" => "Visitors"
    ],
    "Shop" => [
     "PaymentProcessors" => [
      "Braintree",
      "PayPal"
     ]
    ],
    "Subscriptions" => [
      "Artist" => [
       "Description" => "Create and manage a Shop, accept donations, and more, for a 5% commission.",
       "Title" => "Artist",
       "Term" => [1, "month"]
      ],
      "Blogger" => [
       "Description" => "Create and manage blogs.",
       "Title" => "Blogger",
       "Term" => [1, "month"]
      ],
      "VIP" => [
       "Description" => "Enjoy access to all subscriptions, and exclusive content.",
       "Title" => "V.I.P.",
       "Term" => [1, "month"]
      ],
      "XFS" => [
       "Description" => "Upload files to your heart's delight.",
       "Title" => "Unlimited File Storage",
       "Term" => [1, "month"]
      ]
     ],
     "XFS" => [
      "FT" => [
       "_FT" => ["Audio", "Document", "Photo", "Video"],
       "A" => ["aac", "flac", "m4a", "mp3", "wma"],
       "D" => ["3dm", "bimx", "cr", "cr2", "cr3", "doc", "docx", "dwg", "dwf", "pcx", "pdf", "rar", "tar.gz", "ttf", "txt", "usdz", "vbs"],
       "P" => ["bmp", "gif", "jpg", "jpeg", "png", "tiff"],
       "V" => ["avi", "flv", "mov", "mp4", "ogg"]
     ],
     "limits" => [
      "Audio" => 50,
      "Documents" => 250,
      "Images" => 10,
      "Videos" => 200,
      "Total" => 500
     ]
    ],
    "Xmaintanance" => 0,
    "minAge" => 18,
    "minRegAge" => 13
   ];
   $this->Data("Save", ["x", md5("config"), $r]);
   return $r;
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
    "Data" => "[sIMG:CP]",
    "Display" => 1
   ]);
   if(!empty($a)) {
    $r = $efs.base64_decode($a);
   }
   return $r;
  }
  function Credentials($a, $b) {
   $s = (!empty($b)) ? explode(":", $this->Decrypt($b)) : "";
   $sk = $_COOKIE["SK"] ?? "";
   if($a == "UN") {
    $s = (!empty($sk)) ? $s[0] : $this->ID;
   } elseif($a == "PW") {
    $s = (!empty($sk)) ? $s[1] : "P@ssw0rd!";
   }
   return $s;
  }
  function Data(string $action, array $data) {
   if(!empty($data)) {
    $r = $this->DocumentRoot."/data/c.oh.".$data[0];
    $r .= (!empty($data[1])) ? ".".$data[1] : "";
    if($action == "Get") {
     if(!file_exists($r)) {
      $r = json_encode([]);
     } else {
      $r = file_get_contents($r);
      $r = $this->Decrypt($r) ?? json_encode([]);
     }
     return json_decode($r, true);
    } elseif($action == "Purge") {
     if(file_exists($r)) {
      unlink($r);
     }
    } elseif($action == "Save") {
     $data[2] = $data[2] ?? [];
     $r = fopen($r, "w+");
     if(!empty($data[2])) {
      fwrite($r, $this->Encrypt(json_encode($data[2], true)));
      fclose($r);
     }
    }
   }
  }
  function DatabaseSet($a = NULL) {
   $r = array_diff(scandir($this->DocumentRoot."/data/"), [
    ".", "..", "index.php"
   ]);
   foreach($r as $k => $v) {
    if(!empty($a)) {
     if($a == "BLG") {
      $a = "c.oh.blg.";
     } elseif($a == "BlogPosts") {
      $a = "c.oh.bp.";
     } elseif($a == "Files") {
      $a = "c.oh.fs.";
     } elseif($a == "KB") {
      $a = "c.oh.knowledge.";
     } elseif($a == "MBR") {
      $a = "c.oh.mbr.";
     } elseif($a == "PF") {
      $a = "c.oh.pf.";
     } elseif($a == "PG") {
      $a = "c.oh.pg.";
     } elseif($a == "SU") {
      $a = "c.oh.su.";
     } if(strpos($v, $a) !== false) { 
      $r[$k] = $v;
     } else {
      unset($r[$k]);
     }
    } else {
     $r[$k] = $v;
    }
   }
   return $r;
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
  function Encrypt($data) {
   return $this->cypher->Encrypt($data);
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
  function Excerpt($a, $b = 180) {
   $a = substr($a, 0, $b);
   $a = substr($a, 0, strrpos($a, " "));
   $a = (strlen($a) > $b) ? strip_tags($a)."..." : strip_tags($a);
   return htmlentities($a);
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
     // F.A.B. Source: $this->Element(["source", NULL, ["src" => "[base]:8000/listen.pls?sid=1", "type" => "audio/aac"]])
    } elseif($type == "Document") {
     $source = $this->efs."D.jpg";
     $r = $this->Element(["h3", $a["DLL"]["Title"], [
      "class" => "CenterText CoverPhotoCard PreviewDocument",
      "style" => "background:url('$source')"
     ]]);
    } elseif($type == "Photo") {
     // MAKE NoPreview.jpg TO REPLACE D.jpg IN THIS CASE
     $r = "<img src=\"$source\" style=\"width:100%\"/>\r\n";
    } elseif($type == "Video") {
     $r = $this->eElement(["video", $this->Element(["source", NULL, [
      "src" => $source,
      "type" => $a["DLL"]["MIME"]
     ]])]);
    }
   }
   return $r;
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
    "de_DU" => "German",
    "ja_JP" => "Japanese",
    "es_SP" => "Spanish"
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
    $r = $this->NewMember(["Username" => $this->ID]);
   } else {
    $r = $this->Data("Get", ["mbr", md5($username)]) ?? [];
   }
   $r["Activity"]["LastActive"] = $this->timestamp;
   return $r;
  }
  function NewMember(array $a) {
   $a = $this->FixMissing($a, [
    "CoverPhoto",
    "Donations_Patreon",
    "Donations_PayPal",
    "Donations_SubscribeStar",
    "Email",
    "Gender",
    "Password",
    "Patreon",
    "PayPal",
    "PIN",
    "ProfilePicture",
    "Rank",
    "SubscribeStar",
    "Username"
   ]);
   $age = $a["Age"] ?? $this->config["minRegAge"];
   $birthMonth = $a["BirthMonth"] ?? 10;
   $birthYear = $a["BirthYear"] ?? 1995;
   $blogs = $a["Blogs"] ?? [];
   $cart = $a["Cart"] ?? [];
   $displayName = $a["DisplayName"] ?? $this->ID;
   $forums = $a["Forums"] ?? [];
   $email = $a["Email"] ?? "jappleseed@apple.com";
   $firstName = $a["FirstName"] ?? "John";
   $gender = $a["Gender"] ?? "Male";
   $history = $a["History"] ?? [];
   $now = $this->timestamp;
   $lastActive = $a["LastActive"] ?? $now;
   $onlineStatus = $a["OnlineStatus"] ?? 1;
   $pages = $a["Pages"] ?? [];
   $password = $a["Password"] ?? md5("P@ssw0rd!");
   $pin = $a["PIN"] ?? md5(0000000);
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
     "Files" => [],
     "Forums" => [],
     "Forum Posts" => [],
     "Links" => [],
     "Members" => [],
     "Pages" => [],
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
     "Email" => $email,
     "FirstName" => $firstName,
     "Gender" => $gender,
     "MinimalDesign" => 0,
     "ProfilePicture" => $a["ProfilePicture"],
     "RelationshipStatus" => $relationshipStatus,
     "RelationshipWith" => ""
    ],
    "Points" => 1000,
    "Privacy" => [
     "Albums" => md5("Public"),
     "Archive" => md5("Public"),
     "Articles" => md5("Public"),
     "Comments" => md5("Public"),
     "ContactInfo" => md5("Private"),
     "ContactInfoEmails" => md5("Private"),
     "ContactInfoDonate" => md5("Public"),
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
    "Verified" => 1
   ];
  }
  function Page(string $a) {
   $x = $this->Data("Get", ["pg", $a]) ?? [];
   if(empty($x)) {
    $r = $this->Change([[
     "[Error.Back]" => "",
     "[Error.Header]" => "Not Found",
     "[Error.Message]" => "The Extension <em>$a</em> could not be found."
    ], $this->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
   } else {
    $r = $this->PlainText([
     "Data" => $x["Body"],
     "Decode" => 1,
     "Display" => 1,
     "HTMLDecode" => 1
    ]);
   }
   return $r;
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
    $pc = base64_encode("Page:Card");
    $r = preg_replace_callback("/\[LLP:(.*?)\]/i", array(&$this, "Extension"), $r);
    $r = preg_replace_callback("/\[Languages:(.*?)\]/i", array(&$this, "LanguagesTranslation"), $r);
    $r = preg_replace_callback("/\[CoreMedia:(.*?)\]/i", array(&$this, "CoreMedia"), $r);
    $r = preg_replace_callback("/\[sIMG:(.*?)\]/i", array(&$this, "CoreMedia"), $r);// TO BE DISOLVED
    $r = $this->Change([[
     "[App.Constitution]" => base64_encode("v=$pc&ID=".base64_encode("b490a7c4490eddea6cc886b4d82dbb78")),
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
   $r = "[sIMG:LOGO]";
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
    $headers = [
     "Content-Type" => "text/html; charset=UTF-8",
     "From" => "noreply@outerhaven.nyc",
     "MINE-version" => "5.0",
     "color-scheme" => "dark light",
     "supported-color-schemes" => "dark light",
    ];
    $message = $this->Element([
     "html", $this->Element([
      "head", $this->Element([
       "style", $this->Page("669ae04b308fc630f8e06317313d9efe")
      ])
     ]).$this->Element([
      "body", $a["Message"]
     ])
    ]);
    mail($a["To"], $a["Title"], $message, $headers);
   }
  }
  function Setup(string $a) {
   $documentRoot = $this->DocumentRoot;
   $template = "";
   if(!empty($a)) {
    if($a == "App") {
     $a = "$documentRoot/.htaccess";
     $template = "97291f4b155f663aa79cc8b624323c5b";
    }
    $d = fopen($a, "w+");
    fwrite($d, $this->Page($template));
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
  function SQL(string $query, array $values) {
   try {
    $config = $this->config["SQL"] ?? [];
    $sql = "mysql:host=localhost;dbname=ReSearch";
    $sql = new PDO($sql, $config["Username"], base64_decode($config["Password"]), [
     PDO::ATTR_PERSISTENT => true,
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $r = $sql->prepare($query);
    if(!empty($values)) {
     foreach($values as $key => $value) {
      switch(true) {
       case is_int($value):
        $type = PDO::PARAM_INT;
        break;
       case is_bool($value):
        $type = PDO::PARAM_BOOL;
        break;
       case is_null($value):
        $type = PDO::PARAM_NULL;
        break;
       default:
        $type = PDO::PARAM_STR;
      }
      $r->bindValue($key, $value, $type);
     }
    }
    $r->execute();
   } catch(PDOException $error) {
    $r = $error->getMessage();
   }
   return $r;
  }
  function Statistic($a) {
   $m = date("m");
   $x = $this->Data("Get", ["x", "stats"]) ?? [];
   $y = date("Y");
   $x[$y] = $x[$y] ?? [];
   $x[$y][$m] = $x[$y][$m] ?? [];
   $x[$y][$m][$a] = $x[$y][$m][$a] ?? 0;
   $x[$y][$m][$a]++;
   $this->Data("Save", ["x", "stats", $x]);
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
  function Username() {
   $sk = $_COOKIE["SK"] ?? "";
   $r = (!empty($sk)) ? $this->Credentials("UN", $sk) : $this->ID;
   return $r;
  }
  public static function CoreMedia($a = NULL) {
   $x = New Core;
   if(!empty($a)) {
    $r = $x->efs.$x->ID."/".$x->config["Media"][$a[1]];
    $x->__destruct();
    return $r;
   }
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
  public static function Extension($a = NULL) {
   $x = New Core;
   if(!empty($a)) {
    $r = $x->Page($a[1]);
    $x->__destruct();
    return $r;
   }
  }
  public static function LanguagesTranslation($a = NULL) {
   $x = New Core;
   if(!empty($a[1])) {
    $l = explode("-", $a[1]);
    $lt = $x->Data("Get", ["local", $l[0]]) ?? [];
    $r = $lt[$l[1]]["en_US"] ?? "";
    $r = $lt[$l[1]][$x->region] ?? $r;
    $r = (!empty($r)) ? $x-PlainText([
     "BBCodes" => 1,
     "Data" => $r,
     "Decode" => 1,
     "Display" => 1,
     "HTMLDecode" => 1
    ]) : $x->Element(["p", "No Translations Found"]);
    $x->__destruct();
    return $r;
   }
  }
 }
?>
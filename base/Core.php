<?php
 Class Core {
  function __construct() {
   try {
    $this->cypher = New Cypher;
    $this->DITkey = $this->cypher->DITkey;
    $this->DocumentRoot = "/var/www/html";
    $this->config = $this->Configuration();
    $this->ID = "App";
    $this->RestrictedIDs = [
     "1a35f673a438987ec93ef5fd3605b796",
     "5ec1e051bf732d19e09ea9673cd7986b",
     "7216072bbd437563e692cc7ff69cdb69",
     "b490a7c4490eddea6cc886b4d82dbb78",
     "cb3e432f76b38eaa66c7269d658bd7ea"
    ];
    $this->SecureityKey = $this->Authenticate("Get");
    $this->ShopID = $this->config["App"]["ShopID"];
    $this->base = $this->ConfigureBaseURL();
    $this->efs = $this->ConfigureBaseURL("FileSystem");
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
function AESdecrypt(string $data) {
    try {
        $key = hash("sha256", base64_decode($this->DITkey), true); // "testkey" in base64
        if(empty($data)) {
            return $data;
        }
        $data = base64_decode($data);
        if ($data === false) {
            throw new Exception("Base64 decoding failed");
        }
        if (strlen($data) % 16 !== 0) {
            throw new Exception("Decoded data length (" . strlen($data) . ") is not a multiple of AES block size (16)");
        }
        $decrypted = openssl_decrypt($data, "AES-256-ECB", $key, OPENSSL_RAW_DATA);
        if ($decrypted === false) {
            throw new Exception("Decryption failed: " . openssl_error_string());
        }
        return $decrypted;
    } catch (Exception $error) {
        return "AES Decryption error: " . $error->getMessage();
    }
}
function AESencrypt(string $data) {
 try {
  $key = hash("sha256", base64_decode($this->DITkey), true);
  if(empty($data)) {
   return base64_encode("");
  }
  $blockSize = 16;
  $padLength = $blockSize - (strlen($data) % $blockSize);
  $data .= str_repeat(chr($padLength), $padLength);
  $encrypted = openssl_encrypt($data, "AES-256-ECB", $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
  if($encrypted === false) {
   throw new Exception("Encryption failed: ".openssl_error_string());
  }
  return base64_encode($encrypted);
 } catch(Exception $error) {
  return "AES Encryption error: ".$error->getMessage();
 }
}
  function Article(string $id) {
   $article = $this->Data("Get", ["pg", $id]);
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
     $headers = (function_exists("apache_request_headers")) ? apache_request_headers() : [];
     $r = $this->ID;
     #$token = $headers["Token"] ?? base64_encode("");//TEMP
     #$token = base64_decode($token);//TEMP
     $token = $headers["Token"] ?? $this->AESencrypt("");
     $token = $this->AESdecrypt($token);
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
       $you = $this->Data("Get", ["mbr", md5($username)]);
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
   $list = $a[0] ?? [];
   foreach($list as $key => $value) {
    if(!is_array($key) && !is_array($value)) {
     $r = str_replace($key, $value, $r);
    }
   }
   return $r;
  }
  function CheckBlocked(array $a) {
   $r = 0;
   if(!empty($a[1]) && !empty($a[2])) {
    $blacklist = $a[0]["Blocked"][$a[1]] ?? [];
    foreach($blacklist as $key => $id) {
     if($a[2] == $id) {
      $r++;
     }
    }
   }
   $r = ($a[0]["Login"]["Username"] == $this->ID) ? 0 : $r;
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
   $base = $_SERVER["HTTP_HOST"] ?? $this->config["App"]["Domains_Base"];
   if($a == "FileSystem") {
    $r = $this->config["App"]["Domains_FileSystem"]."/";
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
  function CoverPhoto(string $attachment) {
   $coverPhoto = "";
   if(!empty($attachment)) {
    $attachment = explode("-", base64_decode($attachment));
    if(!empty($attachment[0]) && !empty($attachment[1])) {
     $_File = $this->GetContentData([
      "AddTo" => "",
      "Blacklisted" => 0,
      "ID" => base64_encode("File;".$attachment[0].";".$attachment[1])
     ]);
     if($_File["Empty"] == 0) {
      $media = $_File["DataModel"];
      $coverPhoto = $media["Name"] ?? $media;
      $coverPhoto = $this->efs.$attachment[0]."/$coverPhoto";
     }
    }
   }
   return $coverPhoto;
  }
  function Data(string $action, array $data) {
   if(!empty($data)) {
    $dataFile = $this->DocumentRoot."/data/nyc.outerhaven.".$data[0];
    $dataFile .= (!empty($data[1]) && !is_array($data[1])) ? ".".$data[1] : "";
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
     if(file_exists($dataFile) || is_dir($dataFile)) {
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
     $data[$key] = $this->AESdecrypt($value);
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
    $r = strip_tags($data);
   } else {
    $excerpt = substr($data, 0, $limit);
    $lastSpace = strrpos($excerpt, " ");
    if($lastSpace !== false) {
     $excerpt = substr($excerpt, 0, $lastSpace);
    }
    $r = strip_tags($excerpt)."...";
   }
   return $r;
  }
  function Extension(string $id) {
   $extension = $this->Data("Get", ["extension", $id]);
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
    "class" => "FrostedBright RoundedLarge"
   ]]);
   $source = $this->efs.$a["T"]."/".$a["DLL"]["Name"];
   $readEFS = curl_init($source);
   curl_setopt($readEFS, CURLOPT_NOBODY, true);
   curl_exec($readEFS);
   $efsResponse = curl_getinfo($readEFS, CURLINFO_HTTP_CODE);
   curl_close($readEFS);
   if(!empty($a["T"]) && $efsResponse == 200) {
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
   $addTo = $content["AddTo"] ?? "";
   $attachments = "";
   $backTo = $content["BackTo"] ?? "";
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
   $parentView = $content["ParentPage"] ?? "";
   $title = "";
   $type = $id[0] ?? "";
   $vote = "";
   $web = $this->Element(["div", $this->Element([
     "h4", "Content Unavailable"
    ]).$this->Element([
     "p", "The Identifier or Type are missing."
    ]), ["class" => "FrostedBright Rounded"]
   ]);
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id[1]) && !empty($type)) {
    $contentID = $id[1] ?? "";
    $additionalContentID = $id[2] ?? "";
    if($type == "Album" && !empty($additionalContentID)) {
     $data = $this->Data("Get", ["fs", md5($contentID)]);
     $empty = $data["Purge"] ?? 0;
     $data = $data["Albums"][$additionalContentID] ?? [];
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode("")."&Type=".base64_encode("DLC"));
      $body = "";
      $coverPhoto = $data["ICO"] ?? $coverPhoto;
      $coverPhoto = $this->GetSourceFromExtension([
       $contentID,
       $coverPhoto
      ]);
      $description = $data["Description"] ?? "";
      $viewData = json_encode([
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($additionalContentID),
       "v" => base64_encode("Album:Purge")
      ], true);
      $title = $data["Title"] ?? "";
      $vote = ($contentID != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode("Block")."&Content=".base64_encode(base64_encode("$contentID-$additionalContentID"))."&List=".base64_encode("Albums")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("Album:Edit")."&AID=$additionalContentID&UN=".base64_encode($contentID)),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($additionalContentID)."&Type=".base64_encode("Album")."&Username=".base64_encode($contentID)),
       "Upload" => base64_encode("v=".base64_encode("File:Upload")."&AID=$additionalContentID&UN=$contentID"),
       "View" => base64_encode(base64_encode("v=".base64_encode("Album:Home")."&AddTo=$addTo&AID=$additionalContentID&UN=".$contentID)),
       "Vote" => $this->AESencrypt("v=$vote&ID=$contentID&Type=4")
      ];
     }
    } elseif($type == "Blog") {
     $data = $this->Data("Get", ["blg", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["CoverPhoto"] ?? "";
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode($attachments)."&Type=".base64_encode("CoverPhoto"));
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $viewData = json_encode([
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($contentID),
       "v" => base64_encode("Blog:Purge")
      ], true);
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Blogs")),
       "Chat" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode($contentID)."&Integrated=1"),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("Blog:Edit")."&BLG=$contentID"),
       "Invite" => base64_encode("v=".base64_encode("Blog:Invite")."&ID=".base64_encode($contentID)),
       "Post" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=$contentID&new=1"),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($data["ID"])."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "Subscribe" => base64_encode("v=".base64_encode("WebUI:SubscribeSection")."&ID=$contentID&Type=Blog"),
       "View" => $this->AESencrypt("v=".base64_encode("Blog:Home")."&AddTo=$addTo&CARD=1&ID=$contentID"),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
     }
    } elseif($type == "BlogPost" && !empty($additionalContentID)) {
     $data = $this->Data("Get", ["bp", $additionalContentID]);
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
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $viewData = json_encode([
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "BlogID" => base64_encode($contentID),
       "PostID" => base64_encode($additionalContentID),
       "v" => base64_encode("BlogPost:Purge")
      ], true);
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($additionalContentID)."&List=".base64_encode("Blog Posts")),
       "Contributors" => base64_encode("v=".base64_encode("Search:Containers")."&CARD=1&ID=".base64_encode($additionalContentID)."&Type=".base64_encode("BlogPost")."&st=Contributors"),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("BlogPost:Edit")."&Blog=$contentID&Post=$additionalContentID"),
       "Notes" => base64_encode("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($additionalContentID)."&dbID=".base64_encode("bp")),
       "Report" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("BlogPost;$contentID;$additionalContentID")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($additionalContentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "Subscribe" => base64_encode("v=".base64_encode("WebUI:SubscribeSection")."&ID=$additionalContentID&Type=BlogPost"),
       "View" => base64_encode("v=".base64_encode("BlogPost:Home")."&AddTo=$addTo&Blog=$contentID&Post=$additionalContentID&b2=$backTo&back=1"),
       "Vote" => base64_encode("v=$vote&ID=$additionalContentID&Type=2")
      ];
     }
    } elseif($type == "Chat") {
     $active = 0;
     $data = $this->Data("Get", ["chat", $contentID]);
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
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $viewData = json_encode([
       "AddTo" => $addTo,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($contentID),
       "v" => base64_encode("Chat:Purge")
      ], true);
      $view = "v=".base64_encode("Chat:Home")."&AddTo=$addTo&Group=1&ID=".base64_encode($contentID)."&Integrated=".$content["Integrated"];
      $view .= ($content["Integrated"] == 1) ? "&Card=1" : "";
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Group Chats")),
       "Bookmark" => base64_encode("v=".base64_encode("Chat:Bookmark")."&Command=".base64_encode($bookmarkCommand)."&ID=".base64_encode($contentID)),
       "Contributors" => $contributors,
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("Chat:Edit")."&ID=".base64_encode($contentID)."&Username=".base64_encode($data["UN"])),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => base64_encode($view)
      ];
     }
    } elseif($type == "Extension") {
     $data = $this->Data("Get", ["extension", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     $viewData = json_encode([
      "SecureKey" => base64_encode($y["Login"]["PIN"]),
      "ID" => base64_encode($contentID),
      "v" => base64_encode("Extension:Purge")
     ], true);
     $options = [
      "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
      "Edit" => base64_encode("v=".base64_encode("Extension:Edit")."&ID=".base64_encode($contentID))
     ];
    } elseif($type == "File" && !empty($additionalContentID)) {
     $data = $this->Data("Get", ["fs", md5($contentID)]);
     $data = ($contentID == $this->ID) ? $this->Data("Get", ["app", "fs"]) : $data["Files"];
     $empty = $data["Purge"] ?? 0;
     $data = $data[$additionalContentID] ?? [];
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $this->GetAttachmentPreview([
       "DisableButtons" => 1,
       "DLL" => $data,
       "T" => $contentID,
       "Y" => $you
      ]).$this->Element(["div", NULL, [
       "class" => "NONAME",
       "style" => "height:0.5em"
      ]]);
      $description = $data["Description"] ?? "";
      $parentView = $content["ParentView"] ?? "Files";
      $viewData = json_encode([
       "AddTo" => $addTo,
       "ParentView" => $parentView,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode("$contentID-$additionalContentID"),
       "v" => base64_encode("File:Purge")
      ], true);
      $vote = ($contentID != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Files")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Header=".base64_encode($this->Element(["h1", "Delete Media", ["class" => "CenterText"]]))."&ParentPage=Files&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("File:Edit")."&ID=".base64_encode($additionalContentID)."&UN=".base64_encode($contentID)),
       "Report" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("File;$contentID;$additionalContentID")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($additionalContentID)."&Type=".base64_encode($type)."&Username=".base64_encode($contentID)),
       "Source" => $this->GetSourceFromExtension([$contentID, $data]),
       "View" => "$parentView;".base64_encode("v=".base64_encode("File:Home")."&AddTo=$addTo&ID=$additionalContentID&UN=$contentID&ParentView=$parentView&ViewData=$viewData"),
       "Vote" => base64_encode("v=$vote&ID=$additionalContentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "Forum") {
     $data = $this->Data("Get", ["pf", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["CoverPhoto"] ?? "";
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode($attachments)."&Type=".base64_encode("CoverPhoto"));
      $body = "";
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $viewData = json_encode([
       "AddTo" => $addTo,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($contentID),
       "v" => base64_encode("Forum:Purge")
      ], true);
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Forums")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("Forum:Edit")."&ID=$contentID"),
       "Invite" => base64_encode("v=".base64_encode("Forum:Invite")."&ID=".base64_encode($contentID)),
       "Post" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$contentID&new=1"),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => $this->AESencrypt("v=".base64_encode("Forum:Home")."&AddTo=$addTo&CARD=1&ID=".base64_encode($contentID)),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "ForumPost" && !empty($additionalContentID)) {
     $data = $this->Data("Get", ["post", $additionalContentID]);
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
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $viewData = json_encode([
       "AddTo" => $addTo,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ForumID" => base64_encode($contentID),
       "PostID" => base64_encode($additionalContentID),
       "v" => base64_encode("ForumPost:Purge")
      ], true);
      $vote = ($data["From"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($additionalContentID)."&List=".base64_encode("Forum Posts")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("ForumPost:Edit")."&FID=$contentID&ID=$additionalContentID"),
       "Notes" => base64_encode("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($contentID)."&dbID=".base64_encode("post")),
       "Report" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("ForumPost;$contentID;$additionalContentID")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode("$contentID-$additionalContentID")."&Type=".base64_encode($type)."&Username=".base64_encode($data["From"])),
       "View" => base64_encode("v=".base64_encode("ForumPost:Home")."&AddTo=$addTo&FID=$contentID&ID=$additionalContentID"),
       "Vote" => base64_encode("v=$vote&ID=$additionalContentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "Member") {
     $data = $this->Data("Get", ["mbr", $contentID]);
     $empty = $data["Inactive"] ?? 0;
     $empty = $data["Purge"] ?? $empty;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     $them = $data["Login"]["Username"] ?? "";
     if($empty == 0) {
      $attachments = $data["Personal"]["CoverPhoto"] ?? "";
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode($attachments)."&Type=".base64_encode("CoverPhoto"));
      $body = "";
      $coverPhoto = $data["Personal"]["CoverPhoto"] ?? $coverPhoto;
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
       "ShareLink" => $this->base."/@$them",
       "View" => base64_encode("v=".base64_encode("Profile:Home")."&AddTo=$addTo&Card=1&UN=".base64_encode($them)),
       "Vote" => base64_encode("v=$vote&ID=".md5($them)."&Type=4")
      ];
     }
    } elseif($type == "Page") {
     $data = $this->Data("Get", ["pg", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["Attachments"] ?? [];
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $body = $this->PlainText([
       "Data" => $body,
       "Decode" => 1
      ]);
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $viewData = json_encode([
       "AddTo" => $addTo,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($contentID),
       "v" => base64_encode("Page:Purge")
      ], true);
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Pages")),
       "Chat" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode($contentID)."&Integrated=1"),
       "Contributors" => base64_encode("v=".base64_encode("Search:Containers")."&CARD=1&ID=".base64_encode($contentID)."&Type=".base64_encode("Article")."&st=Contributors"),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("Page:Edit")."&ID=".base64_encode($contentID)),
       "Notes" => base64_encode("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($contentID)."&dbID=".base64_encode("pg")),
       "Report" => base64_encode("v=".base64_encode("Congress:Report")."&ID=".base64_encode("Page;".$contentID)),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "Subscribe" => base64_encode("v=".base64_encode("WebUI:SubscribeSection")."&ID=$contentID&Type=Article"),
       "View" => $this->AESencrypt("v=".base64_encode("Page:Home")."&AddTo=$addTo&BackTo=$backTo&ID=$contentID&ParentPage=$parentView"),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=2")
      ];
     }
    } elseif($type == "Poll") {
     $data = $this->Data("Get", ["poll", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode("")."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $viewData = json_encode([
       "AddTo" => $addTo,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($contentID),
       "v" => base64_encode("Poll:Purge")
      ], true);
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Polls")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($data["UN"])),
       "View" => base64_encode("v=".base64_encode("Poll:Home")."&AddTo=$addTo&ID=$contentID")
      ];
     }
    } elseif($type == "Product") {
     $data = $this->Data("Get", ["product", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["DemoFiles"] ?? [];
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $body = $this->PlainText([
       "Data" => $body,
       "Decode" => 1,
       "HTMLDecode" => 1
      ]);
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $title = $data["Title"] ?? "";
      $viewData = json_encode([
       "AddTo" => $addTo,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($contentID),
       "v" => base64_encode("Product:Purge")
      ], true);
      $vote = ($data["UN"] != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Products")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("Product:Edit")."&ParentView=Product$contentID&Editor=".$data["Category"]."&ID=$contentID&Shop=".md5($data["UN"])),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".$data["UN"]),
       "View" => $this->AESencrypt("v=".base64_encode("Product:Home")."&AddTo=$addTo&CARD=1&ID=$contentID&UN=".base64_encode($data["UN"])),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
     }
    } elseif($type == "Shop") {
     $data = $this->Data("Get", ["shop", $contentID]);
     $owner = $this->Data("Get", ["mbr", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty2 = $owner["Purge"] ?? 0;
     $empty = (empty($data) || empty($owner) || $empty == 1 || $empty2 == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["CoverPhoto"] ?? "";
      $attachments = base64_encode("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode($attachments)."&Type=".base64_encode("CoverPhoto"));
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = $data["Description"] ?? "";
      $vote = (md5($you) != $contentID) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Shops")),
       "Chat" => base64_encode("v=".base64_encode("Chat:Home")."&Card=1&Group=1&ID=".base64_encode(md5("Shop$contentID"))."&Integrated=1"),
       "Edit" => base64_encode("v=".base64_encode("Shop:Edit")."&Shop=".base64_encode($contentID)."&Username=".base64_encode($owner["Login"]["Username"])),
       "Revenue" => base64_encode("v=".base64_encode("Revenue:Home")."&Card=1&Shop=".base64_encode($owner["Login"]["Username"])),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($owner["Login"]["Username"])),
       "ShareLink" => $this->base."/MadeInNewYork/".$owner["Login"]["Username"],
       "View" => base64_encode("v=".base64_encode("Shop:Home")."&AddTo=$addTo&CARD=1&UN=".base64_encode($owner["Login"]["Username"])),
       "Vote" => base64_encode("v=$vote&ID=$contentID&Type=4")
      ];
      $title = $data["Title"] ?? "";
     }
    } elseif($type == "StatusUpdate") {
     $data = $this->Data("Get", ["su", $contentID]);
     $empty = $data["Purge"] ?? 0;
     $empty = (empty($data) || $empty == 1) ? 1 : 0;
     if($empty == 0) {
      $attachments = $data["Attachments"] ?? [];
      $attachments = $this->AESencrypt("v=".base64_encode("LiveView:InlineMossaic")."&ID=".base64_encode(implode(";", $attachments))."&Type=".base64_encode("DLC"));
      $body = $data["Body"] ?? "";
      $body = $this->Excerpt($this->PlainText([
       "Data" => base64_decode($body),
       "Display" => 1,
       "HTMLDecode" => 1
      ]), 180);
      $coverPhoto = $data["CoverPhoto"] ?? $coverPhoto;
      $description = "";
      $from = $data["From"] ?? "";
      $title = "Update by <em>$from</em> from ".$this->TimeAgo($data["Created"]);
      $viewData = json_encode([
       "AddTo" => $addTo,
       "SecureKey" => base64_encode($y["Login"]["PIN"]),
       "ID" => base64_encode($contentID),
       "v" => base64_encode("StatusUpdate:Purge")
      ], true);
      $vote = ($from != $you) ? base64_encode("Vote:Containers") : base64_encode("Vote:ViewCount");
      $options = [
       "Block" => base64_encode("v=".base64_encode("Profile:Blacklist")."&Command=".base64_encode($blockCommand)."&Content=".base64_encode($contentID)."&List=".base64_encode("Status Updates")),
       "Delete" => base64_encode("v=".base64_encode("Authentication:ProtectedContent")."&Dialog=1&ViewData=".base64_encode($viewData)),
       "Edit" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&SU=$contentID"),
       "Notes" => $this->AESencrypt("v=".base64_encode("Congress:Notes")."&ID=".base64_encode($contentID)."&dbID=".base64_encode("su")),
       "Share" => base64_encode("v=".base64_encode("Share:Home")."&ID=".base64_encode($contentID)."&Type=".base64_encode($type)."&Username=".base64_encode($from)),
       "ShareLink" => $this->base."/@$from/status/$contentID",
       "View" => base64_encode("v=".base64_encode("StatusUpdate:Home")."&AddTo=$addTo&SU=$contentID"),
       "Vote" => $this->AESencrypt("v=$vote&ID=$contentID&Type=4")
      ];
     }
    }
   }
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
   $id = $this->UUID();
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
       "p", $this->Excerpt($body, 200)
      ]).$this->Element(["div",
       $this->Element([
        "h4", "&bull; &bull; &bull;"
       ]), ["class" => "Attachments$id SideScroll"]
      ]).$this->Element([
       "script", "UpdateContent('.Attachments$id', '$attachments');"
      ]), ["class" => "InnerMargin FrostedBright Rounded"]
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
  function GetMonthConversion(int $month) {
   $r = ($month == "01") ? "January" : $month;
   $r = ($month == "02") ? "February" : $r;
   $r = ($month == "03") ? "March" : $r;
   $r = ($month == "04") ? "April" : $r;
   $r = ($month == "05") ? "May" : $r;
   $r = ($month == "06") ? "June" : $r;
   $r = ($month == "07") ? "July" : $r;
   $r = ($month == "08") ? "August" : $r;
   $r = ($month == "09") ? "September" : $r;
   $r = ($month == "10") ? "October" : $r;
   $r = ($month == "11") ? "November" : $r;
   $r = ($month == "12") ? "December" : $r;
   return $r;
  }
  function GetSourceFromExtension(array $a) {
   $_ALL = $this->config["XFS"]["FT"] ?? [];
   $file = $a[1] ?? "";
   $source = $this->PlainText([
    "Data" => "[Media:Document]",
    "Display" => 1
   ]);
   $r = $this->efs.$source;
   if(!empty($a[0]) && !empty($file)) {
    if(!is_array($file)) {
     $extension = explode(".", $file)[1] ?? "";
     $name = $file;
    } else {
     $extension = $file["EXT"];
     $name = $file["Name"];
    } if(in_array($extension, $_ALL["A"])) {
     $source = $this->PlainText([
      "Data" => "[Media:Audio]",
      "Display" => 1
     ]);
    } elseif(in_array($extension, $_ALL["D"])) {
     $source = $this->PlainText([
      "Data" => "[Media:Document]",
      "Display" => 1
     ]);
    } elseif(in_array($extension, $_ALL["P"])) {
     $source = $this->Thumbnail([
      "File" => $name,
      "Username" => $a[0]
     ])["FullPath"];
    } elseif(in_array($extension, $_ALL["V"])) {
     $source = $this->PlainText([
      "Data" => "[Media:Video]",
      "Display" => 1
     ]);
    } if(in_array($extension, $_ALL["P"])) {
     $r = $source;
    }
   }
   return $r;
  }
  function GetSymbolicLinks($data = [], string $type, $extras = []) {
   $links = [];
   $type = $type ?? "";
   if($type == "LiveView") {
    $_LiveView = base64_encode("LiveView:InlineMossaic");
    $albums = $data["Albums"] ?? [];
    $albums = base64_encode(implode(";", $albums));
    $articles = $data["Articles"] ?? [];
    $articles = base64_encode(implode(";", $articles));
    $attachments = $data["Attachments"] ?? [];
    $attachments = base64_encode(implode(";", $attachments));
    $blogs = $data["Blogs"] ?? [];
    $blogs = base64_encode(implode(";", $blogs));
    $blogPosts = $data["BlogPosts"] ?? [];
    $blogPosts = base64_encode(implode(";", $blogPosts));
    $chats = $data["Chats"] ?? [];
    $chats = base64_encode(implode(";", $chats));
    $demoFiles = $data["DemoFiles"] ?? [];
    $demoFiles = base64_encode(implode(";", $demoFiles));
    $forums = $data["Forums"] ?? [];
    $forums = base64_encode(implode(";", $forums));
    $forumPosts = $data["ForumPosts"] ?? [];
    $forumPosts = base64_encode(implode(";", $forumPosts));
    $members = $data["Members"] ?? [];
    $members = base64_encode(implode(";", $members));
    $polls = $data["Polls"] ?? [];
    $polls = base64_encode(implode(";", $polls));
    $products = $data["Products"] ?? [];
    $products = base64_encode(implode(";", $products));
    $productType = $extras["ProductType"] ?? "ProductNotBundled";
    $shops = $data["Shops"] ?? [];
    $shops = base64_encode(implode(";", $shops));
    $updates = $data["Updates"] ?? [];
    $updates = base64_encode(implode(";", $updates));
    $links = [
     "Albums" => $this->AESencrypt("v=$_LiveView&ID=$albums&Type=".base64_encode("Album")),
     "Articles" => $this->AESencrypt("v=$_LiveView&ID=$articles&Type=".base64_encode("Article")),
     "Attachments" => $this->AESencrypt("v=$_LiveView&ID=$attachments&Type=".base64_encode("DLC")),
     "Blogs" => $this->AESencrypt("v=$_LiveView&ID=$blogs&Type=".base64_encode("Blog")),
     "BlogPosts" => $this->AESencrypt("v=$_LiveView&ID=$blogPosts&Type=".base64_encode("BlogPost")),
     "Chats" => $this->AESencrypt("v=$_LiveView&ID=$chats&Type=".base64_encode("Chat")),
     "DemoFiles" => $this->AESencrypt("v=$_LiveView&ID=$demoFiles&Type=".base64_encode("DemoFile")),
     "Forums" => $this->AESencrypt("v=$_LiveView&ID=$forums&Type=".base64_encode("Forum")),
     "ForumPosts" => $this->AESencrypt("v=$_LiveView&ID=$forumPosts&Type=".base64_encode("ForumPost")),
     "Members" => $this->AESencrypt("v=$_LiveView&ID=$members&Type=".base64_encode("NonArtist")),
     "Polls" => $this->AESencrypt("v=$_LiveView&ID=$polls&Type=".base64_encode("Poll")),
     "Products" => $this->AESencrypt("v=$_LiveView&ID=$products&Type=".base64_encode($productType)),
     "Shops" => $this->AESencrypt("v=$_LiveView&ID=$shops&Type=".base64_encode("Shop")),
     "Updates" => $this->AESencrypt("v=$_LiveView&ID=$updates&Type=".base64_encode("StatusUpdate")),
    ];
   }
   return $links;
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
   $age = $a["Age"] ?? $this->config["minAge"];
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
   $lastPasswordChange = $a["LastPasswordChange"] ?? $now;
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
     "LastPasswordChange" => $lastPasswordChange,
     "OnlineStatus" => $onlineStatus,
     "Registered" => $registered
    ],
    "ArtistCommissionsPaid" => [],
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
    "GroupChats" => [],
    "Inactive" => 0,
    "Login" => [
     "Password" => md5($password),
     "PIN" => md5($pin),
     "RequirePassword" => "Yes",
     "Username" => $username
    ],
    "Pages" => $pages,
    "Personal" => [
     "Age" => $age,
     "AutoResponse" => "",
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
     "RelationshipWith" => "",
     "UIVariant" => 0
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
     "PassPhrase" => "",
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
     "Developer" => [
      "A" => 1,
      "B" => $now,
      "E" => $now
     ],
     "VIP" => [
      "A" => 0,
      "B" => $now,
      "E" => $this->TimePlus($now, 1, "month")
     ]
    ],
    "Verified" => 0
   ];
  }
  function ObfuscateEmail($email) {
   $email = explode("@", $email);
   $name = implode("@", array_slice($email, 0, count($email) - 1));
   $length  = floor(strlen($name) / 2);
   return substr($name, 0, $length).str_repeat("*", $length)."@".end($email);   
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
    $defaultUI = $this->config["App"]["UIVariant"] ?? 2;
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
     "[App.DefaultUI]" => $defaultUI,
     "[App.Name]" => $this->config["App"]["Name"],
     "[App.Username]" => $this->config["App"]["Name"],
     "[base]" => $this->base,
     "[efs]" => $this->efs,
     "[plus]" => "+",
     "[space]" => "&nbsp;",
     "[percent]" => "%"
    ], $r]);
    if($a["BBCodes"] == 1) {
     $r = $this->RecursiveChange([[
      "/\[b\](.*?)\[\/b\]/is" => "<strong>$1</strong>",
      "/\[d:.(.*?)\](.*?)\[\/d\]/is" => "<div class=\"$1\">$2</div>\r\n",
      "/\[d:#(.*?)\](.*?)\[\/d\]/is" => "<div id=\"$1\">$2</div>\r\n",
      "/\[i\](.*?)\[\/i\]/is" => "<em>$1</em>",
      "/\[u\](.*?)\[\/u\]/is" => "<u>$1</u>",
      "/\[(.*?)\[(.*?)\]:(.*?)\]/is" => "<$1 $2>$3</$1>",
      "/\[IMG:s=(.*?)&w=(.*?)\]/is" => "<img src=\"$1\" style=\"width:$2\"/>",
      "/\[P:(.*?)\]/is" => "<p>$1</p>",
      "/@+([A-Za-z0-9_]+)/" => $this->Element(["button", "&commat;$1", [
       "onclick" => "W('".$this->base."/@$1', '_blank');"
      ]]),
      "/\#+([A-Za-z0-9_]+)/" => $this->Element(["button", "#$1", [
       "onclick" => "W('".$this->base."/topics/$1', '_blank');"
      ]])
     ], $r, 0]);
    }
   } if($a["HTMLEncode"] == 1) {
    $r = htmlentities($r);
   } if($a["Encode"] == 1) {
    $r = base64_encode(urlencode(urlencode($r)));
   } if($a["Processor"] == 1) {
    $r = $this->AESencrypt($r);
   }
   return $r;
   // DISOLVE ALL ABOVE FUNCTIONALITY UPON MIGRATION TO CLIENT
   /*--$articleCard = base64_encode("Page:Card");
    $defaultUI = $this->config["App"]["UIVariant"] ?? 2;
    $r = preg_replace_callback("/\[Article:(.*?)\]/i", array(&$this, "GetArticle"), $r);
    $r = preg_replace_callback("/\[Embed:(.*?)\]/i", array(&$this, "GetEmbeddedLink"), $r);
    $r = preg_replace_callback("/\[Extension:(.*?)\]/i", array(&$this, "GetExtension"), $r);
    $r = preg_replace_callback("/\[Media:(.*?)\]/i", array(&$this, "Media"), $r);
    $r = preg_replace_callback("/\[Translate:(.*?)\]/i", array(&$this, "Translate"), $r);
    return $this->Change([[
     "[App.Base]" => $this->base,
     "[App.BillOfRights]" => base64_encode("v=$articleCard&ID=".base64_encode("1a35f673a438987ec93ef5fd3605b796")),
     "[App.Constitution]" => base64_encode("v=$articleCard&ID=".base64_encode("b490a7c4490eddea6cc886b4d82dbb78")),
     "[App.CopyrightInfo]" => $this->GetCopyrightInformation(),
     "[App.CurrentYear]" => date("Y"),
     "[App.DefaultUI]" => $defaultUI,
     "[App.Name]" => $this->config["App"]["Name"],
     "[App.Username]" => $this->config["App"]["Name"],
     "[base]" => $this->base,
     "[efs]" => $this->efs,
     "[plus]" => "+",
     "[space]" => "&nbsp;",
     "[percent]" => "%"
    ], $r]);--*/
  }
  function ProfilePicture(array $member, $style = NULL) {
   $style = (!empty($style)) ? " style=\"$style\"" : "";
   $base = $this->efs;
   $profilePicture = $member["Personal"] ?? [];
   $profilePicture = $member["Personal"]["ProfilePicture"] ?? "";
   $source = "[Media:LOGO]";
   if(!empty($profilePicture) && @fopen($base.base64_decode($profilePicture), "r")) {
    $source = $base.base64_decode($profilePicture);
   }
   return $this->PlainText([
    "Data" => "<img src=\"$source\"$style/>",
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
  function RecursiveDirectoryPurge(string $directory): void {
   $i = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
   $media = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::CHILD_FIRST);
   foreach($media as $media) {
    if($media->isDir()) {
     rmdir($media->getPathname());
    } else {
     unlink($media->getPathname());
    }
   }
   rmdir($directory);
  }
  function RenderGhostMember() {
   $ghost = $this->Member(uniqid("Ghost_"));
   $ghost["Personal"]["DisplayName"] = "Anonymous";
   return $ghost;
  }
  function RenderEventMedia() {
   $events = $this->config["PublicEvents"] ?? [];
   $r = [
    "Banner" => "",
    "CoverPhoto" => $this->PlainText([
     "BBCodes" => 1,
     "Data" => "[Media:CP]"
    ]),
    "Events" => $events
   ];
   foreach($events as $event => $info) {
    if($info["Active"] == 1) {
     $r["Banner"] = $this->Change([[
      "[Banner.Link]" => $info["Link"],
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
  function RenderSearchUI(string $variantID) {
   $id = uniqid("ReSearch".md5($this->timestamp));
   $variants = $this->Data("Get", ["app", md5("SearchUI")]);
   $variant = $this->Element(["p", "No Search UI found for <em>$variantID</em>."]);
   for($i = 0; $i < count($variants); $i++) {
    $info = $variants[$i] ?? [];
    if(!empty($info["UI"]) && $info["ID"] == $variantID) {
     $variant = base64_decode($info["UI"]);
     break;
    }
   }
   return $this->Change([[
    "[Search.ID]" => $id,
    "[Search.UI]" => $this->Change([[
     "[Search.ID]" => $id
    ], $variant])
   ], $this->Extension("caa64184e321777584508a3e89bd6aea")]);
  }
  function RenderUI(string $variantID) {
   $variants = $this->Data("Get", ["app", md5("MainUI")]);
   $variant = $this->Element(["p", "No UI found for <em>$variantID</em>."]);
   for($i = 0; $i < count($variants); $i++) {
    $info = $variants[$i] ?? [];
    if(!empty($info["UI"]) && $info["ID"] == $variantID) {
     $variant = base64_decode($info["UI"]);
     break;
    }
   }
   return $variant;
  }
  function RenderView(string $data) {
   $data = json_decode($data, true);
   $view = $data["View"] ?? $this->Element([
    "p", "No View Data<br/>Source Data: ".json_encode($data, true)
   ]);
   return $view;
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
       "body", $this->Change([[
        "[Mail.Message]" => $a["Message"]
       ], $this->Extension("c790e0a597e171ff1d308f923cfc20c9")])
      ])
     ]);
     $data = $this->cypher->MailCredentials();
     $data = [
      "Host" => $data["Host"],
      "Message" => base64_encode($message),
      "Password" => $data["Password"],
      "Title" => base64_encode($a["Title"]),
      "To" => base64_encode(filter_var($a["To"], FILTER_VALIDATE_EMAIL)),
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
  function Statistic(string $statistic) {
   $statistics = $this->Data("Get", ["app", md5("stats")]) ?? [];
   $stat = $statistics[date("Y")][date("m")][date("d")][$statistic] ?? 0;
   $statistics[date("Y")][date("m")][date("d")][$statistic] = $stat + 1;
   $this->Data("Save", ["app", md5("stats"), $statistics]);
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
  function UUID($data = null) {
   $data = $data ?? random_bytes(16);
   assert(strlen($data) == 16);
   $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
   $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
   $data = vsprintf("%s%s-%s-%s-%s-%s%s%s-", str_split(bin2hex($data), 4));
   return uniqid($data);
  }
  function VerificationBadge() {
   return $this->Element(["span", NULL, [
    "alt" => "This Member is verified via Purchase.",
    "class" => "Verified"
   ]]);
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
    $contentID = $a[1] ?? "";
    if(!empty($contentID) ) {
     $contentID = base64_decode($contentID);
     $content = $oh->GetContentData([
      "Blacklisted" => 0,
      "ID" => base64_encode($contentID)
     ]);
     $preview = $content["Preview"] ?? [];
     $r = $preview["Empty"];
     if($content["Empty"] == 0) {
      $options = $content["ListItem"]["Options"] ?? [];
      $r = ($content["Empty"] == 1) ? $preview["Empty"] : $preview["Content"];
      $r = (!empty($options["View"])) ? $oh->Element(["button", $oh->Element([
       "p", $r, ["class" => "InnerMargin"]
      ]).$oh->Element([
       "p", "View in Full", ["class" => "CenterText"]
      ]), [
       "class" => "FrostedBright OpenCard Rounded",
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
  public static function Media($a = NULL) {
   $oh = New Core;
   $file = $oh->config["Media"][$a[1]]["File"] ?? "";
   if(!empty($a) && !empty($file)) {
    $r = $oh->efs.$oh->ID."/$file";
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
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
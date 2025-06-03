<?php
 if(!class_exists("OH")) {
  $protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") ? "https://" : "http://";
  $host = $protocol.$_SERVER["HTTP_HOST"]."/";
  header("Location: $host");
  exit;
 }
 Class Share extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Chat(array $data): string {
   $_Dialog = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $body = $data["Body"] ?? "";
   $id = $data["ID"] ?? "";
   $query = $data["Query"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = [
     "Body" => "The Content Body is missing."
    ];
    if(!empty($body)) {
     $_Dialog = "";
     $_View = "";
     $contants = $this->core->Data("Get", ["cms", md5($you)]);
     $contacts = $contacts["Contacts"] ?? [];
     $extension = $this->core->Extension("343f78d13872e3b4e2ac0ba587ff2910");
     $i = 0;
     $id = base64_decode($id);
     foreach($contacts as $member => $info) {
      if(empty($query) || strpos($member, $query) !== false) {
       $blocked = $this->core->CheckBlocked([$y, "Members", md5($member)]);;
       $_Member = $this->core->GetContentData([
        "ID" => base64_encode("Member;".md5($member))
       ]);
       if($_Member["Empty"] == 0 && $blocked == 0) {
        $i++;
        $online = $t["Activity"]["OnlineStatus"] ?? 0;
        $online = ($online == 1) ? $this->core->Element([
         "span",
         NULL,
         ["class" => "online"]
        ]) : "";
        $_View .= $this->core->Change([[
         "[Chat.DisplayName]" => $t["Personal"]["DisplayName"],
         "[Chat.Online]" => $online,
         "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%"),
         "[Chat.View]" => base64_encode("v=".base64_encode("Chat:Home")."&1on1=1&Body=$body&Card=1&Username=".base64_encode($t["Login"]["Username"]))
        ], $extension]);
       }
      }
     }
     $na = "No Results";
     $na .= (!empty($query)) ? " for $query" : "";
     $_View = ($i == 0) ? $this->core->Element([
      "h4", $na, ["class" => "CenterText UpperCase"]
     ]) : $_View;
     $_View = [
      "ChangeData" => [],
      "Extension" => $this->core->AESencrypt($_View)
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function GroupChat(array $data): string {
   $_Dialog = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $body = $data["Body"] ?? "";
   $id = $data["ID"] ?? "";
   $query = $data["Query"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = [
     "Body" => "The Content Body is missing."
    ];
    if(!empty($body)) {
     $_Dialog = "";
     $_Extension = $this->core->Extension("343f78d13872e3b4e2ac0ba587ff2910");
     $_View = "";
     $groups = $this->core->DatabaseSet("Chat");
     $i = 0;
     $id = base64_decode($id);
     foreach($groups as $key => $group) {
      $group = str_replace("nyc.outerhaven.chat.", "", $group);
      $blocked = $this->core->CheckBlocked([$y, "Group Chats", $group]);
      $_Chat = $this->core->GetContentData([
       "ID" => base64_encode("Chat;$group"),
       "Integrated" => 1
      ]);
      if($_Chat["Empty"] == 0 && $blocked == 0) {
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
       if($chat["UN"] == $you || ($blocked == 0 && $nsfw == 1 && $privacy == 1)) {
        $contributors = $chat["Contributors"] ?? [];
        $isGroupChat = $chat["Group"] ?? 0;
        if(!empty($contributors) || $isGroupChat == 1) {
         $displayName = $chat["Title"] ?? "Group Chat";
         $check = (strpos($displayName, $query) !== false) ? 1 : 0;
         $check2 = (strpos($chat["Description"], $query) !== false) ? 1 : 0;
         if(empty($query) || $check == 1 || $check2 == 1) {
          $i++;
          $t = $this->core->Member($this->core->ID);
          $_View .= $this->core->Change([[
           "[Chat.DisplayName]" => $displayName,
           "[Chat.Online]" => "",
           "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%"),
           "[Chat.View]" => base64_encode("v=".base64_encode("Chat:Home")."&Body=$body&Card=1&Group=1&ID=".base64_encode($group)."&Integrated=1")
          ], $_Extension]);
         }
        }
       }
      }
     }
     $na = "No Results";
     $na .= (!empty($query)) ? " for $query" : "";
     $_View = ($i == 0) ? $this->core->Element([
      "h4", $na, ["class" => "CenterText UpperCase"]
     ]) : $_View;
     $_View = [
      "ChangeData" => [],
      "Extension" => $this->core->AESencrypt($_View)
     ];
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function Home(array $data): string {
   $_Commands = "";
   $_Dialog = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $data = $data["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = [
     "Body" => "The Content Type is missing."
    ];
    $checkItOut = "";
    $embedCode = "";
    $id = base64_decode($id);
    $shareTitle = "Content";
    $type = base64_decode($type);
    if(!empty($type)) {
     $_Dialog = "";
     $contentID = "Missing";
     $username = $data["Username"] ?? base64_encode($this->core->ID);
     $username = base64_decode($username);
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out ".$t["Personal"]["DisplayName"]."'s Profile!";
     $contentID = ($type == "Album") ? "$type;$username;$id" : $contentID;
     $contentID = ($type == "Article") ? "$type;$id" : $contentID;
     $contentID = ($type == "Blog") ? "$type;$id" : $contentID;
     $contentID = ($type == "BlogPost") ? "$type;$id" : $contentID;
     $contentID = ($type == "Chat") ? "$type;$id" : $contentID;
     $contentID = ($type == "File") ? "$type;$username;$id" : $contentID;
     $contentID = ($type == "Forum") ? "$type;$id" : $contentID;
     $contentID = ($type == "ForumPost") ? "$type;$id" : $contentID;
     $contentID = ($type == "Poll") ? "$type;$id" : $contentID;
     $contentID = ($type == "Product") ? "$type;$id" : $contentID;
     $contentID = ($type == "Member") ? "$type;$id" : $contentID;
     $contentID = ($type == "Shop") ? "$type;$id" : $contentID;
     $contentID = ($type == "StatusUpdate") ? "$type;$id" : $contentID;
     $contentID = base64_encode($contentID);
     $content = $this->core->GetContentData([
      "BackTo" => "",
      "ID" => $contentID,
      "Integrated" => 1,
      "Owner" => $username
     ]) ?? [];
     $listItem = $content["ListItem"] ?? [];
     $description = $listItem["Description"] ?? "";
     $embed = base64_encode("$username-$contentID");
     $embedCode = "[Embed:$embed]";
     $link = $content["ListItem"]["Options"]["ShareLink"] ?? "";
     $preview = $content["Preview"] ?? [];
     $preview = ($content["Empty"] == 1) ? $preview["Empty"] : $preview["Content"];
     $title = $listItem["Title"] ?? "";
     $checkItOut = ($type == "Member") ? $checkItOut : "Check out <em>$title</em> by ".$t["Personal"]["DisplayName"]."!";
     $checkItOut = ($type == "StatusUpdate") ? "Check out ".$t["Personal"]["DisplayName"]."'s status update!" : $checkItOut;
     $body = base64_encode($this->core->PlainText([
      "Data" => $this->core->Element([
       "p", $checkItOut
      ]).$this->core->Element([
       "div", $embedCode, ["class" => "NONAME"]
      ]),
      "HTMLEncode" => 1
     ]));
     $_Card = [
      "Front" => [
       "ChangeData" => [
        "[Share.Code]" => $embed,
        "[Share.ID]" => $id,
        "[Share.Link]" => "",
        "[Share.Preview]" => $preview,
        "[Share.StatusUpdate]" => $this->core->AESencrypt("v=".base64_encode("StatusUpdate:Edit")."&Body=$body&new=1&UN=".base64_encode($you)),
        "[Share.Title]" => $title
       ],
       "ExtensionID" => "de66bd3907c83f8c350a74d9bbfb96f6"
      ]
     ];
     $_Commands = [
      [
       "Name" => "RenderInputs",
       "Parameters" => [
        ".ChatSearch$id",
        [
         [
          "Attributes" => [
           "class" => "LightSearch",
           "data-container" => ".ShareViaChat[Share.ID]",
           "data-list" => "[Share.Chat]",
           "placeholder" => "Search Group Chats...",
           "type" => "text"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => ""
         ]
        ]
       ]
      ],
      [
       "Name" => "RenderInputs",
       "Parameters" => [
        ".GroupChatSearch$id",
        [
         [
          "Attributes" => [
           "class" => "LightSearch",
           "data-container" => ".ShareViaGroupChat[Share.ID]",
           "data-list" => "[Share.Chat.Group]",
           "placeholder" => "Search Group Chats...",
           "type" => "text"
          ],
          "Options" => [],
          "Type" => "Text",
          "Value" => ""
         ]
        ]
       ]
      ],
      [
       "Name" => "UpdateContentAES",
       "Parameters" => [
        ".ShareViaChat$id",
        $this->core->AESencrypt("v=".base64_encode("Share:Chat")."&Body=$body&ID=".base64_encode($id))
       ]
      ],
      [
       "Name" => "UpdateContentAES",
       "Parameters" => [
        ".ShareViaGroupChat$id",
        $this->core->AESencrypt("v=".base64_encode("Share:GroupChat")."&Body=$body&ID=".base64_encode($id))
       ]
      ],
      [
       "Name" => "UpdateContentAES",
       "Parameters" => [
        ".ShareViaRecentChat$id",
        $this->core->AESencrypt("v=".base64_encode("Share:RecentChats")."&Body=$body&ID=".base64_encode($id))
       ]
      ]
     ];
    }
   }
   return $this->core->JSONResponse([
    "Card" => $_Card,
    "Commands" => $_Commands,
    "Dialog" => $_Dialog
   ]);
  }
  function RecentChats(array $data): string {
   $_Dialog = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $_View = "";
   $data = $data["Data"] ?? [];
   $body = $data["Body"] ?? "";
   $id = $data["ID"] ?? "";
   $query = $data["Query"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $_Dialog = [
     "Body" => "The Content Body is missing."
    ];
    if(!empty($body)) {
     $_Dialog = "";
     $_View = "";
     $chat = $this->core->Data("Get", ["chat", md5($you)]);
     $id = base64_decode($id);
     $recentChats = [];
     foreach($chat as $key => $message) {
      $to = $message["To"] ?? "";
      array_push($recentChats, $to);
     }
     $recentChats = array_reverse(array_unique($recentChats));
     foreach($recentChats as $key => $member) {
      if(empty($query) || strpos($member, $query) !== false) {
       $blocked = $this->core->CheckBlocked([$y, "Members", md5($member)]);;
       $_Member = $this->core->GetContentData([
        "ID" => base64_encode("Member;".md5($member))
       ]);
       if($_Member["Empty"] == 0 && $blocked == 0) {
        $_View .= $this->core->Element([
         "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "OpenCard Small",
          "data-encryption" => "AES",
          "data-view" => $this->core->AESencrypt("v=".base64_encode("Chat:Home")."&1on1=1&Body=$body&Card=1&Username=".base64_encode($t["Login"]["Username"]))
         ]
        ]);
       }
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AddTopMargin" => "0",
    "Dialog" => $_Dialog,
    "View" => $_View
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
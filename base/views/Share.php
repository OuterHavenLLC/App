<?php
 Class Share extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Chat(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $body = $data["Body"] ?? "";
   $id = $data["ID"] ?? "";
   $query = $data["Query"] ?? "";
   $r = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $r = [
     "Body" => "The Content Body is missing."
    ];
    if(!empty($body)) {
     $accessCode = "Accepted";
     $contants = $this->core->Data("Get", ["cms", md5($you)]) ?? [];
     $contacts = $contacts["Contacts"] ?? [];
     $extension = $this->core->Extension("343f78d13872e3b4e2ac0ba587ff2910");
     $i = 0;
     $id = base64_decode($id);
     $r = "";
     foreach($contacts as $member => $info) {
      if(empty($query) || strpos($member, $query) !== false) {
       $bl = $this->core->CheckBlocked([$y, "Members", md5($member)]);;
       $_Member = $this->core->GetContentData([
        "Blacklisted" => $bl,
        "ID" => base64_encode("Member;".md5($member))
       ]);
       if($_Member["Empty"] == 0) {
        $i++;
        $online = $t["Activity"]["OnlineStatus"] ?? 0;
        $online = ($online == 1) ? $this->core->Element([
         "span",
         NULL,
         ["class" => "online"]
        ]) : "";
        $r .= $this->core->Change([[
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
     $r = ($i == 0) ? $this->core->Element([
      "h4", $na, ["class" => "CenterText UpperCase"]
     ]) : $r;
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function GroupChat(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $body = $data["Body"] ?? "";
   $id = $data["ID"] ?? "";
   $query = $data["Query"] ?? "";
   $r = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $r = [
     "Body" => "The Content Body is missing."
    ];
    if(!empty($body)) {
     $accessCode = "Accepted";
     $extension = $this->core->Extension("343f78d13872e3b4e2ac0ba587ff2910");
     $groups = $this->core->DatabaseSet("Chat") ?? [];
     $i = 0;
     $id = base64_decode($id);
     $r = "";
     foreach($groups as $key => $group) {
      $group = str_replace("nyc.outerhaven.chat.", "", $group);
      $bl = $this->core->CheckBlocked([$y, "Group Chats", $group]);
      $_Chat = $this->core->GetContentData([
       "Blacklisted" => $bl,
       "ID" => base64_encode("Chat;$group"),
       "Integrated" => 1
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
         $check = (strpos($displayName, $query) !== false) ? 1 : 0;
         $check2 = (strpos($chat["Description"], $query) !== false) ? 1 : 0;
         if(empty($query) || $check == 1 || $check2 == 1) {
          $i++;
          $t = $this->core->Member($this->core->ID);
          $r .= $this->core->Change([[
           "[Chat.DisplayName]" => $displayName,
           "[Chat.Online]" => "",
           "[Chat.ProfilePicture]" => $this->core->ProfilePicture($t, "margin:0.5em;max-width:4em;width:90%"),
           "[Chat.View]" => base64_encode("v=".base64_encode("Chat:Home")."&Body=$body&Card=1&Group=1&ID=".base64_encode($group)."&Integrated=1")
          ], $extension]);
         }
        }
       }
      }
     }
     $na = "No Results";
     $na .= (!empty($query)) ? " for $query" : "";
     $r = ($i == 0) ? $this->core->Element([
      "h4", $na, ["class" => "CenterText UpperCase"]
     ]) : $r;
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $accessCode = "Accepted";
    $checkItOut = "";
    $embedCode = "";
    $id = base64_decode($id);
    $r = [
     "Body" => "The Content Type is missing."
    ];
    $shareTitle = "Content";
    $type = base64_decode($type);
    if(!empty($type)) {
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
      "Owner" => $username
     ]) ?? [];
     $listItem = $content["ListItem"] ?? [];
     $description = $listItem["Description"] ?? "";
     $embed = base64_encode("$username-$contentID");
     $embedCode = "[Embed:$embed]";
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
     $r = [
      "Front" => $this->core->Change([[
       "[Share.Chat]" => base64_encode("v=".base64_encode("Share:Chat")."&Body=$body&ID=".base64_encode($id)),
       "[Share.Chat.Group]" => base64_encode("v=".base64_encode("Share:GroupChat")."&Body=$body&ID=".base64_encode($id)),
       "[Share.Chat.Recent]" => base64_encode("v=".base64_encode("Share:RecentChats")."&Body=$body&ID=".base64_encode($id)),
       "[Share.Code]" => $embed,
       "[Share.ID]" => $id,
       "[Share.Link]" => "",
       "[Share.Preview]" => $preview,
       "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&Body=$body&new=1&UN=".base64_encode($you)),
       "[Share.Title]" => $title
      ], $this->core->Extension("de66bd3907c83f8c350a74d9bbfb96f6")])
     ];
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function RecentChats(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $body = $data["Body"] ?? "";
   $id = $data["ID"] ?? "";
   $query = $data["Query"] ?? "";
   $r = [
    "Body" => "The Share Card Identifier is missing."
   ];
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id)) {
    $r = [
     "Body" => "The Content Body is missing."
    ];
    if(!empty($body)) {
     $accessCode = "Accepted";
     $chat = $this->core->Data("Get", ["chat", md5($you)]) ?? [];
     $id = base64_decode($id);
     $r = "";
     $recentChats = [];
     foreach($chat as $key => $message) {
      $to = $message["To"] ?? "";
      array_push($recentChats, $to);
     }
     $recentChats = array_reverse(array_unique($recentChats));
     foreach($recentChats as $key => $member) {
      if(empty($query) || strpos($member, $query) !== false) {
       $bl = $this->core->CheckBlocked([$y, "Members", md5($member)]);;
       $_Member = $this->core->GetContentData([
        "Blacklisted" => $bl,
        "ID" => base64_encode("Member;".md5($member))
       ]);
       if($_Member["Empty"] == 0) {
        $r .= $this->core->Element([
         "button", $this->core->ProfilePicture($t, "margin:5%;width:90%"), [
          "class" => "OpenCard Small",
          "data-view" => base64_encode("v=".base64_encode("Chat:Home")."&1on1=1&Body=$body&Card=1&Username=".base64_encode($t["Login"]["Username"]))
         ]
        ]);
       }
      }
     }
    }
   }
   return $this->core->JSONResponse([
    "AccessCode" => $accessCode,
    "Response" => [
     "JSON" => "",
     "Web" => $r
    ],
    "ResponseType" => "View"
   ]);
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
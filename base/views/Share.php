<?php
 Class Share extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  function Home(array $a) {
   $accessCode = "Denied";
   $data = $a["Data"] ?? [];
   $id = $data["ID"] ?? "";
   $r = [
    "Body" => "The Share Sheet Identifier is missing."
   ];
   $type = $data["Type"] ?? "";
   $y = $this->you;
   $you = $y["Login"]["Username"];
   if(!empty($id) && !empty($type)) {
    $accessCode = "Accepted";
    $checkItOut = "";
    $embedCode = "";
    $id = base64_decode($id);
    $shareTitle = "Content";
    $type = base64_decode($type);
    $username = $data["Username"] ?? "";
    $username = (!empty($username)) ? base64_decode($username) : "";
    if($type == "Album") {
     $fileSystem = $this->core->Data("Get", ["fs", md5($un)]) ?? [];
     $fileSystem = $fileSystem["Albums"][$id] ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out <em>".$fileSystem["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!";
     $embedCode = "[Album:".base64_encode("$un;$id")."]";
     $shareTitle = $fileSystem["Title"] ?? $shareTitle;
    } elseif($type == "Article") {
     $article = $this->core->Data("Get", ["pg", $id]) ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out <em>".$article["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!";
     $embedCode = "[Article:$id]";
     $shareTitle = $article["Title"] ?? $shareTitle;
    } elseif($type == "Blog") {
     $blog = $this->core->Data("Get", ["blg", $id]) ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out <em>".$blog["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!";
     $embedCode = "[Blog:$id]";
     $shareTitle = $blog["Title"] ?? $shareTitle;
    } elseif($type == "BlogPost") {
     $post = $this->core->Data("Get", ["bp", $id]) ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out <em>".$post["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!";
     $embedCode = "[BlogPost:$id]";
     $shareTitle = $post["Title"] ?? $shareTitle;
    } elseif($type == "File") {
     $fileSystem = $this->core->Data("Get", ["fs", md5($username)]) ?? [];
     $file = $fileSystem["Files"][$id] ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out ".$file["Type"]." ".$t["Personal"]["DisplayName"]." uploaded!";
     $embedCode = "[Media:".base64_encode("$username;$id")."]";
     $shareTitle = $file["Title"] ?? $shareTitle;
    } elseif($type == "Forum") {
     $forum = $this->core->Data("Get", ["pf", $id]) ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out <em>".$forum["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!";
     $embedCode = "[Forum:$id]";
     $shareTitle = $forum["Title"] ?? $shareTitle;
    } elseif($type == "ForumPost") {
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out this Forum Post by ".$t["Personal"]["DisplayName"]."!";
     $embedCode = "[ForumPost:$id]";
     $shareTitle = $t["Personal"]["DisplayName"]."'s Forum Post" ?? $shareTitle;
    } elseif($type == "Product") {
     $product = $this->core->Data("Get", ["miny", $id]) ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out <em>".$product["Title"]."</em> by ".$t["Personal"]["DisplayName"]."!";
     $embedCode = "[Product:$id]";
     $shareTitle = $product["Title"] ?? $shareTitle;
    } elseif($type == "Profile") {
     $member = $this->core->Member($id);
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out ".$t["Personal"]["DisplayName"]."'s Profile!";
     $embedCode = "[Member:$id]";
     $shareTitle = $t["Personal"]["DisplayName"]."'s Profile";
    } elseif($type == "Shop") {
     $shop = $this->core->Data("Get", ["shop", $id]) ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out ".$t["Personal"]["DisplayName"]."'s Shop <em>".$shop["Title"]."</em>!";
     $embedCode = "[Shop:$id]";
     $shareTitle = $shop["Title"] ?? $shareTitle;
    } elseif($type == "StatusUpdate") {
     $update = $this->core->Data("Get", ["su", $id]) ?? [];
     $t = ($username == $you) ? $y : $this->core->Member($username);
     $checkItOut = "Check out ".$t["Personal"]["DisplayName"]."'s status update!";
     $embedCode = "[StatusUpdate:$id]";
     $shareTitle = "Status Update";
    }
    $body = $this->core->PlainText([
     "Data" => $this->core->Element([
      "p", $checkItOut
     ]).$this->core->Element([
      "div", $embedCode, ["class" => "NONAME"]
     ]),
     "HTMLEncode" => 1
    ]);
    $body = base64_encode($body);
    $r = $this->core->Change([[
     "[Share.Code]" => "v=".base64_encode("LiveView:GetCode")."&Code=$id&Type=$type",
     "[Share.ContentID]" => "Shop",
     "[Share.GroupMessage]" => base64_encode("v=".base64_encode("Chat:ShareGroup")."&ID=$body"),
     "[Share.ID]" => $id,
     "[Share.Link]" => "",
     "[Share.Message]" => base64_encode("v=".base64_encode("Chat:Share")."&ID=$body"),
     "[Share.StatusUpdate]" => base64_encode("v=".base64_encode("StatusUpdate:Edit")."&body=$body&new=1&UN=".base64_encode($y["Login"]["Username"])),
     "[Share.Title]" => $shareTitle
    ], $this->core->Page("de66bd3907c83f8c350a74d9bbfb96f6")]);
    $r = [
     "Front" => $r
    ];
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
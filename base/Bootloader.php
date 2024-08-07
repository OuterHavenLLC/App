<?php
 header("Access-Control-Allow-Origin: *");
 header("Access-Control-Allow-Credentials: true");
 header("Access-Control-Allow-Headers: Language, Token");
 header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
 header("Access-Control-Max-Age: 86400");
 ini_set("display_errors", "on");
 require_once(__DIR__."/Core.php");
 require_once(__DIR__."/Cypher.php");
 require_once(__DIR__."/SQL.php");
 Class OH extends Core {
  function __construct() {
   $this->core = New Core;
  }
  function view(string $view, array $data) {
   $view = explode(":", base64_decode($view));
   $documentRoot = $this->core->DocumentRoot."/base/views/";
   $group = $view[0] ?? "NA";
   $view = $view[1] ?? "NoView";
   $r = $this->core->JSONResponse([
    "AccessCode" => "Denied",
    "Response" => [
     "JSON" => "",
     "Web" => [
      "Body" => "The group <em>$group</em> could not be loaded.",
      "Header" => "Not Found",
      "Scrollable" => $this->core->Element([
       "p", "Requested URI for reference: ".$_SERVER["REQUEST_URI"]
      ])
     ]
    ],
    "ResponseType" => "View"
   ]);
   if(file_exists($documentRoot."$group.php")) {
    require_once($documentRoot."$group.php");
    $this->render = New $group;
    $r = $this->render->$view($data) ?? "";
    if(empty($r)) {
     $r = $this->core->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Not Found",
      "[Error.Message]" => "The view <em>$view</em> from group <em>$group</em> was empty, and could not be loaded."
     ], $this->core->Extension("f7d85d236cc3718d50c9ccdd067ae713")]);
    }
    $r = $this->core->PlainText([
     "Data" => $r,
     "Display" => 1
    ]);
   }
   return $r;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
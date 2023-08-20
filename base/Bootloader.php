<?php
 ini_set("display_errors", "on");
 require_once(__DIR__."/Core.php");
 Class GW extends Core {
  function __construct() {
   $this->core = New Core;
  }
  function view(string $a, array $b) {
   $a = explode(":", base64_decode($a));
   $documentRoot = $this->core->DocumentRoot."/base/views/";
   $group = $a[0] ?? "NA";
   $view = $a[1] ?? "NoView";
   $r = $this->core->JSONResponse([
    "AccessCode" => "Denied",
    "Response" => [
     "JSON" => "",
     "Web" => [
      "Body" => "The group <em>$group</em> could not be loaded.",
      "Header" => "Not Found"
     ]
    ],
    "ResponseType" => "View"
   ]);
   if(file_exists($documentRoot."$group.php")) {
    require_once($documentRoot."$group.php");
    $this->render = New $group;
    $r = $this->render->$view($b) ?? "";
    if(empty($r)) {
     $r = $this->core->Change([[
      "[Error.Back]" => "",
      "[Error.Header]" => "Not Found",
      "[Error.Message]" => "The view <em>$view</em> from group <em>$group</em> was empty, and could not be loaded."
     ], $this->core->Page("f7d85d236cc3718d50c9ccdd067ae713")]);
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
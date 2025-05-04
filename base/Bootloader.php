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
  function view(string $view, array $data, $raw = 0): string {
   $view = explode(":", base64_decode($view));
   $documentRoot = $this->core->DocumentRoot."/base/views/";
   $group = $view[0] ?? "NA";
   $view = $view[1] ?? "NoView";
   $_View = $this->core->JSONResponse([
    "Dialog" => [
     "Body" => "The group <em>$group</em> could not be loaded.",
     "Header" => "Not Found",
     "Scrollable" => $this->core->Element([
      "p", "Requested URI for reference: ".$_SERVER["REQUEST_URI"]
     ])
    ]
   ]);
   if(file_exists($documentRoot."$group.php")) {
    require_once($documentRoot."$group.php");
    $this->render = New $group;
    $_View = $this->render->$view($data) ?? "";
    $_View = (!empty($_View)) ? $this->core->PlainText([
     "Data" => $_View,
     "Display" => 1
    ]) : $this->view(base64_encode("WebUI:Error"), ["Data" => [
     "Error" => 404
    ]]);
   }
   return $_View;
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
 }
?>
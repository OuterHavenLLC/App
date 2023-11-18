<?php
 Class Common extends OH {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Authenticate("Get"));
  }
  function Create() {
   $accessCode = "Accepted";
   $y = $this->you;
   $you = $y["Lohin"]["Username"];
   // CREATE A NEW POLL
  }
  function Home(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $you = $y["Lohin"]["Username"];
  }
  function Save(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $you = $y["Lohin"]["Username"];
   // SAVE
  }
  function SaveDelete(array $a) {
   $accessCode = "Accepted";
   $data = $a["Data"] ?? [];
   $y = $this->you;
   $you = $y["Lohin"]["Username"];
   // DELETE
  }
  function __destruct() {
   // DESTROYS THIS CLASS
  }
?>
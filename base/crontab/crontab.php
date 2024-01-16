<?php
 # Cron Tab Execution Notification
 # Place this last in the Cron Job order, it is meant to notify of success.
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 $r = $oh->core->Element([
  "h1", $oh->core->config["App"]["Name"]."</em> Cron Tab"
 ]).$oh->core->Element([
  "p", "The following crob jobs have been executed:"
 ]).$oh->core->Element([
  "p", "&bull; Content Purging"
 ]).$oh->core->Element([
  "p", "&bull; Blog Index"
 ]).$oh->core->Element([
  "p", "&bull; Chat Index"
 ]).$oh->core->Element([
  "p", "&bull; Feedback Index"
 ]).$oh->core->Element([
  "p", "&bull; Forum Index"
 ]).$oh->core->Element([
  "p", "&bull; Media Index"
 ]).$oh->core->Element([
  "p", "&bull; Member Index"
 ]).$oh->core->Element([
  "p", "&bull; Poll Index"
 ]).$oh->core->Element([
  "p", "&bull; Product Index"
 ]).$oh->core->Element([
  "p", "&bull; Shop Index"
 ]).$oh->core->Element([
  "p", "&bull; Status Update Index"
 ]).$oh->core->Element([
  "p", "&bull; Thumbnail Assurance"
 ]);
 $oh->core->SendEmail([
  "Message" => $r,
  "Title" => "Cron Jobs Executed!",
  "To" => "mike@outerhaven.nyc"
 ]);
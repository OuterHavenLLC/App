<?php
 # Cron Tab Notification
 # Place this last in the Cron Job order, it is meant to notify of success.
 require_once("/var/www/html/base/Bootloader.php");
 $oh = New OH;
 $images = $oh->core->config["XFS"]["FT"]["P"] ?? [];
 $oh->core->SendEmail([
  "Message" => $oh->core->Element([
   "h2", "Tasks Executed!"
  ]).$oh->core->Element(["div", $oh->core->Element([
    "p", "The following cron jobs have been executed on ".$oh->core->timestamp.":"
   ]).$oh->core->Element([
    "p", "&bull; Blog Index"
   ]).$oh->core->Element([
    "p", "&bull; Chat Index"
   ]).$oh->core->Element([
    "p", "&bull; Feedback Index"
   ]).$oh->core->Element([
    "p", "&bull; Forum Index"
   ]).$oh->core->Element([
    "p", "&bull; Links Index"
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
   ]).$oh->core->Element([
    "p", "Please note, the Content Purge is set to run daily at midnight."
   ]), ["class" => "FrostedBright RoundedLarge"]
  ]),
  "Title" => "Cron Tab Notification",
  "To" => "mike@outerhaven.nyc"
 ]);
# Outer Haven Web App
Here you will find the up-to-date change log for all releases of the platform.
See SETUP.md (coming soon...) for Heath Kit configuration and installation.
See GNU-GPL.md for licensing information.

# Change Log
## 1.4: Safe Haven (coming soon…)
### Release Notes
This release brings bug fixes to mission-critical experiences, a hybrid schema for data storage and indexing, and the first version of Heath Kits—an installation tool to help Developers deploy a copy of our platform onto their servers.

### Platform Improvements
* Revised the media upload process.
* Added automatic redirects for protected directories.
* Revised the cron jobs to automatically purge tuples from the Re:Search database where the data file or list item has been marked for purging, except in the case of Core Media which is handled via the Media Purge view.
* Addressed several rendering issues regarding Public Event banners and the Free America Broadcasting Annex.
* Members may now choose UI varients.
* When a new Member signs up, there is now an integrated tutorial presentation in the success view.
* Attached content previews now properly render in content editors.
* Administrators may now set the default platform-wide UI variant and modify allowed media file extensions.
* When purging album, conversation, and media data, the vote databases for all comments are also purged.
* A revamped attachments UI allows Members unprecedented control over media attached to primary content. (starting full adoption...)
* Revised the means by which Cover Photos and Profile Pictures are handled at the Core level.
* A new GetSymbolicLinks() object in the Core library now handles in-line Live View links, with more link types to be added as needed.
* Members may now create a slide show of Cover Photos as an alternative to the traditional (default) Cover Photo.
* More platform-wide optimization and bug fixes.

### Artists
* Up to 100 new jobs may be active while the Hire section is enabled. After this new limit is reached, no new jobs may be created.
* VIP Members are no longer required to pay the initial invoice (pre-set) charge if the shop identifier is that of the platform shop.
* Revised the process for bundled products.
* Shop Partners may add media attachments to invoice notes.
* Charts are now used to graphically represent current sales data, comparisons, and platform statistics. (coming soon...)

### Re:Search
* A new hybrid database schema allows for faster indexing and list retrieval while protecting Member data via the encrypted data files.
* Core objects now render the search and UI variants: RenderSearchUI() and RenderUI(), respectively.
* A new consolidated ReSearch() object now renders the initial search listing, automated pagination to append groups of results while the container exists, and accounts for input-based keyup events to update the search listing in real-time.

### Heath Kit
* Coming soon...

## 1.3: Compendium
### Release Notes
This release focuses on search—both inside and outside the platform, content creation and management, new administrative and congressional functions, revamped revenue and statistics, and quality of life improvements.

### Platform Improvements
* A revamped Content Purge system now efficiently and permanently purges content and their respective dependencies from the platform.
* Download buttons now support simultanious media downloads.
* Forum Posts may now be grouped into Topics.
* The Share Card has been revamped with new options to share via 1:1 Chat, Group Chat, Status Updates, with recent contacts, or by copying the embed code or public link.
* Launched Free America Broadcasting, our radio station for V.I.P. Members to listen to and participate in.
* A new content creation tool (represented by a + button at the bottom-right corner when signed in) creates a central hub where many of the common content types may be created from.
* The Mail Service is now integrated and fully operational.
* Administrators now have a Control Panel to configure the app's core attributes, public events, core media variables, and search lists.
* App Statistics have been revamped, and are now more granular than ever with new daily, monthly, and annual totals now taken into account.
* Developers now have a Card for viewing app documentation, guides, Heath Kit downloads, and submitting feedback.
* Members may now secure Articles and Journal Entries with Pass Phrases.
* Completed the Congressional Ballot.

### Artists
* Addressed an issue that would overrite a Member's shop data even if they were adding or editing products from other shops which they contribute to.
* The Shopping History is now properly documented in real-time.
* The Checkout and Paid Messages workflow via PayPal have been streamlined, and now use the Parent-View structure.
* The Artist Dashboard is now integrated into the Shop.
* We've entirely re-built and consolidated the Revenue and Payroll system. Bi-weekly pay periods are now standard, and Artists may now add external gains and losses—such as refunds and shipping—to be accounted for.
* The Checkout, Donation, and Paid Message Payment workflows are now streamlined and integrated with their parent content.
* Members who make purchases of any kind obtain platform verification.

### Congressional Notes
* Members of Congress may now contribute Congressional Notes to add context.
* Congressional Notes may be voted upon, and the Note with the highest rating will be attached to the Articles, Blog Posts, Forum Posts, and Status Updates until another overtakes it.

### Protected Content
* Members may now add an additional layer of security for primary content—and some secondary content—with pass phrases.
* Currently protected content includes: Albums, Blogs, Blog Posts, Files, Forums, Forum Posts, Group Chats, Products, Preferences, Profiles, Shops, and Status Updates.
* Dialogs which require a PIN for authentication now run on the Protected Content workflow.

### Re:Search
* Refined content visibility across the platform.
* Streamlined and built upon the Re:Search experience.
* Added the ability to submit and index external sites.

## 1.2: Democracy
### Release Notes
A new, democratic form of content moderation is here! We are the first to establish a Congressional form of content moderation.

### Platform Improvements
* Extensions have been consolidated into a single database, and a new Extension manager has been created.
* We've streamlined the means by which most content is rendered by consolidating functionality under a new GetContentData() Core object.

### Congress
* Members may now join Congress if the House has less than 100 members and the Senate has less than 50. After these thresholds are surpassed, future staff must be elected to their Congressional roles.
* Nomination to the Congressional Ballot is now possible from a Member's profile.
* Members may now vote and elect others to Congress via the Congressional Ballot.
* Each Congressional Chamber will present a searchable grid of respective Staff.
* Reported content is now put forth for a vote in the House and, if the content is voted legal, it will be put back into circulation with prejudice.
* If the House deems reported content illegal or a deadlock occures, the Senate will make a confirmation vote.
* If the Senate finds the content legal or their is a deadlock, the content will be put back into circulation with prejudice. Otherwise, it will be marked for purging from the platform and will not be searchable.
* Prejudiced content cannot be reported further, as it is henceforth considered legal.

### Polls
* Members may now create, block, share and delete Polls.
* Members may search for and vote on other Member's Polls.

## 1.1.1: Supplemental
### Release Notes
This release resolves an issue that prevented Chat messages from rendering properly, and enables recursive client-side loading of searchable lists under the new Search() function.

## 1.1: Chat
### Release Notes
This release includes fundamental changes, stability and security improvements, and many bug fixes. It is recommended that any distributed copies of the platform be updated to this release.

### Platform Improvements
* The client-side is now responsible for rendering all form inputs and content visibility filters via new RenderInputs() and RenderVisibilityFilter() objects.
* All responses are now JSON-encoded to enable further expansion and functionality on the client-side.
* We have made many improvements to the underlying client-side functionality and GUI.
* The entire server-side has been purged of any known bugs. If you find any, please let us know by going to outerhaven.nyc/feedback.
* The File System and Payment Gateway have been updated and streamlined.

### Invoices
* Shop Partners may now create and modify Invoices, or create Service pre-sets while editing an Invoice.
* Invoices are on a step-by-step basis, so Partners may set all charges at once, or add more charges as the project moves along.
* Partners may forward invoices to other members if necessary.
* Invoices may be viewed at outerhaven.nyc/invoice/{INVOICE_ID}, or via its Card while within the app.
* Bulletins are sent out to the paying Member (if one exists) when there is a new Invoice, or an update to an existing one.
* Emails are sent out when there is a new Invoice, or an update to an existing one.

### Chat
* We've revamped our Chat experience! You have the option to use the integrated 1:1 and group chats, or go to outerhaven.nyc/chat for a standalone experience.
* Articles, Blogs, Forums, and Shops now have Group Chat functionality should the author enable it.
* Members may now create, update, and purge Group Chats.
* Paid Messages are possible via Group Chats as a form of content monetization if the owner has enabled payment processing via their shop.
* Members may bookmark Group Chats, which will appear under the titular list in the Member's personalized Chat experience.
* A platform-wide list of Group Chats is also available by going to Menu > Discover > Chat. These are subject to content visibility filters.

Lots of minor adjustments and improvements are also included in this release, and we've got some game-changing functionality in the works for 1.2!

## 1.0: Foundation
### Welcome to Outer Haven
Outer Haven is a Constitutional Social Media platform where every Member is afforded the same freedoms and rights as every U.S. citizan, no matter where they are in the world. This release includes the features listed below, among many others:

### Features
* Articles
* Blogs (requires the Blogger Subscription)
* Chat (1:1 and Group Chat, under revision)
* Content Sharing
* Collaboration (Articles, Blogs, Shops, Forums, etc.)
* File Uploads (5GB, upgradable to Unlimited Storage with the Unlimted Uploads Subscription)
* Forums
* Lost + Found (Recover Password, PIN or Username, secured with 2-Factor Authentication via Email)
* Reactions (Like or Dislike)
* Sign Up (secured with 2-Factor Authentication via Email)
* Shops
* Status Updates
* Content Subscriptions (enables Bulletins to be sent out when the related content is updated)
* Subscriptions (Artist, Blogger, Developer, File Uploads, VIP)
* We've got much more in the works!

# Cron Tab
Below is the cron tab configuration we have set up to efficiently maintain the server and (re-)establish Re:Search indexes.

## Root
MAILTO=""
@daily /bin/bash /home/mike/Upkeep.sh
@reboot /bin/bash /home/mike/Upkeep.sh

## www-data
MAILTO=""
@daily /usr/bin/php /var/www/html/base/crontab/purge.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.blog.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.blogpost.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.chat.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.extension.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.feedback.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.forum.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.forumpost.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.link.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.media.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.member.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.poll.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.product.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.shop.php
@hourly /usr/bin/php /var/www/html/base/crontab/search.statusupdate.php
@hourly /usr/bin/php /var/www/html/base/crontab/thumbnails.php
@hourly /usr/bin/php /var/www/html/base/crontab/crontab.php

We welcome your feedback regarding bugs and general suggestions via the Company Feedback form.
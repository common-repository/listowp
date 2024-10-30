=== ListoWP  ===
Contributors: JaworskiMatt, rsusanto, justmatt
Plugin Name: ListoWP
Tags: todo,to do,reminders,list,lists
Requires PHP: 7.4
Requires at least: 6.0
Tested up to: 6.2
Stable tag: 1.0.3
License: GPLv2 or later

Front-end to-do lists for your WordPress users!

== Description ==

ListoWP is a beautiful, modern, fully front-end managed to-do list plugin.

It allows any WordPress user to create Lists, Tasks (with deadlines), mark Tasks as done, move them between Lists etc.

Based on a single block (or shortcode) it looks best in a standalone Page, but can be placed anywhere in your website.

Users manage everything in the front-end, meaning they don't need any access to wp-admin.

All Lists and Tasks are private, meaning users can only see their own content.

We do plan to implement collaboration such as List sharing and Task assignments in the future.

===  ListoWP Features (Free version) ===

* Tasks (To-Do items) with title and description
* Task deadlines
* Smart Lists (Expired, Scheduled, To Do, Done, Inbox)
* Admin can enable/disable and rename Smart Lists
* Custom Lists
* Ability to drag-and-drop Lists and Tasks to reorder them
* Ability to drag-and-drop Tasks between Lists
* Light theme and Dark theme - admin can decide which one to use
* Fullscreen mode
* GDPR (and similar) compliance

=== Speaks Your Language
ListoWP is available in English and several other languages. We have included regional dialects (for example French - Canada and Spanish - Colombia) whenever possible.

* Dutch
* French
* German
* Italian
* Macedonian
* Polish
* Portuguese
* Spanish
* Russian
* Ukrainian


===  ListoWP Pro ===

The [Pro version of ListoWP](https://ListoWP.com/pricing) has all the features of ListoWP Free and more:

* **Recurring tasks** - based on the universal RFC5455 standard
* **PeepSo** profile integration
* **BuddyBoss** profile integration
* **WooCommerce** profile integration
* Hands-on **support**
* Receive any and **all future features** at no extra cost
* Yearly subscription or one time lifetime payment

== Nerdy details ==

Unlike most WordPress plugins, ListoWP is based on custom database design and will not clutter your posts tables.

It's a Single Page Application (SPA) based on a REST API, with as much Client Side Rendering (CSR) as humanly possible.

== Changelog ==
= 1.0.3 =
* Jun 5, 2023
* NEW • Ability to set icons on custom Lists
* NEW • The "done" icon in List header now toggles done Tasks visibility when clicked
* NEW • The Lists panel can now be collapsed/expanded
* IMP • List icons show on Tasks when browsing in Smart List context (except Inbox). When clicked, they open the List
* IMP • Design of the "new" buttons
* IMP • Added CSS to hide the native WordPress login/logout block in ListoWP context for logged in users
* FIX • Better handling of "initials based" List icons if the name contains special characters and spaces
* FIX • Opening the date pop-up on a "done" task results in the pop-up being hidden under the tasks below
* I8N • Japanese translation is now 70% done


= 1.0.2 =
* May 15, 2023
* I8N • 100% translation for Russian

= 1.0.1 =
* May 10, 2023
* IMP • User preferences now have saving/success indicators
* IMP • Added a loading indicator when deleting a List or a Task
* IMP • When opening a date interface, if the Task has no date, automatically suggest Today
* IMP • After GDPR wipe is performed, reload the page and apply default user preferences
* FIX • Switching Lists should not show previous Tasks while loading
* FIX • Removed race conditions leading to duplicated entries when "save" button was clicked rapidly
* FIX • Fixed a REST route conflict with another plugin with a similar name.
* I8N • Untranslatable string in configuration
* I8N • 100% translations for Dutch, French, German, Italian, Macedonian, Polish, Portuguese & Spanish


= 1.0.0 =
* Apr 17, 2023
* NEW • User preferences: ability do define default sort order and whether to hide done tasks
* NEW • Mobile list view is now under a button instead of always-on
* IMP • Minor layout improvements around deadlines and date picker UI
* IMP • Light & Dark mode color improvements
* IMP • Add "new item" button to new lists
* IMP • Mobile performance & UX


= 0.9.6 =
* Mar 28, 2023 • BETA7
* IMP • Deadline time picker now respects WordPress 12/24-hour clock
* IMP • Deadline date picker now respects the ListoWP date format
* IMP • Deadline date picker improvements: the entire field now triggers the picker, the placeholder is more user-friendly
* FIX • Task counts are now properly updated when Tasks are moved between Lists

= 0.9.5 =
* Mar 21, 2023 • BETA6
* NEW • PRO • Task deadlines can now be recurring
* NEW • Task deadlines can also be set with time
* IMP • Task deadline setting is now a drop-down menu
* IMP • PRO • BuddyBoss integration: added extra explanation to the "position" option as BB does not number the profile tabs from "1"
* IMP • PRO • Separate activation script for Pro related options & database tables
* IMP • PRO • Add a warning if admin is trying to activate both Free and Pro at the same time
* FIX • PRO • PeepSo integration: wrong use of slug & label
* FIX • Edited tasks couldn't be dragged
* FIX • Plugin activation resets configuration

= 0.9.4 =
* Mar 10, 2023 • BETA5
* IMP • User preferences are now contained in the ListoWP container
* I8N • Updated translations. New translation: Macedonian.

= 0.9.3 =
* Mar 9, 2023 • BETA4
* IMP • Phased out the "add" buttons and added placeholders for New List and New Task on top of the respective containers
* IMP • The "done" Task strikethrough now has some transparency

= 0.9.2 =
* Mar 6, 2023 • BETA3
* NEW • Dark mode
* NEW • Config: dark/light mode switch
* NEW • Recurring Tasks are mostly implemented, just missing the UI to set recurrence rules
* NEW • "Recurring" Smart Lists that will contain all recurring Tasks
* NEW • User Preferences drop-down in the lower left corner
* IMP • Phased out the usage of "a" tags where not necessary to improve mobile UX
* IMP • Tooltips are not shown on mobile as they interfere with tap actions
* IMP • Further reorganization of the UI
* IMP • Done/Not Done switch is now on the right, with more contrast
* IMP • Icons for editing and expanding description are now on the right and show on demand
* IMP • Done Tasks are no longer semi-transparent,added strikethrough to title
* IMP • Date picker improvements
* IMP • Disabled tooltips on mobile
* IMP • Improved fullscreen mode for extremely wide monitors
* FIX • Date picker not showing in Mobile Safari


= 0.9.1 =
* Mar 1, 2023 • BETA2
* NEW • Added basic GDPR features (export, delete)
* NEW • Config: setting to enable/disable GDPR functions
* NEW • Config: setting to enable/disable debug log
* IMP • Moved "due" counts in the sidebar to a notification bubble in the List icons
* IMP • Added list meta (open count, done count, due count) to the List headers
* IMP • Long list meta (counts over 1000) are now shortened to 1k, 2k etc
* IMP • Moved delete icons (Lists and Tasks) to the Edit UI
* IMP • Added "no description" placeholders for Lists and Tasks
* IMP • Improved & styled List and Task edit UI
* IMP • Added tooltips to multiple buttons
* IMP • Switched to latest FontAwesome (6.3)
* IMP • Date UI & datepicker refactoring. Short date is now used for due dates this year. Long date for tasks due in any other year (past or future)

= 0.9.0 =
* Feb 27, 2023 • BETA 1
* NEW • Task deadlines can now be set
* NEW • Config: customizable date formats (long and short)
* I8N • The following languages are at least 90% done: Polish, German, Spanish, French, Italian, Ukrainian

= 0.8.0 =
* Feb 25, 2023 • ALPHA8
* NEW • Basic full item view
* NEW • Items can now be deleted

= 0.7.0 =
* Feb 20, 2023 • ALPHA7
* NEW • Lists can now be deleted. That operation deletes all Tasks too - to avod that, drag them to another List

= 0.6.0 =
* Feb 18, 2023 • ALPHA6
* NEW • PRO • Implemented licensing
* IMP • Config: refactoring & redesign
* IMP • Moved the "cancel" button away from the form when adding a List in mobile view
* I8N • Preliminary front-end translations to Polish, German and Spanish


= 0.5.0 =
* Feb 16, 2023 • ALPHA5
* NEW • PRO • Integration: PeepSo
* NEW • PRO • Integration: BuddyBoss
* NEW • PRO • Integration: WooCommerce
* NEW • Added fullscreen mode
* NEW • Config: ability to rename  smart lists
* NEW • Config: ability to disable smart lists (except Inbox)
* IMP • Lists panel on mobile is now a fly-out sidebar


= 0.4.0 =
* Feb 15, 2023 • ALPHA4
* NEW • User made list icons are now based on the list name
* IMP • Improved mobile view
* IMP • Changed the color scheme and ordering of smart lists

= 0.3.0 =
* Feb 13, 2023 • ALPHA3
* NEW • Added some keyboard shortcuts
* IMP • More compact design with less paddings/margins

= 0.2.0 =
* Feb 10, 2023 • ALPHA2
* NEW • Redesign
* NEW • Tasks can now be completed
* NEW • Added a "Completed" and "Open" smart lists

= 0.1.0 =
* Feb 5, 2023 • ALPHA1
* NEW • Plugin structure
* NEW • Database design
* NEW • REST API design
* NEW • Lists and Tasks
* NEW • Assigning Tasks to Lists
* NEW • Drag and drop
* NEW • Smart lists: "Scheduled", "Due", "Inbox"


== Screenshots ==
1. Lists, Tasks, Deadlines (Free) & Recurring Tasks (Pro)
2. Intuitive UI with drag-and-drop features to organize Lists and Tasks
3. Mobile friendly & with a great fullscreen mode
4. Powerful configuration panel & third party integrations (Pro version only)
5. Dark mode for websites using a dark theme
6. Supports several languages out of the box
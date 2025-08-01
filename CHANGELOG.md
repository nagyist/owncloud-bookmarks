## Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [15.1.3] - 2025-07-27

### Fixed

* fix(FolderService): Always make sure description is set
* chore: Update nextcloud/vue Marcel Klehr 2 minutes ago
* fix(UNDELETE_FOLDER): Make sure to reload childrenOrder of -1
* fix(BookmarkMapper): Fix duplicated filter
* fix(CopyDialog): Fix onSubmit action
* fix(PublicFolders): Do not allow sharing publicly if disabled in global settings
* fix(FileCache): Make sure to always create cache folder
* fix(SidebarFolder) Debounce participants search
* fix(Bookmark): Prevent more 404 errors
* fix(ShareMapper#findBySharedFolder): Make sure exceptions are caught
* fix: Make db queries more typesafe
* fix(l10n): Update translations from Transifex

## [15.1.2] - 2025-07-13

### Fixed

* fix(CrawlService): archive.enabled check would always be true Marcel Klehr 8 minutes ago
* feat(ClearPreviews command): Clear last_preview column in addition to cache
* fix(BookmarkPreviewer): Do not store guaranteed null responses in cache
* fix(GenericUrlBookmarkPreviewer): Check URL not API key & Add generic previewer to previewers list
* Build(deps): Update rowbot/url requirement from ^3.0 to ^4.0
* fix(l10n): Update translations from Transifex

## [15.1.1] - 2025-06-21

### Fixed

* fix(composer): Remove dependencies that are in server already
* Fix(l10n): Update translations from Transifex

## [15.1.0] - 2025-02-23

### New

 * feat: Add support for Nextcloud 31

### Fixed

* Fix(l10n): Update translations from Transifex
* fix: Fix archive setting

## [15.0.5] - 2025-01-18

### Fixed

* fix(FileCacheGCJob): Turn error into a warning and make it clearer what's needed to fix it
* fix(archival): Properly interpret setting value
* fix(IMPORT_BOOKMARKS): Properly reload folders after import
* fix(TreeMapper#findChildren): Don't mix up type of children vs type of folder
* fix: Make export work in non-root pages
* Build(deps): Bump sanitize-html from 2.13.1 to 2.14.0
* Build(deps): Bump @nextcloud/vue from 8.21.0 to 8.22.0
* fix(FoldersController): Refactor addToFolder
* Fix(l10n): Update translations from Transifex

## [15.0.4] - 2024-12-12

### Fixed

* fix(BookmarkMapper): Be compatible with mysql ANSI_QUOTES option
* Fix(l10n): Update translations from Transifex
* fix(Bookmarkmapper): Don't count soft deleted bookmarks
* chore: update deps

## [15.0.3] - 2024-11-30

### Fixed
* fix(BookmarkService): Prevent creation of duplicate bookmarks
* fix(softUndeleteEntry): Only reset index of bookmarks if it is indeed soft-deleted
* Fix(l10n): Update translations from Transifex

## [15.0.2] - 2024-09-22

### Fix

* fix(TreeMapper#isFolderSharedWithUser): Check full depth
* fix(TreeMapper): Fix treatment of soft-deleted bookmarks

## [15.0.1] - 2024-09-12

### Fix

* Don't use existing user when header is set

## [15.0.0] - 2024-09-07

### Breaking changes

- Dropped support for Nextcloud < 30

### New

* New Nextcloud 30 UI Design
* feat(Settings): Link to app store
* feat(ux): Sync folder sidebar with opening folders

### Fixed

* fix(Db\Bookmark): Fix UTF-8 encoding of user-facing content
* fix(HtmlImport): Don't fail if add_date int is too large
* fix: Shared with you view was broken
* fix(TreeMapper#getSoftDeletedRootItems): Avoid O(n^2) algorithm
* fix(LoadingModal): Show spinner while emptying trashbin
* fix(BookmarkController#import): Give more meanigful error message when upload failed
* fix(WebviewController): Fix Copypasta
* fix(BookmarkController#countBookmarks): Fix root folder count
* fix(Navigation): Don't display Files menu entry if feature is not enabled
* fix(Search): Only search after >2 characters have been entered

## [14.2.3] - 2024-08-04

### Fixed

* fix(TreeMapper#deleteOldTrashbinItems)
* feat(Trash bin): Add "empty trash bin" button
* fix(Bookmark): Use strlen instead of mb_strlen to count bytes in string
* fix(CrawlService): Increase timeout
* fix(BackupJob): use array_merge instead of +
* fix: php 8.0 support
* fix: refactor API endpoints
* Fix(l10n): Update translations from Transifex
* Fix php 8.0 support
* fix(Settings): Archiving and Backup options were not working
* fix(previews): use secure version of screeenly (Thanks to Stefan Zweifel)

## [14.2.2] - 2024-06-17

### Fixed

* fix settings controller

## [14.2.1] - 2024-06-15

### Fixed

* perf: Use distributed cache instead of local
* fix: Folder search in root folder was broken
* fix(Navigation): limit meter was broken
* fix: Improve UX of admin settings

## [14.2.0] - 2024-06-08

### New

* feat: Visualize click count

### Fixed

* fix: Soft-deleting multiple folders breaks interface
* fix(Settings): Add icons for need-help and support-project sections
* fix(PageresBookmarkPreviewer)
* fix(FaviconPreviewer): Correctly use inferred favicon from scraper
* fix: Improve virtual scroll
* fix: Fix hard deletion of folders
* fix: Fix hard deletion of bookmarks
* fix: Small visual fixes 
* fix: Display URL if title is empty

## [14.1.2] - 2024-06-01

### Fixed

* Fix UI glitch of bookmarks spilling over to trashbin and shared folders sometimes

## [14.1.1] - 2024-06-01

### Fixed

* Fix circle usage in code to avoid dependency hell

## [14.1.0] - 2024-06-01

### New

- Allow sharing with Circles
- Add trash bin to restore deleted items (Removes trash bin items after two months)
- Add help and project support sections to settings

### Fixed

* fix(BookmarkMapper#_filterFolder): Include current folder in search Marcel Klehr 2 minutes ago
* perf(TreeCacheManager): Increase cache lifetime to improve sync performance
* perf: speed up getSubFolders for deleted folders
* perf: speed up initial page load
* Fix(Bookmark): Display URL if title is empty
* fix(Authorizer): Don't throw HTTP 500 on malformed credentials
* fix(l10n): word typo
* fix: Fix backups

## [14.0.2] - 2024-04-29

### New

* Add support for Nextcloud 29


## [14.0.0] - 2024-04-28

### Breaking changes
* Drop support for Nextclod < 28

### New
* enh(search): Always search in current folder + Allow searching globally with one click
* enh(BookmarksList): Allow searching for folders
* enh(TreeFolder): Show bookmarks count in Folders overview
* enh(BookmarksList): Wrap bookmarkslist__description in NcNoteCard

### Changes
* overhaul settings: Use settings modal
* migrate(nc-vue): Upgrade to v8.x
* feat(disable archive): Allow disabling archive functionality

### Fixed
* fix(IBookmarkPreviewer): No more on-demand loading
* fix(VirtualScroll): Try to make it smoother
* fix(Controls): Don't show folder when searching in root
* fix(ItemSkeleton): Make it more beautiful
* fix(Controls): Correct title of rss feed button
* fix(Search): Increase width of search field
* fix(SharedFolderIcon)
* fix(WebViewController): Fix public link
* fix(CustomPickerElement): Remove heading
* fix(TreeFolder): Don't set childrenShown if there's no child folders
* fix(Bookmark): Fix fallback icon
* fix(Navigation): Always show counters even if 0
* fix(ui): Scale down icons to 20px
* fix(SettingsController): NoAdminRequired
* refactor(settings): Create a UserSettingsService and load all settings via initial state
* fix(Controls): Debounce new search bar
* fix(BookmarkMapper#_selectTags): Always return a string for tags column
* fix(TreeMapper#getChildren): Add more aggressive per-layer caching
* fix(BookmarkWithTagsAndParent): Avoid requerying tags if a bookmark has no tags
* fix(Authorizer): Don't run login again if there is already a session based on basic auth
* Fix(l10n): Update translations from Transifex

## [13.1.3] - 2023-12-18

### Fixes

* fix(Authorizer)

## [13.1.2] - 2023-12-14

### Fixed

* fix(Controls): Always show search bar
* fix(SearchProvider): Handle url being null
* fix(remove cors annotation) Fix user getting logged out on public links
* fix(WhatsnewModal check) Only show for major or minor version bumps
* fix(service worker)
* fix: drop collaboration resources integration to be compatible with nc 28
* Fix(l10n): Update translations from Transifex

## [13.1.1] - 2023-08-20

### Fixed

- Fix children endpoint

## [13.1.0] - 2023-08-17

### New
 - Add a changelog-style whatsnew modal
 - Allow specifying default folder in bookmarklet URL
 - Allow creating javascript and file links
 - Create WRITE permission to signify the permission to edit children
 - Folder.vue: Allow editing (direct) share roots even if they're from a non-writable share

### Fixed
 - UI: Streamline initial load
 - Fix Bookmarklet UI
 - Fix UI glitch in bookmark-content Marcel Klehr Yesterday 16:52
 - Update @nextcloud/vue
 - Fix flow.js script name
 - UI: Always align shared icon with folder icon
 - Backend: Avoid Share loops
 - UI: Rollback moving folders after failure
 - propagate permissions across multi-share boundaries 
 - fix(TreeMapper#getParentOf): Select correct type
 - Check for empty arrays before accessing them
 - BookmarksParser: Distinguish between dates in ms and s
 - fix(CrawlService): Catch whatever Readability might throw at us
 - fix(composer): Don't use "classmap-authoritative"
 - Use the color-primary-element* variables
 - Fix(l10n): 🔠 Update translations from Transifex

## [13.0.1] - 2023-03-21

### Fixed
 - fix(CustomPickerElement): Autofocus
 - fix(ReferenceProvider): Fix usage of preg_match :see_no_evil:

## [13.0.0] - 2023-03-12

### Changed
- Drop support for nc 25

### New
- Implement a reference provider and a front-end widget for bookmarks

## [12.1.0] - 2023-03-12

### New
- TreeFolder: Add more indentation layers
- UX: Display loading skeletons only after 350ms

### Fixed
- chore(deps): Update dependencies
- fix(ui): Use display names instead of ids
- fix(CrawlService): Correct MAX_BODY_LENGTH from 90KB to 90 MB
- fix(backups): refactor
- fix(LinkExplorer): Replace phpUri with Rowbot\URL
- Use built-in browser clipboard API
- Fix(l10n): 🔠 Update translations from Transifex

## [12.0.0] - 2022-12-13
### Breaking changes
 - Drop support for nc < v25
 - Remove projects support

### New
 - Implement sorting by URL
 - Fix archived files UX
 - Fix bookmarkslist getting emptied when folderoverview is hidden/shown
 - Make search available on public links
 - Upgrade to new NC25 design

### Fixed
 - Remove ExtractFromNotesJob
 - Fix WorkflowEngine integration
 - ItemSkeleton: Fix background color on dark theme
 - Update readability.php
 - More translations

## [11.0.4] - 2022-10-10

### Fixed
 - LinkExplorer: Fix to provide privacy-by-default

## [11.0.3] - 2022-09-24

### Fixed

 - Support Nextcloud 25
 - ExtractFromNotesJob: decrease frequency to releave performance impact

## [11.0.2] - 2022-09-19

### Fixed
 - FolderOverview: Make scrollable
 - Fix VirtualScroll component

## [11.0.1] - 2022-07-28

### Fixed

- Fixed Background job error "Trying to access array offset on value of type int"

## [11.0.0] - 2022-07-27

### Changed

- BookmarkMapper#findAll: Implement recursive query

### Breaking changes
- Breaks compatibility with MySQL 5.7, MySQL 8 is now required

### New

- Implement virtual scrolling
- First load time improvement: Load settings using initial state
- Bookmark: Show globe symbol while loading screenshot

### Fixed
- BgJob: Do not check notes if user has no bookmarks
- UI: Better sizing of Bookmark fallback background
- UI: Fix Item alignment in list view

## [10.5.1] - 2022-06-24

### Fixed

- Reverted "BookmarkMapper#findAll: Implement recursive query" to keep compatibility with MySQL 5.7

## [10.5.0] - 2022-06-22

### New
- Add ClearPreviews command
- UI: Implement folder tree in AppContentList

### Fixed
- UI: Add "K" to bookmarks count
- CrawlService: Make sure archived files always have a file ext
- Fix BackupJob check
- BookmarkMapper#findAll: Implement recursive query. Should fix some performance problems
- BookmarksList: Do not show FirstRun view when on public link
- Fix COPY_SELECTION to not remove bookmark
- FirstRun view: Fix button style

## [10.4.0] - 2022-06-08

### New
 - "Copy link" menu option for bookmarks

### Fixed
 - ExtractFomNotes: Change warning to debug log message
 - Disable Backup by default
 - Navigation: Display large numbers as "10.2K"
 - Sync performance: BookmarkService#create: Don't crawl new bookmarks directly but add IndividualCrawlJob
 - New translations from from transifex
 - Update dependencies

## [10.3.1] - 2022-04-19

### Fixed

 - Fix backup user setting
 - Fix localized backup file path

## [10.3.0] - 2022-04-12

### New

 - Allow disabling backups
 - Support Nextcloud 24

### Fixed

- Explicitly do not support Oracle

## [10.2.1] - 2022-03-26

### Fixed

- Backups: Leave other files untouched

## [10.2.0] - 2022-03-25

### New

- Implement bookmarks backup to Files
- Feature: Show and count duplicates
- Allow linking bookmarks and bookmark folders to projects
- Automatically link bookmarks to notes that contain the URL
- Search: Use AND conjunction
- LockManager: Timeout locks automatically
- UX: Directly crawl bookmark upon creation
- Previewers: Set log level to debug
- User settings: Remove 'clear all data'
- UX: Add explainers for SHARED_FOLDERS and UNAVAILABLE routes
- SidebarBookmark: Polish folder details
- SidebarBookmark: Allow clicking link

### Fixed
- FoldersController#getFolders: Fix parent_folder values of root-level folders
- Fix SHARED_FOLDERS view

## [10.1.0] - 2022-03-01

### New
- Integrate with Nextcloud Talk: Allow bookmarking mentioned links Marcel Klehr Today 16:53
- Add FirstRun view
- Implement Shared folders filter
- Remember whether user has seen scraping settings notification
- Support right click on items
- Add option for setting Pageres ENV vars

### Fixed
- AdminSettings: Use initialState to load settings
- BookmarkController: Remove hierarchy limit for public links
- UX: Don't open details of newly created bookmark
- Improve folder moving UX
- Improve loading UX
- Improve drag and drop UX
- UI: Polish bookmarks description in overview
- Update marcelklehr/link-preview

## [10.0.3] - 2021-12-04

### Fixed
 - App info: Mark as compatible with nc 23
 - Fix getPermissionsOfFolder: Use correct share
 - Fix Bookmark creation
 - Update @nextcloud/vue
 - UX: Highlight possible drop targets before hovering over them
 - Fix UX: Only display back button when viewing a folder
 - Fix "sharing folder doesn't display contents"

## [10.0.2] - 2021-10-18

### Fixed
- UX: Improve bookmarked files feature and increase CrawlJob frequency

## [10.0.1] - 2021-10-06

### Fixed
 - Drop symfony dependency
 - Cap description content at 1024 chars
 - API: Properly return 401 if no auth header was sent
 - API: Fix CSRF vulnerability

## [10.0.0] - 2021-09-21

### New
- Add "Add to folders" action for bookmarks
- API: Implement locking

### Fixed
- Update browserlist
- Update dependencies
- BookmarkService: Don't update bookmark if there have been no changes
- Disable drag-and-drop when renaming item 
- SidebarBookmark: Show which folders a bookmark belongs to
- Fix: Don't hide search behind bookmark details
- Major version bump to v10 to fix release tags in accordance with semantic versioning

## [4.4.1] - 2021-08-22

### Fixed
- Accommodate small sort buffer sizes in mysql


## [4.4.0] - 2021-07-26

### New
- Use webpack code splitting for faster loading times
- bookmark endpoint: Improve performance by introducing supporting query
- DB: Add new composite index for bookmarks_shared_folders (performance optimzation)
- Switch to npm7 and use latests global configs

### Fixed
 - NoBookmarks view: Don't display import and sync buttons in public view
 - Fix: Adding same bookmark second time removes preexisting tags
 - Fix input.focus error
 - Fix hash endpoint: Allow hashing with tags

## [4.3.0] - 2021-07-18

### New
- Support Nextcloud 22
- New screenshots

### Fixed
- Fix OrphanedTreeItemsRepairStep
- Fix multiselect in sidebar (see #1554)
- Fix uncaught UrlParseErrors (see #1598)
- API: Permission errors now have status 403 instead of 405 (see #1602)
- Add clickcount db index (see #1588)

## [4.2.2] - 2021-06-09

### Fixed
 - Fix OrphanedTreeItemsRepairStep
 - Switch out readability.php (#1563)
 - Fix BulkEditing covering MoveDialog
 - Add description to "Install on home screen"
 - UX: Navigation: re-add Search tags menu item
 - UI: Performance: Don't load children order unnecessarily
 - UX: Improve initial load time
 - Fix tag filter query performance issue

## [4.2.1] - 2021-05-16

### Fixed
 - CrawlService: Don't log server errors
 - Fix Public link view
 - Fix Authorizer
 - Fix folder title in controls
 - Fix bookmarklet

## [4.2.0] - 2021-05-10

### New 
 - UX: Implement drag and drop for moving items
 - UI: Add visual indicators for sharee privileges
 - UX: Add note for admins if scraping is disabled
 - UX: Allow dragging bookmarks onto tags
 - UX: Allow creating new tags in main menu (optimistically)
 - UX: Optimistically add bookmark to list when creating
 - UI: Move Sort order from settings to Controls
 - Dashboard: Add 'frequent bookmarks' widget
 - Implement full-text archiving of HTML pages

### Fixed
- Updated translations from transifex :hearts:
- Authorizer: Check token first, then check user credentials
- BookmarkService: Keep tags when moving across share boundaries
- Migration: Ensure all tables have primary key
- CrawlJob: Set some time and size limits for archiving content
- Fix: Recover from multiple root folders
- Fix OrphanedTreeItemsRepairStep: Reinsert lost bookmarks instead of deleting them
- UI: Display archived content in full-page overlay
- UI: Change "Archived" to "Files" menu entry (+new icon)
- UX: Refactor and improve folder picker
- UX: Bookmarklet: Adjust window height
- UI: Remove frustrating paddings from Item.vue
- UI: Don't cover bookmark titles when there's many tags
- UX: Don't display spinners for the first 500ms
- UX: Improve speed of moving items
- UX: Make sharing visible in folder menu
- UX: Move BulkEditing to the left
- UI: Fix getPermissionsForFolder

## [4.1.0] - 2021-02-19

### New 

- Bulk tagging
- Nextcloud 21 support

### Fixed
- Update translations
- Run GC on cache dir and add CACHDIR.TAG
- API: Fix renaming and deleting tags
- TreeCacheManager: Invalidate sharees when invalidating a folder
- Fix bookmark notes
- Enable service worker

## [4.0.8] - 2021-01-26

### Fixed

- Fix OrphanedTreeItems Repair step

## [4.0.7] - 2021-01-26

### Fixed
- TreeMapper: Cache children & childrenOrder
- TreeMapper: Fix Folder deletion; Don't leak deleted bookmarks
- Dependency updates
- UI: Use unified search input
- Performance: More caching
- NoBookmarks view: Link to floccus
- Translation updates

## [4.0.6] - 2021-01-19

### Fixed

- Fix autofocus in Create{Bookmark|Folder}
- Fix moving folders across share borders
- Fix creating a new folder, when no folders exist yet
- Fix: Remove a tag from all displayed bookmarks when deleting it from the app
- Fix Bookmark Notes: Don't loose line breaks
- API: Report success if item to delete is already deleted
- Fix ScreenshotMachineBookmarkPreviewer
- Fix: Actually Count bookmark clicks
- Fix: pageres bookmark previewer
- ViewAdmin: add link to github of pageres
- Update dependencies

## [4.0.5] - 2020-11-04

### Fixed

- Fix clean up repair step

## [4.0.4] - 2020-11-03

### Fixed

- Fix API bookmarks queries

## [4.0.3] - 2020-11-03

### Fixed
 - Restore app compliance
 - Fix specific API Bookmark queries
 - Build: Don't include dev dependencies in build
 - Update dependencies

## [4.0.2] - 2020-10-28

### Fixed

 - Extend OrphanedTreeItems repair step
 - Fix TreeMapper#deleteEntry: Properly remove orphaned bookmarks

## [4.0.1] - 2020-10-27

### Fixed
 - Fix bookmark hashing
 - Update PHP dependencies
 - Update JS dependencies
 - Apply minor static analysis fixes

## [4.0.0] - 2020-10-22

### New

- Add Dashboard widget
- Integrate with unified search
- Add bookmarks-only search UI

### Fixed

- Don't add empty tag to bookmarks without tags
- Improve performance of bulk actions
- Improve perforamnce of API

## [3.4.4] - 2020-10-18

### Fixed

- Fix: Don't hide nested shared bookmarks

## [3.4.3] - 2020-10-09

### Fixed

- Fix tag duplication

## [3.4.2] - 2020-10-08

### Fixed
 - UI: Fix typo in rename button
 - Speed up import and mass deletion
 - UI: Improve waiting experience
 - Fix BookmarkMapper#mapRowToEntity
 - Fix export

## [3.4.1] - 2020-09-30

### Fixed
- Remove legacy files

## [3.4.0] - 2020-09-29

### Fixed
- UI: Fix navigation
- BookmarkController: counting bookmarks should only require READ perms
- Make tags clickable again
- UI: Make whole folder card clickable
- UI: Implement "Select all" instead of "select all visible"
- Make export and re-import work as expected
- UI: Make sure the newly created bookmark is visible in the list
- UI: Fix default icon for add-actions
- Speedup bookmark queries (#1182)
- UI: Fix folder route on direct entry
- UI: Fix settings styles
- UI: Don't show tags control in navigation if there are no tags
- UI: Fix bookmark/folder creation
- UI: Close actions after clicking
- UI: BookmarksList: Make cards fixed width and add padding
- UI: Automatically add currently filtered tags to new bookmarks
- UI: Only show checkboxes when selection mode is activated
- BookmarkController: Show default image for previews
- UI: Improve no-bookmarks view
- Fix don't allow creating folder loops
- UI: Allow editing bookmark title in sidebar
- Fix: Improve DB query performance
- Fix: Fix dependency hell with symfony v5 

### New
- Implement Pageres cli previewer
- Add custom sort option
- Refactor previews system and add support for new providers
- Try to fix favicon error
- Implement Flow integration (#1138)
- UI: Fix folder creation
- Implement dead link detection
- Use material design icons
- API: Add count endpoint publicly
- Implement file archiver (#1167)

## [3.4.0-beta.1] - 2020-08-27

### Fixed
- UI: Fix navigation
- BookmarkController: counting bookmarks should only require READ perms
- Make tags clickable again
- UI: Make whole folder card clickable
- UI: Implement "Select all" instead of "select all visible"
- Make export and re-import work as expected
- UI: Make sure the newly created bookmark is visible in the list
- UI: Fix default icon for add-actions
- Speedup bookmark queries (#1182)
- UI: Fix folder route on direct entry
- UI: Fix settings styles
- UI: Don't show tags control in navigation if there are no tags
- UI: Fix bookmark/folder creation
- UI: Close actions after clicking
- UI: BookmarksList: Make cards fixed width and add padding
- UI: Automatically add currently filtered tags to new bookmarks
- UI: Only show checkboxes when selection mode is activated
- BookmarkController: Show default image for previews
- UI: Improve no-bookmarks view

### New
- Implement Pageres cli previewer
- Add custom sort option
- Refactor previews system and add support for new providers
- Try to fix favicon error
- Implement Flow integration (#1138)
- UI: Fix folder creation
- Implement dead link detection
- Use material design icons
- API: Add count endpoint publicly
- Implement file archiver (#1167)

## [3.3.4] - 2020-08-16

### Fixed
- Fix folder endpoint
- Fix undefined index error

## [3.3.3] - 2020-07-30

### Fixed

- Fix repair step
- Fix ActivityPublisher when no user is authenticated

## [3.3.2] - 2020-07-28

### Fixed

- UI: Fix Navigation
- Fix tag count
- Fix deduplication repair step

## [3.3.1] - 2020-07-23

### Fixed

- Fix info.xml

## [3.3.0] - 2020-07-23

### New
- Implement Activity app integration

### Fixed
- Add repair step for duplicate shared folders
- Fix untagged search (on postgres)
- UI: Fix rename Tag
- Build: Don't ship source maps
- Repair steps: Add debug output
- Bookmarks: Additionally always sort by ID to make ordering stable
- Fix deletion of bookmarks that are not in tree
- Update dependencies

## [3.2.5] - 2020-07-17

### Fixed
 - UI: Open URLs in new tabs
 - UI: Fix infinite scroll
 - Update documentation of API responses
 - Fix changing bookmark's folders
 - Fix tags API

## [3.2.4] - 2020-06-29

### Fixed
- API: Fix PUT bookmark requests

## [3.2.2] - 2020-06-28

### New
- In grid view the entire bookmark behaves like a link
- Custom sort option setting for displaying the original order.
E.g. in case of a browser sync as source.

### Fixed
- Horizontal breadcrumb text alignment
- Checkbox size in grid view
- Menu toggle position
- Deletion of tags
- API: Don't send folder IDs that the client cannot access

## [3.2.1] - 2020-06-16

### Fixed
Fix service worker

## [3.2.0] - 2020-06-14

### Fixed
Bookmarklet: Fix description input

### New
Installable as Progressive web app

## [3.1.1] - 2020-06-03

## Fixed

- Fixed getChildren SQL query

## [3.1.0] - 2020-06-03

## New
1d0d91689cdab27f26c168d7ffb2201ac69c2ef9 Bookmarklet: Add folder picker
906de9ce56792638ff03ba10f6afc964acf999bf Add option to copy public link as RSS feed
e86437c93bd9e733731bb2a650c9dd4ffa216639 Add option to open selection in new tabs
45da16e9d795c1aca474b88b1141e1ae7d440a5d Allow bulk selection actions for folders

## Changed
e7eb042d7c5f14feba284884689327822ba4fa37 Import into currently open folder

## Fixed
622103eb0949eb347335b73f97ae71ab5e33fef0 Fix tag filtering
e391da95c3049cf5bd637235075c8df8d82282f4 Filter by tag: Include tags from shared folders
04cb5e01c63f0898e16ae34be4d925e00a96e820 Fix shared folder icon
d17c7cb96d36b1e6a80289864f3ada4a7eff8756 Fix shares
ba523ff9bffc90bfa554acd2c675a93067844cde Fix: Display a loading indicator while moving items
88d32dd23bb9cd7cb72d3c6d44c820cd3d7a3cf5 Fix Bookmark deletion by selection: Remove only bookmark entry not whole bookmark
abe7eaf039a52074477b336eb6bd49b3698a41f8 Fix search when sorting alphabetically
fdbe69ebc888132a4bb88f834c25d5f492b0fa10 Docs: Document /export API endpoint

## [3.0.13] - 2020-05-11

## Changed
- Fix export
- Really drop libgmp dependency

## [3.0.12] - 2020-05-08

## Changed
 - Fix bookmark deletion
 - Fix v3 migration: Correctly account for duplicates in the root folder

## [3.0.11] - 2020-05-07

## Changed
 - Fix Auth error for users of certain setups


## [3.0.10] - 2020-05-05

## Changed
 - Fix Auth error when creating bookmarks without selecting a folder


## [3.0.9] - 2020-05-05

## Changed
 - Change column type for tree index to allow **alot** of bookmarks.

## [3.0.8] - 2020-05-04

## Changed
 - Fix Authorization for newBookmark API endpoint
 - Try to fix Authentication for some setups
 - Remove orphaned items from bookmarks tree
 - Remove superfluous shared folders from bookmarks tree
 - Fix share creation to avoid sharing a folder with oneself

## [3.0.7] - 2020-05-03

## Changed
 - Fix delete-all-bookmarks API endpoint

## [3.0.6] - 2020-05-03

## Changed
 - Fix bookmarks by folders API endpoint
 - Fix v3 db migration to account for db inconsistencies
 - Fix client-side usage of 'finally'
 - Fix getFolders API endpoint

## [3.0.5] - 2020-04-30

## Changed
 - Fix: Remove uninstall repair step which would also run on disabling the app
- Fix bookmarklet

## [3.0.4] - 2020-04-30

## Changed
 - Fix: Delete shares when deleting a shared folder
 
## [3.0.3] - 2020-04-30

## Changed
 - Fix hash endpoint

## [3.0.2] - 2020-04-30

## Changed
 - Fix sharee search for installations without mod_rewrite

## [3.0.1] - 2020-04-30

## Changed
 - Fix hash endpoint

## [3.0.0] - 2020-04-30

### Changed
 - UI: Selection: Implement "select all" and "cancel selection"
 - Better document how the screeenly api url looks like
 - Move UI: Automatically open current folder
 - Implement bookmark counting endpoints and UI indicators
 - Fix bookmark creation: Assign folders correctly
 - Drop tables on uninstall
 - Fix import and export
 - Allow editing URLs
 - Implement a full children endpoint
 - Breadcrumbs: -AddBookmark button +Add Folder/Bookmark dropdown
 - Don't enable scraping by default
 - Drop libgmp dependency
 - Major backend refactoring

### Added
 - Implement hash caching
 - Implement private sharing and public links of folders


## [2.3.4] - 2019-12-12

### Changed
- Fix Bookmark creation outside folders
- Fix browser compatibility

## [2.3.3]

### Changed
- Fix Creating bookmarks in the current folder

## [2.3.2]

### Changed
- Fix webpack build
- Fix Creating bookmarks in the current folder
- Fix Make folder icons clickable
- Fix translations

## [2.3.1]

### Changed
- FIX: Load tags on app init

## v2.3.0

 - NEW: UI: Implement bulk editing
 - NEW: translations from transifex
 - FIX: overflow on bookmarks list
 - FIX: sorting
 - FIX: Fix humanized duration: Use stem language only

## v2.2.0

- NEW: Use routes history mode instead of hash URLs
- FIX: Sort folders alphabetically
- FIX: Allow canceling page fetches
- FIX: Import
- UI: Fixgrid view, descriptions in list view & bread crumbs

## v2.1.1

- FIX: Fix build script

## v2.1.0

- NEW: Rewrite UI
- NEW: Allow limiting the number of bookmarks per user
- NEW: Allow disabling web requests to bookmarked web pages

## v2.0.3

- NEW: Properly specify dependencies in app manifest (allows conditional support for nc 15 again)

Supported are NC 15 and 16, provided you are using PHP v7.1 and have gmp, intl and mbstring php extensions installed

## v2.0.2

- NEW: Drop support for nextcloud 15

## v2.0.1

- fix composer lock file

## v2.0.0

- NEW: gmp, intl, mbstring are now required
- NEW: Drop support for nextcloud 14 and php 7.0
- FIX: Switch URL normalizer to adhere strictly to WHATWG URL spec

## v1.1.2

- Revert breaking changes of v1.0.8

## v1.1.1

- FIX import from web UI

## v1.1.0

- NEW translations
- NEW: API endpoint to import into a specific folder

## v1.0.8

- NEW: gmp, intl, mbstring are now required
- NEW translations
- FIX: Switch URL normalizer to adhere strictly to WHATWG URL spec
- FIX: Update dependencies
- FIX: Run previews job in small batches instead of all at once

## v1.0.6

- FIX: Set timeout for submitting tags
- NEW: Create favicon
- FIX: Bump interactjs from 1.3.4 to 1.4.10
- FIX: UrlNormalizer: Don't enforce normalization
- NEW: updated from transifex
- FIX: Truncate tags in grid view
- FIX: Remove background colors
- FIX folder collapse css
- FIX: Speed up findBookmarks SQL query

[10.2.1]: https://github.com/nextcloud/bookmarks/compare/v10.2.0...v10.2.1
[10.2.0]: https://github.com/nextcloud/bookmarks/compare/v10.1.0...v10.2.0
[10.1.0]: https://github.com/nextcloud/bookmarks/compare/v10.0.3...v10.1.0
[10.0.3]: https://github.com/nextcloud/bookmarks/compare/v10.0.2...v10.0.3
[10.0.2]: https://github.com/nextcloud/bookmarks/compare/v10.0.1...v10.0.2
[10.0.1]: https://github.com/nextcloud/bookmarks/compare/v10.0.0...v10.0.1
[10.0.0]: https://github.com/nextcloud/bookmarks/compare/v4.4.1...v10.0.0
[4.4.1]: https://github.com/nextcloud/bookmarks/compare/v4.4.0...v4.4.1
[4.4.0]: https://github.com/nextcloud/bookmarks/compare/v4.3.0...v4.4.0
[4.3.0]: https://github.com/nextcloud/bookmarks/compare/v4.2.2...v4.3.0
[4.2.2]: https://github.com/nextcloud/bookmarks/compare/v4.2.1...v4.2.2
[4.2.1]: https://github.com/nextcloud/bookmarks/compare/v4.2.0...v4.2.1
[4.2.0]: https://github.com/nextcloud/bookmarks/compare/v4.1.0...v4.2.0
[4.0.8]: https://github.com/nextcloud/bookmarks/compare/v4.0.7...v4.0.8
[4.0.7]: https://github.com/nextcloud/bookmarks/compare/v4.0.6...v4.0.7
[4.0.6]: https://github.com/nextcloud/bookmarks/compare/v4.0.5...v4.0.6
[4.0.5]: https://github.com/nextcloud/bookmarks/compare/v4.0.4...v4.0.5
[4.0.4]: https://github.com/nextcloud/bookmarks/compare/v4.0.3...v4.0.4
[4.0.3]: https://github.com/nextcloud/bookmarks/compare/v4.0.2...v4.0.3
[4.0.2]: https://github.com/nextcloud/bookmarks/compare/v4.0.1...v4.0.2
[4.0.1]: https://github.com/nextcloud/bookmarks/compare/v4.0.0...v4.0.1
[4.0.0]: https://github.com/nextcloud/bookmarks/compare/v3.4.4...v4.0.0
[3.4.4]: https://github.com/nextcloud/bookmarks/compare/v3.4.3...v3.4.4
[3.4.3]: https://github.com/nextcloud/bookmarks/compare/v3.4.2...v3.4.3
[3.4.2]: https://github.com/nextcloud/bookmarks/compare/v3.4.1...v3.4.2
[3.4.1]: https://github.com/nextcloud/bookmarks/compare/v3.4.0...v3.4.1
[3.4.0]: https://github.com/nextcloud/bookmarks/compare/v3.3.4...v3.4.0
[3.4.0-beta.1]: https://github.com/nextcloud/bookmarks/compare/v3.3.4...v3.4.0-beta.1
[3.3.3]: https://github.com/nextcloud/bookmarks/compare/v3.3.2...v3.3.3
[3.3.2]: https://github.com/nextcloud/bookmarks/compare/v3.3.1...v3.3.2
[3.3.1]: https://github.com/nextcloud/bookmarks/compare/v3.3.0...v3.3.1
[3.3.0]: https://github.com/nextcloud/bookmarks/compare/v3.2.5...v3.3.0
[3.2.5]: https://github.com/nextcloud/bookmarks/compare/v3.2.4...v3.2.5
[3.2.4]: https://github.com/nextcloud/bookmarks/compare/v3.2.2...v3.2.4
[3.2.2]: https://github.com/nextcloud/bookmarks/compare/v3.2.1...v3.2.2
[3.2.1]: https://github.com/nextcloud/bookmarks/compare/v3.2.0...v3.2.1
[3.2.0]: https://github.com/nextcloud/bookmarks/compare/v3.1.1...v3.2.0
[3.1.1]: https://github.com/nextcloud/bookmarks/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/nextcloud/bookmarks/compare/v3.0.13...v3.1.0
[3.0.13]: https://github.com/nextcloud/bookmarks/compare/v3.0.12...v3.0.13
[3.0.12]: https://github.com/nextcloud/bookmarks/compare/v3.0.11...v3.0.12
[3.0.11]: https://github.com/nextcloud/bookmarks/compare/v3.0.10...v3.0.11
[3.0.10]: https://github.com/nextcloud/bookmarks/compare/v3.0.9...v3.0.10
[3.0.9]: https://github.com/nextcloud/bookmarks/compare/v3.0.8...v3.0.9
[3.0.8]: https://github.com/nextcloud/bookmarks/compare/v3.0.7...v3.0.8
[3.0.7]: https://github.com/nextcloud/bookmarks/compare/v3.0.6...v3.0.7
[3.0.6]: https://github.com/nextcloud/bookmarks/compare/v3.0.5...v3.0.6
[3.0.5]: https://github.com/nextcloud/bookmarks/compare/v3.0.4...v3.0.5
[3.0.4]: https://github.com/nextcloud/bookmarks/compare/v3.0.3...v3.0.4
[3.0.3]: https://github.com/nextcloud/bookmarks/compare/v3.0.2...v3.0.3
[3.0.2]: https://github.com/nextcloud/bookmarks/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/nextcloud/bookmarks/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/nextcloud/bookmarks/compare/v2.3.4...v3.0.0
[2.3.4]: https://github.com/nextcloud/bookmarks/compare/v2.3.3...v2.3.4
[2.3.3]: https://github.com/nextcloud/bookmarks/compare/v2.3.2...v2.3.3
[2.3.2]: https://github.com/nextcloud/bookmarks/compare/v2.3.1...v2.3.2
[2.3.1]: https://github.com/nextcloud/bookmarks/compare/v2.3.0...v2.3.1
[2.3.0]: https://github.com/nextcloud/bookmarks/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/nextcloud/bookmarks/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/nextcloud/bookmarks/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/nextcloud/bookmarks/compare/v2.0.3...v2.1.0
[2.0.3]: https://github.com/nextcloud/bookmarks/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/nextcloud/bookmarks/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/nextcloud/bookmarks/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/nextcloud/bookmarks/compare/v1.1.2...v2.0.0
[1.1.2]: https://github.com/nextcloud/bookmarks/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/nextcloud/bookmarks/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/nextcloud/bookmarks/compare/v1.0.8...v1.1.0
[1.0.8]: https://github.com/nextcloud/bookmarks/compare/v1.0.6...v1.0.8
[1.0.6]: https://github.com/nextcloud/bookmarks/compare/v1.0.5...v1.0.6

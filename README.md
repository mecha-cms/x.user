User Extension for [Mecha](https://github.com/mecha-cms/mecha)
==============================================================

![User](/user/lot/asset/index.png)

Release Notes
-------------

### 2.0.0

 - [ ] Updated for Mecha 3.0.0.
 - [x] Added `choke` option to throttle user requests to the log-in/out page.
 - [x] Allowed integer value for `Is::user()` to check if current user has a specific status (#2)

### 1.13.0

 - Added `user-form` and `user-form-tasks` hooks.
 - Added external CSS file to center the user form.
 - [@mecha-cms/mecha#96](https://github.com/mecha-cms/mecha/issues/96)

### 1.12.1

 - Improved the log-in system to allow the same user to be logged-in on multiple devices at once.

### 1.12.0

 - Updated for Mecha 2.5.0.

### 1.11.4

 - Added `target` attribute to the log-in form.

### 1.11.3

 - Fix broken user profile page that always redirect to the log-in page.

### 1.11.2

 - Added log-in attempts counter.
 - Added routes to the user profile page.
 - Changed `$` property to `author` to store the human-friendly user name.
 - Default log-in and log-out system is now enabled by this extension.
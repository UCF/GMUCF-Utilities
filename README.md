# GMUCF Utilities #

Feature and utility plugin for the GMUCF WordPress site.


## Description ##

Includes features and functionality for the GMUCF WordPress site that are otherwise not suitable for inclusion in the [GMUCF Theme](https://github.com/UCF/UCF-GMUCF-Theme).


## Installation Requirements ##

This plugin is developed and tested against WordPress 5.3+ and PHP 7.3+.

### Theme ###
This plugin is intended for use only with the [GMUCF Theme](https://github.com/UCF/UCF-GMUCF-Theme).

### Required plugins ###
These plugins _must_ be activated for GMUCF-Utilities to function properly.

* [Advanced Custom Fields PRO](https://advancedcustomfields.com/)
* [UCF Email Editor Plugin](https://github.com/UCF/UCF-Email-Editor-Plugin)

### Recommended Plugins ###
These plugins are not technically required for this plugin to function normally, but are generally expected to be installed for full functionality of the GMUCF site:
* [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/) (for Email CPT instant sends)


## Changelog ##

### 1.0.4 ###
Enhancements:
* Added composer file.

### 1.0.3 ###
Enhancements:
- Modified email "preview" nomenclature throughout the plugin to more clearly define them as "tests"

### 1.0.2 ###
Bug Fixes:
- Made sure the email preview button only shows up for emails.
- Fixed/cleaned up some instant send related logic.

Enhancements:
- Updated preview list retrieval logic to ensure lists of preview recipients are matched and reduced using case-insensitive string matching.
- Implemented a base list of email preview recipients to send all previews to.

### 1.0.1 ###
Bug Fixes:
- Updated `instant_send()` to ensure that an email's requester gets included in the recipient list used for sending previews.
- Instant send recipient lists are now filtered to ensure email addresses aren't repeated, and that no empty strings get passed along as recipient addresses.

### 1.0.0 ###
* Initial release


## Upgrade Notice ##

n/a


## Development ##

Note that compiled, minified css and js files are included within the repo.  Changes to these files should be tracked via git (so that users installing the plugin using traditional installation methods will have a working plugin out-of-the-box.)

[Enabling debug mode](https://codex.wordpress.org/Debugging_in_WordPress) in your `wp-config.php` file is recommended during development to help catch warnings and bugs.

### Requirements ###
* node
* gulp-cli

### Instructions ###
1. Clone the GMUCF-Utilities repo into your local development environment, within your WordPress installation's `plugins/` directory: `git clone https://github.com/UCF/GMUCF-Utilities.git`
2. `cd` into the new GMUCF-Utilities directory, and run `npm install` to install required packages for development into `node_modules/` within the repo
3. Optional: If you'd like to enable [BrowserSync](https://browsersync.io) for local development, or make other changes to this project's default gulp configuration, copy `gulp-config.template.json`, make any desired changes, and save as `gulp-config.json`.

    To enable BrowserSync, set `sync` to `true` and assign `syncTarget` the base URL of a site on your local WordPress instance that will use this plugin, such as `http://localhost/wordpress/my-site/`.  Your `syncTarget` value will vary depending on your local host setup.

    The full list of modifiable config values can be viewed in `gulpfile.js` (see `config` variable).
3. Run `gulp default` to process front-end assets.
4. If you haven't already done so, create a new WordPress site on your development environment to test this plugin against, and [install and activate all plugin dependencies](https://github.com/UCF/GMUCF-Utilities/wiki/Installation#installation-requirements).
5. Activate this plugin on your development WordPress site.
6. Run `gulp watch` to continuously watch changes to scss and js files.  If you enabled BrowserSync in `gulp-config.json`, it will also reload your browser when plugin files change.

### Other Notes ###
* This plugin's README.md file is automatically generated. Please only make modifications to the README.txt file, and make sure the `gulp readme` command has been run before committing README changes.

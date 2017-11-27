# Charitable — WordPress Donation Plugin
[![Plugin Version](https://img.shields.io/wordpress/plugin/v/charitable.svg?maxAge=2592000)](https://wordpress.org/plugins/charitable/) [![Total Downloads](https://img.shields.io/wordpress/plugin/dt/charitable.svg)](https://wordpress.org/plugins/charitable/) [![Plugin Rating](https://img.shields.io/wordpress/plugin/r/charitable.svg)](https://wordpress.org/support/plugin/charitable/reviews/) [![WordPress Compatibility](https://img.shields.io/wordpress/v/charitable.svg?maxAge=2592000)](https://wordpress.org/plugins/charitable/) [![Build Status](https://travis-ci.org/Charitable/Charitable.svg?branch=master)](https://travis-ci.org/Charitable/Charitable) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Charitable/Charitable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Charitable/Charitable/?branch=master) [![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)](https://github.com/Charitable/Charitable/blob/master/license.txt)

This is the GitHub repository for Charitable, the WordPress donation plugin that grows with you. Charitable helps you accept donations on your WordPress website, giving you complete control over their fundraising experience. Unlike other WordPress plugins, Charitable was created with a core focus on being extendable and feature-rich.

If you are not a developer or you want to find out more about Charitable's features and extensions, visit [wpcharitable.com](https://www.wpcharitable.com/).

## Installation

If you would like to test/develop with Charitable, you can clone it into your WordPress installation via the command line:

```
cd /path/to/wordpress/
cd wp-content/plugins
git clone git@github.com:Charitable/Charitable.git charitable
```

After that, you can activate Charitable via your WordPress dashboard or with WP CLI:

```
wp plugin activate charitable
```

**Note:** If you are not a developer, please install Charitable via your WordPress dashboard.

### Pre-populate pages, campaigns & donations

If you would like to pre-populate a test site site with pages, campaigns and donations, you can use the provided setup script.

```
bin/setup.sh
```

**Optional Parameters**

| Parameter               | Description                                                                                     |
| ----------------------- | ----------------------------------------------------------------------------------------------- |
| --campaigns=<campaigns> | The number of campaigns to be created. Defaults to `0`.                                         |
| --donations=<donations> | The number of donations to be created automatically. Defaults to `0`.                           |
| --allow-root            | Run the command as `root` user. This passes the `--allow-root` argument to the WP CLI commands. |

**Example**

This automatically sets up all pages with shortcodes, updates settings, then creates 10 campaigns and 500 donations.

```
bin/setup.sh --campaigns=10 --donations=500
```

### Key Branches

* `master` is the primary development branch. We do not recommend using this on production sites.
* `stable` is the latest stable branch, corresponding to the most recent public release.

Past releases can be checked out via `git checkout 1.5.0` (replace `1.5.0` with the release you would like to check out). View the full set of releases at [https://github.com/Charitable/Charitable/releases](https://github.com/Charitable/Charitable/releases)

## How to Report Bugs

If you find a bug you can report it [right here on GitHub](https://github.com/Charitable/Charitable/issues/new). To disclose a security issue, please [submit a private report](https://www.wpcharitable.com/support/).

## Support

This repository is specifically for developers; it is not a place to get general Charitable support. If you have anything you need help with with Charitable, please get in touch via [our Support page](https://www.wpcharitable.com/support/).

## Contributions

Patches, bug reports and other contributions are always welcome! 

Charitable has active translation projects in many languages, and we are always looking for new translation contributors and editors. [Read more](https://www.wpcharitable.com/documentation/translating-charitable/).

## Related Tools / Projects

- [Code snippets library](https://github.com/Charitable/library) - A collection of 130+ code snippets to customize Charitable.
- [Extension boilerplate](https://github.com/Charitable/charitable-extension-boilerplate/) - Rapidly scaffold a Charitable extension using a one-line Grunt command.

PixProof
========
**Easy Photo Proofing for Photographers**

## Description

WordPress photo gallery proofing plugin. Using special protected galleries you will allow your clients to examine and approve your photos.

**PixProof Gallery [Demo #1](http://bit.ly/1m3LmS0)** (use ‘demo’ as password)

It is a simple to use plugin that uses a Custom Post Type called Proof Galleries to help you manage your "clients" galleries separated from other post types you may have around (like regular galleries or projects).

These can be either public galleries (you will use the fact that the URL is not public and provide it only to your client) or password protected galleries (this is the recommended way) and you will provide the URL and the password to your individual clients (you can even make them private galleries - it is up to you).

You can read more about PixProof plugin on this article: [Proofing Photos with your Clients](https://pixelgrade.com/docs/timber/pages-and-content/proof-photos-clients/)


### Enabling Images Download

Go to Settings - PixProof - General Settings and check "Enable Images Download".

You can choose from two options:

* Manually (default) - you have to upload a ZIP archive to each gallery.
* Automatically - the client can download the selected images in a ZIP archive (automatically generated by the server).

### Compatible

PixProof should work with any theme as it uses standard WordPress hooks and filters.

Always use the [stable version](https://wordpress.org/plugins/pixproof) from wordpress.org

## Installation

Installing "PixProof" can be done either by searching for "PixProof" via the "Plugins → Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org.
2. Upload the ZIP file through the _Plugins → Add New → Upload_ screen in your WordPress dashboard.
3. Activate the plugin through the _Plugins_ menu in WordPress.
4. Head over to _Settings → PixProof_ and set it up, or go to _Proof Galleries_ and manage your galleries.

## Issues

If you identify any errors or have an idea for improving the plugin, please open an [issue](https://github.com/pixelgrade/pixproof/issues?stage=open). We're more than excited to see what the community thinks of this little plugin, and we welcome your input!

If Github is not your thing but you are passionate about PixProof and want to help us make it better, don't hesitate to [reach us](https://pixelgrade.com/contact/).

## Credits

* [CMB2](https://github.com/CMB2/CMB2) Metaboxes, custom fields library - License: GPLv2 or later
* [CMB2 Conditionals](https://github.com/jcchavezs/cmb2-conditionals/) plugin for CMB2 - License: GPLv2 or later

## Changelog

= 2.0.0 =
* This is a big rewrite of the plugin to make it easier to maintain and understand, also improved performance and future proofing compatibility.
* This update is **fully backward compatible** with previous versions.
* Tested with the latest WordPress version (5.2.3).
* Fixed minor issues.

= 1.2.4 =
* Added the `pixproof_filter_gallery_filename` filter
* Fixed some gallery filename issues.

= 1.2.3 =
* Improved strings translation.
* Fixed random order for images in gallery
* Fixed free access for archives created automatically(security fix)
* Fixed small warnings

= 1.2.2 =
* Improved strings translation. Thanks [David Perez](https://github.com/pixelgrade/pixproof/pull/17)
* Quit .po/.mo files for a general .pot one.

= 1.2.1 =
* Added the ability to disable the plugin style.
* Fixed some admin page style issues

= 1.2.0 =
* Added the ability to disable the download zip archives.
* Added the option to select the thumbnail size for individual gallery but also for all of them.
* Added the option to select the grid size
* Fixed the random order gallery option
* Fixed an issue where images are messed up after the plugin activation
* Small style improvements

= 1.1.1 =
* Fixed ZIP archive download on Windows
* Fixed Small bugs

= 1.1.0 =
* Added "Download Selected Images" (ZIP) option

= 1.0.7 =
* General style fixes
* Fix private galleries

= 1.0.5 =
* This is where all the magic started.

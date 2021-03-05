=== PixProof - Easy Photo Proofing for Photographers ===
Contributors: pixelgrade, vlad.olaru, babbardel
Tags: gallery, proofing, images, photography, proof, thumbnails, image, photos, picture, media, clients, photo album
Requires at least: 4.9.9
Tested up to: 5.7.0
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

PixProof allows you to have a protected, straight forward environment to discuss and proof photos from photo shootings.

== Description ==

PixProof is a custom WordPress plugin that is meant to **ease the way photographers interact with their clients**. It allows you to have a protected straight forward environment to discuss and proof photos from photo shootings, all in a interactive, AJAX based, interface.

**PixProof Gallery [Demo #1](http://bit.ly/1m3LmS0)** (use ‘demo’ as password)

It is a simple to use plugin that uses a Custom Post Type called Proof Galleries to help you manage your "clients" galleries separated from other post types you may have around (like regular galleries or projects).

These can be either public galleries (you will use the fact that the URL is not public and provide it only to your client) or password protected galleries (this is the recommended way) and you will provide the URL and the password to your individual clients (you can even make them private galleries - it is up to you).

You can read more about PixProof plugin on this article: [Proofing Photos with your Clients](https://pixelgrade.com/docs/timber/pages-and-content/proof-photos-clients/)

= Tested with the following WordPress themes: =

* [Timber](https://pixelgrade.com/themes/portfolio/timber/) _by Pixelgrade_
* [Border](https://pixelgrade.com/themes/portfolio/border/) _by Pixelgrade_
* [Lens](https://pixelgrade.com/themes/portfolio/lens/) _by Pixelgrade_

**Made with love by [Pixelgrade](https://pixelgrade.com/)**

== Contributing ==

The proposed value of **Open Source** is that by freely sharing the code with the community, others can use, improve and contribute back to it.

It's great if you're willing to use your skills, knowledge, and experience to help further refine this project with your own improvements. We really appreciate it and you're 💯 welcome to submit an issue or pull request on any topic.

=== How can you help? ===

* **Discovered an issue?** Please report it [here](https://github.com/pixelgrade/pixproof/issues/new "here").
* **Fixed a bug?** Send a [pull request](https://github.com/pixelgrade/pixproof/pulls "pull request").
* **Need a feature?** Propose it [here](https://github.com/pixelgrade/pixproof/issues/new "here").
* **Have you made something great?** [Share](https://github.com/pixelgrade/pixproof/issues/new "Share") it with us.

== Translations ==

You can translate PixProof on [__translate.wordpress.org__](https://translate.wordpress.org/projects/wp-plugins/pixproof).

== Credits ==

Unless otherwise specified, all the plugins files, scripts and images are licensed under GNU General Public License v2 or later.

The PixProof plugin bundles the following third-party resources:

* [CMB2](https://github.com/CMB2/CMB2) Metaboxes, custom fields library - License: GPLv2 or later
* [CMB2 Conditionals](https://github.com/jcchavezs/cmb2-conditionals/) plugin for CMB2 - License: GPLv2 or later

== Installation ==

Installing "PixProof" can be done either by searching for "PixProof" via the "Plugins → Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org.
2. Upload the ZIP file through the _Plugins → Add New → Upload_ screen in your WordPress dashboard.
3. Activate the plugin through the _Plugins_ menu in WordPress.
4. Head over to _Settings → PixProof_ and set it up, or go to _Proof Galleries_ and manage your galleries.

== Frequently Asked Questions ==

= Can I use the PixProof with my own theme? =

Yes! PixProof should work with any theme as it relies on standard WordPress hooks and functionality (galleries, comments, etc). Some custom CSS styling may be required to achieve a smooth integration with your site's overall design.

= Is PixProof free? =

Yes! PixProof's core features are free to use.

== Changelog ==

= 2.0.1 =
* Improve compatibility with WordPress 5.7.

= 2.0.0 =
* This update is a rewrite of the plugin to make it easier to maintain and understand, also improved performance and future-proofing compatibility.
* This update is **fully backward compatible** with previous versions.
* Tested with the latest WordPress version (5.2.4).
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

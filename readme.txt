=== Recommender ===
Contributors: missprogrammer
Tags: recommender, wordpress, woocommerce, plugin
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 4.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Wordpress and Woocommerce Plugin for Recommender.ir

== Description ==

Once you have activated the plugin, go to the 'Recommender' screen from your wordpress dashboard to configure the plugin.

= Main settings tab =
You have to enter your recommender.ir service address.

**[Note]** Please make sure you opened your recommender.ir service port as a outbound port.

= Advanced settings tab =
You can configure some advanced settings.

= Widget =
You can use recommender widget in order to show following methods:

* recommend (recommend to user)
* similarity (similar items)
* trendShortTime (trends in short time)
* trendLongTime (trends in long time)
* termBasedRecommendInclusive (recommend to user based on terms)
* termBasedSimilarityInclusive (similar items based on terms)

Requires at least Wordpress 4.0 and PHP 5.4

To order or request additional information, please visit <http://recommender.ir/#order>

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/recommender` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Recommender screen to configure the plugin
4. Make sure you opened your recommender.ir service port as a outbound port

== Screenshots ==

1. Main settings
2. Advanced settings
3. Widget

== Changelog ==

= 1.2.0 =
* Added two new methods.

= 1.1.0 =
* Added capability to sending post tags to recommender.ir service.
* Replaced jQuery dependencies with vanilla javascript.
* Improved AJAX performance.

= 1.0.2 =
* Fixed an error.
* Change ingest method to POST.

= 1.0.1 =
* Fixed a bug.

= 1.0.0 =
* Just released.

== Upgrade Notice ==

= 1.2.0 =
* Added two new methods.
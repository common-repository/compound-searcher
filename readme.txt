=== Compound Searcher ===
Contributors: gnaka08
Tags: search
Requires at least: 3.8
Tested up to: 3.8
Stable tag: 0.1.4
License: LGPLv2.1 or later
License URI: http://www.gnu.org/licenses/lgpl.html

Lets search boxes on your blog support search operators like Google's.

== Description ==
The plugin gives search boxes on the blog additional functionalities for operators among search words, such as Google's. The other supported operators than the standard 'and' search are OR, -(exclude), *(any string) and parenthesis.

The plugin contains [SearchWordsSQL](https://github.com/yonaka/SearchWordsSQL) and [Genericons](http://genericons.com) libraries. These are subject to the respective licenses. If you want to redistribute this plugin with Genericons, you may have to comply with GPLv2 (not lesser).

== Search Operators ==

* Specify words separated by space(s) to search posts containing all the words.
* Quote a string containing space(s) by double quotes to search posts which contain phrase(s) consisted of those words only in the order.
* Insert an asterisk (*) in a word without any space to search posts which contain the word(s) regardless of what the part of the asterisk is.
* Prepend a minus sign (-) without any space to a word to search posts which do not contain the word(s).
* Specify two words separated by " OR " in upper case to search posts which contain either of words or both. This takes precedence over words separated by space(s) only.
* You can combine the above expression. Use a pair of parenthesis to group expressions if needed.

It searches in the bodies and titles of posts in a case-insensitive manner.

== Installation ==

Requires PHP 5.3.6 or later with mbstring enabled. Tested on PHP 5.3.10.

1. Extract the archive into the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Configure the settings in the general settings page.

== Frequently Asked Questions ==
= The help icon is displayed on the weird location. =
The feature could be incompatible with some themes and browsers. Twenty Twelve and Twenty Thirteen seem to be incompatible so far.

= Are the searches case-insensitive? =
Yes if your backend RDBMS is a MySQL.

= What fields does it search in? =
The body and title, including HTML tags in them.

= The search result is incorrect. It seems to do a standard search, rather than a compound search. =
If your query had a syntax error for a compound search, the search would fall back to the standard search mode. Check an error log of your server (e.g. Apache).

== Changelog ==
= 0.1.3 =
* Fix incorrect html for the icon.

= 0.1.2 =
* Now the plugin runs independently of the name of the plugin directory.
* Changed the license from LGPLv3 to LGPLv2.1 or later.

= 0.1.1 =
* changed the plugin directory name.

= 0.1 =
* Initial release.

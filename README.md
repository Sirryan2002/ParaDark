ParaDark Skin
========================

Installation
------------
download this shit into a folder, make sure it's named "ParaDark"

go to MediaWiki LocalSetting.php and add this line `wfLoadSkin( 'ParaDark' );` next to all the other ones that look like that

users should now be able to go to preferences>appearance and change their wiki skin.

### Configuration options

See [skin.json](skin.json).

Best way to alter this skin on a basic level is to edit the mustache templates and LESS stylesheets.

Don't try and mess with the PHP template/skin files unless you know what you're doing because chances are you're just going to break shit.

Development
-----------

I swore at my laptop atleast 100+ times already so this skin may be cursed


### Coding conventions

We strive for compliance with MediaWiki conventions:

<https://www.mediawiki.org/wiki/Manual:Coding_conventions>

Additions and deviations from those conventions that are more tailored to this
project are noted at:

<https://www.mediawiki.org/wiki/Reading/Web/Coding_conventions>

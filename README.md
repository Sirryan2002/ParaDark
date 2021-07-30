ParaDark Skin
========================

Installation
------------
Download all these files into a folder name "ParaDark"

go to MediaWiki LocalSetting.php and add this line `wfLoadSkin( 'ParaDark' );` next to all the other wfLoadSkin() calls

Now when users go to their Special:Preferences page, they can change their appearance setting theme to ParaDark

Unistall
-----------
REQUIRED: remove this line `wfLoadSkin( 'ParaDark' );` from LocalSetting.php

MediaWiki will automatically switch users using ParaDark back to the default skin

You can keep the ParaDark folder in Skins if you just want to disable its use for the time being without
any issues.

### Configuration options

See [skin.json](skin.json).

Best way to alter this skin on a basic level is to edit the .mustache templates and .LESS stylesheets.

variables.less will allow you to change most Coloring and Styling uniformly without a ton of work or worry of breaking things.

The Mustache templates rely heavily on the data they get from the PHP Template/Skin files so be wary about
messing with them

Don't try and mess with the PHP template/skin files unless you have a good understanding of Mediawiki Software and PHP
removing certain calls for data will have a large impact on everything else.

Development
-----------
Most of the Template/Skin PHP files is sourced from the Vector Skin(I did a bit of customization) so credit goes to everyone who worked on that. 
### Coding conventions

We strive for compliance with MediaWiki conventions:

<https://www.mediawiki.org/wiki/Manual:Coding_conventions>

Additions and deviations from those conventions that are more tailored to this
project are noted at:

<https://www.mediawiki.org/wiki/Reading/Web/Coding_conventions>

Licensing
-----------
this is protected under GDL 2.0+ so you're free to take and use this and modify it to whatever the hell you want. Just credit the author and follow the license.
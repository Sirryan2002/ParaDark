<?php

/**
 * SkinTemplate class for ParaDark
 * 
 * I think this section does some wizard shit, it doesn't build the HTML I don't think
 * and it doesn't nab any base template but what I think it does is
 * whip some fucking css stlyesheets along with whatever the hell else I'm doing
 * also if I wanted to add references to JS scripts in this skin I could do that here
 */

 class SkinParaDark extends SkinTemplate { //We're extending the base MW skintemplate
    var $skinname = 'paradark', $stylename = 'ParaDark',
    $template = 'ParaDarkTemplate';
    /**
     * I'm about a week into trying to develop this fucking custom skin for ParadiseStation and 
     * it's fucking difficult. It took me this long to get a proper PHP file setup and I still 
     * don't know what I'm doing to even a minimal extent
     */

    /**
     * so this protected function SetupTemplates() took me a moment to understand (see:https://www.mediawiki.org/wiki/Manual:HTML_templates)
     * what we're doing is using a constructor to build a template parser object, essentially what this does is
     * allow us to get ready to read .mustache templates and implement them. a.k.a convert them into HTML for us
     * this is the extent we work with this mustache templates on this php file though, the rest is handled in
     * the template php
     */
 
    protected function SetupTemplates() {
      $templateParser = new TemplateParser(  __DIR__ . 'includes/templates' ); //creating object and defining where mustache tempaltes are
      echo $tp->processTemplate(
        'MyWidget',
        [
            'username' => $user->getName(),
            'message' => 'Hello!'
        ]
      );
    }

   /**
     * We're adding all of our CSS skins that we want applied via the resource loader
     * we don't need to specify all of them here b/c we've already done that in our skin.json file
      */
    function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( array(
			'mediawiki.skinning.interface', 'skins.paradark' 
			/* 'skins.paradark' is the name we used in our skin.json file */
		) );
	}
 }
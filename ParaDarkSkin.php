<?php

/**
 * SkinTemplate class for ParaDark
 * 
 * This file extends the MW SkinTemplate which gives us some base functions to allow us to interact with out
 * Skin JSON file as well as connect up with our LESS Stylesheets and get Mustache Templates sent to the Template file
 * This then applies all that styling data ontop of the HTML our template PHP file spat out
 */

use MediaWiki\MediaWikiServices;
use Wikimedia\WrappedString;

 class SkinParaDark extends SkinTemplate { //We're extending the base MW skintemplate
    var $skinname = 'paradark', 
    $stylename = 'ParaDark',
    $template = 'ParaDarkTemplate';
    /**
     * I'm about a week into trying to develop this fucking custom skin for ParadiseStation and 
     * it's fucking difficult. It took me this long to get a proper PHP file setup and I still 
     * don't know what I'm doing to even a minimal extent
     */

    public function getDefaultModules() {
      $modules = parent::getDefaultModules();
      $modules['styles'] = array_merge(
        $modules['styles'],
         [ 'skins.paradark.styles' ]
        );
      return $modules;
    }
    /**
     * so this protected function setupTemplate() took me a moment to understand (see:https://www.mediawiki.org/wiki/Manual:HTML_templates)
     * what we're doing is using a constructor to build a template parser object, essentially what this does is
     * allow us to get ready to read .mustache templates and implement them. a.k.a convert them into HTML for us
     * this is the extent we work with this mustache templates on this php file though, the rest is handled in
     * the template php
     */
 
    protected function setupTemplate($classname) {
      $tp = new TemplateParser( __DIR__ . '/includes/templates');
      return new ParaDarkTemplate( $this->getConfig(), $tp );
    }

  public function getTemplateData() {
		$out = $this->getOutput();
		$title = $out->getTitle();

		$indicators = [];
		foreach ( $out->getIndicators() as $id => $content ) {
			$indicators[] = [
				'id' => Sanitizer::escapeIdForAttribute( "mw-indicator-$id" ),
				'class' => 'mw-indicator',
				'html' => $content,
			];
		}

		$printFooter = Html::rawElement(
			'div',
			[ 'class' => 'printfooter' ],
			$this->printSource()
		);

		return [
			// Data objects:
			'array-indicators' => $indicators,
			// HTML strings:
			'html-printtail' => WrappedString::join( "\n", [
				MWDebug::getHTMLDebugLog(),
				MWDebug::getDebugHTML( $this->getContext() ),
				$this->bottomScripts(),
				wfReportTime( $out->getCSP()->getNonce() )
			] ) . '</body></html>',
			'html-site-notice' => $this->getSiteNotice(),
			'html-userlangattributes' => $this->prepareUserLanguageAttributes(),
			'html-subtitle' => $this->prepareSubtitle(),
			// Always returns string, cast to null if empty.
			'html-undelete-link' => $this->prepareUndeleteLink() ?: null,
			// Result of OutputPage::addHTML calls
			'html-body-content' => $this->wrapHTML( $title, $out->mBodytext )
				. $printFooter,
			'html-after-content' => $this->afterContentHook(),
		];
	}
  public function getMenuProps() {
		return $this->buildContentNavigationUrls();
	}
 }
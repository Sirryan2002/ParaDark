<?php
/**
 * BaseTemplate class for ParaDark skin
 * so this extends the BaseTemplate class which itself is an extension of the SkinTemplate class
 * this just gives us access to some functions and shit which make it easier for us to interact
 * and build the HTML of the page. Especially using syntax like html::get
 * 
 * it also gives us all the date used to construct our HTML
 * think of all the shit that goes in the sidebar and body content(a.k.a the wikicode for an article you see in the edit tab)
 * 
 * this is where we output the html contents of the page we're building so theoretically this
 * class needs to echo some html shit at some point
 */

class ParaDarkTemplate extends BaseTemplate {


	private const MENU_LABEL_KEYS = [
		'cactions' => 'paradark-more-actions',
		'tb' => 'toolbox',
		'personal' => 'personaltools',
		'lang' => 'otherlanguages',
	];
	/** @var int */
	private const MENU_TYPE_DEFAULT = 0;
	/** @var int */
	private const MENU_TYPE_TABS = 1;
	/** @var int */
	private const MENU_TYPE_DROPDOWN = 2;
	private const MENU_TYPE_PORTAL = 3;

	private $templateParser;
	private $templateRoot;
	/**
	 * Outputs the entire contents of the page
     * also if you're reading this.... fuck you!
	 */
	public function __construct(Config $config, TemplateParser $templateParser) {
		parent::__construct( $config );
		$this->templateParser = $templateParser;
		$this->templateRoot = 'skin';
	}

	private function getConfig() {
		return $this->config;
	}


    protected function getTemplateParser() { //grab that fucking template parser
		if ( $this->templateParser === null ) {
			throw new \LogicException(
				'TemplateParser has to be set first via setTemplateParser method'
			);
		}
		return $this->templateParser;
	}

    /**
     * essentially what we're doing here is calling almost every single functions our parent class feeds us
     * it's combining all page data into one happy array that we'll use later.
     */
    private function getSkinData() : array {
        $contentNavigation = $this->getSkin()->getMenuProps();
		$skin = $this->getSkin();
		$out = $skin->getOutput();
		$title = $out->getTitle();
        $mainPageHref = Skin::makeMainPageUrl();

        $commonSkinData = $skin->getTemplateData() + [
			'html-headelement' => $out->headElement( $skin ),
			'page-isarticle' => (bool)$out->isArticle(),

			// From OutputPage::getPageTitle, via ::setPageTitle().
			'html-title' => $out->getPageTitle(),
			'msg-tagline' => $skin->msg( 'tagline' )->text(),

			'msg-paradark-jumptonavigation' => $skin->msg( 'paradark-jumptonavigation' )->text(),
			'msg-paradark-jumptosearch' => $skin->msg( 'paradark-jumptosearch' )->text(),

			'html-printfooter' => $skin->printSource(),
			'html-categories' => $skin->getCategories(),
			'data-footer' => $this->getFooterData(),
			'html-navigation-heading' => $skin->msg( 'navigation-heading' ),
			'data-search-box' => $this->buildSearchProps(),

			// Header
			'data-logos' => ResourceLoaderSkinModule::getAvailableLogos( $this->getConfig() ),
			'msg-sitetitle' => $skin->msg( 'sitetitle' )->text(),
			'msg-sitesubtitle' => $skin->msg( 'sitesubtitle' )->text(),
			'main-page-href' => $mainPageHref,

			'data-sidebar' => $this->buildSidebar(),
			'sidebar-visible' => $this->isSidebarVisible(),
			'msg-paradark-action-toggle-sidebar' => $skin->msg( 'paradark-action-toggle-sidebar' )->text(),
		] + $this->getMenuProps();

        return $commonSkinData; //we gonna feed this shit to mustache soon enough
    }

	public function execute() {
        $tp = $this->getTemplateParser(); //grabbing our template parser
		echo $tp->processTemplate( $this->templateRoot, $this->getSkinData() );
	}

    private function getFooterData() : array {
        $skin = $this->getSkin();
        $footerRows = [];
         foreach ( $this->getFooterLinks() as $category => $links ) {
              $items = [];
              $rowId = "footer-$category";
    
              foreach ( $links as $link ) {
                   $items[] = [
                    'id' => "$rowId-$link",
                    'html' => $this->get( $link, '' ),
                 ];
             }

              $footerRows[] = [
                  'id' => $rowId,
                  'className' => null,
                 'array-items' => $items
             ];
        }
    
         // If footer icons are enabled append to the end of the rows
        $footerIcons = $this->getFooterIcons( 'icononly' );
         if ( count( $footerIcons ) > 0 ) {
              $items = [];
               foreach ( $footerIcons as $blockName => $blockIcons ) {
                $html = '';
                  foreach ( $blockIcons as $icon ) {
                       $html .= $skin->makeFooterIcon( $icon );
                   }
                  $items[] = [
                      'id' => 'footer-' . htmlspecialchars( $blockName ) . 'ico',
                     'html' => $html,
                ];
            }
    
            $footerRows[] = [
                'id' => 'footer-icons',
                'className' => 'noprint',
                'array-items' => $items,
            ];
        }
    
        $data = [
            'array-footer-rows' => $footerRows,
        ];

        return $data;
    }

	private function isSidebarVisible() {
		$skin = $this->getSkin();
		if ( $skin->getUser()->isLoggedIn() ) {
			return true; //is user logged in? 
		}
		return false; //don't show sidebar when not logged in :)
	}

    private function buildSidebar() : array {
		$skin = $this->getSkin();
		$portals = $skin->buildSidebar();
		$props = [];
		$languages = null;

		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ) {
				continue;
			}

			// Numeric strings gets an integer when set as key, cast back - T73639
			$name = (string)$name;

			switch ( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$portal = $this->getMenuData(
						'Editor Tools', $content, self::MENU_TYPE_PORTAL //definining sidebar heading for toolbox here
					);
					$props[] = $portal;
					break;
				case 'LANGUAGES':
					$portal = $this->getMenuData(
						'lang',
						$content,
						self::MENU_TYPE_PORTAL
					);
					// The language portal will be added provided either
					// languages exist or there is a value in html-after-portal
					// for example to show the add language wikidata link (T252800)
					if ( count( $content ) ) {
						$languages = $portal;
					}
					break;
				default:
					// Historically some portals have been defined using HTML rather than arrays.
					// Let's move away from that to a uniform definition.
					if ( !is_array( $content ) ) {
						$html = $content;
						$content = [];
						wfDeprecated(
							"`content` field in portal $name must be array."
								. "Previously it could be a string but this is no longer supported.",
							'1.35.0'
						);
					} else {
						$html = false;
					}
					$portal = $this->getMenuData(
						$name, $content, self::MENU_TYPE_PORTAL
					);
					if ( $html ) {
						$portal['html-items'] .= $html;
					}
					$props[] = $portal;
					break;
			}
		}

		$firstPortal = $props[0] ?? null;

		return [
			'has-logo' => true,
			'html-logo-attributes' => Xml::expandAttributes(
				Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) + [
					'class' => 'mw-wiki-logo',
					'href' => Skin::makeMainPageUrl(),
				]
			),
			'array-portals-rest' => array_slice( $props, 1 ),
			'data-portals-first' => $firstPortal,
			'data-portals-languages' => $languages,
		];
	}

	/**
	 * @param string $label to be used to derive the id and human readable label of the menu
	 *  If the key has an entry in the constant MENU_LABEL_KEYS then that message will be used for the
	 *  human readable text instead.
	 * @param array $urls to convert to list items stored as string in html-items key
	 * @param array $options (optional) to be passed to makeListItem
	 * @param bool $setLabelToSelected (optional) the menu label will take the value of the
	 *  selected item if found.
	 * @return array
	 */
	private function getMenuData(
		string $label,
		array $urls = [],
		int $type = self::MENU_TYPE_DEFAULT,
		array $options = [],
		bool $setLabelToSelected = false
	) : array {
		$skin = $this->getSkin();
		$extraClasses = [
			self::MENU_TYPE_DROPDOWN => 'paradark-menu',
			self::MENU_TYPE_TABS => 'paradark-menu paradark-menu-tabs paradarkTabs',
			self::MENU_TYPE_PORTAL => 'paradark-menu paradark-menu-portal portal',
			self::MENU_TYPE_DEFAULT => 'paradark-menu',
		];
		// A list of classes to apply the list element and override the default behavior.
		$listClasses = [
			// `.menu` is on the portal for historic reasons.
			// It should not be applied elsewhere per T253329.
			self::MENU_TYPE_DROPDOWN => 'menu paradark-menu-content-list',
		];
		$isPortal = self::MENU_TYPE_PORTAL === $type;
		// For some menu items, there is no language key corresponding with its menu key.
		// These inconsitencies are captured in MENU_LABEL_KEYS
		$msgObj = $skin->msg($label);
		$props = [
			'id' => "p-$label",
			'label-id' => "p-{$label}-label",
			// If no message exists fallback to plain text (T252727)
			'label' => $msgObj->exists() ? $msgObj->text() : $label,
			'html-items' => '',
			'html-tooltip' => Linker::tooltip( 'p-' . $label ),
		];

		foreach ( $urls as $key => $item ) {
			$props['html-items'] .= $this->getSkin()->makeListItem( $key, $item, $options );

			// Check the class of the item for a `selected` class and if so, propagate the items
			// label to the main label.
			if ( $setLabelToSelected ) {
				if ( isset( $item['class'] ) && stripos( $item['class'], 'selected' ) !== false ) {
					$props['label'] = $item['text'];
				}
			}
		}
		return $props;
	}

	/**
	 * @return array
	 */
	private function getMenuProps() : array {
		// @phan-suppress-next-line PhanUndeclaredMethod
		$contentNavigation = $this->getSkin()->getMenuProps();
		$personalTools = $this->getPersonalTools();
		$skin = $this->getSkin();

		// For logged out users ParaDark shows a "Not logged in message"
		// This should be upstreamed to core, with instructions for how to hide it for skins
		// that do not want it.
		// For now we create a dedicated list item to avoid having to sync the API internals
		// of makeListItem.
		if ( !$skin->getUser()->isLoggedIn() && User::groupHasPermission( '*', 'edit' ) ) {
			$loggedIn =
				Html::element( 'li',
					[ 'id' => 'pt-anonuserpage' ],
					$skin->msg( 'notloggedin' )->text()
				);
		} else {
			$loggedIn = '';
		}

		// This code doesn't belong here, it belongs in the UniversalLanguageSelector
		// It is here to workaround the fact that it wants to be the first item in the personal menus.
		if ( array_key_exists( 'uls', $personalTools ) ) {
			$uls = $skin->makeListItem( 'uls', $personalTools[ 'uls' ] );
			unset( $personalTools[ 'uls' ] );
		} else {
			$uls = '';
		}

		$ptools = $this->getMenuData( 'personal', $personalTools );
		// Append additional link items if present.
		$ptools['html-items'] = $uls . $loggedIn . $ptools['html-items'];

		return [
			'data-personal-menu' => $ptools,
			'data-namespace-tabs' => $this->getMenuData(
				'namespaces',
				$contentNavigation[ 'namespaces' ] ?? []
			),
			'data-variants' => $this->getMenuData(
				'variants',
				$contentNavigation[ 'variants' ] ?? []
			),
			'data-page-actions' => $this->getMenuData(
				'views',
				$contentNavigation[ 'views' ] ?? []
			),
			'data-page-actions-more' => $this->getMenuData(
				'cactions',
				$contentNavigation[ 'actions' ] ?? []
			),
		];
	}

	/**
	 * @return array
	 */
	private function buildSearchProps() : array {
		$config = $this->getConfig();
		$skin = $this->getSkin();
		$props = [
			'form-action' => $config->get( 'Script' ),
			'html-button-search-fallback' => $this->makeSearchButton(
				'fulltext',
				[ 'id' => 'mw-searchButton', 'class' => 'searchButton mw-fallbackSearchButton' ]
			),
			'html-button-search' => $this->makeSearchButton(
				'go',
				[ 'id' => 'searchButton', 'class' => 'searchButton' ]
			),
			'html-input' => $this->makeSearchInput( [ 'id' => 'searchInput' ] ),
			'msg-search' => $skin->msg( 'search' ),
			'page-title' => SpecialPage::getTitleFor( 'Search' )->getPrefixedDBkey(),
		];
		return $props;
	}
}
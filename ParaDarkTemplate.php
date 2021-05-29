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

class FooBarTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
     * also if you're reading this.... fuck you!
	 */
	public function execute() {
		$this->html( 'headelement' ); 
        $this->text( 'sitename' );?>

        <a href="<?php // This outputs your wiki's main page URL to the browser.
		    echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] );
            ?>"
	        <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) 
        ?>>
            <img src="<?php 
		 	    $this->text( 'logopath' ); 	
		 	    // This outputs our stupid logo image
		    ?>">
        </a>

        <?php
        // 
        if ( $this->data['title'] !== '' ) {
	        // we're defining the attributes in our title first in this variables
            // such as the ID/Class so we can refer to it in our CSS stylesheets later
	        $titleAttribs = [ 'id' => 'firstHeading', 'class' => 'firstHeading' ];
	        // Note that we don't use $this->html( 'title' ) here, because we don't want to echo the title at that point.
	        echo Html::rawElement( 'h1', $titleAttribs, $this->data[ 'title' ] );
            //methinks that echo is the syntax that throws html shitcode where it needs to go
            //im going to bed now
        }
        $this->printTrail(); ?>
</body>
</html><?php 
/** 
 * I think what this part does is close off the body and html tags of the page but dont
 * fucking quote me on that ok?
 */
	}
}
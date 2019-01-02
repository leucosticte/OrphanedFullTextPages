<?php
class SpecialOrphanedFullTextPages extends SpecialPage {
	function __construct() {
		parent::__construct( 'OrphanedFullTextPages' );
	}
	
	function execute( $par ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();
		#$output->addWikiText ( '<poem>' );
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'page',
			'page_title',
			array( 'page_namespace' => 3000 )
		);
		$anyResults = false;
		$text = '';
		foreach ( $res as $row ) {
			$title = $dbr->selectField(
				'page',
				'page_title',
				array( 'page_namespace' => 0, 'page_title' => $row->page_title )
			);
			if ( !$title ) {
				if ( !$anyResults ) {
					$output->addWikiText ( "The following Full text: pages are orphaned:\n\n" );
					$anyResults = true;
				}
				$text .= '[[Full text:' . str_replace( '_', ' ', $row->page_title ) . "]]\n";
			}
		}
		if ( !$anyResults ) {
			$output->addWikiText( "No orphaned Full text: pages were found." );
		} else {
			$params = array(
				'id' => 'wpTextbox1',
				'name' => 'wpTextbox1',
				'cols' => $this->getUser()->getOption( 'cols' ),
				'rows' => $this->getUser()->getOption( 'rows' ),
				'readonly' => 'readonly'
				#'lang' => $pageLang->getHtmlCode(),
				#'dir' => $pageLang->getDir(),
			);
			$output->addHTML( Html::element( 'textarea', $params, $text ) );
		}
		#$output->addWikiText( '</poem>' );
	}
	
	function getGroupName() {
        return 'maintenance';
    }
}
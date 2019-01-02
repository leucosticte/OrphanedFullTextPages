<?php
class SpecialOrphanedFullTextPages extends SpecialPage {
	function __construct() {
		parent::__construct( 'OrphanedFullTextPages' );
	}
	
	function execute( $par ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();
		$wikitext = '<poem>';
		$dbr = wfGetDB( DB_REPLICA );
		$res = $dbr->select(
			'page',
			'page_title',
			array( 'page_namespace' => 3000 )
		);
		$anyResults = false;
		foreach ( $res as $row ) {
			$title = $dbr->selectField(
				'page',
				'page_title',
				array( 'page_namespace' => 0, 'page_title' => $row->page_title )
			);
			if ( !$title ) {
				if ( !$anyResults ) {
					$wikitext .= "The following Full text: pages are orphaned:\n\n";
					$anyResults = true;
				}
				$wikitext .= '[[Full text:' . str_replace( '_', ' ', $row->page_title ) . "]]\n";
			}
		}
		if ( !$anyResults ) {
			$wikitext .= "No orphaned Full text: pages were found.";
		}
		$wikitext .= '</poem>';
		$output->addWikiText ( $wikitext );
	}
	
	function getGroupName() {
        return 'maintenance';
    }
}
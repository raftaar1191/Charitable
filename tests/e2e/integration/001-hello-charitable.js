describe( 'Hello Charitable', () => {
    before( () => {
        cy.login();
    } );
    
    it( 'Should show the Charitable menu tab', () => {
		// Assertions
        cy.get( '#adminmenu' )
            .contains( 'Charitable' );
	} );
} );
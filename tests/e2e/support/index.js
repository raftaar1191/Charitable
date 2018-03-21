Cypress.Commands.add( 'newCampaign', () => {
	cy.visitAdmin( '/post-new.php?post_type=campaign' );
} );

Cypress.Commands.add( 'visitAdmin', ( adminPath ) => {
	cy.visit( '/wp-admin/' + adminPath ).location( 'pathname' ).then( ( path ) => {
		if ( path.endsWith( '/wp-login.php' ) ) {
			cy.login();
		}
	} );
} );

Cypress.Commands.add( 'login', ( username = Cypress.env( 'username' ), password = Cypress.env( 'password' ) ) => {
	// A best practice would be to avoid this in each test
	// and fake it by calling an API and setting a cookie
	// (not sure this is possible in WP)

	cy.location( 'pathname' ).then( ( path ) => {
		if ( ! path.endsWith( '/wp-login.php' ) ) {
			cy.visit( '/wp-login.php' );
		}
	} );
	cy.get( '#user_login' ).clear().type( username );
	cy.get( '#user_pass' ).clear().type( password );
	cy.get( '#wp-submit' ).click();
} );

Cypress.Cookies.defaults( {
	whitelist: /^wordpress_/,
} );

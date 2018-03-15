describe( 'Create a campaign', () => {
    before( () => {
        cy.newCampaign();
    } );
    
    it( 'Should have meta boxes', () => {
        cy.get( '#campaign-description' );
        cy.get( '#campaign-goal' );
        cy.get( '#campaign-end-date' );
        cy.get( '#charitable-campaign-advanced-metabox' );
        cy.get( '#charitable-campaign-advanced-metabox' ).find( '[aria-controls=campaign-donation-options]' ).should( 'have.class', 'ui-state-active' ).find( 'a' ).contains( 'Donation Options' );
        cy.get( '#campaign-donation-options' ).find( '#charitable-campaign-suggested-donations tbody .no-suggested-amounts' ).contains( 'No suggested amounts have been created yet.' );
        cy.get( '#campaign-donation-options' ).find( '#charitable-campaign-suggested-donations tfoot .button' ).contains( '+ Add a Suggested Amount' );
        cy.get( '#campaign-donation-options' ).find( '#campaign_allow_custom_donations' ).should( 'be.checked' );
        cy.get( '#charitable-campaign-advanced-metabox' ).find( '[aria-controls=campaign-extended-description]' ).contains( 'Extended Description' );
        cy.get( '#charitable-campaign-advanced-metabox' ).find( '#charitable-extended-description_ifr' );
        cy.get( '#campaign_categorydiv' );
        cy.get( '#tagsdiv-campaign_tag' );
    } );
    
    it( 'Should create a new Charitable campaign', () => {
		// Create campaign.
        cy.get( '#title' ).type( 'Test Campaign' );        
        cy.get( '#campaign_description' ).type( 'My campaign description' );        
        cy.get( '#campaign_goal' ).type( '500' );
        cy.get( '#campaign_end_date' ).type( 'December 31, 2029' );
        cy.get( '#charitable-campaign-suggested-donations tfoot .button' ).click();
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).last().find( '.reorder-col' );
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).last().find( '.amount-col input' ).type( '15' );
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).last().find( '.description-col input' ).type( 'Fifteen' );
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).last().find( '.remove-col' );
        cy.get( '#charitable-campaign-suggested-donations tfoot .button' ).click();
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).should( 'have.length', 4 );
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).last().find( '.charitable-delete-row' ).click();
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).should( 'have.length', 3 );
        cy.get( '#campaign_allow_custom_donations' ).uncheck();
        cy.contains( 'Publish' ).click();
    
        // Assert that values were saved.
        cy.get( '#title' ).should( 'have.value', 'Test Campaign' );
        cy.get( '#campaign_description' ).should( 'have.value', 'My campaign description' );
        cy.get( '#campaign_goal' ).should( 'have.value', '500' );
        cy.get( '#campaign_end_date' ).should( 'have.value', 'December 31, 2029' );
        cy.get( '#charitable-campaign-suggested-donations tbody tr' ).should( 'have.length', 3 );
        cy.get( '#charitable-campaign-suggested-donations .amount-col input' ).last().should( 'have.value', '15' );
        cy.get( '#charitable-campaign-suggested-donations .description-col input' ).last().should( 'have.value', 'Fifteen' );
        cy.get( '#campaign_allow_custom_donations' ).should( 'not.be.checked' );
	} );
} );
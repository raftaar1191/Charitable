( function( $, window ){

    /**
    * Global tests.
    *
    * These tests will always be run.
    */
    var Global_Tests = function() {

        // Sanity check: Make sure the window.Charitable object is present.
        QUnit.test( "Charitable exists", function( assert ) {
            assert.ok( window.CHARITABLE, 'CHARITABLE object found in window.' );
        });
    } 

    /**
     * Donation Form tests.
     *
     * These tests will only be run if a donation form is located on the page we're currently looking at.
     */
    var Donation_Form_Tests = function( CHARITABLE ) {
        var $form      = $( '.charitable-donation-form' ).first(),
            helper     = new CHARITABLE.Donation_Form( $form ),
            $suggested = $form.find( '.donation-amounts' );
            $custom    = $form.find( '.custom-donation-input' );

        if ( $suggested.length ) {

            // Choose a suggested donation amount.
            QUnit.test( "Choose a suggested donation amount", function( assert ) {
                $el = $suggested.find( '.suggested-donation-amount input[name=donation_amount]' ).first();
                $el.trigger( 'click' );

                assert.ok( $el.closest( 'li' ).hasClass( 'selected' ), 'Suggested donation amount has selected class.' );
                assert.equal( helper.get_amount(), $el.val(), 'CHARITABLE.Donation_Form.get_amount() returns suggested donation amount.' );
            });

        } 

        if ( $custom.length ) {

            // CHARITABLE.Donation_Form.get_amount
            QUnit.test( "CHARITABLE.Donation_Form.get_amount", function( assert ) {
                var $el     = $form.find( '.custom-donation-input' ),
                    amounts = [ 
                        { amount : 15, formatted : "15.00" }, 
                        { amount : 150, formatted : "150.00" }, 
                        { amount : 1500, formatted : "1,500.00" }, 
                        { amount : 15.5, formatted : "15.50" },
                        { amount : 1500.5, formatted : "1,500.50" }
                    ], 
                    amount, 
                    i;

                $el.trigger( 'focus' );
                
                for ( i = 0; i < amounts.length; i += 1 ) {
                    amount = amounts[i];
                    
                    $el.val( amount.amount ).trigger( 'blur' );

                    assert.equal( helper.get_amount(), amount.amount, 'Test amount is ' + amount.amount );
                    assert.equal( $el.val(), amount.formatted, 'Test formatted amount is ' + amount.formatted );
                }

                if ( $suggested.length ) {
                    assert.ok( $el.closest( 'li' ).hasClass( 'selected' ), 'Custom input has selected class.' );
                }                
            });
        }

        // CHARITABLE.Donation_Form.get_email
        QUnit.test( "CHARITABLE.Donation_Form.get_email", function( assert ) {
            $form.find( '[name=email]' ).val( 'info@example.com' );
            assert.equal( helper.get_email(), 'info@example.com', 'CHARITABLE.Donation_Form.get_email() returns set email address.' );
        });

        // CHARITABLE.Donation_Form.get_input
        QUnit.test( "CHARITABLE.Donation_Form.get_input", function( assert ) {
            $form.find( '[name=first_name]' ).val( 'Eric' );
            assert.equal( helper.get_input( 'first_name' ).val(), 'Eric', 'CHARITABLE.Donation_Form.get_input() returns set first name.' );
        });

        // Validate donation form with errors
        QUnit.test( "Validate donation form with errors", function( assert ) {

            // Empty out all required fields.        
            helper.get_required_fields().each( function() {
                $( this ).find( 'input, select, textarea' ).val( '' );
            });

            // Uncheck all suggested donation fields.
            $form.find( '[name=donation_amount]:checked' ).each( function() {
                $( this ).prop( 'checked', false );
            });

            // Set custom donation input to empty.
            $form.find( 'input.custom-donation-input' ).val( 0 );

            // Run validate() on the form.
            assert.notOk( helper.validate(), 'CHARITABLE.Donation_Form.validate() returns false.' );
            assert.notOk( helper.validate_amount(), 'CHARITABLE.Donation_Form.validate_amount() returns false.' );
            assert.notOk( helper.validate_required_fields(), 'CHARITABLE.Donation_Form.validate_required_fields() returns false.' );

        });
    } 

    $( document ).ready( function() {
        $( 'body' ).append( '<div id="charitable-qunit"></div>' );
        $( '#charitable-qunit' ).append(
            '<h1 id="qunit-header">Charitable QUnit Tests</h1>' +
            '<h2 id="qunit-banner"></h2>' +
            '<div id="qunit-testrunner-toolbar"></div>' +
            '<h2 id="qunit-userAgent"></h2>' +
            '<ol id="qunit-tests"></ol>' +
            '<div id="qunit-fixture">test markup</div>');
        });

    Global_Tests();

    if ( ! window.CHARITABLE ) {
        return;
    }

    if ( $( '.charitable-donation-form' ).length ) {
        Donation_Form_Tests( window.CHARITABLE );
    }


})( jQuery, window );
CHARITABLE = window.CHARITABLE || {};

( function( exports ){

    var Sessions = function() {
        this.session_id = Cookies.get( CHARITABLE_SESSION.cookie_name );

        // Set a cookie if none exists.
        if ( ! this.session_id ) {
            set_cookie();
        }

        // If a session ID is set and it matches the one we received, proceed no further.
        if ( this.session_id && ( this.session_id === CHARITABLE_SESSION.id ) ) {
            return;
        }

        if (document.readyState != 'loading') {
            init();
        } else if (document.addEventListener) {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            document.attachEvent('onreadystatechange', function() {
                document.readyState != 'loading' && init();
            });
        }

        // Init.
        function init() {
            var elements = document.querySelectorAll('.charitable-session-content');
            var i = 0; 

            for (i; i < elements.length; i++) {
                var element = elements[i];
                var template = element.getAttribute('data-template');
                var data = 'action=charitable_get_session_content&template=' + element.getAttribute('data-template') + '&' + element.getAttribute('data-args');
                var request = new XMLHttpRequest();
                request.open('POST', CHARITABLE_SESSION.ajaxurl, true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                request.onreadystatechange = function() {
                    if (this.readyState === 4) {
                        if (this.status >= 200 && this.status < 400) {
                            element.innerHTML = JSON.parse( this.response ).data;
                            element.style.display = 'block';
                        } else {
                            element.style.display = 'block';                            
                        }
                    }
                };
                request.send(data);
                request = null;
            }
        }

        // Set cookie.
        function set_cookie() {
            Cookies.set( CHARITABLE_SESSION.cookie_name,
                CHARITABLE_SESSION.generated_id + '||' + CHARITABLE_SESSION.expiration + '||' + CHARITABLE_SESSION.expiration_variant, 
                {
                    expires: new Date( new Date().getTime() + ( parseInt( CHARITABLE_SESSION.expiration ) * 1000 ) ),
                    path: CHARITABLE_SESSION.cookie_path,
                    domain: CHARITABLE_SESSION.cookie_domain,
                    secure: CHARITABLE_SESSION.secure
                }
            );

            return CHARITABLE_SESSION.generated_id + '||' + CHARITABLE_SESSION.expiration + '||' + CHARITABLE_SESSION.expiration_variant;
        }
    }

    exports.Sessions = Sessions();

})( CHARITABLE );
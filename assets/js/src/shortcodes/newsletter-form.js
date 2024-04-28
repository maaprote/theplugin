/**
 * Newsletter Form Shortcode.
 * 
 * The main.js file is a dependency for this file.
 */

'use strict';

ThePlugin.newsletterForm = {
	mainSelector: '.rtp-newsletter-form',
	submitSelector: '.rtp-newsletter-form__form-submit',
	errorSelector: '.rtp-newsletter-form__error',

	init: function() {
		const self = this;

		if ( typeof thepluginData === 'undefined' ) {
			return false;
		}

		if ( ! ThePlugin.componentExists( this.mainSelector ) ) {
			return false;
		}
		
		const newsletters = document.querySelectorAll( this.mainSelector );
		if ( newsletters.length === 0 ) {
			return false;
		}

		for( const newsletter of newsletters ) {
			const form = newsletter.querySelector( 'form' );
			if ( ! form ) {
				return false;
			}
			
			form.addEventListener( 'submit', function( e ) {
				e.preventDefault();
				
				const data = new FormData( form );
				const url = thepluginData.ajaxurl + '?action=' + thepluginNewsletterFormData.ajaxAction + '&nonce=' + thepluginNewsletterFormData.nonce;
				const newsletterFormWrapper = document.querySelector( self.mainSelector );
				const submitButton = document.querySelector( self.submitSelector );
				const initialSubmitText = document.querySelector( self.submitSelector ).innerHTML;
				const errorWrapper = document.querySelector( self.errorSelector );

				submitButton.innerHTML = submitButton.getAttribute( 'data-loading' );

				fetch( url, {
					method: 'POST',
					body: data
				})
				.then( response => response.json() )
				.then( response => {
					if ( response.success ) {
						form.reset();
						newsletterFormWrapper.classList.remove( 'has-error' );
						
						window.location.reload();
					} else {
						newsletterFormWrapper.classList.add( 'has-error' );
						errorWrapper.innerHTML = response.data.message;
					}

					submitButton.innerHTML = initialSubmitText;
				})
				.catch( error => {
					console.error( error );
				});
			});
		}
	}
}

// Initialize.
document.addEventListener('DOMContentLoaded', function() {
	ThePlugin.newsletterForm.init();
});
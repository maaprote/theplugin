/**
 * Newsletter Form Entries Shortcode.
 * 
 * The main.js file is a dependency for this file.
 */

'use strict';

ThePlugin.newsletterFormEntries = {
	mainSelector: '.rtp-newsletter-entries',
    entriesSelector: '.rtp-newsletter-entries__entries',
    errorSelector: '.rtp-newsletter-entries__error',

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
				const url = thepluginData.ajaxurl + '?action=' + thepluginNewsletterFormEntriesData.ajaxAction + '&nonce=' + thepluginNewsletterFormEntriesData.nonce;
				const newsletterEntriesWrapper = document.querySelector( self.mainSelector );
				const errorWrapper = document.querySelector( self.errorSelector );

				newsletterEntriesWrapper.classList.remove( 'has-error' );
				newsletterEntriesWrapper.classList.add( 'is-loading' );
				errorWrapper.innerHTML = '';

				fetch( url, {
					method: 'POST',
					body: data
				})
				.then( response => response.json() )
				.then( response => {
					if ( response.success ) {
						form.reset();
						document.querySelector( self.entriesSelector ).innerHTML = response.data.entries_output;
					} else {
						newsletterEntriesWrapper.classList.add( 'has-error' );
						errorWrapper.innerHTML = response.data.message;
					}

					newsletterEntriesWrapper.classList.remove( 'is-loading' );
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
	ThePlugin.newsletterFormEntries.init();
});
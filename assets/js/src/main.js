/**
 * Main
 * 
 */

'use strict';

let ThePlugin = ThePlugin || {};

ThePlugin = {
	componentExists: function( componentSelector ) {
		return document.body.contains( document.querySelector( componentSelector ) );
	},

	// as the plugin grows, we can add more methods here
}
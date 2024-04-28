/**
 * Main
 * 
 */

'use strict';

let ThePlugin = ThePlugin || {};

ThePlugin = {
	init: function() {

	},

	componentExists: function( componentSelector ) {
		return document.body.contains( document.querySelector( componentSelector ) );
	},
}

// Initialize.
document.addEventListener('DOMContentLoaded', function() {
	ThePlugin.init();
});
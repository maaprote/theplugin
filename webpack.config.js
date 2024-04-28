const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
module.exports = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		admin: './assets/js/src/admin/page-options/page-options.js',
	},
    output: {
        ...defaultConfig.output,
        filename: 'page-options-build.js',
        path: __dirname + '/assets/js/admin/',
    }
};
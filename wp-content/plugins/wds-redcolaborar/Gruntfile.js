/**
 * Gruntfile.
 *
 * This is mainly used to generate language files for us.
 *
 * @since  1.0.0
 * @package WDS\RedColaborar
 */

/* globals require, module */

module.exports = function( grunt ) {

	// Load grunt.
	require( 'load-grunt-tasks' )( grunt );

	// Load package.json.
	var pkg = grunt.file.readJSON( 'package.json' );

	// Project configuration.
	grunt.initConfig( {
		pkg: pkg,

		// Create a .pot file.
		makepot: {
			dist: {
				options: {
					domainPath: '/languages/',
					potFilename: 'redcolaborar.pot',
					type: 'wp-plugin'
				}
			}
		}
	} );

	// grunt languages.
	grunt.registerTask( 'languages', [ 'makepot' ] );

	// grunt.
	grunt.registerTask( 'default', [ 'languages' ] );

	// EOL CLU!
	grunt.util.linefeed = '\n';
};

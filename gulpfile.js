/*
 * Load Plugins
 */
var gulp = require( 'gulp' ),
	sass = require( 'gulp-ruby-sass' ),
	prefix = require( 'gulp-autoprefixer' ),
	exec = require( 'gulp-exec' ),
	clean = require( 'gulp-clean' ),
	zip = require( 'gulp-zip' );


gulp.task( 'styles', function() {
	return gulp.src( 'scss/**/*.scss' )
		.pipe( sass( {sourcemap: false, style: 'nested'} ) )
		.on( 'error', function( e ) {
			console.log( e.message );
		} )
		.pipe( prefix( "last 1 version", "> 1%", "ie 8", "ie 7" ) )
		.pipe( gulp.dest( './css/' ) )
		.pipe( notify( 'Styles task complete' ) );
} );

gulp.task( 'styles-watch', function() {
	return gulp.watch( 'scss/**/*.scss', ['styles'] );
} );

/**
 * Create a zip archive out of the cleaned folder and delete the folder
 */
gulp.task( 'zip', ['build'], function() {

	return gulp.src( './' )
		.pipe( exec( 'cd ./../; rm -rf pixproof.zip; cd ./build/; zip -r -X ./../pixproof.zip ./pixproof; cd ./../; rm -rf build' ) );

} );

/**
 * Copy theme folder outside in a build folder, recreate styles before that
 */
gulp.task( 'copy-folder', function() {

	return gulp.src( './' )
		.pipe( exec( 'rm -Rf ./../build; mkdir -p ./../build/pixproof; cp -Rf ./* ./../build/pixproof/' ) );
} );

/**
 * Clean the folder of unneeded files and folders
 */
gulp.task( 'build', ['copy-folder'], function() {

	// files that should not be present in build zip
	files_to_remove = [
		'**/codekit-config.json',
		'node_modules',
		'config.rb',
		'gulpfile.js',
		'package.json',
		'wpgrade-core/vendor/redux2',
		'wpgrade-core/features',
		'wpgrade-core/tests',
		'wpgrade-core/**/*.less',
		'wpgrade-core/**/*.scss',
		'wpgrade-core/**/*.rb',
		'wpgrade-core/**/sass',
		'wpgrade-core/**/scss',
		'pxg.json',
		'build',
		'.idea',
		'**/*.css.map',
		'**/.sass*',
		'.sass*',
		'**/.git*',
		'*.sublime-project',
		'.DS_Store',
		'**/.DS_Store',
		'__MACOSX',
		'**/__MACOSX',
		'.labels'
	];

	files_to_remove.forEach( function( e, k ) {
		files_to_remove[k] = '../build/pixproof/' + e;
	} );

	return gulp.src( files_to_remove, {read: false} )
		.pipe( clean( {force: true} ) );
} );


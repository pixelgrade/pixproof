var plugin = 'pixproof',

  gulp 		= require('gulp'),
  plugins 	= require('gulp-load-plugins' )(),
  fs          = require('fs'),
  cp = require('child_process'),
  del         = require('del'),
  cleanCSS = require('gulp-clean-css'),
  commandExistsSync = require('command-exists').sync;

var u = plugins.util,
  c = plugins.util.colors,
  log = plugins.util.log;


gulp.task( 'styles', function() {
	return gulp.src( 'scss/**/*.scss' )
		.pipe(plugins.sass( {sourcemap: false, style: 'nested'} ) )
		.pipe(plugins.autoprefixer() )
    .pipe(plugins.replace(/^@charset \"UTF-8\";\n/gm, ''))
    .pipe(cleanCSS())
		.pipe( gulp.dest( './css/' ) );
} );

gulp.task( 'styles-watch', function() {
	return gulp.watch( 'scss/**/*.scss', ['styles'] );
} );

/**
 * Copy plugin folder outside in a build folder, recreate styles before that
 */
gulp.task( 'copy-folder', function() {
  var dir = process.cwd();
  return gulp.src( './*' )
    .pipe( plugins.exec( 'rm -Rf ./../build; mkdir -p ./../build/' + plugin + ';', {
      silent: true,
      continueOnError: true // default: false
    } ) )
    .pipe(plugins.rsync({
      root: dir,
      destination: '../build/' + plugin + '/',
      // archive: true,
      progress: false,
      silent: false,
      compress: false,
      recursive: true,
      emptyDirectories: true,
      clean: true,
      exclude: ['node_modules']
    }));
} );

/**
 * Clean the folder of unneeded files and folders
 */
gulp.task( 'remove-files', function() {

  // files that should not be present in build zip
  var files_to_remove = [
    'node_modules',
    'bin',
    'tests',
    '.travis.yml',
    '.babelrc',
    '.gitignore',
    '.codeclimate.yml',
    '.csslintrc',
    '.eslintignore',
    '.eslintrc',
    'circle.yml',
    'phpunit.xml.dist',
    '.sass-cache',
    'config.rb',
    'gulpfile.js',
    'webpack.config.js',
    'package.json',
    'package-lock.json',
    'pxg.json',
    'build',
    '.idea',
    '**/*.css.map',
    '**/.git*',
    '*.sublime-project',
    '.DS_Store',
    '**/.DS_Store',
    '__MACOSX',
    '**/__MACOSX',
    '.csscomb',
    '.csscomb.json',
    '.codeclimate.yml',
    'tests',
    'circle.yml',
    '.circleci',
    '.labels',
    '.jscsrc',
    '.jshintignore',
    'browserslist',
    'README.md',
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
    files_to_remove[k] = '../build/' + plugin + '/' + e;
  } );

  return del(files_to_remove, {force: true});
} );

function maybeFixBuildDirPermissions(done) {

  cp.execSync('find ./../build -type d -exec chmod 755 {} \\;');

  return done();
}
maybeFixBuildDirPermissions.description = 'Make sure that all directories in the build directory have 755 permissions.';
gulp.task( 'fix-build-dir-permissions', maybeFixBuildDirPermissions );

function maybeFixBuildFilePermissions(done) {
  if (!commandExistsSync('dos2unix')) {
    log( c.red( 'Could not ensure that line endings are correct on the build files since you are missing the "dos2unix" utility! You should install it.' ) );
    log( c.red( 'However, this is not a very big deal. The build task will continue.' ) );
  } else {
    cp.execSync('find ./../build -type f -print0 | xargs -0 -n 1 -P 4 dos2unix');
  }

  return done();
}
maybeFixBuildFilePermissions.description = 'Make sure that all files in the build directory have 644 permissions.';
gulp.task( 'fix-build-file-permissions', maybeFixBuildFilePermissions );

function maybeFixIncorrectLineEndings(done) {

  cp.execSync('find ./../build -type f -print0 | xargs -0 -n 1 -P 4 dos2unix');

  return done();
}
maybeFixIncorrectLineEndings.description = 'Make sure that all line endings in the files in the build directory are UNIX line endings.';
gulp.task( 'fix-line-endings', maybeFixIncorrectLineEndings );

/**
 * Create a zip archive out of the cleaned folder and delete the folder
 */
gulp.task( 'make-zip', function() {
  var versionString = '';
  // get plugin version from the main plugin file
  var contents = fs.readFileSync("./" + plugin + ".php", "utf8");

  // split it by lines
  var lines = contents.split(/[\r\n]/);

  function checkIfVersionLine(value, index, ar) {
    var myRegEx = /^[\s\*]*[Vv]ersion:/;
    if (myRegEx.test(value)) {
      return true;
    }
    return false;
  }

  // apply the filter
  var versionLine = lines.filter(checkIfVersionLine);

  versionString = versionLine[0].replace(/^[\s\*]*[Vv]ersion:/, '').trim();
  versionString = '-' + versionString.replace(/\./g, '-');

  return gulp.src('./')
    .pipe(plugins.exec('cd ./../; rm -rf ' + plugin[0].toUpperCase() + plugin.slice(1) + '*.zip; cd ./build/; zip -r -X ./../' + plugin[0].toUpperCase() + plugin.slice(1) + versionString + '.zip ./; cd ./../; rm -rf build'));

} );

function buildSequence(cb) {
  return gulp.series( 'copy-folder', 'remove-files', 'fix-build-dir-permissions', 'fix-build-file-permissions', 'fix-line-endings' )(cb);
}
buildSequence.description = 'Sets up the build folder';
gulp.task( 'build', buildSequence );

function zipSequence(cb) {
  return gulp.series( 'build', 'make-zip' )(cb);
}
zipSequence.description = 'Creates the zip file';
gulp.task( 'zip', zipSequence  );


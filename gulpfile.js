// Scripts to process.
const watchScripts = './assets/js/src/**/*.js';
const scripts = [

	{
		name: 'main',
		src: './assets/js/src/main.js',
		destination: './assets/js',
		file: 'main',
	},
	{
		name: 'newsletterForm',
		src: './assets/js/src/shortcodes/newsletter-form.js',
		destination: './assets/js/shortcodes',
		file: 'newsletter-form',
	},
	{
		name: 'newsletterFormEntries',
		src: './assets/js/src/shortcodes/newsletter-form-entries.js',
		destination: './assets/js/shortcodes',
		file: 'newsletter-form-entries',
	},

];

const watchStyles = './assets/sass/**/*.scss';
const styles = [

	{
		name: 'main',
		src: './assets/sass/main.scss',
		destination: './assets/css',
		file: 'main',
	},
	{
		name: 'newsletterForm',
		src: './assets/sass/shortcodes/newsletter-form.scss',
		destination: './assets/css/shortcodes',
		file: 'newsletter-form',
	},
	{
		name: 'newsletterFormEntrie',
		src: './assets/sass/shortcodes/newsletter-form-entries.scss',
		destination: './assets/css/shortcodes',
		file: 'newsletter-form-entries',
	},

];

const gulp = require('gulp');

const nodesass     = require('sass');
const sass         = require('gulp-sass')(nodesass);

const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const babel  = require('gulp-babel');
const autoprefixer = require('gulp-autoprefixer');

const rename      = require('gulp-rename');
const filter      = require('gulp-filter');
const lineec      = require('gulp-line-ending-corrector');
const mmq          = require('gulp-merge-media-queries');
const notify      = require('gulp-notify');
const remember    = require('gulp-remember');
const plumber     = require('gulp-plumber');

/**
 * Custom Error Handler.
 */
const errorHandler = r => {
	notify.onError('\n\n❌  ===> ERROR: <%= error.message %>\n')(r);
	beep();
};

/**
 * Task: `styles`.
 */
const styleTasks = styles.map((style) => {
	const taskName = style.name + 'StyleTask';

	gulp.task(taskName, () => {
		return gulp
			.src(style.src, {allowEmpty: true})
			.pipe(plumber(errorHandler))
			.pipe(
				sass({
					errLogToConsole: true,
					outputStyle: 'expanded',
					precision: 10
				})
			)
			.on('error', sass.logError)
			.pipe(autoprefixer(['last 2 version', '> 1%']))
			.pipe(lineec())
			.pipe(
				rename({
					basename: style.file
				})
			)
			.pipe(gulp.dest(style.destination))
			.pipe(filter('**/*.css'))
			.pipe(mmq({log: true}))
			.pipe(
				notify({
					message: '\n\n✅  ===> CSS - ' + style.name + ' Expanded — completed!\n',
					onLast: true
				})
			);
	});

	return taskName;
});
 
/**
 * Task: StylesMin.
 */
const styleMinTasks = styles.map((style) => {
	const taskName = style.name + 'StyleMinTask';

	gulp.task(taskName, () => {
		return gulp
			.src(style.src, {allowEmpty: true})
			.pipe(plumber(errorHandler))
			.pipe(
				sass({
					errLogToConsole: true,
					outputStyle: 'compressed',
					precision: 10
				})
			)
			.on('error', sass.logError)
			.pipe(autoprefixer(['last 2 version', '> 1%']))
			.pipe(lineec())
			.pipe(
				rename({
					basename: style.file,
					suffix: '.min'
				})
			)
			.pipe(gulp.dest(style.destination))
			.pipe(filter('**/*.css'))
			.pipe(mmq({log: true}))
			.pipe(
				notify({
					message: '\n\n✅  ===> CSS - ' + style.name + ' Minified — completed!\n',
					onLast: true
				})
			);
	});

	return taskName;
});

/**
 * Task: Scripts.
 */
const scriptTasks = scripts.map((script) => {
	const taskName = script.name + 'ScriptTask';

	gulp.task(taskName, () => {
		return gulp
			.src(script.src, {since: gulp.lastRun(taskName)})
			// .pipe(newer(script.destination))
			.pipe(plumber(errorHandler))
			.pipe(
				babel({
					presets: [
						[
							'@babel/preset-env',
							{
								targets: {browsers: ['last 2 version', '> 1%']}
							}
						]
					]
				})
			)
			.pipe(remember(script.src))
			.pipe(concat(script.file + '.js'))
			.pipe(lineec())
			.pipe(gulp.dest(script.destination))
			.pipe(
				rename({
					basename: script.file,
					suffix: '.min'
				})
			)
			.pipe(uglify())
			.pipe(lineec())
			.pipe(gulp.dest(script.destination))
			.pipe(
				notify({
					message: '\n\n✅  ===> JS - ' + script.name + ' — completed!\n',
					onLast: true
				})
			);
	});

	return taskName;
});

/**
 * Watch Tasks.
 */
gulp.task(
	'default',
	gulp.parallel(
        ...styleTasks,
		...styleMinTasks,
		...scriptTasks, () => {

		// Global.
		// gulp.watch(config.watchPhp);
		
		// Styles.
		for (const style of styles) {
			gulp.watch(watchStyles, gulp.parallel(style.name + 'StyleTask'));
			gulp.watch(watchStyles, gulp.parallel(style.name + 'StyleMinTask'));
		}

		// Scripts.
		for (const script of scripts) {
			gulp.watch(watchScripts, gulp.series(script.name + 'ScriptTask'));
		}

	})
);

/**
 * Production Tasks.
 *
 * Compile all assets files and exit.
 */
gulp.task( 'production', gulp.parallel( ...styleTasks, ...styleMinTasks, ...scriptTasks ) );
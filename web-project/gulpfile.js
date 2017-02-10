var gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    less = require('gulp-less'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    nittro = require('gulp-nittro');

var options = {
    vendor: {
        js: [
            // libraries from other vendors, such as jQuery...
        ],
        css: [

        ]
    },

    // Nittro components to include. Note that dependencies are
    // added automatically, so if you ask for e.g. the "page"
    // component, the "core" and "ajax" packages will be added
    // automatically.
    base: {
        core: true,
        datetime: true,
        neon: true,
        di: true,
        forms: true, // note that including the forms component
            // will automatically include the netteForms.js asset
        ajax: true,
        page: true,
        flashes: true,
        routing: true
    },
    extras: {
        checklist: true,
        storage: true,
        dialogs: true,
        confirm: true,
        keymap: true,
        dropzone: true,
        paginator: true
    },

    // other libraries to include after Nittro, e.g. your site's
    // proprietary libraries and styles
    libraries: {
        js: [

        ],
        css: [
            './node_modules/bootstrap/dist/css/bootstrap.css'
        ]
    },
    bootstrap: true, // true = generated bootstrap, otherwise provide a path
    stack: true // include the _stack library
};

var builder = new nittro.Builder(options);


gulp.task('js', function() {
	return nittro('js', builder)
        .pipe(sourcemaps.init())
		.pipe(concat('nittro.min.js'))
		.pipe(uglify({mangle: false}))
        .pipe(sourcemaps.write('.'))
		.pipe(gulp.dest('www/js/'));
});

gulp.task('css', function() {
    return nittro('css', builder)
        .pipe(sourcemaps.init())
        .pipe(less({compress: true}))
        .pipe(concat('nittro.min.css'))
        .pipe(postcss([ autoprefixer() ]))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('www/css/'));
});

gulp.task('fonts', function () {
    return gulp.src('./node_modules/bootstrap/dist/fonts/*')
        .pipe(gulp.dest('www/fonts/'));
});

gulp.task('default', ['js', 'css', 'fonts']);

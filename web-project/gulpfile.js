var gulp = require('gulp'),
    less = require('gulp-less'),
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
        storage: true,
        routing: true
    },
    extras: {
        flashes: true,
        dialogs: true,
        confirm: true,
        dropzone: true,
        paginator: true
    },

    // other libraries to include after Nittro, e.g. your site's
    // proprietary libraries and styles
    libraries: {
        js: [

        ],
        css: [
            './bower_components/bootstrap/dist/css/bootstrap.css'
        ]
    },
    bootstrap: true, // true = generated bootstrap, otherwise provide a path
    stack: true // include the _stack library
};

var builder = new nittro.Builder(options);


gulp.task('js', function() {
	return nittro('js', builder)
		.pipe(concat('nittro-full.min.js'))
		.pipe(uglify({mangle: false}))
		.pipe(gulp.dest('www/js/'));
});

gulp.task('js-dev', function() {
    return nittro('js', builder)
        .pipe(concat('nittro-full.js'))
        .pipe(gulp.dest('www/js/'));
});


gulp.task('css', function() {
    return nittro('css', builder)
        .pipe(less())
        .pipe(concat('nittro-full.css'))
        .pipe(gulp.dest('www/css/'));
});

gulp.task('fonts', function () {
    return gulp.src('./bower_components/bootstrap/dist/fonts/*')
        .pipe(gulp.dest('www/fonts/'));
});

gulp.task('default', ['js-dev', 'css', 'fonts']);

var gulp = require('gulp');
var less = require('gulp-less');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var changed = require('gulp-changed');
var minifycss = require('gulp-minify-css');
var rename = require('gulp-rename');
var jshint = require('gulp-jshint');
var argv = require('yargs').argv;

var paths = {
    scripts: 'public/source/js/*.js',
    styles: 'public/source/less/*.less'
};

var themeBasePath = 'public/assets/themes/';

var themePaths = {
    darkness: {
        less: themeBasePath + 'Darkness/less/*.less',
        build: themeBasePath + 'Darkness'
    },
    halloween: {
        less: themeBasePath + 'Halloween/less/*.less',
        build: themeBasePath + 'Halloween'
    },
    sky: {
        less: themeBasePath + 'Sky/less/*.less',
        build: themeBasePath + 'Sky'
    },
};

function minifyLess(src, dest) {
    gulp.src(src)
       .pipe(sourcemaps.init())
       .pipe(less())
       .pipe(minifycss())
       .pipe(rename({extname: ".min.css"}))
       .pipe(sourcemaps.write('maps'))
       .pipe(gulp.dest(dest));
}

function minifyTheme(theme) {
    minifyLess(themePaths[theme].less, themePaths[theme].build)
}

gulp.task('less', function() {
    minifyLess(paths.styles, 'public/build/css');
});

gulp.task('themes', function() {
    // Compile single theme
    if (argv.t) {
        minifyTheme(argv.t);
        return;
    }

    // Compile all the themes
    for (var theme in themePaths) {
        if (themePaths.hasOwnProperty(theme)) {
            minifyTheme(theme);
        }
    }
});

gulp.task('scripts', function() {
   gulp.src(paths.scripts)
       .pipe(jshint())
       .pipe(jshint.reporter('default'));

   gulp.src(paths.scripts)
       .pipe(sourcemaps.init())
       .pipe(uglify())
       .pipe(rename({extname: ".min.js"}))
       .pipe(sourcemaps.write('maps'))
       .pipe(gulp.dest('public/build/js'));
});

gulp.task('changedStyles', function() {
    gulp.src(paths.styles)
       .pipe(changed('public/build/css'))
       .pipe(sourcemaps.init())
       .pipe(less())
       .pipe(minifycss())
       .pipe(rename({extname: ".min.css"}))
       .pipe(sourcemaps.write('maps'))
       .pipe(gulp.dest('public/build/css'));
});

gulp.task('watch', function() {
    gulp.watch(paths.scripts, ['scripts']);
    gulp.watch(paths.styles, ['changedStyles']);
});

gulp.task('default', ['scripts', 'less']);

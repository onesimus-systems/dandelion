var gulp = require('gulp');
var less = require('gulp-less');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var minifycss = require('gulp-minify-css');
var rename = require('gulp-rename');
var ts = require('gulp-typescript');

var srcPaths = {
    typescript: [
      'public/source/ts/*.ts',
      'public/source/dts/*.ts'
    ],
    themes: {
      modern: 'public/assets/themes/modern/less/*.less',
      legacy: 'public/assets/themes/legacy/less/*.less'
    }
};

var destPaths = {
    scripts: 'public/build/js',
    themes: {
      modern: 'public/assets/themes/modern',
      legacy: 'public/assets/themes/legacy'
    }
};

var tsProject = ts.createProject({
    declarationFiles: true,
    noExternalResolve: true,
    target: "es5",
    module: "commonjs",
    declaration: false,
    noImplicitAny: false,
    removeComments: true
});

function minifyLess(src, dest) {
    gulp.src(src)
       .pipe(sourcemaps.init())
       .pipe(less())
       .pipe(minifycss())
       .pipe(rename({extname: ".min.css"}))
       .pipe(sourcemaps.write('maps'))
       .pipe(gulp.dest(dest));
}

gulp.task('typescript', function() {
    var tsResult = gulp.src(srcPaths.typescript)
                       .pipe(sourcemaps.init())
                       .pipe(ts(tsProject, undefined, 'defaultReporter'));

    return tsResult.js
                   .pipe(uglify())
                   .pipe(rename({extname: ".min.js"}))
                   .pipe(sourcemaps.write('maps'))
                   .pipe(gulp.dest(destPaths.scripts));
});

gulp.task('themes', function() {
    for (var theme in srcPaths.themes) {
      minifyLess(srcPaths.themes[theme], destPaths.themes[theme]);
    }
});

gulp.task('watch', function() {
    gulp.watch(srcPaths.typescript, ['typescript']);
    gulp.watch(srcPaths.themes.modern, ['themes']);
    gulp.watch(srcPaths.themes.legacy, ['themes']);
});

gulp.task('default', ['typescript', 'themes']);
gulp.task('do-watch', ['default', 'watch']);

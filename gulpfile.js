var gulp = require('gulp');
var less = require('gulp-less');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var changed = require('gulp-changed');
var minifycss = require('gulp-minify-css');
var rename = require('gulp-rename');
var ts = require('gulp-typescript');

var srcPaths = {
    typescript: [
        'public/source/ts/*.ts',
        'public/source/dts/*.ts'
    ],
    styles: 'public/source/less/*.less'
};

var destPaths = {
    scripts: 'public/build/js',
    styles: 'public/build/css'
};

var tsProject = ts.createProject({
    declarationFiles: true,
    noExternalResolve: true
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
                   //.pipe(uglify())
                   .pipe(rename({extname: ".min.js"}))
                   .pipe(sourcemaps.write('maps'))
                   .pipe(gulp.dest(destPaths.scripts));
});

gulp.task('styles', function() {
    minifyLess(srcPaths.styles, destPaths.styles);
});

gulp.task('changedStyles', function() {
    gulp.src(srcPaths.styles)
       .pipe(changed(destPaths.styles))
       .pipe(sourcemaps.init())
       .pipe(less())
       .pipe(minifycss())
       .pipe(rename({extname: ".min.css"}))
       .pipe(sourcemaps.write('maps'))
       .pipe(gulp.dest(destPaths.styles));
});

gulp.task('watch', function() {
    gulp.watch(srcPaths.typescript, ['typescript']);
    gulp.watch(srcPaths.typescript, ['typescript']);
    gulp.watch(srcPaths.styles, ['changedStyles']);
});

gulp.task('default', ['typescript', 'styles']);
gulp.task('do-watch', ['default', 'watch']);

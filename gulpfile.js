var gulp = require('gulp');
var less = require('gulp-less');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sourcemaps = require('gulp-sourcemaps');
var changed = require('gulp-changed');
var minifycss = require('gulp-minify-css');
var rename = require('gulp-rename');

var paths = {
    scripts: 'public/source/js/*.js',
    styles: 'public/source/less/*.less'
};

gulp.task('less', function() {
   gulp.src(paths.styles)
       .pipe(less())
       .pipe(minifycss())
       .pipe(rename({extname: ".min.css"}))
       .pipe(gulp.dest('public/build/css'));
});

gulp.task('scripts', function() {
   gulp.src(paths.scripts)
       .pipe(sourcemaps.init())
       .pipe(uglify())
       .pipe(concat('javascript.min.js'))
       .pipe(sourcemaps.write('maps'))
       .pipe(gulp.dest('public/build/js'));
});

gulp.task('changedStyles', function() {
   return gulp.src(paths.styles)
       .pipe(changed('public/build/css'))
       .pipe(less())
       .pipe(gulp.dest('public/build/css'));
});

gulp.task('watch', function() {
    gulp.watch(paths.scripts, ['scripts']);
    gulp.watch(paths.styles, ['changedStyles']);
});

gulp.task('default', ['scripts', 'less']);

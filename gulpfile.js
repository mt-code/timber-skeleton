var IS_DEBUG = true;
var gulp = require('gulp');

var	concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    minifyCSS = require('gulp-minify-css'),
    autoprefixer = require('gulp-autoprefixer'),
    browserify = require('browserify'),
    uglify = require('gulp-uglify'),
    babelify = require('babelify'),
    source = require('vinyl-source-stream');

var paths = {
    src : {
        css: ['./assets/css/main.css'],
        js: ['./assets/js/main.js']
    },
    dst : {
        js: './assets/js/build',
        css: './assets/css/build',
    }
};

gulp.task('js:build', function() {
    return browserify({
        entries: paths.src.js,
        debug: IS_DEBUG
    })
        .transform('babelify', {
            presets: ['@babel/preset-env']
        })
        .bundle()
        .pipe(source('main.js'))
        .pipe(gulp.dest(paths.dst.js));
});

gulp.task('js:compile', function() {
    return gulp.src(`${paths.dst.js}/main.js`)
        .pipe(rename('main.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.dst.js));
});

gulp.task('css:build', () => {
    return gulp.src(paths.src.css)
        .pipe(concat('main.css'))
        .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9'))
        .pipe(rename('main.css'))
        .pipe(gulp.dest(paths.dst.css));
});

gulp.task('css:compile', () => {
    return gulp.src(`${paths.dst.css}/main.css`)
        .pipe(minifyCSS())
        .pipe(rename('main.min.css'))
        .pipe(gulp.dest(paths.dst.css));
});

gulp.task('watch', function() {
    gulp.watch(['./assets/css/main.css'], gulp.series('css:build', 'css:compile'));
    gulp.watch(['./assets/js/main.js', './assets/js/scripts/*.js'], gulp.series('js:build', 'js:compile'));
});

gulp.task('default', gulp.series(
    'js:build',
    'js:compile',
    'css:build',
    'css:compile',
    'watch'
));
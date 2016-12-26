'use strict';

var gulp            = require('gulp'),
    sass            = require('gulp-sass'),
    browserSync     = require('browser-sync'),
    concat          = require('gulp-concat'),
    uglify          = require('gulp-uglifyjs'),
    cssnano			= require('gulp-cssnano'),
    rename			= require('gulp-rename'),
    del				= require('del'),
    imagemin		= require('gulp-imagemin'),
    pngquant		= require('imagemin-pngquant'),
    cache			= require('gulp-cache'),
    autoprefixer 	= require('gulp-autoprefixer'),
    sourcemaps      = require('gulp-sourcemaps'),
    minify_css = require('gulp-minify-css');

// source and distribution folder
var source = 'src/',
    dest = 'dist/';

// Bootstrap scss source
var bootstrapSass = {
    in: source + 'vendor/bootstrap-sass'
};

var path = {
    dest: {
        html: 'dest/',
        js: 'dest/js/',
        css: 'dest/css/',
        img: 'dest/img/',
        fonts: 'dest/fonts/'
    },
    src: {
        html: 'src/*.html',
        js: 'src/js/main.js',
        sass: 'src/sass/main.scss',
        css: 'src/css/',
        img: 'src/img/**/*.*',
        fonts: 'src/fonts/**/*.*'
    },
    watch: {
        html: 'src/**/*.html',
        js: 'src/js/**/*.js',
        sass: 'src/sass/**/*.scss',
        img: 'src/img/**/*.*',
        fonts: [source + 'fonts/*.*', bootstrapSass.in + 'assets/fonts/**/*']
    },
    clean: './dest'
};



// Bootstrap fonts source
var fonts = {
    in: [ bootstrapSass.in + 'fonts/**/*'],
    out: dest + 'fonts/'
};

var vendorJs = {
    in: [
        'src/vendor/jquery/dist/jquery.min.js',
        'src/vendor/scrollreveal/dist/scrollreveal.min.js',
        bootstrapSass.in + 'javascripts/bootstrap.min.js'
    ]
}
var jquery = {
    in: 'src/vendor/jquery/dist/jquery.min.js'
};

gulp.task('fonts', function() {
    return gulp.src([
        fonts.in
    ])
        .pipe(gulp.dest(fonts.out));
});


gulp.task('sass', function() {
    return gulp.src(path.watch.sass)
        .pipe(sass({
            includePaths: [
                bootstrapSass.in + '/assets/stylesheets'
                //'./node_modules/bootstrap/dist/js/bootstrap.min.js'
            ]
        }).on('error', sass.logError))
        .pipe(autoprefixer(['last 15 versions', '> 1%', 'ie 8', 'ie 7'], { cascade: true}))
        .pipe(minify_css({compatibility: 'ie8'}))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(path.src.css))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task('browser-sync', function() {
    browserSync({
        server: {
            baseDir: 'src'
        },
        notify: false
    });
});

gulp.task('scripts', function() {
    return gulp.src(
        vendorJs.in
    )
        .pipe(concat('lib.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(source + 'js'));
});

gulp.task('css-libs', ['sass'], function() {
    return gulp.src([
        path.src.css + '/main.css'
    ])
        .pipe(cssnano())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(source + 'css'));
});

gulp.task('clean', function() {
    return del.sync('dist');
});

gulp.task('clear', function() {
    return cache.clearAll();
});

gulp.task('img', function() {
    return gulp.src(path.src.img)
        .pipe(cache(imagemin({
            interlaced: true,
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        })))
        .pipe(gulp.dest(path.dest.img));
});

gulp.task('watch', ['browser-sync', 'scripts', 'css-libs', ], function() {
    gulp.watch(path.watch.sass, ['sass']);
    gulp.watch(path.watch.html, browserSync.reload);
    gulp.watch(path.watch.js, browserSync.reload);
});

gulp.task('build', ['clean', 'img', 'sass', 'fonts', 'scripts'], function() {

    var buildCss = gulp.src([
        path.src.css + 'main.css'
        // source + 'css/libs.min.css'
    ])
        .pipe(gulp.dest(path.dest.css));

    var buildFonts = gulp.src(path.src.fonts)
        .pipe(gulp.dest(path.dest.fonts));

    var buildJs = gulp.src(path.watch.js)
        .pipe(gulp.dest(path.dest.js));

    var buildHtml = gulp.src(source + '*.html')
        .pipe(gulp.dest(dest));

});

gulp.task('default', ['watch']);
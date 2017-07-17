var gulp = require('gulp'),
    sass = require('gulp-sass'),
    browserSync     = require('browser-sync'),
    connectPHP = require('gulp-connect-php'),
    autoprefixer 	= require('gulp-autoprefixer'),
    sourcemaps      = require('gulp-sourcemaps'),
    minify_css = require('gulp-minify-css'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify');

var reload      = browserSync.reload;

var paths = {
    html:['views/**/*.php', 'modules/**/*.php', 'components/**/*.php'],
    css:['gulp/sass/**/*.scss'],
    bootstrap: {
        sass: ['gulp/libs/bootstrap-sass/assets/stylesheets']
    },
    js: {
        src: 'gulp/js/**/*.js',
        dest: 'web/js'
    }
};


// -----------------------------------------------------------------------------
// Sass
// Compile Sass Files and autoprefix css
// -----------------------------------------------------------------------------
gulp.task('sass', function() {
    return gulp.src(paths.css)
        .pipe(sass({
            includePaths: [
                paths.bootstrap.sass
            ]
        }).on('error', sass.logError))
        .pipe(autoprefixer(['last 15 versions', '> 1%', 'ie 8', 'ie 7'], { cascade: true}))
        .pipe(minify_css({compatibility: 'ie8'}))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/css'))
        .pipe(reload({stream: true}));
});

// gulp.task('browser-sync', function() {
//     browserSync({
//         proxy:'127.0.0.1',
//         port:7000,
//         notify: false
//     });
// });

gulp.task('scripts', function() {
    return gulp.src(paths.js.src)
        .pipe(concat('app.js'))
        .pipe(gulp.dest(paths.js.dest))
        .pipe(rename('app.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(paths.js.dest))
        .pipe(reload({stream: true}));
});

gulp.task('php', function(){
    connectPHP.server({ base: 'web/', keepalive:true, hostname: 'localhost', port:7000, open: false}, function () {
        browserSync({
            port: 7001,
            proxy:'127.0.0.1'
        });
    });
});

gulp.task('reload', function() {
    browserSync.reload();
    browserSync.stream();
});

gulp.task('html', function(){
    gulp.src(paths.html)
        .pipe(reload({stream:true}));
});

gulp.task('watch', function () {
    gulp.watch(paths.css, ['sass', 'reload']);
    gulp.watch(paths.html, ['html', 'reload']);
    gulp.watch(paths.js.src, ['scripts', 'reload']);
});
gulp.task('default', ['watch', 'php']);
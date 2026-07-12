const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const autoprefixer = require("gulp-autoprefixer");
const cleanCSS = require("gulp-clean-css");
const uglify = require("gulp-uglify");
const concat = require("gulp-concat");
const imagemin = require("gulp-imagemin");
const webp = require("gulp-webp");
const plumber = require("gulp-plumber");
const rename = require("gulp-rename");
const sourcemaps = require("gulp-sourcemaps"); 

// Paths
const paths = {
  scss: "assets/scss/style.scss", // Main SCSS file
  css: "assets/css/", // Compiled CSS output
  js: "assets/js/main.js", // Main JS file
  jsDest: "assets/js/", // Minified JS output
  images: "assets/images/**/*.{png,jpg,jpeg,gif,svg}",
  imagesDest: "assets/images/",
};

// Compile SCSS
function styles() {
  return gulp
    .src(paths.scss)
    .pipe(plumber())
    .pipe(sourcemaps.init()) // Initialize sourcemaps before processing
    .pipe(sass().on("error", sass.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS())
    .pipe(rename({ suffix: ".min" })) // Minify and rename the CSS
    // .pipe(sourcemaps.write(".", { sourceMappingURL: "style.css.map" })) // Write source map with desired name
    .pipe(gulp.dest(paths.css));
}

// Minify JS
function scripts() {
  return gulp
    .src(paths.js)
    .pipe(plumber())
    .pipe(rename({ suffix: ".min" }))
    .pipe(uglify())
    .pipe(gulp.dest(paths.jsDest));
}

// Optimize Images
function images() {
  return gulp
    .src(paths.images)
    .pipe(imagemin())
    .pipe(gulp.dest(paths.imagesDest))
    .pipe(webp())
    .pipe(gulp.dest(paths.imagesDest));
}

// Watch Files
function watchFiles() {
  gulp.watch(["assets/scss/**/*.scss", "includes/flexible/layouts/**/*.scss"], styles);  // Watches theme SCSS and flexible layout SCSS
  gulp.watch("assets/js/**/*.js", scripts);     // Watches all JS files in the assets/js directory and subdirectories
  gulp.watch("**/*.php", function(done) {       // Watches PHP files in the root directory
    done();
  });
}

// Define Gulp Tasks
exports.styles = styles;
exports.scripts = scripts;
exports.images = images;
exports.watch = gulp.series(styles, scripts, images, watchFiles);
exports.default = exports.watch;

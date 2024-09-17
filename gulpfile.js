const gulp = require('gulp');
const concat = require('gulp-concat');
const minify = require('gulp-minify');
const sass = require('gulp-sass');
sass.compiler = require('node-sass');

gulp.task('scss', function () {
  return gulp.src('./src/com_tjvendors/media/scss/style.css')
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(gulp.dest('./src/com_tjvendors/media/css'));
});

gulp.task('js', async function() {
  gulp.src(['./src/com_tjvendors/media/ts/tjvendors.js'])
    .pipe(concat('script.js'))
    .pipe(minify())
    .pipe(gulp.dest('./src/com_tjvendors/media/js'))
});

gulp.task('watch', function() {
	gulp.watch('./src/com_tjvendors/media/scss/**/*.scss', gulp.series('scss'));
  	
  	gulp.watch('./src/com_tjvendors/media/ts/*.js', gulp.series('js'));
});

gulp.task('default', gulp.parallel(['scss','js','watch']));

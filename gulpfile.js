const { src, dest, watch, series, parallel } = require("gulp");
const del = require("del");
const sass = require("gulp-sass");
const autoprefixer = require("gulp-autoprefixer");
const sourcemaps = require("gulp-sourcemaps");
const uglifyjs = require("gulp-uglify-es").default;
const rename = require('gulp-rename');
const babel = require('gulp-babel');

// 기존 css 파일 삭제
function clean() {
	return del(["./dist/css/*.css", "./dist/js/*.js"]);
}

// sass 작업 admin
function sass_admin() {
	return (
		src("./src/scss/admin/*.scss")
			.pipe(sourcemaps.init())
			.pipe(
				sass({ outputStyle: "compressed" }).on("error", sass.logError)
			)
			.pipe(
				autoprefixer({
					cascade: false
				})
			)
			.pipe(sourcemaps.write())
			.pipe(dest("./dist/css"))
	);
}

// seller, partner용 scss
function sass_mypage() {
	return (
		src("./src/scss/mypage/*.scss")
			.pipe(sourcemaps.init())
			.pipe(
				sass({ outputStyle: "compressed" }).on("error", sass.logError)
			)
			.pipe(
				autoprefixer({
					cascade: false
				})
			)
			.pipe(sourcemaps.write())
			.pipe(dest("./dist/css"))
	);
}

// sass 작업 web
function sass_web() {
	return (
		src("./src/scss/pc/*.scss")
			.pipe(sourcemaps.init())
			.pipe(
				sass({ outputStyle: "compressed" }).on("error", sass.logError)
			)
			.pipe(
				autoprefixer({
					cascade: false
				})
			)
			.pipe(sourcemaps.write())
			.pipe(dest("./dist/css"))
	);
}

// sass 작업 web
function sass_mobile() {
	return (
		src("./src/scss/mobile/*.scss")
			.pipe(sourcemaps.init())
			.pipe(
				sass({ outputStyle: "compressed" }).on("error", sass.logError)
			)
			.pipe(
				autoprefixer({
					cascade: false
				})
			)
			.pipe(sourcemaps.write())
			.pipe(dest("./dist/css"))
	);
}

// uglifyjs
function uglify() {
	return src("./src/js/*.js")
		.pipe(uglifyjs())
		.pipe(dest("./dist/js"));
}

// copy
function copy() {
	return src("./src/js/*.js").
	pipe(dest("./dist/js"));
}

function babelrc() {
	return src("./src/js/*.js").
	pipe(babel()).
	pipe(dest("./dist/js"));
}

function babel_pc() {
	return src("./src/js/pc/*.js").
	pipe(babel()).
	pipe(rename({suffix: "_pc"})).
	pipe(dest("./dist/js"));
}

function babel_mobile() {
	return src("./src/js/mobile/*.js").
	pipe(babel()).
	pipe(rename({suffix: "_m"})).
	pipe(dest("./dist/js"));
}

// watch
function g_watch() {
	watch(["./src/scss/common/*.scss", "./src/scss/admin/*.scss"], sass_admin);
	watch(["./src/scss/common/*.scss", "./src/scss/mypage/*.scss"], sass_mypage);
	watch(["./src/scss/common/*.scss", "./src/scss/pc/*.scss"], sass_web);
	watch(["./src/scss/common/*.scss", "./src/scss/mobile/*.scss"], sass_mobile);
	watch("./src/js/*.js", babelrc);
	watch("./src/pc/js/*.js", babel_pc);
	watch("./src/mobile/js/*.js", babel_mobile);
}

// gulp 작업
const g_work = parallel(sass_admin, sass_mypage, sass_web, sass_mobile, babelrc, babel_pc, babel_mobile);

// devlelop css
// 기존 css 파일 삭제 후 sass
const dev = series(clean, g_work);

// gulp default
// 작업 후 watch
exports.default = series(dev, g_watch);
'use strict';

const sass = require('gulp-sass')(require('sass'));
const { dest, watch, series, src } = require('gulp');

exports.build = function () {
    return src('./scss/style.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(dest('./'));
}

exports.watch = function () {
    watch('./scss/**/*.scss', series('build'));
}

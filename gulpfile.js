var gulp = require('./gulp/loader')(['release']);

var phpspecTasks = require("gulp-cm-phpspec-tasks");

phpspecTasks.addTasks(gulp, 'CubicMushroom\\Payments\\Stripe\\', {bin: 'vendor/bin/phpspec'});

/**
 * Test task
 *
 * This should run each of the relevant test tasks
 */
gulp.task('test', ['phpspec']);
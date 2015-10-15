var gulp         = require('gulp'),
    phpspecTasks = require("gulp-cm-phpspec-tasks"),
    versionTasks = require('./gulp/version-tasks');


phpspecTasks.addTasks(gulp, 'CubicMushroom\\Payments\\Stripe\\', {bin: 'vendor/bin/phpspec'});

/**
 * Test task
 *
 * This should run each of the relevant test tasks
 */
gulp.task('test', ['phpspec']);

/**
 * Versioning tasks
 */
versionTasks.addTasks();
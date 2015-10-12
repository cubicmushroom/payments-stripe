var gulp    = require('gulp'),
  phpspecTasks = require("gulp-cm-phpspec-tasks");


phpspecTasks.addTasks(gulp, 'CubicMushroom\\Payments\\Stripe\\',{bin: 'vendor/bin/phpspec'});
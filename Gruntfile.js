module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    jshint: {
      all: ['app/static/js/*.js']
    },

    less: {
      dev: {
        files: [{
          expand: true,
          cwd: 'app/static/styles/less',
          src: ['*.less'],
          dest: 'app/static/styles/css/',
          ext: '.css'
        }]
      }
    },

    watch: {
      scripts: {
        files: ['app/static/js/*.js'],
        tasks: ['newer:jshint:all']
      },
      styles: {
        files: ['app/static/styles/less/*.less'],
        tasks: ['newer:less:dev']
      }
    }
  });

  grunt.event.on('watch', function(action, filepath, target) {
    grunt.log.writeln('\n' + target + ': ' + filepath + ' has ' + action);
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-newer');

  grunt.registerTask('default', ['jshint', 'less']);

};
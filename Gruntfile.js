module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    jshint: {
      all: ['static/js/*.js']
    },

    less: {
      dev: {
        files: [{
          expand: true,
          cwd: 'static/styles/less',
          src: ['*.less'],
          dest: 'static/styles/',
          ext: '.css'
        }]
      }
    },

    watch: {
      scripts: {
        files: ['static/js/*.js'],
        tasks: ['newer:jshint:all']
      },
      styles: {
        files: ['static/styles/less/*.less'],
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
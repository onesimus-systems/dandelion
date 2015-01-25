module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    jshint: {
      all: ['public/assets/js/*.js']
    },

    less: {
      dev: {
        files: [{
          expand: true,
          cwd: 'public/assets/styles/less',
          src: ['*.less'],
          dest: 'public/assets/styles/css/',
          ext: '.css'
        }]
      }
    },

    watch: {
      scripts: {
        files: ['public/assets/js/*.js'],
        tasks: ['newer:jshint:all']
      },
      styles: {
        files: ['public/assets/styles/less/*.less'],
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

module.exports = function(grunt) {
    'use strict';

    require('time-grunt')(grunt);

    grunt.initConfig({
        clean: {
            dist: [
                'dist',
                'src/AppBundle/Resources/public/css',
                'src/AppBundle/Resources/public/js',
                'src/AdminBundle/Resources/public/css',
                'src/AdminBundle/Resources/public/js'
            ]
        },
        sass: {
            options: {
                sourcemap: 'inline',
                cacheLocation: 'vendor/.sass-cache'
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: 'src/AppBundle/Resources/private/sass',
                    src: ['*.scss'],
                    dest: 'dist/app/css',
                    ext: '.css'
                }, {
                    expand: true,
                    cwd: 'src/AdminBundle/Resources/private/sass',
                    src: ['*.scss'],
                    dest: 'dist/admin/css',
                    ext: '.css'
                }]
            }
        },
        cssmin: {
            dist: {
                files: [{
                    expand: true,
                    cwd: 'dist/app/css',
                    src: ['*.css', '!*.min.css'],
                    dest: 'dist/app/css',
                    ext: '.min.css'
                }, {
                    expand: true,
                    cwd: 'dist/admin/css',
                    src: ['*.css', '!*.min.css'],
                    dest: 'dist/admin/css',
                    ext: '.min.css'
                }]
            }
        },
        copy: {
            dist: {
                files: [
                    {
                        expand: true,
                        cwd: 'dist/app/css',
                        src: '*.min.css',
                        dest: 'src/AppBundle/Resources/public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'dist/app/js',
                        src: '*.min.js',
                        dest: 'src/AppBundle/Resources/public/js/'
                    },
                    {
                        expand: true,
                        cwd: 'dist/admin/css',
                        src: '*.min.css',
                        dest: 'src/AdminBundle/Resources/public/css/'
                    },
                    {
                        expand: true,
                        cwd: 'dist/admin/js',
                        src: '*.min.js',
                        dest: 'src/AdminBundle/Resources/public/js/'
                    }
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-coffee');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default', ['clean', 'sass', 'cssmin', 'copy']);
};

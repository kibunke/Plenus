module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        bowercopy: {
            options: {
                srcPrefix: 'bower_components',
                destPrefix: 'web/assets'
            },
            scripts: {
                files: {
                    'js/jquery.js': 'jquery/dist/jquery.js',
                    'js/bootstrap.js': 'bootstrap/dist/js/bootstrap.js',
                    'js/blockUI.js': 'blockUI/jquery.blockUI.js',
                    'js/data-tables.js': 'datatables.net/js/jquery.dataTables.js',
                    'js/data-tables.bootstrap.js': 'datatables.net-bs/js/dataTables.bootstrap.js',
                    'js/bootstrap-datetimepicker.js': 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                    'js/jquery.mask.js': 'jquery-mask-plugin/dist/jquery.mask.js',
                    'js/moment.js': 'moment/min/moment.min.js',
                    'js/moment-locale-es.js': 'moment/locale/es.js',
                    'js/perfect-scrollbar.jquery.js': 'perfect-scrollbar/js/perfect-scrollbar.jquery.js',
                    'js/toastr.js': 'toastr/toastr.js',
                    'js/velocity.js': 'velocity/velocity.js',
                    'js/scrollTo.js': 'jquery.scrollTo/jquery.scrollTo.js',
                    'js/sweetalert.js': 'sweetalert/dist/sweetalert.min.js',
                    'js/select2.js': 'select2/dist/js/select2.min.js',
                    'js/highcharts.js': 'highcharts/js/highcharts.js',
                    'js/highstock.js': 'highcharts/js/highstock.js',
                    'js/jquery.easypiechart.min.js': 'jquery.easy-pie-chart/dist/jquery.easypiechart.min.js',
                    'js/fileSaver.min.js': 'file-saverjs/FileSaver.min.js',
                    'js/tableexport.min.js': 'tableexport.js/dist/js/tableexport.min.js'
                }
            },
            stylesheets: {
                files: {
                    'css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css',
                    'css/font-awesome.css': 'font-awesome/css/font-awesome.css',
                    'css/bootstrap-datetimepicker.min.css': 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
                    'css/perfect-scrollbar.css': 'perfect-scrollbar/css/perfect-scrollbar.css',
                    'css/data-tables.bootstrap.css': 'datatables.net-bs/css/dataTables.bootstrap.css',
                    'css/animate.min.css': 'animate.css/animate.min.css',
                    'css/toastr.min.css': 'toastr/toastr.min.css',
                    'css/sweetalert.css': 'sweetalert/dist/sweetalert.css',
                    'css/select2.css': 'select2/dist/css/select2.min.css',
                    'css/highcharts.css': 'highcharts/css/highcharts.css'
                }
            },
            fonts: {
                files: {
                    'fonts': 'font-awesome/fonts'
                }
            }
        },
        cssmin : {
            bundled:{
                src: 'web/assets/css/bundled.css',
                dest: 'web/assets/css/bundled.min.css'
            },
            login:{
                src: 'web/assets/css/login.css',
                dest: 'web/assets/css/login.min.css'
            }
        },
        uglify : {
            js: {
                files: {
                    'web/assets/js/bundled.min.js': ['web/assets/js/bundled.js'],
                    'web/assets/js/login.min.js': ['web/assets/js/login.js']
                }
            }
        },
        concat: {
            options: {
                stripBanners: true
            },
            coreCSS: {
                src: [
                    'web/assets/css/bootstrap.css',
                    'web/assets/css/font-awesome.css',
                    'web/assets/css/bootstrap-datetimepicker.min.css',
                    'web/assets/css/perfect-scrollbar.css',
                    'web/assets/css/data-tables.bootstrap.css',
                    'web/assets/css/animate.min.css',
                    'web/assets/css/toastr.min.css',
                    'web/assets/css/sweetalert.css',
                    'web/assets/css/select2.css',
                    'src/CommonBundle/Resources/public/css/*.css'
                ],
                dest: 'web/assets/css/bundled.css'
            },
            coreJS : {
                src : [
                    'web/assets/js/jquery.js',
                    'web/assets/js/bootstrap.js',
                    'web/assets/js/blockUI.js',
                    'web/assets/js/data-tables.js',
                    'web/assets/js/data-tables.bootstrap.js',
                    'web/assets/js/moment.js',
                    'web/assets/js/moment-locale-es.js',
                    'web/assets/js/bootstrap-datetimepicker.js',
                    'web/assets/js/jquery.mask.js',
                    'web/assets/js/perfect-scrollbar.jquery.js',
                    'web/assets/js/toastr.js',
                    'web/assets/js/sweetalert.js',
                    'web/assets/js/velocity.js',
                    'web/assets/js/scrollTo.js',
                    'web/assets/js/select2.js',
                    //'src/CommonBundle/Resources/public/js/main.js',
                    'src/CommonBundle/Resources/public/js/*.js'
                ],
                dest: 'web/assets/js/bundled.js'
            },
            loginCSS: {
                src: [
                    'web/assets/css/bootstrap.css',
                    'web/assets/css/font-awesome.css',
                    'web/assets/css/animate.min.css',
                    'web/assets/css/toastr.min.css',
                    'web/assets/css/sweetalert.css',
                    'src/SeguridadBundle/Resources/public/css/*.css'
                ],
                dest: 'web/assets/css/login.css'
            },
            loginJS : {
                src : [
                    'web/assets/js/jquery.js',
                    'web/assets/js/bootstrap.js',
                    'web/assets/js/moment.js',
                    'web/assets/js/jquery.mask.js',
                    'web/assets/js/toastr.js',
                    'web/assets/js/sweetalert.js',
                    'src/SeguridadBundle/Resources/public/js/*.js'
                ],
                dest: 'web/assets/js/login.js'
            }
        },
        copy: {
            images: {
                expand: true,
                cwd: 'src/CommonBundle/Resources/public/images/',
                src: '**',
                dest: 'web/assets/images/'
            },
            main: {
                expand: true,
                cwd: 'src/CommonBundle/Resources/public/js/',
                src: '**',
                dest: 'web/assets/common',
              }
        }
    });

    grunt.loadNpmTasks('grunt-bowercopy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['bowercopy','copy', 'concat', 'cssmin', 'uglify']);
};

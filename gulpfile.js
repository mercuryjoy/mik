var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
	mix.sass('app.scss');

	mix.styles([
		'../bower/AdminLTE/bootstrap/css/bootstrap.css',
		'../bower/AdminLTE/plugins/datatables/dataTables.bootstrap.css',
		'../bower/AdminLTE/plugins/select2/select2.css',
		'../bower/AdminLTE/plugins/daterangepicker/daterangepicker-bs3.css',
		'../bower/AdminLTE/dist/css/AdminLTE.css',
		'../bower/AdminLTE/dist/css/skins/_all-skins.css',
		'../bower/iCheck/skins/square/blue.css',
		'../bower/font-awesome/css/font-awesome.css',
		'../bower/Ionicons/css/ionicons.css'
	], 'public/css/vendor.css');

	mix.sass([
		'../bower/AdminLTE/bootstrap/css/bootstrap.min.css',
		'../bower/font-awesome/css/font-awesome.css',
		'app.scss',
		'error.scss'
	], 'public/css/error.css');

	mix.scripts([
		'../bower/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js',
		'../bower/AdminLTE/plugins/select2/select2.js',
		'../bower/AdminLTE/plugins/select2/i18n/zh-CN.js',
		'../bower/AdminLTE/plugins/daterangepicker/moment.js',
		'../bower/AdminLTE/plugins/daterangepicker/daterangepicker.js',
		'../bower/AdminLTE/bootstrap/js/bootstrap.min.js',
		'../bower/AdminLTE/dist/js/app.min.js',
		'../bower/iCheck/icheck.js',
		'../bower/jsrender/jsrender.js',
		'../javascript/area.js'
	], 'public/js/vendor.js');

	mix.copy('resources/assets/bower/AdminLTE/dist/img', 'public/img/adminlte');
	mix.copy('resources/assets/bower/iCheck/skins/square/blue@2x.png', 'public/css/');
	mix.copy('resources/assets/bower/iCheck/skins/square/blue.png', 'public/css/');

	mix.copy('resources/assets/bower/font-awesome/fonts', 'public/fonts');
	mix.copy('resources/assets/bower/Ionicons/fonts', 'public/fonts');
	mix.copy('resources/assets/bower/Ionicons/fonts', 'public/fonts');
	mix.copy('resources/assets/bower/lato/font/lato-medium/', 'public/fonts/');
});


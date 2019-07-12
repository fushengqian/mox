var version = +new Date();
require.config({
    urlArgs: 'v=' + version, 
    baseUrl: '/static/js',
    paths: {
        'jquery': '/static/js/jquery-1.7.2.min',
        'jquery.gcjs': '/static/dist/jquery/jquery.gcjs',
        'jquery.cookie': '/static/dist/jquery/jquery.cookie',
        'tpl':'/static/dist/tmodjs',
        'foxui':'/static/dist/foxui/js/foxui.min',
        'foxui.picker':'/static/dist/foxui/js/foxui.picker.min',
        'foxui.citydata':'/static/dist/foxui/js/foxui.citydata.min',
        'foxui.citydatanew':'/static/dist/foxui/js/foxui.citydatanew.min',
        'foxui.street':'/static/dist/foxui/js/foxui.street.min',
        'jquery.qrcode':'/static/dist/jquery/jquery.qrcode.min',
        'ydb':'/static/dist/Ydb/YdbOnline',
        'swiper':'/static/dist/swiper/swiper.min',
        'jquery.fly': '/static/dist/jquery/jquery.fly',
    },
    shim: {
        'foxui':{
            deps:['jquery']
        },
        'foxui.picker': {
            exports: "foxui",
            deps: ['foxui','foxui.citydata']
        },
		'jquery.gcjs': {
	                 deps:['jquery']
		},
		'jquery.cookie': {
	                 deps:['jquery']
		},
		'jquery.fly': {
	                 deps:['jquery']
		}
    },
    waitSeconds: 0
});

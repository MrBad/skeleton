var Skeleton = (function () {

	var lang = 'ro';
	var jFiles = [
		[
			'/editMyLocation/|/addLocation/',
			[
				'/js/cities.js',
				'/js/phones.js'
			]

		], [
			'^/messages/',
			[
				'/js/messages.js'
			]
		]

	];

	var translateGet = function (frm) {
		var parts = frm.toQueryString().split('&');

		var str = '';
		var params = [];
		var values = [];
		for (var i = 0; i < parts.length; i++) {
			var sprt = (parts[i]).split('=');

			if (sprt[1]) {
				sprt[1] = sprt[1].replace(/%20/g, '+').replace(/%2F/gi, '^');
				var pos = params.indexOf(sprt[0]);
				if (pos >= 0) {
					values[pos] += '|' + sprt[1];
				} else {
					params.push(sprt[0]);
					values.push(sprt[1]);
				}
				//str+=sprt[0]+'/'+sprt[1].replace(/%2F/gi,'^')+'/'; // ugly fix stupid apache bug
			}
		}
		if (params.length != values.length) {
			if (console) {
				console.log("translateGet ERROR");
			}
			return false;
		}
		params.each(function (el, idx) {
			str += el + '/' + values[idx] + '/';
		});
		return (frm.getAttribute('action') + str);
	};

	var rUrl = function () {
		$$('.rUrl').each(function (el) {
			el.addEvent('submit', function (e) {
				e.stop();
				e.preventDefault();

				$$('input.ainput').each(function (obj) {
					if (obj.value == obj.title)
						obj.value = '';
				});
				document.location = translateGet(e.target);
			});
		});
	};

	if ($$('.calendarDate').length) {
		var style = 'datepicker_bootstrap';
		//Asset.css('/htdocs/js/datepicker/Source/'+style+'/'+style+'.css');
		jsl.load('/js/datepicker/datepicker.min.js', function () {
			var m = 12;
			var maxDate = new Date();
			maxDate.setMonth(maxDate.getMonth() + m);

			new Picker.Date($$('input.calendarDate'), {
				timePicker: false,
				minDate: new Date(),
				maxDate: maxDate,
				positionOffset: {x: 5, y: 0},
				pickerClass: style,
				useFadeInOut: !Browser.ie,
				format: '%Y-%m-%d'
			});

		});
	}

	this.loadClasses = function () {
		var path = location.pathname;
		jFiles.forEach(function (el) {

			var reg = el[0];
			var files = el[1];
			var callback = el[2];

			if (path.match(reg)) {
				files.forEach(function (file) {
					jsl.add(file);
				});
			}
		}, this);
	};


	this.loadClasses();
	rUrl();

	$$('.tapArea').addEvents({
		click: function (e) {
			var target = e.target;
			var el;
			if (target.hasClass('tapArea')) {
				el = target;
			} else {
				el = e.target.getParent('.tapArea');
			}
			var t = target.nodeName.toLowerCase();
			if (!el || t == 'a' || t == 'input' || t == 'label') {
				return;
			}
			var fa = el.getElement('a');
			if (fa) {
				var href = fa.get('href');
				if (href) {
					new Spinner(el).show();
					document.location = href;
				}
			}
		}
	});


	$('toggle_menu').addEvent('click', function (e) {
		e.stop();
		$('main_menu').toggleClass('active');
	});


	jsl.load();

	return {
		translateGet: translateGet
	};

})();

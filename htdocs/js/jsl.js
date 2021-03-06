/*!
 * JSL JavaScript Loader v1.1.0
 * http://www.andresvidal.com/jsl
 *
 * Copyright (c) 2010 Andres Vidal
 * Dual licensed under the MIT and GPL licenses.
 * http://www.andresvidal.com/jsl/license
 *
 */

var JSL, jsl;
JSL = jsl = {
	jsl: "1.1.0", _loadedUrls: [], scripts: [], d: window.document, trim: function (a) {
		return a.replace(/^\s+|\s+$/g, "")
	}, script: function (a) {
		var b = jsl.d.createElement("script");
		b.type = "text/javascript";
		b.src = a;
		return b
	}, tag: function (a) {
		return jsl.d.getElementsByTagName(a)
	}, clean: function (a) {
		for (var b = 0; b < a.length; b++)if (typeof a[b] === "undefined") {
			a.splice(b, 1);
			b--
		}
		jsl.scripts = a;
		return jsl.scripts
	}, inArray: function (a, b) {
		for (var c = 0; c < b.length; c++)if (b[c] === a)return c;
		return false
	}, isLoaded: function (a, b) {
		if (jsl._loadedUrls[a]) {
			typeof b !== "undefined" && b();
			return true
		}
		return false
	}, add: function (a, b) {
		if (typeof a !== "string" || jsl.trim(a) == "")return false;
		a = jsl.trim(a);
		if (jsl.isLoaded[a] || jsl.inArray(a, jsl.scripts))return true; else if (parseInt(b) < 0 && jsl.isLoaded(a) === false) {
			document.write('<script src="' + a + '" type="text/javascript"><\/script>');
			return jsl._loadedUrls[a] = true
		} else if (parseInt(b) > -1 && typeof jsl.scripts[b] === "undefined") {
			jsl.scripts[b] = a;
			return true
		} else if (parseInt(b) > -1 && jsl.scripts[b]) {
			jsl.scripts.splice(b + 1, 0, a);
			return true
		} else jsl.scripts.push(a)
	}, domAppend: function (a, b) {
		if (jsl.isLoaded(a, b))return true;
		var c = jsl.tag("head")[0], d = jsl.script(a), e = false;
		d.onload = d.onreadystatechange = function () {
			if (!e && (!this.readyState || this === "loaded" || this.readyState === "complete")) {
				e = true;
				typeof b !== "undefined" && b();
				d.onload = d.onreadystatechange = null
			}
		};
		c.appendChild(d);
		jsl._loadedUrls[a] = true
	}, load: function (a, b) {
		if (typeof a === "undefined" || jsl.trim(a) == "") {
			jsl.clean(jsl.scripts)
			for (var c = 0; c < jsl.scripts.length; c++)
				jsl.domAppend(jsl.scripts[c]);
			return true
		} else if (typeof a == "string" && typeof b !== "undefined") {
			jsl.domAppend(a, b);
			return true
		} else if (jsl.isLoaded(a) === false) {
			document.write('<script src="' + a + '" type="text/javascript"><\/script>');
			return jsl._loadedUrls[a] = true
		}
	}
};
var scripts = document.getElementsByTagName("script");
eval(scripts[scripts.length - 1].innerHTML);
/*
	mobile.js

	Copyright Tommi Pääkkö
	2012-12-21

*/

// Discriminate poor browsers.

// Mobile navigation

// Wrapper for querySelector
function qs(queryString) {
	"use strict";
	try {
		return document.querySelector(queryString);
	} catch (e) {
		return false;
	}
}

function qsa(queryString) {
	'use strict';
	try {
		return document.querySelectorAll(queryString);
	} catch (e) {
		return false;
	}
}

// removes a class from element
function removeClass(elm, strClass) {
	"use strict";
	if (!!elm) {
		var re = new RegExp(strClass, 'g');
		elm.className = elm.className.replace(re, '');
	}
}

// returns boolean if element has class name
function hasClass(elm, strClass) {
	"use strict";
	var re = new RegExp(strClass, 'g');
	return !!elm.className.match(re);
}

// add a class to an element
function addClass(elm, strClass) {
	"use strict";
	var re = new RegExp(strClass, 'g');
	if (!elm.className.match(re)) {
		elm.className += ' ' + strClass;
	}
}

// adds or removes a class on an element
function toggleClass(elm, strClass) {
	"use strict";
	if (hasClass(elm, strClass)) {
		removeClass(elm, strClass);
	} else {
		addClass(elm, strClass);
	}
}

var mobileNav = {
	tg : function(el){
		el.addEventListener('click', function (e) {
			e.preventDefault();
			toggleClass(this, 'nav-open');
		}, false);
	},
	load: function () {
		'use strict';
		if (!document.querySelector) {
			return;
		}

		var navToggle = qsa('.nav-toggle');

		for (var i = navToggle.length - 1; i >= 0; i--) {
			this.tg(navToggle[i]);
		}
	}
};

mobileNav.load();

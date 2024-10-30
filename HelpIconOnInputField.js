var HelpIconOnInputFieldObjects = {};
var InputFieldCounterWithHelpIcon = 0;
var HelpIconURL;
function setHelpIconURL(url) {
	HelpIconURL = url;
}
function HelpIconOnInputField(e) {
	if (! (id = e.target.id)) {
		id = e.target.id = "WithHelpIcon_" + InputFieldCounterWithHelpIcon++;
	}
	if (HelpIconOnInputFieldObjects[id] == null) {
		HelpIconOnInputFieldObjects[id] = new HelpIconOnInputFieldClass();
	}
	HelpIconOnInputFieldObjects[id].onEvent(e);
}

function HelpIconOnInputFieldClass() {
	this.DismissFunc = null;
	this.targetId = null;
}
HelpIconOnInputFieldClass.prototype = {
	onEvent: function(e, type) {
		this.targetId = e.target.id;
		if (!type) type = e.type;
		switch (type) {
			case 'mouseover':
				this.DismissFunc = null;
//				if (e.target == document.activeElement) return;
				if (e.target.value != "") return;
//				console.log("mouseover " + e.target.id);
				this.Display(e);
				break;
			case 'mouseout':
//			case 'focus':
			case 'blur':
//				console.log("mouseout or blur " + e.target.id);
				this.DismissFunc = function(){this.Dismiss(e);};
				var self = this;
				setTimeout(
					function() {
						if (self.DismissFunc) {
//							console.log("dismiss called " + e.target.id);
							self.DismissFunc(e);
						} else {
//							console.log("dismiss cancelled");
						}
					},
					500
				);
				break;
		}
	},
	getIconId: function() { return 'HelpIconOnInputField_' + this.targetId; },
	Display: function(e) {
		if (document.getElementById(this.getIconId()) == null) {
			var t = e.target;
			var tComputed = window.getComputedStyle(t, '');
			var tFontSize = tComputed.lineHeight;
			var tMarginTop = tComputed.marginTop;
			var tMarginBottom = tComputed.marginBottom;
			var tPaddingTop = tComputed.paddingTop;
			var tPaddingBottom = tComputed.paddingBottom;
			var p = t.offsetParent;
			var icon = document.createElement('a');
			icon.className = "HelpIconOnInputField";
			icon.id = this.getIconId();
			icon.href = HelpIconURL;
			icon.style.marginTop = tMarginTop;
			icon.style.marginBottom =  tMarginBottom;
			icon.style.paddingTop =  tPaddingTop;
			icon.style.paddingBottom = tPaddingBottom;
			icon.style.position = "absolute";
			icon.style.fontFamily = 'genericons';
			icon.style.fontSize = tFontSize;
			icon.textContent = "";	// (?) icon: unicode \f105
			var self = this;
			icon.onmouseover = function(ie) {
				self.onEvent(e, 'mouseover');
			};
			icon.onmouseout = function(ie) {
				self.onEvent(e, 'mouseout');
			};
			p.appendChild(icon);
			var tLeft = t.offsetLeft;
			var tWidth = t.offsetWidth;
			var cWidth = icon.offsetWidth;
			icon.style.left = (tLeft + tWidth - cWidth) + "px";
			icon.style.top = t.offsetTop + "px";
		}
	},
	Dismiss: function(e) {
		var t = e.target;
		var p = t.offsetParent;
		var icon = document.getElementById(this.getIconId());
		if (icon) p.removeChild(icon);
	}
}

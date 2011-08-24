window.addEvent('domready', function() {
	
	//create our Accordion instance
	var myAccordion = new Accordion($('accordion'), 'h3.toggler', 'div.element', {
		opacity: false,
		onActive: function(toggler, element){
			toggler.setStyle('color', '#ffffff');
			toggler.setStyle('background-position', 'left 0px');
		},
		onBackground: function(toggler, element){
			toggler.setStyle('color', '#41464D');
			toggler.setStyle('background-position', 'left -24px');
		}
	});
});
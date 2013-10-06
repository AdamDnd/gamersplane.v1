$(function() {
	$('select').prettySelect();
	if ($('.prettySelectOptions').length) {
		$('html').click(function () {
			$('.prettySelectOptions').hide();
		});
	}

	$('input[type="checkbox"]').prettyCheckbox();
/*	$('input[type="checkbox"]').each(function () {
		$(this).wrap('<div class="prettyCheckbox"></div>');
		if ($(this).is(':checked')) $(this).parent().addClass('checked');
	}).hide().change(function (e) {
		$(this).parent().toggleClass('checked');
	});
	$('.prettyCheckbox').click(function (e) {
		$(this).toggleClass('checked');
		$checkbox = $(this).find('input');
		$checkbox.prop('checked', !$checkbox.prop('checked'));
	});*/

	$('input[type="radio"]').each(function () {
		$(this).wrap('<div class="prettyRadio"></div>');
		if ($(this).is(':checked')) $(this).parent().addClass('checked');
	}).hide().change(function (e) {
		$(this).parent().toggleClass('checked');
	});
	$('.prettyRadio').click(function (e) {
		if (!$(this).hasClass('checked')) {
			$radio = $(this).find('input');
			radioName = $radio.attr('name');
			$('input[name="' + radioName + '"]').prop('checked', false).parent().removeClass('checked');
			$(this).addClass('checked');
			$radio.prop('checked', true);
		}
	});


	$('.loginLink').colorbox();

	$('.placeholder').each(function () {
		$(this).val($(this).data('placeholder')).addClass('default').focus(function () {
			if ($(this).val() == $(this).data('placeholder')) $(this).val('').removeClass('default');
		}).blur(function () {
			if ($(this).val() == '') $(this).val($(this).data('placeholder')).addClass('default');
		});
	});

	if ($('body').hasClass('modal')) {
		$('a').attr('target', '_parent');
		parent.$.colorbox.resize({ 'innerWidth': $('body').data('modalWidth') } );
		parent.$.colorbox.resize({ 'innerHeight': $('body').height() } );
	}

	$('.headerbar, .fancyButton, .wingDiv').each(setupWingContainer);
	$('.wing').each(setupWings);
	if ($('.headerbar .wing').length) {
		leftMargin = $('.headerbar .wing').css('border-right-width');
		$('.hbMargined:not(textarea)').css({ 'margin-left': leftMargin, 'margin-right': leftMargin });
		$('.hbTopper').css({ 'marginLeft': leftMargin });
	}
	if ($('.hbDark .wing').length) {
		leftMargin = $('.hbDark .wing').css('border-right-width');
		$('.hbdMargined:not(textarea)').css({ 'margin-left': leftMargin, 'margin-right': leftMargin });
		$('.hbdTopper').css({ 'marginLeft': leftMargin });

		leftMargin = leftMargin.slice(0, -2);
		$('textarea.hbdMargined').each(function () {
			tWidth = $(this).parent().width();
			$(this).css({ 'margin-left': leftMargin + 'px', 'margin-right': leftMargin + 'px', 'width': (tWidth - 2 * leftMargin) + 'px' });
		});
	}

	$('#mainMenu li').mouseenter(function () {
		$(this).children('ul').stop(true, true).slideDown();
	}).mouseleave(function () {
		$(this).children('ul').stop(true, true).slideUp();
	}).find('ul').each(function () {
		$(this).css('minWidth', $(this).parent().width());
	});

	if ($('#fixedMenu').size()) {
		var $fixedMenu = $('#fixedMenu_window');
		$('html').click(function () {
			$fixedMenu.find('.submenu, .subwindow').slideUp(250);
		});
		
		var fm_currentlyOpen = '';
		$fixedMenu.click(function (e) { e.stopPropagation(); })
		$fixedMenu.find('li > a').filter(function () {
			return $(this).siblings('.submenu, .subwindow').length;
		}).click(function (e) {
			e.stopPropagation();

			currentID = $(this).parent().attr('id');
			$parentMenu = $(this).parent().parent();
			$subwindow = $(this).siblings('.submenu, .subwindow');

			$parentMenu.find('.fm_smOpen').not($subwindow).slideUp(250).removeClass('fm_smOpen');
			$subwindow.slideToggle(250).toggleClass('fm_smOpen');
			
			e.preventDefault();
		});
		
		
		$('#fm_roll').click(function (e) {
			e.stopPropagation();
			var dice = $('#customDiceRoll input').val();
			if (dice != '') fm_rollDice(dice);
			
			e.preventDefault();
		});
		
		$('#fm_diceRoller input').keypress(function (e) {
			if (e.which == 13) {
				var dice = $(this).val();
				if (dice != '') fm_rollDice(dice);
				
				e.preventDefault();
			}
		}).click(function (e) { e.stopPropagation(); });
		
		$('#fm_diceRoller .diceBtn').click(function (e) {
			e.stopPropagation();
			var dice = '1' + $(this).attr('name');
			if (dice != '1') fm_rollDice(dice);

			e.preventDefault();
		});
	}

	$('.cbf_basic').append('<input type="hidden" name="modal" value="1">').ajaxForm({
		beforeSubmit: function () {
			$('.cbf_basic .required').each(function () {
				if ($(this).val().length == 0) return false;
			});

			return true;
		},
		success: function (data) {
			if (data == '1') {
				parent.document.location.reload();
			}
		}
	});


	/* Individual Pages */
	if (!$('body').hasClass('modal')) var curPage = $('#content > div > div').attr('id').substring(5);
	else var curPage = $('body > div').attr('id').substring(5);
});
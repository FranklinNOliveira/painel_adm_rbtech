$(document).ready(function(){
	$("input").addClass('radius5');
	$("textarea").addClass('radius5');
	$("fieldset").addClass('radius5');
	$("#sidebar li").addClass('radius5');
	
	//Accordion
	$('#accordion a.item').click(function(){
		$('#accordion li').children('ul').slideUp('fast');
		$('#accordion li').each(function(){
			$(this).removeClass('active');
		});
		$(this).siblings('ul').slideDown('fast');
		$(this).parent().addClass('active');
		return false;
	});
});
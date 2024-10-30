(function($){
 $(document).ready(function(){
	 //	$(".clickquote p").after("<a class='qq'><img src='wp-content/plugins/clickquote/quote2.png' alt='q' /></a>");

	 $(".clickquote").hover(function(){
		 $(this).addClass('cqhover');
		 }, function() {
		 $(this).removeClass('cqhover');
		 });

	 $(".clickquote").click(function(){
		 $("#comment").val( $("#comment").val() + "<blockquote>" + $.trim($(this).text()) + "</blockquote>\n\n");
		 });
	 });

 })(jQuery);


jQuery(document).ready(function() {
 	var $ = jQuery;
    var screenRes = $(window).width();
    var screenHeight = $(window).height();

    $("[href=#]").click(function(event){
        event.preventDefault();
    });

// Add gradient to IE
    setTimeout(function () {
        $("input, textarea, .select_styled, .body_wrap, .boxed-velvet .inner, .widget_categories li, .dropdown > li a, .tabs li a, .tab-pane, .comment-body .inner, .chzn-container, .carousel-title, .note").addClass("gradient");
    }, 0);

// buttons
    $(".btn").not(".btn-round").hover(function(){
        $(this).stop().animate({"opacity": 0.8});
    },function(){
        $(this).stop().animate({"opacity": 1});
    });
	$('a.btn, span.btn').on('mousedown', function(){
		$(this).addClass('active')
	});
	$('a.btn, span.btn').on('mouseup mouseout', function(){
		$(this).removeClass('active')
	});

// style Select, Radio, Checkbox
    if ($("select").hasClass("select_styled")) {
        cuSel({changedEl: ".select_styled", visRows: 10});
    }
    if ($("div,p").hasClass("input_styled")) {
        $(".input_styled input").customInput();
    }


// Service List 2
$('.service_list_2 .service_item').not(':even').addClass('even');
$('.service_list_2 .service_item').not(':odd').addClass('odd');

// Smooth Scroling of ID anchors
    function filterPath(string) {
        return string
            .replace(/^\//,'')
            .replace(/(index|default).[a-zA-Z]{3,4}$/,'')
            .replace(/\/$/,'');
    }
    var locationPath = filterPath(location.pathname);
    var scrollElem = scrollableElement('html', 'body');

    $('a[href*=#].anchor').each(function() {
        $(this).click(function(event) {
            var thisPath = filterPath(this.pathname) || locationPath;
            if (  locationPath == thisPath
                && (location.hostname == this.hostname || !this.hostname)
                && this.hash.replace(/#/,'') ) {
                var $target = $(this.hash), target = this.hash;
                if (target && $target.length != 0) {
                    var targetOffset = $target.offset().top;
                    event.preventDefault();
                    $(scrollElem).animate({scrollTop: targetOffset}, 400, function() {
                        location.hash = target;
                    });
                }
            }
        });
    });

    // use the first element that is "scrollable"
    function scrollableElement(els) {
        for (var i = 0, argLength = arguments.length; i <argLength; i++) {
            var el = arguments[i],
                $scrollElement = $(el);
            if ($scrollElement.scrollTop()> 0) {
                return el;
            } else {
                $scrollElement.scrollTop(1);
                var isScrollable = $scrollElement.scrollTop()> 0;
                $scrollElement.scrollTop(0);
                if (isScrollable) {
                    return el;
                }
            }
        }
        return [];
    };

// Placeholders
setTimeout(function () {
    if($("[placeholder]").size() > 0) {
		$.Placeholder.init({ color : "#ededed" });
	}
}, 0);

// Scroll Bars
    var $scrolls_on_page = $('.scrollbar.style2').length;
    var $scroll_height = 0;

    for(var i = 1; i <= $scrolls_on_page; i++){
        $('.scrollbar.style2').eq(i-1).addClass('id'+i);
    };

    setTimeout(function () {
        $(".jspTrack").append("<div class='jspProgress'></div>");
        $(document).on('jsp-scroll-y','.scrollbar.style2',function(){
            for(var i = 1; i <= $scrolls_on_page; i++){
                $scroll_height = $('.scrollbar.style2.id'+i+' .jspDrag').css('top');
                $('.scrollbar.style2.id'+i+' .jspDrag').siblings(".jspProgress").css({"height":parseInt($scroll_height, 10)+10+"px"});
            }
        });
    }, 0);
});
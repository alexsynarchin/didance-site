/**
 * Created by user on 14.11.2016.
 */
$(function () {
    // инициализировать все элементы на страницы, имеющих атрибут data-toggle="tooltip", как компоненты tooltip
    $('[data-toggle="tooltip"]').tooltip();
    mobileMenu();
    $('.owl-carousel').owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            321: {
                items: 2
            },
            600: {
                items: 3
            },
            1000: {
                items: 5
            }
        }
    });
    /* Get iframe src attribute value i.e. YouTube video url
     and store it in a variable */
    var url = $("#cartoonVideo").attr('src');

    /* Assign empty url value to the iframe src attribute when
     modal hide, which stop the video playing */
    $("#video-modal").on('hide.bs.modal', function(){
        $("#cartoonVideo").attr('src', '');
        $('.video-block__link-wrap a').focus(function() {
            this.blur();
        });
    });

    /* Assign the initially stored url back to the iframe src
     attribute when modal is displayed again */
    $("#video-modal").on('show.bs.modal', function(){
        $("#cartoonVideo").attr('src', url);
    });

});
function mobileMenu() {
    var toggle =  $('.main-nav__panel-toggle');
    var icon = $('.main-nav__panel-icon');
    toggle.on('click', function() {
        icon.toggleClass("main-nav__panel-icon--mobile-open");
    });
}
function aboutUS() {
    $('.about-didance__list').viewportChecker({
        callbackFunction:function (elem, action) {
            var target = $('.about-didance__item');
            var hold = 200;
            $.each(target,function(i,t){
                var $this = $(t);
                $this.addClass('invisible');
                setTimeout(function(){
                    $this.removeClass('invisible');
                    $this.addClass('visible animated fadeIn');
                },i*hold);
            });
        }
    });
}
function centerModals($element) {
    var $modals;
    if ($element.length) {
        $modals = $element;
    } else {
        $modals = $('.modal-vcenter:visible');
    }
    $modals.each( function(i) {
        var $clone = $(this).clone().css('display', 'block').appendTo('body');
        var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
        top = top > 0 ? top : 0;
        $clone.remove();
        $(this).find('.modal-content').css("margin-top", top);
    });
}
$('.modal-vcenter').on('show.bs.modal', function(e) {
    centerModals($(this));
});

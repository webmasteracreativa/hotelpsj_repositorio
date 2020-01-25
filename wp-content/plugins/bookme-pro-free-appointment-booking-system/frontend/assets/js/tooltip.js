(function ($) {
    $.fn.bookmeProTooltip = function () {
        var ttips = this;
        if (!document.getElementById("bookme-pro-tooltip")) {
            var ttipWrap = document.createElement("span");
            ttipWrap.id = "bookme-pro-tooltip";
            document.body.appendChild(ttipWrap);
        }

        var data = ttips.attr("title");
        ttips.attr("title", "");
        ttips.attr("data-ttip", data);

        ttips.onmouseover = ttipShow;
        ttips.onmouseout = ttipHide;
        ttips.on('mouseover', ttipShow);
        ttips.on('mouseout', ttipHide);
    };

    function ttipShow(e) {

        var ttipWrap = document.getElementById("bookme-pro-tooltip"),
            $span = $(this).find('span').get(0),
            rect = $span.getBoundingClientRect(),
            ex = rect.left + window.scrollX,
            ey = rect.top + window.scrollY;
        ttipWrap.innerHTML = $(this).attr("data-ttip");
        ttipWrap.style.top = ey - ttipWrap.offsetHeight - 5 + 'px';
        if ((ttipWrap.offsetWidth + ex) < window.innerWidth) {
            ttipWrap.style.left = ex - ttipWrap.offsetWidth / 2 + $span.offsetWidth / 2 + "px";
        } else {
            ttipWrap.style.left = window.innerWidth - ttipWrap.offsetWidth + "px";
        }
        ttipWrap.style.visibility = "visible";
        ttipWrap.style.opacity = 1;

    }

    function ttipHide() {
        document.getElementById("bookme-pro-tooltip").style.visibility = "hidden";
        document.getElementById("bookme-pro-tooltip").style.opacity = 0;
    }
})(jQuery);

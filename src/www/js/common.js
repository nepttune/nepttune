function redrawControl(control)
{
    $.nette.ajax({
        method: 'GET',
        traditional: true,
        url: redrawControlUrl + '&control=' + control
    });
}

function redrawRow(control, rowId)
{
    $.nette.ajax({
        method: 'GET',
        traditional: true,
        url: redrawRowUrl + '&control=' + control + '&rowId=' + rowId
    });
}

function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

jQuery.fn.fade = function()
{
    this.fadeTo('slow', 0, 'linear').slideUp('slow', 'linear', function() { $(this).remove(); });
};


function flashFadeOut()
{
    $('.flash').delay(2000).fade();
}

function refreshPlugins(context)
{
    $(context).find('.iframePopup').magnificPopup({type: 'iframe'});
    $(context).find('.ajaxPopup').magnificPopup({type: 'ajax'});
}

$(document).ready(function ()
{
    flashFadeOut();

    $.nette.ext('snippets').after(function (el)
    {
        refreshPlugins(el);
    });
    $.nette.ext('flash', {
        complete: flashFadeOut
    });
    $.nette.init();

    refreshPlugins(document.body);
});

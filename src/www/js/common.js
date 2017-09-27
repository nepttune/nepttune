const selectizeOptions =
{
    delimiter: ', ',
    create: false,
    persist: false,
    preload: true,
    plugins: ['clear_selection']
};
const iCheckOptions =
{
    checkboxClass: 'icheckbox_square-red',
    radioClass: 'iradio_square-red'
};

Chart.defaults.global.legend.display = false;
Chart.defaults.global.title.display = true;
const chartBackgroundColors =
[
    'rgba(255, 99, 132, 0.2)',
    'rgba(54, 162, 235, 0.2)',
    'rgba(255, 206, 86, 0.2)',
    'rgba(75, 192, 192, 0.2)',
    'rgba(153, 102, 255, 0.2)',
    'rgba(255, 159, 64, 0.2)'
];
const chartBorderColors =
[
    'rgba(255,99,132, 1)',
    'rgba(54, 162, 235, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 159, 64, 1)'
];

Nette.showFormErrors = function (form, errors)
{
    $(form).find('ul.form-errors, span.form-error').remove();

    for (var i = 0; i < errors.length; i++)
    {
        $(errors[i].element).parent().parent().find('th').append('<span class="form-error">' + errors[i].message + '</span>');
    }

    if (errors.length > 0)
    {
        var elem = errors[0].element;

        if (elem.nodeName === 'SELECT')
        {
            $(elem).parent().find('input[type="text"]').trigger('click');
            return;
        }
        $(elem).focus();
    }
};

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

function refreshPlugins(context, recaptcha)
{
    if (typeof recaptcha !== 'undefined' && recaptcha)
    {
        g_ReCaptchaOnLoad();
    }

    var checkInputs = $(context).find('input[type="radio"], input[type="checkbox"]');
    checkInputs.iCheck(iCheckOptions);
    checkInputs.on('ifChanged', function (event)
    {
        event = document.createEvent('HTMLEvents');
        event.initEvent('change', true, true);
        event.eventName = 'change';

        this.dispatchEvent(event);
    });

    var selectInputs = $(context).find('select');
    selectInputs.selectize(selectizeOptions);

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

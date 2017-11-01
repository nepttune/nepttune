const selectOptions =
{
    width: 'auto'
};
const iCheckOptions =
{
    checkboxClass: 'icheckbox_square-red',
    radioClass: 'iradio_square-red'
};

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

form_refereshPlugins = refreshPlugins;

refreshPlugins = function (el)
{
    form_refereshPlugins(el);

    var checkInputs = $(el).find('input[type="radio"], input[type="checkbox"]');
    checkInputs.iCheck(iCheckOptions);
    checkInputs.on('ifChanged', function (event)
    {
        event = document.createEvent('HTMLEvents');
        event.initEvent('change', true, true);
        event.eventName = 'change';

        this.dispatchEvent(event);
    });

    var selectInputs = $(el).find('select');
    selectInputs.select2(selectOptions);

    if ($('.g-recaptcha').length !== 0)
    {
        g_ReCaptchaOnLoad();

    }
};

function getArgsVal(elem, args)
{
    return $(elem).closest('form').find('input[name="'+args+'"]').val();
}

Nette.validators.AppValidatorCoreValidator_sameLength = function(elem, args, val)
{
    return val.length == getArgsVal(elem, args).length;
};

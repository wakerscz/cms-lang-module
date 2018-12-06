/**
 * Copyright (c) 2018 Wakers.cz
 *
 * @author Jiří Zapletal (http://www.wakers.cz, zapletal@wakers.cz)
 *
 * @var translations
 *
 */

$(function ()
{
    var i18n = {
        //'cs': {},
        //'en': {}
    };


    var $html = $('html');
    var dLang = $html.attr('lang').length !== 0 ? $html.attr('lang') : 'en';


    $.i18nAdd = function (lang, slug, message)
    {
        if (typeof i18n[lang] === 'undefined')
        {
            i18n[lang] = {};
        }

        i18n[lang][slug] = message;
    };


    $.i18nGet = function (slug, params = {})
    {
        if (typeof i18n[dLang] === 'undefined')
        {
            i18n[dLang] = {};
        }

        var transAll = i18n['all'][slug];
        var translation = (typeof transAll === 'undefined') ? i18n[dLang][slug] : transAll;

        if (typeof translation === 'undefined')
        {
            $.notification(
                'error',
                'Missing (' + dLang + ') translation',
                '$.i18nAdd(\'' + dLang + '\', \'' + slug + '\', \'...\');'
            );

            return slug;
        }

        return translation;
    };
});
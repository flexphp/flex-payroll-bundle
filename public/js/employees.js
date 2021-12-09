jQuery(document).ready(function ($) {
    'use strict';

    const documentTypeIdUrl = $('[id$=form_documentTypeId]').data('autocomplete-url');
    const typeUrl = $('[id$=form_type]').data('autocomplete-url');
    const subTypeUrl = $('[id$=form_subType]').data('autocomplete-url');
    const paymentMethodUrl = $('[id$=form_paymentMethod]').data('autocomplete-url');
    const accountTypeUrl = $('[id$=form_accountType]').data('autocomplete-url');

    $('[id$=form_documentTypeId]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 0,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: documentTypeIdUrl,
            method: 'POST',
            dataType: 'json',
            delay: 300,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            }
        },
    });

    $('[id$=form_type]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 0,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: typeUrl,
            method: 'POST',
            dataType: 'json',
            delay: 300,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            }
        },
    });

    $('[id$=form_subType]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 0,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: subTypeUrl,
            method: 'POST',
            dataType: 'json',
            delay: 300,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            }
        },
    });

    $('[id$=form_paymentMethod]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 3,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: paymentMethodUrl,
            method: 'POST',
            dataType: 'json',
            delay: 300,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            }
        },
    });

    $('[id$=form_accountType]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 0,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: accountTypeUrl,
            method: 'POST',
            dataType: 'json',
            delay: 300,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            }
        },
    });
});

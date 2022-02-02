jQuery(document).ready(function ($) {
    'use strict';

    const typeUrl = $('[id$=form_type]').data('autocomplete-url');
    const employeeIdUrl = $('[id$=form_employeeId]').data('autocomplete-url');
    const agreementIdUrl = $('[id$=form_agreementId]').data('autocomplete-url');
    const statusIdUrl = $('[id$=form_statusId]').data('autocomplete-url');

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

    $('[id$=form_employeeId]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 3,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: employeeIdUrl,
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

    $('[id$=form_agreementId]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 3,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: agreementIdUrl,
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

    $('[id$=form_statusId]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 0,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: statusIdUrl,
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

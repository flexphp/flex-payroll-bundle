jQuery(document).ready(function ($) {
    'use strict';

    const employeeUrl = $('[id$=form_employee]').data('autocomplete-url');
    const providerUrl = $('[id$=form_provider]').data('autocomplete-url');
    const statusUrl = $('[id$=form_status]').data('autocomplete-url');
    const typeUrl = $('[id$=form_type]').data('autocomplete-url');
    const parentIdUrl = $('[id$=form_parentId]').data('autocomplete-url');

    $('[id$=form_employee]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 3,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: employeeUrl,
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

    $('[id$=form_provider]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 3,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: providerUrl,
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

    $('[id$=form_status]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 3,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: statusUrl,
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
        minimumInputLength: 3,
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

    $('[id$=form_parentId]').select2({
        theme: 'bootstrap4',
        minimumInputLength: 3,
        allowClear: true,
        placeholder: '',
        ajax: {
            url: parentIdUrl,
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

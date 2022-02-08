jQuery(document).ready(function ($) {
    'use strict';

    $('.find-employee').on('change', function () {
        const $container = $(this).parent().parent();
        const $documentTypeId = $container.find('[name$="[documentTypeId]"]');
        const $documentNumber = $container.find('[name$="[documentNumber]"]');
        const employeeId = $container.find('[name$="[id]"]').val() || 0;

        $.ajax({
            url: window.flex.baseUrl + '/paysheets/find-employees',
            method: 'POST',
            data: {
                documentTypeId: $documentTypeId.val(),
                documentNumber: $documentNumber.val(),
                employeeId: employeeId,
            },
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            beforeSend: function () {
                $('.overlay').show();
            }
        }).always(function () {
            $('.overlay').hide();
        }).done(function (response) {
            const data = response.results;
            let employeeId = data.id || '';

            if (!employeeId && $documentNumber.val() !== '') {
                employeeId = $container.find('[name$="[id]"]').val();
            }

            $container.find('[name$="[id]"]').val(employeeId);
            $container.find('[name$="[documentTypeId]"]').val(data.documentTypeId || $documentTypeId.val());
            $container.find('[name$="[documentNumber]"]').val(data.documentNumber || $documentNumber.val());
            $container.find('[name$="[firstName]"]').val(data.firstName || '');
            $container.find('[name$="[secondName]"]').val(data.secondName || '');
            $container.find('[name$="[firstSurname]"]').val(data.firstSurname || '');
            $container.find('[name$="[secondSurname]"]').val(data.secondSurname || '');
            $container.find('[name$="[accountNumber]"]').val(data.accountNumber || '');

            const $type = $container.find('[name$="[type]"]').empty();

            if (data.typeId) {
                $type.append(new Option(data.typeName, data.typeId, true, false));
            }

            const $subType = $container.find('[name$="[subType]"]').empty();

            if (data.subTypeId) {
                $subType.append(new Option(data.subTypeName, data.subTypeId, true, false));
            }

            const $paymentMethod = $container.find('[name$="[paymentMethod]"]').empty();

            if (data.paymentMethodId) {
                $paymentMethod.append(new Option(data.paymentMethodName, data.paymentMethodId, true, false));
            }

            const $accountType = $container.find('[name$="[accountType]"]').empty();

            if (data.accountTypeId) {
                $accountType.append(new Option(data.accountTypeName, data.accountTypeId, true, false));
            }
        });
    });

    $('[name="agreement[id]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/paysheets/find-agreements',
            method: 'POST',
            dataType: 'json',
            delay: 500,
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
    }).on('select2:select', function (e) {
        const data = e.params.data;

        if (data.typeId) {
            $('[name="agreement[type]"]').empty().append(new Option(data.typeName, data.typeId, true, false));
        }

        if (data.statusId) {
            $('[name="agreement[status]"]').empty().append(new Option(data.statusName, data.statusId, true, false));
        }

        if (data.periodId) {
            $('[name="agreement[period]"]').empty().append(new Option(data.periodName, data.periodId, true, false));
        }

        if (data.currencyId) {
            $('[name="agreement[currency]"]').empty().append(new Option(data.currencyName, data.currencyId, true, false));
        }

        $('[name="agreement[name]"]').val(data.name || '');
        $('[name="agreement[salary]"]').val(data.salary || '');
        $('[name="agreement[healthPercentage]"]').val(data.healthPercentage || 0);
        $('[name="agreement[pensionPercentage]"]').val(data.pensionPercentage || 0);
        $('[name="agreement[initAt]"]').val(data.initAt || '');
        $('[name="agreement[finishAt]"]').val(data.finishAt || '');
        $('[name="agreement[integralSalary]"]').attr('checked', data.integralSalary || 0);
        $('[name="agreement[highRisk]"]').attr('checked', data.highRisk || 0);

        // getHistoryServices(data.id);
    });

//     function getHistoryServices(agreementId) {
//         $.ajax({
//             url: window.flex.baseUrl + '/paysheets/find-history-services',
//             method: 'POST',
//             data: {
//                 agreementId: agreementId
//             },
//             headers: {
//                 'X-XSRF-Token': getCookie('XSRF-Token')
//             },
//             beforeSend: function () {
//                 $('.overlay').show();
//             }
//         }).always(function () {
//             $('.overlay').hide();
//         }).done(function (response) {
//             const paysheet = response.results.paysheet;
//             const history = response.results.history;
//             const kilometers = parseInt(paysheet.kilometers || 0);
//             const kilometersToChange = parseInt(paysheet.kilometersToChange || 0);

//             $('[name="paysheet[createdAt]"]').html(getDateTimeFormat(paysheet.createdAt || ''));
//             $('[name="paysheet[kilometers]"]').val(kilometers);
//             $('[name="paysheet[kilometersToChange]"]').val(kilometersToChange).change();

//             for (var i = 0, l = history.length; i < l; i++) {
//                 let type = history[i].type || '';
//                 let id = history[i].id || '';
//                 let name = history[i].name || '';
//                 let $container = $('[name="history_service[' + type + ']"]');

//                 if ($container.length > 0 && type !== '' && id !== '' && name !== '') {
//                     $container.html(name);
//                     $container.append(' <i class="fas fa-plus-circle"></i>');
//                     $container.data('item-id', id);
//                     $container.data('name', name);
//                     $container.data('price', history[i].price || 0);
//                     $container.data('quantity', history[i].quantity || 1);
//                     $container.data('taxes', history[i].taxes || 0);
//                     $container.css('display', 'inline-block');
//                 }

//                 if (type === 'oil') {
//                     $('[name="history_service[' + type + 'Quantity]"]').html(history[i].quantity + ' ' + history[i].quantityName);
//                 }
//             }

//             $('[name="agreement[type]"]').change();
//             $('[name="agreement[brand]"]').change();
//             $('[name="agreement[serie]"]').change();

//             if (!$('[name="employee[id]"]').val()) {
//                 $('[name="employee[id]"]').val(paysheet.employeeId || null);
//                 $('[name="employee[documentTypeId]"]').val(paysheet.documentTypeId || null);
//                 $('[name="employee[documentNumber]"]').val(paysheet.documentNumber || null);

//                 if ($('[name="employee[id]"]').val()) {
//                     $('[name="employee[id]"]').change();
//                 }
//             }

//             if (paysheet.createdAt) {
//                 $('#lastServiceInfo').collapse('show');
//                 $('#buttonLastServiceInfo').show();
//             } else {
//                 $('#lastServiceInfo').collapse('hide');
//                 $('#buttonLastServiceInfo').hide();
//             }
//         });
//     }

//     $('[name="agreement[type]"]').select2({
//         theme: 'bootstrap4',
//         placeholder: '',
//         minimumInputLength: 0,
//         allowClear: true,
//         ajax: {
//             url: window.flex.baseUrl + '/agreements/find-agreement-types',
//             method: 'POST',
//             dataType: 'json',
//             delay: 500,
//             cache: true,
//             headers: {
//                 'X-XSRF-Token': getCookie('XSRF-Token')
//             },
//             data: function (params) {
//                 return {
//                     term: params.term,
//                     page: params.page
//                 };
//             }
//         },
//     });

    $('[name="employee[type]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/employees/find-employee-types',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

    $('[name="employee[subType]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/employees/find-employee-sub-types',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

    $('[name="employee[paymentMethod]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/employees/find-payment-methods',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

    $('[name="employee[accountType]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/employees/find-account-types',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

    $('[name="agreement[type]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/agreements/find-agreement-types',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

    $('[name="agreement[status]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/agreements/find-agreement-status',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

    $('[name="agreement[period]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/agreements/find-agreement-periods',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

    $('[name="agreement[currency]"]').select2({
        theme: 'bootstrap4',
        placeholder: '',
        minimumInputLength: 0,
        allowClear: true,
        ajax: {
            url: window.flex.baseUrl + '/agreements/find-currencies',
            method: 'POST',
            dataType: 'json',
            delay: 500,
            cache: true,
            headers: {
                'X-XSRF-Token': getCookie('XSRF-Token')
            },
            data: function (params) {
                return {
                    term: params.term,
                    page: params.page
                };
            },
        },
    });

//     function findAlternativeItems() {
//         const $agreementBrand = $('[name="agreement[brand]"]');
//         const $agreementSerie = $('[name="agreement[serie]"]');
//         const oilId = $('[name="history_service[oil]"]').data('item-id');
//         const oilFilterId = $('[name="history_service[oilFilter]"]').data('item-id');
//         const airFilterId = $('[name="history_service[airFilter]"]').data('item-id');
//         const gasFilterId = $('[name="history_service[gasFilter]"]').data('item-id');

//         if ($agreementBrand.val() !== null && $agreementSerie.val() !== null) {
//             $.ajax({
//                 url: window.flex.baseUrl + '/paysheets/find-alternative-items',
//                 method: 'POST',
//                 data: {
//                     brandId: $agreementBrand.val(),
//                     serieId: $agreementSerie.val(),
//                     oilId: oilId,
//                     oilFilterId: oilFilterId,
//                     airFilterId: airFilterId,
//                     gasFilterId: gasFilterId,
//                 },
//                 headers: {
//                     'X-XSRF-Token': getCookie('XSRF-Token')
//                 },
//                 beforeSend: function () {
//                     $('.overlay').show();
//                 }
//             }).always(function () {
//                 $('.overlay').hide();
//             }).done(function (response) {
//                 const data = response.results;

//                 for (var i = 0, l = data.length; i < l; i++) {
//                     let alternative = data[i].alternative || '';
//                     let id = data[i].id || '';
//                     let name = data[i].name || '';
//                     let $container = $('[name="history_service[' + alternative + 'Alternative]"]');

//                     if ($container.length > 0 && alternative !== '' && id !== '' && name !== '') {
//                         $container.html(name);
//                         $container.append(' <i class="fas fa-plus-circle"></i>');
//                         $container.data('quantity', data[i].quantity || 1);
//                         $container.data('item-id', id);
//                         $container.data('name', name);
//                         $container.data('price', data[i].price || 0);
//                         $container.data('taxes', data[i].taxes || 0);
//                         $container.css('display', 'inline-block');
//                     }
//                 }

//                 if (data.length > 0) {
//                     $('#lastServiceInfo').collapse('show');
//                 }
//             });
//         }
//     }

//     $('body').on('click', '.preload-item', function () {
//         const $item = $(this);
//         const quantity = $item.data('quantity');
//         const itemId = $item.data('item-id');
//         const itemName = $item.data('name');
//         const price = $item.data('price');
//         const taxes = $item.data('taxes');

//         $item.attr('title', 'Agregado')
//             .removeClass('badge-info badge-success')
//             .addClass('preloaded-item badge-light')
//             .find('i')
//             .removeClass('fa-plus-circle')
//             .addClass('fa-check');

//         preloadItem('', quantity, itemId, itemName, price, taxes);
//     });

//     function preloadItem(id, quantity, itemId, itemName, price, taxes) {
//         $('.add-row-paysheet-detail').click();

//         const $row = $('#detailPaysheet > tbody > tr:last');

//         $row.find('[name^="paysheet_detail[id]"]').val(id || '');
//         $row.find('[name^="paysheet_detail[quantity]"]').val(quantity);
//         $row.find('[name^="paysheet_detail[itemId]"]')
//             .empty()
//             .append(new Option(itemName, itemId, true, false))
//             .change();
//         $row.find('[name^="paysheet_detail[price]"]').val(price || 0);
//         $row.find('[name^="paysheet_detail[tax]"]').val(taxes || 0);

//         setTotal($row);
//     }

//     $('[name="employee[id]"], [name="agreement[id]"]').on('change', function () {
//         const $employeeId = $('[name="employee[id]"]');
//         const $agreementId = $('[name="agreement[id]"]');
//         const $paysheetType = $('[name="paysheet[type]"]');
//         const $paysheetId = $('[name="paysheet[id]"]');

//         if ($employeeId.val() > 0 && $agreementId.val() > 0) {
//             $.ajax({
//                 url: window.flex.baseUrl + '/paysheets/get-last',
//                 method: 'POST',
//                 data: {
//                     agreementId: $agreementId.val(),
//                     employeeId: $employeeId.val(),
//                     paysheetType: $paysheetType.val(),
//                     paysheetId: $paysheetId.val(),
//                 },
//                 headers: {
//                     'X-XSRF-Token': getCookie('XSRF-Token')
//                 },
//                 beforeSend: function () {
//                     $('.overlay').show();
//                 }
//             }).always(function () {
//                 $('.overlay').hide();
//             }).done(function (response) {
//                 const paysheet = response.results.paysheet;
//                 const details = response.results.details;

//                 $('[name="last_paysheet[subTotal]"]').html(getMoneyFormat(paysheet.subTotal || 0));
//                 $('[name="last_paysheet[taxes]"]').html(getMoneyFormat(paysheet.taxes || 0));
//                 $('[name="last_paysheet[total]"]').html(getMoneyFormat(paysheet.total || 0));
//                 $('[name="last_paysheet[notes]"]').html(paysheet.notes || '');

//                 var html = '';
//                 var template = $('#lastPaysheet > tbody > tr:first').html();

//                 for (var i = 0, l = details.length; i < l; i++) {
//                     const detail = details[i];

//                     let preloadOption = ''
//                         + '<div class="badge badge-info preload-item d-inline-block"'
//                         + ' data-quantity="' + detail.quantity + '" '
//                         + ' data-item-id="' + detail.itemId + '" '
//                         + ' data-name="' + detail.name + '" '
//                         + ' data-price="' + detail.itemPrice + '" '
//                         + ' data-taxes="' + detail.itemTaxes + '" '
//                         +'>'
//                         + detail.name
//                         + ' <i class="fas fa-plus-circle"></i>'
//                         + '</div>';

//                     let tr = template
//                         .replace('#quantity#', detail.quantity)
//                         .replace('#itemName#', preloadOption)
//                         .replace('#price#', getMoneyFormat(detail.price))
//                         .replace('#tax#', getMoneyFormat(detail.tax))
//                         .replace('#total#', getMoneyFormat(detail.price * detail.quantity));

//                     html += '<tr>' + tr + '</tr>';
//                 }

//                 $('#lastPaysheet > tbody').find("tr:gt(0)").remove();

//     function findAlternativeItems() {
//         const $agreementBrand = $('[name="agreement[brand]"]');
//         const $agreementSerie = $('[name="agreement[serie]"]');
//         const oilId = $('[name="history_service[oil]"]').data('item-id');
//         const oilFilterId = $('[name="history_service[oilFilter]"]').data('item-id');
//         const airFilterId = $('[name="history_service[airFilter]"]').data('item-id');
//         const gasFilterId = $('[name="history_service[gasFilter]"]').data('item-id');

//         if ($agreementBrand.val() !== null && $agreementSerie.val() !== null) {
//             $.ajax({
//                 url: window.flex.baseUrl + '/paysheets/find-alternative-items',
//                 method: 'POST',
//                 data: {
//                     brandId: $agreementBrand.val(),
//                     serieId: $agreementSerie.val(),
//                     oilId: oilId,
//                     oilFilterId: oilFilterId,
//                     airFilterId: airFilterId,
//                     gasFilterId: gasFilterId,
//                 },
//                 headers: {
//                     'X-XSRF-Token': getCookie('XSRF-Token')
//                 },
//                 beforeSend: function () {
//                     $('.overlay').show();
//                 }
//             }).always(function () {
//                 $('.overlay').hide();
//             }).done(function (response) {
//                 const data = response.results;

//                 for (var i = 0, l = data.length; i < l; i++) {
//                     let alternative = data[i].alternative || '';
//                     let id = data[i].id || '';
//                     let name = data[i].name || '';
//                     let $container = $('[name="history_service[' + alternative + 'Alternative]"]');

//                     if ($container.length > 0 && alternative !== '' && id !== '' && name !== '') {
//                         $container.html(name);
//                         $container.append(' <i class="fas fa-plus-circle"></i>');
//                         $container.data('quantity', data[i].quantity || 1);
//                         $container.data('item-id', id);
//                         $container.data('name', name);
//                         $container.data('price', data[i].price || 0);
//                         $container.data('taxes', data[i].taxes || 0);
//                         $container.css('display', 'inline-block');
//                     }
//                 }

//                 if (data.length > 0) {
//                     $('#lastServiceInfo').collapse('show');
//                 }
//             });
//         }
//     }

//     $('body').on('click', '.preload-item', function () {
//         const $item = $(this);
//         const quantity = $item.data('quantity');
//         const itemId = $item.data('item-id');
//         const itemName = $item.data('name');
//         const price = $item.data('price');
//         const taxes = $item.data('taxes');

//         $item.attr('title', 'Agregado')
//             .removeClass('badge-info badge-success')
//             .addClass('preloaded-item badge-light')
//             .find('i')
//             .removeClass('fa-plus-circle')
//             .addClass('fa-check');

//         preloadItem('', quantity, itemId, itemName, price, taxes);
//     });

//     function preloadItem(id, quantity, itemId, itemName, price, taxes) {
//         $('.add-row-paysheet-detail').click();

//         const $row = $('#detailPaysheet > tbody > tr:last');

//         $row.find('[name^="paysheet_detail[id]"]').val(id || '');
//         $row.find('[name^="paysheet_detail[quantity]"]').val(quantity);
//         $row.find('[name^="paysheet_detail[itemId]"]')
//             .empty()
//             .append(new Option(itemName, itemId, true, false))
//             .change();
//         $row.find('[name^="paysheet_detail[price]"]').val(price || 0);
//         $row.find('[name^="paysheet_detail[tax]"]').val(taxes || 0);

//         setTotal($row);
//     }

//     $('[name="employee[id]"], [name="agreement[id]"]').on('change', function () {
//         const $employeeId = $('[name="employee[id]"]');
//         const $agreementId = $('[name="agreement[id]"]');
//         const $paysheetType = $('[name="paysheet[type]"]');
//         const $paysheetId = $('[name="paysheet[id]"]');

//         if ($employeeId.val() > 0 && $agreementId.val() > 0) {
//             $.ajax({
//                 url: window.flex.baseUrl + '/paysheets/get-last',
//                 method: 'POST',
//                 data: {
//                     agreementId: $agreementId.val(),
//                     employeeId: $employeeId.val(),
//                     paysheetType: $paysheetType.val(),
//                     paysheetId: $paysheetId.val(),
//                 },
//                 headers: {
//                     'X-XSRF-Token': getCookie('XSRF-Token')
//                 },
//                 beforeSend: function () {
//                     $('.overlay').show();
//                 }
//             }).always(function () {
//                 $('.overlay').hide();
//             }).done(function (response) {
//                 const paysheet = response.results.paysheet;
//                 const details = response.results.details;

//                 $('[name="last_paysheet[subTotal]"]').html(getMoneyFormat(paysheet.subTotal || 0));
//                 $('[name="last_paysheet[taxes]"]').html(getMoneyFormat(paysheet.taxes || 0));
//                 $('[name="last_paysheet[total]"]').html(getMoneyFormat(paysheet.total || 0));
//                 $('[name="last_paysheet[notes]"]').html(paysheet.notes || '');

//                 var html = '';
//                 var template = $('#lastPaysheet > tbody > tr:first').html();

//                 for (var i = 0, l = details.length; i < l; i++) {
//                     const detail = details[i];

//                     let preloadOption = ''
//                         + '<div class="badge badge-info preload-item d-inline-block"'
//                         + ' data-quantity="' + detail.quantity + '" '
//                         + ' data-item-id="' + detail.itemId + '" '
//                         + ' data-name="' + detail.name + '" '
//                         + ' data-price="' + detail.itemPrice + '" '
//                         + ' data-taxes="' + detail.itemTaxes + '" '
//                         +'>'
//                         + detail.name
//                         + ' <i class="fas fa-plus-circle"></i>'
//                         + '</div>';

//                     let tr = template
//                         .replace('#quantity#', detail.quantity)
//                         .replace('#itemName#', preloadOption)
//                         .replace('#price#', getMoneyFormat(detail.price))
//                         .replace('#tax#', getMoneyFormat(detail.tax))
//                         .replace('#total#', getMoneyFormat(detail.price * detail.quantity));

//                     html += '<tr>' + tr + '</tr>';
//                 }

//                 $('#lastPaysheet > tbody').find("tr:gt(0)").remove();

//                 if (details.length > 0) {
//                     $('#lastPaysheet > tbody').append(html);
//                     $('#buttonLastPaysheet').show();
//                 } else {
//                     $('#buttonLastPaysheet').hide();
//                 }
//             });
//         }
//     });

//     $('.add-row-paysheet-detail').on('click', function () {
//         const template = $('#template-row-paysheet-detail').html().replace(/\[0\]/g, '[' + ((new Date()).getTime()) + ']');

//         $('#detailPaysheet > tbody').append('<tr>' + template + '</tr>');

//         $('#detailPaysheet > tbody > tr:last')
//             .find('select.find-item')
//             .select2({
//                 theme: 'bootstrap4',
//                 placeholder: '',
//                 minimumInputLength: 0,
//                 allowClear: true,
//                 ajax: {
//                     url: window.flex.baseUrl + '/paysheet-details/find-items',
//                     method: 'POST',
//                     dataType: 'json',
//                     delay: 500,
//                     cache: true,
//                     headers: {
//                         'X-XSRF-Token': getCookie('XSRF-Token')
//                     },
//                     data: function (params) {
//                         return {
//                             term: params.term,
//                             page: params.page
//                         };
//                     }
//                 },
//             })
//             .on('select2:select', function (e) {
//                 const data = e.params.data;
//                 const $row = $(this).closest('tr');

//                 $row.find('[name^="paysheet_detail[price]"]').val(data.price || 0);
//                 $row.find('[name^="paysheet_detail[tax]"]').val(data.taxes || 0);

//                 setTotal($row);
//             });

//         $('#messagePaysheetDetails').hide();
//     });

//     $('#detailPaysheet').on('click', '.remove-row-paysheet-detail', function () {
//         $(this).closest('tr').remove();

//         setSubTotal();
//         setTotalTaxes();
//         setTotalNeto();
//     });

//     $('#detailPaysheet').on('change keyup', '.re-calculate', function () {
//         setTotal($(this).closest('tr'));
//     });

//     function setTotal(row) {
//         const price = row.find('[name^="paysheet_detail[price]"]').val();
//         const quantity = row.find('[name^="paysheet_detail[quantity]"]').val();
//         const tax = row.find('[name^="paysheet_detail[tax]"]').val();
//         const taxes = quantity * getTaxes(price, tax) || 0;
//         const total = quantity * price || 0;

//         row.find('[name^="paysheet_detail[taxes]"]').html(taxes).change();
//         row.find('[name^="paysheet_detail[total]"]').html(total + taxes).change();

//         setSubTotal();
//         setTotalTaxes();
//         setTotalNeto();
//     }

//     function setSubTotal() {
//         let subTotal = 0;
//         let taxes = 0;
//         const $row = $('#detailPaysheet > tbody > tr');
//         const numberItems = $row.length;

//         for (let i = 0; i < numberItems; i++) {
//             taxes += $row.eq(i).find('[name^="paysheet_detail[taxes]"]').data('mf-amount') * 1;
//         }

//         for (let i = 0; i < numberItems; i++) {
//             subTotal += $row.eq(i).find('[name^="paysheet_detail[total]"]').data('mf-amount') * 1;
//         }

//         $('[name="subTotal"]').html(subTotal - taxes).change();
//     }

//     function setTotalTaxes() {
//         let totalTaxes = 0;
//         const $row = $('#detailPaysheet > tbody > tr');
//         const numberItems = $row.length;

//         for (let i = 0; i < numberItems; i++) {
//             totalTaxes += $row.eq(i).find('[name^="paysheet_detail[taxes]"]').data('mf-amount') * 1;
//         }

//         $('[name="totalTaxes"]').html(totalTaxes).change();
//     }

//     function setTotalNeto() {
//         const subTotal = $('[name="subTotal"]').data('mf-amount') * 1;
//         const totalTaxes = $('[name="totalTaxes"]').data('mf-amount') * 1;
//         const totalNeto = subTotal + totalTaxes;

//         $('[name="totalNeto"]').html(totalNeto).change();
//         $('[name="payment[total]"]').html(totalNeto).change();
//         setTotalToPay();
//     }

//     function getTaxes(price, taxes) {
//         price = price || 0;
//         taxes = taxes || 0;

//         return (price * (taxes / 100)).toFixed(2);
//     }

//     $('#buttonPayment').on('click', function () {
//         let $paysheetBill = $('#__paysheetBill');
//         let $paysheetPayment = $('#__paysheetPayment');

//         if (!validPaysheetDetails()) {
//             $paysheetBill.hide();
//             $paysheetPayment.hide();

//             return undefined;
//         }

//         $paysheetBill.show();
//         $paysheetPayment.show();

//         $(this).closest('.card-footer').hide();

//         return undefined;
//     });

//     $('#__paysheet').on('submit', function () {
//         if (!validPaysheetDetails) {
//             return false;
//         }

//         return true;
//     });

//     function validPaysheetDetails () {
//         let $message = $('#messagePaysheetDetails');

//         if ($('#detailPaysheet > tbody > tr').length === 0) {
//             $message.show();

//             return false;
//         }

//         $message.hide();

//         return true;
//     }

//     $('.uppercase').on('keyup', function () {
//         $(this).val($(this).val().toUpperCase());
//     });

//     $('.uppercase-no-space').on('keyup', function () {
//         $(this).val($(this).val().replace(' ', '').toUpperCase());
//     });

//     $('.find-cities').select2({
//         theme: 'bootstrap4',
//         minimumInputLength: 0,
//         allowClear: true,
//         placeholder: '',
//         ajax: {
//             url: window.flex.baseUrl + '/employees/find-cities',
//             method: 'POST',
//             dataType: 'json',
//             delay: 500,
//             cache: true,
//             headers: {
//                 'X-XSRF-Token': getCookie('XSRF-Token')
//             },
//             data: function (params) {
//                 return {
//                     term: params.term,
//                     page: params.page
//                 };
//             }
//         },
//     });

//     $('.add-row-payment').on('click', function () {
//         const template = $('#template-row-payment').html().replace(/\[0\]/g, '[' + ((new Date()).getTime()) + ']');

//         $('#payment > tbody').append('<tr>' + template + '</tr>');

//         $('#payment > tbody > tr:last')
//             .find('select.find-payment-method')
//             .select2({
//                 theme: 'bootstrap4',
//                 placeholder: '',
//                 minimumInputLength: 0,
//                 allowClear: true,
//                 ajax: {
//                     url: window.flex.baseUrl + '/payments/find-payment-methods',
//                     method: 'POST',
//                     dataType: 'json',
//                     delay: 500,
//                     cache: true,
//                     headers: {
//                         'X-XSRF-Token': getCookie('XSRF-Token')
//                     },
//                     data: function (params) {
//                         return {
//                             term: params.term,
//                             page: params.page
//                         };
//                     }
//                 },
//             });
//     });

//     $('#payment').on('click', '.remove-row-payment', function () {
//         $(this).closest('tr').remove();

//         setTotalPayed();
//     });

//     $('#payment').on('change keyup', '.re-calculate-payment', function () {
//         setTotalPayed();
//     });

//     $('[name="paysheet[discount]').on('change keyup', function () {
//         setTotalToPay()
//     });

//     function setTotalToPay() {
//         let totalNeto = $('[name="totalNeto"]').data('mf-amount') * 1;
//         let discount = ($('[name="paysheet[discount]').val() || 0) * 1;
//         let ten = totalNeto - Math.floor(totalNeto / 50) * 50;
//         ten = (totalNeto - ten) < 50 ? ten : 0;
//         let totalToPay = totalNeto - (discount + ten);

//         if (totalToPay >= 0) {
//             $('[name="payment[totalToPay]').html(totalToPay).change();
//             $('[name="payment[ten]').html(-ten).change();
//         }

//         setTotalChange();
//     }

//     function setTotalPayed() {
//         let totalPayed = 0;
//         const $row = $('#payment > tbody > tr');
//         const numberPayments = $row.length;

//         for (let i = 0; i < numberPayments; i++) {
//             totalPayed += $row.eq(i).find('[name^="payment[amount]"]').val() * 1;
//         }

//         $('[name="payment[totalPayed]"]').html(totalPayed).change();
//         setTotalChange();
//     }

//     function setTotalChange () {
//         const totalToPay = $('[name="payment[totalToPay]').data('mf-amount') * 1;
//         const totalPayed = $('[name="payment[totalPayed]').data('mf-amount') * 1;
//         let outAmount = totalToPay - totalPayed;
//         let addClass = 'text-danger';
//         let removeClass = 'text-sucess';

//         if (totalToPay >= 0 && totalPayed >= totalToPay) {
//             addClass = 'text-success';
//             removeClass = 'text-danger';
//         }

//         $('[name="payment[outAmount]')
//             .addClass(addClass)
//             .removeClass(removeClass)
//             .html(outAmount)
//             .change();
//     }

//     if ($('[name="paysheet[id]"]').val()) {
//         let paysheetId = $('[name="paysheet[id]"]').val();

//         $.ajax({
//             url: window.flex.baseUrl + '/api/v1/paysheets/' + paysheetId + '/worker',
//             method: 'GET',
//             dataType: 'json',
//             headers: {
//                 'X-XSRF-Token': getCookie('XSRF-Token')
//             },
//             beforeSend: function () {
//                 $('.overlay').show();
//             }
//         }).always(function () {
//             $('.overlay').hide();
//         }).done(function (response) {
//             let worker = response.data;

//             if (!worker.id) {
//                 return undefined;
//             }

//             $('[name="paysheet[worker]"]')
//                 .empty()
//                 .append(new Option(worker.name, worker.id, true, false))
//                 .trigger({
//                     type: 'select2:select',
//                     params: {
//                         data: worker
//                     }
//                 });
//         });

//         $.ajax({
//             url: window.flex.baseUrl + '/api/v1/paysheets/' + paysheetId + '/agreement',
//             method: 'GET',
//             dataType: 'json',
//             headers: {
//                 'X-XSRF-Token': getCookie('XSRF-Token')
//             },
//             beforeSend: function () {
//                 $('.overlay').show();
//             }
//         }).always(function () {
//             $('.overlay').hide();
//         }).done(function (response) {
//             let agreement = response.data;

//             if (!agreement.id) {
//                 return undefined;
//             }

//             $('[name="agreement[id]"]')
//                 .empty()
//                 .append(new Option(agreement.placa, agreement.id, true, false))
//                 .trigger({
//                     type: 'select2:select',
//                     params: {
//                         data: agreement
//                     }
//                 }).trigger('change');
//         });

//         $.ajax({
//             url: window.flex.baseUrl + '/api/v1/paysheets/' + paysheetId + '/paysheet-details',
//             method: 'GET',
//             dataType: 'json',
//             headers: {
//                 'X-XSRF-Token': getCookie('XSRF-Token')
//             },
//             beforeSend: function () {
//                 $('.overlay').show();
//             }
//         }).always(function () {
//             $('.overlay').hide();
//         }).done(function (response) {
//             let paysheetDetails = response.data;

//             if (!paysheetDetails.length) {
//                 return undefined;
//             }

//             for (let i = 0, j = paysheetDetails.length; i < j; i++) {
//                 let paysheetDetail = paysheetDetails[i];

//                 preloadItem(paysheetDetail.id, paysheetDetail.quantity, paysheetDetail.itemId, paysheetDetail.name, paysheetDetail.price, paysheetDetail.tax);
//             }
//         });

//         $.ajax({
//             url: window.flex.baseUrl + '/api/v1/paysheets/' + paysheetId + '/payments',
//             method: 'GET',
//             dataType: 'json',
//             headers: {
//                 'X-XSRF-Token': getCookie('XSRF-Token')
//             },
//             beforeSend: function () {
//                 $('.overlay').show();
//             }
//         }).always(function () {
//             $('.overlay').hide();
//         }).done(function (response) {
//             let payments = response.data;

//             if (!payments.length) {
//                 return undefined;
//             }

//             for (let i = 0, j = payments.length; i < j; i++) {
//                 let payment = payments[i];

//                 preloadPayment(payment.id, payment.currencyId, payment.paymentMethodId, payment.paymentMethodName, payment.paymentStatusId, payment.amount);
//             }

//             updateDraftView();

//             setTotalPayed();
//         });

//         if ($('[name="employee[id]"]').val()) {
//             $('[name="employee[id]"]').change();
//         }
//     }

//     function preloadPayment(id, currencyId, paymentMethodId, paymentMethodName, paymentStatusId, amount) {
//         $('.add-row-payment').click();

//         const $row = $('#payment > tbody > tr:last');

//         $row.find('[name^="payment[id]"]').val(id || '');
//         $row.find('[name^="payment[currencyId]"]').val(currencyId || '');
//         $row.find('[name^="payment[paymentMethodId]"]')
//             .empty()
//             .append(new Option(paymentMethodName, paymentMethodId, true, false))
//             .change();
//         $row.find('[name^="payment[paymenStatusId]"]').val(paymentStatusId || '');
//         $row.find('[name^="payment[amount]"]').val(amount || 0);
//     }
});

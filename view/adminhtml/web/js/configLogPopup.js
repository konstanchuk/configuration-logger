/**
 * Configuration Logger Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
define(['jquery',
        'uiComponent',
        'ko',
        'Magento_Ui/js/modal/modal',
        'loadingPopup',
        'Magento_Ui/js/modal/alert',
        'Magento_Ui/js/modal/confirm'],
    function ($, Component, ko, modal, loader, alert, confirmation) {
        'use strict';
        return Component.extend({
            defaults: {
                'popupId': '#configLogPopup'
            },
            configLog: ko.observableArray([]),
            visible: ko.observable(false),
            selectedValue: ko.observable(),
            initialize: function () {
                var self = this;
                jQuery('body').loadingPopup({
                    timeout: 10000
                }).trigger('hideLoadingPopup');

                $('.section-config tr[data-path]').each(function () {
                    var elem = $(this),
                        iconElem = $('<div />').addClass('conf-logger-icon');
                    elem.find('td.value:first').append(iconElem);
                    iconElem.on('click', function () {
                        self._renderData({
                            scope: elem.data('scope'),
                            scopeId: elem.data('scope_id'),
                            path: elem.data('path'),
                            id: elem.data('id'),
                            label: elem.data('label')
                        });
                    });
                });

                this._super();
                if (!(this.load_url && this.restore_url)) {
                    throw new Error($.mage.__('Undefined config logs load url or restore url'));
                }
            },
            restoreValue: function (data, key) {
                var self = this;
                confirmation({
                    title: $.mage.__('You really want to set this value?'),
                    content: $.mage.__('after confirmation will need to clear the cache'),
                    actions: {
                        confirm: function () {
                            $('body').trigger('showLoadingPopup');
                            $.ajax({
                                url: self.restore_url,
                                data: {
                                    form_key: window.FORM_KEY,
                                    log_id: data.log_id
                                },
                                type: 'POST'
                            }).done(function (data) {
                                if (data.status == 'success') {
                                    window.location.reload();
                                } else {
                                    self._showError($.mage.__('An error occurred during the operation'));
                                }
                            });
                        },
                        cancel: function () {
                        },
                        always: function () {
                        }
                    }
                });
            },
            _showError: function (message) {
                alert({
                    title: $.mage.__('Error'),
                    content: message,
                    actions: {
                        always: function () {
                        }
                    }
                });
            },
            _renderData: function (options) {
                var self = this;
                $('body').trigger('showLoadingPopup');
                $.ajax({
                    method: "GET",
                    data: {
                        'scope': options.scope,
                        'scope_id': options.scopeId,
                        'path': options.path,
                        'id': options.id
                    },
                    url: this.load_url
                }).done(function (data) {
                    if (data.error) {
                        $('body').trigger('hideLoadingPopup');
                        self._showError($.mage.__('An error occurred while loading data'));
                    } else {
                        self.configLog(data.data);
                        self.selectedValue(data.key);
                        $('body').trigger('hideLoadingPopup');
                        self.visible(true);
                        self._openPopup(options.label);
                    }
                });
            },
            _openPopup: function (title) {
                var self = this;
                modal({
                    autoOpen: true,
                    responsive: true,
                    clickableOverlay: false,
                    title: $.mage.__('Config logs for "%1":').replace('%1', title),
                    closed: function (e, modal) {
                        self.visible(false);
                        self.selectedValue();
                    },
                    buttons: [{
                        text: $.mage.__('Ok'),
                        class: 'action close-popup wide',
                        click: this.closeModal
                    }]
                }, this.popupId);
            }
        });
    }
);
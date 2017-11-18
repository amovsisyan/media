@extends('admin.index')

@section('content-body')
    <section id="create-post-panel">
        <div class="container">
            @include('admin.posts.crud.create-template')
        </div>
        <div class="container">
            <div class="row right-align m_t_50 post-create-btns">
                <a class="waves-effect waves-light btn">Finish & Test</a>
                <!-- Modal -->
                <a id='add_post' class="waves-effect waves-light btn modal-trigger" href="#modal_add_post">Finish & Add</a>
                <div id="modal_add_post" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Create Post?</h4>
                    </div>
                    <div class="modal-footer">
                        <a id='confirm_post' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
                        <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="deletePostPartModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Delete This Part?</h4>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat" id="confirm-part-delete">Delete</a>
                <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
            </div>
        </div>
    </section>
@endsection

@section('script')
    {{--require imageStandards --}}
    <script src="/js/admin/imageStandards.js"></script>

    <script>
        $(document).ready(function(){
            $('#subcategory_select').material_select();
            $('#hashtag_select').material_select();
            $('.modal').modal();
        });

        PostCreate = {
            defaultProperties: {
                partTemplateCounter: {},
                basicPartTemplate: document.getElementsByClassName('post-part')[0]
            },

            postMainImages: document.getElementsByClassName('main_image'),
            postPartAddButtons: document.getElementsByClassName('post-part-add-button'),
            localeSwitchInputs: document.getElementsByClassName('locale-switch-input'),

            confirmPartDeleteBtn: document.getElementById('confirm-part-delete'),

            _init: function () {
                this.addListeners();
                this.firstRenderPartTemplate();
            },

            addListeners: function () {
                var self = this;

                // add button listener
                Array.prototype.forEach.call(this.postPartAddButtons, (function (element, index, array) {
                        element.addEventListener('click', self.addPostPart.bind(self));
                    })
                );
                // main image listener
                Array.prototype.forEach.call(this.postMainImages, (function (element, index, array) {
                        element.addEventListener('change', self._imageSizeWarningLocal.bind(self));
                    })
                );
                // locale switcher generation and listeners
                Array.prototype.forEach.call(this.localeSwitchInputs, (function (element, index, array) {
                        element.addEventListener('change', self.localeSwitcher.bind(self));
                        element.classList.add('checked');
                        $(element).prop('checked', true)
                    })
                );
                //modal btn listeners
                this.confirmPartDeleteBtn.addEventListener('click', self.removePostPart.bind(self));
            },

            firstRenderPartTemplate: function () {
                this._initDefaultCounter();
                var self = this,
                    parts = document.getElementsByClassName('post-part');
                // render post parts at first
                Array.prototype.forEach.call(parts, (function (postPart, index, array) {
                    self.renderPartTemplate(postPart)
                }));
            },

            addPostPart: function(e) {
                // make clone and append
                var container = getClosest(e.target, '.part-locale-container'),
                    locale = container.dataset.localename,
                    innerContainer = container.getElementsByClassName('part-locale-inner-container')[0],
                    clone = this.defaultProperties.basicPartTemplate.cloneNode(true);

                innerContainer.appendChild(clone);

                var num = this.defaultProperties.partTemplateCounter[locale]++, // increase counter
                    lastAddedPostPart = innerContainer.getElementsByClassName('post-part')[num];
                this.renderPartTemplate(lastAddedPostPart);
            },

            renderPartTemplate: function (postPart) {
                // empty postPart
                this._emptyPostPart(postPart);
                // add ids
                this._makePostPartIds(postPart);
                // add post part listeners
                this._addPostPartListeners(postPart);
            },

            localeSwitcher: function (e) {
                var el = e.target;
                this._toggleSwitcher(el);
                this._proceedLocalePartEnablingDisabling(el);
            },

            passIdToPostPartDeleteModal: function (e) {
                var postPartLocaleContainer = getClosest(e.target, '.part-locale-container'),
                    postPart = getClosest(e.target, '.post-part'),
                    locale = postPartLocaleContainer.dataset.localename,
                    num = postPart.dataset.partnumber;

                this.confirmPartDeleteBtn.dataset.locale = locale;
                this.confirmPartDeleteBtn.dataset.partnumber = num;
            },

            removePostPart: function () {
                var locale = this.confirmPartDeleteBtn.dataset.locale,
                    num = this.confirmPartDeleteBtn.dataset.partnumber,
                    id = 'post-part-' + locale + '-' + num,
                    deletingPart = document.getElementById(id);

                if (this.defaultProperties.partTemplateCounter[locale] === 1) {
                    return false
                } else {
                    var postPartLocaleContainer = getClosest(deletingPart, '.part-locale-container');
                    deletingPart.remove();
                    this.defaultProperties.partTemplateCounter[locale] = num;
                    this._regenerateAfterPartDelete(postPartLocaleContainer);
                }
            },

            _initDefaultCounter: function () {
                var self = this,
                    partLocaleContainers = document.getElementsByClassName('part-locale-container');
                Array.prototype.forEach.call(partLocaleContainers, (function (currContainer, index, array) {
                    var starter = currContainer.getElementsByClassName('post-part').length, // int 1
                        localeName = currContainer.dataset.localename;

                    self.defaultProperties.partTemplateCounter[localeName] = starter;
                }));
            },

            _imageSizeWarningLocal: function(e) {
                var el = e.target,
                    files = el.files,
                    standard = this._getStandard(el);
                imageSizeWarning(files, standard);
            },

            _getStandard: function (element) {
                return element.classList.contains('part-image') ? imageStandards.partsImageStandard : imageStandards.mainImageStandard
            },

            _emptyPostPart: function (postPart) {
                postPart.getElementsByClassName('part-header')[0].value = '';
                postPart.getElementsByClassName('part-footer')[0].value = '';
                postPart.getElementsByClassName('part-image')[0].value = '';
                postPart.getElementsByClassName('file-path ')[0].value = '';
            },

            _makePostPartIds: function (postPart) {
                var postPartLocaleContainer = getClosest(postPart, '.part-locale-container'),
                    locale = postPartLocaleContainer.dataset.localename,
                    num = this.defaultProperties.partTemplateCounter[locale],
                    postNumber = postPartLocaleContainer.getElementsByClassName('post-number')[num - 1];

                postPart.id = 'post-part-' + locale + '-' + num;
                postPart.dataset.partnumber = num;
                postNumber.innerHTML = num;
            },

            _addPostPartListeners: function (postPart) {
                postPart
                    .getElementsByClassName('part-delete-button')[0]
                    .getElementsByClassName('modal-trigger')[0]
                    .addEventListener('click', this.passIdToPostPartDeleteModal.bind(this));

                postPart.getElementsByClassName('part-image')[0].addEventListener('change',
                    this._imageSizeWarningLocal.bind(this)
                );
            },

            _toggleSwitcher: function (el) {
                if (el.classList.contains('checked')) {
                    el.classList.remove('checked');
                    el.classList.add('unchecked');
                } else {
                    el.classList.add('checked');
                    el.classList.remove('unchecked');
                }
            },

            _regenerateAfterPartDelete: function(postPartLocaleContainer) {
                var self = this,
                    allPostParts = postPartLocaleContainer.getElementsByClassName('post-part'),
                    locale = postPartLocaleContainer.dataset.localename;

                Array.prototype.forEach.call(allPostParts, (function (element, index, array) {
                    var partNumber = element.dataset.partnumber;

                    if (partNumber > self.defaultProperties.partTemplateCounter[locale]) {
                        self._makePostPartIds(element);
                        self.defaultProperties.partTemplateCounter[locale]++;
                    }
                }));

                this.defaultProperties.partTemplateCounter[locale]--;
            },

            _proceedLocalePartEnablingDisabling: function (el) {
                var localeMainContainer = getClosest(el, '.main-locale-container'),
                    mainBtns = localeMainContainer.getElementsByClassName('btn'),
                    mainInputs = localeMainContainer.getElementsByClassName('input-area'),

                    locale = localeMainContainer.dataset.localename,
                    postPartLocaleContainer = document.getElementById('part-locale-' + locale),
                    localeBtns = postPartLocaleContainer.getElementsByClassName('btn'),
                    localeInputs = postPartLocaleContainer.getElementsByTagName('input'),

                    triggeredDisableEnableArr = [mainInputs, localeInputs],
                    classedDisableEnableArr = [mainBtns, localeBtns];

                if (el.classList.contains('checked')) {
                    this._doTriggerDisableEnable(triggeredDisableEnableArr, false);
                    this._doClassedDisableEnable(classedDisableEnableArr, false);
                } else {
                    this._doTriggerDisableEnable(triggeredDisableEnableArr, true);
                    this._doClassedDisableEnable(classedDisableEnableArr, true);
                }
            },

            _doTriggerDisableEnable: function (butchArr, bool) {
                Array.prototype.forEach.call(butchArr, (function (butch) {
                    Array.prototype.forEach.call(butch, (function (element, index, array) {
                        element.disabled = bool;
                    }));
                }));
            },

            _doClassedDisableEnable: function (butchArr, bool) {
                Array.prototype.forEach.call(butchArr, (function (butch) {
                    Array.prototype.forEach.call(butch, (function (element, index, array) {
                        if (bool) {
                            element.classList.add('disabled');
                        } else {
                            element.classList.remove('disabled');
                        }
                    }));
                }));
            }
        };

        PostCreate._init();
    </script>
@endsection
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
                        {{--<p></p>--}}
                    </div>
                    <div class="modal-footer">
                        <a id='confirm_post' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
                        <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        {{--<!-- Modal -->--}}
        {{--<div class="modal" id="saveConfirmModal">--}}
            {{--<div class="modal-content left-align">--}}
                {{--<h4>Are You Sure You Want Save This Changes?</h4>--}}
                {{--<p></p>--}}
            {{--</div>--}}
            {{--<div class="modal-footer">--}}
                {{--<a class="modal-action modal-close waves-effect waves-green btn-flat confirm-save-changes">Save</a>--}}
                {{--<a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>--}}
            {{--</div>--}}
        {{--</div>--}}
        <!-- Modal -->
        <div class="modal" id="deletePostPartModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Delete This Part?</h4>
                {{--<p></p>--}}
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat confirm-delete">Delete</a>
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

            addButton: document.getElementById('add_post'),
            confirmButton: document.getElementById('confirm_post'),
            postAlias: document.getElementById('alias'),
            postMainHeader: document.getElementById('main_header'),
            postMainText: document.getElementById('main_text'),
            postMainImages: document.getElementsByClassName('main_image'),
            postSubcategory: document.getElementById('subcategory_select'),
            hashtagSelect: document.getElementById('hashtag_select'),
            modalAddPost: document.getElementById('modal_add_post'),
            postPartAddButtons: document.getElementsByClassName('post-part-add-button'),
//            postCreateParts: document.getElementById('post-create-parts'),
            deletePostPartModal: document.getElementById('deletePostPartModal'),
            localeSwitchInputs: document.getElementsByClassName('locale-switch-input'),

            _init: function() {
                this.firstRenderPartTemplate();
                this.addListeners();
            },

            firstRenderPartTemplate: function () {
                var self = this,
                    templates = document.getElementsByClassName('post-part'),
                    partLocaleContainers = document.getElementsByClassName('part-locale-container');

                // render post parts at first
                Array.prototype.forEach.call(templates, (function (currTempl, index, array) {
                    self.renderPartTemplate(currTempl)
                }));

                // render counter starter
                Array.prototype.forEach.call(partLocaleContainers, (function (currContainer, index, array) {
                    var starter = currContainer.getElementsByClassName('post-part').length,
                        localeName = currContainer.dataset.localename;

                    self.defaultProperties.partTemplateCounter[localeName] = starter;
                }));
            },

            addListeners: function () {
                var self = this;

               // main image listener
                Array.prototype.forEach.call(this.postMainImages, (function (element, index, array) {
                        element.addEventListener('change', self._imageSizeWarningLocal.bind(self));
                    })
                );

                // add button listener
                Array.prototype.forEach.call(this.postPartAddButtons, (function (element, index, array) {
                        element.addEventListener('click', self.addPostPart.bind(self));
                    })
                );

                // locale switcher generation and listeners
                Array.prototype.forEach.call(this.localeSwitchInputs, (function (element, index, array) {
                        element.addEventListener('change', self.localeSwitcher.bind(self));
                        element.classList.add('checked');
                        $(element).prop('checked', true)
                    })
                );
            },

            renderPartTemplate: function(currTempl) {
                var self = this;

                self._regeneratePostPartIds(currTempl);
                self._emptyPostPartValues(currTempl);

//                currTempl.getElementsByClassName('part-delete-button')[0].addEventListener('click',
//                    this.createModelPartDelete
//                );

                currTempl.getElementsByClassName('part-image')[0].addEventListener('change',
                    this._imageSizeWarningLocal.bind(this)
                );
            },

            confirmPost: function(){
                var updateBtns = [this.addButton, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    hashtags = [],
                    xhr = new XMLHttpRequest(),
                    allPostParts = document.getElementsByClassName('post-part'),
                    formData = new FormData();

                Array.prototype.forEach.call(this.getHashtagList(), (function (element, index, array) {
                    var elementContent = element.getElementsByTagName('span')[0].textContent;
                    hashtags.push(explodeGetLast(elementContent, '_'))
                }));

                // Main
                formData.append("postAlias", this.postAlias.value);
                formData.append("postMainHeader", this.postMainHeader.value);
                formData.append("postMainText", this.postMainText.value);
                formData.append("postMainImage", this.postMainImage.files[0]);
                formData.append("postSubcategory", this.postSubcategory.options[this.postSubcategory.selectedIndex].value);
                formData.append("postHashtag", JSON.stringify(hashtags));

                // Parts
                Array.prototype.forEach.call(allPostParts, (function (element, index, array) {
                    formData.append('partHeader[]', element.getElementsByClassName('part-header')[0].value);

                    var fileContainer = element.getElementsByClassName('part-image')[0].files,
                        file = [];
                    if (fileContainer.length) {
                        file = element.getElementsByClassName('part-image')[0].files[0]
                    }
                    formData.append('partImage[' + index + ']', file);
                    formData.append('partFooter[]', element.getElementsByClassName('part-footer')[0].value);
                }));

                xhr.open('POST', location.pathname, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());

                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Added New Post');
                        self._regenerateAfterNewCreation();
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(formData);
            },

            confirmPartRemove: function(e) {
                var self = PostCreate,
                    id = self.deletePostPartModal.getElementsByClassName('confirm-delete')[0].getAttribute('data-id'),
                    currElem = document.getElementById('post-id-'+id);


                if(self.defaultProperties.partTemplateCounter == 1 && id) {return false};
                self.defaultProperties.partTemplateCounter = id;

                currElem.remove();
                self.regenerateAfterPartDelete();
            },

            regenerateAfterPartDelete: function() {
                var self = this,
                    allPostParts = document.getElementsByClassName('post-part');

                Array.prototype.forEach.call(allPostParts, (function (element, index, array) {
                    var arr = element.id.split('-'),
                        id = arr[arr.length-1];
                    if (id > self.defaultProperties.partTemplateCounter) {
                        self._regeneratePostPartIds(element);
                        self.defaultProperties.partTemplateCounter++;
                    }
                }));

                self.defaultProperties.partTemplateCounter--;
            },

            addPostPart: function(e) {
                var container = getClosest(e.target, '.part-locale-container'),
                    locale = container.dataset.localename,
                    innerContainer = container.getElementsByClassName('part-locale-inner-container')[0];
                    clone = this.defaultProperties.basicPartTemplate.cloneNode(true);

                innerContainer.appendChild(clone);

                var num = ++this.defaultProperties.partTemplateCounter[locale],
                    lastAddedPostPart = innerContainer.getElementsByClassName('post-part')[num - 1];

                this.renderPartTemplate(lastAddedPostPart);
            },

            getHashtagList: function() {
                var hashtags = getClosest(this.hashtagSelect, '.select-wrapper')
                    .getElementsByClassName('multiple-select-dropdown')[0]
                    .getElementsByClassName('active');
                return hashtags ? hashtags : [];
            },

            localeSwitcher: function (e) {
                var el = e.target;
                this.toggleSwitcher(el);
                this.proceedLocalePartEnablingDisabling(el);
            },

            toggleSwitcher: function (el) {
                if (el.classList.contains('checked')) {
                    el.classList.remove('checked');
                    el.classList.add('unchecked');
                } else {
                    el.classList.add('checked');
                    el.classList.remove('unchecked');
                }
            },

            proceedLocalePartEnablingDisabling: function (el) {
                var localeMainContainer = getClosest(el, '.main-locale-container'),
                    localeMainAllInputs = localeMainContainer.getElementsByClassName('input-field');
                    inputAreas = localeMainContainer.getElementsByClassName('input-area');

                if (el.classList.contains('checked')) {
                    Array.prototype.forEach.call(inputAreas, (function (element, index, array) {
                            element.disabled = false;
                        })
                    );
                } else {
                    Array.prototype.forEach.call(inputAreas, (function (element, index, array) {
                            element.disabled = true;
                        })
                    );
                }
            },

            _regeneratePostPartIds: function(element) {
                var container = getClosest(element, '.part-locale-container'),
                    locale = container.dataset.localename,
                    currId = this.defaultProperties.partTemplateCounter[locale];

                element.dataset.id = this.defaultProperties.partTemplateCounter[locale];
                element.id = 'part-id-' + locale + '-' + currId;
            },

            _regenerateAfterNewCreation: function(element) {
                this.defaultProperties.partTemplateCounter = 1;
                var self = this,
                    allParts = document.getElementsByClassName('post-part');

                Array.prototype.forEach.call(allParts, (function (element, index, array) {
                        if (index === 0) {
                            self._emptyPostPartValues(element);
                        } else {
                            element.remove();
                        }
                    })
                );
                this.postAlias.value = '';
                this.postMainHeader.value = '';
                this.postMainText.value = '';
                this.postMainImage.value = '';
                document.getElementById('post-create-main').getElementsByClassName('file-path')[0].value = '';
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

            _emptyPostPartValues: function (element) {
                element.getElementsByClassName('part-header')[0].value = '';
                element.getElementsByClassName('part-footer')[0].value = '';
                element.getElementsByClassName('part-image')[0].value = '';
                element.getElementsByClassName('file-path ')[0].value = '';
            },


//            createModelPartDelete: function(e) {
//                var self = PostCreate,
//                    currElem = getClosest(e.target, '.post-part'),
//                    paragraph = self.deletePostPartModal.getElementsByTagName('p')[0],
//                    postParts = document.getElementsByClassName('post-part'),
//                    _html = '';
//
//                if (postParts.length == 1) {
//                    _html = "<h4 class='red-text'>Can't Delete Last Part</h4>"
//                } else {
//                    _html = '<p>Post N_' + currElem.getElementsByClassName('post-number')[0].innerHTML + '</p>' +
//                        '<p>Post Header: ' + currElem.getElementsByClassName('part-header')[0].value + '</p>' +
//                        '<p>Post Footer: ' + currElem.getElementsByClassName('part-footer')[0].value + '</p>';
//                }
//
//                paragraph.innerHTML = _html;
//                self.deletePostPartModal.getElementsByClassName('confirm-delete')[0].dataset.id = currElem.getAttribute('data-id');
//
//                self.deletePostPartModal.getElementsByClassName('confirm-delete')[0].addEventListener('click',
//                    self.confirmPartRemove
//                );
//            },

//            createModalContent: function() {
//                var content = this.modalAddPost.getElementsByClassName('modal-content')[0],
//                    paragraph = content.getElementsByTagName('p')[0],
//                    _html = '<p>Header: ' + this.postMainHeader.value + '</p>' +
//                        '<p>Main Text: ' + this.postMainText.value + '</p>' +
//                        '<p>Category: ' + this.postSubcategory.options[this.postSubcategory.selectedIndex].text + '</p>';
//
//                paragraph.innerHTML = _html;
//            },
        };

        PostCreate.confirmButton.addEventListener('click', PostCreate.confirmPost.bind(PostCreate));
//        PostCreate.addButton.addEventListener('click', PostCreate.createModalContent.bind(PostCreate));
//        PostCreate.postPartAddButton.addEventListener('click', PostCreate.addPostPart.bind(PostCreate));
        PostCreate._init();
    </script>
@endsection
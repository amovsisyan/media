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
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <a id='confirm_post' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
                        <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal" id="saveConfirmModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Save This Changes?</h4>
                <p></p>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat confirm-save-changes">Save</a>
                <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal" id="deletePostPartModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Delete This Part?</h4>
                <p></p>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat confirm-delete">Delete</a>
                <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('#subcategory_select').material_select();
            $('#hashtag_select').material_select();
            $('.modal').modal();
        });

        PostCreate = {
            defaultProperties: {
                partTemplateCounter: 0,
                basicPartTemplate: document.querySelector('.post-part')
            },
            addButton: document.getElementById('add_post'),
            confirmButton: document.getElementById('confirm_post'),
            postAlias: document.getElementById('alias'),
            postMainHeader: document.getElementById('main_header'),
            postMainText: document.getElementById('main_text'),
            postMainImage: document.getElementById('main_image'),
            postSubcategory: document.getElementById('subcategory_select'),
            hashtagSelect: document.getElementById('hashtag_select'),
            modalAddPost: document.getElementById('modal_add_post'),
            postPartAddButton: document.getElementById('post-part-add-button'),
            postCreateParts: document.getElementById('post-create-parts'),
            deletePostPartModal: document.getElementById('deletePostPartModal'),

            _init: function() {
                this.renderPartTemplate();
            },

            renderPartTemplate: function() {
                var num = this.defaultProperties.partTemplateCounter++,
                    currTempl = document.querySelectorAll('.post-part')[num];
                this._regeneratePostPartIds(currTempl);

                currTempl.querySelector('.part-header').value = '';
                currTempl.querySelector('.part-footer').value = '';
                currTempl.querySelector('.file-path ').value = '';

                currTempl.querySelector('.part-delete-button').addEventListener('click',
                    this.createModelPartDelete
                );
            },

            confirmPost: function(){
                var self = this,
                    hashtags = [],
                    xhr = new XMLHttpRequest(),
                    allPostParts = document.querySelectorAll('.post-part'),
                    formData = new FormData();

                Array.prototype.forEach.call(this.getHashtagList(), (function (element, index, array) {
                    var elementContent = element.querySelector('span').textContent;
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
                    formData.append('partHeader[]', element.querySelector('.part-header').value);

                    var fileContainer = element.querySelector('.part-image').files,
                        file = [];
                    if (fileContainer.length) {
                        file = element.querySelector('.part-image').files[0]
                    }
                    formData.append('partImage[' + index + ']', file);
                    formData.append('partFooter[]', element.querySelector('.part-footer').value);
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
                    self.updateAddConfirmButtons();
                };
                xhr.send(formData);
            },

            updateAddConfirmButtons: function() {
                this.addButton.classList.remove('disabled');
                this.confirmButton.classList.remove('disabled');
            },

            confirmPartRemove: function(e) {
                var self = PostCreate,
                    id = self.deletePostPartModal.querySelector('.confirm-delete').getAttribute('data-id'),
                    currElem = document.getElementById('post-id-'+id);


                if(self.defaultProperties.partTemplateCounter == 1 && id) {return false};
                self.defaultProperties.partTemplateCounter = id;

                currElem.remove();
                self.regenerateAfterDelete();
            },

            regenerateAfterDelete: function() {
                var self = this,
                    allPostParts = document.querySelectorAll('.post-part');

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

            _regeneratePostPartIds: function(element) {
                element.dataset.id = this.defaultProperties.partTemplateCounter;
                element.id = 'post-id-' + this.defaultProperties.partTemplateCounter;
                element.querySelector('.post-number').innerHTML = this.defaultProperties.partTemplateCounter;
            },

            _regenerateAfterNewCreation: function(element) {
                this.defaultProperties.partTemplateCounter = 1;
                var allParts = document.querySelectorAll('.post-part');

                Array.prototype.forEach.call(allParts, (function (element, index, array) {
                        if (index === 0) {
                            element.querySelector('.part-header').value = '';
                            element.querySelector('.part-footer').value = '';
                            element.querySelector('.part-image').value = '';
                            element.querySelector('.file-path').value = '';
                        } else {
                            element.remove();
                        }
                    })
                );
                this.postAlias.value = '';
                this.postMainHeader.value = '';
                this.postMainText.value = '';
                this.postMainImage.value = '';
                document.getElementById('post-create-main').querySelector('.file-path').value = '';
            },

            createModelPartDelete: function(e) {
                var self = PostCreate,
                    currElem = getClosest(e.target, '.post-part'),
                    paragraph = self.deletePostPartModal.querySelector('p'),
                    postParts = document.querySelectorAll('.post-part'),
                    _html = '';

                if (postParts.length == 1) {
                    _html = "<h4 class='red-text'>Can't Delete Last Part</h4>"
                } else {
                    _html = '<p>Post N_' + currElem.querySelector('.post-number').innerHTML + '</p>' +
                        '<p>Post Header: ' + currElem.querySelector('.part-header').value + '</p>' +
                        '<p>Post Footer: ' + currElem.querySelector('.part-footer').value + '</p>';
                }

                paragraph.innerHTML = _html;
                self.deletePostPartModal.querySelector('.confirm-delete').dataset.id = currElem.getAttribute('data-id');

                self.deletePostPartModal.querySelector('.confirm-delete').addEventListener('click',
                    self.confirmPartRemove
                );
            },

            createModalContent: function() {
                var content = this.modalAddPost.querySelector('.modal-content'),
                    paragraph = content.querySelector('p'),
                    _html = '<p>Header: ' + this.postMainHeader.value + '</p>' +
                        '<p>Main Text: ' + this.postMainText.value + '</p>' +
                        '<p>Category: ' + this.postSubcategory.options[this.postSubcategory.selectedIndex].text + '</p>';

                paragraph.innerHTML = _html;
            },

            addPostPart: function() {
                var clone = this.defaultProperties.basicPartTemplate.cloneNode(true);
                this.postCreateParts.appendChild(clone);
                this.renderPartTemplate();
            },

            getHashtagList: function() {
                var hashtags = getClosest(this.hashtagSelect, '.select-wrapper').querySelector('.multiple-select-dropdown').querySelectorAll('.active');
                return hashtags ? hashtags : [];
            }
        };

        PostCreate.confirmButton.addEventListener('click', PostCreate.confirmPost.bind(PostCreate));
        PostCreate.addButton.addEventListener('click', PostCreate.createModalContent.bind(PostCreate));
        PostCreate.postPartAddButton.addEventListener('click', PostCreate.addPostPart.bind(PostCreate));
        PostCreate._init();
    </script>
@endsection
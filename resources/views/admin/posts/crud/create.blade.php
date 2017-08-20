@extends('admin.index')

@section('content-body')
    <section id="create-post-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Create Post</h4>
                <h6>(All fields with <span class="important_icon">*</span> are required)</h6>
            </div>
            <div class="row" id="post-create-main">
                <div class="col s12">
                    <h4>Main</h4>
                </div>
                <div class="col m7 s12">
                    <div class="input-field">
                        <input id="alias" name="alias" type="text" class="validate">
                        <label for="alias">Alias (English) <span class="important_icon">*</span></label>
                    </div>
                </div>
                <div class="col s12">
                    <div class="input-field">
                        <textarea id="main_header" name="main_header" class="materialize-textarea"></textarea>
                        <label for="main_header">Header (Russian) <span class="important_icon">*</span></label>
                    </div>
                </div>
                <div class="col s12">
                    <div class="input-field">
                        <textarea id="main_text" name="main_text" class="materialize-textarea"></textarea>
                        <label for="main_text">Main Text (Russian) <span class="important_icon">*</span></label>
                    </div>
                </div>
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>Main Image <span class="important_icon">*</span></span>
                            <input type="file" id="main_image" name="main_image" accept="image/*" enctype="multipart/form-data">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                    </div>
                </div>
                <div class="input-field col m7 s12">
                    <select id="subcategory_select">
                        @foreach ($response['categories'] as $category)
                            <optgroup label="{{ $category[0]['name'] }}">
                                @foreach ($category['subcategory'] as $subcategory)
                                    <option value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <label>Select Subcategory <span class="important_icon">*</span></label>
                </div>
                <div class="input-field col m7 s12">
                    <select multiple id="hashtag_select">
                        <option value="" disabled selected>Select Hashtag</option>
                        @foreach ($response['hashtags'] as $hashtag)
                            <option value="{{ $hashtag['id'] }}">{{ $hashtag['hashtag'] }}_{{ $hashtag['id'] }}</option>
                        @endforeach
                    </select>
                    <label>You can choose multiple hashtags <span class="important_icon">*</span></label>
                </div>
            </div>
            <div class="row" id="post-create-parts">
                <div class="col s12">
                    <h4>Parts</h4>
                </div>
                @include('admin.posts.crud.create-part')
            </div>
            <div class="row right-align post-part-add-button-container">
                <a class="waves-effect waves-light btn" id="post-part-add-button">+Add Part</a>
            </div>
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
            addButton: document.querySelector('#add_post'),
            confirmButton: document.querySelector('#confirm_post'),
            postAlias: document.querySelector('#alias'),
            postMainHeader: document.querySelector('#main_header'),
            postMainText: document.querySelector('#main_text'),
            postMainImage: document.querySelector('#main_image'),
            postSubcategory: document.querySelector('#subcategory_select'),
            hashtagSelect: document.querySelector('#hashtag_select'),
            modalAddPost: document.querySelector('#modal_add_post'),
            postPartAddButton: document.querySelector('#post-part-add-button'),
            postCreateParts: document.querySelector('#post-create-parts'),

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
                    hashtags.push(element.querySelector('span').textContent)
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
                    formData.append('partImage[]', file);

                    formData.append('partFooter[]', element.querySelector('.part-footer').value);
                }));

                xhr.open('POST', location.pathname, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());

                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(xhr.status, 'Added New Post', 'status_ok');
                        if (xhr.status === 200) {
                            self._regenerateAfterNewCreation();
                        };
                        self.updateAddConfirmButtons();
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        if (response.response && response.type) {
                            var errors = response.response,
                                _html = response.type + ': ';
                            errors.forEach(function (element, index, array) {
                                _html += element;
                            });
                        } else {
                            _html = 'Something Was Wrong'
                        }
                        handleResponseToast(xhr.status, _html, 'status_warning');
                        self.updateAddConfirmButtons();
                    }
                };
                xhr.send(formData);
            },

            updateAddConfirmButtons: function() {
                this.addButton.classList.remove('disabled');
                this.confirmButton.classList.remove('disabled');
            },

            confirmPartRemove: function(e) {
                var currElem = getClosest(e.target, '.post-part'),
                    arr =  currElem.id.split('-');

                if(PostCreate.defaultProperties.partTemplateCounter == 1 && arr[arr.length-1]) {return false};
                PostCreate.defaultProperties.partTemplateCounter = arr[arr.length-1];

                currElem.remove();
                //can't access to rela this, that's why call PostCreate
                PostCreate.regenerateAfterDelete();
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
                element.querySelector('.modal-trigger').href = '#modal_delete_part_' + this.defaultProperties.partTemplateCounter;
                element.querySelector('.modal').id = 'modal_delete_part_' + this.defaultProperties.partTemplateCounter;
                $('#modal_delete_part_' + this.defaultProperties.partTemplateCounter).modal();
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
                document.querySelector('#post-create-main').querySelector('.file-path').value = '';
            },

            createModelPartDelete: function(e) {
                var currElem = getClosest(e.target, '.post-part'),
                    content = currElem.querySelector('.modal-content'),
                    paragraph = content.querySelector('p'),
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

                currElem.querySelector('.confirm-delete').addEventListener('click',
                    //can't access to real this, that's why call PostCreate
                    PostCreate.confirmPartRemove
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
                var hashtags = getClosest(this.hashtagSelect, '.select-wrapper').querySelector('.multiple-select-dropdown').querySelector('.active');
                return hashtags ? hashtags : [];
            }
        };

        PostCreate.confirmButton.addEventListener('click', PostCreate.confirmPost.bind(PostCreate));
        PostCreate.addButton.addEventListener('click', PostCreate.createModalContent.bind(PostCreate));
        PostCreate.postPartAddButton.addEventListener('click', PostCreate.addPostPart.bind(PostCreate));
        PostCreate._init();
    </script>
@endsection
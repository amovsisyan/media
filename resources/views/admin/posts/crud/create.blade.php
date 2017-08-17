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
                basicPartTemplate: document.getElementsByClassName('post-part')[0],
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

            _init: function() {
                this.renderPartTemplate();
            },

            renderPartTemplate: function() {
                var num = this.defaultProperties.partTemplateCounter++,
                    currTempl = document.getElementsByClassName('post-part')[num];
                this._regeneratePostPartIds(currTempl);

                currTempl.getElementsByClassName('part_header')[0].value = '';
                currTempl.getElementsByClassName('part_footer')[0].value = '';
                currTempl.getElementsByClassName('file-path ')[0].value = '';

                currTempl.getElementsByClassName('part-delete-button')[0].addEventListener('click',
                    this.createModelPartDelete
                );
            },

            confirmPost: function(){
                var self = this,
                    hashtags = [],
                    xhr = new XMLHttpRequest(),
                    formData = new FormData();

                Array.prototype.forEach.call(this.getHashtagList(), (function (element, index, array) {
                    hashtags.push(element.getElementsByTagName('span')[0].textContent)
                }));

                formData.append("post_alias", this.postAlias.value);
                formData.append("post_main_header", this.postMainHeader.value);
                formData.append("post_main_text", this.postMainText.value);
                formData.append("post_subcategory", this.postSubcategory.options[this.postSubcategory.selectedIndex].value);
                formData.append("post_hashtag", JSON.stringify(hashtags));
                formData.append("post_main_image", this.postMainImage.files[0]);

                xhr.open('POST', location.pathname, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());

                xhr.onload = function() {
//                    var response = JSON.parse(xhr.responseText);
//                    if (xhr.status === 200 && response.error !== true) {
//                        self.handleResponseToast(xhr.status, 'Added New Hashtag', 'status_ok');
//                        if (xhr.status === 200) {
//                            self.hashtagName.value = '';
//                            self.hashtagAlias.value = '';
//                        }
//                    }
//                    else if (xhr.status !== 200 || response.error === true) {
//                        if (response.response && response.validate_error === true) {
//                            var errors = response.response,
//                                _html = '';
//                            errors.forEach(function (element, index, array) {
//                                _html += element;
//                            });
//                        } else {
//                            _html = 'Something Was Wrong'
//                        }
//                        self.handleResponseToast(xhr.status, _html, 'status_warning');
//                    }
                };
                xhr.send(formData);
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

            _regeneratePostPartIds: function(element) {
                element.dataset.id = this.defaultProperties.partTemplateCounter;
                element.id = 'post-id-' + this.defaultProperties.partTemplateCounter;
                element.getElementsByClassName('post-number')[0].innerHTML = this.defaultProperties.partTemplateCounter;
                element.getElementsByClassName('modal-trigger')[0].href = '#modal_delete_part_' + this.defaultProperties.partTemplateCounter;
                element.getElementsByClassName('modal')[0].id = 'modal_delete_part_' + this.defaultProperties.partTemplateCounter;
                $('#modal_delete_part_' + this.defaultProperties.partTemplateCounter).modal();
            },

            createModelPartDelete: function(e) {
                var currElem = getClosest(e.target, '.post-part'),
                    content = currElem.getElementsByClassName('modal-content'),
                    paragraph = content[0].getElementsByTagName('p')[0],
                    _html = '<p>Post N_' + currElem.getElementsByClassName('post-number')[0].innerHTML + '</p>' +
                        '<p>Post Header: ' + currElem.getElementsByClassName('part_header')[0].value + '</p>' +
                        '<p>Post Footer: ' + currElem.getElementsByClassName('part_footer')[0].value + '</p>';

                paragraph.innerHTML = _html;

                currElem.getElementsByClassName('confirm-delete')[0].addEventListener('click',
                    //can't access to rela this, that's why call PostCreate
                    PostCreate.confirmPartRemove
                );

            },

            createModalContent: function() {
                var content = this.modalAddPost.getElementsByClassName('modal-content'),
                    paragraph = content[0].getElementsByTagName('p')[0],
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

            handleResponseToast: function(status, text, style) {
                Materialize.toast(text, 5000, 'rounded');
                var toasts = document.getElementById("toast-container").getElementsByClassName("toast "),
                    toast = toasts[toasts.length-1];

                toast.classList.add(style);
                this.addButton.classList.remove('disabled');
                this.confirmButton.classList.remove('disabled');
            },

            getHashtagList: function() {
                return getClosest(this.hashtagSelect, '.select-wrapper').getElementsByClassName('multiple-select-dropdown')[0].getElementsByClassName('active');
            }
        };

        PostCreate.confirmButton.addEventListener('click', PostCreate.confirmPost.bind(PostCreate));
        PostCreate.addButton.addEventListener('click', PostCreate.createModalContent.bind(PostCreate));
        PostCreate.postPartAddButton.addEventListener('click', PostCreate.addPostPart.bind(PostCreate));
        PostCreate._init();
    </script>
@endsection
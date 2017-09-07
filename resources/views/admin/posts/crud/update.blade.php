@extends('admin.index')

@section('content-body')
    <section id="edit-post-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Edit Post</h4>
                <h6>(Select search type, after add search value and Search)</h6>
            </div>
            <div class="row">
                <div class="input-field col m4 s12">
                    <select id="search-type">
                        <option value="" disabled selected>Search type</option>
                        <option value="1">by ID</option>
                        <option value="2">by Header</option>
                        <option value="3">by Alias</option>
                    </select>
                </div>
                <div class="input-field col m7 s12">
                    <input id="search-text" type="text" class="validate">
                    <label for="search-input">Search text</label>
                </div>
                <div class="col m1 s12">
                    <a class="btn-floating waves-effect waves-light red" id="search-button"><i class="material-icons">search</i></a>
                </div>
            </div>
        </div>
        <div class="container" id="search-result">
            @include('admin.posts.crud.update-parts')
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
            $('#search-type').material_select();
        });

        PostEdit = {
            partTemplateCounter: 0,
            searchButton: document.getElementById('search-button'),
            searchTypeSelect: document.getElementById('search-type'),
            searchText: document.getElementById('search-text'),
            partTemplate: document.querySelector('.part-template'),
            partDetailTemplate: document.querySelector('.part-detail-template'),
            createPart: document.querySelector('.create-part'),
            partNoResult: document.querySelector('.part-no-result'),
            searchResultContainer: document.getElementById('search-result'),
            saveConfirmModal: document.getElementById('saveConfirmModal'),
            subcategorySelect: document.getElementById('subcategory_select'),
            deletePostPartModal: document.getElementById('deletePostPartModal'),

            searchPostRequest: function(){
                this.searchButton.classList.add('disabled');

                var self = this,
                    data = 'searchType=' + this.searchTypeSelect.value
                        + '&searchText=' + this.searchText.value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        self.makeParts(response);
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    self.searchButton.classList.remove('disabled');
                };
                xhr.send(encodeURI(data));
            },

            makeParts: function(response) {
                var self = this;
                self.searchResultContainer.innerHTML = '';
                if (response.response && response.response.posts.length) {
                    Array.prototype.forEach.call(response.response.posts, (function (element, index, array) {
                        var clone = self.partTemplate.cloneNode(true);

                        clone.querySelector('.part-number').innerHTML = ++index;
                        clone.querySelector('.post-id').innerHTML = 'ID = ' + element.id;
                        clone.querySelector('.part-header').value = element.header;
                        clone.querySelector('.part-text').value = element.text;
                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;
                        self.searchResultContainer.appendChild(clone);
                        clone.classList.remove('hide');
                        clone.querySelector('.part-details-button').addEventListener('click',
                            self.partDetailsRequest.bind(PostEdit)
                        );
                    }));
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            partDetailsRequest: function(e) {
                var self = this,
                    currElem = getClosest(e.target, '.part-template'),
                    partDetailsButtons = document.querySelectorAll('.part-details-button'),
                    data = 'postId=' + currElem.dataset.id,
                    xhr = new XMLHttpRequest();

                partDetailsButtons.forEach(function (element, index, array) {
                    element.classList.add('disabled');
                });

                xhr.open('POST', location.pathname + '/edit_details');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        self.makeDetailsPart(response);
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    partDetailsButtons.forEach(function (element, index, array) {
                        element.classList.remove('disabled');
                    });
                };
                xhr.send(encodeURI(data));
            },

            makeDetailsPart: function(response) {
                var self = this,
                    res = response.response;
                self.searchResultContainer.innerHTML = '';
                if (res && res.post && res.categories && res.categories.length && res.hashtags && res.hashtags.length) {
                    this.partDetailTemplate.classList.remove('hide');
                    this.createPart.remove();
                    var cloneMain = this.partDetailTemplate.cloneNode(true);

                    self._initializeMainPart(cloneMain, res.post);

                    self._initializeCategoriesSelect(cloneMain, response.response.categories, res.post.subcateg_id);

                    self._initializeHashtagSelect(cloneMain, response.response.hashtags, res.post.hashtags);

                    if (res.post.postparts) {
                        self._initializePostParts(cloneMain, res.post.postparts);
                    }

                    this.searchResultContainer.appendChild(cloneMain);
                    document.getElementById('post-part-add-button').addEventListener('click',
                        self.addPostPart.bind(this)
                    );
                    $('#subcategory_select').material_select();
                    $('#hashtag_select').material_select();
                    $('.modal').modal();
                } else {
                    this.partNoResult.classList.remove('hide');
                    this.searchResultContainer.appendChild(this.partNoResult);
                }
            },

            _initializeMainPart: function(apendingElement, post) {
                apendingElement.querySelector('#post-create-main').dataset.id = post.id;
                apendingElement.querySelector('#alias').value = post.alias;
                apendingElement.querySelector('#main_header').value = post.header;
                apendingElement.querySelector('#main_text').value = post.text;
                apendingElement.querySelector('.file-path').value = post.image;
            },

            _initializeCategoriesSelect: function(apendingElement, categories, selectedSubcat) {
                categories.forEach(function (element, index, array) {
                    var optGroup = document.createElement("optgroup");
                    optGroup.value = element.category.name;
                    element.subcategory.forEach(function (el, i, arr) {
                        var opt = document.createElement("option");
                        opt.value = el.id;
                        opt.innerHTML = el.name;
                        if (el.id ===  selectedSubcat) {
                            opt.selected = true;
                        }
                        optGroup.appendChild(opt);
                    });
                    apendingElement.querySelector('#subcategory_select').appendChild(optGroup);
                });
            },

            _initializeHashtagSelect: function(apendingElement, hashtags, selectedHashtags) {
                hashtags.forEach(function (element, index, array) {
                    var opt = document.createElement("option");
                    opt.value = element.id;
                    opt.innerHTML = element.hashtag + '_' + element.id;
                    if (selectedHashtags && selectedHashtags.indexOf(element.id) !== -1) {
                        opt.selected = true;
                    }
                    apendingElement.querySelector('#hashtag_select').appendChild(opt);
                });
            },

            _initializePostParts: function(apendingElement, postparts) {
                var self = this;
                postparts.forEach(function (element, index, array) {
                    var clonePart = self.createPart.cloneNode(true);
                    clonePart.querySelector('.post-number').innerHTML = ++index;
                    self.partTemplateCounter = index;
                    clonePart.dataset.number = index;
                    clonePart.querySelector('.part-header').value = element.head;
                    clonePart.querySelector('.file-path').value = element.body;
                    clonePart.querySelector('.part-footer').value = element.foot;
                    clonePart.querySelector('.part-delete-button').addEventListener('click',
                        self.createModalPostPartDelete.bind(self)
                    );
                    apendingElement.querySelector('#parts-container').appendChild(clonePart);
                })
            },

            createModalPostPartDelete: function(e) {
                var self = this,
                    deletePostPartModal = document.getElementById('deletePostPartModal'),
                    currElem = getClosest(e.target, '.post-part'),
                    paragraph = deletePostPartModal.querySelector('p'),
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
                deletePostPartModal.querySelector('.confirm-delete').dataset.number = currElem.querySelector('.post-number').innerHTML;
            },

            addPostPart: function() {
                var self = this,
                    clone = this.createPart.cloneNode(true);
                clone.querySelector('.post-number').innerHTML = ++this.partTemplateCounter;
                clone.dataset.number = this.partTemplateCounter;
                clone.querySelector('.part-delete-button').addEventListener('click',
                    self.createModalPostPartDelete.bind(self)
                );
                document.getElementById('parts-container').appendChild(clone);
            },

            confirmPartRemove: function(еlem) {
                var self = this,
                    currElem = еlem.target,
                    deletingNumber = currElem.dataset.number,
                    postParts = document.querySelectorAll('.post-part');
                if (postParts.length == 1) {
                    return
                }
                Array.prototype.forEach.call(document.querySelectorAll('.post-part'), (function (element, index, array) {
                    if (element.dataset.number === deletingNumber) {
                        self.partTemplateCounter = element.querySelector('.post-number').innerHTML;
                        element.remove();
                    } else if (element.dataset.number > self.partTemplateCounter) {
                        element.dataset.number = self.partTemplateCounter++;
                        element.querySelector('.post-number').innerHTML = element.dataset.number;
                    }
                }));
                this.partTemplateCounter--;
            }
        };
        PostEdit.searchButton.addEventListener('click', PostEdit.searchPostRequest.bind(PostEdit));
        PostEdit.deletePostPartModal.addEventListener('click', PostEdit.confirmPartRemove.bind(PostEdit));
//        document.getElementById('deletePostPartModal').querySelector('.confirm-delete').addEventListener('click',
//            this.confirmPartRemove.bind(this)
//        );
    </script>
@endsection
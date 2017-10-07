@extends('admin.index')

@section('content-body')
    <section id="attach-part">
        <div class="container">
            <div class="row center-align">
                <h4>ATTACH PART TO NEW POST</h4>
                <h6>(Select search type, after add search value and Search to get attaching new Post)</h6>
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
            @include('admin.posts.crud.attach-part-before')
        </div>
    </section>
    <section id="finish-attaching">
        <div class="container">
            <div class="row">
                <div id="selected-post-container" class="input-field col m5 s12 hide">
                    <input disabled id="selected-post" type="text" class="validate">
                </div>
                <div id="selected-post-part-container" class="input-field col m5 s12">
                    <input disabled  value="{{$response['postpart']['head']}}" id="selected-post-part" type="text" class="validate">
                </div>
                <div class="input-field col m2 s12">
                    <a class="modal-trigger waves-effect waves-orange orange btn-flat" id="attaching-btn" href="#confirmAttachingModal">Attach</a>
                </div>
            </div>
        </div>
    </section>
    <!-- Modal -->
    <div class="modal" id="confirmAttachingModal">
        <div class="modal-content left-align">
            <h4>Are You Sure You Want Remove from Old Post and Attach to New Post?</h4>
            <p></p>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close waves-effect waves-orange btn-flat orange" id="confirm-attaching-btn">Attach</a>
            <a class="modal-action modal-close waves-effect waves-orange btn-flat">Cancel</a>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('#search-type').material_select();
            $('.modal').modal();
        });

        AttachPart = {
            searchButton: document.getElementById('search-button'),
            searchTypeSelect: document.getElementById('search-type'),
            searchText: document.getElementById('search-text'),
            searchResultContainer: document.getElementById('search-result'),
            partTemplate: document.getElementsByClassName('part-template')[0],
            partNoResult: document.getElementById('part-no-result'),
            selectedPostContainer: document.getElementById('selected-post-container'),
            selectedPost: document.getElementById('selected-post'),

            attachingBtn: document.getElementById('attaching-btn'),
            confirmAttachingBtn: document.getElementById('confirm-attaching-btn'),

            searchPostRequest: function(){
                var updateBtns = [this.searchButton];
                updateAddConfirmButtons(updateBtns, true);

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
                        self.makePosts(response);
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            makePosts: function(response) {
                var self = this;
                self.searchResultContainer.innerHTML = '';
                if (response.response && response.response.posts.length) {
                    self.partNoResult.classList.add('hide');
                    Array.prototype.forEach.call(response.response.posts, (function (element, index, array) {
                        self.partTemplate.classList.remove('hide');
                        var clone = self.partTemplate.cloneNode(true);
                        clone.getElementsByClassName('post-id')[0].innerHTML = element.id;
                        clone.getElementsByClassName('post-header')[0].value = element.header;
                        clone.getElementsByClassName('post-text')[0].value = element.text;
                        clone.id = 'search-post-id-' + element.id;
                        clone.getElementsByClassName('post-select-btn')[0].addEventListener('click',
                            self.selectPost.bind(self)
                        );
                        self.searchResultContainer.appendChild(clone);
                    }));
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            selectPost: function (e) {
                var el = getClosest(e.target, '.part-template');
                this.selectedPostContainer.classList.remove('hide');
                this.selectedPost.value = el.getElementsByClassName('post-header')[0].value;
                this.selectedPost.dataset.id = el.getElementsByClassName('post-id')[0].innerHTML;
            },

            confirmAttachingRequest: function(){
                var updateBtns = [this.attachingBtn, this.confirmAttachingBtn];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    data = 'newPostId=' + this.selectedPost.dataset.id,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/save');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Attached Successfully');
                        self.regenerateAfterAttach();
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            regenerateAfterAttach: function () {
                this.searchResultContainer.innerHTML = '';
                this.selectedPostContainer.classList.add('hide');
                this.searchText.value = '';
            }
        };

        AttachPart.searchButton.addEventListener('click', AttachPart.searchPostRequest.bind(AttachPart));
        AttachPart.confirmAttachingBtn.addEventListener('click', AttachPart.confirmAttachingRequest.bind(AttachPart));
    </script>
@endsection
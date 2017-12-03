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
                        <option value="2">by Alias</option>
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
            @include('admin.posts.crud.update-before')
        </div>

        <!-- Modal -->
        <div class="modal" id="deletePostModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Delete This Post?</h4>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat red" id="post-confirm-delete">Delete</a>
                <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('#search-type').material_select();
            $('.collapsible').collapsible();
            $('.modal').modal();
        });

        PostEdit = {
            searchButton: document.getElementById('search-button'),
            searchTypeSelect: document.getElementById('search-type'),
            searchText: document.getElementById('search-text'),
            searchResultContainer: document.getElementById('search-result'),
            partTemplate: document.getElementsByClassName('part-template')[0],
            collapsePostPart: document.getElementsByClassName('collapse-post-part')[0],
            collapsibleContainer: document.getElementsByClassName('collapsible-container')[0],
            postConfirmDelete: document.getElementById('post-confirm-delete'),
            partNoResult: document.getElementById('part-no-result'),

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
                        self.makeParts(response);
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            makeParts: function(response) {
                var self = this;
                self.collapsibleContainer.innerHTML = '';
                if (response.response && response.response.posts.length) {
                    self.partNoResult.classList.add('hide');
                    Array.prototype.forEach.call(response.response.posts, (function (element, index, array) {
                        var clone = self.collapsePostPart.cloneNode(true);
                        clone.getElementsByClassName('part-number')[0].innerHTML = ++index;
                        clone.getElementsByClassName('post-id')[0].innerHTML = 'ID = ' + element.id;
                        clone.getElementsByClassName('part-alias')[0].innerHTML = element.alias;
                        clone.getElementsByClassName('post-alias-text')[0].innerHTML = 'ALIAS = ' + element.alias;
                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;
                        clone.getElementsByClassName('post-main-btn')[0].href = location.pathname + '/main/' + element.id;
                        clone.getElementsByClassName('post-parts-btn')[0].href = location.pathname +  '/main/' + element.id + '/parts';
                        clone.getElementsByClassName('post-delete-btn')[0].addEventListener('click',
                            self.initDeleteModal.bind(self)
                        );
                        self.collapsibleContainer.appendChild(clone);
                        self.partTemplate.classList.remove('hide');
                    }));
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            initDeleteModal: function(e) {
                var el = getClosest(e.target, '.collapse-post-part'),
                    id = el.dataset.id;
                this.postConfirmDelete.dataset.id = id;
            },

            postConfirmDeleteRequest: function(e) {
                var updateBtns = [this.postConfirmDelete];
                updateAddConfirmButtons(updateBtns, true);

                var id = e.target.dataset.id,
                    data = 'postId=' + id,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/delete', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Post deleted');
                        document.getElementById('search-part-id-' + id).remove();
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                };
                xhr.send(encodeURI(data));
                updateAddConfirmButtons(updateBtns, false);
            }
        };
        PostEdit.searchButton.addEventListener('click', PostEdit.searchPostRequest.bind(PostEdit));
        PostEdit.postConfirmDelete.addEventListener('click', PostEdit.postConfirmDeleteRequest.bind(PostEdit));
    </script>
@endsection
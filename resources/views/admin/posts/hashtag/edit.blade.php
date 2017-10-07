@extends('admin.index')

@section('content-body')
    <section id="edit-hashtag-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Edit Hashtag</h4>
                <h6>(Select search type, after add search value and Search)</h6>
            </div>
            <div class="row">
                <div class="input-field col m4 s12">
                    <select id="search-type">
                        <option value="" disabled selected>Search type</option>
                        <option value="1">by ID</option>
                        <option value="2">by Name</option>
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
            @include('admin.posts.hashtag.edit-parts')
        </div>
        <!-- Modal Save -->
        <div class="modal" id="saveConfirmModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Make This Changes?</h4>
                <p></p>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat confirm-changes">Save</a>
                <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
            </div>
        </div>
        <!-- Modal Delete -->
        <div class="modal" id="deleteConfirmModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Delete This Hashtag?</h4>
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
            $('#saveConfirmModal').modal();
            $('#deleteConfirmModal').modal();
        });
        HashtagEdit = {
            searchButton: document.getElementById('search-button'),
            searchTypeSelect: document.getElementById('search-type'),
            searchText: document.getElementById('search-text'),
            partTemplate: document.getElementsByClassName('part-template')[0],
            partNoResult: document.getElementsByClassName('part-no-result')[0],
            searchResultContainer: document.getElementById('search-result'),
            saveConfirmModal: document.getElementById('saveConfirmModal'),
            deleteConfirmModal: document.getElementById('deleteConfirmModal'),

            searchHashtagRequest: function(){
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
                self.searchResultContainer.innerHTML = '';
                if (response.response && response.response.hashtags && response.response.hashtags.length) {
                    Array.prototype.forEach.call(response.response.hashtags, (function (element, index, array) {
                        var clone = self.partTemplate.cloneNode(true);

                        clone.getElementsByClassName('part-number')[0].innerHTML = ++index;
                        clone.getElementsByClassName('part-alias')[0].value = element.alias;
                        clone.getElementsByClassName('part-name')[0].value = element.name;
                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;
                        self.searchResultContainer.appendChild(clone);
                        clone.classList.remove('hide');
                        clone.getElementsByClassName('save-confirm-button')[0].addEventListener('click',
                            self.createSaveModelPartConfirm
                        );
                        clone.getElementsByClassName('delete-confirm-button')[0].addEventListener('click',
                            self.createDeleteModelPartConfirm
                        );
                    }));
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            createSaveModelPartConfirm: function(e) {
                var self = HashtagEdit,
                    currElem = getClosest(e.target, '.part-template'),
                    paragraph = self.saveConfirmModal.getElementsByTagName('p')[0],
                    _html = '<p>ID: <span class="red-text part-id">' + currElem.dataset.id + '</span></p>' +
                        '<p>New Hashtag Alias: <span class="red-text part-alias">' + currElem.getElementsByClassName('part-alias')[0].value + '</span></p>' +
                        '<p>New Hashtag Name: <span class="red-text part-name">' + currElem.getElementsByClassName('part-name')[0].value + '</span></p>';
                paragraph.innerHTML = _html;
                self.saveConfirmModal.getElementsByClassName('confirm-changes')[0].addEventListener('click',
                    self.createSaveModelSearchConfirm
                );
            },

            createDeleteModelPartConfirm: function(e) {
                var self = HashtagEdit,
                    currElem = getClosest(e.target, '.part-template'),
                    paragraph = self.deleteConfirmModal.getElementsByTagName('p')[0],
                    _html = '<p>ID: <span class="red-text part-id">' + currElem.dataset.id + '</span></p>' +
                        '<p>Hashtag Alias: <span class="red-text part-alias">' + currElem.getElementsByClassName('part-alias')[0].value + '</span></p>' +
                        '<p>Hashtag Name: <span class="red-text part-name">' + currElem.getElementsByClassName('part-name')[0].value + '</span></p>';
                paragraph.innerHTML = _html;
                self.deleteConfirmModal.getElementsByClassName('confirm-delete')[0].addEventListener('click',
                    self.createDeleteModelSearchConfirm
                );
            },

            createSaveModelSearchConfirm: function(e) {
                var updateBtns = [this];
                updateAddConfirmButtons(updateBtns, true);
                var self = HashtagEdit,
                    elThis = this,
                    currElem = getClosest(e.target, '.modal'),
                    data = 'id=' + currElem.getElementsByClassName('part-id')[0].innerHTML
                        + '&newAlias=' + currElem.getElementsByClassName('part-alias')[0].innerHTML
                        + '&newName=' + currElem.getElementsByClassName('part-name')[0].innerHTML,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/save');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Hashtag was updated');
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            createDeleteModelSearchConfirm: function(e) {
                var updateBtns = [this];
                updateAddConfirmButtons(updateBtns, true);
                var self = HashtagEdit,
                    elThis = this,
                    currElem = getClosest(e.target, '.modal'),
                    id = currElem.getElementsByClassName('part-id')[0].innerHTML,
                    data = 'id=' + id,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/delete');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Hashtag was deleted');
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    document.getElementById('search-part-id-' + id).remove();
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            }
        };
        HashtagEdit.searchButton.addEventListener('click', HashtagEdit.searchHashtagRequest.bind(HashtagEdit));
    </script>
@endsection
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
            @include('admin.posts.hashtag.edit-parts')
        </div>
        <!-- Modal Save -->
        <div class="modal" id="saveConfirmModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Make This Changes?</h4>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat" id="confirm-changes">Save</a>
                <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
            </div>
        </div>
        <!-- Modal Delete -->
        <div class="modal" id="deleteConfirmModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Delete This Hashtag?</h4>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat" id="confirm-delete">Delete</a>
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
            partTemplateLi: document.getElementsByClassName('part-template')[0].getElementsByTagName('li')[0],
            partTemplateCollapsible: document.getElementsByClassName('part-template')[0].getElementsByClassName('collapsible')[0],
            partNoResult: document.getElementsByClassName('part-no-result')[0],
            searchResultContainer: document.getElementById('search-result'),
            confirmChangesBtn: document.getElementById('confirm-changes'),
            confirmDeleteBtn: document.getElementById('confirm-delete'),

            searchHashtagRequest: function() {
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
                self.partTemplateCollapsible.innerHTML = '';
                self.partNoResult.classList.add('hide');

                if (response.response && response.response.hashtags && response.response.hashtags.length) {
                    Array.prototype.forEach.call(response.response.hashtags, (function (element, index, array) {
                        var clone = self.partTemplateLi.cloneNode(true);
                        clone.getElementsByClassName('hashtag-id')[0].innerHTML = element.id;
                        clone.getElementsByClassName('hashtag-alias')[0].innerHTML = element.alias;
                        clone.getElementsByClassName('alias-input')[0].value = element.alias;

                        // Locale Part
                        var localeTemplate = clone.getElementsByClassName('hashtag-locale')[0],
                            localeTemplateInput = localeTemplate.getElementsByClassName('inner-container')[0];
                        localeTemplate.innerHTML = '';
                        Array.prototype.forEach.call(element.locale, (function (el, i, arr) {
                            var localeClone = localeTemplateInput.cloneNode(true);
                            localeClone.getElementsByTagName('input')[0].value = el.hashtag;
                            localeClone.getElementsByTagName('img')[0].src = '/img/flags/' + el.abbr + '.svg';
                            localeClone.dataset.id = el.id;
                            localeTemplate.appendChild(localeClone);
                        }));
                        // --endLocale Part

                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;
                        self.partTemplateCollapsible.appendChild(clone);
                        self.partTemplateCollapsible.classList.remove('hide');

                        clone.getElementsByClassName('save-confirm-button')[0].addEventListener('click',
                            self.passIdToSaveModal.bind(self)
                        );
                        clone.getElementsByClassName('delete-confirm-button')[0].addEventListener('click',
                            self.passIdToDeleteModal.bind(self)
                        );
                    }));
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            passIdToSaveModal: function (e) {
                var currItem = getClosest(e.target, '.collapsible-item');
                this.confirmChangesBtn.dataset.id = currItem.dataset.id;
            },

            passIdToDeleteModal: function (e) {
                var currItem = getClosest(e.target, '.collapsible-item');
                this.confirmDeleteBtn.dataset.id = currItem.dataset.id;
            },

            createSaveModelSearchConfirm: function(e) {
                var updateBtns = [this];
                updateAddConfirmButtons(updateBtns, true);
                var hashtagId = e.target.dataset.id,
                    currElem = document.getElementById('search-part-id-' + hashtagId),
                    localeInnerContainers = currElem.getElementsByClassName('inner-container'),
                    data = 'id=' + hashtagId
                        + '&hashtagAlias=' + currElem.getElementsByClassName('alias-input')[0].value
                        + '&hashtagNames=' +  this.getHashtagsNames(localeInnerContainers);
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

            getHashtagsNames: function (hashtags) {
                var hashtagsNames = [];
                Array.prototype.forEach.call(hashtags, (function (element, index, array) {
                    // todo standardizatoin needed 1-1 -->5
                    var names = {
                        locale_id: element.dataset.id,
                        name: element.getElementsByTagName('input')[0].value
                    };
                    hashtagsNames.push(names);
                }));
                return JSON.stringify(hashtagsNames);
            },

            createDeleteModelSearchConfirm: function(e) {
                var updateBtns = [e.target];
                updateAddConfirmButtons(updateBtns, true);
                var id = e.target.dataset.id
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
        HashtagEdit.confirmChangesBtn.addEventListener('click', HashtagEdit.createSaveModelSearchConfirm.bind(HashtagEdit));
        HashtagEdit.confirmDeleteBtn.addEventListener('click', HashtagEdit.createDeleteModelSearchConfirm.bind(HashtagEdit));
    </script>
@endsection
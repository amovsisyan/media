@extends('admin.index')

@section('content-body')
    <section id="create-category-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Edit Category</h4>
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
            @include('admin.categories.categories.edit-parts')
        </div>
        <!-- Modal -->
        <div class="modal" id="changesConfirmModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Make This Changes?</h4>
                <p></p>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat confirm-changes">Save</a>
                <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('#search-type').material_select();
            $('#changesConfirmModal').modal();
        });
        CategoryEdit = {
            searchButton: document.querySelector('#search-button'),
            searchTypeSelect: document.querySelector('#search-type'),
            searchText: document.querySelector('#search-text'),
            partTemplate: document.querySelector('.part-template'),
            partNoResult: document.querySelector('.part-no-result'),
            searchResultContainer: document.querySelector('#search-result'),
            changesConfirmModal: document.querySelector('#changesConfirmModal'),

            searchCategoryRequest: function(){
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
                if (response.response && response.response.length) {
                    Array.prototype.forEach.call(response.response, (function (element, index, array) {
                        var clone = self.partTemplate.cloneNode(true);
                        clone.querySelector('.part-number').innerHTML = ++index;
                        clone.querySelector('.part-alias').value = element.alias;
                        clone.querySelector('.part-name').value = element.name;
                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;
                        self.searchResultContainer.appendChild(clone);
                        clone.classList.remove('hide');
                        clone.querySelector('.part-confirm-button').addEventListener('click',
                            self.createModelPartConfirm
                        );
                    }));
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            createModelPartConfirm: function(e) {
                var self = CategoryEdit,
                    currElem = getClosest(e.target, '.part-template'),
                    paragraph = self.changesConfirmModal.querySelector('p'),
                    _html = '<p>ID: <span class="red-text part-id">' + currElem.dataset.id + '</span></p>' +
                        '<p>New Post Alias: <span class="red-text part-alias">' + currElem.querySelector('.part-alias').value + '</span></p>' +
                        '<p>New Post Name: <span class="red-text part-name">' + currElem.querySelector('.part-name').value + '</span></p>';
                paragraph.innerHTML = _html;
                self.changesConfirmModal.querySelector('.confirm-changes').addEventListener('click',
                    self.createModelSearchConfirm
                );
            },

            createModelSearchConfirm: function(e) {
                this.classList.add('disabled');
                var self = CategoryEdit,
                    elThis = this,
                    currElem = getClosest(e.target, '.modal'),
                    data = 'id=' + currElem.querySelector('.part-id').innerHTML
                        + '&newAlias=' + currElem.querySelector('.part-alias').innerHTML
                        + '&newName=' + currElem.querySelector('.part-name').innerHTML,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/save');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Category was updated');
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    elThis.classList.remove('disabled');
                };
                xhr.send(encodeURI(data));
            }
        };
        CategoryEdit.searchButton.addEventListener('click', CategoryEdit.searchCategoryRequest.bind(CategoryEdit));
    </script>
@endsection
@extends('admin.index')

@section('content-body')
    <section id="edit-subcategory-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Edit Subcategory</h4>
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
            @include('admin.categories.subcategories.edit-parts')
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
        SubcategoryEdit = {
            searchButton: document.getElementById('search-button'),
            searchTypeSelect: document.getElementById('search-type'),
            searchText: document.getElementById('search-text'),
            partTemplate: document.querySelector('.part-template'),
            partNoResult: document.querySelector('.part-no-result'),
            searchResultContainer: document.getElementById('search-result'),
            changesConfirmModal: document.getElementById('changesConfirmModal'),

            searchSubcategoryRequest: function(){
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
                        $('.category-for-subcategory').material_select();
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
                if (response.response && response.response.categories.length && response.response.subcategories.length) {
                    Array.prototype.forEach.call(response.response.subcategories, (function (element, index, array) {
                        var clone = self.partTemplate.cloneNode(true),
                            _options = '',
                            categoryForSubcategory = clone.querySelector('.category-for-subcategory');

                        clone.querySelector('.part-number').innerHTML = ++index;
                        clone.querySelector('.part-alias').value = element.alias;
                        clone.querySelector('.part-name').value = element.name;
                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;

                        Array.prototype.forEach.call(response.response.categories, (function (el, i, arr) {
                            if (el.id === element.categ_id) {
                                _options += '<option value="' + el.id + '" selected>' + el.name + '</option>';
                            } else {
                                _options += '<option value="' + el.id + '">' + el.name + '</option>';
                            }
                        }));
                        categoryForSubcategory.innerHTML = _options;
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
                var self = SubcategoryEdit,
                    currElem = getClosest(e.target, '.part-template'),
                    paragraph = self.changesConfirmModal.querySelector('p'),
                    categoryForSubcategory = currElem.querySelector('select.category-for-subcategory'),
                    _html = '<p>ID: <span class="red-text part-id">' + currElem.dataset.id + '</span></p>' +
                        '<p>New Category: <span class="red-text part-category" data-id="' + categoryForSubcategory.options[categoryForSubcategory.selectedIndex].value + '">' + categoryForSubcategory.options[categoryForSubcategory.selectedIndex].text + '</span></p>' +
                        '<p>New Post Alias: <span class="red-text part-alias">' + currElem.querySelector('.part-alias').value + '</span></p>' +
                        '<p>New Post Name: <span class="red-text part-name">' + currElem.querySelector('.part-name').value + '</span></p>';
                paragraph.innerHTML = _html;
                self.changesConfirmModal.querySelector('.confirm-changes').addEventListener('click',
                    self.createModelSearchConfirm
                );
            },

            createModelSearchConfirm: function(e) {
                var updateBtns = [this];
                updateAddConfirmButtons(updateBtns, true);
                var self = SubcategoryEdit,
                    elThis = this,
                    currElem = getClosest(e.target, '.modal'),
                    data = 'id=' + currElem.querySelector('.part-id').innerHTML
                        + '&newCategoryId=' + currElem.querySelector('.part-category').dataset.id
                        + '&newAlias=' + currElem.querySelector('.part-alias').innerHTML
                        + '&newName=' + currElem.querySelector('.part-name').innerHTML,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/save');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Subcategory was updated');
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            }
        };
        SubcategoryEdit.searchButton.addEventListener('click', SubcategoryEdit.searchSubcategoryRequest.bind(SubcategoryEdit));
    </script>
@endsection
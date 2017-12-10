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
            @include('admin.categories.subcategories.edit-parts')
        </div>
        <!-- Modal -->
        <div class="modal" id="changesConfirmModal">
            <div class="modal-content left-align">
                <h4>Are You Sure You Want Make This Changes?</h4>
            </div>
            <div class="modal-footer">
                <a class="modal-action modal-close waves-effect waves-green btn-flat" id="confirm-changes">Save</a>
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
            $('.collapsible').collapsible();
        });
        SubcategoryEdit = {
            searchButton: document.getElementById('search-button'),
            searchTypeSelect: document.getElementById('search-type'),
            searchText: document.getElementById('search-text'),
            partTemplate: document.getElementById('part-template'),
            partNoResult: document.getElementsByClassName('part-no-result')[0],
            searchResultContainer: document.getElementById('search-result'),
            subcategoryPart: document.getElementsByClassName('collapse-subcategory-part')[0],
            collapsibleContainer: document.getElementById('collapsible-container'),
            confirmChangesBtn: document.getElementById('confirm-changes'),

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
                self.collapsibleContainer.innerHTML = '';
                if (response.response &&
                    response.response.categories && response.response.categories.length &&
                    response.response.subcategories && response.response.subcategories.length) {
                    Array.prototype.forEach.call(response.response.subcategories, (function (element, index, array) {
                        var categories = response.response.categories,
                            elLocales = element.subcategoriesLocale,
                            clone = self.subcategoryPart.cloneNode(true),

                            collapseHeader = clone.getElementsByClassName('collapsible-header')[0],
                            headerNumber = collapseHeader.getElementsByClassName('part-number')[0],
                            headerAlias = collapseHeader.getElementsByClassName('part-alias')[0],

                            collapseBody = clone.getElementsByClassName('collapsible-body')[0],
                            bodyAlias = collapseBody.getElementsByClassName('part-alias')[0],

                            categoriesSelect = collapseBody.getElementsByClassName('categories-select')[0],
                            option = categoriesSelect.getElementsByTagName('option')[0],

                            localeContainer = collapseBody.getElementsByClassName('locale-part-container')[0],
                            localePart = localeContainer.getElementsByClassName('locale-part')[0];

                        categoriesSelect.innerHTML = '';
                        localeContainer.innerHTML = '';

                        headerNumber.innerHTML = ++index;
                        headerAlias.innerHTML = element.alias;
                        bodyAlias.value = element.alias;

                        // making categories
                        Array.prototype.forEach.call(categories, (function (el) {
                            cloneOption = option.cloneNode(true);
                            cloneOption.value = el.id;
                            cloneOption.innerHTML = el.alias;
                            if (el.id === element.categ_id) {
                                cloneOption.selected = true;
                            }
                            categoriesSelect.appendChild(cloneOption);
                        }));

                        // making each locale
                        Array.prototype.forEach.call(elLocales, (function (e) {
                            cloneLocale = localePart.cloneNode(true);
                            cloneLocale.dataset.localename = e.locale_name;
                            cloneLocale.dataset.id = e.id;
                            cloneLocale.getElementsByTagName('img')[0].src = '/img/flags/' + e.locale_name +'.svg';
                            cloneLocale.getElementsByClassName('part-locale-name')[0].value = e.name;
                            localeContainer.appendChild(cloneLocale);
                        }));
                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;

                        self.collapsibleContainer.appendChild(clone);
                        self.partTemplate.classList.remove('hide');
                        self.partNoResult.classList.add('hide');

                        clone.getElementsByClassName('part-confirm-button')[0].addEventListener('click',
                            self.createModelPartConfirm.bind(self)
                        );
                    }));
                    $('.categories-select').material_select();
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.partTemplate.classList.add('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            createModelPartConfirm: function(e) {
                this.confirmChangesBtn.dataset.id = getClosest(e.target, '.collapse-subcategory-part').dataset.id;
            },

            confirmChanges: function(e) {
                var updateBtns = [this.confirmChangesBtn];
                updateAddConfirmButtons(updateBtns, true);
                var self = this,
                    dataId = e.target.dataset.id,
                    currElem = document.getElementById('search-part-id-' + dataId),
                    currSubCategoryElem = currElem.querySelector('select.categories-select'),

                    id = currElem.dataset.id,
                    newCategoryId = currSubCategoryElem.options[currSubCategoryElem.selectedIndex].value,
                    newAlias = currElem.querySelector('input.part-alias').value,
                    subcategoryNames = this._getLocaleNames(currElem),

                    data = 'id=' + id
                        + '&newCategoryId=' + newCategoryId
                        + '&newAlias=' + newAlias
                        + '&subcategoryNames=' + subcategoryNames,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/save');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Subcategory was updated');
                        currElem.querySelector('span.part-alias').innerHTML = newAlias;
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            _getLocaleNames: function (currElem) {
                var names = [],
                    parts = currElem.getElementsByClassName('locale-part');
                Array.prototype.forEach.call(parts, (function (e) {
                    var locale = e.dataset.localename,
                        subcategoryLocaleId = e.dataset.id,
                        name = e.getElementsByClassName('part-locale-name')[0].value;
                    names.push({
                        name: name,
                        id: subcategoryLocaleId
                    });
                }));
                return JSON.stringify(names);
            }
        };
        SubcategoryEdit.searchButton.addEventListener('click', SubcategoryEdit.searchSubcategoryRequest.bind(SubcategoryEdit))
        SubcategoryEdit.confirmChangesBtn.addEventListener('click', SubcategoryEdit.confirmChanges.bind(SubcategoryEdit))
    </script>
@endsection
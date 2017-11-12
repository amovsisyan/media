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
            @include('admin.categories.categories.edit-parts')
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

        CategoryEdit = {
            searchButton: document.getElementById('search-button'),
            confirmChangesBtn: document.getElementById('confirm-changes'),
            searchTypeSelect: document.getElementById('search-type'),
            searchText: document.getElementById('search-text'),
            partTemplate: document.getElementsByClassName('part-template')[0],
            partTemplateLi: document.getElementsByClassName('part-template')[0].getElementsByTagName('li')[0],
            partTemplateCollapsible: document.getElementsByClassName('part-template')[0].getElementsByClassName('collapsible')[0],
            partNoResult: document.getElementsByClassName('part-no-result')[0],
            searchResultContainer: document.getElementById('search-result'),

            searchCategoryRequest: function(){
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

                if (response.response && response.response.length) {
                    Array.prototype.forEach.call(response.response, (function (element, index, array) {
                        var clone = self.partTemplateLi.cloneNode(true);
                        clone.getElementsByClassName('category-id')[0].innerHTML = element.id;
                        clone.getElementsByClassName('category-alias')[0].innerHTML = element.alias;
                        clone.getElementsByClassName('alias-input')[0].value = element.alias;

                        // Locale Part
                        var localeTemplate = clone.getElementsByClassName('category-locale')[0],
                            localeTemplateInput = localeTemplate.getElementsByClassName('inner-container')[0];
                        localeTemplate.innerHTML = '';
                        Array.prototype.forEach.call(element.categoriesLocale, (function (el, i, arr) {
                            var localeClone = localeTemplateInput.cloneNode(true);
                            localeClone.getElementsByTagName('input')[0].value = el.name;
                            localeClone.getElementsByTagName('img')[0].src = '/img/flags/' + el.localeAbbr + '.svg';
                            localeClone.dataset.id = el.id;
                            localeTemplate.appendChild(localeClone);
                        }));
                        // --endLocale Part

                        clone.id = 'search-part-id-' + element.id;
                        clone.dataset.id = element.id;
                        self.partTemplateCollapsible.appendChild(clone);
                        self.partTemplateCollapsible.classList.remove('hide');
                        clone.getElementsByClassName('part-confirm-button')[0].addEventListener('click',
                            self.createModelPartConfirm.bind(self)
                        );
                    }));
                } else {
                    self.partNoResult.classList.remove('hide');
                    self.searchResultContainer.appendChild(self.partNoResult);
                }
            },

            createModelPartConfirm: function(e) {
                var currItem = getClosest(e.target, '.collapsible-item');
                this.confirmChangesBtn.dataset.id = currItem.dataset.id;
            },

            createModelSearchConfirm: function(e) {
                var updateBtns = [e.target];
                updateAddConfirmButtons(updateBtns, true);

                var catId = e.target.dataset.id,
                    categoryElement = document.getElementById('search-part-id-' + catId),
                    catAlias = categoryElement.getElementsByClassName('alias-input')[0].value,
                    localeInnerContainers = categoryElement.getElementsByClassName('inner-container'),
                    localesInfo = [],
                    xhr = new XMLHttpRequest();

                Array.prototype.forEach.call(localeInnerContainers, (function (element, index, array) {
                    // todo standardizatoin needed 1-1 -->2
                    var info = {
                        locale_id: element.dataset.id,
                        name: element.getElementsByTagName('input')[0].value
                    };
                    localesInfo.push(info);
                }));

                var data = 'catId=' + catId
                        + '&catAlias=' + catAlias
                        + '&localesInfo=' + JSON.stringify(localesInfo);

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
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            }
        };
        CategoryEdit.searchButton.addEventListener('click', CategoryEdit.searchCategoryRequest.bind(CategoryEdit));
        CategoryEdit.confirmChangesBtn.addEventListener('click', CategoryEdit.createModelSearchConfirm.bind(CategoryEdit));
    </script>
@endsection
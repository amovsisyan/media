@extends('admin.index')

@section('content-body')
    <section id="create-subcategory-panel">
        <div class="container">
            <div class="row">
                <h4 class="center-align">Create Subcategory</h4>
            </div>
            <div class="row">
                <div class="col m8 s12">
                    <div class="input-field">
                        <input id="subcategory_alias" name="alias" type="text" class="validate" data-length={{$response['colLength']['alias']}}>
                        <label for="alias">Alias(English)</label>
                    </div>
                </div>
            </div>
            @foreach ($response['activeLocales'] as $locale)
            <div class="row">
                <div class="col m1 s12">
                    <img src="/img/flags/{{$locale['name']}}.svg" alt="">
                </div>
                <div class="col m7 s12">
                    <div class="input-field">
                        <input data-localeid="{{$locale['id']}}" class="subcategory_name" name="name" type="text" class="validate" data-length={{$response['colLength']['name']}}>
                        <label for="name">Name(Russian)</label>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="row">
                <div class="col m4 s12">
                    <div class="input-field col s10">
                        <select id="category_select">
                            <option value="" disabled selected>Choose category</option>
                            @foreach ($response['categories'] as $category)
                                <option value="{{ $category['id'] }}">{{ $category['alias'] }}</option>
                            @endforeach
                        </select>
                        <label>Category Select</label>
                    </div>
                </div>
            </div>
            <div class="row right-align m_t_50">
                <a class="waves-effect waves-light btn">test it</a>
                <!-- Modal -->
                <a id='add_subcategory' class="waves-effect waves-light btn modal-trigger" href="#modal_add_subcategory">Add</a>
                <div id="modal_add_subcategory" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Create Subcategory?</h4>
                    </div>
                    <div class="modal-footer">
                        <a id='confirm_subcategory' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
                        <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('select').material_select();
            $('.modal').modal();
        });

        SubcategoryCreate = {
            addButton: document.getElementById('add_subcategory'),
            confirmButton: document.getElementById('confirm_subcategory'),
            subcategoryAlias: document.getElementById('subcategory_alias'),
            categorySelect: document.getElementById('category_select'),
            subcategoriesNames: document.getElementsByClassName('subcategory_name'),

            confirmCategory: function(){
                var updateBtns = [this.addButton, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    data = 'subcategoryAlias=' + this.subcategoryAlias.value
                        + '&subcategoryNames=' + this.getSubCategoriesNames()
                        + '&categoryId=' + this.categorySelect.options[this.categorySelect.selectedIndex].value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Added New Subcategory');
                        self.regenerateAfterDelete();
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            getSubCategoriesNames: function () {
                var subcategoriesNames = [];
                Array.prototype.forEach.call(this.subcategoriesNames, (function (element, index, array) {
                    // todo standardizatoin needed 1-1 -->3
                    var names = {
                        locale_id: element.dataset.localeid,
                        name: element.value
                    };
                    subcategoriesNames.push(names);
                }));
                return JSON.stringify(subcategoriesNames);
            },

            regenerateAfterDelete: function () {
                Array.prototype.forEach.call(this.subcategoriesNames, (function (element, index, array) {
                    element.value = ''
                }));
                this.subcategoryAlias.value = '';
            }
        };
        SubcategoryCreate.confirmButton.addEventListener('click', SubcategoryCreate.confirmCategory.bind(SubcategoryCreate));
    </script>
@endsection
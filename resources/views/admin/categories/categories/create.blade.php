@extends('admin.index')

@section('content-body')
    <section id="create-category-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Create Category</h4>
                <h6>(After creating Category, make sure You have also created Subcategory to see it in user side)</h6>
            </div>
            <div class="row">
                <div class="col m6 s12">
                    <div class="input-field col s8">
                        <input id="category_alias" name="alias" type="text" class="validate" data-length={{$response['colLength']['alias']}}>
                        <label for="alias">Alias(English, without spaces)</label>
                    </div>
                </div>

            </div>
            <div class="row">
                @if (!empty($response['activeLocales']))
                    @foreach($response['activeLocales'] as $locale)
                        <div class="col s1">
                            <img src="/img/flags/{{$locale['name']}}.svg" alt="">
                        </div>
                        <div class="col s11">
                            <div class="input-field col s8">
                                <input data-localeid="{{$locale['id']}}" name="name" type="text" class="category-name validate" data-length={{$response['colLength']['name']}}>
                                <label for="name">Name( {{$locale['name']}} )</label>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="row right-align m_t_50">
                <a class="waves-effect waves-light btn">test it</a>
                <!-- Modal -->
                <a id='add_category' class="waves-effect waves-light btn modal-trigger" href="#modal_add_category">Add</a>
                <div id="modal_add_category" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Create Category?</h4>
                    </div>
                    <div class="modal-footer">
                        <a id='confirm_category' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
                        <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('.modal').modal();
        });

        CategoryCreate = {
            addButton: document.getElementById('add_category'),
            confirmButton: document.getElementById('confirm_category'),
            categoriesNames: document.getElementsByClassName('category-name'),
            categoryAlias: document.getElementById('category_alias'),
            modalAddCategory: document.getElementById('modal_add_category'),

            confirmCategory: function(){
                var updateBtns = [this.addButton, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    data = 'categories_names=' + this.getCategoriesNames() + '&category_alias=' + this.categoryAlias.value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Added New Category');
                        self.cleanInputs();
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            getCategoriesNames: function () {
                var categoriesNames = [];
                Array.prototype.forEach.call(this.categoriesNames, (function (element, index, array) {
                    // todo standardizatoin needed 1-1 -->1
                    var names = {
                        locale_id: element.dataset.localeid,
                        name: element.value
                    };
                    categoriesNames.push(names);
                }));
                return JSON.stringify(categoriesNames);
            },

            cleanInputs: function () {
                this.categoryAlias.value = '';
                Array.prototype.forEach.call(this.categoriesNames, (function (element, index, array) {
                    element.value = '';
                }));
            }
        };
        CategoryCreate.confirmButton.addEventListener('click', CategoryCreate.confirmCategory.bind(CategoryCreate));
    </script>
@endsection
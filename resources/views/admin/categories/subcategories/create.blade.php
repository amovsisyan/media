@extends('admin.index')

@section('content-body')
    <section id="create-subcategory-panel">
        <div class="container">
            <div class="row">
                <h4 class="center-align">Create Subcategory</h4>
            </div>
            <div class="row">
                <div class="col m4 s12">
                    <div class="input-field col s10">
                        <select id="category_select">
                            <option value="" disabled selected>Choose category</option>
                            @foreach ($response['categories'] as $category)
                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                        <label>Category Select</label>
                    </div>
                </div>
                <div class="col m4 s12">
                    <div class="input-field col s10">
                        <input id="subcategory_alias" name="alias" type="text" class="validate">
                        <label for="alias">Alias(English)</label>
                    </div>
                </div>
                <div class="col m4 s12">
                    <div class="input-field col s10">
                        <input id="subcategory_name" name="name" type="text" class="validate">
                        <label for="name">Name(Russian)</label>
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
                        <p></p>
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
            subcategoryName: document.getElementById('subcategory_name'),
            subcategoryAlias: document.getElementById('subcategory_alias'),
            categorySelect: document.getElementById('category_select'),
            modalAddSubcategory: document.getElementById('modal_add_subcategory'),

            confirmCategory: function(){
                this.addButton.classList.add('disabled');
                this.confirmButton.classList.add('disabled');

                var self = this,
                    data = 'subcategory_name=' + this.subcategoryName.value
                        + '&subcategory_alias=' + this.subcategoryAlias.value
                        + '&categorySelect=' + this.categorySelect.options[this.categorySelect.selectedIndex].value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        self.handleResponseToast(xhr.status, 'Added New Subcategory', 'status_ok');
                        if (xhr.status === 200) {
                            self.subcategoryName.value = '';
                            self.subcategoryAlias.value = '';
                        }
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        if (response.response && response.validate_error === true) {
                            var errors = response.response,
                                _html = '';
                            errors.forEach(function (element, index, array) {
                                _html += element;
                            });
                        } else {
                            _html = 'Something Was Wrong'
                        }
                        self.handleResponseToast(xhr.status, _html, 'status_warning');
                    }
                };
                xhr.send(encodeURI(data));
            },

            createModalContent: function() {
                var content = this.modalAddSubcategory.getElementsByClassName('modal-content'),
                    paragraph = content[0].getElementsByTagName('p')[0],
                    _html = '<p>For Category: ' + this.categorySelect.options[this.categorySelect.selectedIndex].text + '</p>'+
                            '<p>Name: ' + this.subcategoryName.value + '</p>' +
                            '<p>Alias: ' + this.subcategoryAlias.value + '</p>';
                paragraph.innerHTML = _html;
            },

            handleResponseToast: function(status, text, style) {
                Materialize.toast(text, 5000, 'rounded');
                var toasts = document.getElementById("toast-container").getElementsByClassName("toast "),
                    toast = toasts[toasts.length-1];

                toast.classList.add(style);
                this.addButton.classList.remove('disabled');
                this.confirmButton.classList.remove('disabled');
            }
        };
        SubcategoryCreate.addButton.addEventListener('click', SubcategoryCreate.createModalContent.bind(SubcategoryCreate));
        SubcategoryCreate.confirmButton.addEventListener('click', SubcategoryCreate.confirmCategory.bind(SubcategoryCreate));
    </script>
@endsection
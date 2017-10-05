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
                var updateBtns = [this.addButton, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);

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
                        handleResponseToast(response, true, 'Added New Subcategory');
                        self.subcategoryName.value = '';
                        self.subcategoryAlias.value = '';
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            createModalContent: function() {
                var content = this.modalAddSubcategory.querySelector('.modal-content'),
                    paragraph = content.querySelector('p'),
                    _html = '<p>For Category: ' + this.categorySelect.options[this.categorySelect.selectedIndex].text + '</p>'+
                            '<p>Name: ' + this.subcategoryName.value + '</p>' +
                            '<p>Alias: ' + this.subcategoryAlias.value + '</p>';
                paragraph.innerHTML = _html;
            }
        };
        SubcategoryCreate.addButton.addEventListener('click', SubcategoryCreate.createModalContent.bind(SubcategoryCreate));
        SubcategoryCreate.confirmButton.addEventListener('click', SubcategoryCreate.confirmCategory.bind(SubcategoryCreate));
    </script>
@endsection
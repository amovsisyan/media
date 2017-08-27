@extends('admin.index')

@section('content-body')
    <section id="delete-subcategory-panel">
        <div class="container">
            <div class="row" id="subcategory_delete_row">
                <div class="input-field col m6 s12">
                    <select id="subcategory_select">
                        @foreach ($response['categories'] as $category)
                            <optgroup label="{{ $category[0]['name'] }}">
                                @foreach ($category['subcategory'] as $subcategory)
                                    <option value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <label>Select Subcategory for delete <span class="important_icon">*</span></label>
                </div>
                <div class="row right-align m_t_50">
                    <!-- Modal -->
                    <a id='delete_subcategory' class="waves-effect waves-light btn modal-trigger" href="#modal_delete_subcategory">Delete</a>
                    <div id="modal_delete_subcategory" class="modal">
                        <div class="modal-content left-align">
                            <h4>Are You Sure You Want Delete Subcategory?</h4>
                            <p></p>
                        </div>
                        <div class="modal-footer">
                            <a id='confirm_delete_subcategory' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
                            <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $('#subcategory_select').material_select();
            $('.modal').modal();
        });

        SubcategoryDelete = {
            deleteBtn: document.querySelector('#delete_subcategory'),
            confirmButton: document.querySelector('#confirm_delete_subcategory'),
//            subcategoryDeleteRow: document.querySelector('#subcategory_delete_row'),
//            deleteConfirmBtn: document.querySelector('#confirm_delete_subcategory'),
            modalDeleteSubcategory: document.querySelector('#modal_delete_subcategory'),
            subcategorySelect: document.querySelector('#subcategory_select'),

            createModalContent: function() {
                var paragraph = this.modalDeleteSubcategory.querySelector('p'),
                    selectedOption = this.subcategorySelect.options[this.subcategorySelect.selectedIndex],
                    _html = '<p>SubcategoryId: ' + selectedOption.value + '</p>' +
                        '<p>SubcategoryName: ' + selectedOption.innerHTML + '</p>';

                paragraph.innerHTML = _html;
                this.confirmButton.addEventListener('click',
                    this.deleteSubcategoryRequest.bind(this)
                );
            },

            deleteSubcategoryRequest: function() {
                this.deleteBtn.classList.add('disabled');
                this.confirmButton.classList.add('disabled');

                var self = this,
                    data = 'subcategoryId=' + this.subcategorySelect.options[this.subcategorySelect.selectedIndex].value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Deleted Subcategory');
                        if (response.ids && response.ids.length) {
                            var options = self.subcategorySelect.querySelectorAll('option');
                            response.ids.forEach(function (element, index, array) {
                                Array.prototype.forEach.call(options,(function (el, i, arr){
                                    if(el.value === element) {
                                        el.remove();
                                    }
                                }));
                            });
                            $('#subcategory_select').material_select();
                        }
                        self.updateAddConfirmButtons();
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                        self.updateAddConfirmButtons();
                    }
                };
                xhr.send(encodeURI(data));
            },

            updateAddConfirmButtons: function() {
                this.deleteBtn.classList.remove('disabled');
                this.confirmButton.classList.remove('disabled');
            }
        };
        SubcategoryDelete.deleteBtn.addEventListener('click', SubcategoryDelete.createModalContent.bind(SubcategoryDelete));
//        CategoryDelete.confirmButton.addEventListener('click', CategoryDelete.confirmDeleteCategory.bind(CategoryDelete));
    </script>
@endsection
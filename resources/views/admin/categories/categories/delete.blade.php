@extends('admin.index')

@section('content-body')
    <section id="delete-category-panel">
        <div class="container">
            <div class="row" id="category_delete_row">
                <div class="input-field col m6 s12">
                    <select multiple id="category_select">
                            <option value="" disabled selected>Delete Category</option>
                        @foreach ($response['categories'] as $category)
                            <option>{{ $category['alias'] }}_{{ $category['id'] }}</option>
                        @endforeach
                    </select>
                    <label>You can choose multiple options</label>
                </div>
                <div class="row right-align m_t_50">
                    <!-- Modal -->
                    <a id='delete_category' class="waves-effect waves-light btn modal-trigger" href="#modal_delete_category">Delete</a>
                    <div id="modal_delete_category" class="modal">
                        <div class="modal-content left-align">
                            <h4>Are You Sure You Want Delete Category?</h4>
                        </div>
                        <div class="modal-footer">
                            <a id='confirm_delete_category' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
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
            $('#category_select').material_select();
            $('.modal').modal();
        });

        CategoryDelete = {
            deleteBtn: document.getElementById('delete_category'),
            confirmButton: document.getElementById('confirm_delete_category'),
            categoryDeleteRow: document.getElementById('category_delete_row'),
            deleteConfirmBtn: document.getElementById('confirm_delete_category'),
            modalDeleteCategory: document.getElementById('modal_delete_category'),
            categorySelect: document.getElementById('category_select'),

            confirmDeleteCategory: function() {
                var updateBtns = [this.deleteBtn, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);
                var self = this,
                    prepData = [],
                    list = this.getDeleteList(),
                    xhr = new XMLHttpRequest();

                Array.prototype.forEach.call(list, (function (element, index, array) {
                    var elementContent = element.getElementsByTagName('span')[0].textContent;
                    prepData.push(explodeGetLast(elementContent, '_'));
                }));

                var data = 'data=' + JSON.stringify(prepData);

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Deleted Category(es)');
                        if (response.ids && response.ids.length) {
                            var options = self.categorySelect.getElementsByTagName('option');
                            response.ids.forEach(function (element, index, array) {
                                Array.prototype.forEach.call(options, (function (el, i, arr){
                                    if(explodeGetLast(el.value, '_') === element) {
                                        el.remove();
                                    }
                                }));
                            });
                            $('#category_select').material_select();
                        }
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            getDeleteList: function() {
                return this.categoryDeleteRow.getElementsByClassName('multiple-select-dropdown')[0].getElementsByClassName('active');
            }
        };
        CategoryDelete.confirmButton.addEventListener('click', CategoryDelete.confirmDeleteCategory.bind(CategoryDelete));
    </script>
@endsection
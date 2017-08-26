@extends('admin.index')

@section('content-body')
    <section id="delete-category-panel">
        <div class="container">
            <div class="row" id="category_delete_row">
                <div class="input-field col m6 s12">
                    <select multiple id="category_select">
                            <option value="" disabled selected>Delete Category</option>
                        @foreach ($response['categories'] as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}_{{ $category['id'] }}</option>
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
                            <p></p>
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
            deleteBtn: document.querySelector('#delete_category'),
            confirmButton: document.querySelector('#confirm_delete_category'),
            categoryDeleteRow: document.querySelector('#category_delete_row'),
            deleteConfirmBtn: document.querySelector('#confirm_delete_category'),
            modalDeleteCategory: document.querySelector('#modal_delete_category'),
            categorySelect: document.querySelector('#category_select'),

            createModalContent: function() {
                var content = this.modalDeleteCategory.querySelector('.modal-content'),
                    paragraph = content.querySelector('p'),
                    list = this.getDeleteList(),
                    cat_s = '';
                Array.prototype.forEach.call(list, (function (element, index, array) {
                    cat_s += '<p>' + ++index + '. ' + element.querySelector('span').textContent + '</p>';
                }));
                var _html = '<p>Delete Category(es):' + cat_s + '</p>';
                paragraph.innerHTML = _html;
            },

            confirmDeleteCategory: function() {
                this.deleteBtn.classList.add('disabled');
                this.confirmButton.classList.add('disabled');
                var self = this,
                    prepData = [],
                    list = this.getDeleteList(),
                    xhr = new XMLHttpRequest();

                Array.prototype.forEach.call(list, (function (element, index, array) {
                    var elementContent = element.querySelector('span').textContent;
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
                            response.ids.forEach(function (element, index, array) {
                                var options = self.categorySelect.querySelectorAll('option');
                                Array.prototype.forEach.call(options,(function (el, i, arr){
                                    if(el.value === element) {
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
                    self.updateButtonsAfterConfirm();
                };
                xhr.send(encodeURI(data));
            },

            updateButtonsAfterConfirm: function() {
                this.deleteBtn.classList.remove('disabled');
                this.confirmButton.classList.remove('disabled');
            },

            getDeleteList: function() {
                return this.categoryDeleteRow.querySelector('.multiple-select-dropdown').querySelectorAll('.active');
            }
        };
        CategoryDelete.deleteBtn.addEventListener('click', CategoryDelete.createModalContent.bind(CategoryDelete));
        CategoryDelete.confirmButton.addEventListener('click', CategoryDelete.confirmDeleteCategory.bind(CategoryDelete));
    </script>
@endsection
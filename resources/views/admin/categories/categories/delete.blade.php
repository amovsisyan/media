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
            deleteBtn: document.getElementById('delete_category'),
            confirmButton: document.getElementById('confirm_delete_category'),
            categoryDeleteRow: document.getElementById('category_delete_row'),
            deleteConfirmBtn: document.getElementById('confirm_delete_category'),
            modalDeleteCategory: document.getElementById('modal_delete_category'),
            categorySelect: document.getElementById('category_select'),

            createModalContent: function() {
                var content = this.modalDeleteCategory.getElementsByClassName('modal-content'),
                    paragraph = content[0].getElementsByTagName('p')[0],
                    list = this.getDeleteList(),
                    cat_s = '';
                Array.prototype.forEach.call(list, (function (element, index, array) {
                    cat_s += '<p>' + ++index + '. ' + element.getElementsByTagName('span')[0].textContent + '</p>';
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
                    prepData.push(element.getElementsByTagName('span')[0].textContent);
                }));
                var data = 'data=' + JSON.stringify(prepData);

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        self.handleResponseToast(xhr.status, 'Deleted Category(es)', 'status_ok')
                        if (response.ids && response.ids.length) {
                            response.ids.forEach(function (element, index, array) {
                                var options = self.categorySelect.getElementsByTagName('option');
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
                        var errors = response.response,
                            _html = '';
                        errors.forEach(function (element, index, array) {
                            _html += element;
                        });
                        self.handleResponseToast(xhr.status, _html, 'status_warning')
                    }
                };
                xhr.send(encodeURI(data));
            },

            handleResponseToast: function(status, text, style) {
                Materialize.toast(text, 5000, 'rounded');
                var toasts = document.getElementById("toast-container").getElementsByClassName("toast "),
                    toast = toasts[toasts.length-1];

                toast.classList.add(style);
                this.deleteBtn.classList.remove('disabled');
                this.confirmButton.classList.remove('disabled');
            },

            getDeleteList: function() {
                return this.categoryDeleteRow.getElementsByClassName('multiple-select-dropdown')[0].getElementsByClassName('active');
            }
        };
        CategoryDelete.deleteBtn.addEventListener('click', CategoryDelete.createModalContent.bind(CategoryDelete));
        CategoryDelete.confirmButton.addEventListener('click', CategoryDelete.confirmDeleteCategory.bind(CategoryDelete));
    </script>
@endsection
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
                        <input id="category_alias" name="alias" type="text" class="validate">
                        <label for="last_name">Alias(English)</label>
                    </div>
                </div>
                <div class="col m6 s12">
                    <div class="input-field col s8">
                        <input id="category_name" name="name" type="text" class="validate">
                        <label for="last_name">Name(Russian)</label>
                    </div>
                </div>
            </div>
            <div class="row right-align m_t_50">
                <a class="waves-effect waves-light btn">test it</a>
                <!-- Modal -->
                <a id='add_category' class="waves-effect waves-light btn modal-trigger" href="#modal_add_category">Add</a>
                <div id="modal_add_category" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Create Category?</h4>
                        <p></p>
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
            categoryName: document.getElementById('category_name'),
            categoryAlias: document.getElementById('category_alias'),
            modalAddCategory: document.getElementById('modal_add_category'),

            confirmCategory: function(){
                this.addButton.classList.add('disabled');
                this.confirmButton.classList.add('disabled');

                var self = this,
                    data = 'category_name=' + this.categoryName.value + '&category_alias=' + this.categoryAlias.value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', window.location.href);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        self.handleResponseToast(xhr.status, 'Added New Category', 'status_ok');
                        if (xhr.status === 200) {
                            self.categoryName.value = '';
                            self.categoryAlias.value = '';
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
                var content = this.modalAddCategory.getElementsByClassName('modal-content'),
                    paragraph = content[0].getElementsByTagName('p')[0],
                    _html = '<p>Name: ' + this.categoryName.value + '</p><p>Alias: ' + this.categoryAlias.value + '</p>';
                paragraph.innerHTML = _html;
            },

            handleResponseToast: function(status, text, style) {
                Materialize.toast(text, 5000, 'rounded');
                var toasts = document.getElementById("toast-container").getElementsByClassName("toast "),
                    toast = toasts[toasts.length-1];

                toast.classList.add(style);
                this.addButton.classList.remove('disabled');
                this.confirmButton.classList.add('disabled');
            }
        };
        CategoryCreate.confirmButton.addEventListener('click', CategoryCreate.confirmCategory.bind(CategoryCreate));
        CategoryCreate.addButton.addEventListener('click', CategoryCreate.createModalContent.bind(CategoryCreate));
    </script>
@endsection
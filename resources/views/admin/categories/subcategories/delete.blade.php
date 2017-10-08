@extends('admin.index')

@section('content-body')
    <section id="delete-subcategory-panel">
        <div class="container">
            <div class="row" id="subcategory_delete_row">
                <div class="input-field col m6 s12">
                    <select id="subcategory_select">
                        @if ($response && isset($response['categories']) && !empty($response['categories']))
                            @foreach ($response['categories'] as $category)
                                <optgroup label="{{ $category[0]['name'] }}">
                                    @foreach ($category['subcategory'] as $subcategory)
                                        <option value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endif
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
            deleteBtn: document.getElementById('delete_subcategory'),
            confirmButton: document.getElementById('confirm_delete_subcategory'),
            modalDeleteSubcategory: document.getElementById('modal_delete_subcategory'),
            subcategorySelect: document.getElementById('subcategory_select'),

            createModalContent: function() {
                var paragraph = this.modalDeleteSubcategory.getElementsByTagName('p')[0],
                    selectedOption = this.subcategorySelect.options[this.subcategorySelect.selectedIndex],
                    _html = '<p>SubcategoryId: ' + selectedOption.value + '</p>' +
                        '<p>SubcategoryName: ' + selectedOption.innerHTML + '</p>';

                paragraph.innerHTML = _html;
                this.confirmButton.addEventListener('click',
                    this.deleteSubcategoryRequest.bind(this)
                );
            },

            deleteSubcategoryRequest: function() {
                var updateBtns = [this.deleteBtn, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);

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
                            var options = self.subcategorySelect.getElementsByTagName('option');
                            response.ids.forEach(function (element, index, array) {
                                Array.prototype.forEach.call(options,(function (el, i, arr){
                                    if(el.value === element) {
                                        el.remove();
                                    }
                                }));
                            });
                            $('#subcategory_select').material_select();
                        }
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            }
        };
        SubcategoryDelete.deleteBtn.addEventListener('click', SubcategoryDelete.createModalContent.bind(SubcategoryDelete));
    </script>
@endsection
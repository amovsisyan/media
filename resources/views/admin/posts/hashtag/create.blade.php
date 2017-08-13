@extends('admin.index')

@section('content-body')
    <section id="create-hashtag-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Create Hashtag</h4>
            </div>
            <div class="row">
                <div class="col m6 s12">
                    <div class="input-field col s8">
                        <input id="hashtag_alias" name="alias" type="text" class="validate">
                        <label for="alias">Alias(English)</label>
                    </div>
                </div>
                <div class="col m6 s12">
                    <div class="input-field col s8">
                        <input id="hashtag_name" name="name" type="text" class="validate">
                        <label for="name">Name(Russian)</label>
                    </div>
                </div>
            </div>
            <div class="row right-align m_t_50">
                <!-- Modal -->
                <a id='add_hashtag' class="waves-effect waves-light btn modal-trigger" href="#modal_add_hashtag">Add</a>
                <div id="modal_add_hashtag" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Create Hashtag?</h4>
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <a id='confirm_hashtag' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
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

        HashtagCreate = {
            addButton: document.getElementById('add_hashtag'),
            confirmButton: document.getElementById('confirm_hashtag'),
            hashtagName: document.getElementById('hashtag_name'),
            hashtagAlias: document.getElementById('hashtag_alias'),
            modalAddHashtag: document.getElementById('modal_add_hashtag'),

            confirmHashtag: function(){
                this.addButton.classList.add('disabled');
                this.confirmButton.classList.add('disabled');

                var self = this,
                    data = 'hashtag_name=' + this.hashtagName.value + '&hashtag_alias=' + this.hashtagAlias.value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', window.location.href);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        self.handleResponseToast(xhr.status, 'Added New Hashtag', 'status_ok');
                        if (xhr.status === 200) {
                            self.hashtagName.value = '';
                            self.hashtagAlias.value = '';
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
                var content = this.modalAddHashtag.getElementsByClassName('modal-content'),
                    paragraph = content[0].getElementsByTagName('p')[0],
                    _html = '<p>Name: ' + this.hashtagName.value + '</p><p>Alias: ' + this.hashtagAlias.value + '</p>';
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
        HashtagCreate.confirmButton.addEventListener('click', HashtagCreate.confirmHashtag.bind(HashtagCreate));
        HashtagCreate.addButton.addEventListener('click', HashtagCreate.createModalContent.bind(HashtagCreate));
    </script>
@endsection
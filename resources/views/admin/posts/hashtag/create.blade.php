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
                        <input id="hashtag_alias" name="alias" type="text" class="validate" data-length={{$response['colLength']['alias']}}>
                        <label for="alias">Alias(English)</label>
                    </div>
                </div>
                <div class="col m6 s12">
                    <div class="input-field col s8">
                        <input id="hashtag_name" name="name" type="text" class="validate" data-length={{$response['colLength']['hashtag']}}>
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
                var updateBtns = [this.addButton, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    data = 'hashtag_name=' + this.hashtagName.value + '&hashtag_alias=' + this.hashtagAlias.value,
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Added New Hashtag');
                        self.hashtagName.value = '';
                        self.hashtagAlias.value = '';
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            createModalContent: function() {
                var content = this.modalAddHashtag.getElementsByClassName('modal-content')[0],
                    paragraph = content.getElementsByTagName('p')[0],
                    _html = '<p>Name: ' + this.hashtagName.value + '</p><p>Alias: ' + this.hashtagAlias.value + '</p>';
                paragraph.innerHTML = _html;
            }
        };
        HashtagCreate.confirmButton.addEventListener('click', HashtagCreate.confirmHashtag.bind(HashtagCreate));
        HashtagCreate.addButton.addEventListener('click', HashtagCreate.createModalContent.bind(HashtagCreate));
    </script>
@endsection
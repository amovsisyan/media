@extends('admin.index')

@section('content-body')
    <section id="create-hashtag-panel">
        <div class="container">
            <div class="row center-align">
                <h4>Create Hashtag</h4>
            </div>
            <div class="row">
                <div class="col m8 s12">
                    <div class="input-field">
                        <input id="hashtag_alias" type="text" class="validate" data-length={{$response['colLength']['alias']}}>
                        <label for="alias">Alias(English)</label>
                    </div>
                </div>
            </div>
            @if (!empty($response['activeLocales']))
                @foreach ($response['activeLocales'] as $locale)
                    <div class="row">
                        <div class="col m1 s12">
                            <img src="/img/flags/{{$locale['name']}}.svg" alt="">
                        </div>
                        <div class="col m7 s12">
                            <div class="input-field">
                                <input data-localeid="{{$locale['id']}}" class="hashtag_name" type="text" class="validate" data-length={{$response['colLength']['hashtag']}}>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            <div class="row right-align m_t_50">
                <!-- Modal -->
                <a id='add_hashtag' class="waves-effect waves-light btn modal-trigger" href="#modal_add_hashtag">Add</a>
                <div id="modal_add_hashtag" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Create Hashtag?</h4>
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
            hashtagNames: document.getElementsByClassName('hashtag_name'),
            hashtagAlias: document.getElementById('hashtag_alias'),
            modalAddHashtag: document.getElementById('modal_add_hashtag'),

            confirmHashtag: function(){
                var updateBtns = [this.addButton, this.confirmButton];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    data = 'hashtagAlias=' + this.hashtagAlias.value
                        + '&hashtagNames=' + this.getHashtagNames(),
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Added New Hashtag');
                        self.regenerateAfterDelete();
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(encodeURI(data));
            },

            getHashtagNames: function () {
                var hashtagNames = [];
                Array.prototype.forEach.call(this.hashtagNames, (function (element, index, array) {
                    // todo standardizatoin needed 1-1 -->4
                    var names = {
                        locale_id: element.dataset.localeid,
                        name: element.value
                    };
                    hashtagNames.push(names);
                }));
                return JSON.stringify(hashtagNames);
            },

            regenerateAfterDelete: function () {
                Array.prototype.forEach.call(this.hashtagNames, (function (element, index, array) {
                    element.value = ''
                }));
                this.hashtagAlias.value = '';
            }
        };
        HashtagCreate.confirmButton.addEventListener('click', HashtagCreate.confirmHashtag.bind(HashtagCreate));
    </script>
@endsection
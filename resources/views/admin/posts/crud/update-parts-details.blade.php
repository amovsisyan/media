@extends('admin.index')

@section('content-body')
    <section id="post-parts-details">
        <div class="container">
            <div class="row">
            @if(!empty($response['post']))
                @foreach($response['post']['postparts'] as $locale => $postParts)
                    <div class="existing-parts-container col s{{$response['templateDivider']}}" id="main-locale-{{$locale}}" data-localename="{{$locale}}">
                        <div class="row">
                            <div class="col s12">
                                <div class="col s2">
                                    <img src="/img/flags/{{$locale}}.svg" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <ul class="collapsible popout collapsible-container" data-collapsible="accordion">
                            @foreach($postParts as $key => $postPart)
                                <li class="collapse-post-parts" id="part-num-{{$postPart['id']}}" data-partid="{{$postPart['id']}}">
                                    <div class="collapsible-header">
                                        <span class="part-number">{{$key+1}}</span>
                                        <span class="part-header">{{$postPart['head']}}</span>
                                    </div>
                                    <div class="collapsible-body">
                                        <div class="part-id col m2 s12" data-id="{{$postPart['id']}}">id: {{$postPart['id']}}</div>
                                        <div class="col m8 s12">
                                            <div class="input-field col s12">
                                                <textarea class="part-head materialize-textarea" name="part-head" data-length={{$response['colLength']['parts']['head']}}>{{$postPart['head']}}</textarea>
                                                <label for="part-head">Part Head</label>
                                            </div>
                                            <div class="file-field input-field col s12">
                                                <div class="btn">
                                                    <span>Part Image <span class="important_icon">*</span></span>
                                                    <input type="file" class="part-image" name="part-image" accept="image/*" enctype="multipart/form-data">
                                                </div>
                                                <div class="file-path-wrapper">
                                                    <input class="file-path validate" type="text" value="{{$postPart['body']}}">
                                                </div>
                                            </div>
                                            <div class="input-field col s12">
                                                <textarea class="part-foot materialize-textarea" name="part-foot" data-length={{$response['colLength']['parts']['foot']}}>{{$postPart['foot']}}</textarea>
                                                <label for="part-foot">Part Foot</label>
                                            </div>
                                        </div>
                                        <div class="col m2 s12">
                                            <div class="fixed-action-btn vertical click-to-toggle col m1 s12">
                                                <a class="btn-floating red">
                                                    <i class="material-icons">menu</i>
                                                </a>
                                                <ul>
                                                    <li><a class="btn-floating modal-trigger part-save-btn" href="#updatePostPartModal">Save</a></li>
                                                    <li><a class="btn-floating red modal-trigger part-delete-btn" href="#removePostPartModal">Del</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            @endif
            </div>
        </div>
    </section>
    <section id="post-parts-details">
        <div class="container">
            <div class="row" id="new-post-parts">
                <div class="col s12">
                    <h4>New Post Part</h4>
                </div>
                <div id="parts-container">
                    @include('admin.posts.crud.create-part')
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal" id="modal-add-parts">
        <div class="modal-content left-align">
            <h4>Are You Sure You Want Add This Post Part?</h4>
            <p></p>
        </div>
        <div class="modal-footer">
            <a id='confirm-post-part-addition' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
            <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="removePostPartModal">
        <div class="modal-content left-align">
            <h4>Are You Sure You Want Delete This Post Part?</h4>
            <p></p>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close waves-effect waves-green btn-flat red" id="post-part-confirm-delete">Delete</a>
            <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="updatePostPartModal">
        <div class="modal-content left-align">
            <h4>Are You Sure You Want Update This Post Part?</h4>
            <p></p>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close waves-effect waves-green btn-flat green" id="post-part-confirm-update">Update</a>
            <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
        </div>
    </div>
@endsection

@section('script')
    {{--require imageStandards --}}
    <script src="/js/admin/imageStandards.js"></script>

    <script>
        $('.modal').modal();

        PostPartDetails = {
            partDeleteBtns: document.getElementsByClassName('part-delete-button'),
            existingPartDeleteBtns: document.getElementsByClassName('part-delete-btn'),

            partSaveBtns:document.getElementsByClassName('part-save-btn'),
            postPartConfirmUpdate:document.getElementById('post-part-confirm-update'),
            postPartAddBtns:document.getElementsByClassName('post-part-add-button'),

            postPartConfirmDelete:document.getElementById('post-part-confirm-delete'),

            postPartImageInputs:document.getElementsByClassName('part-image'),

            confirmPostPartAdditionBtn: document.getElementById('confirm-post-part-addition'),

            _init: function() {
                var self = this;
                Array.prototype.forEach.call(self.partDeleteBtns, (function (element, index, array) {
                        element.getElementsByClassName('btn')[0].classList.add('hide');
                    })
                )
                this.makeListenersForExistedParts();
            },

            makeListenersForExistedParts: function () {
                var self = this;

                // Part Save Btns
                Array.prototype.forEach.call(self.partSaveBtns, (function (element, index, array) {
                    element.addEventListener('click', self.sendIdToUpdateModal.bind(self));
                }));

                // Generate Post Part add btns
                Array.prototype.forEach.call(self.postPartAddBtns, (function (element, index, array) {
                    element.href = '#modal-add-parts';
                    element.addEventListener('click', self.sendIdToNewPartModal.bind(self));
                }));

                // Generate Post Part delete btns
                Array.prototype.forEach.call(self.existingPartDeleteBtns, (function (element, index, array) {
                    element.addEventListener('click', self.sendIdToDeleteModal.bind(self));
                }));

                // Image size changer
                Array.prototype.forEach.call(self.postPartImageInputs, (function (element, index, array) {
                    element.addEventListener('change', self.imageSizeWarningLocal.bind(self));
                }));

                // Modal Post Part Update Confirm Listener
                self.postPartConfirmUpdate.addEventListener('click', self.updatePostPartRequest.bind(self));
                // Modal Post Part Add Listener
                self.confirmPostPartAdditionBtn.addEventListener('click', self.addPostPartsRequest.bind(self));
                // Modal Post Part Delete Listener
                self.postPartConfirmDelete.addEventListener('click', self.deletePostPartRequest.bind(self));
            },

            imageSizeWarningLocal: function(e) {
                var el = e.target,
                    files = el.files;
                imageSizeWarning(files, imageStandards.partsImageStandard);
            },

            sendIdToUpdateModal: function (e) {
                var el = getClosest(e.target, '.collapsible-body'),
                    id = el.getElementsByClassName('part-id')[0].dataset.id;
                 this.postPartConfirmUpdate.dataset.id = id;
            },

            sendIdToNewPartModal: function (e) {
                var localename = getClosest(e.target, '.part-locale-container').dataset.localename;
                this.confirmPostPartAdditionBtn.dataset.localename = localename;
            },

            sendIdToDeleteModal: function (e) {
                var partId = getClosest(e.target, '.collapse-post-parts').dataset.partid;
                this.postPartConfirmDelete.dataset.partid = partId;
            },

            updatePostPartRequest: function(e) {
                var updateBtns = [this.postPartConfirmUpdate];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    id = e.target.dataset.id,
                    el = document.getElementById('part-num-' + id),
                    head = el.getElementsByClassName('part-head')[0].value,
                    foot = el.getElementsByClassName('part-foot')[0].value,
                    xhr = new XMLHttpRequest(),
                    data = new FormData();

                data.append("partId", id);
                data.append("head", head);
                data.append("foot", foot);

                if (el.getElementsByClassName('part-image')[0].files.length) {
                    var body = el.getElementsByClassName('part-image')[0].files[0];
                    data.append("body", body);
                }

                xhr.open('POST', location.pathname);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Post Part Updated');
                        location.reload();
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                    updateAddConfirmButtons(updateBtns, false);
                };
                xhr.send(data);
            },

            deletePostPartRequest: function (e) {
                var updateBtns = [this.postPartConfirmDelete];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    id = e.target.dataset.partid,
                    data = encodeURI('partId=' + id),
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname + '/delete');
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Post Part deleted');
                        document.getElementById('part-num-' + id).remove();
                    } else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                };
                xhr.send(data);
                updateAddConfirmButtons(updateBtns, false);
            },

            addPostPartsRequest: function (e) {
                var updateBtns = [this.confirmPostPartAdditionBtn];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    locale = e.target.dataset.localename,
                    xhr = new XMLHttpRequest(),
                    postPart = document.getElementById('part-locale-' + locale),
                    formData = new FormData();

                formData.append('locale', locale);
                formData.append('partHeader[' + locale + '][]', postPart.getElementsByClassName('part-header')[0].value);
                formData.append('partFooter[' + locale + '][]', postPart.getElementsByClassName('part-footer')[0].value);

                var fileContainer = postPart.getElementsByClassName('part-image')[0].files,
                file = [];
                if (fileContainer.length) {
                    file = postPart.getElementsByClassName('part-image')[0].files[0]
                }
                formData.append('partImage[' + locale + '][]', file);

                xhr.open('POST', location.pathname + '/add-part', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());

                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Added New Post Parts');
                        self._regenerateAfterNewCreation(postPart);
                        location.reload();
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                };
                xhr.send(formData);
                updateAddConfirmButtons(updateBtns, false);
            },

            _regenerateAfterNewCreation: function(postPart) {
                postPart.getElementsByClassName('part-header')[0].value = '';
                postPart.getElementsByClassName('part-footer')[0].value = '';
                postPart.getElementsByClassName('part-image')[0].value = '';
                postPart.getElementsByClassName('file-path')[0].value = '';
            }
        };

        PostPartDetails._init();
    </script>
@endsection
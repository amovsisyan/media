@extends('admin.index')

@section('content-body')
    <section id="post-parts-details">
        <div class="container">
            <div class="row">
                <ul class="collapsible popout collapsible-container" data-collapsible="accordion">
                    @if(!empty($response['post']))
                        @foreach($response['post']['postparts'] as $key => $postParts)
                            <li class="collapse-post-parts" id="part-num-{{$postParts['id']}}">
                                <div class="collapsible-header">
                                    <span class="part-number">{{$key+1}}</span>
                                    <span class="part-header">{{$postParts['head']}}</span>
                                </div>
                                <div class="collapsible-body">
                                    <div class="part-id col m2 s12" data-id="{{$postParts['id']}}">id: {{$postParts['id']}}</div>
                                    <div class="col m8 s12">
                                        <div class="input-field col s12">
                                            <input type="text" class="part-head validate" name=part-head"
                                                   value="{{$postParts['head']}}"
                                            >
                                            <label for="part-head">Part Head</label>
                                        </div>
                                        <div class="file-field input-field col s12">
                                            <div class="btn">
                                                <span>Part Image <span class="important_icon">*</span></span>
                                                <input type="file" class="part-image" name="part-image" accept="image/*" enctype="multipart/form-data">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text"
                                                       value="{{$postParts['body']}}"
                                                >
                                            </div>
                                        </div>
                                        <div class="input-field col s12">
                                            <input type="text" class="part-foot validate" name="part-foot"
                                                   value="{{$postParts['foot']}}"
                                            >
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
                                                <li><a class="btn-floating orange" href="/qwentin/posts/crud/attach_post_part/{{$postParts['id']}}" target="_blank">Attach</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </section>
    <section id="post-parts-details">
        <div class="container">
            <div class="row" id="new-post-parts">
                <div class="col s12">
                    <h4>New Post Parts</h4>
                </div>
                <div id="parts-container">
                    @include('admin.posts.crud.create-part')
                </div>
            </div>
            <div class="row right-align post-part-add-button-container">
                <a class="waves-effect waves-light btn" id="post-part-add-button">+Add Part</a>
            </div>
        </div>
    </section>

    <section id="post-parts-add-buttons">
        <div class="container">
            <div class="row right-align m_t_50 post-create-btns">
                <a class="waves-effect waves-light btn">Finish & Test</a>
                <!-- Modal -->
                <a id='add-parts' class="waves-effect waves-light btn modal-trigger" href="#modal-add-parts">Finish & Add</a>
                <div id="modal-add-parts" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Add This(these) Post Part(s)?</h4>
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <a id='confirm-post-parts-addition' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
                        <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

    <!-- Modal -->
    <div class="modal" id="deletePostPartModal">
        <div class="modal-content left-align">
            <h4>Are You Sure You Want Delete This Not Generated Post Part?</h4>
            <p></p>
        </div>
        <div class="modal-footer">
            <a class="modal-action modal-close waves-effect waves-green btn-flat red" id="post-part-delete">Delete</a>
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
            newPartCount: 1,
            addPartsBtn:document.getElementById('add-parts'),

            partSaveBtns:document.getElementsByClassName('part-save-btn'),
            postPartConfirmUpdate:document.getElementById('post-part-confirm-update'),
            postPartDelete:document.getElementById('post-part-delete'),
            postPartAddBtn:document.getElementById('post-part-add-button'),

            partDeleteBtns:document.getElementsByClassName('part-delete-btn'),
            postPartConfirmDelete:document.getElementById('post-part-confirm-delete'),

            postPartImageInputs:document.getElementsByClassName('part-image'),

            partsContainer: document.getElementById('parts-container'),
            postPart:document.getElementsByClassName('post-part')[0],
            partDeleteBtn:document.getElementsByClassName('part-delete-button')[0],

            confirmPostPartsAdditionBtn: document.getElementById('confirm-post-parts-addition'),

            _init: function() {
                this.makeListenersForExistedParts();
                document.getElementsByClassName('post-number')[0].innerHTML = this.newPartCount;
                this.postPart.id = 'post-part-id-' + this.newPartCount;
                this.postPart.dataset.num = this.newPartCount;
            },

            makeListenersForExistedParts: function () {
                var self = this;

                Array.prototype.forEach.call(self.partSaveBtns, (function (element, index, array) {
                    element.addEventListener('click', self.sendIdToUpdateModal.bind(self));
                }));
                Array.prototype.forEach.call(self.partDeleteBtns, (function (element, index, array) {
                    element.addEventListener('click', self.sendIdToDeleteModal.bind(self));
                }));
                Array.prototype.forEach.call(self.postPartImageInputs, (function (element, index, array) {
                    element.addEventListener('change', self.imageSizeWarningLocal.bind(self));
                }));
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

            sendIdToDeleteModal: function (e) {
                var el = getClosest(e.target, '.collapsible-body'),
                    id = el.getElementsByClassName('part-id')[0].dataset.id;
                this.postPartConfirmDelete.dataset.id = id;
            },

            updatePostPartRequest: function(e) {
                var updateBtns = [this.postPartConfirmUpdate];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    id = e.target.dataset.id,
                    el = document.getElementById('part-num-' + id),
                    head = el.getElementsByClassName('part-head')[0].value,
                    foot = el.getElementsByClassName('part-foot')[0].value,
                    data = '',
                    xhr = new XMLHttpRequest();

                xhr.open('POST', location.pathname);

                if (el.getElementsByClassName('part-image')[0].files.length) {
                    var body = el.getElementsByClassName('part-image')[0].files[0];

                    data = new FormData();
                    data.append("partId", id);
                    data.append("head", head);
                    data.append("body", body);
                    data.append("foot", foot);
                } else {
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    data = encodeURI('partId=' + id + '&head=' + head + '&foot=' + foot)
                }


                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Post Part Updated');
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
                    id = e.target.dataset.id,
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

            partDelEvent: function(e) {
                this.postPartDelete.dataset.num = getClosest(e.target, '.create-part').dataset.num;
            },

            addPostPart: function() {
                var clone = this.postPart.cloneNode(true);
                this.partsContainer.appendChild(clone);
                this.renderPartTemplate();
            },

            renderPartTemplate: function() {
                var num = this.newPartCount++,
                    currTempl = document.getElementsByClassName('post-part')[num];
                this._regeneratePostPartIds(currTempl);

                currTempl.getElementsByClassName('part-header')[0].value = '';
                currTempl.getElementsByClassName('part-footer')[0].value = '';
                currTempl.getElementsByClassName('file-path ')[0].value = '';

                currTempl.getElementsByClassName('part-delete-button')[0].addEventListener('click',
                    this.partDelEvent.bind(this)
                );
                currTempl.getElementsByClassName('part-image')[0].addEventListener('change',
                    this.imageSizeWarningLocal.bind(self)
                );
            },

            _regeneratePostPartIds: function(element) {
                element.dataset.num = this.newPartCount;
                element.id = 'post-part-id-' + this.newPartCount;
                element.getElementsByClassName('post-number')[0].innerHTML = this.newPartCount;
            },

            generatePartDelBtnListener: function(e) {
                if (this.newPartCount === 1) {
                    alert('You Cant Delete Last Part');
                } else {
                    document.getElementById('post-part-id-' + e.target.dataset.num).remove();
                    this.newPartCount = e.target.dataset.num;
                    this.regenerateAfterPartDelete();
                }
            },

            regenerateAfterPartDelete: function () {
                var self = this,
                    allPostParts = document.getElementsByClassName('post-part');

                Array.prototype.forEach.call(allPostParts, (function (element, index, array) {
                    var arr = element.id.split('-'),
                        id = arr[arr.length-1];
                    if (id > self.newPartCount) {
                        self._regeneratePostPartIds(element);
                        self.newPartCount++;
                    }
                }));

                this.newPartCount--;
            },

            addPostPartsRequest: function () {
                var updateBtns = [this.confirmPostPartsAdditionBtn, this.addPartsBtn];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    xhr = new XMLHttpRequest(),
                    allPostParts = document.getElementsByClassName('post-part'),
                    formData = new FormData();

                // Parts
                Array.prototype.forEach.call(allPostParts, (function (element, index, array) {
                    formData.append('partHeader[]', element.getElementsByClassName('part-header')[0].value);

                    var fileContainer = element.getElementsByClassName('part-image')[0].files,
                        file = [];
                    if (fileContainer.length) {
                        file = element.getElementsByClassName('part-image')[0].files[0]
                    }
                    formData.append('partImage[' + index + ']', file);
                    formData.append('partFooter[]', element.getElementsByClassName('part-footer')[0].value);
                }));

                xhr.open('POST', location.pathname + '/add-parts', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());

                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Added New Post Parts');
                        self._regenerateAfterNewCreation();
                        location.reload();
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                };
                xhr.send(formData);
                updateAddConfirmButtons(updateBtns, false);
            },

            _regenerateAfterNewCreation: function(element) {
                this.newPartCount = 1;
                var allParts = document.getElementsByClassName('post-part');

                Array.prototype.forEach.call(allParts, (function (element, index, array) {
                        if (index === 0) {
                            element.getElementsByClassName('part-header')[0].value = '';
                            element.getElementsByClassName('part-footer')[0].value = '';
                            element.getElementsByClassName('part-image')[0].value = '';
                            element.getElementsByClassName('file-path')[0].value = '';
                        } else {
                            element.remove();
                        }
                    })
                );
            }
        };

        PostPartDetails.postPartConfirmUpdate.addEventListener('click', PostPartDetails.updatePostPartRequest.bind(PostPartDetails));
        PostPartDetails.postPartConfirmDelete.addEventListener('click', PostPartDetails.deletePostPartRequest.bind(PostPartDetails));
        PostPartDetails.postPartDelete.addEventListener('click', PostPartDetails.generatePartDelBtnListener.bind(PostPartDetails));
        PostPartDetails.partDeleteBtn.addEventListener('click', PostPartDetails.partDelEvent.bind(PostPartDetails));
        PostPartDetails.postPartAddBtn.addEventListener('click', PostPartDetails.addPostPart.bind(PostPartDetails));
        PostPartDetails.confirmPostPartsAdditionBtn.addEventListener('click', PostPartDetails.addPostPartsRequest.bind(PostPartDetails));
        PostPartDetails._init();
    </script>
@endsection
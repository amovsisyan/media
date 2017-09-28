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
                                                <li><a class="btn-floating red modal-trigger part-delete-btn" href="#deletePostPartModal">Del</a></li>
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

        <!-- Modal -->
        <div class="modal" id="deletePostPartModal">
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
    </section>
@endsection

@section('script')
    <script>
        $('.modal').modal();
        PostPartDetails = {
            partSaveBtns:document.getElementsByClassName('part-save-btn'),
            postPartConfirmUpdate:document.getElementById('post-part-confirm-update'),

            partDeleteBtns:document.getElementsByClassName('part-delete-btn'),
            postPartConfirmDelete:document.getElementById('post-part-confirm-delete'),

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
                this.postPartConfirmUpdate.classList.add('disabled');
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
                    self.postPartConfirmUpdate.classList.remove('disabled');
                };
                xhr.send(data);
            },

            deletePostPartRequest: function (e) {
                this.postPartConfirmDelete.classList.add('disabled');
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
                this.postPartConfirmDelete.classList.remove('disabled');
            }
        };

        Array.prototype.forEach.call(PostPartDetails.partSaveBtns, (function (element, index, array) {
            element.addEventListener('click', PostPartDetails.sendIdToUpdateModal.bind(PostPartDetails));
        }));
        Array.prototype.forEach.call(PostPartDetails.partDeleteBtns, (function (element, index, array) {
            element.addEventListener('click', PostPartDetails.sendIdToDeleteModal.bind(PostPartDetails));
        }));

        PostPartDetails.postPartConfirmUpdate.addEventListener('click', PostPartDetails.updatePostPartRequest.bind(PostPartDetails));
        PostPartDetails.postPartConfirmDelete.addEventListener('click', PostPartDetails.deletePostPartRequest.bind(PostPartDetails));
    </script>
@endsection
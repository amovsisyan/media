@extends('admin.index')

@section('content-body')
    <section id="post-main-details">
        <div class="container">
            <div class="row center-align">
                <h4>Update Post</h4>
                <h6>(All fields with <span class="important_icon">*</span> are required)</h6>
            </div>
            <div class="row" id="post-update-main">
                <div class="col s12">
                    <h4>Main</h4>
                </div>
                <div class="col m7 s12">
                    <div class="input-field">
                        <input id="alias" name="alias" type="text" class="validate"
                               value="{{$response['post']['alias']}}"
                        >
                        <label for="alias">Alias (English) <span class="important_icon">*</span></label>
                    </div>
                </div>
                <div class="col s12">
                    <div class="input-field">
                        <textarea id="main_header" name="main_header" class="materialize-textarea">{{$response['post']['header']}}</textarea>
                        <label for="main_header">Header (Russian) <span class="important_icon">*</span></label>
                    </div>
                </div>
                <div class="col s12">
                    <div class="input-field">
                        <textarea id="main_text" name="main_text" class="materialize-textarea">{{$response['post']['text']}}</textarea>
                        <label for="main_text">Main Text (Russian) <span class="important_icon">*</span></label>
                    </div>
                </div>
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn">
                            <span>Main Image <span class="important_icon">*</span></span>
                            <input type="file" id="main_image" name="main_image" accept="image/*" enctype="multipart/form-data">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" value="{{$response['post']['image']}}">
                        </div>
                    </div>
                </div>
                <div class="input-field col m7 s12" id="subcategory_select_container">
                    <select id="subcategory_select">
                        @if (!empty($response) && !empty($response['categories']))
                            @foreach ($response['categories'] as $category)
                                <optgroup label="{{ $category['category']['name'] }}">
                                    @foreach ($category['subcategory'] as $subcategory)
                                        @if($subcategory['id'] === $response['post']['subcateg_id'])
                                            <option value="{{ $subcategory['id'] }}" selected>{{ $subcategory['name'] }}</option>
                                        @else
                                            <option value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}</option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endif
                    </select>
                    <label>Select Subcategory <span class="important_icon">*</span></label>
                </div>
                <div class="input-field col m7 s12" id="hashtag_select_container">
                    <select multiple id="hashtag_select">
                        <option value="" disabled selected>Select Hashtag</option>
                        @if (!empty($response) && !empty($response['hashtags']))
                            @foreach ($response['hashtags'] as $hashtag)
                                @if(in_array($hashtag['id'], $response['post']['hashtags']))
                                    <option value="{{ $hashtag['id'] }}" selected>{{ $hashtag['hashtag'] }}_{{ $hashtag['id'] }}</option>
                                @else
                                    <option value="{{ $hashtag['id'] }}">{{ $hashtag['hashtag'] }}_{{ $hashtag['id'] }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <label>You can choose multiple hashtags <span class="important_icon">*</span></label>
                </div>
            </div>

            <div class="row right-align m_t_50 post-update-btns">
                <a class="waves-effect waves-light btn">Finish & Test</a>
                <a id='update_post' class="waves-effect waves-light btn modal-trigger" href="#modal_update_post">Finish & Update</a>
                <!-- Modal -->
                <div id="modal_update_post" class="modal">
                    <div class="modal-content left-align">
                        <h4>Are You Sure You Want Update Post?</h4>
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <a id='confirm-main-update' class="modal-action modal-close waves-effect waves-green btn-flat">Agree</a>
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
            $('#subcategory_select').material_select();
            $('#hashtag_select').material_select();
            $('.modal').modal();
        });

        PostMainUpdate = {
            updatePostBtn: document.getElementById('update_post'),
            postMainDetails: document.getElementById('post-main-details'),
            confirmMainUpdate: document.getElementById('confirm-main-update'),

            confirmMainUpdateRequest: function () {
                var updateBtns = [this.confirmMainUpdate, this.updatePostBtn];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    hashtags = [],
                    xhr = new XMLHttpRequest(),
                    hashtagContainer = document.getElementById('hashtag_select_container'),
                    mainAlias = document.getElementById('alias'),
                    mainHeader = document.getElementById('main_header'),
                    mainText = document.getElementById('main_text'),
                    mainImage = document.getElementById('main_image'),
                    subcategorySelect = document.getElementById('subcategory_select'),
                    formData = new FormData();

                Array.prototype.forEach.call(this.getHashtagList(hashtagContainer), (function (element, index, array) {
                    var elementContent = element.getElementsByTagName('span')[0].textContent;
                    hashtags.push(explodeGetLast(elementContent, '_'))
                }));

                // Main
                formData.append("postAlias", mainAlias.value);
                formData.append("postMainHeader", mainHeader.value);
                formData.append("postMainText", mainText.value);
                formData.append("postMainImage", mainImage.files[0]);
                formData.append("postSubcategory", subcategorySelect.options[subcategorySelect.selectedIndex].value);
                formData.append("postHashtag", JSON.stringify(hashtags));

                xhr.open('POST', location.pathname, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());

                xhr.onload = function() {
                    var response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && response.error !== true) {
                        handleResponseToast(response, true, 'Post Was Updated');
                    }
                    else if (xhr.status !== 200 || response.error === true) {
                        handleResponseToast(response, false);
                    }
                };
                xhr.send(formData);
                updateAddConfirmButtons(updateBtns, false);
            },

            getHashtagList: function(container) {
                var hashtags = container
                    .getElementsByClassName('select-wrapper')[0]
                    .getElementsByClassName('multiple-select-dropdown')[0]
                    .getElementsByClassName('active');
                return hashtags ? hashtags : [];
            }
        };
        PostMainUpdate.confirmMainUpdate.addEventListener('click', PostMainUpdate.confirmMainUpdateRequest.bind(PostMainUpdate));
    </script>
@endsection
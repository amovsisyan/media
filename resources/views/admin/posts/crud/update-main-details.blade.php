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
            </div>
            <div class="row">
                <div class="col m4 s12">
                    <div class="input-field">
                        <input id="alias" name="alias" type="text" class="validate"
                               value="{{$response['post']['alias']}}"
                               data-length={{$response['colLength']['post']['alias']}}
                        >
                        <label for="alias">Alias (English) <span class="important_icon">*</span></label>
                    </div>
                </div>
                <div class="input-field col m4 s12" id="subcategory-select-container">
                    <select id="subcategory-select">
                        @if (!empty($response) && !empty($response['categories']))
                            @foreach ($response['categories'] as $category)
                                <optgroup label="{{ $category['category']['alias'] }}">
                                    @foreach ($category['subcategory'] as $subcategory)
                                        @if($subcategory['id'] === $response['post']['subcateg_id'])
                                            <option value="{{ $subcategory['id'] }}" selected>{{ $subcategory['alias'] }}</option>
                                        @else
                                            <option value="{{ $subcategory['id'] }}">{{ $subcategory['alias'] }}</option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endforeach
                        @endif
                    </select>
                    <label>Select Subcategory <span class="important_icon">*</span></label>
                </div>
                <div class="input-field col m4 s12" id="hashtag-select-container">
                    <select multiple id="hashtag-select">
                        <option value="" disabled selected>Select Hashtag</option>
                        @if (!empty($response) && !empty($response['hashtags']))
                            @foreach ($response['hashtags'] as $hashtag)
                                @if(in_array($hashtag['id'], $response['post']['hashtags']))
                                    <option value="{{ $hashtag['id'] }}" selected>{{ $hashtag['alias'] }}_{{ $hashtag['id'] }}</option>
                                @else
                                    <option value="{{ $hashtag['id'] }}">{{ $hashtag['alias'] }}_{{ $hashtag['id'] }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    <label>You can choose multiple hashtags <span class="important_icon">*</span></label>
                </div>
            </div>
            <div class="row">
                @if (!empty($response) && isset($response['post']) && isset($response['post']['postLocale']) && !empty($response['post']['postLocale']))
                    @foreach ($response['post']['postLocale'] as $localePost)
                        <div class="main-locale-container col s{{$response['templateDivider']}}" id="main-locale-{{$localePost['localeName']}}" data-localename="{{$localePost['localeName']}}" data-localeid="{{$localePost['localeId']}}">
                            <div class="col s12">
                                <div class="col s2">
                                    <img src="/img/flags/{{$localePost['localeName']}}.svg" alt="">
                                </div>
                            </div>
                            <div class="col s12">
                                <div class="input-field">
                                    <textarea name="main-header" class="main-header materialize-textarea input-area" data-length={{$response['colLength']['post']['header']}}>{{$localePost['header']}}</textarea>
                                    <label for="main-header">Header ({{$localePost['localeName']}}) <span class="important_icon">*</span></label>
                                </div>
                            </div>
                            <div class="col s12">
                                <div class="input-field">
                                    <textarea name="main-text" class="main-text materialize-textarea input-area" data-length={{$response['colLength']['post']['text']}}>{{$localePost['text']}}</textarea>
                                    <label for="main-text">Main Text ({{$localePost['localeName']}}) <span class="important_icon">*</span></label>
                                </div>
                            </div>
                            <div class="col s12">
                                <div class="file-field input-field">
                                    <div class="btn">
                                        <span>Main Image <span class="important_icon">*</span></span>
                                        <input type="file" class="main-image input-area" name="main-image" accept="image/*" enctype="multipart/form-data">
                                    </div>
                                    <div class="file-path-wrapper">
                                        <input value="{{$localePost['image']}}" class="file-path input-area" type="text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
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
    {{--require imageStandards --}}
    <script src="/js/admin/imageStandards.js"></script>

    <script>
        $(document).ready(function(){
            $('#subcategory-select').material_select();
            $('#hashtag-select').material_select();
            $('.modal').modal();
        });

        PostMainUpdate = {
            updatePostBtn: document.getElementById('update_post'),
            confirmMainUpdate: document.getElementById('confirm-main-update'),
            postMainImages: document.getElementsByClassName('main-image'),
            hashtagSelect: document.getElementById('hashtag-select'),
            postAlias: document.getElementById('alias'),
            postSubcategory: document.getElementById('subcategory-select'),

            _init: function () {
                var self = this;
                // main image listener
                Array.prototype.forEach.call(this.postMainImages, (function (element, index, array) {
                        element.addEventListener('change', self._imageSizeWarningLocal.bind(self));
                    })
                );
            },

            confirmMainUpdateRequest: function () {
                var updateBtns = [this.confirmMainUpdate, this.updatePostBtn];
                updateAddConfirmButtons(updateBtns, true);

                var self = this,
                    postAlias = this.postAlias.value,
                    postSubcategory = this.postSubcategory.options[this.postSubcategory.selectedIndex].value,
                    postHashtags = this._getHashtagList(),
                    xhr = new XMLHttpRequest(),
                    formData = new FormData();

                if (!postAlias || !postHashtags.length) {
                    var response = {};
                    response.type = 'Validation error';

                    if (!postAlias) {
                        response.response = ['Alias Required']
                    } else if (!postHashtags.length) {
                        response.response = ['Select at least one hashtag']
                    }

                    handleResponseToast(response, false);
                    updateAddConfirmButtons(updateBtns, false);
                    return;
                }
                // Main
                formData.append("postAlias", postAlias);
                formData.append("postSubcategory", postSubcategory);
                formData.append("postHashtag", JSON.stringify(postHashtags));

                // Main Localed image, header and text + Part localed info
                this._appendLocaledData(formData); // formData by reference

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

            _getHashtagList: function() {
                var hashtagsList = [],
                    hashtags = getClosest(this.hashtagSelect, '.select-wrapper')
                        .getElementsByClassName('multiple-select-dropdown')[0]
                        .getElementsByClassName('active'),
                    hashtagArr = hashtags ? hashtags : [];

                Array.prototype.forEach.call(hashtagArr, (function (element, index, array) {
                    var elementContent = element.getElementsByTagName('span')[0].textContent;
                    hashtagsList.push(explodeGetLast(elementContent, '_'))
                }));

                return hashtagsList ? hashtagsList : [];
            },

            _appendLocaledData: function (formData) {
                var activeLocales = document.getElementsByClassName('main-locale-container');
                Array.prototype.forEach.call(activeLocales, (function (element, index, array) {
                    // Localed Main
                    var locale = element.dataset.localename,
                        localeMainContainer = document.getElementById('main-locale-' + locale),
                        fileContainer = localeMainContainer.getElementsByClassName('main-image')[0].files,
                        file = [];
                    if (fileContainer.length) {
                        file = localeMainContainer.getElementsByClassName('main-image')[0].files[0]
                    }
                    formData.append('header[' + locale + ']', localeMainContainer.getElementsByClassName('main-header')[0].value);
                    formData.append('mainImage[' + locale + ']', file);
                    formData.append('text[' + locale + ']', localeMainContainer.getElementsByClassName('main-text')[0].value);
                }));
            },

            _imageSizeWarningLocal: function(e) {
                var el = e.target,
                    files = el.files;
                imageSizeWarning(files, imageStandards.mainImageStandard);
            },
        };
        PostMainUpdate.confirmMainUpdate.addEventListener('click', PostMainUpdate.confirmMainUpdateRequest.bind(PostMainUpdate));
        PostMainUpdate._init();
    </script>
@endsection
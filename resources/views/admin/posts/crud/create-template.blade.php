<div class="row center-align">
    <h4>Create Post</h4>
    <h6>(All fields with <span class="important_icon">*</span> are required)</h6>
</div>
<div class="row" id="post-create-main">
    <div class="row">
        <div class="col s12">
            <h4>Main</h4>
        </div>
    </div>
    <div class="row">
        <div class="col m4 s12">
            <div class="input-field">
                <input id="alias" name="alias" type="text" data-length={{$response['colLength']['post']['alias']}}>
                <label for="alias">Alias (English) <span class="important_icon">*</span></label>
            </div>
        </div>
        <div class="input-field col m4 s12" id="subcategory_select_container">
            <select id="subcategory_select">
                @if (!empty($response) && !empty($response['categories']))
                    @foreach ($response['categories'] as $category)
                        <optgroup label="{{ $category['category']['alias'] }}">
                            @foreach ($category['subcategory'] as $subcategory)
                                <option value="{{ $subcategory['id'] }}">{{ $subcategory['alias'] }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                @endif
            </select>
            <label>Select Subcategory <span class="important_icon">*</span></label>
        </div>
        <div class="input-field col m4 s12" id="hashtag_select_container">
            <select multiple id="hashtag_select">
                <option value="" disabled selected>Select Hashtag</option>
                @if (!empty($response) && !empty($response['hashtags']))
                    @foreach ($response['hashtags'] as $hashtag)
                        <option value="{{ $hashtag['id'] }}">{{ $hashtag['alias'] }}_{{ $hashtag['id'] }}</option>
                    @endforeach
                @endif
            </select>
            <label>You can choose multiple hashtags <span class="important_icon">*</span></label>
        </div>
    </div>
    <div class="row">
        @if(!empty($response) && !empty($response['activeLocales']))
            @foreach($response['activeLocales'] as $locale)
                <div class="main-locale-container col s{{$response['templateDivider']}}" id="main-locale-{{$locale['name']}}" data-localename="{{$locale['name']}}" data-localeid="{{$locale['id']}}">
                    <div class="col s12">
                        <div class="col s2">
                            <img src="/img/flags/{{$locale['name']}}.svg" alt="">
                        </div>
                        <div class="col s2">
                            <div class="locale-switch switch">
                                <label>
                                    Off
                                    <input type="checkbox" class="locale-switch-input">
                                    <span class="lever"></span>
                                    On
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="input-field">
                            <textarea name="main_header" class="main_header materialize-textarea input-area" data-length={{$response['colLength']['post']['header']}}></textarea>
                            <label for="main_header">Header ({{$locale['name']}}) <span class="important_icon">*</span></label>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="input-field">
                            <textarea name="main_text" class="main_text materialize-textarea input-area" data-length={{$response['colLength']['post']['text']}}></textarea>
                            <label for="main_text">Main Text ({{$locale['name']}}) <span class="important_icon">*</span></label>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Main Image <span class="important_icon">*</span></span>
                                <input type="file" class="main_image input-area" name="main_image" accept="image/*" enctype="multipart/form-data">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path input-area" type="text">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
<div class="row" id="post-create-parts">
    <div class="col s12">
        <h4>Parts</h4>
    </div>
    <div id="parts-container">
        @include('admin.posts.crud.create-part')
    </div>
</div>
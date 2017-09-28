<div class="row center-align">
    <h4>Create Post</h4>
    <h6>(All fields with <span class="important_icon">*</span> are required)</h6>
</div>
<div class="row" id="post-create-main">
    <div class="col s12">
        <h4>Main</h4>
    </div>
    <div class="col m7 s12">
        <div class="input-field">
            <input id="alias" name="alias" type="text" class="validate">
            <label for="alias">Alias (English) <span class="important_icon">*</span></label>
        </div>
    </div>
    <div class="col s12">
        <div class="input-field">
            <textarea id="main_header" name="main_header" class="materialize-textarea"></textarea>
            <label for="main_header">Header (Russian) <span class="important_icon">*</span></label>
        </div>
    </div>
    <div class="col s12">
        <div class="input-field">
            <textarea id="main_text" name="main_text" class="materialize-textarea"></textarea>
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
                <input class="file-path validate" type="text">
            </div>
        </div>
    </div>
    <div class="input-field col m7 s12" id="subcategory_select_container">
        <select id="subcategory_select">
            @if (!empty($response) && !empty($response['categories']))
                @foreach ($response['categories'] as $category)
                    <optgroup label="{{ $category['category']['name'] }}">
                        @foreach ($category['subcategory'] as $subcategory)
                            <option value="{{ $subcategory['id'] }}">{{ $subcategory['name'] }}</option>
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
                    <option value="{{ $hashtag['id'] }}">{{ $hashtag['hashtag'] }}_{{ $hashtag['id'] }}</option>
                @endforeach
            @endif
        </select>
        <label>You can choose multiple hashtags <span class="important_icon">*</span></label>
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

<div class="row right-align post-part-add-button-container">
    <a class="waves-effect waves-light btn" id="post-part-add-button">+Add Part</a>
</div>
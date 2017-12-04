@if(!empty($response) && !empty($response['activeLocales']))
    @foreach($response['activeLocales'] as $locale)
        <div class="part-locale-container col s{{$response['templateDivider']}}" id="part-locale-{{$locale['name']}}" data-localename="{{$locale['name']}}" data-localeid="{{$locale['id']}}">
            <div class="part-locale-inner-container">
                <div class="post-part col s12 create-part">
                    <div class="col s4">
                        <h6>Part N_<span class="post-number"></span></h6>
                    </div>
                    <div class="col s4">
                        <div class="part-delete-button">
                            <a class="btn waves-effect waves-light red modal-trigger" href="#deletePostPartModal"><i class="material-icons">delete</i></a>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="input-field">
                            <textarea class="part-header materialize-textarea" data-length={{$response['colLength']['parts']['head']}}></textarea>
                            <label for="part-header">Part Header<span class="important_icon">*</span></label>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Part Image <span class="important_icon">*</span></span>
                                <input type="file" class="part-image" name="main_image" accept="image/*">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="col s12">
                        <div class="input-field">
                            <textarea class="part-footer materialize-textarea" data-length={{$response['colLength']['parts']['foot']}}></textarea>
                            <label for="part-footer">Part Footer<span class="important_icon">*</span></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row right-align post-part-add-button-container">
                <a class="waves-effect waves-light btn post-part-add-button">+Add Part</a>
            </div>
        </div>
    @endforeach
@endif
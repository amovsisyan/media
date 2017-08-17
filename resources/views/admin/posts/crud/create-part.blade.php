<div class="post-part col s12">
    <div class="col m3 s6">
        <h6>Part N_<span class="post-number"></span></h6>
    </div>
    <div class="col m3 s6">
        <div class="part-delete-button">
            <a class="btn-floating waves-effect waves-light red modal-trigger"><i class="material-icons">delete</i></a>
            <!-- Modal -->
            <div class="modal">
                <div class="modal-content left-align">
                    <h4>Are You Sure You Want Delete This Part?</h4>
                    <p></p>
                </div>
                <div class="modal-footer">
                    <a class="modal-action modal-close waves-effect waves-green btn-flat confirm-delete">Delete</a>
                    <a class="modal-action modal-close waves-effect waves-green btn-flat">Cancel</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col m7 s12">
        <div class="input-field">
            <input type="text" class="part_header validate">
            <label for="part_header">Part Header (Russian) <span class="important_icon">*</span></label>
        </div>
    </div>
    <div class="col s12">
        <div class="file-field input-field">
            <div class="btn">
                <span>Part Image <span class="important_icon">*</span></span>
                <input type="file" class="part_image" name="main_image" accept="image/*">
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text">
            </div>
        </div>
    </div>
    <div class="col m7 s12">
        <div class="input-field">
            <input type="text" class="part_footer validate">
            <label for="part_footer">Part Footer (Russian) <span class="important_icon">*</span></label>
        </div>
    </div>
</div>
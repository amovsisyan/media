<div class="row hide" id="part-template">
    <ul class="collapsible" id="collapsible-container" data-collapsible="accordion">
        <li class="collapse-subcategory-part">
            <div class="collapsible-header">
                <div class="col s1">
                    <span class="part-number"></span>
                </div>
                <div class="col s11">
                    <span class="part-alias"></span>
                </div>
            </div>
            <div class="collapsible-body">
                <div class="row">
                    <div class="input-field col s6">
                        <input type="text" class="part-alias validate">
                        <label for="part-alias">Alias</label>
                    </div>
                    <div class="input-field col s6">
                        <select class="categories-select">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="locale-part-container">
                    <div class="row locale-part">
                        <div class="input-field col s1">
                            <img src="" alt="">
                        </div>
                        <div class="input-field col s11">
                            <input type="text" class="part-locale-name validate">
                            <label for="part-locale-name"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <a class="btn-floating waves-effect waves-light modal-trigger part-confirm-button"
                       href="#changesConfirmModal"><i class="material-icons">check</i></a>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="row hide part-no-result">
    <div class="col s12">
        <h5>There Was No Result</h5>
    </div>
</div>

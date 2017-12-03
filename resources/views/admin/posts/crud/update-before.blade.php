<div class="row hide part-template">
    <ul class="collapsible popout collapsible-container" data-collapsible="accordion">
        <li class="collapse-post-part">
            <div class="collapsible-header">
                <span class="part-number"></span>
                <span class="part-alias"></span>
            </div>
            <div class="collapsible-body">
                <div class="post-id col s12"></div>
                <div class="post-alias-text col s10"></div>
                {{--<div class="part-text col m9 s12"></div>--}}
                <div class="fixed-action-btn horizontal click-to-toggle col s2 right-align">
                    <a class="btn-floating red">
                        <i class="material-icons">menu</i>
                    </a>
                    <ul>
                        <li><a class="btn-floating red modal-trigger post-delete-btn" href="#deletePostModal">Del</a></li>
                        <li><a target="_blank" class="btn-floating yellow darken-1 post-parts-btn">Parts</a></li>
                        <li><a target="_blank" class="btn-floating green post-main-btn">Main</a></li>
                    </ul>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="row hide" id="part-no-result">
    <div class="col s12">
        <h5>There Was No Result</h5>
    </div>
</div>
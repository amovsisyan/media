<div class="row hide part-template">
    <div class="col m1 s12">
        <h5 class="part-number"></h5>
    </div>
    <div class="col m1 s12">
        <h5 class="post-id"></h5>
    </div>
    <div class="input-field col m4 s12">
        <input disabled value="" type="text" class="part-header validate">
    </div>
    <div class="input-field col m5 s12">
        <input disabled value="" type="text" class="part-text validate">
    </div>
    <div class="col m1 s12">
        <a class="btn-floating waves-effect waves-light modal-trigger part-details-button"><i class="material-icons">details</i></a>
    </div>
</div>

<div class="row hide part-no-result">
    <div class="col s12">
        <h5>There Was No Result</h5>
    </div>
</div>

<div class="row hide part-detail-template">
    @include('admin.posts.crud.create-update-template')
</div>
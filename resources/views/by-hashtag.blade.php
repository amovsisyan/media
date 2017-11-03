@extends('layouts.app')

@section('content')
    <section id="hashtag-posts">
        <div class="container">
            @if(!empty($response['hashtag']))
                <div class="row">
                    <div class="col s12">
                        <div class="hashtag-header">
                            <h5>Hashtag: </h5>
                            <div class="chip">#{{$response['hashtag']}}</div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                @if(!empty($response) && !empty($response['posts']))
                    @foreach ($response['posts'] as $post)
                        <div class="col s4">
                            <a href="{{url('/' . Request::segment(1) . '/' . $post['cat_alias'] . '/' . $post['sub_alias'] . '/' . $post['alias'])}}">
                                <div class="category-post">
                                    <div class="category-post-img">
                                        <img src="/img/cat/{{$post['sub_alias']}}/{{$post['alias']}}/{{$post['image']}}" alt="">
                                    </div>
                                    <h5>{{$post['header']}}</h5>
                                    <h6>{{$post['text']}}</h6>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endsection

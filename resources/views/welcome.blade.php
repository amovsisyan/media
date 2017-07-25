@extends('layouts.app')

@section('content')
        <section id="welcome-posts">
            <div class="container">
                <div class="row">
                    @foreach ($response['posts'] as $post)
                        <div class="col s4">
                            <a href="{{url($post['cat_alias'] . '/' . $post['sub_alias'] . '_' . $post['sub_id'] . '/' . $post['alias'] . '_' . $post['id'])}}">
                                <div class="category-post">
                                    <div class="category-post-img">
                                        <img src="/img/cat/{{$post['sub_alias']}}_{{$post['sub_id']}}/{{$post['alias']}}/{{$post['image']}}" alt="">
                                    </div>
                                    <h5>{{$post['header']}}</h5>
                                    <h6>{{$post['text']}}</h6>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
@endsection

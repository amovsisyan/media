@extends('layouts.app')

@section('content')
    <section id="posts">
        <div class="container">
            <div class="row">
                @foreach ($response['posts'] as $post)
                    <div class="col s4">
                        <a href="{{url(Request::url() . '/' . $post['alias'] . '_' . $post['id'])}}">
                            <div class="category-post">
                                <div class="category-post-img">
                                    <img src="/img/cat/{{Request::segment(2)}}/{{$post['alias']}}_{{$post['id']}}/{{$post['image']}}" alt="">
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

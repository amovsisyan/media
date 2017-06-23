@extends('layouts.app')

@section('content')
    <section id="posts">
        <div class="container">
            <div class="row">
                @foreach ($response['posts'] as $post)
                    <div class="col s4">
                        <div class="category-post">
                            <div class="category-post-img">
                                <img src="/img/cat/{{Request::segment(1)}}/{{Request::segment(2)}}/{{$post['alias']}}/{{$post['image']}}" alt="">
                            </div>
                            <h5>{{$post['header']}}</h5>
                            <h6>{{$post['text']}}</h6>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('content')
    <?php
    $locale = \App::getLocale()
    ?>

        <section id="welcome-posts">
            <div class="container">
                <div class="row">
                    @if(!empty($response) && !empty($response['posts']))
                        @foreach ($response['posts'] as $post)
                            <div class="col s4">
                                <a href="{{url('/' . $locale . '/' . $post['cat_alias'] . '/' . $post['sub_alias'] . '/' . $post['alias'])}}">
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

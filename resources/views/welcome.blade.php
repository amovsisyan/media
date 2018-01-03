<?php
$locale = \App::getLocale();
?>

@extends('layouts.app')

@section('content')
    <section id="welcome-posts">
        <div class="container set-dimesions">
            <div class="row">
                <div class="col s10">
                    @if(!empty($response) && !empty($response['posts']))
                        @foreach ($response['posts'] as $post)
                            <div class="col s4">
                                <a href="{{url('/' . $locale . '/' . $post['cat_alias'] . '/' . $post['sub_alias'] . '/' . $post['alias'])}}">
                                    <div class="category-post">
                                        <div class="category-post-img">
                                            <img src="/img/cat/{{$post['sub_alias']}}/{{$post['alias']}}/{{$locale}}/{{$post['image']}}" alt="">
                                        </div>
                                        <h5>{{$post['header']}}</h5>
                                        <h6>{{$post['text']}}</h6>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col s2">
                    @include('templates.google-add')
                </div>
            </div>
        </div>
    </section>
    @include('pagination.simple-template')
@endsection

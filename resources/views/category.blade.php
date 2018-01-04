<?php
$locale = \App::getLocale();
$categDir = Request::segment(2);
$subcategDir = Request::segment(3);
?>

@extends('layouts.app')

@section('content')
    <section id="posts">
        <div class="container set-dimesions">
            <div class="row">
                <div class="col s10">
                @foreach ($response['posts'] as $post)
                    <div class="col s4">
                        <a href="{{url(Request::url() . '/' . $post['alias'])}}">
                            <div class="category-post">
                                <div class="category-post-img">
                                    <img
                                            src="/img/cat/{{$subcategDir}}/{{$post['alias']}}/{{$locale}}/{{$post['image']}}"
                                            alt="{{$categDir . '-' . $subcategDir . '-' . $post['image']}}">
                                </div>
                                <h5>{{$post['header']}}</h5>
                                <h6>{{$post['text']}}</h6>
                            </div>
                        </a>
                    </div>
                @endforeach
                </div>
                <div class="col s2">
                    @include('templates.google-add')
                </div>
            </div>
        </div>
    </section>
    @include('pagination.simple-template')
@endsection

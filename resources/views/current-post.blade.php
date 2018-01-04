<?php
$locale = \App::getLocale();
$categDir = Request::segment(2);
$subcategDir = Request::segment(3);
$postDir = Request::segment(4);
?>

@extends('layouts.app')

@section('content')
    <section id="current-post">
        <div class="container set-dimesions">
            <div class="row">
                <div class="col s10">
                    <div class="col s12 current-post-header left-align">
                        <h4>
                            {{$response['post_header']}}
                        </h4>
                    </div>

                    {{--<-- SHARE BUTTONS -->--}}
                    <div class="addthis_inline_share_toolbox_3soo"></div>

                    @foreach ($response['post_parts'] as $post_part)
                    <div class="col s12 left-align">
                        <div class="post-part">
                            <h5 class="head">
                                {{$post_part['head']}}
                            </h5>
                            @if(!empty($post_part['body']))
                                <div class="body">
                                    <img
                                            src="/img/cat/{{$subcategDir}}/{{$postDir}}/{{$locale}}/parts/{{$post_part['body']}}"
                                            alt="{{$categDir . '-' . $subcategDir . '-' . $postDir . '-image'}}">
                                </div>
                            @endif
                            <h6 class="foot">
                                {{$post_part['foot']}}
                            </h6>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="col s2">
                    @include('templates.google-add')
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="hashtags">
                        @foreach ($response['hashtags'] as $hashtag)
                            <a href="{{url('/' . Request::segment(1) . '/hashtag/' . $hashtag['alias'])}}">
                                <div class="chip">
                                    #{{$hashtag['hashtag']}}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

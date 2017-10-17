@extends('layouts.app')

@section('content')
    <section id="current-post">
        <div class="container">
            <div class="row">
                <div class="col s10">
                    <div class="col s12 current-post-header left-align">
                        <h4>
                            {{$response['post_header']}}
                        </h4>
                    </div>
                    @foreach ($response['post_parts'] as $post_part)
                    <div class="col s12 left-align">
                        <div class="post-part">
                            <h5 class="head">
                                {{$post_part['head']}}
                            </h5>
                            @if(!is_null($post_part['body']))
                                <div class="body">
                                    <img src="/img/cat/{{Request::segment(2)}}/{{Request::segment(3)}}/parts/{{$post_part['body']}}" alt="">
                                </div>
                            @endif
                            <h6 class="foot">
                                {{$post_part['foot']}}
                            </h6>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <div class="hashtags">
                        @foreach ($response['hashtags'] as $hashtag)
                            <a href="{{url('/hashtag/' . $hashtag['alias'])}}">
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

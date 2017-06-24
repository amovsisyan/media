@extends('layouts.app')

@section('content')
    <section id="current-post">
        <div class="container">
            <div class="row">
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
                            <div class="body">
                                {{$post_part['body']}}
                            </div>
                            <h6 class="foot">
                                {{$post_part['foot']}}
                            </h6>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

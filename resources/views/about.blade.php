<?php
$locale = \App::getLocale();
?>

@extends('layouts.app')

@section('content')
    <section id="about-info">
        <div class="container">
            <div class="row">
                <div class="col s12 no-margin">
                    <div class="col s3">
                        <p>Project by: </p>
                    </div>
                    <div class="col s3">
                        <a target="_blank" href="https://www.linkedin.com/in/arthur-movsisyan/">NoCoffee Solution</a>
                    </div>
                </div>
                <div class="col s12 no-margin">
                    <div class="col s3">
                        <p>Project Content By: </p>
                    </div>
                    <div class="col s3">
                        <a target="_blank" href="#">N. Kirakosyan</a>
                    </div>
                </div>
                <div class="col s12 no-margin">
                    <div class="col s3">
                        <p>Project Promotion By: </p>
                    </div>
                    <div class="col s3">
                        <a target="_blank" href="#">N. Kirakosyan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

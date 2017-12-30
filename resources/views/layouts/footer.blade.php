<footer class="page-footer">
    @if (!empty($response))
        <div class="container footer-navbar">
            <div class="row">
                @foreach ($response['navbar'] as $navbar)
                    @if(!empty($navbar['subcategory']))
                        <ul class="col s4">
                            <li>{{$navbar['category']['name']}}</li>
                            @foreach ($navbar['subcategory'] as $subcat)
                                <li><a href="{{ url('/' . Request::segment(1) . '/' . $navbar['category']['alias'] . '/' . $subcat['alias']) }}">{{$subcat['name']}}</a></li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
    <div class="footer-copyright">
        <div class="container">
            <div class="row no-margin">
                <a target="_blank" class="grey-text text-lighten-4 col s4" href="https://www.linkedin.com/in/arthur-movsisyan/">
                    <h6>© 201(7-8) Copyright NoCoffee Solutions</h6>
                </a>
                <a target="_blank" class="grey-text text-lighten-4 col s4 center-align" href="{{url(Request::url() . '/about')}}">
                    <h6>About us (Contact us)</h6>
                </a>
                <a target="_blank" class="grey-text text-lighten-4 col s4 right-align" href="https://www.linkedin.com/in/arthur-movsisyan/">
                    <h6>Page Owner A. Movsisyan</h6>
                </a>
            </div>
        </div>
    </div>
</footer>
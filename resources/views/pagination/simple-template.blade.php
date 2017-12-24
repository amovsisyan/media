@if(!empty($response) && !empty($response['pagination']))
    <section id="pagination-section">
        <ul class="pagination center-align">
            <?php
            $disabledFirst = $response['pagination']['currentPage'] === 1 ? 'disabled' : 'waves-effect';
            $disabledLast = $response['pagination']['currentPage'] === $response['pagination']['lastPage']  ? 'disabled' : 'waves-effect';
            ?>
            <li class="{{$disabledFirst}}"><a href="{{Request::url()}}?page=1"><i class="material-icons">chevron_left</i></a></li>

            @for ($i = 1; $i <= $response['pagination']['lastPage']; $i++)
                @if($i === $response['pagination']['currentPage'] && $className = 'active disabled')
                @elseif($className = 'waves-effect')
                @endif

                <li class="{{$className}}"><a href="{{Request::url()}}?page={{$i}}">{{$i}}</a></li>
            @endfor

            <li class="{{$disabledLast}}"><a href="{{Request::url()}}?page={{$response['pagination']['lastPage']}}"><i class="material-icons">chevron_right</i></a></li>
        </ul>
    </section>

    <script>
        Pegination = {
            paginationSection: document.getElementById('pagination-section'),
            paginationHrefs: document.getElementById('pagination-section').getElementsByTagName('li'),

            _init: function () {
                this.addListeners();
            },

            addListeners:function () {
                var self = this;
                _.forEach(self.paginationHrefs, (function (href, index, array) {
                    href.addEventListener('click',
                        self.checkEnable.bind(self)
                    );
                }));
            },

            checkEnable: function (e) {
                var li = getClosest(e.target, 'li');
                if (hasClass(li, 'disabled')) {
                    e.preventDefault();
                }
            }
        };
        Pegination._init();
    </script>
@endif
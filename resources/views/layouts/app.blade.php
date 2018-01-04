<?php
$locale = \App::getLocale();
?>

<!DOCTYPE html>
<html lang={{$locale}}>
<head>
	@include('layouts.head')
</head>
<body>
<header>
	@include('layouts.header')
</header>

	@yield('content')
	@include('layouts.footer')

{{--<-- SHARE BUTTONS SCRIPT -->--}}
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5a4ca2d937e8dd97"></script>

</body>

<script src="/js/my.js"></script>
<script>
	Main = {
        localeBtns: document.getElementsByClassName('locale-btn'),
		
	    init: function () {
			this.addListeners();
        },
		
		addListeners: function () {
            var self = this;
            _.forEach(this.localeBtns, function(localeBtn) {
                localeBtn.addEventListener('click',
                    self.changeLocale.bind(self)
                );
            });
        },

        changeLocale: function (e) {
            var arr = window.location.pathname.split("/");
            arr[1] = e.target.dataset.locale;
            var newLocation = _.join(arr, '/');
            window.location.replace(newLocation);
        }
	};

	Main.init()
</script>
</html>

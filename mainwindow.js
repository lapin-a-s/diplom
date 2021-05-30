
    $(document).ready(function () {
    $("#sidebar").mCustomScrollbar({
        theme: "minimal"
    });

    $('#dismiss, .overlay').on('click', function () {
    $('#sidebar').removeClass('active');
    $('.overlay').removeClass('active');
});

    $('#sidebarCollapse').on('click', function () {
    $('#sidebar').addClass('active');
    $('.overlay').addClass('active');
    $('.collapse.in').toggleClass('in');
    $('a[aria-expanded=true]').attr('aria-expanded', 'false');
});
});

        function coding() {
        var obj = document.getElementById('url');
        var url = obj.value;
        obj.value = encodeURIComponent(url);
    }
        function decoding() {
        var obj = document.getElementById('url');
        var url = obj.value;
        obj.value = decodeURIComponent(url.replace(/\+/g,  " "));
    }

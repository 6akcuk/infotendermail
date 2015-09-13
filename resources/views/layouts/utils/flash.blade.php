<script>
    @if (Session::has('flash_notification.message'))
        @if (Session::has('flash_notification.overlay'))
            @include('flash::modal', ['modalClass' => 'flash-modal', 'title' => Session::get('flash_notification.title'), 'body' => Session::get('flash_notification.message')])
        @else
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right"
            };
            toastr['{{ Session::get('flash_notification.level') }}']('{{ Session::get('flash_notification.message') }}');
        @endif
    @endif
</script>
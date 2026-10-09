@foreach (['success' => 'alert-success', 'error' => 'alert-error'] as $key => $class)
    @if (session($key))
        <div class="alert {{ $class }} no-print" role="alert">
            <span>{{ session($key) }}</span>
            <button type="button" class="alert-close" data-dismiss="alert" aria-label="Đóng">&times;</button>
        </div>
    @endif
@endforeach

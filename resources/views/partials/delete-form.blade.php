<form method="POST" action="{{ $action }}" class="inline-form" data-confirm="{{ $confirm }}">
    @csrf
    @method('DELETE')
    <button type="submit" @class(['btn', 'btn-danger', 'btn-sm' => $small ?? true])>{{ $label ?? 'Xóa' }}</button>
</form>

@if ($product->imageUrl())
    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="thumb" loading="lazy">
@else
    <span class="thumb thumb-empty" aria-hidden="true">{{ mb_substr($product->name, 0, 1) }}</span>
@endif

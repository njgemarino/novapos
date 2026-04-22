{{-- resources/views/vendor/pagination/simple.blade.php --}}
@if ($paginator->hasPages())
<div style="display:flex;align-items:center;justify-content:space-between;font-size:.78rem">
    <span style="color:var(--text-muted)">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </span>
    <div style="display:flex;gap:4px">
        @if ($paginator->onFirstPage())
            <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--text-muted);background:var(--surface)">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text-muted);background:var(--surface);text-decoration:none;transition:all .1s">← Prev</a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text-muted);background:var(--surface);text-decoration:none;transition:all .1s">Next →</a>
        @else
            <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--text-muted);background:var(--surface)">Next →</span>
        @endif
    </div>
</div>
@endif

@if ($paginator->hasPages())
    <nav class="msc-pagination-container" role="navigation" aria-label="Navigasi Halaman">
        <div class="msc-pagination-info">
            Menampilkan <span class="msc-page-highlight">{{ $paginator->firstItem() }}</span>&ndash;<span class="msc-page-highlight">{{ $paginator->lastItem() }}</span> dari <span class="msc-page-highlight">{{ $paginator->total() }}</span> data
        </div>

        <ul class="msc-pagination-list">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="msc-page-item disabled" aria-disabled="true" title="Halaman sebelumnya">
                    <span class="msc-page-link nav-btn" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="desktop-text">Prev</span>
                    </span>
                </li>
            @else
                <li class="msc-page-item">
                    <a class="msc-page-link nav-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Halaman sebelumnya">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="desktop-text">Prev</span>
                    </a>
                </li>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik-titik --}}
                @if (is_string($element))
                    <li class="msc-page-item disabled dots" aria-disabled="true">
                        <span class="msc-page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="msc-page-item active" aria-current="page">
                                <span class="msc-page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="msc-page-item">
                                <a class="msc-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <li class="msc-page-item">
                    <a class="msc-page-link nav-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Halaman selanjutnya">
                        <span class="desktop-text">Next</span>
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </li>
            @else
                <li class="msc-page-item disabled" aria-disabled="true" title="Halaman selanjutnya">
                    <span class="msc-page-link nav-btn" aria-hidden="true">
                        <span class="desktop-text">Next</span>
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

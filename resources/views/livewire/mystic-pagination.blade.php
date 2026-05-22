@if ($paginator->hasPages())
    <div class="mystic-pagination-wrapper">
        <nav role="navigation" aria-label="Pagination Navigation" class="mystic-pagination-nav">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="mystic-pagination-btn disabled" aria-disabled="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span>Anterior</span>
                </span>
            @else
                <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" class="mystic-pagination-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span>Anterior</span>
                </button>
            @endif

            {{-- Page Numbers --}}
            <div class="mystic-pagination-pages">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="mystic-pagination-dots" aria-disabled="true">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="mystic-pagination-page active" aria-current="page">{{ $page }}</span>
                            @else
                                <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="mystic-pagination-page">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" class="mystic-pagination-btn">
                    <span>Próximo</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            @else
                <span class="mystic-pagination-btn disabled" aria-disabled="true">
                    <span>Próximo</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </span>
            @endif
        </nav>

        {{-- Meta details --}}
        <div class="mystic-pagination-meta">
            Exibindo {{ $paginator->firstItem() ?? 0 }} a {{ $paginator->lastItem() ?? 0 }} de {{ $paginator->total() }} resultados
        </div>
    </div>

    <style>
        .mystic-pagination-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.8rem;
            margin-top: 2.5rem;
            width: 100%;
            position: relative;
            z-index: 10;
        }
        .mystic-pagination-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }
        .mystic-pagination-btn, .mystic-pagination-page {
            background: rgba(252, 248, 237, 0.45) !important;
            border: 1px solid rgba(44, 24, 16, 0.18) !important;
            border-radius: 0.6rem;
            color: #2c1810 !important;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-shadow: 1px 1px 0px rgba(255,255,255,0.5);
            box-sizing: border-box;
            outline: none;
        }
        .mystic-pagination-btn {
            padding: 0.5rem 1rem !important;
            gap: 0.4rem;
            min-height: 2.3rem;
        }
        .mystic-pagination-page {
            width: 2.3rem;
            height: 2.3rem;
            padding: 0 !important;
        }
        .mystic-pagination-btn svg {
            width: 1rem;
            height: 1rem;
            stroke: currentColor;
            display: inline-block;
        }
        .mystic-pagination-pages {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .mystic-pagination-dots {
            font-family: 'Cinzel', serif;
            color: #2c1810;
            opacity: 0.5;
            padding: 0 0.2rem;
            font-size: 1.1rem;
        }
        
        /* Hover and Focus */
        .mystic-pagination-btn:not(.disabled):hover, .mystic-pagination-page:not(.active):hover {
            background: rgba(252, 248, 237, 0.9) !important;
            border-color: #d4af37 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.15);
            color: #741b1b !important;
        }
        .mystic-pagination-btn:not(.disabled):active, .mystic-pagination-page:not(.active):active {
            transform: translateY(0);
        }
        
        /* Active State */
        .mystic-pagination-page.active {
            background: #d4af37 !important;
            border-color: #d4af37 !important;
            color: #2c1810 !important;
            font-weight: 900;
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.35);
            text-shadow: none;
            cursor: default;
        }
        
        /* Disabled State */
        .mystic-pagination-btn.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background: rgba(252, 248, 237, 0.2) !important;
            box-shadow: none;
        }
        
        /* Meta text */
        .mystic-pagination-meta {
            font-family: 'Cinzel', serif;
            font-size: 0.72rem;
            color: #2c1810;
            opacity: 0.6;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        /* Dark Theme overrides */
        [data-theme="dark"] .mystic-pagination-btn, 
        [data-theme="dark"] .mystic-pagination-page {
            background: rgba(26, 22, 34, 0.5) !important;
            border-color: rgba(74, 62, 46, 0.4) !important;
            color: #e2dcd0 !important;
            text-shadow: none;
        }
        [data-theme="dark"] .mystic-pagination-btn:not(.disabled):hover, 
        [data-theme="dark"] .mystic-pagination-page:not(.active):hover {
            background: rgba(26, 22, 34, 0.8) !important;
            border-color: #d4af37 !important;
            color: #d4af37 !important;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.1);
        }
        [data-theme="dark"] .mystic-pagination-page.active {
            background: #d4af37 !important;
            border-color: #d4af37 !important;
            color: #0c0a0f !important;
        }
        [data-theme="dark"] .mystic-pagination-dots {
            color: #e2dcd0;
        }
        [data-theme="dark"] .mystic-pagination-meta {
            color: #e2dcd0;
        }
    </style>
@endif

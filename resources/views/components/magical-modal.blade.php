@props([
    'show' => false,
    'title' => '✨ Selar Alteração?',
    'description' => 'Deseja confirmar as mudanças?',
    'confirmAction' => 'save',
    'cancelAction' => 'cancelEditing',
    'type' => 'primary'
])

<div x-data="{ show: @entangle($show) }" x-show="show" x-cloak class="modal-wrapper">
    <div class="modal-backdrop" @click="$wire.call('{{ $cancelAction }}')">
        <div class="modal-content {{ $type == 'danger' ? 'danger-modal' : 'primary-modal' }}" @click.stop>
            <div class="modal-header-magic {{ $type == 'danger' ? 'danger' : '' }}">
                <h2>{{ $title }}</h2>
            </div>
            <div class="modal-body text-center">
                <p>{!! $description !!}</p>
                
                <div class="modal-magic-actions">
                    <button wire:click="{{ $confirmAction }}" class="btn-magic-{{ $type == 'danger' ? 'danger' : 'primary' }}">
                        @if($type == 'danger') Banir Roda @else Confirmar @endif
                    </button>
                    <button wire:click="{{ $cancelAction }}" class="btn-magic-secondary">
                        @if($type == 'danger') Cancelar @else Descartar @endif
                    </button>
                </div>
            </div>
        </div>
    </div>

    @once
    <style>
        [x-cloak] { display: none !important; }
        .modal-wrapper { position: fixed; inset: 0; z-index: 9999; }
        .modal-backdrop { 
            position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); 
            backdrop-filter: blur(10px); display: flex; align-items: center; 
            justify-content: center; padding: 1rem;
        }
        .modal-content { 
            background: #fdfaf3; /* var(--card-bg) */
            border: 2px solid #d4af37; /* var(--gold-color) */
            border-radius: 1.5rem; padding: 2.5rem; width: 100%; 
            max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            position: relative; animation: modalAppear 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes modalAppear { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .modal-header-magic { text-align: center; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .modal-header-magic h2 { font-family: 'Cinzel', serif; font-size: 1.6rem; color: #d4af37; margin: 0; text-shadow: 0 0 10px rgba(212,175,55,0.2); }
        .modal-header-magic.danger h2 { color: #741b1b; text-shadow: 0 0 10px rgba(116,27,27,0.2); }
        
        .modal-body p { font-family: 'Spectral', serif; font-size: 1.1rem; color: #4a4a4a; line-height: 1.5; margin: 0; }
        
        .modal-magic-actions { display: flex; gap: 1.2rem; margin-top: 2.5rem; justify-content: center; }
        .btn-magic-primary, .btn-magic-danger, .btn-magic-secondary { 
            padding: 0.8rem 2.2rem; font-family: 'Cinzel', serif; font-weight: 700; 
            cursor: pointer; border-radius: 0.6rem; transition: all 0.2s; font-size: 0.9rem;
        }
        .btn-magic-primary { background: #741b1b; color: #fff; border: 1px solid #d4af37; }
        .btn-magic-danger { background: #741b1b; color: #fff; border: 1px solid #d4af37; }
        .btn-magic-secondary { background: transparent; color: #4a4a4a; border: 1px solid rgba(0,0,0,0.1); }
        
        .btn-magic-primary:hover, .btn-magic-danger:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(116,27,27,0.3); }
        .btn-magic-secondary:hover { background: rgba(0,0,0,0.03); }
    </style>
    @endonce
</div>

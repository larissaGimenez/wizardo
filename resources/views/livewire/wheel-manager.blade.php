<div class="wheel-manager-container">
    <!-- Header com botão -->
    <div class="page-header">
        <h1>Gerenciador de Rodas</h1>
        <button wire:click="openModal" class="btn btn-primary">
            <span class="icon">➕</span> Nova Roda
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <!-- Grid de Cards -->
    <div class="wheels-grid">
        @forelse ($wheels as $wheel)
            <a href="{{ route('wheel.details', $wheel->id) }}" class="wheel-card-link">
                <div class="wheel-card {{ $selectedWheelId == $wheel->id ? 'selected' : '' }}">
                    <!-- Imagem da Roda -->
                    <div class="wheel-image-container">
                        @if($wheel->image)
                            <img src="{{ asset('storage/' . $wheel->image->path) }}" alt="{{ $wheel->name }}" class="wheel-image">
                        @else
                            <div class="wheel-image-placeholder">
                                <span class="icon">🎡</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-content">
                        <h3 class="wheel-name">{{ $wheel->name }}</h3>
                        <p class="wheel-desc">{{ Illuminate\Support\Str::limit($wheel->description, 60) }}</p>
                        <div class="wheel-meta">
                            <span class="spell-count">✨ {{ $wheel->spells->count() }} Feitiços</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Nenhuma roda cadastrada ainda.</p>
                <button wire:click="openModal" class="btn btn-secondary">Cadastrar Primeira Roda</button>
            </div>
        @endforelse
    </div>

    <!-- Modal de Cadastro/Edição -->
    @if($showModal)
        <div class="modal-backdrop" wire:click="closeModal">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2>{{ $isEditing ? 'Editar Roda' : 'Cadastrar Nova Roda' }}</h2>
                    <button wire:click="closeModal" class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="form-group">
                            <label for="name">Nome da Roda</label>
                            <input type="text" id="name" wire:model="name" placeholder="Ex: Roda de Fogo">
                            @error('name') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <textarea id="description" wire:model="description" placeholder="Descrição da roda..."></textarea>
                            @error('description') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <!-- Upload de Imagem -->
                        <div class="form-group">
                            <label for="wheel_image">Imagem da Roda</label>
                            <input type="file" id="wheel_image" wire:model="image" accept="image/*">
                            <div wire:loading wire:target="image" class="loading-text">Carregando imagem...</div>
                            @error('image') <span class="error">{{ $message }}</span> @enderror
                            
                            <!-- Preview -->
                            @if ($image)
                                <div class="image-preview mt-2">
                                    <img src="{{ $image->temporaryUrl() }}" style="max-width: 100px; border-radius: 0.5rem;">
                                </div>
                            @endif
                        </div>

                        <!-- Associação de Feitiços -->
                        <div class="form-group">
                            <label>Associar Feitiços (Puxar para esta roda)</label>
                            <div class="checkbox-list">
                                @forelse($all_spells as $spell)
                                    <label class="checkbox-item">
                                        <input type="checkbox" wire:model="selected_spells" value="{{ $spell->id }}">
                                        <span class="checkbox-label">
                                            {{ $spell->name }} 
                                            <small>({{ $spell->wheel->name ?? 'Sem roda' }})</small>
                                        </span>
                                    </label>
                                @empty
                                    <p class="empty-text">Nenhum feitiço cadastrado no sistema.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                {{ $isEditing ? 'Atualizar' : 'Cadastrar' }}
                            </button>
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .wheel-manager-container {
            width: 100%;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        /* Grid */
        .wheels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        /* Cards */
        .wheel-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            cursor: pointer;
        }

        .wheel-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .wheel-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .wheel-card.selected {
            border-color: #38bdf8;
            background-color: rgba(56, 189, 248, 0.02);
            box-shadow: 0 0 0 2px #38bdf8;
        }

        /* Imagem */
        .wheel-image-container {
            height: 160px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .wheel-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .wheel-image-placeholder {
            font-size: 3rem;
            color: #cbd5e1;
        }

        .card-content {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .wheel-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 0.5rem 0;
        }

        .wheel-desc {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0 0 1rem 0;
            line-height: 1.4;
        }

        .wheel-meta {
            display: flex;
            justify-content: flex-start;
        }

        .spell-count {
            font-size: 0.75rem;
            font-weight: 600;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .card-actions {
            display: flex;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .btn-action {
            flex: 1;
            padding: 0.75rem;
            border: none;
            background: none;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-action:first-child {
            border-right: 1px solid #e2e8f0;
        }

        .btn-action:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .btn-action.delete:hover {
            background-color: #fee2e2;
            color: #ef4444;
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.2s ease-out;
            padding: 1rem;
            box-sizing: border-box;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 500px;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .modal-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .close-btn:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .modal-body {
            padding: 1.5rem;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
            outline: none;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        /* Checkbox List */
        .checkbox-list {
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            padding: 0.5rem;
            background: #f8fafc;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.5rem;
            cursor: pointer;
            border-radius: 0.25rem;
            transition: background-color 0.1s;
        }

        .checkbox-item:hover {
            background-color: #e2e8f0;
        }

        .checkbox-label {
            font-size: 0.875rem;
            color: #1e293b;
        }

        .checkbox-label small {
            color: #64748b;
        }

        .error {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
        }

        .loading-text {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        /* Buttons */
        .btn {
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: #38bdf8;
            color: #0f172a;
        }

        .btn-primary:hover {
            background-color: #0ea5e9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #475569;
        }

        .btn-secondary:hover {
            background-color: #cbd5e1;
        }

        /* Helpers & Animations */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>
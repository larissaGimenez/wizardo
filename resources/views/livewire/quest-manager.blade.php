<div class="quest-manager-container">
    <!-- Header com botão e filtro -->
    <div class="page-header">
        <h1>Gerenciador de Missões</h1>
        
        <div class="header-actions">
            <!-- Filtro por Roda -->
            <div class="filter-group">
                <label for="filter_wheel">Filtrar por Roda:</label>
                <select id="filter_wheel" wire:model.live="filter_wheel_id">
                    <option value="">Todas as Rodas</option>
                    @foreach($wheels as $wheel)
                        <option value="{{ $wheel->id }}">{{ $wheel->name }}</option>
                    @endforeach
                </select>
            </div>

            <button wire:click="openModal" class="btn btn-primary">
                <span class="icon">➕</span> Nova Missão
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <!-- Grid de Cards de Missões -->
    <div class="spells-grid"> <!-- Reutilizando a classe do grid para manter o padrão -->
        @forelse ($quests as $quest)
            <div class="spell-card"> <!-- Reutilizando a classe do card para manter o padrão -->
                <!-- Imagem da Missão -->
                <div class="spell-image-container">
                    @if($quest->image)
                        <img src="{{ asset('storage/' . $quest->image->path) }}" alt="{{ $quest->name }}" class="spell-image">
                    @else
                        <div class="spell-image-placeholder">
                            <span class="icon">📜</span>
                        </div>
                    @endif
                </div>

                <div class="spell-header">
                    <h4 class="spell-name">{{ $quest->name }}</h4>
                    <div class="spell-badges">
                        <span class="badge badge-gain">⬆️ Ganho: {{ $quest->gain }}</span>
                    </div>
                </div>
                <div class="spell-body">
                    <p class="spell-action">{{ Illuminate\Support\Str::limit($quest->description, 80) ?? 'Sem descrição definida.' }}</p>
                    <div class="spell-meta">
                        <span class="wheel-tag">🎡 {{ $quest->wheel?->name ?? 'Sem Roda' }}</span>
                    </div>
                </div>
                <div class="spell-actions">
                    <button wire:click="edit({{ $quest->id }})" class="btn-action" title="Editar">✏️ Editar</button>
                    <button wire:click="delete({{ $quest->id }})" wire:confirm="Tem certeza que deseja excluir esta missão?" class="btn-action delete" title="Excluir">🗑️ Excluir</button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>Nenhuma missão encontrada.</p>
                <button wire:click="openModal" class="btn btn-secondary">Cadastrar Primeira Missão</button>
            </div>
        @endforelse
    </div>

    <!-- Paginação -->
    <div class="pagination-container">
        {{ $quests->links() }}
    </div>

    <!-- Modal de Cadastro/Edição -->
    @if($showModal)
        <div class="modal-backdrop" wire:click="closeModal">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2>{{ $isEditing ? 'Editar Missão' : 'Cadastrar Nova Missão' }}</h2>
                    <button wire:click="closeModal" class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="form-group">
                            <label for="wheel_id">Pertence à Roda</label>
                            <select id="wheel_id" wire:model="wheel_id">
                                <option value="">Sem Roda (Desassociar)</option>
                                @foreach($wheels as $wheel)
                                    <option value="{{ $wheel->id }}">{{ $wheel->name }}</option>
                                @endforeach
                            </select>
                            @error('wheel_id') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="quest_name">Nome da Missão</label>
                            <input type="text" id="quest_name" wire:model="name" placeholder="Ex: Derrotar o Dragão">
                            @error('name') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="quest_description">Descrição</label>
                            <textarea id="quest_description" wire:model="description" placeholder="O que deve ser feito na missão..."></textarea>
                            @error('description') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="quest_gain">Ganho</label>
                            <input type="number" id="quest_gain" wire:model="gain" placeholder="0">
                            @error('gain') <span class="error">{{ $message }}</span> @enderror
                        </div>

                        <!-- Upload de Imagem -->
                        <div class="form-group">
                            <label for="quest_image">Imagem da Missão</label>
                            <input type="file" id="quest_image" wire:model="image" accept="image/*">
                            <div wire:loading wire:target="image" class="loading-text">Carregando imagem...</div>
                            @error('image') <span class="error">{{ $message }}</span> @enderror
                            
                            <!-- Preview -->
                            @if ($image)
                                <div class="image-preview mt-2">
                                    <img src="{{ $image->temporaryUrl() }}" style="max-width: 100px; border-radius: 0.5rem;">
                                </div>
                            @endif
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
        .quest-manager-container {
            width: 100%;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
        }

        .filter-group select {
            padding: 0.5rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            background-color: white;
        }

        /* Grid */
        .spells-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        /* Cards */
        .spell-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .spell-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        /* Imagem */
        .spell-image-container {
            height: 160px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .spell-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .spell-image-placeholder {
            font-size: 3rem;
            color: #cbd5e1;
        }

        .spell-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .spell-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 0.5rem 0;
        }

        .spell-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .badge-gain {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-damage {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .spell-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .spell-action {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0 0 1rem 0;
            line-height: 1.4;
        }

        .spell-meta {
            display: flex;
            justify-content: flex-start;
        }

        .wheel-tag {
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .spell-actions {
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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

        /* Paginação */
        .pagination-container {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }

        .pagination-container nav {
            display: flex;
            gap: 0.5rem;
        }

        .pagination-container span, .pagination-container a {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background: white;
            color: #475569;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .pagination-container .active {
            background-color: #38bdf8;
            color: #0f172a;
            border-color: #38bdf8;
            font-weight: 600;
        }

        .pagination-container a:hover {
            background-color: #f1f5f9;
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

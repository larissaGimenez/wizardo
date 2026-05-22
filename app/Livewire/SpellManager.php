<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Spell;
use App\Models\Wheel;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SpellManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $wheel_id;
    public $name;
    public $action;
    public $gain = 0;
    public $damage = 0;
    public $image;
    public $type;
    public $editingSpellId;
    public $isEditing = false;
    public $showModal = false;
    public $filter_wheel_id;
    public $spellToDelete = null;

    protected $rules = [
        'wheel_id' => 'required|exists:wheels,id',
        'name' => 'required|string|max:255',
        'action' => 'nullable|string|max:500',
        'gain' => 'required|integer',
        'damage' => 'required|integer',
        'image' => 'nullable|image|max:1024', // 1MB Max
    ];

    public function updatingFilterWheelId()
    {
        $this->resetPage();
    }

    public function updatedType($value)
    {
        if ($value === 'penalidade das trevas') {
            $this->gain = 0;
        }
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        if ($this->type === 'penalidade das trevas') {
            $this->gain = 0;
        }

        $this->validate([
            'wheel_id' => 'nullable|exists:wheels,id',
            'name' => 'required|string|max:255|unique:spells,name,' . $this->editingSpellId,
            'action' => 'nullable|string|max:500',
            'gain' => 'required|integer',
            'damage' => 'required|integer',
            'image' => 'nullable|image|max:1024',
            'type' => 'required|string|in:feitiço diário,penalidade das trevas',
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('spells', 'public');
        }

        if ($this->isEditing) {
            $spell = Spell::find($this->editingSpellId);
            $spell->update([
                'wheel_id' => $this->wheel_id ?: null,
                'name' => $this->name,
                'action' => $this->action,
                'gain' => $this->gain,
                'damage' => $this->damage,
                'type' => $this->type,
            ]);
            
            if ($imagePath) {
                $spell->image()->updateOrCreate([], ['path' => $imagePath]);
            }
            
            session()->flash('message', 'Feitiço atualizado com sucesso!');
        } else {
            $spell = Spell::create([
                'wheel_id' => $this->wheel_id ?: null,
                'name' => $this->name,
                'action' => $this->action,
                'gain' => $this->gain,
                'damage' => $this->damage,
                'type' => $this->type,
            ]);
            
            if ($imagePath) {
                $spell->image()->create(['path' => $imagePath]);
            }
            
            session()->flash('message', 'Feitiço cadastrado com sucesso!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $spell = Spell::findOrFail($id);
        $this->editingSpellId = $id;
        $this->wheel_id = $spell->wheel_id;
        $this->name = $spell->name;
        $this->action = $spell->action;
        $this->gain = $spell->gain;
        $this->damage = $spell->damage;
        $this->type = $spell->type;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        Spell::findOrFail($id)->delete();
        $this->spellToDelete = null;
        session()->flash('message', 'Feitiço excluído com sucesso!');
    }

    private function resetForm()
    {
        $this->wheel_id = null;
        $this->name = '';
        $this->action = '';
        $this->gain = 0;
        $this->damage = 0;
        $this->type = '';
        $this->image = null;
        $this->editingSpellId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Spell::with(['wheel', 'image']);

        if ($this->filter_wheel_id) {
            $query->where('wheel_id', $this->filter_wheel_id);
        }

        return view('livewire.spell-manager', [
            'spells' => $query->paginate(6),
            'wheels' => Wheel::all()
        ])->extends('layouts.app')->section('content');
    }
}

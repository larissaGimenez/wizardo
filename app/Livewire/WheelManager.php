<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wheel;
use App\Models\Spell;
use Illuminate\Support\Str;

class WheelManager extends Component
{
    public $wheels;
    public $name;
    public $description;
    public $selected_spells = [];
    public $editingWheelId;
    public $isEditing = false;
    public $selectedWheelId;
    public $showModal = false;

    public function mount()
    {
        $this->loadWheels();
        if ($this->wheels->isNotEmpty()) {
            $this->selectWheel($this->wheels->first()->id);
        }
    }

    public function loadWheels()
    {
        $this->wheels = Wheel::with(['spells'])->get();
    }

    public function selectWheel($id)
    {
        $this->selectedWheelId = $id;
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
        $this->validate([
            'name' => 'required|string|max:255|unique:wheels,name,' . $this->editingWheelId,
            'description' => 'nullable|string|max:500',
        ]);

        if ($this->isEditing) {
            $wheel = Wheel::find($this->editingWheelId);
            $wheel->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            
            // Desassocia os feitiços que foram desmarcados
            $currentSpells = $wheel->spells->pluck('id')->toArray();
            $toDisassociate = array_diff($currentSpells, $this->selected_spells);
            Spell::whereIn('id', $toDisassociate)->update(['wheel_id' => null]);

            // Associa os feitiços selecionados a esta roda
            if (!empty($this->selected_spells)) {
                Spell::whereIn('id', $this->selected_spells)->update(['wheel_id' => $wheel->id]);
            }
            
            session()->flash('message', 'Roda atualizada com sucesso!');
        } else {
            $wheel = Wheel::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            
            // Associa os feitiços selecionados a esta roda
            if (!empty($this->selected_spells)) {
                Spell::whereIn('id', $this->selected_spells)->update(['wheel_id' => $wheel->id]);
            }
            
            session()->flash('message', 'Roda cadastrada com sucesso!');
            $this->selectWheel($wheel->id);
        }

        $this->showModal = false;
        $this->resetForm();
        $this->loadWheels();
    }

    public function edit($id)
    {
        $wheel = Wheel::findOrFail($id);
        $this->editingWheelId = $id;
        $this->name = $wheel->name;
        $this->description = $wheel->description;
        $this->selected_spells = $wheel->spells->pluck('id')->toArray();
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        Wheel::findOrFail($id)->delete();
        session()->flash('message', 'Roda excluída com sucesso!');
        $this->loadWheels();
        
        if ($this->selectedWheelId == $id) {
            $this->selectedWheelId = $this->wheels->first()?->id;
        }
        
        if ($this->editingWheelId == $id) {
            $this->resetForm();
        }
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->selected_spells = [];
        $this->editingWheelId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $spellsQuery = Spell::with('wheel')->whereNull('wheel_id');
        
        if ($this->isEditing) {
            $spellsQuery->orWhere('wheel_id', $this->editingWheelId);
        }

        return view('livewire.wheel-manager', [
            'all_spells' => $spellsQuery->get(),
            'selectedWheel' => $this->selectedWheelId ? Wheel::find($this->selectedWheelId) : null
        ])->extends('layouts.app')->section('content');
    }
}
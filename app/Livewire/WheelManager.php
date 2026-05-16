<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wheel;
use Livewire\Attributes\On;

class WheelManager extends Component
{
    public $wheels;
    public $selectedWheelId;

    public function mount()
    {
        $this->loadWheels();
        if ($this->wheels->isNotEmpty()) {
            $this->selectWheel($this->wheels->first()->id);
        }
    }

    #[On('wheel-saved')]
    public function loadWheels()
    {
        $this->wheels = Wheel::with(['spells', 'quests', 'challenges'])->get();
    }

    public function selectWheel($id)
    {
        $this->selectedWheelId = $id;
    }

    public function openCreateModal()
    {
        $this->dispatch('open-wheel-modal');
    }

    public function edit($id)
    {
        $this->dispatch('open-wheel-modal', wheelId: $id);
    }

    public function delete($id)
    {
        Wheel::findOrFail($id)->delete();
        session()->flash('message', 'Roda excluída com sucesso!');
        $this->loadWheels();
        
        if ($this->selectedWheelId == $id) {
            $this->selectedWheelId = $this->wheels->first()?->id;
        }
    }

    public function render()
    {
        return view('livewire.wheel-manager', [
            'selectedWheel' => $this->selectedWheelId ? Wheel::find($this->selectedWheelId) : null
        ])->extends('layouts.app')->section('content');
    }
}
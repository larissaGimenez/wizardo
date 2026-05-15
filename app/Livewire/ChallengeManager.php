<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Challenge;
use App\Models\Wheel;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ChallengeManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $wheel_id;
    public $name;
    public $description;
    public $level = 1;
    public $prize_name;
    public $prize_description;
    public $image;
    public $editingChallengeId;
    public $isEditing = false;
    public $showModal = false;
    public $filter_wheel_id;

    public function updatingFilterWheelId()
    {
        $this->resetPage();
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
            'wheel_id' => 'nullable|exists:wheels,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level' => 'required|integer|min:1|max:10',
            'prize_name' => 'required|string|max:255',
            'prize_description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('challenges', 'public');
        }

        if ($this->isEditing) {
            $challenge = Challenge::find($this->editingChallengeId);
            $challenge->update([
                'wheel_id' => $this->wheel_id ?: null,
                'name' => $this->name,
                'description' => $this->description,
                'level' => $this->level,
                'prize_name' => $this->prize_name,
                'prize_description' => $this->prize_description,
            ]);
            
            if ($imagePath) {
                $challenge->image()->updateOrCreate([], ['path' => $imagePath]);
            }
            
            session()->flash('message', 'Desafio atualizado com sucesso!');
        } else {
            $challenge = Challenge::create([
                'wheel_id' => $this->wheel_id ?: null,
                'name' => $this->name,
                'description' => $this->description,
                'level' => $this->level,
                'prize_name' => $this->prize_name,
                'prize_description' => $this->prize_description,
            ]);
            
            if ($imagePath) {
                $challenge->image()->create(['path' => $imagePath]);
            }
            
            session()->flash('message', 'Desafio cadastrado com sucesso!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function edit($id)
    {
        $challenge = Challenge::findOrFail($id);
        $this->editingChallengeId = $id;
        $this->wheel_id = $challenge->wheel_id;
        $this->name = $challenge->name;
        $this->description = $challenge->description;
        $this->level = $challenge->level;
        $this->prize_name = $challenge->prize_name;
        $this->prize_description = $challenge->prize_description;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        Challenge::findOrFail($id)->delete();
        session()->flash('message', 'Desafio excluído com sucesso!');
    }

    private function resetForm()
    {
        $this->wheel_id = null;
        $this->name = '';
        $this->description = '';
        $this->level = 1;
        $this->prize_name = '';
        $this->prize_description = '';
        $this->image = null;
        $this->editingChallengeId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Challenge::with(['wheel', 'image']);

        if ($this->filter_wheel_id) {
            $query->where('wheel_id', $this->filter_wheel_id);
        }

        return view('livewire.challenge-manager', [
            'challenges' => $query->paginate(6),
            'wheels' => Wheel::all()
        ])->extends('layouts.app')->section('content');
    }
}

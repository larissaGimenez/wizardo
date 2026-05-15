<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Quest;
use App\Models\Wheel;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class QuestManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $wheel_id;
    public $name;
    public $description;
    public $gain = 0;
    public $image;
    public $editingQuestId;
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
            'name' => 'required|string|max:255|unique:quests,name,' . $this->editingQuestId,
            'description' => 'nullable|string|max:500',
            'gain' => 'required|integer',
            'image' => 'nullable|image|max:1024',
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('quests', 'public');
        }

        if ($this->isEditing) {
            $quest = Quest::find($this->editingQuestId);
            $quest->update([
                'wheel_id' => $this->wheel_id ?: null,
                'name' => $this->name,
                'description' => $this->description,
                'gain' => $this->gain,
            ]);
            
            if ($imagePath) {
                $quest->image()->updateOrCreate([], ['path' => $imagePath]);
            }
            
            session()->flash('message', 'Missão atualizada com sucesso!');
        } else {
            $quest = Quest::create([
                'wheel_id' => $this->wheel_id ?: null,
                'name' => $this->name,
                'description' => $this->description,
                'gain' => $this->gain,
            ]);
            
            if ($imagePath) {
                $quest->image()->create(['path' => $imagePath]);
            }
            
            session()->flash('message', 'Missão cadastrada com sucesso!');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $quest = Quest::findOrFail($id);
        $this->editingQuestId = $id;
        $this->wheel_id = $quest->wheel_id;
        $this->name = $quest->name;
        $this->description = $quest->description;
        $this->gain = $quest->gain;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        Quest::findOrFail($id)->delete();
        session()->flash('message', 'Missão excluída com sucesso!');
    }

    private function resetForm()
    {
        $this->wheel_id = null;
        $this->name = '';
        $this->description = '';
        $this->gain = 0;
        $this->image = null;
        $this->editingQuestId = null;
        $this->isEditing = false;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Quest::with('wheel');

        if ($this->filter_wheel_id) {
            $query->where('wheel_id', $this->filter_wheel_id);
        }

        return view('livewire.quest-manager', [
            'quests' => $query->paginate(6),
            'wheels' => Wheel::all()
        ])->extends('layouts.app')->section('content');
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResrouce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type' => 'tasks',
            'id' => $this->id,
            'attributes' => [
                'body' => $this->body,
                'completed' => $this->completed,
                'project' => [
                    'title' => $this->project->title,
                    'description' => $this->project->description
                ]
            ]
        ];
    }
}

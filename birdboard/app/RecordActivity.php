<?php

namespace App;

use Illuminate\Support\Arr;

trait RecordActivity

{
    public $old =  [];

    public static function bootRecordActivity()
    {
        foreach (self::recordedEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $event = $event . '_' . strtolower(class_basename($model));
                $model->recordActivity($event);
            });

            if ($event == 'updated') {
                static::updating(function ($model) {
                    $model->old = $model->getOriginal();
                });
            }
        }
    }


    public static function recordedEvents()
    {
        if (isset(static::$recordedEvents)) {

            return static::$recordedEvents;
        }
        return ['created', 'updated', 'deleted'];
    }

    public function recordActivity($type)
    {
        $this->activites()->create([
            'user_id' => class_basename($this) == 'Project' ? $this->user_id : $this->project->user_id, //it should be auth()->id
            'project_id' => class_basename($this) == 'project' ? $this->id : $this->project_id,
            'description' => $type,
            'changes' => $this->getActivityChanges($type)
        ]);
    }

    public function getActivityChanges($type)
    {
        if ($this->wasChanged()) {
            return [
                'old' => Arr::except(array_diff($this->old, $this->getAttributes()), 'updated_at'),
                'new' => Arr::except($this->getChanges(), 'updated_at')
            ];
        }
    }

    public function activites()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}

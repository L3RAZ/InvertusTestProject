<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use DB;

class TaskController extends Controller
{
    public function list()
    {
        $tasks = Task::all();
        $firstTask = $tasks->where('after',null)->first();     
        return view('tasks.list',compact('tasks','firstTask'));
    }

    

    public function create()
    {
        $lastTask = Task::where('before',null)->first();
        $task = new Task;
        $task->notes = request('notes');
        $task->save();
        if($lastTask != null)
        {
        $task->after = $lastTask->id;
        $task->save();
        $lastTask->before = $task->id;
        $lastTask->save();
        }
        return response($task->id, 200);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        $taskBefore = Task::where('id',$task->after)->first();
        $taskAfter = Task::where('id',$task->before)->first();
        if($taskBefore !=null && $taskAfter!=null )
        {
            $taskAfter->after=$taskBefore->id;
            $taskAfter->save();
            $taskBefore->before=$taskAfter->id;
            $taskBefore->save();
        }
        elseif($taskBefore == null && $taskAfter != null)
        {
            $taskAfter->after=null;
            $taskAfter->save();
        }
        elseif($taskAfter==null && $taskBefore != null)
        {
            $taskBefore->before=null;
            $taskBefore->save();
        }
        $task->delete();
    }

    public function markAsDone($id)
    {
        $task = Task::find($id);
        $task->isDone=1;
        $task->save();
    }

    public function relocate($id, $beforeId)
    {
        $task = Task::findOrFail($id);
        $taskBefore = Task::where('id',$task->after)->first();
        $taskAfter = Task::where('id',$task->before)->first();
        if($beforeId == null) //If task is moved to the back of the list
        {
            $lastTask = Task::where('before',null)->first();
            if($lastTask != $task) //if the task is in the back, doesn't move it
            {
                if($taskBefore == null) //If the task is at the front
                {
                    $task->after = $lastTask->id;
                    $task->before = null;
                    $task->save();

                    if($lastTask == $taskAfter) //If there are two tasks
                    {
                        $taskAfter->after = null;
                        $taskAfter->before = $task->id;
                        $taskAfter->save();
                    }
                    else
                    {
                        $taskAfter->after=null;
                        $taskAfter->save();
                        $lastTask->before=$task->id;
                        $lastTask->save();
                    }
                }
                else
                {
                    if($taskAfter != null)
                    {
                        $lastTask->before = $task->id;
                        $lastTask->save();
                        $taskBefore->before = $taskAfter->id;
                        $taskBefore->save();
                        $taskAfter->after = $taskBefore->id;
                        $taskAfter->save();
                    }
                }
            }
        }
        else
        {
            $taskAfterInsertionPoint = Task::where('id',$beforeId)->first();
            $taskBeforeInsertionPoint = Task::where('id',$taskAfterInsertionPoint->after)->first();
            if($task->id != $taskAfterInsertionPoint->after)
            {
                if($taskBefore == null) //if task is moved from front to middle
                {
                    $task->before= $taskAfterInsertionPoint->id;
                    $task->after= $taskBeforeInsertionPoint->id;
                    $task->save();
                    $taskAfter->after=null;
                    $taskAfter->save();
                    $taskAfterInsertionPoint->after=$task->id;
                    $taskAfterInsertionPoint->save();
                    $taskBeforeInsertionPoint->before=$task->id;
                    $taskBeforeInsertionPoint->save();
                }
                elseif($taskAfter == null) //if the task is moved from back
                {
                    $taskBefore->before=null;
                    $taskBefore->save();
                    $firstTask = Task::where('after',null)->first();
                    if($taskBefore->after=null) //if there are only 2 tasks
                    {
                        $taskBefore->after=$task->id;
                        $taskBefore->save();
                    }
                    elseif($firstTask->id == $beforeId) //if the task is inserted to the front
                    {   
                        $task->before=$firstTask->id;
                        $task->after=null;
                        $task->save();
                        $firstTask->after=$task->id;
                        $firstTask->save();
                    }
                    else //if the task is inserted to the middle
                    {
                        $taskAfterInsertionPoint->after=$task->id;
                        $taskAfterInsertionPoint->save();
                        $taskBeforeInsertionPoint->before=$task->id;
                        $taskBeforeInsertionPoint->save();
                        $task->after=$taskBeforeInsertionPoint->id;
                        $task->before=$taskAfterInsertionPoint->id;
                        $task->save();
                    }
                }
                elseif($taskBeforeInsertionPoint == null) //if the task is moved from the middle to front
                {
                    $task->after=null;
                    $task->before=$taskAfterInsertionPoint->id;
                    $task->save();
                    $taskAfter->after=$taskBefore->id;
                    $taskAfter->save();
                    $taskBefore->before=$taskAfter->id;
                    $taskBefore->save();
                    $taskAfterInsertionPoint->after=$task->id;
                    $taskAfterInsertionPoint->save();
                }
                else //if the task is moved from middle to middle
                {
                    $task->after=$taskBeforeInsertionPoint->id;
                    $task->before=$taskAfterInsertionPoint->id;
                    $task->save();
                    $taskAfterInsertionPoint->after=$task->id;
                    $taskAfterInsertionPoint->save();
                    $taskBeforeInsertionPoint->before=$task->id;
                    $taskBeforeInsertionPoint->save();
                    $taskAfter->after=$taskBefore->id;
                    $taskAfter->save();
                    $taskBefore->before=$taskAfter->id;
                    $taskBefore->save();
                }
            }
        }
    }
}

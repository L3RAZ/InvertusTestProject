@extends('layouts.app')
@section('content')
@php
$task = $firstTask;
@endphp
<input type="hidden" id="token" value="{{ csrf_token() }}">
<div class="container">
    <div class="col-lg-auto">
        <button class="btn btn-success" id="modal">Create Task</button>
        <hr>
        <table id="tasks" class="table table-hover">
        <tbody>
            @while($task != null)
                <tr id="taskRow" class="{{$task->isDone==0?"unfinished":"finished"}}">
                    <td>
                    {{$task->notes}}
                    </td>
                    <td width="15%">
                        @if($task->isDone==0)
                        <button id="doneBtn" class="btn btn-success js-done" data-task-id="{{$task->id}}">Mark as done</button>
                        @endif
                    </td>
                    <td width="15%">
                        <button id="deleteBtn" data-task-id="{{$task->id}}" class="btn btn-danger js-delete">Delete</button>
                    </td>
                </tr>
            @php
            $task = $tasks->where('after',$task->id)->first();  
             @endphp
            @endwhile
        </tbody>
        </table>
    </div>
</div>

<div id="formModal" class="modal">
    <div class="modal-content">
        <span class="close" float="right">&times;</span>
        <h3>Create a new task!</h3>
        <div class="col-md-auto">
            <div class="form-group">
                <label for="notes">Task Notes:</label>
                <input type="text" class="form-control" id="notes" name="notes" required>
            </div>
        <button class="btn btn-success" id="createBtn" type="submit">Submit</button>
        </div>
    </div> 
</div>
@endsection
<script>
window.onload = function(){ 

var modal = document.getElementById('formModal');
var btn = document.getElementById("modal");
var span = document.getElementsByClassName("close")[0];
btn.onclick = function() {
    modal.style.display = "block";
}
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

document.getElementById("createBtn").addEventListener("click", function(){
    var button = $(this);
    if($('#notes').val()=='')
    {
        alert("Notes input can't be empty.");
    }
    else
    {
        $.ajax({
                url: "/tasks",
                method: "POST",
                data:{
                    notes:$('input#notes').val(),
                    _token:$('input#token').val()
                    },
                success: function ( taskId ) {
                    console.log(taskId);
                    var notes = $('#notes').val();
                    $('#tasks tbody').append(
                        '<tr id="taskRow" class="unfinished">'+
                        '<td>'+notes+'</td>'+
                        '<td width="15%"><button id="doneBtn" class="btn btn-success js-done" data-task-id="'+taskId+'">Mark as done</button></td>'+
                        '<td width="15%"><button id="deleteBtn" data-task-id="'+taskId+'" class="btn btn-danger js-delete">Delete</button></td></tr>'
                    );
                    $('#notes').val('');
                }
            });
    }
    });

    $("#tasks").on("click", ".js-delete", function () {
    var button = $(this);
    $.ajax({
                url: "/tasks/"+button.attr("data-task-id"),
                method: "POST",
                data:{
                    _token:$('input#token').val(),
                    _method:"DELETE"
                    },
                success: function () {
                    button.parent().parent().remove();
                }
            });
    });

    $("#tasks").on("click", ".js-done", function () {
    var button = $(this);
    $.ajax({
                url: "/tasks/"+button.attr("data-task-id"),
                method: "POST",
                data:{
                    _token:$('input#token').val(),
                    _method:"PATCH"
                    },
                success: function () {
                    button.parent().parent().removeClass("unfinished");
                    button.parent().parent().addClass("finished");
                    button.remove();
                }
            });
    });


};
</script>
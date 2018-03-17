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
        <tbody id="taskTableBody">
            @while($task != null)
                <tr class="{{$task->isDone==0?"unfinished":"finished"}}" data-task-id="{{$task->id}}">
                    <td width="3%">
                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                    </td>
                    <td width="72%">
                    {{$task->notes}}
                    </td>
                    <td width="15%">
                        @if($task->isDone==0)
                        <button id="doneBtn" class="btn btn-success js-done">Mark as done</button>
                        @endif
                    </td>
                    <td width="10%">
                        <button id="deleteBtn" class="btn btn-danger js-delete">Delete</button>
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
        <span id="close" class="close" float="right">&times;</span>
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

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
window.onload = function(){ 
var token = document.head.querySelector("[name=csrf-token]").content;
var modal = document.getElementById('formModal');
var openModalBtn = document.getElementById("modal");
var closeModalBtn = document.getElementById("close");

openModalBtn.onclick = function() {
    modal.style.display = "block";
}

closeModalBtn.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

document.getElementById("createBtn").addEventListener("click", function(){
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
                    _token:token
                    },
                success: function ( taskId ) {
                    var notes = $('#notes').val();
                    $('#tasks tbody').append(
                        '<tr id="taskRow" class="unfinished" data-task-id="'+taskId+'">'+
                        '<td width="3%"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>'+
                        '<td width="72%">'+notes+'</td>'+
                        '<td width="15%"><button id="doneBtn" class="btn btn-success js-done" >Mark as done</button></td>'+
                        '<td width="10%"><button id="deleteBtn" class="btn btn-danger js-delete">Delete</button></td></tr>'
                    );
                    $('#notes').val('');
                }
            });
    }
    });

    $("#tasks").on("click", ".js-delete", function () {
    var button = $(this);
    var taskId = button.closest("tr").attr("data-task-id");
    $.ajax({
                url: "/tasks/"+taskId,
                method: "POST",
                data:{
                    _token:token,
                    _method:"DELETE"
                    },
                success: function () {
                    button.closest("tr").remove();
                }
            });
    });

    $("#tasks").on("click", ".js-done", function () {
    var button = $(this); 
    var taskId = button.closest("tr").attr("data-task-id");
    $.ajax({
                url: "/tasks/"+taskId,
                method: "POST",
                data:{
                    _token:token,
                    _method:"PATCH"
                    },
                success: function () {
                    button.closest("tr").removeClass("unfinished");
                    button.closest("tr").addClass("finished");
                    button.remove();
                }
            });
    });

    $( function() {
    $( "#taskTableBody" ).disableSelection();
    $( "#taskTableBody" ).sortable({
        stop: function(event, ui) {
            var currentTaskRow = $(ui.item);
            var taskAfter = currentTaskRow.closest('tr').next('tr');
            $.ajax({
                    url: "/tasks",
                    method: "POST",
                    data:{
                    _token:token,
                    _method:"PUT",
                    id:currentTaskRow.attr('data-task-id'),
                    beforeId:taskAfter.attr('data-task-id')
                    },
                    success: function () {
                    }
                });
        }
        });
    });
};
</script>
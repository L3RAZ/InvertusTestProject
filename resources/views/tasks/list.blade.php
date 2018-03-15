@extends('layouts.app')
@section('content')
@php
$task = $firstTask;
@endphp
<div class="container">
    <div class="col-lg-auto">
        <button class="btn btn-success" id="modal">Create Task</button>
        <hr>
        <table class="table table-hover">
        <tbody>
            @while($task != null)
                <tr id="taskRow" class="{{$task->isDone==0?"unfinished":"finished"}}">
                    <td>
                    {{$task->notes}}
                    </td>

                    <td width="15%">
                        @if($task->isDone==0)
                        <form method="POST" action="/tasks/{{$task->id}}">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}
                            <button class="btn btn-success" type="submit">Mark as done</button>
                    </form>

                        @endif
                    </td>

                    <td width="15%">
                        <form method="POST" action="/tasks/{{$task->id}}">
                                {{ method_field('DELETE') }}
                                {{ csrf_field() }}
                        <button id="deleteBtn" class="btn btn-danger">Delete</button>
                        </form>
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

@endsection
<div id="formModal" class="modal">
        <div class="modal-content">
            <span class="close" float="right">&times;</span>
            <h3>Create a new task!</h3>
            <div class="col-md-auto">
                <form method="POST" action="/tasks">
                    {{ csrf_field() }}  
                    <div class="form-group">
                            <label for="notes">Task Notes:</label>
                            <input type="text" class="form-control" id="notes" name="notes" required>
                          </div>
                    <button class="btn btn-success" type="submit">Submit</button>
                </form>
            </div>
        </div>
      
      </div>
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

document.getElementById("deleteBtn").addEventListener("click", function(event){
    event.preventDefault(),
    $(this).parent().parent().parent().remove()//AJAX here
    
});
};
</script>
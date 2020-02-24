@extends('layouts.app')

@section('content')
@push('css')
<meta name="receiver_id" content="@isset($receiver->id){{$receiver->id }}@endisset" >
<link href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.1/emojionearea.min.css" rel="stylesheet" type="text/css" />
<style>
    .imgStyle{
        background-size: cover;
        width: 113px;
        height: 156px;
        border-radius: inherit;
        background-position: center center;
        border-bottom-right-radius: 25px;
        border-top-right-radius: 25px;
        border-top-left-radius: 25px;
        background-color: #c4c4c4;
        margin-bottom: 5px
    }
    .radiusBLeft{
        border-bottom-left-radius: 4px !important;
        border-bottom-right-radius: 25px !important;
    }
    .radiusBRight{
        border-bottom-right-radius: 4px !important;
        border-bottom-left-radius: 25px !important;
    }
    #clickToChoosePhoto{
        font-size: 20px;
        color: #3f3f48;
        margin-right: 22px;
        margin-top: 2px;
        text-decoration: none;
    }
    #clickToChoosePhoto:hover{
        text-decoration: none;
        color: gray;
    }
</style>
@endpush

{{-- <meta name="sender_id" content="{{auth()->check() ?  auth()->user()->id : 'null' }}"> --}}
<div class="container">
    <div class="row">
        <audio id='runAudioNofti'>
            <source src="{{ asset('sounds/chat.mp3') }}" type="audio/mpeg">
          Your browser does not support the audio element.
          </audio>
        <div class="col-md-3">
            <h4>Online users</h4><hr>
            <ul class="list-group" id="online-users"></ul>
        </div>
        <div class="col-md-9 d-flex flex-column" style="height:500px">
            <div class="h-100 bg-white mb-4 p-5" id="chat" style="overflow-y:scroll">
                @isset($messages)
                @foreach ($messages as $message)
                      @if($message->image&&$message->body)
                            <div id='message-{{  $message->id }}' style="display: table;border-bottom-left-radius: 4px;" class=" {{auth()->user()->id == $message->sender_id ? 'float-right radiusBRight': 'float-left radiusBLeft'}}">
                                <div style="background-image:url({{ asset('images/'.$message->image) }})" class="imgStyle  {{auth()->user()->id == $message->sender_id ? 'radiusBRight': 'radiusBLeft'}}"></div>
                                <div style="width: 100%; margin-top: -5px;" class="text-white mb-1 p-1 rounded  {{auth()->user()->id == $message->sender_id ? 'float-right bg-primary': 'float-left bg-warning'}}"> {{$message->body }}</div>
                                {{-- <img src="{{ asset('images/'.$message->image) }}" alt=""> --}}
                            </div>
                        @endif
                      @if($message->image&&!$message->body)
                        <div id='message-{{  $message->id }}' class="rounded {{auth()->user()->id == $message->sender_id ? 'float-right radiusBRight': 'float-left radiusBLeft'}}">
                            <div style="background-image:url({{ asset('images/'.$message->image) }})" class="imgStyle {{auth()->user()->id == $message->sender_id ? 'radiusBRight': 'radiusBLeft'}}"></div>
                        </div>
                      @endif
                    @if(!$message->image&&$message->body)
                      <div id='message-{{  $message->id }}' style="display: table;" class="mt-3 mb-1  text-white p-1 rounded {{auth()->user()->id == $message->sender_id ? 'float-right bg-primary': 'float-left bg-warning'}}">
                        <p style="width: 100%; ">  {{$message->body }}</p>
                    </div>
                     @endif
                        @if (auth()->user()->id == $message->sender_id)
                        <div>
                            <input type="hidden" name='message_id' class="message_id" value="{{$message->id}}">
                            @method('delete')
                            <button class="float-right mt-3 btn btn-link delete_message" >x</button>
                        </div>
                        @endif
                        <div class="clearfix"></div>
                    @endforeach
                @endisset

            </div>
            {{-- <input type="text" name="" id="data-message"> --}}
            <span class="d-none" id="typing">typing...</span>

            <div class="form-group img">

            </div> <!-- form-group// -->
            <form method="post" action="{{ route('upload') }}" enctype="multipart/form-data" id="upload-image">
                @csrf
                <input type="file" name = "image" id="image" class="d-none">
                <input type="submit" id='uploadAjaxSubmit' name="upload" value="Upload" class="d-none" />

            </form>

                <textarea name="" id="data-message" cols="30" rows="10"></textarea>
                <button class="btn btn-sm btn-primary"  id="send">Send</button>


        </div>
    </div>
</div>
@push('js')
<script>
    url = "{{url('/messages/ChatUser')}}";

</script>

@endpush

@push('jsforpage')

<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.1/emojionearea.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.textcomplete/1.8.5/jquery.textcomplete.min.js"></script>
<script src="{{ asset('js/functions.js') }}"></script>

<script>
var body = '';

$(document).ready(function () {

    $(document).on('click','#send',function(){
        sendData($("#data-message")[0].emojioneArea.getText(),"{{route('messages.store')}}","{{ asset('/images/') }}");
    });

    $(document).keydown('#data-message',function(e){
        if (e.which == 13) {
            e.preventDefault();
        }

    });
    $('#chat').animate({scrollTop: $('#chat').prop("scrollHeight")}, 500);


    //delete message

    $(document).on('click','.delete_message',function(){
        let id = $(this).siblings('.message_id').val();
        let remove = $(this).parent();
        let token =$('meta[name="csrf-token"]').attr('content');
        $.ajax(
        {
            url:`{{url('messages/${id}')}}`,
            type: 'Post',
            dataType: "JSON",
            data: {
                "id": id,
                "_method": 'DELETE',
                "_token": token,
            },
            success: function (response)
            {
                if (!response.error) {
                    $('#message-'+id).remove();
                    remove.remove();
                }
            }
        });


    });

    //end delete message


    $("#data-message").emojioneArea({

        events: {
        onLoad () {
            $('.emojionearea').append(`<a href="#" class="fa fa-paperclip emojionearea-button attach" id="clickToChoosePhoto" ></a>`);
        },
        keyup: function (editor, event) {

            if (event.which == 13 && ($.trim(editor.text()).length > 0 || $.trim(editor.html()).length > 0)) {
                sendData(this.getText(),"{{route('messages.store')}}","{{ asset('/images/') }}");
            }
        }
        }
    });

    $(document).on('click','#clickToChoosePhoto',function(e){
        e.preventDefault();
        document.getElementById('image').click();

    });

    $('#image').change(function() {

        uploadForm("#upload-image");
        $("#uploadAjaxSubmit").click();
        PreviewImage($(this),'.img');
    });




});

</script>


@endpush
@endsection

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./jquery.form');
require('../../public/js/functions');
// window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app',
// });
function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

import Echo from "laravel-echo"

window.io = require('socket.io-client');

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001'
});
const sender_id = $('meta[name="sender_id"]').attr('content');
const receiver_id = $('meta[name="receiver_id"]').attr('content');
// console.log(receiver_id);
window.Echo.join('online')
    .here((users) => {
       users.forEach(function(user){
        $('#online-users').append(`<a href='${url}/${user.id}'><li class="list-group-item" id="user-${user.name}"><span class="fa fa-circle text-success"></span>`+ user.name+`</li></a>`);
      });
    })
    .joining((user) => {
         $('#online-users').append(`<a href='${url}/${user.id}'><li class="list-group-item" id="user-${user.name}"><span class="fa fa-circle text-success"></span>`+ user.name+`</li></a>`);
    })
    .leaving((user) => {
     $('#user-'+user.name).remove();
     if(!$('#typing').hasClass('d-none')){
        $('#typing').addClass('d-none');
     }

    });


    window.Echo.channel(`laravel_database_chat-real.${sender_id}${receiver_id}`)
    .listen('MessageDelivered', (e) => {
      //  console.log(e);
        if(e.delete == 1 ){
            $('#message-'+e.message.id).remove();
        }else{
            if(e.message.body==null&&e.message.image!=null){
                $('#chat').append(`
                <div id='message-${e.message.id}' class="rounded float-left radiusBLeft ">
                <div style="background-image:url(http://localhost:8000/images/${e.message.image})" class="imgStyle radiusBLeft"}}"></div>
            </div><div class="clearfix"></div> `)

            }else if(e.message.body!=null&&e.message.image!=null){
                $('#chat').append(`
                <div id='message-${e.message.id}' style="display: table;border-bottom-left-radius: 4px;" class=" float-left radiusBLeft">
                    <div style="background-image:url(http://localhost:8000/images/${e.message.image})" class="imgStyle "></div>
                   <div style="width: 100%; margin-top: -5px;" class="text-white p-1 rounded float-right bg-warning"> ${htmlEntities(e.message.body)}</div>
                </div>
               <div class="clearfix"></div>
                `)
            }else{
                $('#chat').append(`
                <div id='message-${e.message.id}' style="display: table;" class="mt-3 mb-1  text-white p-1 rounded float-left bg-warning">
                   <div style="width: 100%; "> ${htmlEntities(e.message.body)}</div>
                 </div>
               <div class="clearfix"></div>
                `)
            }

            $('#runAudioNofti')[0].play();
            $('#typing').addClass('d-none');
        }


       // console.log(e.message.body);
    });


    if(sender_id>receiver_id){
        var roomShared = sender_id+""+receiver_id;
       }else{
        var roomShared = receiver_id+""+sender_id;
       }
       //console.log($('.emojionearea-editor').length);
       function typing(){
            if($('.emojionearea-editor').length !==0){
                $('.emojionearea-editor')[0].addEventListener("keydown", function(e){
                    typing = true;
                    if($('.emojionearea-editor')[0].innerHTML.length == 0){
                        typing = false;
                    }
                    let channel = window.Echo.private(`typing-${roomShared}`)
                    setTimeout( () => {
                    channel.whisper('typing',{
                        typing:typing
                    })
                    }, 300)
                })
            }
       }

       setTimeout( () => {
            if($('.emojionearea-editor').length!=0){
                typing();
            }
        }, 1000)

            window.Echo.private(`typing-${roomShared}`)
            .listenForWhisper('typing', (e) => {
                if(e.typing){
                  $('#typing').removeClass('d-none');
                }else{
                  $('#typing').addClass('d-none');
                }
            });






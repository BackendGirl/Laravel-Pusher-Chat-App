<!DOCTYPE html>
<html lang="en">
<head>
  <title>Chat App</title>
  <link rel="icon" href="https://assets.edlin.app/favicon/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- JavaScript -->
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <!-- End JavaScript -->

  <!-- CSS -->
  <link rel="stylesheet" href="/style.css">
  <!-- End CSS -->

  <style>
    .messages {
  height: 500px; /* or whatever height suits your design */
  overflow-y: auto;
}
  </style>

</head>

<body>
<div class="chat">

  <!-- Header -->
  <div class="top">
    <img src="{{asset('prarthana-profile.jpg')}}" alt="Avatar" style="hieght:75px;width:55px">
    <div>
      <p>Prarthana</p>
      <small>Online</small>
    </div>
  </div>
  <!-- End Header -->

  <!-- Chat -->
  <div class="messages">
    @include('broadcast', ['message' => "Hey! What's up!  👋"])
    @include('receive', ['message' => "Ask a friend to open this link and you can chat with them!"])
  </div>
  <!-- End Chat -->

  <!-- Footer -->
  <div class="bottom">
    <form>
      <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
      <button type="submit"></button>
    </form>
  </div>
  <!-- End Footer -->

</div>
</body>

<script>
$(document).ready(function() {
  const pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
    cluster: 'ap2'
  });
  const channel = pusher.subscribe('public');

  // Receive messages
  channel.bind('chat', function (data) {
    $.post("/receive", {
      _token: '{{csrf_token()}}',
      message: data.message,
    })
    .done(function (res) {
      $(".messages").append(res);
      setTimeout(function() {
        $(document).scrollTop($(document).height());
      }, 100);
    });
  });

  // Broadcast messages
  $("form").submit(function (event) {
    event.preventDefault();

    $.ajax({
      url: "/broadcast",
      method: 'POST',
      headers: {
        'X-Socket-Id': pusher.connection.socket_id
      },
      data: {
        _token: '{{csrf_token()}}',
        message: $("form #message").val(),
      }
    }).done(function (res) {
      $(".messages").append(res);
      $("form #message").val('');
      setTimeout(function() {
        $(document).scrollTop($(document).height());
      }, 100);
    });
  });
});

</script>
</html>
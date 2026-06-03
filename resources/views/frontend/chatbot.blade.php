@extends('frontend.layout')

@section('title', 'Health Chatbot')

@section('content')
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h1 class="mb-3">Health Chatbot</h1>
            <p class="text-muted mb-4">Ask any general health or clinic-related question below. The chatbot will reply with helpful information.</p>
            <div id="chat-page-messages" class="border rounded-3 p-3 mb-3" style="min-height: 320px; background: #f8f9fa; overflow-y: auto;"></div>
            <form id="chat-page-form" class="d-flex gap-2">
              <input id="chat-page-input" type="text" class="form-control" placeholder="Type your question..." autocomplete="off" />
              <button type="submit" class="btn btn-primary">Send</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
  $(function () {
    const $messages = $('#chat-page-messages');
    const $input = $('#chat-page-input');
    const $form = $('#chat-page-form');
    const chatRoute = '{{ route('chatbot.send') }}';

    function addMessage(type, text) {
      const item = $('<div>').addClass('p-3 mb-3 rounded-3');
      if (type === 'user') {
        item.addClass('bg-primary text-white ms-auto').css('max-width', '85%');
      } else {
        item.addClass('bg-white border text-dark me-auto').css('max-width', '85%');
      }
      item.text(text);
      $messages.append(item);
      $messages.scrollTop($messages[0].scrollHeight);
    }

    addMessage('bot', 'Hello! I am your health assistant. Ask any general health or clinic-related question.');

    $form.on('submit', function (event) {
      event.preventDefault();
      const text = $input.val().trim();
      if (!text) {
        return;
      }

      addMessage('user', text);
      $input.val('');
      const thinking = $('<div>').addClass('p-3 mb-3 rounded-3 bg-white border text-dark me-auto').text('Thinking...');
      $messages.append(thinking);
      $messages.scrollTop($messages[0].scrollHeight);

      axios.post(chatRoute, { message: text })
        .then(function (response) {
          thinking.text(response.data.message || 'Sorry, I could not retrieve an answer.');
        })
        .catch(function (error) {
          thinking.text(error.response?.data?.message || 'Unable to reach the chat service.');
        });
    });
  });
</script>
@endpush

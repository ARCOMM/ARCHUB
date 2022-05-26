<input
    type="text"
    placeholder="Subject"
    name="subject"
    value="{{ (isset($email)) ? $email->subject : '' }}"
    class="form-control mb-3"
    maxlength="255">

<textarea
    name="content"
    placeholder="Content"
    class="form-control mb-5"
    rows="10">{!! (isset($email)) ? $email->content : '' !!}
</textarea>

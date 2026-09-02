<!DOCTYPE html>
<html>
<head><title>netuqo diagnostic</title></head>
<body style="font-family: sans-serif; max-width: 500px; margin: 40px auto;">
    <h1>Diagnostic</h1>

    <p>Button 1: POST with no CSRF protection at all (simplest possible POST).</p>
    <form method="POST" action="/diag-ping-nocsrf">
        <button type="submit">Test 1: POST without CSRF</button>
    </form>

    <p>Button 2: POST with normal CSRF protection (same as the real task form).</p>
    <form method="POST" action="/diag-ping">
        @csrf
        <button type="submit">Test 2: POST with CSRF</button>
    </form>
</body>
</html>

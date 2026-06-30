@if ($errors->any())
    <div class="alert alert-danger" style="position: fixed; top: 20px; right: 20px; z-index: 9999; width: 300px;">
        <strong>There were some problems:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
    setTimeout(() => {
        const alert = document.querySelector('.alert.alert-danger');
        if (alert) {
            alert.remove();
        }
    }, 5000); // hide after 5 seconds
</script>
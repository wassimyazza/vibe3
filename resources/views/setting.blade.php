<form method="POST" action="{{ route('settings.update') }}">
    @csrf
    <div>
        <label for="delete_after_24h">Delete Messages After 24 Hours</label>
        <input type="checkbox" id="delete_after_24h" name="delete_after_24h" {{ auth()->user()->delete_after_24h ? 'checked' : '' }}>
    </div>
    <button type="submit">Save Settings</button>
</form>
